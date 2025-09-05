@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Importazione Dati</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Passo 2: Mappatura dei Campi</h6>
        </div>
        <div class="card-body">
            <p>Abbina le colonne del tuo file CSV (a destra) con i campi corrispondenti del CRM (a sinistra). I campi con l'asterisco (*) sono obbligatori.</p>
            
            {{-- Questo form punterà alla rotta finale che avvierà l'importazione in background --}}
            <form action="{{ route('import.process') }}" method="POST">
            {{-- <form action="{{ route('import.process') }}" method="POST"> --}}
                @csrf
                <input type="hidden" name="file_path" value="{{ $filePath }}">
                <input type="hidden" name="module_class" value="{{ $moduleClass }}">

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Campo CRM</th>
                                <th>Colonna del Tuo File CSV</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($crmFields as $field)
                                <tr>
                                    <td class="align-middle">
                                        {{ $field->label }}
                                        @if($field->is_required) <span class="text-danger">*</span> @endif
                                    </td>
                                    <td>
                                        <select name="field_map[{{ $field->name }}]" class="form-select">
                                            <option value="">-- Ignora questo campo --</option>
                                            @foreach($csvHeadings as $heading)
                                                {{-- Tentativo di pre-selezione intelligente --}}
                                                @php
                                                    $normalizedField = strtolower(str_replace('_', '', $field->name));
                                                    $normalizedHeading = strtolower(str_replace(['_', ' '], '', $heading));
                                                @endphp
                                                <option value="{{ $heading }}" @if($normalizedField === $normalizedHeading) selected @endif>
                                                    {{ $heading }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('import.create') }}" class="btn btn-secondary">← Torna Indietro</a>
                    <button type="submit" class="btn btn-success">Conferma e Avvia Importazione</button>
                </div>
            </form>
        </div>
    </div>
@endsection