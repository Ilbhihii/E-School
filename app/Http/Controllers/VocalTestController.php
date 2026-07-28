<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use App\Models\VocalTestSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;
use Throwable;

class VocalTestController extends Controller
{
    public function create(Subject $subject, Level $level, ClassRoom $class)
    {
        $this->validatePath($subject, $level, $class);

        if (!VocalTestPrompt::isSupportedPath($subject, $level, $class)) {
            return redirect()
                ->route('front.subject.levels', $subject->id)
                ->with(
                    'info',
                    'Ce parcours a été remplacé par la nouvelle structure.'
                );
        }

        if (VocalTestPrompt::isExcludedPath($subject, $level, $class)) {
            return $this->redirectExcludedPath();
        }

        $prompt = $this->findPrompt($subject, $level, $class);

        $isCompletionTest =
            VocalTestPrompt::isInteractiveCompletionPath(
                $subject,
                $level,
                $class,
                $prompt
            );

        $completionDefinition = null;

        if ($isCompletionTest) {
            $completionDefinition =
                VocalTestPrompt::completionDefinition();

            shuffle($completionDefinition['choices']);
        }

        return view('front.vocal-test', [
            'subject' => $subject,
            'level' => $level,
            'class' => $class,
            'prompt' => $prompt,
            'isCompletionTest' => $isCompletionTest,
            'completionDefinition' => $completionDefinition,
        ]);
    }

    public function store(Request $request, Subject $subject, Level $level, ClassRoom $class)
    {
        $this->validatePath($subject, $level, $class);

        if (!VocalTestPrompt::isSupportedPath($subject, $level, $class)) {
            return redirect()
                ->route('front.subject.levels', $subject->id)
                ->with(
                    'info',
                    'Ce parcours a été remplacé par la nouvelle structure.'
                );
        }

        if (VocalTestPrompt::isExcludedPath($subject, $level, $class)) {
            return $this->redirectExcludedPath();
        }

        $prompt = $this->findPrompt($subject, $level, $class);

        if (
            VocalTestPrompt::isInteractiveCompletionPath(
                $subject,
                $level,
                $class,
                $prompt
            )
        ) {
            return $this->storeCompletionTest(
                $request,
                $subject,
                $level,
                $class,
                $prompt
            );
        }

        $validated = $request->validate([
            'audio' => [
                'required',
                'file',
                'mimetypes:audio/webm,audio/ogg,audio/mpeg,audio/mp4,audio/wav,audio/x-wav,audio/aac,video/webm',
                'min:1',
                'max:20480',
            ],
            'duration_seconds' => [
                'required',
                'integer',
                'min:1',
                'max:' . $prompt->maximum_duration,
            ],
        ], [
            'audio.required' => 'Veuillez enregistrer votre lecture avant de continuer.',
            'audio.min' => 'L’enregistrement est vide. Vérifiez votre microphone et recommencez.',
            'audio.max' => 'L’enregistrement ne doit pas dépasser 20 Mo.',
            'audio.mimetypes' => 'Format audio non supporté.',
            'duration_seconds.required' => 'La durée de l’enregistrement est manquante.',
            'duration_seconds.min' => 'L’enregistrement doit durer au moins une seconde.',
            'duration_seconds.max' => 'La durée de l’enregistrement dépasse la durée maximale autorisée.',
        ]);

        $file = $validated['audio'];
        $fileSize = (int) $file->getSize();

        // Un fichier WAV vide contient généralement seulement un en-tête de 44 octets.
        if (!$file->isValid() || $fileSize < 512) {
            throw ValidationException::withMessages([
                'audio' => 'L’enregistrement est vide ou incomplet. Vérifiez votre microphone et recommencez.',
            ]);
        }

        $mimeType = $this->normalizeMimeType(
            (string) ($file->getClientMimeType() ?: $file->getMimeType()),
            strtolower((string) $file->getClientOriginalExtension())
        );

        $storedPath = $file->store('vocal-tests', 'local');

        if (!$storedPath || !Storage::disk('local')->exists($storedPath)) {
            throw ValidationException::withMessages([
                'audio' => 'Impossible d’enregistrer le fichier audio. Veuillez recommencer.',
            ]);
        }

        $finalPath = $storedPath;

        try {
            /*
             * Conversion systématique en MP3.
             * Cela évite les fichiers WebM Chrome avec "Duration: N/A"
             * et garantit la lecture dans Chrome, Edge, Firefox, Android et iPhone.
             */
            $finalPath = $this->convertAudioToMp3($storedPath);

            $originalBaseName = pathinfo(
                basename($file->getClientOriginalName()),
                PATHINFO_FILENAME
            );

            $submission = VocalTestSubmission::create([
                'user_id' => auth()->id(),
                'vocal_test_prompt_id' => $prompt->id,
                'subject_id' => $subject->id,
                'level_id' => $level->id,
                'class_id' => $class->id,
                'reading_text' => $prompt->reading_text,
                'test_mode' => $prompt->test_mode,
                'audio_path' => $finalPath,
                'audio_original_name' => ($originalBaseName ?: 'enregistrement') . '.mp3',
                'audio_mime_type' => 'audio/mpeg',
                'audio_size' => (int) Storage::disk('local')->size($finalPath),
                'duration_seconds' => (int) $validated['duration_seconds'],
                'status' => VocalTestSubmission::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete(array_unique([
                $storedPath,
                $finalPath,
            ]));

            throw $exception;
        }

        return redirect()
            ->route('appointment.create', [
                'type' => 'test',
                'vocal_submission' => $submission->id,
            ])
            ->with(
                'success',
                '✅ Votre test vocal a été enregistré. Complétez maintenant votre rendez-vous.'
            );
    }

    private function storeCompletionTest(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $class,
        VocalTestPrompt $prompt
    ): RedirectResponse {
        $definition = VocalTestPrompt::completionDefinition();
        $expectedAnswers = $definition['expected_answers'];
        $allowedChoices = $definition['choices'];
        $questionCount = count($expectedAnswers);

        $validated = $request->validate([
            'answers' => [
                'required',
                'array',
                'size:' . $questionCount,
            ],
            'answers.*' => [
                'required',
                'string',
                Rule::in($allowedChoices),
            ],
        ], [
            'answers.required' =>
                'Complétez tous les espaces avant de continuer.',
            'answers.size' =>
                'Les quatre espaces doivent être complétés.',
            'answers.*.required' =>
                'Chaque espace doit contenir un mot.',
            'answers.*.in' =>
                'Un des mots sélectionnés n’est pas autorisé.',
        ]);

        $answers = array_values($validated['answers']);

        if (count(array_unique($answers)) !== $questionCount) {
            throw ValidationException::withMessages([
                'answers' =>
                    'Chaque carte ne peut être utilisée qu’une seule fois.',
            ]);
        }

        $results = [];
        $correctCount = 0;

        foreach ($expectedAnswers as $index => $expectedAnswer) {
            $isCorrect =
                ($answers[$index] ?? null) === $expectedAnswer;

            $results[] = $isCorrect;

            if ($isCorrect) {
                $correctCount++;
            }
        }

        $score = (int) round(
            ($correctCount / max(1, $questionCount)) * 100
        );

        $submission = VocalTestSubmission::create([
            'user_id' => auth()->id(),
            'vocal_test_prompt_id' => $prompt->id,
            'subject_id' => $subject->id,
            'level_id' => $level->id,
            'class_id' => $class->id,
            'reading_text' => $prompt->reading_text,
            'test_mode' => $prompt->test_mode,
            'submission_type' =>
                VocalTestSubmission::TYPE_COMPLETION,
            'answer_data' => [
                'answers' => $answers,
                'expected_answers' => $expectedAnswers,
                'results' => $results,
            ],
            'auto_correct_count' => $correctCount,
            'auto_total_questions' => $questionCount,
            'audio_path' => null,
            'audio_original_name' => null,
            'audio_mime_type' => null,
            'audio_size' => null,
            'duration_seconds' => null,
            'status' => VocalTestSubmission::STATUS_SUBMITTED,
            'score' => $score,
            'score_memorization' => $score,
            'final_score' => $score,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('appointment.create', [
                'type' => 'test',
                'vocal_submission' => $submission->id,
            ])
            ->with(
                'success',
                '✅ Vos réponses ont été enregistrées. '
                . 'Complétez maintenant votre rendez-vous.'
            );
    }

    private function redirectExcludedPath(): RedirectResponse
    {
        return redirect()
            ->route('appointment.create', ['type' => 'test'])
            ->with(
                'info',
                'Aucun test vocal n’est demandé pour ce parcours débutant. Vous pouvez prendre rendez-vous directement.'
            );
    }

    private function findPrompt(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ): VocalTestPrompt {
        $prompt = VocalTestPrompt::activeForPath(
            $subject,
            $level,
            $class
        );

        abort_unless(
            $prompt,
            404,
            'Aucun test vocal actif n’est disponible pour cette sélection.'
        );

        return $prompt;
    }

    private function validatePath(Subject $subject, Level $level, ClassRoom $class): void
    {
        abort_unless((int) $level->subject_id === (int) $subject->id, 404);
        abort_unless((int) $class->level_id === (int) $level->id, 404);
        abort_unless(
            $class->subjects()
                ->where('subjects.id', $subject->id)
                ->exists(),
            404
        );
    }

    /**
     * Convertit l'audio reçu en MP3 lisible par tous les navigateurs.
     * FFmpeg doit être accessible avec la commande "ffmpeg".
     */
    private function convertAudioToMp3(string $sourceRelativePath): string
    {
        $disk = Storage::disk('local');

        if (!$disk->exists($sourceRelativePath)) {
            throw ValidationException::withMessages([
                'audio' => 'Le fichier audio source est introuvable.',
            ]);
        }

        $sourceAbsolutePath = $disk->path($sourceRelativePath);
        $directory = trim(
            pathinfo($sourceRelativePath, PATHINFO_DIRNAME),
            './\\'
        );

        $targetFilename = pathinfo(
            $sourceRelativePath,
            PATHINFO_FILENAME
        ) . '-normalized.mp3';

        $targetRelativePath = ($directory ? $directory . '/' : '')
            . $targetFilename;

        $targetAbsolutePath = $disk->path($targetRelativePath);

        /*
         * Utiliser un chemin absolu évite le problème suivant sous Windows :
         * FFmpeg fonctionne dans CMD, mais le processus PHP déjà démarré
         * ne connaît pas encore le nouveau PATH.
         *
         * Exemple dans .env :
         * FFMPEG_PATH="C:/ffmpeg/bin/ffmpeg.exe"
         */
        $ffmpegBinary = (string) config('services.ffmpeg.path', 'ffmpeg');

        if (
            $ffmpegBinary !== 'ffmpeg'
            && !is_file($ffmpegBinary)
        ) {
            throw ValidationException::withMessages([
                'audio' =>
                    'Le chemin FFmpeg configuré est introuvable : ' .
                    $ffmpegBinary,
            ]);
        }

        $process = new Process([
            $ffmpegBinary,
            '-y',
            '-fflags',
            '+genpts',
            '-i',
            $sourceAbsolutePath,
            '-vn',
            '-ac',
            '1',
            '-ar',
            '44100',
            '-c:a',
            'libmp3lame',
            '-b:a',
            '128k',
            $targetAbsolutePath,
        ]);

        $process->setTimeout(120);
        $process->run();

        $conversionSucceeded =
            $process->isSuccessful()
            && $disk->exists($targetRelativePath)
            && $disk->size($targetRelativePath) >= 1024;

        if (!$conversionSucceeded) {
            $disk->delete($targetRelativePath);

            Log::error('Échec de conversion FFmpeg du test vocal.', [
                'ffmpeg_binary' => $ffmpegBinary,
                'command' => $process->getCommandLine(),
                'exit_code' => $process->getExitCode(),
                'error_output' => $process->getErrorOutput(),
                'standard_output' => $process->getOutput(),
                'source_path' => $sourceAbsolutePath,
                'target_path' => $targetAbsolutePath,
            ]);

            throw ValidationException::withMessages([
                'audio' =>
                    'La conversion audio a échoué. ' .
                    'Consultez storage/logs/laravel.log pour connaître l’erreur FFmpeg.',
            ]);
        }

        if ($sourceRelativePath !== $targetRelativePath) {
            $disk->delete($sourceRelativePath);
        }

        return $targetRelativePath;
    }

    private function normalizeMimeType(string $mimeType, string $extension): string
    {
        $mimeType = strtolower(trim($mimeType));

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

        if (strpos($mimeType, 'webm') !== false) {
            return 'audio/webm';
        }

        if (strpos($mimeType, 'wav') !== false) {
            return 'audio/wav';
        }

        if (strpos($mimeType, 'mpeg') !== false || strpos($mimeType, 'mp3') !== false) {
            return 'audio/mpeg';
        }

        if (strpos($mimeType, 'ogg') !== false) {
            return 'audio/ogg';
        }

        if (strpos($mimeType, 'mp4') !== false || strpos($mimeType, 'm4a') !== false) {
            return 'audio/mp4';
        }

        if (strpos($mimeType, 'aac') !== false) {
            return 'audio/aac';
        }

        return 'audio/webm';
    }
}