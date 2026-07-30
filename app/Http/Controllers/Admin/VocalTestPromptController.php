<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VocalTestPromptController extends Controller
{
    public function index()
    {
        $prompts = VocalTestPrompt::with(['subject', 'level', 'classRoom'])
            ->orderBy('subject_id')
            ->orderBy('level_id')
            ->orderBy('class_id')
            ->paginate(20);

        return view('admin.vocal-tests.prompts.index', compact('prompts'));
    }

    public function create()
    {
        /*
         * Hiérarchie du formulaire :
         *
         * Matière
         * └── Niveaux appartenant à cette matière
         *     └── Classes appartenant au niveau
         *         et liées à la matière
         */
        $promptHierarchy =
            $this->buildPromptHierarchy();

        $subjects = collect($promptHierarchy)
            ->map(
                fn (array $subject) =>
                    (object) [
                        'id' => $subject['id'],
                        'name' => $subject['name'],
                    ]
            )
            ->values();

        $modes = VocalTestPrompt::getModes();

        return view(
            'admin.vocal-tests.prompts.create',
            compact(
                'subjects',
                'promptHierarchy',
                'modes'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'                 => 'required|exists:subjects,id',
            'level_id'                   => 'required|exists:levels,id',
            'class_id'                   => 'required|exists:class_rooms,id',
            'title'                      => 'required|string|max:255',
            'instructions'               => 'nullable|string',
            'reading_text'               => 'required|string',
            'test_mode'                  => 'required|in:reading,tajwid,hifd',
            'preparation_seconds'        => 'nullable|integer|min:0|max:300',
            'maximum_duration'           => 'nullable|integer|min:15|max:600',
            'hide_text_during_recording' => 'boolean',
            'is_active'                  => 'boolean',
        ], [
            'subject_id.required'   => 'La matière est obligatoire.',
            'level_id.required'     => 'Le niveau est obligatoire.',
            'class_id.required'     => 'La classe est obligatoire.',
            'title.required'        => 'Le titre est obligatoire.',
            'reading_text.required' => 'Le texte à lire est obligatoire.',
            'test_mode.required'    => 'Le mode de test est obligatoire.',
        ]);

        // Vérifier la cohérence matière / niveau / classe
        $this->validateCoherence(
            $validated['subject_id'],
            $validated['level_id'],
            $validated['class_id']
        );

        $validated['maximum_duration'] = $validated['maximum_duration'] ?? 120;
        $validated['preparation_seconds'] = $validated['preparation_seconds'] ?? 0;
        $validated['hide_text_during_recording'] = $request->boolean('hide_text_during_recording', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        VocalTestPrompt::updateOrCreate(
            [
                'subject_id' => $validated['subject_id'],
                'level_id'   => $validated['level_id'],
                'class_id'   => $validated['class_id'],
            ],
            $validated
        );

        return redirect()->route('admin.vocal-tests.prompts.index')
            ->with('success', 'Test vocal créé avec succès.');
    }

    public function edit(VocalTestPrompt $prompt)
    {
        $subjects = Subject::whereIn('name', ['Arabe', 'Coran'])->orderBy('name')->get();
        $levels = Level::orderBy('name')->get();
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.vocal-tests.prompts.edit', compact('prompt', 'subjects', 'levels', 'classes'));
    }

    public function update(Request $request, VocalTestPrompt $prompt)
    {
        $validated = $request->validate([
            'subject_id'                 => [
                'required',
                'exists:subjects,id',
            ],
            'level_id'                   => [
                'required',
                'exists:levels,id',
            ],
            'class_id'                   => [
                'required',
                'exists:class_rooms,id',
                Rule::unique('vocal_test_prompts')->where(function ($query) use ($request, $prompt) {
                    $query->where('subject_id', $request->subject_id)
                          ->where('level_id', $request->level_id);
                })->ignore($prompt->id),
            ],
            'title'                      => 'required|string|max:255',
            'instructions'               => 'nullable|string',
            'reading_text'               => 'required|string',
            'test_mode'                  => 'required|in:reading,tajwid,hifd',
            'preparation_seconds'        => 'nullable|integer|min:0|max:300',
            'maximum_duration'           => 'nullable|integer|min:15|max:600',
            'hide_text_during_recording' => 'boolean',
            'is_active'                  => 'boolean',
        ]);

        // Vérifier la cohérence matière / niveau / classe
        $this->validateCoherence(
            $validated['subject_id'],
            $validated['level_id'],
            $validated['class_id']
        );

        $validated['maximum_duration'] = $validated['maximum_duration'] ?? 120;
        $validated['preparation_seconds'] = $validated['preparation_seconds'] ?? 0;
        $validated['hide_text_during_recording'] = $request->boolean('hide_text_during_recording', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        $prompt->update($validated);

        return redirect()->route('admin.vocal-tests.prompts.index')
            ->with('success', 'Test vocal mis à jour avec succès.');
    }

    public function destroy(VocalTestPrompt $prompt)
    {
        $prompt->delete();

        return redirect()->route('admin.vocal-tests.prompts.index')
            ->with('success', 'Test vocal supprimé avec succès.');
    }

    /**
     * Construit les choix autorisés :
     * Matière → Niveau → Classe.
     */
    private function buildPromptHierarchy(): array
    {
        $subjects = Subject::query()
            ->whereIn(
                'name',
                ['Arabe', 'Coran']
            )
            ->orderByRaw(
                "CASE
                    WHEN LOWER(name) = 'arabe' THEN 1
                    WHEN LOWER(name) = 'coran' THEN 2
                    ELSE 3
                END"
            )
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
                    $allowedLevelNames = array_map(
                        [
                            VocalTestPrompt::class,
                            'normalizePathName',
                        ],
                        VocalTestPrompt::pathNamesForSubject(
                            $subject
                        )
                    );

                    $subjectLevels = $levels
                        ->where(
                            'subject_id',
                            $subject->id
                        )
                        ->filter(
                            fn (Level $level) =>
                                in_array(
                                    VocalTestPrompt
                                        ::normalizePathName(
                                            $level->name
                                        ),
                                    $allowedLevelNames,
                                    true
                                )
                        )
                        ->map(
                            function (
                                Level $level
                            ) use ($subject) {
                                $classes = $level
                                    ->classes
                                    ->filter(
                                        function (
                                            ClassRoom $classRoom
                                        ) use (
                                            $subject,
                                            $level
                                        ) {
                                            return
                                                $classRoom
                                                    ->subjects
                                                    ->contains(
                                                        'id',
                                                        $subject->id
                                                    )
                                                && VocalTestPrompt
                                                    ::isSupportedPath(
                                                        $subject,
                                                        $level,
                                                        $classRoom
                                                    )
                                                && !VocalTestPrompt
                                                    ::isExcludedPath(
                                                        $subject,
                                                        $level,
                                                        $classRoom
                                                    );
                                        }
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

    /**
     * Vérifier que le niveau appartient à la matière et que la classe appartient au niveau
     */
    private function validateCoherence(int $subjectId, int $levelId, int $classId): void
    {
        $subject = Subject::findOrFail($subjectId);
        $level   = Level::findOrFail($levelId);
        $class   = ClassRoom::findOrFail($classId);

        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            422,
            'Ce niveau n\'appartient pas à la matière sélectionnée.'
        );

        abort_unless(
            (int) $class->level_id === (int) $level->id,
            422,
            'Cette classe n\'appartient pas au niveau sélectionné.'
        );

        abort_unless(
            $class->subjects()
                ->where(
                    'subjects.id',
                    $subject->id
                )
                ->exists(),
            422,
            'Cette classe n\'est pas liée à la matière sélectionnée.'
        );

        abort_unless(
            VocalTestPrompt::isSupportedPath($subject, $level, $class),
            422,
            'Cette sélection ne fait pas partie de la nouvelle structure.'
        );

        abort_if(
            VocalTestPrompt::isExcludedPath($subject, $level, $class),
            422,
            'Aucun test vocal ne doit être créé pour un parcours d’Arabe en classe Débutant.'
        );
    }
}
