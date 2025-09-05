<div class="modal fade" id="customizeViewModal" tabindex="-1" aria-labelledby="customizeViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="customizeViewForm" method="POST">
                @csrf
                {{-- CORREZIONE: Aggiunto input per il metodo (PUT/POST) gestito via JS --}}
                <input type="hidden" name="_method" id="formMethodInput" value="POST">
                
                {{-- CORREZIONE: L'ID della vista e il nome della classe del modulo sono gestiti dinamicamente via JS e dal template che include questo partial --}}
                <input type="hidden" name="view_id" id="viewIdInput">
                <input type="hidden" name="module_class" value="{{ $moduleClass ?? '' }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="customizeViewModalLabel">Personalizza Vista</h5>
                    {{-- CORREZIONE: Sintassi del pulsante di chiusura per Bootstrap 5 --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- CORREZIONE: 'form-group' sostituito con 'mb-3' per coerenza con Bootstrap 5 --}}
                    <div class="mb-3">
                        <label for="viewNameInput" class="form-label">Nome della Vista</label>
                        <input type="text" class="form-control" id="viewNameInput" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Colonne Visibili</label>
                        <div class="row">
                            {{-- La logica itera sulle colonne disponibili passate dalla vista principale --}}
                            @if(isset($columnLabels))
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
                            @endif
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

{{-- NOTA: Tutto il blocco <script> e @push('scripts') è stato volutamente rimosso. 
La logica di interazione corretta risiede già nella vista principale (es. 'leads/index.blade.php') 
che include questo file, eliminando conflitti e duplicazioni. --}}