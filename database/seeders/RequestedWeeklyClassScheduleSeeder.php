<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class RequestedWeeklyClassScheduleSeeder extends Seeder
{
    public function run()
    {
        $professor = User::query()
            ->where('role', User::ROLE_PROF)
            ->where(function ($query) {
                $query->where('is_active', true)->orWhereNull('is_active');
            })
            ->orderBy('id')
            ->first();

        if (!$professor) {
            throw new RuntimeException(
                'Créez d’abord au moins un professeur, puis relancez ce seeder.'
            );
        }

        $room = Room::query()->firstOrCreate(
            ['name' => 'Classe 1'],
            ['capacity' => 20, 'is_active' => true]
        );

        $definitions = [
            ['subject' => 'Arabe', 'class' => ['Débutant'], 'day' => 3, 'start' => '13:00', 'end' => '14:00'],
            ['subject' => 'Arabe', 'class' => ['Intermédiaire'], 'day' => 3, 'start' => '14:00', 'end' => '15:00'],
            ['subject' => 'Arabe', 'class' => ['Avancé', 'Avancée'], 'day' => 3, 'start' => '15:30', 'end' => '16:30'],
            ['subject' => 'Coran', 'class' => ['Débutant'], 'day' => 3, 'start' => '16:30', 'end' => '17:30'],
            ['subject' => 'Coran', 'class' => ['Intermédiaire'], 'day' => 3, 'start' => '17:30', 'end' => '18:30'],
            ['subject' => 'Coran', 'class' => ['Avancé', 'Avancée'], 'day' => 6, 'start' => '08:00', 'end' => '09:00'],
        ];

        $validFrom = now()->startOfWeek(Carbon::MONDAY)->startOfDay();

        foreach ($definitions as $definition) {
            $subject = Subject::query()
                ->get()
                ->first(fn (Subject $item) => $this->normalize($item->name) === $this->normalize($definition['subject']));

            if (!$subject) {
                $this->command?->warn('Matière introuvable : ' . $definition['subject']);
                continue;
            }

            $classRoom = $this->findClassRoom($subject, $definition['class']);

            if (!$classRoom || !$classRoom->level) {
                $this->command?->warn(
                    'Classe introuvable pour ' . $definition['subject'] . ' : ' . implode('/', $definition['class'])
                );
                continue;
            }

            $anchor = $this->firstOccurrenceOnOrAfter($validFrom, $definition['day']);

            Schedule::query()->updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'class_id' => $classRoom->id,
                    'room_id' => $room->id,
                    'day_of_week' => $definition['day'],
                    'recurrence' => Schedule::RECURRENCE_WEEKLY,
                    'start_time' => $anchor->format('Y-m-d') . ' ' . $definition['start'] . ':00',
                ],
                [
                    'prof_id' => $professor->id,
                    'level_id' => $classRoom->level_id,
                    'subject' => $subject->name,
                    'date' => $anchor->toDateString(),
                    'end_time' => $anchor->format('Y-m-d') . ' ' . $definition['end'] . ':00',
                    'valid_from' => $validFrom->toDateString(),
                    'valid_until' => null,
                    'status' => Schedule::STATUS_ACTIVE,
                    'notes' => 'Planning hebdomadaire initial demandé.',
                ]
            );
        }

        $this->command?->info(
            'Planning initial ajouté. Professeur utilisé : ' . $professor->name
        );
    }

    private function findClassRoom(Subject $subject, array $names): ?ClassRoom
    {
        $normalizedNames = collect($names)->map(fn ($name) => $this->normalize($name));

        return ClassRoom::query()
            ->with('level')
            ->whereHas('level', fn ($query) => $query->where('subject_id', $subject->id))
            ->get()
            ->first(fn (ClassRoom $classRoom) => $normalizedNames->contains($this->normalize($classRoom->name)));
    }

    private function firstOccurrenceOnOrAfter(Carbon $date, int $dayOfWeek): Carbon
    {
        $cursor = $date->copy()->startOfDay();
        $difference = ($dayOfWeek - $cursor->dayOfWeekIso + 7) % 7;

        return $cursor->addDays($difference);
    }

    private function normalize(?string $value): string
    {
        return Str::lower(Str::ascii(trim((string) $value)));
    }
}
