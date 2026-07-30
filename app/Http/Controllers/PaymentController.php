<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        list(
            $planCode,
            $selectedPlan
        ) = $this->resolvePlan(
            $request->query('plan')
        );

        /*
         * Le plan choisi est enregistré comme intention.
         * Cela n’accorde aucun accès : is_paid reste inchangé.
         *
         * Après un virement ou un paiement PayPal manuel,
         * l’administrateur peut activer le paiement et le
         * périmètre restera associé au bon plan.
         */
        if (
            Auth::check()
            && Auth::user()->isStudent()
            && $request->filled('plan')
        ) {
            Auth::user()->forceFill([
                'subscription_type' => $planCode,
            ])->save();
        }

        return view(
            'payment',
            compact(
                'planCode',
                'selectedPlan'
            )
        );
    }

    public function processPayment(
        Request $request
    ) {
        $plans = config('plans.offers', []);

        $validated = $request->validate([
            'payment_method' => [
                'required',
                'string',
            ],
            'plan' => [
                'required',
                Rule::in(
                    array_keys($plans)
                ),
            ],
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Vous devez être connecté.',
            ], 401);
        }

        $planCode = $validated['plan'];
        $plan = $plans[$planCode];

        try {
            Stripe::setApiKey(
                config('services.stripe.secret_key')
            );

            $user = Auth::user();

            $paymentIntent =
                PaymentIntent::create([
                    'amount' =>
                        $plan['amount_minor'],
                    'currency' =>
                        $plan['currency'],
                    'payment_method' =>
                        $validated[
                            'payment_method'
                        ],
                    'confirmation_method' =>
                        'manual',
                    'confirm' => true,
                    'return_url' =>
                        route(
                            'student.payment',
                            [
                                'plan' =>
                                    $planCode,
                            ]
                        ),
                    'metadata' => [
                        'user_id' =>
                            $user->id,
                        'plan' =>
                            $planCode,
                    ],
                ]);

            if (
                $paymentIntent->status
                === 'succeeded'
            ) {
                $user->forceFill([
                    'is_paid' => true,
                    'is_subscribed' => true,
                    'subscription_type' =>
                        $planCode,
                    'payment_date' =>
                        now()->toDateString(),
                ])->save();

                return response()->json([
                    'success' => true,
                    'message' =>
                        'Paiement réussi.',
                    'redirect' =>
                        route(
                            'student.dashboard'
                        ),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' =>
                    'Le paiement nécessite '
                    . 'une action supplémentaire.',
            ], 400);
        } catch (\Throwable $exception) {
            Log::error(
                'Payment process failed: '
                . $exception->getMessage()
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Le paiement n’a pas pu '
                    . 'être traité.',
            ], 500);
        }
    }

    public function checkout(Request $request)
    {
        $plans = config('plans.offers', []);

        $validated = $request->validate([
            'plan' => [
                'required',
                Rule::in(
                    array_keys($plans)
                ),
            ],
        ]);

        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Connectez-vous avant '
                    . 'de continuer le paiement.'
                );
        }

        $planCode = $validated['plan'];
        $plan = $plans[$planCode];
        $user = Auth::user();

        $user->forceFill([
            'subscription_type' =>
                $planCode,
        ])->save();

        Stripe::setApiKey(
            config('services.stripe.secret_key')
        );

        $session =
            \Stripe\Checkout\Session::create([
                'payment_method_types' => [
                    'card',
                ],
                'line_items' => [[
                    'price_data' => [
                        'currency' =>
                            $plan['currency'],
                        'product_data' => [
                            'name' =>
                                'Abonnement '
                                . $plan['name'],
                            'description' =>
                                $plan['scope'],
                        ],
                        'unit_amount' =>
                            $plan[
                                'amount_minor'
                            ],
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
                        'student.payment',
                        [
                            'plan' =>
                                $planCode,
                        ]
                    ),
                'metadata' => [
                    'user_id' => $user->id,
                    'plan' => $planCode,
                ],
            ]);

        return redirect($session->url);
    }

    public function paypalCheckout(
        Request $request
    ) {
        list(
            $planCode
        ) = $this->resolvePlan(
            $request->query('plan')
        );

        if (
            Auth::check()
            && Auth::user()->isStudent()
        ) {
            Auth::user()->forceFill([
                'subscription_type' =>
                    $planCode,
            ])->save();
        }

        return redirect(
            'https://www.sandbox.paypal.com'
        );
    }

    private function resolvePlan(
        $requestedPlan
    ) {
        $plans = config('plans.offers', []);
        $defaultPlan = config(
            'plans.default',
            'premium'
        );

        $planCode = is_string($requestedPlan)
            && isset($plans[$requestedPlan])
                ? $requestedPlan
                : $defaultPlan;

        abort_unless(
            isset($plans[$planCode]),
            500,
            'La configuration des offres '
            . 'est incomplète.'
        );

        return [
            $planCode,
            $plans[$planCode],
        ];
    }
}
