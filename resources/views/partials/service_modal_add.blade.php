<div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addServiceModalLabel">Aggiungi Nuovo Servizio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addServiceForm" method="POST" action="{{ route('customer_services.store') }}"> {{-- MODIFICATO QUI --}}
                @csrf
                <input type="hidden" name="company_id" id="service_company_id">
                <input type="hidden" name="contact_id" id="service_contact_id">
                <input type="hidden" name="opportunity_id" id="service_opportunity_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="service_type_id" class="form-label">Tipo di Servizio</label>
                        <select class="form-control" id="service_type_id" name="service_type_id" required>
                            <option value="">Seleziona un tipo di servizio</option>
                            @foreach($serviceTypes as $serviceType)
                                <option value="{{ $serviceType->id }}">{{ $serviceType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="service_date" class="form-label">Data Servizio</label>
                        <input type="date" class="form-control" id="service_date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="service_description" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="service_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="service_cost" class="form-label">Costo (€)</label>
                        <input type="number" step="0.01" class="form-control" id="service_cost" name="cost" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary">Salva Servizio</button>
                </div>
            </form>
        </div>
    </div>
</div>