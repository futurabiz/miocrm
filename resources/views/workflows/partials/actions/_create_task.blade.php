@php $params = $workflow->action_parameters ?? []; @endphp
<div class="col-md-12 mb-3">
    <label for="param-title" class="form-label">Titolo Attività:</label>
    <input type="text" name="action_parameters[title]" id="param-title" class="form-control" value="{{ old('action_parameters.title', $params['title'] ?? '') }}" required>
</div>
<div class="col-md-12 mb-3">
    <label for="param-description" class="form-label">Descrizione:</label>
    <textarea name="action_parameters[description]" id="param-description" class="form-control" rows="3">{{ old('action_parameters.description', $params['description'] ?? '') }}</textarea>
</div>
<div class="col-md-6 mb-3">
    <label for="param-due_date" class="form-label">Data Scadenza:</label>
    <input type="date" name="action_parameters[due_date]" id="param-due_date" class="form-control" value="{{ old('action_parameters.due_date', $params['due_date'] ?? '') }}">
</div>
<div class="col-md-6 mb-3">
    <label for="param-priority" class="form-label">Priorità:</label>
    <select name="action_parameters[priority]" id="param-priority" class="form-select" required>
        @foreach($actionConfig['parameters']['priority']['options'] as $value => $label)
            <option value="{{ $value }}" @selected(old('action_parameters.priority', $params['priority'] ?? 'Media') == $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
