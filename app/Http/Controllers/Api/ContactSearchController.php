<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact; // Assicurati che il nome del tuo modello Contatto sia 'Contact'

class ContactSearchController extends Controller
{
    /**
     * Cerca contatti per nome, cognome o email per Select2 AJAX.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('q'); // 'q' è il parametro di ricerca di Select2
        $results = [];

        if (empty($searchTerm)) {
            return response()->json(['results' => []]);
        }

        // Cerca i contatti nel database
        $contacts = Contact::where('first_name', 'like', '%' . $searchTerm . '%')
                           ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                           ->orWhere('email', 'like', '%' . $searchTerm . '%')
                           ->limit(20) // Limita i risultati per performance
                           ->get(['id', 'first_name', 'last_name', 'email']); // Seleziona solo i campi necessari

        foreach ($contacts as $contact) {
            $text = trim($contact->first_name . ' ' . $contact->last_name);
            if (!empty($contact->email)) {
                $text .= ' (' . $contact->email . ')';
            }
            $results[] = [
                'id' => $contact->id,
                'text' => $text
            ];
        }

        return response()->json(['results' => $results]);
    }
}