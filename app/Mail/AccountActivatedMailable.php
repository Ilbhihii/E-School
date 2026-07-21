<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountActivatedMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $dashboardUrl;
    public $logoUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->dashboardUrl = route('student.dashboard');
        $this->logoUrl = 'https://raw.githubusercontent.com/Ilbhihii/E-School/main/public/images/logoSSA-removebg-preview.png';
    }

    /**
     * Construit le message avec l'API compatible avec la version Laravel du projet.
     */
    public function build()
    {
        return $this
            ->subject('Votre compte étudiant est activé — Smart School Academy')
            ->view('mail.account-activated');
    }
}
