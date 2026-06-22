<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Live;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveController extends Controller
{
    public function index()
    {
        $lives = Live::where('user_id', Auth::id())
            ->with('classRoom')
            ->latest()
            ->paginate(15);
        
        $totalLives = Live::where('user_id', Auth::id())->count();
        $recentLives = Live::where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();
        
        return view('prof.lives.index', compact('lives', 'totalLives', 'recentLives'));
    }

    public function create()
    {
        $classes = ClassRoom::all();
        return view('prof.lives.create', compact('classes'));
    }

    public function store(Request $request)
    {
        if(!in_array(auth()->user()->role, ['admin','prof'])){
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:class_rooms,id',
            'stream_url' => 'required|url',
            'live_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $conflict = Live::where('live_date', $request->live_date)
            ->where('class_id', $request->class_id)
            ->where(function($q) use ($request) {
                $q->whereColumn('start_time', '<=', $request->end_time)
                  ->whereColumn('end_time', '>=', $request->start_time);
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['live_date' => 'Conflit horaire pour cette classe!']);
        }

        Live::create([
            'title' => $request->title,
            'class_id' => $request->class_id,
            'stream_url' => $request->stream_url,
            'live_date' => $request->live_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('prof.lives.index')->with('success', 'Live créé avec succès!');
    }

    public function edit(Live $live)
    {
        if ($live->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $classes = ClassRoom::all();
        return view('prof.lives.edit', compact('live', 'classes'));
    }

    public function update(Request $request, Live $live)
    {
        if ($live->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:class_rooms,id',
            'stream_url' => 'required|url',
            'live_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $live->update($request->only([
            'title', 'class_id', 'stream_url', 'live_date', 'start_time', 'end_time'
        ]));

        return redirect()->route('prof.lives.index')->with('success', 'Live mis à jour!');
    }

    public function destroy(Live $live)
    {
        if ($live->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $live->delete();

        return back()->with('success', 'Live supprimé!');
    }
}

