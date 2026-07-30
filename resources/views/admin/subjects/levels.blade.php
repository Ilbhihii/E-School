@extends('layouts.admin')

@section('title', 'Niveaux - ' . $subject->name)
@section('page_title', $subject->name)
@section('breadcrumb', 'Matières → Niveaux')

@section('content')

@php
    $normalizedSubjectName =
        \App\Models\VocalTestPrompt::normalizePathName(
            $subject->name
        );

    $isHighSchoolSupport =
        (bool) (
            $subject->is_high_school_support
            ?? (
                $normalizedSubjectName === 'soutien lycee'
            )
        );

    $subjectDesign = match ($normalizedSubjectName) {
        'arabe' => [
            'icon' => 'bi-translate',
            'gradient' =>
                'linear-gradient(135deg,#2563EB,#06B6D4)',
            'soft' => 'rgba(37,99,235,0.12)',
            'border' => 'rgba(59,130,246,0.2)',
            'accent' => '#38BDF8',
        ],

        'coran', 'quran', 'القران' => [
            'icon' => 'bi-book-half',
            'gradient' =>
                'linear-gradient(135deg,#7C3AED,#A855F7)',
            'soft' => 'rgba(124,58,237,0.12)',
            'border' => 'rgba(168,85,247,0.2)',
            'accent' => '#C084FC',
        ],

        'soutien lycee' => [
            'icon' => 'bi-journal-bookmark-fill',
            'gradient' =>
                'linear-gradient(135deg,#4F46E5,#7C3AED)',
            'soft' => 'rgba(79,70,229,0.12)',
            'border' => 'rgba(124,58,237,0.2)',
            'accent' => '#A78BFA',
        ],

        default => [
            'icon' => 'bi-layers-fill',
            'gradient' =>
                'linear-gradient(135deg,#0F766E,#14B8A6)',
            'soft' => 'rgba(20,184,166,0.12)',
            'border' => 'rgba(45,212,191,0.2)',
            'accent' => '#2DD4BF',
        ],
    };

    $totalItems = $levels->sum(
        fn ($level) => $level->classes->count()
    );

    $itemLabel = $isHighSchoolSupport
        ? (
            $totalItems === 1
                ? 'matière'
                : 'matières'
        )
        : (
            $totalItems === 1
                ? 'classe'
                : 'classes'
        );

    $pageSubtitle = $isHighSchoolSupport
        ? 'Sélectionnez le niveau BAC pour gérer ses matières.'
        : 'Sélectionnez un parcours pour gérer ses niveaux et ses cours.';
@endphp

<style>
@keyframes levelCardReveal {
    from {
        opacity: 0;
        transform: translateY(18px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.subject-levels-summary {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    gap: 20px;
    margin-bottom: 1.5rem;
    padding: 1.35rem 1.45rem;
    border: 1px solid rgba(255,255,255,0.065);
    border-radius: 18px;
    background:
        linear-gradient(
            145deg,
            rgba(15,23,42,0.94),
            rgba(8,16,31,0.97)
        );
}

.subject-levels-summary-main {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
}

.subject-levels-summary-icon {
    width: 58px;
    height: 58px;
    flex: 0 0 58px;
    display: grid;
    place-items: center;
    border-radius: 16px;
    color: #ffffff;
    font-size: 1.45rem;
    box-shadow: 0 12px 26px rgba(0,0,0,0.18);
}

.subject-levels-summary h2 {
    margin: 0 0 5px;
    color: rgba(255,255,255,0.96);
    font-size: 1.08rem;
    font-weight: 800;
}

.subject-levels-summary p {
    margin: 0;
    color: var(--adm-text-muted);
    font-size: 0.82rem;
    line-height: 1.55;
}

.subject-levels-summary-stats {
    display: flex;
    align-items: stretch;
    gap: 10px;
}

.subject-levels-summary-stat {
    min-width: 102px;
    padding: 11px 14px;
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 13px;
    text-align: center;
    background: rgba(255,255,255,0.025);
}

.subject-levels-summary-stat strong {
    display: block;
    color: rgba(255,255,255,0.96);
    font-size: 1.25rem;
    font-weight: 850;
    line-height: 1;
}

.subject-levels-summary-stat span {
    display: block;
    margin-top: 6px;
    color: var(--adm-text-muted);
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.045em;
    text-transform: uppercase;
}

.level-cards-grid {
    width: min(100%, 1050px);
    margin: 0 auto;
    display: grid;
    grid-template-columns:
        repeat(
            auto-fit,
            minmax(min(100%, 280px), 330px)
        );
    justify-content: center;
    align-items: stretch;
    gap: 20px;
}

.level-card-wrapper {
    min-width: 0;
    height: 100%;
    opacity: 0;
    animation:
        levelCardReveal 0.42s ease forwards;
}

.level-card-wrapper:nth-child(1) {
    animation-delay: 0.04s;
}

.level-card-wrapper:nth-child(2) {
    animation-delay: 0.1s;
}

.level-card-wrapper:nth-child(3) {
    animation-delay: 0.16s;
}

.level-card-link,
.level-card-link:hover {
    display: block;
    width: 100%;
    height: 100%;
    color: inherit;
    text-decoration: none;
}

.level-admin-card {
    width: 100%;
    min-height: 315px;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-radius: 19px;
    cursor: pointer;
    transition:
        transform 0.25s ease,
        border-color 0.25s ease,
        box-shadow 0.25s ease;
}

.level-admin-card:hover {
    transform: translateY(-5px);
    border-color: var(--level-border);
    box-shadow: 0 22px 48px rgba(0,0,0,0.27);
}

.level-card-cover {
    position: relative;
    height: 106px;
    flex: 0 0 106px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.level-card-cover::before,
.level-card-cover::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.09);
}

.level-card-cover::before {
    width: 140px;
    height: 140px;
    top: -78px;
    right: -38px;
}

.level-card-cover::after {
    width: 86px;
    height: 86px;
    left: -26px;
    bottom: -47px;
}

.level-card-icon {
    position: relative;
    z-index: 1;
    color: rgba(255,255,255,0.88);
    font-size: 2.45rem;
    filter: drop-shadow(0 8px 18px rgba(0,0,0,0.2));
}

.level-card-body {
    min-height: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1.2rem;
    text-align: center;
}

.level-card-subject {
    min-height: 23px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    align-self: center;
    margin-bottom: 0.6rem;
    padding: 4px 11px;
    border: 1px solid rgba(255,255,255,0.04);
    border-radius: 999px;
    color: var(--level-accent);
    background: var(--level-soft);
    font-size: 0.65rem;
    font-weight: 780;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.level-card-title {
    min-height: 49px;
    margin: 0 0 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.96);
    font-size: 1.05rem;
    font-weight: 820;
    line-height: 1.4;
}

.level-card-stat {
    min-height: 66px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 11px;
    margin-bottom: 1rem;
    padding: 11px;
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 13px;
    background: rgba(255,255,255,0.025);
}

.level-card-stat-icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    color: var(--level-accent);
    background: var(--level-soft);
    font-size: 0.95rem;
}

.level-card-stat-content {
    text-align: left;
}

.level-card-stat-content strong {
    display: block;
    color: rgba(255,255,255,0.95);
    font-size: 1.12rem;
    font-weight: 850;
    line-height: 1;
}

.level-card-stat-content span {
    display: block;
    margin-top: 5px;
    color: var(--adm-text-muted);
    font-size: 0.7rem;
}

.level-card-action {
    width: 100%;
    min-height: 41px;
    margin-top: auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border-radius: 11px;
    color: #ffffff;
    font-size: 0.77rem;
    font-weight: 780;
    box-shadow: 0 9px 22px rgba(0,0,0,0.14);
}

.level-card-action i {
    transition: transform 0.22s ease;
}

.level-admin-card:hover .level-card-action i {
    transform: translateX(4px);
}

@media (prefers-reduced-motion: reduce) {
    .level-card-wrapper {
        opacity: 1;
        animation: none;
    }

    .level-admin-card,
    .level-card-action i {
        transition: none;
    }
}

@media (max-width: 800px) {
    .subject-levels-summary {
        grid-template-columns: 1fr;
    }

    .subject-levels-summary-stats {
        width: 100%;
    }

    .subject-levels-summary-stat {
        flex: 1;
    }
}

@media (max-width: 575px) {
    .subject-levels-summary-main {
        align-items: flex-start;
    }

    .subject-levels-summary-icon {
        width: 50px;
        height: 50px;
        flex-basis: 50px;
        border-radius: 14px;
    }

    .level-cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="adm-page-header">
    <div>
        <div
            style="
                display:flex;
                align-items:center;
                flex-wrap:wrap;
                gap:8px;
                margin-bottom:6px;
                font-size:0.8rem;
                color:var(--adm-text-muted);
            "
        >
            <a
                href="{{ route('admin.subjects.index') }}"
                style="
                    color:var(--adm-text-muted);
                    text-decoration:none;
                "
            >
                <i class="bi bi-book me-1"></i>
                Matières
            </a>

            <span>/</span>

            <span
                style="
                    color:rgba(255,255,255,0.65);
                    font-weight:600;
                "
            >
                {{ $subject->name }}
            </span>
        </div>

        <h1>
            <i
                class="bi {{ $subjectDesign['icon'] }} me-2"
                style="color:{{ $subjectDesign['accent'] }};"
            ></i>

            {{ $isHighSchoolSupport
                ? 'Niveau — ' . $subject->name
                : 'Parcours — ' . $subject->name }}
        </h1>

        <div class="subtitle">
            {{ $pageSubtitle }}
        </div>
    </div>

    <div class="page-actions">
        <a
            href="{{ route('admin.subjects.index') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Retour aux matières
        </a>
    </div>
</div>

<div class="subject-levels-summary">
    <div class="subject-levels-summary-main">
        <div
            class="subject-levels-summary-icon"
            style="background:{{ $subjectDesign['gradient'] }};"
        >
            <i class="bi {{ $subjectDesign['icon'] }}"></i>
        </div>

        <div>
            <h2>{{ $subject->name }}</h2>

            <p>
                {{ $isHighSchoolSupport
                    ? 'Structure active : BAC → Mathématiques et Physique-Chimie.'
                    : 'Sélectionnez un parcours pour afficher ses classes et ses cours.' }}
            </p>
        </div>
    </div>

    <div class="subject-levels-summary-stats">
        <div class="subject-levels-summary-stat">
            <strong>{{ $levels->count() }}</strong>

            <span>
                {{ $levels->count() === 1
                    ? 'parcours'
                    : 'parcours' }}
            </span>
        </div>

        <div class="subject-levels-summary-stat">
            <strong>{{ $totalItems }}</strong>
            <span>{{ $itemLabel }}</span>
        </div>
    </div>
</div>

@if($levels->isEmpty())
    <div class="adm-card">
        <div class="adm-empty" style="padding:4rem 2rem;">
            <div class="adm-empty-icon">
                <i class="bi bi-layers"></i>
            </div>

            <h5>Aucun niveau disponible</h5>

            <p>
                Aucun parcours actif n’est associé à
                {{ $subject->name }}.
            </p>
        </div>
    </div>
@else
    <div class="level-cards-grid">
        @foreach($levels as $level)
            @php
                $itemCount = $level->classes->count();

                $itemCountLabel = $isHighSchoolSupport
                    ? (
                        $itemCount === 1
                            ? 'matière disponible'
                            : 'matières disponibles'
                    )
                    : (
                        $itemCount === 1
                            ? 'classe disponible'
                            : 'classes disponibles'
                    );

                $actionLabel = $isHighSchoolSupport
                    ? 'Voir les matières'
                    : 'Voir les classes';
            @endphp

            <div class="level-card-wrapper">
                <a
                    href="{{
                        route(
                            'admin.subjects.classes',
                            [$subject, $level]
                        )
                    }}"
                    class="level-card-link"
                    aria-label="{{
                        $actionLabel . ' de ' . $level->name
                    }}"
                >
                    <article
                        class="adm-card level-admin-card"
                        style="
                            --level-soft:
                                {{ $subjectDesign['soft'] }};
                            --level-border:
                                {{ $subjectDesign['border'] }};
                            --level-accent:
                                {{ $subjectDesign['accent'] }};
                        "
                    >
                        <div
                            class="level-card-cover"
                            style="
                                background:
                                    {{ $subjectDesign['gradient'] }};
                            "
                        >
                            <i
                                class="bi {{
                                    $isHighSchoolSupport
                                        ? 'bi-mortarboard-fill'
                                        : 'bi-layers-fill'
                                }} level-card-icon"
                            ></i>
                        </div>

                        <div class="level-card-body">
                            <span class="level-card-subject">
                                {{ $subject->name }}
                            </span>

                            <h2 class="level-card-title">
                                {{ $level->name }}
                            </h2>

                            <div class="level-card-stat">
                                <div class="level-card-stat-icon">
                                    <i
                                        class="bi {{
                                            $isHighSchoolSupport
                                                ? 'bi-journal-bookmark'
                                                : 'bi-people'
                                        }}"
                                    ></i>
                                </div>

                                <div class="level-card-stat-content">
                                    <strong>{{ $itemCount }}</strong>
                                    <span>{{ $itemCountLabel }}</span>
                                </div>
                            </div>

                            <span
                                class="level-card-action"
                                style="
                                    background:
                                        {{ $subjectDesign['gradient'] }};
                                "
                            >
                                {{ $actionLabel }}
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </article>
                </a>
            </div>
        @endforeach
    </div>
@endif

@endsection
