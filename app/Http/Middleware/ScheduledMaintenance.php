<?php

namespace App\Http\Middleware;

use App\Models\MaintenanceSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class ScheduledMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        if (!Schema::hasTable('maintenance_settings')) {
            return $next($request);
        }

        $maintenance = MaintenanceSetting::query()->find(1);

        if (!$maintenance) {
            return $next($request);
        }

        View::share(
            'maintenanceNotice',
            $maintenance->isAnnouncementVisible()
                ? $maintenance
                : null
        );

        if (!$maintenance->isBlockingNow()) {
            return $next($request);
        }

        $user = auth()->user();

        if (
            $user
            && method_exists($user, 'isAdmin')
            && $user->isAdmin()
        ) {
            return $next($request);
        }

        // Permet à un administrateur de se connecter pendant la maintenance.
        if (
            $request->is('login')
            || $request->is('logout')
        ) {
            return $next($request);
        }

        $remainingSeconds = max(
            0,
            now()->diffInSeconds(
                $maintenance->end_at,
                false
            )
        );

        return response()
            ->view(
                'maintenance',
                compact(
                    'maintenance',
                    'remainingSeconds'
                ),
                503
            )
            ->header(
                'Retry-After',
                (string) $remainingSeconds
            );
    }
}
