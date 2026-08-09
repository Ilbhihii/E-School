<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Assignment;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Level;
use App\Models\Live;
use App\Models\ProfAssignment;
use App\Models\Subject;
use App\Models\User;
use App\Services\LearningPathService;
use App\Services\ProfessorPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfController extends Controller
{
    private LearningPathService $paths;
    private ProfessorPathService $profPaths;

    public function __construct(
        LearningPathService $paths,
        ProfessorPathService $profPaths
    ) {
        $this->paths = $paths;
        $this->profPaths = $profPaths;
    }

    public function dashboard()
    {
        $profAssignments =
            $this->profPaths->assignments(
                auth()->id()
            );

        $slotIds = $profAssignments
            ->pluck('class_slot_id')
            ->filter()
            ->unique()
            ->values();

        $studentIds =
            $this->profPaths->studentIds(
                auth()->id()
            );

        $coursesQuery = Course::query()->approved()
            ->where('user_id', auth()->id());

        $devoirsQuery = Assignment::query()
            ->where('user_id', auth()->id());

        if ($slotIds->isNotEmpty()) {
            $devoirsQuery->whereIn(
                'class_slot_id',
                $slotIds
            );
        }

        $submissionsQuery = Assignment::query()
            ->whereIn('user_id', $studentIds)
            ->when(
                $slotIds->isNotEmpty(),
                fn ($query) =>
                    $query->whereIn(
                        'class_slot_id',
                        $slotIds
                    ),
                fn ($query) =>
                    $query->whereRaw('1 = 0')
            );

        $attendanceQuery = Absence::query()
            ->whereIn('user_id', $studentIds)
            ->when(
                $slotIds->isNotEmpty(),
                fn ($query) =>
                    $query->whereIn(
                        'class_slot_id',
                        $slotIds
                    ),
                fn ($query) =>
                    $query->whereRaw('1 = 0')
            );

        $studentsCount = $studentIds->count();
        $coursesCount = (clone $coursesQuery)->count();
        $myDevoirsCount = (clone $devoirsQuery)->count();
        $assignmentsCount =
            (clone $submissionsQuery)->count();

        $correctedCount =
            (clone $submissionsQuery)
                ->whereNotNull('grade')
                ->count();

        $pendingCount = max(
            $assignmentsCount - $correctedCount,
            0
        );

        $correctionRate =
            $assignmentsCount > 0
                ? round(
                    (
                        $correctedCount
                        / $assignmentsCount
                    ) * 100
                )
                : 0;

        $averageGrade = (float) (
            (clone $submissionsQuery)
                ->whereNotNull('grade')
                ->avg('grade')
            ?? 0
        );

        $absencesCount =
            (clone $attendanceQuery)
                ->where('present', false)
                ->count();

        $attendanceCount =
            (clone $attendanceQuery)->count();

        $presenceRate =
            $attendanceCount > 0
                ? round(
                    (
                        (clone $attendanceQuery)
                            ->where('present', true)
                            ->count()
                        / $attendanceCount
                    ) * 100
                )
                : 100;

        $livesCount = Live::query()
            ->when(
                $slotIds->isNotEmpty(),
                fn ($query) =>
                    $query->whereIn(
                        'class_slot_id',
                        $slotIds
                    ),
                fn ($query) =>
                    $query->whereRaw('1 = 0')
            )
            ->count();

        $recentSubmissions =
            (clone $submissionsQuery)
                ->with([
                    'user',
                    'subject',
                    'classSlot',
                ])
                ->latest()
                ->take(5)
                ->get();

        return view(
            'prof.dashboard',
            compact(
                'studentsCount',
                'coursesCount',
                'assignmentsCount',
                'myDevoirsCount',
                'correctedCount',
                'pendingCount',
                'correctionRate',
                'averageGrade',
                'absencesCount',
                'presenceRate',
                'livesCount',
                'profAssignments',
                'recentSubmissions'
            )
        );
    }

    /**
     * Copies des étudiants.
     * Structure :
     * Matière → Niveau → Classe → Créneau.
     */
    public function assignments(
        Request $request
    ) {
        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $visibleScope =
            $this->profPaths
                ->filteredAssignments(
                    auth()->id(),
                    $request
                );

        $slotIds = $visibleScope
            ->pluck('class_slot_id')
            ->filter()
            ->unique()
            ->values();

        $studentIds = $visibleScope
            ->flatMap(
                fn (ProfAssignment $assignment) =>
                    $this->profPaths
                        ->studentIdsForAssignment(
                            $assignment
                        )
            )
            ->unique()
            ->values();

        $assignments = Assignment::query()
            ->with([
                'user',
                'subject',
                'classSlot.subject',
                'classSlot.level',
                'classSlot.classRoom',
            ])
            ->whereIn('user_id', $studentIds)
            ->when(
                $slotIds->isNotEmpty(),
                fn ($query) =>
                    $query->whereIn(
                        'class_slot_id',
                        $slotIds
                    ),
                fn ($query) =>
                    $query->whereRaw('1 = 0')
            )
            ->latest()
            ->get();

        $filters =
            $this->profPaths
                ->selectedFilters($request);

        return view(
            'prof.assignments',
            array_merge(
                compact(
                    'assignments',
                    'profHierarchy'
                ),
                $filters
            )
        );
    }

    public function grade(Request $request)
    {
        $request->validate([
            'id' =>
                'required|integer|exists:assignments,id',
            'status' =>
                'required|in:acquis,en_cours,non_acquis',
            'comment' =>
                'nullable|string|max:2000',
        ]);

        $assignment = Assignment::query()
            ->with([
                'user',
                'classSlot',
            ])
            ->whereKey($request->id)
            ->whereHas(
                'user',
                fn ($query) =>
                    $query->where(
                        'role',
                        User::ROLE_STUDENT
                    )
            )
            ->firstOrFail();

        abort_unless(
            $assignment->class_slot_id
            && $this->profPaths->ownsSlot(
                auth()->id(),
                (int) $assignment->class_slot_id
            ),
            403
        );

        $scope = ProfAssignment::query()
            ->where('prof_id', auth()->id())
            ->where(
                'class_slot_id',
                $assignment->class_slot_id
            )
            ->firstOrFail();

        abort_unless(
            $this->profPaths
                ->studentBelongsToSlot(
                    (int) $assignment->user_id,
                    $scope
                ),
            403
        );

        $assignment->grade =
            match ($request->status) {
                'acquis' => 20,
                'en_cours' => 10,
                default => 0,
            };

        $assignment->comment =
            $request->comment ?: '';

        $assignment->save();

        $label = match ($assignment->grade) {
            20 => 'Acquis',
            10 => 'En cours d\'acquisition',
            default => 'Non acquis',
        };

        return back()->with(
            'success',
            "Devoir corrigé : {$label}"
        );
    }

    public function absences()
    {
        $profAssignments =
            $this->profPaths->assignments(
                auth()->id()
            );

        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        return view(
            'prof.absences',
            compact(
                'profAssignments',
                'profHierarchy'
            )
        );
    }

    public function absencesList(
        Request $request
    ) {
        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $visibleScope =
            $this->profPaths
                ->filteredAssignments(
                    auth()->id(),
                    $request
                );

        $slotIds = $visibleScope
            ->pluck('class_slot_id')
            ->filter()
            ->unique()
            ->values();

        $studentIds = $visibleScope
            ->flatMap(
                fn (ProfAssignment $assignment) =>
                    $this->profPaths
                        ->studentIdsForAssignment(
                            $assignment
                        )
            )
            ->unique()
            ->values();

        $query = Absence::query()
            ->with([
                'user',
                'subject',
                'level',
                'classRoom',
                'classSlot',
            ])
            ->whereIn('user_id', $studentIds)
            ->when(
                $slotIds->isNotEmpty(),
                fn ($query) =>
                    $query->whereIn(
                        'class_slot_id',
                        $slotIds
                    ),
                fn ($query) =>
                    $query->whereRaw('1 = 0')
            );

        $allowedSorts = [
            'date',
            'created_at',
            'present',
        ];

        if (
            $request->filled('sort')
            && in_array(
                $request->sort,
                $allowedSorts,
                true
            )
        ) {
            $query->orderBy(
                $request->sort,
                $request->query('dir') === 'asc'
                    ? 'asc'
                    : 'desc'
            );
        } else {
            $query
                ->orderByDesc('date')
                ->orderByDesc('created_at');
        }

        $absences = $query
            ->paginate(15)
            ->appends(
                $request->query()
            );

        $filters =
            $this->profPaths
                ->selectedFilters($request);

        return view(
            'prof.absences-list',
            array_merge(
                compact(
                    'absences',
                    'profHierarchy'
                ),
                $filters
            )
        );
    }

    public function updateAbsence(
        Request $request,
        $id
    ) {
        $request->validate([
            'present' => 'required|boolean',
        ]);

        $absence = Absence::query()
            ->findOrFail($id);

        abort_unless(
            $absence->class_slot_id
            && $this->profPaths->ownsSlot(
                auth()->id(),
                (int) $absence->class_slot_id
            ),
            403
        );

        $absence->present =
            (int) $request->present;

        $absence->save();

        return back()->with(
            'success',
            'Statut de présence mis à jour.'
        );
    }

    /**
     * Étudiants du créneau exact.
     */
    public function getStudents(
        Request $request,
        $id
    ) {
        $validated = $request->validate([
            'subject_id' =>
                ['required', 'integer'],
            'level_id' =>
                ['required', 'integer'],
            'class_slot_id' =>
                ['required', 'integer'],
        ]);

        $scope =
            $this->profPaths
                ->findExactAssignment(
                    auth()->id(),
                    (int) $validated['subject_id'],
                    (int) $validated['level_id'],
                    (int) $id,
                    (int) $validated['class_slot_id']
                );

        abort_unless($scope, 403);

        $studentIds =
            $this->profPaths
                ->studentIdsForAssignment(
                    $scope
                );

        $students = User::query()
            ->where(
                'role',
                User::ROLE_STUDENT
            )
            ->whereIn('id', $studentIds)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return response()->json(
            $students
        );
    }

    public function storeAbsence(
        Request $request
    ) {
        $validated = $request->validate([
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'level_id' => [
                'required',
                'integer',
                'exists:levels,id',
            ],
            'class_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],
            'class_slot_id' => [
                'required',
                'integer',
                'exists:class_slots,id',
            ],
            'date' => [
                'required',
                'date',
            ],
            'students' => [
                'required',
                'array',
            ],
            'students.*' => [
                'required',
                'boolean',
            ],
        ]);

        $scope =
            $this->profPaths
                ->findExactAssignment(
                    auth()->id(),
                    (int) $validated['subject_id'],
                    (int) $validated['level_id'],
                    (int) $validated['class_id'],
                    (int) $validated['class_slot_id']
                );

        abort_unless($scope, 403);

        $allowedStudentIds =
            $this->profPaths
                ->studentIdsForAssignment(
                    $scope
                );

        $alertStudents = [];

        foreach (
            $validated['students']
            as $studentId => $status
        ) {
            abort_unless(
                $allowedStudentIds->contains(
                    (int) $studentId
                ),
                403
            );

            Absence::query()->updateOrCreate(
                [
                    'user_id' =>
                        (int) $studentId,
                    'subject_id' =>
                        (int) $validated[
                            'subject_id'
                        ],
                    'level_id' =>
                        (int) $validated[
                            'level_id'
                        ],
                    'class_id' =>
                        (int) $validated[
                            'class_id'
                        ],
                    'class_slot_id' =>
                        (int) $validated[
                            'class_slot_id'
                        ],
                    'date' =>
                        $validated['date'],
                ],
                [
                    'present' =>
                        (bool) $status,
                ]
            );

            $absenceCount = Absence::query()
                ->where(
                    'user_id',
                    $studentId
                )
                ->where(
                    'class_slot_id',
                    $validated[
                        'class_slot_id'
                    ]
                )
                ->where(
                    'present',
                    false
                )
                ->whereDate(
                    'date',
                    '<=',
                    $validated['date']
                )
                ->count();

            if ($absenceCount >= 3) {
                $alertStudents[] =
                    (int) $studentId;
            }
        }

        if (!empty($alertStudents)) {
            session()->flash(
                'alert',
                'Certains étudiants ont atteint '
                . 'ou dépassé 3 absences '
                . 'dans ce créneau.'
            );
        }

        return back()->with(
            'success',
            'Présences enregistrées pour '
            . ($scope->classSlot?->code ?? 'le créneau')
            . '.'
        );
    }

    public function updateProfile(
        Request $request
    ) {
        $user = auth()->user();

        $request->validate([
            'name' =>
                'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')
                    ->ignore($user->id),
            ],
        ]);

        $user->update(
            $request->only(
                'name',
                'email'
            )
        );

        return back()->with(
            'success',
            'Profil mis à jour avec succès !'
        );
    }

    public function updatePassword(
        Request $request
    ) {
        $request->validate([
            'current_password' =>
                'required|current_password',
            'password' =>
                'required|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' =>
                Hash::make(
                    $request->password
                ),
        ]);

        return back()->with(
            'success',
            'Mot de passe mis à jour avec succès !'
        );
    }

    public function browseLives(
        Level $level,
        ClassRoom $class
    ) {
        $scope = ProfAssignment::query()
            ->where('prof_id', auth()->id())
            ->where('level_id', $level->id)
            ->where('class_id', $class->id)
            ->get();

        abort_if($scope->isEmpty(), 403);

        $slotIds = $scope
            ->pluck('class_slot_id')
            ->filter()
            ->values();

        $lives = Live::query()
            ->whereIn(
                'class_slot_id',
                $slotIds
            )
            ->latest()
            ->get();

        return view(
            'prof.lives.browse',
            compact(
                'level',
                'class',
                'lives'
            )
        );
    }

    public function browseDevoirs(
        Level $level,
        ClassRoom $class,
        Subject $subject
    ) {
        $scope = ProfAssignment::query()
            ->with('classSlot')
            ->where('prof_id', auth()->id())
            ->where('subject_id', $subject->id)
            ->where('level_id', $level->id)
            ->where('class_id', $class->id)
            ->get();

        abort_if($scope->isEmpty(), 403);

        $slotCodes = $scope
            ->pluck('classSlot.code')
            ->filter()
            ->values();

        $courses = Course::query()->approved()
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
            ->whereIn(
                'slot_code',
                $slotCodes
            )
            ->where(
                'user_id',
                auth()->id()
            )
            ->with('devoirs')
            ->get();

        return view(
            'prof.devoir.browse',
            compact(
                'level',
                'class',
                'subject',
                'courses'
            )
        );
    }

    /**
     * /prof/lives
     * Matière → Niveau → Classe → Créneau.
     */
    public function livesIndex(
        Request $request
    ) {
        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $visibleScope =
            $this->profPaths
                ->filteredAssignments(
                    auth()->id(),
                    $request
                );

        $slotIds = $visibleScope
            ->pluck('class_slot_id')
            ->filter()
            ->unique()
            ->values();

        $query = Live::query()
            ->with([
                'classRoom.level',
                'classSlot.subject',
                'classSlot.level',
                'classSlot.classRoom',
            ])
            ->when(
                $slotIds->isNotEmpty(),
                fn ($query) =>
                    $query->whereIn(
                        'class_slot_id',
                        $slotIds
                    ),
                fn ($query) =>
                    $query->whereRaw('1 = 0')
            );

        $totalLives =
            (clone $query)->count();

        $upcomingLives =
            (clone $query)
                ->where(function ($query) {
                    $query
                        ->whereDate(
                            'live_date',
                            '>=',
                            now()->toDateString()
                        )
                        ->orWhereNull(
                            'live_date'
                        );
                })
                ->count();

        $recentLives =
            (clone $query)
                ->latest()
                ->limit(5)
                ->get();

        $lives = $query
            ->orderByDesc('live_date')
            ->orderByDesc('start_time')
            ->paginate(15)
            ->appends(
                $request->query()
            );

        $filters =
            $this->profPaths
                ->selectedFilters($request);

        return view(
            'prof.lives.index',
            array_merge(
                compact(
                    'lives',
                    'totalLives',
                    'recentLives',
                    'upcomingLives',
                    'profHierarchy'
                ),
                $filters
            )
        );
    }

    // ═══ Matières → Niveaux → Classes → Créneaux ═══

    public function subjectsList()
    {
        $scope =
            $this->profPaths->assignments(
                auth()->id()
            );

        $subjects = Subject::query()
            ->whereIn(
                'id',
                $scope->pluck(
                    'subject_id'
                )
            )
            ->orderBy('name')
            ->get();

        $subjects->each(
            function (
                Subject $subject
            ) use ($scope) {
                $subjectScope =
                    $scope->where(
                        'subject_id',
                        $subject->id
                    );

                $subject->assigned_levels_count =
                    $subjectScope
                        ->pluck('level_id')
                        ->unique()
                        ->count();

                $subject->assigned_classes_count =
                    $subjectScope
                        ->pluck('class_id')
                        ->unique()
                        ->count();

                $subject->assigned_slots_count =
                    $subjectScope
                        ->pluck('class_slot_id')
                        ->filter()
                        ->unique()
                        ->count();
            }
        );

        return view(
            'prof.subjects.index',
            compact('subjects')
        );
    }

    public function subjectLevels(
        Subject $subject
    ) {
        $scope =
            $this->profPaths->assignments(
                auth()->id()
            )
            ->where(
                'subject_id',
                $subject->id
            );

        abort_if(
            $scope->isEmpty(),
            403
        );

        $levels = Level::query()
            ->whereIn(
                'id',
                $scope->pluck(
                    'level_id'
                )
            )
            ->orderBy('name')
            ->get();

        $levels->each(
            function (
                Level $level
            ) use ($scope) {
                $levelScope =
                    $scope->where(
                        'level_id',
                        $level->id
                    );

                $level->assigned_classes_count =
                    $levelScope
                        ->pluck('class_id')
                        ->unique()
                        ->count();

                $level->assigned_slots_count =
                    $levelScope
                        ->pluck('class_slot_id')
                        ->filter()
                        ->unique()
                        ->count();
            }
        );

        return view(
            'prof.subjects.levels',
            compact(
                'subject',
                'levels'
            )
        );
    }

    public function subjectClasses(
        Subject $subject,
        Level $level
    ) {
        abort_unless(
            (int) $level->subject_id
                === (int) $subject->id,
            404
        );

        $scope =
            $this->profPaths->assignments(
                auth()->id()
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'level_id',
                $level->id
            );

        abort_if(
            $scope->isEmpty(),
            403
        );

        $classes = ClassRoom::query()
            ->whereIn(
                'id',
                $scope->pluck(
                    'class_id'
                )
            )
            ->orderBy('name')
            ->get();

        $classes->each(
            function (
                ClassRoom $class
            ) use ($scope) {
                $class->assignedSlots =
                    $scope
                        ->where(
                            'class_id',
                            $class->id
                        )
                        ->pluck(
                            'classSlot'
                        )
                        ->filter()
                        ->sortBy(
                            'position'
                        )
                        ->unique(
                            'id'
                        )
                        ->values();
            }
        );

        return view(
            'prof.subjects.classes',
            compact(
                'subject',
                'level',
                'classes'
            )
        );
    }

    public function subjectCourses(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        $scope =
            $this->scopeForClass(
                $subject,
                $level,
                $class
            );

        $selectedSlot =
            $this->selectedSlotFromScope(
                $request,
                $scope
            );

        $slotCodes = $selectedSlot
            ? collect([
                $selectedSlot->code,
            ])
            : $scope
                ->pluck('classSlot.code')
                ->filter()
                ->unique()
                ->values();

        $courses = Course::query()->approved()
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
            ->whereIn(
                'slot_code',
                $slotCodes
            )
            ->where(
                'user_id',
                auth()->id()
            )
            ->with([
                'classRoom',
                'subject',
                'level',
            ])
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        return view(
            'prof.subjects.courses',
            compact(
                'subject',
                'level',
                'class',
                'courses',
                'selectedSlot',
                'scope'
            )
        );
    }

    public function subjectLives(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        $scope =
            $this->scopeForClass(
                $subject,
                $level,
                $class
            );

        $selectedSlot =
            $this->selectedSlotFromScope(
                $request,
                $scope
            );

        $slotIds = $selectedSlot
            ? collect([
                $selectedSlot->id,
            ])
            : $scope
                ->pluck('class_slot_id')
                ->filter()
                ->unique()
                ->values();

        $lives = Live::query()
            ->with('classSlot')
            ->whereIn(
                'class_slot_id',
                $slotIds
            )
            ->latest()
            ->get();

        return view(
            'prof.subjects.lives',
            compact(
                'subject',
                'level',
                'class',
                'lives',
                'selectedSlot',
                'scope'
            )
        );
    }

    public function subjectDevoirs(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        $scope =
            $this->scopeForClass(
                $subject,
                $level,
                $class
            );

        $selectedSlot =
            $this->selectedSlotFromScope(
                $request,
                $scope
            );

        $slotIds = $selectedSlot
            ? collect([
                $selectedSlot->id,
            ])
            : $scope
                ->pluck('class_slot_id')
                ->filter()
                ->unique()
                ->values();

        $devoirs = Assignment::query()
            ->with([
                'subject',
                'classSlot',
                'course',
            ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->whereIn(
                'class_slot_id',
                $slotIds
            )
            ->latest()
            ->get();

        return view(
            'prof.subjects.devoirs',
            compact(
                'subject',
                'level',
                'class',
                'devoirs',
                'selectedSlot',
                'scope'
            )
        );
    }

    private function scopeForClass(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        abort_unless(
            (int) $level->subject_id
                === (int) $subject->id
            && (int) $class->level_id
                === (int) $level->id,
            404
        );

        $scope =
            $this->profPaths
                ->assignments(
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
                ->values();

        abort_if(
            $scope->isEmpty(),
            403
        );

        return $scope;
    }

    private function selectedSlotFromScope(
        Request $request,
        $scope
    ) {
        $slotId =
            (int) $request->query(
                'class_slot_id',
                0
            );

        if (!$slotId) {
            return null;
        }

        $assignment =
            $scope->first(
                fn (
                    ProfAssignment $assignment
                ) =>
                    (int) $assignment
                        ->class_slot_id
                    === $slotId
            );

        abort_unless(
            $assignment
            && $assignment->classSlot,
            403
        );

        return $assignment->classSlot;
    }

    private function assignedClasses()
    {
        $ids =
            $this->profPaths
                ->assignments(
                    auth()->id()
                )
                ->pluck('class_id')
                ->unique();

        return ClassRoom::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get();
    }

    private function assignedStudentIds()
    {
        return $this->profPaths
            ->studentIds(
                auth()->id()
            );
    }

    private function authorizeTeachingScope(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ): void {
        abort_if(
            $this->scopeForClass(
                $subject,
                $level,
                $class
            )->isEmpty(),
            403
        );
    }
}
