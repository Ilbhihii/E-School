<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function subscribe()
    {
        $user = auth()->user();
        $user->is_subscribed = true;
        $user->save();

        return redirect()->route('student.dashboard')
            ->with('success','Abonnement activé !');
    }
}
