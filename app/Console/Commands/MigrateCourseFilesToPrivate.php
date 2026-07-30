<?php

namespace App\Console\Commands;

use App\Models\Course;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateCourseFilesToPrivate extends Command
{
    protected $signature = 'courses:migrate-private-files {--dry-run}';
    protected $description = 'Déplace les vidéos et PDF de cours du disque public vers le disque privé.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $moved = 0;

        Course::query()->orderBy('id')->chunkById(100, function ($courses) use ($dryRun, &$moved) {
            foreach ($courses as $course) {
                foreach (['video', 'pdf'] as $field) {
                    $path = $course->{$field};
                    if (!$path || !Storage::disk('public')->exists($path)) {
                        continue;
                    }

                    $target = 'course-resources/' . $field . '/' . $course->id . '-' . basename($path);
                    $this->line(($dryRun ? '[DRY] ' : '') . $path . ' -> ' . $target);

                    if (!$dryRun) {
                        Storage::disk('local')->put($target, Storage::disk('public')->get($path));
                        Storage::disk('public')->delete($path);
                        $course->{$field} = $target;
                        $course->save();
                    }

                    $moved++;
                }
            }
        });

        $this->info($moved . ' fichier(s) traité(s).');
        return self::SUCCESS;
    }
}
