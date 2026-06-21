<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Live;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LiveController extends Controller
{
    /**
     * Affiche la liste des matières avec le nombre de lives (entrée de la navigation hiérarchique)
     */
    public function index()
    {
        // Tous les lives globaux pour les stats
        $allLives = Live::with('classRoom')->orderBy('created_at', 'desc')->get();

        // Stats
        $totalLives = $allLives->count();
        $recentLives = $allLives->take(5);

        // Sujets qui ont des lives via leurs classes
        $subjects = Subject::whereHas('classes', function($q) {
            $q->whereHas('lives');
        })->withCount(['classes' => function($q) {
            $q->whereHas('lives');
        }])->orderBy('name')->get();

        // Compter les lives par sujet
        $subjectLiveCounts = [];
        foreach ($subjects as $subject) {
            $subjectLiveCounts[$subject->id] = Live::whereHas('classRoom.subjects', function($q) use ($subject) {
                $q->where('subject_id', $subject->id);
            })->count();
        }

        return view('admin.lives.index', compact('subjects', 'totalLives', 'recentLives', 'subjectLiveCounts', 'allLives'));
    }

    /**
     * Affiche les niveaux disponibles pour une matière
     */
    public function subjectLevels(Subject $subject)
    {
        // Niveaux qui ont des classes avec des lives pour cette matière
        $levelIds = $subject->classes()
            ->whereHas('lives')
            ->pluck('class_rooms.level_id')
            ->unique()
            ->filter();
        $levels = Level::whereIn('id', $levelIds)->orderBy('name')->get();

        // Compter les lives par niveau pour cette matière
        $levelLiveCounts = [];
        foreach ($levels as $level) {
            $levelLiveCounts[$level->id] = Live::whereHas('classRoom', function($q) use ($subject, $level) {
                $q->where('level_id', $level->id)
                  ->whereHas('subjects', fn($sq) => $sq->where('subject_id', $subject->id));
            })->count();
        }

        // Compter les classes par niveau pour cette matière
        $levelClassCounts = [];
        foreach ($levels as $level) {
            $levelClassCounts[$level->id] = ClassRoom::where('level_id', $level->id)
                ->whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
                ->whereHas('lives')
                ->count();
        }

        return view('admin.lives.levels', compact('subject', 'levels', 'levelLiveCounts', 'levelClassCounts'));
    }

    /**
     * Affiche les classes d'un niveau pour une matière spécifique
     */
    public function subjectClasses(Subject $subject, Level $level)
    {
        $classes = ClassRoom::where('level_id', $level->id)
            ->whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
            ->whereHas('lives')
            ->withCount('lives')
            ->get();

        return view('admin.lives.classes', compact('subject', 'level', 'classes'));
    }

    /**
     * Affiche les lives d'une classe spécifique
     */
    public function classLives(Subject $subject, Level $level, ClassRoom $class)
    {
        $lives = Live::where('class_id', $class->id)
            ->with('classRoom')
            ->orderBy('live_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $totalLives = $lives->count();

        return view('admin.lives.class-lives', compact('subject', 'level', 'class', 'lives', 'totalLives'));
    }

    // Formulaire création
    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        $levels = Level::orderBy('name')->get();
        $classes = ClassRoom::with('level', 'subjects')->orderBy('name')->get();

        // Construire la correspondance Niveau → Matières (via les classes liées)
        $levelSubjectMap = [];
        foreach ($classes as $class) {
            if (!isset($levelSubjectMap[$class->level_id])) {
                $levelSubjectMap[$class->level_id] = [];
            }
            foreach ($class->subjects as $subject) {
                if (!in_array($subject->id, $levelSubjectMap[$class->level_id])) {
                    $levelSubjectMap[$class->level_id][] = $subject->id;
                }
            }
        }

        return view('admin.lives.create', compact('subjects', 'levels', 'classes', 'levelSubjectMap'));
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
            'stream_url' => 'nullable|url',
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

        return redirect()->to(url()->previous())->with('success', 'Live créé avec succès');
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


        return redirect()->to(url()->previous())
                         ->with('success', 'Live modifié avec succès');
    }

    // Supprimer un live
    public function destroy($id)
    {
        Live::destroy($id);

        return redirect()->to(url()->previous())
                         ->with('success', 'Live supprimé avec succès');
    }
}
