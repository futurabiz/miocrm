@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Importazione Dati</h1>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Passo 1: Seleziona il file e il modulo di destinazione</h6>
        </div>
        <div class="card-body">
            <p class="text-muted">Carica un file in formato CSV. La prima riga del file deve contenere le intestazioni delle colonne (es. "Nome", "Cognome", "Email").</p>
            
            <form action="{{ route('import.map') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="module_class" class="form-label"><strong>Importa in:</strong></label>
                        <select name="module_class" id="module_class" class="form-select" required>
                            <option value="" selected disabled>-- Seleziona un modulo --</option>
                            <option value="App\Models\Lead">Lead</option>
                            <option value="App\Models\Contact">Contatti</option>
                            <option value="App\Models\Company">Aziende</option>
                            {{-- Aggiungi qui altri moduli se necessario --}}
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="import_file" class="form-label"><strong>Seleziona File CSV:</strong></label>
                        <input type="file" name="import_file" id="import_file" class="form-control" accept=".csv" required>
                    </div>
                </div>

                <div class="mt-3 border-top pt-3">
                    <button type="submit" class="btn btn-primary">Procedi alla Mappatura →</button>
                </div>
            </form>
        </div>
    </div>
@endsection