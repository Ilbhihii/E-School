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


    public function pricingOption(array $plan, $requestedDuration = null)
    {
        $options = collect($plan['pricing_options'] ?? []);

        if ($options->isEmpty()) {
            $options = collect([[
                'duration_months' => 12,
                'label' => '12 mois — Annuel',
                'amount_minor' => (int) ($plan['amount_minor'] ?? 0),
                'amount_display' => (string) ($plan['amount_display'] ?? '0'),
                'period_label' => (string) ($plan['period'] ?? 'an'),
                'is_best_value' => true,
            ]]);
        }

        $hasExplicitDuration =
            $requestedDuration !== null
            && $requestedDuration !== ''
            && is_numeric($requestedDuration);

        $duration = $hasExplicitDuration
            ? (int) $requestedDuration
            : 12;

        $selected = $options->first(function ($option) use ($duration) {
            return (int) ($option['duration_months'] ?? 0) === $duration;
        });

        if ($selected) {
            return $selected;
        }

        return $hasExplicitDuration
            ? null
            : $options->first();
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

                $plan['whatsapp_france'] =
                    $plan['whatsapp_france']
                    ?? '+33 7 60 96 12 74';

                $plan['whatsapp_maroc'] =
                    $plan['whatsapp_maroc']
                    ?? '+212 6 65 72 99 77';

                $plan['whatsapp_message'] =
                    $plan['whatsapp_message']
                    ?? 'Bonjour, je souhaite envoyer mon reçu de paiement pour l’offre {offre}. Durée : {duree}. Référence : {reference}. Montant : {montant} {devise}. Je joins le reçu à ce message.';

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

                $plan['pricing_options'] = [[
                    'duration_months' => 12,
                    'label' => '12 mois — Annuel',
                    'amount_minor' => (int) ($plan['amount_minor'] ?? 0),
                    'amount_display' => (string) ($plan['amount_display'] ?? '0'),
                    'period_label' => (string) ($plan['period'] ?? 'an'),
                    'is_best_value' => true,
                ]];

                return [$code => $plan];
            }
        );
    }
}
