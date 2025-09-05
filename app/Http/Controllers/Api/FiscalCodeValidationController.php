<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use robertogallea\LaravelCodiceFiscale\CodiceFiscale; // Importa la classe CodiceFiscale
use App\Models\City; // Importa il modello City

class FiscalCodeValidationController extends Controller
{
    /**
     * Valida il Codice Fiscale in base ai dati anagrafici forniti.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateFiscalCode(Request $request)
    {
        // Validazione dei dati in ingresso
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:M,F',
            'birthdate' => 'required|date',
            'city_code' => 'required|string', // Codice fiscale del comune di nascita (Belfiore)
            'fiscal_code' => 'nullable|string|max:16', // Il CF da validare
        ]);

        // Pulisci e normalizza i dati in ingresso
        $firstName = trim($request->input('first_name'));
        $lastName = trim($request->input('last_name'));
        $birthdate = $request->input('birthdate'); // La data è già in formato YYYY-MM-DD
        $gender = trim($request->input('gender'));
        $fiscalCode = strtoupper(trim($request->input('fiscal_code'))); // Converte in maiuscolo e pulisce
        $cityCode = trim($request->input('city_code')); // Pulisce il codice del comune

        // Recupera il codice fiscale (Belfiore) del comune dal database
        $city = City::where('fiscal_code', $cityCode)->first();
        $birthPlaceCode = $city ? $city->fiscal_code : null;

        // Se il codice fiscale del comune non è stato trovato, la validazione fallisce
        if (!$birthPlaceCode) {
            return response()->json(['is_valid' => false, 'error' => 'Codice fiscale del comune di nascita non valido. Assicurati di aver selezionato un comune valido.']);
        }

        try {
            $cfGenerator = new CodiceFiscale();
            // Genera il Codice Fiscale con i dati anagrafici forniti
            $generatedFiscalCode = $cfGenerator->generate(
                $firstName,
                $lastName,
                $birthdate,
                $birthPlaceCode,
                $gender
            );

            // Compara direttamente il CF inserito con quello generato dalla libreria
            $isValid = ($fiscalCode === $generatedFiscalCode);

            return response()->json(['is_valid' => $isValid]);

        } catch (\Exception $e) {
            // In caso di errore nella libreria (es. dati malformati), restituisci false con messaggio
            return response()->json(['is_valid' => false, 'error' => 'Errore interno di validazione del Codice Fiscale: ' . $e->getMessage()]);
        }
    }
}