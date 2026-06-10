<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payment');
    }

    public function paypalCheckout()
    {
        // Redirection vers PayPal
        return redirect('https://www.paypal.com/checkout');
    }
}

