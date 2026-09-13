<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\ProfAssignment;
use App\Services\ProfessorPathService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DevoirController extends Controller
{
    private ProfessorPathService $profPaths;

    public function __construct(
        ProfessorPathService $profPaths
    ) {
        $this->profPaths = $profPaths;
    }

    public function index(Request $request)
    {
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

        $pathPairs = $visibleScope
            ->map(fn ($item) => [
                'subject_id' => (int) $item->subject_id,
                'class_id' => (int) $item->class_id,
            ])
            ->unique(
                fn ($item) =>
                    $item['subject_id'] . ':' . $item['class_id']
            )
            ->values();

        $courseId =
            (int) $request->query(
                'course_id',
                0
            );

        $course = null;

        $query = Assignment::query()
            ->with([
                'subject',
                'classRoom.level',
                'classSlot.subject',
                'classSlot.level',
                'classSlot.classRoom',
                'course',
            ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->where(function ($pathQuery) use ($pathPairs) {
                if ($pathPairs->isEmpty()) {
                    $pathQuery->whereRaw('1 = 0');
                    return;
                }

                foreach ($pathPairs as $pair) {
                    $pathQuery->orWhere(
                        function ($pairQuery) use ($pair) {
                            $pairQuery
                                ->where(
                                    'subject_id',
                                    $pair['subject_id']
                                )
                                ->where(
                                    'class_room_id',
                                    $pair['class_id']
                                );
                        }
                    );
                }
            })
            ->when(
                $request->filled('class_slot_id'),
                fn ($query) =>
                    $query->where(
                        'class_slot_id',
                        (int) $request->query(
                            'class_slot_id'
                        )
                    )
            );

        if ($courseId) {
            $course = Course::query()->approved()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->findOrFail($courseId);

            $query->where(
                'course_id',
                $course->id
            );
        }

        $devoirs = $query
            ->latest()
            ->paginate(10)
            ->appends(
                $request->query()
            );

        $profAssignmentsForGroups =
            $this->profPaths->assignments(
                auth()->id()
            );

        $devoirs->getCollection()
            ->each(
                function ($devoir) use (
                    $profAssignmentsForGroups
                ) {
                    $resolved =
                        $devoir->classSlot?->code
                        ?? $devoir->course?->slot_code;

                    if (!$resolved) {
                        $levelId =
                            $devoir
                                ->classRoom
                                ?->level_id
                            ?? $devoir
                                ->course
                                ?->level_id;

                        $matches =
                            $profAssignmentsForGroups
                                ->filter(
                                    fn ($assignment) =>
                                        (int) $assignment
                                            ->subject_id
                                            === (int) $devoir
                                                ->subject_id
                                        && (int) $assignment
                                            ->level_id
                                            === (int) $levelId
                                        && (int) $assignment
                                            ->class_id
                                            === (int) $devoir
                                                ->class_room_id
                                        && $assignment
                                            ->classSlot
                                )
                                ->unique(
                                    'class_slot_id'
                                )
                                ->values();

                        if ($matches->count() === 1) {
                            $resolved =
                                $matches
                                    ->first()
                                    ?->classSlot
                                    ?->code;
                        }
                    }

                    $devoir->resolved_group_code =
                        $resolved;
                }
            );


        $courses = Course::query()->approved()
            ->where(
                'user_id',
                auth()->id()
            )
            ->orderBy('title')
            ->get();

        $filters =
            $this->profPaths
                ->selectedFilters($request);

        return view(
            'prof.devoir.index',
            array_merge(
                compact(
                    'devoirs',
                    'courseId',
                    'course',
                    'courses',
                    'profHierarchy'
                ),
                $filters
            )
        );
    }

    public function create(Request $request)
    {
        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $profAssignments =
            $this->profPaths->assignments(
                auth()->id()
            );

        $courseId =
            (int) $request->query(
                'course_id',
                0
            );

        $course = $courseId
            ? Course::query()->approved()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->findOrFail($courseId)
            : null;

        $defaultAssignment = null;

        if ($course) {
            $defaultAssignment =
                $profAssignments->first(
                    function (
                        ProfAssignment $assignment
                    ) use ($course) {
                        return
                            (int) $assignment->subject_id
                                === (int) $course->subject_id
                            && (int) $assignment->level_id
                                === (int) $course->level_id
                            && (int) $assignment->class_id
                                === (int) $course->class_id;
                    }
                );
        }

        $courses = Course::query()->approved()
            ->where(
                'user_id',
                auth()->id()
            )
            ->orderBy('title')
            ->get();

        $weekSuggestions = [];

        $profAssignments
            ->map(fn ($assignment) => [
                'subject_id' =>
                    (int) $assignment->subject_id,
                'class_id' =>
                    (int) $assignment->class_id,
            ])
            ->unique(
                fn ($item) =>
                    $item['subject_id']
                    . ':'
                    . $item['class_id']
            )
            ->each(
                function ($item) use (&$weekSuggestions) {
                    $key =
                        $item['subject_id']
                        . ':'
                        . $item['class_id'];

                    $weekSuggestions[$key] =
                        $this->resolveCurrentWeekNumber(
                            auth()->id(),
                            $item['subject_id'],
                            $item['class_id']
                        );
                }
            );

        return view(
            'prof.devoir.create',
            [
                'course' => $course,
                'courses' => $courses,
                'courseId' => $courseId ?: null,
                'profHierarchy' =>
                    $profHierarchy,
                'weekSuggestions' =>
                    $weekSuggestions,
                'selectedSubjectId' =>
                    old(
                        'subject_id',
                        $request->query(
                            'subject_id',
                            $defaultAssignment
                                ?->subject_id
                        )
                    ),
                'selectedLevelId' =>
                    old(
                        'level_id',
                        $request->query(
                            'level_id',
                            $defaultAssignment
                                ?->level_id
                        )
                    ),
                'selectedClassId' =>
                    old(
                        'class_id',
                        $request->query(
                            'class_id',
                            $defaultAssignment
                                ?->class_id
                        )
                    ),
                'selectedSlotId' =>
                    old(
                        'class_slot_id',
                        $request->query(
                            'class_slot_id',
                            $defaultAssignment
                                ?->class_slot_id
                        )
                    ),
            ]
        );
    }

    public function store(Request $request)
    {
        abort_unless(
            in_array(
                auth()->user()->role,
                ['admin', 'prof'],
                true
            ),
            403
        );

        $validated = $request->validate([
            'description' => [
                'nullable',
                'string',
            ],
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
            'course_id' => [
                'nullable',
                'integer',
                'exists:courses,id',
            ],
            'file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:5120',
            ],
            'attachments' => [
                'nullable',
                'array',
                'max:10',
            ],
            'attachments.*' => [
                'file',
                'max:102400',
                function ($attribute, $value, $fail) {
                    $extension = mb_strtolower(
                        trim((string) $value->getClientOriginalExtension())
                    );

                    $blocked = [
                        'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
                        'cgi', 'pl', 'py', 'sh', 'bash',
                        'bat', 'cmd', 'com', 'exe', 'msi', 'dll',
                        'ps1', 'vbs', 'scr',
                        'js', 'mjs', 'html', 'htm', 'svg',
                        'jar', 'apk',
                    ];

                    if (
                        $extension !== ''
                        && in_array($extension, $blocked, true)
                    ) {
                        $fail(
                            'Ce type de fichier n’est pas autorisé '
                            . 'pour des raisons de sécurité.'
                        );
                    }
                },
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

        $course = null;

        if (!empty($validated['course_id'])) {
            $course = Course::query()->approved()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->findOrFail(
                    $validated['course_id']
                );

            $courseMatchesPath =
                (int) $course->subject_id
                    === (int) $scope->subject_id
                && (int) $course->level_id
                    === (int) $scope->level_id
                && (int) $course->class_id
                    === (int) $scope->class_id;

            if (!$courseMatchesPath) {
                throw ValidationException::withMessages([
                    'course_id' =>
                        'Le cours sélectionné ne correspond pas '
                        . 'à la classe pédagogique choisie.',
                ]);
            }
        }

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath =
                $request->file('file')
                    ->store(
                        'assignments',
                        'public'
                    );
        }

        $weekNumber =
            $this->resolveCurrentWeekNumber(
                auth()->id(),
                (int) $scope->subject_id,
                (int) $scope->class_id
            );

        $assignmentTitle =
            'SEMAINE ' . $weekNumber;

        $automaticDueDate =
            now()->addDays(5)->toDateString();

        $extraFiles =
            $this->storeExtraFiles(
                $request,
                'assignments/extra'
            );

        Assignment::create([
            'title' =>
                $assignmentTitle,
            'week_number' =>
                $weekNumber,
            'description' =>
                $validated['description']
                ?? null,
            'file' =>
                $filePath,
            'extra_files' =>
                $extraFiles,
            'due_date' =>
                $automaticDueDate,
            'course_id' =>
                $course?->id,
            'subject_id' =>
                $scope->subject_id,
            'class_room_id' =>
                $scope->class_id,
            'class_slot_id' =>
                $scope->class_slot_id,
            'user_id' =>
                auth()->id(),
        ]);

        return redirect()
            ->route(
                'prof.devoir.index',
                [
                    'subject_id' =>
                        $scope->subject_id,
                    'level_id' =>
                        $scope->level_id,
                    'class_id' =>
                        $scope->class_id,
                    'class_slot_id' =>
                        $scope->class_slot_id,
                ]
            )
            ->with(
                'success',
                $assignmentTitle
                . ' créé avec succès. Date limite : '
                . now()->addDays(5)->format('d/m/Y')
                . '.'
            );
    }

    public function edit(Assignment $devoir)
    {
        abort_unless(
            (int) $devoir->user_id
                === (int) auth()->id(),
            403
        );

        abort_unless(
            !$devoir->class_slot_id
            || $this->profPaths->ownsSlot(
                auth()->id(),
                (int) $devoir->class_slot_id
            ),
            403
        );

        $devoir->load([
            'course',
            'classSlot.subject',
            'classSlot.level',
            'classSlot.classRoom',
        ]);

        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $courses = Course::query()->approved()
            ->where(
                'user_id',
                auth()->id()
            )
            ->orderBy('title')
            ->get();

        $weekNumber =
            $this->resolveStoredWeekNumber(
                $devoir
            );

        $automaticDueDate =
            $devoir->due_date
                ? \Carbon\Carbon::parse(
                    $devoir->due_date
                )
                : (
                    $devoir->created_at
                        ? $devoir->created_at
                            ->copy()
                            ->addDays(5)
                        : now()->addDays(5)
                );

        return view(
            'prof.devoir.edit',
            [
                'devoir' => $devoir,
                'weekNumber' =>
                    $weekNumber,
                'automaticDueDate' =>
                    $automaticDueDate,
                'courses' => $courses,
                'profHierarchy' =>
                    $profHierarchy,
                'selectedSubjectId' =>
                    old(
                        'subject_id',
                        $devoir->subject_id
                        ?? $devoir->course?->subject_id
                    ),
                'selectedLevelId' =>
                    old(
                        'level_id',
                        $devoir->course?->level_id
                        ?? ClassRoom::query()
                            ->whereKey($devoir->class_room_id)
                            ->value('level_id')
                    ),
                'selectedClassId' =>
                    old(
                        'class_id',
                        $devoir->class_room_id
                        ?? $devoir->course?->class_id
                    ),
                'selectedSlotId' =>
                    old(
                        'class_slot_id',
                        $devoir->class_slot_id
                    ),
            ]
        );
    }

    public function update(
        Request $request,
        Assignment $devoir
    ) {
        abort_unless(
            (int) $devoir->user_id
                === (int) auth()->id(),
            403
        );

        $validated = $request->validate([
            'description' => [
                'nullable',
                'string',
            ],
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
            'course_id' => [
                'nullable',
                'integer',
                'exists:courses,id',
            ],
            'file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:5120',
            ],
            'attachments' => [
                'nullable',
                'array',
                'max:10',
            ],
            'attachments.*' => [
                'file',
                'max:102400',
                function ($attribute, $value, $fail) {
                    $extension = mb_strtolower(
                        trim((string) $value->getClientOriginalExtension())
                    );

                    $blocked = [
                        'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
                        'cgi', 'pl', 'py', 'sh', 'bash',
                        'bat', 'cmd', 'com', 'exe', 'msi', 'dll',
                        'ps1', 'vbs', 'scr',
                        'js', 'mjs', 'html', 'htm', 'svg',
                        'jar', 'apk',
                    ];

                    if (
                        $extension !== ''
                        && in_array($extension, $blocked, true)
                    ) {
                        $fail(
                            'Ce type de fichier n’est pas autorisé '
                            . 'pour des raisons de sécurité.'
                        );
                    }
                },
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

        abort_unless(
            $scope,
            403,
            'Cette classe ne fait pas partie de vos affectations.'
        );

        $course = null;

        if (!empty($validated['course_id'])) {
            $course = Course::query()->approved()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->findOrFail(
                    (int) $validated['course_id']
                );

            $courseMatchesPath =
                (int) $course->subject_id
                    === (int) $scope->subject_id
                && (int) $course->level_id
                    === (int) $scope->level_id
                && (int) $course->class_id
                    === (int) $scope->class_id;

            if (!$courseMatchesPath) {
                throw ValidationException::withMessages([
                    'course_id' =>
                        'Le cours sélectionné ne correspond pas '
                        . 'à la classe pédagogique choisie.',
                ]);
            }
        }

        if ($request->hasFile('file')) {
            if ($devoir->file) {
                Storage::disk('public')
                    ->delete(
                        $devoir->file
                    );
            }

            $devoir->file =
                $request->file('file')
                    ->store(
                        'assignments',
                        'public'
                    );
        }

        $newExtraFiles =
            $this->storeExtraFiles(
                $request,
                'assignments/extra'
            );

        if (!empty($newExtraFiles)) {
            $devoir->extra_files =
                array_values(
                    array_merge(
                        $devoir->extra_files ?? [],
                        $newExtraFiles
                    )
                );
        }
        $pathChanged =
            (int) $devoir->subject_id
                !== (int) $scope->subject_id
            || (int) $devoir->class_room_id
                !== (int) $scope->class_id
            || (int) $devoir->class_slot_id
                !== (int) $scope->class_slot_id;

        $weekNumber =
            $pathChanged
                ? $this->resolveCurrentWeekNumber(
                    auth()->id(),
                    (int) $scope->subject_id,
                    (int) $scope->class_id,
                    (int) $devoir->id
                )
                : $this->resolveStoredWeekNumber(
                    $devoir
                );

        $assignmentTitle =
            'SEMAINE ' . $weekNumber;

        $devoir->title =
            $assignmentTitle;

        $devoir->week_number =
            $weekNumber;

        $devoir->description =
            $validated['description']
            ?? null;

        /*
         * La date limite est fixée une seule fois à la création
         * (date de création + 5 jours).
         * Une modification du devoir ne prolonge donc pas
         * automatiquement le délai.
         */
        if (!$devoir->due_date) {
            $devoir->due_date =
                ($devoir->created_at
                    ? $devoir->created_at->copy()->addDays(5)
                    : now()->addDays(5)
                )->toDateString();
        }

        $devoir->subject_id =
            $scope->subject_id;

        $devoir->class_room_id =
            $scope->class_id;

        $devoir->class_slot_id =
            $scope->class_slot_id;

        $devoir->course_id =
            $course?->id;

        $devoir->save();

        return redirect()
            ->route(
                'prof.devoir.index',
                [
                    'subject_id' =>
                        $scope->subject_id,
                    'level_id' =>
                        $scope->level_id,
                    'class_id' =>
                        $scope->class_id,
                    'class_slot_id' =>
                        $scope->class_slot_id,
                ]
            )
            ->with(
                'success',
                $assignmentTitle
                . ' mis à jour avec succès. Date limite conservée : '
                . (
                    $devoir->due_date
                        ? \Carbon\Carbon::parse(
                            $devoir->due_date
                        )->format('d/m/Y')
                        : 'non définie'
                )
                . '.'
            );
    }

    /**
     * Numéro pédagogique de la semaine en cours pour une
     * matière + classe données.
     *
     * Deux devoirs créés pendant la même semaine pour le même
     * parcours reçoivent le même numéro de semaine.
     * Une nouvelle semaine reçoit le numéro suivant.
     */
    private function resolveCurrentWeekNumber(
        int $professorId,
        int $subjectId,
        int $classId,
        ?int $ignoreAssignmentId = null
    ): int {
        $startOfWeek =
            now()->copy()->startOfWeek();

        $endOfWeek =
            now()->copy()->endOfWeek();

        $baseQuery = Assignment::query()
            ->where('user_id', $professorId)
            ->where('subject_id', $subjectId)
            ->where('class_room_id', $classId);

        if ($ignoreAssignmentId) {
            $baseQuery->whereKeyNot(
                $ignoreAssignmentId
            );
        }

        $currentWeekNumber =
            (clone $baseQuery)
                ->whereBetween(
                    'created_at',
                    [$startOfWeek, $endOfWeek]
                )
                ->whereNotNull('week_number')
                ->orderByDesc('week_number')
                ->value('week_number');

        if ($currentWeekNumber) {
            return max(
                1,
                (int) $currentWeekNumber
            );
        }

        $maxStoredWeek =
            (clone $baseQuery)
                ->whereNotNull('week_number')
                ->max('week_number');

        if ($maxStoredWeek) {
            return (int) $maxStoredWeek + 1;
        }

        /*
         * Compatibilité avec les anciens devoirs créés avant
         * l'ajout de week_number : on compte les semaines
         * calendaires distinctes déjà utilisées.
         */
        $previousWeekCount =
            (clone $baseQuery)
                ->where(
                    'created_at',
                    '<',
                    $startOfWeek
                )
                ->pluck('created_at')
                ->filter()
                ->map(
                    fn ($date) =>
                        Carbon::parse($date)
                            ->startOfWeek()
                            ->toDateString()
                )
                ->unique()
                ->count();

        return max(
            1,
            $previousWeekCount + 1
        );
    }

    /**
     * Retourne le numéro déjà attribué à un devoir.
     * Pour un ancien devoir sans week_number, le numéro est
     * déduit de son titre ou de sa position chronologique.
     */
    private function resolveStoredWeekNumber(
        Assignment $devoir
    ): int {
        if ((int) $devoir->week_number > 0) {
            return (int) $devoir->week_number;
        }

        if (
            preg_match(
                '/^SEMAINE\s+(\d+)$/iu',
                trim((string) $devoir->title),
                $matches
            )
        ) {
            return max(1, (int) $matches[1]);
        }

        $assignmentDate =
            $devoir->created_at
                ? Carbon::parse($devoir->created_at)
                : now();

        $endOfAssignmentWeek =
            $assignmentDate
                ->copy()
                ->endOfWeek();

        $weekCount = Assignment::query()
            ->where('user_id', $devoir->user_id)
            ->where('subject_id', $devoir->subject_id)
            ->where('class_room_id', $devoir->class_room_id)
            ->where(
                'created_at',
                '<=',
                $endOfAssignmentWeek
            )
            ->pluck('created_at')
            ->filter()
            ->map(
                fn ($date) =>
                    Carbon::parse($date)
                        ->startOfWeek()
                        ->toDateString()
            )
            ->unique()
            ->count();

        return max(1, $weekCount);
    }

    public function destroy(
        Assignment $devoir
    ) {
        abort_unless(
            (int) $devoir->user_id
                === (int) auth()->id(),
            403
        );

        abort_unless(
            !$devoir->class_slot_id
            || $this->profPaths->ownsSlot(
                auth()->id(),
                (int) $devoir->class_slot_id
            ),
            403
        );

        if ($devoir->file) {
            Storage::disk('public')
                ->delete(
                    $devoir->file
                );
        }

        $devoir->delete();

        return redirect()
            ->route('prof.devoir.index')
            ->with(
                'success',
                'Devoir supprimé !'
            );
    }

    private function storeExtraFiles(
        Request $request,
        string $directory
    ): array {
        $stored = [];

        foreach (
            $request->file(
                'attachments',
                []
            )
            as $file
        ) {
            $path = $file->store(
                $directory,
                'local'
            );

            $stored[] = [
                'path' => $path,
                'name' =>
                    $file->getClientOriginalName(),
                'mime' =>
                    $file->getMimeType(),
                'size' =>
                    (int) $file->getSize(),
            ];
        }

        return $stored;
    }

}
