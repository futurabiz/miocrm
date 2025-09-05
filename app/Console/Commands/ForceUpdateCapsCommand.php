<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ForceUpdateCapsCommand extends Command
{
    /**
     * La firma del comando.
     */
    protected $signature = 'crm:force-update-caps';

    /**
     * La descrizione del comando.
     */
    protected $description = 'Forza l\'aggiornamento dei CAP usando query SQL dirette, bypassando Eloquent.';

    /**
     * Esegue la logica del comando.
     */
    public function handle()
    {
        $this->info("▶️ Inizio aggiornamento forzato dei CAP...");

        $fileName = 'comuni.json';
        if (!Storage::disk('local')->exists($fileName)) {
            $this->error("❌ File '{$fileName}' non trovato in 'storage/app/'.");
            return 1;
        }

        $jsonContent = Storage::disk('local')->get($fileName);
        $comuniDaFile = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("❌ Errore nella lettura del file JSON: " . json_last_error_msg());
            return 1;
        }

        $bar = $this->output->createProgressBar(count($comuniDaFile));
        $bar->start();

        $updatedCount = 0;

        foreach ($comuniDaFile as $comune) {
            
            $fiscalCode = $comune['codiceCatastale'] ?? null;
            $capArray = $comune['cap'] ?? [];
            $cap = (is_array($capArray) && count($capArray) > 0) ? $capArray[0] : null;

            if (!$fiscalCode || !$cap) {
                $bar->advance();
                continue;
            }

            // Eseguiamo un aggiornamento SQL diretto. Questo è un ordine, non una richiesta.
            $affectedRows = DB::table('cities')
                ->where('fiscal_code', $fiscalCode)
                ->update(['cap' => $cap]);

            if ($affectedRows > 0) {
                $updatedCount++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        
        $this->info("\n\n✅ Aggiornamento forzato completato!");
        $this->line("   - Comuni il cui CAP è stato aggiornato: <fg=yellow>{$updatedCount}</>");

        return 0;
    }
}