<!-- Salva come: resources/views/partials/notes_section.blade.php -->

<div class="card mt-5">
    <div class="card-header">
        <h4>Note</h4>
    </div>
    <div class="card-body">
        
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Form per aggiungere una nuova nota -->
        <form action="{{ route('notes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <textarea name="content" class="form-control" rows="3" placeholder="Aggiungi una nuova nota..." required></textarea>
            </div>
            
            <!-- Questi campi nascosti dicono al controller a cosa allegare la nota -->
            <input type="hidden" name="notable_id" value="{{ $model->id }}">
            <input type="hidden" name="notable_type" value="{{ get_class($model) }}">
            
            <button type="submit" class="btn btn-primary btn-sm">Aggiungi Nota</button>
        </form>

        <hr>

        <!-- Lista delle note esistenti -->
        <h5 class="mt-4">Note Esistenti:</h5>
        @if($model->notes->isEmpty())
            <p>Nessuna nota presente.</p>
        @else
            <ul class="list-group list-group-flush">
                @foreach($model->notes->sortByDesc('created_at') as $note)
                    <li class="list-group-item px-0">
                        <p class="mb-1">{{ $note->content }}</p>
                        <small class="text-muted">Aggiunta il: {{ $note->created_at->format('d/m/Y H:i') }}</small>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
