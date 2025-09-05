<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Company;
use App\Models\User;
use App\Models\ModuleBlock;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\HandlesListViews;
use App\Exports\ContactsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use robertogallea\LaravelCodiceFiscale\CodiceFiscale;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    use HandlesListViews;

    private function getModuleStructure()
    {
        return ModuleBlock::where('module_class', Contact::class)
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
                    case 'date': $fieldRules[] = 'date'; break;
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
        $defaultColumns = ['first_name', 'last_name', 'company_id', 'email', 'phone'];
        $columnLabels = [
            'first_name' => 'Nome', 'last_name' => 'Cognome', 'salutation' => 'Saluto', 'codice_fiscale' => 'Codice Fiscale', 'email' => 'Email', 'phone' => 'Telefono Fisso', 'mobile_phone' => 'Cellulare', 'company_id' => 'Azienda', 'role' => 'Ruolo', 'assigned_to_id' => 'Assegnato a', 'description' => 'Descrizione', 'address_street' => 'Via', 'address_city' => 'Città', 'address_state' => 'Provincia', 'address_postalcode' => 'CAP', 'address_country' => 'Nazione', 'created_at' => 'Data Creazione', 'updated_at' => 'Ultima Modifica',
        ];
        $viewData = $this->getListViewData($request, Contact::class, $defaultColumns, $columnLabels);
        $query = Contact::query();
        if(in_array('company_id', $viewData['currentView']->columns)) { $query->with('company'); }
        if(in_array('assigned_to_id', $viewData['currentView']->columns)) { $query->with('assignedTo'); }
        $contacts = $query->latest()->paginate(15);

        // --- MODIFICA QUI: Aggiunta la variabile 'columns' mancante ---
        return view('contatti.index', array_merge($viewData, [
            'contacts' => $contacts,
            'columns' => $viewData['currentView']->columns
        ]));
        // --- FINE MODIFICA ---
    }

    public function create()
    {
        $blocks = $this->getModuleStructure();
        $users = User::orderBy('name')->get();
        return view('contatti.create', compact('blocks', 'users'));
    }

    public function store(Request $request)
    {
        $blocks = $this->getModuleStructure();
        $rules = $this->buildValidationRules($blocks);
        
        $rules['province_id'] = 'nullable|integer|exists:provinces,id';
        $rules['city_id'] = 'nullable|integer|exists:cities,id';
        $rules['postal_code_id'] = 'nullable|integer|exists:postal_codes,id';

        $validatedData = Validator::make($request->all(), $rules)->validate();
        try { $this->validateFiscalCode($request); } catch (ValidationException $e) { return back()->withInput()->withErrors($e->errors()); }
        
        $contactData = $validatedData;
        $customData = $validatedData['custom_fields_data'] ?? [];
        unset($contactData['custom_fields_data']);
        if (!empty($customData)) { $contactData['custom_fields_data'] = json_encode($customData); }
        
        try {
            Contact::create($contactData);
            return redirect()->route('contacts.index')->with('success', 'Contatto creato con successo!');
        } catch (\Exception $e) {
            Log::error('Errore creazione Contatto: ' . $e->getMessage() . ' | Dati: ' . json_encode($contactData));
            return back()->withInput()->with('error', 'Si è verificato un errore imprevisto durante il salvataggio.');
        }
    }

    public function show(Contact $contact)
    {
        $contact->load('company', 'assignedTo', 'notes.user', 'activities.user', 'opportunities', 'emailLists', 'tags');
        $timelineItems = $contact->notes->concat($contact->activities)->sortByDesc('created_at');
        return view('contatti.show', compact('contact', 'timelineItems'));
    }

    public function edit(Contact $contact)
    {
        $blocks = $this->getModuleStructure();
        $users = User::orderBy('name')->get();
        return view('contatti.edit', compact('contact', 'blocks', 'users'));
    }

    public function update(Request $request, Contact $contact)
    {
        $blocks = $this->getModuleStructure();
        $rules = $this->buildValidationRules($blocks);
        
        $rules['province_id'] = 'nullable|integer|exists:provinces,id';
        $rules['city_id'] = 'nullable|integer|exists:cities,id';
        $rules['postal_code_id'] = 'nullable|integer|exists:postal_codes,id';

        $validatedData = Validator::make($request->all(), $rules)->validate();
        try { $this->validateFiscalCode($request); } catch (ValidationException $e) { return back()->withInput()->withErrors($e->errors()); }
        
        $contactData = $validatedData;
        $customDataFromRequest = $validatedData['custom_fields_data'] ?? [];
        unset($contactData['custom_fields_data']);
        $existingCustomData = $contact->custom_fields_data;
        if (!is_array($existingCustomData)) { $existingCustomData = json_decode($existingCustomData, true) ?? []; }
        $mergedCustomData = array_merge($existingCustomData, $customDataFromRequest);
        if (!empty($mergedCustomData)) { $contactData['custom_fields_data'] = json_encode($mergedCustomData); }
        
        try {
            $contact->update($contactData);
            return redirect()->route('contacts.show', $contact->id)->with('success', 'Contatto aggiornato con successo!');
        } catch (\Exception $e) {
            Log::error('Errore aggiornamento Contatto: ' . $e->getMessage() . ' | Dati: ' . json_encode($contactData));
            return back()->withInput()->with('error', 'Si è verificato un errore imprevisto durante l\'aggiornamento.');
        }
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contatto eliminato con successo!');
    }

    public function export()
    {
        return Excel::download(new ContactsExport, 'contatti-' . now()->format('Y-m-d') . '.csv');
    }
    
    private function validateFiscalCode(Request $request) { /* ... logica invariata ... */ }
}