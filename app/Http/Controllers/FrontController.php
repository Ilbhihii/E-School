<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Level;
use App\Models\Course;
use App\Models\ClassRoom;
use App\Models\VocalTestPrompt;

class FrontController extends Controller
{
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

        if (!VocalTestPrompt::isSupportedPath($subject, $level, $class)) {
            return redirect()
                ->route('front.subject.levels', $subject->id)
                ->with(
                    'info',
                    'Ce parcours a été remplacé par la nouvelle structure.'
                );
        }

        if (VocalTestPrompt::isExcludedPath($subject, $level, $class)) {
            $appointmentUrl = route(
                'appointment.create',
                ['type' => 'test']
            );

            if (auth()->guest()) {
                session()->put('url.intended', $appointmentUrl);

                return redirect()
                    ->route('register')
                    ->with(
                        'info',
                        'Ce parcours débutant ne nécessite aucun test vocal. Créez votre compte pour continuer.'
                    );
            }

            return redirect()
                ->to($appointmentUrl)
                ->with(
                    'info',
                    'Aucun test vocal n’est demandé pour ce parcours débutant. Vous pouvez prendre rendez-vous directement.'
                );
        }

        $prompt = VocalTestPrompt::activeForPath(
            $subject,
            $level,
            $class
        );

        if ($prompt) {
            $vocalTestUrl = route(
                'vocal-test.create',
                [$subject, $level, $class]
            );

            if (auth()->guest()) {
                session()->put('url.intended', $vocalTestUrl);

                return redirect()
                    ->route('register')
                    ->with(
                        'info',
                        'Créez votre compte pour passer le test vocal.'
                    );
            }

            return redirect()->to($vocalTestUrl);
        }

        $appointmentUrl = route(
            'appointment.create',
            ['type' => 'test']
        );

        if (auth()->guest()) {
            session()->put('url.intended', $appointmentUrl);

            return redirect()
                ->route('register')
                ->with(
                    'info',
                    'Créez votre compte pour poursuivre votre inscription.'
                );
        }

        return redirect()
            ->to($appointmentUrl)
            ->with(
                'info',
                'Aucun test vocal actif n’est configuré pour cette sélection. Vous pouvez prendre rendez-vous.'
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