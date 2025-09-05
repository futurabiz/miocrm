@php
    // Variabile per gestire i valori esistenti nel form di modifica
    $modelInstance = $modelInstance ?? null;
@endphp

<div class="col-md-4 mb-3">
    <label for="address_region" class="form-label">Regione:</label>
    {{-- Il campo Regione serve solo per avviare la cascata, non viene salvato --}}
    <select id="address_region" class="form-select address-region">
        <option value="">-- Seleziona Regione --</option>
    </select>
</div>

<div class="col-md-4 mb-3">
    <label for="address_province" class="form-label">Provincia:</label>
    {{-- MODIFICATO: il name ora è 'province_id' --}}
    <select id="address_province" name="province_id" class="form-select address-province" disabled>
        <option value="">-- Prima seleziona una Regione --</option>
    </select>
</div>

<div class="col-md-4 mb-3">
    <label for="address_city" class="form-label">Comune:</label>
    {{-- MODIFICATO: il name ora è 'city_id' --}}
    <select id="address_city" name="city_id" class="form-select address-city" disabled>
        <option value="">-- Prima seleziona una Provincia --</option>
    </select>
</div>

<div class="col-md-4 mb-3" id="postal-code-container" style="display: none;">
    <label for="address_postal_code" class="form-label">CAP:</label>
    {{-- MODIFICATO: ora è un select con name 'postal_code_id' --}}
    <select id="address_postal_code" name="postal_code_id" class="form-select address-postal-code" disabled>
         <option value="">-- Prima seleziona un Comune --</option>
    </select>
</div>