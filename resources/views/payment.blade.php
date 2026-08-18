@extends('layouts.front')

@section('title', 'Paiement — ' . $selectedPlan['name'])

@section('content')

<div class="payment-page">
    <div class="container py-5">
        <div class="payment-card">
            <a href="{{ route('plans') }}" class="payment-back">
                <i class="bi bi-arrow-left"></i>
                Retour aux offres
            </a>

            <span class="payment-plan-icon">
                <i class="bi {{ $selectedPlan['icon'] }}"></i>
            </span>

            <small class="payment-scope">{{ $selectedPlan['scope'] }}</small>
            <h1>{{ $selectedPlan['name'] }}</h1>
            <p>{{ $selectedPlan['subtitle'] }}</p>

            @if(request('checkout') === 'success')
                <div class="payment-alert success">
                    <i class="bi bi-check-circle-fill"></i>
                    Paiement reçu. L’accès sera activé après validation sécurisée.
                </div>
            @endif

            @if(session('error'))
                <div class="payment-alert error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="payment-price">
                <strong>{{ $selectedPlan['amount_display'] }}</strong>
                <span>{{ $selectedPlan['currency_symbol'] }}</span>
                <small>/ {{ $selectedPlan['period'] }}</small>
            </div>

            <div class="payment-features">
                @foreach($selectedPlan['features'] as $feature)
                    <div>
                        <span><i class="bi bi-check-lg"></i></span>
                        {{ $feature }}
                    </div>
                @endforeach
            </div>

            @if($selectedPlan['restricted_to_high_school'] ?? false)
                <div class="payment-restriction">
                    <i class="bi bi-shield-lock-fill"></i>
                    Cette formule est limitée à Mathématiques BAC
                    et Physique-Chimie BAC. Arabe et Coran restent bloqués.
                </div>
            @endif

            <div class="payment-reference">
                <span>Référence</span>
                <strong>SSA-{{ strtoupper(str_replace('_', '-', $planCode)) }}-{{ auth()->check() ? auth()->id() : 'COMPTE' }}</strong>
            </div>

            <div class="payment-methods">
                @if(
                    request('method') === 'paypal'
                    && ($selectedPlan['allow_paypal'] ?? true)
                )
                    <div class="method-box">
                        <h3><i class="bi bi-paypal"></i> Paiement PayPal</h3>
                        <p>
                            Payez {{ $selectedPlan['amount_display'] }}
                            {{ $selectedPlan['currency_symbol'] }}, puis envoyez
                            la confirmation et la référence à l’administration.
                        </p>
                        <a href="{{ $selectedPlan['paypal_url'] ?: 'https://www.paypal.me/abdelghanimaloulou1' }}" target="_blank" rel="noopener" class="payment-button paypal">
                            <i class="bi bi-paypal"></i>
                            Continuer sur PayPal
                        </a>
                    </div>
                    <a href="{{ route('student.payment', ['plan' => $planCode]) }}" class="change-method">
                        <i class="bi bi-arrow-left"></i> Changer la méthode
                    </a>
                @elseif(
                    request('method') === 'bank'
                    && ($selectedPlan['allow_bank'] ?? true)
                )
                    <div class="method-box">
                        <h3><i class="bi bi-bank"></i> Virement bancaire</h3>
                        <div class="bank-block">
                            <strong>Maroc — Banque Populaire</strong>
                            <span>RIB : 123456789012345678901234</span>
                            <span>Titulaire : M. Abdelghani Maloulou</span>
                        </div>
                        <div class="bank-block">
                            <strong>France — EUROCOMPTE SÉRÉNITÉ</strong>
                            <span>IBAN : FR76 1027 8089 7600 0210 7440 103</span>
                            <span>BIC : CMCIFR2A</span>
                        </div>
                        <p>
                            Indiquez la référence dans le motif du virement,
                            puis envoyez le reçu à l’administration.
                        </p>
                    </div>
                    <a href="{{ route('student.payment', ['plan' => $planCode]) }}" class="change-method">
                        <i class="bi bi-arrow-left"></i> Changer la méthode
                    </a>
                @else
                    @if($selectedPlan['allow_paypal'] ?? true)
                        <a href="{{ route('student.payment', ['plan' => $planCode, 'method' => 'paypal']) }}" class="payment-button paypal">
                            <i class="bi bi-paypal"></i>
                            Payer avec PayPal
                        </a>
                    @endif

                    @if($selectedPlan['allow_bank'] ?? true)
                        <a href="{{ route('student.payment', ['plan' => $planCode, 'method' => 'bank']) }}" class="payment-button bank">
                            <i class="bi bi-bank"></i>
                            Virement bancaire
                        </a>
                    @endif

                    @if(
                        !($selectedPlan['allow_paypal'] ?? true)
                        && !($selectedPlan['allow_bank'] ?? true)
                    )
                        <a href="{{ route('appointment.create') }}" class="payment-button bank">
                            <i class="bi bi-chat-dots-fill"></i>
                            Contacter l’administration
                        </a>
                    @endif
                @endif
            </div>

            <div class="payment-security">
                <i class="bi bi-shield-check"></i>
                Le plan est mémorisé, mais is_paid ne change pas
                avant confirmation réelle du paiement.
            </div>
        </div>
    </div>
</div>

<style>
.payment-page{position:relative;min-height:100vh;padding:6.5rem 0 4rem;background:radial-gradient(circle at 15% 20%,rgba(37,99,235,.18),transparent 32%),radial-gradient(circle at 88% 75%,rgba(124,58,237,.18),transparent 34%),linear-gradient(135deg,#0f0c29,#302b63,#24243e)}
.payment-card{max-width:650px;margin:0 auto;padding:1.7rem;border:1px solid rgba(255,255,255,.11);border-radius:27px;background:rgba(255,255,255,.065);box-shadow:0 30px 75px rgba(0,0,0,.35);backdrop-filter:blur(20px);text-align:center}
.payment-back{display:inline-flex;align-items:center;gap:7px;float:left;color:rgba(255,255,255,.42);font-size:.68rem;text-decoration:none}.payment-back:hover{color:#fff}
.payment-plan-icon{width:68px;height:68px;display:grid;place-items:center;clear:both;margin:2rem auto 1rem;border-radius:21px;color:#fff;background:linear-gradient(135deg,#2563eb,#7c3aed);font-size:1.55rem}
.payment-scope{display:inline-flex;padding:5px 9px;border-radius:999px;color:#bfdbfe;background:rgba(37,99,235,.13);font-size:.61rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase}
.payment-card h1{margin:.65rem 0 .3rem;color:#fff;font-size:1.75rem;font-weight:850}.payment-card>p{margin:0;color:rgba(255,255,255,.46);font-size:.78rem}
.payment-alert{display:flex;align-items:center;gap:8px;margin-top:1rem;padding:10px 12px;border-radius:11px;font-size:.7rem;text-align:left}.payment-alert.success{border:1px solid rgba(34,197,94,.18);color:#86efac;background:rgba(34,197,94,.08)}.payment-alert.error{border:1px solid rgba(239,68,68,.18);color:#fca5a5;background:rgba(239,68,68,.08)}
.payment-price{display:flex;align-items:baseline;justify-content:center;gap:5px;margin:1.25rem 0}.payment-price strong{color:#fff;font-size:3.2rem;font-weight:900;letter-spacing:-.05em;line-height:1}.payment-price span{color:rgba(255,255,255,.72);font-size:1.05rem;font-weight:800}.payment-price small{color:rgba(255,255,255,.33);font-size:.73rem}
.payment-features{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px;margin-bottom:1rem;text-align:left}.payment-features>div{display:flex;align-items:flex-start;gap:8px;color:rgba(255,255,255,.7);font-size:.68rem;line-height:1.45}.payment-features span{width:20px;height:20px;flex:0 0 20px;display:grid;place-items:center;border-radius:50%;color:#fff;background:linear-gradient(135deg,#667eea,#764ba2);font-size:.52rem}
.payment-restriction{display:flex;align-items:flex-start;gap:8px;margin-bottom:.9rem;padding:10px;border:1px solid rgba(245,158,11,.17);border-radius:11px;color:#fcd34d;background:rgba(245,158,11,.08);font-size:.65rem;line-height:1.45;text-align:left}
.payment-reference{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:.9rem;padding:10px 12px;border:1px dashed rgba(96,165,250,.24);border-radius:11px;background:rgba(37,99,235,.06);text-align:left}.payment-reference span{color:rgba(255,255,255,.38);font-size:.61rem}.payment-reference strong{color:#bfdbfe;font-size:.67rem;word-break:break-word}
.payment-methods{display:flex;flex-direction:column;gap:9px}.payment-button{min-height:47px;display:flex;align-items:center;justify-content:center;gap:8px;border-radius:14px;color:#fff;font-size:.77rem;font-weight:800;text-decoration:none;transition:.25s ease}.payment-button:hover{color:#fff;transform:translateY(-2px);filter:brightness(1.08)}.payment-button.paypal{background:linear-gradient(135deg,#0070ba,#1546a0)}.payment-button.bank{border:1px solid rgba(255,255,255,.17);background:rgba(255,255,255,.07)}
.method-box{padding:1rem;border:1px solid rgba(255,255,255,.08);border-radius:15px;background:rgba(0,0,0,.15);text-align:left}.method-box h3{display:flex;align-items:center;gap:8px;margin:0 0 .75rem;color:#fff;font-size:.8rem}.method-box p{margin:.75rem 0;color:rgba(255,255,255,.45);font-size:.66rem;line-height:1.5}.bank-block{display:flex;flex-direction:column;gap:4px;margin-bottom:8px;padding:10px;border-radius:10px;background:rgba(255,255,255,.045)}.bank-block strong{color:rgba(255,255,255,.74);font-size:.64rem}.bank-block span{color:rgba(255,255,255,.48);font-size:.62rem;word-break:break-word}.change-method{display:inline-flex;align-items:center;justify-content:center;gap:7px;color:rgba(255,255,255,.42);font-size:.66rem;text-decoration:none}.change-method:hover{color:#fff}
.payment-security{display:flex;align-items:center;justify-content:center;gap:7px;margin-top:1rem;color:rgba(255,255,255,.32);font-size:.61rem;line-height:1.45}
html.light-mode .payment-page{background:linear-gradient(135deg,#f0f4ff,#e8edf5,#f5f7fa)}html.light-mode .payment-card{border-color:rgba(15,23,42,.09);background:rgba(255,255,255,.95);box-shadow:0 25px 60px rgba(15,23,42,.1)}html.light-mode .payment-card h1,html.light-mode .payment-price strong{color:#172033}html.light-mode .payment-card>p,html.light-mode .payment-price small,html.light-mode .payment-security,html.light-mode .payment-back{color:#64748b}html.light-mode .payment-price span,html.light-mode .payment-features>div{color:#334155}html.light-mode .method-box{border-color:rgba(15,23,42,.08);background:rgba(15,23,42,.03)}html.light-mode .method-box h3,html.light-mode .bank-block strong{color:#334155}html.light-mode .method-box p,html.light-mode .bank-block span{color:#64748b}html.light-mode .bank-block{background:rgba(15,23,42,.04)}html.light-mode .payment-button.bank{border-color:rgba(15,23,42,.12);color:#334155;background:rgba(15,23,42,.04)}
@media(max-width:620px){.payment-page{padding-top:5.5rem}.payment-card{padding:1.15rem;border-radius:21px}.payment-features{grid-template-columns:1fr}.payment-reference{align-items:flex-start;flex-direction:column}}
</style>

@endsection
