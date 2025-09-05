<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanySearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $companies = Company::where('name', 'like', '%'.$query.'%')
                            ->orWhere('email', 'like', '%'.$query.'%')
                            ->limit(10) 
                            ->get();

        $results = [];
        foreach ($companies as $company) {
            $results[] = [
                'id' => $company->id,
                'text' => $company->name
            ];
        }

        // MODIFICATO QUI: Restituisci un oggetto JSON con la chiave 'results'.
        // Questo è il formato standard che Select2 si aspetta per il suo 'processResults'.
        return response()->json(['results' => $results]); 
    }
}
