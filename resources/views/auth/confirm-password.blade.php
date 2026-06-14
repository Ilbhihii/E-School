@extends('layouts.guest')

@section('title', 'Confirmer le mot de passe')

@section('content')

<!-- ERRORS -->
@if ($errors->any())
    <div class="alert alert-danger text-center py-2 mb-4" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.2); color: #FCA5A5; border-radius: 12px; font-size: 0.875rem;">
        {{ $errors->first() }}
    </div>
@endif

<p style="color: rgba(255,255,255,0.5); font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.5rem; text-align: center;">
    Ceci est une zone sécurisée. Veuillez confirmer votre mot de passe avant de continuer.
</p>

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <!-- PASSWORD -->
    <div class="mb-3">
        <label class="auth-label-3d">Mot de passe</label>
        <input id="password" type="password" name="password"
               class="auth-input-3d"
               placeholder="••••••••"
               required autocomplete="current-password">
    </div>

    <!-- BTN -->
    <button type="submit" class="btn-3d btn-3d-gradient w-100 justify-content-center" style="padding: 14px;">
        <i class="bi bi-shield-check"></i>
        Confirmer
    </button>
</form>

<!-- BACK TO LOGIN -->
<div class="text-center mt-4">
    <a href="{{ route('login') }}" class="auth-link-3d" style="font-size: 0.875rem;">
        <i class="bi bi-arrow-left"></i>
        Retour à la connexion
    </a>
</div>

@endsection
