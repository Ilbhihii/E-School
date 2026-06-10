<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::with('subject')->latest()->get();
        $subjects = Subject::orderBy('name')->get();

        return view('admin.levels.index', compact('levels', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();

        return view('admin.levels.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        Level::create($request->only(['name', 'subject_id']));

        return redirect()->route('admin.levels.index')->with('success', 'Niveau ajouté avec succès');
    }

    public function show(Level $level)
    {
        return view('admin.levels.show', compact('level'));
    }

    public function edit(Level $level)
    {
        $subjects = Subject::orderBy('name')->get();

        return view('admin.levels.edit', compact('level', 'subjects'));
    }

    public function update(Request $request, Level $level)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $level->update($request->only(['name', 'subject_id']));

        return redirect()->route('admin.levels.index')->with('success', 'Niveau modifié');
    }

    public function destroy(Level $level)
    {
        $level->delete();

        return redirect()->route('admin.levels.index')->with('success', 'Niveau supprimé');
    }
}

