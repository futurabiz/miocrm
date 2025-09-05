@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Intestazione con Nome e pulsanti di azione --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            {{-- Placeholder per il titolo specifico dell'entità --}}
            @yield('entity_header_title') 
        </div>
        <div>
            {{-- Placeholder per i pulsanti di azione specifici dell'entità (es. Modifica, Torna alla Lista) --}}
            @yield('entity_header_actions')
        </div>
    </div>

    {{-- Layout a 3 colonne --}}
    <div class="row">
        
        {{-- COLONNA DI SINISTRA: Dettagli dell'Entità --}}
        <div class="col-lg-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">@yield('left_column_title', 'Dettagli')</h6>
                </div>
                <div class="card-body">
                    {{-- Placeholder per i dettagli specifici (es. leads.partials._details) --}}
                    @yield('left_column_content')
                </div>
            </div>
        </div>

        {{-- COLONNA CENTRALE: Timeline Attività --}}
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                 <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Timeline Attività</h6>
                    {{-- Questo yield è opzionale per pulsanti specifici della timeline --}}
                    @yield('timeline_actions') 
                </div>
                <div class="card-body">
                    {{-- Assumiamo che $model e $timelineItems siano passati ai partial --}}
                    {{-- Questo partial è comune e dovrebbe ricevere $model e $timelineItems --}}
                    @include('partials.timeline_section', ['model' => $model, 'timelineItems' => $timelineItems])
                </div>
            </div>
        </div>

        {{-- COLONNA DI DESTRA: Relazioni Collegate --}}
        <div class="col-lg-3">
            <div class="card shadow mb-4">
                 <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">@yield('right_column_title', 'Relazioni Collegate')</h6>
                </div>
                <div class="card-body">
                    {{-- Placeholder per le relazioni specifiche (es. Azienda Collegata, Opportunità) --}}
                    @yield('right_column_content')
                </div>
            </div>
        </div>

    </div>
</div>
@endsection