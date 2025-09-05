<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomField;
use App\Models\ModuleBlock;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\ProcessImportJob; // Aggiunto

class ImportController extends Controller
{
    public function create()
    {
        return view('import.create');
    }

    public function showMapping(Request $request)
    {
        $request->validate([ 'import_file' => 'required|file|mimes:csv,txt', 'module_class' => 'required|string' ]);
        $path = $request->file('import_file')->store('temp_imports');
        $moduleClass = $request->input('module_class');
        try {
            $headings = (new \Maatwebsite\Excel\HeadingRowImport)->toCollection($path)->first()->first()->toArray();
        } catch (\Exception $e) {
            return back()->with('error', 'Impossibile leggere le intestazioni dal file CSV.');
        }
        $crmFields = CustomField::whereHas('block', function ($query) use ($moduleClass) {
            $query->where('module_class', $moduleClass);
        })->orderBy('order')->get();

        return view('import.mapping', [ 'csvHeadings' => $headings, 'crmFields' => $crmFields, 'filePath' => $path, 'moduleClass' => $moduleClass ]);
    }

    /**
     * Avvia il processo di importazione in background.
     */
    public function process(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string',
            'module_class' => 'required|string',
            'field_map' => 'required|array',
        ]);

        // Filtriamo solo i campi che sono stati effettivamente mappati
        $fieldMap = array_filter($request->input('field_map'));

        if (empty($fieldMap)) {
            return back()->with('error', 'Devi mappare almeno un campo per poter avviare l-importazione.');
        }

        // Avviamo il job in coda
        ProcessImportJob::dispatch(
            $request->input('file_path'),
            $request->input('module_class'),
            $fieldMap
        );

        return redirect()->route('dashboard')->with('success', 'Importazione avviata! Il processo continuerà in background.');
    }
}