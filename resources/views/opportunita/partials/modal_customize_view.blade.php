<div class="modal fade" id="customizeViewModal" tabindex="-1" role="dialog" aria-labelledby="customizeViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customizeViewModalLabel">Personalizza Vista</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="customizeViewForm" method="POST" action="">
                @csrf
                <input type="hidden" name="view_id" id="customizeViewId">
                {{-- Assicurati che il valore del module_class sia corretto per il modulo Opportunità --}}
                <input type="hidden" name="module_class" value="App\Models\Opportunity"> 
                <div class="modal-body">
                    <div class="form-group">
                        <label for="viewName">Nome della Vista</label>
                        <input type="text" class="form-control" id="viewName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Colonne Visibili</label>
                        <div class="row">
                            @foreach ($columnLabels as $columnName => $columnLabel)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input column-checkbox" type="checkbox" 
                                               value="{{ $columnName }}" id="column-{{ $columnName }}" name="columns[]">
                                        <label class="form-check-label" for="column-{{ $columnName }}">
                                            {{ $columnLabel }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva Vista</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#customizeViewModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var viewId = button.data('view-id'); // Può essere vuoto per "Nuova Vista"
        var modal = $(this);

        modal.find('#customizeViewForm')[0].reset();
        modal.find('#customizeViewId').val('');
        modal.find('#viewName').val('');
        modal.find('.column-checkbox').prop('checked', false);

        if (viewId) {
            // Modifica vista esistente
            $.ajax({
                url: '/list_views/' + viewId + '/edit', // Rotta per ottenere i dati della vista
                method: 'GET',
                success: function(response) {
                    modal.find('#customizeViewId').val(response.id);
                    modal.find('#viewName').val(response.name);
                    response.columns.forEach(function(column) {
                        modal.find('#column-' + column).prop('checked', true);
                    });
                    // Aggiorna l'azione del form per l'update
                    modal.find('#customizeViewForm').attr('action', '{{ route('list_views.update', ['list_view' => ':viewId']) }}'.replace(':viewId', viewId)).attr('method', 'POST');
                    modal.find('#customizeViewForm').append('<input type="hidden" name="_method" value="PUT">');
                }
            });
        } else {
            // Nuova vista
            modal.find('#customizeViewForm').attr('action', '{{ route('list_views.store') }}').attr('method', 'POST'); // MODIFICATO QUI
            modal.find('#customizeViewForm').find('input[name="_method"]').remove(); // Assicurati che non ci sia il metodo PUT/PATCH
        }
    });
});
</script>
@endpush