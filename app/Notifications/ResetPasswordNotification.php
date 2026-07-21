<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe — Smart School Academy')
            ->view('emails.password-reset', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
                'expiresIn' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
                'logoUrl' => 'https://raw.githubusercontent.com/Ilbhihii/E-School/main/public/images/logoSSA-removebg-preview.png',
            ]);
    }
}
