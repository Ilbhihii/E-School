<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Live;
use App\Models\Absence;
use App\Models\Level;
use App\Models\Result;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Services\LearningPathService;
use App\Models\HighSchoolTestSubmission;
use App\Models\VocalTestPrompt;

class StudentController extends Controller
{
    private LearningPathService $paths;

    public function __construct(LearningPathService $paths)
    {
        $this->paths = $paths;
    }

    // dashboard étudiant
    public function dashboard()
    {
        $user = auth()->user();

        // Seuls les étudiants inactifs sont redirigés
        if ($user->role === 'student' && !$user->is_active) {
            return redirect()->route('student.waiting');
        }
        $assignmentRows = $this->paths->studentAssignmentRows($user->id);
        $classIds = $assignmentRows->pluck('class_id')->unique()->values();
        $subjectIds = $assignmentRows->pluck('subject_id')->unique()->values();

        $classRoom = $classIds->isNotEmpty()
            ? ClassRoom::with('level')->find($classIds->first())
            : null;
        $assignedSubject = $subjectIds->isNotEmpty()
            ? Subject::find($subjectIds->first())
            : null;
        $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();

        $courseQuery = Course::query();
        if ($assignmentRows->isEmpty()) {
            $courseQuery->whereRaw('1 = 0');
        } else {
            $courseQuery->where(function ($query) use ($assignmentRows) {
                foreach ($assignmentRows as $row) {
                    $query->orWhere(function ($pair) use ($row) {
                        $pair->where('subject_id', $row->subject_id)
                            ->where('level_id', $row->level_id)
                            ->where('class_id', $row->class_id);
                    });
                }
            });
        }
        $coursesCount = (clone $courseQuery)->count();
        $recentCourses = (clone $courseQuery)->latest()->take(4)->get();
        $recentCourses2 = (clone $courseQuery)->latest()->take(2)->get();
        $livesCount = $classIds->isNotEmpty()
            ? Live::whereIn('class_id', $classIds)->count()
            : 0;

        $profAssignments = Assignment::query()
            ->when($classRoom, fn($query) => $query->where('class_room_id', $classRoom->id))
            ->when($assignedSubject, fn($query) => $query->where('subject_id', $assignedSubject->id))
            ->whereHas('user', fn($query) => $query->where('role', 'prof'));
        $totalAssignments = $classRoom && $assignedSubject ? $profAssignments->count() : 0;

        $assignmentsQuery = Assignment::where('user_id', $user->id)
            ->when($assignedSubject, fn($query) => $query->where('subject_id', $assignedSubject->id));
        $assignments = (clone $assignmentsQuery)->latest()->get();
        $assignmentsSent = $assignments->count();
        $assignmentsCorrected = $assignments->whereNotNull('grade')->count();
        $assignmentCompletion = $totalAssignments > 0
            ? min(100, round(($assignmentsSent / $totalAssignments) * 100))
            : ($assignmentsSent > 0 ? 100 : 0);
        $sentPercent = $assignmentCompletion;
        $correctedPercent = $assignmentsSent > 0
            ? round(($assignmentsCorrected / $assignmentsSent) * 100)
            : 0;

        $attendanceRecords = Absence::where('user_id', $user->id)->get();
        $totalSessions = $attendanceRecords->count();
        $totalAbsences = $attendanceRecords->where('present', false)->count();
        $presencePercent = $totalSessions > 0
            ? round(($attendanceRecords->where('present', true)->count() / $totalSessions) * 100)
            : 100;

        $average = $assignments->whereNotNull('grade')->avg('grade') ?? 0;
        $grades = $assignments->whereNotNull('grade')->pluck('grade')->values();
        $engagement = round(($assignmentCompletion + $correctedPercent + $presencePercent) / 3);

        $recentAssignments = $assignments->take(2);
        $absences = $attendanceRecords->where('present', false)->sortByDesc('date')->take(3)->values();
        if($totalAbsences <= 2){
            $situation = "Situation normale";
        }
        elseif($totalAbsences <= 4){
            $situation = "Avertissement oral";
        }
        else{
            $situation = "Avertissement écrit (message ou appel parents)";
        }

        return view('student.dashboard', compact(
            'coursesCount',
            'livesCount', 
            'recentCourses',
            'recentAssignments',
            'absences',
            'situation',
            'totalAbsences',
            'assignmentsSent',
            'assignmentsCorrected',
            'assignmentCompletion',
            'totalAssignments',
            'average',
            'grades',
            'assignments',
            'sentPercent',
            'correctedPercent',
            'presencePercent',
            'engagement',
            'subjects',
            'classRoom',
            'assignedSubject',
            'recentCourses2'
        ));

    }


    public function lives(
        Request $request
    ) {
        $user = auth()->user();

        /*
         * Conserver les assignations class_user ainsi que
         * l'éventuelle classe principale historique.
         */
        $assignmentRows = $this->paths
            ->studentAssignmentRows($user->id);

        $assignedClassIds = $assignmentRows
            ->pluck('class_id')
            ->push($user->class_id)
            ->filter()
            ->map(
                function ($classId) {
                    return (int) $classId;
                }
            )
            ->unique()
            ->values();

        $assignedClasses = ClassRoom::query()
            ->whereIn('id', $assignedClassIds)
            ->with('level')
            ->orderBy('name')
            ->get()
            ->filter(
                function ($classRoom) {
                    return $classRoom->level !== null;
                }
            )
            ->values();

        $assignedLevels = Level::query()
            ->whereIn(
                'id',
                $assignedClasses
                    ->pluck('level_id')
                    ->unique()
            )
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        /*
         * Sans level_id :
         * afficher tous les lives des classes assignées.
         */
        $requestedLevelId = $request->query(
            'level_id'
        );

        $selectedLevel = null;

        if (
            $requestedLevelId !== null
            && $requestedLevelId !== ''
        ) {
            $selectedLevel = $assignedLevels
                ->firstWhere(
                    'id',
                    (int) $requestedLevelId
                );
        }

        $classesForSelectedLevel = collect();
        $selectedClass = null;

        if ($selectedLevel) {
            $classesForSelectedLevel =
                $assignedClasses
                    ->filter(
                        function ($classRoom) use (
                            $selectedLevel
                        ) {
                            return
                                (int) $classRoom->level_id
                                === (int) $selectedLevel->id;
                        }
                    )
                    ->sortBy('name')
                    ->values();

            $requestedClassId = $request->query(
                'class_id'
            );

            if (
                $requestedClassId !== null
                && $requestedClassId !== ''
            ) {
                $selectedClass =
                    $classesForSelectedLevel
                        ->firstWhere(
                            'id',
                            (int) $requestedClassId
                        );
            }

            /*
             * Si seul le niveau est choisi, sélectionner
             * automatiquement la première classe assignée.
             */
            if (!$selectedClass) {
                $selectedClass =
                    $classesForSelectedLevel
                        ->first();
            }
        }

        if ($selectedClass) {
            $visibleClassIds = collect([
                (int) $selectedClass->id,
            ]);
        } elseif ($selectedLevel) {
            $visibleClassIds =
                $classesForSelectedLevel
                    ->pluck('id')
                    ->map(
                        function ($classId) {
                            return (int) $classId;
                        }
                    )
                    ->values();
        } else {
            $visibleClassIds =
                $assignedClasses
                    ->pluck('id')
                    ->map(
                        function ($classId) {
                            return (int) $classId;
                        }
                    )
                    ->values();
        }

        $lives = Live::query()
            ->with([
                'classRoom.level',
            ])
            ->when(
                $visibleClassIds->isNotEmpty(),
                function ($query) use (
                    $visibleClassIds
                ) {
                    $query->whereIn(
                        'class_id',
                        $visibleClassIds
                    );
                },
                function ($query) {
                    $query->whereRaw('1 = 0');
                }
            )
            ->orderByDesc('live_date')
            ->orderByDesc('start_time')
            ->orderByDesc('id')
            ->get();

        /*
         * Données utilisées par JavaScript pour remplir
         * automatiquement les classes du niveau choisi.
         */
        $classOptionsByLevel = $assignedLevels
            ->mapWithKeys(
                function ($level) use (
                    $assignedClasses
                ) {
                    $options = $assignedClasses
                        ->filter(
                            function (
                                $classRoom
                            ) use ($level) {
                                return
                                    (int) $classRoom->level_id
                                    === (int) $level->id;
                            }
                        )
                        ->sortBy('name')
                        ->values()
                        ->map(
                            function ($classRoom) {
                                return [
                                    'id' =>
                                        (int) $classRoom->id,
                                    'name' =>
                                        $classRoom->name,
                                ];
                            }
                        )
                        ->all();

                    return [
                        (string) $level->id =>
                            $options,
                    ];
                }
            )
            ->all();

        $hasActiveFilter =
            $selectedLevel !== null;

        $visibleClassCount =
            $visibleClassIds->count();

        return view(
            'student.lives',
            compact(
                'lives',
                'assignedLevels',
                'assignedClasses',
                'selectedLevel',
                'selectedClass',
                'classesForSelectedLevel',
                'classOptionsByLevel',
                'hasActiveFilter',
                'visibleClassCount'
            )
        );
    }


    public function classCourses($id)
    {
        $user = auth()->user();
        $rows = $this->paths->studentAssignmentRows($user->id)
            ->where('class_id', (int) $id);
        abort_if($rows->isEmpty(), 403);

        $class = ClassRoom::with('level')->findOrFail($id);
        $courses = Course::query()
            ->where('class_id', $class->id)
            ->whereIn('subject_id', $rows->pluck('subject_id'))
            ->get();

        return view('student.class.courses', compact('class', 'courses'));
    }


    public function coursesBySubject($classId, $subjectId)
    {
        $class = ClassRoom::with('level')->findOrFail($classId);
        abort_unless(
            $class->level
            && $this->paths->studentCanAccessPath(
                auth()->user(),
                (int) $subjectId,
                (int) $class->level_id,
                (int) $class->id
            ),
            403
        );

        $courses = Course::where('class_id', $class->id)
            ->where('subject_id', $subjectId)
            ->where('level_id', $class->level_id)
            ->get();

        return view('student.class.courses', compact('courses', 'class'));
    }


    public function showCourse($id)
    {
        $course = Course::with([
                'subject',
                'level',
                'classRoom.level',
                'devoirs',
            ])
            ->findOrFail($id);

        abort_unless(
            $this->paths->userCanAccessCourse(
                auth()->user(),
                $course
            ),
            403
        );

        $courseLevel =
            $course->level
            ?? $course->classRoom?->level;

        abort_unless(
            $course->subject
            && $courseLevel
            && $course->classRoom,
            404
        );

        $this->ensureHighSchoolTestApproved(
            $course->subject,
            $courseLevel,
            $course->classRoom
        );

        $resourceUrls = [];

        foreach (
            ['video', 'pdf', 'link']
            as $type
        ) {
            $exists = $type === 'video'
                ? (
                    $course->video
                    || $course->video_url
                )
                : (
                    $type === 'pdf'
                        ? $course->pdf
                        : $course->course_link
                );

            if ($exists) {
                $resourceUrls[$type] =
                    \URL::temporarySignedRoute(
                        'course.resource',
                        now()->addMinutes(10),
                        [
                            'course' =>
                                $course->id,
                            'type' => $type,
                        ]
                    );
            }
        }

        return view(
            'student.class.course-show',
            compact(
                'course',
                'resourceUrls'
            )
        );
    }


    // page devoirs étudiant
    public function assignments()
    {
        $user = auth()->user();

        /*
         * Parcours exacts assignés à l'étudiant :
         * Matière → Niveau → Classe.
         */
        $assignmentRows = $this->paths
            ->studentAssignmentRows($user->id);

        $subjects = Subject::query()
            ->whereIn(
                'id',
                $assignmentRows->pluck('subject_id')
            )
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->whereIn(
                'id',
                $assignmentRows->pluck('level_id')
            )
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $classes = ClassRoom::query()
            ->whereIn(
                'id',
                $assignmentRows->pluck('class_id')
            )
            ->with('level')
            ->orderBy('name')
            ->get();

        $subjectsById = $subjects->keyBy('id');
        $levelsById = $levels->keyBy('id');
        $classesById = $classes->keyBy('id');

        $assignmentPaths = $assignmentRows
            ->map(
                function ($row) use (
                    $subjectsById,
                    $levelsById,
                    $classesById
                ) {
                    $subject = $subjectsById->get(
                        (int) $row->subject_id
                    );

                    $level = $levelsById->get(
                        (int) $row->level_id
                    );

                    $classRoom = $classesById->get(
                        (int) $row->class_id
                    );

                    if (
                        !$subject
                        || !$level
                        || !$classRoom
                    ) {
                        return null;
                    }

                    return (object) [
                        'subject_id' =>
                            (int) $subject->id,
                        'level_id' =>
                            (int) $level->id,
                        'class_id' =>
                            (int) $classRoom->id,
                        'subject' => $subject,
                        'level' => $level,
                        'classRoom' => $classRoom,
                    ];
                }
            )
            ->filter()
            ->sortBy(
                function ($path) {
                    return sprintf(
                        '%s|%06d|%s|%s',
                        mb_strtolower(
                            $path->subject->name
                        ),
                        (int) (
                            $path->level->order
                            ?? 999999
                        ),
                        mb_strtolower(
                            $path->level->name
                        ),
                        mb_strtolower(
                            $path->classRoom->name
                        )
                    );
                }
            )
            ->values();

        /*
         * Données des listes dépendantes du formulaire :
         * matière → niveaux → classes.
         */
        $levelsBySubject = $assignmentPaths
            ->groupBy('subject_id')
            ->mapWithKeys(
                function (
                    $subjectPaths,
                    $subjectId
                ) {
                    $options = $subjectPaths
                        ->unique('level_id')
                        ->sortBy(
                            function ($path) {
                                return sprintf(
                                    '%06d|%s',
                                    (int) (
                                        $path->level->order
                                        ?? 999999
                                    ),
                                    mb_strtolower(
                                        $path->level->name
                                    )
                                );
                            }
                        )
                        ->values()
                        ->map(
                            function ($path) {
                                return [
                                    'id' =>
                                        (int) $path->level_id,
                                    'name' =>
                                        $path->level->name,
                                ];
                            }
                        )
                        ->all();

                    return [
                        (string) $subjectId =>
                            $options,
                    ];
                }
            )
            ->all();

        $classesBySubjectLevel = $assignmentPaths
            ->groupBy('subject_id')
            ->mapWithKeys(
                function (
                    $subjectPaths,
                    $subjectId
                ) {
                    $levels = $subjectPaths
                        ->groupBy('level_id')
                        ->mapWithKeys(
                            function (
                                $levelPaths,
                                $levelId
                            ) {
                                $options = $levelPaths
                                    ->unique('class_id')
                                    ->sortBy(
                                        fn ($path) =>
                                            mb_strtolower(
                                                $path
                                                    ->classRoom
                                                    ->name
                                            )
                                    )
                                    ->values()
                                    ->map(
                                        function ($path) {
                                            return [
                                                'id' =>
                                                    (int) $path
                                                        ->class_id,
                                                'name' =>
                                                    $path
                                                        ->classRoom
                                                        ->name,
                                            ];
                                        }
                                    )
                                    ->all();

                                return [
                                    (string) $levelId =>
                                        $options,
                                ];
                            }
                        )
                        ->all();

                    return [
                        (string) $subjectId =>
                            $levels,
                    ];
                }
            )
            ->all();

        $assignments = Assignment::query()
            ->where('user_id', $user->id)
            ->with([
                'subject',
                'course.subject',
            ])
            ->latest()
            ->get();

        /*
         * Afficher les devoirs des professeurs pour tous les
         * parcours assignés à l'étudiant, et non uniquement
         * sa première classe.
         */
        $profAssignments = collect();

        if ($assignmentRows->isNotEmpty()) {
            $profAssignments = Assignment::query()
                ->whereHas(
                    'user',
                    fn ($query) =>
                        $query->where('role', 'prof')
                )
                ->where(
                    function ($query) use (
                        $assignmentRows
                    ) {
                        foreach (
                            $assignmentRows
                            as $row
                        ) {
                            $query->orWhere(
                                function ($pathQuery) use (
                                    $row
                                ) {
                                    $pathQuery
                                        ->where(
                                            'subject_id',
                                            $row->subject_id
                                        )
                                        ->where(
                                            'class_room_id',
                                            $row->class_id
                                        );
                                }
                            );
                        }
                    }
                )
                ->with([
                    'user',
                    'subject',
                    'course',
                ])
                ->latest()
                ->get();

            $now = now();

            $profAssignments->each(
                function ($profAssignment) use (
                    $now,
                    $assignments
                ) {
                    $dueDate =
                        $profAssignment->due_date
                            ? \Carbon\Carbon::parse(
                                $profAssignment->due_date
                            )
                            : null;

                    $isOverdue =
                        $dueDate
                        && $now->gt($dueDate);

                    $studentSubmission =
                        $assignments->first(
                            function (
                                $submission
                            ) use (
                                $profAssignment
                            ) {
                                if (
                                    $submission->course_id
                                    && $profAssignment->course_id
                                    && (int) $submission
                                        ->course_id
                                        === (int) $profAssignment
                                            ->course_id
                                ) {
                                    return true;
                                }

                                if (
                                    (int) $submission
                                        ->subject_id
                                    !== (int) $profAssignment
                                        ->subject_id
                                ) {
                                    return false;
                                }

                                if (
                                    $submission->class_room_id
                                    && $profAssignment
                                        ->class_room_id
                                    && (int) $submission
                                        ->class_room_id
                                        !== (int) $profAssignment
                                            ->class_room_id
                                ) {
                                    return false;
                                }

                                $profTitle = trim(
                                    (string) $profAssignment
                                        ->title
                                );

                                if ($profTitle === '') {
                                    return false;
                                }

                                return mb_stripos(
                                    (string) $submission
                                        ->title,
                                    $profTitle
                                ) !== false;
                            }
                        );

                    $profAssignment->has_file =
                        !empty(
                            $profAssignment->file
                        );

                    $profAssignment->is_locked =
                        $isOverdue
                        || !$profAssignment->has_file;

                    if ($studentSubmission) {
                        $profAssignment
                            ->student_submitted = true;

                        $profAssignment
                            ->student_grade =
                                $studentSubmission
                                    ->grade;

                        if (
                            $studentSubmission
                                ->grade !== null
                        ) {
                            $profAssignment
                                ->student_grade_status =
                                    $studentSubmission
                                        ->grade >= 10
                                            ? 'acqui'
                                            : 'non_acquis';
                        } else {
                            $profAssignment
                                ->student_grade_status =
                                    'en_cours';
                        }
                    } else {
                        $profAssignment
                            ->student_submitted = false;

                        $profAssignment
                            ->student_grade = null;

                        $profAssignment
                            ->student_grade_status =
                                'non_acquis';
                    }
                }
            );
        }

        return view(
            'student.assignments',
            compact(
                'assignments',
                'subjects',
                'assignmentPaths',
                'levelsBySubject',
                'classesBySubjectLevel',
                'profAssignments'
            )
        );
    }


    // envoyer devoir
    public function sendAssignment(Request $request)
    {
        $user = auth()->user();

        $assignmentRows = $this->paths
            ->studentAssignmentRows($user->id);

        if ($assignmentRows->isEmpty()) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Aucun parcours pédagogique n’est assigné à votre compte.'
                );
        }

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'subject_id' => [
                'nullable',
                'integer',
                'exists:subjects,id',
            ],
            'level_id' => [
                'nullable',
                'integer',
                'exists:levels,id',
            ],
            'class_id' => [
                'nullable',
                'integer',
                'exists:class_rooms,id',
            ],
            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],
        ]);

        /*
         * Le serveur complète automatiquement une valeur
         * lorsqu’un seul choix est possible.
         */
        $subjectId = isset(
            $validated['subject_id']
        )
            ? (int) $validated['subject_id']
            : null;

        $availableSubjectIds = $assignmentRows
            ->pluck('subject_id')
            ->map(
                fn ($id) => (int) $id
            )
            ->unique()
            ->values();

        if (!$subjectId) {
            if ($availableSubjectIds->count() === 1) {
                $subjectId =
                    (int) $availableSubjectIds->first();
            } else {
                throw ValidationException::withMessages([
                    'subject_id' =>
                        'Veuillez choisir une matière.',
                ]);
            }
        }

        $subjectRows = $assignmentRows
            ->filter(
                fn ($row) =>
                    (int) $row->subject_id
                    === $subjectId
            )
            ->values();

        if ($subjectRows->isEmpty()) {
            throw ValidationException::withMessages([
                'subject_id' =>
                    'Cette matière ne vous est pas assignée.',
            ]);
        }

        $levelId = isset(
            $validated['level_id']
        )
            ? (int) $validated['level_id']
            : null;

        $availableLevelIds = $subjectRows
            ->pluck('level_id')
            ->map(
                fn ($id) => (int) $id
            )
            ->unique()
            ->values();

        if (!$levelId) {
            if ($availableLevelIds->count() === 1) {
                $levelId =
                    (int) $availableLevelIds->first();
            } else {
                throw ValidationException::withMessages([
                    'level_id' =>
                        'Veuillez choisir un niveau.',
                ]);
            }
        }

        $levelRows = $subjectRows
            ->filter(
                fn ($row) =>
                    (int) $row->level_id
                    === $levelId
            )
            ->values();

        if ($levelRows->isEmpty()) {
            throw ValidationException::withMessages([
                'level_id' =>
                    'Ce niveau ne correspond pas à la matière sélectionnée.',
            ]);
        }

        $classId = isset(
            $validated['class_id']
        )
            ? (int) $validated['class_id']
            : null;

        $availableClassIds = $levelRows
            ->pluck('class_id')
            ->map(
                fn ($id) => (int) $id
            )
            ->unique()
            ->values();

        if (!$classId) {
            if ($availableClassIds->count() === 1) {
                $classId =
                    (int) $availableClassIds->first();
            } else {
                throw ValidationException::withMessages([
                    'class_id' =>
                        'Veuillez choisir une classe.',
                ]);
            }
        }

        $selectedPath = $levelRows->first(
            fn ($row) =>
                (int) $row->class_id
                === $classId
        );

        if (!$selectedPath) {
            throw ValidationException::withMessages([
                'class_id' =>
                    'Cette classe ne fait pas partie du parcours sélectionné.',
            ]);
        }

        /*
         * La classe détermine déjà son niveau. On accepte
         * également les anciens cours dont level_id est NULL.
         */
        $course = Course::query()
            ->where('subject_id', $subjectId)
            ->where('class_id', $classId)
            ->where(
                function ($query) use ($levelId) {
                    $query
                        ->where(
                            'level_id',
                            $levelId
                        )
                        ->orWhereNull('level_id');
                }
            )
            ->orderByRaw(
                'CASE WHEN level_id = ? THEN 0 ELSE 1 END',
                [$levelId]
            )
            ->orderBy('order')
            ->orderBy('id')
            ->first();

        if (!$course) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Aucun cours n’est disponible pour la matière, le niveau et la classe sélectionnés.'
                );
        }

        $file = $request
            ->file('file')
            ->store(
                'assignments',
                'public'
            );

        Assignment::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'file' => $file,
            'course_id' => $course->id,
            'subject_id' => $subjectId,
            'class_room_id' => $classId,
        ]);

        return back()->with(
            'success',
            'Devoir envoyé avec succès !'
        );
    }




    public function profile()
    {
        $user = auth()->user();

        $assignmentRows = $this->paths
            ->studentAssignmentRows($user->id);

        $subjectIds = $assignmentRows
            ->pluck('subject_id')
            ->filter()
            ->unique()
            ->values();

        $levelIds = $assignmentRows
            ->pluck('level_id')
            ->filter()
            ->unique()
            ->values();

        $classIds = $assignmentRows
            ->pluck('class_id')
            ->filter()
            ->unique()
            ->values();

        $subjectsById = Subject::query()
            ->whereIn('id', $subjectIds)
            ->get()
            ->keyBy('id');

        $levelsById = Level::query()
            ->whereIn('id', $levelIds)
            ->get()
            ->keyBy('id');

        $classesById = ClassRoom::query()
            ->whereIn('id', $classIds)
            ->get()
            ->keyBy('id');

        $learningPaths = $assignmentRows
            ->map(
                function ($row) use (
                    $subjectsById,
                    $levelsById,
                    $classesById
                ) {
                    $subject = $subjectsById->get(
                        (int) $row->subject_id
                    );

                    $level = $levelsById->get(
                        (int) $row->level_id
                    );

                    $classRoom = $classesById->get(
                        (int) $row->class_id
                    );

                    if (!$subject || !$level || !$classRoom) {
                        return null;
                    }

                    return [
                        'subject' => $subject->name,
                        'level' => $level->name,
                        'class' => $classRoom->name,
                    ];
                }
            )
            ->filter()
            ->unique(
                fn ($path) =>
                    $path['subject']
                    . '|'
                    . $path['level']
                    . '|'
                    . $path['class']
            )
            ->values();

        $courseQuery = Course::query();

        if ($assignmentRows->isEmpty()) {
            $courseQuery->whereRaw('1 = 0');
        } else {
            $courseQuery->where(
                function ($query) use ($assignmentRows) {
                    foreach ($assignmentRows as $row) {
                        $query->orWhere(
                            function ($pair) use ($row) {
                                $pair
                                    ->where(
                                        'subject_id',
                                        $row->subject_id
                                    )
                                    ->where(
                                        'level_id',
                                        $row->level_id
                                    )
                                    ->where(
                                        'class_id',
                                        $row->class_id
                                    );
                            }
                        );
                    }
                }
            );
        }

        $studentAssignments = Assignment::query()
            ->where('user_id', $user->id)
            ->get();

        $gradedAssignments = $studentAssignments
            ->whereNotNull('grade');

        $coursesCount = $courseQuery->count();
        $subjectsCount = $subjectIds->count();
        $assignmentsSent = $studentAssignments->count();
        $average = $gradedAssignments->isNotEmpty()
            ? round((float) $gradedAssignments->avg('grade'), 1)
            : 0;

        return view(
            'student.profile',
            compact(
                'learningPaths',
                'coursesCount',
                'subjectsCount',
                'assignmentsSent',
                'average'
            )
        );
    }

    public function settings()
    {
        return view('student.settings');
    }

    public function absences()
    {
        $absences = Absence::where('user_id', auth()->id())
                    ->where('present', false)
                    ->latest()
                    ->get();

        $totalAbsences = $absences->count();

        // La colonne 'justified' n'existe pas dans la table
        $justifiedCount = 0;

        // situation étudiant
        if ($totalAbsences <= 2) {
            $situation = "Situation normale";
            $color = "success";
        } 
        elseif ($totalAbsences <= 4) {
            $situation = "Avertissement oral";
            $color = "warning";
        } 
        else {
            $situation = "Avertissement écrit (message ou appel parents)";
            $color = "danger";
        }

        return view('student.absences', compact(
            'absences',
            'totalAbsences',
            'justifiedCount',
            'situation',
            'color'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
            'remove_profile_photo' => [
                'nullable',
                'boolean',
            ],
        ]);

        if (
            $request->boolean('remove_profile_photo')
            && $user->profile_photo
        ) {
            Storage::disk('public')->delete(
                $user->profile_photo
            );

            $user->profile_photo = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete(
                    $user->profile_photo
                );
            }

            $user->profile_photo = $request
                ->file('profile_photo')
                ->store('profiles', 'public');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return back()->with(
            'success',
            'Profil et photo mis à jour avec succès !'
        );
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);
        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }

    public function indexSubjects(
        Request $request
    ) {
        $user = auth()->user();

        $rows = $this->paths
            ->studentAssignmentRows($user->id);

        $assignedLevels = Level::query()
            ->whereIn(
                'id',
                $rows->pluck('level_id')
            )
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $assignedClasses = ClassRoom::query()
            ->whereIn(
                'id',
                $rows->pluck('class_id')
            )
            ->with('level')
            ->orderBy('name')
            ->get();

        $assignedSubjects = Subject::query()
            ->whereIn(
                'id',
                $rows->pluck('subject_id')
            )
            ->orderBy('name')
            ->get();

        $levelsById =
            $assignedLevels->keyBy('id');

        $classesById =
            $assignedClasses->keyBy('id');

        $subjectsById =
            $assignedSubjects->keyBy('id');

        /*
         * Une carte correspond à un parcours exact :
         * matière + niveau + classe.
         *
         * Cela évite de mélanger les matières de plusieurs
         * niveaux lorsque l'étudiant possède plusieurs
         * assignations.
         */
        $assignments = $rows
            ->map(
                function ($row) use (
                    $levelsById,
                    $classesById,
                    $subjectsById
                ) {
                    $level = $levelsById->get(
                        (int) $row->level_id
                    );

                    $classRoom = $classesById->get(
                        (int) $row->class_id
                    );

                    $subject = $subjectsById->get(
                        (int) $row->subject_id
                    );

                    if (
                        !$level
                        || !$classRoom
                        || !$subject
                    ) {
                        return null;
                    }

                    return (object) [
                        'subject_id' =>
                            (int) $subject->id,
                        'level_id' =>
                            (int) $level->id,
                        'class_id' =>
                            (int) $classRoom->id,
                        'subject' => $subject,
                        'level' => $level,
                        'classRoom' => $classRoom,
                    ];
                }
            )
            ->filter()
            ->sortBy(
                function ($assignment) {
                    return sprintf(
                        '%06d|%s|%s|%s',
                        (int) (
                            $assignment
                                ->level
                                ->order
                            ?? 999999
                        ),
                        mb_strtolower(
                            $assignment->level->name
                        ),
                        mb_strtolower(
                            $assignment
                                ->classRoom
                                ->name
                        ),
                        mb_strtolower(
                            $assignment
                                ->subject
                                ->name
                        )
                    );
                }
            )
            ->values();

        /*
         * Le filtre est facultatif.
         * Sans level_id, toutes les assignations sont
         * affichées.
         */
        $requestedLevelId = $request->query(
            'level_id'
        );

        $selectedLevel = null;

        if (
            $requestedLevelId !== null
            && $requestedLevelId !== ''
        ) {
            $selectedLevel =
                $assignedLevels->firstWhere(
                    'id',
                    (int) $requestedLevelId
                );
        }

        $classesForSelectedLevel = collect();
        $selectedClass = null;

        if ($selectedLevel) {
            $classesForSelectedLevel =
                $assignedClasses
                    ->filter(
                        function ($classRoom) use (
                            $selectedLevel
                        ) {
                            return
                                (int) $classRoom
                                    ->level_id
                                === (int) $selectedLevel
                                    ->id;
                        }
                    )
                    ->sortBy('name')
                    ->values();

            $requestedClassId =
                $request->query('class_id');

            if (
                $requestedClassId !== null
                && $requestedClassId !== ''
            ) {
                $selectedClass =
                    $classesForSelectedLevel
                        ->firstWhere(
                            'id',
                            (int) $requestedClassId
                        );
            }

            /*
             * Lorsqu'un niveau est choisi sans classe,
             * sélectionner automatiquement la première
             * classe assignée dans ce niveau.
             */
            if (!$selectedClass) {
                $selectedClass =
                    $classesForSelectedLevel
                        ->first();
            }
        }

        $visibleAssignments = $assignments;

        if ($selectedLevel) {
            $visibleAssignments =
                $visibleAssignments->where(
                    'level_id',
                    (int) $selectedLevel->id
                );
        }

        if ($selectedClass) {
            $visibleAssignments =
                $visibleAssignments->where(
                    'class_id',
                    (int) $selectedClass->id
                );
        }

        $visibleAssignments =
            $visibleAssignments->values();

        /*
         * Données utilisées par JavaScript pour remplir
         * automatiquement le sélecteur des classes.
         */
        $classOptionsByLevel =
            $assignedLevels
                ->mapWithKeys(
                    function ($level) use (
                        $assignedClasses
                    ) {
                        $options =
                            $assignedClasses
                                ->filter(
                                    function (
                                        $classRoom
                                    ) use ($level) {
                                        return
                                            (int) $classRoom
                                                ->level_id
                                            === (int) $level
                                                ->id;
                                    }
                                )
                                ->sortBy('name')
                                ->values()
                                ->map(
                                    function (
                                        $classRoom
                                    ) {
                                        return [
                                            'id' =>
                                                (int) $classRoom
                                                    ->id,
                                            'name' =>
                                                $classRoom
                                                    ->name,
                                        ];
                                    }
                                )
                                ->all();

                        return [
                            (string) $level->id =>
                                $options,
                        ];
                    }
                )
                ->all();

        $hasActiveFilter =
            $selectedLevel !== null;

        $visibleSubjectCount =
            $visibleAssignments
                ->pluck('subject_id')
                ->unique()
                ->count();

        return view(
            'student.subjects.index',
            compact(
                'assignedLevels',
                'assignedClasses',
                'assignments',
                'visibleAssignments',
                'selectedLevel',
                'selectedClass',
                'classesForSelectedLevel',
                'classOptionsByLevel',
                'hasActiveFilter',
                'visibleSubjectCount'
            )
        );
    }

// ═══ Navigation hiérarchique : Matières → Niveaux → Classes → Cours ═══

public function subjectLevels(Subject $subject)
{
    $hierarchy = collect($this->paths->hierarchyForStudent(auth()->id()));
    $node = $hierarchy->firstWhere('id', $subject->id);
    abort_unless($node, 403);

    $levels = Level::whereIn('id', collect($node['levels'])->pluck('id'))->orderBy('name')->get();
    return view('student.subjects.levels', compact('subject', 'levels'));
}

public function subjectClasses(Subject $subject, Level $level)
{
    $rows = $this->paths->studentAssignmentRows(auth()->id())
        ->where('subject_id', $subject->id)
        ->where('level_id', $level->id);
    abort_if($rows->isEmpty(), 403);

    $classes = ClassRoom::whereIn('id', $rows->pluck('class_id'))->orderBy('name')->get();
    return view('student.subjects.classes', compact('subject', 'level', 'classes'));
}

public function subjectCourses(
    Subject $subject,
    Level $level,
    ClassRoom $class
) {
    abort_unless(
        $this->paths->studentCanAccessPath(
            auth()->user(),
            $subject->id,
            $level->id,
            $class->id
        ),
        403
    );

    $this->ensureHighSchoolTestApproved(
        $subject,
        $level,
        $class
    );

    $courses = Course::where(
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
        ->withCount('devoirs')
        ->get();

    return view(
        'student.subjects.courses',
        compact(
            'subject',
            'level',
            'class',
            'courses'
        )
    );
}

    public function waiting()
    {
        $user = auth()->user();
        
        // Get the latest test result for this user
        $latestResult = Result::where('user_id', $user->id)
            ->with('test')
            ->latest()
            ->first();
        
        $score = $latestResult?->score ?? 0;
        $total = $latestResult?->total_questions ?? 0;
        $percentage = $latestResult?->percentage ?? 0;
        $testTitle = $latestResult?->test?->title ?? null;
        
        return view('student.waiting', compact(
            'latestResult', 'score', 'total', 'percentage', 'testTitle'
        ));
    }

public function levels()
{
    $user = auth()->user();
    
    // Si l'étudiant a déjà une classe assignée, rediriger vers les matières
    if ($user->classRoom && $user->classRoom->level) {
        return redirect()->route('student.subjects.index')
            ->with('success', 'Vous êtes déjà assigné à la classe ' . $user->classRoom->name . '. Choisissez une matière.');
    }
    
    // Sans classe assignée → ne montre pas tous les niveaux, redirige vers dashboard
    return redirect()->route('student.dashboard')
        ->with('warning', 'Vous n\'avez pas encore de classe assignée. Veuillez contacter l\'administration.');
}

public function levelClasses(Level $level)
{
    return redirect()->route('student.subjects.index')
        ->with('info', 'Utilisez la navigation par matières pour accéder à vos cours.');
}

public function levelSubjects(Level $level, ClassRoom $class)
{
    return redirect()->route('student.subjects.index')
        ->with('info', 'Utilisez la navigation par matières pour accéder à vos cours.');
}

public function subjects(Level $level)
{
    return redirect()->route('student.subjects.index')
        ->with('info', 'Utilisez la navigation par matières pour accéder à vos cours.');
}

public function classes(Subject $subject, Level $level)
{
    return redirect()->route('student.subjects.classes', [$subject, $level]);
}

public function courses(Subject $subject, ClassRoom $class)
{
    $level = $class->level;
    
    if (!$level) {
        return redirect()->route('student.subjects.index')
            ->with('error', 'Niveau non trouvé pour cette classe.');
    }

    abort_unless(
        $this->paths->studentCanAccessPath(auth()->user(), $subject->id, $level->id, $class->id),
        403
    );

    return redirect()->route('student.subjects.courses', [$subject, $level, $class]);
}

    private function ensureHighSchoolTestApproved(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ): void {
        $isHighSchoolSupport =
            VocalTestPrompt::normalizePathName(
                $subject->name
            ) === 'soutien lycee';

        if (!$isHighSchoolSupport) {
            return;
        }

        $approved =
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
                ->where(
                    'status',
                    HighSchoolTestSubmission
                        ::STATUS_APPROVED
                )
                ->exists();

        abort_unless(
            $approved,
            403,
            'Votre test écrit doit être validé '
            . 'avant l’accès aux cours.'
        );
    }

    public function index(Request $request)
    {
        $classId = $request->query('class') ?? $request->query('1') ?? null;
        $subjectId = $request->query('subject') ?? $request->query('2') ?? null;
        
        if ($classId) {
            return redirect()->route('student.class.courses', $classId);
        }

        $user = auth()->user();

        $classes = ClassRoom::where('id', $user->class_id)
                    ->withCount('courses')
                    ->get();

        return view('student.courses', compact('classes'));
    }


}
