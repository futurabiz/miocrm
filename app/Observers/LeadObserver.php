<?php

namespace App\Observers;

use App\Models\Lead;
use App\Observers\Traits\HandlesWorkflowActions;

class LeadObserver
{
    use HandlesWorkflowActions;

    public function created(Lead $lead): void
    {
        $this->triggerWorkflows($lead);
    }

    public function updated(Lead $lead): void
    {
        $this->triggerWorkflows($lead);
    }
}