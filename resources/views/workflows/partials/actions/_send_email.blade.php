@php
$params = isset($workflow) && isset($workflow->action_parameters) ? $workflow->action_parameters : [];
$actionConfig = $actionConfig ?? [];
$templateLabel = $actionConfig['parameters']['template_id']['label'] ?? 'Template Email';
$toLabel = $actionConfig['parameters']['to']['label'] ?? "A (opzionale, usa l'email del record se vuoto)";
$subjectLabel = $actionConfig['parameters']['subject']['label'] ?? 'Oggetto';
$bodyLabel = $actionConfig['parameters']['body']['label'] ?? 'Messaggio';
// Recupera tutti i template dal controller, se necessario
$emailTemplates = $emailTemplates ?? [];
@endphp

<div class="col-md-12 mb-3">
    <label for="param-template_id" class="form-label">{{ $templateLabel }}:</label>
    <select name="action_parameters[template_id]" id="param-template_id" class="form-select" required>
        <option value="">-- Seleziona Template --</option>
        @foreach($emailTemplates as $tpl)
            <option value="{{ $tpl->id }}" @selected(old('action_parameters.template_id', $params['template_id'] ?? '') == $tpl->id)>
                {{ $tpl->name }}
            </option>
        @endforeach
    </select>
</div>
<div class="col-md-12 mb-3">
    <label for="param-to" class="form-label">{{ $toLabel }}:</label>
    <input type="email" name="action_parameters[to]" id="param-to" class="form-control" value="{{ old('action_parameters.to', $params['to'] ?? '') }}">
</div>
<div class="col-md-12 mb-3">
    <label for="param-subject" class="form-label">{{ $subjectLabel }}:</label>
    <input type="text" name="action_parameters[subject]" id="param-subject" class="form-control" value="{{ old('action_parameters.subject', $params['subject'] ?? '') }}">
</div>
<div class="col-md-12 mb-3">
    <label for="param-body" class="form-label">{{ $bodyLabel }}:</label>
    <textarea name="action_parameters[body]" id="param-body" class="form-control tinymce-editor" rows="8" required>{{ old('action_parameters.body', $params['body'] ?? '') }}</textarea>
</div>
