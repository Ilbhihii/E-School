@extends('layouts.prof')

@section('title', 'Paramètres')
@section('page_title', 'Paramètres')
@section('breadcrumb', 'Profil et sécurité')

@section('content')
@php
    $professor = auth()->user();
    $emailVerified = !empty($professor->email_verified_at);
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-sliders2"></i> Gestion du compte</span>
        <h1 class="pp-page-title">Paramètres</h1>
        <p class="pp-page-description">
            Mettez à jour vos informations personnelles et protégez votre compte avec un mot de passe sécurisé.
        </p>
    </div>

    <div class="pp-page-actions">
        <a href="{{ route('prof.profile') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-person-circle"></i> Voir mon profil
        </a>
    </div>
</section>

@if($errors->any())
    <div class="adm-alert adm-alert-danger">
        <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
        <div>
            <strong>Veuillez corriger les informations indiquées.</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="pp-settings-grid">
    <section class="pp-panel">
        <header class="pp-panel-head">
            <div class="d-flex align-items-center gap-3">
                <span class="pp-settings-head-icon" style="--settings-color:#a78bfa;"><i class="bi bi-person-fill"></i></span>
                <div class="pp-panel-title-wrap">
                    <h2 class="pp-panel-title">Informations personnelles</h2>
                    <p class="pp-panel-subtitle">Nom et adresse email du compte.</p>
                </div>
            </div>
        </header>

        <form method="POST" action="{{ route('prof.settings.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="pp-form-section">
                <div class="pp-field">
                    <label for="name" class="pp-label"><i class="bi bi-person"></i> Nom complet <span class="required">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $professor->name) }}"
                        class="adm-form-control @error('name') is-invalid @enderror"
                        maxlength="255"
                        required
                    >
                    @error('name') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>

                <div class="pp-field">
                    <label for="email" class="pp-label"><i class="bi bi-envelope"></i> Adresse email <span class="required">*</span></label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $professor->email) }}"
                        class="adm-form-control @error('email') is-invalid @enderror"
                        maxlength="255"
                        required
                    >
                    @error('email') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>

                <div class="pp-info-card">
                    <span class="pp-info-icon"><i class="bi bi-geo-alt-fill"></i></span>
                    <span class="pp-info-copy">
                        <span class="pp-info-label">Localisation</span>
                        <strong class="pp-info-value">{{ $professor->location ?: 'Non renseignée' }}</strong>
                    </span>
                </div>
                <p class="pp-help">La localisation est gérée séparément par l’administration.</p>
            </div>

            <div class="pp-form-actions">
                <button type="submit" class="adm-btn adm-btn-primary">
                    <i class="bi bi-check2-circle"></i> Enregistrer le profil
                </button>
            </div>
        </form>
    </section>

    <section class="pp-panel">
        <header class="pp-panel-head">
            <div class="d-flex align-items-center gap-3">
                <span class="pp-settings-head-icon" style="--settings-color:#4ade80;"><i class="bi bi-shield-lock-fill"></i></span>
                <div class="pp-panel-title-wrap">
                    <h2 class="pp-panel-title">Sécurité du compte</h2>
                    <p class="pp-panel-subtitle">Changez votre mot de passe en toute sécurité.</p>
                </div>
            </div>
        </header>

        <form method="POST" action="{{ route('prof.settings.password.update') }}">
            @csrf
            @method('PUT')

            <div class="pp-form-section">
                <div class="pp-field">
                    <label for="current_password" class="pp-label"><i class="bi bi-lock"></i> Mot de passe actuel <span class="required">*</span></label>
                    <div class="pp-password-wrap">
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="adm-form-control @error('current_password') is-invalid @enderror"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="pp-password-toggle" data-password-toggle="current_password" aria-label="Afficher le mot de passe">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('current_password') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>

                <div class="pp-field">
                    <label for="password" class="pp-label"><i class="bi bi-key"></i> Nouveau mot de passe <span class="required">*</span></label>
                    <div class="pp-password-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="adm-form-control @error('password') is-invalid @enderror"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="pp-password-toggle" data-password-toggle="password" aria-label="Afficher le mot de passe">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password') <span class="adm-form-error">{{ $message }}</span> @enderror
                    <p class="pp-help">Utilisez au moins 8 caractères, avec des lettres, des chiffres et un symbole.</p>
                </div>

                <div class="pp-field">
                    <label for="password_confirmation" class="pp-label"><i class="bi bi-check-circle"></i> Confirmation <span class="required">*</span></label>
                    <div class="pp-password-wrap">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="adm-form-control"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="pp-password-toggle" data-password-toggle="password_confirmation" aria-label="Afficher le mot de passe">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pp-form-actions">
                <button type="submit" class="adm-btn adm-btn-success">
                    <i class="bi bi-shield-check"></i> Modifier le mot de passe
                </button>
            </div>
        </form>
    </section>
</div>

<section class="pp-panel pp-section-gap">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-info-circle-fill"></i> Informations du compte</h2>
            <p class="pp-panel-subtitle">État général de votre accès professeur.</p>
        </div>
    </header>
    <div class="pp-panel-body">
        <div class="pp-account-strip">
            <div class="pp-account-fact"><span>Rôle</span><span>Professeur</span></div>
            <div class="pp-account-fact"><span>Membre depuis</span><span>{{ $professor->created_at?->format('d/m/Y') ?? 'Non disponible' }}</span></div>
            <div class="pp-account-fact"><span>Adresse email</span><span>{{ $emailVerified ? 'Vérifiée' : 'Non vérifiée' }}</span></div>
            <div class="pp-account-fact"><span>Statut</span><span>Compte actif</span></div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.passwordToggle);
            const icon = button.querySelector('i');
            if (!input || !icon) return;

            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !show);
            icon.classList.toggle('bi-eye-slash', show);
            button.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        });
    });
});
</script>
@endpush
@endsection
