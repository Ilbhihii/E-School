@extends('layouts.front')

@section('title', 'Plans Premium')

@section('content')

<div class="plans-wrapper min-vh-100 position-relative d-flex align-items-center overflow-hidden"
     style="background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);">

    <!-- Particules flottantes décoratives -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="pointer-events:none; overflow:hidden;">
        <div class="position-absolute rounded-circle opacity-25"
             style="width:300px;height:300px;background:radial-gradient(circle,#667eea,transparent);top:-50px;right:-80px;animation:float 8s ease-in-out infinite;"></div>
        <div class="position-absolute rounded-circle opacity-20"
             style="width:200px;height:200px;background:radial-gradient(circle,#764ba2,transparent);bottom:10%;left:-40px;animation:float 10s ease-in-out infinite reverse;"></div>
        <div class="position-absolute rounded-circle opacity-15"
             style="width:150px;height:150px;background:radial-gradient(circle,#f093fb,transparent);top:40%;right:15%;animation:float 12s ease-in-out infinite 2s;"></div>
    </div>

    <div class="container py-5 position-relative" style="z-index:2;">

        <!-- HEADER -->
        <div class="text-center mb-5">
            <span class="badge bg-white bg-opacity-10 text-white px-4 py-2 rounded-pill mb-3"
                  style="backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.1);font-size:0.85rem;letter-spacing:1px;">
                🚀 ABONNEMENT
            </span>
            <h1 class="fw-bold text-white display-5">Choisissez votre formule</h1>
            <p class="text-white-50 fs-5">Accès illimité à tous les cours premium</p>
        </div>

        <div class="row justify-content-center">

            <!-- PLAN PREMIUM -->
            <div class="col-md-5 col-lg-4">

                <div class="card border-0 text-center p-4 p-xl-5 plan-card">

                    <!-- Badge -->
                    <span class="badge badge-premium mx-auto mb-3">
                        ⭐ Recommandé
                    </span>

                    <h3 class="fw-bold text-white mb-1">Premium</h3>
                    <p class="text-white-50 small mb-3">Accès complet & illimité</p>

                    <!-- Prix -->
                    <div class="d-flex align-items-center justify-content-center mb-4">
                        <span class="text-white-50 fs-4 align-top mt-2">€</span>
                        <span class="fw-bold text-white display-3 lh-1">200</span>
                        <span class="text-white-50 align-self-end mb-2 ms-1">/ an</span>
                    </div>

                    <!-- Features avec icônes -->
                    <ul class="list-unstyled text-start mx-auto mb-4" style="max-width:220px;">
                        <li class="d-flex align-items-center gap-3 text-white mb-3">
                            <span class="feature-check">✓</span>
                            Accès à tous les cours
                        </li>
                        <li class="d-flex align-items-center gap-3 text-white mb-3">
                            <span class="feature-check">✓</span>
                            Lives illimités
                        </li>
                        <li class="d-flex align-items-center gap-3 text-white mb-3">
                            <span class="feature-check">✓</span>
                            Téléchargement PDF
                        </li>
                        <li class="d-flex align-items-center gap-3 text-white mb-3">
                            <span class="feature-check">✓</span>
                            Support prioritaire
                        </li>
                        <li class="d-flex align-items-center gap-3 text-white mb-3">
                            <span class="feature-check">✓</span>
                            Certificat inclus
                        </li>
                    </ul>

                    <!-- Boutons -->
                    <div class="d-grid gap-3">

                        <a href="{{ route('student.payment') }}?method=paypal"
                           class="btn btn-paypal rounded-pill">
                            <span class="d-flex align-items-center justify-content-center gap-2">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                                    <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z"/>
                                </svg>
                                Payer avec PayPal
                            </span>
                        </a>

                        <a href="{{ route('student.payment') }}?method=bank"
                           class="btn btn-bank rounded-pill">
                            <span class="d-flex align-items-center justify-content-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="8" width="20" height="14" rx="2"/>
                                    <path d="M12 2L2 8h20L12 2z"/>
                                    <path d="M12 14v4"/>
                                </svg>
                                Virement bancaire
                            </span>
                        </a>

                        <p class="text-white-50 small mt-2 mb-0 opacity-75">
                            🔒 Paiement sécurisé — Annulation à tout moment
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<style>
/* Animation flottante */
@keyframes float {
    0%, 100% { transform: translateY(0px) scale(1); }
    50%      { transform: translateY(-30px) scale(1.05); }
}

/* Carte glassmorphism */
.plan-card {
    background: rgba(255,255,255,0.06) !important;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 28px;
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
}

.plan-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
    transition: left 0.7s;
}

.plan-card:hover::before {
    left: 100%;
}

.plan-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 30px 60px rgba(0,0,0,0.4);
    border-color: rgba(255,255,255,0.25);
}

/* Badge premium */
.badge-premium {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 6px 20px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(245,87,108,0.4);
}

/* Checkmark features */
.feature-check {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
    flex-shrink: 0;
}

/* Bouton PayPal */
.btn-paypal {
    background: linear-gradient(135deg, #0070ba, #1546a0);
    color: white !important;
    border: none;
    padding: 14px 20px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(0,112,186,0.3);
}

.btn-paypal:hover {
    transform: scale(1.03);
    box-shadow: 0 8px 25px rgba(0,112,186,0.5);
    background: linear-gradient(135deg, #0089d0, #1a5bbf);
}

/* Bouton Virement */
.btn-bank {
    background: rgba(255,255,255,0.08);
    color: white !important;
    border: 1px solid rgba(255,255,255,0.2);
    padding: 14px 20px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s;
}

.btn-bank:hover {
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.4);
    transform: scale(1.03);
}

/* Responsive */
@media (max-width: 576px) {
    .plan-card { padding: 2rem 1.5rem !important; }
    .display-3 { font-size: 3.5rem; }
}

/* ══════════════════════════════════════════════════════════════
   MODE CLAIR — PLANS PAGE
   ══════════════════════════════════════════════════════════════ */
html.light-mode .plans-wrapper {
    background: linear-gradient(135deg, #f0f4ff 0%, #e8edf5 50%, #f5f7fa 100%) !important;
}

html.light-mode .plans-wrapper .opacity-25,
html.light-mode .plans-wrapper .opacity-20,
html.light-mode .plans-wrapper .opacity-15 {
    opacity: 0.08 !important;
}

html.light-mode .plans-wrapper div[style*="radial-gradient(circle,#667eea"] {
    background: radial-gradient(circle, #a0b8ff, transparent) !important;
}
html.light-mode .plans-wrapper div[style*="radial-gradient(circle,#764ba2"] {
    background: radial-gradient(circle, #c084e0, transparent) !important;
}
html.light-mode .plans-wrapper div[style*="radial-gradient(circle,#f093fb"] {
    background: radial-gradient(circle, #f5b8fd, transparent) !important;
}

/* Header badge */
html.light-mode .plans-wrapper .badge.bg-white.bg-opacity-10 {
    background: rgba(0, 58, 143, 0.08) !important;
    border-color: rgba(0, 58, 143, 0.15) !important;
    color: #003A8F !important;
}

/* Header titles — restaurés en foncé car le fond est clair */
html.light-mode .plans-wrapper h1.text-white {
    color: #1e293b !important;
}
html.light-mode .plans-wrapper .text-white-50 {
    color: #64748b !important;
}

/* Plan card — fond blanc */
html.light-mode .plans-wrapper .plan-card {
    background: rgba(255, 255, 255, 0.95) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.06) !important;
}
html.light-mode .plans-wrapper .plan-card:hover {
    box-shadow: 0 30px 60px rgba(0, 58, 143, 0.12) !important;
    border-color: rgba(0, 58, 143, 0.15) !important;
}
html.light-mode .plans-wrapper .plan-card::before {
    background: linear-gradient(90deg, transparent, rgba(0, 58, 143, 0.03), transparent);
}

/* Card text */
html.light-mode .plans-wrapper .plan-card .text-white {
    color: #1e293b !important;
}
html.light-mode .plans-wrapper .plan-card .text-white-50 {
    color: #64748b !important;
}
html.light-mode .plans-wrapper .plan-card h3.text-white {
    color: #1e293b !important;
}

/* Prix */
html.light-mode .plans-wrapper .plan-card .display-3 {
    color: #1e293b !important;
}

/* Features list */
html.light-mode .plans-wrapper .plan-card li.text-white {
    color: #334155 !important;
}

/* Bouton Virement bancaire — version claire */
html.light-mode .plans-wrapper .btn-bank {
    background: rgba(0, 0, 0, 0.03) !important;
    color: #334155 !important;
    border-color: rgba(0, 0, 0, 0.12) !important;
}
html.light-mode .plans-wrapper .btn-bank:hover {
    background: rgba(0, 0, 0, 0.06) !important;
    border-color: #003A8F !important;
    color: #003A8F !important;
}

/* Paiement sécurisé texte */
html.light-mode .plans-wrapper .opacity-75.text-white-50 {
    color: #94a3b8 !important;
}
</style>

@endsection
