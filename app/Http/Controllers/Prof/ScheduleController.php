<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ProfAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $assignments =
            $this->scheduledAssignments();

        $classes =
            ClassRoom::query()
                ->whereIn(
                    'id',
                    $this->assignedClassIds()
                )
                ->orderBy('name')
                ->get();

        return view(
            'prof.schedule',
            compact(
                'classes',
                'assignments'
            )
        );
    }

    public function classes()
    {
        $classes =
            ClassRoom::query()
                ->whereIn(
                    'id',
                    $this->assignedClassIds()
                )
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                ]);

        return response()->json(
            $classes
        );
    }

    /**
     * Retourne les occurrences hebdomadaires
     * enregistrées par l'administration.
     */
    public function data(Request $request)
    {
        $rangeStart =
            Carbon::parse(
                $request->query(
                    'start',
                    now()
                        ->startOfWeek()
                        ->toIso8601String()
                )
            )->startOfDay();

        $rangeEnd =
            Carbon::parse(
                $request->query(
                    'end',
                    now()
                        ->endOfWeek()
                        ->addDay()
                        ->toIso8601String()
                )
            )->startOfDay();

        if ($rangeEnd->lessThanOrEqualTo(
            $rangeStart
        )) {
            $rangeEnd =
                $rangeStart
                    ->copy()
                    ->addWeek();
        }

        $query =
            ProfAssignment::query()
                ->with([
                    'subject',
                    'level',
                    'classRoom',
                ])
                ->where(
                    'prof_id',
                    auth()->id()
                )
                ->whereNotNull(
                    'day_of_week'
                )
                ->whereNotNull(
                    'start_time'
                )
                ->whereNotNull(
                    'end_time'
                );

        if ($request->filled('class_id')) {
            abort_unless(
                $this
                    ->assignedClassIds()
                    ->contains(
                        (int) $request->class_id
                    ),
                403
            );

            $query->where(
                'class_id',
                $request->class_id
            );
        }

        $assignments =
            $query
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();

        $events = collect();

        foreach ($assignments as $assignment) {
            $firstOccurrence =
                $rangeStart
                    ->copy()
                    ->addDays(
                        (
                            (int) $assignment
                                ->day_of_week
                            - $rangeStart
                                ->dayOfWeekIso
                            + 7
                        ) % 7
                    );

            $startClock =
                Carbon::parse(
                    $assignment->start_time
                )->format('H:i:s');

            $endClock =
                Carbon::parse(
                    $assignment->end_time
                )->format('H:i:s');

            for (
                $date =
                    $firstOccurrence->copy();
                $date->lessThan($rangeEnd);
                $date->addWeek()
            ) {
                $start =
                    $date
                        ->copy()
                        ->setTimeFromTimeString(
                            $startClock
                        );

                $end =
                    $date
                        ->copy()
                        ->setTimeFromTimeString(
                            $endClock
                        );

                $color =
                    $this->eventColor(
                        (int) $assignment
                            ->subject_id
                    );

                $events->push([
                    'id' =>
                        $assignment->id
                        . '-'
                        . $date->format(
                            'Ymd'
                        ),
                    'title' =>
                        (
                            $assignment
                                ->subject
                                ?->name
                            ?? 'Matière'
                        )
                        . ' ('
                        . (
                            $assignment
                                ->classRoom
                                ?->name
                            ?? 'Classe'
                        )
                        . ')',
                    'start' =>
                        $start
                            ->toIso8601String(),
                    'end' =>
                        $end
                            ->toIso8601String(),
                    'backgroundColor' =>
                        $color,
                    'borderColor' =>
                        $color,
                    'textColor' =>
                        '#ffffff',
                    'extendedProps' => [
                        'assignment_id' =>
                            $assignment->id,
                        'subject' =>
                            $assignment
                                ->subject
                                ?->name,
                        'level' =>
                            $assignment
                                ->level
                                ?->name,
                        'class' =>
                            $assignment
                                ->classRoom
                                ?->name,
                        'day' =>
                            $assignment
                                ->day_label,
                        'time' =>
                            $assignment
                                ->time_range_label,
                    ],
                ]);
            }
        }

        return response()->json(
            $events->values()
        );
    }

    /**
     * Le planning du professeur est géré
     * uniquement par l'administration.
     */
    public function store(Request $request)
    {
        abort(
            403,
            'Le planning est géré '
            . 'par l’administration.'
        );
    }

    public function update(Request $request)
    {
        abort(
            403,
            'Le planning est géré '
            . 'par l’administration.'
        );
    }

    public function destroy($id)
    {
        abort(
            403,
            'Le planning est géré '
            . 'par l’administration.'
        );
    }

    private function assignedClassIds()
    {
        return ProfAssignment::query()
            ->where(
                'prof_id',
                auth()->id()
            )
            ->pluck('class_id')
            ->unique()
            ->values();
    }

    private function scheduledAssignments()
    {
        return ProfAssignment::query()
            ->with([
                'subject',
                'level',
                'classRoom',
            ])
            ->where(
                'prof_id',
                auth()->id()
            )
            ->whereNotNull(
                'day_of_week'
            )
            ->whereNotNull(
                'start_time'
            )
            ->whereNotNull(
                'end_time'
            )
            ->orderBy(
                'day_of_week'
            )
            ->orderBy(
                'start_time'
            )
            ->get();
    }

    private function eventColor(
        int $subjectId
    ): string {
        $colors = [
            '#4F6FF5',
            '#7C3AED',
            '#0891B2',
            '#16A34A',
            '#D97706',
            '#DC2626',
        ];

        return $colors[
            $subjectId
            % count($colors)
        ];
    }
}
