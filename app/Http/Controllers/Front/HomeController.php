<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Live;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    private const ACTIVE_SUBJECTS = [
        'Arabe',
        'Coran',
        'Soutien Lycée',
    ];

    public function index()
    {
        $classes = ClassRoom::count();
        $courses = Course::approved()->count();
        $lives = Live::latest()->take(3)->get();

        $subjectsReligieux = Subject::query()
            ->where('name', 'Coran')
            ->get();

        $subjectsScolaire = Subject::query()
            ->whereIn(
                'name',
                ['Arabe', 'Soutien Lycée']
            )
            ->get();

        $subjectsGrouped = [
            'Matières Religieuses' => [
                'subjects' => $subjectsReligieux,
                'color' => 'primary',
            ],
            'Matières Scolaires' => [
                'subjects' => $subjectsScolaire,
                'color' => 'success',
            ],
        ];

        return view(
            'front.home',
            compact(
                'classes',
                'courses',
                'lives',
                'subjectsGrouped'
            )
        );
    }

    public function niveaux()
    {
        $levels = \App\Models\Level::withCount([
                'classes as class_count',
            ])
            ->with([
                'classes' => function ($query) {
                    $query->withCount('subjects');
                },
            ])
            ->orderBy('order')
            ->get();

        $totalClasses = $levels->sum('class_count');
        $totalCourses = Course::approved()->count();

        return view(
            'front.public-levels',
            compact(
                'levels',
                'totalClasses',
                'totalCourses'
            )
        );
    }

    public function classes()
    {
        /*
         * La page publique /classes affiche uniquement
         * les matières dont le statut administratif est "active".
         *
         * - active      => visible
         * - coming_soon => masquée
         * - inactive    => masquée
         *
         * On ne supprime aucune donnée : le filtrage est uniquement
         * appliqué à l'affichage public.
         */
        $subjects = Subject::query()
            ->with([
                'levels.classes.subjects',
            ])
            ->where('status', 'active')
            /*
             * "Administration" est une matière interne.
             * Elle ne doit jamais apparaître sur la page publique
             * /classes, même si elle est marquée Active.
             */
            ->whereRaw(
                "LOWER(TRIM(name)) <> ?",
                ['administration']
            )
            ->orderByRaw(
                "CASE
                    WHEN LOWER(name) = 'arabe' THEN 1
                    WHEN LOWER(name) = 'coran' THEN 2
                    WHEN LOWER(name) IN (
                        'soutien lycée',
                        'soutient lycée'
                    ) THEN 3
                    ELSE 4
                END"
            )
            ->orderBy('name')
            ->get();

        $subjects->each(function (Subject $subject) {
            $normalizedSubjectName =
                VocalTestPrompt::normalizePathName(
                    $subject->name
                );

            /*
             * Pour les matières officielles déjà structurées,
             * on conserve les règles pédagogiques existantes.
             *
             * Pour toute nouvelle matière créée depuis l'input
             * libre (Français, Mathématiques, etc.), on affiche
             * simplement ses niveaux/classes réellement liés.
             */
            $usesOfficialPathRules = in_array(
                $normalizedSubjectName,
                [
                    'arabe',
                    'coran',
                    'soutien lycee',
                    'soutient lycee',
                ],
                true
            );

            if ($usesOfficialPathRules) {
                $allowedLevelNames =
                    $this->levelNamesForSubject(
                        $subject
                    );

                $allowedItemNames =
                    $this->itemNamesForSubject(
                        $subject
                    );

                $normalizedAllowedLevels =
                    array_map(
                        [
                            VocalTestPrompt::class,
                            'normalizePathName',
                        ],
                        $allowedLevelNames
                    );

                $normalizedAllowedItems =
                    array_map(
                        [
                            VocalTestPrompt::class,
                            'normalizePathName',
                        ],
                        $allowedItemNames
                    );

                $subject->available_levels =
                    $subject->levels
                        ->filter(
                            fn ($level) =>
                                in_array(
                                    VocalTestPrompt::normalizePathName(
                                        $level->name
                                    ),
                                    $normalizedAllowedLevels,
                                    true
                                )
                        )
                        ->sortBy('order')
                        ->unique(
                            fn ($level) =>
                                VocalTestPrompt::normalizePathName(
                                    $level->name
                                )
                        )
                        ->values();

                $availableItems =
                    $subject->available_levels
                        ->flatMap(
                            fn ($level) =>
                                $level->classes
                        )
                        ->filter(
                            function (
                                $classRoom
                            ) use (
                                $subject,
                                $normalizedAllowedItems
                            ) {
                                $belongsToSubject =
                                    $classRoom
                                        ->subjects
                                        ->contains(
                                            'id',
                                            $subject->id
                                        );

                                $isAllowed =
                                    in_array(
                                        VocalTestPrompt::normalizePathName(
                                            $classRoom->name
                                        ),
                                        $normalizedAllowedItems,
                                        true
                                    );

                                return
                                    $belongsToSubject
                                    && $isAllowed;
                            }
                        )
                        ->unique(
                            fn ($classRoom) =>
                                VocalTestPrompt::normalizePathName(
                                    $classRoom->name
                                )
                        )
                        ->values();
            } else {
                /*
                 * Nouvelle matière libre :
                 * tous ses niveaux et toutes ses classes liées
                 * sont considérés comme disponibles.
                 */
                $subject->available_levels =
                    $subject->levels
                        ->sortBy('order')
                        ->unique(
                            fn ($level) =>
                                VocalTestPrompt::normalizePathName(
                                    $level->name
                                )
                        )
                        ->values();

                $availableItems =
                    $subject->available_levels
                        ->flatMap(
                            fn ($level) =>
                                $level->classes
                        )
                        ->filter(
                            fn ($classRoom) =>
                                $classRoom
                                    ->subjects
                                    ->contains(
                                        'id',
                                        $subject->id
                                    )
                        )
                        ->unique('id')
                        ->values();
            }

            $subject->setAttribute(
                'classes_count',
                $availableItems->count()
            );

            $subject->setAttribute(
                'is_high_school_support',
                $this->isHighSchoolSupport(
                    $subject
                )
            );

            /*
             * Si la matière arrive jusqu'ici, son statut BDD
             * est obligatoirement "active". Le visiteur voit
             * donc le badge "Disponible".
             */
            $subject->status_label =
                'Disponible';
            $subject->status_icon =
                'bi-check-circle-fill';
            $subject->status_color =
                '#4ADE80';
            $subject->status_bg =
                'rgba(34,197,94,0.15)';
            $subject->status_border =
                'rgba(34,197,94,0.2)';
        });

        return view(
            'front.classes',
            compact('subjects')
        );
    }

    public function classCourses($id)
    {
        $class = ClassRoom::with([
            'courses',
            'subjects',
        ])->findOrFail($id);

        $courses = $class->courses;

        return view(
            'front.class-courses',
            compact('class', 'courses')
        );
    }

    public function courseShow($id)
    {
        $course = Course::approved()->findOrFail($id);

        return view(
            'front.course-show',
            compact('course')
        );
    }

    public function allClassesCourses()
    {
        $classes = ClassRoom::with([
            'courses',
            'subjects',
        ])->get();

        return view(
            'front.all-classes-courses',
            compact('classes')
        );
    }

    public function lives()
    {
        $user = auth()->user();
        $accessRestricted = false;

        $query = Live::query()
            ->with('classRoom')
            ->latest();

        if ($user && $user->isStudent()) {
            if (!(bool) $user->is_active) {
                $accessRestricted = true;
                $lives = collect();
            } else {
                $classIds = collect([
                    $user->class_id,
                ])->filter();

                if (Schema::hasTable('class_user')) {
                    $assignedClassIds = DB::table(
                        'class_user'
                    )
                        ->where('user_id', $user->id)
                        ->whereNotNull('class_id')
                        ->pluck('class_id');

                    $classIds = $classIds
                        ->merge($assignedClassIds);
                }

                $classIds = $classIds
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();

                $lives = $classIds->isEmpty()
                    ? collect()
                    : $query
                        ->whereIn(
                            'class_id',
                            $classIds
                        )
                        ->get();
            }
        } else {
            $lives = $query->get();
        }

        return view(
            'front.lives',
            compact(
                'lives',
                'accessRestricted'
            )
        );
    }

    private function levelNamesForSubject(
        Subject $subject
    ): array {
        if ($this->isHighSchoolSupport($subject)) {
            return ['BAC'];
        }

        return VocalTestPrompt::pathNamesForSubject(
            $subject
        );
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

        return VocalTestPrompt::allowedClassNames();
    }

    private function isHighSchoolSupport(
        Subject $subject
    ): bool {
        return in_array(
            VocalTestPrompt::normalizePathName(
                $subject->name
            ),
            [
                'soutien lycee',
                'soutient lycee',
            ],
            true
        );
    }
}
