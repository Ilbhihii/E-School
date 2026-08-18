<?php

namespace App\Http\Controllers;

use App\Services\PlanCatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PlanController extends Controller
{
    public function index(
        Request $request,
        PlanCatalogService $catalog
    ) {
        $plans = $catalog->all(true);
        $offerCode = trim(
            (string) $request->query('offer', '')
        );

        $singleOffer = false;
        $singleOfferName = null;

        if ($offerCode !== '') {
            $plan = $plans->get($offerCode);

            abort_unless(
                $plan,
                404,
                'Cette offre est indisponible.'
            );

            $plans = collect([
                $offerCode => $plan,
            ]);

            $singleOffer = true;
            $singleOfferName = $plan['name'];
        }

        /*
         * Variable historique conservée pour les anciennes variantes
         * de la vue et les liens Soutien Lycée existants.
         */
        $showOnlySoutien =
            $singleOffer
            && (bool) (
                $plans->first()[
                    'restricted_to_high_school'
                ] ?? false
            );

        return view(
            'plans.index',
            [
                'plans' => $plans->all(),
                'singleOffer' => $singleOffer,
                'singleOfferName' => $singleOfferName,
                'showOnlySoutien' => $showOnlySoutien,
            ]
        );
    }

    /**
     * Flux Stripe conservé pour compatibilité.
     * L'accès n'est accordé qu'après confirmation réelle du paiement.
     */
    public function checkout(
        Request $request,
        PlanCatalogService $catalog
    ) {
        $validated = $request->validate([
            'plan' => [
                'required',
                'string',
                'max:60',
            ],
        ]);

        $planCode = $validated['plan'];
        $plan = $catalog->find($planCode, true);

        if (!$plan) {
            throw ValidationException::withMessages([
                'plan' => 'L’offre sélectionnée est indisponible.',
            ]);
        }

        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Connectez-vous avant de choisir une offre.'
                );
        }

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
                ['offer' => $planCode]
            ),
            'metadata' => [
                'user_id' => Auth::id(),
                'plan' => $planCode,
            ],
        ]);

        return redirect($session->url);
    }
}
