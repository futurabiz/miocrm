{{-- 
    Questo è il modal riutilizzabile per creare e salvare viste personalizzate.
    Riceve le variabili: $module, $availableColumns, $columnLabels
--}}
<div class="modal fade" id="customizeViewModal" tabindex="-1" aria-labelledby="customizeViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('list-views.store') }}" method="POST">
            @csrf
            <input type="hidden" name="module" value="{{ $module }}">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customizeViewModalLabel">Personalizza Vista Lista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Crea una nuova vista personalizzata selezionando le colonne che desideri visualizzare. Puoi salvare più viste per ogni modulo.</p>
                    
                    <div class="mb-3">
                        <label for="view_name" class="form-label"><strong>Nome della Vista:</strong></label>
                        <input type="text" name="name" id="view_name" class="form-control" placeholder="Es. Contatti da Richiamare" required>
                    </div>

                    <div class="mb-3">
                        <label for="columns" class="form-label"><strong>Seleziona Colonne da Mostrare:</strong></label>
                        <select name="columns[]" id="columns" class="form-select" multiple required size="10">
                            @foreach ($availableColumns as $column)
                                <option value="{{ $column }}">{{ $columnLabels[$column] ?? ucwords(str_replace('_', ' ', $column)) }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Tieni premuto Ctrl (o Cmd su Mac) per selezionare più colonne.</small>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="is_default_view">
                        <label class="form-check-label" for="is_default_view">
                            Imposta come vista di default per questo modulo.
                        </label>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva Vista</button>
                </div>
            </div>
        </form>
    </div>
</div>
