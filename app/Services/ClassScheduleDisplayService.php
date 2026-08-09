<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\ProfAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClassScheduleDisplayService
{
    /**
     * Liste publique complète : chaque planning actif apparaît une seule fois.
     * Les répétitions hebdomadaires ne sont donc pas dupliquées sur plusieurs semaines.
     */
    public function allPublicSchedules(): Collection
    {
        $today = now()->startOfDay();

        return $this->baseQuery()
            ->where(function (Builder $query) use ($today) {
                $query
                    ->where(function (Builder $weekly) use ($today) {
                        $weekly
                            ->where('recurrence', Schedule::RECURRENCE_WEEKLY)
                            ->where(function (Builder $validity) use ($today) {
                                $validity
                                    ->whereNull('valid_until')
                                    ->orWhereDate('valid_until', '>=', $today->toDateString());
                            });
                    })
                    ->orWhere(function (Builder $once) use ($today) {
                        $once
                            ->where(function (Builder $recurrence) {
                                $recurrence
                                    ->whereNull('recurrence')
                                    ->orWhere('recurrence', Schedule::RECURRENCE_ONCE);
                            })
                            ->where(function (Builder $date) use ($today) {
                                $date
                                    ->whereDate('date', '>=', $today->toDateString())
                                    ->orWhereDate('valid_from', '>=', $today->toDateString());
                            });
                    });
            })
            ->orderByRaw('COALESCE(day_of_week, 8) ASC')
            ->orderByRaw('TIME(start_time) ASC')
            ->get()
            ->map(function (Schedule $schedule) {
                return $this->formatPublicSchedule($schedule);
            })
            ->values();
    }

    /**
     * Prochaines occurrences publiques dans une période donnée.
     */
    public function forPublic(?Carbon $from = null, int $days = 14, ?int $limit = null): Collection
    {
        $from = ($from ?: now())->copy();
        $to = $from->copy()->addDays(max(1, $days));

        return $this->occurrences($this->baseQuery()->get(), $from, $to, $limit);
    }

    /**
     * Planning étudiant : uniquement les parcours affectés à l'étudiant
     * dans la table class_user (subject_id + class_id).
     */
    public function forStudent(
        User $student,
        ?Carbon $from = null,
        int $days = 14,
        ?int $limit = null,
        array $filters = []
    ): Collection {
        $from = ($from ?: now())->copy();
        $to = $from->copy()->addDays(max(1, $days));

        if (!Schema::hasTable('class_user')) {
            return collect();
        }

        $assignmentsQuery =
            DB::table('class_user')
                ->where(
                    'class_user.user_id',
                    $student->id
                );

        if (Schema::hasTable('class_slots')) {
            $assignmentsQuery
                ->leftJoin(
                    'class_slots',
                    'class_user.class_slot_id',
                    '=',
                    'class_slots.id'
                );
        }

        $assignments = $assignmentsQuery
            ->get(
                Schema::hasTable('class_slots')
                    ? [
                        'class_user.subject_id',
                        'class_user.class_id',
                        'class_slots.code as slot_code',
                    ]
                    : [
                        'class_user.subject_id',
                        'class_user.class_id',
                    ]
            );

        if ($assignments->isEmpty()) {
            return collect();
        }

        $query = $this->baseQuery();

        $scheduleHasSlotCode =
            Schema::hasColumn(
                'schedules',
                'slot_code'
            );

        $query->where(
            function (Builder $outer) use (
                $assignments,
                $scheduleHasSlotCode
            ) {
                foreach (
                    $assignments as $assignment
                ) {
                    $outer->orWhere(
                        function (
                            Builder $path
                        ) use (
                            $assignment,
                            $scheduleHasSlotCode
                        ) {
                            $path->where(
                                'class_id',
                                $assignment->class_id
                            );

                            if (
                                !empty(
                                    $assignment->subject_id
                                )
                            ) {
                                $path->where(
                                    'subject_id',
                                    $assignment->subject_id
                                );
                            }

                            /*
                             * Le créneau étudiant est structurel.
                             * Lorsqu'un horaire D1/I1/A1 est créé
                             * plus tard dans /admin/schedule,
                             * le planning étudiant le retrouve
                             * automatiquement par slot_code.
                             */
                            if (
                                $scheduleHasSlotCode
                                && !empty(
                                    $assignment->slot_code
                                    ?? null
                                )
                            ) {
                                $path->where(
                                    'slot_code',
                                    $assignment->slot_code
                                );
                            }
                        }
                    );
                }
            }
        );

        $subjectId = !empty($filters['subject_id'])
            ? (int) $filters['subject_id']
            : null;
        $levelId = !empty($filters['level_id'])
            ? (int) $filters['level_id']
            : null;
        $classId = !empty($filters['class_id'])
            ? (int) $filters['class_id']
            : null;

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        if ($levelId) {
            $query->where(function (Builder $levelQuery) use ($levelId) {
                $levelQuery
                    ->where('level_id', $levelId)
                    ->orWhereHas('classRoom', function (Builder $classQuery) use ($levelId) {
                        $classQuery->where('level_id', $levelId);
                    });
            });
        }

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $slotCode = !empty($filters['slot_code'])
            ? trim((string) $filters['slot_code'])
            : null;

        if (
            $slotCode
            && Schema::hasColumn('schedules', 'slot_code')
        ) {
            $query->whereRaw(
                'UPPER(TRIM(slot_code)) = ?',
                [strtoupper($slotCode)]
            );
        }

        return $this->occurrences($query->get(), $from, $to, $limit);
    }

    /**
     * Planning professeur : uniquement les créneaux structurels
     * qui lui ont été affectés par l'administration.
     */
    public function forProfessor(
        User $professor,
        ?Carbon $from = null,
        int $days = 14,
        ?int $limit = null,
        array $filters = []
    ): Collection {
        $from = ($from ?: now())->copy();
        $to = $from->copy()->addDays(
            max(1, $days)
        );

        if (
            !Schema::hasTable(
                'prof_assignments'
            )
        ) {
            return collect();
        }

        $assignmentQuery =
            ProfAssignment::query()
                ->where(
                    'prof_id',
                    $professor->id
                );

        if (
            Schema::hasColumn(
                'prof_assignments',
                'class_slot_id'
            )
        ) {
            $assignmentQuery
                ->with('classSlot')
                ->whereNotNull(
                    'class_slot_id'
                );
        }

        $assignments =
            $assignmentQuery->get();

        if ($assignments->isEmpty()) {
            return collect();
        }

        $query = $this->baseQuery();

        $scheduleHasSlotCode =
            Schema::hasColumn(
                'schedules',
                'slot_code'
            );

        $query->where(
            function (
                Builder $outer
            ) use (
                $assignments,
                $scheduleHasSlotCode
            ) {
                foreach (
                    $assignments as $assignment
                ) {
                    $outer->orWhere(
                        function (
                            Builder $path
                        ) use (
                            $assignment,
                            $scheduleHasSlotCode
                        ) {
                            $path->where(
                                'subject_id',
                                $assignment->subject_id
                            )
                            ->where(
                                'level_id',
                                $assignment->level_id
                            )
                            ->where(
                                'class_id',
                                $assignment->class_id
                            );

                            if (
                                $scheduleHasSlotCode
                                && $assignment
                                    ->classSlot
                                && trim(
                                    (string) $assignment
                                        ->classSlot
                                        ->code
                                ) !== ''
                            ) {
                                $path->whereRaw(
                                    'UPPER(TRIM(slot_code)) = ?',
                                    [
                                        strtoupper(
                                            trim(
                                                (string)
                                                $assignment
                                                    ->classSlot
                                                    ->code
                                            )
                                        ),
                                    ]
                                );
                            }
                        }
                    );
                }
            }
        );

        $subjectId = !empty(
            $filters['subject_id']
        )
            ? (int) $filters['subject_id']
            : null;

        $levelId = !empty(
            $filters['level_id']
        )
            ? (int) $filters['level_id']
            : null;

        $classId = !empty(
            $filters['class_id']
        )
            ? (int) $filters['class_id']
            : null;

        $slotCode = !empty(
            $filters['slot_code']
        )
            ? strtoupper(
                trim(
                    (string)
                    $filters['slot_code']
                )
            )
            : null;

        if ($subjectId) {
            $query->where(
                'subject_id',
                $subjectId
            );
        }

        if ($levelId) {
            $query->where(
                'level_id',
                $levelId
            );
        }

        if ($classId) {
            $query->where(
                'class_id',
                $classId
            );
        }

        if (
            $slotCode
            && $scheduleHasSlotCode
        ) {
            $query->whereRaw(
                'UPPER(TRIM(slot_code)) = ?',
                [$slotCode]
            );
        }

        return $this->occurrences(
            $query->get(),
            $from,
            $to,
            $limit
        );
    }

    private function baseQuery(): Builder
    {
        return Schedule::query()
            ->active()
            ->with([
                'subjectModel',
                'level',
                'classRoom.level',
                'prof',
                'room',
            ]);
    }

    private function formatPublicSchedule(Schedule $schedule): array
    {
        $subject = optional($schedule->subjectModel)->name
            ?: $schedule->subject
            ?: 'Matière';
        $level = optional($schedule->level)->name
            ?: optional(optional($schedule->classRoom)->level)->name
            ?: 'Niveau';
        $className = optional($schedule->classRoom)->name ?: 'Classe';
        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);
        $duration = max(1, $start->diffInMinutes($end));

        $dayOfWeek = (int) ($schedule->day_of_week ?: $start->dayOfWeekIso);
        $dayLabel = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ][$dayOfWeek] ?? '-';

        $recurrence = $schedule->recurrence ?: Schedule::RECURRENCE_ONCE;
        $dateValue = $schedule->date ?: $schedule->valid_from;

        return [
            'schedule_id' => $schedule->id,
            'day_of_week' => $dayOfWeek,
            'day_label' => $dayLabel,
            'start_label' => $start->format('H:i'),
            'end_label' => $end->format('H:i'),
            'time_label' => $start->format('H:i') . ' – ' . $end->format('H:i'),
            'slot_label' => $dayLabel . ' · ' . $start->format('H:i') . ' – ' . $end->format('H:i'),
            'duration_label' => $this->formatDurationLabel($duration),
            'subject_id' => $schedule->subject_id ? (int) $schedule->subject_id : null,
            'level_id' => $schedule->level_id
                ? (int) $schedule->level_id
                : (optional($schedule->classRoom)->level_id ? (int) optional($schedule->classRoom)->level_id : null),
            'class_id' => $schedule->class_id ? (int) $schedule->class_id : null,
            'subject' => $subject,
            'level' => $level,
            'class_name' => $className,
            'slot_code' => trim((string) ($schedule->slot_code ?? '')),
            'room' => optional($schedule->room)->name ?: 'Salle à confirmer',
            'path' => collect([
                $subject,
                $level,
                $className,
                trim((string) ($schedule->slot_code ?? '')) ?: null,
            ])->filter()->implode(' → '),
            'full_path' => collect([
                $subject,
                $level,
                $className,
                $dayLabel . ' · ' . $start->format('H:i') . ' – ' . $end->format('H:i'),
            ])->filter()->implode(' → '),
            'teacher' => optional($schedule->prof)->name ?: 'Professeur à confirmer',
            'recurrence' => $recurrence,
            'recurrence_label' => $recurrence === Schedule::RECURRENCE_WEEKLY
                ? 'Chaque semaine'
                : 'Séance unique',
            'date_label' => $recurrence === Schedule::RECURRENCE_ONCE && $dateValue
                ? Carbon::parse($dateValue)->locale('fr')->isoFormat('D MMMM YYYY')
                : null,
        ];
    }

    private function occurrences(
        Collection $schedules,
        Carbon $from,
        Carbon $to,
        ?int $limit
    ): Collection {
        $items = collect();

        foreach ($schedules as $schedule) {
            if (($schedule->recurrence ?: Schedule::RECURRENCE_ONCE) === Schedule::RECURRENCE_WEEKLY) {
                $this->appendWeeklyOccurrences($items, $schedule, $from, $to);
            } else {
                $this->appendSingleOccurrence($items, $schedule, $from, $to);
            }
        }

        $items = $items
            ->sortBy(function (array $item) {
                return $item['start']->timestamp;
            })
            ->values();

        if ($limit !== null && $limit > 0) {
            $items = $items->take($limit)->values();
        }

        return $items;
    }

    private function appendWeeklyOccurrences(
        Collection $items,
        Schedule $schedule,
        Carbon $from,
        Carbon $to
    ): void {
        $dayOfWeek = (int) $schedule->day_of_week;

        if ($dayOfWeek < 1 || $dayOfWeek > 7 || !$schedule->start_time || !$schedule->end_time) {
            return;
        }

        $cursor = $from->copy()->startOfDay();
        $offset = ($dayOfWeek - $cursor->dayOfWeekIso + 7) % 7;
        $cursor->addDays($offset);

        while ($cursor->lte($to)) {
            if ($this->isInsideValidityPeriod($schedule, $cursor)) {
                $start = $cursor->copy()->setTimeFromTimeString(
                    Carbon::parse($schedule->start_time)->format('H:i:s')
                );
                $end = $cursor->copy()->setTimeFromTimeString(
                    Carbon::parse($schedule->end_time)->format('H:i:s')
                );

                if ($end->gte($from) && $start->lte($to)) {
                    $items->push($this->formatOccurrence($schedule, $start, $end));
                }
            }

            $cursor->addWeek();
        }
    }

    private function appendSingleOccurrence(
        Collection $items,
        Schedule $schedule,
        Carbon $from,
        Carbon $to
    ): void {
        if (!$schedule->start_time || !$schedule->end_time) {
            return;
        }

        $dateValue = $schedule->date ?: $schedule->valid_from;

        if (!$dateValue) {
            return;
        }

        $date = Carbon::parse($dateValue)->startOfDay();
        $start = $date->copy()->setTimeFromTimeString(
            Carbon::parse($schedule->start_time)->format('H:i:s')
        );
        $end = $date->copy()->setTimeFromTimeString(
            Carbon::parse($schedule->end_time)->format('H:i:s')
        );

        if ($end->gte($from) && $start->lte($to) && $this->isInsideValidityPeriod($schedule, $date)) {
            $items->push($this->formatOccurrence($schedule, $start, $end));
        }
    }

    private function isInsideValidityPeriod(Schedule $schedule, Carbon $date): bool
    {
        if ($schedule->valid_from && $date->lt(Carbon::parse($schedule->valid_from)->startOfDay())) {
            return false;
        }

        if ($schedule->valid_until && $date->gt(Carbon::parse($schedule->valid_until)->endOfDay())) {
            return false;
        }

        return true;
    }

    private function formatOccurrence(Schedule $schedule, Carbon $start, Carbon $end): array
    {
        $subject = optional($schedule->subjectModel)->name
            ?: $schedule->subject
            ?: 'Matière';
        $level = optional($schedule->level)->name
            ?: optional(optional($schedule->classRoom)->level)->name
            ?: 'Niveau';
        $className = optional($schedule->classRoom)->name ?: 'Classe';

        $duration = max(1, (int) $start->diffInMinutes($end));

        return [
            'schedule_id' => $schedule->id,
            'start' => $start,
            'end' => $end,
            'date_key' => $start->format('Y-m-d'),
            'date_label' => ucfirst($start->locale('fr')->isoFormat('dddd D MMMM')),
            'day_short' => ucfirst($start->locale('fr')->isoFormat('ddd')),
            'day_number' => $start->format('d'),
            'time_label' => $start->format('H:i') . ' – ' . $end->format('H:i'),
            'slot_label' => ucfirst($start->locale('fr')->isoFormat('dddd'))
                . ' · ' . $start->format('H:i') . ' – ' . $end->format('H:i'),
            'start_label' => $start->format('H:i'),
            'end_label' => $end->format('H:i'),
            'duration_label' => $this->formatDurationLabel($duration),
            'subject_id' => $schedule->subject_id ? (int) $schedule->subject_id : null,
            'level_id' => $schedule->level_id
                ? (int) $schedule->level_id
                : (optional($schedule->classRoom)->level_id ? (int) optional($schedule->classRoom)->level_id : null),
            'class_id' => $schedule->class_id ? (int) $schedule->class_id : null,
            'subject' => $subject,
            'level' => $level,
            'class_name' => $className,
            'slot_code' => trim((string) ($schedule->slot_code ?? '')),
            'room' => optional($schedule->room)->name ?: 'Salle à confirmer',
            'path' => collect([
                $subject,
                $level,
                $className,
                trim((string) ($schedule->slot_code ?? '')) ?: null,
            ])->filter()->implode(' → '),
            'full_path' => collect([
                $subject,
                $level,
                $className,
                ucfirst($start->locale('fr')->isoFormat('dddd'))
                    . ' · ' . $start->format('H:i') . ' – ' . $end->format('H:i'),
            ])->filter()->implode(' → '),
            'teacher' => optional($schedule->prof)->name ?: 'Professeur à confirmer',
        ];
    }

    private function formatDurationLabel(int $minutes): string
    {
        $minutes = max(1, $minutes);
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours === 0) {
            return $minutes . ' min';
        }

        $hourLabel = $hours === 1 ? '1 heure' : $hours . ' heures';

        return $remainingMinutes > 0
            ? $hourLabel . ' ' . $remainingMinutes . ' min'
            : $hourLabel;
    }
}
