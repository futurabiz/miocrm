{{--
================================================================================
    File: resources/views/leads/partials/_form_field.blade.php
    Descrizione: Partial definitivo per il rendering dei campi del modulo Lead.
                 Unisce la logica a switch con i dettagli UI del backup.
================================================================================
--}}
@php
    $inputName = $field->is_standard ? $field->name : "custom_fields_data[{$field->name}]";
    $value = old($inputName, isset($model) ? ($field->is_standard ? $model->{$field->name} : ($model->custom_fields_data[$field->name] ?? null)) : null);
@endphp

<div class="col-md-6 mb-3">
    <label for="field-{{ $field->name }}" class="form-label">
        <strong>{{ $field->label }}</strong>
        @if($field->is_required)<span class="text-danger">*</span>@endif
    </label>

    @switch($field->type)

        @case('text')
        @case('number')
        @case('email')
        @case('password')
        @case('tel')
            <input type="{{ $field->type }}" class="form-control" id="field-{{ $field->name }}" name="{{ $inputName }}" value="{{ $value }}">
            @break

        @case('date')
            <input type="date" class="form-control" id="field-{{ $field->name }}" name="{{ $inputName }}" value="{{ $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '' }}">
            @break

        @case('textarea')
            <textarea class="form-control" id="field-{{ $field->name }}" name="{{ $inputName }}" rows="3">{{ $value }}</textarea>
            @break

        @case('select')
            @php
                $options = [];
                if ($field->name === 'status' && isset($leadStages)) { $options = $leadStages; }
                elseif ($field->name === 'source' && isset($leadSources)) { $options = $leadSources; }
                elseif ($field->name === 'role' && isset($roles)) { $options = $roles; }
                elseif ($field->options) { $options = is_array($field->options) ? $field->options : (json_decode($field->options, true) ?? []); }
            @endphp
            <select class="form-select" id="field-{{ $field->name }}" name="{{ $inputName }}">
                <option value="">-- Seleziona --</option>
                @foreach ($options as $optionValue => $optionLabel)
                    @php $val = is_int($optionValue) ? $optionLabel : $optionValue; @endphp
                    <option value="{{ $val }}" @selected((string)$value === (string)$val)>{{ $optionLabel }}</option>
                @endforeach
            </select>
            @break

        @case('related')
            @if ($field->name === 'company_id')
                <div class="input-group">
                    <select class="form-control" id="field-{{ $field->name }}" name="{{ $inputName }}" data-type="companies" style="width: 85%;">
                         @if (isset($model) && $model->company)
                            <option value="{{ $model->company->id }}" selected>{{ $model->company->name }}</option>
                        @endif
                    </select>
                    <a href="{{ route('companies.create') }}" target="_blank" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Crea Nuova Azienda"><i class="bi bi-plus-lg"></i></a>
                </div>
            @elseif ($field->name === 'assigned_to_id')
                <select class="form-select" id="field-{{ $field->name }}" name="{{ $inputName }}">
                    <option value="">-- Seleziona Utente --</option>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}" @selected($value == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            @endif
            @break

        @case('related_city')
        @case('related_municipality')
        @case('related_street')
            <select class="form-control" id="field-{{ $field->name }}" name="{{ $inputName }}" style="width: 100%;">
                 @if(isset($model) && $value)
                    {{-- Il valore iniziale viene renderizzato qui, Select2 lo userà per la visualizzazione iniziale --}}
                    <option value="{{ $value }}" selected>{{-- Il testo verrà caricato da Select2 --}}</option>
                 @endif
            </select>
            @break

        @case('text_readonly')
            <input type="text" class="form-control" id="field-{{ $field->name }}" name="{{ $inputName }}" value="{{ $value }}" readonly>
            @break

        @default
            <input type="text" class="form-control" id="field-{{ $field->name }}" name="{{ $inputName }}" value="{{ $value }}">

    @endswitch
</div>