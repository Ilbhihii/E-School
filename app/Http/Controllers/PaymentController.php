<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payment');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string'
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret_key'));

            $user = Auth::user();

            // Create and confirm PaymentIntent
            $paymentIntent = Stripe\PaymentIntent::create([
                'amount' => 20000, // 200 EUR in cents
                'currency' => 'eur',
                'payment_method' => $request->payment_method,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'return_url' => route('student.payment'),
                'metadata' => [
                    'user_id' => $user->id
                ]
            ]);

            if ($paymentIntent->status === 'succeeded') {
                $user->is_paid = true;
                $user->save();

                return response()->json([
                    'success' => true, 
                    'message' => 'Paiement réussi ! Redirection...',
                    'redirect' => route('student.dashboard')
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Paiement nécessite action (3D Secure)'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment process failed: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Erreur paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Abonnement étudiant',
                    ],
                    'unit_amount' => 20000,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/payment-success'),
            'cancel_url' => url('/payment'),
        ]);

        return redirect($session->url);
    }

    public function paypalCheckout()
    {
        // Pour test (redirection simple)
        return redirect('https://www.sandbox.paypal.com');

        // PLUS TARD → intégrer vraie API PayPal
    }

}

