@extends('layouts.admin')

@php
    $normalizedSubjectName = mb_strtolower(
        trim($subject->name ?? '')
    );

    $isHighSchoolSupport = in_array(
        $normalizedSubjectName,
        [
            'soutien lycée',
            'soutien lycee',
        ],
        true
    );

    $pageEntityName = $isHighSchoolSupport
        ? 'Matières du BAC'
        : 'Classes';

    $pageEntitySingular = $isHighSchoolSupport
        ? 'matière'
        : 'classe';
@endphp

@section(
    'title',
    $pageEntityName
        . ' - '
        . $subject->name
        . ' - '
        . $level->name
)

@section(
    'page_title',
    $level->name
        . ' — '
        . $subject->name
)

@section(
    'breadcrumb',
    $isHighSchoolSupport
        ? 'Matières → Niveaux → Matières du BAC'
        : 'Matières → Niveaux → Classes'
)

@section('content')

<div class="adm-page-header subjects-classes-header">
    <div>
        <div class="subject-class-breadcrumb">
            <a href="{{ route('admin.subjects.index') }}">
                <i class="bi bi-book me-1"></i>
                Matières
            </a>

            <i class="bi bi-chevron-right"></i>

            <a
                href="{{
                    route(
                        'admin.subjects.levels',
                        $subject
                    )
                }}"
            >
                {{ $subject->name }}
            </a>

            <i class="bi bi-chevron-right"></i>

            <span>{{ $level->name }}</span>
        </div>

        <h1>
            <span class="subject-page-icon">
                <i
                    class="bi {{
                        $isHighSchoolSupport
                            ? 'bi-journal-bookmark-fill'
                            : 'bi-building'
                    }}"
                ></i>
            </span>

            {{ $pageEntityName }}
            <span class="subject-page-separator">—</span>
            {{ $subject->name }}
            <span class="subject-page-dot">·</span>
            {{ $level->name }}
        </h1>

        <div class="subtitle">
            Sélectionnez une {{ $pageEntitySingular }}
            pour consulter et gérer ses cours.
        </div>
    </div>

    <div class="page-actions">
        <a
            href="{{ route('admin.subjects.classes.create', [$subject, $level]) }}"
            class="adm-btn adm-btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Nouvelle classe
        </a>

        <a
            href="{{
                route(
                    'admin.subjects.levels',
                    $subject
                )
            }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-arrow-left"></i>
            Retour aux niveaux
        </a>
    </div>
</div>

@if($classes->isEmpty())
    <div class="adm-card">
        <div class="adm-empty subject-empty-state">
            <div class="adm-empty-icon">
                <i
                    class="bi {{
                        $isHighSchoolSupport
                            ? 'bi-journal-x'
                            : 'bi-building'
                    }}"
                ></i>
            </div>

            <h5>
                Aucune {{ $pageEntitySingular }}
            </h5>

            <p>
                Aucune {{ $pageEntitySingular }} n’est liée à cette
                matière pour le niveau {{ $level->name }}.
            </p>
        </div>
    </div>
@else
    <div class="subject-class-grid">
        @foreach($classes as $class)
            @php
                $normalizedClassName = mb_strtolower(
                    trim($class->name ?? '')
                );

                $classTheme = match (true) {
                    str_contains(
                        $normalizedClassName,
                        'math'
                    ) => [
                        'icon' => 'bi-calculator-fill',
                        'eyebrow' => 'Sciences exactes',
                        'gradient' =>
                            'linear-gradient(135deg,#0EA5E9,#2563EB)',
                        'soft' => 'rgba(14,165,233,0.13)',
                        'border' => 'rgba(56,189,248,0.2)',
                        'accent' => '#38BDF8',
                    ],

                    str_contains(
                        $normalizedClassName,
                        'physique'
                    ) => [
                        'icon' => 'bi-atom',
                        'eyebrow' => 'Sciences physiques',
                        'gradient' =>
                            'linear-gradient(135deg,#7C3AED,#A855F7)',
                        'soft' => 'rgba(124,58,237,0.13)',
                        'border' => 'rgba(167,139,250,0.2)',
                        'accent' => '#A78BFA',
                    ],

                    str_contains(
                        $normalizedClassName,
                        'débutant'
                    )
                    || str_contains(
                        $normalizedClassName,
                        'debutant'
                    ) => [
                        'icon' => 'bi-1-circle-fill',
                        'eyebrow' => 'Niveau débutant',
                        'gradient' =>
                            'linear-gradient(135deg,#16A34A,#22C55E)',
                        'soft' => 'rgba(34,197,94,0.12)',
                        'border' => 'rgba(74,222,128,0.18)',
                        'accent' => '#4ADE80',
                    ],

                    str_contains(
                        $normalizedClassName,
                        'intermédiaire'
                    )
                    || str_contains(
                        $normalizedClassName,
                        'intermediaire'
                    ) => [
                        'icon' => 'bi-2-circle-fill',
                        'eyebrow' => 'Niveau intermédiaire',
                        'gradient' =>
                            'linear-gradient(135deg,#D97706,#F59E0B)',
                        'soft' => 'rgba(245,158,11,0.12)',
                        'border' => 'rgba(251,191,36,0.18)',
                        'accent' => '#FBBF24',
                    ],

                    str_contains(
                        $normalizedClassName,
                        'avancé'
                    )
                    || str_contains(
                        $normalizedClassName,
                        'avance'
                    ) => [
                        'icon' => 'bi-3-circle-fill',
                        'eyebrow' => 'Niveau avancé',
                        'gradient' =>
                            'linear-gradient(135deg,#DC2626,#EF4444)',
                        'soft' => 'rgba(239,68,68,0.12)',
                        'border' => 'rgba(248,113,113,0.18)',
                        'accent' => '#F87171',
                    ],

                    default => [
                        'icon' => 'bi-mortarboard-fill',
                        'eyebrow' => 'Parcours pédagogique',
                        'gradient' =>
                            'linear-gradient(135deg,#2563EB,#4F46E5)',
                        'soft' => 'rgba(37,99,235,0.12)',
                        'border' => 'rgba(96,165,250,0.18)',
                        'accent' => '#60A5FA',
                    ],
                };

                $courseCount = \App\Models\Course::query()
                    ->where('class_id', $class->id)
                    ->where('subject_id', $subject->id)
                    ->count();
            @endphp

            <article
                class="subject-class-card st-fade-up"
                style="
                    --class-gradient:
                        {{ $classTheme['gradient'] }};
                    --class-soft:
                        {{ $classTheme['soft'] }};
                    --class-border:
                        {{ $classTheme['border'] }};
                    --class-accent:
                        {{ $classTheme['accent'] }};
                "
            >
                <div class="subject-class-cover">
                    <div class="subject-class-cover-orb orb-one"></div>
                    <div class="subject-class-cover-orb orb-two"></div>

                    <div class="subject-class-main-icon">
                        <i
                            class="bi {{
                                $classTheme['icon']
                            }}"
                        ></i>
                    </div>

                    <span class="subject-class-level-badge">
                        {{ $level->name }}
                    </span>
                </div>

                <div class="subject-class-body">
                    <div class="subject-class-eyebrow">
                        {{ $classTheme['eyebrow'] }}
                    </div>

                    <h2 class="subject-class-title">
                        {{ $class->name }}
                    </h2>

                    <p class="subject-class-description">
                        {{
                            $isHighSchoolSupport
                                ? 'Consultez et organisez les cours de cette matière du BAC.'
                                : 'Consultez et organisez les cours associés à cette classe.'
                        }}
                    </p>

                    <div class="subject-class-stat">
                        <span class="subject-class-stat-icon">
                            <i class="bi bi-play-btn-fill"></i>
                        </span>

                        <div>
                            <strong>{{ $courseCount }}</strong>

                            <span>
                                {{
                                    $courseCount > 1
                                        ? 'cours disponibles'
                                        : 'cours disponible'
                                }}
                            </span>
                        </div>
                    </div>

                    <div class="subject-class-actions-row">

                        {{-- Voir les cours --}}
                        <a
                            href="{{
                                route(
                                    'admin.subjects.courses',
                                    [
                                        $subject,
                                        $level,
                                        $class,
                                    ]
                                )
                            }}"
                            class="subject-class-action"
                        >
                            <span>
                                <i class="bi bi-collection-play"></i>
                                Voir les cours
                            </span>

                            <i class="bi bi-arrow-right"></i>
                        </a>

                        {{-- Modifier --}}
                        <a
                            href="{{ route('admin.subjects.classes.edit', [$subject, $level, $class]) }}"
                            class="subject-class-icon-btn edit"
                            title="Modifier {{ $class->name }}"
                            aria-label="Modifier {{ $class->name }}"
                        >
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        {{-- Supprimer --}}
                        <form
                            method="POST"
                            action="{{
                                route(
                                    'admin.subjects.classes.destroy',
                                    [
                                        $subject,
                                        $level,
                                        $class,
                                    ]
                                )
                            }}"
                            class="subject-class-delete-form"
                            onsubmit="return confirm('Voulez-vous vraiment supprimer cette classe ?');"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="subject-class-icon-btn delete"
                                title="Supprimer {{ $class->name }}"
                                aria-label="Supprimer {{ $class->name }}"
                            >
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>

                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endif

<style>
/* =========================================================
   EN-TÊTE
   ========================================================= */

.subjects-classes-header {
    margin-bottom: 1.35rem;
}

.subject-class-breadcrumb {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
    margin-bottom: 8px;
    color: var(--adm-text-muted);
    font-size: 0.78rem;
}

.subject-class-breadcrumb a {
    color: var(--adm-text-muted);
    text-decoration: none;
    transition: color 0.2s ease;
}

.subject-class-breadcrumb a:hover {
    color: var(--adm-accent);
}

.subject-class-breadcrumb > i {
    font-size: 0.58rem;
    opacity: 0.55;
}

.subject-class-breadcrumb span {
    color: rgba(255,255,255,0.67);
    font-weight: 600;
}

.subjects-classes-header h1 {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
}

.subject-page-icon {
    width: 37px;
    height: 37px;
    display: inline-grid;
    place-items: center;
    margin-right: 2px;
    border: 1px solid rgba(59,130,246,0.18);
    border-radius: 11px;
    color: #60A5FA;
    background: rgba(37,99,235,0.11);
    font-size: 1rem;
}

.subject-page-separator,
.subject-page-dot {
    color: rgba(255,255,255,0.26);
}

/* =========================================================
   GRILLE
   ========================================================= */

.subject-class-grid {
    width: min(100%, 940px);
    display: grid;
    grid-template-columns:
        repeat(
            2,
            minmax(0, 1fr)
        );
    align-items: stretch;
    gap: 22px;
    margin: 0 auto;
}

/* =========================================================
   CARTE
   ========================================================= */

.subject-class-card {
    min-width: 0;
    min-height: 355px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid var(--class-border);
    border-radius: 22px;
    background:
        linear-gradient(
            150deg,
            rgba(17,27,47,0.98),
            rgba(9,17,32,0.99)
        );
    box-shadow:
        0 18px 44px rgba(0,0,0,0.23);
    transition:
        transform 0.28s ease,
        border-color 0.28s ease,
        box-shadow 0.28s ease;
}

.subject-class-card:hover {
    transform: translateY(-6px);
    border-color: var(--class-accent);
    box-shadow:
        0 26px 58px rgba(0,0,0,0.3);
}

.subject-class-cover {
    position: relative;
    height: 122px;
    flex: 0 0 122px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: var(--class-gradient);
}

.subject-class-cover::after {
    content: "";
    position: absolute;
    inset: auto 0 0;
    height: 48%;
    background:
        linear-gradient(
            180deg,
            transparent,
            rgba(4,10,22,0.12)
        );
}

.subject-class-cover-orb {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
}

.subject-class-cover-orb.orb-one {
    width: 150px;
    height: 150px;
    top: -88px;
    right: -30px;
}

.subject-class-cover-orb.orb-two {
    width: 96px;
    height: 96px;
    left: -35px;
    bottom: -55px;
}

.subject-class-main-icon {
    position: relative;
    z-index: 2;
    width: 62px;
    height: 62px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 19px;
    color: rgba(255,255,255,0.95);
    background: rgba(255,255,255,0.13);
    box-shadow:
        0 12px 28px rgba(0,0,0,0.18);
    backdrop-filter: blur(10px);
    font-size: 1.65rem;
}

.subject-class-level-badge {
    position: absolute;
    z-index: 3;
    top: 14px;
    right: 14px;
    padding: 5px 10px;
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 999px;
    color: rgba(255,255,255,0.92);
    background: rgba(7,15,30,0.18);
    font-size: 0.64rem;
    font-weight: 800;
    letter-spacing: 0.055em;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
}

.subject-class-body {
    min-height: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1.25rem;
}

.subject-class-eyebrow {
    margin-bottom: 0.4rem;
    color: var(--class-accent);
    font-size: 0.67rem;
    font-weight: 800;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.subject-class-title {
    min-height: 32px;
    margin: 0 0 0.45rem;
    color: rgba(255,255,255,0.96);
    font-size: 1.18rem;
    font-weight: 820;
    line-height: 1.35;
}

.subject-class-description {
    min-height: 46px;
    margin: 0 0 0.95rem;
    color: rgba(255,255,255,0.48);
    font-size: 0.78rem;
    line-height: 1.55;
}

.subject-class-stat {
    display: flex;
    align-items: center;
    gap: 11px;
    margin-bottom: 1rem;
    padding: 10px 11px;
    border: 1px solid var(--class-border);
    border-radius: 13px;
    background: var(--class-soft);
}

.subject-class-stat-icon {
    width: 36px;
    height: 36px;
    flex: 0 0 36px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    color: var(--class-accent);
    background: rgba(255,255,255,0.045);
    font-size: 0.9rem;
}

.subject-class-stat > div {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.subject-class-stat strong {
    color: rgba(255,255,255,0.95);
    font-size: 1rem;
    line-height: 1;
}

.subject-class-stat span {
    color: rgba(255,255,255,0.47);
    font-size: 0.69rem;
}

.subject-class-action {
    min-height: 43px;
    margin-top: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 10px 14px;
    border-radius: 12px;
    color: #ffffff;
    background: var(--class-gradient);
    box-shadow:
        0 10px 25px rgba(0,0,0,0.14);
    font-size: 0.79rem;
    font-weight: 780;
    text-decoration: none;
    transition:
        transform 0.22s ease,
        filter 0.22s ease;
}

.subject-class-action span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.subject-class-action:hover {
    color: #ffffff;
    filter: brightness(1.06);
    transform: translateY(-2px);
}

.subject-class-action > i {
    transition: transform 0.22s ease;
}

.subject-class-action:hover > i {
    transform: translateX(4px);
}


/* =========================================================
   ACTIONS : COURS / MODIFIER / SUPPRIMER
   ========================================================= */

.subject-class-actions-row {
    width: 100%;
    margin-top: auto;
    display: flex;
    align-items: center;
    gap: 9px;
}

.subject-class-actions-row .subject-class-action {
    flex: 1;
}

.subject-class-delete-form {
    margin: 0;
    padding: 0;
}

.subject-class-icon-btn {
    width: 43px;
    height: 43px;
    flex: 0 0 43px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border-radius: 12px;

    font-size: 1rem;
    line-height: 1;

    text-decoration: none;
    cursor: pointer;

    transition:
        transform 0.2s ease,
        background 0.2s ease,
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

/* Modifier */
.subject-class-icon-btn.edit {
    color: #FBBF24;
    border: 1px solid rgba(251,191,36,0.28);
    background: rgba(245,158,11,0.10);
}

.subject-class-icon-btn.edit:hover {
    color: #FDE68A;
    background: rgba(245,158,11,0.20);
    border-color: rgba(251,191,36,0.55);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245,158,11,0.12);
}

/* Supprimer */
.subject-class-icon-btn.delete {
    color: #FB7185;
    border: 1px solid rgba(244,63,94,0.28);
    background: rgba(244,63,94,0.10);
}

.subject-class-icon-btn.delete:hover {
    color: #FDA4AF;
    background: rgba(244,63,94,0.20);
    border-color: rgba(244,63,94,0.55);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(244,63,94,0.12);
}

.subject-class-icon-btn:focus-visible {
    outline: 2px solid rgba(96,165,250,0.9);
    outline-offset: 2px;
}

@media (max-width: 575.98px) {
    .subject-class-icon-btn {
        width: 41px;
        height: 41px;
        flex-basis: 41px;
    }
}

/* =========================================================
   ÉTAT VIDE
   ========================================================= */

.subject-empty-state {
    padding: 4rem 2rem;
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 820px) {
    .subject-class-grid {
        width: min(100%, 520px);
        grid-template-columns: 1fr;
    }
}

@media (max-width: 575.98px) {
    .subjects-classes-header h1 {
        font-size: 1.15rem;
    }

    .subject-page-separator,
    .subject-page-dot {
        display: none;
    }

    .subject-class-card {
        min-height: 340px;
        border-radius: 19px;
    }

    .subject-class-cover {
        height: 110px;
        flex-basis: 110px;
    }

    .subject-class-body {
        padding: 1.05rem;
    }
}
</style>

@endsection
