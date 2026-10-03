<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Subject;

class AppServiceProvider extends ServiceProvider
{
    

    public function boot()
    {
        Schema::defaultStringLength(191);

        /*
         * ADMIN_NOTIFICATIONS_EMAIL_WHATSAPP_V1
         *
         * Notifications administrateur centralisées.
         * Les erreurs e-mail / WhatsApp ne bloquent jamais
         * l'action de l'utilisateur.
         */
        \App\Models\User::created(
            function (\App\Models\User $user) {
                if (
                    $user->role
                    !== 'student'
                ) {
                    return;
                }

                app(
                    \App\Services\AdminNotificationService::class
                )->notify(
                    'Nouvelle inscription étudiant',
                    [
                        'Nom' =>
                            $user->name,
                        'E-mail' =>
                            $user->email,
                        'Téléphone' =>
                            $user->phone,
                        'Pays' =>
                            $user->country,
                        'Ville' =>
                            $user->city,
                    ],
                    url('/admin/users')
                );
            }
        );

        \App\Models\TestAppointment::created(
            function (
                \App\Models\TestAppointment $appointment
            ) {
                app(
                    \App\Services\AdminNotificationService::class
                )->notify(
                    'Nouveau rendez-vous',
                    [
                        'Nom' =>
                            $appointment->full_name,
                        'E-mail' =>
                            $appointment->email,
                        'Téléphone' =>
                            $appointment->phone,
                        'Type' =>
                            $appointment->type_label,
                        'Ville' =>
                            $appointment->city,
                        'Pays' =>
                            $appointment->country,
                        'Date souhaitée' =>
                            $appointment->preferred_date
                                ?->format('d/m/Y'),
                        'Heure souhaitée' =>
                            $appointment
                                ->preferred_time_label,
                    ],
                    url('/admin/appointments')
                );
            }
        );

        \App\Models\ContactRequest::created(
            function (
                \App\Models\ContactRequest $request
            ) {
                app(
                    \App\Services\AdminNotificationService::class
                )->notify(
                    'Nouvelle prise de contact',
                    [
                        'Nom' =>
                            trim(
                                $request->first_name
                                . ' '
                                . $request->last_name
                            ),
                        'E-mail' =>
                            $request->email,
                        'Téléphone' =>
                            $request->phone,
                        'Pays' =>
                            $request->country,
                        'Message' =>
                            mb_substr(
                                (string) $request->reason,
                                0,
                                500
                            ),
                    ],
                    url('/admin/contacts')
                );
            }
        );

        \App\Models\VocalTestSubmission::created(
            function (
                \App\Models\VocalTestSubmission $submission
            ) {
                app(
                    \App\Services\AdminNotificationService::class
                )->notify(
                    'Nouveau test vocal',
                    [
                        'Soumission' =>
                            '#' . $submission->id,
                        'Utilisateur' =>
                            $submission->user?->name
                            ?? 'Visiteur',
                        'Type' =>
                            $submission->submission_type,
                        'Mode' =>
                            $submission->test_mode,
                        'Statut' =>
                            $submission->status,
                    ],
                    url(
                        '/admin/vocal-tests/submissions/'
                        . $submission->id
                    )
                );
            }
        );

        \App\Models\HighSchoolTestSubmission::created(
            function (
                \App\Models\HighSchoolTestSubmission $submission
            ) {
                app(
                    \App\Services\AdminNotificationService::class
                )->notify(
                    'Nouveau test écrit',
                    [
                        'Soumission' =>
                            '#' . $submission->id,
                        'Utilisateur' =>
                            $submission->user?->name
                            ?? 'Visiteur',
                        'Test' =>
                            $submission->test_title,
                        'Statut' =>
                            $submission->status,
                    ],
                    url(
                        '/admin/high-school-tests/'
                        . $submission->id
                    )
                );
            }
        );

        \App\Models\StudentPayment::created(
            function (
                \App\Models\StudentPayment $payment
            ) {
                if (
                    $payment->status
                    !== \App\Models\StudentPayment::STATUS_PAID
                ) {
                    return;
                }

                app(
                    \App\Services\AdminNotificationService::class
                )->notify(
                    'Nouveau paiement étudiant',
                    [
                        'Étudiant' =>
                            $payment->student?->name
                            ?? ('#' . $payment->user_id),
                        'Montant' =>
                            $payment->amount,
                        'Formule' =>
                            $payment->plan_label,
                        'Méthode' =>
                            $payment->payment_method_label,
                    ],
                    url('/admin/student-payments')
                );
            }
        );


        /*
         * PUSH_NOTIFICATIONS_ALL_ROLES_V11
         *
         * Les événements pédagogiques alimentent le centre de notifications
         * ET Firebase. Une panne Firebase ne doit jamais bloquer l'action
         * principale de l'utilisateur.
         */
        $safeSchoolNotification = function (callable $callback): void {
            try {
                $callback(
                    app(
                        \App\Services\SchoolNotificationService::class
                    )
                );
            } catch (\Throwable $exception) {
                \Illuminate\Support\Facades\Log::warning(
                    '[SchoolNotification] Notification ignorée.',
                    [
                        'exception' => $exception->getMessage(),
                    ]
                );
            }
        };

        \App\Models\Live::created(
            function (\App\Models\Live $live) use ($safeSchoolNotification) {
                $safeSchoolNotification(
                    fn ($service) => $service->liveCreated($live)
                );
            }
        );

        \App\Models\Live::updated(
            function (\App\Models\Live $live) use ($safeSchoolNotification) {
                $safeSchoolNotification(
                    fn ($service) => $service->liveUpdated($live)
                );
            }
        );

        \App\Models\Assignment::created(
            function (\App\Models\Assignment $assignment) use ($safeSchoolNotification) {
                $safeSchoolNotification(
                    fn ($service) => $service->assignmentCreated($assignment)
                );
            }
        );

        \App\Models\Assignment::updated(
            function (\App\Models\Assignment $assignment) use ($safeSchoolNotification) {
                $safeSchoolNotification(
                    fn ($service) => $service->assignmentUpdated($assignment)
                );
            }
        );

        \App\Models\Course::created(
            function (\App\Models\Course $course) use ($safeSchoolNotification) {
                $safeSchoolNotification(
                    fn ($service) => $service->courseCreated($course)
                );
            }
        );

        \App\Models\Course::updated(
            function (\App\Models\Course $course) use ($safeSchoolNotification) {
                $safeSchoolNotification(
                    fn ($service) => $service->courseUpdated($course)
                );
            }
        );

        \App\Models\Message::created(
            function (\App\Models\Message $message) use ($safeSchoolNotification) {
                $safeSchoolNotification(
                    fn ($service) => $service->messageCreated($message)
                );
            }
        );

        view()->composer('layouts.front', function ($view) {
            $religieux = \App\Models\Subject::where('type', 'religieux')->get();
            $scolaire = \App\Models\Subject::where('type', 'scolaire')->get();
            
            $subjectsGrouped = [
                'Matières Religieuses' => [
                    'subjects' => $religieux,
                    'color' => 'primary'
                ],
                'Matières Scolaires' => [
                    'subjects' => $scolaire,
                    'color' => 'success'
                ]
            ];
            
            $view->with('subjectsGrouped', $subjectsGrouped);
        });
    }
}
