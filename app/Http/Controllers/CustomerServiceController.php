<?php

namespace App\Http\Controllers;

use App\Models\CustomerService;
use Illuminate\Http\Request;

class CustomerServiceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
            'customerable_id' => 'required|integer',
            'customerable_type' => 'required|string',
            'custom_fields_data' => 'nullable|array'
        ]);

        CustomerService::create($request->all());

        return back()->with('success', 'Servizio aggiunto con successo.');
    }

    public function update(Request $request, CustomerService $customerService)
    {
        $request->validate([
            'custom_fields_data' => 'nullable|array'
        ]);

        $customerService->update($request->only('custom_fields_data'));

        return back()->with('success', 'Servizio aggiornato con successo.');
    }

    public function destroy(CustomerService $customerService)
    {
        $customerService->delete();
        return back()->with('success', 'Servizio rimosso con successo.');
    }
}
