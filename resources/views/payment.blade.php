@extends('layouts.front')

@section('title', 'Paiement — ' . $selectedPlan['name'])

@section('content')

<div class="payment-wrapper">
    <div class="payment-decoration"></div>

    <div class="container payment-container">
        <div class="payment-card">
            <a
                href="{{ route('plans') }}"
                class="payment-back"
            >
                <i class="bi bi-arrow-left"></i>
                Retour aux offres
            </a>

            <div class="payment-icon">
                <i
                    class="bi {{
                        $selectedPlan['icon']
                    }}"
                ></i>
            </div>

            <span class="payment-scope">
                {{ $selectedPlan['scope'] }}
            </span>

            <h1>{{ $selectedPlan['name'] }}</h1>

            <p class="payment-subtitle">
                {{ $selectedPlan['subtitle'] }}
            </p>

            @if(session('error'))
                <div class="payment-alert error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="payment-alert success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="payment-price">
                <strong>
                    {{ $selectedPlan['amount_display'] }}
                </strong>

                <span>
                    {{ $selectedPlan['currency_symbol'] }}
                </span>

                <small>
                    / {{ $selectedPlan['period'] }}
                </small>
            </div>

            <div class="payment-features">
                @foreach(
                    $selectedPlan['features']
                    as $feature
                )
                    <div>
                        <span>
                            <i class="bi bi-check-lg"></i>
                        </span>

                        {{ $feature }}
                    </div>
                @endforeach
            </div>

            @if(
                $planCode
                === 'soutien_lycee'
            )
                <div class="payment-restriction">
                    <i class="bi bi-shield-lock-fill"></i>

                    Cette formule permet uniquement
                    l’accès au parcours Soutien Lycée :
                    Mathématiques BAC et
                    Physique-Chimie BAC.
                </div>
            @endif

            <div class="payment-reference">
                <span>Référence à communiquer</span>

                <strong>
                    SSA-{{
                        strtoupper(
                            str_replace(
                                '_',
                                '-',
                                $planCode
                            )
                        )
                    }}-{{
                        auth()->check()
                            ? auth()->id()
                            : 'COMPTE'
                    }}
                </strong>
            </div>

            <div class="payment-methods">
                @if(request('method') === 'paypal')
                    <section class="payment-method-box">
                        <header>
                            <i class="bi bi-paypal"></i>

                            <div>
                                <strong>Paiement PayPal</strong>
                                <span>
                                    {{
                                        $selectedPlan[
                                            'amount_display'
                                        ]
                                    }}
                                    {{
                                        $selectedPlan[
                                            'currency_symbol'
                                        ]
                                    }}
                                </span>
                            </div>
                        </header>

                        <p>
                            Après le paiement, envoyez la
                            confirmation et la référence affichée
                            ci-dessus à l’administration.
                        </p>

                        <a
                            href="https://www.paypal.me/abdelghanimaloulou1"
                            target="_blank"
                            rel="noopener"
                            class="payment-button paypal"
                        >
                            <i class="bi bi-paypal"></i>
                            Continuer sur PayPal
                        </a>
                    </section>

                    <a
                        href="{{
                            route(
                                'student.payment',
                                ['plan' => $planCode]
                            )
                        }}"
                        class="payment-change"
                    >
                        <i class="bi bi-arrow-left"></i>
                        Changer la méthode
                    </a>
                @elseif(
                    request('method')
                    === 'bank'
                )
                    <section class="payment-method-box">
                        <header>
                            <i class="bi bi-bank"></i>

                            <div>
                                <strong>Virement bancaire</strong>
                                <span>
                                    {{
                                        $selectedPlan[
                                            'amount_display'
                                        ]
                                    }}
                                    {{
                                        $selectedPlan[
                                            'currency_symbol'
                                        ]
                                    }}
                                </span>
                            </div>
                        </header>

                        <div class="bank-account">
                            <span>
                                Maroc — Banque Populaire
                            </span>

                            <p>
                                <small>RIB</small>
                                123456789012345678901234
                            </p>

                            <p>
                                <small>Titulaire</small>
                                M. Abdelghani Maloulou
                            </p>
                        </div>

                        <div class="bank-account">
                            <span>
                                France — EUROCOMPTE SÉRÉNITÉ
                            </span>

                            <p>
                                <small>IBAN</small>
                                FR76 1027 8089 7600 0210 7440 103
                            </p>

                            <p>
                                <small>BIC</small>
                                CMCIFR2A
                            </p>
                        </div>

                        <p>
                            Indiquez la référence du paiement
                            dans le motif du virement.
                        </p>
                    </section>

                    <a
                        href="{{
                            route(
                                'student.payment',
                                ['plan' => $planCode]
                            )
                        }}"
                        class="payment-change"
                    >
                        <i class="bi bi-arrow-left"></i>
                        Changer la méthode
                    </a>
                @else
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
                        class="payment-button paypal"
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
                        class="payment-button bank"
                    >
                        <i class="bi bi-bank"></i>
                        Virement bancaire
                    </a>
                @endif
            </div>

            <p class="payment-security">
                <i class="bi bi-shield-check"></i>

                Le choix du plan est enregistré,
                mais l’accès reste bloqué jusqu’à
                confirmation du paiement.
            </p>
        </div>
    </div>
</div>

<style>
.payment-wrapper {
    position: relative;
    min-height: 100vh;
    overflow: hidden;
    padding: 6.5rem 0 4rem;
    background:
        radial-gradient(
            circle at 15% 20%,
            rgba(37,99,235,.17),
            transparent 32%
        ),
        radial-gradient(
            circle at 87% 75%,
            rgba(124,58,237,.17),
            transparent 34%
        ),
        linear-gradient(
            135deg,
            #07101F,
            #15102D
        );
}

.payment-decoration {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.payment-decoration::before,
.payment-decoration::after {
    position: absolute;
    content: '';
    border-radius: 50%;
    filter: blur(2px);
}

.payment-decoration::before {
    top: 6%;
    right: 8%;
    width: 260px;
    height: 260px;
    background:
        radial-gradient(
            circle,
            rgba(96,165,250,.2),
            transparent 68%
        );
}

.payment-decoration::after {
    bottom: 4%;
    left: 7%;
    width: 280px;
    height: 280px;
    background:
        radial-gradient(
            circle,
            rgba(245,158,11,.12),
            transparent 68%
        );
}

.payment-container {
    position: relative;
    z-index: 2;
}

.payment-card {
    max-width: 620px;
    margin: 0 auto;
    padding: 1.7rem;
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 27px;
    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,.075),
            rgba(255,255,255,.035)
        );
    box-shadow:
        0 30px 75px rgba(0,0,0,.35);
    backdrop-filter: blur(20px);
    text-align: center;
}

.payment-back {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    float: left;
    color: rgba(255,255,255,.4);
    font-size: .68rem;
    text-decoration: none;
}

.payment-back:hover {
    color: #fff;
}

.payment-icon {
    width: 67px;
    height: 67px;
    display: grid;
    place-items: center;
    clear: both;
    margin: 2rem auto 1rem;
    border-radius: 20px;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563EB,
            #7C3AED
        );
    box-shadow:
        0 14px 35px rgba(37,99,235,.24);
    font-size: 1.55rem;
}

.payment-scope {
    display: inline-flex;
    padding: 5px 9px;
    border-radius: 999px;
    color: #BFDBFE;
    background: rgba(37,99,235,.12);
    font-size: .62rem;
    font-weight: 800;
    letter-spacing: .06em;
    text-transform: uppercase;
}

.payment-card h1 {
    margin: .65rem 0 .35rem;
    color: #fff;
    font-size: 1.7rem;
    font-weight: 850;
}

.payment-subtitle {
    margin: 0;
    color: rgba(255,255,255,.45);
    font-size: .78rem;
}

.payment-alert {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 1rem;
    padding: 10px 12px;
    border-radius: 11px;
    font-size: .7rem;
    text-align: left;
}

.payment-alert.error {
    border: 1px solid rgba(239,68,68,.16);
    color: #FCA5A5;
    background: rgba(239,68,68,.08);
}

.payment-alert.success {
    border: 1px solid rgba(34,197,94,.16);
    color: #86EFAC;
    background: rgba(34,197,94,.08);
}

.payment-price {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 5px;
    margin: 1.2rem 0;
}

.payment-price strong {
    color: #fff;
    font-size: 3.2rem;
    font-weight: 900;
    letter-spacing: -.05em;
    line-height: 1;
}

.payment-price span {
    color: rgba(255,255,255,.74);
    font-size: 1.05rem;
    font-weight: 800;
}

.payment-price small {
    color: rgba(255,255,255,.32);
    font-size: .72rem;
}

.payment-features {
    display: grid;
    grid-template-columns:
        repeat(2,minmax(0,1fr));
    gap: 8px;
    margin-bottom: 1rem;
    text-align: left;
}

.payment-features > div {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    color: rgba(255,255,255,.68);
    font-size: .68rem;
    line-height: 1.45;
}

.payment-features span {
    width: 19px;
    height: 19px;
    flex: 0 0 19px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563EB,
            #7C3AED
        );
    font-size: .52rem;
}

.payment-restriction {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: .85rem;
    padding: 9px 10px;
    border: 1px solid rgba(245,158,11,.15);
    border-radius: 10px;
    color: #FCD34D;
    background: rgba(245,158,11,.07);
    font-size: .65rem;
    line-height: 1.45;
    text-align: left;
}

.payment-reference {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: .9rem;
    padding: 10px 12px;
    border: 1px dashed rgba(96,165,250,.22);
    border-radius: 11px;
    background: rgba(37,99,235,.055);
    text-align: left;
}

.payment-reference span {
    color: rgba(255,255,255,.38);
    font-size: .61rem;
}

.payment-reference strong {
    color: #BFDBFE;
    font-size: .67rem;
    word-break: break-word;
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 9px;
}

.payment-button {
    min-height: 47px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 13px;
    color: #fff;
    font-size: .76rem;
    font-weight: 800;
    text-decoration: none;
    transition:
        transform .25s ease,
        filter .25s ease;
}

.payment-button:hover {
    color: #fff;
    transform: translateY(-2px);
    filter: brightness(1.08);
}

.payment-button.paypal {
    background:
        linear-gradient(
            135deg,
            #0070BA,
            #1546A0
        );
}

.payment-button.bank {
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.06);
}

.payment-method-box {
    padding: 1rem;
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 14px;
    background: rgba(0,0,0,.14);
    text-align: left;
}

.payment-method-box header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: .8rem;
}

.payment-method-box header > i {
    color: #60A5FA;
    font-size: 1.3rem;
}

.payment-method-box header > div {
    display: flex;
    flex-direction: column;
}

.payment-method-box header strong {
    color: rgba(255,255,255,.85);
    font-size: .75rem;
}

.payment-method-box header span {
    color: rgba(255,255,255,.36);
    font-size: .62rem;
}

.payment-method-box > p {
    margin: 0 0 .8rem;
    color: rgba(255,255,255,.43);
    font-size: .66rem;
    line-height: 1.5;
}

.bank-account {
    margin-bottom: 8px;
    padding: 10px;
    border-radius: 10px;
    background: rgba(255,255,255,.045);
}

.bank-account > span {
    display: block;
    margin-bottom: 6px;
    color: rgba(255,255,255,.42);
    font-size: .59rem;
    font-weight: 800;
    text-transform: uppercase;
}

.bank-account p {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin: 3px 0;
    color: rgba(255,255,255,.7);
    font-size: .65rem;
}

.bank-account small {
    color: rgba(255,255,255,.32);
}

.payment-change {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    color: rgba(255,255,255,.4);
    font-size: .66rem;
    text-decoration: none;
}

.payment-change:hover {
    color: #fff;
}

.payment-security {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    margin: 1rem 0 0;
    color: rgba(255,255,255,.3);
    font-size: .61rem;
    line-height: 1.45;
}

html.light-mode .payment-wrapper {
    background:
        radial-gradient(
            circle at 15% 20%,
            rgba(37,99,235,.08),
            transparent 32%
        ),
        linear-gradient(
            135deg,
            #F3F6FC,
            #E8EDF6
        );
}

html.light-mode .payment-card {
    border-color: rgba(15,23,42,.08);
    background: rgba(255,255,255,.95);
    box-shadow:
        0 25px 60px rgba(15,23,42,.1);
}

html.light-mode .payment-card h1,
html.light-mode .payment-price strong {
    color: #172033;
}

html.light-mode .payment-subtitle,
html.light-mode .payment-price small,
html.light-mode .payment-security,
html.light-mode .payment-back {
    color: #64748B;
}

html.light-mode .payment-price span,
html.light-mode .payment-features > div {
    color: #334155;
}

html.light-mode .payment-method-box {
    border-color: rgba(15,23,42,.08);
    background: rgba(15,23,42,.025);
}

html.light-mode .payment-method-box header strong,
html.light-mode .bank-account p {
    color: #334155;
}

html.light-mode .payment-method-box > p,
html.light-mode .payment-method-box header span,
html.light-mode .bank-account > span,
html.light-mode .bank-account small {
    color: #64748B;
}

html.light-mode .bank-account {
    background: rgba(15,23,42,.035);
}

html.light-mode .payment-button.bank {
    border-color: rgba(15,23,42,.12);
    color: #334155;
    background: rgba(15,23,42,.035);
}

html.light-mode .payment-reference {
    background: rgba(37,99,235,.045);
}

html.light-mode .payment-reference span {
    color: #64748B;
}

@media (max-width:620px) {
    .payment-wrapper {
        padding-top: 5.4rem;
    }

    .payment-card {
        padding: 1.15rem;
        border-radius: 20px;
    }

    .payment-features {
        grid-template-columns: 1fr;
    }

    .payment-reference {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>

@endsection
