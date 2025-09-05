@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Gestione Workflow</h2>
                <a class="btn btn-success" href="{{ route('workflows.create') }}"> Crea Nuovo Workflow</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success mt-2 alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mt-3">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nome Workflow</th>
                        <th>Modulo Trigger</th>
                        <th>Stato</th>
                        <th width="150px">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workflows as $workflow)
                    <tr>
                        <td>{{ $workflow->name }}</td>
                        <td>
                            {{-- 
                                MODIFICA APPLICATA QUI: 
                                Invece di chiamare una funzione inesistente, leggiamo direttamente
                                dal file di configurazione. Se non trova una traduzione, mostra
                                il nome della classe come fallback.
                            --}}
                            {{ config('workflows.available_models.' . $workflow->trigger_model) ?? basename($workflow->trigger_model) }}
                        </td>
                        <td>
                            @if($workflow->is_active)
                                <span class="badge bg-success">Attivo</span>
                            @else
                                <span class="badge bg-secondary">Non Attivo</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <div class="btn-group" role="group">
                                <a href="{{ route('workflows.edit', $workflow->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifica">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('workflows.destroy', $workflow->id) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo workflow?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Elimina">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Nessun workflow trovato.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($workflows->hasPages())
        <div class="card-footer">
            {!! $workflows->links() !!}
        </div>
        @endif
    </div>
@endsection