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
     * Règle :
     * - un même professeur ne possède qu'une seule affectation
     *   sur un même class_slot_id ;
     * - plusieurs professeurs différents peuvent partager
     *   exactement le même créneau.
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
            $created = 0;
            $updated = 0;

            foreach ($targets as $slot) {
                $schedule = $this->scheduleForSlot($slot);

                $assignment = ProfAssignment::query()
                    ->where('prof_id', $professor->id)
                    ->where('class_slot_id', $slot->id)
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
                 * Nettoyage uniquement des anciennes affectations
                 * ambiguës DU MÊME PROFESSEUR et du même parcours.
                 *
                 * On ne touche jamais aux affectations des autres profs.
                 */
                ProfAssignment::query()
                    ->where('prof_id', $professor->id)
                    ->where('subject_id', $slot->subject_id)
                    ->where('level_id', $slot->level_id)
                    ->where('class_id', $slot->class_id)
                    ->whereNull('class_slot_id')
                    ->delete();

                /*
                 * IMPORTANT :
                 * on ne modifie pas schedules.prof_id ici.
                 *
                 * Une affectation créée depuis /admin/prof-assignments
                 * ne doit pas remplacer le professeur éventuellement
                 * affiché comme professeur principal dans le planning.
                 */
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
     * Remplace toutes les affectations ACTIVES d'un professeur
     * par la sélection reçue.
     *
     * Les affectations appartenant aux autres professeurs sont
     * totalement indépendantes et ne sont jamais supprimées.
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
            $existing = ProfAssignment::query()
                ->with([
                    'subject',
                    'classSlot',
                ])
                ->where('prof_id', $professor->id)
                ->whereHas(
                    'subject',
                    fn ($query) =>
                        $query->where('status', 'active')
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

                /*
                 * On supprime seulement l'affectation de CE professeur.
                 * Le même créneau peut rester affecté à d'autres profs.
                 */
                $assignment->delete();
                $removed++;
            }

            $created = 0;
            $updated = 0;

            foreach ($targets as $slot) {
                $schedule = $this->scheduleForSlot($slot);

                $assignment = ProfAssignment::query()
                    ->where('prof_id', $professor->id)
                    ->where('class_slot_id', $slot->id)
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
            }

            return [
                'created' => $created,
                'updated' => $updated,
                'removed' => $removed,
                'total' => $targets->count(),
            ];
        });
    }

    /**
     * Supprime une seule affectation.
     *
     * Les autres professeurs partageant le même créneau
     * restent affectés normalement.
     */
    public function remove(
        ProfAssignment $assignment
    ): void {
        DB::transaction(function () use (
            $assignment
        ) {
            $assignment->delete();
        });
    }

    /**
     * Utilisé par /admin/schedule lorsque l'admin choisit
     * explicitement un professeur pour une séance.
     *
     * Ce choix ajoute/met à jour l'affectation du professeur choisi,
     * mais ne supprime jamais les autres professeurs qui partagent
     * le même créneau.
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
            ->where('subject_id', $subjectId)
            ->where('level_id', $levelId)
            ->where('class_id', $classId)
            ->whereRaw(
                'UPPER(TRIM(code)) = ?',
                [strtoupper(trim($slotCode))]
            )
            ->where('is_active', true)
            ->first();

        if (!$slot) {
            $level = Level::query()
                ->whereKey($levelId)
                ->where('subject_id', $subjectId)
                ->first();

            $classRoom = ClassRoom::query()
                ->whereKey($classId)
                ->where('level_id', $levelId)
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
                                trim((string) $candidate->code)
                            ) === strtoupper(trim($slotCode))
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
            $schedule = $this->scheduleForSlot($slot);

            /*
             * IMPORTANT :
             * aucune suppression des autres professeurs.
             */
            $assignment = ProfAssignment::query()
                ->updateOrCreate(
                    [
                        'prof_id' => $professor->id,
                        'class_slot_id' => $slot->id,
                    ],
                    $this->assignmentData(
                        $professor,
                        $slot,
                        $schedule
                    )
                );

            ProfAssignment::query()
                ->where('prof_id', $professor->id)
                ->where('subject_id', $slot->subject_id)
                ->where('level_id', $slot->level_id)
                ->where('class_id', $slot->class_id)
                ->whereNull('class_slot_id')
                ->delete();

            /*
             * Ici seulement, le planning peut garder ce professeur
             * comme professeur principal de la séance.
             * Cela n'efface aucune autre ProfAssignment.
             */
            $this->syncScheduleProfessor(
                $slot,
                $professor->id
            );

            return $assignment;
        });
    }

    /**
     * Vérifie et transforme les lignes du formulaire en ClassSlot.
     */
    private function resolveTargets(
        array $rows
    ): Collection {
        $targets = collect();

        foreach ($rows as $index => $row) {
            $subject = Subject::query()
                ->whereKey((int) $row['subject_id'])
                ->where('status', 'active')
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

        /*
         * Si le même créneau est envoyé deux fois dans le formulaire,
         * on le garde une seule fois pour ce professeur.
         */
        return $targets
            ->unique('id')
            ->values();
    }

    /**
     * Données enregistrées dans prof_assignments.
     */
    private function assignmentData(
        User $professor,
        ClassSlot $slot,
        ?Schedule $schedule
    ): array {
        return [
            'prof_id' => $professor->id,
            'subject_id' => $slot->subject_id,
            'level_id' => $slot->level_id,
            'class_id' => $slot->class_id,
            'class_slot_id' => $slot->id,
            'day_of_week' => $schedule?->day_of_week,
            'start_time' => $schedule?->start_time,
            'end_time' => $schedule?->end_time,
        ];
    }

    /**
     * Récupère l'horaire correspondant au créneau.
     */
    private function scheduleForSlot(
        ClassSlot $slot
    ): ?Schedule {
        return $this->scheduleQueryForSlot($slot)
            ->active()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->first();
    }

    /**
     * Met à jour uniquement le professeur principal stocké dans schedules.
     * Cette méthode n'est utilisée que depuis la logique du planning.
     */
    private function syncScheduleProfessor(
        ClassSlot $slot,
        ?int $professorId,
        ?int $expectedCurrentProfessorId = null
    ): void {
        $query = $this
            ->scheduleQueryForSlot($slot)
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
            ->where('subject_id', $slot->subject_id)
            ->where('level_id', $slot->level_id)
            ->where('class_id', $slot->class_id)
            ->whereRaw(
                'UPPER(TRIM(slot_code)) = ?',
                [
                    strtoupper(
                        trim((string) $slot->code)
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
