<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfAssignment;
use App\Models\ProfessorAvailability;
use App\Models\User;
use App\Services\ProfessorAutoSchedulerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        /*
         * Onglet affiché au chargement.
         * Le tableau récapitulatif devient la vue par défaut afin que
         * l'administrateur voie immédiatement l'ensemble des professeurs.
         */
        $activeTab = (string) $request->query('tab', 'summary');

        if (!in_array($activeTab, ['editor', 'week', 'summary'], true)) {
            $activeTab = 'summary';
        }

        /*
         * Filtre optionnel de la vue hebdomadaire.
         * 0 = tous les professeurs.
         */
        $weekProfessorId = (int) $request->query(
            'week_professor_id',
            0
        );

        if (
            $weekProfessorId > 0
            && !$professors->contains(
                'id',
                $weekProfessorId
            )
        ) {
            $weekProfessorId = 0;
        }

        $weekProfessor = $weekProfessorId > 0
            ? $professors->firstWhere('id', $weekProfessorId)
            : null;

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
        $professorColors = [];

        foreach ($professors as $index => $professor) {
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

            $professorColors[$professor->id] =
                $this->resolveProfessorColor(
                    $professor,
                    $items,
                    (int) $index
                );
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
                'professorColors' => $professorColors,
                'activeTab' => $activeTab,
                'weekProfessorId' => $weekProfessorId,
                'weekProfessor' => $weekProfessor,
                'days' => ProfessorAvailability::DAYS,
                'timeSlots' => ProfessorAvailability::timeSlots(),
                'stats' => $stats,
            ]
        );
    }

    public function update(
        Request $request,
        User $professor,
        ProfessorAutoSchedulerService $autoScheduler
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

        /*
         * Après l'enregistrement des disponibilités, on croise
         * automatiquement :
         *
         * ProfAssignment
         * Matière → Niveau → Classe → D1/D2/I1/A1...
         *              +
         * ProfessorAvailability
         * Jour → heure début → heure fin
         *              =
         * Schedule hebdomadaire.
         *
         * Le service ne supprime jamais un planning manuel existant.
         */
        $autoPlanning = $autoScheduler->syncForProfessor($professor);

        $successMessage =
            count($rows)
            . ' créneau(x) de disponibilité enregistré(s) pour '
            . $professor->name
            . '. Planification automatique : '
            . ($autoPlanning['requested_sessions'] ?? 0)
            . ' séance(s)/semaine demandée(s), '
            . $autoPlanning['created']
            . ' créée(s), '
            . $autoPlanning['reused']
            . ' réutilisée(s), '
            . ($autoPlanning['rescheduled'] ?? 0)
            . ' repositionnée(s), '
            . ($autoPlanning['removed'] ?? 0)
            . ' retirée(s) après réduction, '
            . $autoPlanning['pending']
            . ' séance(s) restant à planifier.';

        $redirect = redirect()
            ->route(
                'admin.professor-availability.index',
                [
                    'professor_id' => $professor->id,
                    'tab' => 'editor',
                ]
            )
            ->with('success', $successMessage);

        if (!empty($autoPlanning['issues'])) {
            $issues = collect($autoPlanning['issues'])
                ->take(4)
                ->implode(' ');

            if (count($autoPlanning['issues']) > 4) {
                $issues .= ' D’autres affectations restent également à vérifier.';
            }

            $redirect->with('warning', $issues);
        }

        return $redirect;
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
            )
            ->with(
                'warning',
                'Par sécurité, aucun emploi du temps existant n’a été supprimé automatiquement.'
            );
    }

    /**
     * Retourne une couleur stable pour chaque professeur.
     *
     * Règles :
     * - Hamza  : bleu foncé
     * - Maryam : bleu ciel
     * - Nadia  : bleu turquoise
     * - autres professeurs d'Arabe : nuances de bleu
     * - Anglais : nuances violettes
     * - autres matières : palette distincte
     */
    private function resolveProfessorColor(
        User $professor,
        $assignments,
        int $fallbackIndex
    ): array {
        $name = Str::lower(
            Str::ascii((string) $professor->name)
        );

        $subjectNames = collect($assignments)
            ->map(function (ProfAssignment $assignment) {
                return optional($assignment->subject)->name;
            })
            ->filter()
            ->map(function ($subjectName) {
                return Str::lower(
                    Str::ascii((string) $subjectName)
                );
            })
            ->implode(' ');

        if (Str::contains($name, 'hamza')) {
            return [
                'hex' => '#1D4ED8',
                'rgb' => '29,78,216',
                'label' => 'Bleu foncé',
            ];
        }

        if (
            Str::contains($name, 'maryam')
            || Str::contains($name, 'meryem')
        ) {
            return [
                'hex' => '#38BDF8',
                'rgb' => '56,189,248',
                'label' => 'Bleu ciel',
            ];
        }

        if (Str::contains($name, 'nadia')) {
            return [
                'hex' => '#06B6D4',
                'rgb' => '6,182,212',
                'label' => 'Bleu turquoise',
            ];
        }

        $arabicPalette = [
            [
                'hex' => '#2563EB',
                'rgb' => '37,99,235',
                'label' => 'Bleu',
            ],
            [
                'hex' => '#60A5FA',
                'rgb' => '96,165,250',
                'label' => 'Bleu clair',
            ],
            [
                'hex' => '#0EA5E9',
                'rgb' => '14,165,233',
                'label' => 'Bleu azur',
            ],
            [
                'hex' => '#14B8A6',
                'rgb' => '20,184,166',
                'label' => 'Turquoise',
            ],
        ];

        $englishPalette = [
            [
                'hex' => '#7C3AED',
                'rgb' => '124,58,237',
                'label' => 'Violet',
            ],
            [
                'hex' => '#A855F7',
                'rgb' => '168,85,247',
                'label' => 'Violet clair',
            ],
            [
                'hex' => '#C026D3',
                'rgb' => '192,38,211',
                'label' => 'Violet fuchsia',
            ],
            [
                'hex' => '#6366F1',
                'rgb' => '99,102,241',
                'label' => 'Indigo',
            ],
        ];

        $generalPalette = [
            [
                'hex' => '#22C55E',
                'rgb' => '34,197,94',
                'label' => 'Vert',
            ],
            [
                'hex' => '#F59E0B',
                'rgb' => '245,158,11',
                'label' => 'Ambre',
            ],
            [
                'hex' => '#F97316',
                'rgb' => '249,115,22',
                'label' => 'Orange',
            ],
            [
                'hex' => '#E11D48',
                'rgb' => '225,29,72',
                'label' => 'Rose',
            ],
            [
                'hex' => '#8B5CF6',
                'rgb' => '139,92,246',
                'label' => 'Violet',
            ],
            [
                'hex' => '#10B981',
                'rgb' => '16,185,129',
                'label' => 'Émeraude',
            ],
        ];

        if (
            Str::contains($subjectNames, 'arabe')
            || Str::contains($subjectNames, 'arabic')
        ) {
            return $arabicPalette[
                $fallbackIndex % count($arabicPalette)
            ];
        }

        if (
            Str::contains($subjectNames, 'anglais')
            || Str::contains($subjectNames, 'english')
        ) {
            return $englishPalette[
                $fallbackIndex % count($englishPalette)
            ];
        }

        return $generalPalette[
            $fallbackIndex % count($generalPalette)
        ];
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
