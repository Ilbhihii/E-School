<?php

namespace App\Http\Controllers;

use App\Services\PlanCatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function index(
        Request $request,
        PlanCatalogService $catalog
    ) {
        list($planCode, $selectedPlan) = $this->resolvePlan(
            $request->query('plan'),
            $catalog
        );

        $selectedPricing = $catalog->pricingOption(
            $selectedPlan,
            $request->query('duration')
        );

        abort_unless(
            $selectedPricing,
            404,
            'Cette durée n’est pas disponible pour l’offre sélectionnée.'
        );

        $durationMonths = (int) $selectedPricing['duration_months'];
        $method = (string) $request->query('method', '');

        if ($method === 'paypal' && empty($selectedPlan['allow_paypal'])) {
            return redirect()
                ->route('student.payment', [
                    'plan' => $planCode,
                    'duration' => $durationMonths,
                ])
                ->with('error', 'Le paiement PayPal n’est pas activé pour cette offre.');
        }

        if ($method === 'bank' && empty($selectedPlan['allow_bank'])) {
            return redirect()
                ->route('student.payment', [
                    'plan' => $planCode,
                    'duration' => $durationMonths,
                ])
                ->with('error', 'Le virement bancaire n’est pas activé pour cette offre.');
        }

        if (
            Auth::check()
            && Auth::user()->isStudent()
            && $request->filled('plan')
        ) {
            Auth::user()->forceFill([
                'subscription_type' => $planCode,
            ])->save();
        }

        return view('payment', compact(
            'planCode',
            'selectedPlan',
            'selectedPricing',
            'durationMonths'
        ));
    }

    public function processPayment(
        Request $request,
        PlanCatalogService $catalog
    ) {
        $validated = $request->validate([
            'payment_method' => ['required', 'string'],
            'plan' => ['required', 'string', 'max:60'],
            'duration' => ['nullable', 'integer', 'in:1,2,3,4,12'],
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté.',
            ], 401);
        }

        $planCode = $validated['plan'];
        $plan = $catalog->find($planCode, true);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Cette offre est indisponible.',
            ], 422);
        }

        $pricing = $catalog->pricingOption(
            $plan,
            $validated['duration'] ?? null
        );

        if (!$pricing) {
            return response()->json([
                'success' => false,
                'message' => 'Cette durée n’est pas disponible pour cette offre.',
            ], 422);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret_key'));

            $user = Auth::user();

            $paymentIntent = PaymentIntent::create([
                'amount' => (int) $pricing['amount_minor'],
                'currency' => $plan['currency'],
                'payment_method' => $validated['payment_method'],
                'confirmation_method' => 'manual',
                'confirm' => true,
                'return_url' => route('student.payment', [
                    'plan' => $planCode,
                    'duration' => $pricing['duration_months'],
                ]),
                'metadata' => [
                    'user_id' => $user->id,
                    'plan' => $planCode,
                    'duration_months' => (int) $pricing['duration_months'],
                ],
            ]);

            if ($paymentIntent->status === 'succeeded') {
                $user->forceFill([
                    'is_paid' => true,
                    'is_subscribed' => true,
                    'subscription_type' => $planCode,
                    'payment_date' => now()->toDateString(),
                ])->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Paiement réussi.',
                    'redirect' => route('student.dashboard'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Le paiement nécessite une action supplémentaire.',
            ], 400);
        } catch (\Throwable $exception) {
            Log::error('Payment process failed: ' . $exception->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Le paiement n’a pas pu être traité.',
            ], 500);
        }
    }

    public function checkout(
        Request $request,
        PlanCatalogService $catalog
    ) {
        $validated = $request->validate([
            'plan' => ['required', 'string', 'max:60'],
            'duration' => ['nullable', 'integer', 'in:1,2,3,4,12'],
        ]);

        $planCode = $validated['plan'];
        $plan = $catalog->find($planCode, true);

        if (!$plan) {
            throw ValidationException::withMessages([
                'plan' => 'L’offre sélectionnée est indisponible.',
            ]);
        }

        $pricing = $catalog->pricingOption(
            $plan,
            $validated['duration'] ?? null
        );

        if (!$pricing) {
            throw ValidationException::withMessages([
                'duration' => 'Cette durée n’est pas disponible pour cette offre.',
            ]);
        }

        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Connectez-vous avant de continuer le paiement.');
        }

        $user = Auth::user();

        $user->forceFill([
            'subscription_type' => $planCode,
        ])->save();

        Stripe::setApiKey(config('services.stripe.secret_key'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $plan['currency'],
                    'product_data' => [
                        'name' => 'Abonnement ' . $plan['name']
                            . ' — ' . $pricing['label'],
                        'description' => $plan['scope'],
                    ],
                    'unit_amount' => (int) $pricing['amount_minor'],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url(
                '/payment-success?plan=' . urlencode($planCode)
                . '&duration=' . (int) $pricing['duration_months']
            ),
            'cancel_url' => route('student.payment', [
                'plan' => $planCode,
                'duration' => $pricing['duration_months'],
            ]),
            'metadata' => [
                'user_id' => $user->id,
                'plan' => $planCode,
                'duration_months' => (int) $pricing['duration_months'],
            ],
        ]);

        return redirect($session->url);
    }

    public function paypalCheckout(
        Request $request,
        PlanCatalogService $catalog
    ) {
        list($planCode, $plan) = $this->resolvePlan(
            $request->query('plan'),
            $catalog
        );

        $pricing = $catalog->pricingOption(
            $plan,
            $request->query('duration')
        );

        abort_unless(
            $pricing,
            404,
            'Cette durée n’est pas disponible pour cette offre.'
        );

        abort_unless(
            !empty($plan['allow_paypal']),
            404,
            'PayPal n’est pas activé pour cette offre.'
        );

        if (Auth::check() && Auth::user()->isStudent()) {
            Auth::user()->forceFill([
                'subscription_type' => $planCode,
            ])->save();
        }

        return redirect()->away(
            $plan['paypal_url']
            ?: 'https://www.paypal.me/abdelghanimaloulou1'
        );
    }

    private function resolvePlan(
        $requestedPlan,
        PlanCatalogService $catalog
    ) {
        $requestedPlan = is_string($requestedPlan)
            ? trim($requestedPlan)
            : '';

        if ($requestedPlan !== '') {
            $plan = $catalog->find($requestedPlan, true);

            if ($plan) {
                return [$requestedPlan, $plan];
            }
        }

        $planCode = $catalog->defaultCode();
        $plan = $catalog->find($planCode, true);

        abort_unless(
            $plan,
            404,
            'Aucune offre active n’est disponible.'
        );

        return [$planCode, $plan];
    }
}
