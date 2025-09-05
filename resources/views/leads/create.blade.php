@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Crea Nuovo Lead</h1>
        <a href="{{ route('leads.index') }}" class="btn btn-secondary">Annulla</a>
    </div>
    <form action="{{ route('leads.store') }}" method="POST" data-address-form="true">
        @csrf

        <x-form-renderer :blocks="$blocks" :users="$users" />
        
        <div class="text-center mt-4 mb-4">
            <button type="submit" class="btn btn-primary">Salva Lead</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/address-logic.js') }}"></script>
{{-- Aggiungeremo uno script per inizializzare i Select2 --}}
@endpush
