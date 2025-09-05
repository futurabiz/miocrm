@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <h1 class="h3 mb-0 text-gray-800 me-3">Contatti</h1>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="viewSelector" data-bs-toggle="dropdown" aria-expanded="false">
                   Vista: <strong>{{ $currentView->name ?? 'Standard' }}</strong>
                </button>
                <ul class="dropdown-menu" aria-labelledby="viewSelector">
                    <li><h6 class="dropdown-header">Viste Disponibili</h6></li>
                    <li><a class="dropdown-item" href="{{ route('contacts.index') }}">Vista di Default</a></li>
                    @if($listViews->isNotEmpty())
                        @foreach($listViews as $view)
                        <li><a class="dropdown-item" href="{{ route('contacts.index', ['view_id' => $view->id]) }}">{{ $view->name }}</a></li>
                        @endforeach
                    @endif
                    
                    @if(isset($currentView) && is_numeric($currentView->id))
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Azioni sulla Vista</h6></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#customizeViewModal" id="editCurrentViewBtn"><i class="bi bi-pencil-fill me-2"></i>Modifica Vista Corrente</a></li>
                        <li><a href="#" class="dropdown-item text-danger" id="deleteCurrentViewBtn" data-view-name="{{ $currentView->name }}" data-delete-url="{{ route('list_views.destroy', $currentView->id) }}"><i class="bi bi-trash-fill me-2"></i>Elimina Vista Corrente</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#customizeViewModal" id="createNewViewBtn" title="Personalizza Vista"><i class="bi bi-gear-fill"></i></button>
            <a href="{{ route('contacts.export') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Esporta CSV"><i class="bi bi-download"></i></a>
            <a href="{{ route('contacts.create') }}" class="btn btn-success" data-bs-toggle="tooltip" title="Crea Nuovo Contatto"><i class="bi bi-plus-lg"></i></a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ $message }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            @foreach($columns as $column)
                                <th>{{ $columnLabels[$column] ?? ucfirst(str_replace('_', ' ', $column)) }}</th>
                            @endforeach
                            <th width="150px">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contacts as $contact)
                        <tr>
                            @foreach($columns as $column)
                                <td>
                                    @if($column === 'company_id') {{ $contact->company->name ?? 'N/D' }}
                                    @elseif($column === 'assigned_to_id') {{ $contact->assignedTo->name ?? 'N/D' }}
                                    @else {{ $contact->$column }} @endif
                                </td>
                            @endforeach
                            <td class="text-nowrap">
                                <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('Sei sicuro?');" class="d-inline">
                                    <a href="{{ route('contacts.show', $contact->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Visualizza"><i class="bi bi-eye-fill"></i></a>
                                    <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Modifica"><i class="bi bi-pencil-fill"></i></a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Elimina"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="text-center">Nessun contatto trovato.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($contacts->hasPages())
        <div class="card-footer">
            {!! $contacts->appends(request()->except('page'))->links() !!}
        </div>
        @endif
    </div>
</div>

@include('partials.modal_customize_view', [
    'moduleClass' => 'App\\Models\\Contact',
    'columnLabels' => $columnLabels
])

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inizializza i tooltip di Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const customizeViewModal = document.getElementById('customizeViewModal');
    if (customizeViewModal) {
        const form = document.getElementById('customizeViewForm');
        const modalTitle = document.getElementById('customizeViewModalLabel');
        const viewIdInput = document.getElementById('viewIdInput');
        const viewNameInput = document.getElementById('viewNameInput');
        const methodInput = document.getElementById('formMethodInput');
        const allCheckboxes = form.querySelectorAll('.column-checkbox');
        const currentViewData = @json($currentView ?? null);
        const defaultColumns = @json($defaultColumns ?? []);

        customizeViewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const isEdit = button && button.id === 'editCurrentViewBtn';
            
            form.reset();
            allCheckboxes.forEach(checkbox => checkbox.checked = false);
            
            if (isEdit && currentViewData && typeof currentViewData.id === 'number') {
                modalTitle.textContent = 'Modifica Vista: ' + currentViewData.name;
                viewIdInput.value = currentViewData.id;
                viewNameInput.value = currentViewData.name;
                methodInput.value = 'PUT';
                form.action = `{{ url('list_views') }}/${currentViewData.id}`;
                currentViewData.columns.forEach(column => {
                    const checkbox = form.querySelector(`#column-${column}`);
                    if(checkbox) checkbox.checked = true;
                });
            } else {
                modalTitle.textContent = 'Crea Nuova Vista';
                viewIdInput.value = '';
                methodInput.value = 'POST';
                form.action = "{{ route('list_views.store') }}";
                defaultColumns.forEach(column => {
                    const checkbox = form.querySelector(`#column-${column}`);
                    if(checkbox) checkbox.checked = true;
                });
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData,
            })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) { throw data; }
                if (data.success && data.view_id) {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('view_id', data.view_id);
                    window.location.href = currentUrl.toString();
                } else { alert(data.message || 'Si è verificato un errore.'); }
            })
            .catch(errorData => {
                let errorMessage = "Si è verificato un errore.";
                if (errorData.errors) {
                    errorMessage = "Correggi i seguenti errori:\n\n";
                    for (const field in errorData.errors) { errorMessage += `- ${errorData.errors[field].join(', ')}\n`; }
                } else if (errorData.message) { errorMessage = errorData.message; }
                alert(errorMessage);
            });
        });
    }

    const deleteBtn = document.getElementById('deleteCurrentViewBtn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const viewName = this.getAttribute('data-view-name');
            const deleteUrl = this.getAttribute('data-delete-url');
            if (confirm(`Sei sicuro di voler eliminare la vista "${viewName}"? L'azione è irreversibile.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
});
</script>
@endpush