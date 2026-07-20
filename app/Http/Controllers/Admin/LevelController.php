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
     * Affiche la page des matières (point d'entrée de la hiérarchie Matière → Niveau → Classe)
     */
    public function subjectsIndex()
    {
        $subjects = Subject::whereIn('name', ['Arabe', 'Coran'])
            ->with(['levels', 'classes'])
            ->orderBy('name')
            ->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Affiche les niveaux disponibles pour une matière
     */
    public function subjectLevels(Subject $subject)
    {
        $levels = Level::where(function ($query) use ($subject) {
                $query->where('subject_id', $subject->id)
                    ->orWhereHas('classes.subjects', fn($subjectQuery) => $subjectQuery->where('subjects.id', $subject->id));
            })
            ->with(['classes' => function($q) use ($subject) {
                $q->whereHas('subjects', fn($sq) => $sq->where('subject_id', $subject->id));
            }])
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        if (mb_strtolower($subject->name) === 'arabe') {
            $arabicLevelNames = [
                1 => 'Découverte de l’alphabet',
                2 => 'Lecture et communication',
                3 => 'Maîtrise intermédiaire',
                4 => 'Expression écrite et orale',
            ];

            $arabicLevelsByName = Level::where(function ($query) {
                    $query->where('name', 'like', '%alphabet%')
                        ->orWhere('name', 'like', '%Lecture et communication%')
                        ->orWhere('name', 'like', '%Maîtrise intermédiaire%')
                        ->orWhere('name', 'like', '%Expression écrite et orale%');
                })
                ->with(['classes' => function($q) use ($subject) {
                    $q->whereHas('subjects', fn($sq) => $sq->where('subject_id', $subject->id));
                }])
                ->get();

            $levels = $levels->concat($arabicLevelsByName)->unique('id');

            $levels = $levels
                ->filter(function ($level) use ($arabicLevelNames) {
                    $name = mb_strtolower($level->name);

                    return isset($arabicLevelNames[(int) $level->order])
                        || collect($arabicLevelNames)->contains(function ($expectedName) use ($name) {
                            $searchName = $expectedName === 'Découverte de l’alphabet'
                                ? 'alphabet'
                                : mb_strtolower($expectedName);

                            return str_contains($name, $searchName);
                        });
                })
                ->each(function ($level) use ($arabicLevelNames) {
                    $name = mb_strtolower($level->name);
                    $matchedName = collect($arabicLevelNames)->first(function ($expectedName) use ($name) {
                        $searchName = $expectedName === 'Découverte de l’alphabet'
                            ? 'alphabet'
                            : mb_strtolower($expectedName);

                        return str_contains($name, $searchName);
                    });

                    $level->name = $matchedName ?? $arabicLevelNames[(int) $level->order];
                })
                ->sortBy(fn($level) => array_search($level->name, $arabicLevelNames, true))
                ->values();
        } elseif (mb_strtolower($subject->name) === 'coran') {
            $quranLevelNames = [
                1 => 'Apprendre les règles',
                2 => 'Tajwid et Hifd',
            ];

            $quranLevelsByName = Level::where(function ($query) {
                    $query->where('name', 'like', '%Apprendre les règles%')
                        ->orWhere('name', 'like', '%Tajwid et Hifd%');
                })
                ->with(['classes' => function($q) use ($subject) {
                    $q->whereHas('subjects', fn($sq) => $sq->where('subject_id', $subject->id));
                }])
                ->get();

            $levels = $levels->concat($quranLevelsByName)->unique('id');

            $levels = $levels
                ->filter(function ($level) use ($quranLevelNames) {
                    $name = mb_strtolower($level->name);

                    return isset($quranLevelNames[(int) $level->order])
                        || collect($quranLevelNames)->contains(
                            fn($expectedName) => str_contains($name, mb_strtolower($expectedName))
                        );
                })
                ->each(function ($level) use ($quranLevelNames) {
                    $matchedName = collect($quranLevelNames)->first(
                        fn($expectedName) => str_contains(mb_strtolower($level->name), mb_strtolower($expectedName))
                    );

                    $level->name = $matchedName ?? $quranLevelNames[(int) $level->order];
                })
                ->sortBy(fn($level) => array_search($level->name, $quranLevelNames, true))
                ->values();
        }

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

    /**
     * Affiche les cours d'une matière pour une classe (nouveau chemin Matière → Niveau → Classe → Cours)
     */
    public function subjectCourses(Subject $subject, Level $level, ClassRoom $class)
    {
        $courses = Course::where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->with(['classRoom', 'subject'])
            ->get();

        return view('admin.subjects.courses', compact('level', 'class', 'subject', 'courses'));
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

