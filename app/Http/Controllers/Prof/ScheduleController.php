<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassSlot;
use App\Services\ClassScheduleDisplayService;
use App\Services\ProfessorPathService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    private ProfessorPathService $profPaths;

    public function __construct(
        ProfessorPathService $profPaths
    ) {
        $this->profPaths = $profPaths;
    }

    public function index(
        Request $request,
        ClassScheduleDisplayService $scheduleService
    ) {
        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $filters =
            $this->profPaths
                ->selectedFilters($request);

        $slot = !empty(
            $filters['selectedSlotId']
        )
            ? ClassSlot::query()->find(
                $filters['selectedSlotId']
            )
            : null;

        if (
            $slot
            && !$this->profPaths->ownsSlot(
                auth()->id(),
                (int) $slot->id
            )
        ) {
            abort(403);
        }

        $scheduleFilters = array_filter([
            'subject_id' =>
                $filters['selectedSubjectId'],
            'level_id' =>
                $filters['selectedLevelId'],
            'class_id' =>
                $filters['selectedClassId'],
            'slot_code' =>
                $slot?->code,
        ]);

        $occurrences =
            $scheduleService
                ->forProfessor(
                    auth()->user(),
                    now()->startOfDay(),
                    35,
                    null,
                    $scheduleFilters
                );

        $classes = ClassRoom::query()
            ->whereIn(
                'id',
                $this->profPaths
                    ->assignments(
                        auth()->id()
                    )
                    ->pluck('class_id')
                    ->unique()
            )
            ->orderBy('name')
            ->get();

        return view(
            'prof.schedule',
            array_merge(
                compact(
                    'profHierarchy',
                    'occurrences',
                    'classes'
                ),
                $filters
            )
        );
    }

    public function classes()
    {
        $classes = ClassRoom::query()
            ->whereIn(
                'id',
                $this->profPaths
                    ->assignments(
                        auth()->id()
                    )
                    ->pluck('class_id')
                    ->unique()
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

    public function data(
        Request $request,
        ClassScheduleDisplayService $scheduleService
    ) {
        try {
            $rangeStart = Carbon::parse(
                $request->query(
                    'start',
                    now()
                        ->startOfWeek()
                        ->toIso8601String()
                )
            );

            $rangeEnd = Carbon::parse(
                $request->query(
                    'end',
                    now()
                        ->endOfWeek()
                        ->addDay()
                        ->toIso8601String()
                )
            );
        } catch (\Throwable $exception) {
            return response()->json(
                [],
                422
            );
        }

        if (
            $rangeEnd
                ->lessThanOrEqualTo(
                    $rangeStart
                )
        ) {
            $rangeEnd = $rangeStart
                ->copy()
                ->addWeek();
        }

        $days = min(
            370,
            max(
                1,
                $rangeStart
                    ->copy()
                    ->startOfDay()
                    ->diffInDays(
                        $rangeEnd
                            ->copy()
                            ->endOfDay()
                    ) + 1
            )
        );

        $filters =
            $this->profPaths
                ->selectedFilters($request);

        $slot = !empty(
            $filters['selectedSlotId']
        )
            ? ClassSlot::query()->find(
                $filters['selectedSlotId']
            )
            : null;

        if (
            $slot
            && !$this->profPaths->ownsSlot(
                auth()->id(),
                (int) $slot->id
            )
        ) {
            abort(403);
        }

        $scheduleFilters = array_filter([
            'subject_id' =>
                $filters['selectedSubjectId'],
            'level_id' =>
                $filters['selectedLevelId'],
            'class_id' =>
                $filters['selectedClassId'],
            'slot_code' =>
                $slot?->code,
        ]);

        $events =
            $scheduleService
                ->forProfessor(
                    auth()->user(),
                    $rangeStart,
                    $days,
                    null,
                    $scheduleFilters
                )
                ->map(
                    function (
                        array $occurrence
                    ) {
                        return [
                            'id' =>
                                $occurrence[
                                    'schedule_id'
                                ]
                                . '-'
                                . $occurrence[
                                    'date_key'
                                ],
                            'title' =>
                                collect([
                                    $occurrence[
                                        'subject'
                                    ],
                                    $occurrence[
                                        'class_name'
                                    ],
                                    $occurrence[
                                        'slot_code'
                                    ]
                                        ?: null,
                                ])
                                    ->filter()
                                    ->implode(
                                        ' · '
                                    ),
                            'start' =>
                                $occurrence[
                                    'start'
                                ]
                                    ->toIso8601String(),
                            'end' =>
                                $occurrence[
                                    'end'
                                ]
                                    ->toIso8601String(),
                            'allDay' =>
                                false,
                            'extendedProps' => [
                                'subject' =>
                                    $occurrence[
                                        'subject'
                                    ],
                                'level' =>
                                    $occurrence[
                                        'level'
                                    ],
                                'class' =>
                                    $occurrence[
                                        'class_name'
                                    ],
                                'slot_code' =>
                                    $occurrence[
                                        'slot_code'
                                    ],
                                'path' =>
                                    $occurrence[
                                        'path'
                                    ],
                                'time' =>
                                    $occurrence[
                                        'time_label'
                                    ],
                                'teacher' =>
                                    $occurrence[
                                        'teacher'
                                    ],
                                'room' =>
                                    $occurrence[
                                        'room'
                                    ],
                            ],
                        ];
                    }
                )
                ->values();

        return response()->json(
            $events
        );
    }

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
}
