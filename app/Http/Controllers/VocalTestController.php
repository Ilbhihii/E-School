<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;

class VocalTestController extends Controller
{
    private const RECITATION_TEXT = 'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ۝ الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ ۝ الرَّحْمَنِ الرَّحِيمِ ۝ مَالِكِ يَوْمِ الدِّينِ ۝ إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ ۝ اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ ۝ صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ';

    public function create(Subject $subject, Level $level, ClassRoom $class)
    {
        $this->validatePath($subject, $level, $class);

        return view('front.vocal-test', [
            'subject' => $subject,
            'level' => $level,
            'class' => $class,
            'recitationText' => self::RECITATION_TEXT,
        ]);
    }

    public function store(Request $request, Subject $subject, Level $level, ClassRoom $class)
    {
        $this->validatePath($subject, $level, $class);

        $validated = $request->validate([
            'audio' => ['required', 'file', 'max:15360'],
        ], [
            'audio.required' => 'Veuillez enregistrer votre récitation avant de continuer.',
            'audio.max' => 'L’enregistrement ne doit pas dépasser 15 Mo.',
        ]);

        $file = $validated['audio'];
        $mimeType = $file->getClientMimeType() ?: $file->getMimeType();
        if (!$mimeType || $mimeType === 'application/octet-stream') {
            $mimeType = 'audio/webm';
        }

        $submission = VocalTestSubmission::create([
            'user_id' => auth()->id(),
            'subject_id' => $subject->id,
            'level_id' => $level->id,
            'class_id' => $class->id,
            'recitation_text' => self::RECITATION_TEXT,
            'audio_path' => $file->store('vocal-tests'),
            'audio_mime_type' => $mimeType,
        ]);

        return redirect()->route('appointment.create', [
            'type' => 'test',
            'vocal_submission' => $submission->id,
        ])->with('success', 'Votre récitation a été enregistrée. Complétez maintenant votre rendez-vous.');
    }

    private function validatePath(Subject $subject, Level $level, ClassRoom $class): void
    {
        abort_unless(mb_strtolower($subject->name) === 'coran', 404);
        abort_unless((int) $level->subject_id === (int) $subject->id, 404);
        abort_unless((int) $class->level_id === (int) $level->id, 404);
        abort_unless($class->subjects()->where('subjects.id', $subject->id)->exists(), 404);
    }
}
