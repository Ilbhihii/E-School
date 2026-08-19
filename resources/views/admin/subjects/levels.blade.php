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



.level-card-visibility-actions {
    position: absolute;
    top: 52px;
    right: 10px;
    z-index: 8;
    display: flex;
    gap: 5px;
}

.level-card-visibility-actions form {
    margin: 0;
}

.level-visibility-btn {
    min-height: 27px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 7px;
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 8px;
    color: rgba(255,255,255,.72);
    background: rgba(8,15,29,.52);
    backdrop-filter: blur(8px);
    font-size: .5rem;
    font-weight: 850;
    cursor: pointer;
}

.level-visibility-btn.is-activate.is-current {
    color: #86efac;
    border-color: rgba(34,197,94,.35);
    background: rgba(22,101,52,.56);
}

.level-visibility-btn.is-hide.is-current {
    color: #fde68a;
    border-color: rgba(245,158,11,.35);
    background: rgba(120,53,15,.56);
}

.level-visibility-btn:not(.is-current) {
    opacity: .66;
}

.level-visibility-badge {
    position: absolute;
    left: 12px;
    bottom: 10px;
    z-index: 4;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px;
    border-radius: 999px;
    font-size: .52rem;
    font-weight: 850;
    backdrop-filter: blur(8px);
}

.level-visibility-badge.is-active {
    color: #bbf7d0;
    border: 1px solid rgba(34,197,94,.28);
    background: rgba(22,101,52,.62);
}

.level-visibility-badge.is-hidden {
    color: #fde68a;
    border: 1px solid rgba(245,158,11,.30);
    background: rgba(120,53,15,.64);
}

.level-card-wrapper {
    position: relative;
}

.level-card-admin-actions {
    position: absolute;
    z-index: 6;
    top: 12px;
    right: 12px;
    display: flex;
    gap: 7px;
}

.level-card-admin-btn {
    width: 34px;
    height: 34px;
    display: inline-grid;
    place-items: center;
    border: 1px solid rgba(255,255,255,.20);
    border-radius: 10px;
    color: #fff;
    cursor: pointer;
    background: rgba(8,15,30,.35);
    backdrop-filter: blur(8px);
    text-decoration: none;
}

.level-card-admin-btn:hover {
    color: #fff;
    background: rgba(8,15,30,.60);
}

.level-card-admin-btn.is-danger {
    color: #fecdd3;
    border-color: rgba(244,63,94,.28);
}

.level-card-admin-actions form {
    margin: 0;
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

.subject-add-panel {
    margin-bottom: 1.35rem;
    padding: 1.2rem;
    border: 1px solid rgba(99,102,241,.22);
    border-radius: 16px;
    background:
        linear-gradient(
            145deg,
            rgba(20,30,52,.96),
            rgba(10,18,34,.98)
        );
}

.subject-add-panel[hidden] {
    display: none !important;
}

.subject-add-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
}

.subject-add-panel-head h3 {
    margin: 0;
    color: rgba(255,255,255,.96);
    font-size: .95rem;
    font-weight: 820;
}

.subject-add-form-grid {
    display: grid;
    grid-template-columns: minmax(0,1fr) minmax(0,1.35fr) auto;
    align-items: end;
    gap: 12px;
}

.subject-add-field label {
    display: block;
    margin-bottom: 6px;
    color: rgba(255,255,255,.78);
    font-size: .7rem;
    font-weight: 740;
}

.subject-add-control {
    width: 100%;
    min-height: 42px;
    padding: 9px 11px;
    border: 1px solid rgba(148,163,184,.16);
    border-radius: 10px;
    outline: none;
    color: #f8fafc;
    background: rgba(8,15,29,.78);
    font: inherit;
    font-size: .78rem;
}

.subject-add-control:focus {
    border-color: rgba(99,102,241,.6);
    box-shadow: 0 0 0 3px rgba(99,102,241,.10);
}

.subject-add-error {
    margin-top: 6px;
    color: #fda4af;
    font-size: .68rem;
}

@media (max-width: 850px) {
    .subject-add-form-grid {
        grid-template-columns: 1fr;
    }
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
        <button
            type="button"
            class="adm-btn adm-btn-primary"
            data-toggle-add-level
        >
            <i class="bi bi-plus-circle"></i>
            Ajouter un niveau
        </button>

        <a
            href="{{ route('admin.subjects.index') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Retour aux matières
        </a>
    </div>
</div>

<div
    class="subject-add-panel"
    id="addLevelPanel"
    @if(old('_form') !== 'level') hidden @endif
>
    <div class="subject-add-panel-head">
        <h3>
            <i class="bi bi-plus-circle me-1"></i>
            Ajouter un niveau à {{ $subject->name }}
        </h3>

        <button
            type="button"
            class="adm-btn adm-btn-ghost"
            data-close-add-level
        >
            <i class="bi bi-x-lg"></i>
            Fermer
        </button>
    </div>

    <form
        method="POST"
        action="{{ route('admin.subjects.levels.store', $subject) }}"
    >
        @csrf
        <input type="hidden" name="_form" value="level">

        <div class="subject-add-form-grid">
            <div class="subject-add-field">
                <label for="newLevelName">
                    Nom du niveau *
                </label>

                <input
                    id="newLevelName"
                    type="text"
                    name="name"
                    class="subject-add-control"
                    value="{{ old('_form') === 'level' ? old('name') : '' }}"
                    placeholder="Ex. Débutant"
                    maxlength="120"
                    required
                >

                @if(old('_form') === 'level')
                    @error('name')
                        <div class="subject-add-error">
                            {{ $message }}
                        </div>
                    @enderror
                @endif
            </div>

            <div class="subject-add-field">
                <label for="newLevelDescription">
                    Description
                </label>

                <input
                    id="newLevelDescription"
                    type="text"
                    name="description"
                    class="subject-add-control"
                    value="{{ old('_form') === 'level' ? old('description') : '' }}"
                    placeholder="Description optionnelle"
                    maxlength="500"
                >
            </div>

            <button
                type="submit"
                class="adm-btn adm-btn-primary"
            >
                <i class="bi bi-check-lg"></i>
                Ajouter
            </button>
        </div>
    </form>
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

            <button
                type="button"
                class="adm-btn adm-btn-primary mt-3"
                data-toggle-add-level
            >
                <i class="bi bi-plus-circle"></i>
                Ajouter le premier niveau
            </button>
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
                <div class="level-card-admin-actions">
                    <a
                        href="{{ route('admin.subjects.levels.edit', [$subject, $level]) }}"
                        class="level-card-admin-btn"
                        title="Modifier {{ $level->name }}"
                    >
                        <i class="bi bi-pencil-square"></i>
                    </a>

                    <form
                        method="POST"
                        action="{{ route('admin.subjects.levels.destroy', [$subject, $level]) }}"
                        onsubmit="return confirm('Supprimer le niveau {{ addslashes($level->name) }} ? Supprimez d’abord ses classes s’il en contient.');"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="level-card-admin-btn is-danger"
                            title="Supprimer {{ $level->name }}"
                        >
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>

                @php
                    $levelIsActive = (bool) ($level->is_active ?? true);
                @endphp

                <div class="level-card-visibility-actions">
                    <form
                        method="POST"
                        action="{{ route('admin.subjects.levels.update', [$subject, $level]) }}"
                    >
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="_visibility_only" value="1">
                        <input type="hidden" name="is_active" value="1">
                        <button
                            type="submit"
                            class="level-visibility-btn is-activate {{ $levelIsActive ? 'is-current' : '' }}"
                            title="Activer {{ $level->name }}"
                        >
                            <i class="bi bi-eye-fill"></i>
                            Activer
                        </button>
                    </form>

                    <form
                        method="POST"
                        action="{{ route('admin.subjects.levels.update', [$subject, $level]) }}"
                    >
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="_visibility_only" value="1">
                        <input type="hidden" name="is_active" value="0">
                        <button
                            type="submit"
                            class="level-visibility-btn is-hide {{ !$levelIsActive ? 'is-current' : '' }}"
                            title="Masquer {{ $level->name }}"
                        >
                            <i class="bi bi-eye-slash-fill"></i>
                            Masquer
                        </button>
                    </form>
                </div>

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
                            <span class="level-visibility-badge {{ $levelIsActive ? 'is-active' : 'is-hidden' }}">
                                <i class="bi {{ $levelIsActive ? 'bi-check-circle-fill' : 'bi-eye-slash-fill' }}"></i>
                                {{ $levelIsActive ? 'Active' : 'Masquée' }}
                            </span>

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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const panel = document.getElementById('addLevelPanel');
    const openButtons = document.querySelectorAll('[data-toggle-add-level]');
    const closeButton = document.querySelector('[data-close-add-level]');

    openButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            panel.hidden = false;
            panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const input = document.getElementById('newLevelName');
            if (input) {
                window.setTimeout(function () { input.focus(); }, 250);
            }
        });
    });

    if (closeButton) {
        closeButton.addEventListener('click', function () {
            panel.hidden = true;
        });
    }
});
</script>

@endsection
