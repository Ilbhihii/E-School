<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    public function index()
    {
        return view('plans.index');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:standard,premium'
        ]);

        $amounts = [
            'standard' => 9900,  // 99 MAD
            'premium' => 14900   // 149 MAD
        ];

        Stripe::setApiKey(config('services.stripe.secret_key'));

        $session = Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'mad',
                    'product_data' => [
                        'name' => 'Abonnement ' . ucfirst($request->plan),
                    ],
                    'unit_amount' => $amounts[$request->plan],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/payment-success?plan=' . $request->plan),
            'cancel_url' => url('/plans'),
            'metadata' => [
                'user_id' => Auth::id(),
                'plan' => $request->plan
            ]
        ]);

        return redirect($session->url);
    }
}

