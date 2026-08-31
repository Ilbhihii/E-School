<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentAccountCreatedMailable extends Mailable
{
    use Queueable, SerializesModels;

    public User $student;
    public string $temporaryPassword;
    public string $loginUrl;
    public string $expiresAt;

    public function __construct(User $student, string $temporaryPassword)
    {
        $this->student = $student;
        $this->temporaryPassword = $temporaryPassword;
        $this->loginUrl = route('login');
        $expiresAt = $student->getAttribute('temporary_password_expires_at');
        $this->expiresAt = $expiresAt
            ? Carbon::parse($expiresAt)->format('d/m/Y à H:i')
            : now()->addHours(48)->format('d/m/Y à H:i');
    }

    public function build()
    {
        return $this
            ->subject('Vos accès étudiant — Smart School Academy')
            ->view('mail.student-account-created');
    }
}
