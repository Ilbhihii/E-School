@extends('layouts.guest')

@section('title', 'Vérification email')

@section('content')

<!-- SUCCESS -->
@if (session('status') == 'verification-link-sent')
    <div class="alert text-center py-2 mb-4" style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.2); color: #6EE7B7; border-radius: 12px; font-size: 0.875rem;">
        <i class="bi bi-check-circle-fill me-1"></i>
        Un nouveau lien de vérification a été envoyé à votre adresse email.
    </div>
@endif

<div class="text-center mb-3">
    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
        <i class="bi bi-envelope-check" style="font-size: 1.75rem; color: #6EE7B7;"></i>
    </div>
    <h5 class="fw-bold text-white mb-1" style="font-family: 'Poppins', sans-serif;">Vérifiez votre email</h5>
    <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem; line-height: 1.6;">
        Merci de vous être inscrit ! Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.
    </p>
</div>

<div class="d-flex flex-column gap-3">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-3d btn-3d-gradient w-100 justify-content-center" style="padding: 14px;">
            <i class="bi bi-arrow-repeat"></i>
            Renvoyer l'email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="background: none; border: none; color: rgba(255,255,255,0.4); font-size: 0.85rem; cursor: pointer; transition: color 0.3s;"
                onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
            <i class="bi bi-box-arrow-right me-1"></i> Se déconnecter
        </button>
    </form>
</div>

@endsection
