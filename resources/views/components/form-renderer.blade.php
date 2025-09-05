@props(['blocks', 'model' => null, 'users' => []])

@foreach ($blocks as $block)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $block->name }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Logica speciale per il blocco Località, che non ha campi nel DB --}}
                @if ($block->name === 'Località')
                    @include('partials._address_fields', ['modelInstance' => $model])
                @else
                {{-- Logica per tutti gli altri blocchi che leggono i campi dal DB --}}
                    @foreach ($block->fields->sortBy('order') as $field)
                        <div class="col-md-6 mb-3">
                            <label for="field-{{ $field->name }}" class="form-label">
                                <strong>{{ $field->label }}:</strong>
                                @if($field->is_required) <span class="text-danger">*</span> @endif
                            </label>
                            @php
                                $inputName = $field->is_standard ? $field->name : "custom_fields_data[{$field->name}]";
                                $currentValue = old(str_replace(['[', ']'], ['.', ''], $inputName), $model ? ($field->is_standard ? $model->{$field->name} : ($model->custom_fields_data[$field->name] ?? null)) : null);
                            @endphp

                            {{-- SWITCH INTELLIGENTE PER VISUALIZZARE IL CAMPO CORRETTO --}}
                            @switch($field->type)
                                @case('textarea')
                                    <textarea name="{{ $inputName }}" id="field-{{ $field->name }}" class="form-control">{{ $currentValue }}</textarea>
                                    @break

                                @case('select')
                                    <select name="{{ $inputName }}" id="field-{{ $field->name }}" class="form-select">
                                        <option value="">-- Seleziona --</option>
                                        @php $options = is_string($field->options) ? json_decode($field->options, true) : ($field->options ?? []); @endphp
                                        @foreach ($options as $value => $label)
                                            <option value="{{ $value }}" @selected($currentValue == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('related_user')
                                    <select name="{{ $inputName }}" id="field-{{ $field->name }}" class="form-select">
                                        <option value="">-- Seleziona Utente --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" @selected($currentValue == $user->id)>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                
                                @case('related_company')
                                @case('related_contact')
                                @case('related_city')
                                    <select name="{{ $inputName }}" id="field-{{ $field->name }}" class="form-select generic-select2" data-type="{{ str_replace('related_', '', $field->type) }}">
                                        @if($currentValue)
                                            {{-- Il valore iniziale viene recuperato via JS --}}
                                            <option value="{{ $currentValue }}" selected>Caricamento...</option>
                                        @endif
                                    </select>
                                    @break

                                @default
                                    {{-- Gestisce text, number, date, tel, email, etc. --}}
                                    <input type="{{ $field->type }}" name="{{ $inputName }}" id="field-{{ $field->name }}" class="form-control" value="{{ $currentValue instanceof \Carbon\Carbon ? $currentValue->format('Y-m-d') : $currentValue }}">
                            @endswitch
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endforeach