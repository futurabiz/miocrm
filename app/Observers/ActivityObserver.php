<?php

namespace App\Observers;

use App\Models\Activity;
use App\Observers\Traits\HandlesWorkflowActions;

class ActivityObserver
{
    use HandlesWorkflowActions;

    public function created(Activity $activity): void
    {
        $this->triggerWorkflows($activity);
    }

    public function updated(Activity $activity): void
    {
        $this->triggerWorkflows($activity);
    }
}