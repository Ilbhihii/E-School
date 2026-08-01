<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::query()
            ->with([
                'classRoom.level',
                'subjectModel',
                'level',
                'prof',
            ]);

        foreach (['subject_id', 'level_id', 'class_id', 'prof_id', 'status'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        $schedules = $query
            ->orderByRaw('COALESCE(day_of_week, 8) asc')
            ->orderByRaw('TIME(start_time) asc')
            ->get();

        $scheduleHierarchy = $this->buildScheduleHierarchy();

        $subjects = collect($scheduleHierarchy)
            ->map(function (array $subject) {
                return (object) [
                    'id' => $subject['id'],
                    'name' => $subject['name'],
                ];
            })
            ->values();

        $teachers = User::query()
            ->where('role', User::ROLE_PROF)
            ->orderBy('name')
            ->get();

        $levels = Level::query()->orderBy('name')->get();
        $classes = ClassRoom::query()->orderBy('name')->get();

        return view('admin.schedule.index', compact(
            'schedules',
            'subjects',
            'teachers',
            'levels',
            'classes',
            'scheduleHierarchy'
        ));
    }

    public function events(Request $request): JsonResponse
    {
        $rangeStart = Carbon::parse($request->query('start', now()->startOfWeek()))
            ->startOfDay();

        // FullCalendar envoie une date de fin exclusive.
        $rangeEnd = Carbon::parse($request->query('end', now()->addWeeks(6)))
            ->subSecond();

        $query = Schedule::query()
            ->active()
            ->with([
                'classRoom.level',
                'subjectModel',
                'level',
                'prof',
            ]);

        foreach (['subject_id', 'level_id', 'class_id', 'prof_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        $events = [];

        foreach ($query->get() as $schedule) {
            foreach ($this->occurrenceDates($schedule, $rangeStart, $rangeEnd) as $date) {
                $start = $date->copy()->setTimeFromTimeString(
                    Carbon::parse($schedule->start_time)->format('H:i:s')
                );

                $end = $date->copy()->setTimeFromTimeString(
                    Carbon::parse($schedule->end_time)->format('H:i:s')
                );

                $events[] = [
                    'id' => $schedule->id . '-' . $date->format('Ymd'),
                    'title' => $this->eventTitle($schedule),
                    'start' => $start->toIso8601String(),
                    'end' => $end->toIso8601String(),
                    'backgroundColor' => $this->eventColor($schedule),
                    'borderColor' => $this->eventColor($schedule),
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'schedule_id' => $schedule->id,
                        'path' => $schedule->path_label,
                        'teacher' => optional($schedule->prof)->name ?: 'Professeur non défini',
                        'recurrence' => $schedule->recurrence,
                    ],
                ];
            }
        }

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $data = $this->validateAndNormalize($request);

        $this->assertNoConflict($data);

        Schedule::create($data);

        return redirect()
            ->route('admin.schedule.index')
            ->with('success', 'La séance a été planifiée avec succès.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $this->validateAndNormalize($request);

        $this->assertNoConflict($data, $schedule->id);

        $schedule->update($data);

        return redirect()
            ->route('admin.schedule.index')
            ->with('success', 'Le planning a été modifié avec succès.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()
            ->route('admin.schedule.index')
            ->with('success', 'La séance a été supprimée du planning.');
    }

    private function validateAndNormalize(Request $request): array
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'class_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'prof_id' => ['required', 'integer', 'exists:users,id'],
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'recurrence' => [
                'required',
                Rule::in([Schedule::RECURRENCE_ONCE, Schedule::RECURRENCE_WEEKLY]),
            ],
            'valid_from' => ['required', 'date'],
            'status' => [
                'required',
                Rule::in([Schedule::STATUS_ACTIVE, Schedule::STATUS_INACTIVE]),
            ],
        ], [
            'subject_id.required' => 'Veuillez sélectionner une matière.',
            'level_id.required' => 'Veuillez sélectionner un niveau.',
            'class_id.required' => 'Veuillez sélectionner une classe pédagogique.',
            'prof_id.required' => 'Veuillez sélectionner un professeur.',
            'start_time.required' => 'Veuillez sélectionner l’heure de début.',
            'end_time.required' => 'Veuillez sélectionner l’heure de fin.',
            'end_time.date_format' => 'L’heure de fin doit être au format HH:MM.',
            'valid_from.required' => 'Veuillez sélectionner la date de début.',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        $level = Level::findOrFail($validated['level_id']);
        $classRoom = ClassRoom::query()
            ->with('subjects')
            ->findOrFail($validated['class_id']);
        $teacher = User::findOrFail($validated['prof_id']);

        if ((int) $level->subject_id !== (int) $subject->id) {
            throw ValidationException::withMessages([
                'level_id' => 'Le niveau sélectionné n’appartient pas à cette matière.',
            ]);
        }

        if ((int) $classRoom->level_id !== (int) $level->id) {
            throw ValidationException::withMessages([
                'class_id' => 'La classe sélectionnée n’appartient pas à ce niveau.',
            ]);
        }

        if (!$classRoom->subjects->contains('id', (int) $subject->id)) {
            throw ValidationException::withMessages([
                'class_id' => 'Cette classe n’est pas liée à la matière sélectionnée.',
            ]);
        }

        if ($teacher->role !== User::ROLE_PROF) {
            throw ValidationException::withMessages([
                'prof_id' => 'Le compte sélectionné n’est pas un professeur.',
            ]);
        }

        $validFrom = Carbon::parse($validated['valid_from'])->startOfDay();

        if ($validated['recurrence'] === Schedule::RECURRENCE_ONCE) {
            $dayOfWeek = $validFrom->dayOfWeekIso;
            $anchorDate = $validFrom->copy();
            $validUntil = $validFrom->copy();
        } else {
            $dayOfWeek = (int) $validated['day_of_week'];
            $anchorDate = $this->firstOccurrenceOnOrAfter($validFrom, $dayOfWeek);
            $validUntil = null; // Planning hebdomadaire sans date de fin.
        }

        $startDateTime = Carbon::parse(
            $anchorDate->format('Y-m-d') . ' ' . $validated['start_time'] . ':00'
        );
        $endDateTime = Carbon::parse(
            $anchorDate->format('Y-m-d') . ' ' . $validated['end_time'] . ':00'
        );

        if ($endDateTime->lte($startDateTime)) {
            throw ValidationException::withMessages([
                'end_time' => 'L’heure de fin doit être postérieure à l’heure de début.',
            ]);
        }

        return [
            'subject_id' => $subject->id,
            'level_id' => $level->id,
            'class_id' => $classRoom->id,
            'room_id' => null,
            'prof_id' => $teacher->id,
            'subject' => $subject->name,
            'date' => $anchorDate->toDateString(),
            'day_of_week' => $dayOfWeek,
            'recurrence' => $validated['recurrence'],
            'valid_from' => $validFrom->toDateString(),
            'valid_until' => $validUntil ? $validUntil->toDateString() : null,
            'status' => $validated['status'],
            'notes' => null,
            'start_time' => $startDateTime->format('Y-m-d H:i:s'),
            'end_time' => $endDateTime->format('Y-m-d H:i:s'),
        ];
    }

    private function assertNoConflict(array $data, ?int $ignoreScheduleId = null): void
    {
        if ($data['status'] !== Schedule::STATUS_ACTIVE) {
            return;
        }

        $candidates = Schedule::query()
            ->active()
            ->when($ignoreScheduleId, function ($query) use ($ignoreScheduleId) {
                return $query->where('id', '<>', $ignoreScheduleId);
            })
            ->where(function ($query) use ($data) {
                $query->where('prof_id', $data['prof_id'])
                    ->orWhere('class_id', $data['class_id']);
            })
            ->with(['prof', 'classRoom'])
            ->get();

        foreach ($candidates as $existing) {
            if (!$this->timesOverlap(
                Carbon::parse($existing->start_time)->format('H:i:s'),
                Carbon::parse($existing->end_time)->format('H:i:s'),
                Carbon::parse($data['start_time'])->format('H:i:s'),
                Carbon::parse($data['end_time'])->format('H:i:s')
            )) {
                continue;
            }

            if (!$this->datePatternsOverlap($existing, $data)) {
                continue;
            }

            $reasons = [];

            if ((int) $existing->prof_id === (int) $data['prof_id']) {
                $reasons[] = 'le professeur « ' . (optional($existing->prof)->name ?: 'sélectionné') . ' »';
            }

            if ((int) $existing->class_id === (int) $data['class_id']) {
                $reasons[] = 'la classe pédagogique « ' . (optional($existing->classRoom)->name ?: 'sélectionnée') . ' »';
            }

            throw ValidationException::withMessages([
                'start_time' => 'Conflit avec la séance #' . $existing->id
                    . ' : ' . implode(' et ', $reasons) . ' est déjà occupé(e) à cette heure.',
            ]);
        }
    }

    private function timesOverlap(
        string $existingStart,
        string $existingEnd,
        string $newStart,
        string $newEnd
    ): bool {
        return $newStart < $existingEnd && $newEnd > $existingStart;
    }

    private function datePatternsOverlap(Schedule $existing, array $new): bool
    {
        $existingRecurrence = $existing->recurrence ?: Schedule::RECURRENCE_ONCE;
        $newRecurrence = $new['recurrence'];

        $existingDate = Carbon::parse(
            $existing->date ?: $existing->start_time
        )->startOfDay();
        $newDate = Carbon::parse($new['date'])->startOfDay();

        if (
            $existingRecurrence === Schedule::RECURRENCE_ONCE
            && $newRecurrence === Schedule::RECURRENCE_ONCE
        ) {
            return $existingDate->isSameDay($newDate);
        }

        if (
            $existingRecurrence === Schedule::RECURRENCE_WEEKLY
            && $newRecurrence === Schedule::RECURRENCE_WEEKLY
        ) {
            if ((int) $existing->day_of_week !== (int) $new['day_of_week']) {
                return false;
            }

            return $this->dateRangesOverlap(
                Carbon::parse($existing->valid_from ?: $existingDate),
                $existing->valid_until ? Carbon::parse($existing->valid_until) : null,
                Carbon::parse($new['valid_from']),
                !empty($new['valid_until']) ? Carbon::parse($new['valid_until']) : null
            );
        }

        if ($existingRecurrence === Schedule::RECURRENCE_WEEKLY) {
            return $this->dateMatchesWeeklySchedule($newDate, $existing);
        }

        return $this->dateMatchesWeeklyData($existingDate, $new);
    }

    private function dateRangesOverlap(
        Carbon $startA,
        ?Carbon $endA,
        Carbon $startB,
        ?Carbon $endB
    ): bool {
        $endA = $endA ?: Carbon::create(9999, 12, 31);
        $endB = $endB ?: Carbon::create(9999, 12, 31);

        return $startA->lte($endB) && $startB->lte($endA);
    }

    private function dateMatchesWeeklySchedule(Carbon $date, Schedule $weekly): bool
    {
        $from = Carbon::parse($weekly->valid_from ?: $weekly->date ?: $weekly->start_time)
            ->startOfDay();
        $until = $weekly->valid_until
            ? Carbon::parse($weekly->valid_until)->endOfDay()
            : null;

        return $date->dayOfWeekIso === (int) $weekly->day_of_week
            && $date->gte($from)
            && (!$until || $date->lte($until));
    }

    private function dateMatchesWeeklyData(Carbon $date, array $weekly): bool
    {
        $from = Carbon::parse($weekly['valid_from'])->startOfDay();
        $until = !empty($weekly['valid_until'])
            ? Carbon::parse($weekly['valid_until'])->endOfDay()
            : null;

        return $date->dayOfWeekIso === (int) $weekly['day_of_week']
            && $date->gte($from)
            && (!$until || $date->lte($until));
    }

    private function occurrenceDates(
        Schedule $schedule,
        Carbon $rangeStart,
        Carbon $rangeEnd
    ): array {
        $recurrence = $schedule->recurrence ?: Schedule::RECURRENCE_ONCE;

        if ($recurrence === Schedule::RECURRENCE_ONCE) {
            $date = Carbon::parse($schedule->date ?: $schedule->start_time)->startOfDay();

            return $date->betweenIncluded($rangeStart, $rangeEnd)
                ? [$date]
                : [];
        }

        $validFrom = Carbon::parse(
            $schedule->valid_from ?: $schedule->date ?: $schedule->start_time
        )->startOfDay();
        $validUntil = $schedule->valid_until
            ? Carbon::parse($schedule->valid_until)->endOfDay()
            : $rangeEnd->copy();

        $from = $validFrom->gt($rangeStart) ? $validFrom : $rangeStart->copy();
        $until = $validUntil->lt($rangeEnd) ? $validUntil : $rangeEnd->copy();

        if ($from->gt($until)) {
            return [];
        }

        $cursor = $this->firstOccurrenceOnOrAfter(
            $from,
            (int) ($schedule->day_of_week ?: $from->dayOfWeekIso)
        );

        $dates = [];
        while ($cursor->lte($until)) {
            $dates[] = $cursor->copy();
            $cursor->addWeek();
        }

        return $dates;
    }

    private function firstOccurrenceOnOrAfter(Carbon $date, int $dayOfWeek): Carbon
    {
        $cursor = $date->copy()->startOfDay();
        $difference = ($dayOfWeek - $cursor->dayOfWeekIso + 7) % 7;

        return $cursor->addDays($difference);
    }

    private function eventTitle(Schedule $schedule): string
    {
        $subject = optional($schedule->subjectModel)->name ?: $schedule->subject;
        $class = optional($schedule->classRoom)->name ?: 'Classe';

        return trim($subject . ' — ' . $class);
    }

    private function eventColor(Schedule $schedule): string
    {
        $subject = mb_strtolower(
            optional($schedule->subjectModel)->name ?: $schedule->subject ?: ''
        );

        if (mb_strpos($subject, 'coran') !== false) {
            return '#16a34a';
        }

        if (mb_strpos($subject, 'arabe') !== false) {
            return '#2563eb';
        }

        if (mb_strpos($subject, 'soutien') !== false) {
            return '#ea580c';
        }

        return '#7c3aed';
    }

    private function buildScheduleHierarchy(): array
    {
        $subjects = Subject::query()
            ->orderByRaw(
                "CASE
                    WHEN LOWER(name) = 'arabe' THEN 1
                    WHEN LOWER(name) = 'coran' THEN 2
                    WHEN LOWER(name) = 'soutien lycée' THEN 3
                    ELSE 4
                END"
            )
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->with(['classes.subjects'])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return $subjects
            ->map(function (Subject $subject) use ($levels) {
                $subjectLevels = $levels
                    ->where('subject_id', $subject->id)
                    ->map(function (Level $level) use ($subject) {
                        $classes = $level->classes
                            ->filter(function (ClassRoom $classRoom) use ($subject) {
                                return $classRoom->subjects->contains('id', $subject->id);
                            })
                            ->sortBy('name')
                            ->unique('id')
                            ->values()
                            ->map(function (ClassRoom $classRoom) {
                                return [
                                    'id' => $classRoom->id,
                                    'name' => $classRoom->name,
                                ];
                            })
                            ->all();

                        if (empty($classes)) {
                            return null;
                        }

                        return [
                            'id' => $level->id,
                            'name' => $level->name,
                            'classes' => $classes,
                        ];
                    })
                    ->filter()
                    ->unique('id')
                    ->values()
                    ->all();

                if (empty($subjectLevels)) {
                    return null;
                }

                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'levels' => $subjectLevels,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
