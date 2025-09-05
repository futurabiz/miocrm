@extends('layouts.entity_show', ['model' => $contact, 'timelineItems' => $timelineItems])

@section('entity_header_title')
    <h1 class="h3 mb-0 text-gray-800">Contatto: {{ $contact->full_name }}</h1>
@endsection

@section('entity_header_actions')
    <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-primary">Modifica</a>
    <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Torna alla Lista</a>
@endsection

@section('left_column_title', 'Dettagli Contatto')

@section('left_column_content')
    {{-- Includiamo il partial _details specifico per i contatti.
        Assicurati che questo partial si trovi in `resources/views/contatti/partials/_details.blade.php`
        e che usi la variabile `$contatto` al suo interno.
    --}}
    @include('contatti.partials._details', ['contatto' => $contact])
@endsection

@section('right_column_title', 'Relazioni Collegate')

@section('right_column_content')
    {{-- Blocco Azienda collegata --}}
    @if($contact->company)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Azienda Collegata</h6>
            </div>
            <div class="card-body">
                <h5><a href="{{ route('companies.show', $contact->company->id) }}">{{ $contact->company->name }}</a></h5>
                <p class="mb-1"><i class="bi bi-telephone-fill me-2"></i>{{ $contact->company->phone ?? 'N/D' }}</p>
                <p class="mb-0"><i class="bi bi-envelope-fill me-2"></i>{{ $contact->company->email ?? 'N/D' }}</p>
            </div>
        </div>
    @else
        <p class="text-muted">Nessuna azienda collegata.</p>
    @endif

    {{-- Blocco Opportunità collegate --}}
    @if($contact->opportunities->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Opportunità Collegate</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($contact->opportunities as $opportunity)
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

    {{-- Blocco Liste Email Collegate --}}
    @if($contact->emailLists->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Liste Email</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($contact->emailLists as $emailList)
                        <li class="list-group-item">{{ $emailList->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Blocco Tag --}}
    @if($contact->tags->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tag</h6>
            </div>
            <div class="card-body">
                @foreach($contact->tags as $tag)
                    <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                @endforeach
            </div>
        </div>
    @endif
@endsection