<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PlanController extends Controller
{
    public function index()
    {
        $plans = config('plans.offers', []);

        return view(
            'plans.index',
            compact('plans')
        );
    }

    /*
     * Cette méthode est conservée pour compatibilité.
     * Le flux actuellement utilisé par les vues passe
     * principalement par PaymentController.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'plan' => [
                'required',
                'string',
            ],
        ]);

        $plans = config('plans.offers', []);
        $planCode = $validated['plan'];

        abort_unless(
            isset($plans[$planCode]),
            422,
            'L’offre sélectionnée est invalide.'
        );

        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Connectez-vous avant de continuer le paiement.'
                );
        }

        $plan = $plans[$planCode];

        /*
         * Le choix est mémorisé, mais aucun accès payant
         * n’est activé avant confirmation du paiement.
         */
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
                        'name' =>
                            'Abonnement '
                            . $plan['name'],
                        'description' =>
                            $plan['scope'],
                    ],
                    'unit_amount' =>
                        $plan['amount_minor'],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' =>
                url(
                    '/payment-success?plan='
                    . urlencode($planCode)
                ),
            'cancel_url' =>
                route(
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
