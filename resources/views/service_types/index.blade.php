@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tipi di Servizio</h1>
        <a href="{{ route('service_types.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"> {{-- MODIFICATO QUI --}}
            <i class="fas fa-plus fa-sm text-white-50"></i> Crea Nuovo Tipo Servizio
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Elenco Tipi Servizio</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Descrizione</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceTypes as $serviceType)
                        <tr>
                            <td>{{ $serviceType->name }}</td>
                            <td>{{ $serviceType->description }}</td>
                            <td>
                                <a href="{{ route('service_types.edit', $serviceType->id) }}" class="btn btn-info btn-sm">Modifica</a>
                                <form action="{{ route('service_types.destroy', $serviceType->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro di voler eliminare questo tipo di servizio?');">Elimina</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('js/demo/datatables-demo.js') }}"></script>
@endpush