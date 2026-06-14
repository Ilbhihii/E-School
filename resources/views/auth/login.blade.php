@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')

<!-- ERRORS -->
@if ($errors->any())
    <div class="alert alert-danger text-center py-2 mb-4" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.2); color: #FCA5A5; border-radius: 12px; font-size: 0.875rem;">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- EMAIL -->
    <div class="mb-3">
        <label class="auth-label-3d">Email</label>
        <input type="email" name="email"
               class="auth-input-3d"
               placeholder="exemple@email.com"
               value="{{ old('email') }}"
               required autofocus>
    </div>

    <!-- PASSWORD -->
    <div class="mb-3">
        <label class="auth-label-3d">Mot de passe</label>
        <input type="password" name="password"
               class="auth-input-3d"
               placeholder="••••••••"
               required>
    </div>

    <!-- REMEMBER & FORGOT -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember"
                   style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.2); border-radius: 4px;">
            <label class="form-check-label" for="remember" style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">
                Se souvenir de moi
            </label>
        </div>
        <a href="{{ route('password.request') }}" class="auth-link-3d" style="font-size: 0.85rem;">
            Mot de passe oublié ?
        </a>
    </div>

    <!-- BTN -->
    <button type="submit" class="btn-3d btn-3d-gradient w-100 justify-content-center" style="padding: 14px;">
        <i class="bi bi-box-arrow-in-right"></i>
        Se connecter
    </button>
</form>

<!-- REGISTER LINK -->
<div class="text-center mt-4">
    <span style="color: rgba(255,255,255,0.35); font-size: 0.875rem;">
        Pas encore inscrit ?
    </span>
    <a href="{{ route('register') }}" class="auth-link-3d fw-semibold ms-1" style="color: var(--3d-gold); font-size: 0.875rem;">
        Créer un compte <i class="bi bi-arrow-right"></i>
    </a>
</div>

@endsection
