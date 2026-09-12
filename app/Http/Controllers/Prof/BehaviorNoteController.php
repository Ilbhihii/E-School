<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\ProfAssignment;
use App\Models\StudentBehaviorNote;
use App\Models\User;
use App\Services\ProfessorPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BehaviorNoteController extends Controller
{
    private ProfessorPathService $profPaths;

    public function __construct(
        ProfessorPathService $profPaths
    ) {
        $this->profPaths = $profPaths;
    }

    public function index(Request $request)
    {
        $professorId = (int) auth()->id();

        $profHierarchy =
            $this->profPaths->hierarchy($professorId);

        $filters =
            $this->profPaths->selectedFilters($request);

        $students = collect();
        $selectedAssignment = null;

        /*
         * Affichage automatique :
         *
         * - sans filtre : on ouvre automatiquement le premier
         *   parcours affecté au professeur ;
         * - avec un filtre partiel : on complète automatiquement
         *   avec le premier niveau / la première classe disponible ;
         * - avec Matière + Niveau + Classe : on respecte exactement
         *   le parcours demandé.
         *
         * Le professeur n'est donc plus obligé de sélectionner
         * les trois champs avant de voir les étudiants.
         */
        $hasRequestedFilter =
            $request->filled('subject_id')
            || $request->filled('level_id')
            || $request->filled('class_id');

        if ($hasRequestedFilter) {
            $selectedAssignment =
                $this->profPaths
                    ->filteredAssignments(
                        $professorId,
                        $request
                    )
                    ->first();

            abort_unless(
                $selectedAssignment,
                403,
                'Ce parcours ne fait pas partie de vos affectations.'
            );
        } else {
            $selectedAssignment =
                $this->profPaths
                    ->assignments($professorId)
                    ->first();
        }

        if ($selectedAssignment) {
            $subjectId =
                (int) $selectedAssignment->subject_id;

            $levelId =
                (int) $selectedAssignment->level_id;

            $classId =
                (int) $selectedAssignment->class_id;

            /*
             * On renvoie les valeurs réelles à la vue afin que les
             * listes Matière → Niveau → Classe affichent directement
             * le parcours actuellement ouvert.
             */
            $filters['selectedSubjectId'] =
                $subjectId;

            $filters['selectedLevelId'] =
                $levelId;

            $filters['selectedClassId'] =
                $classId;

            $filters['selectedSlotId'] = null;

            $studentIds =
                $this->profPaths
                    ->studentIdsForAssignment(
                        $selectedAssignment
                    );

            $stats = StudentBehaviorNote::query()
                ->where(
                    'professor_id',
                    $professorId
                )
                ->where(
                    'subject_id',
                    $subjectId
                )
                ->where(
                    'level_id',
                    $levelId
                )
                ->where(
                    'class_room_id',
                    $classId
                )
                ->whereIn(
                    'student_id',
                    $studentIds
                )
                ->select([
                    'student_id',
                    DB::raw(
                        "SUM(CASE WHEN type = 'positive' THEN points ELSE 0 END) as positive_points"
                    ),
                    DB::raw(
                        "SUM(CASE WHEN type = 'negative' THEN points ELSE 0 END) as negative_points"
                    ),
                    DB::raw(
                        'COUNT(*) as notes_count'
                    ),
                    DB::raw(
                        'MAX(noted_at) as last_noted_at'
                    ),
                ])
                ->groupBy('student_id')
                ->get()
                ->keyBy('student_id');

            $students = User::query()
                ->where(
                    'role',
                    User::ROLE_STUDENT
                )
                ->whereIn(
                    'id',
                    $studentIds
                )
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'email',
                ])
                ->map(
                    function (User $student) use ($stats) {
                        $studentStats =
                            $stats->get($student->id);

                        $student->positive_points =
                            (int) (
                                $studentStats
                                    ->positive_points
                                ?? 0
                            );

                        $student->negative_points =
                            (int) (
                                $studentStats
                                    ->negative_points
                                ?? 0
                            );

                        $student->notes_count =
                            (int) (
                                $studentStats
                                    ->notes_count
                                ?? 0
                            );

                        $student->last_noted_at =
                            $studentStats
                                ->last_noted_at
                            ?? null;

                        $student->balance_points =
                            $student->positive_points
                            - $student->negative_points;

                        return $student;
                    }
                );
        }

        return view(
            'prof.behavior-notes.index',
            array_merge(
                compact(
                    'profHierarchy',
                    'students',
                    'selectedAssignment'
                ),
                $filters
            )
        );
    }

    public function show(
        Request $request,
        User $student
    ) {
        $context = $this->resolveStudentContext(
            $request,
            $student
        );

        $notes = StudentBehaviorNote::query()
            ->where('professor_id', auth()->id())
            ->where('student_id', $student->id)
            ->where('subject_id', $context['subject_id'])
            ->where('level_id', $context['level_id'])
            ->where('class_room_id', $context['class_id'])
            ->orderByDesc('noted_at')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->query());

        $totals = StudentBehaviorNote::query()
            ->where('professor_id', auth()->id())
            ->where('student_id', $student->id)
            ->where('subject_id', $context['subject_id'])
            ->where('level_id', $context['level_id'])
            ->where('class_room_id', $context['class_id'])
            ->selectRaw(
                "COALESCE(SUM(CASE WHEN type = 'positive' THEN points ELSE 0 END), 0) as positive_points"
            )
            ->selectRaw(
                "COALESCE(SUM(CASE WHEN type = 'negative' THEN points ELSE 0 END), 0) as negative_points"
            )
            ->selectRaw('COUNT(*) as notes_count')
            ->first();

        $positivePoints =
            (int) ($totals->positive_points ?? 0);

        $negativePoints =
            (int) ($totals->negative_points ?? 0);

        $balancePoints =
            $positivePoints - $negativePoints;

        return view(
            'prof.behavior-notes.show',
            [
                'student' => $student,
                'assignment' => $context['assignment'],
                'notes' => $notes,
                'positivePoints' => $positivePoints,
                'negativePoints' => $negativePoints,
                'balancePoints' => $balancePoints,
                'notesCount' =>
                    (int) ($totals->notes_count ?? 0),
            ]
        );
    }

    public function store(
        Request $request,
        User $student
    ) {
        $context = $this->resolveStudentContext(
            $request,
            $student
        );

        $validated = $request->validate([
            'type' => [
                'required',
                'in:positive,negative',
            ],
            'points' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'note' => [
                'required',
                'string',
                'max:2000',
            ],
            'noted_at' => [
                'required',
                'date',
            ],
        ]);

        StudentBehaviorNote::query()->create([
            'professor_id' => auth()->id(),
            'student_id' => $student->id,
            'subject_id' => $context['subject_id'],
            'level_id' => $context['level_id'],
            'class_room_id' => $context['class_id'],
            'type' => $validated['type'],
            'points' => (int) $validated['points'],
            'note' => trim($validated['note']),
            'noted_at' => $validated['noted_at'],
        ]);

        return redirect()
            ->route(
                'prof.behavior-notes.show',
                [
                    'student' => $student->id,
                    'subject_id' => $context['subject_id'],
                    'level_id' => $context['level_id'],
                    'class_id' => $context['class_id'],
                ]
            )
            ->with(
                'success',
                'Observation ajoutée au bloc-notes.'
            );
    }

    public function update(
        Request $request,
        User $student,
        StudentBehaviorNote $behaviorNote
    ) {
        $this->authorizeNote(
            $student,
            $behaviorNote
        );

        $context = $this->resolveStoredNoteContext(
            $student,
            $behaviorNote
        );

        $validated = $request->validate([
            'type' => [
                'required',
                'in:positive,negative',
            ],
            'points' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'note' => [
                'required',
                'string',
                'max:2000',
            ],
            'noted_at' => [
                'required',
                'date',
            ],
        ]);

        $behaviorNote->update([
            'type' => $validated['type'],
            'points' => (int) $validated['points'],
            'note' => trim($validated['note']),
            'noted_at' => $validated['noted_at'],
        ]);

        return redirect()
            ->route(
                'prof.behavior-notes.show',
                [
                    'student' => $student->id,
                    'subject_id' => $context['subject_id'],
                    'level_id' => $context['level_id'],
                    'class_id' => $context['class_id'],
                ]
            )
            ->with(
                'success',
                'Observation mise à jour.'
            );
    }

    public function destroy(
        User $student,
        StudentBehaviorNote $behaviorNote
    ) {
        $this->authorizeNote(
            $student,
            $behaviorNote
        );

        $context = $this->resolveStoredNoteContext(
            $student,
            $behaviorNote
        );

        $behaviorNote->delete();

        return redirect()
            ->route(
                'prof.behavior-notes.show',
                [
                    'student' => $student->id,
                    'subject_id' => $context['subject_id'],
                    'level_id' => $context['level_id'],
                    'class_id' => $context['class_id'],
                ]
            )
            ->with(
                'success',
                'Observation supprimée.'
            );
    }

    private function resolveStudentContext(
        Request $request,
        User $student
    ): array {
        abort_unless(
            $student->role === User::ROLE_STUDENT,
            404
        );

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
        ]);

        $assignment =
            $this->profPaths->findClassAssignment(
                (int) auth()->id(),
                (int) $validated['subject_id'],
                (int) $validated['level_id'],
                (int) $validated['class_id']
            );

        abort_unless(
            $assignment,
            403,
            'Ce parcours ne fait pas partie de vos affectations.'
        );

        abort_unless(
            $this->profPaths
                ->studentIdsForAssignment($assignment)
                ->contains((int) $student->id),
            403,
            'Cet étudiant ne fait pas partie de cette classe.'
        );

        return [
            'subject_id' =>
                (int) $validated['subject_id'],
            'level_id' =>
                (int) $validated['level_id'],
            'class_id' =>
                (int) $validated['class_id'],
            'assignment' => $assignment,
        ];
    }

    private function resolveStoredNoteContext(
        User $student,
        StudentBehaviorNote $behaviorNote
    ): array {
        $assignment =
            $this->profPaths->findClassAssignment(
                (int) auth()->id(),
                (int) $behaviorNote->subject_id,
                (int) $behaviorNote->level_id,
                (int) $behaviorNote->class_room_id
            );

        abort_unless($assignment, 403);

        abort_unless(
            $this->profPaths
                ->studentIdsForAssignment($assignment)
                ->contains((int) $student->id),
            403
        );

        return [
            'subject_id' =>
                (int) $behaviorNote->subject_id,
            'level_id' =>
                (int) $behaviorNote->level_id,
            'class_id' =>
                (int) $behaviorNote->class_room_id,
            'assignment' => $assignment,
        ];
    }

    private function authorizeNote(
        User $student,
        StudentBehaviorNote $behaviorNote
    ): void {
        abort_unless(
            $student->role === User::ROLE_STUDENT,
            404
        );

        abort_unless(
            (int) $behaviorNote->professor_id
                === (int) auth()->id()
            && (int) $behaviorNote->student_id
                === (int) $student->id,
            403
        );
    }
}
