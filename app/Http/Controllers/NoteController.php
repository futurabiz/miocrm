<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NoteController extends Controller
{
    /**
     * Salva una nuova nota e la collega al modello corretto (Azienda, Contatto, ecc.).
     */
    public function store(Request $request)
    {
        // 1. Valida i dati ricevuti dal form
        $request->validate([
            'content' => 'required|string',
            'notable_id' => 'required|integer',
            'notable_type' => 'required|string',
        ]);

        try {
            // 2. Trova il "tipo" di modello (es. 'App\Models\Company')
            $modelType = $request->input('notable_type');
            
            // 3. Trova il record specifico (es. l'Azienda con ID 5)
            $model = $modelType::findOrFail($request->input('notable_id'));
            
            // 4. Usa la relazione polimorfica per creare la nota
            // Laravel si occuperà di riempire automaticamente notable_id e notable_type
            $model->notes()->create([
                'content' => $request->input('content')
            ]);

            return back()->with('success', 'Nota aggiunta con successo.');

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Impossibile trovare il record a cui allegare la nota.');
        } catch (\Exception $e) {
            // Per qualsiasi altro errore
            return back()->with('error', 'Si è verificato un errore imprevisto.');
        }
    }
}
