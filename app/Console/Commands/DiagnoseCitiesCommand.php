<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use Illuminate\Support\Facades\Storage;

class DiagnoseCitiesCommand extends Command
{
    protected $signature = 'crm:diagnose-cities';
    protected $description = 'Diagnostica avanzata: confronta un singolo record (Villabate) dal DB e dal JSON.';

    public function handle()
    {
        $this->info("▶️ Inizio diagnostica avanzata...");

        $fileName = 'comuni.json';
        if (!Storage::disk('local')->exists($fileName)) {
            $this->error("❌ File '{$fileName}' non trovato in 'storage/app/'.");
            return 1;
        }
        $jsonContent = Storage::disk('local')->get($fileName);
        $comuniDaFile = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("❌ Errore nel file JSON: " . json_last_error_msg());
            return 1;
        }
        $this->info("✅ File JSON letto e decodificato correttamente.");

        $villabateFromJson = null;
        foreach ($comuniDaFile as $comune) {
            // *** CORREZIONE: Uso 'codiceCatastale' (camelCase) invece di 'codice_catastale' ***
            if (($comune['codiceCatastale'] ?? '') === 'L916') {
                $villabateFromJson = $comune;
                break;
            }
        }

        if (!$villabateFromJson) {
            $this->error("❌ Non ho trovato Villabate (codice L916) nel file comuni.json.");
            return 1;
        }

        $this->line("\n--- Dati da file JSON per Villabate (L916) ---");
        dump($villabateFromJson);

        $this->line("\n--- Tentativo di recupero dati dal Database per Villabate (L916) ---");
        $villabateFromDb = City::where('fiscal_code', 'L916')->first();

        if (!$villabateFromDb) {
            $this->error("❌ DRAMMATICO: Non ho trovato Villabate nel database usando il codice 'L916'.");
            return 1;
        }

        $this->info("✅ Record trovato nel database!");
        $this->line("\n--- Dati dal Database per Villabate (L916) ---");
        dump($villabateFromDb->toArray());

        $this->line("\n--- CONCLUSIONE DIAGNOSTICA ---");
        $capFromJson = (is_array($villabateFromJson['cap']) && count($villabateFromJson['cap']) > 0) ? $villabateFromJson['cap'][0] : null;
        $capFromDb = $villabateFromDb->cap;

        $this->info("CAP da JSON: " . ($capFromJson ?? 'NULL'));
        $this->info("CAP da DB:   " . ($capFromDb ?? 'NULL'));

        if ($capFromJson === $capFromDb) {
            $this->warn("I CAP sono identici. Questo spiega perché l'aggiornamento non avviene.");
        } else {
            $this->info("I CAP sono diversi. L'aggiornamento DOVREBBE avvenire. C'è un'anomalia profonda nel sistema.");
        }
        
        return 0;
    }
}