<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue; // Per il processamento in background

class CrmDataImporter implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    protected string $modelClass;
    protected array $fieldMap;

    public function __construct(string $modelClass, array $fieldMap)
    {
        $this->modelClass = $modelClass;
        $this->fieldMap = $fieldMap;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $dataToInsert = [];

        // Usiamo la mappatura per costruire l'array dei dati da inserire
        foreach ($this->fieldMap as $crmField => $csvHeading) {
            if (isset($row[$csvHeading])) {
                $dataToInsert[$crmField] = $row[$csvHeading];
            }
        }

        // Se non abbiamo mappato nessun campo, saltiamo la riga
        if (empty($dataToInsert)) {
            return null;
        }

        // Creiamo il nuovo record del modello specificato (Lead, Contact, etc.)
        return new $this->modelClass($dataToInsert);
    }

    public function chunkSize(): int
    {
        return 100; // Processa 100 righe alla volta
    }
}