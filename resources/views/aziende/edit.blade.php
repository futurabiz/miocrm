@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifica Azienda: {{ $company->name }}</h1>
        <a class="btn btn-secondary" href="{{ route('companies.index') }}">Indietro</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mt-2">
            <strong>Attenzione!</strong> Correggi i seguenti errori:
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('companies.update', $company->id) }}" method="POST"
          data-address-form="true"
          data-region-id="{{ $company->province->region_id ?? '' }}"
          data-province-id="{{ $company->province_id ?? '' }}"
          data-city-id="{{ $company->city_id ?? '' }}"
          data-postal-code-id="{{ $company->postal_code_id ?? '' }}">
        @csrf
        @method('PUT')
        
        @foreach ($blocks as $block)
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">{{ $block->name }}</h6></div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($block->fields as $field)
                            <x-form-field :field="$field" :model="$company" :users="$users ?? []" />
                        @endforeach

                        @if (str_contains($block->name, 'Località'))
                            @include('partials._address_fields', ['modelInstance' => $company])
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Aggiorna Azienda</button>
            <a href="{{ route('companies.show', $company->id) }}" class="btn btn-secondary">Annulla</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/address-logic.js') }}"></script>
<script>
$(document).ready(function() {
    function initializeSelect2(selector) {
        if (!$(selector).length) return;
        $(selector).select2({
            theme: "bootstrap-5",
            placeholder: $(selector).data('placeholder'),
            ajax: { 
                url: $(selector).data('ajax-url'), 
                dataType: 'json', 
                delay: 250, 
                processResults: r => ({results: r.results || r}) 
            }
        });
    }
    initializeSelect2('select[data-ajax-url]');
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush