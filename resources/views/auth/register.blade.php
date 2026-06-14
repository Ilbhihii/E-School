@extends('layouts.guest')

@section('title', 'Inscription')

@section('content')

<!-- ERRORS -->
@if ($errors->any())
    <div class="alert alert-danger text-center py-2 mb-4" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.2); color: #FCA5A5; border-radius: 12px; font-size: 0.875rem;">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- NAME -->
    <div class="mb-3">
        <label class="auth-label-3d">Nom complet</label>
        <input type="text" name="name"
               class="auth-input-3d"
               placeholder="Votre nom"
               value="{{ old('name') }}"
               required autofocus>
    </div>

    <!-- EMAIL -->
    <div class="mb-3">
        <label class="auth-label-3d">Email</label>
        <input type="email" name="email"
               class="auth-input-3d"
               placeholder="exemple@email.com"
               value="{{ old('email') }}"
               required>
    </div>

    <!-- PASSWORD -->
    <div class="mb-3">
        <label class="auth-label-3d">Mot de passe</label>
        <input type="password" name="password"
               class="auth-input-3d"
               placeholder="Minimum 8 caractères"
               required>
    </div>

    <!-- CONFIRM PASSWORD -->
    <div class="mb-3">
        <label class="auth-label-3d">Confirmer mot de passe</label>
        <input type="password" name="password_confirmation"
               class="auth-input-3d"
               placeholder="••••••••"
               required>
    </div>

    <!-- COUNTRY & CITY ROW -->
    <div class="row g-3 mb-3">
        <div class="col-6">
            <label class="auth-label-3d">Pays</label>
            <input type="text" name="country"
                   class="auth-input-3d"
                   placeholder="Maroc"
                   value="{{ old('country') }}">
        </div>
        <div class="col-6">
            <label class="auth-label-3d">Ville</label>
            <input type="text" name="city"
                   class="auth-input-3d"
                   placeholder="Rabat"
                   value="{{ old('city') }}">
        </div>
    </div>

    <!-- BTN -->
    <button type="submit" class="btn-3d btn-3d-gold w-100 justify-content-center" style="padding: 14px;">
        <i class="bi bi-person-plus"></i>
        Créer mon compte
    </button>
</form>

<!-- LOGIN LINK -->
<div class="text-center mt-4">
    <span style="color: rgba(255,255,255,0.35); font-size: 0.875rem;">
        Déjà inscrit ?
    </span>
    <a href="{{ route('login') }}" class="auth-link-3d fw-semibold ms-1" style="color: var(--3d-gold); font-size: 0.875rem;">
        Se connecter <i class="bi bi-arrow-right"></i>
    </a>
</div>

@endsection
