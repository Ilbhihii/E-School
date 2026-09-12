<?php

namespace App\Services;

use App\Mail\MissingAssignmentStudentMailable;
use App\Mail\MultipleMissingAssignmentsParentMailable;
use App\Models\Assignment;
use App\Models\AssignmentReminder;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HomeworkReminderService
{
    private HomeworkSubmissionService $submissions;

    public function __construct(
        HomeworkSubmissionService $submissions
    ) {
        $this->submissions = $submissions;
    }

    public function preview(): Collection
    {
        return $this->missingItems();
    }

    public function run(): array
    {
        if (!config('homework_reminders.enabled')) {
            return [
                'missing' => 0,
                'student_emails' => 0,
                'parent_emails' => 0,
                'failed' => 0,
            ];
        }

        $missing = $this->missingItems();

        $stats = [
            'missing' => $missing->count(),
            'student_emails' => 0,
            'parent_emails' => 0,
            'failed' => 0,
        ];

        if (config('homework_reminders.student_email_enabled')) {
            foreach ($missing as $item) {
                if ($this->sendStudentReminder($item)) {
                    $stats['student_emails']++;
                }
            }
        }

        if (config('homework_reminders.parent_email_enabled')) {
            $windowDays = max(
                1,
                (int) config('homework_reminders.parent_window_days', 30)
            );

            $recentMissing = $missing->filter(
                function ($item) use ($windowDays) {
                    return $item->assignment->due_date
                        && $item->assignment->due_date
                            ->gte(now()->subDays($windowDays)->startOfDay());
                }
            );

            foreach ($recentMissing->groupBy('student.id') as $studentItems) {
                if ($this->sendParentComplaint($studentItems)) {
                    $stats['parent_emails']++;
                }
            }
        }

        return $stats;
    }

    private function missingItems(): Collection
    {
        $teacherAssignments = Assignment::query()
            ->with([
                'user:id,name,email,role',
                'subject:id,name',
                'classRoom:id,name,level_id',
                'course:id,title',
            ])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereNotNull('subject_id')
            ->whereNotNull('class_room_id')
            ->whereHas('user', function ($query) {
                $query->whereIn('role', [
                    User::ROLE_PROF,
                    User::ROLE_ADMIN,
                ]);
            })
            ->orderBy('due_date')
            ->get();

        $missing = collect();

        foreach ($teacherAssignments as $assignment) {
            $students = $this->submissions
                ->studentsForAssignment($assignment);

            foreach ($students as $student) {
                if (
                    $this->submissions->hasSubmitted(
                        $assignment,
                        (int) $student->id
                    )
                ) {
                    continue;
                }

                $missing->push((object) [
                    'assignment' => $assignment,
                    'student' => $student,
                ]);
            }
        }

        return $missing->values();
    }

    private function sendStudentReminder($item): bool
    {
        $student = $item->student;
        $assignment = $item->assignment;

        if (!$student->email) {
            return false;
        }

        $alreadySent = AssignmentReminder::query()
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->where('recipient_user_id', $student->id)
            ->where('kind', AssignmentReminder::KIND_STUDENT_MISSING)
            ->where('channel', 'email')
            ->where('status', AssignmentReminder::STATUS_SENT)
            ->exists();

        if ($alreadySent) {
            return false;
        }

        try {
            Mail::to($student->email)->send(
                new MissingAssignmentStudentMailable(
                    $student,
                    $assignment
                )
            );

            AssignmentReminder::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'recipient_user_id' => $student->id,
                'kind' => AssignmentReminder::KIND_STUDENT_MISSING,
                'channel' => 'email',
                'recipient' => $student->email,
                'missing_count' => 1,
                'status' => AssignmentReminder::STATUS_SENT,
                'sent_at' => now(),
                'meta' => [
                    'assignment_title' => $assignment->title,
                    'due_date' => optional($assignment->due_date)->toDateString(),
                ],
            ]);

            return true;
        } catch (\Throwable $e) {
            $this->recordFailure(
                $assignment->id,
                $student->id,
                $student->id,
                AssignmentReminder::KIND_STUDENT_MISSING,
                $student->email,
                $e
            );

            return false;
        }
    }

    private function sendParentComplaint(Collection $studentItems): bool
    {
        if ($studentItems->isEmpty()) {
            return false;
        }

        $threshold = max(
            1,
            (int) config('homework_reminders.parent_threshold', 3)
        );

        $missingCount = $studentItems->count();

        if ($missingCount < $threshold) {
            return false;
        }

        $student = $studentItems->first()->student;

        $parents = $student->parents()
            ->where('users.role', User::ROLE_PARENT)
            ->where(function ($query) {
                $query->whereNull('users.is_active')
                    ->orWhere('users.is_active', true);
            })
            ->orderByDesc('parent_student.is_primary')
            ->get();

        $sentAny = false;

        foreach ($parents as $parent) {
            if (!$parent->email) {
                continue;
            }

            if (!$this->parentComplaintIsDue(
                $student->id,
                $parent->id,
                $missingCount
            )) {
                continue;
            }

            try {
                Mail::to($parent->email)->send(
                    new MultipleMissingAssignmentsParentMailable(
                        $parent,
                        $student,
                        $studentItems
                    )
                );

                AssignmentReminder::create([
                    'assignment_id' => null,
                    'student_id' => $student->id,
                    'recipient_user_id' => $parent->id,
                    'kind' => AssignmentReminder::KIND_PARENT_MULTIPLE,
                    'channel' => 'email',
                    'recipient' => $parent->email,
                    'missing_count' => $missingCount,
                    'status' => AssignmentReminder::STATUS_SENT,
                    'sent_at' => now(),
                    'meta' => [
                        'assignment_ids' => $studentItems
                            ->pluck('assignment.id')
                            ->values()
                            ->all(),
                    ],
                ]);

                $sentAny = true;
            } catch (\Throwable $e) {
                $this->recordFailure(
                    null,
                    $student->id,
                    $parent->id,
                    AssignmentReminder::KIND_PARENT_MULTIPLE,
                    $parent->email,
                    $e,
                    $missingCount
                );
            }
        }

        return $sentAny;
    }

    private function parentComplaintIsDue(
        int $studentId,
        int $parentId,
        int $missingCount
    ): bool {
        $last = AssignmentReminder::query()
            ->where('student_id', $studentId)
            ->where('recipient_user_id', $parentId)
            ->where('kind', AssignmentReminder::KIND_PARENT_MULTIPLE)
            ->where('channel', 'email')
            ->where('status', AssignmentReminder::STATUS_SENT)
            ->latest('sent_at')
            ->first();

        if (!$last) {
            return true;
        }

        $cooldown = max(
            1,
            (int) config('homework_reminders.parent_cooldown_days', 7)
        );

        $repeatStep = max(
            1,
            (int) config('homework_reminders.parent_repeat_step', 2)
        );

        if (
            $last->sent_at
            && $last->sent_at->gt(now()->subDays($cooldown))
        ) {
            return false;
        }

        return $missingCount
            >= ((int) $last->missing_count + $repeatStep);
    }

    private function recordFailure(
        ?int $assignmentId,
        int $studentId,
        ?int $recipientUserId,
        string $kind,
        ?string $recipient,
        \Throwable $e,
        ?int $missingCount = null
    ): void {
        Log::error(
            'Homework reminder email failed: ' . $e->getMessage()
        );

        AssignmentReminder::create([
            'assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'recipient_user_id' => $recipientUserId,
            'kind' => $kind,
            'channel' => 'email',
            'recipient' => $recipient,
            'missing_count' => $missingCount,
            'status' => AssignmentReminder::STATUS_FAILED,
            'error_message' => mb_substr($e->getMessage(), 0, 2000),
            'sent_at' => now(),
        ]);
    }
}
