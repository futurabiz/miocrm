<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\ModuleBlock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class CompaniesExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $fields;

    public function __construct()
    {
        $this->fields = ModuleBlock::where('module_class', Company::class)
            ->with(['fields' => fn($q) => $q->orderBy('order')])
            ->orderBy('order')
            ->get()
            ->pluck('fields')
            ->flatten();
    }

    public function collection()
    {
        return Company::with('assignedTo', 'mainContact')->get();
    }

    public function headings(): array
    {
        return $this->fields->pluck('label')->toArray();
    }

    public function map($company): array
    {
        $row = [];
        foreach ($this->fields as $field) {
            $fieldName = $field->name;
            if ($field->is_standard) {
                if ($fieldName === 'main_contact_id') {
                    $row[] = $company->mainContact->full_name ?? '';
                } elseif ($fieldName === 'assigned_to_id') {
                    $row[] = $company->assignedTo->name ?? '';
                } elseif ($company->{$fieldName} instanceof \Carbon\Carbon) {
                    $row[] = $company->{$fieldName}->format('d/m/Y');
                } else {
                    $row[] = $company->{$fieldName};
                }
            } else {
                $row[] = $company->custom_fields_data[$fieldName] ?? '';
            }
        }
        return $row;
    }
}