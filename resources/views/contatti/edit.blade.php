@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifica Contatto: {{ $contact->full_name }}</h1>
        <a class="btn btn-secondary" href="{{ route('contacts.index') }}">Indietro</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mt-2">
            <strong>Attenzione!</strong> Correggi i seguenti errori:
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('contacts.update', $contact->id) }}" method="POST"
          data-address-form="true"
          data-region-id="{{ $contact->province->region_id ?? '' }}"
          data-province-id="{{ $contact->province_id ?? '' }}"
          data-city-id="{{ $contact->city_id ?? '' }}"
          data-postal-code-id="{{ $contact->postal_code_id ?? '' }}">
        @csrf
        @method('PUT')
        
        @foreach ($blocks as $block)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $block->name }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($block->fields as $field)
                            <x-form-field :field="$field" :model="$contact" :users="$users ?? []" />
                        @endforeach

                        {{-- --- CORREZIONE QUI --- --}}
                        {{-- Controlliamo se il nome del blocco CONTIENE la parola 'Località' --}}
                        @if (str_contains($block->name, 'Località'))
                            @include('partials._address_fields', ['modelInstance' => $contact])
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="text-center mt-4 mb-4">
            <button type="submit" class="btn btn-primary">Aggiorna Contatto</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- Script rimangono invariati --}}
<script src="{{ asset('js/address-logic.js') }}"></script>
<script>
$(document).ready(function() {
    function initializeSelect2(selector, ajaxUrl, placeholder) {
        if (!$(selector).length) return;
        $(selector).select2({
            theme: "bootstrap-5",
            placeholder: placeholder,
            ajax: { url: ajaxUrl, dataType: 'json', delay: 250, processResults: r => ({results: r.results || r}) }
        });
    }
    initializeSelect2('select[data-ajax-url]', $('select[data-ajax-url]').data('ajax-url'), $('select[data-ajax-url]').data('placeholder'));
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush