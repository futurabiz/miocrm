<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Mostra la pagina principale del calendario, passando le liste di contatti e aziende.
     */
    public function index()
    {
        $contatti = Contact::select('id', 'first_name', 'last_name')->orderBy('last_name')->get();
        $aziende = Company::select('id', 'name')->orderBy('name')->get();
        return view('calendario.index', compact('contatti', 'aziende'));
    }

    /**
     * Fornisce gli eventi al calendario, includendo le informazioni sulla relazione.
     */
    public function getEvents(Request $request)
    {
        $activities = Activity::with('activityable')->get();

        $events = $activities->map(function ($activity) {
            $title = $activity->title;
            // Aggiunge il nome del contatto/azienda al titolo dell'evento per una visualizzazione chiara
            if ($activity->activityable) {
                $relatedName = $activity->activityable->name ?? ($activity->activityable->first_name . ' ' . $activity->activityable->last_name);
                $title .= ' (' . $relatedName . ')';
            }

            return [
                'id'    => $activity->id,
                'title' => $title,
                'start' => $activity->start_time->toIso8601String(),
                'end'   => $activity->end_time ? $activity->end_time->toIso8601String() : null,
                'extendedProps' => [ // Dati extra usati nel frontend
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'activityable_id' => $activity->activityable_id,
                    'activityable_type' => $activity->activityable_type,
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Salva una nuova attività, gestendo la relazione polimorfica.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'type' => 'required|in:task,meeting,call',
            'related_to' => 'nullable|string', // Es. 'contact-5' o 'company-3'
        ]);

        $data = $request->only(['title', 'start_time', 'end_time', 'type']);
        
        if ($request->filled('related_to') && $request->input('related_to') !== '') {
            [$type, $id] = explode('-', $request->input('related_to'));
            if ($type === 'contact') {
                $data['activityable_type'] = Contact::class;
                $data['activityable_id'] = $id;
            } elseif ($type === 'company') {
                $data['activityable_type'] = Company::class;
                $data['activityable_id'] = $id;
            }
        }

        $activity = Activity::create($data);

        return response()->json(['status' => 'success', 'activity' => $activity]);
    }

    /**
     * Aggiorna un'attività esistente.
     */
    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'related_to' => 'nullable|string',
        ]);

        $data = $request->only(['title', 'start_time', 'end_time']);

        if ($request->filled('related_to') && $request->input('related_to') !== '') {
             [$type, $id] = explode('-', $request->input('related_to'));
            if ($type === 'contact') {
                $data['activityable_type'] = Contact::class;
                $data['activityable_id'] = $id;
            } elseif ($type === 'company') {
                $data['activityable_type'] = Company::class;
                $data['activityable_id'] = $id;
            }
        } else {
            // Se viene passato un valore vuoto, scollega l'attività
            $data['activityable_type'] = null;
            $data['activityable_id'] = null;
        }

        $activity->update($data);

        return response()->json(['status' => 'success']);
    }

    /**
     * Elimina un'attività.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();
        return response()->json(['status' => 'success']);
    }
}
