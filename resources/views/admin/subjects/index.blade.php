@extends('layouts.admin')

@section('title', 'Gestion des Matières')
@section('page_title', 'Matières')
@section('breadcrumb', 'Matières')

@section('content')

<style>
@keyframes subjFadeIn {
    from {
        opacity: 0;
        transform: translateY(24px) scale(0.96);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.subject-cards-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(260px, 310px));
    justify-content: center;
    align-items: stretch;
    gap: 20px;
    width: 100%;
}

.subject-card-outer {
    min-width: 0;
    opacity: 0;
    animation:
        subjFadeIn 0.55s
        cubic-bezier(0.175, 0.885, 0.32, 1.275)
        forwards;
    will-change: transform, opacity;
}

.subject-card-outer:nth-child(1) {
    animation-delay: 0.05s;
}

.subject-card-outer:nth-child(2) {
    animation-delay: 0.12s;
}

.subject-card-link,
.subject-card-link:hover {
    display: block;
    height: 100%;
    color: inherit;
    text-decoration: none;
}

.subject-admin-card {
    height: 100%;
    overflow: hidden;
    cursor: pointer;
    transition:
        transform 0.25s ease,
        border-color 0.25s ease,
        box-shadow 0.25s ease;
}

.subject-admin-card:hover {
    transform: translateY(-5px);
    border-color: rgba(96, 165, 250, 0.28);
    box-shadow: 0 22px 48px rgba(0, 0, 0, 0.24);
}

.subject-card-cover {
    position: relative;
    height: 96px;
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
    background: rgba(255, 255, 255, 0.08);
}

.subject-card-cover::before {
    width: 125px;
    height: 125px;
    top: -62px;
    right: -38px;
}

.subject-card-cover::after {
    width: 82px;
    height: 82px;
    bottom: -42px;
    left: -26px;
}

.subject-card-icon {
    position: relative;
    z-index: 1;
    color: rgba(255, 255, 255, 0.74);
    font-size: 2.5rem;
}

.subject-card-body {
    padding: 1.15rem;
    text-align: center;
}

.subject-type-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.55rem;
    padding: 4px 12px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.06);
    color: var(--adm-text-muted);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.subject-card-title {
    margin: 0 0 0.85rem;
    color: rgba(255, 255, 255, 0.94);
    font-size: 1.08rem;
    font-weight: 800;
}

.subject-statistics {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 1rem;
}

.subject-stat {
    padding: 11px 8px;
    border: 1px solid rgba(148, 163, 184, 0.13);
    border-radius: 13px;
    background: rgba(255, 255, 255, 0.025);
}

.subject-stat-value {
    display: block;
    color: rgba(255, 255, 255, 0.96);
    font-size: 1.22rem;
    font-weight: 800;
    line-height: 1;
}

.subject-stat-label {
    display: block;
    margin-top: 6px;
    color: var(--adm-text-muted);
    font-size: 0.7rem;
}

.subject-stat-label i {
    margin-right: 4px;
}

.subject-structure-note {
    margin: -0.2rem 0 0.9rem;
    color: rgba(148, 163, 184, 0.86);
    font-size: 0.72rem;
    line-height: 1.5;
}

.subject-action {
    display: inline-flex;
    width: 100%;
    min-height: 39px;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: 0;
    border-radius: 10px;
    color: #fff;
    font-size: 0.8rem;
    font-weight: 750;
}

@media (prefers-reduced-motion: reduce) {
    .subject-card-outer {
        animation: none;
        opacity: 1;
    }
}

@media (max-width: 700px) {
    .subject-cards-grid {
        grid-template-columns: minmax(0, 390px);
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

            $classLabel =
                $classCount === 1 ? 'classe' : 'classes';
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