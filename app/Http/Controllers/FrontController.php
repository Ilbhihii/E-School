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
        $subjectsReligieux = Subject::where('type', 'religieux')
            ->where('status', 'active')
            ->get();
        $subjectsScolaire = Subject::where('type', 'scolaire')
            ->where('status', 'active')
            ->get();
        return view('front.classes', compact('subjectsReligieux', 'subjectsScolaire'));
    }

    public function subjectClasses($id)
    {
        $subject = Subject::query()
            ->where('status', 'active')
            ->with([
                'classes' => fn ($query) =>
                    $query
                        ->where('is_active', true)
                        ->whereHas(
                            'level',
                            fn ($levelQuery) =>
                                $levelQuery->where(
                                    'is_active',
                                    true
                                )
                        ),
            ])
            ->findOrFail($id);

        return view('front.subject-classes', compact('subject'));
    }

    public function subjectLevels($id)
    {
        $subject = Subject::query()
            ->where('status', 'active')
            ->findOrFail($id);

        $allowedLevelNames =
            VocalTestPrompt::pathNamesForSubject($subject);

        $allowedClassNames =
            VocalTestPrompt::allowedClassNames();

        $classOrder = array_flip(
            array_map(
                [VocalTestPrompt::class, 'normalizePathName'],
                $allowedClassNames
            )
        );

        $levels = Level::query()
            ->where('subject_id', $subject->id)
            ->where('is_active', true)
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
                    $allowedClassNames
                ) {
                    $query
                        ->where('is_active', true)
                        ->whereIn(
                            'name',
                            $allowedClassNames
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
            ->sortBy(function (Level $level) use (
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
            })
            ->unique(
                fn (Level $level) =>
                    VocalTestPrompt::normalizePathName(
                        $level->name
                    )
            )
            ->values();

        /*
         * On ne compte que les trois classes actives :
         * Débutant, Intermédiaire et Avancé.
         *
         * Les anciennes classes et les doublons présents
         * dans la base ne sont pas inclus.
         */
        $levels->each(
            function (Level $level) use (
                $classOrder,
                $subject
            ) {
                $validClasses = $level->classes
                    ->sortBy(
                        fn (ClassRoom $classRoom) =>
                            $classOrder[
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

                /*
                 * Préparer les informations nécessaires à l'arbre
                 * affiché directement dans subject-levels.blade.php.
                 */
                $validClasses->each(
                    function (ClassRoom $classRoom) use (
                        $subject,
                        $level
                    ) {
                        $classRoom->setAttribute(
                            'requires_vocal_test',
                            VocalTestPrompt::requiresVocalTest(
                                $subject,
                                $level,
                                $classRoom
                            )
                        );

                        $classRoom->setAttribute(
                            'is_without_vocal_test',
                            VocalTestPrompt::isExcludedPath(
                                $subject,
                                $level,
                                $classRoom
                            )
                        );
                    }
                );

                $level->setRelation(
                    'classes',
                    $validClasses
                );

                $level->setAttribute(
                    'available_classes_count',
                    $validClasses->count()
                );
            }
        );

        /*
         * Cette valeur est utilisée dans la partie supérieure
         * de resources/views/front/subject-levels.blade.php.
         *
         * Résultat :
         * - Arabe : 2 parcours × 3 classes = 6
         * - Coran : 1 parcours × 3 classes = 3
         */
        $subject->setAttribute(
            'classes_count',
            $levels->sum('available_classes_count')
        );

        $subject->setAttribute(
            'validated_levels_count',
            $levels->count()
        );

        $sameFamilySubjects = Subject::query()
            ->where('type', $subject->type)
            ->where('status', 'active')
            ->where('id', '!=', $subject->id)
            ->whereIn('name', ['Arabe', 'Coran'])
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
        $subject = Subject::query()
            ->where('status', 'active')
            ->findOrFail($subjectId);
        $level = Level::whereKey($levelId)
            ->where('subject_id', $subject->id)
            ->where('is_active', true)
            ->firstOrFail();

        abort_unless(
            VocalTestPrompt::isSupportedLevel($subject, $level),
            404,
            'Ce parcours ne fait pas partie de la structure actuelle.'
        );

        $allowedClassNames = VocalTestPrompt::allowedClassNames();
        $classOrder = array_flip(array_map(
            [VocalTestPrompt::class, 'normalizePathName'],
            $allowedClassNames
        ));

        $classes = ClassRoom::where('level_id', $level->id)
            ->where('is_active', true)
            ->whereIn('name', $allowedClassNames)
            ->whereHas(
                'subjects',
                fn($query) => $query->where('subjects.id', $subject->id)
            )
            ->get()
            ->sortBy(function (ClassRoom $class) use ($classOrder) {
                return $classOrder[
                    VocalTestPrompt::normalizePathName($class->name)
                ] ?? 99;
            })
            ->values();

        $classes->each(function (ClassRoom $class) use ($subject, $level) {
            $class->requires_vocal_test = VocalTestPrompt::requiresVocalTest(
                $subject,
                $level,
                $class
            );

            $class->is_without_vocal_test = VocalTestPrompt::isExcludedPath(
                $subject,
                $level,
                $class
            );
        });

        return view(
            'front.level-classes',
            compact('subject', 'level', 'classes')
        );
    }

    public function levelCourses($id)
    {
        $level = Level::query()
            ->where('is_active', true)
            ->with(['courses', 'subject'])
            ->findOrFail($id);

        abort_unless(
            $level->subject
                && ($level->subject->status ?? 'active') === 'active',
            404
        );

        return view('front.level-courses', compact('level'));
    }

    public function showCourse($id)
    {
        $course = Course::with(['learningTests', 'subject', 'level', 'classRoom'])->findOrFail($id);

        abort_unless(
            $course->subject
                && ($course->subject->status ?? 'active') === 'active'
                && (!$course->level || (bool) ($course->level->is_active ?? true))
                && (!$course->classRoom || (bool) ($course->classRoom->is_active ?? true)),
            404
        );

        // Autres matières de la même famille (même type : religieux / scolaire)
        $sameFamilySubjects = collect([]);
        if ($course->subject) {
            $sameFamilySubjects = Subject::where('type', $course->subject->type)
                ->where('status', 'active')
                ->where('id', '!=', $course->subject->id)
                ->withCount('courses')
                ->limit(4)
                ->get();
        }

        return view('front.course-show', compact('course', 'sameFamilySubjects'));
    }

    public function courses($subject_id, $level_id, $class_id)
    {
        $subject = Subject::query()
            ->where('status', 'active')
            ->findOrFail($subject_id);

        $level = Level::whereKey($level_id)
            ->where('subject_id', $subject->id)
            ->where('is_active', true)
            ->firstOrFail();

        $class = ClassRoom::whereKey($class_id)
            ->where('level_id', $level->id)
            ->where('is_active', true)
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


    /**
     * Affiche les classes d'un niveau (navigation publique)
     */
    public function publicClasses(Level $level)
    {
        $level->load('subject');

        abort_unless(
            (bool) ($level->is_active ?? true)
                && $level->subject
                && ($level->subject->status ?? 'active') === 'active',
            404
        );

        $classes = \App\Models\ClassRoom::where('level_id', $level->id)
            ->where('is_active', true)
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
        $level->load('subject');

        abort_unless(
            (bool) ($level->is_active ?? true)
                && (bool) ($class->is_active ?? true)
                && (int) $class->level_id === (int) $level->id
                && $level->subject
                && ($level->subject->status ?? 'active') === 'active',
            404
        );

        $subjects = Subject::whereIn('name', ['Arabe', 'Coran'])
            ->where('status', 'active')
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

        abort_unless(
            ($subject->status ?? 'active') === 'active'
                && (bool) ($level->is_active ?? true)
                && (bool) ($class->is_active ?? true)
                && (int) $class->level_id === (int) $level->id,
            404
        );

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
            ->where('status', 'active')
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
            ->where('status', 'active')
            ->where('name', 'Arabe')
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