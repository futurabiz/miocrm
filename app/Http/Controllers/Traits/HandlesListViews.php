<?php
namespace App\Http\Controllers\Traits;

use App\Models\ListView;
use Illuminate\Http\Request;

trait HandlesListViews
{
    protected function getListViewData(Request $request, string $moduleClass, array $defaultColumns, array $columnLabels): array
    {
        $userViews = ListView::where('module_class', $moduleClass)
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        $currentView = null;
        if ($request->has('view_id')) {
            $currentView = $userViews->firstWhere('id', $request->query('view_id'));
        }
        if (!$currentView) {
            $currentView = $userViews->firstWhere('is_default', true);
        }
        if (!$currentView) {
            // Se non ci sono viste salvate, usa la vista standard
            $currentView = new ListView([
                'id' => 'standard',
                'name' => 'Vista Standard',
                'columns' => $defaultColumns,
                'is_default' => true
            ]);
        }

        // ASSICURATI che columns sia SEMPRE valorizzato con un array
        $columns = $currentView->columns ?? $defaultColumns;

        return [
            'listViews'      => $userViews,
            'currentView'    => $currentView,
            'columns'        => $columns, // <--- QUESTA RIGA RISOLVE IL TUO PROBLEMA
            'columnLabels'   => $columnLabels,
            'defaultColumns' => $defaultColumns,
        ];
    }
}
