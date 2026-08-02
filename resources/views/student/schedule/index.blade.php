@extends('layouts.student')

@section('title', 'Emploi du temps')
@section('page_title', 'Emploi du temps')
@section('breadcrumb', 'Emploi du temps')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/student-pages-v6.css') }}"
    >
@endpush

@section('content')
@php
    $sessionCount = $occurrences->count();
    $dayCount = $days->count();
@endphp

<div class="sp-page sp-schedule-page">

    <section class="sp-hero sp-hero-schedule">
        <div class="sp-hero-icon">
            <i class="bi bi-calendar-week-fill"></i>
        </div>

        <div class="sp-hero-copy">
            <span class="sp-kicker">
                Organisation des cours
            </span>

            <h2>Mon emploi du temps</h2>

            <p>
                Retrouvez les prochaines séances correspondant à votre
                parcours Matière → Niveau → Classe.
            </p>
        </div>

        <div class="sp-hero-summary">
            <strong>{{ $sessionCount }}</strong>
            <span>
                séance{{ $sessionCount > 1 ? 's' : '' }}
                à venir
            </span>
        </div>
    </section>

    <section class="sp-filter-card">
        <div class="sp-card-heading">
            <div class="sp-card-heading-icon blue">
                <i class="bi bi-funnel-fill"></i>
            </div>

            <div>
                <h3>Filtrer l’emploi du temps</h3>

                <p>
                    Choisissez une matière puis un niveau.
                </p>
            </div>

            @if($hasActiveFilter)
                <a
                    href="{{ route('student.schedule.index') }}"
                    class="sp-reset-link"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Réinitialiser
                </a>
            @endif
        </div>

        <form
            method="GET"
            action="{{ route('student.schedule.index') }}"
            class="sp-filter-grid"
            id="studentScheduleFilterForm"
        >
            <div class="sp-field">
                <label for="scheduleSubject">
                    Matière
                </label>

                <div class="sp-select-wrap">
                    <i class="bi bi-journal-bookmark-fill"></i>

                    <select
                        name="subject_id"
                        id="scheduleSubject"
                    >
                        <option value="">
                            Toutes les matières
                        </option>

                        @foreach($subjects as $subject)
                            <option
                                value="{{ $subject->id }}"
                                {{
                                    (int) $selectedSubjectId
                                    === (int) $subject->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="sp-field">
                <label for="scheduleLevel">
                    Niveau
                </label>

                <div class="sp-select-wrap">
                    <i class="bi bi-layers-fill"></i>

                    <select
                        name="level_id"
                        id="scheduleLevel"
                        {{ $selectedSubjectId ? '' : 'disabled' }}
                    >
                        <option value="">
                            Tous les niveaux
                        </option>
                    </select>
                </div>
            </div>

            <button
                type="submit"
                class="sp-primary-button"
            >
                <i class="bi bi-search"></i>
                Afficher
            </button>
        </form>
    </section>

    <section class="sp-metrics sp-metrics-three">
        <article class="sp-metric-card">
            <span class="sp-metric-icon violet">
                <i class="bi bi-calendar2-check-fill"></i>
            </span>

            <div>
                <small>Séances visibles</small>
                <strong>{{ $sessionCount }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon blue">
                <i class="bi bi-journal-bookmark-fill"></i>
            </span>

            <div>
                <small>Matières</small>
                <strong>{{ $subjects->count() }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon green">
                <i class="bi bi-building-fill"></i>
            </span>

            <div>
                <small>Classes du parcours</small>
                <strong>{{ $availableClassCount }}</strong>
            </div>
        </article>
    </section>

    <section class="sp-current-filter">
        <div>
            <span class="sp-current-filter-icon">
                <i class="bi bi-diagram-3-fill"></i>
            </span>

            <div>
                <small>Parcours affiché</small>

                <strong>
                    {{
                        optional($selectedSubject)->name
                        ?? 'Toutes les matières'
                    }}
                    <i class="bi bi-chevron-right"></i>
                    {{
                        optional($selectedLevel)->name
                        ?? 'Tous les niveaux'
                    }}
                </strong>
            </div>
        </div>

        <span class="sp-soft-badge">
            {{ $dayCount }}
            jour{{ $dayCount > 1 ? 's' : '' }}
            programmé{{ $dayCount > 1 ? 's' : '' }}
        </span>
    </section>

    <div class="sp-schedule-days">
        @forelse($days as $date => $dayOccurrences)
            @php
                $firstOccurrence = $dayOccurrences->first();
            @endphp

            <section class="sp-day-card">
                <header class="sp-day-header">
                    <div class="sp-day-date">
                        <span class="sp-day-short">
                            {{ $firstOccurrence['day_short'] }}
                        </span>

                        <div>
                            <h3>
                                {{ $firstOccurrence['date_label'] }}
                            </h3>

                            <p>
                                {{ $dayOccurrences->count() }}
                                {{
                                    $dayOccurrences->count() > 1
                                        ? 'séances'
                                        : 'séance'
                                }}
                            </p>
                        </div>
                    </div>

                    <span class="sp-status-badge blue">
                        <i class="bi bi-calendar-event"></i>
                        Programme du jour
                    </span>
                </header>

                <div class="sp-session-list">
                    @foreach($dayOccurrences as $occurrence)
                        <article class="sp-session-row">
                            <div class="sp-session-time">
                                <span>
                                    <i class="bi bi-clock-fill"></i>
                                </span>

                                <div>
                                    <strong>
                                        {{ $occurrence['time_label'] }}
                                    </strong>

                                    <small>
                                        {{
                                            $occurrence[
                                                'duration_label'
                                            ]
                                        }}
                                    </small>
                                </div>
                            </div>

                            <div class="sp-session-main">
                                <span class="sp-session-subject">
                                    {{ $occurrence['subject'] }}
                                </span>

                                <h4>
                                    {{ $occurrence['level'] }}
                                    <i class="bi bi-chevron-right"></i>
                                    {{ $occurrence['class_name'] }}
                                </h4>

                                <p>
                                    <i class="bi bi-diagram-3"></i>
                                    {{ $occurrence['path'] }}
                                </p>
                            </div>

                            <div class="sp-session-details">
                                <span>
                                    <i class="bi bi-door-open-fill"></i>
                                    {{
                                        $occurrence['room']
                                        ?? 'Salle à confirmer'
                                    }}
                                </span>

                                <span>
                                    <i class="bi bi-person-video3"></i>
                                    {{ $occurrence['teacher'] }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <section class="sp-empty-state">
                <span class="sp-empty-icon">
                    <i class="bi bi-calendar2-x"></i>
                </span>

                @if($hasActiveFilter)
                    <h3>Aucune séance pour ce filtre</h3>

                    <p>
                        Aucun cours n’est programmé pour la matière
                        et le niveau sélectionnés pendant les
                        35 prochains jours.
                    </p>

                    <a
                        href="{{ route('student.schedule.index') }}"
                        class="sp-primary-button"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Tout afficher
                    </a>
                @else
                    <h3>Aucune séance à venir</h3>

                    <p>
                        Les nouvelles séances apparaîtront ici
                        après leur publication.
                    </p>
                @endif
            </section>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const subjectSelect = document.getElementById(
        'scheduleSubject'
    );

    const levelSelect = document.getElementById(
        'scheduleLevel'
    );

    const form = document.getElementById(
        'studentScheduleFilterForm'
    );

    const levelsBySubject = @json($levelsBySubject);
    const selectedLevelId = @json($selectedLevelId);

    if (!subjectSelect || !levelSelect || !form) {
        return;
    }

    function fillLevels(subjectId, selectedId = null) {
        levelSelect.innerHTML = '';

        const defaultOption =
            document.createElement('option');

        defaultOption.value = '';
        defaultOption.textContent = 'Tous les niveaux';

        levelSelect.appendChild(defaultOption);

        if (!subjectId) {
            levelSelect.disabled = true;
            return;
        }

        const options = levelsBySubject[subjectId] || [];

        options.forEach(function (level) {
            const option = document.createElement('option');

            option.value = level.id;
            option.textContent = level.name;

            option.selected =
                selectedId !== null
                && String(level.id) === String(selectedId);

            levelSelect.appendChild(option);
        });

        levelSelect.disabled = false;
    }

    fillLevels(subjectSelect.value, selectedLevelId);

    subjectSelect.addEventListener('change', function () {
        fillLevels(subjectSelect.value);
        form.submit();
    });
});
</script>
@endpush
