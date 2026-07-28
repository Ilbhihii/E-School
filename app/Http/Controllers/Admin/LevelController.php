<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Course;
use App\Models\VocalTestPrompt;
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
     * Affiche les matières avec les compteurs de la structure pédagogique active.
     *
     * Les anciens parcours et les anciennes classes présents dans la base
     * ne sont pas inclus dans les compteurs.
     */
    public function subjectsIndex()
    {
        $subjects = Subject::query()
            ->whereIn('name', ['Arabe', 'Coran'])
            ->orderByRaw(
                "CASE
                    WHEN LOWER(name) = 'arabe' THEN 1
                    WHEN LOWER(name) = 'coran' THEN 2
                    ELSE 3
                END"
            )
            ->get();

        $subjects->each(function (Subject $subject) {
            $allowedLevelNames =
                VocalTestPrompt::pathNamesForSubject($subject);

            $allowedClassNames =
                VocalTestPrompt::allowedClassNames();

            if (empty($allowedLevelNames)) {
                $subject->setAttribute(
                    'validated_level_count',
                    0
                );

                $subject->setAttribute(
                    'validated_class_count',
                    0
                );

                return;
            }

            $validLevels = Level::query()
                ->where('subject_id', $subject->id)
                ->whereIn('name', $allowedLevelNames)
                ->with([
                    'classes' => function ($query) use (
                        $subject,
                        $allowedClassNames
                    ) {
                        $query
                            ->whereIn('name', $allowedClassNames)
                            ->whereHas(
                                'subjects',
                                fn ($subjectQuery) =>
                                    $subjectQuery->where(
                                        'subjects.id',
                                        $subject->id
                                    )
                            );
                    },
                ])
                ->get();

            /*
             * Les groupements par nom empêchent les anciens doublons
             * de gonfler les nombres affichés.
             */
            $levelGroups = $validLevels->groupBy(
                fn (Level $level) =>
                    VocalTestPrompt::normalizePathName(
                        $level->name
                    )
            );

            $levelCount = $levelGroups->count();

            $classCount = $levelGroups->sum(
                function ($levels) {
                    return $levels
                        ->flatMap(
                            fn (Level $level) => $level->classes
                        )
                        ->unique(
                            fn (ClassRoom $classRoom) =>
                                VocalTestPrompt::normalizePathName(
                                    $classRoom->name
                                )
                        )
                        ->count();
                }
            );

            $subject->setAttribute(
                'validated_level_count',
                $levelCount
            );

            $subject->setAttribute(
                'validated_class_count',
                $classCount
            );
        });

        return view(
            'admin.subjects.index',
            compact('subjects')
        );
    }

    /**
     * Affiche uniquement la structure pédagogique validée pour Arabe et Coran.
     *
     * Arabe :
     * - Lecture & Écriture
     * - Communication
     *
     * Coran :
     * - Apprentissage & Tajwid
     *
     * Chaque parcours contient seulement :
     * - Débutant
     * - Intermédiaire
     * - Avancé
     */
    public function subjectLevels(Subject $subject)
    {
        $allowedLevelNames = VocalTestPrompt::pathNamesForSubject($subject);
        $allowedClassNames = VocalTestPrompt::allowedClassNames();

        if (empty($allowedLevelNames)) {
            $levels = Level::query()
                ->where(function ($query) use ($subject) {
                    $query->where('subject_id', $subject->id)
                        ->orWhereHas(
                            'classes.subjects',
                            fn ($subjectQuery) => $subjectQuery
                                ->where('subjects.id', $subject->id)
                        );
                })
                ->with([
                    'classes' => function ($query) use ($subject) {
                        $query->whereHas(
                            'subjects',
                            fn ($subjectQuery) => $subjectQuery
                                ->where('subjects.id', $subject->id)
                        );
                    },
                ])
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            return view(
                'admin.subjects.levels',
                compact('subject', 'levels')
            );
        }

        $levels = Level::query()
            ->where('subject_id', $subject->id)
            ->whereIn('name', $allowedLevelNames)
            ->with([
                'classes' => function ($query) use (
                    $subject,
                    $allowedClassNames
                ) {
                    $query
                        ->whereIn('name', $allowedClassNames)
                        ->whereHas(
                            'subjects',
                            fn ($subjectQuery) => $subjectQuery
                                ->where('subjects.id', $subject->id)
                        );
                },
            ])
            ->get()
            ->sortBy(function (Level $level) use ($allowedLevelNames) {
                $position = array_search(
                    $level->name,
                    $allowedLevelNames,
                    true
                );

                return $position === false ? PHP_INT_MAX : $position;
            })
            ->unique('name')
            ->values();

        $classOrder = array_flip($allowedClassNames);

        $levels->each(function (Level $level) use ($classOrder) {
            $level->setRelation(
                'classes',
                $level->classes
                    ->sortBy(
                        fn (ClassRoom $classRoom) =>
                            $classOrder[$classRoom->name] ?? PHP_INT_MAX
                    )
                    ->unique('name')
                    ->values()
            );
        });

        return view(
            'admin.subjects.levels',
            compact('subject', 'levels')
        );
    }

    /**
     * Affiche les classes d'un niveau pour une matière spécifique
     */
    public function subjectClasses(Subject $subject, Level $level)
    {
        abort_unless(
            VocalTestPrompt::isSupportedLevel($subject, $level),
            404,
            'Ce parcours ne fait pas partie de la structure pédagogique active.'
        );

        $allowedClassNames = VocalTestPrompt::allowedClassNames();
        $classOrder = array_flip($allowedClassNames);

        $classes = ClassRoom::query()
            ->where('level_id', $level->id)
            ->whereIn('name', $allowedClassNames)
            ->whereHas(
                'subjects',
                fn ($query) => $query->where('subjects.id', $subject->id)
            )
            ->get()
            ->sortBy(
                fn (ClassRoom $classRoom) =>
                    $classOrder[$classRoom->name] ?? PHP_INT_MAX
            )
            ->unique('name')
            ->values();

        return view(
            'admin.subjects.classes',
            compact('subject', 'level', 'classes')
        );
    }

    /**
     * Affiche les cours d'une matière pour une classe (nouveau chemin Matière → Niveau → Classe → Cours)
     */
    public function subjectCourses(Subject $subject, Level $level, ClassRoom $class)
    {
        abort_unless(
            VocalTestPrompt::isSupportedPath($subject, $level, $class),
            404,
            'Ce parcours ne fait pas partie de la structure pédagogique active.'
        );

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
