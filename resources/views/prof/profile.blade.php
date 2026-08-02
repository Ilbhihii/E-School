@extends('layouts.prof')

@section('title', 'Mon profil')
@section('page_title', 'Mon profil')
@section('breadcrumb', 'Profil enseignant')

@section('content')
@php
    $professor = auth()->user();
    $initials = collect(preg_split('/\s+/', trim($professor->name ?? 'Professeur')))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $createdAt = $professor->created_at?->format('d/m/Y') ?? 'Non disponible';
    $emailVerified = !empty($professor->email_verified_at);
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-person-badge-fill"></i> Compte enseignant</span>
        <h1 class="pp-page-title">Mon profil</h1>
        <p class="pp-page-description">
            Consultez les informations principales associées à votre compte professeur.
        </p>
    </div>

    <div class="pp-page-actions">
        <a href="{{ route('prof.settings') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-pencil-square"></i> Modifier mes informations
        </a>
    </div>
</section>

<section class="pp-profile-hero">
    <div class="pp-profile-avatar">{{ $initials ?: 'PR' }}</div>

    <div class="pp-profile-copy">
        <h1>{{ $professor->name }}</h1>
        <p class="pp-profile-email">{{ $professor->email }}</p>
        <div class="pp-profile-badges">
            <span class="adm-badge adm-badge-accent"><i class="bi bi-person-badge"></i> Professeur</span>
            <span class="adm-badge adm-badge-success"><i class="bi bi-shield-check"></i> Compte actif</span>
            <span class="adm-badge {{ $emailVerified ? 'adm-badge-success' : 'adm-badge-warning' }}">
                <i class="bi {{ $emailVerified ? 'bi-envelope-check-fill' : 'bi-envelope-exclamation-fill' }}"></i>
                {{ $emailVerified ? 'Email vérifié' : 'Email non vérifié' }}
            </span>
        </div>
    </div>

    <div class="pp-page-actions">
        <a href="{{ route('prof.dashboard') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-grid-1x2"></i> Tableau de bord
        </a>
        <a href="{{ route('prof.settings') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-gear"></i> Paramètres
        </a>
    </div>
</section>

<div class="pp-layout-two">
    <section class="pp-panel">
        <header class="pp-panel-head">
            <div class="pp-panel-title-wrap">
                <h2 class="pp-panel-title"><i class="bi bi-info-circle-fill"></i> Informations personnelles</h2>
                <p class="pp-panel-subtitle">Données visibles dans votre espace enseignant.</p>
            </div>
        </header>
        <div class="pp-panel-body">
            <div class="pp-info-grid">
                <div class="pp-info-card">
                    <span class="pp-info-icon"><i class="bi bi-person-fill"></i></span>
                    <span class="pp-info-copy">
                        <span class="pp-info-label">Nom complet</span>
                        <strong class="pp-info-value">{{ $professor->name }}</strong>
                    </span>
                </div>
                <div class="pp-info-card">
                    <span class="pp-info-icon"><i class="bi bi-envelope-fill"></i></span>
                    <span class="pp-info-copy">
                        <span class="pp-info-label">Adresse email</span>
                        <strong class="pp-info-value">{{ $professor->email }}</strong>
                    </span>
                </div>
                <div class="pp-info-card">
                    <span class="pp-info-icon"><i class="bi bi-geo-alt-fill"></i></span>
                    <span class="pp-info-copy">
                        <span class="pp-info-label">Localisation</span>
                        <strong class="pp-info-value">{{ $professor->location ?: 'Non renseignée' }}</strong>
                    </span>
                </div>
                <div class="pp-info-card">
                    <span class="pp-info-icon"><i class="bi bi-calendar-check-fill"></i></span>
                    <span class="pp-info-copy">
                        <span class="pp-info-label">Membre depuis</span>
                        <strong class="pp-info-value">{{ $createdAt }}</strong>
                    </span>
                </div>
                <div class="pp-info-card">
                    <span class="pp-info-icon"><i class="bi bi-person-vcard-fill"></i></span>
                    <span class="pp-info-copy">
                        <span class="pp-info-label">Rôle</span>
                        <strong class="pp-info-value">Professeur</strong>
                    </span>
                </div>
                <div class="pp-info-card">
                    <span class="pp-info-icon"><i class="bi bi-shield-lock-fill"></i></span>
                    <span class="pp-info-copy">
                        <span class="pp-info-label">Sécurité</span>
                        <strong class="pp-info-value">Mot de passe protégé</strong>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <aside class="pp-panel">
        <header class="pp-panel-head">
            <div class="pp-panel-title-wrap">
                <h2 class="pp-panel-title"><i class="bi bi-lightning-charge-fill"></i> Accès rapides</h2>
                <p class="pp-panel-subtitle">Rejoignez les sections principales.</p>
            </div>
        </header>
        <div class="pp-panel-body">
            <div class="pp-profile-links">
                <a href="{{ route('prof.subjects.list') }}" class="pp-profile-link">
                    <span><i class="bi bi-journals"></i> Mes matières</span><i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('prof.schedule') }}" class="pp-profile-link">
                    <span><i class="bi bi-calendar3-week"></i> Emploi du temps</span><i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('prof.assignments') }}" class="pp-profile-link">
                    <span><i class="bi bi-journal-check"></i> Copies des étudiants</span><i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('prof.settings') }}" class="pp-profile-link">
                    <span><i class="bi bi-sliders2"></i> Paramètres du compte</span><i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </aside>
</div>
@endsection
