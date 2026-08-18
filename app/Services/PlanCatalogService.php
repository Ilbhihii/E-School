<?php

namespace App\Services;

use App\Models\Plan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class PlanCatalogService
{
    public function all($activeOnly = true)
    {
        /*
         * Avant l'exécution de la migration, on garde la configuration
         * historique comme filet de sécurité. Dès que la table existe,
         * la base de données devient l'unique source de vérité.
         */
        if (Schema::hasTable('plans')) {
            $query = Plan::query();

            if ($activeOnly) {
                $query->active();
            }

            return $query
                ->ordered()
                ->get()
                ->mapWithKeys(function (Plan $plan) {
                    return [
                        $plan->code => $plan->toCatalogArray(),
                    ];
                });
        }

        return $this->fromConfig($activeOnly);
    }

    public function find($code, $activeOnly = true)
    {
        $code = trim((string) $code);

        if ($code === '') {
            return null;
        }

        if (Schema::hasTable('plans')) {
            $query = Plan::query()->where('code', $code);

            if ($activeOnly) {
                $query->active();
            }

            $plan = $query->first();

            return $plan
                ? $plan->toCatalogArray()
                : null;
        }

        return $this->all($activeOnly)->get($code);
    }

    public function defaultCode()
    {
        $configured = (string) config(
            'plans.default',
            'premium'
        );

        if ($this->find($configured, true)) {
            return $configured;
        }

        return (string) $this
            ->all(true)
            ->keys()
            ->first();
    }

    private function fromConfig($activeOnly = true)
    {
        $offers = collect(
            config('plans.offers', [])
        );

        return $offers->mapWithKeys(
            function ($plan, $code) {
                $plan = (array) $plan;

                $plan['code'] =
                    $plan['code'] ?? $code;

                $plan['allow_paypal'] =
                    $plan['allow_paypal'] ?? true;

                $plan['allow_bank'] =
                    $plan['allow_bank'] ?? true;

                $plan['paypal_url'] =
                    $plan['paypal_url']
                    ?? 'https://www.paypal.me/abdelghanimaloulou1';

                $plan['is_recommended'] =
                    $plan['is_recommended']
                    ?? (
                        (string) ($plan['badge'] ?? '')
                        === 'Recommandé'
                    );

                $plan['is_active'] =
                    $plan['is_active'] ?? true;

                $plan['sort_order'] =
                    $plan['sort_order'] ?? 0;

                $plan['features'] =
                    array_values(
                        (array) ($plan['features'] ?? [])
                    );

                return [$code => $plan];
            }
        );
    }
}
