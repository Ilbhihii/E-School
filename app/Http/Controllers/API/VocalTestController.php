<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VocalTestController extends Controller
{
    /**
     * GET /api/vocal-test/text?subject_id=&level_id=&class_id=
     */
    public function recitationText(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'level_id' => 'required|exists:levels,id',
            'class_id' => 'required|exists:class_rooms,id',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        $level = Level::findOrFail($validated['level_id']);
        $classRoom = ClassRoom::findOrFail($validated['class_id']);

        $this->validatePath($subject, $level, $classRoom);

        if (!VocalTestPrompt::isSupportedPath(
            $subject,
            $level,
            $classRoom
        )) {
            return response()->json([
                'success' => false,
                'requires_vocal_test' => false,
                'message' => 'Ce parcours a été remplacé par la nouvelle structure.',
            ], 404);
        }

        if (VocalTestPrompt::isExcludedPath(
            $subject,
            $level,
            $classRoom
        )) {
            return response()->json([
                'success' => true,
                'requires_vocal_test' => false,
                'message' => 'Aucun test vocal n’est demandé pour ce parcours débutant.',
                'data' => null,
            ]);
        }

        $prompt = VocalTestPrompt::activeForPath(
            $subject,
            $level,
            $classRoom
        );

        if (!$prompt) {
            return response()->json([
                'success' => false,
                'requires_vocal_test' => false,
                'message' => 'Aucun test vocal actif n’est disponible.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'requires_vocal_test' => true,
            'data' => [
                'id' => $prompt->id,
                'title' => $prompt->title,
                'instructions' => $prompt->instructions,
                'reading_text' => $prompt->reading_text,
                'test_mode' => $prompt->test_mode,
                'preparation_seconds' => $prompt->preparation_seconds,
                'maximum_duration' => $prompt->maximum_duration,
                'hide_text_during_recording' => $prompt->hide_text_during_recording,
                'subject' => $subject->name,
                'level' => $level->name,
                'class' => $classRoom->name,
                'interaction_type' =>
                    VocalTestPrompt::isInteractiveCompletionPath(
                        $subject,
                        $level,
                        $classRoom,
                        $prompt
                    )
                        ? VocalTestSubmission::TYPE_COMPLETION
                        : VocalTestSubmission::TYPE_AUDIO,
                'completion' =>
                    VocalTestPrompt::isInteractiveCompletionPath(
                        $subject,
                        $level,
                        $classRoom,
                        $prompt
                    )
                        ? VocalTestPrompt::completionDefinition()
                        : null,
            ],
        ]);
    }

    /**
     * POST /api/vocal-test/submit
     */
    public function submit(Request $request)
    {
        $base = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'level_id' => 'required|exists:levels,id',
            'class_id' => 'required|exists:class_rooms,id',
        ]);

        $subject = Subject::findOrFail($base['subject_id']);
        $level = Level::findOrFail($base['level_id']);
        $classRoom = ClassRoom::findOrFail($base['class_id']);

        $this->validatePath($subject, $level, $classRoom);

        if (
            !VocalTestPrompt::isSupportedPath(
                $subject,
                $level,
                $classRoom
            )
            || VocalTestPrompt::isExcludedPath(
                $subject,
                $level,
                $classRoom
            )
        ) {
            return response()->json([
                'success' => false,
                'requires_vocal_test' => false,
                'message' =>
                    'Aucun test ne peut être envoyé pour cette sélection.',
            ], 422);
        }

        $prompt = VocalTestPrompt::activeForPath(
            $subject,
            $level,
            $classRoom
        );

        if (!$prompt) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun test actif n’est disponible.',
            ], 404);
        }

        if (
            VocalTestPrompt::isInteractiveCompletionPath(
                $subject,
                $level,
                $classRoom,
                $prompt
            )
        ) {
            return $this->submitCompletion(
                $request,
                $subject,
                $level,
                $classRoom,
                $prompt
            );
        }

        $validated = $request->validate([
            'audio' => [
                'required',
                'file',
                'mimetypes:audio/webm,audio/ogg,audio/mpeg,audio/wav,audio/x-wav,audio/mp4,video/webm',
                'max:20480',
            ],
            'duration_seconds' =>
                'nullable|integer|min:1|max:600',
        ]);

        $file = $validated['audio'];
        $audioPath = $file->store('vocal-tests');

        $submission = VocalTestSubmission::create([
            'user_id' => $request->user()->id,
            'vocal_test_prompt_id' => $prompt->id,
            'subject_id' => $subject->id,
            'level_id' => $level->id,
            'class_id' => $classRoom->id,
            'reading_text' => $prompt->reading_text,
            'test_mode' => $prompt->test_mode,
            'submission_type' =>
                VocalTestSubmission::TYPE_AUDIO,
            'audio_path' => $audioPath,
            'audio_original_name' =>
                $file->getClientOriginalName(),
            'audio_mime_type' =>
                $file->getClientMimeType()
                ?: $file->getMimeType(),
            'audio_size' => $file->getSize(),
            'duration_seconds' =>
                $validated['duration_seconds'] ?? null,
            'status' =>
                VocalTestSubmission::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test vocal soumis avec succès.',
            'data' => [
                'id' => $submission->id,
                'submission_type' =>
                    $submission->submission_type,
                'consumed_at' => $submission->consumed_at,
            ],
        ], 201);
    }

    private function submitCompletion(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $classRoom,
        VocalTestPrompt $prompt
    ) {
        $definition = VocalTestPrompt::completionDefinition();
        $expected = $definition['expected_answers'];
        $allowed = $definition['choices'];
        $total = count($expected);

        $validated = $request->validate([
            'answers' => ['required', 'array', 'size:' . $total],
            'answers.*' => [
                'required',
                'string',
                Rule::in($allowed),
            ],
        ]);

        $answers = array_values($validated['answers']);

        if (count(array_unique($answers)) !== $total) {
            throw ValidationException::withMessages([
                'answers' =>
                    'Chaque carte ne peut être utilisée qu’une fois.',
            ]);
        }

        $results = [];
        $correct = 0;

        foreach ($expected as $index => $word) {
            $isCorrect = ($answers[$index] ?? null) === $word;
            $results[] = $isCorrect;
            $correct += $isCorrect ? 1 : 0;
        }

        $score = (int) round(($correct / max(1, $total)) * 100);

        $submission = VocalTestSubmission::create([
            'user_id' => $request->user()->id,
            'vocal_test_prompt_id' => $prompt->id,
            'subject_id' => $subject->id,
            'level_id' => $level->id,
            'class_id' => $classRoom->id,
            'reading_text' => $prompt->reading_text,
            'test_mode' => $prompt->test_mode,
            'submission_type' =>
                VocalTestSubmission::TYPE_COMPLETION,
            'answer_data' => [
                'answers' => $answers,
                'expected_answers' => $expected,
                'results' => $results,
            ],
            'auto_correct_count' => $correct,
            'auto_total_questions' => $total,
            'score' => $score,
            'score_memorization' => $score,
            'final_score' => $score,
            'status' =>
                VocalTestSubmission::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Réponses de complétion enregistrées.',
            'data' => [
                'id' => $submission->id,
                'submission_type' =>
                    $submission->submission_type,
                'score' => $score,
                'correct_answers' => $correct,
                'total_questions' => $total,
            ],
        ], 201);
    }

    public function submissions(Request $request)
    {
        $submissions = VocalTestSubmission::where(
            'user_id',
            $request->user()->id
        )
            ->with([
                'subject',
                'level',
                'classRoom',
                'prompt',
                'appointment',
            ])
            ->latest()
            ->get()
            ->map(fn($submission) => [
                'id' => $submission->id,
                'subject' => $submission->subject?->name,
                'level' => $submission->level?->name,
                'class' => $submission->classRoom?->name,
                'test_title' => $submission->prompt?->title,
                'test_mode' => $submission->test_mode,
                'submission_type' =>
                    $submission->submission_type,
                'status' => $submission->status,
                'score' => $submission->final_score
                    ?? $submission->score,
                'consumed_at' => $submission->consumed_at,
                'has_appointment' => $submission->appointment !== null,
                'appointment_status' => $submission->appointment?->status,
                'created_at' => $submission->created_at,
                'submitted_at' => $submission->submitted_at,
            ]);

        return response()->json([
            'success' => true,
            'data' => $submissions,
        ]);
    }

    private function validatePath(
        Subject $subject,
        Level $level,
        ClassRoom $classRoom
    ): void {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            404,
            'Ce niveau n’appartient pas à cette matière.'
        );

        abort_unless(
            (int) $classRoom->level_id === (int) $level->id,
            404,
            'Cette classe n’appartient pas à ce niveau.'
        );

        abort_unless(
            $classRoom->subjects()
                ->where('subjects.id', $subject->id)
                ->exists(),
            404,
            'Cette matière n’est pas liée à cette classe.'
        );
    }
}