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

        /*
         * L'annonce reste pilotée normalement par la case
         * "Afficher l'annonce".
         *
         * Mais dès qu'une maintenance bloquante est réellement en cours,
         * l'accueil doit impérativement informer le visiteur.
         */
        $blockingNow = $maintenance->isBlockingNow();

        View::share(
            'maintenanceNotice',
            (
                $maintenance->isAnnouncementVisible()
                || $blockingNow
            )
                ? $maintenance
                : null
        );

        if (!$blockingNow) {
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

        /*
         * L'accueil public reste visible pendant la maintenance.
         * Il reçoit maintenanceNotice via View::share() ci-dessus,
         * donc le bandeau d'information est affiché.
         */
        if (
            $request->routeIs('home')
            || $request->is('/')
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
