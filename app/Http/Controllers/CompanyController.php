<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\ModuleBlock;
use App\Models\Contact;
use App\Models\ServiceType;
use App\Http\Controllers\Traits\HandlesListViews;
use Illuminate\Http\Request;
use App\Exports\CompaniesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    use HandlesListViews;

    private function getModuleStructure()
    {
        return ModuleBlock::where('module_class', Company::class)
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
                    case 'email': $fieldRules[] = 'email'; break;
                    case 'number': $fieldRules[] = 'numeric'; break;
                }
                $inputName = $field->is_standard ? $field->name : "custom_fields_data.{$field->name}";
                $rules[$inputName] = implode('|', $fieldRules);
            }
        }
        return $rules;
    }

    public function index(Request $request)
    {
        $defaultColumns = ['name', 'email', 'phone', 'industry', 'assigned_to_id'];
        $columnLabels = [
            'name' => 'Nome', 'email' => 'Email', 'phone' => 'Telefono', 'industry' => 'Settore', 'assigned_to_id' => 'Assegnato a', 'vat_number' => 'Partita IVA', 'company_tax_code' => 'Codice Fiscale', 'address_city' => 'Città', 'main_contact_id' => 'Contatto Principale'
        ];
        $viewData = $this->getListViewData($request, Company::class, $defaultColumns, $columnLabels);
        $query = Company::query();
        if(in_array('assigned_to_id', $viewData['currentView']->columns)) { $query->with('assignedTo'); }
        if(in_array('main_contact_id', $viewData['currentView']->columns)) { $query->with('mainContact'); }
        $companies = $query->latest()->paginate(10);
        
        // --- CORREZIONE QUI ---
        return view('aziende.index', array_merge($viewData, [
            'companies' => $companies,
            'columns' => $viewData['currentView']->columns
        ]));
    }

    public function create()
    {
        $blocks = $this->getModuleStructure();
        $users = User::orderBy('name')->get();
        return view('aziende.create', compact('blocks', 'users'));
    }

    public function store(Request $request)
    {
        $blocks = $this->getModuleStructure();
        $rules = $this->buildValidationRules($blocks);

        // --- MODIFICA QUI ---
        $rules['province_id'] = 'nullable|integer|exists:provinces,id';
        $rules['city_id'] = 'nullable|integer|exists:cities,id';
        $rules['postal_code_id'] = 'nullable|integer|exists:postal_codes,id';

        $validatedData = Validator::make($request->all(), $rules)->validate();
        $companyData = $validatedData;
        $customData = $validatedData['custom_fields_data'] ?? [];
        unset($companyData['custom_fields_data']);
        if (!empty($customData)) { $companyData['custom_fields_data'] = json_encode($customData); }
        try {
            $company = Company::create($companyData);
            return redirect()->route('companies.show', $company->id)->with('success', 'Azienda creata con successo!');
        } catch (\Exception $e) {
            Log::error('Errore creazione Azienda: ' . $e->getMessage() . ' | Dati: ' . json_encode($companyData));
            return back()->withInput()->with('error', 'Si è verificato un errore imprevisto durante il salvataggio.');
        }
    }

    public function show($id)
    {
        $company = Company::with(['assignedTo', 'mainContact', 'contacts', 'opportunities', 'customerServices.serviceType', 'notes' => fn($q) => $q->latest(), 'activities' => fn($q) => $q->latest()])->findOrFail($id);
        $timelineItems = $company->notes->merge($company->activities)->sortByDesc('created_at');
        $serviceTypes = ServiceType::all();
        return view('aziende.show', compact('company', 'timelineItems', 'serviceTypes'));
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $blocks = $this->getModuleStructure();
        $users = User::orderBy('name')->get();
        return view('aziende.edit', compact('company', 'blocks', 'users'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $blocks = $this->getModuleStructure();
        $rules = $this->buildValidationRules($blocks);

        // --- MODIFICA QUI ---
        $rules['province_id'] = 'nullable|integer|exists:provinces,id';
        $rules['city_id'] = 'nullable|integer|exists:cities,id';
        $rules['postal_code_id'] = 'nullable|integer|exists:postal_codes,id';

        $validatedData = Validator::make($request->all(), $rules)->validate();
        $companyData = $validatedData;
        $customDataFromRequest = $validatedData['custom_fields_data'] ?? [];
        unset($companyData['custom_fields_data']);
        $existingCustomData = $company->custom_fields_data;
        if (!is_array($existingCustomData)) { $existingCustomData = json_decode($existingCustomData, true) ?? []; }
        $mergedCustomData = array_merge($existingCustomData, $customDataFromRequest);
        if (!empty($mergedCustomData)) { $companyData['custom_fields_data'] = json_encode($mergedCustomData); }
        try {
            $company->update($companyData);
            return redirect()->route('companies.show', $company->id)->with('success', 'Azienda aggiornata con successo!');
        } catch (\Exception $e) {
            Log::error('Errore aggiornamento Azienda: ' . $e->getMessage() . ' | Dati: ' . json_encode($companyData));
            return back()->withInput()->with('error', 'Si è verificato un errore imprevisto durante l\'aggiornamento.');
        }
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Azienda eliminata con successo!');
    }

    public function export()
    {
        return Excel::download(new CompaniesExport, 'companies-' . now()->format('Y-m-d') . '.csv');
    }
}