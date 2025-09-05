@extends('layouts.entity_show', ['model' => $lead, 'timelineItems' => $timelineItems])

@section('entity_header_title')
    <h1 class="h3 mb-0 text-gray-800">{{ $lead->first_name }} {{ $lead->last_name }}</h1>
@endsection

@section('entity_header_actions')
    <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-primary">Modifica</a>
    <a href="{{ route('leads.index') }}" class="btn btn-secondary">Torna alla Lista</a>
@endsection

@section('left_column_title', 'Dettagli Lead')

@section('left_column_content')
    {{-- Questo partial mostra i dettagli base specifici del Lead. --}}
    @include('leads.partials._details', ['lead' => $lead])
@endsection

@section('right_column_title', 'Azienda Collegata')

@section('right_column_content')
    @if($lead->company)
        <h5><a href="{{ route('companies.show', $lead->company->id) }}">{{ $lead->company->name }}</a></h5>
        <p class="mb-1"><i class="bi bi-telephone-fill me-2"></i>{{ $lead->company->phone ?? 'N/D' }}</p>
        <p class="mb-0"><i class="bi bi-envelope-fill me-2"></i>{{ $lead->company->email ?? 'N/D' }}</p>
    @else
        <p class="text-muted">Nessuna azienda collegata.</p>
    @endif
@endsection