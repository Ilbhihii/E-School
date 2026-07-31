<?php

namespace App\Mail;

use App\Models\TestAppointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentPaymentInvitationMailable extends Mailable
{
    use Queueable, SerializesModels;

    public TestAppointment $appointment;
    public string $paymentUrl;
    public array $plan;
    public ?string $subjectName;
    public ?string $levelName;
    public ?string $className;

    public function __construct(
        TestAppointment $appointment,
        string $paymentUrl,
        array $plan
    ) {
        $this->appointment = $appointment;
        $this->paymentUrl = $paymentUrl;
        $this->plan = $plan;

        $pathSubject =
            $appointment->subject
            ?? $appointment->vocalSubmission?->subject
            ?? $appointment->highSchoolTestSubmission?->subject;

        $pathLevel =
            $appointment->level
            ?? $appointment->vocalSubmission?->level
            ?? $appointment->highSchoolTestSubmission?->level;

        $pathClass =
            $appointment->classRoom
            ?? $appointment->vocalSubmission?->classRoom
            ?? $appointment->highSchoolTestSubmission?->classRoom;

        $this->subjectName = $pathSubject?->name;
        $this->levelName = $pathLevel?->name;
        $this->className = $pathClass?->name;
    }

    public function build()
    {
        return $this
            ->subject(
                'Finalisez votre inscription — '
                . $this->plan['name']
            )
            ->view(
                'mail.appointment-payment-invitation'
            );
    }
}
