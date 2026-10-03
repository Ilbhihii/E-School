<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\ClassSlot;
use App\Models\Course;
use App\Models\Live;
use App\Models\Message;
use App\Models\ProfAssignment;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SchoolNotificationService
{
    public function __construct(
        protected NotificationCenterService $notifications
    ) {
    }

    public function liveCreated(Live $live): void
    {
        $this->notifyLive(
            $live,
            'Nouveau live programmé',
            'Un nouveau live a été ajouté à votre planning.'
        );
    }

    public function liveUpdated(Live $live): void
    {
        if (!$live->wasChanged([
            'live_date',
            'start_time',
            'end_time',
            'class_slot_id',
            'assignment_day_of_week',
            'assignment_start_time',
            'professor_id',
            'stream_url',
            'title',
        ])) {
            return;
        }

        $this->notifyLive(
            $live,
            'Live mis à jour',
            'Les informations d’un live ont été modifiées.'
        );
    }

    public function liveReminder(Live $live): void
    {
        $this->notifyLive(
            $live,
            'Live dans 30 minutes',
            'Votre live commence bientôt.'
        );
    }

    public function assignmentCreated(Assignment $assignment): void
    {
        $assignment->loadMissing([
            'user',
            'subject',
            'classRoom.level',
            'classSlot',
            'course',
        ]);

        $creator = $assignment->user;

        if (!$creator) {
            return;
        }

        if ($creator->role === User::ROLE_STUDENT) {
            $this->notifyStudentSubmission($assignment);
            return;
        }

        if (!in_array($creator->role, [User::ROLE_PROF, User::ROLE_ADMIN], true)) {
            return;
        }

        [$subjectId, $classId, $slotId, $day, $time, $code] =
            $this->assignmentScope($assignment);

        if (!$subjectId || !$classId) {
            return;
        }

        $students = $this->studentsForScope(
            $subjectId,
            $classId,
            $slotId,
            $day,
            $time,
            $code
        );

        if ($students->isEmpty()) {
            return;
        }

        $title = 'Nouveau devoir';
        $body = trim((string) $assignment->title) !== ''
            ? 'Un nouveau devoir est disponible : ' . $assignment->title
            : 'Un nouveau devoir est disponible.';

        $this->notifications->sendToUsers(
            $students,
            $title,
            $body,
            'assignment',
            url('/student/assignments'),
            'bi bi-file-earmark-text-fill',
            ['assignment_id' => $assignment->id],
            true,
            'high'
        );
    }

    public function assignmentUpdated(Assignment $assignment): void
    {
        if (!$assignment->wasChanged(['grade', 'comment'])) {
            return;
        }

        $assignment->loadMissing('user');
        $student = $assignment->user;

        if (!$student || $student->role !== User::ROLE_STUDENT) {
            return;
        }

        $message = 'Votre devoir a été corrigé.';

        if ($assignment->grade !== null) {
            $message .= ' Résultat : ' . $this->gradeLabel($assignment->grade) . '.';
        }

        $this->notifications->send(
            $student,
            'Correction de devoir',
            $message,
            'result',
            url('/student/assignments'),
            'bi bi-patch-check-fill',
            ['assignment_id' => $assignment->id],
            true,
            'high'
        );
    }

    public function courseCreated(Course $course): void
    {
        $course->loadMissing('creator');

        if (
            $course->creator
            && $course->creator->role === User::ROLE_PROF
            && $course->approval_status === Course::STATUS_PENDING
        ) {
            $this->notifications->sendToAdmins(
                'Nouveau cours à valider',
                $course->creator->name . ' a proposé le cours « ' . $course->title . ' ».',
                'course',
                url('/admin/courses'),
                'bi bi-journal-check',
                ['course_id' => $course->id],
                true,
                'high'
            );

            return;
        }

        if ($course->approval_status === Course::STATUS_APPROVED) {
            $this->notifyCourseAvailable($course);
        }
    }

    public function courseUpdated(Course $course): void
    {
        if (
            $course->wasChanged('approval_status')
            && $course->approval_status === Course::STATUS_APPROVED
        ) {
            $this->notifyCourseAvailable($course);
        }

        if (
            $course->wasChanged('approval_status')
            && $course->approval_status === Course::STATUS_REJECTED
        ) {
            $course->loadMissing('creator');

            if ($course->creator && $course->creator->role === User::ROLE_PROF) {
                $this->notifications->send(
                    $course->creator,
                    'Cours refusé',
                    'Votre proposition « ' . $course->title . ' » doit être corrigée.',
                    'course',
                    url('/prof/courses'),
                    'bi bi-exclamation-triangle-fill',
                    ['course_id' => $course->id],
                    true,
                    'high'
                );
            }
        }
    }

    public function messageCreated(Message $message): void
    {
        $message->loadMissing(['user', 'subject']);

        $sender = $message->user;
        $subject = $message->subject;

        if (!$sender || !$subject) {
            return;
        }

        $excerpt = Str::limit(
            trim((string) $message->message),
            110
        );

        if ($message->private_professor_id && $message->conversation_user_id) {
            $recipientId = $sender->role === User::ROLE_STUDENT
                ? (int) $message->private_professor_id
                : (int) $message->conversation_user_id;

            $recipient = User::query()
                ->whereKey($recipientId)
                ->where('is_active', true)
                ->first();

            if ($recipient) {
                $this->notifications->send(
                    $recipient,
                    'Nouveau message privé',
                    $sender->name . ' : ' . $excerpt,
                    'chat',
                    $recipient->role === User::ROLE_PROF
                        ? url('/prof/private-chats')
                        : url('/student/private-chats'),
                    'bi bi-chat-dots-fill',
                    ['message_id' => $message->id],
                    true,
                    'normal'
                );
            }

            return;
        }

        if ($this->isAdministrationSubject($subject)) {
            if ($sender->role === User::ROLE_ADMIN && $message->conversation_user_id) {
                $recipient = User::query()
                    ->whereKey((int) $message->conversation_user_id)
                    ->where('is_active', true)
                    ->first();

                if ($recipient) {
                    $this->notifications->send(
                        $recipient,
                        'Message de l’administration',
                        $excerpt,
                        'chat',
                        $recipient->role === User::ROLE_PROF
                            ? url('/prof/chat/subjects')
                            : url('/student/chats'),
                        'bi bi-headset',
                        ['message_id' => $message->id],
                        true,
                        'high'
                    );
                }
            } else {
                $this->notifications->sendToAdmins(
                    'Nouveau message',
                    $sender->name . ' : ' . $excerpt,
                    'chat',
                    url('/admin/chat'),
                    'bi bi-chat-dots-fill',
                    ['message_id' => $message->id],
                    true,
                    'normal'
                );
            }

            return;
        }

        $recipients = collect();

        if ($message->class_slot_id) {
            $recipients = $recipients
                ->merge(
                    $this->studentsForScope(
                        (int) $message->subject_id,
                        null,
                        (int) $message->class_slot_id,
                        null,
                        null,
                        (string) ($message->assignment_code ?? '')
                    )
                )
                ->merge(
                    $this->professorsForScope(
                        (int) $message->subject_id,
                        null,
                        (int) $message->class_slot_id,
                        null,
                        null
                    )
                );
        }

        $recipients = $recipients
            ->filter(fn ($user) => (int) $user->id !== (int) $sender->id)
            ->unique('id')
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        $this->notifications->sendToUsers(
            $recipients,
            'Nouveau message — ' . $subject->name,
            $sender->name . ' : ' . $excerpt,
            'chat',
            null,
            'bi bi-chat-square-text-fill',
            ['message_id' => $message->id],
            true,
            'normal'
        );
    }

    protected function notifyLive(
        Live $live,
        string $title,
        string $fallbackBody
    ): void {
        $live->loadMissing([
            'classSlot.subject',
            'classSlot.classRoom',
            'professor',
        ]);

        $slot = $live->classSlot;

        if (!$slot) {
            return;
        }

        $body = $fallbackBody;

        if ($live->live_date) {
            $body = trim((string) $live->title)
                . ' · '
                . Carbon::parse($live->live_date)->format('d/m/Y')
                . ' à '
                . substr((string) $live->start_time, 0, 5);
        }

        if ($live->professor && $live->professor->is_active) {
            $this->notifications->send(
                $live->professor,
                $title,
                $body,
                'live',
                url('/prof/lives'),
                'bi bi-camera-video-fill',
                ['live_id' => $live->id],
                true,
                'high'
            );
        }

        $students = $this->studentsForScope(
            (int) $slot->subject_id,
            (int) $slot->class_id,
            (int) $slot->id,
            $live->assignment_day_of_week
                ? (int) $live->assignment_day_of_week
                : null,
            $live->assignment_start_time
                ? (string) $live->assignment_start_time
                : null,
            null
        );

        $this->notifications->sendToUsers(
            $students,
            $title,
            $body,
            'live',
            url('/student/lives'),
            'bi bi-camera-video-fill',
            ['live_id' => $live->id],
            true,
            'high'
        );
    }

    protected function notifyStudentSubmission(Assignment $assignment): void
    {
        [$subjectId, $classId, $slotId, $day, $time] =
            $this->assignmentScope($assignment);

        if (!$subjectId || !$classId) {
            return;
        }

        $professors = $this->professorsForScope(
            $subjectId,
            $classId,
            $slotId,
            $day,
            $time
        );

        $body = $assignment->user->name
            . ' a envoyé un devoir'
            . (trim((string) $assignment->title) !== ''
                ? ' : ' . $assignment->title
                : '.');

        $this->notifications->sendToUsers(
            $professors,
            'Devoir étudiant reçu',
            $body,
            'assignment',
            url('/prof/assignments'),
            'bi bi-file-earmark-arrow-up-fill',
            ['assignment_id' => $assignment->id],
            true,
            'high'
        );

        $this->notifications->sendToAdmins(
            'Nouveau devoir étudiant',
            $body,
            'assignment',
            url('/admin/devoirs'),
            'bi bi-file-earmark-arrow-up-fill',
            ['assignment_id' => $assignment->id],
            true,
            'normal'
        );
    }

    protected function notifyCourseAvailable(Course $course): void
    {
        $slotId = null;

        if (
            $course->subject_id
            && $course->level_id
            && $course->class_id
            && trim((string) $course->slot_code) !== ''
        ) {
            $slotId = ClassSlot::query()
                ->where('subject_id', $course->subject_id)
                ->where('level_id', $course->level_id)
                ->where('class_id', $course->class_id)
                ->whereRaw(
                    'UPPER(TRIM(code)) = ?',
                    [strtoupper(trim((string) $course->slot_code))]
                )
                ->value('id');
        }

        $students = $this->studentsForScope(
            (int) $course->subject_id,
            (int) $course->class_id,
            $slotId ? (int) $slotId : null,
            $course->assignment_day_of_week
                ? (int) $course->assignment_day_of_week
                : null,
            $course->assignment_start_time
                ? (string) $course->assignment_start_time
                : null,
            (string) ($course->assignment_code ?? '')
        );

        $this->notifications->sendToUsers(
            $students,
            'Nouveau cours disponible',
            'Le cours « ' . $course->title . ' » est maintenant disponible.',
            'course',
            url('/student/subjects'),
            'bi bi-play-btn-fill',
            ['course_id' => $course->id],
            true,
            'normal'
        );
    }

    protected function studentsForScope(
        int $subjectId,
        ?int $classId,
        ?int $classSlotId,
        ?int $day,
        ?string $time,
        ?string $assignmentCode
    ): Collection {
        if (!Schema::hasTable('class_user')) {
            return collect();
        }

        $query = DB::table('class_user')
            ->where('subject_id', $subjectId);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($classSlotId && Schema::hasColumn('class_user', 'class_slot_id')) {
            $query->where('class_slot_id', $classSlotId);
        }

        if ($day && Schema::hasColumn('class_user', 'student_day_of_week')) {
            $query->where('student_day_of_week', $day);
        }

        if ($time && Schema::hasColumn('class_user', 'student_start_time')) {
            $query->whereTime(
                'student_start_time',
                '=',
                $this->normalizeTime($time)
            );
        }

        if (
            trim((string) $assignmentCode) !== ''
            && Schema::hasColumn('class_user', 'student_slot_code')
        ) {
            $query->whereRaw(
                'UPPER(TRIM(student_slot_code)) = ?',
                [strtoupper(trim((string) $assignmentCode))]
            );
        }

        $ids = $query
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        return User::query()
            ->whereIn('id', $ids)
            ->where('role', User::ROLE_STUDENT)
            ->where('is_active', true)
            ->get();
    }

    protected function professorsForScope(
        int $subjectId,
        ?int $classId,
        ?int $classSlotId,
        ?int $day,
        ?string $time
    ): Collection {
        $query = ProfAssignment::query()
            ->where('subject_id', $subjectId);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($classSlotId) {
            $query->where('class_slot_id', $classSlotId);
        }

        if ($day) {
            $query->where('day_of_week', $day);
        }

        if ($time) {
            $query->whereTime('start_time', '=', $this->normalizeTime($time));
        }

        $ids = $query
            ->pluck('prof_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        return User::query()
            ->whereIn('id', $ids)
            ->where('role', User::ROLE_PROF)
            ->where('is_active', true)
            ->get();
    }

    protected function assignmentScope(Assignment $assignment): array
    {
        $course = $assignment->course;

        $subjectId = (int) (
            $assignment->subject_id
            ?: ($course?->subject_id ?? 0)
        );

        $classId = (int) (
            $assignment->class_room_id
            ?: ($course?->class_id ?? 0)
        );

        $slotId = $assignment->class_slot_id
            ? (int) $assignment->class_slot_id
            : null;

        if (!$slotId && $course && trim((string) $course->slot_code) !== '') {
            $slotId = ClassSlot::query()
                ->where('subject_id', $subjectId)
                ->where('level_id', $course->level_id)
                ->where('class_id', $classId)
                ->whereRaw(
                    'UPPER(TRIM(code)) = ?',
                    [strtoupper(trim((string) $course->slot_code))]
                )
                ->value('id');

            $slotId = $slotId ? (int) $slotId : null;
        }

        return [
            $subjectId ?: null,
            $classId ?: null,
            $slotId,
            $assignment->assignment_day_of_week
                ? (int) $assignment->assignment_day_of_week
                : ($course?->assignment_day_of_week
                    ? (int) $course->assignment_day_of_week
                    : null),
            $assignment->assignment_start_time
                ?: ($course?->assignment_start_time ?? null),
            $assignment->assignment_code
                ?: ($course?->assignment_code ?? null),
        ];
    }

    protected function normalizeTime(string $time): string
    {
        try {
            return Carbon::parse($time)->format('H:i:s');
        } catch (\Throwable $e) {
            return $time;
        }
    }

    protected function gradeLabel($grade): string
    {
        return match ((int) $grade) {
            20 => 'Acquis',
            10 => 'En cours d’acquisition',
            0 => 'Non acquis',
            default => (string) $grade,
        };
    }

    protected function isAdministrationSubject(Subject $subject): bool
    {
        return mb_strtolower(trim((string) $subject->name)) === 'administration';
    }
}
