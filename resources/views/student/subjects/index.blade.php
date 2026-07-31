@extends('layouts.student')

@section('title', 'Mes Matières')

@section('page_title', 'Matières')
@section('breadcrumb', 'Matières')

@section('content')

<style>
.student-path-filter {
    margin-bottom: 1.4rem;
    padding: 1.15rem 1.25rem;
    border: 1px solid rgba(124, 58, 237, 0.14);
    border-radius: 14px;
    background:
        linear-gradient(
            135deg,
            rgba(15, 23, 42, 0.92),
            rgba(30, 41, 59, 0.72)
        );
}

.student-path-filter-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}

.student-path-filter-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #f1f5f9;
    font-size: 0.9rem;
    font-weight: 800;
}

.student-path-filter-title span {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    border-radius: 10px;
    color: #c4b5fd;
    background: rgba(124, 58, 237, 0.14);
    border: 1px solid rgba(167, 139, 250, 0.16);
}

.student-path-filter-subtitle {
    margin-top: 4px;
    color: #64748b;
    font-size: 0.72rem;
    line-height: 1.5;
}

.student-path-filter-grid {
    display: grid;
    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr)
        auto;
    gap: 0.85rem;
    align-items: end;
}

.student-filter-field label {
    display: block;
    margin-bottom: 0.45rem;
    color: #94a3b8;
    font-size: 0.66rem;
    font-weight: 750;
    letter-spacing: 0.045em;
    text-transform: uppercase;
}

.student-filter-select {
    width: 100%;
    min-height: 44px;
    padding: 0 2.4rem 0 0.85rem;
    color: #e2e8f0;
    background-color: rgba(15, 23, 42, 0.85);
    border: 1px solid rgba(148, 163, 184, 0.15);
    border-radius: 10px;
    outline: none;
    font-size: 0.8rem;
    font-weight: 650;
    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.student-filter-select:focus {
    border-color: rgba(129, 140, 248, 0.72);
    box-shadow:
        0 0 0 4px rgba(99, 102, 241, 0.11);
}

.student-filter-select:disabled {
    cursor: not-allowed;
    color: #475569;
    opacity: 0.7;
}

.student-filter-reset {
    min-height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 0 1rem;
    color: #cbd5e1;
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    font-size: 0.73rem;
    font-weight: 750;
    text-decoration: none;
    white-space: nowrap;
}

.student-filter-reset:hover {
    color: #ffffff;
    border-color: rgba(129, 140, 248, 0.35);
    background: rgba(99, 102, 241, 0.09);
}

.student-path-summary {
    margin-bottom: 1.5rem;
    padding: 1.15rem 1.35rem;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.15rem;
    border: 1px solid rgba(124, 58, 237, 0.11);
    border-radius: 13px;
    background:
        linear-gradient(
            135deg,
            rgba(79, 70, 229, 0.1),
            rgba(124, 58, 237, 0.045)
        );
}

.student-path-summary-item {
    display: flex;
    align-items: center;
    gap: 11px;
    min-width: 190px;
}

.student-path-summary-icon {
    width: 43px;
    height: 43px;
    flex: 0 0 43px;
    display: grid;
    place-items: center;
    border-radius: 10px;
    color: #ffffff;
    font-size: 1.05rem;
}

.student-path-summary-icon.level {
    background:
        linear-gradient(
            135deg,
            #4f46e5,
            #7c3aed
        );
}

.student-path-summary-icon.classroom {
    background:
        linear-gradient(
            135deg,
            #059669,
            #10b981
        );
}

.student-path-summary-label {
    color: #64748b;
    font-size: 0.62rem;
    font-weight: 650;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.student-path-summary-value {
    margin-top: 2px;
    color: #f1f5f9;
    font-size: 0.92rem;
    font-weight: 780;
}

.student-path-summary-count {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 7px;
    color: #64748b;
    font-size: 0.73rem;
}

.student-path-summary-count span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    box-shadow:
        0 0 0 4px rgba(16, 185, 129, 0.08);
}

.student-subject-card-link {
    height: 100%;
    display: block;
    text-decoration: none;
}

.student-subject-card {
    height: 100%;
    min-height: 210px;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.35rem 1rem;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.045);
    border-radius: 13px;
    background:
        linear-gradient(
            135deg,
            #1e293b,
            #192333
        );
    transition:
        transform 0.22s ease,
        border-color 0.22s ease,
        box-shadow 0.22s ease;
}

.student-subject-card:hover {
    transform: translateY(-4px);
    border-color: rgba(129, 140, 248, 0.28);
    box-shadow:
        0 15px 35px rgba(0, 0, 0, 0.22);
}

.student-subject-decoration {
    position: absolute;
    top: -19px;
    right: -19px;
    width: 82px;
    height: 82px;
    border-radius: 50%;
    opacity: 0.075;
}

.student-subject-icon {
    width: 56px;
    height: 56px;
    display: grid;
    place-items: center;
    margin-bottom: 0.8rem;
    border-radius: 50%;
    font-size: 1.3rem;
    transition: transform 0.22s ease;
}

.student-subject-card:hover
.student-subject-icon {
    transform: scale(1.08);
}

.student-subject-name {
    margin: 0 0 0.55rem;
    color: #f1f5f9;
    font-size: 0.97rem;
    font-weight: 800;
}

.student-subject-path {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 5px;
    margin-bottom: 0.75rem;
}

.student-subject-path-badge {
    max-width: 100%;
    padding: 4px 7px;
    overflow: hidden;
    color: #94a3b8;
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.055);
    border-radius: 999px;
    font-size: 0.58rem;
    font-weight: 650;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.student-subject-action {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #64748b;
    font-size: 0.73rem;
}

.student-subject-action i {
    color: #818cf8;
}

.student-filter-empty {
    padding: 2.3rem 1rem;
    border: 1px dashed rgba(148, 163, 184, 0.16);
    border-radius: 14px;
    color: #64748b;
    background: rgba(15, 23, 42, 0.45);
    text-align: center;
}

.student-filter-empty i {
    display: block;
    margin-bottom: 0.7rem;
    color: #818cf8;
    font-size: 2rem;
}

.student-filter-empty h5 {
    margin-bottom: 0.4rem;
    color: #e2e8f0;
    font-size: 0.95rem;
}

.student-filter-empty p {
    margin: 0;
    font-size: 0.76rem;
}

@media (max-width: 900px) {
    .student-path-filter-grid {
        grid-template-columns: 1fr 1fr;
    }

    .student-filter-reset {
        grid-column: 1 / -1;
    }
}

@media (max-width: 650px) {
    .student-path-filter-grid {
        grid-template-columns: 1fr;
    }

    .student-filter-reset {
        grid-column: auto;
    }

    .student-path-summary {
        align-items: flex-start;
    }

    .student-path-summary-item {
        width: 100%;
        min-width: 0;
    }

    .student-path-summary-count {
        width: 100%;
        margin-left: 0;
    }
}
</style>

<div class="st-page-header">
    <div>
        <h1>
            <i
                class="bi bi-book"
                style="color:#818CF8;"
            ></i>
            Mes Matières
        </h1>

        <div class="subtitle">
            Filtrez par niveau et par classe,
            ou affichez tous vos parcours.
        </div>
    </div>
</div>

@if(
    $assignedLevels->count() > 1
    || $assignedClasses->count() > 1
)
    <div class="student-path-filter">
        <div class="student-path-filter-header">
            <div>
                <div class="student-path-filter-title">
                    <span>
                        <i class="bi bi-funnel-fill"></i>
                    </span>

                    Filtrer mes matières
                </div>

                <div class="student-path-filter-subtitle">
                    La liste des classes change automatiquement
                    selon le niveau sélectionné.
                </div>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('student.subjects.index') }}"
            id="studentPathFilterForm"
            class="student-path-filter-grid"
        >
            <div class="student-filter-field">
                <label for="studentLevelFilter">
                    Niveau
                </label>

                <select
                    name="level_id"
                    id="studentLevelFilter"
                    class="student-filter-select"
                >
                    <option value="">
                        Tous les niveaux
                    </option>

                    @foreach($assignedLevels as $levelOption)
                        <option
                            value="{{ $levelOption->id }}"
                            {{
                                $selectedLevel
                                && (int) $selectedLevel->id
                                    === (int) $levelOption->id
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            {{ $levelOption->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="student-filter-field">
                <label for="studentClassFilter">
                    Classe
                </label>

                <select
                    name="class_id"
                    id="studentClassFilter"
                    class="student-filter-select"
                    {{ !$selectedLevel ? 'disabled' : '' }}
                >
                    @if(!$selectedLevel)
                        <option value="">
                            Choisissez d’abord un niveau
                        </option>
                    @else
                        @foreach(
                            $classesForSelectedLevel
                            as $classOption
                        )
                            <option
                                value="{{ $classOption->id }}"
                                {{
                                    $selectedClass
                                    && (int) $selectedClass->id
                                        === (int) $classOption->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $classOption->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <a
                href="{{ route('student.subjects.index') }}"
                class="student-filter-reset"
                title="Afficher toutes les matières"
            >
                <i class="bi bi-arrow-counterclockwise"></i>
                Tout afficher
            </a>
        </form>
    </div>
@endif

<div class="student-path-summary">
    <div class="student-path-summary-item">
        <div class="student-path-summary-icon level">
            <i class="bi bi-mortarboard-fill"></i>
        </div>

        <div>
            <div class="student-path-summary-label">
                Niveau
            </div>

            <div class="student-path-summary-value">
                {{ $selectedLevel
                    ? $selectedLevel->name
                    : 'Tous les niveaux' }}
            </div>
        </div>
    </div>

    <div class="student-path-summary-item">
        <div class="student-path-summary-icon classroom">
            <i class="bi bi-building"></i>
        </div>

        <div>
            <div class="student-path-summary-label">
                Classe
            </div>

            <div class="student-path-summary-value">
                {{ $selectedClass
                    ? $selectedClass->name
                    : 'Toutes les classes' }}
            </div>
        </div>
    </div>

    <div class="student-path-summary-count">
        <span></span>

        {{ $visibleSubjectCount }}
        matière{{ $visibleSubjectCount > 1 ? 's' : '' }}

        ·

        {{ $visibleAssignments->count() }}
        parcours disponible{{
            $visibleAssignments->count() > 1
                ? 's'
                : ''
        }}
    </div>
</div>

@if($visibleAssignments->isNotEmpty())
    <div class="row g-3">
        @foreach($visibleAssignments as $assignment)
            @php
                $icons = [
                    'calculator',
                    'flask',
                    'translate',
                    'globe',
                    'palette',
                    'music-note-beamed',
                    'cpu',
                    'graph-up',
                    'book',
                    'pencil',
                    'journal',
                    'robot',
                ];

                $colors = [
                    '#4F46E5',
                    '#059669',
                    '#7C3AED',
                    '#D97706',
                    '#DC2626',
                    '#0284C7',
                    '#0891B2',
                    '#9333EA',
                ];

                $stableIndex = abs(
                    crc32(
                        $assignment->subject_id
                        . ':'
                        . $assignment->level_id
                        . ':'
                        . $assignment->class_id
                    )
                );

                $color =
                    $colors[
                        $stableIndex
                        % count($colors)
                    ];

                $icon =
                    $icons[
                        $stableIndex
                        % count($icons)
                    ];

                [$r, $g, $b] = sscanf(
                    $color,
                    '#%02x%02x%02x'
                );
            @endphp

            <div class="col-sm-6 col-lg-4 col-xl-3">
                <a
                    href="{{ route(
                        'student.subjects.courses',
                        [
                            $assignment->subject_id,
                            $assignment->level_id,
                            $assignment->class_id,
                        ]
                    ) }}"
                    class="student-subject-card-link"
                >
                    <div class="student-subject-card">
                        <div
                            class="student-subject-decoration"
                            style="
                                background:rgba(
                                    {{ $r }},
                                    {{ $g }},
                                    {{ $b }},
                                    1
                                );
                            "
                        ></div>

                        <div
                            class="student-subject-icon"
                            style="
                                color:{{ $color }};
                                background:rgba(
                                    {{ $r }},
                                    {{ $g }},
                                    {{ $b }},
                                    0.11
                                );
                            "
                        >
                            <i class="bi bi-{{ $icon }}"></i>
                        </div>

                        <h5 class="student-subject-name">
                            {{ $assignment->subject->name }}
                        </h5>

                        <div class="student-subject-path">
                            <span
                                class="student-subject-path-badge"
                                title="{{
                                    $assignment->level->name
                                }}"
                            >
                                <i
                                    class="bi bi-mortarboard me-1"
                                ></i>
                                {{ $assignment->level->name }}
                            </span>

                            <span
                                class="student-subject-path-badge"
                                title="{{
                                    $assignment->classRoom->name
                                }}"
                            >
                                <i
                                    class="bi bi-building me-1"
                                ></i>
                                {{
                                    $assignment
                                        ->classRoom
                                        ->name
                                }}
                            </span>
                        </div>

                        <div class="student-subject-action">
                            <i class="bi bi-play-circle"></i>
                            Voir mes cours
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@elseif($assignments->isNotEmpty())
    <div class="student-filter-empty">
        <i class="bi bi-funnel"></i>

        <h5>
            Aucune matière dans ce filtre
        </h5>

        <p>
            Choisissez un autre niveau ou utilisez
            « Tout afficher ».
        </p>
    </div>
@else
    <div class="pr-empty">
        <div class="pr-empty-icon">
            <i class="bi bi-book"></i>
        </div>

        <h5>Aucune matière disponible</h5>

        <p>
            Aucune matière ne vous est assignée
            pour le moment.
        </p>

        <a
            href="{{ route('student.dashboard') }}"
            class="st-btn st-btn-ghost st-btn-sm"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Retour au tableau de bord
        </a>
    </div>
@endif

@if(
    $assignedLevels->count() > 1
    || $assignedClasses->count() > 1
)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById(
        'studentPathFilterForm'
    );

    const levelSelect = document.getElementById(
        'studentLevelFilter'
    );

    const classSelect = document.getElementById(
        'studentClassFilter'
    );

    const classesByLevel =
        @json($classOptionsByLevel);

    if (
        !form
        || !levelSelect
        || !classSelect
    ) {
        return;
    }

    const addOption = (
        value,
        label,
        selected = false
    ) => {
        const option =
            document.createElement('option');

        option.value = value;
        option.textContent = label;
        option.selected = selected;

        classSelect.appendChild(option);
    };

    levelSelect.addEventListener(
        'change',
        () => {
            const levelId = levelSelect.value;

            classSelect.innerHTML = '';

            if (!levelId) {
                classSelect.disabled = true;

                addOption(
                    '',
                    'Choisissez d’abord un niveau',
                    true
                );

                form.submit();
                return;
            }

            const classes =
                classesByLevel[levelId] || [];

            classSelect.disabled = false;

            if (classes.length === 0) {
                addOption(
                    '',
                    'Aucune classe assignée',
                    true
                );

                form.submit();
                return;
            }

            classes.forEach(
                (classRoom, index) => {
                    addOption(
                        classRoom.id,
                        classRoom.name,
                        index === 0
                    );
                }
            );

            /*
             * Le navigateur envoie immédiatement le
             * premier class_id du niveau sélectionné.
             */
            form.submit();
        }
    );

    classSelect.addEventListener(
        'change',
        () => {
            form.submit();
        }
    );
});
</script>
@endif

@endsection
