<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfAssignment;
use App\Models\ProfessorAvailability;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProfessorAvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $professors = User::query()
            ->where('role', User::ROLE_PROF)
            ->orderBy('name')
            ->get();

        $professorIds = $professors->pluck('id');

        $allAvailabilities = ProfessorAvailability::query()
            ->with('professor')
            ->when(
                $professorIds->isNotEmpty(),
                function ($query) use ($professorIds) {
                    $query->whereIn('prof_id', $professorIds);
                }
            )
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
            ->when(
                $professorIds->isNotEmpty(),
                function ($query) use ($professorIds) {
                    $query->whereIn('prof_id', $professorIds);
                }
            )
            ->get()
            ->groupBy('prof_id');

        $requestedProfessorId = (int) $request->query(
            'professor_id',
            optional($professors->first())->id
        );

        $selectedProfessor = $professors->firstWhere(
            'id',
            $requestedProfessorId
        );

        if (!$selectedProfessor) {
            $selectedProfessor = $professors->first();
        }

        $selectedAvailabilityKeys = collect();

        if ($selectedProfessor) {
            $selectedAvailabilityKeys = $allAvailabilities
                ->where('prof_id', $selectedProfessor->id)
                ->map(function (ProfessorAvailability $availability) {
                    return $this->availabilityKey(
                        (int) $availability->day_of_week,
                        $availability->start_time,
                        $availability->end_time
                    );
                })
                ->values();
        }

        $availabilityMatrix = $allAvailabilities->groupBy(
            function (ProfessorAvailability $availability) {
                return (int) $availability->day_of_week
                    . '|'
                    . Carbon::parse($availability->start_time)
                        ->format('H:i');
            }
        );

        $availabilityByProfessor = $allAvailabilities
            ->groupBy('prof_id');

        $teachingSummary = [];

        foreach ($professors as $professor) {
            $items = $assignments->get(
                $professor->id,
                collect()
            );

            $teachingSummary[$professor->id] = $items
                ->map(function (ProfAssignment $assignment) {
                    $parts = collect([
                        optional($assignment->subject)->name,
                        optional($assignment->level)->name,
                        optional($assignment->classRoom)->name,
                        optional($assignment->classSlot)->code,
                    ])->filter()->values();

                    return $parts->isNotEmpty()
                        ? $parts->implode(' · ')
                        : null;
                })
                ->filter()
                ->unique()
                ->values();
        }

        $professorsWithAvailability = $availabilityByProfessor
            ->filter(function ($items) {
                return $items->isNotEmpty();
            })
            ->count();

        $stats = [
            'total_professors' => $professors->count(),
            'completed' => $professorsWithAvailability,
            'pending' => max(
                0,
                $professors->count() - $professorsWithAvailability
            ),
            'availability_slots' => $allAvailabilities->count(),
        ];

        return view(
            'admin.professor-availability.index',
            [
                'professors' => $professors,
                'selectedProfessor' => $selectedProfessor,
                'selectedAvailabilityKeys' => $selectedAvailabilityKeys,
                'availabilityMatrix' => $availabilityMatrix,
                'availabilityByProfessor' => $availabilityByProfessor,
                'teachingSummary' => $teachingSummary,
                'days' => ProfessorAvailability::DAYS,
                'timeSlots' => ProfessorAvailability::timeSlots(),
                'stats' => $stats,
            ]
        );
    }

    public function update(
        Request $request,
        User $professor
    ) {
        $this->assertProfessor($professor);

        $validated = $request->validate(
            [
                'slots' => ['nullable', 'array'],
                'slots.*' => ['string', 'max:40'],
            ],
            [
                'slots.array' =>
                    'La liste des créneaux est invalide.',
            ]
        );

        $selectedSlots = collect(
            $validated['slots'] ?? []
        )
            ->unique()
            ->values();

        $allowed = $this->allowedSlotMap();
        $rows = [];

        foreach ($selectedSlots as $rawSlot) {
            if (!isset($allowed[$rawSlot])) {
                throw ValidationException::withMessages([
                    'slots' =>
                        'Un créneau envoyé n’est pas autorisé.',
                ]);
            }

            $slot = $allowed[$rawSlot];

            $rows[] = [
                'prof_id' => $professor->id,
                'day_of_week' => $slot['day'],
                'start_time' => $slot['start'] . ':00',
                'end_time' => $slot['end'] . ':00',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::transaction(function () use (
            $professor,
            $rows
        ) {
            ProfessorAvailability::query()
                ->where('prof_id', $professor->id)
                ->delete();

            if (!empty($rows)) {
                ProfessorAvailability::query()
                    ->insert($rows);
            }
        });

        return redirect()
            ->route(
                'admin.professor-availability.index',
                ['professor_id' => $professor->id]
            )
            ->with(
                'success',
                count($rows)
                    . ' créneau(x) de disponibilité enregistré(s) pour '
                    . $professor->name
                    . '.'
            );
    }

    public function destroy(User $professor)
    {
        $this->assertProfessor($professor);

        ProfessorAvailability::query()
            ->where('prof_id', $professor->id)
            ->delete();

        return redirect()
            ->route('admin.professor-availability.index')
            ->with(
                'success',
                'Les disponibilités de '
                . $professor->name
                . ' ont été effacées.'
            );
    }

    private function assertProfessor(User $professor): void
    {
        abort_unless(
            $professor->role === User::ROLE_PROF,
            404
        );
    }

    private function allowedSlotMap(): array
    {
        $allowed = [];

        foreach (ProfessorAvailability::DAYS as $day => $label) {
            foreach (ProfessorAvailability::timeSlots() as $slot) {
                $key = $this->availabilityKey(
                    (int) $day,
                    $slot['start'],
                    $slot['end']
                );

                $allowed[$key] = [
                    'day' => (int) $day,
                    'start' => $slot['start'],
                    'end' => $slot['end'],
                ];
            }
        }

        return $allowed;
    }

    private function availabilityKey(
        int $day,
        $start,
        $end
    ): string {
        return $day
            . '|'
            . Carbon::parse($start)->format('H:i')
            . '|'
            . Carbon::parse($end)->format('H:i');
    }
}
