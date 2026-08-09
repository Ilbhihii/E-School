<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassSlot;
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
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Services\LearningPathService;
use App\Services\ClassScheduleDisplayService;
use Carbon\Carbon;
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
        Request $request,
        ClassScheduleDisplayService $scheduleService
    ) {
        $user = auth()->user();

        $assignmentRows = $this->paths
            ->studentAssignmentRows($user->id)
            ->filter(
                fn ($row) =>
                    !empty($row->class_slot_id)
                    && !empty($row->slot_code)
            )
            ->values();

        $subjectsModels = Subject::query()
            ->whereIn('id', $assignmentRows->pluck('subject_id'))
            ->orderBy('name')
            ->get();

        $levelsModels = Level::query()
            ->whereIn('id', $assignmentRows->pluck('level_id'))
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $classesModels = ClassRoom::query()
            ->whereIn('id', $assignmentRows->pluck('class_id'))
            ->orderBy('name')
            ->get();

        $slotsModels = ClassSlot::query()
            ->whereIn(
                'id',
                $assignmentRows->pluck('class_slot_id')->filter()
            )
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('code')
            ->get();

        $subjectsById = $subjectsModels->keyBy('id');
        $levelsById = $levelsModels->keyBy('id');
        $classesById = $classesModels->keyBy('id');
        $slotsById = $slotsModels->keyBy('id');

        $paths = $assignmentRows
            ->map(function ($row) use (
                $subjectsById,
                $levelsById,
                $classesById,
                $slotsById
            ) {
                $subject = $subjectsById->get((int) $row->subject_id);
                $level = $levelsById->get((int) $row->level_id);
                $classRoom = $classesById->get((int) $row->class_id);
                $slot = $slotsById->get((int) $row->class_slot_id);

                if (!$subject || !$level || !$classRoom || !$slot) {
                    return null;
                }

                return (object) [
                    'subject_id' => (int) $subject->id,
                    'level_id' => (int) $level->id,
                    'class_id' => (int) $classRoom->id,
                    'class_slot_id' => (int) $slot->id,
                    'slot_code' => $slot->code,
                    'subject' => $subject,
                    'level' => $level,
                    'classRoom' => $classRoom,
                    'classSlot' => $slot,
                ];
            })
            ->filter()
            ->unique(fn ($path) => implode(':', [
                $path->subject_id,
                $path->level_id,
                $path->class_id,
                $path->class_slot_id,
            ]))
            ->values();

        [
            $selectedSubjectId,
            $selectedLevelId,
            $selectedClassId,
            $selectedSlotId,
        ] = $this->resolveStudentPathFilters($request, $paths);

        $visiblePaths = $this->filterStudentPaths(
            $paths,
            $selectedSubjectId,
            $selectedLevelId,
            $selectedClassId,
            $selectedSlotId
        );

        $visibleSlotIds = $visiblePaths
            ->pluck('class_slot_id')
            ->filter()
            ->unique()
            ->values();

        $selectedSlot = $selectedSlotId
            ? $slotsModels->firstWhere('id', $selectedSlotId)
            : null;

        $scheduleFilters = array_filter([
            'subject_id' => $selectedSubjectId,
            'level_id' => $selectedLevelId,
            'class_id' => $selectedClassId,
            'slot_code' => $selectedSlot?->code,
        ]);

        $lives = Live::query()
            ->with([
                'classRoom.level',
                'classSlot.subject',
                'classSlot.level',
                'classSlot.classRoom',
            ])
            ->when(
                Schema::hasColumn('lives', 'class_slot_id')
                && $visibleSlotIds->isNotEmpty(),
                fn ($query) => $query->whereIn(
                    'class_slot_id',
                    $visibleSlotIds
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->orderByDesc('live_date')
            ->orderByDesc('start_time')
            ->orderByDesc('id')
            ->get();

        /*
         * Le calendrier est chargé pour une large fenêtre afin que les vues
         * semaine / mois puissent être parcourues sans seconde interface.
         */
        $calendarStart = now()->subMonth()->startOfMonth();

        $scheduleOccurrences = $scheduleService->forStudent(
            $user,
            $calendarStart,
            400,
            null,
            $scheduleFilters
        );

        $todayKey = now()->toDateString();

        $todayOccurrences = $scheduleOccurrences
            ->where('date_key', $todayKey)
            ->values();

        $todayLives = $lives
            ->filter(
                fn (Live $live) =>
                    $live->start_date_time
                    && $live->start_date_time->toDateString() === $todayKey
            )
            ->sortBy(
                fn (Live $live) =>
                    $live->start_date_time?->timestamp
                    ?? PHP_INT_MAX
            )
            ->values();

        $pathByScheduleKey = $visiblePaths->keyBy(
            fn ($path) =>
                (int) $path->subject_id
                . ':' . (int) $path->level_id
                . ':' . (int) $path->class_id
                . ':' . strtoupper(trim((string) $path->slot_code))
        );

        $todayOccurrences = $todayOccurrences
            ->map(function (array $occurrence) use (
                $pathByScheduleKey,
                $todayLives
            ) {
                $key =
                    (int) ($occurrence['subject_id'] ?? 0)
                    . ':' . (int) ($occurrence['level_id'] ?? 0)
                    . ':' . (int) ($occurrence['class_id'] ?? 0)
                    . ':' . strtoupper(
                        trim((string) ($occurrence['slot_code'] ?? ''))
                    );

                $path = $pathByScheduleKey->get($key);

                $occurrence['linked_live'] = $path
                    ? $todayLives->first(
                        fn (Live $live) =>
                            (int) $live->class_slot_id
                            === (int) $path->class_slot_id
                    )
                    : null;

                return $occurrence;
            })
            ->values();

        $calendarEvents = $scheduleOccurrences
            ->map(function (array $occurrence) {
                $slotCode = trim(
                    (string) ($occurrence['slot_code'] ?? '')
                );

                return [
                    'id' => 'course-'
                        . $occurrence['schedule_id']
                        . '-' . $occurrence['date_key'],
                    'title' => collect([
                        'Cours',
                        $slotCode ?: null,
                        $occurrence['subject'],
                    ])->filter()->implode(' · '),
                    'start' => $occurrence['start']->toIso8601String(),
                    'end' => $occurrence['end']->toIso8601String(),
                    'allDay' => false,
                    'classNames' => ['student-calendar-course'],
                    'extendedProps' => [
                        'type' => 'course',
                        'path' => $occurrence['path'],
                    ],
                ];
            })
            ->values();

        $liveCalendarEvents = $lives
            ->map(function (Live $live) {
                $start = $live->start_date_time;
                $end = $live->end_date_time;

                if (!$start || !$end) {
                    return null;
                }

                return [
                    'id' => 'live-' . $live->id,
                    'title' => collect([
                        'LIVE',
                        $live->classSlot?->code,
                        $live->title,
                    ])->filter()->implode(' · '),
                    'start' => $start->toIso8601String(),
                    'end' => $end->toIso8601String(),
                    'allDay' => false,
                    'url' => $live->stream_url
                        ? route('live.access.request', $live)
                        : null,
                    'classNames' => [
                        'student-calendar-live',
                        'student-calendar-live-' . $live->schedule_status,
                    ],
                    'extendedProps' => [
                        'type' => 'live',
                    ],
                ];
            })
            ->filter()
            ->values();

        $calendarEvents = $calendarEvents
            ->concat($liveCalendarEvents)
            ->sortBy('start')
            ->values();

        $filterData = $this->studentPathFilterData($paths);

        return view(
            'student.lives',
            array_merge(
                compact(
                    'lives',
                    'paths',
                    'visiblePaths',
                    'todayOccurrences',
                    'todayLives',
                    'calendarEvents'
                ),
                $filterData,
                [
                    'selectedSubjectId' => $selectedSubjectId,
                    'selectedLevelId' => $selectedLevelId,
                    'selectedClassId' => $selectedClassId,
                    'selectedSlotId' => $selectedSlotId,
                    'hasActiveFilter' => (bool) (
                        $selectedSubjectId
                        || $selectedLevelId
                        || $selectedClassId
                        || $selectedSlotId
                    ),
                    'liveNowCount' => $lives
                        ->where('schedule_status', 'live')
                        ->count(),
                    'upcomingCount' => $lives
                        ->where('schedule_status', 'upcoming')
                        ->count(),
                    'todayScheduleCount' => $todayOccurrences->count(),
                ]
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

        if (
            Schema::hasColumn('courses', 'slot_code')
            && trim((string) $course->slot_code) !== ''
        ) {
            $allowedSlot = $this->paths
                ->studentAssignmentRows(auth()->id())
                ->contains(
                    fn ($row) =>
                        (int) $row->subject_id === (int) $course->subject_id
                        && (int) $row->level_id === (int) (
                            $course->level_id
                            ?: $course->classRoom?->level_id
                        )
                        && (int) $row->class_id === (int) $course->class_id
                        && strtoupper(trim((string) ($row->slot_code ?? '')))
                            === strtoupper(trim((string) $course->slot_code))
                );

            abort_unless($allowedSlot, 403);
        }

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

        $assignmentRows = $this->paths
            ->studentAssignmentRows($user->id)
            ->filter(
                fn ($row) =>
                    !empty($row->class_slot_id)
                    && !empty($row->slot_code)
            )
            ->values();

        $subjects = Subject::query()
            ->whereIn('id', $assignmentRows->pluck('subject_id'))
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->whereIn('id', $assignmentRows->pluck('level_id'))
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $classes = ClassRoom::query()
            ->whereIn('id', $assignmentRows->pluck('class_id'))
            ->orderBy('name')
            ->get();

        $slots = ClassSlot::query()
            ->whereIn(
                'id',
                $assignmentRows->pluck('class_slot_id')->filter()
            )
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('code')
            ->get();

        $subjectsById = $subjects->keyBy('id');
        $levelsById = $levels->keyBy('id');
        $classesById = $classes->keyBy('id');
        $slotsById = $slots->keyBy('id');

        $assignmentPaths = $assignmentRows
            ->map(function ($row) use (
                $subjectsById,
                $levelsById,
                $classesById,
                $slotsById
            ) {
                $subject = $subjectsById->get((int) $row->subject_id);
                $level = $levelsById->get((int) $row->level_id);
                $classRoom = $classesById->get((int) $row->class_id);
                $slot = $slotsById->get((int) $row->class_slot_id);

                if (!$subject || !$level || !$classRoom || !$slot) {
                    return null;
                }

                return (object) [
                    'subject_id' => (int) $subject->id,
                    'level_id' => (int) $level->id,
                    'class_id' => (int) $classRoom->id,
                    'class_slot_id' => (int) $slot->id,
                    'slot_code' => $slot->code,
                    'subject' => $subject,
                    'level' => $level,
                    'classRoom' => $classRoom,
                    'classSlot' => $slot,
                ];
            })
            ->filter()
            ->sortBy(fn ($path) => sprintf(
                '%s|%06d|%s|%s|%s',
                mb_strtolower($path->subject->name),
                (int) ($path->level->order ?? 999999),
                mb_strtolower($path->level->name),
                mb_strtolower($path->classRoom->name),
                mb_strtolower($path->slot_code)
            ))
            ->values();

        $filterData = $this->studentPathFilterData($assignmentPaths);

        /*
         * Cette page utilise déjà $subjects comme collection Eloquent
         * pour le formulaire d'envoi. La clé "subjects" générée par
         * studentPathFilterData() est destinée aux filtres des autres
         * pages et ne doit pas écraser cette collection.
         */
        unset($filterData['subjects']);

        $assignments = Assignment::query()
            ->where('user_id', $user->id)
            ->with([
                'subject',
                'course.subject',
                'course.level',
                'course.classRoom',
                'classSlot',
            ])
            ->latest()
            ->get();

        $profAssignments = collect();

        if ($assignmentPaths->isNotEmpty()) {
            $pathPairs = $assignmentPaths
                ->map(fn ($path) => [
                    'subject_id' => $path->subject_id,
                    'class_id' => $path->class_id,
                ])
                ->unique(fn ($pair) =>
                    $pair['subject_id'] . ':' . $pair['class_id']
                )
                ->values();

            $profAssignments = Assignment::query()
                ->whereHas(
                    'user',
                    fn ($query) => $query->where('role', 'prof')
                )
                ->where(function ($query) use ($pathPairs) {
                    foreach ($pathPairs as $pair) {
                        $query->orWhere(function ($pathQuery) use ($pair) {
                            $pathQuery
                                ->where('subject_id', $pair['subject_id'])
                                ->where('class_room_id', $pair['class_id']);
                        });
                    }
                })
                ->with([
                    'user',
                    'subject',
                    'course.subject',
                    'course.level',
                    'course.classRoom',
                    'classSlot',
                ])
                ->latest()
                ->get()
                ->filter(function ($profAssignment) use ($assignmentPaths) {
                    $subjectId = (int) (
                        $profAssignment->subject_id
                        ?: $profAssignment->course?->subject_id
                    );

                    $classId = (int) (
                        $profAssignment->class_room_id
                        ?: $profAssignment->course?->class_id
                    );

                    if (!$subjectId || !$classId) {
                        return false;
                    }

                    $candidatePaths = $assignmentPaths
                        ->where('subject_id', $subjectId)
                        ->where('class_id', $classId)
                        ->values();

                    if ($candidatePaths->isEmpty()) {
                        return false;
                    }

                    $resolved = null;

                    if (!empty($profAssignment->class_slot_id)) {
                        $resolved = $candidatePaths->firstWhere(
                            'class_slot_id',
                            (int) $profAssignment->class_slot_id
                        );
                    }

                    if (!$resolved && !empty($profAssignment->course?->slot_code)) {
                        $courseSlot = strtoupper(trim(
                            (string) $profAssignment->course->slot_code
                        ));

                        $resolved = $candidatePaths->first(
                            fn ($path) =>
                                strtoupper(trim((string) $path->slot_code))
                                === $courseSlot
                        );
                    }

                    /*
                     * Ancien devoir sans créneau : on le montre uniquement
                     * si l'étudiant n'a qu'un seul groupe dans ce parcours.
                     */
                    if (!$resolved && $candidatePaths->count() === 1) {
                        $resolved = $candidatePaths->first();
                    }

                    if (!$resolved) {
                        return false;
                    }

                    $profAssignment->resolved_class_slot_id =
                        (int) $resolved->class_slot_id;
                    $profAssignment->resolved_slot_code =
                        $resolved->slot_code;
                    $profAssignment->resolved_level_name =
                        $resolved->level->name;
                    $profAssignment->resolved_class_name =
                        $resolved->classRoom->name;

                    return true;
                })
                ->values();

            $now = now();

            $profAssignments->each(
                function ($profAssignment) use (
                    $now,
                    $assignments
                ) {
                    $dueDate = $profAssignment->due_date
                        ? \Carbon\Carbon::parse($profAssignment->due_date)
                        : null;

                    $isOverdue = $dueDate && $now->gt($dueDate);

                    $studentSubmission = $assignments->first(
                        function ($submission) use ($profAssignment) {
                            if (
                                !empty($submission->class_slot_id)
                                && !empty($profAssignment->resolved_class_slot_id)
                                && (int) $submission->class_slot_id
                                    !== (int) $profAssignment->resolved_class_slot_id
                            ) {
                                return false;
                            }

                            if (
                                $submission->course_id
                                && $profAssignment->course_id
                                && (int) $submission->course_id
                                    === (int) $profAssignment->course_id
                            ) {
                                return true;
                            }

                            if (
                                (int) $submission->subject_id
                                !== (int) $profAssignment->subject_id
                            ) {
                                return false;
                            }

                            if (
                                $submission->class_room_id
                                && $profAssignment->class_room_id
                                && (int) $submission->class_room_id
                                    !== (int) $profAssignment->class_room_id
                            ) {
                                return false;
                            }

                            $profTitle = trim((string) $profAssignment->title);

                            return $profTitle !== ''
                                && mb_stripos(
                                    (string) $submission->title,
                                    $profTitle
                                ) !== false;
                        }
                    );

                    $profAssignment->has_file = !empty($profAssignment->file);
                    $profAssignment->is_locked =
                        $isOverdue || !$profAssignment->has_file;

                    if ($studentSubmission) {
                        $profAssignment->student_submitted = true;
                        $profAssignment->student_grade = $studentSubmission->grade;
                        $profAssignment->student_grade_status =
                            $studentSubmission->grade === null
                                ? 'en_cours'
                                : (
                                    $studentSubmission->grade >= 10
                                        ? 'acqui'
                                        : 'non_acquis'
                                );
                    } else {
                        $profAssignment->student_submitted = false;
                        $profAssignment->student_grade = null;
                        $profAssignment->student_grade_status = 'non_acquis';
                    }
                }
            );
        }

        return view(
            'student.assignments',
            array_merge(
                compact(
                    'assignments',
                    'subjects',
                    'assignmentPaths',
                    'profAssignments'
                ),
                $filterData
            )
        );
    }


    // envoyer devoir
    public function sendAssignment(Request $request)
    {
        $user = auth()->user();

        $assignmentRows = $this->paths
            ->studentAssignmentRows($user->id)
            ->filter(
                fn ($row) =>
                    !empty($row->class_slot_id)
                    && !empty($row->slot_code)
            )
            ->values();

        if ($assignmentRows->isEmpty()) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Aucun parcours Matière → Niveau → Classe → Créneau n’est assigné à votre compte.'
                );
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'class_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'class_slot_id' => ['required', 'integer', 'exists:class_slots,id'],
            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],
        ]);

        $selectedPath = $assignmentRows->first(
            fn ($row) =>
                (int) $row->subject_id === (int) $validated['subject_id']
                && (int) $row->level_id === (int) $validated['level_id']
                && (int) $row->class_id === (int) $validated['class_id']
                && (int) $row->class_slot_id === (int) $validated['class_slot_id']
        );

        if (!$selectedPath) {
            throw ValidationException::withMessages([
                'class_slot_id' =>
                    'Ce créneau ne fait pas partie de votre parcours pédagogique.',
            ]);
        }

        $slotCode = strtoupper(trim((string) $selectedPath->slot_code));

        $course = Course::query()
            ->where('subject_id', (int) $validated['subject_id'])
            ->where('class_id', (int) $validated['class_id'])
            ->where(function ($query) use ($validated) {
                $query
                    ->where('level_id', (int) $validated['level_id'])
                    ->orWhereNull('level_id');
            })
            ->when(
                Schema::hasColumn('courses', 'slot_code'),
                fn ($query) => $query->whereRaw(
                    'UPPER(TRIM(slot_code)) = ?',
                    [$slotCode]
                )
            )
            ->orderByRaw(
                'CASE WHEN level_id = ? THEN 0 ELSE 1 END',
                [(int) $validated['level_id']]
            )
            ->orderBy('order')
            ->orderBy('id')
            ->first();

        if (!$course) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Aucun cours n’est disponible pour ce créneau ' . $slotCode . '.'
                );
        }

        $file = $request
            ->file('file')
            ->store('assignments', 'public');

        Assignment::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'file' => $file,
            'course_id' => $course->id,
            'subject_id' => (int) $validated['subject_id'],
            'class_room_id' => (int) $validated['class_id'],
            'class_slot_id' => (int) $validated['class_slot_id'],
        ]);

        return back()->with(
            'success',
            'Devoir envoyé avec succès pour le créneau ' . $slotCode . ' !'
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

    public function absences(Request $request)
    {
        $user = auth()->user();

        $assignmentRows = $this->paths
            ->studentAssignmentRows($user->id)
            ->filter(
                fn ($row) =>
                    !empty($row->class_slot_id)
                    && !empty($row->slot_code)
            )
            ->values();

        $subjects = Subject::query()
            ->whereIn('id', $assignmentRows->pluck('subject_id'))
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->whereIn('id', $assignmentRows->pluck('level_id'))
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $classes = ClassRoom::query()
            ->whereIn('id', $assignmentRows->pluck('class_id'))
            ->orderBy('name')
            ->get();

        $slots = ClassSlot::query()
            ->whereIn(
                'id',
                $assignmentRows->pluck('class_slot_id')->filter()
            )
            ->where('is_active', true)
            ->orderBy('position')
            ->get();

        $subjectsById = $subjects->keyBy('id');
        $levelsById = $levels->keyBy('id');
        $classesById = $classes->keyBy('id');
        $slotsById = $slots->keyBy('id');

        $paths = $assignmentRows
            ->map(function ($row) use (
                $subjectsById,
                $levelsById,
                $classesById,
                $slotsById
            ) {
                $subject = $subjectsById->get((int) $row->subject_id);
                $level = $levelsById->get((int) $row->level_id);
                $classRoom = $classesById->get((int) $row->class_id);
                $slot = $slotsById->get((int) $row->class_slot_id);

                if (!$subject || !$level || !$classRoom || !$slot) {
                    return null;
                }

                return (object) [
                    'subject_id' => (int) $subject->id,
                    'level_id' => (int) $level->id,
                    'class_id' => (int) $classRoom->id,
                    'class_slot_id' => (int) $slot->id,
                    'slot_code' => $slot->code,
                    'subject' => $subject,
                    'level' => $level,
                    'classRoom' => $classRoom,
                    'classSlot' => $slot,
                ];
            })
            ->filter()
            ->values();

        [$selectedSubjectId, $selectedLevelId, $selectedClassId, $selectedSlotId] =
            $this->resolveStudentPathFilters($request, $paths);

        $baseQuery = Absence::query()
            ->where('user_id', $user->id)
            ->where('present', false)
            ->with([
                'subject',
                'level',
                'classRoom',
                'classSlot',
            ]);

        $allAbsences = (clone $baseQuery)
            ->latest('date')
            ->get();

        $absences = (clone $baseQuery)
            ->when(
                $selectedSubjectId,
                fn ($query) => $query->where('subject_id', $selectedSubjectId)
            )
            ->when(
                $selectedLevelId,
                fn ($query) => $query->where('level_id', $selectedLevelId)
            )
            ->when(
                $selectedClassId,
                fn ($query) => $query->where('class_id', $selectedClassId)
            )
            ->when(
                $selectedSlotId
                && Schema::hasColumn('absences', 'class_slot_id'),
                fn ($query) => $query->where('class_slot_id', $selectedSlotId)
            )
            ->latest('date')
            ->get();

        $totalAbsences = $allAbsences->count();
        $justifiedCount = 0;

        if ($totalAbsences <= 2) {
            $situation = 'Situation normale';
            $color = 'success';
        } elseif ($totalAbsences <= 4) {
            $situation = 'Avertissement oral';
            $color = 'warning';
        } else {
            $situation = 'Avertissement écrit (message ou appel parents)';
            $color = 'danger';
        }

        $filterData = $this->studentPathFilterData($paths);

        return view(
            'student.absences',
            array_merge(
                compact(
                    'absences',
                    'totalAbsences',
                    'justifiedCount',
                    'situation',
                    'color',
                    'paths'
                ),
                $filterData,
                [
                    'selectedSubjectId' => $selectedSubjectId,
                    'selectedLevelId' => $selectedLevelId,
                    'selectedClassId' => $selectedClassId,
                    'selectedSlotId' => $selectedSlotId,
                    'hasActiveFilter' => (bool) (
                        $selectedSubjectId
                        || $selectedLevelId
                        || $selectedClassId
                        || $selectedSlotId
                    ),
                ]
            )
        );
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
            ->studentAssignmentRows($user->id)
            ->filter(
                fn ($row) =>
                    !empty($row->class_slot_id)
                    && !empty($row->slot_code)
            )
            ->values();

        $subjects = Subject::query()
            ->whereIn('id', $rows->pluck('subject_id'))
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->whereIn('id', $rows->pluck('level_id'))
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $classes = ClassRoom::query()
            ->whereIn('id', $rows->pluck('class_id'))
            ->orderBy('name')
            ->get();

        $slots = ClassSlot::query()
            ->whereIn(
                'id',
                $rows->pluck('class_slot_id')->filter()
            )
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('code')
            ->get();

        $subjectsById = $subjects->keyBy('id');
        $levelsById = $levels->keyBy('id');
        $classesById = $classes->keyBy('id');
        $slotsById = $slots->keyBy('id');

        $assignments = $rows
            ->map(function ($row) use (
                $subjectsById,
                $levelsById,
                $classesById,
                $slotsById
            ) {
                $subject = $subjectsById->get((int) $row->subject_id);
                $level = $levelsById->get((int) $row->level_id);
                $classRoom = $classesById->get((int) $row->class_id);
                $slot = $slotsById->get((int) $row->class_slot_id);

                if (!$subject || !$level || !$classRoom || !$slot) {
                    return null;
                }

                return (object) [
                    'subject_id' => (int) $subject->id,
                    'level_id' => (int) $level->id,
                    'class_id' => (int) $classRoom->id,
                    'class_slot_id' => (int) $slot->id,
                    'slot_code' => $slot->code,
                    'subject' => $subject,
                    'level' => $level,
                    'classRoom' => $classRoom,
                    'classSlot' => $slot,
                ];
            })
            ->filter()
            ->sortBy(fn ($assignment) => sprintf(
                '%s|%06d|%s|%s|%s',
                mb_strtolower($assignment->subject->name),
                (int) ($assignment->level->order ?? 999999),
                mb_strtolower($assignment->level->name),
                mb_strtolower($assignment->classRoom->name),
                mb_strtolower($assignment->slot_code)
            ))
            ->values();

        [$selectedSubjectId, $selectedLevelId, $selectedClassId, $selectedSlotId] =
            $this->resolveStudentPathFilters($request, $assignments);

        $visibleAssignments = $this->filterStudentPaths(
            $assignments,
            $selectedSubjectId,
            $selectedLevelId,
            $selectedClassId,
            $selectedSlotId
        );

        $filterData = $this->studentPathFilterData($assignments);

        return view(
            'student.subjects.index',
            array_merge(
                compact('assignments', 'visibleAssignments', 'subjects'),
                $filterData,
                [
                    'selectedSubjectId' => $selectedSubjectId,
                    'selectedLevelId' => $selectedLevelId,
                    'selectedClassId' => $selectedClassId,
                    'selectedSlotId' => $selectedSlotId,
                    'hasActiveFilter' => (bool) (
                        $selectedSubjectId
                        || $selectedLevelId
                        || $selectedClassId
                        || $selectedSlotId
                    ),
                ]
            )
        );
    }

// ═══ Navigation hiérarchique// ═══ Navigation hiérarchique : Matières → Niveaux → Classes → Cours ═══

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
    ClassRoom $class,
    Request $request
) {
    $slotId = (int) $request->query('class_slot_id');

    $assignment = $this->paths
        ->studentAssignmentRows(auth()->id())
        ->first(
            fn ($row) =>
                (int) $row->subject_id === (int) $subject->id
                && (int) $row->level_id === (int) $level->id
                && (int) $row->class_id === (int) $class->id
                && (int) ($row->class_slot_id ?? 0) === $slotId
        );

    abort_unless($assignment && $slotId, 403);

    $classSlot = ClassSlot::query()
        ->whereKey($slotId)
        ->where('subject_id', $subject->id)
        ->where('level_id', $level->id)
        ->where('class_id', $class->id)
        ->where('is_active', true)
        ->firstOrFail();

    $this->ensureHighSchoolTestApproved(
        $subject,
        $level,
        $class
    );

    $courses = Course::query()
        ->where('subject_id', $subject->id)
        ->where('level_id', $level->id)
        ->where('class_id', $class->id)
        ->when(
            Schema::hasColumn('courses', 'slot_code'),
            fn ($query) => $query->whereRaw(
                'UPPER(TRIM(slot_code)) = ?',
                [strtoupper(trim((string) $classSlot->code))]
            )
        )
        ->withCount('devoirs')
        ->get();

    return view(
        'student.subjects.courses',
        compact(
            'subject',
            'level',
            'class',
            'classSlot',
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

    /**
     * Construit les listes dépendantes communes aux pages étudiant.
     * Chaque entrée suit : Matière → Niveau → Classe → Créneau.
     */
    private function studentPathFilterData($paths): array
    {
        $subjects = $paths
            ->unique('subject_id')
            ->sortBy(fn ($path) => mb_strtolower($path->subject->name))
            ->values()
            ->map(fn ($path) => [
                'id' => $path->subject_id,
                'name' => $path->subject->name,
            ])
            ->all();

        $levelsBySubject = $paths
            ->groupBy('subject_id')
            ->mapWithKeys(function ($subjectPaths, $subjectId) {
                return [
                    (string) $subjectId => $subjectPaths
                        ->unique('level_id')
                        ->sortBy(fn ($path) => sprintf(
                            '%06d|%s',
                            (int) ($path->level->order ?? 999999),
                            mb_strtolower($path->level->name)
                        ))
                        ->values()
                        ->map(fn ($path) => [
                            'id' => $path->level_id,
                            'name' => $path->level->name,
                        ])
                        ->all(),
                ];
            })
            ->all();

        $classesBySubjectLevel = [];
        $slotsByPath = [];

        foreach ($paths as $path) {
            $subjectKey = (string) $path->subject_id;
            $levelKey = (string) $path->level_id;
            $classKey = (string) $path->class_id;

            $classesBySubjectLevel[$subjectKey][$levelKey][$classKey] = [
                'id' => $path->class_id,
                'name' => $path->classRoom->name,
            ];

            $slotsByPath[$subjectKey][$levelKey][$classKey][
                (string) $path->class_slot_id
            ] = [
                'id' => $path->class_slot_id,
                'code' => $path->slot_code,
            ];
        }

        foreach ($classesBySubjectLevel as $subjectId => $levels) {
            foreach ($levels as $levelId => $classes) {
                $classesBySubjectLevel[$subjectId][$levelId] =
                    array_values($classes);
            }
        }

        foreach ($slotsByPath as $subjectId => $levels) {
            foreach ($levels as $levelId => $classes) {
                foreach ($classes as $classId => $slotOptions) {
                    $slotsByPath[$subjectId][$levelId][$classId] =
                        array_values($slotOptions);
                }
            }
        }

        return compact(
            'subjects',
            'levelsBySubject',
            'classesBySubjectLevel',
            'slotsByPath'
        );
    }

    private function resolveStudentPathFilters(
        Request $request,
        $paths
    ): array {
        $subjectId = $request->filled('subject_id')
            ? (int) $request->query('subject_id')
            : null;

        if (
            $subjectId
            && !$paths->contains('subject_id', $subjectId)
        ) {
            $subjectId = null;
        }

        $levelId = $request->filled('level_id')
            ? (int) $request->query('level_id')
            : null;

        if (
            !$subjectId
            || (
                $levelId
                && !$paths
                    ->where('subject_id', $subjectId)
                    ->contains('level_id', $levelId)
            )
        ) {
            $levelId = null;
        }

        $classId = $request->filled('class_id')
            ? (int) $request->query('class_id')
            : null;

        if (
            !$subjectId
            || !$levelId
            || (
                $classId
                && !$paths
                    ->where('subject_id', $subjectId)
                    ->where('level_id', $levelId)
                    ->contains('class_id', $classId)
            )
        ) {
            $classId = null;
        }

        $slotId = $request->filled('class_slot_id')
            ? (int) $request->query('class_slot_id')
            : null;

        if (
            !$subjectId
            || !$levelId
            || !$classId
            || (
                $slotId
                && !$paths
                    ->where('subject_id', $subjectId)
                    ->where('level_id', $levelId)
                    ->where('class_id', $classId)
                    ->contains('class_slot_id', $slotId)
            )
        ) {
            $slotId = null;
        }

        return [
            $subjectId,
            $levelId,
            $classId,
            $slotId,
        ];
    }

    private function filterStudentPaths(
        $paths,
        ?int $subjectId,
        ?int $levelId,
        ?int $classId,
        ?int $slotId
    ) {
        return $paths
            ->when(
                $subjectId,
                fn ($items) => $items->where('subject_id', $subjectId)
            )
            ->when(
                $levelId,
                fn ($items) => $items->where('level_id', $levelId)
            )
            ->when(
                $classId,
                fn ($items) => $items->where('class_id', $classId)
            )
            ->when(
                $slotId,
                fn ($items) => $items->where('class_slot_id', $slotId)
            )
            ->values();
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
