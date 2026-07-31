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
                    Une formule complète pour toute la plateforme
                    ou une offre réservée au Soutien Lycée.
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
                {{ $showOnlySoutien
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
.plans-page{position:relative;min-height:100vh;overflow:hidden;padding:7rem 0 4rem;background:linear-gradient(135deg,#0f0c29 0%,#302b63 50%,#24243e 100%)}
.plans-glow{position:absolute;border-radius:50%;pointer-events:none;filter:blur(3px)}
.plans-glow-one{width:340px;height:340px;top:-80px;right:-70px;background:radial-gradient(circle,rgba(102,126,234,.28),transparent 68%)}
.plans-glow-two{width:300px;height:300px;bottom:-90px;left:-60px;background:radial-gradient(circle,rgba(245,158,11,.18),transparent 68%)}
.plans-content{position:relative;z-index:2}
.plans-page-single{
    padding-top:5.5rem;
    padding-bottom:2.5rem
}
.plans-page-single .plans-content{
    min-height:calc(100vh - 8rem);
    display:flex;
    align-items:center;
    justify-content:center
}
.plans-grid-single{
    width:100%;
    max-width:520px;
    grid-template-columns:minmax(0,1fr)
}
.plans-grid-single .offer-features{
    min-height:auto
}
.plans-heading{max-width:780px;margin:0 auto 2.4rem;text-align:center}
.plans-label{display:inline-flex;align-items:center;gap:8px;margin-bottom:1rem;padding:8px 14px;border:1px solid rgba(255,255,255,.12);border-radius:999px;color:#dbeafe;background:rgba(255,255,255,.06);font-size:.72rem;font-weight:800;letter-spacing:.08em}
.plans-heading h1{margin-bottom:.7rem;color:#fff;font-size:clamp(2rem,5vw,3.4rem);font-weight:850}
.plans-heading p{margin:0;color:rgba(255,255,255,.55);font-size:1rem}
.plans-alert{max-width:900px;display:flex;align-items:center;gap:9px;margin:0 auto 1rem;padding:12px 14px;border:1px solid rgba(239,68,68,.2);border-radius:12px;color:#fca5a5;background:rgba(239,68,68,.08)}
.plans-grid{max-width:1050px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px;margin:0 auto}
.offer-card{display:flex;flex-direction:column;padding:1.6rem;border:1px solid rgba(255,255,255,.12);border-radius:28px;background:rgba(255,255,255,.065);box-shadow:0 28px 70px rgba(0,0,0,.3);backdrop-filter:blur(20px);transition:.3s ease}
.offer-card:hover{transform:translateY(-8px);border-color:rgba(255,255,255,.25)}
.offer-card-high-school{border-color:rgba(245,158,11,.3);background:linear-gradient(145deg,rgba(245,158,11,.12),rgba(255,255,255,.045))}
.offer-card-top{display:flex;align-items:center;justify-content:space-between;gap:12px}
.offer-badge{padding:6px 11px;border-radius:999px;color:#fff;background:linear-gradient(135deg,#f093fb,#f5576c);font-size:.67rem;font-weight:800}
.offer-card-high-school .offer-badge{background:linear-gradient(135deg,#d97706,#f59e0b)}
.offer-icon{width:47px;height:47px;display:grid;place-items:center;border-radius:15px;color:#c4b5fd;background:rgba(124,58,237,.16);font-size:1.15rem}
.offer-card-high-school .offer-icon{color:#fcd34d;background:rgba(245,158,11,.15)}
.offer-title{margin-top:1.1rem}
.offer-title small{color:#93c5fd;font-size:.64rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase}
.offer-card-high-school .offer-title small{color:#fcd34d}
.offer-title h2{margin:.25rem 0;color:#fff;font-size:1.5rem;font-weight:850}
.offer-title p{margin:0;color:rgba(255,255,255,.48);font-size:.77rem}
.offer-price{display:flex;align-items:baseline;gap:5px;margin:1.2rem 0}
.offer-price strong{color:#fff;font-size:3rem;font-weight:900;letter-spacing:-.05em;line-height:1}
.offer-price span{color:rgba(255,255,255,.72);font-size:1.05rem;font-weight:800}
.offer-price small{color:rgba(255,255,255,.34);font-size:.76rem}
.offer-features{display:flex;flex-direction:column;gap:10px;min-height:245px;margin:0 0 1rem;padding:0;list-style:none}
.offer-features li{display:flex;align-items:flex-start;gap:9px;color:rgba(255,255,255,.76);font-size:.76rem;line-height:1.45}
.offer-features li span{width:22px;height:22px;flex:0 0 22px;display:grid;place-items:center;border-radius:50%;color:#fff;background:linear-gradient(135deg,#667eea,#764ba2);font-size:.56rem}
.offer-card-high-school .offer-features li span{background:linear-gradient(135deg,#d97706,#f59e0b)}
.offer-restriction{display:flex;align-items:flex-start;gap:8px;margin-bottom:1rem;padding:10px;border:1px solid rgba(245,158,11,.18);border-radius:11px;color:#fcd34d;background:rgba(245,158,11,.08);font-size:.66rem;line-height:1.45}
.offer-actions{display:flex;flex-direction:column;gap:9px;margin-top:auto}
.offer-button{min-height:46px;display:flex;align-items:center;justify-content:center;gap:8px;border-radius:14px;color:#fff;font-size:.77rem;font-weight:800;text-decoration:none;transition:.25s ease}
.offer-button:hover{color:#fff;transform:translateY(-2px);filter:brightness(1.08)}
.offer-paypal{background:linear-gradient(135deg,#0070ba,#1546a0)}
.offer-bank{border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.07)}
.plans-security{display:flex;align-items:center;justify-content:center;gap:8px;max-width:850px;margin:1.25rem auto 0;color:rgba(255,255,255,.38);font-size:.68rem;text-align:center}
html.light-mode .plans-page{background:linear-gradient(135deg,#f0f4ff,#e8edf5,#f5f7fa)}
html.light-mode .plans-heading h1,html.light-mode .offer-title h2,html.light-mode .offer-price strong{color:#172033}
html.light-mode .plans-heading p,html.light-mode .offer-title p,html.light-mode .offer-price small,html.light-mode .plans-security{color:#64748b}
html.light-mode .offer-card{border-color:rgba(15,23,42,.09);background:rgba(255,255,255,.95);box-shadow:0 25px 60px rgba(15,23,42,.09)}
html.light-mode .offer-card-high-school{border-color:rgba(217,119,6,.22);background:linear-gradient(145deg,#fffaf0,#fff)}
html.light-mode .offer-features li,html.light-mode .offer-price span{color:#334155}
html.light-mode .offer-bank{border-color:rgba(15,23,42,.12);color:#334155;background:rgba(15,23,42,.04)}
@media(max-width:850px){.plans-grid{grid-template-columns:1fr}.offer-features{min-height:auto}}
@media(max-width:575px){.plans-page{padding-top:5.7rem}.offer-card{padding:1.2rem;border-radius:21px}.offer-price strong{font-size:2.55rem}}
</style>

@endsection
