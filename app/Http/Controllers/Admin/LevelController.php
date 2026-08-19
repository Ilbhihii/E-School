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
use Illuminate\Support\Facades\Schema;

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
        /*
         * Alias toléré si une ancienne donnée a été enregistrée
         * avec "Soutient Lycée".
         */
        'soutient lycee' => [
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
        $courses = Course::approved()->where('subject_id', $subject->id)
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
        /*
         * L'administration doit pouvoir gérer TOUTES les matières,
         * quel que soit leur statut :
         *
         * - Active
         * - Bientôt disponible
         * - Inactive
         *
         * Le filtrage "active uniquement" reste réservé au site
         * public (/classes).
         */
        $subjects = Subject::query()
            ->get()
            ->sortBy(function (Subject $subject) {
                $normalized =
                    VocalTestPrompt::normalizePathName(
                        $subject->name
                    );

                /*
                 * Les trois matières historiques restent en tête.
                 * Les nouvelles matières suivent ensuite par statut
                 * puis par nom.
                 */
                $officialOrder = [
                    'arabe' => 1,
                    'coran' => 2,
                    'soutien lycee' => 3,
                    'soutient lycee' => 3,
                ];

                if (
                    array_key_exists(
                        $normalized,
                        $officialOrder
                    )
                ) {
                    return sprintf(
                        '0-%02d-%s',
                        $officialOrder[$normalized],
                        $normalized
                    );
                }

                $statusOrder = [
                    'active' => 1,
                    'coming_soon' => 2,
                    'inactive' => 3,
                ];

                $status = $subject->status
                    ?? 'active';

                return sprintf(
                    '1-%02d-%s',
                    $statusOrder[$status] ?? 9,
                    $normalized
                );
            })
            ->values();

        $subjects->each(function (Subject $subject) {
            $levels = Level::query()
                ->where('subject_id', $subject->id)
                ->with([
                    'classes' => function ($query) use ($subject) {
                        $query->whereHas(
                            'subjects',
                            fn ($subjectQuery) =>
                                $subjectQuery->where(
                                    'subjects.id',
                                    $subject->id
                                )
                        );
                    },
                ])
                ->orderBy('order')
                ->orderBy('name')
                ->get();

            $subject->setAttribute(
                'validated_level_count',
                $levels->count()
            );

            $subject->setAttribute(
                'validated_class_count',
                $levels
                    ->flatMap(
                        fn (Level $level) =>
                            $level->classes
                    )
                    ->unique('id')
                    ->count()
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
    public function storeSubjectHierarchy(
        Request $request,
        ClassSlotService $classSlotService
    ) {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:120',
                ],
                'description' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
                'status' => [
                    'required',
                    'in:active,coming_soon,inactive',
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
                'name.required' =>
                    'Écrivez le nom de la matière.',
                'status.required' =>
                    'Choisissez le statut de la matière.',
                'status.in' =>
                    'Le statut sélectionné est invalide.',
                'levels.required' =>
                    'Ajoutez au moins un niveau.',
                'levels.min' =>
                    'Ajoutez au moins un niveau.',
                'levels.*.name.required' =>
                    'Chaque niveau doit avoir un nom.',
                'levels.*.classes.required' =>
                    'Ajoutez au moins une classe à chaque niveau.',
                'levels.*.classes.min' =>
                    'Ajoutez au moins une classe à chaque niveau.',
                'levels.*.classes.*.name.required' =>
                    'Chaque classe doit avoir un nom.',
            ]
        );

        $subjectName = trim($validated['name']);

        /*
         * Vérification des doublons dans les niveaux et les classes
         * avant toute écriture en base.
         */
        $levelNames = [];

        foreach (
            $validated['levels']
            as $levelIndex => $levelData
        ) {
            $normalizedLevelName =
                VocalTestPrompt::normalizePathName(
                    $levelData['name']
                );

            if (
                in_array(
                    $normalizedLevelName,
                    $levelNames,
                    true
                )
            ) {
                return back()
                    ->withErrors([
                        "levels.{$levelIndex}.name" =>
                            'Ce niveau est déjà présent dans cette matière.',
                    ])
                    ->withInput();
            }

            $levelNames[] = $normalizedLevelName;
            $classNames = [];

            foreach (
                $levelData['classes']
                as $classIndex => $classData
            ) {
                $normalizedClassName =
                    VocalTestPrompt::normalizePathName(
                        $classData['name']
                    );

                if (
                    in_array(
                        $normalizedClassName,
                        $classNames,
                        true
                    )
                ) {
                    return back()
                        ->withErrors([
                            "levels.{$levelIndex}.classes.{$classIndex}.name" =>
                                'Cette classe est déjà présente dans ce niveau.',
                        ])
                        ->withInput();
                }

                $classNames[] =
                    $normalizedClassName;
            }
        }

        $subject = DB::transaction(
            function () use (
                $validated,
                $subjectName,
                $classSlotService
            ) {
                $normalizedSubjectName =
                    VocalTestPrompt::normalizePathName(
                        $subjectName
                    );

                /*
                 * Si la matière existe déjà, on réutilise la même
                 * fiche. Sinon elle est créée.
                 */
                $subject = Subject::query()
                    ->get()
                    ->first(
                        fn (Subject $candidate) =>
                            VocalTestPrompt::normalizePathName(
                                $candidate->name
                            ) === $normalizedSubjectName
                    );

                /*
                 * Coran reste religieux.
                 * Toute nouvelle matière est scolaire par défaut.
                 * Une matière existante conserve son type.
                 */
                $inferredType =
                    $normalizedSubjectName === 'coran'
                        ? 'religieux'
                        : 'scolaire';

                $subjectData = [
                    'name' => $subjectName,
                    'type' => $subject
                        ? ($subject->type ?: $inferredType)
                        : $inferredType,
                ];

                /*
                 * Statut administratif de la matière.
                 * Le statut est enregistré sans supprimer
                 * aucune donnée pédagogique.
                 */
                $subjectStatus =
                    $validated['status'];

                $description = trim(
                    (string) (
                        $validated['description']
                        ?? ''
                    )
                );

                if ($description !== '') {
                    $subjectData['description'] =
                        $description;
                }

                if ($subject) {
                    $subject->fill($subjectData);
                    $subject->status =
                        $subjectStatus;
                    $subject->save();
                } else {
                    $subject =
                        Subject::create(
                            $subjectData
                        );

                    $subject->status =
                        $subjectStatus;
                    $subject->save();
                }

                foreach (
                    $validated['levels']
                    as $levelIndex => $levelData
                ) {
                    $normalizedLevelName =
                        VocalTestPrompt
                            ::normalizePathName(
                                $levelData['name']
                            );

                    $level = Level::query()
                        ->where(
                            'subject_id',
                            $subject->id
                        )
                        ->get()
                        ->first(
                            fn (Level $candidate) =>
                                VocalTestPrompt
                                    ::normalizePathName(
                                        $candidate->name
                                    )
                                === $normalizedLevelName
                        );

                    $levelDataToSave = [
                        'subject_id' =>
                            $subject->id,
                        'name' =>
                            trim(
                                $levelData['name']
                            ),
                        'description' =>
                            isset(
                                $levelData[
                                    'description'
                                ]
                            )
                                ? trim(
                                    (string) $levelData[
                                        'description'
                                    ]
                                )
                                : null,
                        'order' =>
                            $levelIndex + 1,
                    ];

                    if ($level) {
                        $level->fill(
                            $levelDataToSave
                        );
                        $level->save();
                    } else {
                        $level = Level::create(
                            $levelDataToSave
                        );
                    }

                    foreach (
                        $levelData['classes']
                        as $classData
                    ) {
                        $normalizedClassName =
                            VocalTestPrompt
                                ::normalizePathName(
                                    $classData[
                                        'name'
                                    ]
                                );

                        $classRoom =
                            ClassRoom::query()
                                ->where(
                                    'level_id',
                                    $level->id
                                )
                                ->get()
                                ->first(
                                    fn (
                                        ClassRoom
                                        $candidate
                                    ) =>
                                        VocalTestPrompt
                                            ::normalizePathName(
                                                $candidate
                                                    ->name
                                            )
                                        ===
                                        $normalizedClassName
                                );

                        if (!$classRoom) {
                            $classRoom =
                                ClassRoom::create([
                                    'name' =>
                                        trim(
                                            $classData[
                                                'name'
                                            ]
                                        ),
                                    'level_id' =>
                                        $level->id,
                                ]);
                        }

                        $classRoom
                            ->subjects()
                            ->syncWithoutDetaching([
                                $subject->id,
                            ]);

                        /*
                         * Les quatre créneaux sont créés
                         * AUTOMATIQUEMENT dès que la classe existe.
                         *
                         * Débutant      → D1 D2 D3 D4
                         * Intermédiaire → I1 I2 I3 I4
                         * Avancé        → A1 A2 A3 A4
                         * Autre         → G1 G2 G3 G4
                         */
                        $classSlotService
                            ->syncForPath(
                                $subject,
                                $level,
                                $classRoom
                            );
                    }
                }

                return $subject;
            }
        );

        return redirect()
            ->route(
                'admin.subjects.levels',
                $subject
            )
            ->with(
                'success',
                'La structure de la matière et ses 4 créneaux par classe ont été enregistrés avec succès.'
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
    public function subjectLevels(
        Subject $subject
    ) {
        $subjects = Subject::query()
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->where(
                'subject_id',
                $subject->id
            )
            ->with([
                'classes' =>
                    function ($query) use (
                        $subject
                    ) {
                        $query
                            ->whereHas(
                                'subjects',
                                fn ($subjectQuery) =>
                                    $subjectQuery
                                        ->where(
                                            'subjects.id',
                                            $subject->id
                                        )
                            )
                            ->orderBy('name');
                    },
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        /*
         * Pour la matière Arabe, l'administration doit afficher
         * uniquement les deux parcours officiels :
         *
         * - Communication
         * - Lecture & Écriture
         *
         * Les anciennes lignes Débutant / Intermédiaire / Avancé
         * restent dans la base de données pour ne rien supprimer,
         * mais elles ne sont plus affichées sur cette page.
         */
        if (
            VocalTestPrompt::normalizePathName(
                $subject->name
            ) === 'arabe'
        ) {
            $allowedArabicPaths = [
                'communication',
                VocalTestPrompt::normalizePathName(
                    'Lecture & Écriture'
                ),
            ];

            $levels = $levels
                ->filter(
                    fn (Level $level) =>
                        in_array(
                            VocalTestPrompt::normalizePathName(
                                $level->name
                            ),
                            $allowedArabicPaths,
                            true
                        )
                )
                ->sortBy(function (Level $level) {
                    $order = [
                        'communication' => 1,
                        'lecture et ecriture' => 2,
                    ];

                    return $order[
                        VocalTestPrompt::normalizePathName(
                            $level->name
                        )
                    ] ?? 99;
                })
                ->values();
        }

        $subject->setAttribute(
            'is_high_school_support',
            $this->isHighSchoolSupport(
                $subject
            )
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
            (int) $level->subject_id ===
                (int) $subject->id,
            404
        );

        $classes = ClassRoom::query()
            ->where(
                'level_id',
                $level->id
            )
            ->whereHas(
                'subjects',
                fn ($query) =>
                    $query->where(
                        'subjects.id',
                        $subject->id
                    )
            )
            ->orderBy('name')
            ->get();

        /*
         * Garantit que chaque classe possède ses quatre
         * créneaux structurels même pour les anciennes classes.
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
                        ->orderBy(
                            'position'
                        )
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
    public function subjectCourses(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        abort_unless(
            (int) $level->subject_id ===
                (int) $subject->id
            && (int) $class->level_id ===
                (int) $level->id
            && $class
                ->subjects()
                ->where(
                    'subjects.id',
                    $subject->id
                )
                ->exists(),
            404,
            'Cette classe n’appartient pas à la matière sélectionnée.'
        );

        $courses = Course::approved()
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'class_id',
                $class->id
            )
            ->with([
                'classRoom',
                'subject',
            ])
            ->get();

        return view(
            'admin.subjects.courses',
            compact(
                'level',
                'class',
                'subject',
                'courses'
            )
        );
    }

    private function allowedSubjectConfig(
        string $subjectName
    ): ?array {
        $normalizedName =
            VocalTestPrompt::normalizePathName($subjectName);

        return self::ALLOWED_SUBJECTS[$normalizedName] ?? null;
    }


    /**
     * Formulaire de modification d'une matière.
     */
    public function editSubject(
        Subject $subject
    ) {
        return view(
            'admin.subjects.edit',
            compact('subject')
        );
    }

    /**
     * Modifier une matière sans toucher à sa structure.
     */
    public function updateSubject(
        Request $request,
        Subject $subject
    ) {
        /*
         * Action rapide depuis la carte Matière :
         * Activer / Masquer sans modifier le reste de la fiche.
         */
        if ($request->boolean('_visibility_only')) {
            $visibility = $request->validate([
                'is_active' => [
                    'required',
                    'boolean',
                ],
            ]);

            $isActive = (bool) $visibility['is_active'];

            $subject->status = $isActive
                ? 'active'
                : 'inactive';
            $subject->save();

            return back()->with(
                'success',
                $isActive
                    ? 'La matière « ' . $subject->name . ' » est maintenant active.'
                    : 'La matière « ' . $subject->name . ' » est maintenant masquée.'
            );
        }

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:120',
                ],
                'description' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
                'status' => [
                    'required',
                    'in:active,coming_soon,inactive',
                ],
            ],
            [
                'name.required' =>
                    'Écrivez le nom de la matière.',
                'status.required' =>
                    'Choisissez le statut de la matière.',
                'status.in' =>
                    'Le statut sélectionné est invalide.',
            ]
        );

        $newName = trim(
            $validated['name']
        );

        $normalizedNewName =
            VocalTestPrompt::normalizePathName(
                $newName
            );

        $duplicate = Subject::query()
            ->whereKeyNot($subject->id)
            ->get()
            ->contains(
                fn (Subject $candidate) =>
                    VocalTestPrompt::normalizePathName(
                        $candidate->name
                    ) === $normalizedNewName
            );

        if ($duplicate) {
            return back()
                ->withErrors([
                    'name' =>
                        'Une matière avec ce nom existe déjà.',
                ])
                ->withInput();
        }

        $subject->name = $newName;

        /*
         * Même règle que le formulaire de création actuel :
         * Coran = Religieux, le reste = Scolaire.
         */
        $subject->type =
            $normalizedNewName === 'coran'
                ? 'religieux'
                : 'scolaire';

        $subject->status =
            $validated['status'];

        $description = trim(
            (string) (
                $validated['description']
                ?? ''
            )
        );

        $subject->description =
            $description !== ''
                ? $description
                : null;

        $subject->save();

        return redirect()
            ->route('admin.subjects.index')
            ->with(
                'success',
                'La matière a été modifiée avec succès.'
            );
    }

    /**
     * Supprimer une matière uniquement si elle n'est plus utilisée.
     *
     * Cette protection évite de supprimer accidentellement des
     * cours, lives, étudiants, rendez-vous ou historiques.
     */
    public function destroySubject(
        Subject $subject
    ) {
        if (
            Level::query()
                ->where(
                    'subject_id',
                    $subject->id
                )
                ->exists()
        ) {
            return back()->with(
                'error',
                'Impossible de supprimer cette matière : supprimez d’abord ses niveaux.'
            );
        }

        $usage = $this->firstRelatedUsage(
            'subject_id',
            $subject->id,
            [
                'courses' =>
                    'des cours',
                'class_room_subject' =>
                    'des classes',
                'class_slots' =>
                    'des créneaux',
                'messages' =>
                    'des messages',
                'prof_assignments' =>
                    'des affectations professeur',
                'assignments' =>
                    'des devoirs',
                'schedules' =>
                    'du planning',
                'vocal_test_prompts' =>
                    'des tests vocaux',
                'vocal_test_submissions' =>
                    'des soumissions de tests vocaux',
                'high_school_test_submissions' =>
                    'des tests écrits',
                'test_appointments' =>
                    'des rendez-vous',
                'absences' =>
                    'des absences',
                'class_user' =>
                    'des inscriptions étudiantes',
                'tests' =>
                    'des tests',
            ]
        );

        if ($usage !== null) {
            return back()->with(
                'error',
                'Impossible de supprimer cette matière : elle est encore utilisée par '
                . $usage
                . '.'
            );
        }

        $subjectName = $subject->name;
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with(
                'success',
                'La matière « '
                . $subjectName
                . ' » a été supprimée.'
            );
    }

    /**
     * Ajouter un nouveau niveau / parcours directement depuis
     * /admin/subjects/{subject}/levels.
     */
    public function storeSubjectLevel(
        Request $request,
        Subject $subject
    ) {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:120',
                ],
                'description' => [
                    'nullable',
                    'string',
                    'max:500',
                ],
            ],
            [
                'name.required' =>
                    'Écrivez le nom du niveau.',
            ]
        );

        $newName = trim(
            $validated['name']
        );

        $normalizedNewName =
            VocalTestPrompt::normalizePathName(
                $newName
            );

        $duplicate = Level::query()
            ->where(
                'subject_id',
                $subject->id
            )
            ->get()
            ->contains(
                fn (Level $candidate) =>
                    VocalTestPrompt::normalizePathName(
                        $candidate->name
                    ) === $normalizedNewName
            );

        if ($duplicate) {
            return back()
                ->withErrors([
                    'name' =>
                        'Ce niveau existe déjà dans cette matière.',
                ])
                ->withInput();
        }

        $nextOrder = (
            (int) Level::query()
                ->where(
                    'subject_id',
                    $subject->id
                )
                ->max('order')
        ) + 1;

        $description = trim(
            (string) (
                $validated['description']
                ?? ''
            )
        );

        Level::create([
            'subject_id' =>
                $subject->id,
            'name' =>
                $newName,
            'description' =>
                $description !== ''
                    ? $description
                    : null,
            'order' =>
                $nextOrder,
        ]);

        return redirect()
            ->route(
                'admin.subjects.levels',
                $subject
            )
            ->with(
                'success',
                'Le niveau « '
                . $newName
                . ' » a été ajouté avec succès.'
            );
    }

    /**
     * Ajouter une classe directement depuis
     * /admin/subjects/{subject}/levels/{level}/classes.
     *
     * Les quatre créneaux structurels sont générés immédiatement.
     */
    public function storeSubjectClass(
        Request $request,
        Subject $subject,
        Level $level,
        ClassSlotService $classSlotService
    ) {
        $this->assertLevelBelongsToSubject(
            $subject,
            $level
        );

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:120',
                ],
            ],
            [
                'name.required' =>
                    'Écrivez le nom de la classe.',
            ]
        );

        $newName = trim(
            $validated['name']
        );

        $normalizedNewName =
            VocalTestPrompt::normalizePathName(
                $newName
            );

        /*
         * Une classe portant le même nom peut déjà exister dans ce
         * niveau mais ne pas être encore liée à cette matière.
         * Dans ce cas on la réutilise au lieu d'en créer une copie.
         */
        $classRoom = ClassRoom::query()
            ->where(
                'level_id',
                $level->id
            )
            ->get()
            ->first(
                fn (ClassRoom $candidate) =>
                    VocalTestPrompt::normalizePathName(
                        $candidate->name
                    ) === $normalizedNewName
            );

        if (
            $classRoom
            && $classRoom
                ->subjects()
                ->where(
                    'subjects.id',
                    $subject->id
                )
                ->exists()
        ) {
            return back()
                ->withErrors([
                    'name' =>
                        'Cette classe existe déjà dans ce niveau.',
                ])
                ->withInput();
        }

        DB::transaction(
            function () use (
                $subject,
                $level,
                $newName,
                $classRoom,
                $classSlotService
            ) {
                if (!$classRoom) {
                    $classRoom =
                        ClassRoom::create([
                            'name' =>
                                $newName,
                            'level_id' =>
                                $level->id,
                        ]);
                }

                $classRoom
                    ->subjects()
                    ->syncWithoutDetaching([
                        $subject->id,
                    ]);

                $classSlotService
                    ->syncForPath(
                        $subject,
                        $level,
                        $classRoom
                    );
            }
        );

        return redirect()
            ->route(
                'admin.subjects.classes',
                [
                    $subject,
                    $level,
                ]
            )
            ->with(
                'success',
                'La classe « '
                . $newName
                . ' » et ses 4 créneaux ont été ajoutés.'
            );
    }

    /**
     * Formulaire de modification d'un niveau / parcours.
     */
    public function editSubjectLevel(
        Subject $subject,
        Level $level
    ) {
        $this->assertLevelBelongsToSubject(
            $subject,
            $level
        );

        return view(
            'admin.subjects.level-edit',
            compact(
                'subject',
                'level'
            )
        );
    }

    /**
     * Modifier un niveau / parcours.
     */
    public function updateSubjectLevel(
        Request $request,
        Subject $subject,
        Level $level
    ) {
        $this->assertLevelBelongsToSubject(
            $subject,
            $level
        );

        /*
         * Action rapide depuis la carte Niveau / Parcours.
         * On conserve les classes, cours, créneaux et affectations.
         */
        if ($request->boolean('_visibility_only')) {
            $visibility = $request->validate([
                'is_active' => [
                    'required',
                    'boolean',
                ],
            ]);

            $isActive = (bool) $visibility['is_active'];

            $level->is_active = $isActive;
            $level->save();

            return back()->with(
                'success',
                $isActive
                    ? 'Le niveau « ' . $level->name . ' » est maintenant actif.'
                    : 'Le niveau « ' . $level->name . ' » est maintenant masqué.'
            );
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $newName = trim(
            $validated['name']
        );

        $normalizedNewName =
            VocalTestPrompt::normalizePathName(
                $newName
            );

        $duplicate = Level::query()
            ->where(
                'subject_id',
                $subject->id
            )
            ->whereKeyNot($level->id)
            ->get()
            ->contains(
                fn (Level $candidate) =>
                    VocalTestPrompt::normalizePathName(
                        $candidate->name
                    ) === $normalizedNewName
            );

        if ($duplicate) {
            return back()
                ->withErrors([
                    'name' =>
                        'Ce niveau existe déjà dans cette matière.',
                ])
                ->withInput();
        }

        $level->name = $newName;

        $description = trim(
            (string) (
                $validated['description']
                ?? ''
            )
        );

        $level->description =
            $description !== ''
                ? $description
                : null;

        $level->save();

        return redirect()
            ->route(
                'admin.subjects.levels',
                $subject
            )
            ->with(
                'success',
                'Le niveau a été modifié avec succès.'
            );
    }

    /**
     * Supprimer un niveau uniquement lorsqu'il ne contient plus
     * de classes ni d'autres données liées.
     */
    public function destroySubjectLevel(
        Subject $subject,
        Level $level
    ) {
        $this->assertLevelBelongsToSubject(
            $subject,
            $level
        );

        if (
            ClassRoom::query()
                ->where(
                    'level_id',
                    $level->id
                )
                ->exists()
        ) {
            return back()->with(
                'error',
                'Impossible de supprimer ce niveau : supprimez d’abord ses classes.'
            );
        }

        $usage = $this->firstRelatedUsage(
            'level_id',
            $level->id,
            [
                'courses' =>
                    'des cours',
                'modules' =>
                    'des modules',
                'class_slots' =>
                    'des créneaux',
                'prof_assignments' =>
                    'des affectations professeur',
                'assignments' =>
                    'des devoirs',
                'schedules' =>
                    'du planning',
                'vocal_test_prompts' =>
                    'des tests vocaux',
                'vocal_test_submissions' =>
                    'des soumissions de tests vocaux',
                'high_school_test_submissions' =>
                    'des tests écrits',
                'test_appointments' =>
                    'des rendez-vous',
                'absences' =>
                    'des absences',
                'tests' =>
                    'des tests',
            ]
        );

        if ($usage !== null) {
            return back()->with(
                'error',
                'Impossible de supprimer ce niveau : il est encore utilisé par '
                . $usage
                . '.'
            );
        }

        $levelName = $level->name;
        $level->delete();

        return redirect()
            ->route(
                'admin.subjects.levels',
                $subject
            )
            ->with(
                'success',
                'Le niveau « '
                . $levelName
                . ' » a été supprimé.'
            );
    }

    /**
     * Formulaire de modification d'une classe.
     */
    public function editSubjectClass(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        $this->assertClassBelongsToPath(
            $subject,
            $level,
            $class
        );

        return view(
            'admin.subjects.class-edit',
            compact(
                'subject',
                'level',
                'class'
            )
        );
    }

    /**
     * Modifier une classe et resynchroniser automatiquement
     * ses quatre créneaux structurels.
     */
    public function updateSubjectClass(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $class,
        ClassSlotService $classSlotService
    ) {
        $this->assertClassBelongsToPath(
            $subject,
            $level,
            $class
        );

        /*
         * Action rapide depuis la carte Classe.
         * Masquer une classe ne supprime aucune donnée liée.
         */
        if ($request->boolean('_visibility_only')) {
            $visibility = $request->validate([
                'is_active' => [
                    'required',
                    'boolean',
                ],
            ]);

            $isActive = (bool) $visibility['is_active'];

            $class->is_active = $isActive;
            $class->save();

            return back()->with(
                'success',
                $isActive
                    ? 'La classe « ' . $class->name . ' » est maintenant active.'
                    : 'La classe « ' . $class->name . ' » est maintenant masquée.'
            );
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
            ],
        ]);

        $newName = trim(
            $validated['name']
        );

        $normalizedNewName =
            VocalTestPrompt::normalizePathName(
                $newName
            );

        $duplicate = ClassRoom::query()
            ->where(
                'level_id',
                $level->id
            )
            ->whereKeyNot($class->id)
            ->get()
            ->contains(
                fn (ClassRoom $candidate) =>
                    VocalTestPrompt::normalizePathName(
                        $candidate->name
                    ) === $normalizedNewName
            );

        if ($duplicate) {
            return back()
                ->withErrors([
                    'name' =>
                        'Cette classe existe déjà dans ce niveau.',
                ])
                ->withInput();
        }

        $class->name = $newName;
        $class->save();

        $class
            ->subjects()
            ->syncWithoutDetaching([
                $subject->id,
            ]);

        /*
         * Après un renommage, par exemple :
         * Débutant → Avancé,
         * les créneaux actifs deviennent A1 A2 A3 A4.
         */
        $classSlotService->syncForPath(
            $subject,
            $level,
            $class
        );

        return redirect()
            ->route(
                'admin.subjects.classes',
                [
                    $subject,
                    $level,
                ]
            )
            ->with(
                'success',
                'La classe et ses 4 créneaux ont été mis à jour.'
            );
    }

    /**
     * Supprimer une classe seulement si aucune donnée métier
     * importante ne dépend encore d'elle.
     */
    public function destroySubjectClass(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        $this->assertClassBelongsToPath(
            $subject,
            $level,
            $class
        );

        $usage = $this->firstRelatedUsage(
            'class_id',
            $class->id,
            [
                'courses' =>
                    'des cours',
                'lives' =>
                    'des lives',
                'users' =>
                    'des comptes utilisateurs',
                'class_user' =>
                    'des inscriptions étudiantes',
                'prof_assignments' =>
                    'des affectations professeur',
                'assignments' =>
                    'des devoirs',
                'schedules' =>
                    'du planning',
                'absences' =>
                    'des absences',
                'vocal_test_prompts' =>
                    'des tests vocaux',
                'vocal_test_submissions' =>
                    'des soumissions de tests vocaux',
                'high_school_test_submissions' =>
                    'des tests écrits',
                'test_appointments' =>
                    'des rendez-vous',
                'tests' =>
                    'des tests',
            ]
        );

        if ($usage !== null) {
            return back()->with(
                'error',
                'Impossible de supprimer cette classe : elle est encore utilisée par '
                . $usage
                . '.'
            );
        }

        $className = $class->name;

        DB::transaction(
            function () use (
                $subject,
                $level,
                $class
            ) {
                /*
                 * Les class_slots ne sont pas considérés comme
                 * une dépendance bloquante : ce sont les 4 groupes
                 * structurels générés automatiquement.
                 */
                if (
                    Schema::hasTable('class_slots')
                ) {
                    DB::table('class_slots')
                        ->where(
                            'subject_id',
                            $subject->id
                        )
                        ->where(
                            'level_id',
                            $level->id
                        )
                        ->where(
                            'class_id',
                            $class->id
                        )
                        ->delete();
                }

                $class
                    ->subjects()
                    ->detach(
                        $subject->id
                    );

                /*
                 * Si cette classe n'est liée à aucune autre matière,
                 * elle peut être supprimée complètement.
                 */
                if (
                    !$class
                        ->subjects()
                        ->exists()
                ) {
                    $class->delete();
                }
            }
        );

        return redirect()
            ->route(
                'admin.subjects.classes',
                [
                    $subject,
                    $level,
                ]
            )
            ->with(
                'success',
                'La classe « '
                . $className
                . ' » a été supprimée de cette matière.'
            );
    }

    private function assertLevelBelongsToSubject(
        Subject $subject,
        Level $level
    ): void {
        abort_unless(
            (int) $level->subject_id
                === (int) $subject->id,
            404
        );
    }

    private function assertClassBelongsToPath(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ): void {
        $this->assertLevelBelongsToSubject(
            $subject,
            $level
        );

        abort_unless(
            (int) $class->level_id
                === (int) $level->id
            && $class
                ->subjects()
                ->where(
                    'subjects.id',
                    $subject->id
                )
                ->exists(),
            404
        );
    }

    /**
     * Retourne le premier type d'utilisation trouvé pour une
     * colonne donnée. Les tables/colonnes absentes sont ignorées
     * afin de rester compatible avec les installations existantes.
     */
    private function firstRelatedUsage(
        string $column,
        int $id,
        array $tables
    ): ?string {
        foreach (
            $tables
            as $table => $label
        ) {
            if (
                !Schema::hasTable($table)
                || !Schema::hasColumn(
                    $table,
                    $column
                )
            ) {
                continue;
            }

            if (
                DB::table($table)
                    ->where(
                        $column,
                        $id
                    )
                    ->exists()
            ) {
                return $label;
            }
        }

        return null;
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
