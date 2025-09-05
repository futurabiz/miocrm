<?php

namespace App\Policies;

use App\Models\ListView;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ListViewPolicy
{
    /**
     * Determina se l'utente può aggiornare il modello.
     */
    public function update(User $user, ListView $listView): bool
    {
        // L'utente può aggiornare la vista solo se ne è il proprietario.
        return $user->id === $listView->user_id;
    }

    /**
     * Determina se l'utente può eliminare il modello.
     */
    public function delete(User $user, ListView $listView): bool
    {
        // L'utente può eliminare la vista solo se ne è il proprietario.
        return $user->id === $listView->user_id;
    }
}