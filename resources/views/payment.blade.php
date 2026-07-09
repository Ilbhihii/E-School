@extends('layouts.front')

@section('title', 'Abonnement requis')

@section('content')

<style>
.payment-block-wrapper {
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.payment-block-wrapper::before {
    content: '';
    position: absolute;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(102,126,234,0.15), transparent 70%);
    top: -100px;
    right: -100px;
    animation: floatPayment 8s ease-in-out infinite;
}

.payment-block-wrapper::after {
    content: '';
    position: absolute;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(118,75,162,0.12), transparent 70%);
    bottom: -80px;
    left: -80px;
    animation: floatPayment 10s ease-in-out infinite reverse;
}

@keyframes floatPayment {
    0%, 100% { transform: translateY(0px) scale(1); }
    50% { transform: translateY(-20px) scale(1.05); }
}

.payment-glass-card {
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 24px;
    padding: 2.5rem;
    text-align: center;
    position: relative;
    z-index: 2;
}

.payment-icon-wrap {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #F59E0B, #D97706);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    box-shadow: 0 10px 30px rgba(245,158,11,0.3);
}

.payment-icon-wrap i {
    font-size: 1.75rem;
    color: white;
}

.payment-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 16px 24px;
    border-radius: 14px;
    font-weight: 700;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    font-family: inherit;
}

.payment-btn:hover {
    transform: translateY(-3px);
}

.btn-paypal {
    background: linear-gradient(135deg, #0070ba, #1546a0);
    color: white !important;
    box-shadow: 0 8px 25px rgba(0,112,186,0.3);
}

.btn-paypal:hover {
    box-shadow: 0 12px 35px rgba(0,112,186,0.5);
    background: linear-gradient(135deg, #0089d0, #1a5bbf);
}

.btn-bank {
    background: rgba(255,255,255,0.08);
    color: white !important;
    border: 1px solid rgba(255,255,255,0.2);
}

.btn-bank:hover {
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.4);
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}

.feature-item:last-child {
    border-bottom: none;
}

.feature-item .check {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10B981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.65rem;
    flex-shrink: 0;
}

/* Light mode */
html.light-mode .payment-block-wrapper {
    background: linear-gradient(135deg, #f0f4ff 0%, #e8edf5 50%, #f5f7fa 100%);
}

html.light-mode .payment-block-wrapper::before {
    background: radial-gradient(circle, rgba(102,126,234,0.08), transparent 70%);
}

html.light-mode .payment-block-wrapper::after {
    background: radial-gradient(circle, rgba(118,75,162,0.06), transparent 70%);
}

html.light-mode .payment-glass-card {
    background: rgba(255,255,255,0.95);
    border-color: rgba(0,0,0,0.08);
}

html.light-mode .payment-glass-card h2 {
    color: #1e293b !important;
}

html.light-mode .payment-glass-card .text-white-50 {
    color: #64748b !important;
}

html.light-mode .payment-glass-card .feature-item div:last-child {
    color: #334155 !important;
}

html.light-mode .btn-bank {
    background: rgba(0,0,0,0.03);
    color: #334155 !important;
    border-color: rgba(0,0,0,0.12);
}

html.light-mode .btn-bank:hover {
    background: rgba(0,0,0,0.06);
    border-color: #003A8F;
    color: #003A8F !important;
}
</style>

<div class="payment-block-wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="payment-glass-card">

                    <div class="payment-icon-wrap">
                        <i class="bi bi-credit-card"></i>
                    </div>

                    <h2 class="fw-bold text-white mb-2" style="font-family:'Poppins',sans-serif;">Abonnement requis</h2>
                    <p class="text-white-50 mb-4" style="max-width:400px;margin-left:auto;margin-right:auto;">
                        Vous devez souscrire à un abonnement pour accéder à l'espace étudiant.
                    </p>

                    @if(session('error'))
                        <div class="alert" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.15);color:#FCA5A5;border-radius:12px;padding:12px 16px;font-size:0.85rem;margin-bottom:1.25rem;">
                            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert" style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.15);color:#4ADE80;border-radius:12px;padding:12px 16px;font-size:0.85rem;margin-bottom:1.25rem;">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <!-- Prix -->
                    <div style="margin-bottom:1.5rem;">
                        <span style="font-size:0.8rem;color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:0.1em;font-weight:600;">Offre Premium</span>
                        <div style="display:flex;align-items:baseline;justify-content:center;gap:4px;margin-top:4px;">
                            <span style="font-size:3rem;font-weight:800;color:white;line-height:1;">200</span>
                            <span style="font-size:1.1rem;color:rgba(255,255,255,0.4);">€</span>
                            <span style="font-size:0.9rem;color:rgba(255,255,255,0.3);margin-left:4px;">/ an</span>
                        </div>
                    </div>

                    <!-- Features -->
                    <div style="text-align:left;margin-bottom:1.5rem;padding:0 0.5rem;">
                        <div class="feature-item">
                            <span class="check"><i class="bi bi-check"></i></span>
                            <div style="color:rgba(255,255,255,0.75);font-size:0.85rem;">Accès à tous les cours et lives</div>
                        </div>
                        <div class="feature-item">
                            <span class="check"><i class="bi bi-check"></i></span>
                            <div style="color:rgba(255,255,255,0.75);font-size:0.85rem;">Chat avec les professeurs</div>
                        </div>
                        <div class="feature-item">
                            <span class="check"><i class="bi bi-check"></i></span>
                            <div style="color:rgba(255,255,255,0.75);font-size:0.85rem;">Suivi pédagogique personnalisé</div>
                        </div>
                        <div class="feature-item">
                            <span class="check"><i class="bi bi-check"></i></span>
                            <div style="color:rgba(255,255,255,0.75);font-size:0.85rem;">Exercices, quiz et certificat</div>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:12px;">
                        @if(request('method') === 'paypal')
                            <!-- Paiement PayPal -->
                            <div style="background:rgba(0,0,0,0.15);border-radius:14px;padding:1.25rem;margin-bottom:0.5rem;">
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:0.75rem;">
                                    <i class="bi bi-paypal" style="font-size:1.5rem;color:#0070ba;"></i>
                                    <span style="font-weight:600;color:white;">Paiement PayPal</span>
                                </div>
                                <p style="color:rgba(255,255,255,0.5);font-size:0.82rem;margin-bottom:1rem;">
                                    Cliquez ci-dessous pour être redirigé vers PayPal et effectuer votre paiement en toute sécurité.
                                </p>
                                <a href="https://www.paypal.me/abdelghanimaloulou1" target="_blank" class="payment-btn btn-paypal" style="padding:12px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z"/>
                                    </svg>
                                    Payer avec PayPal
                                </a>
                                <p style="color:rgba(255,255,255,0.3);font-size:0.7rem;margin-top:0.75rem;">
                                    Après paiement, veuillez contacter l'administrateur avec votre confirmation.
                                </p>
                            </div>
                            <a href="{{ route('student.payment') }}" class="payment-btn" style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5);padding:10px;font-weight:500;font-size:0.85rem;">
                                <i class="bi bi-arrow-left"></i> Autres méthodes de paiement
                            </a>

                        @elseif(request('method') === 'bank')
                            <!-- Virement bancaire -->
                            <div style="background:rgba(0,0,0,0.15);border-radius:14px;padding:1.25rem;margin-bottom:0.5rem;">
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:0.75rem;">
                                    <i class="bi bi-bank" style="font-size:1.3rem;color:#4ADE80;"></i>
                                    <span style="font-weight:600;color:white;">Virement bancaire</span>
                                </div>

                                <div style="background:rgba(255,255,255,0.04);border-radius:10px;padding:0.85rem 1rem;text-align:left;margin-bottom:0.75rem;">
                                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.4);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Maroc — Banque Populaire</div>
                                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.6);margin-bottom:4px;"><span style="color:rgba(255,255,255,0.3);">RIB :</span> 123456789012345678901234</div>
                                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.6);"><span style="color:rgba(255,255,255,0.3);">Titulaire :</span> M. Abdelghani Maloulou</div>
                                </div>

                                <div style="background:rgba(255,255,255,0.04);border-radius:10px;padding:0.85rem 1rem;text-align:left;margin-bottom:0.75rem;">
                                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.4);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">France — EUROCOMPTE SÉRÉNITÉ</div>
                                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.6);margin-bottom:2px;"><span style="color:rgba(255,255,255,0.3);">RIB :</span> 10278 08976 00021074401 03</div>
                                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.6);margin-bottom:2px;"><span style="color:rgba(255,255,255,0.3);">IBAN :</span> FR76 1027 8089 7600 0210 7440 103</div>
                                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.6);"><span style="color:rgba(255,255,255,0.3);">BIC :</span> CMCIFR2A</div>
                                </div>

                                <p style="color:rgba(255,255,255,0.4);font-size:0.75rem;">
                                    <i class="bi bi-info-circle me-1"></i> Après virement, envoyez le reçu à l'administrateur pour activer votre accès.
                                </p>
                            </div>
                            <a href="{{ route('student.payment') }}" class="payment-btn" style="background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5);padding:10px;font-weight:500;font-size:0.85rem;">
                                <i class="bi bi-arrow-left"></i> Autres méthodes de paiement
                            </a>

                        @else
                            <!-- Choix de la méthode -->
                            <a href="{{ route('student.payment') }}?method=paypal" class="payment-btn btn-paypal">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                                    <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z"/>
                                </svg>
                                Payer avec PayPal
                            </a>
                            <a href="{{ route('student.payment') }}?method=bank" class="payment-btn btn-bank">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="8" width="20" height="14" rx="2"/>
                                    <path d="M12 2L2 8h20L12 2z"/>
                                    <path d="M12 14v4"/>
                                </svg>
                                Virement bancaire
                            </a>
                            <p style="color:rgba(255,255,255,0.25);font-size:0.72rem;margin-top:0.5rem;">
                                🔒 Paiement sécurisé
                            </p>
                        @endif
                    </div>

                    <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.04);">
                        <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.3);text-decoration:none;font-size:0.82rem;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.6)'" onmouseout="this.style.color='rgba(255,255,255,0.3)'">
                            <i class="bi bi-house me-1"></i> Retour à l'accueil
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
