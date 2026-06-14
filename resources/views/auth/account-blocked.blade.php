@extends('layouts.guest')

@section('title', 'Compte en attente')

@section('content')

<div class="text-center">

    <!-- LOADER -->
    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(255, 209, 102, 0.12); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
        <div class="spinner-border" style="width: 2rem; height: 2rem; color: #FFD166;" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
    </div>

    <h5 class="fw-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">
        Compte en attente ⏳
    </h5>

    <p class="mb-1" style="color: #6EE7B7; font-weight: 600; font-size: 0.9rem;">
        Validation en cours...
    </p>

    <p class="mb-3" style="color: rgba(255,255,255,0.7); font-size: 0.95rem;">
        👋 Bonjour <strong class="text-white">{{ auth()->user()->name ?? 'Étudiant' }}</strong>
    </p>

    <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem; line-height: 1.6;">
        Votre inscription est en cours de validation par l'administrateur.
    </p>

    <p class="mb-4" style="color: var(--3d-gold); font-weight: 600; font-size: 0.85rem;">
        ⚡ Vous serez redirigé automatiquement après activation
    </p>

    <!-- STEPS -->
    <div class="mb-4 text-start" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1rem;">
        <div class="d-flex align-items-center gap-2 mb-2" style="color: rgba(255,255,255,0.6); font-size: 0.8rem;">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: rgba(255,209,102,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #FFD166; animation: pulse 1.5s ease-in-out infinite;"></span>
            </span>
            Vérification du compte
        </div>
        <div class="d-flex align-items-center gap-2 mb-2" style="color: rgba(255,255,255,0.4); font-size: 0.8rem;">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="bi bi-envelope" style="font-size: 0.7rem;"></i>
            </span>
            Email de confirmation
        </div>
        <div class="d-flex align-items-center gap-2" style="color: rgba(255,255,255,0.4); font-size: 0.8rem;">
            <span style="width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="bi bi-rocket-takeoff" style="font-size: 0.7rem;"></i>
            </span>
            Accès complet à la plateforme
        </div>
    </div>

    @if (auth()->user()->created_at)
        <p style="color: rgba(255,255,255,0.25); font-size: 0.75rem; margin-bottom: 1rem;">
            Compte créé le {{ auth()->user()->created_at->format('d/m/Y') }}
        </p>
    @endif

    <!-- LOGOUT -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-3d btn-3d-outline w-100 justify-content-center" style="padding: 12px;">
            <i class="bi bi-box-arrow-right"></i>
            Se déconnecter
        </button>
    </form>

</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}
</style>

<!-- AUTO REDIRECT -->
<script>
setInterval(() => {
    fetch('/check-status')
        .then(res => res.json())
        .then(data => {
            if (data.active) {
                window.location.href = '/dashboard';
            }
        });
}, 5000);
</script>

@endsection
