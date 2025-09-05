@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Modifica Lead: {{ $lead->first_name }} {{ $lead->last_name }}
        </h1>
        <a class="btn btn-secondary" href="{{ route('leads.show', $lead->id) }}">Indietro</a>
    </div>
    <form action="{{ route('leads.update', $lead->id) }}" method="POST" 
          id="lead-edit-form"
          data-address-form="true"
          data-region-id="{{ $lead->province->region_id ?? '' }}"
          data-province-id="{{ $lead->province_id ?? '' }}"
          data-city-id="{{ $lead->city_id ?? '' }}"
          data-postal-code-id="{{ $lead->postal_code_id ?? '' }}">
        @csrf
        @method('PUT')
        
        <x-form-renderer :blocks="$blocks" :model="$lead" :users="$users" />
        <div class="text-center mt-4 mb-4">
            <button type="submit" class="btn btn-primary">Aggiorna Lead</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/address-logic.js') }}"></script>
{{-- Aggiungeremo uno script per inizializzare i Select2 e i valori iniziali --}}
@endpush
