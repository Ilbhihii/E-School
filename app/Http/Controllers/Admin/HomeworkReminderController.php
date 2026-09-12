<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentReminder;
use App\Services\HomeworkReminderService;
use Illuminate\Http\Request;

class HomeworkReminderController extends Controller
{
    private HomeworkReminderService $service;

    public function __construct(
        HomeworkReminderService $service
    ) {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $missing = $this->service->preview();

        if ($request->filled('student')) {
            $term = mb_strtolower(trim((string) $request->input('student')));
            $missing = $missing->filter(
                function ($item) use ($term) {
                    return mb_stripos(
                        mb_strtolower((string) $item->student->name),
                        $term
                    ) !== false;
                }
            )->values();
        }

        $logs = AssignmentReminder::query()
            ->with([
                'student:id,name,email',
                'recipientUser:id,name,email',
                'assignment:id,title,subject_id,class_room_id,due_date',
                'assignment.subject:id,name',
                'assignment.classRoom:id,name',
            ])
            ->latest('id')
            ->paginate(25);

        $studentsConcerned = $missing
            ->pluck('student.id')
            ->unique()
            ->count();

        $summary = [
            'missing' => $missing->count(),
            'students' => $studentsConcerned,
            'sent_today' => AssignmentReminder::query()
                ->whereDate('sent_at', now()->toDateString())
                ->where('status', AssignmentReminder::STATUS_SENT)
                ->count(),
        ];

        return view(
            'admin.homework-reminders.index',
            compact('missing', 'logs', 'summary')
        );
    }

    public function run()
    {
        $stats = $this->service->run();

        return redirect()
            ->route('admin.homework-reminders.index')
            ->with(
                'success',
                'Vérification terminée : '
                . $stats['student_emails']
                . ' rappel(s) étudiant et '
                . $stats['parent_emails']
                . ' réclamation(s) parent envoyés.'
            );
    }
}
