@extends('layouts.app') {{-- Assicura che la dashboard usi il nostro layout principale --}}

@section('content')
    {{-- Intestazione della Dashboard --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    {{-- Riga con le KPI Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Contatti Totali</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalContacts }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-people-fill fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- ... (Le altre tue KPI cards) ... --}}
    </div>

    <div class="row">
        {{-- Colonna con il Grafico a Imbuto --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Funnel Opportunità Aperte</h6>
                </div>
                <div class="card-body">
                    {{-- Qui andrà il canvas per il grafico (es. con Chart.js) --}}
                    <div class="chart-area" style="height: 320px; background-color: #f1f1f1; display: flex; align-items: center; justify-content: center;">
                        <p>Grafico Funnel (da implementare)</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonna con le Attività di Oggi --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Attività di Oggi</h6>
                </div>
                <div class="card-body">
                    @forelse($todayActivities as $activity)
                        <div class="small mb-2">
                            <strong>{{ $activity->start_time->format('H:i') }}</strong> - {{ $activity->title }}
                        </div>
                    @empty
                        <p class="small text-muted">Nessuna attività per oggi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
