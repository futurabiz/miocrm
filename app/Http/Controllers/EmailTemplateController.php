<?php
namespace App\Http\Controllers;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $emailTemplates = EmailTemplate::all(); 
        return view('email_templates.index', compact('emailTemplates'));
    }

    public function create()
    {
        return view('email_templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
        EmailTemplate::create($request->all());
        return redirect()->route('email_templates.index')
                         ->with('success', 'Template email creato con successo.');
    }

    public function show(EmailTemplate $emailTemplate)
    {
        // Se vuoi una vista show:
        // return view('email_templates.show', compact('emailTemplate'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('email_templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
        $emailTemplate->update($request->all());
        return redirect()->route('email_templates.index')
                         ->with('success', 'Template email aggiornato con successo.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return redirect()->route('email_templates.index')
                         ->with('success', 'Template email eliminato con successo.');
    }
}
