@php $params = $workflow->action_parameters ?? []; @endphp
<div class="col-md-6 mb-3">
    <label for="param-field" class="form-label">Campo da Aggiornare:</label>
    <select name="action_parameters[field]" id="param-field" class="form-select" required>
        <option value="">-- Seleziona Campo --</option>
        @php
            $modelClass = old('trigger_model', $workflow->trigger_model ?? '');
            if($modelClass && isset($availableModels[$modelClass])) {
                foreach($availableModels[$modelClass]['fields'] as $fieldName => $fieldConfig) {
                    echo '<option value="' . $fieldName . '" ' . (old('action_parameters.field', $params['field'] ?? '') == $fieldName ? 'selected' : '') . '>' . $fieldConfig['label'] . '</option>';
                }
            }
        @endphp
    </select>
</div>
<div class="col-md-6 mb-3">
    <label for="param-value" class="form-label">Nuovo Valore:</label>
    <input type="text" name="action_parameters[value]" id="param-value" class="form-control" value="{{ old('action_parameters.value', $params['value'] ?? '') }}" required>
</div>
