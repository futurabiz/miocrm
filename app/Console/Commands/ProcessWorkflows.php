<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Workflow;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessWorkflows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-workflows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa tutti i workflow attivi e esegue le azioni necessarie';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Inizio processamento dei workflow...');

        $activeWorkflows = Workflow::where('is_active', true)->get();

        if ($activeWorkflows->isEmpty()) {
            $this->info('Nessun workflow attivo da processare.');
            return 0;
        }

        foreach ($activeWorkflows as $workflow) {
            $this->line("Processando workflow: '{$workflow->name}'");

            try {
                $modelClass = $workflow->trigger_model;
                $query = $modelClass::query();

                $field = $workflow->trigger_condition_field;
                $operator = $workflow->trigger_condition_operator;
                $value = $this->parseDynamicValue($workflow->trigger_condition_value);

                $recordsToProcess = $query->where($field, $operator, $value)->get();

                if ($recordsToProcess->isEmpty()) {
                    $this->line(" -> Nessun record trovato che soddisfa la condizione.");
                    continue;
                }

                $this->info(" -> Trovati {$recordsToProcess->count()} record. Esecuzione azione...");

                foreach ($recordsToProcess as $record) {
                    $this->executeAction($workflow, $record);
                }
            } catch (\Exception $e) {
                $this->error(" -> Errore durante il processamento del workflow ID {$workflow->id}: " . $e->getMessage());
                Log::error("Errore workflow ID {$workflow->id}: " . $e->getMessage());
            }
        }

        $this->info('Processamento dei workflow completato.');
        return 0;
    }

    /**
     * Esegue l'azione definita nel workflow per un dato record.
     */
    private function executeAction(Workflow $workflow, $record)
    {
        switch ($workflow->action_type) {
            case 'create_activity':
                $this->createActivityAction($workflow->action_parameters, $record);
                break;
            
            // --- NUOVA AZIONE ---
            case 'update_field':
                $this->updateFieldAction($workflow->action_parameters, $record);
                break;

            default:
                $this->warn(" -> Azione '{$workflow->action_type}' non riconosciuta.");
                break;
        }
    }

    /**
     * Logica per l'azione 'create_activity'.
     */
    private function createActivityAction(array $parameters, $relatedRecord)
    {
        $existingActivity = Activity::where('title', $parameters['title'])
                                    ->where('activityable_id', $relatedRecord->id)
                                    ->where('activityable_type', get_class($relatedRecord))
                                    ->whereDate('created_at', Carbon::today()) // Evita duplicati nello stesso giorno
                                    ->first();

        if ($existingActivity) {
            $this->line(" -> Attività '{$parameters['title']}' già esistente per il record ID {$relatedRecord->id}. Salto.");
            return;
        }

        Activity::create([
            'title' => $parameters['title'],
            'type' => $parameters['type'] ?? 'task',
            'start_time' => Carbon::now(),
            'activityable_id' => $relatedRecord->id,
            'activityable_type' => get_class($relatedRecord),
        ]);

        $this->info(" -> Creata attività '{$parameters['title']}' per il record ID {$relatedRecord->id}.");
    }

    /**
     * NUOVA LOGICA: per l'azione 'update_field'.
     */
    private function updateFieldAction(array $parameters, $record)
    {
        $fieldToUpdate = $parameters['field_to_update'] ?? null;
        $newValue = $parameters['new_value'] ?? null;

        if (!$fieldToUpdate || is_null($newValue)) {
            $this->warn(" -> Parametri mancanti per l'azione 'update_field' sul record ID {$record->id}.");
            return;
        }

        // Controlla se il campo esiste nel modello per sicurezza
        if (!array_key_exists($fieldToUpdate, $record->getAttributes())) {
             $this->warn(" -> Il campo '{$fieldToUpdate}' non esiste sul modello per il record ID {$record->id}.");
            return;
        }

        $record->{$fieldToUpdate} = $newValue;
        $record->save();

        $this->info(" -> Aggiornato campo '{$fieldToUpdate}' a '{$newValue}' per il record ID {$record->id}.");
    }


    /**
     * Interpreta valori di data dinamici.
     */
    private function parseDynamicValue(string $value)
    {
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return $value;
        }
    }
}
