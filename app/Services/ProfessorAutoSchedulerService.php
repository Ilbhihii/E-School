<?php

namespace App\Services;

use App\Models\ProfAssignment;
use App\Models\ProfessorAvailability;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfessorAutoSchedulerService
{
    private const AUTO_NOTE_PREFIX = '[AUTO:PROF_AVAILABILITY]';

    /**
     * Construit le planning à partir de :
     * - ProfAssignment = QUI enseigne QUOI ;
     * - weekly_sessions = combien de séances / semaine ;
     * - ProfessorAvailability = QUAND le professeur peut enseigner ;
     * - prof_assignment_schedule = liaison exacte entre une affectation
     *   et une ou plusieurs lignes Schedule.
     *
     * Exemple :
     * Coran → Apprentissage & Tajwid → Intermédiaire → I2
     * weekly_sessions = 2
     * => I2 peut être placé le mardi ET le samedi.
     */
    public function syncForProfessor(User $professor): array
    {
        if ($professor->role !== User::ROLE_PROF) {
            return $this->emptyResult(
                'Le compte sélectionné n’est pas un professeur.'
            );
        }

        if (
            !Schema::hasColumn('prof_assignments', 'weekly_sessions')
            || !Schema::hasTable('prof_assignment_schedule')
        ) {
            return $this->emptyResult(
                'La migration multi-séances n’est pas encore appliquée. '
                . 'Exécutez php artisan migrate.'
            );
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
                    'schedules',
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
                    $code = strtoupper(
                        trim((string) optional($assignment->classSlot)->code)
                    );

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

            $requestedSessions = (int) $assignments->sum(function (
                ProfAssignment $assignment
            ) {
                return $this->desiredSessions($assignment);
            });

            $result = [
                'assignments' => $assignments->count(),
                'requested_sessions' => $requestedSessions,
                'created' => 0,
                'reused' => 0,
                'rescheduled' => 0,
                'removed' => 0,
                'pending' => 0,
                'issues' => [],
            ];

            if ($assignments->isEmpty()) {
                $result['issues'][] =
                    'Aucune affectation Matière → Niveau → Classe → Créneau '
                    . 'n’est définie pour ce professeur.';

                return $result;
            }

            if ($availabilities->isEmpty()) {
                $result['pending'] = $requestedSessions;
                $result['issues'][] =
                    'Aucune disponibilité n’est enregistrée. '
                    . 'Le planning existant est conservé.';

                return $result;
            }

            foreach ($assignments as $assignment) {
                $this->syncAssignment(
                    $professor,
                    $assignment,
                    $availabilities,
                    $assignments,
                    $result
                );
            }

            return $result;
        });
    }

    private function syncAssignment(
        User $professor,
        ProfAssignment $assignment,
        Collection $availabilities,
        Collection $professorAssignments,
        array &$result
    ): void {
        if (
            !$assignment->classSlot
            || !$assignment->classSlot->is_active
        ) {
            $missing = $this->desiredSessions($assignment);
            $result['pending'] += $missing;
            $result['issues'][] = $this->assignmentLabel($assignment)
                . ' : créneau structurel inactif ou introuvable.';
            return;
        }

        $slotCode = $this->assignmentSlotCode($assignment);

        if ($slotCode === '') {
            $missing = $this->desiredSessions($assignment);
            $result['pending'] += $missing;
            $result['issues'][] = $this->assignmentLabel($assignment)
                . ' : créneau structurel absent.';
            return;
        }

        $desired = $this->desiredSessions($assignment);

        /*
         * 1) On commence par les Schedule déjà explicitement liés
         * à cette affectation via le pivot multi-séances.
         */
        $linked = $this->linkedWeeklySchedules($assignment);

        /*
         * Si le nombre a été diminué (ex. 3 -> 2), on retire les liens en
         * trop. Une séance AUTO exclusive peut être supprimée ; un planning
         * manuel ou partagé n'est jamais supprimé, seulement détaché.
         */
        if ($linked->count() > $desired) {
            $extras = $linked
                ->sortByDesc(function (Schedule $schedule) {
                    return sprintf(
                        '%d|%s',
                        (int) $schedule->day_of_week,
                        Carbon::parse($schedule->start_time)->format('H:i:s')
                    );
                })
                ->take($linked->count() - $desired);

            foreach ($extras as $schedule) {
                $this->detachSchedule(
                    $assignment,
                    $schedule,
                    $professor,
                    true
                );
                $result['removed']++;
            }

            $linked = $this->linkedWeeklySchedules($assignment);
        }

        $keptScheduleIds = collect();

        foreach ($linked as $schedule) {
            if ($keptScheduleIds->count() >= $desired) {
                break;
            }

            if (
                $this->availabilityCoversSchedule(
                    $availabilities,
                    $schedule
                )
                && !$this->hasConflict(
                    $professor,
                    $assignment,
                    $this->candidateFromSchedule($schedule, $slotCode),
                    $professorAssignments,
                    $schedule->id
                )
            ) {
                $this->attachSchedule(
                    $assignment,
                    $schedule,
                    $professor
                );
                $keptScheduleIds->push((int) $schedule->id);
                $result['reused']++;
                continue;
            }

            /*
             * Une séance auto appartenant uniquement à ce professeur peut
             * être déplacée vers une nouvelle disponibilité.
             */
            if ($this->scheduleCanBeAutoRescheduled($schedule, $professor)) {
                $candidate = $this->firstAvailableCandidate(
                    $professor,
                    $assignment,
                    $slotCode,
                    $availabilities,
                    $professorAssignments,
                    $schedule->id
                );

                if ($candidate) {
                    $this->rescheduleExistingSchedule(
                        $schedule,
                        $professor,
                        $candidate
                    );

                    $this->attachSchedule(
                        $assignment,
                        $schedule,
                        $professor
                    );

                    $keptScheduleIds->push((int) $schedule->id);
                    $result['rescheduled']++;
                    continue;
                }
            }

            /*
             * Si la disponibilité a changé et qu'on ne peut pas déplacer la
             * séance en toute sécurité, on retire uniquement le LIEN du prof.
             * Le Schedule lui-même est conservé pour ne rien perdre.
             */
            $this->detachSchedule(
                $assignment,
                $schedule,
                $professor,
                false
            );
        }

        /*
         * 2) On tente de réutiliser des lignes Schedule déjà existantes pour
         * le même parcours/slot, mais pas encore liées à cette affectation.
         */
        if ($keptScheduleIds->count() < $desired) {
            $matching = $this->matchingWeeklySchedulesForAssignment(
                $assignment,
                $slotCode
            );

            foreach ($matching as $schedule) {
                if ($keptScheduleIds->count() >= $desired) {
                    break;
                }

                if ($keptScheduleIds->contains((int) $schedule->id)) {
                    continue;
                }

                if ($this->assignmentAlreadyLinkedToSchedule(
                    $assignment,
                    $schedule
                )) {
                    continue;
                }

                if (
                    $this->availabilityCoversSchedule(
                        $availabilities,
                        $schedule
                    )
                    && !$this->hasConflict(
                        $professor,
                        $assignment,
                        $this->candidateFromSchedule(
                            $schedule,
                            $slotCode
                        ),
                        $professorAssignments,
                        $schedule->id
                    )
                ) {
                    $this->attachSchedule(
                        $assignment,
                        $schedule,
                        $professor
                    );

                    $keptScheduleIds->push((int) $schedule->id);
                    $result['reused']++;
                    continue;
                }

                if ($this->scheduleCanBeAutoRescheduled(
                    $schedule,
                    $professor
                )) {
                    $candidate = $this->firstAvailableCandidate(
                        $professor,
                        $assignment,
                        $slotCode,
                        $availabilities,
                        $professorAssignments,
                        $schedule->id
                    );

                    if ($candidate) {
                        $this->rescheduleExistingSchedule(
                            $schedule,
                            $professor,
                            $candidate
                        );

                        $this->attachSchedule(
                            $assignment,
                            $schedule,
                            $professor
                        );

                        $keptScheduleIds->push((int) $schedule->id);
                        $result['rescheduled']++;
                    }
                }
            }
        }

        /*
         * 3) Il manque encore des séances : on crée autant de lignes Schedule
         * qu'il faut, chacune sur une disponibilité différente sans conflit.
         */
        while ($keptScheduleIds->count() < $desired) {
            $candidate = $this->firstAvailableCandidate(
                $professor,
                $assignment,
                $slotCode,
                $availabilities,
                $professorAssignments
            );

            if (!$candidate) {
                break;
            }

            $schedule = $this->createSchedule(
                $professor,
                $assignment,
                $slotCode,
                $candidate
            );

            $this->attachSchedule(
                $assignment,
                $schedule,
                $professor
            );

            $keptScheduleIds->push((int) $schedule->id);
            $result['created']++;
        }

        $missing = max(
            0,
            $desired - $keptScheduleIds->count()
        );

        if ($missing > 0) {
            $result['pending'] += $missing;
            $result['issues'][] = $this->assignmentLabel($assignment)
                . ' : '
                . $missing
                . ' séance(s) sur '
                . $desired
                . ' reste(nt) à planifier faute de disponibilité libre.';
        }

        $this->syncLegacyAssignmentTime($assignment);
    }

    private function desiredSessions(ProfAssignment $assignment): int
    {
        return max(
            1,
            min(7, (int) ($assignment->weekly_sessions ?: 1))
        );
    }

    private function linkedWeeklySchedules(
        ProfAssignment $assignment
    ): Collection {
        return $assignment
            ->schedules()
            ->active()
            ->where('recurrence', Schedule::RECURRENCE_WEEKLY)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    private function matchingWeeklySchedulesForAssignment(
        ProfAssignment $assignment,
        string $slotCode
    ): Collection {
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
                    ->orWhereDate(
                        'valid_until',
                        '>=',
                        $today->toDateString()
                    );
            })
            ->orderByRaw(
                'CASE WHEN prof_id = ? THEN 0 '
                . 'WHEN prof_id IS NULL THEN 1 ELSE 2 END',
                [$assignment->prof_id]
            )
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    private function assignmentAlreadyLinkedToSchedule(
        ProfAssignment $assignment,
        Schedule $schedule
    ): bool {
        return DB::table('prof_assignment_schedule')
            ->where('prof_assignment_id', $assignment->id)
            ->where('schedule_id', $schedule->id)
            ->exists();
    }

    private function attachSchedule(
        ProfAssignment $assignment,
        Schedule $schedule,
        User $professor
    ): void {
        $assignment->schedules()->syncWithoutDetaching([
            $schedule->id,
        ]);

        /*
         * schedules.prof_id reste seulement le professeur principal/fallback.
         * S'il est déjà occupé par un autre professeur, on ne l'écrase pas :
         * le pivot conserve correctement tous les professeurs liés.
         */
        if (empty($schedule->prof_id)) {
            $schedule->update([
                'prof_id' => $professor->id,
            ]);
        }
    }

    private function detachSchedule(
        ProfAssignment $assignment,
        Schedule $schedule,
        User $professor,
        bool $deleteAutoIfExclusive
    ): void {
        $assignment->schedules()->detach($schedule->id);

        $remainingAssignmentIds = DB::table('prof_assignment_schedule')
            ->where('schedule_id', $schedule->id)
            ->pluck('prof_assignment_id');

        $remainingProfessorIds = $remainingAssignmentIds->isEmpty()
            ? collect()
            : ProfAssignment::query()
                ->whereIn('id', $remainingAssignmentIds)
                ->pluck('prof_id')
                ->unique()
                ->values();

        if (
            $deleteAutoIfExclusive
            && $remainingAssignmentIds->isEmpty()
            && strpos(
                (string) $schedule->notes,
                self::AUTO_NOTE_PREFIX
            ) !== false
        ) {
            $schedule->delete();
            return;
        }

        if ((int) $schedule->prof_id === (int) $professor->id) {
            $schedule->update([
                'prof_id' => $remainingProfessorIds->first(),
            ]);
        }
    }

    private function createSchedule(
        User $professor,
        ProfAssignment $assignment,
        string $slotCode,
        array $candidate
    ): Schedule {
        return Schedule::create([
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
                . ' Affectation #'
                . $assignment->id
                . ' — séance créée automatiquement depuis les disponibilités de '
                . $professor->name
                . '.',
        ]);
    }

    private function firstAvailableCandidate(
        User $professor,
        ProfAssignment $assignment,
        string $slotCode,
        Collection $availabilities,
        Collection $professorAssignments,
        ?int $ignoreScheduleId = null
    ): ?array {
        /*
         * Pour plusieurs séances d'un même I2/D1/..., on préfère les
         * répartir sur des jours différents. Si ce n'est pas possible, une
         * deuxième plage du même jour reste autorisée.
         */
        $usedDays = Schema::hasTable('prof_assignment_schedule')
            ? $assignment->schedules()
                ->active()
                ->where('recurrence', Schedule::RECURRENCE_WEEKLY)
                ->when(
                    $ignoreScheduleId,
                    fn ($query) =>
                        $query->where('schedules.id', '<>', $ignoreScheduleId)
                )
                ->pluck('day_of_week')
                ->map(fn ($day) => (int) $day)
                ->unique()
                ->values()
            : collect();

        $orderedAvailabilities = $availabilities
            ->sortBy(function ($availability) use ($usedDays) {
                $day = (int) $availability->day_of_week;
                $time = Carbon::parse(
                    $availability->start_time
                )->format('H:i:s');

                return sprintf(
                    '%d|%d|%s',
                    $usedDays->contains($day) ? 1 : 0,
                    $day,
                    $time
                );
            })
            ->values();

        foreach ($orderedAvailabilities as $availability) {
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
                $professorAssignments,
                $ignoreScheduleId
            )) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Conflits bloquants :
     * - le professeur est déjà réellement lié à une autre séance ;
     * - le même groupe classe + même code I2/D1/... est déjà occupé au même
     *   moment.
     */
    private function hasConflict(
        User $professor,
        ProfAssignment $assignment,
        array $candidate,
        Collection $professorAssignments,
        ?int $ignoreScheduleId = null
    ): bool {
        $schedules = Schedule::query()
            ->active()
            ->with(['classRoom'])
            ->get();

        foreach ($schedules as $existing) {
            if (
                $ignoreScheduleId
                && (int) $existing->id === (int) $ignoreScheduleId
            ) {
                continue;
            }

            if (!$this->scheduleCanOverlapWeeklyCandidate(
                $existing,
                $candidate['anchor_date'],
                (int) $candidate['day_of_week']
            )) {
                continue;
            }

            $existingStart = Carbon::parse(
                $existing->start_time
            )->format('H:i:s');
            $existingEnd = Carbon::parse(
                $existing->end_time
            )->format('H:i:s');
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

            $sameTeacher = $this
                ->actualProfessorIdsForSchedule($existing)
                ->contains((int) $professor->id);

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

    private function actualProfessorIdsForSchedule(
        Schedule $schedule
    ): Collection {
        $ids = collect();

        if (Schema::hasTable('prof_assignment_schedule')) {
            $assignmentIds = DB::table('prof_assignment_schedule')
                ->where('schedule_id', $schedule->id)
                ->pluck('prof_assignment_id');

            if ($assignmentIds->isNotEmpty()) {
                $ids = $ids->merge(
                    ProfAssignment::query()
                        ->whereIn('id', $assignmentIds)
                        ->pluck('prof_id')
                );
            }
        }

        if ($schedule->prof_id) {
            $ids->push((int) $schedule->prof_id);
        }

        /*
         * Fallback anciennes données : day/time de ProfAssignment.
         */
        $levelId = (int) (
            $schedule->level_id
            ?: optional($schedule->classRoom)->level_id
        );

        $slotCode = strtoupper(
            trim((string) $schedule->slot_code)
        );

        if (
            $schedule->start_time
            && $schedule->end_time
            && $schedule->day_of_week
        ) {
            $start = Carbon::parse($schedule->start_time)->format('H:i');
            $end = Carbon::parse($schedule->end_time)->format('H:i');

            $legacyIds = ProfAssignment::query()
                ->with('classSlot')
                ->where('subject_id', $schedule->subject_id)
                ->where('level_id', $levelId)
                ->where('class_id', $schedule->class_id)
                ->where('day_of_week', $schedule->day_of_week)
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->get()
                ->filter(function (ProfAssignment $assignment) use (
                    $slotCode,
                    $start,
                    $end
                ) {
                    $assignmentSlot = $this->assignmentSlotCode($assignment);

                    if (
                        $slotCode !== ''
                        && $assignmentSlot !== ''
                        && $slotCode !== $assignmentSlot
                    ) {
                        return false;
                    }

                    return Carbon::parse($assignment->start_time)->format('H:i') === $start
                        && Carbon::parse($assignment->end_time)->format('H:i') === $end;
                })
                ->pluck('prof_id');

            $ids = $ids->merge($legacyIds);
        }

        return $ids
            ->filter()
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values();
    }

    private function scheduleCanOverlapWeeklyCandidate(
        Schedule $existing,
        Carbon $candidateValidFrom,
        int $candidateDayOfWeek
    ): bool {
        $recurrence = $existing->recurrence
            ?: Schedule::RECURRENCE_ONCE;

        if ($recurrence === Schedule::RECURRENCE_WEEKLY) {
            $existingDay = (int) (
                $existing->day_of_week
                ?: Carbon::parse(
                    $existing->date ?: $existing->start_time
                )->dayOfWeekIso
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

    private function scheduleCanBeAutoRescheduled(
        Schedule $schedule,
        User $professor
    ): bool {
        $actualProfessorIds = $this
            ->actualProfessorIdsForSchedule($schedule);

        $otherProfessorIds = $actualProfessorIds
            ->reject(function ($professorId) use ($professor) {
                return (int) $professorId === (int) $professor->id;
            })
            ->values();

        if ($otherProfessorIds->isNotEmpty()) {
            return false;
        }

        if (empty($schedule->prof_id)) {
            return true;
        }

        return
            (int) $schedule->prof_id === (int) $professor->id
            && strpos(
                (string) $schedule->notes,
                self::AUTO_NOTE_PREFIX
            ) !== false;
    }

    private function rescheduleExistingSchedule(
        Schedule $schedule,
        User $professor,
        array $candidate
    ): void {
        $notes = trim((string) $schedule->notes);

        if (strpos($notes, self::AUTO_NOTE_PREFIX) === false) {
            $notes = trim(
                $notes
                . ' '
                . self::AUTO_NOTE_PREFIX
                . ' Créneau libre repositionné automatiquement.'
            );
        }

        $schedule->update([
            'prof_id' => $schedule->prof_id ?: $professor->id,
            'start_time' => $candidate['start_at']->format('Y-m-d H:i:s'),
            'end_time' => $candidate['end_at']->format('Y-m-d H:i:s'),
            'date' => $candidate['anchor_date']->toDateString(),
            'day_of_week' => $candidate['day_of_week'],
            'recurrence' => Schedule::RECURRENCE_WEEKLY,
            'valid_from' => $candidate['anchor_date']->toDateString(),
            'valid_until' => null,
            'status' => Schedule::STATUS_ACTIVE,
            'notes' => $notes,
        ]);
    }

    private function availabilityCoversSchedule(
        Collection $availabilities,
        Schedule $schedule
    ): bool {
        $dayOfWeek = (int) (
            $schedule->day_of_week
            ?: Carbon::parse(
                $schedule->date ?: $schedule->start_time
            )->dayOfWeekIso
        );

        $dayAvailabilities = $availabilities
            ->where('day_of_week', $dayOfWeek)
            ->sortBy(function ($availability) {
                return Carbon::parse(
                    $availability->start_time
                )->format('H:i:s');
            })
            ->values();

        if ($dayAvailabilities->isEmpty()) {
            return false;
        }

        $referenceDate = Carbon::parse(
            $schedule->start_time
        )->format('Y-m-d');
        $merged = [];

        foreach ($dayAvailabilities as $availability) {
            $start = Carbon::parse(
                $referenceDate
                . ' '
                . Carbon::parse(
                    $availability->start_time
                )->format('H:i:s')
            );

            $end = Carbon::parse(
                $referenceDate
                . ' '
                . Carbon::parse(
                    $availability->end_time
                )->format('H:i:s')
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

    private function candidateFromSchedule(
        Schedule $schedule,
        string $slotCode
    ): array {
        $anchorDate = Carbon::parse(
            $schedule->date
            ?: $schedule->valid_from
            ?: $schedule->start_time
        )->startOfDay();

        return [
            'day_of_week' => (int) (
                $schedule->day_of_week
                ?: $anchorDate->dayOfWeekIso
            ),
            'anchor_date' => $anchorDate,
            'start_at' => Carbon::parse($schedule->start_time),
            'end_at' => Carbon::parse($schedule->end_time),
            'slot_code' => $slotCode,
        ];
    }

    /**
     * Les anciens champs day_of_week/start_time/end_time restent remplis avec
     * la PREMIÈRE séance uniquement pour la rétrocompatibilité. La vraie
     * source multi-séances est désormais prof_assignment_schedule.
     */
    private function syncLegacyAssignmentTime(
        ProfAssignment $assignment
    ): void {
        $first = $this->linkedWeeklySchedules($assignment)->first();

        if (!$first) {
            $assignment->update([
                'day_of_week' => null,
                'start_time' => null,
                'end_time' => null,
            ]);
            return;
        }

        $assignment->update([
            'day_of_week' => (int) $first->day_of_week,
            'start_time' => Carbon::parse(
                $first->start_time
            )->format('H:i:s'),
            'end_time' => Carbon::parse(
                $first->end_time
            )->format('H:i:s'),
        ]);
    }

    private function assignmentSlotCode(
        ProfAssignment $assignment
    ): string {
        return strtoupper(
            trim((string) optional($assignment->classSlot)->code)
        );
    }

    private function assignmentLabel(
        ProfAssignment $assignment
    ): string {
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
        $difference = (
            $dayOfWeek - $cursor->dayOfWeekIso + 7
        ) % 7;

        return $cursor->addDays($difference);
    }

    private function timesOverlap(
        string $existingStart,
        string $existingEnd,
        string $newStart,
        string $newEnd
    ): bool {
        return $newStart < $existingEnd
            && $newEnd > $existingStart;
    }

    private function emptyResult(string $issue): array
    {
        return [
            'assignments' => 0,
            'requested_sessions' => 0,
            'created' => 0,
            'reused' => 0,
            'rescheduled' => 0,
            'removed' => 0,
            'pending' => 0,
            'issues' => [$issue],
        ];
    }
}
