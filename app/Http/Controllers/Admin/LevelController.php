<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    public function index()
    {
        return redirect()
            ->route('admin.subjects.index')
            ->with('info', 'Les niveaux sont gérés depuis les matières.');
    }

    public function classes(Level $level)
    {
        $classes = ClassRoom::query()
            ->where('level_id', $level->id)
            ->with('subjects')
            ->orderBy('name')
            ->get();

        return view('admin.levels.classes', compact('level', 'classes'));
    }

    public function subjects(Level $level, ClassRoom $class)
    {
        abort_unless((int) $class->level_id === (int) $level->id, 404);

        $subjects = Subject::query()
            ->whereHas('classes', function ($query) use ($class) {
                $query->where('class_room_id', $class->id);
            })
            ->withCount([
                'courses as course_count' => function ($query) use ($class) {
                    $query->where('class_id', $class->id);
                },
            ])
            ->orderBy('name')
            ->get();

        $allSubjects = Subject::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.levels.subjects',
            compact('level', 'class', 'subjects', 'allSubjects')
        );
    }

    public function courses(Level $level, ClassRoom $class, Subject $subject)
    {
        abort_unless((int) $class->level_id === (int) $level->id, 404);

        $courses = Course::query()
            ->where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->with(['classRoom', 'subject'])
            ->get();

        return view(
            'admin.levels.courses',
            compact('level', 'class', 'subject', 'courses')
        );
    }

    public function attachSubject(
        Request $request,
        Level $level,
        ClassRoom $class
    ) {
        abort_unless((int) $class->level_id === (int) $level->id, 404);

        $validated = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
        ]);

        $class->subjects()->syncWithoutDetaching([
            $validated['subject_id'],
        ]);

        return back()->with(
            'success',
            'Matière liée à la classe avec succès.'
        );
    }

    public function detachSubject(
        Level $level,
        ClassRoom $class,
        Subject $subject
    ) {
        abort_unless((int) $class->level_id === (int) $level->id, 404);

        $class->subjects()->detach($subject->id);

        return back()->with(
            'success',
            'Matière retirée de la classe.'
        );
    }

    public function subjectsIndex()
    {
        $subjects = Subject::query()
            ->where('name', '!=', 'Administration')
            ->orderBy('name')
            ->get();

        $subjects->each(function (Subject $subject) {
            $levels = Level::query()
                ->where('subject_id', $subject->id)
                ->with([
                    'classes' => function ($query) use ($subject) {
                        $query->whereHas(
                            'subjects',
                            fn ($subjectQuery) =>
                                $subjectQuery->where('subjects.id', $subject->id)
                        );
                    },
                ])
                ->get();

            $subject->setAttribute(
                'validated_level_count',
                $levels->count()
            );

            $subject->setAttribute(
                'validated_class_count',
                $levels
                    ->flatMap(fn (Level $level) => $level->classes)
                    ->unique('id')
                    ->count()
            );

            $subject->setAttribute(
                'is_high_school_support',
                mb_strtolower(trim($subject->name)) === 'soutien lycée'
            );
        });

        return view('admin.subjects.index', compact('subjects'));
    }

    public function subjectLevels(Subject $subject)
    {
        $subjects = Subject::query()
            ->where('name', '!=', 'Administration')
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->where('subject_id', $subject->id)
            ->with([
                'classes' => function ($query) use ($subject) {
                    $query
                        ->whereHas(
                            'subjects',
                            fn ($subjectQuery) =>
                                $subjectQuery->where('subjects.id', $subject->id)
                        )
                        ->orderBy('name');
                },
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $subject->setAttribute(
            'is_high_school_support',
            mb_strtolower(trim($subject->name)) === 'soutien lycée'
        );

        return view(
            'admin.subjects.levels',
            compact('subject', 'subjects', 'levels')
        );
    }

    public function subjectClasses(
        Subject $subject,
        Level $level
    ) {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            404,
            'Ce niveau n’appartient pas à cette matière.'
        );

        $classes = ClassRoom::query()
            ->where('level_id', $level->id)
            ->whereHas(
                'subjects',
                fn ($query) =>
                    $query->where('subjects.id', $subject->id)
            )
            ->orderBy('name')
            ->get();

        return view(
            'admin.subjects.classes',
            compact('subject', 'level', 'classes')
        );
    }

    public function subjectCourses(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            404,
            'Ce niveau n’appartient pas à cette matière.'
        );

        abort_unless(
            (int) $class->level_id === (int) $level->id,
            404,
            'Cette classe n’appartient pas à ce niveau.'
        );

        abort_unless(
            $class
                ->subjects()
                ->where('subjects.id', $subject->id)
                ->exists(),
            404,
            'Cette classe n’est pas liée à cette matière.'
        );

        $courses = Course::query()
            ->where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->with(['classRoom', 'subject'])
            ->get();

        return view(
            'admin.subjects.courses',
            compact('level', 'class', 'subject', 'courses')
        );
    }


    /**
     * Affiche le formulaire de création d'une classe depuis :
     * Matière → Niveau → Classes.
     */
    public function createSubjectClass(
        Subject $subject,
        Level $level
    ) {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            404,
            'Ce niveau n’appartient pas à cette matière.'
        );

        return view(
            'admin.subjects.class-form',
            compact('subject', 'level')
        );
    }


    public function storeSubjectClass(
        Request $request,
        Subject $subject,
        Level $level
    ) {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            404,
            'Ce niveau n’appartient pas à cette matière.'
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('class_rooms', 'name')
                    ->where(
                        fn ($query) =>
                            $query->where('level_id', $level->id)
                    ),
            ],
            'admission_mode' => [
                'required',
                Rule::in(['contact', 'vocal_test']),
            ],
        ]);

        $class = ClassRoom::create([
            'name' => trim($validated['name']),
            'level_id' => $level->id,
        ]);

        $class->admission_mode = $validated['admission_mode'];
        $class->save();

        $class->subjects()->syncWithoutDetaching([
            $subject->id,
        ]);

        return redirect()
            ->route(
                'admin.subjects.classes',
                [
                    'subject' => $subject->id,
                    'level' => $level->id,
                ]
            )
            ->with('success', 'Classe créée avec succès.');
    }


    /**
     * Affiche le formulaire de modification d'une classe depuis :
     * Matière → Niveau → Classes.
     */
    public function editSubjectClass(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            404,
            'Ce niveau n’appartient pas à cette matière.'
        );

        abort_unless(
            (int) $class->level_id === (int) $level->id,
            404,
            'Cette classe n’appartient pas à ce niveau.'
        );

        abort_unless(
            $class
                ->subjects()
                ->where('subjects.id', $subject->id)
                ->exists(),
            404,
            'Cette classe n’est pas liée à cette matière.'
        );

        return view(
            'admin.subjects.class-form',
            compact('subject', 'level', 'class')
        );
    }


    public function updateSubjectClass(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id
            && (int) $class->level_id === (int) $level->id,
            404
        );

        abort_unless(
            $class
                ->subjects()
                ->where('subjects.id', $subject->id)
                ->exists(),
            404,
            'Cette classe n’est pas liée à cette matière.'
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('class_rooms', 'name')
                    ->ignore($class->id)
                    ->where(
                        fn ($query) =>
                            $query->where('level_id', $level->id)
                    ),
            ],
            'admission_mode' => [
                'required',
                Rule::in(['contact', 'vocal_test']),
            ],
        ]);

        $class->name = trim($validated['name']);
        $class->admission_mode = $validated['admission_mode'];
        $class->save();

        $class->subjects()->syncWithoutDetaching([
            $subject->id,
        ]);

        return redirect()
            ->route(
                'admin.subjects.classes',
                [
                    'subject' => $subject->id,
                    'level' => $level->id,
                ]
            )
            ->with('success', 'Classe modifiée avec succès.');
    }

    public function destroySubjectClass(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id
            && (int) $class->level_id === (int) $level->id,
            404
        );

        $class->subjects()->detach();
        $class->delete();

        return redirect()
            ->route(
                'admin.subjects.classes',
                [
                    'subject' => $subject->id,
                    'level' => $level->id,
                ]
            )
            ->with('success', 'Classe supprimée avec succès.');
    }

    public function storeSubjectLevel(
        Request $request,
        Subject $subject
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('levels', 'name')
                    ->where(fn ($query) =>
                        $query->where('subject_id', $subject->id)
                    ),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $nextOrder =
            ((int) Level::query()
                ->where('subject_id', $subject->id)
                ->max('order')) + 1;

        Level::create([
            'name' => trim($validated['name']),
            'description' =>
                $validated['description'] ?? 'Niveau éducatif',
            'subject_id' => $subject->id,
            'order' => $nextOrder,
        ]);

        return redirect()
            ->route('admin.subjects.levels', $subject)
            ->with('success', 'Niveau créé avec succès.');
    }

    public function updateSubjectLevel(
        Request $request,
        Subject $subject,
        Level $level
    ) {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            404
        );

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $level->update([
            'name' => trim($validated['name']),
            'description' =>
                $validated['description'] ?? $level->description,
        ]);

        return redirect()
            ->route('admin.subjects.levels', $subject)
            ->with('success', 'Niveau modifié avec succès.');
    }

    public function destroySubjectLevel(
        Subject $subject,
        Level $level
    ) {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            404
        );

        $classes = ClassRoom::query()
            ->where('level_id', $level->id)
            ->get();

        foreach ($classes as $class) {
            $class->subjects()->detach();
            $class->delete();
        }

        $level->delete();

        return redirect()
            ->route('admin.subjects.levels', $subject)
            ->with('success', 'Niveau supprimé avec succès.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
        ]);

        Level::create([
            'name' => trim($validated['name']),
            'description' =>
                $validated['description'] ?? 'Niveau éducatif',
            'subject_id' => $validated['subject_id'] ?? null,
        ]);

        return back()->with(
            'success',
            'Niveau ajouté avec succès.'
        );
    }

    public function update(
        Request $request,
        Level $level
    ) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $level->update([
            'name' => trim($validated['name']),
            'description' =>
                $validated['description'] ?? $level->description,
        ]);

        return back()->with(
            'success',
            'Niveau modifié.'
        );
    }

    public function destroy(Level $level)
    {
        $level->delete();

        return back()->with(
            'success',
            'Niveau supprimé.'
        );
    }
}
