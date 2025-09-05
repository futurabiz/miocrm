<?php

namespace App\Exports;

use App\Models\Contact;
use App\Models\ModuleBlock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ContactsExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $fields;

    public function __construct()
    {
        $this->fields = ModuleBlock::where('module_class', Contact::class)
            ->with(['fields' => fn($q) => $q->orderBy('order')])
            ->orderBy('order')
            ->get()
            ->pluck('fields')
            ->flatten();
    }

    public function collection()
    {
        return Contact::with('company', 'assignedTo')->get();
    }

    public function headings(): array
    {
        return $this->fields->pluck('label')->toArray();
    }

    public function map($contact): array
    {
        $row = [];
        foreach ($this->fields as $field) {
            $fieldName = $field->name;
            if ($field->is_standard) {
                if ($fieldName === 'company_id') {
                    $row[] = $contact->company->name ?? '';
                } elseif ($fieldName === 'assigned_to_id') {
                    $row[] = $contact->assignedTo->name ?? '';
                } elseif ($contact->{$fieldName} instanceof \Carbon\Carbon) {
                    $row[] = $contact->{$fieldName}->format('d/m/Y');
                } else {
                    $row[] = $contact->{$fieldName};
                }
            } else {
                $row[] = $contact->custom_fields_data[$fieldName] ?? '';
            }
        }
        return $row;
    }
}