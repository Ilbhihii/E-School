<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class MultipleMissingAssignmentsParentMailable extends Mailable
{
    use Queueable, SerializesModels;

    public User $parent;
    public User $student;
    public Collection $missingAssignments;

    public function __construct(
        User $parent,
        User $student,
        Collection $missingAssignments
    ) {
        $this->parent = $parent;
        $this->student = $student;
        $this->missingAssignments = $missingAssignments;
    }

    public function build()
    {
        return $this
            ->subject(
                'Suivi pédagogique — plusieurs devoirs non remis pour '
                . $this->student->name
            )
            ->view('mail.homework-missing-parent');
    }
}
