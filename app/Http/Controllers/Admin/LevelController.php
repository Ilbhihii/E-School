<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Course;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    /**
     * Redirige vers la page des matières (les niveaux sont gérés via Matières → Niveaux → Classes)
     */
    public function index()
    {
        return redirect()->route('admin.subjects.index')
            ->with('info', 'Les niveaux sont maintenant gérés depuis la page des matières.');
    }

    /**
     * Affiche les classes d'un niveau spécifique
     */
    public function classes(Level $level)
    {
        $classes = ClassRoom::where('level_id', $level->id)->with('subjects')->get();

        return view('admin.levels.classes', compact('level', 'classes'));
    }

    /**
     * Affiche les matières associées à une classe dans un niveau
     */
    public function subjects(Level $level, ClassRoom $class)
    {
        $subjects = Subject::whereHas('classes', function($q) use ($class) {
            $q->where('class_room_id', $class->id);
        })->withCount(['courses as course_count' => function($q) use ($class) {
            $q->where('class_id', $class->id);
        }])->get();

        $allSubjects = Subject::orderBy('name')->get();

        return view('admin.levels.subjects', compact('level', 'class', 'subjects', 'allSubjects'));
    }

    /**
     * Affiche les cours d'une matière pour une classe spécifique
     */
    public function courses(Level $level, ClassRoom $class, Subject $subject)
    {
        $courses = Course::where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->with(['classRoom', 'subject'])
            ->get();

        return view('admin.levels.courses', compact('level', 'class', 'subject', 'courses'));
    }

    /**
     * Ajouter une matière à une classe
     */
    public function attachSubject(Request $request, Level $level, ClassRoom $class)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $class->subjects()->syncWithoutDetaching([$request->subject_id]);

        return back()->with('success', 'Matière liée à la classe avec succès');
    }

    /**
     * Retirer une matière d'une classe
     */
    public function detachSubject(Level $level, ClassRoom $class, Subject $subject)
    {
        $class->subjects()->detach($subject->id);

        return back()->with('success', 'Matière retirée de la classe');
    }

    /**
     * Affiche toutes les matières (entrée de la navigation Matières → Niveaux → Classes)
     */
    public function subjectsIndex()
    {
        $subjects = Subject::orderBy('name')->get();

        // Niveaux avec leurs classes et les matières liées à chaque classe
        $levels = Level::with(['classes' => function ($q) {
            $q->orderBy('name');
        }, 'classes.subjects'])->orderBy('name')->get();

        // Tous les cours groupés par (subject_id + '-' + class_id)
        $allCourses = Course::select('id', 'title', 'subject_id', 'class_id', 'level_id', 'order')
            ->orderBy('order')
            ->get()
            ->groupBy(fn($c) => $c->subject_id . '-' . $c->class_id);

        // Construire l'arbre hiérarchique
        $tree = [];
        foreach ($subjects as $subject) {
            $subjectLevels = [];
            foreach ($levels as $level) {
                $levelClasses = $level->classes->filter(function ($class) use ($subject) {
                    return $class->subjects->contains('id', $subject->id);
                });

                if ($levelClasses->isNotEmpty()) {
                    $classData = [];
                    foreach ($levelClasses as $class) {
                        $courseKey = $subject->id . '-' . $class->id;
                        $classCourses = $allCourses->get($courseKey, collect());

                        $classData[] = [
                            'class' => $class,
                            'courses' => $classCourses,
                        ];
                    }

                    $subjectLevels[] = [
                        'level' => $level,
                        'classes' => $classData,
                    ];
                }
            }

            $tree[] = [
                'subject' => $subject,
                'levels' => $subjectLevels,
            ];
        }

        return view('admin.subjects.index', compact('tree', 'subjects', 'levels'));
    }

    /**
     * Affiche les niveaux disponibles pour une matière
     */
    public function subjectLevels(Subject $subject)
    {
        // Charger TOUS les niveaux, avec leurs classes filtrées par matière
        $levels = Level::with(['classes' => function($q) use ($subject) {
                $q->whereHas('subjects', fn($sq) => $sq->where('subject_id', $subject->id));
            }])
            ->orderBy('name')
            ->get();

        return view('admin.subjects.levels', compact('subject', 'levels'));
    }

    /**
     * Affiche les classes d'un niveau pour une matière spécifique
     */
    public function subjectClasses(Subject $subject, Level $level)
    {
        $classes = ClassRoom::where('level_id', $level->id)
            ->whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
            ->get();

        return view('admin.subjects.classes', compact('subject', 'level', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Level::create([
            'name' => $request->name,
            'description' => $request->description ?? 'Niveau éducatif',
        ]);

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

