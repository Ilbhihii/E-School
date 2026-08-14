<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\ClassSlot;
use App\Models\Level;
use App\Models\ProfAssignment;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProfessorAssignmentService
{
    public function __construct(
        private ClassSlotService $slots
    ) {
    }

    /**
     * Ajoute une ou plusieurs affectations à un professeur.
     *
     * Un professeur peut avoir autant de Matières → Niveaux → Classes
     * → Créneaux que nécessaire. Un créneau structurel donné reste,
     * par contre, affecté à un seul professeur principal à la fois.
     */
    public function add(
        User $professor,
        array $rows
    ): array {
        $this->assertProfessor($professor);

        $targets = $this->resolveTargets($rows);

        return DB::transaction(function () use (
            $professor,
            $targets
        ) {
            $this->assertNoOtherProfessor(
                $professor,
                $targets
            );

            $created = 0;
            $updated = 0;

            foreach ($targets as $slot) {
                $schedule = $this->scheduleForSlot(
                    $slot
                );

                $assignment = ProfAssignment::query()
                    ->where(
                        'prof_id',
                        $professor->id
                    )
                    ->where(
                        'class_slot_id',
                        $slot->id
                    )
                    ->first();

                $data = $this->assignmentData(
                    $professor,
                    $slot,
                    $schedule
                );

                if ($assignment) {
                    $assignment->update($data);
                    $updated++;
                } else {
                    ProfAssignment::create($data);
                    $created++;
                }

                /*
                 * Nettoyage des anciennes affectations sans class_slot_id
                 * du même parcours. Elles sont devenues ambiguës depuis
                 * l'introduction des groupes D1/D2/I1/A1...
                 */
                ProfAssignment::query()
                    ->where(
                        'prof_id',
                        $professor->id
                    )
                    ->where(
                        'subject_id',
                        $slot->subject_id
                    )
                    ->where(
                        'level_id',
                        $slot->level_id
                    )
                    ->where(
                        'class_id',
                        $slot->class_id
                    )
                    ->whereNull(
                        'class_slot_id'
                    )
                    ->delete();

                $this->syncScheduleProfessor(
                    $slot,
                    $professor->id
                );
            }

            return [
                'created' => $created,
                'updated' => $updated,
                'removed' => 0,
                'total' => $targets->count(),
            ];
        });
    }

    /**
     * Remplace toutes les affectations ACTIVES d'un professeur par la
     * sélection reçue. Les affectations d'une ancienne matière devenue
     * inactive sont conservées pour ne pas supprimer de l'historique
     * invisible depuis le formulaire.
     */
    public function replaceActive(
        User $professor,
        array $rows
    ): array {
        $this->assertProfessor($professor);

        $targets = $this->resolveTargets($rows);
        $targetSlotIds = $targets
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        return DB::transaction(function () use (
            $professor,
            $targets,
            $targetSlotIds
        ) {
            $this->assertNoOtherProfessor(
                $professor,
                $targets
            );

            $existing = ProfAssignment::query()
                ->with([
                    'subject',
                    'classSlot',
                ])
                ->where(
                    'prof_id',
                    $professor->id
                )
                ->whereHas(
                    'subject',
                    fn ($query) =>
                        $query->where(
                            'status',
                            'active'
                        )
                )
                ->get();

            $removed = 0;

            foreach ($existing as $assignment) {
                if (
                    $assignment->class_slot_id
                    && $targetSlotIds->contains(
                        (int) $assignment->class_slot_id
                    )
                ) {
                    continue;
                }

                if ($assignment->classSlot) {
                    $this->syncScheduleProfessor(
                        $assignment->classSlot,
                        null,
                        (int) $professor->id
                    );
                }

                $assignment->delete();
                $removed++;
            }

            $created = 0;
            $updated = 0;

            foreach ($targets as $slot) {
                $schedule = $this->scheduleForSlot(
                    $slot
                );

                $assignment = ProfAssignment::query()
                    ->where(
                        'prof_id',
                        $professor->id
                    )
                    ->where(
                        'class_slot_id',
                        $slot->id
                    )
                    ->first();

                $data = $this->assignmentData(
                    $professor,
                    $slot,
                    $schedule
                );

                if ($assignment) {
                    $assignment->update($data);
                    $updated++;
                } else {
                    ProfAssignment::create($data);
                    $created++;
                }

                $this->syncScheduleProfessor(
                    $slot,
                    $professor->id
                );
            }

            return [
                'created' => $created,
                'updated' => $updated,
                'removed' => $removed,
                'total' => $targets->count(),
            ];
        });
    }

    public function remove(
        ProfAssignment $assignment
    ): void {
        DB::transaction(function () use (
            $assignment
        ) {
            $assignment->loadMissing(
                'classSlot'
            );

            if ($assignment->classSlot) {
                $this->syncScheduleProfessor(
                    $assignment->classSlot,
                    null,
                    (int) $assignment->prof_id
                );
            }

            $assignment->delete();
        });
    }

    /**
     * Utilisé par l'emploi du temps lorsque l'admin choisit directement
     * un professeur. Ici, la modification est explicite : si le créneau
     * appartenait à un autre professeur, il est réaffecté.
     */
    public function reassignFromSchedule(
        User $professor,
        int $subjectId,
        int $levelId,
        int $classId,
        string $slotCode
    ): ?ProfAssignment {
        $this->assertProfessor($professor);

        $subject = Subject::query()
            ->whereKey($subjectId)
            ->where('status', 'active')
            ->first();

        if (!$subject) {
            return null;
        }

        $slot = ClassSlot::query()
            ->where(
                'subject_id',
                $subjectId
            )
            ->where(
                'level_id',
                $levelId
            )
            ->where(
                'class_id',
                $classId
            )
            ->whereRaw(
                'UPPER(TRIM(code)) = ?',
                [
                    strtoupper(
                        trim($slotCode)
                    ),
                ]
            )
            ->where(
                'is_active',
                true
            )
            ->first();

        if (!$slot) {
            $level = Level::query()
                ->whereKey($levelId)
                ->where(
                    'subject_id',
                    $subjectId
                )
                ->first();

            $classRoom = ClassRoom::query()
                ->whereKey($classId)
                ->where(
                    'level_id',
                    $levelId
                )
                ->first();

            if ($level && $classRoom) {
                $slot = $this->slots
                    ->syncForPath(
                        $subject,
                        $level,
                        $classRoom
                    )
                    ->first(
                        fn (ClassSlot $candidate) =>
                            strtoupper(
                                trim(
                                    (string) $candidate->code
                                )
                            )
                            === strtoupper(
                                trim($slotCode)
                            )
                    );
            }
        }

        if (!$slot) {
            return null;
        }

        return DB::transaction(function () use (
            $professor,
            $slot
        ) {
            /*
             * L'emploi du temps représente une modification explicite
             * du professeur du groupe. On évite donc de laisser deux
             * enseignants principaux sur le même class_slot_id.
             */
            ProfAssignment::query()
                ->where(
                    'class_slot_id',
                    $slot->id
                )
                ->where(
                    'prof_id',
                    '<>',
                    $professor->id
                )
                ->delete();

            $schedule = $this->scheduleForSlot(
                $slot
            );

            $assignment = ProfAssignment::query()
                ->updateOrCreate(
                    [
                        'prof_id' =>
                            $professor->id,
                        'class_slot_id' =>
                            $slot->id,
                    ],
                    $this->assignmentData(
                        $professor,
                        $slot,
                        $schedule
                    )
                );

            ProfAssignment::query()
                ->where(
                    'prof_id',
                    $professor->id
                )
                ->where(
                    'subject_id',
                    $slot->subject_id
                )
                ->where(
                    'level_id',
                    $slot->level_id
                )
                ->where(
                    'class_id',
                    $slot->class_id
                )
                ->whereNull(
                    'class_slot_id'
                )
                ->delete();

            $this->syncScheduleProfessor(
                $slot,
                $professor->id
            );

            return $assignment;
        });
    }

    private function resolveTargets(
        array $rows
    ): Collection {
        $targets = collect();

        foreach ($rows as $index => $row) {
            $subject = Subject::query()
                ->whereKey(
                    (int) $row['subject_id']
                )
                ->where(
                    'status',
                    'active'
                )
                ->first();

            if (!$subject) {
                throw ValidationException::withMessages([
                    "assignments.$index.subject_id" =>
                        'Cette matière n’est pas active.',
                ]);
            }

            $slot = $this->slots->slotForPath(
                (int) $row['class_slot_id'],
                (int) $subject->id,
                (int) $row['level_id'],
                (int) $row['class_id']
            );

            if (!$slot) {
                throw ValidationException::withMessages([
                    "assignments.$index.class_slot_id" =>
                        'Le créneau sélectionné ne correspond pas au parcours Matière → Niveau → Classe.',
                ]);
            }

            $targets->push($slot);
        }

        return $targets
            ->unique('id')
            ->values();
    }

    private function assertNoOtherProfessor(
        User $professor,
        Collection $targets
    ): void {
        $slotIds = $targets
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($slotIds->isEmpty()) {
            return;
        }

        $conflicts = ProfAssignment::query()
            ->with([
                'prof',
                'subject',
                'level',
                'classRoom',
                'classSlot',
            ])
            ->whereIn(
                'class_slot_id',
                $slotIds
            )
            ->where(
                'prof_id',
                '<>',
                $professor->id
            )
            ->lockForUpdate()
            ->get();

        if ($conflicts->isEmpty()) {
            return;
        }

        $labels = $conflicts
            ->map(function (
                ProfAssignment $assignment
            ) {
                return collect([
                    $assignment->subject?->name,
                    $assignment->level?->name,
                    $assignment->classRoom?->name,
                    $assignment->classSlot?->code,
                ])
                    ->filter()
                    ->implode(' → ')
                    . ' : '
                    . (
                        $assignment->prof?->name
                        ?? 'autre professeur'
                    );
            })
            ->unique()
            ->values()
            ->implode(' ; ');

        throw ValidationException::withMessages([
            'assignments' =>
                'Certains créneaux sont déjà affectés à un autre professeur : '
                . $labels
                . '. Retirez ou modifiez d’abord ces affectations.',
        ]);
    }

    private function assignmentData(
        User $professor,
        ClassSlot $slot,
        ?Schedule $schedule
    ): array {
        return [
            'prof_id' =>
                $professor->id,
            'subject_id' =>
                $slot->subject_id,
            'level_id' =>
                $slot->level_id,
            'class_id' =>
                $slot->class_id,
            'class_slot_id' =>
                $slot->id,
            'day_of_week' =>
                $schedule?->day_of_week,
            'start_time' =>
                $schedule?->start_time,
            'end_time' =>
                $schedule?->end_time,
        ];
    }

    private function scheduleForSlot(
        ClassSlot $slot
    ): ?Schedule {
        return $this->scheduleQueryForSlot(
            $slot
        )
            ->active()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->first();
    }

    private function syncScheduleProfessor(
        ClassSlot $slot,
        ?int $professorId,
        ?int $expectedCurrentProfessorId = null
    ): void {
        $query = $this
            ->scheduleQueryForSlot(
                $slot
            )
            ->active();

        if ($expectedCurrentProfessorId) {
            $query->where(
                'prof_id',
                $expectedCurrentProfessorId
            );
        }

        $query->update([
            'prof_id' => $professorId,
        ]);
    }

    private function scheduleQueryForSlot(
        ClassSlot $slot
    ) {
        return Schedule::query()
            ->where(
                'subject_id',
                $slot->subject_id
            )
            ->where(
                'level_id',
                $slot->level_id
            )
            ->where(
                'class_id',
                $slot->class_id
            )
            ->whereRaw(
                'UPPER(TRIM(slot_code)) = ?',
                [
                    strtoupper(
                        trim(
                            (string) $slot->code
                        )
                    ),
                ]
            );
    }

    private function assertProfessor(
        User $professor
    ): void {
        if ($professor->role !== User::ROLE_PROF) {
            throw ValidationException::withMessages([
                'prof_id' =>
                    'Le compte sélectionné n’est pas un professeur.',
            ]);
        }
    }
}
