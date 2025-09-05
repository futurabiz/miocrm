@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Modifica Workflow: {{ $workflow->name }}</h2>
            <a class="btn btn-primary" href="{{ route('workflows.index') }}"> Indietro</a>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger mt-2">
        <strong>Ops!</strong> Ci sono stati problemi con i dati inseriti.<br><br>
        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
@endif

<form action="{{ route('workflows.update', $workflow->id) }}" method="POST" class="mt-3 card card-body">
    @csrf
    @method('PUT')
    <input type="hidden" name="workflow_id" value="{{ $workflow->id }}">
    @include('workflows.partials._form_body')
    <div class="col-md-12 text-center mt-4">
        <button type="submit" class="btn btn-primary">Salva Modifiche</button>
    </div>
</form>
@endsection

@push('scripts')
{{-- Forniamo le librerie necessarie --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- Lo script di TinyMCE è nel layout app.blade.php --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modelSelector = document.getElementById('trigger_model');
    const fieldSelector = document.getElementById('trigger_condition_field');
    const operatorSelector = document.getElementById('trigger_condition_operator');
    const valueContainer = document.getElementById('trigger-value-container');
    const actionSelector = document.getElementById('action_type');
    const paramsContainer = document.getElementById('action-parameters-container');
    let allFields = {};

    const savedData = {
        field: "{{ old('trigger_condition_field', $workflow->trigger_condition_field ?? '') }}",
        operator: "{{ old('trigger_condition_operator', $workflow->trigger_condition_operator ?? '') }}",
        value: `{!! old('trigger_condition_value', $workflow->trigger_condition_value ?? '') !!}`,
        actionParams: @json(old('action_parameters', $workflow->action_parameters ?? []))
    };

    async function updateFields(preselectField, callback) {
        const modelClass = modelSelector.value;
        fieldSelector.innerHTML = '<option value="">Caricamento...</option>';
        if (!modelClass) {
            fieldSelector.innerHTML = '<option value="">-- Seleziona prima un Modulo --</option>';
            return;
        }
        try {
            const response = await fetch(`{{ route('workflows.getFields') }}?model=${encodeURIComponent(modelClass)}`);
            allFields = await response.json();
            fieldSelector.innerHTML = '<option value="">-- Seleziona Campo --</option>';
            for (const [fieldName, fieldData] of Object.entries(allFields)) {
                const option = new Option(fieldData.label, fieldName);
                if(fieldName === preselectField) option.selected = true;
                fieldSelector.appendChild(option);
            }
            if (callback) callback();
        } catch (error) { console.error('Errore nel caricamento dei campi:', error); }
    }

    function updateOperators(preselectOp) {
        operatorSelector.innerHTML = '';
        const operators = { '=': 'Uguale a', '!=': 'Diverso da' };
        for (const [opValue, opLabel] of Object.entries(operators)) {
            const option = new Option(opLabel, opValue);
            if (opValue === preselectOp) option.selected = true;
            operatorSelector.appendChild(option);
        }
        updateValueInput();
    }

    function updateValueInput() {
        if ($('#trigger_condition_value').data('select2')) {
            $('#trigger_condition_value').select2('destroy');
        }
        const fieldData = allFields[fieldSelector.value];
        if (!fieldData) {
            valueContainer.innerHTML = '<label class="form-label">...e questo valore:</label><input type="text" class="form-control" disabled>';
            return;
        }
        const labelHtml = `<label for="trigger_condition_value" class="form-label">...e questo valore:</label>`;
        valueContainer.innerHTML = `${labelHtml}<select name="trigger_condition_value" id="trigger_condition_value" class="form-select"></select>`;
        const selectElement = $('#trigger_condition_value');
        if (fieldData.type === 'options') {
            selectElement.empty().append(new Option('Caricamento...', '')).prop('disabled', true);
            fetch(`{{ route('workflows.getFieldOptions') }}?model=${encodeURIComponent(modelSelector.value)}&field=${fieldSelector.value}`)
                .then(response => response.json())
                .then(options => {
                    selectElement.empty().append(new Option('-- Seleziona Valore --', ''));
                    if (Array.isArray(options)) {
                        options.forEach(option => { selectElement.append(new Option(option, option)); });
                    } else if (options && typeof options === 'object') {
                        for (const [value, label] of Object.entries(options)) { selectElement.append(new Option(label, value)); }
                    }
                    selectElement.prop('disabled', false);
                    if (savedData.value) { selectElement.val(savedData.value); }
                    selectElement.select2({ theme: "bootstrap-5", placeholder: 'Seleziona un valore', allowClear: true });
                })
                .catch(error => { console.error('Errore nel caricamento delle opzioni:', error); selectElement.empty().append(new Option('Errore nel caricamento', '')); selectElement.prop('disabled', false); });
            return;
        }
        let ajaxUrl; let placeholder = 'Digita per cercare...';
        switch (fieldData.type) {
            case 'select_ajax_user': ajaxUrl = "{{ route('api.users.search') }}"; placeholder = 'Cerca un Utente...'; break;
            case 'select_ajax_company': ajaxUrl = "{{ route('api.companies.search') }}"; placeholder = 'Cerca un\'Azienda...'; break;
            case 'select_ajax_contact': ajaxUrl = "{{ route('api.contacts.search') }}"; placeholder = 'Cerca un Contatto...'; break;
            default: ajaxUrl = "{{ route('workflows.searchValues') }}"; placeholder = 'Cerca un valore...'; break;
        }
        selectElement.select2({
            theme: "bootstrap-5", placeholder: placeholder, allowClear: true, tags: true,
            ajax: {
                url: ajaxUrl, dataType: 'json', delay: 250,
                data: function(params) {
                    let query = { term: params.term, q: params.term };
                    const genericSearchTypes = ['text_searchable', 'number_searchable', 'date'];
                    if (genericSearchTypes.includes(fieldData.type)) {
                        query.model = modelSelector.value;
                        query.field = fieldSelector.value;
                    }
                    return query;
                },
                processResults: function (data) {
                    let sourceArray = (data && data.results && Array.isArray(data.results)) ? data.results : (Array.isArray(data) ? data : []);
                    const mappedResults = sourceArray.map(item => {
                        if (typeof item !== 'object' || item === null) return { id: item, text: item };
                        return { id: item.id, text: item.text || item.name || item.label || item.id };
                    });
                    return { results: mappedResults };
                }
            }
        });
        const preselectId = savedData.value;
        if (preselectId && fieldData.type.startsWith('select_ajax_')) {
            fetch(`{{ route('workflows.getInitialEntity') }}?field=${fieldSelector.value}&id=${preselectId}`)
                .then(response => response.json())
                .then(data => { if (data && data.id && data.text) { selectElement.append(new Option(data.text, data.id, true, true)).trigger('change'); } })
                .catch(error => console.error('Error fetching initial value:', error));
        } else if (preselectId) {
            selectElement.append(new Option(preselectId, preselectId, true, true)).trigger('change');
        }
    }
    
    function initializeTinyMCE() {
        if (tinymce.get('param-body')) {
            tinymce.remove('#param-body');
        }
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table directionality emoticons template paste textpattern',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
            height: 300,
        });
    }

    function updateShortcodeList() {
        const modelClass = modelSelector.value;
        const shortcodeList = document.getElementById('shortcode-list');
        if (!shortcodeList) return;
        
        const allShortcodes = @json(collect(config('workflows.available_models'))->mapWithKeys(function ($config, $model) {
            return [$model => $config['shortcodes'] ?? []];
        }));

        shortcodeList.innerHTML = '';
        if (modelClass && allShortcodes[modelClass]) {
            for (const [code, description] of Object.entries(allShortcodes[modelClass])) {
                const link = document.createElement('a');
                link.href = '#';
                link.className = 'list-group-item list-group-item-action';
                link.dataset.shortcode = code;
                link.innerHTML = `<strong>${code}</strong><br><small>${description}</small>`;
                link.onclick = function(e) {
                    e.preventDefault();
                    tinymce.get('param-body')?.insertContent(this.dataset.shortcode);
                };
                shortcodeList.appendChild(link);
            }
        } else {
            shortcodeList.innerHTML = '<a href="#" class="list-group-item list-group-item-action disabled">Seleziona un modulo trigger</a>';
        }
    }

    async function renderActionParams() {
        const actionType = actionSelector.value;
        paramsContainer.innerHTML = '<div class="col-12"><p>Caricamento parametri...</p></div>';

        if (!actionType) {
            paramsContainer.innerHTML = '';
            return;
        }

        try {
            const workflowId = document.querySelector('input[name="workflow_id"]')?.value || '';
            const url = `{{ route('workflows.getActionParameters') }}?action_type=${actionType}&workflow_id=${workflowId}`;
            
            const response = await fetch(url);
            if (!response.ok) throw new Error(`Errore HTTP: ${response.status}`);
            
            const data = await response.json();
            paramsContainer.innerHTML = data.html;

            if (actionType === 'send_email') {
                initializeTinyMCE();
                updateShortcodeList();
            }
        } catch (error) {
            console.error('Errore nel caricamento dei parametri dell\'azione:', error);
            paramsContainer.innerHTML = '<div class="col-12"><p class="text-danger">Errore nel caricamento dei parametri.</p></div>';
        }
    }

    // Event Listeners
    modelSelector.addEventListener('change', () => {
        updateFields(null, () => updateOperators(null));
        if (actionSelector.value === 'send_email') {
            updateShortcodeList();
        }
    });
    fieldSelector.addEventListener('change', () => updateOperators(null));
    actionSelector.addEventListener('change', renderActionParams);

    // Inizializzazione
    if (modelSelector.value) {
        updateFields(savedData.field, () => updateOperators(savedData.operator));
    }
    renderActionParams();
});
</script>
@endpush
