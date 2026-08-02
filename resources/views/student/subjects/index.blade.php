@extends('layouts.student')

@section('title', 'Mes matières')
@section('page_title', 'Mes matières')
@section('breadcrumb', 'Matières')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/student-subjects-v5.css') }}"
    >
@endpush

@section('content')
@php
    $totalPaths = $assignments->count();
    $visiblePaths = $visibleAssignments->count();

    $currentLevelName = $selectedLevel
        ? $selectedLevel->name
        : 'Tous les niveaux';

    $currentClassName = $selectedClass
        ? $selectedClass->name
        : 'Toutes les classes';
@endphp

<div class="student-learning-page">

    <section class="learning-page-intro">
        <div class="learning-page-intro-copy">
            <span class="learning-section-kicker">
                <i class="bi bi-mortarboard-fill"></i>
                Mon parcours
            </span>

            <h2>Choisissez une matière</h2>

            <p>
                Accédez à vos cours selon le niveau et la classe
                qui vous ont été attribués.
            </p>
        </div>

        <div class="learning-intro-stats">
            <div class="learning-intro-stat">
                <span>
                    <i class="bi bi-journal-bookmark-fill"></i>
                </span>

                <div>
                    <strong>{{ $visibleSubjectCount }}</strong>
                    <small>
                        matière{{ $visibleSubjectCount > 1 ? 's' : '' }}
                    </small>
                </div>
            </div>

            <div class="learning-intro-stat">
                <span>
                    <i class="bi bi-diagram-3-fill"></i>
                </span>

                <div>
                    <strong>{{ $visiblePaths }}</strong>
                    <small>
                        parcours disponible{{ $visiblePaths > 1 ? 's' : '' }}
                    </small>
                </div>
            </div>
        </div>
    </section>

    @if(
        $assignedLevels->count() > 1
        || $assignedClasses->count() > 1
    )
        <section class="learning-filter-panel">
            <div class="learning-panel-heading">
                <div class="learning-panel-icon">
                    <i class="bi bi-funnel-fill"></i>
                </div>

                <div>
                    <h3>Filtrer mon parcours</h3>
                    <p>
                        Sélectionnez un niveau, puis la classe
                        correspondante.
                    </p>
                </div>
            </div>

            <form
                method="GET"
                action="{{ route('student.subjects.index') }}"
                id="studentPathFilterForm"
                class="learning-filter-form"
            >
                <div class="learning-field">
                    <label for="studentLevelFilter">
                        Niveau
                    </label>

                    <div class="learning-select-wrap">
                        <i class="bi bi-mortarboard"></i>

                        <select
                            name="level_id"
                            id="studentLevelFilter"
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
                </div>

                <div class="learning-field">
                    <label for="studentClassFilter">
                        Classe
                    </label>

                    <div class="learning-select-wrap">
                        <i class="bi bi-building"></i>

                        <select
                            name="class_id"
                            id="studentClassFilter"
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
                </div>

                <a
                    href="{{ route('student.subjects.index') }}"
                    class="learning-reset-button"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Réinitialiser</span>
                </a>
            </form>
        </section>
    @endif

    <section class="learning-current-path">
        <div class="learning-current-item">
            <span class="learning-current-icon blue">
                <i class="bi bi-mortarboard-fill"></i>
            </span>

            <div>
                <small>Niveau affiché</small>
                <strong>{{ $currentLevelName }}</strong>
            </div>
        </div>

        <div class="learning-current-divider"></div>

        <div class="learning-current-item">
            <span class="learning-current-icon green">
                <i class="bi bi-building-fill"></i>
            </span>

            <div>
                <small>Classe affichée</small>
                <strong>{{ $currentClassName }}</strong>
            </div>
        </div>

        <div class="learning-current-count">
            <span></span>
            {{ $visiblePaths }} sur {{ $totalPaths }}
            parcours
        </div>
    </section>

    @if($visibleAssignments->isNotEmpty())
        <section>
            <div class="learning-list-heading">
                <div>
                    <span class="learning-section-kicker">
                        Matières disponibles
                    </span>

                    <h3>Mes cours par matière</h3>
                </div>

                <span class="learning-list-count">
                    {{ $visibleAssignments->count() }}
                    résultat{{
                        $visibleAssignments->count() > 1
                            ? 's'
                            : ''
                    }}
                </span>
            </div>

            <div class="learning-subject-grid">
                @foreach($visibleAssignments as $assignment)
                    @php
                        $subjectName = $assignment->subject->name;
                        $subjectSlug = \Illuminate\Support\Str::lower(
                            \Illuminate\Support\Str::ascii($subjectName)
                        );

                        if (str_contains($subjectSlug, 'coran')) {
                            $subjectTone = 'emerald';
                            $subjectIcon = 'book-half';
                        } elseif (str_contains($subjectSlug, 'arabe')) {
                            $subjectTone = 'indigo';
                            $subjectIcon = 'translate';
                        } elseif (str_contains($subjectSlug, 'soutien')) {
                            $subjectTone = 'amber';
                            $subjectIcon = 'mortarboard-fill';
                        } else {
                            $tones = [
                                'indigo',
                                'emerald',
                                'violet',
                                'cyan',
                                'amber',
                            ];

                            $icons = [
                                'journal-bookmark-fill',
                                'calculator',
                                'flask-fill',
                                'globe2',
                                'cpu-fill',
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

                            $subjectTone = $tones[
                                $stableIndex % count($tones)
                            ];

                            $subjectIcon = $icons[
                                $stableIndex % count($icons)
                            ];
                        }
                    @endphp

                    <a
                        href="{{ route(
                            'student.subjects.courses',
                            [
                                $assignment->subject_id,
                                $assignment->level_id,
                                $assignment->class_id,
                            ]
                        ) }}"
                        class="learning-subject-card {{ $subjectTone }}"
                    >
                        <div class="learning-subject-card-top">
                            <span class="learning-subject-icon">
                                <i class="bi bi-{{ $subjectIcon }}"></i>
                            </span>

                            <span class="learning-subject-arrow">
                                <i class="bi bi-arrow-up-right"></i>
                            </span>
                        </div>

                        <div class="learning-subject-card-body">
                            <span class="learning-subject-label">
                                Matière
                            </span>

                            <h4>{{ $subjectName }}</h4>

                            <div class="learning-subject-path">
                                <span>
                                    <i class="bi bi-mortarboard"></i>
                                    {{ $assignment->level->name }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span>
                                    <i class="bi bi-building"></i>
                                    {{ $assignment->classRoom->name }}
                                </span>
                            </div>
                        </div>

                        <div class="learning-subject-card-footer">
                            <span>Consulter les cours</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @elseif($assignments->isNotEmpty())
        <section class="learning-empty-state">
            <span class="learning-empty-icon">
                <i class="bi bi-funnel"></i>
            </span>

            <h3>Aucune matière dans ce filtre</h3>

            <p>
                Sélectionnez un autre parcours ou réinitialisez
                les filtres.
            </p>

            <a
                href="{{ route('student.subjects.index') }}"
                class="learning-primary-button"
            >
                <i class="bi bi-arrow-counterclockwise"></i>
                Tout afficher
            </a>
        </section>
    @else
        <section class="learning-empty-state">
            <span class="learning-empty-icon">
                <i class="bi bi-journal-x"></i>
            </span>

            <h3>Aucune matière disponible</h3>

            <p>
                Aucune matière ne vous est assignée pour le moment.
                Contactez l’administration si nécessaire.
            </p>

            <a
                href="{{ route('student.dashboard') }}"
                class="learning-primary-button"
            >
                <i class="bi bi-arrow-left"></i>
                Tableau de bord
            </a>
        </section>
    @endif
</div>
@endsection

@if(
    $assignedLevels->count() > 1
    || $assignedClasses->count() > 1
)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById(
                    'studentPathFilterForm'
                );

                const levelSelect = document.getElementById(
                    'studentLevelFilter'
                );

                const classSelect = document.getElementById(
                    'studentClassFilter'
                );

                const classesByLevel = @json($classOptionsByLevel);

                if (!form || !levelSelect || !classSelect) {
                    return;
                }

                function appendOption(
                    value,
                    label,
                    selected = false
                ) {
                    const option = document.createElement('option');

                    option.value = value;
                    option.textContent = label;
                    option.selected = selected;

                    classSelect.appendChild(option);
                }

                levelSelect.addEventListener('change', function () {
                    const levelId = levelSelect.value;

                    classSelect.innerHTML = '';

                    if (!levelId) {
                        classSelect.disabled = true;

                        appendOption(
                            '',
                            'Choisissez d’abord un niveau',
                            true
                        );

                        form.submit();
                        return;
                    }

                    const classes = classesByLevel[levelId] || [];

                    classSelect.disabled = false;

                    if (classes.length === 0) {
                        appendOption(
                            '',
                            'Aucune classe assignée',
                            true
                        );

                        form.submit();
                        return;
                    }

                    classes.forEach(function (classRoom, index) {
                        appendOption(
                            classRoom.id,
                            classRoom.name,
                            index === 0
                        );
                    });

                    form.submit();
                });

                classSelect.addEventListener('change', function () {
                    form.submit();
                });
            });
        </script>
    @endpush
@endif
