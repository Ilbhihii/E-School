@extends('layouts.front')

@section('content')
<style>
.waiting-glass {
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.1);
    overflow: hidden;
    animation: fadeUp 0.6s ease forwards;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.waiting-icon-wrap {
    width: 80px; height: 80px;
    background: linear-gradient(135deg, #F59E0B, #D97706);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    box-shadow: 0 15px 40px rgba(245,158,11,0.35);
    animation: pulseGlow 2s ease-in-out infinite;
}
@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 15px 40px rgba(245,158,11,0.35); }
    50% { box-shadow: 0 20px 60px rgba(245,158,11,0.5); }
}
.waiting-icon-wrap i {
    font-size: 2rem;
    color: white;
}
.score-card {
    background: linear-gradient(135deg, #10B981, #059669);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    text-align: center;
    box-shadow: 0 15px 40px rgba(16,185,129,0.3);
    margin-bottom: 1.5rem;
}
.progress-bar-track {
    width: 100%;
    height: 8px;
    background: rgba(255,255,255,0.25);
    border-radius: 10px;
    overflow: hidden;
    margin-top: 1rem;
}
.progress-bar-fill {
    height: 100%;
    background: white;
    border-radius: 10px;
    transition: width 1.5s ease;
}
.btn-3d-waiting {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    border-radius: 14px;
    font-weight: 700;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    font-family: inherit;
}
.btn-3d-waiting:hover {
    transform: translateY(-3px);
}
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="waiting-glass p-5 text-center">

                <div class="waiting-icon-wrap">
                    <i class="bi bi-hourglass-split"></i>
                </div>

                @if($latestResult)
                    <!-- Apres avoir passe le test -->
                    <h2 class="fw-bold mb-2" style="color:#1F2937;">Test complété avec succès !</h2>
                    <p class="text-muted mb-4">Votre test a été enregistré. Un administrateur va examiner vos résultats.</p>

                    <div class="score-card">
                        <h3 class="fw-bold mb-3" style="font-size:1.1rem;opacity:0.9;">Votre Score</h3>
                        @if($testTitle)
                            <div style="font-size:0.9rem;opacity:0.8;margin-bottom:0.5rem;">{{ $testTitle }}</div>
                        @endif
                        <div style="font-size:3rem;font-weight:800;line-height:1;">{{ $score }} / {{ $total }}</div>
                        <div style="font-size:1.3rem;font-weight:600;margin-top:0.5rem;opacity:0.95;">{{ $percentage }}%</div>
                        <div class="progress-bar-track">
                            <div class="progress-bar-fill" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @else
                    <!-- Avant de passer le test (juste apres inscription + rendez-vous) -->
                    <h2 class="fw-bold mb-2" style="color:#1F2937;">Inscription reçue !</h2>
                    <p class="text-muted mb-4">Votre demande de rendez-vous a été envoyée avec succès. Nous vous contacterons rapidement pour programmer votre test de niveau.</p>

                    <div style="background: #F0F9FF; border: 1px solid #BAE6FD; border-radius: 16px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; text-align: left;">
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:0.75rem;">
                            <div style="width:36px;height:36px;border-radius:50%;background:rgba(59,130,246,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-info-circle" style="color:#2563EB;font-size:1rem;"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;color:#1E293B;font-size:0.88rem;">Prochaines étapes</div>
                            </div>
                        </div>
                        <ol style="margin:0;padding-left:1.25rem;color:#475569;font-size:0.82rem;line-height:1.8;">
                            <li>Un administrateur vous contactera pour fixer la date de votre test de niveau</li>
                            <li>Vous passerez le test pour évaluer votre niveau</li>
                            <li>Une fois le test réussi, vous pourrez choisir une offre et commencer votre apprentissage</li>
                        </ol>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert" style="background:rgba(16,185,129,0.1);color:#059669;border:1px solid rgba(16,185,129,0.15);border-radius:12px;padding:14px 18px;font-size:0.92rem;margin-bottom:1.5rem;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(!$latestResult)
                    <div class="alert" style="background:#FEF3C7;border:1px solid #FDE68A;color:#92400E;border-radius:12px;padding:14px 18px;font-size:0.9rem;margin-bottom:1.5rem;text-align:left;">
                        <div style="display:flex;align-items:flex-start;gap:8px;">
                            <i class="bi bi-clock-history" style="font-size:1.1rem;margin-top:1px;flex-shrink:0;"></i>
                            <span>Votre compte est en attente de validation. Vous serez notifié dès qu'un administrateur aura traité votre demande.</span>
                        </div>
                    </div>
                @else
                    <div class="alert" style="background:#F0F9FF;border:1px solid #BAE6FD;color:#0369A1;border-radius:12px;padding:14px 18px;font-size:0.9rem;margin-bottom:1.5rem;">
                        <i class="bi bi-info-circle me-2"></i>
                        Vous serez notifié par email dès que votre compte sera activé.
                    </div>
                @endif

                <!-- Deux boutons : Accueil + Offres -->
                <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                    <a href="{{ route('home') }}" class="btn-3d-waiting" style="background:linear-gradient(135deg,#3B82F6,#1D4ED8);color:white;box-shadow:0 8px 25px rgba(59,130,246,0.3);">
                        <i class="bi bi-house"></i> Retour à l'accueil
                    </a>
                    <a href="{{ route('plans') }}" class="btn-3d-waiting" style="background:linear-gradient(135deg,#F59E0B,#D97706);color:white;box-shadow:0 8px 25px rgba(245,158,11,0.3);">
                        <i class="bi bi-credit-card"></i> Voir les offres
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
