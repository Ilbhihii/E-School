<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Live;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LiveController extends Controller
{
    // Afficher tous les lives
    public function index()
    {
        // Tous les lives
        $lives = Live::with('classRoom')->orderBy('created_at', 'desc')->get();

        // Stats
        $totalLives = $lives->count();
        $recentLives = $lives->take(5); // 5 derniers lives

        return view('admin.lives.index', compact('lives', 'totalLives', 'recentLives'));
    }


    // Formulaire création
    public function create()
    {
        $classes = ClassRoom::all();
        return view('admin.lives.create', compact('classes'));
    }

    // Enregistrer un live
    public function store(Request $request)
    {
        if(!in_array(auth()->user()->role, ['admin','prof'])){
            abort(403);
        }

        $request->validate([
            'title' => 'required',
            'class_id' => 'required',
            'stream_url' => 'required|url',
            'live_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        // 🔥 Vérifier conflit
        $conflict = Live::where('live_date', $request->live_date)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['live_date' => '⚠️ Cette date/heure est déjà occupée par une autre classe.']);
        }

        Live::create([
            'title' => $request->title,
            'class_id' => $request->class_id,
            'stream_url' => $request->stream_url,
            'admin_id' => auth()->id(),
            'live_date' => $request->live_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('admin.lives.index')->with('success', 'Live créé avec succès');
    }



    // Formulaire édition
    public function edit($id)
    {
        $live = Live::findOrFail($id);
        $classes = ClassRoom::all();
        return view('admin.lives.edit', compact('live','classes'));
    }

    // Mettre à jour un live
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'stream_url' => 'required|url'
        ]);

        $live = Live::findOrFail($id);
        $live->update([
            'title' => $request->title,
            'class_id' => $request->class_id,
            'stream_url' => $request->stream_url,
        ]);


        return redirect()->route('admin.lives.index')
                         ->with('success', 'Live modifié avec succès');
    }

    // Supprimer un live
    public function destroy($id)
    {
        Live::destroy($id);

        return redirect()->route('admin.lives.index')
                         ->with('success', 'Live supprimé avec succès');
    }
}
