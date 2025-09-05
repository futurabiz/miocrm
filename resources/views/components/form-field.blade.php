@php
    // Determina il nome dell'input per il form
    $inputName = $field->is_standard ? $field->name : "custom_fields_data[{$field->name}]";
    
    // Determina il valore corrente del campo
    $value = old(str_replace(['[', ']'], ['.', ''], $inputName));
    if ($value === null && isset($model)) {
        $value = $field->is_standard ? $model->{$field->name} : ($model->custom_fields_data[$field->name] ?? null);
    }
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
            <select class="form-select" id="field-{{ $field->name }}" name="{{ $inputName }}">
                <option value="">-- Seleziona --</option>
                @php $options = is_string($field->options) ? json_decode($field->options, true) : ($field->options ?? []); @endphp
                @foreach ($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected((string)$value === (string)$optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>
            @break

        @case('related_user')
            <select class="form-select" id="field-{{ $field->name }}" name="{{ $inputName }}">
                <option value="">-- Seleziona Utente --</option>
                @foreach($users ?? [] as $user)
                    <option value="{{ $user->id }}" @selected($value == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            @break

        @case('related_company')
        @case('related_contact')
        @case('related_city')
            @php
                $relationName = \Illuminate\Support\Str::camel(str_replace('_id', '', $field->name));
                $relatedModel = isset($model) && $model->{$relationName} ? $model->{$relationName} : null;
                
                $ajaxRoute = '';
                $placeholder = '-- Cerca --';
                if ($field->type === 'related_company') {
                    $ajaxRoute = route('api.companies.search');
                    $placeholder = '-- Cerca Azienda --';
                } elseif ($field->type === 'related_contact') {
                    // --- MODIFICA QUI ---
                    $ajaxRoute = route('api.contacts.search'); 
                    $placeholder = '-- Cerca Contatto --';
                } elseif ($field->type === 'related_city') {
                    $ajaxRoute = route('api.cities.search');
                    $placeholder = '-- Cerca Comune di Nascita --';
                }
            @endphp
            <select name="{{ $inputName }}" id="field-{{ $field->name }}" class="form-select" data-ajax-url="{{ $ajaxRoute }}" data-placeholder="{{ $placeholder }}">
                @if ($relatedModel)
                    @php 
                        $optionValue = ($field->name === 'city_code') ? $relatedModel->fiscal_code : $relatedModel->id;
                        $optionText = $relatedModel->name ?? $relatedModel->full_name ?? ($relatedModel->first_name . ' ' . $relatedModel->last_name);
                    @endphp
                    <option value="{{ $optionValue }}" selected>{{ $optionText }}</option>
                @endif
            </select>
            @break

        @default
            <input type="text" class="form-control" id="field-{{ $field->name }}" name="{{ $inputName }}" value="{{ $value }}">
    @endswitch
</div>