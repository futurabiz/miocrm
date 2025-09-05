<?php

namespace App\Exports;

use App\Models\Lead;
use App\Models\ModuleBlock; // Modificato per usare ModuleBlock
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection; // Corretto per usare Illuminate\Support\Collection

class LeadsExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $fields;

    public function __construct()
    {
        // Carica tutti i campi, ordinati per blocco e per ordine interno, e li appiattisce in una singola collezione
        $this->fields = ModuleBlock::where('module_class', Lead::class)
            ->with(['fields' => fn($q) => $q->orderBy('order')])
            ->orderBy('order')
            ->get()
            ->pluck('fields')
            ->flatten();
    }

    /**
    * Recupera la collezione di dati da esportare.
    */
    public function collection()
    {
        // Seleziona solo i campi necessari per l'esportazione per ottimizzare la query
        $fieldNames = $this->fields->where('is_standard', true)->pluck('name')->toArray();
        return Lead::with('company', 'assignedTo')->select(array_merge(['id', 'custom_fields_data', 'created_at'], $fieldNames))->get();
    }

    /**
     * Crea dinamicamente le intestazioni del CSV.
     */
    public function headings(): array
    {
        return $this->fields->pluck('label')->toArray();
    }

    /**
     * Mappa dinamicamente i dati di ogni lead.
     */
    public function map($lead): array
    {
        $row = [];
        
        foreach ($this->fields as $field) {
            $fieldName = $field->name;

            if ($field->is_standard) {
                // Gestione speciale per le relazioni
                if ($fieldName === 'company_id') {
                    $row[] = $lead->company->name ?? '';
                } elseif ($fieldName === 'assigned_to_id') {
                    $row[] = $lead->assignedTo->name ?? '';
                } elseif ($lead->{$fieldName} instanceof \Carbon\Carbon) {
                    // Formatta correttamente le date
                    $row[] = $lead->{$fieldName}->format('d/m/Y H:i');
                }
                else {
                    $row[] = $lead->{$fieldName};
                }
            } else {
                // Per i campi personalizzati
                $row[] = $lead->custom_fields_data[$fieldName] ?? '';
            }
        }
        
        return $row;
    }
}