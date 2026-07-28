<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class VocalTestController extends Controller
{
    /**
     * Récupérer le texte de récitation pour un test vocal donné
     * GET /api/vocal-test/text?subject_id=&level_id=&class_id=
     */
    public function recitationText(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'level_id'   => 'required|exists:levels,id',
            'class_id'   => 'required|exists:class_rooms,id',
        ]);

        $subject   = Subject::findOrFail($validated['subject_id']);
        $level     = Level::findOrFail($validated['level_id']);
        $classRoom = ClassRoom::findOrFail($validated['class_id']);

        $this->validatePath($subject, $level, $classRoom);

        $prompt = VocalTestPrompt::query()
            ->where('subject_id', $subject->id)
            ->where('level_id', $level->id)
            ->where('class_id', $classRoom->id)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                     => $prompt->id,
                'title'                  => $prompt->title,
                'instructions'           => $prompt->instructions,
                'reading_text'           => $prompt->reading_text,
                'test_mode'              => $prompt->test_mode,
                'preparation_seconds'    => $prompt->preparation_seconds,
                'maximum_duration'       => $prompt->maximum_duration,
                'hide_text_during_recording' => $prompt->hide_text_during_recording,
                'subject'                => $subject->name,
                'level'                  => $level->name,
                'class'                  => $classRoom->name,
            ],
        ]);
    }

    /**
     * Soumettre un test vocal
     * POST /api/vocal-test/submit
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'subject_id'       => 'required|exists:subjects,id',
            'level_id'         => 'required|exists:levels,id',
            'class_id'         => 'required|exists:class_rooms,id',
            'audio'            => [
                'required',
                'file',
                'mimetypes:audio/webm,audio/ogg,audio/mpeg,audio/wav,audio/x-wav,audio/mp4,video/webm',
                'max:20480',
            ],
            'duration_seconds' => 'nullable|integer|min:1|max:600',
        ]);

        $subject   = Subject::findOrFail($validated['subject_id']);
        $level     = Level::findOrFail($validated['level_id']);
        $classRoom = ClassRoom::findOrFail($validated['class_id']);

        $this->validatePath($subject, $level, $classRoom);

        $prompt = VocalTestPrompt::query()
            ->where('subject_id', $subject->id)
            ->where('level_id', $level->id)
            ->where('class_id', $classRoom->id)
            ->where('is_active', true)
            ->firstOrFail();

        $file = $validated['audio'];
        $mimeType = $file->getClientMimeType() ?: $file->getMimeType();
        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        // Store audio
        $audioPath = $file->store('vocal-tests');

        $submission = VocalTestSubmission::create([
            'user_id'             => auth()->id(),
            'vocal_test_prompt_id' => $prompt->id,
            'subject_id'          => $subject->id,
            'level_id'            => $level->id,
            'class_id'            => $classRoom->id,
            'reading_text'        => $prompt->reading_text,
            'test_mode'           => $prompt->test_mode,
            'audio_path'          => $audioPath,
            'audio_original_name' => $originalName,
            'audio_mime_type'     => $mimeType,
            'audio_size'          => $fileSize,
            'duration_seconds'    => $validated['duration_seconds'] ?? null,
            'status'              => 'submitted',
            'submitted_at'        => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test vocal soumis avec succès.',
            'data'    => [
                'id'          => $submission->id,
                'consumed_at' => $submission->consumed_at,
            ],
        ], 201);
    }

    /**
     * Liste des soumissions de l'utilisateur connecté
     * GET /api/vocal-test/submissions
     */
    public function submissions(Request $request)
    {
        $submissions = VocalTestSubmission::where('user_id', $request->user()->id)
            ->with(['subject', 'level', 'classRoom', 'prompt', 'appointment'])
            ->latest()
            ->get()
            ->map(fn($sub) => [
                'id'                 => $sub->id,
                'subject'            => $sub->subject?->name,
                'level'              => $sub->level?->name,
                'class'              => $sub->classRoom?->name,
                'test_title'         => $sub->prompt?->title,
                'test_mode'          => $sub->test_mode,
                'status'             => $sub->status,
                'score'              => $sub->final_score ?? $sub->score,
                'consumed_at'        => $sub->consumed_at,
                'has_appointment'    => $sub->appointment !== null,
                'appointment_status' => $sub->appointment?->status,
                'created_at'         => $sub->created_at,
                'submitted_at'       => $sub->submitted_at,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $submissions,
        ]);
    }

    /**
     * Vérifier la cohérence matière / niveau / classe
     */
    private function validatePath(Subject $subject, Level $level, ClassRoom $classRoom): void
    {
        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            404,
            'Ce niveau n\'appartient pas à cette matière.'
        );

        abort_unless(
            (int) $classRoom->level_id === (int) $level->id,
            404,
            'Cette classe n\'appartient pas à ce niveau.'
        );

        abort_unless(
            $classRoom->subjects()->where('subjects.id', $subject->id)->exists(),
            404,
            'Cette matière n\'est pas liée à cette classe.'
        );
    }
}
