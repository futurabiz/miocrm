<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::latest()->paginate(15);
        return view('tags.index', compact('tags'));
    }

    public function create()
    {
        return view('tags.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'color' => 'required|string|max:7', // Es. #FFFFFF
        ]);

        Tag::create($request->all());

        return redirect()->route('tags.index')
                         ->with('success', 'Tag creato con successo.');
    }

    public function edit(Tag $tag)
    {
        return view('tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'color' => 'required|string|max:7',
        ]);

        $tag->update($request->all());

        return redirect()->route('tags.index')
                         ->with('success', 'Tag aggiornato con successo.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('tags.index')
                         ->with('success', 'Tag eliminato con successo.');
    }
}