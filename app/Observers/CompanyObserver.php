<?php

namespace App\Observers;

use App\Models\Company;
use App\Observers\Traits\HandlesWorkflowActions;

class CompanyObserver
{
    use HandlesWorkflowActions;

    public function created(Company $company): void
    {
        $this->triggerWorkflows($company);
    }

    public function updated(Company $company): void
    {
        $this->triggerWorkflows($company);
    }
}