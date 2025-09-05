<!-- Salva come: resources/views/partials/activities_section.blade.php -->

<div class="list-group list-group-flush">
    @forelse($model->activities->sortByDesc('start_time') as $activity)
        <div class="list-group-item px-0">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">
                    @if($activity->type === 'task')
                        <i class="bi bi-check2-square text-warning"></i>
                    @elseif($activity->type === 'meeting')
                        <i class="bi bi-calendar-event text-primary"></i>
                    @else
                        <i class="bi bi-telephone text-success"></i>
                    @endif
                    {{ $activity->title }}
                </h5>
                <small>{{ $activity->start_time->format('d/m/Y H:i') }}</small>
            </div>
            @if($activity->description)
                <p class="mb-1">{{ $activity->description }}</p>
            @endif
            <small class="text-muted">Tipo: {{ ucfirst($activity->type) }}</small>
        </div>
    @empty
        <p class="text-center p-3">Nessuna attività collegata a questo record.</p>
    @endforelse
</div>

<div class="mt-3">
    <a href="{{ route('calendario.index') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> Aggiungi Nuova Attività
    </a>
    <small class="ms-2">Puoi aggiungere nuove attività dalla pagina del Calendario.</small>
</div>
