<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;

class VocalTestSubmissionController extends Controller
{
    /**
     * Liste uniquement les tests explicitement partagés avec le professeur connecté.
     */
    public function index(Request $request)
    {
        $profId = auth()->id();

        $query = VocalTestSubmission::query()
            ->whereHas(
                'professors',
                fn ($profQuery) =>
                    $profQuery->where('users.id', $profId)
            )
            ->with([
                'user',
                'subject',
                'level',
                'classRoom',
                'prompt',
                'appointment',
            ])
            ->orderByDesc('created_at');

        if (
            $request->filled('status')
            && $request->status !== 'all'
        ) {
            $query->where('status', $request->status);
        }

        if (
            $request->filled('test_mode')
            && $request->test_mode !== 'all'
        ) {
            $query->where('test_mode', $request->test_mode);
        }

        $submissions = $query
            ->paginate(20)
            ->withQueryString();

        return view(
            'prof.vocal-tests.index',
            compact('submissions')
        );
    }

    public function show(VocalTestSubmission $submission)
    {
        $this->ensureAssigned($submission);

        $submission->load([
            'user',
            'subject',
            'level',
            'classRoom',
            'prompt',
            'appointment',
        ]);

        return view(
            'prof.vocal-tests.show',
            [
                'submission' => $submission,
                'isCompletionSubmission' =>
                    $submission->isCompletionSubmission(),
                'isObservationSubmission' =>
                    $submission->isObservationSubmission(),
            ]
        );
    }

    /**
     * Réutilise le streaming audio/image déjà sécurisé du centre admin,
     * après contrôle de l'affectation du professeur.
     */
    public function audio(
        Request $request,
        VocalTestSubmission $submission
    ) {
        $this->ensureAssigned($submission);

        return app(
            \App\Http\Controllers\Admin\VocalTestSubmissionController::class
        )->audio($request, $submission);
    }

    private function ensureAssigned(
        VocalTestSubmission $submission
    ): void {
        $allowed = $submission
            ->professors()
            ->where('users.id', auth()->id())
            ->exists();

        abort_unless(
            $allowed,
            403,
            'Ce test ne vous a pas été affecté par l’administrateur.'
        );
    }
}
