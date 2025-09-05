{{-- Questo partial renderizza la timeline di attività e note --}}

{{-- Form per aggiungere una nuova NOTA (già esistente, lo spostiamo qui) --}}
@include('partials.notes_section', ['model' => $model])
<hr>

{{-- Feed della Timeline --}}
<div class="mt-4">
    @forelse($timelineItems as $item)
        <div class="d-flex mb-4">
            <div class="me-3">
                {{-- Icona diversa a seconda del tipo di item --}}
                @if($item instanceof \App\Models\Note)
                    <span class="btn btn-circle btn-sm btn-warning"><i class="bi bi-sticky-fill"></i></span>
                @elseif($item instanceof \App\Models\Activity)
                    <span class="btn btn-circle btn-sm btn-info"><i class="bi bi-calendar-event-fill"></i></span>
                @endif
            </div>
            <div class="w-100">
                <div class="d-flex justify-content-between">
                    <h6 class="mb-1">
                        @if($item instanceof \App\Models\Note)
                            Nota aggiunta da {{ $item->user->name ?? 'Sistema' }}
                        @elseif($item instanceof \App\Models\Activity)
                            Attività: {{ $item->title }} ({{ $item->type }})
                        @endif
                    </h6>
                    <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-0 small">
                    @if($item instanceof \App\Models\Note)
                        {{ $item->content }}
                    @elseif($item instanceof \App\Models\Activity)
                        {{ $item->description ?? 'Nessuna descrizione.' }}
                        @if($item->start_time)
                            <br><strong>Data:</strong> {{ $item->start_time->format('d/m/Y H:i') }}
                        @endif
                    @endif
                </p>
            </div>
        </div>
    @empty
        <div class="text-center text-muted">
            <p>Nessuna attività o nota registrata per questo lead.</p>
        </div>
    @endforelse
</div>