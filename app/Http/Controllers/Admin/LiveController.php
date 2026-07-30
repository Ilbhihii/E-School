<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Live;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class LiveController extends Controller
{
    /**
     * Affiche la liste des matières avec le nombre de lives (entrée de la navigation hiérarchique)
     */
    public function index()
    {
        // Tous les lives globaux pour les stats
        $allLives = Live::with('classRoom')->orderBy('created_at', 'desc')->get();

        // Stats
        $totalLives = $allLives->count();
        $recentLives = $allLives->take(5);

        // Sujets qui ont des lives via leurs classes
        $subjects = Subject::whereHas('classes', function($q) {
            $q->whereHas('lives');
        })->withCount(['classes' => function($q) {
            $q->whereHas('lives');
        }])->orderBy('name')->get();

        // Compter les lives par sujet
        $subjectLiveCounts = [];
        foreach ($subjects as $subject) {
            $subjectLiveCounts[$subject->id] = Live::whereHas('classRoom.subjects', function($q) use ($subject) {
                $q->where('subject_id', $subject->id);
            })->count();
        }

        return view('admin.lives.index', compact('subjects', 'totalLives', 'recentLives', 'subjectLiveCounts', 'allLives'));
    }

    /**
     * Affiche les niveaux disponibles pour une matière
     */
    public function subjectLevels(Subject $subject)
    {
        // Niveaux qui ont des classes avec des lives pour cette matière
        $levelIds = $subject->classes()
            ->whereHas('lives')
            ->pluck('class_rooms.level_id')
            ->unique()
            ->filter();
        $levels = Level::whereIn('id', $levelIds)->orderBy('name')->get();

        // Compter les lives par niveau pour cette matière
        $levelLiveCounts = [];
        foreach ($levels as $level) {
            $levelLiveCounts[$level->id] = Live::whereHas('classRoom', function($q) use ($subject, $level) {
                $q->where('level_id', $level->id)
                  ->whereHas('subjects', fn($sq) => $sq->where('subject_id', $subject->id));
            })->count();
        }

        // Compter les classes par niveau pour cette matière
        $levelClassCounts = [];
        foreach ($levels as $level) {
            $levelClassCounts[$level->id] = ClassRoom::where('level_id', $level->id)
                ->whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
                ->whereHas('lives')
                ->count();
        }

        return view('admin.lives.levels', compact('subject', 'levels', 'levelLiveCounts', 'levelClassCounts'));
    }

    /**
     * Affiche les classes d'un niveau pour une matière spécifique
     */
    public function subjectClasses(Subject $subject, Level $level)
    {
        $classes = ClassRoom::where('level_id', $level->id)
            ->whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
            ->whereHas('lives')
            ->withCount('lives')
            ->get();

        return view('admin.lives.classes', compact('subject', 'level', 'classes'));
    }

    /**
     * Affiche les lives d'une classe spécifique
     */
    public function classLives(Subject $subject, Level $level, ClassRoom $class)
    {
        $lives = Live::where('class_id', $class->id)
            ->with('classRoom')
            ->orderBy('live_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $totalLives = $lives->count();

        return view('admin.lives.class-lives', compact('subject', 'level', 'class', 'lives', 'totalLives'));
    }

    // Formulaire création
    public function create()
    {
        /*
         * Structure utilisée par les deux formulaires :
         *
         * Matière
         * └── Niveaux appartenant à la matière
         *     └── Classes appartenant au niveau
         *         et liées à la matière
         */
        $liveHierarchy =
            $this->buildLiveHierarchy();

        $subjects = collect($liveHierarchy)
            ->map(
                fn (array $subject) =>
                    (object) [
                        'id' => $subject['id'],
                        'name' => $subject['name'],
                    ]
            )
            ->values();

        $recentLives = Live::with([
                'classRoom.level',
                'classRoom.subjects',
            ])
            ->orderBy(
                'created_at',
                'desc'
            )
            ->take(10)
            ->get();

        return view(
            'admin.lives.create',
            compact(
                'subjects',
                'liveHierarchy',
                'recentLives'
            )
        );
    }

    // Enregistrer un live
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
            'title' => [
                'required',
                'string',
                'max:255',
            ],
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
            'provider' => [
                'required',
                'in:teams,google_meet',
            ],
            'stream_url' => [
                'required',
                'url',
            ],
            'live_date' => [
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

        /*
         * Vérification 1 :
         * le niveau appartient à la matière.
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
         * la classe appartient au niveau.
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
         * la classe est liée à la matière.
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

        $meetingHost = strtolower(
            (string) parse_url(
                $validated['stream_url'],
                PHP_URL_HOST
            )
        );

        $validProviderLink =
            $validated['provider'] === 'teams'
                ? in_array(
                    $meetingHost,
                    [
                        'teams.microsoft.com',
                        'teams.live.com',
                    ],
                    true
                )
                : $meetingHost === 'meet.google.com';

        if (!$validProviderLink) {
            $providerName =
                $validated['provider'] === 'teams'
                    ? 'Microsoft Teams'
                    : 'Google Meet';

            return back()
                ->withInput()
                ->withErrors([
                    'stream_url' =>
                        "Le lien doit être un lien "
                        . "{$providerName} valide.",
                ]);
        }

        /*
         * Conflit réel :
         * nouveau début < fin existante
         * ET nouvelle fin > début existant.
         */
        $conflict = Live::query()
            ->whereDate(
                'live_date',
                $validated['live_date']
            )
            ->where(
                'start_time',
                '<',
                $validated['end_time']
            )
            ->where(
                'end_time',
                '>',
                $validated['start_time']
            )
            ->exists();

        if ($conflict) {
            return back()
                ->withInput()
                ->withErrors([
                    'live_date' =>
                        'Cette plage horaire est déjà occupée '
                        . 'par un autre live.',
                ]);
        }

        Live::create([
            'title' => $validated['title'],
            'class_id' => $classRoom->id,
            'stream_url' =>
                $validated['stream_url'],
            'provider' =>
                $validated['provider'],
            'admin_id' => auth()->id(),
            'user_id' => auth()->id(),
            'live_date' =>
                $validated['live_date'],
            'start_time' =>
                $validated['start_time'],
            'end_time' =>
                $validated['end_time'],
        ]);

        return redirect()
            ->route('admin.lives.create')
            ->with(
                'success',
                'Live créé avec succès.'
            );
    }

    /**
     * Construit la hiérarchie :
     * Matière → Niveau → Classe.
     */
    private function buildLiveHierarchy(): array
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

    // Formulaire édition
    public function edit($id)
    {
        $live = Live::findOrFail($id);
        $classes = ClassRoom::all();
        return view('admin.lives.edit', compact('live','classes'));
    }

    // Mettre à jour un live
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:class_rooms,id',
            'stream_url' => 'required|url'
        ]);

        $live = Live::findOrFail($id);
        $live->update([
            'title' => $request->title,
            'class_id' => $request->class_id,
            'stream_url' => $request->stream_url,
        ]);


        return redirect()->to(url()->previous())
                         ->with('success', 'Live modifié avec succès');
    }

    // Supprimer un live
    public function destroy($id)
    {
        Live::destroy($id);

        return redirect()->to(url()->previous())
                         ->with('success', 'Live supprimé avec succès');
    }
}