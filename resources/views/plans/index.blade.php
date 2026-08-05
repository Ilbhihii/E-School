@extends('layouts.front')

@section(
    'title',
    $showOnlySoutien
        ? 'Offre Soutien Lycée'
        : 'Nos offres'
)

@section('content')

<div
    class="plans-page
        {{ $showOnlySoutien
            ? 'plans-page-single'
            : ''
        }}"
>
    <div class="plans-glow plans-glow-one"></div>
    <div class="plans-glow plans-glow-two"></div>

    <div class="container plans-content">
        @unless($showOnlySoutien)
            <header class="plans-heading">
                <span class="plans-label">
                    <i class="bi bi-stars"></i>
                    ABONNEMENTS SMART SCHOOL
                </span>

                <h1>Choisissez votre formule</h1>

                <p>
                    Une formule complète pour accéder
                    à toute la plateforme Smart School Academy.
                </p>
            </header>
        @endunless

        @if(session('error'))
            <div class="plans-alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
            </div>
        @endif

        <div
            class="plans-grid
                {{ count($plans) === 1
                    ? 'plans-grid-single'
                    : ''
                }}"
        >
            @foreach($plans as $planCode => $plan)
                <article class="offer-card {{ $planCode === 'soutien_lycee' ? 'offer-card-high-school' : '' }}">
                    <div class="offer-card-top">
                        <span class="offer-badge">
                            {{ $plan['badge'] }}
                        </span>

                        <span class="offer-icon">
                            <i class="bi {{ $plan['icon'] }}"></i>
                        </span>
                    </div>

                    <div class="offer-title">
                        <small>{{ $plan['scope'] }}</small>
                        <h2>{{ $plan['name'] }}</h2>
                        <p>{{ $plan['subtitle'] }}</p>
                    </div>

                    <div class="offer-price">
                        <strong>{{ $plan['amount_display'] }}</strong>
                        <span>{{ $plan['currency_symbol'] }}</span>
                        <small>/ {{ $plan['period'] }}</small>
                    </div>

                    <ul class="offer-features">
                        @foreach($plan['features'] as $feature)
                            <li>
                                <span><i class="bi bi-check-lg"></i></span>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    @if(
                        (
                            $plan[
                                'restricted_to_high_school'
                            ] ?? false
                        )
                        || $planCode === 'soutien_lycee'
                    )
                        <div class="offer-restriction">
                            <i class="bi bi-shield-lock-fill"></i>
                            Cette offre ne donne pas accès
                            aux parcours Arabe et Coran.
                        </div>
                    @endif

                    <div class="offer-actions">
                        <a
                            href="{{ route('student.payment', ['plan' => $planCode, 'method' => 'paypal']) }}"
                            class="offer-button offer-paypal"
                        >
                            <i class="bi bi-paypal"></i>
                            Payer avec PayPal
                        </a>

                        <a
                            href="{{ route('student.payment', ['plan' => $planCode, 'method' => 'bank']) }}"
                            class="offer-button offer-bank"
                        >
                            <i class="bi bi-bank"></i>
                            Virement bancaire
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        @unless($showOnlySoutien)
            <div class="plans-security">
                <i class="bi bi-shield-check"></i>
                Le choix de l’offre ne rend pas le compte payant.
                L’accès est activé seulement après confirmation du paiement.
            </div>
        @endunless
    </div>
</div>

<style>
/*
|--------------------------------------------------------------------------
| SMART SCHOOL ACADEMY — OFFRES V13
|--------------------------------------------------------------------------
| Amélioration visuelle uniquement : aucun texte, lien, formulaire,
| contenu Blade ou fonctionnement de paiement n'est modifié.
*/

.plans-page {
    --plans-bg: #07101d;
    --plans-panel: rgba(15, 27, 47, 0.94);
    --plans-panel-soft: rgba(20, 35, 58, 0.78);
    --plans-border: rgba(148, 163, 184, 0.14);
    --plans-border-strong: rgba(148, 163, 184, 0.24);
    --plans-text: #f8fafc;
    --plans-soft: #d2dbea;
    --plans-muted: #8492a8;
    --plans-blue: #4f72f5;
    --plans-violet: #7554e8;
    --plans-cyan: #23a8ca;
    --plans-green: #24b786;
    --plans-amber: #e1a53a;
    --plans-red: #e05b68;

    position: relative;
    min-height: 100vh;
    overflow: hidden;
    padding: clamp(7.6rem, 10vw, 9.4rem) 0 5.5rem;
    color: var(--plans-text);
    background:
        radial-gradient(
            circle at 12% 17%,
            rgba(79, 114, 245, 0.12),
            transparent 29%
        ),
        radial-gradient(
            circle at 87% 22%,
            rgba(117, 84, 232, 0.10),
            transparent 27%
        ),
        linear-gradient(180deg, #091322 0%, var(--plans-bg) 100%);
    isolation: isolate;
}

.plans-page::before {
    position: absolute;
    inset: 0;
    z-index: -2;
    content: "";
    pointer-events: none;
    opacity: 0.28;
    background-image:
        linear-gradient(
            rgba(148, 163, 184, 0.035) 1px,
            transparent 1px
        ),
        linear-gradient(
            90deg,
            rgba(148, 163, 184, 0.035) 1px,
            transparent 1px
        );
    background-size: 58px 58px;
    mask-image: linear-gradient(to bottom, #000, transparent 82%);
    -webkit-mask-image: linear-gradient(to bottom, #000, transparent 82%);
}

.plans-page::after {
    position: absolute;
    z-index: -1;
    top: 5.5rem;
    left: 50%;
    width: min(1050px, 86vw);
    height: 330px;
    content: "";
    pointer-events: none;
    border-radius: 50%;
    background: rgba(79, 114, 245, 0.075);
    filter: blur(95px);
    transform: translateX(-50%);
}

.plans-glow {
    position: absolute;
    z-index: -1;
    border-radius: 50%;
    pointer-events: none;
    filter: blur(5px);
}

.plans-glow-one {
    top: -115px;
    right: -105px;
    width: 370px;
    height: 370px;
    background: radial-gradient(
        circle,
        rgba(79, 114, 245, 0.17),
        transparent 69%
    );
}

.plans-glow-two {
    bottom: -130px;
    left: -105px;
    width: 350px;
    height: 350px;
    background: radial-gradient(
        circle,
        rgba(225, 165, 58, 0.10),
        transparent 69%
    );
}

.plans-content {
    position: relative;
    z-index: 2;
}

.plans-heading {
    max-width: 760px;
    margin: 0 auto 2.75rem;
    text-align: center;
}

.plans-label {
    display: inline-flex;
    min-height: 34px;
    align-items: center;
    gap: 8px;
    padding: 0 13px;
    margin-bottom: 1rem;
    color: #9eb2ff;
    border: 1px solid rgba(79, 114, 245, 0.22);
    border-radius: 999px;
    background: rgba(79, 114, 245, 0.075);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.025);
}

.plans-label i {
    color: #e3b04c;
}

.plans-heading h1 {
    margin: 0 0 0.75rem;
    color: var(--plans-text);
    font-family: "Poppins", sans-serif;
    font-size: clamp(2.15rem, 5vw, 3.65rem);
    font-weight: 800;
    line-height: 1.08;
    letter-spacing: -0.05em;
    text-wrap: balance;
}

.plans-heading p {
    max-width: 680px;
    margin: 0 auto;
    color: var(--plans-muted);
    font-size: 0.92rem;
    line-height: 1.75;
    text-wrap: balance;
}

.plans-alert {
    display: flex;
    max-width: 900px;
    align-items: center;
    gap: 9px;
    padding: 12px 14px;
    margin: 0 auto 1rem;
    color: #f6a5ad;
    border: 1px solid rgba(224, 91, 104, 0.2);
    border-radius: 13px;
    background: rgba(224, 91, 104, 0.075);
    font-size: 0.74rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
}

.plans-grid {
    display: grid;
    max-width: 1080px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-items: stretch;
    gap: 20px;
    margin: 0 auto;
}

.offer-card {
    --offer-accent: linear-gradient(90deg, #4f72f5, #7554e8);
    --offer-accent-solid: #5e69ed;
    --offer-icon-color: #aebcff;
    --offer-icon-bg: rgba(79, 114, 245, 0.12);
    --offer-badge-bg: linear-gradient(135deg, #536ff0, #7554e8);
    --offer-border-hover: rgba(98, 119, 245, 0.42);
    --offer-glow: rgba(79, 114, 245, 0.11);

    position: relative;
    display: flex;
    min-width: 0;
    overflow: hidden;
    flex-direction: column;
    padding: clamp(1.35rem, 2.4vw, 1.8rem);
    border: 1px solid var(--plans-border);
    border-radius: 22px;
    background:
        radial-gradient(
            circle at 90% 0%,
            var(--offer-glow),
            transparent 31%
        ),
        linear-gradient(
            145deg,
            rgba(18, 32, 54, 0.97),
            rgba(11, 21, 37, 0.97)
        );
    box-shadow:
        0 22px 55px rgba(0, 0, 0, 0.22),
        inset 0 1px 0 rgba(255, 255, 255, 0.026);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    transition:
        transform 220ms ease,
        border-color 220ms ease,
        box-shadow 220ms ease;
}

.offer-card::before {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    height: 3px;
    content: "";
    background: var(--offer-accent);
}

.offer-card::after {
    position: absolute;
    top: -88px;
    right: -82px;
    width: 195px;
    height: 195px;
    content: "";
    pointer-events: none;
    border: 28px solid var(--offer-glow);
    border-radius: 50%;
}

.offer-card:hover {
    border-color: var(--offer-border-hover);
    box-shadow:
        0 30px 68px rgba(0, 0, 0, 0.30),
        0 0 0 1px rgba(79, 114, 245, 0.045);
    transform: translateY(-6px);
}

.offer-card-high-school {
    --offer-accent: linear-gradient(90deg, #d9982c, #f0bd54);
    --offer-accent-solid: #dca13a;
    --offer-icon-color: #f2c66f;
    --offer-icon-bg: rgba(225, 165, 58, 0.12);
    --offer-badge-bg: linear-gradient(135deg, #c98120, #e7ab3d);
    --offer-border-hover: rgba(225, 165, 58, 0.42);
    --offer-glow: rgba(225, 165, 58, 0.09);
}

.offer-card-top {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.offer-badge {
    display: inline-flex;
    min-height: 29px;
    align-items: center;
    justify-content: center;
    padding: 0 10px;
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 999px;
    background: var(--offer-badge-bg);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.025em;
}

.offer-icon {
    display: grid;
    width: 48px;
    height: 48px;
    flex: 0 0 48px;
    place-items: center;
    color: var(--offer-icon-color);
    border: 1px solid rgba(255, 255, 255, 0.055);
    border-radius: 14px;
    background: var(--offer-icon-bg);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.035);
    font-size: 1.1rem;
}

.offer-title {
    position: relative;
    z-index: 2;
    padding-bottom: 1rem;
    margin-top: 1.2rem;
    border-bottom: 1px solid var(--plans-border);
}

.offer-title small {
    display: block;
    margin-bottom: 0.28rem;
    color: var(--offer-icon-color);
    font-size: 0.61rem;
    font-weight: 800;
    letter-spacing: 0.09em;
    text-transform: uppercase;
}

.offer-title h2 {
    margin: 0 0 0.3rem;
    color: var(--plans-text);
    font-family: "Poppins", sans-serif;
    font-size: 1.38rem;
    font-weight: 780;
    letter-spacing: -0.025em;
}

.offer-title p {
    min-height: 2.5em;
    margin: 0;
    color: var(--plans-muted);
    font-size: 0.73rem;
    line-height: 1.55;
}

.offer-price {
    position: relative;
    z-index: 2;
    display: flex;
    min-height: 92px;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 5px;
    padding: 1.15rem 0 1rem;
}

.offer-price strong {
    color: var(--plans-text);
    font-family: "Poppins", sans-serif;
    font-size: clamp(2.65rem, 5vw, 3.35rem);
    font-weight: 820;
    line-height: 0.95;
    letter-spacing: -0.065em;
}

.offer-price span {
    padding-bottom: 0.18rem;
    color: var(--plans-soft);
    font-size: 1rem;
    font-weight: 800;
}

.offer-price small {
    padding-bottom: 0.25rem;
    color: #69768b;
    font-size: 0.68rem;
    font-weight: 650;
}

.offer-features {
    position: relative;
    z-index: 2;
    display: flex;
    min-height: 250px;
    flex-direction: column;
    gap: 9px;
    padding: 0;
    margin: 0 0 1rem;
    list-style: none;
}

.offer-features li {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    color: #b8c4d5;
    font-size: 0.72rem;
    line-height: 1.48;
}

.offer-features li span {
    display: grid;
    width: 21px;
    height: 21px;
    flex: 0 0 21px;
    place-items: center;
    margin-top: 1px;
    color: #ffffff;
    border-radius: 7px;
    background: var(--offer-accent);
    box-shadow: 0 6px 14px var(--offer-glow);
    font-size: 0.53rem;
}

.offer-restriction {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 11px;
    margin-bottom: 1rem;
    color: #e8bd69;
    border: 1px solid rgba(225, 165, 58, 0.17);
    border-radius: 11px;
    background: rgba(225, 165, 58, 0.065);
    font-size: 0.63rem;
    line-height: 1.48;
}

.offer-restriction i {
    flex: 0 0 auto;
    margin-top: 1px;
}

.offer-actions {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 9px;
    padding-top: 1rem;
    margin-top: auto;
    border-top: 1px solid var(--plans-border);
}

.offer-button,
.offer-button:link,
.offer-button:visited,
.offer-button:hover,
.offer-button:focus,
.offer-button:active {
    text-decoration: none !important;
}

.offer-button {
    display: inline-flex;
    min-height: 46px;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 0 12px;
    color: #ffffff;
    border: 1px solid transparent;
    border-radius: 11px;
    font-size: 0.69rem;
    font-weight: 760;
    text-align: center;
    transition:
        color 180ms ease,
        border-color 180ms ease,
        background 180ms ease,
        box-shadow 180ms ease,
        transform 180ms ease;
}

.offer-button:hover {
    color: #ffffff;
    transform: translateY(-2px);
}

.offer-button:focus-visible {
    outline: 3px solid rgba(132, 160, 255, 0.30);
    outline-offset: 3px;
}

.offer-paypal {
    background: linear-gradient(135deg, #0069b7, #1744a2);
    box-shadow: 0 10px 22px rgba(0, 82, 160, 0.18);
}

.offer-paypal:hover {
    box-shadow: 0 14px 27px rgba(0, 82, 160, 0.26);
}

.offer-bank {
    color: var(--plans-soft);
    border-color: var(--plans-border-strong);
    background: rgba(148, 163, 184, 0.055);
}

.offer-bank:hover {
    color: #ffffff;
    border-color: var(--offer-border-hover);
    background: var(--offer-icon-bg);
}

.plans-security {
    display: flex;
    max-width: 880px;
    min-height: 52px;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 15px;
    margin: 1.3rem auto 0;
    color: #748198;
    border: 1px solid var(--plans-border);
    border-radius: 13px;
    background: rgba(15, 27, 47, 0.55);
    font-size: 0.65rem;
    line-height: 1.45;
    text-align: center;
}

.plans-security i {
    color: var(--plans-green);
    font-size: 0.9rem;
}

/* Offre unique */
.plans-page-single {
    padding-top: clamp(7rem, 9vw, 8.4rem);
    padding-bottom: 3.5rem;
}

.plans-page-single .plans-content {
    display: flex;
    min-height: calc(100vh - 11rem);
    align-items: center;
    justify-content: center;
}

.plans-grid-single {
    width: 100%;
    max-width: 540px;
    grid-template-columns: minmax(0, 1fr);
}

.plans-grid-single .offer-features {
    min-height: auto;
}

/* Mode clair conservé sans changer le fonctionnement existant */
html.light-mode .plans-page {
    --plans-bg: #eef3fb;
    --plans-panel: rgba(255, 255, 255, 0.96);
    --plans-panel-soft: rgba(248, 250, 252, 0.9);
    --plans-border: rgba(15, 23, 42, 0.09);
    --plans-border-strong: rgba(15, 23, 42, 0.14);
    --plans-text: #172033;
    --plans-soft: #334155;
    --plans-muted: #64748b;

    background:
        radial-gradient(
            circle at 12% 17%,
            rgba(79, 114, 245, 0.09),
            transparent 29%
        ),
        radial-gradient(
            circle at 87% 22%,
            rgba(117, 84, 232, 0.07),
            transparent 27%
        ),
        linear-gradient(180deg, #f7f9fd 0%, #edf2f9 100%);
}

html.light-mode .plans-page::before {
    opacity: 0.35;
}

html.light-mode .offer-card {
    background:
        radial-gradient(
            circle at 90% 0%,
            var(--offer-glow),
            transparent 31%
        ),
        rgba(255, 255, 255, 0.96);
    box-shadow:
        0 20px 50px rgba(15, 23, 42, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

html.light-mode .offer-title p,
html.light-mode .offer-features li {
    color: #526176;
}

html.light-mode .offer-price small,
html.light-mode .plans-security {
    color: #64748b;
}

html.light-mode .offer-bank {
    color: #334155;
    background: rgba(15, 23, 42, 0.035);
}

html.light-mode .plans-security {
    background: rgba(255, 255, 255, 0.7);
}

/* Responsive */
@media (max-width: 900px) {
    .plans-grid {
        max-width: 620px;
        grid-template-columns: 1fr;
    }

    .offer-features {
        min-height: auto;
    }
}

@media (max-width: 575px) {
    .plans-page {
        padding: 6.7rem 0 3.5rem;
    }

    .plans-heading {
        margin-bottom: 1.8rem;
    }

    .plans-heading h1 {
        font-size: 2.25rem;
    }

    .plans-heading p {
        font-size: 0.8rem;
    }

    .offer-card {
        padding: 1.2rem;
        border-radius: 18px;
    }

    .offer-card::after {
        width: 155px;
        height: 155px;
    }

    .offer-title h2 {
        font-size: 1.22rem;
    }

    .offer-price {
        min-height: 82px;
    }

    .offer-price strong {
        font-size: 2.55rem;
    }

    .offer-actions {
        grid-template-columns: 1fr;
    }

    .plans-security {
        align-items: flex-start;
        text-align: left;
    }
}

@media (prefers-reduced-motion: reduce) {
    .offer-card,
    .offer-button {
        transition: none !important;
    }
}

/* =========================================================
   PAGE /PLANS — VERSION PLUS COMPACTE
   ========================================================= */

.plans-page {
    min-height: calc(100vh - 70px);
    padding: 5.6rem 0 2.8rem;
}

.plans-page::after {
    top: 4.5rem;
    height: 230px;
}

.plans-glow-one {
    width: 280px;
    height: 280px;
}

.plans-glow-two {
    width: 260px;
    height: 260px;
}

.plans-heading {
    max-width: 650px;
    margin-bottom: 1.65rem;
}

.plans-label {
    min-height: 28px;
    gap: 6px;
    padding: 0 10px;
    margin-bottom: 0.65rem;
    font-size: 0.59rem;
}

.plans-heading h1 {
    margin-bottom: 0.5rem;
    font-size: clamp(1.8rem, 3.6vw, 2.85rem);
    line-height: 1.06;
}

.plans-heading p {
    max-width: 570px;
    font-size: 0.82rem;
    line-height: 1.55;
}

.plans-alert {
    padding: 9px 11px;
    margin-bottom: 0.8rem;
    font-size: 0.68rem;
}

.plans-grid {
    max-width: 950px;
    gap: 14px;
}

.offer-card {
    padding: 1.1rem;
    border-radius: 18px;
}

.offer-card::after {
    top: -72px;
    right: -68px;
    width: 155px;
    height: 155px;
    border-width: 22px;
}

.offer-card:hover {
    transform: translateY(-4px);
}

.offer-card-top {
    gap: 9px;
}

.offer-badge {
    min-height: 25px;
    padding: 0 8px;
    font-size: 0.56rem;
}

.offer-icon {
    width: 40px;
    height: 40px;
    flex-basis: 40px;
    border-radius: 11px;
    font-size: 0.95rem;
}

.offer-title {
    padding-bottom: 0.7rem;
    margin-top: 0.8rem;
}

.offer-title small {
    margin-bottom: 0.2rem;
    font-size: 0.55rem;
}

.offer-title h2 {
    margin-bottom: 0.2rem;
    font-size: 1.15rem;
}

.offer-title p {
    min-height: 2.2em;
    font-size: 0.66rem;
    line-height: 1.45;
}

.offer-price {
    min-height: 70px;
    gap: 4px;
    padding: 0.8rem 0 0.7rem;
}

.offer-price strong {
    font-size: clamp(2.15rem, 4vw, 2.7rem);
}

.offer-price span {
    font-size: 0.88rem;
}

.offer-price small {
    font-size: 0.61rem;
}

.offer-features {
    min-height: 198px;
    gap: 7px;
    margin-bottom: 0.75rem;
}

.offer-features li {
    gap: 7px;
    font-size: 0.65rem;
    line-height: 1.4;
}

.offer-features li span {
    width: 18px;
    height: 18px;
    flex-basis: 18px;
    border-radius: 6px;
    font-size: 0.46rem;
}

.offer-restriction {
    gap: 6px;
    padding: 8px 9px;
    margin-bottom: 0.75rem;
    font-size: 0.57rem;
    line-height: 1.4;
}

.offer-actions {
    gap: 7px;
    padding-top: 0.75rem;
}

.offer-button {
    min-height: 40px;
    gap: 6px;
    padding: 0 9px;
    border-radius: 9px;
    font-size: 0.62rem;
}

.plans-security {
    max-width: 760px;
    min-height: 44px;
    gap: 6px;
    padding: 8px 12px;
    margin-top: 0.9rem;
    font-size: 0.59rem;
}

.plans-page-single {
    padding-top: 5.4rem;
    padding-bottom: 2rem;
}

.plans-page-single .plans-content {
    min-height: calc(100vh - 8rem);
}

.plans-grid-single {
    max-width: 460px;
}

@media (max-width: 900px) {
    .plans-grid {
        max-width: 540px;
    }

    .offer-features {
        min-height: auto;
    }
}

@media (max-width: 575px) {
    .plans-page {
        padding: 5.2rem 0 2rem;
    }

    .plans-heading {
        margin-bottom: 1.25rem;
    }

    .plans-heading h1 {
        font-size: 1.9rem;
    }

    .plans-heading p {
        font-size: 0.74rem;
    }

    .offer-card {
        padding: 0.95rem;
        border-radius: 16px;
    }

    .offer-title h2 {
        font-size: 1.05rem;
    }

    .offer-price {
        min-height: 64px;
    }

    .offer-price strong {
        font-size: 2.15rem;
    }

    .offer-actions {
        grid-template-columns: 1fr;
    }

    .offer-button {
        min-height: 39px;
    }

    .plans-security {
        font-size: 0.56rem;
    }
}

</style>

@endsection