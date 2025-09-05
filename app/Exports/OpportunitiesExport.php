<?php

namespace App\Exports;

use App\Models\Opportunity;
use App\Models\ModuleBlock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class OpportunitiesExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $fields;

    public function __construct()
    {
        $this->fields = ModuleBlock::where('module_class', Opportunity::class)
            ->with(['fields' => fn($q) => $q->orderBy('order')])
            ->orderBy('order')
            ->get()
            ->pluck('fields')
            ->flatten();
    }

    public function collection()
    {
        return Opportunity::with('company', 'contact', 'assignedTo')->get();
    }

    public function headings(): array
    {
        return $this->fields->pluck('label')->toArray();
    }

    public function map($opportunity): array
    {
        $row = [];
        foreach ($this->fields as $field) {
            $fieldName = $field->name;
            if ($field->is_standard) {
                if ($fieldName === 'company_id') {
                    $row[] = $opportunity->company->name ?? '';
                } elseif ($fieldName === 'contact_id') {
                    $row[] = $opportunity->contact->full_name ?? '';
                } elseif ($fieldName === 'assigned_to_id') {
                    $row[] = $opportunity->assignedTo->name ?? '';
                } elseif ($opportunity->{$fieldName} instanceof \Carbon\Carbon) {
                    $row[] = $opportunity->{$fieldName}->format('d/m/Y');
                } else {
                    $row[] = $opportunity->{$fieldName};
                }
            } else {
                $row[] = $opportunity->custom_fields_data[$fieldName] ?? '';
            }
        }
        return $row;
    }
}