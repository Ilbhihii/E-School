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

        $isCompletionSubmission =
            $submission->isCompletionSubmission();

        $isObservationSubmission =
            $submission->isObservationSubmission();

        $isObservationAudioSubmission =
            $isObservationSubmission
            && !empty($submission->audio_path);

        $isLegacyObservationSubmission =
            $isObservationSubmission
            && !$isObservationAudioSubmission;

        $audioState = (
            $isCompletionSubmission
            || $isLegacyObservationSubmission
        )
            ? [
                'exists' => false,
                'playable' => false,
                'size' => null,
                'mime_type' => null,
                'error' => null,
            ]
            : $this->getAudioState($submission);

        $observationReview =
            $isObservationSubmission
                ? [
                    'response_mode' =>
                        $submission
                            ->observationResponseMode(),
                    'text' =>
                        $submission
                            ->observationText(),
                    'image_path' =>
                        $submission
                            ->observationImagePath(),
                    'image_original_name' =>
                        $submission
                            ->observationImageOriginalName(),
                    'image_mime_type' =>
                        $submission
                            ->observationImageMimeType(),
                    'image_size' =>
                        $submission
                            ->observationImageSize(),
                    'prompt_image' =>
                        data_get(
                            $submission->answer_data,
                            'prompt_image'
                        ),
                ]
                : null;

        $completionReview = $isCompletionSubmission
            ? [
                'answers' =>
                    $submission->completionAnswers(),
                'expected_answers' =>
                    $submission->completionExpectedAnswers(),
                'results' =>
                    $submission->completionResults(),
            ]
            : null;

        return view('admin.vocal-tests.submissions.show', [
            'submission' => $submission,
            'isCompletionSubmission' =>
                $isCompletionSubmission,
            'isObservationSubmission' =>
                $isObservationSubmission,
            'isObservationAudioSubmission' =>
                $isObservationAudioSubmission,
            'isLegacyObservationSubmission' =>
                $isLegacyObservationSubmission,
            'observationReview' =>
                $observationReview,
            'completionReview' => $completionReview,
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

    public function audio(Request $request, VocalTestSubmission $submission)
    {
        if (
            $submission->isObservationSubmission()
            && empty($submission->audio_path)
        ) {
            return $this->observationImage(
                $submission
            );
        }

        abort_if(
            $submission->isCompletionSubmission(),
            404,
            'Cet exercice ne contient aucun enregistrement audio.'
        );

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
        $fileSize = (int) filesize($absolutePath);

        abort_if(
            $fileSize <= 0,
            422,
            'Le fichier audio est vide.'
        );

        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        $filename = $submission->audio_original_name
            ? basename($submission->audio_original_name)
            : 'recitation-' . $submission->id . ($extension ? '.' . $extension : '');

        $safeFilename = preg_replace(
            '/[^A-Za-z0-9._-]/',
            '-',
            $filename
        ) ?: 'recitation-' . $submission->id . ($extension ? '.' . $extension : '');

        /*
         * Le lecteur HTML5 envoie souvent un en-tête Range pour récupérer
         * seulement une partie de l'audio. Cette méthode répond correctement
         * avec HTTP 206 Partial Content.
         */
        $start = 0;
        $end = $fileSize - 1;
        $statusCode = 200;

        $rangeHeader = $request->header('Range');

        if (
            is_string($rangeHeader)
            && preg_match('/bytes=(\d*)-(\d*)/i', $rangeHeader, $matches)
        ) {
            $requestedStart = $matches[1];
            $requestedEnd = $matches[2];

            if ($requestedStart === '' && $requestedEnd === '') {
                return response('', 416, [
                    'Content-Range' => 'bytes */' . $fileSize,
                    'Accept-Ranges' => 'bytes',
                ]);
            }

            if ($requestedStart === '') {
                // Exemple : bytes=-500 demande les 500 derniers octets.
                $suffixLength = max(1, (int) $requestedEnd);
                $start = max(0, $fileSize - $suffixLength);
            } else {
                $start = (int) $requestedStart;
            }

            if ($requestedEnd !== '') {
                $end = min((int) $requestedEnd, $fileSize - 1);
            }

            if ($start < 0 || $start >= $fileSize || $start > $end) {
                return response('', 416, [
                    'Content-Range' => 'bytes */' . $fileSize,
                    'Accept-Ranges' => 'bytes',
                ]);
            }

            $statusCode = 206;
        }

        $contentLength = $end - $start + 1;

        $headers = [
            'Content-Type' => $audioState['mime_type'],
            'Content-Disposition' => 'inline; filename="' . $safeFilename . '"',
            'Content-Length' => (string) $contentLength,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($statusCode === 206) {
            $headers['Content-Range'] = sprintf(
                'bytes %d-%d/%d',
                $start,
                $end,
                $fileSize
            );
        }

        return response()->stream(
            function () use ($absolutePath, $start, $contentLength): void {
                $handle = fopen($absolutePath, 'rb');

                if ($handle === false) {
                    return;
                }

                try {
                    fseek($handle, $start);

                    $remaining = $contentLength;
                    $chunkSize = 8192;

                    while ($remaining > 0 && !feof($handle)) {
                        $readLength = min($chunkSize, $remaining);
                        $buffer = fread($handle, $readLength);

                        if ($buffer === false || $buffer === '') {
                            break;
                        }

                        echo $buffer;

                        $remaining -= strlen($buffer);

                        if (connection_aborted()) {
                            break;
                        }

                        flush();
                    }
                } finally {
                    fclose($handle);
                }
            },
            $statusCode,
            $headers
        );
    }

    public function destroy(VocalTestSubmission $submission)
    {
        if (
            !empty($submission->audio_path)
            && Storage::disk('local')->exists($submission->audio_path)
        ) {
            Storage::disk('local')->delete($submission->audio_path);
        }

        $observationImagePath =
            $submission->observationImagePath();

        if (
            $observationImagePath
            && Storage::disk('local')
                ->exists($observationImagePath)
        ) {
            Storage::disk('local')
                ->delete($observationImagePath);
        }

        $submission->delete();

        return redirect()
            ->route('admin.vocal-tests.submissions.index')
            ->with('success', 'Soumission vocale supprimée.');
    }

    private function observationImage(
        VocalTestSubmission $submission
    ) {
        $path =
            $submission->observationImagePath();

        abort_unless(
            $path,
            404,
            'Aucune photo n’est associée à cette réponse.'
        );

        $disk = Storage::disk('local');

        abort_unless(
            $disk->exists($path),
            404,
            'La photo de la réponse est introuvable.'
        );

        $absolutePath = $disk->path($path);
        $mimeType =
            $submission->observationImageMimeType()
            ?: 'image/jpeg';

        $filename = basename(
            $submission
                ->observationImageOriginalName()
            ?: 'observation-' . $submission->id
        );

        $safeFilename = preg_replace(
            '/[^A-Za-z0-9._-]/',
            '-',
            $filename
        ) ?: 'observation-' . $submission->id . '.jpg';

        return response()->file(
            $absolutePath,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' =>
                    'inline; filename="'
                    . $safeFilename
                    . '"',
                'Cache-Control' =>
                    'private, no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Content-Type-Options' =>
                    'nosniff',
            ]
        );
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