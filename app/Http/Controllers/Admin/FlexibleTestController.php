<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\FlexibleTest;
use App\Models\Level;
use App\Models\Subject;
use App\Models\User;
use App\Models\VocalTestPrompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FlexibleTestController extends Controller
{
    private function assertAdmin(): void
    {
        abort_unless(
            auth()->check()
            && auth()->user()->role === User::ROLE_ADMIN,
            403
        );
    }

    public function index()
    {
        $this->assertAdmin();

        return redirect()->route('admin.vocal-tests.prompts.index');
    }

    public function create()
    {
        $this->assertAdmin();

        /*
         * Le créateur de test ne doit proposer que la structure
         * pédagogique réellement disponible :
         *
         * Matière ACTIVE → Niveau de cette matière → Classe de ce niveau.
         *
         * Les filtres "status" sur levels / class_rooms sont appliqués
         * uniquement si ces colonnes existent dans la base.
         */
        $subjectQuery = Subject::query();

        if (Schema::hasColumn('subjects', 'status')) {
            $subjectQuery->where('status', 'active');
        }

        $subjects = $subjectQuery
            ->whereHas('levels', function ($levelQuery) {
                $levelQuery->whereHas('classes');
            })
            ->orderBy('name')
            ->get();

        $subjectIds = $subjects
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        $levelQuery = Level::query()
            ->whereIn('subject_id', $subjectIds);

        if (Schema::hasColumn('levels', 'status')) {
            $levelQuery->where('status', 'active');
        }

        $levels = $levelQuery
            ->whereHas('classes')
            ->orderBy('name')
            ->get();

        $levelIds = $levels
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        $classQuery = ClassRoom::query()
            ->whereIn('level_id', $levelIds);

        if (Schema::hasColumn('class_rooms', 'status')) {
            $classQuery->where('status', 'active');
        }

        $classes = $classQuery
            ->orderBy('name')
            ->get();

        return view(
            'admin.flexible-tests.create',
            compact('subjects', 'levels', 'classes')
        );
    }

    public function store(Request $request)
    {
        $this->assertAdmin();

        $validated = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],

            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:10000'],

            'source_type' => [
                'required',
                Rule::in([
                    FlexibleTest::SOURCE_TEXT,
                    FlexibleTest::SOURCE_FILES,
                    FlexibleTest::SOURCE_MIXED,
                ]),
            ],
            'source_text' => ['nullable', 'string', 'max:100000'],

            'source_files' => ['nullable', 'array', 'max:30'],
            'source_files.*' => [
                'file',
                'max:102400',
                'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,txt',
            ],

            'response_type' => [
                'required',
                Rule::in([
                    FlexibleTest::RESPONSE_VOCAL,
                    FlexibleTest::RESPONSE_WRITTEN,
                    FlexibleTest::RESPONSE_QCM,
                ]),
            ],

            'written_response_mode' => [
                'nullable',
                Rule::in(['text', 'file', 'both']),
            ],

            'vocal_mode' => [
                'nullable',
                Rule::in(['reading', 'tajwid', 'hifd', 'free']),
            ],

            'preparation_seconds' => [
                'nullable',
                'integer',
                'min:0',
                'max:600',
            ],

            'maximum_duration' => [
                'nullable',
                'integer',
                'min:15',
                'max:7200',
            ],

            'questions' => ['nullable', 'array', 'max:100'],
            'questions.*.prompt' => ['nullable', 'string', 'max:5000'],
            'questions.*.choice_a' => ['nullable', 'string', 'max:2000'],
            'questions.*.choice_b' => ['nullable', 'string', 'max:2000'],
            'questions.*.choice_c' => ['nullable', 'string', 'max:2000'],
            'questions.*.choice_d' => ['nullable', 'string', 'max:2000'],
            'questions.*.correct' => [
                'nullable',
                Rule::in(['a', 'b', 'c', 'd']),
            ],

            'is_active' => ['nullable', 'boolean'],
        ]);

        $subject = Subject::query()->findOrFail($validated['subject_id']);
        $level = Level::query()->findOrFail($validated['level_id']);
        $classRoom = ClassRoom::query()->findOrFail($validated['class_id']);

        if (
            Schema::hasColumn('subjects', 'status')
            && (string) $subject->status !== 'active'
        ) {
            throw ValidationException::withMessages([
                'subject_id' =>
                    'Cette matière est inactive et ne peut pas recevoir de test.',
            ]);
        }

        if (
            Schema::hasColumn('levels', 'status')
            && (string) $level->status !== 'active'
        ) {
            throw ValidationException::withMessages([
                'level_id' =>
                    'Ce niveau est inactif et ne peut pas recevoir de test.',
            ]);
        }

        if (
            Schema::hasColumn('class_rooms', 'status')
            && (string) $classRoom->status !== 'active'
        ) {
            throw ValidationException::withMessages([
                'class_id' =>
                    'Cette classe est inactive et ne peut pas recevoir de test.',
            ]);
        }

        if ((int) $level->subject_id !== (int) $validated['subject_id']) {
            throw ValidationException::withMessages([
                'level_id' => 'Le niveau ne correspond pas à la matière choisie.',
            ]);
        }

        if ((int) $classRoom->level_id !== (int) $level->id) {
            throw ValidationException::withMessages([
                'class_id' => 'La classe ne correspond pas au niveau choisi.',
            ]);
        }

        $sourceType = $validated['source_type'];
        $responseType = $validated['response_type'];

        if (
            in_array($sourceType, ['text', 'mixed'], true)
            && blank($validated['source_text'] ?? null)
        ) {
            throw ValidationException::withMessages([
                'source_text' =>
                    'Ajoutez le texte du sujet pour ce type de support.',
            ]);
        }

        if (
            in_array($sourceType, ['files', 'mixed'], true)
            && !$request->hasFile('source_files')
        ) {
            throw ValidationException::withMessages([
                'source_files' =>
                    'Ajoutez au moins une image ou un document.',
            ]);
        }

        if (
            $responseType === FlexibleTest::RESPONSE_WRITTEN
            && empty($validated['written_response_mode'])
        ) {
            throw ValidationException::withMessages([
                'written_response_mode' =>
                    'Choisissez la manière dont l’étudiant répondra à l’écrit.',
            ]);
        }

        if (
            $responseType === FlexibleTest::RESPONSE_VOCAL
            && empty($validated['vocal_mode'])
        ) {
            throw ValidationException::withMessages([
                'vocal_mode' =>
                    'Choisissez le mode du test vocal.',
            ]);
        }

        $qcmQuestions = null;

        if ($responseType === FlexibleTest::RESPONSE_QCM) {
            $qcmQuestions = collect($validated['questions'] ?? [])
                ->filter(function ($question) {
                    return filled($question['prompt'] ?? null);
                })
                ->map(function ($question) {
                    $requiredKeys = [
                        'prompt',
                        'choice_a',
                        'choice_b',
                        'choice_c',
                        'choice_d',
                        'correct',
                    ];

                    foreach ($requiredKeys as $key) {
                        if (blank($question[$key] ?? null)) {
                            throw ValidationException::withMessages([
                                'questions' =>
                                    'Chaque question QCM doit avoir un énoncé, '
                                    . '4 choix et une bonne réponse.',
                            ]);
                        }
                    }

                    return [
                        'prompt' => trim($question['prompt']),
                        'choices' => [
                            'a' => trim($question['choice_a']),
                            'b' => trim($question['choice_b']),
                            'c' => trim($question['choice_c']),
                            'd' => trim($question['choice_d']),
                        ],
                        'correct' => $question['correct'],
                    ];
                })
                ->values()
                ->all();

            if (empty($qcmQuestions)) {
                throw ValidationException::withMessages([
                    'questions' =>
                        'Ajoutez au moins une question pour un test QCM.',
                ]);
            }
        }

        if ($responseType === FlexibleTest::RESPONSE_VOCAL) {
            $subjectForVocal = Subject::query()
                ->findOrFail($validated['subject_id']);

            if (
                !VocalTestPrompt::isSupportedPath(
                    $subjectForVocal,
                    $level,
                    $classRoom
                )
                || VocalTestPrompt::isExcludedPath(
                    $subjectForVocal,
                    $level,
                    $classRoom
                )
            ) {
                throw ValidationException::withMessages([
                    'response_type' =>
                        'Le test vocal n’est pas disponible pour ce parcours. '
                        . 'Choisissez Écrit ou QCM, ou un autre parcours.',
                ]);
            }
        }
        $storedFiles = [];

        foreach ($request->file('source_files', []) as $file) {
            $path = $file->store('flexible-tests/sources');

            $storedFiles[] = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ];
        }

        $flexibleTest = FlexibleTest::create([
            'subject_id' => $validated['subject_id'],
            'level_id' => $validated['level_id'],
            'class_id' => $validated['class_id'],
            'title' => trim($validated['title']),
            'instructions' => $validated['instructions'] ?? null,
            'source_type' => $sourceType,
            'source_text' => $validated['source_text'] ?? null,
            'source_files' => $storedFiles ?: null,
            'response_type' => $responseType,
            'written_response_mode' =>
                $responseType === FlexibleTest::RESPONSE_WRITTEN
                    ? $validated['written_response_mode']
                    : null,
            'vocal_mode' =>
                $responseType === FlexibleTest::RESPONSE_VOCAL
                    ? $validated['vocal_mode']
                    : null,
            'qcm_questions' => $qcmQuestions,
            'preparation_seconds' =>
                (int) ($validated['preparation_seconds'] ?? 0),
            'maximum_duration' =>
                (int) ($validated['maximum_duration'] ?? 120),
            'is_active' => $request->boolean('is_active'),
            'created_by' => auth()->id(),
        ]);

        /*
         * Un test créé comme "Vocal" dans le nouveau créateur
         * alimente aussi VocalTestPrompt.
         *
         * Cela permet au moteur vocal étudiant déjà existant de continuer
         * à récupérer son test sans maintenir deux créations séparées.
         */
        if ($responseType === FlexibleTest::RESPONSE_VOCAL) {
            $legacyMode = $validated['vocal_mode'] ?? 'reading';

            if (!in_array($legacyMode, ['reading', 'tajwid', 'hifd'], true)) {
                $legacyMode = 'reading';
            }

            $legacyText = trim((string) (
                $validated['source_text']
                ?? $validated['instructions']
                ?? $validated['title']
            ));

            if ($legacyText === '') {
                $legacyText = trim((string) $validated['title']);
            }

            VocalTestPrompt::updateOrCreate(
                [
                    'subject_id' => $validated['subject_id'],
                    'level_id' => $validated['level_id'],
                    'class_id' => $validated['class_id'],
                ],
                [
                    'title' => trim($validated['title']),
                    'instructions' => $validated['instructions'] ?? null,
                    'reading_text' => $legacyText,
                    'test_mode' => $legacyMode,
                    'preparation_seconds' =>
                        (int) ($validated['preparation_seconds'] ?? 0),
                    'maximum_duration' =>
                        min(
                            600,
                            max(
                                15,
                                (int) ($validated['maximum_duration'] ?? 120)
                            )
                        ),
                    'hide_text_during_recording' =>
                        $legacyMode === 'hifd',
                    'is_active' => $request->boolean('is_active'),
                ]
            );
        }

        return redirect()
            ->route('admin.vocal-tests.prompts.index')
            ->with(
                'success',
                $responseType === FlexibleTest::RESPONSE_VOCAL
                    ? 'Test vocal créé et synchronisé avec le parcours étudiant.'
                    : 'Test créé avec succès.'
            );
    }

    public function downloadFile(FlexibleTest $flexibleTest, int $index)
    {
        $this->assertAdmin();

        $files = $flexibleTest->source_files ?: [];

        abort_unless(isset($files[$index]), 404);

        $file = $files[$index];
        $path = $file['path'] ?? null;

        abort_unless(
            $path && Storage::exists($path),
            404
        );

        return Storage::download(
            $path,
            $file['name'] ?? basename($path)
        );
    }
}