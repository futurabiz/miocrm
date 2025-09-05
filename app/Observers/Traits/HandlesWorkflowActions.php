<?php

namespace App\Observers\Traits;

use App\Services\WorkflowEngine;
use Illuminate\Database\Eloquent\Model;

trait HandlesWorkflowActions
{
    /**
     * Delega l'esecuzione dei controlli del workflow alla Service Class dedicata.
     */
    protected function triggerWorkflows(Model $model): void
    {
        // Risolve la service class dal container di Laravel e chiama il suo metodo run.
        // Questo approccio è pulito, testabile e non ha problemi di visibilità.
        resolve(WorkflowEngine::class)->run($model);
    }
}