<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = config('plans.offers', []);

        $showOnlySoutien =
            $request->query('offer')
            === 'soutien_lycee';

        if ($showOnlySoutien) {
            abort_unless(
                isset($plans['soutien_lycee']),
                404,
                'L’offre Soutien Lycée est indisponible.'
            );

            $plans = [
                'soutien_lycee' =>
                    $plans['soutien_lycee'],
            ];
        } else {
            /*
             * L’offre Soutien Lycée à 1000 DH reste configurée
             * pour les parcours qui utilisent son lien direct,
             * mais elle n’apparaît plus sur la page publique /plans.
             */
            unset($plans['soutien_lycee']);
        }

        return view(
            'plans.index',
            compact(
                'plans',
                'showOnlySoutien'
            )
        );
    }

    /**
     * Flux Stripe conservé pour compatibilité.
     * Le paiement doit ensuite être confirmé par un webhook
     * avant d'accorder définitivement l'accès.
     */
    public function checkout(Request $request)
    {
        $plans = config('plans.offers', []);

        $validated = $request->validate([
            'plan' => [
                'required',
                Rule::in(array_keys($plans)),
            ],
        ]);

        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Connectez-vous avant de choisir une offre.'
                );
        }

        $planCode = $validated['plan'];
        $plan = $plans[$planCode];

        Auth::user()->forceFill([
            'subscription_type' => $planCode,
        ])->save();

        Stripe::setApiKey(
            config('services.stripe.secret_key')
        );

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $plan['currency'],
                    'product_data' => [
                        'name' => 'Abonnement ' . $plan['name'],
                        'description' => $plan['scope'],
                    ],
                    'unit_amount' => $plan['amount_minor'],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route(
                'student.payment',
                [
                    'plan' => $planCode,
                    'checkout' => 'success',
                ]
            ),
            'cancel_url' => route(
                'plans',
                ['plan' => $planCode]
            ),
            'metadata' => [
                'user_id' => Auth::id(),
                'plan' => $planCode,
            ],
        ]);

        return redirect($session->url);
    }
}
