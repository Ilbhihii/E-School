<?php

return [
    'enabled' => env('HOMEWORK_REMINDERS_ENABLED', true),

    // Un rappel étudiant est envoyé une seule fois par devoir manquant.
    'student_email_enabled' => env('HOMEWORK_STUDENT_EMAIL_ENABLED', true),

    // Réclamation parent lorsque plusieurs devoirs sont manquants.
    'parent_email_enabled' => env('HOMEWORK_PARENT_EMAIL_ENABLED', true),
    'parent_threshold' => (int) env('HOMEWORK_PARENT_THRESHOLD', 3),
    'parent_window_days' => (int) env('HOMEWORK_PARENT_WINDOW_DAYS', 30),
    'parent_repeat_step' => (int) env('HOMEWORK_PARENT_REPEAT_STEP', 2),
    'parent_cooldown_days' => (int) env('HOMEWORK_PARENT_COOLDOWN_DAYS', 7),

    // L'heure est locale au serveur Laravel.
    'schedule_time' => env('HOMEWORK_REMINDER_SCHEDULE_TIME', '08:00'),

    /*
     * SMS facultatif.
     * Le projet ne possède pas actuellement de numéro de téléphone sur User.
     * Le service SMS est donc prêt mais reste désactivé tant qu'un fournisseur
     * et un numéro ne sont pas configurés.
     */
    'sms' => [
        'enabled' => env('HOMEWORK_SMS_ENABLED', false),
        'provider' => env('HOMEWORK_SMS_PROVIDER', 'twilio'),
        'twilio_sid' => env('TWILIO_SID'),
        'twilio_token' => env('TWILIO_TOKEN'),
        'twilio_from' => env('TWILIO_FROM'),
    ],
];
