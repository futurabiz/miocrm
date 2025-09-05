@extends('layouts.entity_show', ['model' => $opportunity, 'timelineItems' => $timelineItems])

@section('entity_header_title')
    <h1 class="h3 mb-0 text-gray-800">Opportunità: {{ $opportunity->name }}</h1>
    <p class="mb-0 text-muted">
        Associata a: 
        @if($opportunity->company)
            <a href="{{ route('companies.show', $opportunity->company->id) }}">{{ $opportunity->company->name }}</a>
        @elseif($opportunity->contact)
            <a href="{{ route('contacts.show', $opportunity->contact->id) }}">{{ $opportunity->contact->getFullNameAttribute() }}</a>
        @endif
    </p>
@endsection

@section('entity_header_actions')
    <a href="{{ route('opportunities.edit', $opportunity->id) }}" class="btn btn-primary">Modifica</a>
    <a href="{{ route('opportunities.index') }}" class="btn btn-secondary">Torna alla Lista</a>
@endsection

@section('left_column_title', 'Dettagli Opportunità')

@section('left_column_content')
    {{-- Includiamo il partial _details specifico per le opportunità --}}
    @include('opportunita.partials._details', ['opportunity' => $opportunity])
@endsection

@section('right_column_title', 'Relazioni Collegate')

@section('right_column_content')
    {{-- Blocco Utente Assegnatario --}}
    @if($opportunity->assignedTo)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Assegnata a</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $opportunity->assignedTo->name }}</p>
                <p class="mb-0 text-muted">{{ $opportunity->assignedTo->email }}</p>
            </div>
        </div>
    @else
        <p class="text-muted">Nessun utente assegnato.</p>
    @endif

    {{-- Blocco Azienda Collegata --}}
    @if($opportunity->company)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Azienda Collegata</h6>
            </div>
            <div class="card-body">
                <h5><a href="{{ route('companies.show', $opportunity->company->id) }}">{{ $opportunity->company->name }}</a></h5>
                <p class="mb-1"><i class="bi bi-telephone-fill me-2"></i>{{ $opportunity->company->phone ?? 'N/D' }}</p>
                <p class="mb-0"><i class="bi bi-envelope-fill me-2"></i>{{ $opportunity->company->email ?? 'N/D' }}</p>
            </div>
        </div>
    @else
        <p class="text-muted">Nessuna azienda collegata.</p>
    @endif

    {{-- Blocco Contatto di Riferimento --}}
    @if($opportunity->contact)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Contatto di Riferimento</h6>
            </div>
            <div class="card-body">
                <h5><a href="{{ route('contacts.show', $opportunity->contact->id) }}">{{ $opportunity->contact->getFullNameAttribute() }}</a></h5>
                <p class="mb-1"><i class="bi bi-telephone-fill me-2"></i>{{ $opportunity->contact->phone ?? 'N/D' }}</p>
                <p class="mb-0"><i class="bi bi-envelope-fill me-2"></i>{{ $opportunity->contact->email ?? 'N/D' }}</p>
            </div>
        </div>
    @else
        <p class="text-muted">Nessun contatto di riferimento.</p>
    @endif

    {{-- Blocco Servizi/Prodotti (elenco attuale e pulsante per aggiungere) --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Servizi/Prodotti</h6>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                <i class="bi bi-plus-lg"></i> Aggiungi
            </button>
        </div>
        <div class="card-body">
            @if($opportunity->serviceTypes->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach($opportunity->serviceTypes as $service)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $service->name }} (x{{ $service->pivot->quantity }} - €{{ number_format($service->pivot->price, 2, ',', '.') }})
                            @if($service->pivot->discount > 0)
                                <span class="badge bg-warning text-dark me-2">-{{ $service->pivot->discount }}%</span>
                            @endif
                            <form action="{{ route('opportunities.detachService', [$opportunity->id, $service->id]) }}" method="POST" onsubmit="return confirm('Rimuovere questo servizio?');" class="ms-auto">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-x-lg"></i></button>
                            </form>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3 fw-bold">Valore Totale Servizi: € {{ number_format($opportunity->serviceTypes->sum(function($s){ return $s->pivot->quantity * $s->pivot->price * (1 - ($s->pivot->discount ?? 0) / 100); }), 2, ',', '.') }}</p>
            @else
                <p class="text-muted">Nessun servizio/prodotto associato.</p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Includiamo qui la modale per aggiungere servizi e il suo script.
         Assicurati che 'partials.service_modal_add' riceva 'customer' (che qui è $opportunity) e 'serviceTypes' (che qui è $allServices).
    --}}
    @include('partials.service_modal_add', ['customer' => $opportunity, 'serviceTypes' => $allServices])
    {{-- @include('partials.service_modal_edit') Se hai una modale per la modifica --}}
    @include('partials.service_modal_script')
@endpush