<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Level;
use App\Models\ClassRoom;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;

class VocalTestController extends Controller
{
    private const RECITATION_TEXT = 'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ۝ الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ ۝ الرَّحْمَنِ الرَّحِيمِ ۝ مَالِكِ يَوْمِ الدِّينِ ۝ إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ ۝ اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ ۝ صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ';

    /**
     * Récupérer le texte de récitation pour le test vocal
     * GET /api/vocal-test/text
     */
    public function recitationText()
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'text' => self::RECITATION_TEXT,
                'source' => 'Sourate Al-Fatiha (الفاتحة)',
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
            'subject_id' => 'required|exists:subjects,id',
            'level_id'   => 'required|exists:levels,id',
            'class_id'   => 'required|exists:class_rooms,id',
            'audio'      => 'required|file|mimes:webm,mp3,wav,ogg|max:15360',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        abort_unless(mb_strtolower($subject->name) === 'coran', 404, 'Le test vocal est uniquement disponible pour le Coran.');

        $file = $validated['audio'];
        $mimeType = $file->getClientMimeType() ?: $file->getMimeType();

        $submission = VocalTestSubmission::create([
            'user_id'       => auth()->id(),
            'subject_id'    => $subject->id,
            'level_id'      => $validated['level_id'],
            'class_id'      => $validated['class_id'],
            'recitation_text' => self::RECITATION_TEXT,
            'audio_path'    => $file->store('vocal-tests'),
            'audio_mime_type' => $mimeType,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test vocal soumis avec succès.',
            'data'    => [
                'id' => $submission->id,
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
            ->with(['subject', 'level', 'classRoom', 'appointment'])
            ->latest()
            ->get()
            ->map(fn($sub) => [
                'id'          => $sub->id,
                'subject'     => $sub->subject?->name,
                'level'       => $sub->level?->name,
                'class'       => $sub->classRoom?->name,
                'consumed_at' => $sub->consumed_at,
                'has_appointment' => $sub->appointment !== null,
                'appointment_status' => $sub->appointment?->status,
                'created_at'  => $sub->created_at,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $submissions,
        ]);
    }
}
