<?php

namespace App\Http\Controllers;

use App\Models\ListView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- 1. AGGIUNTO USE

class ListViewController extends Controller
{
    use AuthorizesRequests; // <-- 2. AGGIUNTO TRAIT

    /**
     * Salva una nuova vista personalizzata e restituisce una risposta JSON.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'module_class' => 'required|string',
            'columns' => 'required|array|min:1',
        ]);

        try {
            $listView = ListView::create([
                'name' => $validatedData['name'],
                'module_class' => $validatedData['module_class'],
                'columns' => $validatedData['columns'],
                'user_id' => auth()->id(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Vista personalizzata salvata con successo.',
                'view_id' => $listView->id
            ]);

        } catch (\Exception $e) {
            Log::error('Errore salvataggio vista: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Errore interno del server.'], 500);
        }
    }
    
    /**
     * Aggiorna una vista esistente e restituisce una risposta JSON.
     */
    public function update(Request $request, ListView $listView)
    {
        $this->authorize('update', $listView);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'columns' => 'required|array|min:1',
        ]);
        
        try {
            $listView->update([
                'name' => $validatedData['name'],
                'columns' => $validatedData['columns'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vista aggiornata con successo.',
                'view_id' => $listView->id
            ]);

        } catch (\Exception $e) {
            Log::error('Errore aggiornamento vista: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Errore interno del server.'], 500);
        }
    }

    /**
     * Rimuove una vista personalizzata e restituisce una risposta JSON.
     */
    public function destroy(ListView $listView)
    {
        $this->authorize('delete', $listView);
        
        $listView->delete();

        return redirect()->back()->with('success', 'Vista eliminata con successo.');
    }
}