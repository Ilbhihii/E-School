@extends('layouts.front')

@section('title', 'Nos offres')

@section('content')

<div class="plans-wrapper">
    <div class="plans-decoration">
        <span class="orb orb-one"></span>
        <span class="orb orb-two"></span>
        <span class="orb orb-three"></span>
    </div>

    <div class="container plans-container">
        <header class="plans-header">
            <span class="plans-eyebrow">
                <i class="bi bi-stars"></i>
                ABONNEMENTS SMART SCHOOL
            </span>

            <h1>Choisissez la formule adaptée</h1>

            <p>
                Une offre complète pour toute la plateforme
                ou une formule dédiée au Soutien Lycée.
            </p>
        </header>

        @if(session('error'))
            <div class="plans-alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="plans-grid">
            @foreach($plans as $planCode => $plan)
                <article
                    class="plan-card
                        plan-card-{{ $planCode }}"
                >
                    <div class="plan-card-header">
                        <span class="plan-badge">
                            {{ $plan['badge'] }}
                        </span>

                        <span class="plan-icon">
                            <i
                                class="bi {{
                                    $plan['icon']
                                }}"
                            ></i>
                        </span>
                    </div>

                    <div class="plan-copy">
                        <p class="plan-scope">
                            {{ $plan['scope'] }}
                        </p>

                        <h2>{{ $plan['name'] }}</h2>

                        <p class="plan-subtitle">
                            {{ $plan['subtitle'] }}
                        </p>
                    </div>

                    <div class="plan-price">
                        <span class="plan-amount">
                            {{ $plan['amount_display'] }}
                        </span>

                        <span class="plan-currency">
                            {{ $plan['currency_symbol'] }}
                        </span>

                        <span class="plan-period">
                            / {{ $plan['period'] }}
                        </span>
                    </div>

                    <ul class="plan-features">
                        @foreach(
                            $plan['features']
                            as $feature
                        )
                            <li>
                                <span>
                                    <i class="bi bi-check-lg"></i>
                                </span>

                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    @if(
                        $planCode
                        === 'soutien_lycee'
                    )
                        <div class="plan-restriction">
                            <i class="bi bi-shield-lock-fill"></i>

                            Cette offre ne donne pas accès
                            aux parcours Arabe et Coran.
                        </div>
                    @endif

                    <div class="plan-actions">
                        <a
                            href="{{
                                route(
                                    'student.payment',
                                    [
                                        'plan' =>
                                            $planCode,
                                        'method' =>
                                            'paypal',
                                    ]
                                )
                            }}"
                            class="plan-button
                                plan-button-primary"
                        >
                            <i class="bi bi-paypal"></i>
                            Payer avec PayPal
                        </a>

                        <a
                            href="{{
                                route(
                                    'student.payment',
                                    [
                                        'plan' =>
                                            $planCode,
                                        'method' =>
                                            'bank',
                                    ]
                                )
                            }}"
                            class="plan-button
                                plan-button-secondary"
                        >
                            <i class="bi bi-bank"></i>
                            Virement bancaire
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="plans-note">
            <i class="bi bi-shield-check"></i>

            Le choix du plan ne suffit pas à activer
            l’accès. L’accès est ouvert uniquement après
            confirmation du paiement.
        </div>
    </div>
</div>

<style>
.plans-wrapper {
    position: relative;
    min-height: 100vh;
    overflow: hidden;
    padding: 6.5rem 0 4rem;
    background:
        radial-gradient(
            circle at 12% 20%,
            rgba(37,99,235,.17),
            transparent 32%
        ),
        radial-gradient(
            circle at 88% 22%,
            rgba(124,58,237,.18),
            transparent 32%
        ),
        linear-gradient(
            135deg,
            #07101f 0%,
            #10182d 48%,
            #17102d 100%
        );
}

.plans-decoration {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(4px);
    animation:
        planFloat 9s ease-in-out infinite;
}

.orb-one {
    top: 6%;
    right: 7%;
    width: 250px;
    height: 250px;
    background:
        radial-gradient(
            circle,
            rgba(96,165,250,.22),
            transparent 68%
        );
}

.orb-two {
    bottom: 8%;
    left: 4%;
    width: 290px;
    height: 290px;
    background:
        radial-gradient(
            circle,
            rgba(168,85,247,.2),
            transparent 68%
        );
    animation-delay: -3s;
}

.orb-three {
    top: 42%;
    left: 43%;
    width: 150px;
    height: 150px;
    background:
        radial-gradient(
            circle,
            rgba(245,158,11,.13),
            transparent 68%
        );
    animation-delay: -6s;
}

@keyframes planFloat {
    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-22px);
    }
}

.plans-container {
    position: relative;
    z-index: 2;
}

.plans-header {
    max-width: 780px;
    margin: 0 auto 2.5rem;
    text-align: center;
}

.plans-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 1rem;
    padding: 8px 14px;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 999px;
    color: #BFDBFE;
    background: rgba(255,255,255,.05);
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
}

.plans-header h1 {
    margin-bottom: .8rem;
    color: #fff;
    font-size: clamp(2rem,5vw,3.5rem);
    font-weight: 850;
}

.plans-header p {
    margin: 0;
    color: rgba(255,255,255,.54);
    font-size: 1.02rem;
}

.plans-alert {
    max-width: 850px;
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0 auto 1rem;
    padding: 12px 14px;
    border: 1px solid rgba(239,68,68,.18);
    border-radius: 12px;
    color: #FCA5A5;
    background: rgba(239,68,68,.08);
    font-size: .79rem;
}

.plans-grid {
    max-width: 1030px;
    display: grid;
    grid-template-columns:
        repeat(2,minmax(0,1fr));
    gap: 20px;
    margin: 0 auto;
}

.plan-card {
    position: relative;
    display: flex;
    flex-direction: column;
    padding: 1.5rem;
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 26px;
    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,.075),
            rgba(255,255,255,.035)
        );
    box-shadow:
        0 25px 65px rgba(0,0,0,.28);
    backdrop-filter: blur(18px);
    transition:
        transform .3s ease,
        border-color .3s ease,
        box-shadow .3s ease;
}

.plan-card:hover {
    transform: translateY(-8px);
    border-color: rgba(255,255,255,.2);
    box-shadow:
        0 35px 75px rgba(0,0,0,.38);
}

.plan-card-soutien_lycee {
    border-color: rgba(245,158,11,.26);
    background:
        linear-gradient(
            145deg,
            rgba(245,158,11,.11),
            rgba(255,255,255,.035)
        );
}

.plan-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.plan-badge {
    display: inline-flex;
    padding: 6px 10px;
    border-radius: 999px;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #7C3AED,
            #2563EB
        );
    font-size: .66rem;
    font-weight: 800;
}

.plan-card-soutien_lycee
.plan-card
.plan-badge {
    background:
        linear-gradient(
            135deg,
            #D97706,
            #F59E0B
        );
}

.plan-icon {
    width: 45px;
    height: 45px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    color: #BFDBFE;
    background: rgba(37,99,235,.13);
    font-size: 1.1rem;
}

.plan-card-soutien_lycee
.plan-card
.plan-icon {
    color: #FCD34D;
    background: rgba(245,158,11,.13);
}

.plan-copy {
    margin-top: 1.1rem;
}

.plan-scope {
    margin: 0 0 4px;
    color: #93C5FD;
    font-size: .65rem;
    font-weight: 800;
    letter-spacing: .06em;
    text-transform: uppercase;
}

.plan-card-soutien_lycee
.plan-card
.plan-scope {
    color: #FCD34D;
}

.plan-copy h2 {
    margin: 0 0 5px;
    color: #fff;
    font-size: 1.4rem;
    font-weight: 850;
}

.plan-subtitle {
    margin: 0;
    color: rgba(255,255,255,.46);
    font-size: .75rem;
}

.plan-price {
    display: flex;
    align-items: baseline;
    gap: 5px;
    margin: 1.2rem 0;
}

.plan-amount {
    color: #fff;
    font-size: 3rem;
    font-weight: 900;
    letter-spacing: -.05em;
    line-height: 1;
}

.plan-currency {
    color: rgba(255,255,255,.7);
    font-size: 1.05rem;
    font-weight: 800;
}

.plan-period {
    color: rgba(255,255,255,.34);
    font-size: .76rem;
}

.plan-features {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 250px;
    margin: 0 0 1rem;
    padding: 0;
    list-style: none;
}

.plan-features li {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    color: rgba(255,255,255,.74);
    font-size: .75rem;
    line-height: 1.45;
}

.plan-features li > span {
    width: 21px;
    height: 21px;
    flex: 0 0 21px;
    display: grid;
    place-items: center;
    margin-top: 1px;
    border-radius: 50%;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563EB,
            #7C3AED
        );
    font-size: .58rem;
}

.plan-card-soutien_lycee
.plan-card
.plan-features
li > span {
    background:
        linear-gradient(
            135deg,
            #D97706,
            #F59E0B
        );
}

.plan-restriction {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 1rem;
    padding: 9px 10px;
    border: 1px solid rgba(245,158,11,.15);
    border-radius: 10px;
    color: #FCD34D;
    background: rgba(245,158,11,.07);
    font-size: .65rem;
    line-height: 1.45;
}

.plan-actions {
    display: flex;
    flex-direction: column;
    gap: 9px;
    margin-top: auto;
}

.plan-button {
    min-height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 13px;
    font-size: .76rem;
    font-weight: 800;
    text-decoration: none;
    transition:
        transform .25s ease,
        filter .25s ease;
}

.plan-button:hover {
    transform: translateY(-2px);
    filter: brightness(1.08);
}

.plan-button-primary {
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #0070BA,
            #1546A0
        );
}

.plan-button-primary:hover {
    color: #fff;
}

.plan-button-secondary {
    border: 1px solid rgba(255,255,255,.14);
    color: rgba(255,255,255,.78);
    background: rgba(255,255,255,.055);
}

.plan-button-secondary:hover {
    color: #fff;
}

.plans-note {
    max-width: 800px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    margin: 1.2rem auto 0;
    color: rgba(255,255,255,.37);
    font-size: .68rem;
    text-align: center;
}

html.light-mode .plans-wrapper {
    background:
        radial-gradient(
            circle at 12% 20%,
            rgba(37,99,235,.08),
            transparent 32%
        ),
        radial-gradient(
            circle at 88% 22%,
            rgba(124,58,237,.08),
            transparent 32%
        ),
        linear-gradient(
            135deg,
            #F3F6FC,
            #E8EDF6
        );
}

html.light-mode .plans-header h1,
html.light-mode .plan-copy h2,
html.light-mode .plan-amount {
    color: #172033;
}

html.light-mode .plans-header p,
html.light-mode .plan-subtitle,
html.light-mode .plan-period,
html.light-mode .plans-note {
    color: #64748B;
}

html.light-mode .plans-eyebrow {
    border-color: rgba(37,99,235,.13);
    color: #1D4ED8;
    background: rgba(37,99,235,.06);
}

html.light-mode .plan-card {
    border-color: rgba(15,23,42,.08);
    background: rgba(255,255,255,.94);
    box-shadow:
        0 20px 55px rgba(15,23,42,.08);
}

html.light-mode .plan-card-soutien_lycee {
    border-color: rgba(217,119,6,.2);
    background:
        linear-gradient(
            145deg,
            #FFFDF8,
            #FFFFFF
        );
}

html.light-mode .plan-features li {
    color: #334155;
}

html.light-mode .plan-currency {
    color: #334155;
}

html.light-mode .plan-button-secondary {
    border-color: rgba(15,23,42,.12);
    color: #334155;
    background: rgba(15,23,42,.035);
}

@media (max-width:850px) {
    .plans-grid {
        grid-template-columns: 1fr;
    }

    .plan-features {
        min-height: auto;
    }
}

@media (max-width:575px) {
    .plans-wrapper {
        padding-top: 5.5rem;
    }

    .plan-card {
        padding: 1.15rem;
        border-radius: 20px;
    }

    .plan-amount {
        font-size: 2.55rem;
    }
}
</style>

@endsection
