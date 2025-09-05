<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\ModuleBlock;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\HandlesListViews;
use App\Exports\LeadsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use robertogallea\LaravelCodiceFiscale\CodiceFiscale;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LeadController extends Controller
{
    use HandlesListViews;

    private function getModuleStructure()
    {
        return ModuleBlock::where('module_class', Lead::class)
            ->with(['fields' => fn ($q) => $q->where('is_visible', true)->orderBy('order')])
            ->orderBy('order')->get();
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
                    case 'email': $fieldRules[] = 'email'; break;
                    case 'date': $fieldRules[] = 'date'; break;
                    case 'number': $fieldRules[] = 'numeric'; break;
                    case 'related_user':
                    case 'related_company':
                    case 'related_contact':
                    case 'related_city':
                        $fieldRules[] = 'integer';
                        break;
                }
                
                $inputName = $field->is_standard ? $field->name : "custom_fields_data.{$field->name}";
                $rules[$inputName] = implode('|', $fieldRules);
            }
        }
        return $rules;
    }

    public function index(Request $request)
    {
        $defaultColumns = ['first_name', 'last_name', 'company_id', 'status', 'email', 'phone', 'assigned_to_id'];
        $columnLabels = [
            'first_name' => 'Nome', 'last_name' => 'Cognome', 'company_id' => 'Azienda', 'status' => 'Stato', 'email' => 'Email', 'phone' => 'Telefono Fisso', 'mobile_phone' => 'Cellulare', 'source' => 'Fonte', 'assigned_to_id' => 'Assegnato a', 'created_at' => 'Data Creazione', 'updated_at' => 'Ultima Modifica',
        ];
        $viewData = $this->getListViewData($request, Lead::class, $defaultColumns, $columnLabels);
        $query = Lead::query()->where('status', '!=', 'Convertito');
        if(in_array('company_id', $viewData['currentView']->columns)) { $query->with('company'); }
        if(in_array('assigned_to_id', $viewData['currentView']->columns)) { $query->with('assignedTo'); }
        $leads = $query->latest()->paginate(15);
        return view('leads.index', array_merge($viewData, [
            'leads' => $leads, 'columns' => $viewData['currentView']->columns, 'defaultColumns' => $defaultColumns,
        ]));
    }

    public function create()
    {
        $blocks = $this->getModuleStructure();
        $users = User::orderBy('name')->get();
        return view('leads.create', compact('blocks', 'users'));
    }

    public function store(Request $request)
    {
        $blocks = $this->getModuleStructure();
        $rules = $this->buildValidationRules($blocks);

        // --- MODIFICA QUI: Aggiungiamo le regole per i campi di località ---
        $rules['province_id'] = 'nullable|integer|exists:provinces,id';
        $rules['city_id'] = 'nullable|integer|exists:cities,id';
        $rules['postal_code_id'] = 'nullable|integer|exists:postal_codes,id';
        // --- FINE MODIFICA ---

        $validatedData = Validator::make($request->all(), $rules)->validate();

        try {
            $this->validateFiscalCode($request);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        $leadData = $validatedData;
        $customData = $validatedData['custom_fields_data'] ?? [];
        unset($leadData['custom_fields_data']);
        
        if (!empty($customData)) {
            $leadData['custom_fields_data'] = json_encode($customData);
        }
        
        if (empty($leadData['status'])) {
            $leadData['status'] = 'Nuovo';
        }

        try {
            Lead::create($leadData);
            return redirect()->route('leads.index')->with('success', 'Lead creato con successo!');
        } catch (\Exception $e) {
            Log::error('Errore creazione Lead: ' . $e->getMessage() . ' | Dati: ' . json_encode($leadData));
            return back()->withInput()->with('error', 'Si è verificato un errore imprevisto durante il salvataggio.');
        }
    }

    public function show(Lead $lead)
    {
        $lead->load('company', 'assignedTo', 'notes.user', 'activities.user');
        $timelineItems = $lead->notes->concat($lead->activities)->sortByDesc('created_at');
        return view('leads.show', compact('lead', 'timelineItems'));
    }

    public function edit(Lead $lead)
    {
        $blocks = $this->getModuleStructure();
        $users = User::orderBy('name')->get();
        return view('leads.edit', ['lead' => $lead, 'blocks' => $blocks, 'users' => $users]);
    }

    public function update(Request $request, Lead $lead)
    {
        $blocks = $this->getModuleStructure();
        $rules = $this->buildValidationRules($blocks);
        
        // --- MODIFICA QUI: Aggiungiamo le regole per i campi di località ---
        $rules['province_id'] = 'nullable|integer|exists:provinces,id';
        $rules['city_id'] = 'nullable|integer|exists:cities,id';
        $rules['postal_code_id'] = 'nullable|integer|exists:postal_codes,id';
        // --- FINE MODIFICA ---

        $validatedData = Validator::make($request->all(), $rules)->validate();
        
        try {
            $this->validateFiscalCode($request);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        $leadData = $validatedData;
        $customDataFromRequest = $validatedData['custom_fields_data'] ?? [];
        unset($leadData['custom_fields_data']);
        
        $existingCustomData = $lead->custom_fields_data;
        if (!is_array($existingCustomData)) {
            $existingCustomData = json_decode($existingCustomData, true) ?? [];
        }
        
        $mergedCustomData = array_merge($existingCustomData, $customDataFromRequest);
        
        if (!empty($mergedCustomData)) {
            $leadData['custom_fields_data'] = json_encode($mergedCustomData);
        }
        
        try {
            $lead->update($leadData);
            return redirect()->route('leads.show', $lead->id)->with('success', 'Lead aggiornato con successo!');
        } catch (\Exception $e) {
            Log::error('Errore aggiornamento Lead: ' . $e->getMessage() . ' | Dati: ' . json_encode($leadData));
            return back()->withInput()->with('error', 'Si è verificato un errore imprevisto durante l\'aggiornamento.');
        }
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead eliminato con successo!');
    }

    public function export()
    {
        return Excel::download(new LeadsExport, 'leads-' . now()->format('Y-m-d') . '.csv');
    }

    private function validateFiscalCode(Request $request) {
        if (!$request->filled('codice_fiscale')) { return; }
        $requiredForCf = Validator::make($request->all(), [
            'first_name' => 'required', 'last_name' => 'required', 'birthdate' => 'required|date', 'gender' => 'required|in:M,F', 'city_code' => 'required',
        ]);
        if ($requiredForCf->fails()) { throw ValidationException::withMessages(['codice_fiscale' => 'Per validare il Codice Fiscale, tutti i campi anagrafici sono obbligatori.']); }
        $city = City::where('fiscal_code', $request->input('city_code'))->first();
        if (!$city) { throw ValidationException::withMessages(['city_code' => 'Comune di nascita non valido.']); }
        try {
            $cfGenerator = new CodiceFiscale();
            $generatedFiscalCode = $cfGenerator->generate($request->input('first_name'), $request->input('last_name'), $request->input('birthdate'), $city->fiscal_code, $request->input('gender'));
            if (strtoupper(trim($request->input('codice_fiscale'))) !== $generatedFiscalCode) {
                throw ValidationException::withMessages(['codice_fiscale' => 'Il Codice Fiscale non corrisponde. Atteso: ' . $generatedFiscalCode]);
            }
        } catch (\Exception | \TypeError $e) {
            throw ValidationException::withMessages(['codice_fiscale' => 'Errore validazione Codice Fiscale: ' . $e->getMessage()]);
        }
    }
}