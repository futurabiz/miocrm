@php
// Protezioni anti-errore per variabili
$params = isset($workflow) && isset($workflow->action_parameters) ? $workflow->action_parameters : [];
$typeOptions = [];
if (isset($actionConfig) && isset($actionConfig['parameters']['type']['options']) && is_array($actionConfig['parameters']['type']['options'])) {
    $typeOptions = $actionConfig['parameters']['type']['options'];
} else {
    $typeOptions = [
        'info' => 'Informazione',
        'warning' => 'Avviso',
        'success' => 'Successo',
        'error' => 'Errore'
    ];
}
// Gestione utenti selezionati (siano array o stringa)
$selectedUsers = old('action_parameters.users', $params['users'] ?? []);
if (!is_array($selectedUsers)) $selectedUsers = [$selectedUsers];
@endphp

<div class="col-md-12 mb-3">
    <label for="param-users" class="form-label">Utenti a cui notificare:</label>
    <select name="action_parameters[users][]" id="param-users" class="form-select" multiple required>
        @foreach($selectedUsers as $userId)
            @if($userId)
                <option value="{{ $userId }}" selected>{{ $userId }}</option>
            @endif
        @endforeach
    </select>
    <small class="form-text">Questo campo richiede un Select2 con ricerca AJAX (da implementare).</small>
</div>
<div class="col-md-12 mb-3">
    <label for="param-message" class="form-label">Messaggio Notifica:</label>
    <textarea name="action_parameters[message]" id="param-message" class="form-control" rows="3" required>{{ old('action_parameters.message', $params['message'] ?? '') }}</textarea>
</div>
<div class="col-md-12 mb-3">
    <label for="param-type" class="form-label">Tipo di Notifica:</label>
    <select name="action_parameters[type]" id="param-type" class="form-select" required>
        @foreach($typeOptions as $value => $label)
            <option value="{{ $value }}" @selected(old('action_parameters.type', $params['type'] ?? '') == $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
