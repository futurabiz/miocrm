<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Street;
use Illuminate\Support\Facades\Validator;

class StreetController extends Controller
{
    /**
     * Search for streets based on a query and municipality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        // 1. Validazione dell'input
        // Ci assicuriamo che la richiesta contenga sempre l'ID del comune.
        $validator = Validator::make($request->all(), [
            'municipality_id' => 'required|integer|exists:municipalities,id',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['results' => []]);
        }
        
        // 2. Query al Database
        $searchTerm = $request->input('search', '');
        $municipalityId = $request->input('municipality_id');

        $streets = Street::where('municipality_id', $municipalityId)
                         ->where('name', 'LIKE', '%' . $searchTerm . '%')
                         ->orderBy('name', 'asc')
                         ->take(20) // Limitiamo i risultati a 20 per non sovraccaricare il browser
                         ->get(['id', 'name as text']); // Selezioniamo solo i campi che servono a Select2

        // 3. Restituiamo i dati in formato JSON
        // Select2 si aspetta un oggetto con una chiave 'results'.
        return response()->json(['results' => $streets]);
    }
}