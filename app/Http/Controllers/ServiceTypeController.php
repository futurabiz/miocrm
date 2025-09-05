<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\HandlesListViews;

class ServiceTypeController extends Controller
{
    use HandlesListViews;

    public function index(Request $request)
    {
        // Configurazione per i Tipi di Servizio
        $defaultColumns = ['name', 'description'];
        $columnLabels = ['name' => 'Nome Servizio', 'description' => 'Descrizione'];

        // Otteniamo i dati della vista dal Trait
        $viewData = $this->getListViewData($request, ServiceType::class, $defaultColumns, $columnLabels);

        // Eseguiamo la query
        $columnsForQuery = array_unique(array_merge($viewData['currentView']->columns, ['id']));
        $serviceTypes = ServiceType::select($columnsForQuery)->latest()->paginate(15);
        
        // Passiamo i dati alla vista
        return view('service_types.index', array_merge($viewData, ['serviceTypes' => $serviceTypes]));
    }

    public function create()
    {
        return view('service_types.create');
    }

    public function store(Request $request)
    {
        // ... (la tua logica esistente va bene)
    }

    public function edit(ServiceType $serviceType)
    {
        return view('service_types.edit', compact('serviceType'));
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        // ... (la tua logica esistente va bene)
    }

    public function destroy(ServiceType $serviceType)
    {
        // ... (la tua logica esistente va bene)
    }
}