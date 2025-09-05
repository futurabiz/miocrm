<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact; // Assicurati di importare il modello Contact
use App\Models\Company; // Potrebbe servire anche Company
use App\Models\Lead;    // Potrebbe servire anche Lead

class CalendarController extends Controller
{
    public function index()
    {
        // Recupera i contatti per il calendario
        // Potrebbe essere necessario filtrare o selezionare solo alcuni campi
        $contatti = Contact::all(); // Questo è un esempio base. Adatta in base alle tue esigenze.
        $aziende = Company::all(); // Esempio: se servono anche le aziende
        $leads = Lead::all(); // Esempio: se servono anche i lead

        // Passa i dati alla vista
        return view('calendario.index', compact('contatti', 'aziende', 'leads'));
    }
}