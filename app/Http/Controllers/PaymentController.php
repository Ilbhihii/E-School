<?php

namespace App\Http\Controllers;

use App\Models\StudentPayment;
use App\Models\User;
use App\Services\PlanCatalogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function index(Request $request, PlanCatalogService $catalog)
    {
        [$planCode, $selectedPlan] = $this->resolvePlan($request->query('plan'), $catalog);
        $selectedPricing = $catalog->pricingOption($selectedPlan, $request->query('duration'));
        abort_unless($selectedPricing, 404, 'Cette durée n’est pas disponible pour l’offre sélectionnée.');
        $durationMonths = (int) $selectedPricing['duration_months'];
        $method = (string) $request->query('method', '');

        if ($method === 'paypal' && empty($selectedPlan['allow_paypal'])) {
            return redirect()->route('student.payment', ['plan' => $planCode, 'duration' => $durationMonths])
                ->with('error', 'Le paiement PayPal n’est pas activé pour cette offre.');
        }
        if ($method === 'bank' && empty($selectedPlan['allow_bank'])) {
            return redirect()->route('student.payment', ['plan' => $planCode, 'duration' => $durationMonths])
                ->with('error', 'Le virement bancaire n’est pas activé pour cette offre.');
        }

        // Consulter une offre ne modifie jamais l'abonnement effectif.
        return view('payment', compact('planCode', 'selectedPlan', 'selectedPricing', 'durationMonths'));
    }

    public function processPayment(Request $request, PlanCatalogService $catalog)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string'],
            'plan' => ['required', 'string', 'max:60'],
            'duration' => ['nullable', 'integer', 'in:1,2,3,4,12'],
        ]);
        abort_unless(Auth::check(), 401, 'Vous devez être connecté.');
        $planCode = $validated['plan'];
        $plan = $catalog->find($planCode, true);
        if (!$plan) throw ValidationException::withMessages(['plan' => 'Cette offre est indisponible.']);
        $pricing = $catalog->pricingOption($plan, $validated['duration'] ?? null);
        if (!$pricing) throw ValidationException::withMessages(['duration' => 'Cette durée n’est pas disponible pour cette offre.']);

        try {
            Stripe::setApiKey(config('services.stripe.secret_key'));
            $user = Auth::user();
            $intent = PaymentIntent::create([
                'amount' => (int) $pricing['amount_minor'],
                'currency' => $plan['currency'],
                'payment_method' => $validated['payment_method'],
                'confirmation_method' => 'manual', 'confirm' => true,
                'return_url' => route('student.payment', ['plan' => $planCode, 'duration' => $pricing['duration_months']]),
                'metadata' => ['user_id' => $user->id, 'plan' => $planCode, 'duration_months' => (int) $pricing['duration_months']],
            ]);

            if ($intent->status === 'succeeded') {
                $this->recordVerifiedPayment($user, $planCode, (int) $pricing['duration_months'], (int) $pricing['amount_minor'], $plan['currency'], 'stripe', $intent->id);
                return response()->json(['success' => true, 'message' => 'Paiement réussi.', 'redirect' => route('student.dashboard')]);
            }
            return response()->json(['success' => false, 'message' => 'Le paiement nécessite une action supplémentaire.'], 400);
        } catch (\Throwable $e) {
            Log::error('Payment process failed: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Le paiement n’a pas pu être traité.'], 500);
        }
    }

    public function checkout(Request $request, PlanCatalogService $catalog)
    {
        $validated = $request->validate(['plan' => ['required','string','max:60'], 'duration' => ['nullable','integer','in:1,2,3,4,12']]);
        if (!Auth::check()) return redirect()->route('login')->with('error', 'Connectez-vous avant de continuer le paiement.');
        $planCode = $validated['plan'];
        $plan = $catalog->find($planCode, true);
        if (!$plan) throw ValidationException::withMessages(['plan' => 'L’offre sélectionnée est indisponible.']);
        $pricing = $catalog->pricingOption($plan, $validated['duration'] ?? null);
        if (!$pricing) throw ValidationException::withMessages(['duration' => 'Cette durée n’est pas disponible pour cette offre.']);

        Stripe::setApiKey(config('services.stripe.secret_key'));
        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[ 'price_data' => [
                'currency' => $plan['currency'],
                'product_data' => ['name' => 'Abonnement '.$plan['name'].' — '.$pricing['label'], 'description' => $plan['scope']],
                'unit_amount' => (int) $pricing['amount_minor'],
            ], 'quantity' => 1 ]],
            'mode' => 'payment',
            'success_url' => route('payment.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('student.payment', ['plan' => $planCode, 'duration' => $pricing['duration_months']]),
            'metadata' => ['user_id' => Auth::id(), 'plan' => $planCode, 'duration_months' => (int) $pricing['duration_months']],
        ]);
        return redirect($session->url);
    }

    public function checkoutSuccess(Request $request, PlanCatalogService $catalog)
    {
        $sessionId = trim((string) $request->query('session_id'));
        abort_if($sessionId === '', 400, 'Session de paiement absente.');
        Stripe::setApiKey(config('services.stripe.secret_key'));
        $session = CheckoutSession::retrieve($sessionId);
        abort_unless((int) ($session->metadata->user_id ?? 0) === (int) Auth::id(), 403);
        abort_unless($session->payment_status === 'paid', 409, 'Le paiement n’est pas confirmé.');
        $this->fulfillCheckout($session, $catalog);
        return redirect()->route('student.dashboard')->with('success', 'Paiement confirmé. Votre abonnement est actif.');
    }

    public function stripeWebhook(Request $request, PlanCatalogService $catalog)
    {
        $secret = (string) config('services.stripe.webhook_secret');
        if ($secret === '') return response()->json(['error' => 'Webhook Stripe non configuré.'], 503);
        try {
            $event = Webhook::constructEvent($request->getContent(), (string) $request->header('Stripe-Signature'), $secret);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Signature Stripe invalide.'], 400);
        }
        if ($event->type === 'checkout.session.completed' || $event->type === 'checkout.session.async_payment_succeeded') {
            $this->fulfillCheckout($event->data->object, $catalog);
        }
        return response()->json(['received' => true]);
    }

    public function paypalCheckout(Request $request, PlanCatalogService $catalog)
    {
        [$planCode, $plan] = $this->resolvePlan($request->query('plan'), $catalog);
        $pricing = $catalog->pricingOption($plan, $request->query('duration'));
        abort_unless($pricing, 404, 'Cette durée n’est pas disponible pour cette offre.');
        abort_unless(!empty($plan['allow_paypal']), 404, 'PayPal n’est pas activé pour cette offre.');
        // Ne jamais activer/modifier l'abonnement avant confirmation serveur du paiement.
        return redirect()->away($plan['paypal_url'] ?: 'https://www.paypal.me/abdelghanimaloulou1');
    }

    private function fulfillCheckout($session, PlanCatalogService $catalog): void
    {
        if (($session->payment_status ?? null) !== 'paid') return;
        $user = User::find((int) ($session->metadata->user_id ?? 0));
        if (!$user || !$user->isStudent()) return;
        $planCode = (string) ($session->metadata->plan ?? '');
        $duration = (int) ($session->metadata->duration_months ?? 0);
        $plan = $catalog->find($planCode, false);
        $pricing = $plan ? $catalog->pricingOption($plan, $duration) : null;
        if (!$plan || !$pricing) throw new \RuntimeException('Offre Stripe inconnue.');
        if ((int) ($session->amount_total ?? -1) !== (int) $pricing['amount_minor']) throw new \RuntimeException('Montant Stripe incohérent.');
        if (strtolower((string) ($session->currency ?? '')) !== strtolower((string) $plan['currency'])) throw new \RuntimeException('Devise Stripe incohérente.');
        $this->recordVerifiedPayment($user, $planCode, $duration, (int) $pricing['amount_minor'], $plan['currency'], 'stripe', (string) $session->id);
    }

    private function recordVerifiedPayment(User $user, string $planCode, int $durationMonths, int $amountMinor, string $currency, string $provider, string $reference): void
    {
        DB::transaction(function () use ($user,$planCode,$durationMonths,$amountMinor,$currency,$provider,$reference) {
            if (StudentPayment::where('payment_provider',$provider)->where('provider_reference',$reference)->exists()) return;
            $start = Carbon::today();
            $expires = $start->copy()->addMonthsNoOverflow($durationMonths);
            StudentPayment::create([
                'user_id' => $user->id,
                'plan_type' => $durationMonths === 12 ? StudentPayment::PLAN_ANNUAL : ($durationMonths === 4 ? StudentPayment::PLAN_FOUR_MONTHS : substr($planCode.'_'.$durationMonths.'m',0,30)),
                'amount' => $amountMinor / 100,
                'paid_at' => $start->toDateString(), 'starts_at' => $start->toDateString(), 'expires_at' => $expires->toDateString(),
                'payment_method' => 'online', 'payment_provider' => $provider, 'provider_reference' => $reference,
                'status' => StudentPayment::STATUS_PAID,
                'notes' => 'Paiement vérifié côté serveur ('.strtoupper($currency).').',
            ]);
            $user->forceFill([
                'is_paid' => true, 'is_subscribed' => true, 'subscription_type' => $planCode, 'payment_date' => $start->toDateString(),
            ])->save();
        });
    }

    private function resolvePlan($requestedPlan, PlanCatalogService $catalog)
    {
        $requestedPlan = is_string($requestedPlan) ? trim($requestedPlan) : '';
        if ($requestedPlan !== '') {
            $plan = $catalog->find($requestedPlan, true);
            if ($plan) return [$requestedPlan, $plan];
        }
        $planCode = $catalog->defaultCode();
        $plan = $catalog->find($planCode, true);
        abort_unless($plan, 404, 'Aucune offre active n’est disponible.');
        return [$planCode, $plan];
    }
}
