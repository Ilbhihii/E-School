<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Level;
use App\Models\Course;
use App\Models\ClassRoom;
use App\Models\VocalTestPrompt;
use App\Models\HighSchoolTestSubmission;
use App\Services\LearningPathService;
use Illuminate\Support\Facades\URL;

class FrontController extends Controller
{
    private LearningPathService $paths;

    public function __construct(LearningPathService $paths)
    {
        $this->paths = $paths;
    }
    public function subjects()
    {
        $subjectsReligieux = Subject::where('type', 'religieux')->get();
        $subjectsScolaire = Subject::where('type', 'scolaire')->get();
        return view('front.classes', compact('subjectsReligieux', 'subjectsScolaire'));
    }

    public function subjectClasses($id)
    {
        $subject = Subject::with('classes')->findOrFail($id);
        return view('front.subject-classes', compact('subject'));
    }

    public function subjectLevels($id)
    {
        $subject = Subject::findOrFail($id);

        $allowedLevelNames =
            $this->levelNamesForSubject($subject);

        $allowedItemNames =
            $this->itemNamesForSubject($subject);

        $levelOrder = array_flip(
            array_map(
                [
                    VocalTestPrompt::class,
                    'normalizePathName',
                ],
                $allowedLevelNames
            )
        );

        $itemOrder = array_flip(
            array_map(
                [
                    VocalTestPrompt::class,
                    'normalizePathName',
                ],
                $allowedItemNames
            )
        );

        $levels = Level::query()
            ->where('subject_id', $subject->id)
            ->when(
                !empty($allowedLevelNames),
                fn ($query) =>
                    $query->whereIn(
                        'name',
                        $allowedLevelNames
                    )
            )
            ->withCount([
                'courses' => fn ($query) =>
                    $query->where(
                        'subject_id',
                        $subject->id
                    ),
            ])
            ->with([
                'classes' => function ($query) use (
                    $subject,
                    $allowedItemNames
                ) {
                    $query
                        ->when(
                            !empty($allowedItemNames),
                            fn ($classQuery) =>
                                $classQuery->whereIn(
                                    'name',
                                    $allowedItemNames
                                )
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
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->sortBy(
                function (Level $level) use (
                    $levelOrder
                ) {
                    return $levelOrder[
                        VocalTestPrompt::normalizePathName(
                            $level->name
                        )
                    ] ?? PHP_INT_MAX;
                }
            )
            ->unique(
                fn (Level $level) =>
                    VocalTestPrompt::normalizePathName(
                        $level->name
                    )
            )
            ->values();

        $isHighSchoolSupport =
            $this->isHighSchoolSupport($subject);

        $levels->each(
            function (Level $level) use (
                $itemOrder,
                $subject,
                $isHighSchoolSupport
            ) {
                $validItems = $level->classes
                    ->sortBy(
                        fn (ClassRoom $classRoom) =>
                            $itemOrder[
                                VocalTestPrompt::normalizePathName(
                                    $classRoom->name
                                )
                            ] ?? PHP_INT_MAX
                    )
                    ->unique(
                        fn (ClassRoom $classRoom) =>
                            VocalTestPrompt::normalizePathName(
                                $classRoom->name
                            )
                    )
                    ->values();

                $validItems->each(
                    function (ClassRoom $classRoom) use (
                        $subject,
                        $level,
                        $isHighSchoolSupport
                    ) {
                        $classRoom->setAttribute(
                            'requires_vocal_test',
                            !$isHighSchoolSupport
                            && VocalTestPrompt::requiresVocalTest(
                                $subject,
                                $level,
                                $classRoom
                            )
                        );

                        $classRoom->setAttribute(
                            'is_without_vocal_test',
                            !$isHighSchoolSupport
                            && VocalTestPrompt::isExcludedPath(
                                $subject,
                                $level,
                                $classRoom
                            )
                        );
                    }
                );

                $level->setRelation(
                    'classes',
                    $validItems
                );

                $level->setAttribute(
                    'available_classes_count',
                    $validItems->count()
                );
            }
        );

        $subject->setAttribute(
            'classes_count',
            $levels->sum('available_classes_count')
        );

        $subject->setAttribute(
            'validated_levels_count',
            $levels->count()
        );

        $subject->setAttribute(
            'is_high_school_support',
            $isHighSchoolSupport
        );

        $subject->setAttribute(
            'child_plural_label',
            $isHighSchoolSupport
                ? 'Matières'
                : 'Classes'
        );

        $sameFamilySubjects = Subject::query()
            ->where('type', $subject->type)
            ->where('id', '!=', $subject->id)
            ->whereIn(
                'name',
                ['Arabe', 'Coran', 'Soutien Lycée']
            )
            ->withCount('courses')
            ->get();

        return view(
            'front.subject-levels',
            compact(
                'subject',
                'levels',
                'sameFamilySubjects'
            )
        );
    }

    public function levelClasses($subjectId, $levelId)
    {
        $subject = Subject::findOrFail($subjectId);

        $level = Level::query()
            ->whereKey($levelId)
            ->where('subject_id', $subject->id)
            ->firstOrFail();

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
            404,
            'Ce parcours ne fait pas partie de la structure actuelle.'
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
            ->unique(
                fn (ClassRoom $classRoom) =>
                    VocalTestPrompt::normalizePathName(
                        $classRoom->name
                    )
            )
            ->values();

        $isHighSchoolSupport =
            $this->isHighSchoolSupport($subject);

        $classes->each(
            function (ClassRoom $classRoom) use (
                $subject,
                $level,
                $isHighSchoolSupport
            ) {
                $classRoom->requires_vocal_test =
                    !$isHighSchoolSupport
                    && VocalTestPrompt::requiresVocalTest(
                        $subject,
                        $level,
                        $classRoom
                    );

                $classRoom->is_without_vocal_test =
                    !$isHighSchoolSupport
                    && VocalTestPrompt::isExcludedPath(
                        $subject,
                        $level,
                        $classRoom
                    );
            }
        );

        return view(
            'front.level-classes',
            compact(
                'subject',
                'level',
                'classes'
            )
        );
    }

    public function courses($subject_id, $level_id, $class_id)
    {
        $subject = Subject::findOrFail($subject_id);

        $level = Level::whereKey($level_id)
            ->where('subject_id', $subject->id)
            ->firstOrFail();

        $class = ClassRoom::whereKey($class_id)
            ->where('level_id', $level->id)
            ->whereHas(
                'subjects',
                fn($query) => $query->where('subjects.id', $subject->id)
            )
            ->firstOrFail();

        /*
         * Soutien Lycée :
         * - test validé : accès à l'espace étudiant ;
         * - test en correction : page de suivi ;
         * - sinon : affichage du test écrit.
         */
        if ($this->isHighSchoolSupport($subject)) {
            if (
                auth()->check()
                && auth()->user()->role === 'student'
            ) {
                $submission =
                    HighSchoolTestSubmission::query()
                        ->where(
                            'user_id',
                            auth()->id()
                        )
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
                        ->latest('submitted_at')
                        ->latest('id')
                        ->first();

                if (
                    $submission
                    && $submission->isApproved()
                ) {
                    return redirect()->route(
                        'student.subjects.courses',
                        [
                            $subject,
                            $level,
                            $class,
                        ]
                    )->with(
                        'success',
                        'Test validé : accès aux cours autorisé.'
                    );
                }

                if (
                    $submission
                    && $submission->isPendingReview()
                ) {
                    return redirect()->route(
                        'student.written-tests.show',
                        $submission
                    );
                }
            }

            return redirect()->route(
                'high-school-test.show',
                [
                    $subject,
                    $level,
                    $class,
                ]
            );
        }

        if (!VocalTestPrompt::isSupportedPath($subject, $level, $class)) {
            return redirect()
                ->route('front.subject.levels', $subject->id)
                ->with(
                    'info',
                    'Ce parcours a été remplacé par la nouvelle structure.'
                );
        }

        if (VocalTestPrompt::isExcludedPath($subject, $level, $class)) {
            return redirect()
                ->route('appointment.create', ['type' => 'test'])
                ->with(
                    'info',
                    'Aucun test vocal n’est demandé pour ce parcours débutant. Prenez votre rendez-vous, puis créez votre compte.'
                );
        }

        $prompt = VocalTestPrompt::activeForPath(
            $subject,
            $level,
            $class
        );

        if ($prompt) {
            return redirect()->route(
                'vocal-test.create',
                [$subject, $level, $class]
            );
        }

        return redirect()
            ->route('appointment.create', ['type' => 'test'])
            ->with(
                'info',
                'Aucun test vocal actif n’est configuré pour cette sélection. Prenez votre rendez-vous, puis créez votre compte.'
            );
    }



    /**
     * Liste publique des cours d’un niveau.
     * Les ressources privées ne sont jamais exposées ici.
     */
    public function levelCourses($id)
    {
        $level = Level::with(['subject'])->findOrFail($id);
        $courses = Course::query()
            ->where('level_id', $level->id)
            ->with(['subject', 'level', 'classRoom'])
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $level->setRelation('courses', $courses);

        return view('front.level-courses', compact('level'));
    }

    /**
     * Détail d’un cours public ou autorisé.
     */
    public function showCourse($id)
    {
        $course = Course::with([
            'subject',
            'level',
            'classRoom',
            'learningTests.questions.answers',
        ])->findOrFail($id);

        $user = auth()->user();
        $canAccess = $this->paths->userCanAccessCourse($user, $course);

        if (!$canAccess) {
            if (!$user) {
                session()->put('url.intended', request()->fullUrl());
                return redirect()->route('login')
                    ->with('info', 'Connectez-vous pour accéder à ce cours premium.');
            }

            if ($user->isStudent() && !$user->is_active) {
                return redirect()->route('student.waiting');
            }

            if ($user->isStudent() && !$user->is_paid) {
                return redirect()->route('plans')
                    ->with('error', 'Un abonnement actif est nécessaire pour accéder à ce cours.');
            }

            abort(403, 'Ce cours ne fait pas partie de votre parcours.');
        }

        $sameFamilySubjects = $course->subject
            ? Subject::query()
                ->where('type', $course->subject->type)
                ->where('id', '!=', $course->subject->id)
                ->whereIn('name', ['Arabe', 'Coran', 'Soutien Lycée'])
                ->withCount('courses')
                ->get()
            : collect();

        $resourceUrls = [];
        foreach (['video', 'pdf', 'link'] as $type) {
            $exists = $type === 'video'
                ? ($course->video || $course->video_url)
                : ($type === 'pdf' ? $course->pdf : $course->course_link);

            if ($exists) {
                $resourceUrls[$type] = URL::temporarySignedRoute(
                    'course.resource',
                    now()->addMinutes(10),
                    ['course' => $course->id, 'type' => $type]
                );
            }
        }

        return view('front.course-show', compact(
            'course',
            'sameFamilySubjects',
            'resourceUrls'
        ));
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
        return VocalTestPrompt::normalizePathName(
            $subject->name
        ) === 'soutien lycee';
    }

    /**
     * Affiche les classes d'un niveau (navigation publique)
     */
    public function publicClasses(Level $level)
    {
        $classes = \App\Models\ClassRoom::where('level_id', $level->id)
            ->withCount('subjects')
            ->get();

        return view('front.public-classes', compact('level', 'classes'));
    }

    /**
     * Affiche les matières d'une classe (navigation publique)
     */
    public function publicSubjects(Level $level, \App\Models\ClassRoom $class_room)
    {
        $class = $class_room;
        $subjects = Subject::whereIn('name', ['Arabe', 'Coran'])
            ->whereHas('classes', fn($q) => $q->where('class_room_id', $class->id))
            ->withCount('courses')
            ->get();

        return view('front.public-subjects', compact('level', 'class', 'subjects'));
    }

    /**
     * Affiche les cours d'une matière dans une classe (navigation publique)
     */
    public function publicCourses(Level $level, \App\Models\ClassRoom $class_room, Subject $subject)
    {
        $class = $class_room;
        $courses = Course::where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->where('level_id', $level->id)
            ->withCount('learningTests')
            ->get();

        return view('front.public-courses', compact('level', 'class', 'subject', 'courses'));
    }

    public function religieux()
    {
        $subjects = \App\Models\Subject::withCount(['courses', 'classes'])
            ->where('type', 'religieux')
            ->where('name', 'Coran')
            ->get();

        $subjects->each(function ($subject) {
            if ($subject->courses_count > 0) {
                $subject->status_label = 'Disponible';
                $subject->status_icon = 'bi-check-circle-fill';
                $subject->status_color = '#4ADE80';
                $subject->status_bg = 'rgba(34,197,94,0.15)';
                $subject->status_border = 'rgba(34,197,94,0.2)';
            } elseif ($subject->classes_count > 0) {
                $subject->status_label = 'En cours';
                $subject->status_icon = 'bi-hourglass-split';
                $subject->status_color = '#FB923C';
                $subject->status_bg = 'rgba(251,146,60,0.15)';
                $subject->status_border = 'rgba(251,146,60,0.2)';
            } else {
                $subject->status_label = 'Non disponible';
                $subject->status_icon = 'bi-x-circle-fill';
                $subject->status_color = '#FCA5A5';
                $subject->status_bg = 'rgba(239,68,68,0.15)';
                $subject->status_border = 'rgba(239,68,68,0.2)';
            }
        });

        return view('front.religieux', compact('subjects'));
    }

    public function scolaires()
    {
        $subjects = \App\Models\Subject::withCount(['courses', 'classes'])
            ->where('type', 'scolaire')
            ->whereIn('name', ['Arabe', 'Soutien Lycée'])
            ->get();

        $subjects->each(function ($subject) {
            if ($subject->courses_count > 0) {
                $subject->status_label = 'Disponible';
                $subject->status_icon = 'bi-check-circle-fill';
                $subject->status_color = '#4ADE80';
                $subject->status_bg = 'rgba(34,197,94,0.15)';
                $subject->status_border = 'rgba(34,197,94,0.2)';
            } elseif ($subject->classes_count > 0) {
                $subject->status_label = 'En cours';
                $subject->status_icon = 'bi-hourglass-split';
                $subject->status_color = '#FB923C';
                $subject->status_bg = 'rgba(251,146,60,0.15)';
                $subject->status_border = 'rgba(251,146,60,0.2)';
            } else {
                $subject->status_label = 'Non disponible';
                $subject->status_icon = 'bi-x-circle-fill';
                $subject->status_color = '#FCA5A5';
                $subject->status_bg = 'rgba(239,68,68,0.15)';
                $subject->status_border = 'rgba(239,68,68,0.2)';
            }
        });

        return view('front.scolaires', compact('subjects'));
    }
}