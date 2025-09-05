<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\EmailList;
use App\Models\Tag;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkflowController extends Controller
{
    private function safeTriggerValue($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }
        if (is_null($value)) {
            return '';
        }
        return (string) $value;
    }

    private function prepareWorkflowForView($workflow = null)
    {
        if (!$workflow) {
            $workflow = new Workflow();
        }
        $workflow->trigger_condition_value = $this->safeTriggerValue($workflow->trigger_condition_value ?? '');
        $workflow->trigger_model = $workflow->trigger_model ?? '';
        $workflow->trigger_condition_field = $workflow->trigger_condition_field ?? '';
        $workflow->trigger_condition_operator = $workflow->trigger_condition_operator ?? '';
        $workflow->action_type = $workflow->action_type ?? '';
        $workflow->is_active = $workflow->is_active ?? true;
        $workflow->name = $workflow->name ?? '';
        return $workflow;
    }

    // --- QUESTA È LA SOLA VERSIONE CORRETTA ---
    private function getFieldsForClass($modelClass)
    {
        return config("workflows.available_models.{$modelClass}.fields", []);
    }
    // --- FINE MODIFICA CHIAVE ---

    public function getFields(Request $request)
    {
        $validated = $request->validate(['model' => 'required|string']);
        $fields = $this->getFieldsForClass($validated['model']);
        return response()->json($fields);
    }

    public function searchValues(Request $request)
    {
        Log::info('================ INIZIO RICHIESTA searchFieldValues ================');
        Log::info('Dati ricevuti:', $request->all());
        $validated = $request->validate([
            'model' => 'required|string',
            'field' => 'required|string',
            'term' => 'nullable|string'
        ]);
        $modelClass = $validated['model'];
        $field = $validated['field'];
        $term = $validated['term'] ?? '';
        Log::info("Parametri validati: Modello='{$modelClass}', Campo='{$field}', Termine='{$term}'");
        if (!in_array($modelClass, array_keys(config('workflows.available_models', [])))
            || !isset(config('workflows.available_models.' . $modelClass . '.fields')[$field])) {
            Log::error('Validazione fallita: modello o campo non validi.');
            return response()->json(['results' => []]);
        }
        try {
            $model = app($modelClass);
            $table = $model->getTable();
            $query = \DB::table($table)
                ->where($field, 'LIKE', '%' . $term . '%')
                ->whereNotNull($field)
                ->distinct()
                ->limit(20);

            Log::info('Query SQL costruita:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
            $results = $query->select($field . ' as id', $field . ' as text')->get();
            Log::info('Risultati dal DB:', ['count' => $results->count(), 'data' => $results->toArray()]);
            Log::info('================ FINE RICHIESTA searchFieldValues =================');
            return response()->json(['results' => $results]);
        } catch (\Exception $e) {
            Log::error('ERRORE DURANTE LA QUERY: ' . $e->getMessage());
            return response()->json(['results' => []], 500);
        }
    }

    public function getFieldOptions(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string',
            'field' => 'required|string'
        ]);
        if (!in_array($validated['model'], array_keys(config('workflows.available_models', [])))) {
            return response()->json([], 403);
        }
        $options = config("workflows.field_options.{$validated['model']}.{$validated['field']}", []);
        return response()->json($options);
    }

    public function getInitialEntity(Request $request)
    {
        $validated = $request->validate([
            'field' => 'required|string',
            'id' => 'required'
        ]);
        $field = $validated['field'];
        $id = $validated['id'];
        $modelClass = null;
        switch ($field) {
            case 'company_id': $modelClass = \App\Models\Company::class; break;
            case 'contact_id': $modelClass = \App\Models\Contact::class; break;
            case 'assigned_to_id': $modelClass = \App\Models\User::class; break;
            default: return response()->json(null);
        }
        if (!$modelClass || !class_exists($modelClass) || !$record = $modelClass::find($id)) {
            return response()->json(null);
        }
        $text = ($field === 'contact_id')
            ? trim($record->first_name . ' ' . $record->last_name)
            : $record->name;
        return response()->json(['id' => $record->id, 'text' => $text]);
    }

    // === ECCO IL METODO MANCANTE CHE DEVI COPIARE ===
    public function getActionParameters(Request $request)
    {
        $validated = $request->validate([
            'action_type' => 'required|string',
            'workflow_id' => 'nullable|integer',
        ]);

        $actionType = $validated['action_type'];
        $workflowId = $validated['workflow_id'] ?? null;

        $actionConfig = config("workflows.available_actions.{$actionType}");
        if (!$actionConfig) {
            return response()->json(['html' => 'Azione non valida.'], 404);
        }

        $viewName = "workflows.partials.actions._{$actionType}";
        if (!\View::exists($viewName)) {
            return response()->json(['html' => "Vista parziale '{$viewName}' non trovata."], 404);
        }

        $workflow = $workflowId ? Workflow::find($workflowId) : new Workflow();

        $data = [
            'actionConfig'   => $actionConfig,
            'workflow'       => $workflow,
            'emailTemplates' => class_exists(\App\Models\EmailTemplate::class) ? EmailTemplate::orderBy('name')->get() : [],
            'emailLists'     => class_exists(\App\Models\EmailList::class) ? EmailList::orderBy('name')->get() : [],
            'tags'           => class_exists(\App\Models\Tag::class) ? Tag::orderBy('name')->get() : [],
            'availableModels'=> config('workflows.available_models', [])
        ];

        $html = view($viewName, $data)->render();
        return response()->json(['html' => $html]);
    }
    // === FINE METODO GETACTIONPARAMETERS ===

    public function index()
    {
        $workflows = Workflow::latest()->paginate(15);
        return view('workflows.index', compact('workflows'));
    }

    public function create()
    {
        $workflow = $this->prepareWorkflowForView();
        return view('workflows.create', [
            'workflow' => $workflow,
            'availableModels' => config('workflows.available_models', []),
            'emailLists' => EmailList::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'emailTemplates' => EmailTemplate::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_model' => 'required|string',
            'trigger_condition_field' => 'required|string',
            'trigger_condition_operator'=> 'required|string',
            'trigger_condition_value' => 'required|string',
            'action_type' => 'required|string',
            'action_parameters' => 'required|array',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
        Workflow::create($validated);
        return redirect()->route('workflows.index')->with('success', 'Workflow creato con successo.');
    }

    public function edit(Workflow $workflow)
    {
        $workflow = $this->prepareWorkflowForView($workflow);
        return view('workflows.edit', [
            'workflow' => $workflow,
            'availableModels' => config('workflows.available_models', []),
            'emailLists' => EmailList::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'emailTemplates' => EmailTemplate::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Workflow $workflow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_model' => 'required|string',
            'trigger_condition_field' => 'required|string',
            'trigger_condition_operator'=> 'required|string',
            'trigger_condition_value' => 'required|string',
            'action_type' => 'required|string',
            'action_parameters' => 'required|array',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $workflow->update($validated);
        return redirect()->route('workflows.index')->with('success', 'Workflow aggiornato con successo.');
    }

    public function destroy(Workflow $workflow)
    {
        $workflow->delete();
        return redirect()->route('workflows.index')->with('success', 'Workflow eliminato con successo.');
    }
}
