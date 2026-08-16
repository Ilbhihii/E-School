<?php

namespace App\Services;

use App\Models\ProfAssignment;
use App\Models\ProfessorAvailability;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProfessorAutoSchedulerService
{
    private const AUTO_NOTE_PREFIX = '[AUTO:PROF_AVAILABILITY]';

    /**
     * Construit automatiquement le planning d'un professeur à partir de :
     *
     * 1) ses affectations pédagogiques ProfAssignment
     *    Matière → Niveau → Classe → Créneau (D1/D2/I1/A1...)
     * 2) ses disponibilités ProfessorAvailability
     *    Jour → heure début → heure fin.
     *
     * Règles de sécurité :
     * - aucun planning manuel existant n'est supprimé ;
     * - aucun planning existant n'est déplacé automatiquement ;
     * - si le créneau structurel possède déjà un planning hebdomadaire,
     *   on le réutilise seulement si le professeur est disponible ;
     * - sinon on crée le premier horaire disponible sans conflit ;
     * - un deuxième enregistrement des disponibilités ne crée pas de doublon.
     */
    public function syncForProfessor(User $professor): array
    {
        if ($professor->role !== User::ROLE_PROF) {
            return $this->emptyResult('Le compte sélectionné n’est pas un professeur.');
        }

        return DB::transaction(function () use ($professor) {
            $availabilities = ProfessorAvailability::query()
                ->where('prof_id', $professor->id)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();

            $assignments = ProfAssignment::query()
                ->with([
                    'subject',
                    'level',
                    'classRoom',
                    'classSlot',
                ])
                ->where('prof_id', $professor->id)
                ->whereNotNull('class_slot_id')
                ->whereHas('subject', function ($query) {
                    $query->where('status', 'active');
                })
                ->lockForUpdate()
                ->get()
                ->sortBy(function (ProfAssignment $assignment) {
                    $position = optional($assignment->classSlot)->position ?: 999;
                    $code = strtoupper(trim((string) optional($assignment->classSlot)->code));

                    return sprintf(
                        '%010d|%010d|%010d|%03d|%s',
                        (int) $assignment->subject_id,
                        (int) $assignment->level_id,
                        (int) $assignment->class_id,
                        (int) $position,
                        $code
                    );
                })
                ->values();

            $result = [
                'assignments' => $assignments->count(),
                'created' => 0,
                'reused' => 0,
                'pending' => 0,
                'issues' => [],
            ];

            if ($assignments->isEmpty()) {
                $result['issues'][] =
                    'Aucune affectation Matière → Niveau → Classe → Créneau n’est définie pour ce professeur.';

                return $result;
            }

            if ($availabilities->isEmpty()) {
                $result['pending'] = $assignments->count();
                $result['issues'][] =
                    'Aucune disponibilité n’est enregistrée. Le planning existant est conservé sans suppression.';

                return $result;
            }

            foreach ($assignments as $assignment) {
                if (
                    !$assignment->classSlot
                    || !$assignment->classSlot->is_active
                ) {
                    $result['pending']++;
                    $result['issues'][] = $this->assignmentLabel($assignment)
                        . ' : créneau structurel inactif ou introuvable.';
                    continue;
                }

                $slotCode = $this->assignmentSlotCode($assignment);

                if ($slotCode === '') {
                    $result['pending']++;
                    $result['issues'][] = $this->assignmentLabel($assignment)
                        . ' : créneau structurel absent.';
                    continue;
                }

                /*
                 * Un créneau D1/D2/I1/A1... représente un groupe structurel.
                 * S'il possède déjà un horaire hebdomadaire, on ne crée pas
                 * une seconde ligne Schedule : on réutilise la ligne existante.
                 */
                $existingSchedule = $this->existingWeeklyScheduleForAssignment(
                    $assignment,
                    $slotCode
                );

                if ($existingSchedule) {
                    if ($this->availabilityCoversSchedule(
                        $availabilities,
                        $existingSchedule
                    )) {
                        $this->syncAssignmentWithSchedule(
                            $assignment,
                            $existingSchedule
                        );

                        if (empty($existingSchedule->prof_id)) {
                            $existingSchedule->update([
                                'prof_id' => $professor->id,
                            ]);
                        }

                        $result['reused']++;
                    } else {
                        $result['pending']++;
                        $result['issues'][] = $this->assignmentLabel($assignment)
                            . ' : le créneau existe déjà le '
                            . $existingSchedule->day_label
                            . ' '
                            . $existingSchedule->time_range_label
                            . ', mais cet horaire n’est pas inclus dans les disponibilités de '
                            . $professor->name
                            . '.';
                    }

                    continue;
                }

                $candidate = $this->firstAvailableCandidate(
                    $professor,
                    $assignment,
                    $slotCode,
                    $availabilities,
                    $assignments
                );

                if (!$candidate) {
                    $result['pending']++;
                    $result['issues'][] = $this->assignmentLabel($assignment)
                        . ' : aucune disponibilité libre sans conflit.';
                    continue;
                }

                $schedule = Schedule::create([
                    'prof_id' => $professor->id,
                    'class_id' => $assignment->class_id,
                    'slot_code' => $slotCode,
                    'subject_id' => $assignment->subject_id,
                    'level_id' => $assignment->level_id,
                    'room_id' => null,
                    'subject' => optional($assignment->subject)->name ?: 'Matière',
                    'start_time' => $candidate['start_at']->format('Y-m-d H:i:s'),
                    'end_time' => $candidate['end_at']->format('Y-m-d H:i:s'),
                    'date' => $candidate['anchor_date']->toDateString(),
                    'day_of_week' => $candidate['day_of_week'],
                    'recurrence' => Schedule::RECURRENCE_WEEKLY,
                    'valid_from' => $candidate['anchor_date']->toDateString(),
                    'valid_until' => null,
                    'status' => Schedule::STATUS_ACTIVE,
                    'notes' => self::AUTO_NOTE_PREFIX
                        . ' Créé automatiquement depuis les disponibilités de '
                        . $professor->name
                        . '.',
                ]);

                $this->syncAssignmentWithSchedule($assignment, $schedule);
                $result['created']++;
            }

            return $result;
        });
    }

    private function firstAvailableCandidate(
        User $professor,
        ProfAssignment $assignment,
        string $slotCode,
        Collection $availabilities,
        Collection $professorAssignments
    ): ?array {
        foreach ($availabilities as $availability) {
            $dayOfWeek = (int) $availability->day_of_week;
            $anchorDate = $this->firstOccurrenceOnOrAfter(
                now()->startOfDay(),
                $dayOfWeek
            );

            $startAt = Carbon::parse(
                $anchorDate->format('Y-m-d')
                . ' '
                . Carbon::parse($availability->start_time)->format('H:i:s')
            );

            $endAt = Carbon::parse(
                $anchorDate->format('Y-m-d')
                . ' '
                . Carbon::parse($availability->end_time)->format('H:i:s')
            );

            if ($endAt->lte($startAt)) {
                continue;
            }

            $candidate = [
                'day_of_week' => $dayOfWeek,
                'anchor_date' => $anchorDate,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'slot_code' => $slotCode,
            ];

            if (!$this->hasConflict(
                $professor,
                $assignment,
                $candidate,
                $professorAssignments
            )) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Vérifie les deux conflits qui comptent :
     * - professeur déjà occupé ;
     * - même groupe classe + même créneau D1/D2/I1... déjà occupé.
     */
    private function hasConflict(
        User $professor,
        ProfAssignment $assignment,
        array $candidate,
        Collection $professorAssignments
    ): bool {
        $schedules = Schedule::query()
            ->active()
            ->with(['classRoom', 'prof'])
            ->get();

        foreach ($schedules as $existing) {
            if (!$this->scheduleCanOverlapWeeklyCandidate(
                $existing,
                $candidate['anchor_date'],
                (int) $candidate['day_of_week']
            )) {
                continue;
            }

            $existingStart = Carbon::parse($existing->start_time)->format('H:i:s');
            $existingEnd = Carbon::parse($existing->end_time)->format('H:i:s');
            $candidateStart = $candidate['start_at']->format('H:i:s');
            $candidateEnd = $candidate['end_at']->format('H:i:s');

            if (!$this->timesOverlap(
                $existingStart,
                $existingEnd,
                $candidateStart,
                $candidateEnd
            )) {
                continue;
            }

            $sameTeacher =
                (int) $existing->prof_id === (int) $professor->id
                || $this->professorIsStructurallyAssignedToSchedule(
                    $existing,
                    $professorAssignments
                );

            $existingSlotCode = strtoupper(
                trim((string) $existing->slot_code)
            );

            $newSlotCode = strtoupper(
                trim((string) $candidate['slot_code'])
            );

            $sameClassGroup =
                (int) $existing->class_id === (int) $assignment->class_id
                && (
                    $existingSlotCode === ''
                    || $newSlotCode === ''
                    || $existingSlotCode === $newSlotCode
                );

            if ($sameTeacher || $sameClassGroup) {
                return true;
            }
        }

        return false;
    }

    private function professorIsStructurallyAssignedToSchedule(
        Schedule $schedule,
        Collection $assignments
    ): bool {
        $scheduleLevelId = (int) (
            $schedule->level_id
            ?: optional($schedule->classRoom)->level_id
        );

        $scheduleSlotCode = strtoupper(
            trim((string) $schedule->slot_code)
        );

        foreach ($assignments as $assignment) {
            if (
                (int) $assignment->subject_id !== (int) $schedule->subject_id
                || (int) $assignment->level_id !== $scheduleLevelId
                || (int) $assignment->class_id !== (int) $schedule->class_id
            ) {
                continue;
            }

            $assignmentSlotCode = $this->assignmentSlotCode($assignment);

            if ($scheduleSlotCode !== '' && $assignmentSlotCode !== '') {
                if ($scheduleSlotCode === $assignmentSlotCode) {
                    return true;
                }

                continue;
            }

            if (
                !$assignment->day_of_week
                || !$assignment->start_time
                || !$assignment->end_time
            ) {
                continue;
            }

            if (
                (int) $assignment->day_of_week === (int) $schedule->day_of_week
                && Carbon::parse($assignment->start_time)->format('H:i')
                    === Carbon::parse($schedule->start_time)->format('H:i')
                && Carbon::parse($assignment->end_time)->format('H:i')
                    === Carbon::parse($schedule->end_time)->format('H:i')
            ) {
                return true;
            }
        }

        return false;
    }

    private function scheduleCanOverlapWeeklyCandidate(
        Schedule $existing,
        Carbon $candidateValidFrom,
        int $candidateDayOfWeek
    ): bool {
        $recurrence = $existing->recurrence ?: Schedule::RECURRENCE_ONCE;

        if ($recurrence === Schedule::RECURRENCE_WEEKLY) {
            $existingDay = (int) (
                $existing->day_of_week
                ?: Carbon::parse($existing->date ?: $existing->start_time)->dayOfWeekIso
            );

            if ($existingDay !== $candidateDayOfWeek) {
                return false;
            }

            if (
                $existing->valid_until
                && Carbon::parse($existing->valid_until)
                    ->endOfDay()
                    ->lt($candidateValidFrom)
            ) {
                return false;
            }

            return true;
        }

        $existingDate = Carbon::parse(
            $existing->date ?: $existing->start_time
        )->startOfDay();

        return $existingDate->gte($candidateValidFrom)
            && $existingDate->dayOfWeekIso === $candidateDayOfWeek;
    }

    private function existingWeeklyScheduleForAssignment(
        ProfAssignment $assignment,
        string $slotCode
    ): ?Schedule {
        $today = now()->startOfDay();

        return Schedule::query()
            ->active()
            ->where('subject_id', $assignment->subject_id)
            ->where('level_id', $assignment->level_id)
            ->where('class_id', $assignment->class_id)
            ->whereRaw(
                'UPPER(TRIM(slot_code)) = ?',
                [$slotCode]
            )
            ->where('recurrence', Schedule::RECURRENCE_WEEKLY)
            ->where(function ($query) use ($today) {
                $query
                    ->whereNull('valid_until')
                    ->orWhereDate('valid_until', '>=', $today->toDateString());
            })
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->first();
    }

    private function availabilityCoversSchedule(
        Collection $availabilities,
        Schedule $schedule
    ): bool {
        $dayOfWeek = (int) (
            $schedule->day_of_week
            ?: Carbon::parse($schedule->date ?: $schedule->start_time)->dayOfWeekIso
        );

        $dayAvailabilities = $availabilities
            ->where('day_of_week', $dayOfWeek)
            ->sortBy(function ($availability) {
                return Carbon::parse($availability->start_time)->format('H:i:s');
            })
            ->values();

        if ($dayAvailabilities->isEmpty()) {
            return false;
        }

        $referenceDate = Carbon::parse($schedule->start_time)->format('Y-m-d');
        $merged = [];

        foreach ($dayAvailabilities as $availability) {
            $start = Carbon::parse(
                $referenceDate
                . ' '
                . Carbon::parse($availability->start_time)->format('H:i:s')
            );

            $end = Carbon::parse(
                $referenceDate
                . ' '
                . Carbon::parse($availability->end_time)->format('H:i:s')
            );

            if (empty($merged)) {
                $merged[] = [$start, $end];
                continue;
            }

            $lastIndex = count($merged) - 1;
            $lastEnd = $merged[$lastIndex][1];

            if ($start->lte($lastEnd)) {
                if ($end->gt($lastEnd)) {
                    $merged[$lastIndex][1] = $end;
                }
                continue;
            }

            $merged[] = [$start, $end];
        }

        $scheduleStart = Carbon::parse($schedule->start_time);
        $scheduleEnd = Carbon::parse($schedule->end_time);

        foreach ($merged as $range) {
            if (
                $scheduleStart->gte($range[0])
                && $scheduleEnd->lte($range[1])
            ) {
                return true;
            }
        }

        return false;
    }

    private function syncAssignmentWithSchedule(
        ProfAssignment $assignment,
        Schedule $schedule
    ): void {
        $assignment->update([
            'day_of_week' => (int) $schedule->day_of_week,
            'start_time' => Carbon::parse($schedule->start_time)->format('H:i:s'),
            'end_time' => Carbon::parse($schedule->end_time)->format('H:i:s'),
        ]);
    }

    private function assignmentSlotCode(ProfAssignment $assignment): string
    {
        return strtoupper(
            trim((string) optional($assignment->classSlot)->code)
        );
    }

    private function assignmentLabel(ProfAssignment $assignment): string
    {
        return collect([
            optional($assignment->subject)->name,
            optional($assignment->level)->name,
            optional($assignment->classRoom)->name,
            $this->assignmentSlotCode($assignment),
        ])
            ->filter()
            ->implode(' → ');
    }

    private function firstOccurrenceOnOrAfter(
        Carbon $date,
        int $dayOfWeek
    ): Carbon {
        $cursor = $date->copy()->startOfDay();
        $difference = ($dayOfWeek - $cursor->dayOfWeekIso + 7) % 7;

        return $cursor->addDays($difference);
    }

    private function timesOverlap(
        string $existingStart,
        string $existingEnd,
        string $newStart,
        string $newEnd
    ): bool {
        return $newStart < $existingEnd && $newEnd > $existingStart;
    }

    private function emptyResult(string $issue): array
    {
        return [
            'assignments' => 0,
            'created' => 0,
            'reused' => 0,
            'pending' => 0,
            'issues' => [$issue],
        ];
    }
}
