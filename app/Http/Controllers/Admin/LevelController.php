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
        $subjects = Subject::all();

        return view('admin.levels.index', compact('levels','subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'subject_id' => 'required'
        ]);

        Level::create($request->all());

        return back()->with('success','Niveau ajouté avec succès');
    }

    public function update(Request $request, Level $level)
    {
        $level->update($request->all());

        return back()->with('success','Niveau modifié');
    }

    public function destroy(Level $level)
    {
        $level->delete();

        return back()->with('success','Niveau supprimé');
    }
}

