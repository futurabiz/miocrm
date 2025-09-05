<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;

class LocationController extends Controller
{
    /**
     * Restituisce la lista di tutte le regioni.
     */
    public function regions(Request $request)
    {
        $regions = Region::orderBy('name')->get(['id', 'name as text']);
        return response()->json(['results' => $regions]);
    }

    /**
     * Restituisce la lista delle province per una data regione.
     */
    public function provinces(Request $request, $regionId)
    {
        $provinces = Province::where('region_id', $regionId)
            ->orderBy('name')
            ->get(['id', 'name as text']);
        return response()->json(['results' => $provinces]);
    }

    /**
     * Restituisce la lista dei comuni per una data provincia.
     */
    public function cities(Request $request, $provinceId)
    {
        $cities = City::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name as text']);
        return response()->json(['results' => $cities]);
    }

    /**
     * Restituisce i dettagli di un singolo comune, inclusa la LISTA dei CAP.
     */
    public function cityDetails(Request $request, $cityId)
    {
        // Carica il comune insieme alla sua relazione 'postalCodes', selezionando solo i campi necessari
        $city = City::with('postalCodes:id,city_id,code')->find($cityId);
        return response()->json($city);
    }
}