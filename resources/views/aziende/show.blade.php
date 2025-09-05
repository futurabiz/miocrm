@extends('layouts.entity_show', ['model' => $company, 'timelineItems' => $timelineItems])

@section('entity_header_title')
    <h1 class="h3 mb-0 text-gray-800">Azienda: {{ $company->name }}</h1>
@endsection

@section('entity_header_actions')
    <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-primary">Modifica</a>
    <a href="{{ route('companies.index') }}" class="btn btn-secondary">Torna alla Lista</a>
@endsection

@section('left_column_title', 'Dettagli Azienda')

@section('left_column_content')
    {{-- Includiamo il partial _details specifico per le aziende --}}
    @include('aziende.partials._details', ['company' => $company])
@endsection

@section('right_column_title', 'Relazioni Collegate')

@section('right_column_content')
    {{-- Blocco Contatti Collegati --}}
    @if($company->contacts->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Contatti Collegati</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($company->contacts as $contact)
                        <li class="list-group-item">
                            <a href="{{ route('contacts.show', $contact->id) }}">{{ $contact->full_name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <p class="text-muted">Nessun contatto collegato.</p>
    @endif

    {{-- Blocco Opportunità Collegate --}}
    @if($company->opportunities->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Opportunità Collegate</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($company->opportunities as $opportunity)
                        <li class="list-group-item">
                            <a href="{{ route('opportunities.show', $opportunity->id) }}">{{ $opportunity->name }}</a> ({{ $opportunity->stage }})
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <p class="text-muted">Nessuna opportunità collegata.</p>
    @endif

    {{-- Blocco Servizi Cliente Collegate (se ne hai) --}}
    @if($company->customerServices->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Servizi Cliente</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($company->customerServices as $service)
                        <li class="list-group-item">
                            {{ $service->serviceType->name ?? 'Servizio Sconosciuto' }} (Stato: {{ $service->status }})
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <p class="text-muted">Nessun servizio cliente collegato.</p>
    @endif

    {{-- Se vuoi la modale per aggiungere servizi qui, i pulsanti devono essere nell'header o in un box dedicato --}}
    {{-- Esempio di pulsante per aggiungere un servizio in un box --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Gestione Servizi</h6>
        </div>
        <div class="card-body text-center">
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                <i class="bi bi-plus-lg me-1"></i> Aggiungi Servizio
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Includiamo qui la modale per aggiungere servizi e il suo script.
         Assicurati che 'partials.service_modal_add' riceva 'customer' (che qui è $company) e 'serviceTypes'.
         E che 'partials.service_modal_script' gestisca correttamente l'aggiunta.
    --}}
    @include('partials.service_modal_add', ['customer' => $company, 'serviceTypes' => $serviceTypes])
    @include('partials.service_modal_edit')
    @include('partials.service_modal_script')
@endpush