<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Importa i tuoi modelli CRM qui
use App\Models\Contact;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Lead;
use App\Models\Activity;
use Carbon\Carbon; // Per la gestione delle date
use Illuminate\Support\Facades\DB; // Import per DB::raw

class DashboardController extends Controller
{
    /**
     * Mostra la dashboard principale con i KPI e i dati.
     */
    public function index()
    {
        // --- KPI Cards ---
        $totalContacts = Contact::count();
        $totalCompanies = Company::count();
        $openOpportunitiesValue = Opportunity::whereNotIn('stage', ['Chiusa Vinta', 'Chiusa Persa'])->sum('amount');
        $opportunitiesClosingThisMonth = Opportunity::whereNotIn('stage', ['Chiusa Vinta', 'Chiusa Persa'])
                                                    ->whereMonth('closing_date', Carbon::now()->month)
                                                    ->whereYear('closing_date', Carbon::now()->year)
                                                    ->count();

        // --- Attività di Oggi ---
        $todayActivities = Activity::whereDate('start_time', Carbon::today())
                                    ->orderBy('start_time', 'asc')
                                    ->get();

        // --- Dati per il Grafico a Imbuto (Funnel) ---
        $funnelStages = ['Qualificazione', 'Analisi Bisogni', 'Proposta', 'Negoziazione'];
        $funnelData = Opportunity::select('stage', DB::raw('count(*) as total'))
                                ->whereIn('stage', $funnelStages)
                                ->groupBy('stage')
                                ->orderByRaw("CASE stage WHEN 'Qualificazione' THEN 1 WHEN 'Analisi Bisogni' THEN 2 WHEN 'Proposta' THEN 3 WHEN 'Negoziazione' THEN 4 ELSE 5 END")
                                ->pluck('total', 'stage');
        
        $funnelLabels = $funnelStages;
        $funnelCounts = collect($funnelLabels)->map(function ($stage) use ($funnelData) {
            return $funnelData[$stage] ?? 0;
        })->toArray();

        // --- NUOVO WIDGET: Ultimi Lead Creati ---
        $recentLeads = Lead::latest()->take(5)->get(); // Prendiamo gli ultimi 5 lead

        // Passiamo tutte le variabili alla vista, inclusa la nuova
        // Il percorso della vista è corretto per resources/views/dashboard/index.blade.php
        return view('dashboard.index', compact(
            'totalContacts',
            'totalCompanies',
            'openOpportunitiesValue',
            'opportunitiesClosingThisMonth',
            'todayActivities',
            'funnelLabels',
            'funnelCounts',
            'recentLeads'
        ));
    }
}
