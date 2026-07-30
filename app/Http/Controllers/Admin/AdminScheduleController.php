<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::query()
            ->with([
                'classRoom.level',
                'prof',
            ])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();

        /*
         * Structure exacte utilisée par le formulaire :
         *
         * Matière
         * └── Niveaux appartenant à cette matière
         *     └── Classes appartenant au niveau et liées à la matière
         */
        $scheduleHierarchy =
            $this->buildScheduleHierarchy();

        $subjects = collect($scheduleHierarchy)
            ->map(
                fn (array $subject) =>
                    (object) [
                        'id' => $subject['id'],
                        'name' => $subject['name'],
                    ]
            )
            ->values();

        $teachers = User::query()
            ->where('role', 'prof')
            ->orderBy('name')
            ->get();

        return view(
            'admin.schedule.index',
            compact(
                'schedules',
                'subjects',
                'teachers',
                'scheduleHierarchy'
            )
        );
    }

    public function store(Request $request)
    {
        if (
            !in_array(
                auth()->user()->role,
                ['admin', 'prof'],
                true
            )
        ) {
            abort(403);
        }

        $validated = $request->validate([
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'level_id' => [
                'required',
                'integer',
                'exists:levels,id',
            ],
            'class_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],
            'prof_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'date' => [
                'required',
                'date',
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
        ], [
            'subject_id.required' =>
                'Veuillez sélectionner une matière.',
            'level_id.required' =>
                'Veuillez sélectionner un niveau.',
            'class_id.required' =>
                'Veuillez sélectionner une classe.',
            'prof_id.required' =>
                'Veuillez sélectionner un professeur.',
            'end_time.after' =>
                'L’heure de fin doit être après '
                . 'l’heure de début.',
        ]);

        $subject = Subject::findOrFail(
            $validated['subject_id']
        );

        $level = Level::findOrFail(
            $validated['level_id']
        );

        $classRoom = ClassRoom::query()
            ->with('subjects')
            ->findOrFail(
                $validated['class_id']
            );

        $teacher = User::findOrFail(
            $validated['prof_id']
        );

        /*
         * Vérification 1 :
         * le niveau doit appartenir à la matière choisie.
         */
        if (
            (int) $level->subject_id
            !== (int) $subject->id
        ) {
            throw ValidationException::withMessages([
                'level_id' =>
                    'Le niveau sélectionné n’appartient pas '
                    . 'à cette matière.',
            ]);
        }

        /*
         * Vérification 2 :
         * la classe doit appartenir au niveau choisi.
         */
        if (
            (int) $classRoom->level_id
            !== (int) $level->id
        ) {
            throw ValidationException::withMessages([
                'class_id' =>
                    'La classe sélectionnée n’appartient pas '
                    . 'à ce niveau.',
            ]);
        }

        /*
         * Vérification 3 :
         * la classe doit aussi être liée à la matière.
         */
        if (
            !$classRoom->subjects->contains(
                'id',
                (int) $subject->id
            )
        ) {
            throw ValidationException::withMessages([
                'class_id' =>
                    'Cette classe n’est pas liée à la matière '
                    . 'sélectionnée.',
            ]);
        }

        /*
         * Vérification 4 :
         * le compte sélectionné doit être un professeur.
         */
        if ($teacher->role !== 'prof') {
            throw ValidationException::withMessages([
                'prof_id' =>
                    'Le compte sélectionné n’est pas '
                    . 'un professeur.',
            ]);
        }

        Schedule::create([
            'prof_id' => $teacher->id,
            'class_id' => $classRoom->id,
            'subject' => $subject->name,
            'date' => $validated['date'],
            'start_time' =>
                $validated['date']
                . ' '
                . $validated['start_time']
                . ':00',
            'end_time' =>
                $validated['date']
                . ' '
                . $validated['end_time']
                . ':00',
        ]);

        return back()->with(
            'success',
            'Séance ajoutée au planning avec succès.'
        );
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();

        return back()->with(
            'success',
            'Séance supprimée du planning.'
        );
    }

    /**
     * Construit :
     *
     * Matière → Niveaux → Classes
     */
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
            ->with([
                'classes.subjects',
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return $subjects
            ->map(
                function (
                    Subject $subject
                ) use ($levels) {
                    $subjectLevels = $levels
                        ->where(
                            'subject_id',
                            $subject->id
                        )
                        ->map(
                            function (
                                Level $level
                            ) use ($subject) {
                                $classes = $level
                                    ->classes
                                    ->filter(
                                        fn (
                                            ClassRoom $classRoom
                                        ) =>
                                            $classRoom
                                                ->subjects
                                                ->contains(
                                                    'id',
                                                    $subject->id
                                                )
                                    )
                                    ->sortBy('name')
                                    ->unique('id')
                                    ->values()
                                    ->map(
                                        fn (
                                            ClassRoom $classRoom
                                        ) => [
                                            'id' =>
                                                $classRoom->id,
                                            'name' =>
                                                $classRoom->name,
                                        ]
                                    )
                                    ->all();

                                if (empty($classes)) {
                                    return null;
                                }

                                return [
                                    'id' => $level->id,
                                    'name' => $level->name,
                                    'classes' => $classes,
                                ];
                            }
                        )
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
                }
            )
            ->filter()
            ->values()
            ->all();
    }
}
