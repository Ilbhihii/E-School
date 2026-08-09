<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\ProfAssignment;
use App\Services\ProfessorPathService;
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

        $slotIds = $visibleScope
            ->pluck('class_slot_id')
            ->filter()
            ->unique()
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
                'classSlot.subject',
                'classSlot.level',
                'classSlot.classRoom',
                'course',
            ])
            ->where(
                'user_id',
                auth()->id()
            )
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
                                === (int) $course->class_id
                            && strtoupper(
                                trim(
                                    (string)
                                    $assignment
                                        ->classSlot
                                        ?->code
                                )
                            )
                                === strtoupper(
                                    trim(
                                        (string)
                                        $course->slot_code
                                    )
                                );
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

        return view(
            'prof.devoir.create',
            [
                'course' => $course,
                'courses' => $courses,
                'courseId' => $courseId ?: null,
                'profHierarchy' =>
                    $profHierarchy,
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
            'title' => [
                'required',
                'string',
                'max:255',
            ],
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
            'due_date' => [
                'required',
                'date',
                'after:now',
            ],
            'file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:5120',
            ],
        ]);

        $scope =
            $this->profPaths
                ->findExactAssignment(
                    auth()->id(),
                    (int) $validated[
                        'subject_id'
                    ],
                    (int) $validated[
                        'level_id'
                    ],
                    (int) $validated[
                        'class_id'
                    ],
                    (int) $validated[
                        'class_slot_id'
                    ]
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
                    === (int) $scope->class_id
                && strtoupper(
                    trim(
                        (string)
                        $course->slot_code
                    )
                )
                    === strtoupper(
                        trim(
                            (string)
                            $scope->classSlot?->code
                        )
                    );

            if (!$courseMatchesPath) {
                throw ValidationException::withMessages([
                    'course_id' =>
                        'Le cours sélectionné ne correspond pas '
                        . 'au créneau pédagogique choisi.',
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

        Assignment::create([
            'title' =>
                $validated['title'],
            'description' =>
                $validated['description']
                ?? null,
            'file' =>
                $filePath,
            'due_date' =>
                $validated['due_date'],
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
                'Devoir créé pour le créneau '
                . ($scope->classSlot?->code ?? '')
                . ' avec succès.'
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
            ->whereNotNull('slot_code')
            ->where('slot_code', '!=', '')
            ->orderBy('title')
            ->get();

        return view(
            'prof.devoir.edit',
            [
                'devoir' => $devoir,
                'courses' => $courses,
                'profHierarchy' =>
                    $profHierarchy,
                'selectedSubjectId' =>
                    old(
                        'subject_id',
                        $devoir
                            ->classSlot
                            ?->subject_id
                        ?? $devoir
                            ->course
                            ?->subject_id
                    ),
                'selectedLevelId' =>
                    old(
                        'level_id',
                        $devoir
                            ->classSlot
                            ?->level_id
                        ?? $devoir
                            ->course
                            ?->level_id
                    ),
                'selectedClassId' =>
                    old(
                        'class_id',
                        $devoir
                            ->classSlot
                            ?->class_id
                        ?? $devoir
                            ->course
                            ?->class_id
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
            'title' => [
                'required',
                'string',
                'max:255',
            ],
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
            'due_date' => [
                'required',
                'date',
            ],
            'file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:5120',
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
            'Ce créneau ne fait pas partie de vos affectations.'
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
                    === (int) $scope->class_id
                && strtoupper(
                    trim(
                        (string) $course->slot_code
                    )
                )
                    === strtoupper(
                        trim(
                            (string)
                            $scope->classSlot?->code
                        )
                    );

            if (!$courseMatchesPath) {
                throw ValidationException::withMessages([
                    'course_id' =>
                        'Le cours sélectionné ne correspond pas '
                        . 'au créneau '
                        . ($scope->classSlot?->code ?? '')
                        . '.',
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

        $devoir->title =
            $validated['title'];

        $devoir->description =
            $validated['description']
            ?? null;

        $devoir->due_date =
            $validated['due_date'];

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
                'Devoir mis à jour pour le créneau '
                . ($scope->classSlot?->code ?? '')
                . '.'
            );
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
}
