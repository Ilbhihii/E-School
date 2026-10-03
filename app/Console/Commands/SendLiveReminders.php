<?php

namespace App\Console\Commands;

use App\Models\Live;
use App\Services\SchoolNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendLiveReminders extends Command
{
    protected $signature = 'notifications:live-reminders';

    protected $description = 'Envoie les rappels push des lives environ 30 minutes avant le début';

    public function handle(SchoolNotificationService $notifications): int
    {
        $now = now();

        $lives = Live::query()
            ->whereDate('live_date', '>=', $now->copy()->subDay()->toDateString())
            ->whereDate('live_date', '<=', $now->copy()->addDay()->toDateString())
            ->get();

        $sent = 0;

        foreach ($lives as $live) {
            $start = $live->start_date_time;

            if (!$start) {
                continue;
            }

            $minutes = $now->diffInMinutes($start, false);

            if ($minutes < 25 || $minutes > 35) {
                continue;
            }

            $key = 'ssa:live-reminder:'
                . $live->id
                . ':'
                . $start->format('YmdHi');

            if (!Cache::add($key, true, now()->addHours(8))) {
                continue;
            }

            $notifications->liveReminder($live);
            $sent++;
        }

        $this->info("{$sent} rappel(s) de live traité(s).");

        return self::SUCCESS;
    }
}
