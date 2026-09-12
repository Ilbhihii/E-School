<?php

namespace App\Console\Commands;

use App\Services\HomeworkReminderService;
use Illuminate\Console\Command;

class CheckMissingAssignments extends Command
{
    protected $signature = 'homework:check-missing';

    protected $description =
        'Détecte les devoirs non remis et envoie les réclamations configurées.';

    public function handle(HomeworkReminderService $service)
    {
        $stats = $service->run();

        $this->info('Vérification des devoirs terminée.');
        $this->line('Devoirs manquants détectés : ' . $stats['missing']);
        $this->line('Emails étudiants envoyés : ' . $stats['student_emails']);
        $this->line('Emails parents envoyés : ' . $stats['parent_emails']);
        $this->line('Échecs : ' . $stats['failed']);

        return 0;
    }
}
