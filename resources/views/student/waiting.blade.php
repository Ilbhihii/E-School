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
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="waiting-glass p-5 text-center">

                <div class="waiting-icon-wrap">
                    <i class="bi bi-hourglass-split"></i>
                </div>

                <h2 class="fw-bold mb-2" style="color:#1F2937;">Test complété avec succès !</h2>
                <p class="text-muted mb-4">Votre test a été enregistré. Un administrateur va examiner vos résultats.</p>

                @if (session('success'))
                    @php
                        preg_match('/Score:\s*(\d+)\/(\d+)/', session('success'), $matches);
                        $score = $matches[1] ?? 0;
                        $total = $matches[2] ?? 0;
                        $percentage = $total > 0 ? round(($score / $total) * 100, 1) : 0;
                    @endphp
                    <div class="score-card">
                        <h3 class="fw-bold mb-3" style="font-size:1.1rem;opacity:0.9;">Votre Score</h3>
                        <div style="font-size:3rem;font-weight:800;line-height:1;">{{ $score }} / {{ $total }}</div>
                        <div style="font-size:1.3rem;font-weight:600;margin-top:0.5rem;opacity:0.95;">{{ $percentage }}%</div>
                        <div class="progress-bar-track">
                            <div class="progress-bar-fill" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div class="alert alert-success mb-4" style="border-radius:12px;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="alert alert-info mb-4" style="border-radius:12px;background:#F0F9FF;border:1px solid #BAE6FD;color:#0369A1;">
                    <i class="bi bi-info-circle me-2"></i>
                    Vous serez notifié par email dès que votre compte sera activé.
                </div>

                <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                    @if(auth()->user()->is_active)
                        <a href="{{ route('plans') }}" class="btn btn-lg px-5" style="background:linear-gradient(135deg,#F59E0B,#D97706);color:white;border:none;border-radius:12px;font-weight:700;">
                            <i class="bi bi-credit-card me-2"></i> Aller aux plans
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-lg px-5" style="background:linear-gradient(135deg,#3B82F6,#1D4ED8);color:white;border:none;border-radius:12px;font-weight:700;">
                            <i class="bi bi-house me-2"></i> Retour à l'accueil
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
