<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VocalTestSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = VocalTestSubmission::with([
            'user',
            'subject',
            'level',
            'classRoom',
            'prompt',
            'appointment',
        ])->orderByDesc('created_at');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('test_mode') && $request->test_mode !== 'all') {
            $query->where('test_mode', $request->test_mode);
        }

        $submissions = $query->paginate(20)->withQueryString();

        return view('admin.vocal-tests.submissions.index', compact('submissions'));
    }

    public function show(VocalTestSubmission $submission)
    {
        $submission->load([
            'user',
            'subject',
            'level',
            'classRoom',
            'prompt',
            'appointment',
        ]);

        $audioState = $this->getAudioState($submission);

        return view('admin.vocal-tests.submissions.show', [
            'submission' => $submission,
            'audioExists' => $audioState['exists'],
            'audioPlayable' => $audioState['playable'],
            'audioSize' => $audioState['size'],
            'audioMimeType' => $audioState['mime_type'],
            'audioError' => $audioState['error'],
        ]);
    }

    public function review(Request $request, VocalTestSubmission $submission)
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,under_review,reviewed,accepted,needs_improvement',
            'teacher_comment' => 'nullable|string|max:2000',
            'score' => 'nullable|integer|min:0|max:100',
            'score_pronunciation' => 'nullable|integer|min:0|max:100',
            'score_tajwid' => 'nullable|integer|min:0|max:100',
            'score_memorization' => 'nullable|integer|min:0|max:100',
            'score_fluency' => 'nullable|integer|min:0|max:100',
        ]);

        if (in_array($validated['status'], [
            VocalTestSubmission::STATUS_REVIEWED,
            VocalTestSubmission::STATUS_ACCEPTED,
            VocalTestSubmission::STATUS_NEEDS_IMPROVEMENT,
        ], true)) {
            $validated['reviewed_at'] = now();
        }

        $submission->update($validated);

        $hasMultiScores = $request->filled('score_pronunciation')
            || $request->filled('score_tajwid')
            || $request->filled('score_memorization')
            || $request->filled('score_fluency');

        if ($hasMultiScores) {
            $submission->refresh();
            $finalScore = $submission->calculateFinalScore();

            if ($finalScore !== null) {
                $submission->updateQuietly([
                    'final_score' => $finalScore,
                    'score' => $finalScore,
                ]);
            }
        }

        return redirect()
            ->route('admin.vocal-tests.submissions.show', $submission)
            ->with('success', 'Évaluation enregistrée avec succès.');
    }

    public function audio(VocalTestSubmission $submission)
    {
        $audioState = $this->getAudioState($submission);

        abort_unless(
            $audioState['exists'],
            404,
            $audioState['error'] ?: 'Le fichier audio est introuvable.'
        );

        abort_unless(
            $audioState['playable'],
            422,
            $audioState['error'] ?: 'Le fichier audio est vide ou invalide.'
        );

        $disk = Storage::disk('local');
        $absolutePath = $disk->path($submission->audio_path);
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        /*
         * Ne pas utiliser mime_content_type() ici :
         * Windows peut annoncer un mauvais type pour WebM.
         * Le type est imposé selon l’extension réelle.
         */
        $mimeType = match ($extension) {
            'mp3', 'mpeg' => 'audio/mpeg',
            'webm' => 'audio/webm',
            'ogg', 'oga' => 'audio/ogg',
            'wav' => 'audio/wav',
            'm4a', 'mp4' => 'audio/mp4',
            'aac' => 'audio/aac',
            default => $audioState['mime_type'] ?: 'application/octet-stream',
        };

        $filename = $submission->audio_original_name
            ? basename($submission->audio_original_name)
            : 'recitation-' . $submission->id . ($extension ? '.' . $extension : '');

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy(VocalTestSubmission $submission)
    {
        if (
            !empty($submission->audio_path)
            && Storage::disk('local')->exists($submission->audio_path)
        ) {
            Storage::disk('local')->delete($submission->audio_path);
        }

        $submission->delete();

        return redirect()
            ->route('admin.vocal-tests.submissions.index')
            ->with('success', 'Soumission vocale supprimée.');
    }

    /**
     * Retourne l'état réel du fichier audio et normalise son type MIME.
     * Un WAV de 44 octets ne contient que son en-tête et aucun son.
     */
    private function getAudioState(VocalTestSubmission $submission): array
    {
        $default = [
            'exists' => false,
            'playable' => false,
            'size' => null,
            'mime_type' => 'audio/webm',
            'error' => null,
        ];

        if (empty($submission->audio_path)) {
            $default['error'] = 'Aucun fichier audio n’est associé à cette soumission.';

            return $default;
        }

        $disk = Storage::disk('local');

        if (!$disk->exists($submission->audio_path)) {
            $default['error'] = 'Le fichier audio est introuvable dans storage/app.';

            return $default;
        }

        $absolutePath = $disk->path($submission->audio_path);
        $size = (int) $disk->size($submission->audio_path);
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $storedMimeType = strtolower(trim((string) $submission->audio_mime_type));

        $mimeType = $this->normalizeAudioMimeType(
            $storedMimeType,
            $extension,
            $absolutePath
        );

        $error = null;
        $playable = true;

        if ($size <= 0) {
            $playable = false;
            $error = 'Le fichier audio est vide.';
        } elseif ($extension === 'wav' && $size <= 44) {
            $playable = false;
            $error = 'Ce fichier WAV ne contient aucun son (44 octets seulement). Il faut refaire l’enregistrement.';
        } elseif ($size < 512) {
            $playable = false;
            $error = 'Le fichier audio est trop petit et semble incomplet. Il faut refaire l’enregistrement.';
        }

        return [
            'exists' => true,
            'playable' => $playable,
            'size' => $size,
            'mime_type' => $mimeType,
            'error' => $error,
        ];
    }

    private function normalizeAudioMimeType(
        string $storedMimeType,
        string $extension,
        string $absolutePath
    ): string {
        $mimeByExtension = [
            'webm' => 'audio/webm',
            'wav' => 'audio/wav',
            'mp3' => 'audio/mpeg',
            'mpeg' => 'audio/mpeg',
            'ogg' => 'audio/ogg',
            'oga' => 'audio/ogg',
            'm4a' => 'audio/mp4',
            'mp4' => 'audio/mp4',
            'aac' => 'audio/aac',
        ];

        if (isset($mimeByExtension[$extension])) {
            return $mimeByExtension[$extension];
        }

        if (strpos($storedMimeType, 'webm') !== false) {
            return 'audio/webm';
        }

        if (strpos($storedMimeType, 'wav') !== false) {
            return 'audio/wav';
        }

        if (strpos($storedMimeType, 'mpeg') !== false || strpos($storedMimeType, 'mp3') !== false) {
            return 'audio/mpeg';
        }

        if (strpos($storedMimeType, 'ogg') !== false) {
            return 'audio/ogg';
        }

        if (strpos($storedMimeType, 'mp4') !== false || strpos($storedMimeType, 'm4a') !== false) {
            return 'audio/mp4';
        }

        $detectedMimeType = function_exists('mime_content_type')
            ? mime_content_type($absolutePath)
            : false;

        if (is_string($detectedMimeType) && strpos($detectedMimeType, 'audio/') === 0) {
            return $detectedMimeType;
        }

        return 'application/octet-stream';
    }
}