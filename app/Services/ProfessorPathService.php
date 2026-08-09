<?php

namespace App\Services;

use App\Models\ClassSlot;
use App\Models\ProfAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfessorPathService
{
    public function assignments(int $professorId): Collection
    {
        return ProfAssignment::query()
            ->with([
                'subject',
                'level',
                'classRoom',
                'classSlot',
            ])
            ->where('prof_id', $professorId)
            ->when(
                Schema::hasColumn(
                    'prof_assignments',
                    'class_slot_id'
                ),
                fn ($query) =>
                    $query->whereNotNull(
                        'class_slot_id'
                    )
            )
            ->orderBy('subject_id')
            ->orderBy('level_id')
            ->orderBy('class_id')
            ->orderBy('class_slot_id')
            ->get()
            ->filter(
                fn (ProfAssignment $assignment) =>
                    $assignment->subject
                    && $assignment->level
                    && $assignment->classRoom
                    && (
                        !Schema::hasColumn(
                            'prof_assignments',
                            'class_slot_id'
                        )
                        || $assignment->classSlot
                    )
            )
            ->values();
    }

    public function hierarchy(int $professorId): array
    {
        $assignments = $this->assignments(
            $professorId
        );

        return $assignments
            ->groupBy('subject_id')
            ->map(function (
                Collection $subjectAssignments
            ) {
                $first =
                    $subjectAssignments->first();

                return [
                    'id' => (int) $first->subject_id,
                    'name' =>
                        $first->subject?->name
                        ?? 'Matière',
                    'levels' =>
                        $subjectAssignments
                            ->groupBy('level_id')
                            ->map(function (
                                Collection $levelAssignments
                            ) {
                                $firstLevel =
                                    $levelAssignments
                                        ->first();

                                return [
                                    'id' =>
                                        (int) $firstLevel
                                            ->level_id,
                                    'name' =>
                                        $firstLevel
                                            ->level
                                            ?->name
                                        ?? 'Niveau',
                                    'classes' =>
                                        $levelAssignments
                                            ->groupBy(
                                                'class_id'
                                            )
                                            ->map(
                                                function (
                                                    Collection
                                                    $classAssignments
                                                ) {
                                                    $firstClass =
                                                        $classAssignments
                                                            ->first();

                                                    $slots =
                                                        $classAssignments
                                                            ->filter(
                                                                fn (
                                                                    ProfAssignment
                                                                    $assignment
                                                                ) =>
                                                                    $assignment
                                                                        ->classSlot
                                                            )
                                                            ->sortBy(
                                                                fn (
                                                                    ProfAssignment
                                                                    $assignment
                                                                ) =>
                                                                    $assignment
                                                                        ->classSlot
                                                                        ?->position
                                                                    ?? 999
                                                            )
                                                            ->map(
                                                                fn (
                                                                    ProfAssignment
                                                                    $assignment
                                                                ) => [
                                                                    'id' =>
                                                                        (int) $assignment
                                                                            ->class_slot_id,
                                                                    'code' =>
                                                                        $assignment
                                                                            ->classSlot
                                                                            ?->code
                                                                        ?? '—',
                                                                ]
                                                            )
                                                            ->unique(
                                                                'id'
                                                            )
                                                            ->values()
                                                            ->all();

                                                    return [
                                                        'id' =>
                                                            (int) $firstClass
                                                                ->class_id,
                                                        'name' =>
                                                            $firstClass
                                                                ->classRoom
                                                                ?->name
                                                            ?? 'Classe',
                                                        'slots' =>
                                                            $slots,
                                                    ];
                                                }
                                            )
                                            ->values()
                                            ->all(),
                                ];
                            })
                            ->values()
                            ->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function filteredAssignments(
        int $professorId,
        Request $request
    ): Collection {
        $assignments =
            $this->assignments($professorId);

        $subjectId =
            $this->positiveInt(
                $request->query('subject_id')
            );

        $levelId =
            $this->positiveInt(
                $request->query('level_id')
            );

        $classId =
            $this->positiveInt(
                $request->query('class_id')
            );

        $slotId =
            $this->positiveInt(
                $request->query('class_slot_id')
            );

        return $assignments
            ->filter(
                function (
                    ProfAssignment $assignment
                ) use (
                    $subjectId,
                    $levelId,
                    $classId,
                    $slotId
                ) {
                    if (
                        $subjectId
                        && (int) $assignment->subject_id
                            !== $subjectId
                    ) {
                        return false;
                    }

                    if (
                        $levelId
                        && (int) $assignment->level_id
                            !== $levelId
                    ) {
                        return false;
                    }

                    if (
                        $classId
                        && (int) $assignment->class_id
                            !== $classId
                    ) {
                        return false;
                    }

                    if (
                        $slotId
                        && (int) $assignment->class_slot_id
                            !== $slotId
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->values();
    }

    public function findExactAssignment(
        int $professorId,
        int $subjectId,
        int $levelId,
        int $classId,
        int $slotId
    ): ?ProfAssignment {
        return ProfAssignment::query()
            ->with([
                'subject',
                'level',
                'classRoom',
                'classSlot',
            ])
            ->where('prof_id', $professorId)
            ->where('subject_id', $subjectId)
            ->where('level_id', $levelId)
            ->where('class_id', $classId)
            ->where('class_slot_id', $slotId)
            ->first();
    }

    public function ownsSlot(
        int $professorId,
        int $slotId
    ): bool {
        return ProfAssignment::query()
            ->where('prof_id', $professorId)
            ->where('class_slot_id', $slotId)
            ->exists();
    }

    public function slotIds(
        int $professorId
    ): Collection {
        return $this->assignments(
            $professorId
        )
            ->pluck('class_slot_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    public function slotCodes(
        int $professorId
    ): Collection {
        return $this->assignments(
            $professorId
        )
            ->pluck('classSlot.code')
            ->filter()
            ->map(
                fn ($code) =>
                    strtoupper(
                        trim((string) $code)
                    )
            )
            ->unique()
            ->values();
    }

    public function studentIdsForAssignment(
        ProfAssignment $assignment
    ): Collection {
        if (
            !Schema::hasTable('class_user')
        ) {
            return collect();
        }

        $query = DB::table('class_user')
            ->where(
                'subject_id',
                $assignment->subject_id
            )
            ->where(
                'class_id',
                $assignment->class_id
            );

        if (
            Schema::hasColumn(
                'class_user',
                'class_slot_id'
            )
            && $assignment->class_slot_id
        ) {
            $query->where(
                'class_slot_id',
                $assignment->class_slot_id
            );
        }

        return $query
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    public function studentIds(
        int $professorId
    ): Collection {
        return $this->assignments(
            $professorId
        )
            ->flatMap(
                fn (ProfAssignment $assignment) =>
                    $this->studentIdsForAssignment(
                        $assignment
                    )
            )
            ->unique()
            ->values();
    }

    public function studentBelongsToSlot(
        int $studentId,
        ProfAssignment $assignment
    ): bool {
        return $this
            ->studentIdsForAssignment(
                $assignment
            )
            ->contains($studentId);
    }

    public function slotForFilters(
        int $professorId,
        Request $request
    ): ?ClassSlot {
        $slotId =
            $this->positiveInt(
                $request->query(
                    'class_slot_id',
                    $request->input(
                        'class_slot_id'
                    )
                )
            );

        if (!$slotId) {
            return null;
        }

        if (
            !$this->ownsSlot(
                $professorId,
                $slotId
            )
        ) {
            return null;
        }

        return ClassSlot::query()
            ->find($slotId);
    }

    public function selectedFilters(
        Request $request
    ): array {
        return [
            'selectedSubjectId' =>
                $this->positiveInt(
                    $request->query(
                        'subject_id',
                        $request->input(
                            'subject_id'
                        )
                    )
                ),
            'selectedLevelId' =>
                $this->positiveInt(
                    $request->query(
                        'level_id',
                        $request->input(
                            'level_id'
                        )
                    )
                ),
            'selectedClassId' =>
                $this->positiveInt(
                    $request->query(
                        'class_id',
                        $request->input(
                            'class_id'
                        )
                    )
                ),
            'selectedSlotId' =>
                $this->positiveInt(
                    $request->query(
                        'class_slot_id',
                        $request->input(
                            'class_slot_id'
                        )
                    )
                ),
        ];
    }

    private function positiveInt(
        mixed $value
    ): ?int {
        if (
            $value === null
            || $value === ''
            || !is_numeric($value)
        ) {
            return null;
        }

        $value = (int) $value;

        return $value > 0
            ? $value
            : null;
    }
}
