<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactRequestMailable extends Mailable
{
    use Queueable, SerializesModels;

    public array $contact;

    public function __construct(array $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        $fullName = trim(
            $this->contact['first_name']
            . ' '
            . $this->contact['last_name']
        );

        $subject =
            'Nouvelle prise de contact — Smart School Academy';

        if (
            !empty(
                $this->contact['is_repeat']
            )
        ) {
            $subject .=
                ' — '
                . (int) $this->contact[
                    'submissions_count'
                ]
                . 'e remplissage';
        }

        return $this
            ->subject($subject)
            ->replyTo(
                $this->contact['email'],
                $fullName
            )
            ->view('mail.contact-request');
    }
}
