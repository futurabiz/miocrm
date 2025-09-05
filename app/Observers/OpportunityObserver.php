<?php

namespace App\Observers;

use App\Models\Opportunity;
use App\Observers\Traits\HandlesWorkflowActions;

class OpportunityObserver
{
    use HandlesWorkflowActions;

    public function created(Opportunity $opportunity): void
    {
        $this->triggerWorkflows($opportunity);
    }

    public function updated(Opportunity $opportunity): void
    {
        $this->triggerWorkflows($opportunity);
    }
}