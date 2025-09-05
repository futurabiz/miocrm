@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifica Opportunità: {{ $opportunity->name }}</h1>
        <a class="btn btn-secondary" href="{{ route('opportunities.show', $opportunity->id) }}">Indietro</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mt-2">
            <strong>Attenzione!</strong> Correggi i seguenti errori:
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('opportunities.update', $opportunity->id) }}" method="POST">
        @csrf
        @method('PUT')
        @php
            $assignedToField = null;
            foreach ($blocks as $block) {
                $fieldKey = $block->fields->search(fn($field) => $field->name === 'assigned_to_id');
                if ($fieldKey !== false) {
                    $assignedToField = $block->fields->pull($fieldKey);
                    break;
                }
            }
        @endphp

        @foreach ($blocks as $block)
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">{{ $block->name }}</h6></div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($block->fields->sortBy('order') as $field)
                            <div class="col-md-6 mb-3">
                                <label for="field-{{ $field->name }}" class="form-label">
                                    <strong>{{ $field->label }}:</strong>
                                    @if($field->is_required) <span class="text-danger">*</span> @endif
                                </label>
                                @php
                                    $inputName = $field->is_standard ? $field->name : "custom_fields_data[{$field->name}]";
                                    $currentValue = old(str_replace(['[', ']'], ['.', ''], $inputName), $field->is_standard ? $opportunity->{$field->name} : ($opportunity->custom_fields_data[$field->name] ?? null));
                                @endphp

                                @if ($field->type === 'select_ajax')
                                    <div class="input-group">
                                        <select name="{{ $inputName }}" id="field-{{ $field->name }}" class="form-select">
                                            @if($field->name === 'company_id' && $opportunity->company)
                                                <option value="{{ $opportunity->company->id }}" selected>{{ $opportunity->company->name }}</option>
                                            @elseif($field->name === 'contact_id' && $opportunity->contact)
                                                 <option value="{{ $opportunity->contact->id }}" selected>{{ $opportunity->contact->full_name }}</option>
                                            @endif
                                        </select>
                                        @if($field->name === 'company_id')
                                            <a href="{{ route('companies.create') }}" target="_blank" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Crea Nuova Azienda"><i class="bi bi-plus-lg"></i></a>
                                        @elseif($field->name === 'contact_id')
                                            <a href="{{ route('contacts.create') }}" target="_blank" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Crea Nuovo Contatto"><i class="bi bi-plus-lg"></i></a>
                                        @endif
                                    </div>
                                @elseif ($field->type === 'select')
                                    <select name="{{ $inputName }}" id="field-{{ $field->name }}" class="form-select">
                                        <option value="">-- Seleziona --</option>
                                        @php $options = is_string($field->options) ? json_decode($field->options, true) : ($field->options ?? []); @endphp
                                        @foreach ($options as $value => $label)
                                            <option value="{{ $value }}" @selected($currentValue == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field->type === 'textarea')
                                    <textarea name="{{ $inputName }}" id="field-{{ $field->name }}" class="form-control">{{ $currentValue }}</textarea>
                                @else
                                    <input type="{{ $field->type }}" name="{{ $inputName }}" id="field-{{ $field->name }}" class="form-control" value="{{ $currentValue instanceof \Carbon\Carbon ? $currentValue->format('Y-m-d') : $currentValue }}">
                                @endif
                            </div>
                        @endforeach

                        @if ($loop->first && $assignedToField)
                            <div class="col-md-6 mb-3">
                                <label for="field-assigned_to_id" class="form-label"><strong>{{ $assignedToField->label }}:</strong></label>
                                <select name="assigned_to_id" id="field-assigned_to_id" class="form-select">
                                    @php $currentAssignee = old('assigned_to_id', $opportunity->assigned_to_id); @endphp
                                    <option value="">-- Seleziona Utente --</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected($currentAssignee == $user->id)>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        <div class="text-center mt-4 mb-4">
            <button type="submit" class="btn btn-primary">Aggiorna Opportunità</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#field-company_id').select2({
        theme: "bootstrap-5",
        placeholder: "Cerca un'azienda...",
        ajax: { url: "{{ route('api.companies.search') }}", dataType: 'json', delay: 250, processResults: r => ({results: r.results || r}) }
    });
    $('#field-contact_id').select2({
        theme: "bootstrap-5",
        placeholder: "Cerca un contatto...",
        ajax: { url: "{{ route('api.contacts.search') }}", dataType: 'json', delay: 250, processResults: r => ({results: r.results || r}) }
    });
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush