<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use App\Models\ServiceType;
use App\Models\ModuleBlock;
use App\Http\Controllers\Traits\HandlesListViews;
use Illuminate\Http\Request;
use App\Exports\OpportunitiesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OpportunityController extends Controller
{
    use HandlesListViews;

    private function getModuleStructure()
    {
        return ModuleBlock::where('module_class', Opportunity::class)
            ->with(['fields' => fn ($q) => $q->where('is_visible', true)->orderBy('order')])
            ->orderBy('order')
            ->get();
    }

    private function buildValidationRules($blocks)
    {
        $rules = [];
        foreach ($blocks as $block) {
            foreach ($block->fields as $field) {
                if (!$field->is_visible) continue;
                $fieldRules = [];
                if ($field->is_required) { $fieldRules[] = 'required'; } else { $fieldRules[] = 'nullable'; }
                switch ($field->type) {
                    case 'number': $fieldRules[] = 'numeric'; break;
                    case 'date': $fieldRules[] = 'date'; break;
                }
                $inputName = $field->is_standard ? $field->name : "custom_fields_data.{$field->name}";
                $rules[$inputName] = implode('|', $fieldRules);
            }
        }
        return $rules;
    }

    public function index(Request $request)
    {
        $defaultColumns = ['name', 'company_id', 'stage', 'amount', 'closing_date', 'assigned_to_id'];
        $columnLabels = [
            'name' => 'Nome Opportunità', 'company_id' => 'Azienda', 'contact_id' => 'Contatto', 'stage' => 'Fase', 'amount' => 'Importo', 'closing_date' => 'Data Chiusura', 'assigned_to_id' => 'Assegnato a', 'description' => 'Descrizione'
        ];
        $viewData = $this->getListViewData($request, Opportunity::class, $defaultColumns, $columnLabels);
        $query = Opportunity::query();
        if(in_array('company_id', $viewData['currentView']->columns)) { $query->with('company'); }
        if(in_array('contact_id', $viewData['currentView']->columns)) { $query->with('contact'); }
        if(in_array('assigned_to_id', $viewData['currentView']->columns)) { $query->with('assignedTo'); }
        $opportunities = $query->latest()->paginate(10);
        return view('opportunita.index', array_merge($viewData, ['opportunities' => $opportunities]));
    }

    public function create()
    {
        $blocks = $this->getModuleStructure();
        $users = User::orderBy('name')->get(); // MODIFICA: Carica gli utenti
        return view('opportunita.create', compact('blocks', 'users')); // MODIFICA: Passa gli utenti alla vista
    }

    public function store(Request $request)
    {
        $blocks = $this->getModuleStructure();
        $rules = $this->buildValidationRules($blocks);
        $validatedData = Validator::make($request->all(), $rules)->validate();
        $opportunityData = $validatedData;
        $customData = $validatedData['custom_fields_data'] ?? [];
        unset($opportunityData['custom_fields_data']);
        if (!empty($customData)) { $opportunityData['custom_fields_data'] = json_encode($customData); }
        try {
            $opportunity = Opportunity::create($opportunityData);
            return redirect()->route('opportunities.show', $opportunity->id)->with('success', 'Opportunità creata con successo!');
        } catch (\Exception $e) {
            Log::error('Errore creazione Opportunità: ' . $e->getMessage() . ' | Dati: ' . json_encode($opportunityData));
            return back()->withInput()->with('error', 'Si è verificato un errore imprevisto durante il salvataggio.');
        }
    }

    public function show($id)
    {
        $opportunity = Opportunity::with(['company', 'contact', 'assignedTo', 'notes' => fn($q) => $q->latest(), 'activities' => fn($q) => $q->latest(), 'serviceTypes'])->findOrFail($id);
        $timelineItems = $opportunity->notes->merge($opportunity->activities)->sortByDesc('created_at');
        $allServices = ServiceType::all();
        return view('opportunita.show', compact('opportunity', 'timelineItems', 'allServices'));
    }

    public function edit($id)
    {
        $opportunity = Opportunity::findOrFail($id);
        $blocks = $this->getModuleStructure();
        $users = User::orderBy('name')->get(); // MODIFICA: Carica gli utenti
        return view('opportunita.edit', compact('opportunity', 'blocks', 'users')); // MODIFICA: Passa gli utenti alla vista
    }

    public function update(Request $request, $id)
    {
        $opportunity = Opportunity::findOrFail($id);
        $blocks = $this->getModuleStructure();
        $rules = $this->buildValidationRules($blocks);
        $validatedData = Validator::make($request->all(), $rules)->validate();
        $opportunityData = $validatedData;
        $customDataFromRequest = $validatedData['custom_fields_data'] ?? [];
        unset($opportunityData['custom_fields_data']);
        $existingCustomData = $opportunity->custom_fields_data;
        if (!is_array($existingCustomData)) { $existingCustomData = json_decode($existingCustomData, true) ?? []; }
        $mergedCustomData = array_merge($existingCustomData, $customDataFromRequest);
        if (!empty($mergedCustomData)) { $opportunityData['custom_fields_data'] = json_encode($mergedCustomData); }
        try {
            $opportunity->update($opportunityData);
            return redirect()->route('opportunities.show', $opportunity->id)->with('success', 'Opportunità aggiornata con successo!');
        } catch (\Exception $e) {
            Log::error('Errore aggiornamento Opportunità: ' . $e->getMessage() . ' | Dati: ' . json_encode($opportunityData));
            return back()->withInput()->with('error', 'Si è verificato un errore imprevisto durante l\'aggiornamento.');
        }
    }

    public function destroy(Opportunity $opportunity)
    {
        $opportunity->delete();
        return redirect()->route('opportunities.index')->with('success', 'Opportunità eliminata con successo!');
    }

    public function export()
    {
        return Excel::download(new OpportunitiesExport, 'opportunities-' . now()->format('Y-m-d') . '.csv');
    }
}