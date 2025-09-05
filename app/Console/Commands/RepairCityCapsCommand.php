<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RepairCityCapsCommand extends Command
{
    /**
     * La firma del comando. Lo eseguiremo con 'php artisan crm:repair-caps'
     */
    protected $signature = 'crm:repair-caps';

    /**
     * La descrizione del comando.
     */
    protected $description = 'Riparazione chirurgica: aggiorna solo i CAP dei comuni esistenti usando il file comuni.json';

    /**
     * Esegue la logica del comando.
     */
    public function handle()
    {
        $this->info("▶️ Inizio riparazione dei CAP nel database...");

        $fileName = 'comuni.json';
        if (!Storage::disk('local')->exists($fileName)) {
            $this->error("❌ File '{$fileName}' non trovato in 'storage/app/'. Assicurati che sia presente.");
            return 1;
        }

        $jsonContent = Storage::disk('local')->get($fileName);
        $comuniDaFile = json_decode($jsonContent, true); // Decodifico come array associativo

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("❌ Errore nella lettura del file JSON: " . json_last_error_msg());
            return 1;
        }

        $bar = $this->output->createProgressBar(count($comuniDaFile));
        $bar->start();

        $updatedCount = 0;

        // Usiamo una transazione per sicurezza e velocità
        DB::transaction(function () use ($comuniDaFile, &$updatedCount, $bar) {
            foreach ($comuniDaFile as $comune) {
                
                $fiscalCode = $comune['codice_catastale'] ?? null;
                $capArray = $comune['cap'] ?? [];
                $cap = (is_array($capArray) && count($capArray) > 0) ? $capArray[0] : null;

                // Se mancano i dati essenziali, salta al prossimo
                if (!$fiscalCode || !$cap) {
                    $bar->advance();
                    continue;
                }

                // Eseguiamo un aggiornamento diretto e "grezzo" sul database.
                // Questo bypassa i problemi di Eloquent e forza l'aggiornamento.
                $affectedRows = DB::table('cities')
                    ->where('fiscal_code', $fiscalCode)
                    ->update(['cap' => $cap]);

                if ($affectedRows > 0) {
                    $updatedCount++;
                }
                
                $bar->advance();
            }
        });

        $bar->finish();
        
        $this->info("\n\n✅ Riparazione completata!");
        $this->line("   - Comuni il cui CAP è stato riparato/aggiornato: <fg=yellow>{$updatedCount}</>");

        return 0;
    }
}