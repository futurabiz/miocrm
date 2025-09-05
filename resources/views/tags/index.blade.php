@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Gestione Tag</h2>
                <a class="btn btn-success" href="{{ route('tags.create') }}"> Crea Nuovo Tag</a>
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
                        <th>Nome Tag</th>
                        <th>Colore</th>
                        <th width="150px">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tags as $tag)
                    <tr>
                        <td>{{ $tag->name }}</td>
                        <td>
                            <span class="badge" style="background-color: {{ $tag->color }}; color: {{ \App\Http\Controllers\TagController::isColorDark($tag->color) ? 'white' : 'black' }};">
                                {{ $tag->color }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <div class="btn-group" role="group">
                                <a href="{{ route('tags.edit', $tag->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifica">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('tags.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo tag?');" class="d-inline">
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
                        <td colspan="3" class="text-center">Nessun tag trovato.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tags->hasPages())
        <div class="card-footer">
            {!! $tags->links() !!}
        </div>
        @endif
    </div>
@endsection