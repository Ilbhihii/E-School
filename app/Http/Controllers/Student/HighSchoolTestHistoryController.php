<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\HighSchoolTestSubmission;
use Barryvdh\DomPDF\Facade\Pdf;

class HighSchoolTestHistoryController extends Controller
{
    public function index()
    {
        $submissions =
            HighSchoolTestSubmission::query()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->with([
                    'subject',
                    'level',
                    'classRoom',
                    'reviewer',
                    'appointment',
                ])
                ->latest('submitted_at')
                ->latest('id')
                ->paginate(12);

        return view(
            'student.high-school-tests.index',
            compact('submissions')
        );
    }

    public function show(
        HighSchoolTestSubmission $submission
    ) {
        $this->authorizeOwner($submission);

        $submission->load([
            'subject',
            'level',
            'classRoom',
            'reviewer',
            'appointment',
        ]);

        return view(
            'student.high-school-tests.show',
            compact('submission')
        );
    }

    public function report(
        HighSchoolTestSubmission $submission
    ) {
        $this->authorizeOwner($submission);

        abort_unless(
            $submission->reviewed_at
            || $submission->isApproved(),
            403,
            'Le rapport n’est pas encore disponible.'
        );

        $submission->load([
            'user',
            'subject',
            'level',
            'classRoom',
            'reviewer',
            'appointment',
        ]);

        $pdf = Pdf::loadView(
            'admin.high-school-tests.report',
            compact('submission')
        )->setPaper('a4');

        return $pdf->download(
            'mon-rapport-test-'
            . $submission->id
            . '.pdf'
        );
    }

    private function authorizeOwner(
        HighSchoolTestSubmission $submission
    ): void {
        abort_unless(
            (int) $submission->user_id
            === (int) auth()->id(),
            403
        );
    }
}
