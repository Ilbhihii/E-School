<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfessorAccountCreatedMailable extends Mailable
{
    use Queueable, SerializesModels;

    public User $professor;
    public string $temporaryPassword;
    public string $loginUrl;
    public string $expiresAt;

    public function __construct(
        User $professor,
        string $temporaryPassword
    ) {
        $this->professor = $professor;
        $this->temporaryPassword =
            $temporaryPassword;
        $this->loginUrl = route('login');

        $expiresAt = $professor->getAttribute(
            'temporary_password_expires_at'
        );

        $this->expiresAt = $expiresAt
            ? Carbon::parse($expiresAt)
                ->format('d/m/Y à H:i')
            : now()
                ->addHours(48)
                ->format('d/m/Y à H:i');
    }

    public function build()
    {
        return $this
            ->subject(
                'Vos accès professeur — '
                . 'Smart School Academy'
            )
            ->view(
                'mail.professor-account-created'
            );
    }
}
