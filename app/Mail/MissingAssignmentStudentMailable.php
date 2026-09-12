<?php

namespace App\Mail;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MissingAssignmentStudentMailable extends Mailable
{
    use Queueable, SerializesModels;

    public User $student;
    public Assignment $assignment;

    public function __construct(
        User $student,
        Assignment $assignment
    ) {
        $this->student = $student;
        $this->assignment = $assignment;
    }

    public function build()
    {
        return $this
            ->subject(
                'Rappel — devoir non remis : '
                . $this->assignment->title
            )
            ->view('mail.homework-missing-student');
    }
}
