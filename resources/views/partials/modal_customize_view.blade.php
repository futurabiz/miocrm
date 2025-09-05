{{-- Salva in: resources/views/partials/modal_customize_view.blade.php --}}
<div class="modal fade" id="customizeViewModal" tabindex="-1" role="dialog" aria-labelledby="customizeViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customizeViewModalLabel">Personalizza Vista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customizeViewForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" id="formMethodInput">
                <input type="hidden" name="view_id" id="viewIdInput">
                <input type="hidden" name="module_class" value="{{ $moduleClass }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="viewNameInput" class="form-label">Nome della Vista</label>
                        <input type="text" class="form-control" id="viewNameInput" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Colonne Visibili</label>
                        <div class="row">
                            @foreach ($columnLabels as $columnName => $columnLabel)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input column-checkbox" type="checkbox" value="{{ $columnName }}" id="column-checkbox-{{ $columnName }}" name="columns[]">
                                        <label class="form-check-label" for="column-checkbox-{{ $columnName }}">{{ $columnLabel }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva Vista</button>
                </div>
            </form>
        </div>
    </div>
</div>