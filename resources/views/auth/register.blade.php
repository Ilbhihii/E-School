@extends('layouts.guest')

@section('title', 'Inscription')

@section('content')

<div class="auth-page-heading">
    <span class="auth-page-icon"><i class="bi bi-person-plus"></i></span>
    <h2>Créer votre espace</h2>
    <p>Quelques informations suffisent pour commencer votre parcours personnalisé.</p>
</div>

@if(!empty($pendingRegistration))
    <div
        class="alert text-center py-2 mb-4"
        style="background:rgba(34,197,94,.11);border:1px solid rgba(34,197,94,.2);color:#86EFAC;border-radius:12px;font-size:.78rem;line-height:1.5;"
    >
        <i class="bi bi-check-circle-fill me-1"></i>
        Test et rendez-vous envoyés. Créez votre compte
        pour terminer votre inscription.
    </div>
@endif

@if(session('success'))
    <div
        class="alert text-center py-2 mb-4"
        style="background:rgba(37,99,235,.11);border:1px solid rgba(59,130,246,.2);color:#BFDBFE;border-radius:12px;font-size:.78rem;line-height:1.5;"
    >
        {{ session('success') }}
    </div>
@endif

<!-- ERRORS -->
@if ($errors->any())
    <div class="alert alert-danger text-center py-2 mb-4" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.2); color: #FCA5A5; border-radius: 12px; font-size: 0.875rem;">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- NAME -->
    <div class="mb-3 auth-field">
        <label class="auth-label-3d">Nom complet</label>
        <i class="bi bi-person"></i>
        <input type="text" name="name"
               class="auth-input-3d"
               placeholder="Votre nom"
               value="{{ old('name', $registrationPrefill['name'] ?? '') }}"
               required autofocus>
    </div>

    <!-- EMAIL -->
    <div class="mb-3 auth-field">
        <label class="auth-label-3d">Email</label>
        <i class="bi bi-envelope"></i>
        <input type="email" name="email"
               class="auth-input-3d"
               placeholder="exemple@email.com"
               value="{{ old('email', $registrationPrefill['email'] ?? '') }}"
               required>
    </div>

    <!-- PASSWORD -->
    <div class="mb-3 auth-field">
        <label class="auth-label-3d">Mot de passe</label>
        <i class="bi bi-lock"></i>
        <input type="password" name="password"
               class="auth-input-3d"
               placeholder="Minimum 8 caractères"
               required>
    </div>

    <!-- CONFIRM PASSWORD -->
    <div class="mb-3 auth-field">
        <label class="auth-label-3d">Confirmer mot de passe</label>
        <i class="bi bi-shield-lock"></i>
        <input type="password" name="password_confirmation"
               class="auth-input-3d"
               placeholder="••••••••"
               required>
    </div>

    <!-- COUNTRY & CITY ROW -->
    <div class="row g-3 mb-3">
        <div class="col-6 auth-field">
            <label class="auth-label-3d">Pays</label>
            <i class="bi bi-globe2"></i>
            <input type="text" name="country"
                   class="auth-input-3d"
                   placeholder="Maroc"
                   value="{{ old('country', $registrationPrefill['country'] ?? '') }}">
        </div>
        <div class="col-6 auth-field">
            <label class="auth-label-3d">Ville</label>
            <i class="bi bi-geo-alt"></i>
            <input type="text" name="city"
                   class="auth-input-3d"
                   placeholder="Rabat"
                   value="{{ old('city', $registrationPrefill['city'] ?? '') }}">
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
