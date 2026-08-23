<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    /**
     * Création rapide d'une hiérarchie :
     * Matière → Niveau → Classe.
     *
     * Cette méthode reste tolérante aux anciens formulaires :
     * elle accepte subject_name ou name pour le nom de matière.
     */
    public function storeSubjectHierarchy(Request $request)
    {
        $subjectName = trim((string) (
            $request->input('subject_name')
            ?? $request->input('name')
            ?? ''
        ));

        $levelName = trim((string) (
            $request->input('level_name')
            ?? ''
        ));

        $className = trim((string) (
            $request->input('class_name')
            ?? ''
        ));

        if ($subjectName === '') {
            return back()
                ->withInput()
                ->withErrors([
                    'subject_name' =>
                        'Le nom de la matière est obligatoire.',
                ]);
        }

        $request->validate([
            'subject_name' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'subject_type' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'max:100'],
            'status' => [
                'nullable',
                Rule::in([
                    'active',
                    'coming_soon',
                    'inactive',
                ]),
            ],
            'level_name' => ['nullable', 'string', 'max:255'],
            'class_name' => ['nullable', 'string', 'max:255'],
        ]);

        $subject = Subject::query()
            ->whereRaw(
                'LOWER(TRIM(name)) = LOWER(TRIM(?))',
                [$subjectName]
            )
            ->first();

        if (!$subject) {
            $subject = new Subject();
            $subject->name = $subjectName;

            $subjectType =
                $request->input('subject_type')
                ?? $request->input('type');

            if (
                $subjectType !== null
                && Schema::hasColumn('subjects', 'type')
            ) {
                $subject->type = $subjectType;
            }

            if (Schema::hasColumn('subjects', 'status')) {
                $subject->status =
                    $request->input('status', 'active');
            }

            $subject->save();
        }

        $level = null;

        if ($levelName !== '') {
            $level = Level::query()
                ->where('subject_id', $subject->id)
                ->whereRaw(
                    'LOWER(TRIM(name)) = LOWER(TRIM(?))',
                    [$levelName]
                )
                ->first();

            if (!$level) {
                $level = new Level();
                $level->subject_id = $subject->id;
                $level->name = $levelName;

                if (Schema::hasColumn('levels', 'description')) {
                    $level->description =
                        $request->input(
                            'level_description',
                            'Niveau éducatif'
                        );
                }

                if (Schema::hasColumn('levels', 'order')) {
                    $level->order =
                        ((int) Level::query()
                            ->where(
                                'subject_id',
                                $subject->id
                            )
                            ->max('order')) + 1;
                }

                $level->save();
            }
        }

        if ($className !== '') {
            if (!$level) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'level_name' =>
                            'Un niveau est obligatoire '
                            . 'pour créer une classe.',
                    ]);
            }

            $class = ClassRoom::query()
                ->where('level_id', $level->id)
                ->whereRaw(
                    'LOWER(TRIM(name)) = LOWER(TRIM(?))',
                    [$className]
                )
                ->first();

            if (!$class) {
                $class = new ClassRoom();
                $class->level_id = $level->id;
                $class->name = $className;

                if (
                    Schema::hasColumn(
                        'class_rooms',
                        'admission_mode'
                    )
                ) {
                    $class->admission_mode =
                        $request->input(
                            'admission_mode',
                            'contact'
                        );
                }

                if (
                    Schema::hasColumn(
                        'class_rooms',
                        'is_visible'
                    )
                ) {
                    $class->is_visible =
                        $request->boolean(
                            'is_visible',
                            true
                        );
                }

                $class->save();
            }

            $class->subjects()->syncWithoutDetaching([
                $subject->id,
            ]);
        }

        return redirect()
            ->route('admin.subjects.index')
            ->with(
                'success',
                'Structure pédagogique enregistrée avec succès.'
            );
    }

    /**
     * Affiche le formulaire de modification d'une matière.
     */
    public function editSubject(Subject $subject)
    {
        /*
         * Certaines versions du projet possèdent déjà cette vue.
         * Si elle n'existe pas, on revient proprement à la liste
         * au lieu de provoquer une ViewNotFoundException.
         */
        if (view()->exists('admin.subjects.edit')) {
            return view(
                'admin.subjects.edit',
                compact('subject')
            );
        }

        return redirect()
            ->route('admin.subjects.index')
            ->with(
                'info',
                'Utilisez le formulaire de modification '
                . 'présent sur la page des matières.'
            );
    }

    /**
     * Met à jour une matière.
     *
     * Statuts supportés :
     * - active
     * - coming_soon
     * - inactive
     */
    public function updateSubject(
        Request $request,
        Subject $subject
    ) {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('subjects', 'name')
                    ->ignore($subject->id),
            ],
            'type' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],
            'status' => [
                'sometimes',
                'required',
                Rule::in([
                    'active',
                    'coming_soon',
                    'inactive',
                ]),
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],
        ]);

        if (array_key_exists('name', $validated)) {
            $subject->name = trim($validated['name']);
        }

        if (
            array_key_exists('type', $validated)
            && Schema::hasColumn('subjects', 'type')
        ) {
            $subject->type = $validated['type'];
        }

        if (
            array_key_exists('status', $validated)
            && Schema::hasColumn('subjects', 'status')
        ) {
            $subject->status = $validated['status'];
        }

        if (
            array_key_exists('description', $validated)
            && Schema::hasColumn(
                'subjects',
                'description'
            )
        ) {
            $subject->description =
                $validated['description'];
        }

        $subject->save();

        return redirect()
            ->route('admin.subjects.index')
            ->with(
                'success',
                'Matière modifiée avec succès.'
            );
    }

    /**
     * Supprime une matière seulement lorsqu'elle ne contient
     * plus de niveaux, de classes liées ou de cours.
     *
     * Cela évite de supprimer accidentellement toute une
     * structure pédagogique.
     */
    public function destroySubject(Subject $subject)
    {
        $hasLevels =
            Schema::hasTable('levels')
            && Level::query()
                ->where('subject_id', $subject->id)
                ->exists();

        $hasCourses =
            Schema::hasTable('courses')
            && Course::query()
                ->where('subject_id', $subject->id)
                ->exists();

        $hasClasses =
            Schema::hasTable('class_room_subject')
            && DB::table('class_room_subject')
                ->where('subject_id', $subject->id)
                ->exists();

        if ($hasLevels || $hasCourses || $hasClasses) {
            return redirect()
                ->route('admin.subjects.index')
                ->with(
                    'error',
                    'Cette matière contient encore des '
                    . 'niveaux, classes ou cours. '
                    . 'Supprimez-les d’abord.'
                );
        }

        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with(
                'success',
                'Matière supprimée avec succès.'
            );
    }

    /**
     * Affiche l'édition d'un niveau.
     *
     * Si aucune vue dédiée n'existe dans cette version,
     * la page des niveaux reste utilisable pour la modification.
     */
    public function editSubjectLevel(
        Subject $subject,
        Level $level
    ) {
        abort_unless(
            (int) $level->subject_id
                === (int) $subject->id,
            404,
            'Ce niveau n’appartient pas à cette matière.'
        );

        if (view()->exists('admin.subjects.level-edit')) {
            return view(
                'admin.subjects.level-edit',
                compact('subject', 'level')
            );
        }

        if (view()->exists('admin.levels.edit')) {
            return view(
                'admin.levels.edit',
                compact('subject', 'level')
            );
        }

        return redirect()
            ->route(
                'admin.subjects.levels',
                $subject
            )
            ->with(
                'info',
                'Utilisez le formulaire de modification '
                . 'du niveau sur cette page.'
            );
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
            'is_visible' => [
                'required',
                'boolean',
            ],
        ]);

        $class = ClassRoom::create([
            'name' => trim($validated['name']),
            'level_id' => $level->id,
        ]);

        $class->admission_mode = $validated['admission_mode'];
        $class->is_visible = (bool) $validated['is_visible'];
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
            'is_visible' => [
                'required',
                'boolean',
            ],
        ]);

        $class->name = trim($validated['name']);
        $class->admission_mode = $validated['admission_mode'];
        $class->is_visible = (bool) $validated['is_visible'];
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

        /*
         * Cette méthode accepte maintenant deux usages :
         *
         * 1. Modification normale :
         *    name + description
         *
         * 2. Activation / masquage rapide :
         *    is_active = 1 ou 0
         *
         * Ainsi, les boutons Activer / Masquer de la carte
         * n'ont plus besoin d'envoyer le nom du niveau.
         */
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('levels', 'name')
                    ->ignore($level->id)
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'subject_id',
                                $subject->id
                            )
                    ),
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'is_active' => [
                'sometimes',
                'required',
                'boolean',
            ],
        ]);

        if (array_key_exists('name', $validated)) {
            $level->name = trim($validated['name']);
        }

        if (array_key_exists('description', $validated)) {
            $level->description =
                $validated['description']
                ?? $level->description;
        }

        if (array_key_exists('is_active', $validated)) {
            $level->is_active =
                (bool) $validated['is_active'];
        }

        $level->save();

        if (
            array_key_exists('is_active', $validated)
            && !array_key_exists('name', $validated)
            && !array_key_exists(
                'description',
                $validated
            )
        ) {
            return redirect()
                ->route(
                    'admin.subjects.levels',
                    $subject
                )
                ->with(
                    'success',
                    $level->is_active
                        ? 'Niveau activé avec succès.'
                        : 'Niveau masqué avec succès.'
                );
        }

        return redirect()
            ->route(
                'admin.subjects.levels',
                $subject
            )
            ->with(
                'success',
                'Niveau modifié avec succès.'
            );
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