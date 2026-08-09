<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Course;
use App\Models\VocalTestPrompt;
use App\Services\ClassSlotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    /**
     * Les seules matières autorisées dans la structure pédagogique.
     * Les autres matières déjà présentes en base restent intactes,
     * mais ne sont ni affichées ni configurables depuis cette page.
     */
    private const ALLOWED_SUBJECTS = [
        'arabe' => [
            'name' => 'Arabe',
            'type' => 'scolaire',
        ],
        'coran' => [
            'name' => 'Coran',
            'type' => 'religieux',
        ],
        'soutien lycee' => [
            'name' => 'Soutien Lycée',
            'type' => 'scolaire',
        ],
    ];

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
        $subjectOrder = array_flip(array_keys(self::ALLOWED_SUBJECTS));

        $subjects = Subject::query()
            ->get()
            ->filter(
                fn (Subject $subject) =>
                    $this->isAllowedSubject($subject)
            )
            ->sortBy(
                fn (Subject $subject) =>
                    $subjectOrder[
                        VocalTestPrompt::normalizePathName($subject->name)
                    ] ?? PHP_INT_MAX
            )
            ->values();

        $subjects->each(function (Subject $subject) {
            $allowedLevelNames =
                $this->levelNamesForSubject($subject);

            $allowedItemNames =
                $this->itemNamesForSubject($subject);

            $validLevels = Level::query()
                ->where('subject_id', $subject->id)
                ->whereIn('name', $allowedLevelNames)
                ->with([
                    'classes' => function ($query) use (
                        $subject,
                        $allowedItemNames
                    ) {
                        $query
                            ->whereIn(
                                'name',
                                $allowedItemNames
                            )
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

            $levelGroups = $validLevels->groupBy(
                fn (Level $level) =>
                    VocalTestPrompt::normalizePathName(
                        $level->name
                    )
            );

            $subject->setAttribute(
                'validated_level_count',
                $levelGroups->count()
            );

            $subject->setAttribute(
                'validated_class_count',
                $levelGroups->sum(
                    fn ($levels) =>
                        $levels
                            ->flatMap(
                                fn (Level $level) =>
                                    $level->classes
                            )
                            ->unique('id')
                            ->count()
                )
            );

            $subject->setAttribute(
                'is_high_school_support',
                $this->isHighSchoolSupport($subject)
            );
        });

        return view(
            'admin.subjects.index',
            compact('subjects')
        );
    }

    /**
     * Crée une structure pédagogique complète :
     * Matière → Niveaux → Classes.
     */
    public function storeSubjectHierarchy(Request $request)
    {
        $allowedSubjectNames = array_column(
            self::ALLOWED_SUBJECTS,
            'name'
        );

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    Rule::in($allowedSubjectNames),
                ],
                'description' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
                'levels' => [
                    'required',
                    'array',
                    'min:1',
                    'max:20',
                ],
                'levels.*.name' => [
                    'required',
                    'string',
                    'max:120',
                ],
                'levels.*.description' => [
                    'nullable',
                    'string',
                    'max:500',
                ],
                'levels.*.classes' => [
                    'required',
                    'array',
                    'min:1',
                    'max:30',
                ],
                'levels.*.classes.*.name' => [
                    'required',
                    'string',
                    'max:120',
                ],
            ],
            [
                'name.required' => 'Sélectionnez une matière.',
                'name.in' => 'Seules les matières Arabe, Coran et Soutien Lycée sont autorisées.',
                'levels.required' => 'Ajoutez au moins un niveau.',
                'levels.min' => 'Ajoutez au moins un niveau.',
                'levels.*.name.required' => 'Chaque niveau doit avoir un nom.',
                'levels.*.classes.required' => 'Ajoutez au moins une classe à chaque niveau.',
                'levels.*.classes.min' => 'Ajoutez au moins une classe à chaque niveau.',
                'levels.*.classes.*.name.required' => 'Chaque classe doit avoir un nom.',
            ]
        );

        $subjectConfig = $this->allowedSubjectConfig(
            $validated['name']
        );

        if ($subjectConfig === null) {
            return back()
                ->withErrors([
                    'name' => 'Cette matière n’est pas autorisée.',
                ])
                ->withInput();
        }

        $levelNames = [];

        foreach ($validated['levels'] as $levelIndex => $levelData) {
            $normalizedLevelName =
                VocalTestPrompt::normalizePathName($levelData['name']);

            if (in_array($normalizedLevelName, $levelNames, true)) {
                return back()
                    ->withErrors([
                        "levels.{$levelIndex}.name" =>
                            'Ce niveau est déjà présent dans cette matière.',
                    ])
                    ->withInput();
            }

            $levelNames[] = $normalizedLevelName;
            $classNames = [];

            foreach ($levelData['classes'] as $classIndex => $classData) {
                $normalizedClassName =
                    VocalTestPrompt::normalizePathName($classData['name']);

                if (in_array($normalizedClassName, $classNames, true)) {
                    return back()
                        ->withErrors([
                            "levels.{$levelIndex}.classes.{$classIndex}.name" =>
                                'Cette classe est déjà présente dans ce niveau.',
                        ])
                        ->withInput();
                }

                $classNames[] = $normalizedClassName;
            }
        }

        $subject = DB::transaction(
            function () use ($validated, $subjectConfig) {
                $normalizedSubjectName =
                    VocalTestPrompt::normalizePathName(
                        $subjectConfig['name']
                    );

                $subject = Subject::query()
                    ->get()
                    ->first(
                        fn (Subject $candidate) =>
                            VocalTestPrompt::normalizePathName(
                                $candidate->name
                            ) === $normalizedSubjectName
                    );

                $subjectData = [
                    'name' => $subjectConfig['name'],
                    'type' => $subjectConfig['type'],
                ];

                $description = trim(
                    (string) ($validated['description'] ?? '')
                );

                if ($description !== '') {
                    $subjectData['description'] = $description;
                }

                if ($subject) {
                    $subject->fill($subjectData);
                    $subject->save();
                } else {
                    $subject = Subject::create($subjectData);
                }

                foreach ($validated['levels'] as $levelIndex => $levelData) {
                    $normalizedLevelName =
                        VocalTestPrompt::normalizePathName(
                            $levelData['name']
                        );

                    $level = Level::query()
                        ->where('subject_id', $subject->id)
                        ->get()
                        ->first(
                            fn (Level $candidate) =>
                                VocalTestPrompt::normalizePathName(
                                    $candidate->name
                                ) === $normalizedLevelName
                        );

                    $levelDataToSave = [
                        'subject_id' => $subject->id,
                        'name' => trim($levelData['name']),
                        'description' => isset($levelData['description'])
                            ? trim((string) $levelData['description'])
                            : null,
                        'order' => $levelIndex + 1,
                    ];

                    if ($level) {
                        $level->fill($levelDataToSave);
                        $level->save();
                    } else {
                        $level = Level::create($levelDataToSave);
                    }

                    foreach ($levelData['classes'] as $classData) {
                        $normalizedClassName =
                            VocalTestPrompt::normalizePathName(
                                $classData['name']
                            );

                        $classRoom = ClassRoom::query()
                            ->where('level_id', $level->id)
                            ->get()
                            ->first(
                                fn (ClassRoom $candidate) =>
                                    VocalTestPrompt::normalizePathName(
                                        $candidate->name
                                    ) === $normalizedClassName
                            );

                        if (!$classRoom) {
                            $classRoom = ClassRoom::create([
                                'name' => trim($classData['name']),
                                'level_id' => $level->id,
                            ]);
                        }

                        $classRoom->subjects()->syncWithoutDetaching([
                            $subject->id,
                        ]);
                    }
                }

                return $subject;
            }
        );

        return redirect()
            ->route('admin.subjects.levels', $subject)
            ->with(
                'success',
                'La structure de la matière a été enregistrée avec succès.'
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
        abort_unless(
            $this->isAllowedSubject($subject),
            404
        );

        /*
         * La vue utilise :
         * - $subject  : matière actuellement sélectionnée ;
         * - $subjects : liste des matières actives ;
         * - $levels   : parcours/niveaux de la matière.
         */
        $subjectOrder = array_flip(array_keys(self::ALLOWED_SUBJECTS));

        $subjects = Subject::query()
            ->get()
            ->filter(
                fn (Subject $candidate) =>
                    $this->isAllowedSubject($candidate)
            )
            ->sortBy(
                fn (Subject $candidate) =>
                    $subjectOrder[
                        VocalTestPrompt::normalizePathName(
                            $candidate->name
                        )
                    ] ?? PHP_INT_MAX
            )
            ->values();

        $allowedLevelNames =
            $this->levelNamesForSubject($subject);

        $allowedItemNames =
            $this->itemNamesForSubject($subject);

        $levels = Level::query()
            ->where('subject_id', $subject->id)
            ->whereIn('name', $allowedLevelNames)
            ->with([
                'classes' => function ($query) use (
                    $subject,
                    $allowedItemNames
                ) {
                    $query
                        ->whereIn(
                            'name',
                            $allowedItemNames
                        )
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
            ->get()
            ->sortBy(
                function (Level $level) use (
                    $allowedLevelNames
                ) {
                    $position = array_search(
                        $level->name,
                        $allowedLevelNames,
                        true
                    );

                    return $position === false
                        ? PHP_INT_MAX
                        : $position;
                }
            )
            ->unique('name')
            ->values();

        $itemOrder = array_flip(
            array_map(
                [
                    VocalTestPrompt::class,
                    'normalizePathName',
                ],
                $allowedItemNames
            )
        );

        $levels->each(
            function (Level $level) use (
                $itemOrder
            ) {
                $level->setRelation(
                    'classes',
                    $level->classes
                        ->sortBy(
                            fn (ClassRoom $classRoom) =>
                                $itemOrder[
                                    VocalTestPrompt
                                        ::normalizePathName(
                                            $classRoom->name
                                        )
                                ] ?? PHP_INT_MAX
                        )
                        ->unique('name')
                        ->values()
                );
            }
        );

        $subject->setAttribute(
            'is_high_school_support',
            $this->isHighSchoolSupport($subject)
        );

        return view(
            'admin.subjects.levels',
            compact(
                'subject',
                'subjects',
                'levels'
            )
        );
    }

    /**
     * Affiche les classes d'un niveau pour une matière spécifique
     */
    public function subjectClasses(
        Subject $subject,
        Level $level,
        ClassSlotService $classSlotService
    ) {
        abort_unless(
            $this->isAllowedSubject($subject),
            404
        );

        abort_unless(
            (int) $level->subject_id ===
                (int) $subject->id,
            404
        );

        $allowedLevelNames =
            $this->levelNamesForSubject($subject);

        abort_unless(
            in_array(
                VocalTestPrompt::normalizePathName(
                    $level->name
                ),
                array_map(
                    [
                        VocalTestPrompt::class,
                        'normalizePathName',
                    ],
                    $allowedLevelNames
                ),
                true
            ),
            404
        );

        $allowedItemNames =
            $this->itemNamesForSubject($subject);

        $itemOrder = array_flip(
            array_map(
                [
                    VocalTestPrompt::class,
                    'normalizePathName',
                ],
                $allowedItemNames
            )
        );

        $classes = ClassRoom::query()
            ->where('level_id', $level->id)
            ->whereIn('name', $allowedItemNames)
            ->whereHas(
                'subjects',
                fn ($query) =>
                    $query->where(
                        'subjects.id',
                        $subject->id
                    )
            )
            ->get()
            ->sortBy(
                fn (ClassRoom $classRoom) =>
                    $itemOrder[
                        VocalTestPrompt::normalizePathName(
                            $classRoom->name
                        )
                    ] ?? PHP_INT_MAX
            )
            ->unique('name')
            ->values();

        /*
         * Les créneaux D1-D4 / I1-I4 / A1-A4 sont
         * STRUCTURELS. Ils existent dès que la classe existe,
         * sans attendre la création d'une séance dans l'emploi
         * du temps.
         */
        foreach ($classes as $classRoom) {
            $classSlotService->syncForPath(
                $subject,
                $level,
                $classRoom
            );
        }

        $classes->load([
            'classSlots' =>
                function ($query) use (
                    $subject,
                    $level
                ) {
                    $query
                        ->where(
                            'subject_id',
                            $subject->id
                        )
                        ->where(
                            'level_id',
                            $level->id
                        )
                        ->where(
                            'is_active',
                            true
                        )
                        ->orderBy('position')
                        ->orderBy('code');
                },
        ]);

        return view(
            'admin.subjects.classes',
            compact(
                'subject',
                'level',
                'classes'
            )
        );
    }

    /**
     * Affiche les cours d'une matière pour une classe (nouveau chemin Matière → Niveau → Classe → Cours)
     */
    public function subjectCourses(Subject $subject, Level $level, ClassRoom $class)
    {
        abort_unless(
            $this->isAllowedSubject($subject),
            404
        );

        $allowedLevelNames =
            $this->levelNamesForSubject($subject);

        $allowedItemNames =
            $this->itemNamesForSubject($subject);

        abort_unless(
            (int) $level->subject_id ===
                (int) $subject->id
            && (int) $class->level_id ===
                (int) $level->id
            && in_array(
                VocalTestPrompt::normalizePathName(
                    $level->name
                ),
                array_map(
                    [
                        VocalTestPrompt::class,
                        'normalizePathName',
                    ],
                    $allowedLevelNames
                ),
                true
            )
            && in_array(
                VocalTestPrompt::normalizePathName(
                    $class->name
                ),
                array_map(
                    [
                        VocalTestPrompt::class,
                        'normalizePathName',
                    ],
                    $allowedItemNames
                ),
                true
            )
            && $class->subjects()
                ->where(
                    'subjects.id',
                    $subject->id
                )
                ->exists(),
            404,
            'Ce parcours ne fait pas partie de la structure pédagogique active.'
        );

        $courses = Course::where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->with(['classRoom', 'subject'])
            ->get();

        return view('admin.subjects.courses', compact('level', 'class', 'subject', 'courses'));
    }

    private function allowedSubjectConfig(
        string $subjectName
    ): ?array {
        $normalizedName =
            VocalTestPrompt::normalizePathName($subjectName);

        return self::ALLOWED_SUBJECTS[$normalizedName] ?? null;
    }

    private function isAllowedSubject(
        Subject $subject
    ): bool {
        return $this->allowedSubjectConfig($subject->name) !== null;
    }

    private function levelNamesForSubject(
        Subject $subject
    ): array {
        if ($this->isHighSchoolSupport($subject)) {
            return ['BAC'];
        }

        $configuredNames =
            VocalTestPrompt::pathNamesForSubject($subject);

        if ($configuredNames !== []) {
            return $configuredNames;
        }

        return Level::query()
            ->where('subject_id', $subject->id)
            ->orderBy('order')
            ->orderBy('name')
            ->pluck('name')
            ->filter()
            ->unique(
                fn ($name) =>
                    VocalTestPrompt::normalizePathName($name)
            )
            ->values()
            ->all();
    }

    private function itemNamesForSubject(
        Subject $subject
    ): array {
        if ($this->isHighSchoolSupport($subject)) {
            return [
                'Mathématiques',
                'Physique-Chimie',
            ];
        }

        if (VocalTestPrompt::pathNamesForSubject($subject) !== []) {
            return VocalTestPrompt::allowedClassNames();
        }

        return ClassRoom::query()
            ->whereHas(
                'level',
                fn ($query) =>
                    $query->where('subject_id', $subject->id)
            )
            ->whereHas(
                'subjects',
                fn ($query) =>
                    $query->where('subjects.id', $subject->id)
            )
            ->orderBy('name')
            ->pluck('name')
            ->filter()
            ->unique(
                fn ($name) =>
                    VocalTestPrompt::normalizePathName($name)
            )
            ->values()
            ->all();
    }

    private function isHighSchoolSupport(
        Subject $subject
    ): bool {
        return VocalTestPrompt::normalizePathName(
            $subject->name
        ) === 'soutien lycee';
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
