<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\ProfAssignment;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        foreach (['subject_id', 'level_id', 'class_id', 'slot_code', 'prof_id', 'status'] as $filter) {
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
                        'path' => $schedule->full_path_label,
                        'slot_code' => $schedule->slot_code,
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

        if (!empty($data['prof_id'])) {
            $this->syncTeachingAssignment($data);
        }

        return redirect()
            ->route('admin.schedule.index')
            ->with('success', 'La séance a été planifiée avec succès.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $this->validateAndNormalize($request);

        $this->assertNoConflict($data, $schedule->id);

        $schedule->update($data);

        if (!empty($data['prof_id'])) {
            $this->syncTeachingAssignment($data);
        }

        return redirect()
            ->route('admin.schedule.index')
            ->with('success', 'Le planning a été modifié avec succès.');
    }

    /**
     * Modifier uniquement le professeur associé à un créneau.
     *
     * Hiérarchie :
     * Matière → Niveau → Classe → Créneau.
     * Le professeur est une propriété du créneau.
     */
    public function updateProfessor(
        Request $request,
        Schedule $schedule
    ) {
        $validated = $request->validate([
            'prof_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ], [
            'prof_id.required' =>
                'Veuillez sélectionner un professeur.',
        ]);

        $teacher = User::findOrFail(
            (int) $validated['prof_id']
        );

        if ($teacher->role !== User::ROLE_PROF) {
            throw ValidationException::withMessages([
                'prof_id' =>
                    'Le compte sélectionné n’est pas un professeur.',
            ]);
        }

        $conflictData = [
            'subject_id' => (int) $schedule->subject_id,
            'level_id' => (int) $schedule->level_id,
            'class_id' => (int) $schedule->class_id,
            'prof_id' => (int) $teacher->id,
            'day_of_week' => (int) $schedule->day_of_week,
            'start_time' => Carbon::parse(
                $schedule->start_time
            )->format('H:i'),
            'end_time' => Carbon::parse(
                $schedule->end_time
            )->format('H:i'),
            'recurrence' =>
                $schedule->recurrence
                ?: Schedule::RECURRENCE_WEEKLY,
            'valid_from' => Carbon::parse(
                $schedule->valid_from
                ?: now()
            )->format('Y-m-d'),
            'status' =>
                $schedule->status
                ?: Schedule::STATUS_ACTIVE,
        ];

        $this->assertNoConflict(
            $conflictData,
            $schedule->id
        );

        $schedule->update([
            'prof_id' => $teacher?->id,
        ]);

        $this->syncTeachingAssignment(
            $conflictData
        );

        return redirect()
            ->route('admin.schedule.index')
            ->with(
                'success',
                'Le professeur du créneau a été modifié avec succès.'
            );
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
            'slot_code' => ['required', 'string', 'max:20'],
            'prof_id' => ['nullable', 'integer', 'exists:users,id'],
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
            'slot_code.required' => 'Veuillez sélectionner un créneau (D1, D2, D3, D4...).',
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
        $teacher = !empty($validated['prof_id'])
            ? User::findOrFail($validated['prof_id'])
            : null;

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

        $allowedSlotCodes = $this->slotCodesForClass($classRoom);
        $slotCode = strtoupper(
            trim((string) $validated['slot_code'])
        );

        if (!in_array($slotCode, $allowedSlotCodes, true)) {
            throw ValidationException::withMessages([
                'slot_code' =>
                    'Le créneau sélectionné ne correspond pas à cette classe.',
            ]);
        }

        if ($teacher && $teacher->role !== User::ROLE_PROF) {
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
            'slot_code' => $slotCode,
            'room_id' => null,
            'prof_id' => $teacher?->id,
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

    private function assertNoConflict(
        array $data,
        ?int $ignoreScheduleId = null
    ): void {
        if ($data['status'] !== Schedule::STATUS_ACTIVE) {
            return;
        }

        $candidates = Schedule::query()
            ->active()
            ->when(
                $ignoreScheduleId,
                fn ($query) =>
                    $query->where(
                        'id',
                        '<>',
                        $ignoreScheduleId
                    )
            )
            ->where(function ($query) use ($data) {
                /*
                 * On récupère les séances de la même classe
                 * pour vérifier le même groupe D1/D2/...,
                 * et celles du même professeur s'il existe.
                 */
                $query->where(
                    'class_id',
                    $data['class_id']
                );

                if (!empty($data['prof_id'])) {
                    $query->orWhere(
                        'prof_id',
                        $data['prof_id']
                    );
                }
            })
            ->with(['prof', 'classRoom'])
            ->get();

        foreach ($candidates as $existing) {
            $sameTeacher =
                !empty($data['prof_id'])
                && (int) $existing->prof_id
                    === (int) $data['prof_id'];

            $existingSlot =
                strtoupper(
                    trim(
                        (string) $existing->slot_code
                    )
                );

            $newSlot =
                strtoupper(
                    trim(
                        (string) ($data['slot_code'] ?? '')
                    )
                );

            /*
             * Anciennes séances sans slot_code :
             * on les considère comme couvrant toute la classe
             * pour éviter un chevauchement involontaire.
             */
            $sameClassGroup =
                (int) $existing->class_id
                    === (int) $data['class_id']
                && (
                    $existingSlot === ''
                    || $newSlot === ''
                    || $existingSlot === $newSlot
                );

            if (!$sameTeacher && !$sameClassGroup) {
                continue;
            }

            if (!$this->timesOverlap(
                Carbon::parse(
                    $existing->start_time
                )->format('H:i:s'),
                Carbon::parse(
                    $existing->end_time
                )->format('H:i:s'),
                Carbon::parse(
                    $data['start_time']
                )->format('H:i:s'),
                Carbon::parse(
                    $data['end_time']
                )->format('H:i:s')
            )) {
                continue;
            }

            if (!$this->datePatternsOverlap(
                $existing,
                $data
            )) {
                continue;
            }

            $reasons = [];

            if ($sameTeacher) {
                $reasons[] =
                    'le professeur « '
                    . (
                        optional($existing->prof)->name
                        ?: 'sélectionné'
                    )
                    . ' »';
            }

            if ($sameClassGroup) {
                $label =
                    optional($existing->classRoom)->name
                    ?: 'classe sélectionnée';

                if ($newSlot !== '') {
                    $label .= ' · ' . $newSlot;
                }

                $reasons[] =
                    'le groupe « '
                    . $label
                    . ' »';
            }

            throw ValidationException::withMessages([
                'start_time' =>
                    'Conflit avec la séance #'
                    . $existing->id
                    . ' : '
                    . implode(' et ', $reasons)
                    . ' est déjà occupé(e) à cette heure.',
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
        $subject =
            optional($schedule->subjectModel)->name
            ?: $schedule->subject;

        $class =
            optional($schedule->classRoom)->name
            ?: 'Classe';

        $slotCode =
            trim((string) $schedule->slot_code);

        return collect([
            $subject,
            $class,
            $slotCode !== '' ? $slotCode : null,
        ])
            ->filter()
            ->implode(' — ');
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

    /**
     * Un créneau créé dans l'emploi du temps doit aussi donner au professeur
     * l'accès pédagogique au parcours Matière → Niveau → Classe.
     *
     * Les heures de ProfAssignment sont conservées uniquement pour la
     * rétrocompatibilité. La source officielle des créneaux est schedules.
     */
    private function syncTeachingAssignment(array $data): void
    {
        if (empty($data['prof_id'])) {
            return;
        }

        $assignment = ProfAssignment::firstOrCreate([
            'prof_id' => $data['prof_id'],
            'subject_id' => $data['subject_id'],
            'level_id' => $data['level_id'],
            'class_id' => $data['class_id'],
        ]);

        if (!$assignment->day_of_week) {
            $assignment->day_of_week = $data['day_of_week'];
        }

        if (!$assignment->start_time) {
            $assignment->start_time = Carbon::parse($data['start_time'])
                ->format('H:i:s');
        }

        if (!$assignment->end_time) {
            $assignment->end_time = Carbon::parse($data['end_time'])
                ->format('H:i:s');
        }

        if ($assignment->isDirty()) {
            $assignment->save();
        }
    }

    private function buildScheduleHierarchy(): array
    {
        /*
         * IMPORTANT : l'emploi du temps doit reprendre EXACTEMENT
         * la structure déjà créée dans :
         * Matières → Niveaux → Classes.
         *
         * On ne refiltre donc plus les niveaux/classes avec une liste
         * parallèle. Cela évite le cas où la matière s'affiche mais où
         * le menu "Niveau" reste vide.
         *
         * Structure :
         * Matière → Niveau → Classe → Créneau
         */
        /*
         * L'application utilise uniquement ces trois matières
         * dans la structure pédagogique principale.
         */
        $subjectOrder = [
            'arabe' => 1,
            'coran' => 2,
            'soutien lycee' => 3,
            'soutient lycee' => 3,
        ];

        $subjects = Subject::query()
            ->get()
            ->filter(function (Subject $subject) use ($subjectOrder) {
                return array_key_exists(
                    $this->normalizePathName($subject->name),
                    $subjectOrder
                );
            })
            ->sortBy(function (Subject $subject) use ($subjectOrder) {
                return $subjectOrder[
                    $this->normalizePathName($subject->name)
                ] ?? PHP_INT_MAX;
            })
            ->values();

        $levelsBySubject = Level::query()
            ->with([
                'classes' => fn ($query) =>
                    $query->orderBy('name'),
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->groupBy('subject_id');

        return $subjects
            ->map(function (Subject $subject) use ($levelsBySubject) {
                $subjectLevels = $levelsBySubject
                    ->get($subject->id, collect());

                /*
                 * Pour Arabe, seuls les deux niveaux officiels doivent
                 * apparaître dans toute la chaîne du planning :
                 *
                 * Arabe → Communication
                 * Arabe → Lecture & Écriture
                 *
                 * Les anciens enregistrements Débutant / Intermédiaire /
                 * Avancé éventuellement présents dans la table levels
                 * ne sont PAS supprimés de la base, mais ils ne sont plus
                 * proposés comme niveaux dans /admin/schedule.
                 */
                $allowedLevelNames =
                    $this->allowedLevelNamesForSubject(
                        $subject
                    );

                if ($allowedLevelNames !== null) {
                    $subjectLevels = $subjectLevels
                        ->filter(function (Level $level) use (
                            $allowedLevelNames
                        ) {
                            return in_array(
                                $this->normalizePathName(
                                    $level->name
                                ),
                                $allowedLevelNames,
                                true
                            );
                        })
                        ->sortBy(function (Level $level) use (
                            $allowedLevelNames
                        ) {
                            $normalized =
                                $this->normalizePathName(
                                    $level->name
                                );

                            $position =
                                array_search(
                                    $normalized,
                                    $allowedLevelNames,
                                    true
                                );

                            return $position === false
                                ? PHP_INT_MAX
                                : $position;
                        })
                        ->unique(function (Level $level) {
                            return $this->normalizePathName(
                                $level->name
                            );
                        })
                        ->values();
                }

                $subjectLevels = $subjectLevels
                    ->map(function (Level $level) {
                        $classes = $level->classes
                            ->unique('id')
                            ->values()
                            ->map(function (ClassRoom $classRoom) {
                                return [
                                    'id' => (int) $classRoom->id,
                                    'name' => $classRoom->name,
                                    'slot_codes' =>
                                        $this->slotCodesForClass(
                                            $classRoom
                                        ),
                                ];
                            })
                            ->all();

                        return [
                            'id' => (int) $level->id,
                            'name' => $level->name,
                            'classes' => $classes,
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'id' => (int) $subject->id,
                    'name' => $subject->name,
                    'levels' => $subjectLevels,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Liste officielle des parcours actuellement utilisés.
     * null signifie : aucun filtre spécial pour cette matière.
     */
    private function allowedLevelNamesForSubject(
        Subject $subject
    ): ?array {
        return match (
            $this->normalizePathName($subject->name)
        ) {
            /*
             * Arabe possède exactement deux niveaux.
             * Débutant / Intermédiaire / Avancé sont des CLASSES,
             * pas des niveaux.
             */
            'arabe' => [
                'communication',
                'lecture & ecriture',
            ],

            /*
             * Pour Coran et Soutien Lycée, on conserve les niveaux
             * configurés actuellement dans l'administration.
             */
            default => null,
        };
    }


    /**
     * Les niveaux/parcours restent inchangés.
     *
     * Exemple :
     * Arabe → Communication → Débutant → D1 / D2 / D3 / D4
     * Arabe → Communication → Intermédiaire → I1 / I2 / I3 / I4
     * Arabe → Communication → Avancé → A1 / A2 / A3 / A4
     */
    private function slotCodesForClass(
        ClassRoom $classRoom
    ): array {
        $normalized =
            $this->normalizePathName(
                $classRoom->name
            );

        $prefix = match (true) {
            str_contains(
                $normalized,
                'debutant'
            ) => 'D',

            str_contains(
                $normalized,
                'intermediaire'
            ) => 'I',

            str_contains(
                $normalized,
                'avance'
            ) => 'A',

            default => 'G',
        };

        return collect(range(1, 4))
            ->map(
                fn (int $number) =>
                    $prefix . $number
            )
            ->all();
    }

    private function normalizePathName(
        string $value
    ): string {
        $value = preg_replace(
            '/\\s+/u',
            ' ',
            trim($value)
        );

        return Str::lower(
            Str::ascii((string) $value)
        );
    }

}
