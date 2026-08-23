<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Services\ClassScheduleDisplayService;
use Illuminate\Http\Request;

class PublicScheduleController extends Controller
{
    public function index(
        Request $request,
        ClassScheduleDisplayService $service
    ) {
        $schedules = $service->allPublicSchedules();

        if ($request->filled('schedule')) {
            return $this->showSchedule(
                (int) $request->query('schedule'),
                $schedules
            );
        }

        $days = $schedules->groupBy('day_of_week');

        return view(
            'front.schedule.index',
            compact('schedules', 'days')
        );
    }

    private function showSchedule(
        int $scheduleId,
        $publicSchedules
    ) {
        $selectedSchedule = $publicSchedules->first(
            fn (array $item) =>
                (int) ($item['schedule_id'] ?? 0) === $scheduleId
        );

        abort_unless($selectedSchedule, 404);

        $schedule = Schedule::query()
            ->active()
            ->with([
                'subjectModel',
                'level',
                'classRoom.level',
                'prof',
                'room',
            ])
            ->findOrFail($scheduleId);

        $subjectId = (int) ($selectedSchedule['subject_id'] ?? 0);
        $levelId = (int) ($selectedSchedule['level_id'] ?? 0);
        $classId = (int) ($selectedSchedule['class_id'] ?? 0);

        $availableSchedules = $publicSchedules
            ->filter(function (array $item) use (
                $subjectId,
                $levelId,
                $classId
            ) {
                return (int) ($item['subject_id'] ?? 0) === $subjectId
                    && (int) ($item['level_id'] ?? 0) === $levelId
                    && (int) ($item['class_id'] ?? 0) === $classId;
            })
            ->sortBy(function (array $item) {
                return sprintf(
                    '%02d|%s|%s',
                    (int) ($item['day_of_week'] ?? 8),
                    (string) ($item['start_label'] ?? '99:99'),
                    (string) ($item['slot_code'] ?? '')
                );
            })
            ->values();

        $classRoom = $schedule->classRoom;
        $admissionMode = strtolower(
            trim((string) optional($classRoom)->admission_mode)
        );

        $action = $this->resolveAction(
            $schedule,
            $selectedSchedule,
            $admissionMode
        );

        return view(
            'front.schedule.show',
            compact(
                'schedule',
                'selectedSchedule',
                'availableSchedules',
                'admissionMode',
                'action'
            )
        );
    }

    private function resolveAction(
        Schedule $schedule,
        array $selectedSchedule,
        string $admissionMode
    ): array {
        $subjectId = (int) ($selectedSchedule['subject_id'] ?? 0);
        $levelId = (int) ($selectedSchedule['level_id'] ?? 0);
        $classId = (int) ($selectedSchedule['class_id'] ?? 0);

        if (
            $admissionMode === 'vocal_test'
            && $subjectId > 0
            && $levelId > 0
            && $classId > 0
        ) {
            return [
                'label' => 'Passer le test vocal',
                'icon' => 'bi-mic-fill',
                'url' => route('vocal-test.create', [
                    'subject' => $subjectId,
                    'level' => $levelId,
                    'class' => $classId,
                ]),
                'mode' => 'vocal_test',
            ];
        }

        if (
            $admissionMode === 'contact'
            && $subjectId > 0
            && $levelId > 0
            && $classId > 0
        ) {
            return [
                'label' => 'Prendre contact',
                'icon' => 'bi-calendar-check-fill',
                'url' => route('appointment.create', [
                    'subject_id' => $subjectId,
                    'level_id' => $levelId,
                    'class_id' => $classId,
                    'schedule_id' => $schedule->id,
                    'admission_mode' => 'contact',
                ]),
                'mode' => 'contact',
            ];
        }

        if ($subjectId > 0 && $levelId > 0 && $classId > 0) {
            return [
                'label' => 'Voir les cours',
                'icon' => 'bi-arrow-right-circle-fill',
                'url' => route('front.courses', [
                    'subject' => $subjectId,
                    'level' => $levelId,
                    'class' => $classId,
                ]),
                'mode' => 'direct',
            ];
        }

        return [
            'label' => 'Voir les matières',
            'icon' => 'bi-grid-fill',
            'url' => route('front.classes'),
            'mode' => 'direct',
        ];
    }
}
