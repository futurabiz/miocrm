<?php
namespace App\Http\Controllers;
use App\Models\EmailList;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\HandlesListViews;

class EmailListController extends Controller
{
    use HandlesListViews;

    public function index(Request $request)
    {
        $defaultColumns = ['name', 'description'];
        $columnLabels = ['name' => 'Nome Lista', 'description' => 'Descrizione'];

        $viewData = $this->getListViewData($request, EmailList::class, $defaultColumns, $columnLabels);
        $columnsForQuery = array_unique(array_merge($viewData['currentView']->columns, ['id']));
        $emailLists = EmailList::select($columnsForQuery)->latest()->paginate(15);

        return view('email_lists.index', array_merge($viewData, ['emailLists' => $emailLists]));
    }

    public function create()
    {
        return view('email_lists.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:email_lists,name',
            'description' => 'nullable|string',
        ]);
        EmailList::create($request->all());
        return redirect()->route('email_lists.index')
                         ->with('success', 'Lista Email creata con successo.');
    }

    public function edit(EmailList $emailList)
    {
        return view('email_lists.edit', compact('emailList'));
    }

    public function update(Request $request, EmailList $emailList)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:email_lists,name,' . $emailList->id,
            'description' => 'nullable|string',
        ]);
        $emailList->update($request->all());
        return redirect()->route('email_lists.index')
                         ->with('success', 'Lista Email aggiornata con successo.');
    }

    public function destroy(EmailList $emailList)
    {
        $emailList->delete();
        return redirect()->route('email_lists.index')
                         ->with('success', 'Lista Email eliminata con successo.');
    }
}
