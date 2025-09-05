@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Modifica Tipo di Servizio</h2>
            <a class="btn btn-primary" href="{{ route('service_types.index') }}"> Indietro</a>
        </div>
    </div>
</div>
@if ($errors->any())
    <div class="alert alert-danger mt-2">
        <strong>Ops!</strong> Ci sono stati problemi con i dati inseriti.<br><br>
        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
@endif
<form action="{{ route('service_types.update', $serviceType->id) }}" method="POST" class="mt-3">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="name" class="form-label"><strong>Nome del Tipo Servizio:</strong></label>
            <input type="text" name="name" class="form-control" value="{{ $serviceType->name }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="description" class="form-label"><strong>Descrizione:</strong></label>
            <input type="text" name="description" class="form-control" value="{{ $serviceType->description }}">
        </div>
    </div>
    <hr>
    <h4>Costruttore Campi Personalizzati</h4>
    <div id="fields-container">
        @if($serviceType->fields_schema)
            @foreach($serviceType->fields_schema as $index => $field)
            <div class="row align-items-end mb-3 border p-3 rounded field-row">
                <div class="col-md-3">
                    <label class="form-label">Nome Campo (senza spazi)</label>
                    <input type="text" name="fields[{{ $index }}][name]" class="form-control" value="{{ $field['name'] }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Etichetta (visualizzata)</label>
                    <input type="text" name="fields[{{ $index }}][label]" class="form-control" value="{{ $field['label'] }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo di Campo</label>
                    <select name="fields[{{ $index }}][type]" class="form-select field-type-selector" required>
                        <option value="text" @selected($field['type'] == 'text')>Testo</option>
                        <option value="number" @selected($field['type'] == 'number')>Numero</option>
                        <option value="date" @selected($field['type'] == 'date')>Data</option>
                        <option value="textarea" @selected($field['type'] == 'textarea')>Area di Testo</option>
                        <option value="options" @selected($field['type'] == 'options')>Opzioni (select)</option>
                        <option value="checkbox" @selected($field['type'] == 'checkbox')>Checkbox (Sì/No)</option>
                    </select>
                </div>
                <div class="col-md-2 options-input-container" style="display: none;">
                    <label class="form-label">Opzioni (divise da virgola)</label>
                    <input type="text" name="fields[{{ $index }}][options]" class="form-control" value="{{ $field['options'] ?? '' }}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-field-btn">X</button>
                </div>
            </div>
            @endforeach
        @endif
    </div>
    <button type="button" id="add-field-btn" class="btn btn-secondary mt-2">Aggiungi Campo</button>
    <div class="col-md-12 text-center mt-4">
        <button type="submit" class="btn btn-primary">Salva Modifiche</button>
    </div>
</form>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('fields-container');
    const addBtn = document.getElementById('add-field-btn');
    let fieldIndex = {{ count($serviceType->fields_schema ?? []) }};
    function handleFieldTypeChange(selectElement) {
        const row = selectElement.closest('.field-row');
        const optionsContainer = row.querySelector('.options-input-container');
        const optionsInput = optionsContainer.querySelector('input');
        if (selectElement.value === 'options') {
            optionsContainer.style.display = 'block';
            optionsInput.required = true;
        } else {
            optionsContainer.style.display = 'none';
            optionsInput.required = false;
        }
    }
    container.querySelectorAll('.field-type-selector').forEach(handleFieldTypeChange);
    addBtn.addEventListener('click', function () {
        const fieldHtml = `
            <div class="row align-items-end mb-3 border p-3 rounded field-row">
                <div class="col-md-3">
                    <label class="form-label">Nome Campo (senza spazi)</label>
                    <input type="text" name="fields[${fieldIndex}][name]" class="form-control" placeholder="es_nome_campo" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Etichetta (visualizzata)</label>
                    <input type="text" name="fields[${fieldIndex}][label]" class="form-control" placeholder="Es. Nome Campo" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo di Campo</label>
                    <select name="fields[${fieldIndex}][type]" class="form-select field-type-selector" required>
                        <option value="text">Testo</option>
                        <option value="number">Numero</option>
                        <option value="date">Data</option>
                        <option value="textarea">Area di Testo</option>
                        <option value="options">Opzioni (select)</option>
                        <option value="checkbox">Checkbox (Sì/No)</option>
                    </select>
                </div>
                <div class="col-md-2 options-input-container" style="display: none;">
                    <label class="form-label">Opzioni (divise da virgola)</label>
                    <input type="text" name="fields[${fieldIndex}][options]" class="form-control" placeholder="A,B,C">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-field-btn">X</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', fieldHtml);
        const newSelect = container.lastElementChild.querySelector('.field-type-selector');
        handleFieldTypeChange(newSelect);
        fieldIndex++;
    });
    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-field-btn')) {
            e.target.closest('.row').remove();
        }
    });
    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('field-type-selector')) {
            handleFieldTypeChange(e.target);
        }
    });
});
</script>
@endpush
