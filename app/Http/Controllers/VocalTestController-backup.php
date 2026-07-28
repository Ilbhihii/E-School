<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;
use Throwable;

class VocalTestController extends Controller
{
    public function create(Subject $subject, Level $level, ClassRoom $class)
    {
        $this->validatePath($subject, $level, $class);

        $prompt = $this->findPrompt($subject, $level, $class);

        return view('front.vocal-test', [
            'subject' => $subject,
            'level' => $level,
            'class' => $class,
            'prompt' => $prompt,
        ]);
    }

    public function store(Request $request, Subject $subject, Level $level, ClassRoom $class)
    {
        $this->validatePath($subject, $level, $class);

        $prompt = $this->findPrompt($subject, $level, $class);

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

    private function findPrompt(Subject $subject, Level $level, ClassRoom $class): VocalTestPrompt
    {
        return VocalTestPrompt::query()
            ->where('subject_id', $subject->id)
            ->where('level_id', $level->id)
            ->where('class_id', $class->id)
            ->where('is_active', true)
            ->firstOrFail();
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

        $process = new Process([
            'ffmpeg',
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

            throw ValidationException::withMessages([
                'audio' =>
                    'La conversion de l’enregistrement a échoué. ' .
                    'Vérifiez que FFmpeg est installé et accessible dans le PATH.',
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