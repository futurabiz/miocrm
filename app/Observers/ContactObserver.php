<?php

namespace App\Observers;

use App\Models\Contact;
use App\Observers\Traits\HandlesWorkflowActions;

class ContactObserver
{
    use HandlesWorkflowActions;

    public function created(Contact $contact): void
    {
        // Chiama il nuovo metodo del trait, che a sua volta chiama il WorkflowEngine
        $this->triggerWorkflows($contact);
    }

    public function updated(Contact $contact): void
    {
        $this->triggerWorkflows($contact);
    }
}