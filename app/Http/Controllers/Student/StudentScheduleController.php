<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassSlot;
use App\Models\Level;
use App\Models\Subject;
use App\Services\ClassScheduleDisplayService;
use App\Services\LearningPathService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentScheduleController extends Controller
{
    public function index(
        Request $request,
        ClassScheduleDisplayService $scheduleService,
        LearningPathService $learningPathService
    ) {
        $student = $request->user();

        $assignmentRows = $learningPathService
            ->studentAssignmentRows((int) $student->id)
            ->filter(
                fn ($row) =>
                    !empty($row->class_slot_id)
                    && !empty($row->slot_code)
            )
            ->values();

        $subjects = Subject::query()
            ->whereIn('id', $assignmentRows->pluck('subject_id'))
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->whereIn('id', $assignmentRows->pluck('level_id'))
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $classes = ClassRoom::query()
            ->whereIn('id', $assignmentRows->pluck('class_id'))
            ->orderBy('name')
            ->get();

        $slots = ClassSlot::query()
            ->whereIn('id', $assignmentRows->pluck('class_slot_id')->filter())
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('code')
            ->get();

        $subjectsById = $subjects->keyBy('id');
        $levelsById = $levels->keyBy('id');
        $classesById = $classes->keyBy('id');
        $slotsById = $slots->keyBy('id');

        $paths = $assignmentRows
            ->map(function ($row) use (
                $subjectsById,
                $levelsById,
                $classesById,
                $slotsById
            ) {
                $subject = $subjectsById->get((int) $row->subject_id);
                $level = $levelsById->get((int) $row->level_id);
                $classRoom = $classesById->get((int) $row->class_id);
                $slot = $slotsById->get((int) $row->class_slot_id);

                if (!$subject || !$level || !$classRoom || !$slot) {
                    return null;
                }

                return (object) [
                    'subject_id' => (int) $subject->id,
                    'level_id' => (int) $level->id,
                    'class_id' => (int) $classRoom->id,
                    'class_slot_id' => (int) $slot->id,
                    'slot_code' => $slot->code,
                    'subject' => $subject,
                    'level' => $level,
                    'classRoom' => $classRoom,
                    'classSlot' => $slot,
                ];
            })
            ->filter()
            ->values();

        $selectedSubjectId = $request->filled('subject_id')
            ? (int) $request->query('subject_id')
            : null;

        if ($selectedSubjectId && !$paths->contains('subject_id', $selectedSubjectId)) {
            $selectedSubjectId = null;
        }

        $selectedLevelId = $request->filled('level_id')
            ? (int) $request->query('level_id')
            : null;

        if (
            !$selectedSubjectId
            || (
                $selectedLevelId
                && !$paths->where('subject_id', $selectedSubjectId)
                    ->contains('level_id', $selectedLevelId)
            )
        ) {
            $selectedLevelId = null;
        }

        $selectedClassId = $request->filled('class_id')
            ? (int) $request->query('class_id')
            : null;

        if (
            !$selectedSubjectId
            || !$selectedLevelId
            || (
                $selectedClassId
                && !$paths
                    ->where('subject_id', $selectedSubjectId)
                    ->where('level_id', $selectedLevelId)
                    ->contains('class_id', $selectedClassId)
            )
        ) {
            $selectedClassId = null;
        }

        $selectedSlotId = $request->filled('class_slot_id')
            ? (int) $request->query('class_slot_id')
            : null;

        if (
            !$selectedSubjectId
            || !$selectedLevelId
            || !$selectedClassId
            || (
                $selectedSlotId
                && !$paths
                    ->where('subject_id', $selectedSubjectId)
                    ->where('level_id', $selectedLevelId)
                    ->where('class_id', $selectedClassId)
                    ->contains('class_slot_id', $selectedSlotId)
            )
        ) {
            $selectedSlotId = null;
        }

        $selectedSlot = $selectedSlotId
            ? $slots->firstWhere('id', $selectedSlotId)
            : null;

        $filters = array_filter([
            'subject_id' => $selectedSubjectId,
            'level_id' => $selectedLevelId,
            'class_id' => $selectedClassId,
            'slot_code' => $selectedSlot?->code,
        ]);

        /*
         * FullCalendar recharge les événements selon la période visible.
         * On garde la même route /student/planning pour éviter une route API
         * supplémentaire et on applique exactement les mêmes filtres.
         */
        if (
            $request->boolean('calendar')
            && $request->filled('start')
            && $request->filled('end')
        ) {
            try {
                $calendarStart = Carbon::parse(
                    $request->query('start')
                );

                $calendarEnd = Carbon::parse(
                    $request->query('end')
                )->subSecond();
            } catch (\Throwable $exception) {
                return response()->json([], 422);
            }

            if ($calendarEnd->lt($calendarStart)) {
                return response()->json([]);
            }

            /*
             * Protection contre une plage anormalement grande :
             * une vue FullCalendar normale tient très largement dans 370 jours.
             */
            $calendarDays = min(
                370,
                max(
                    1,
                    $calendarStart
                        ->copy()
                        ->startOfDay()
                        ->diffInDays(
                            $calendarEnd
                                ->copy()
                                ->endOfDay()
                        ) + 1
                )
            );

            $calendarOccurrences = $scheduleService->forStudent(
                $student,
                $calendarStart,
                $calendarDays,
                null,
                $filters
            );

            return response()->json(
                $calendarOccurrences
                    ->map(function (array $occurrence) {
                        $slotCode = trim(
                            (string) ($occurrence['slot_code'] ?? '')
                        );

                        return [
                            'id' => implode('-', [
                                $occurrence['schedule_id'],
                                $occurrence['date_key'],
                            ]),
                            'title' => collect([
                                $slotCode ?: null,
                                $occurrence['subject'],
                            ])->filter()->implode(' · '),
                            'start' => $occurrence['start']->toIso8601String(),
                            'end' => $occurrence['end']->toIso8601String(),
                            'allDay' => false,
                            'extendedProps' => [
                                'subject' => $occurrence['subject'],
                                'level' => $occurrence['level'],
                                'class_name' => $occurrence['class_name'],
                                'slot_code' => $slotCode,
                                'path' => $occurrence['path'],
                                'time_label' => $occurrence['time_label'],
                                'duration_label' => $occurrence['duration_label'],
                                'room' => $occurrence['room'],
                                'teacher' => $occurrence['teacher'],
                            ],
                        ];
                    })
                    ->values()
            );
        }

        $occurrences = $scheduleService->forStudent(
            $student,
            now(),
            35,
            null,
            $filters
        );

        $days = $occurrences->groupBy('date_key');

        $subjectsOptions = $paths
            ->unique('subject_id')
            ->sortBy(fn ($path) => mb_strtolower($path->subject->name))
            ->values()
            ->map(fn ($path) => [
                'id' => $path->subject_id,
                'name' => $path->subject->name,
            ])
            ->all();

        $levelsBySubject = [];
        $classesBySubjectLevel = [];
        $slotsByPath = [];

        foreach ($paths as $path) {
            $s = (string) $path->subject_id;
            $l = (string) $path->level_id;
            $c = (string) $path->class_id;

            $levelsBySubject[$s][$l] = [
                'id' => $path->level_id,
                'name' => $path->level->name,
            ];
            $classesBySubjectLevel[$s][$l][$c] = [
                'id' => $path->class_id,
                'name' => $path->classRoom->name,
            ];
            $slotsByPath[$s][$l][$c][(string) $path->class_slot_id] = [
                'id' => $path->class_slot_id,
                'code' => $path->slot_code,
            ];
        }

        foreach ($levelsBySubject as $s => $items) {
            $levelsBySubject[$s] = array_values($items);
        }
        foreach ($classesBySubjectLevel as $s => $levelItems) {
            foreach ($levelItems as $l => $items) {
                $classesBySubjectLevel[$s][$l] = array_values($items);
            }
        }
        foreach ($slotsByPath as $s => $levelItems) {
            foreach ($levelItems as $l => $classItems) {
                foreach ($classItems as $c => $items) {
                    $slotsByPath[$s][$l][$c] = array_values($items);
                }
            }
        }

        $selectedSubject = $selectedSubjectId
            ? $subjects->firstWhere('id', $selectedSubjectId)
            : null;
        $selectedLevel = $selectedLevelId
            ? $levels->firstWhere('id', $selectedLevelId)
            : null;
        $selectedClass = $selectedClassId
            ? $classes->firstWhere('id', $selectedClassId)
            : null;

        $visiblePaths = $paths
            ->when($selectedSubjectId, fn ($items) => $items->where('subject_id', $selectedSubjectId))
            ->when($selectedLevelId, fn ($items) => $items->where('level_id', $selectedLevelId))
            ->when($selectedClassId, fn ($items) => $items->where('class_id', $selectedClassId))
            ->when($selectedSlotId, fn ($items) => $items->where('class_slot_id', $selectedSlotId))
            ->values();

        return view('student.schedule.index', compact(
            'occurrences',
            'days',
            'paths',
            'visiblePaths',
            'subjectsOptions',
            'levelsBySubject',
            'classesBySubjectLevel',
            'slotsByPath',
            'selectedSubjectId',
            'selectedLevelId',
            'selectedClassId',
            'selectedSlotId',
            'selectedSubject',
            'selectedLevel',
            'selectedClass',
            'selectedSlot'
        ));
    }
}
