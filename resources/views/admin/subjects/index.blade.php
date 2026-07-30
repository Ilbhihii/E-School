@extends('layouts.admin')

@section('title', 'Gestion des Matières')
@section('page_title', 'Matières')
@section('breadcrumb', 'Matières')

@section('content')

<style>
@keyframes subjFadeIn {
    from {
        opacity: 0;
        transform: translateY(18px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* =========================================================
   GRILLE DES MATIÈRES
   ========================================================= */

.subject-cards-grid {
    width: min(100%, 1080px);
    margin: 1.25rem auto 0;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    align-items: stretch;
    gap: 20px;
}

.subject-card-outer {
    min-width: 0;
    height: 100%;
    opacity: 0;
    animation:
        subjFadeIn 0.45s ease forwards;
}

.subject-card-outer:nth-child(1) {
    animation-delay: 0.04s;
}

.subject-card-outer:nth-child(2) {
    animation-delay: 0.1s;
}

.subject-card-outer:nth-child(3) {
    animation-delay: 0.16s;
}

.subject-card-link,
.subject-card-link:hover {
    display: block;
    width: 100%;
    height: 100%;
    color: inherit;
    text-decoration: none;
}

/* =========================================================
   CARTE
   ========================================================= */

.subject-admin-card {
    width: 100%;
    min-height: 390px;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    cursor: pointer;
    border-radius: 20px;
    transition:
        transform 0.26s ease,
        border-color 0.26s ease,
        box-shadow 0.26s ease;
}

.subject-admin-card:hover {
    transform: translateY(-6px);
    border-color: rgba(96,165,250,0.26);
    box-shadow:
        0 24px 54px rgba(0,0,0,0.3);
}

/* =========================================================
   COUVERTURE
   ========================================================= */

.subject-card-cover {
    position: relative;
    height: 118px;
    flex: 0 0 118px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.subject-card-cover::before,
.subject-card-cover::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.09);
}

.subject-card-cover::before {
    width: 150px;
    height: 150px;
    top: -82px;
    right: -42px;
}

.subject-card-cover::after {
    width: 92px;
    height: 92px;
    left: -30px;
    bottom: -50px;
}

.subject-card-icon {
    position: relative;
    z-index: 1;
    color: rgba(255,255,255,0.88);
    font-size: 2.65rem;
    filter:
        drop-shadow(
            0 8px 18px rgba(0,0,0,0.18)
        );
}

/* =========================================================
   CONTENU
   ========================================================= */

.subject-card-body {
    min-height: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1.2rem;
    text-align: center;
}

.subject-type-badge {
    min-height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    align-self: center;
    margin-bottom: 0.6rem;
    padding: 4px 12px;
    border: 1px solid rgba(255,255,255,0.035);
    border-radius: 999px;
    background: rgba(255,255,255,0.06);
    color: var(--adm-text-muted);
    font-size: 0.66rem;
    font-weight: 750;
    letter-spacing: 0.065em;
    text-transform: uppercase;
}

.subject-card-title {
    min-height: 31px;
    margin: 0 0 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.96);
    font-size: 1.08rem;
    font-weight: 820;
    line-height: 1.35;
}

.subject-statistics {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 9px;
    margin-bottom: 0.9rem;
}

.subject-stat {
    min-height: 68px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 10px 8px;
    border: 1px solid rgba(148,163,184,0.14);
    border-radius: 13px;
    background: rgba(255,255,255,0.026);
}

.subject-stat-value {
    display: block;
    color: rgba(255,255,255,0.97);
    font-size: 1.3rem;
    font-weight: 850;
    line-height: 1;
}

.subject-stat-label {
    display: block;
    margin-top: 6px;
    color: var(--adm-text-muted);
    font-size: 0.68rem;
    line-height: 1.25;
}

.subject-stat-label i {
    margin-right: 4px;
}

.subject-structure-note {
    min-height: 42px;
    margin: 0 0 0.95rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(148,163,184,0.88);
    font-size: 0.7rem;
    line-height: 1.5;
}

/* Le bouton reste toujours au même niveau */
.subject-action {
    width: 100%;
    min-height: 42px;
    margin-top: auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 0;
    border-radius: 11px;
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 780;
    box-shadow:
        0 9px 22px rgba(0,0,0,0.13);
    transition:
        transform 0.22s ease,
        filter 0.22s ease;
}

.subject-admin-card:hover .subject-action {
    filter: brightness(1.06);
}

.subject-action i {
    transition: transform 0.22s ease;
}

.subject-admin-card:hover .subject-action i {
    transform: translateX(4px);
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (prefers-reduced-motion: reduce) {
    .subject-card-outer {
        opacity: 1;
        animation: none;
    }

    .subject-admin-card,
    .subject-action,
    .subject-action i {
        transition: none;
    }
}

@media (max-width: 980px) {
    .subject-cards-grid {
        width: min(100%, 720px);
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 650px) {
    .subject-cards-grid {
        width: min(100%, 410px);
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .subject-admin-card {
        min-height: 370px;
    }

    .subject-card-cover {
        height: 108px;
        flex-basis: 108px;
    }
}
</style>

<div class="adm-page-header">
    <div>
        <div
            style="
                display:flex;
                align-items:center;
                gap:8px;
                margin-bottom:6px;
                font-size:0.8rem;
                color:var(--adm-text-muted);
            "
        >
            <span
                style="
                    font-weight:600;
                    color:rgba(255,255,255,0.6);
                "
            >
                Matières
            </span>

            <span>/</span>

            <span style="color:var(--adm-text-muted);">
                Parcours → Niveaux → Cours
            </span>
        </div>

        <h1>
            <i
                class="bi bi-book me-2"
                style="color:var(--adm-primary);"
            ></i>
            Gestion des Matières
        </h1>

        <div class="subtitle">
            Sélectionnez une matière pour parcourir sa structure active
        </div>
    </div>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('info'))
    <div
        class="adm-alert mb-4"
        style="
            background:rgba(6,182,212,0.1);
            border:1px solid rgba(6,182,212,0.2);
            color:#67E8F9;
            border-radius:12px;
            padding:12px 16px;
        "
    >
        <i class="bi bi-info-circle me-2"></i>
        {{ session('info') }}
    </div>
@endif

<div class="subject-cards-grid">
    @forelse($subjects as $subject)
        @php
            $normalizedName =
                \App\Models\VocalTestPrompt::normalizePathName(
                    $subject->name
                );

            $design = match ($normalizedName) {
                'arabe' => [
                    'icon' => 'bi-translate',
                    'gradient' =>
                        'linear-gradient(135deg,#2563EB,#06B6D4)',
                    'structure' =>
                        'Lecture & Écriture • Communication',
                ],

                'coran', 'quran', 'القران' => [
                    'icon' => 'bi-book-half',
                    'gradient' =>
                        'linear-gradient(135deg,#7C3AED,#A855F7)',
                    'structure' =>
                        'Apprentissage & Tajwid',
                ],

                'soutien lycee' => [
                    'icon' => 'bi-journal-bookmark-fill',
                    'gradient' =>
                        'linear-gradient(135deg,#4F46E5,#7C3AED)',
                    'structure' =>
                        'BAC • Mathématiques • Physique-Chimie',
                ],

                default => [
                    'icon' => 'bi-journal-bookmark-fill',
                    'gradient' =>
                        'linear-gradient(135deg,#4F46E5,#7C3AED)',
                    'structure' =>
                        'Structure pédagogique',
                ],
            };

            $levelCount = (int) (
                $subject->validated_level_count ?? 0
            );

            $classCount = (int) (
                $subject->validated_class_count ?? 0
            );

            $levelLabel =
                $levelCount === 1 ? 'niveau' : 'niveaux';

            $isHighSchoolSupport =
                (bool) (
                    $subject->is_high_school_support
                    ?? false
                );

            $classLabel = $isHighSchoolSupport
                ? (
                    $classCount === 1
                        ? 'matière'
                        : 'matières'
                )
                : (
                    $classCount === 1
                        ? 'classe'
                        : 'classes'
                );
        @endphp

        <div class="subject-card-outer">
            <a
                href="{{ route('admin.subjects.levels', $subject) }}"
                class="subject-card-link"
                aria-label="Voir les niveaux de {{ $subject->name }}"
            >
                <article class="adm-card subject-admin-card">
                    <div
                        class="subject-card-cover"
                        style="background:{{ $design['gradient'] }};"
                    >
                        <i
                            class="bi {{ $design['icon'] }}
                                subject-card-icon"
                        ></i>
                    </div>

                    <div class="subject-card-body">
                        <span class="subject-type-badge">
                            {{ $subject->type ?? 'scolaire' }}
                        </span>

                        <h2 class="subject-card-title">
                            {{ $subject->name }}
                        </h2>

                        <div class="subject-statistics">
                            <div class="subject-stat">
                                <strong class="subject-stat-value">
                                    {{ $levelCount }}
                                </strong>

                                <span class="subject-stat-label">
                                    <i class="bi bi-layers"></i>
                                    {{ $levelLabel }}
                                </span>
                            </div>

                            <div class="subject-stat">
                                <strong class="subject-stat-value">
                                    {{ $classCount }}
                                </strong>

                                <span class="subject-stat-label">
                                    <i class="bi bi-mortarboard"></i>
                                    {{ $classLabel }}
                                </span>
                            </div>
                        </div>

                        <p class="subject-structure-note">
                            {{ $design['structure'] }}
                        </p>

                        <span
                            class="subject-action"
                            style="
                                background:
                                    {{ $design['gradient'] }};
                            "
                        >
                            <i class="bi bi-arrow-right"></i>
                            Voir les niveaux
                        </span>
                    </div>
                </article>
            </a>
        </div>
    @empty
        <div class="adm-card">
            <div class="adm-empty" style="padding:4rem 2rem;">
                <div class="adm-empty-icon">
                    <i class="bi bi-book"></i>
                </div>

                <h5>Aucune matière</h5>

                <p>
                    Les matières apparaîtront ici après leur création.
                </p>
            </div>
        </div>
    @endforelse
</div>

@endsection