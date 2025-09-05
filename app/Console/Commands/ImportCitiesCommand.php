<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use Illuminate\Support\Facades\Storage;

class ImportCitiesCommand extends Command
{
    protected $signature = 'crm:import-cities';
    protected $description = 'Importa o aggiorna i comuni italiani dal file comuni.json (modalità forzata).';

    public function handle()
    {
        $this->info("▶️ Inizio importazione finale (modalità forzata)...");
        
        $fileName = 'comuni.json';
        if (!Storage::disk('local')->exists($fileName)) {
            $this->error("❌ File '{$fileName}' non trovato in 'storage/app/'.");
            return 1;
        }

        $jsonContent = Storage::disk('local')->get($fileName);
        $comuni = json_decode($jsonContent);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("❌ Errore nel file JSON: " . json_last_error_msg());
            return 1;
        }

        $bar = $this->output->createProgressBar(count($comuni));
        $bar->start();

        $updatedCount = 0;

        foreach ($comuni as $comune) {
            if (empty($comune->codice_catastale)) {
                $bar->advance();
                continue;
            }

            // Trova il record esistente o crea una nuova istanza (senza salvarla)
            $city = City::firstOrNew(['fiscal_code' => $comune->codice_catastale]);

            // Popola il modello con i nuovi dati
            $city->name = $comune->nome ?? $city->name;
            $city->province_code = $comune->sigla ?? $city->province_code;
            $city->alphanumeric_code = $comune->codice ?? $city->alphanumeric_code;
            $city->cap = (isset($comune->cap) && count($comune->cap) > 0) ? $comune->cap[0] : $city->cap;
            
            // Controlliamo se ci sono effettive modifiche prima di salvare
            if ($city->isDirty()) {
                $city->save();
                $updatedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        
        $this->info("\n\n✅ Importazione completata!");
        $this->line("   - Comuni aggiornati con CAP: <fg->{$updatedCount}</>");
        
        return 0;
    }
}