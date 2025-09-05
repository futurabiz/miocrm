{{-- Corpo del form per la creazione e modifica dei Workflow --}}

<h5>Informazioni di Base</h5>
<div class="row">
    <div class="col-md-8 mb-3">
        <label for="name" class="form-label"><strong>Nome Workflow:</strong></label>
        <input type="text" name="name" class="form-control" placeholder="Es. Follow-up opportunità in scadenza" value="{{ old('name', $workflow->name ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label"><strong>Stato:</strong></label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $workflow->is_active ?? true))>
            <label class="form-check-label" for="is_active">Attivo</label>
        </div>
    </div>
</div>

<hr>
<h5>Condizioni di Attivazione (Trigger)</h5>
<div class="row align-items-end">
    <div class="col-md-4 mb-3">
        <label for="trigger_model" class="form-label">Quando un record di questo modulo...</label>
        <select name="trigger_model" id="trigger_model" class="form-select" required>
            <option value="">-- Seleziona Modulo --</option>
            @foreach(config('workflows.available_models') as $class => $config)
                <option value="{{ $class }}" @if(old('trigger_model', $workflow->trigger_model ?? '') == $class) selected @endif>{{ $config['label'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label for="trigger_condition_field" class="form-label">...ha questo campo...</label>
        <select name="trigger_condition_field" id="trigger_condition_field" class="form-select" required></select>
    </div>
    <div class="col-md-2 mb-3">
        <label for="trigger_condition_operator" class="form-label">...con questo operatore...</label>
        <select name="trigger_condition_operator" id="trigger_condition_operator" class="form-select" required></select>
    </div>
    <div class="col-md-3 mb-3" id="trigger-value-container">
        <label for="trigger_condition_value" class="form-label">...e questo valore:</label>
        <input
            type="text"
            name="trigger_condition_value"
            id="trigger_condition_value"
            class="form-control"
            value="{{ old('trigger_condition_value', is_array($workflow->trigger_condition_value ?? '') ? json_encode($workflow->trigger_condition_value) : ($workflow->trigger_condition_value ?? '')) }}">
    </div>
</div>

<hr>
<h5>Azione da Eseguire</h5>
<div class="row align-items-end">
    <div class="col-md-4 mb-3">
        <label for="action_type" class="form-label">Tipo di Azione</label>
        <select name="action_type" id="action_type" class="form-select" required>
            <option value="">-- Seleziona Azione --</option>
            @foreach(config('workflows.available_actions') as $actionKey => $actionConfig)
                <option value="{{ $actionKey }}" @if(old('action_type', $workflow->action_type ?? '') == $actionKey) selected @endif>{{ $actionConfig['label'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8">
        <div class="row" id="action-parameters-container">
            {{-- Contenuto dinamico per i parametri dell'azione --}}
        </div>
    </div>
</div>
