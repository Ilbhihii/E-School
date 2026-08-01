@extends('layouts.student')

@section('title', 'Emploi du temps')
@section('page_title', 'Emploi du temps')
@section('breadcrumb', 'Emploi du temps')

@section('content')
<div class="student-timetable-page">
    <section class="student-timetable-hero">
        <div class="student-timetable-hero-copy">
            <span class="student-timetable-kicker">
                <i class="bi bi-calendar-week"></i>
                Mon espace étudiant
            </span>

            <h1>Mon emploi du temps</h1>

            <p>
                Consultez uniquement les séances correspondant à votre parcours
                <strong>Matière → Niveau → Classe</strong>.
            </p>
        </div>

        <div class="student-timetable-hero-stat">
            <strong>{{ $occurrences->count() }}</strong>
            <span>séance(s) à venir</span>
        </div>
    </section>

    <section class="student-timetable-filter-card">
        <div class="student-timetable-filter-heading">
            <div>
                <span>Filtrage du parcours</span>
                <h2>Matière → Niveau</h2>
            </div>

            <div class="student-timetable-path-summary">
                <i class="bi bi-diagram-3"></i>
                <span>
                    {{ optional($selectedSubject)->name ?? 'Toutes les matières' }}
                    <i class="bi bi-chevron-right"></i>
                    {{ optional($selectedLevel)->name ?? 'Tous les niveaux' }}
                    <i class="bi bi-chevron-right"></i>
                    {{ $availableClassCount }} classe(s)
                </span>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('student.schedule.index') }}"
            class="student-timetable-filter-form"
            id="studentTimetableFilterForm"
        >
            <div class="student-timetable-field">
                <label for="scheduleSubject">
                    <i class="bi bi-book"></i>
                    Matière
                </label>

                <select name="subject_id" id="scheduleSubject">
                    <option value="">Toutes les matières</option>

                    @foreach($subjects as $subject)
                        <option
                            value="{{ $subject->id }}"
                            {{ (int) $selectedSubjectId === (int) $subject->id ? 'selected' : '' }}
                        >
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="student-timetable-arrow" aria-hidden="true">
                <i class="bi bi-arrow-right"></i>
            </div>

            <div class="student-timetable-field">
                <label for="scheduleLevel">
                    <i class="bi bi-layers"></i>
                    Niveau
                </label>

                <select
                    name="level_id"
                    id="scheduleLevel"
                    {{ $selectedSubjectId ? '' : 'disabled' }}
                >
                    <option value="">Tous les niveaux</option>
                </select>
            </div>

            <div class="student-timetable-filter-actions">
                <button type="submit" class="student-timetable-filter-button">
                    <i class="bi bi-funnel"></i>
                    Filtrer
                </button>

                @if($hasActiveFilter)
                    <a
                        href="{{ route('student.schedule.index') }}"
                        class="student-timetable-reset-button"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </section>

    <section class="student-timetable-overview">
        <article>
            <span class="overview-icon purple">
                <i class="bi bi-calendar2-check"></i>
            </span>
            <div>
                <small>Séances visibles</small>
                <strong>{{ $occurrences->count() }}</strong>
            </div>
        </article>

        <article>
            <span class="overview-icon blue">
                <i class="bi bi-book-half"></i>
            </span>
            <div>
                <small>Matières disponibles</small>
                <strong>{{ $subjects->count() }}</strong>
            </div>
        </article>

        <article>
            <span class="overview-icon gold">
                <i class="bi bi-people"></i>
            </span>
            <div>
                <small>Classes du parcours</small>
                <strong>{{ $availableClassCount }}</strong>
            </div>
        </article>
    </section>

    <div class="student-timetable-days">
        @forelse($days as $date => $dayOccurrences)
            <section class="student-timetable-day">
                <header class="student-timetable-day-header">
                    <div class="student-timetable-day-date">
                        <span>{{ $dayOccurrences->first()['day_short'] }}</span>
                        <div>
                            <strong>{{ $dayOccurrences->first()['date_label'] }}</strong>
                            <small>
                                {{ $dayOccurrences->count() }}
                                {{ $dayOccurrences->count() > 1 ? 'séances' : 'séance' }}
                            </small>
                        </div>
                    </div>

                    <span class="student-timetable-day-badge">
                        <i class="bi bi-calendar-event"></i>
                        Programme du jour
                    </span>
                </header>

                <div class="student-timetable-list">
                    @foreach($dayOccurrences as $occurrence)
                        <article class="student-timetable-row">
                            <div class="student-timetable-time">
                                <i class="bi bi-clock"></i>
                                <strong>{{ $occurrence['time_label'] }}</strong>
                                <small>{{ $occurrence['duration_label'] }}</small>
                            </div>

                            <div class="student-timetable-course">
                                <span class="student-timetable-subject">
                                    {{ $occurrence['subject'] }}
                                </span>

                                <h3>
                                    {{ $occurrence['level'] }}
                                    <i class="bi bi-chevron-right"></i>
                                    {{ $occurrence['class_name'] }}
                                </h3>

                                <p>
                                    <i class="bi bi-diagram-3"></i>
                                    {{ $occurrence['path'] }}
                                </p>
                            </div>

                            <div class="student-timetable-meta">
                                <span>
                                    <i class="bi bi-door-open"></i>
                                    {{ $occurrence['room'] ?? 'Salle à confirmer' }}
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
            <section class="student-timetable-empty">
                <div class="student-timetable-empty-icon">
                    <i class="bi bi-calendar2-x"></i>
                </div>

                @if($hasActiveFilter)
                    <h2>Aucune séance pour ce filtre</h2>
                    <p>
                        Aucun cours n’est programmé pour la matière et le niveau
                        sélectionnés pendant les 35 prochains jours.
                    </p>
                    <a href="{{ route('student.schedule.index') }}">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Afficher tout l’emploi du temps
                    </a>
                @else
                    <h2>Aucune séance à venir</h2>
                    <p>
                        L’administration n’a pas encore publié de séance pour
                        vos matières, niveaux et classes.
                    </p>
                @endif
            </section>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<style>
.student-timetable-page {
    max-width: 1180px;
    margin: 0 auto;
}

.student-timetable-hero {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    padding: 2rem;
    margin-bottom: 1.25rem;
    overflow: hidden;
    border: 1px solid rgba(167, 139, 250, .18);
    border-radius: 24px;
    background:
        radial-gradient(circle at 92% 15%, rgba(167, 139, 250, .22), transparent 34%),
        linear-gradient(135deg, #171328, #33235d 58%, #20224a);
    box-shadow: 0 22px 55px rgba(0, 0, 0, .28);
    color: #fff;
}

.student-timetable-hero::after {
    position: absolute;
    right: -65px;
    bottom: -95px;
    width: 230px;
    height: 230px;
    content: "";
    border-radius: 50%;
    background: rgba(59, 130, 246, .13);
}

.student-timetable-hero-copy,
.student-timetable-hero-stat {
    position: relative;
    z-index: 1;
}

.student-timetable-kicker {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .45rem .75rem;
    margin-bottom: .8rem;
    border: 1px solid rgba(255, 255, 255, .12);
    border-radius: 999px;
    background: rgba(255, 255, 255, .07);
    color: #ddd6fe;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.student-timetable-hero h1 {
    margin: 0 0 .6rem;
    font-family: 'Poppins', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.7rem);
    font-weight: 800;
    letter-spacing: -.04em;
}

.student-timetable-hero p {
    max-width: 680px;
    margin: 0;
    color: #ddd6fe;
    line-height: 1.7;
}

.student-timetable-hero p strong {
    color: #fff;
}

.student-timetable-hero-stat {
    min-width: 155px;
    padding: 1.2rem 1.35rem;
    border: 1px solid rgba(255, 255, 255, .14);
    border-radius: 19px;
    background: rgba(255, 255, 255, .08);
    text-align: center;
    backdrop-filter: blur(12px);
}

.student-timetable-hero-stat strong,
.student-timetable-hero-stat span {
    display: block;
}

.student-timetable-hero-stat strong {
    font-size: 2.25rem;
    line-height: 1;
}

.student-timetable-hero-stat span {
    margin-top: .45rem;
    color: #ddd6fe;
    font-size: .75rem;
}

.student-timetable-filter-card {
    padding: 1.35rem;
    margin-bottom: 1.15rem;
    border: 1px solid var(--st-border-card);
    border-radius: 20px;
    background: var(--st-bg-card);
    box-shadow: var(--st-shadow-card);
    backdrop-filter: blur(16px);
}

.student-timetable-filter-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--st-border-card);
}

.student-timetable-filter-heading > div:first-child > span {
    color: #a78bfa;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.student-timetable-filter-heading h2 {
    margin: .25rem 0 0;
    color: rgba(255, 255, 255, .92);
    font-size: 1.05rem;
    font-weight: 800;
}

.student-timetable-path-summary {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    padding: .65rem .8rem;
    border: 1px solid rgba(124, 58, 237, .22);
    border-radius: 12px;
    background: rgba(124, 58, 237, .09);
    color: #ddd6fe;
    font-size: .77rem;
    font-weight: 700;
}

.student-timetable-path-summary span {
    display: inline-flex;
    align-items: center;
    gap: .38rem;
    flex-wrap: wrap;
}

.student-timetable-filter-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr) auto;
    align-items: end;
    gap: 1rem;
}

.student-timetable-field label {
    display: flex;
    align-items: center;
    gap: .45rem;
    margin-bottom: .5rem;
    color: var(--st-text-secondary);
    font-size: .75rem;
    font-weight: 750;
}

.student-timetable-field label i {
    color: #a78bfa;
}

.student-timetable-field select {
    width: 100%;
    min-height: 47px;
    padding: 0 2.5rem 0 .9rem;
    border: 1px solid var(--st-border-card);
    border-radius: 12px;
    outline: none;
    background: #121a2c;
    color: rgba(255, 255, 255, .9);
    font-size: .85rem;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.student-timetable-field select:focus {
    border-color: rgba(139, 92, 246, .68);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, .12);
}

.student-timetable-field select:disabled {
    cursor: not-allowed;
    opacity: .52;
}

.student-timetable-arrow {
    display: grid;
    place-items: center;
    width: 38px;
    height: 47px;
    color: #8b5cf6;
}

.student-timetable-filter-actions {
    display: flex;
    align-items: center;
    gap: .65rem;
}

.student-timetable-filter-button,
.student-timetable-reset-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .48rem;
    min-height: 47px;
    padding: 0 1rem;
    border-radius: 12px;
    font-size: .78rem;
    font-weight: 800;
    white-space: nowrap;
}

.student-timetable-filter-button {
    border: none;
    background: var(--st-gradient-primary);
    color: #fff;
    box-shadow: 0 10px 22px rgba(79, 70, 229, .25);
}

.student-timetable-reset-button {
    border: 1px solid var(--st-border-card);
    background: rgba(255, 255, 255, .04);
    color: var(--st-text-secondary);
}

.student-timetable-overview {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.student-timetable-overview article {
    display: flex;
    align-items: center;
    gap: .85rem;
    padding: 1rem;
    border: 1px solid var(--st-border-card);
    border-radius: 16px;
    background: var(--st-bg-card);
}

.overview-icon {
    display: grid;
    place-items: center;
    flex: 0 0 45px;
    width: 45px;
    height: 45px;
    border-radius: 13px;
    font-size: 1.05rem;
}

.overview-icon.purple {
    background: rgba(124, 58, 237, .15);
    color: #c4b5fd;
}

.overview-icon.blue {
    background: rgba(37, 99, 235, .15);
    color: #93c5fd;
}

.overview-icon.gold {
    background: rgba(245, 158, 11, .14);
    color: #fcd34d;
}

.student-timetable-overview small,
.student-timetable-overview strong {
    display: block;
}

.student-timetable-overview small {
    color: var(--st-text-muted);
    font-size: .7rem;
}

.student-timetable-overview strong {
    margin-top: .15rem;
    color: rgba(255, 255, 255, .92);
    font-size: 1.15rem;
}

.student-timetable-days {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.student-timetable-day {
    overflow: hidden;
    border: 1px solid var(--st-border-card);
    border-radius: 20px;
    background: var(--st-bg-card);
    box-shadow: var(--st-shadow-card);
}

.student-timetable-day-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.2rem;
    border-bottom: 1px solid var(--st-border-card);
    background: rgba(255, 255, 255, .025);
}

.student-timetable-day-date {
    display: flex;
    align-items: center;
    gap: .8rem;
}

.student-timetable-day-date > span {
    display: grid;
    place-items: center;
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: rgba(124, 58, 237, .14);
    color: #c4b5fd;
    font-size: .75rem;
    font-weight: 850;
    text-transform: uppercase;
}

.student-timetable-day-date strong,
.student-timetable-day-date small {
    display: block;
}

.student-timetable-day-date strong {
    color: rgba(255, 255, 255, .92);
}

.student-timetable-day-date small {
    margin-top: .15rem;
    color: var(--st-text-muted);
    font-size: .72rem;
}

.student-timetable-day-badge {
    display: inline-flex;
    align-items: center;
    gap: .42rem;
    padding: .5rem .7rem;
    border: 1px solid rgba(59, 130, 246, .18);
    border-radius: 999px;
    background: rgba(59, 130, 246, .08);
    color: #93c5fd;
    font-size: .68rem;
    font-weight: 750;
}

.student-timetable-row {
    display: grid;
    grid-template-columns: 170px minmax(0, 1fr) minmax(210px, auto);
    align-items: center;
    gap: 1.2rem;
    padding: 1.15rem 1.2rem;
    border-bottom: 1px solid var(--st-border-card);
    transition: background .2s ease;
}

.student-timetable-row:last-child {
    border-bottom: 0;
}

.student-timetable-row:hover {
    background: rgba(255, 255, 255, .025);
}

.student-timetable-time {
    display: grid;
    grid-template-columns: auto 1fr;
    align-items: center;
    gap: .15rem .55rem;
}

.student-timetable-time > i {
    grid-row: 1 / span 2;
    display: grid;
    place-items: center;
    width: 38px;
    height: 38px;
    border-radius: 11px;
    background: rgba(245, 158, 11, .12);
    color: #fbbf24;
}

.student-timetable-time strong {
    color: rgba(255, 255, 255, .92);
    font-size: .88rem;
}

.student-timetable-time small {
    color: var(--st-text-muted);
    font-size: .68rem;
}

.student-timetable-subject {
    display: inline-flex;
    padding: .3rem .55rem;
    margin-bottom: .42rem;
    border-radius: 999px;
    background: rgba(124, 58, 237, .13);
    color: #c4b5fd;
    font-size: .65rem;
    font-weight: 850;
    letter-spacing: .05em;
    text-transform: uppercase;
}

.student-timetable-course h3 {
    display: flex;
    align-items: center;
    gap: .35rem;
    margin: 0;
    color: rgba(255, 255, 255, .92);
    font-size: .98rem;
    font-weight: 800;
}

.student-timetable-course h3 i {
    color: #8b5cf6;
    font-size: .72rem;
}

.student-timetable-course p {
    display: flex;
    align-items: center;
    gap: .35rem;
    margin: .35rem 0 0;
    color: var(--st-text-muted);
    font-size: .72rem;
}

.student-timetable-meta {
    display: flex;
    align-items: flex-end;
    flex-direction: column;
    gap: .45rem;
}

.student-timetable-meta span {
    display: inline-flex;
    align-items: center;
    gap: .42rem;
    padding: .45rem .6rem;
    border: 1px solid var(--st-border-card);
    border-radius: 999px;
    background: rgba(255, 255, 255, .035);
    color: var(--st-text-secondary);
    font-size: .69rem;
    font-weight: 700;
}

.student-timetable-meta i {
    color: #a78bfa;
}

.student-timetable-empty {
    padding: 3.5rem 1.5rem;
    border: 1px dashed rgba(148, 163, 184, .25);
    border-radius: 22px;
    background: var(--st-bg-card);
    text-align: center;
}

.student-timetable-empty-icon {
    display: grid;
    place-items: center;
    width: 72px;
    height: 72px;
    margin: 0 auto 1rem;
    border-radius: 20px;
    background: rgba(124, 58, 237, .13);
    color: #c4b5fd;
    font-size: 1.8rem;
}

.student-timetable-empty h2 {
    margin: 0 0 .45rem;
    color: rgba(255, 255, 255, .92);
    font-size: 1.25rem;
}

.student-timetable-empty p {
    max-width: 580px;
    margin: 0 auto;
    color: var(--st-text-muted);
    line-height: 1.65;
}

.student-timetable-empty a {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    min-height: 42px;
    padding: 0 .9rem;
    margin-top: 1rem;
    border-radius: 11px;
    background: var(--st-gradient-primary);
    color: #fff;
    font-size: .75rem;
    font-weight: 800;
}

html.light-mode .student-timetable-filter-card,
html.light-mode .student-timetable-overview article,
html.light-mode .student-timetable-day,
html.light-mode .student-timetable-empty {
    border-color: rgba(15, 23, 42, .08);
    background: #fff;
    box-shadow: 0 12px 35px rgba(15, 23, 42, .06);
}

html.light-mode .student-timetable-filter-heading,
html.light-mode .student-timetable-day-header,
html.light-mode .student-timetable-row {
    border-color: rgba(15, 23, 42, .08);
}

html.light-mode .student-timetable-filter-heading h2,
html.light-mode .student-timetable-overview strong,
html.light-mode .student-timetable-day-date strong,
html.light-mode .student-timetable-time strong,
html.light-mode .student-timetable-course h3,
html.light-mode .student-timetable-empty h2 {
    color: #1e293b;
}

html.light-mode .student-timetable-field select {
    border-color: #dbe2ea;
    background: #fff;
    color: #1e293b;
}

html.light-mode .student-timetable-reset-button,
html.light-mode .student-timetable-meta span {
    border-color: #e2e8f0;
    background: #f8fafc;
    color: #475569;
}

html.light-mode .student-timetable-day-header,
html.light-mode .student-timetable-row:hover {
    background: #f8fafc;
}

@media (max-width: 1050px) {
    .student-timetable-filter-form {
        grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
    }

    .student-timetable-filter-actions {
        grid-column: 1 / -1;
        justify-content: flex-end;
    }

    .student-timetable-row {
        grid-template-columns: 150px minmax(0, 1fr);
    }

    .student-timetable-meta {
        grid-column: 2;
        align-items: flex-start;
        flex-direction: row;
        flex-wrap: wrap;
    }
}

@media (max-width: 760px) {
    .student-timetable-hero {
        align-items: flex-start;
        flex-direction: column;
        padding: 1.5rem;
    }

    .student-timetable-hero-stat {
        width: 100%;
    }

    .student-timetable-filter-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .student-timetable-path-summary {
        width: 100%;
    }

    .student-timetable-filter-form {
        grid-template-columns: 1fr;
    }

    .student-timetable-arrow {
        display: none;
    }

    .student-timetable-filter-actions {
        grid-column: auto;
        align-items: stretch;
        flex-direction: column;
    }

    .student-timetable-filter-button,
    .student-timetable-reset-button {
        width: 100%;
    }

    .student-timetable-overview {
        grid-template-columns: 1fr;
    }

    .student-timetable-day-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .student-timetable-row {
        grid-template-columns: 1fr;
        gap: .85rem;
    }

    .student-timetable-meta {
        grid-column: auto;
    }
}

@media (max-width: 480px) {
    .student-timetable-filter-card {
        padding: 1rem;
    }

    .student-timetable-day-header,
    .student-timetable-row {
        padding: 1rem;
    }

    .student-timetable-meta {
        align-items: stretch;
        flex-direction: column;
    }

    .student-timetable-meta span {
        border-radius: 10px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const subjectSelect = document.getElementById('scheduleSubject');
    const levelSelect = document.getElementById('scheduleLevel');
    const levelsBySubject = @json($levelsBySubject);
    const initiallySelectedLevel = @json($selectedLevelId);

    if (!subjectSelect || !levelSelect) {
        return;
    }

    function fillLevels(selectedLevelId = null) {
        const subjectId = subjectSelect.value;
        const levels = subjectId && levelsBySubject[subjectId]
            ? levelsBySubject[subjectId]
            : [];

        levelSelect.innerHTML = '<option value="">Tous les niveaux</option>';
        levelSelect.disabled = !subjectId;

        levels.forEach(function (level) {
            const option = document.createElement('option');
            option.value = level.id;
            option.textContent = level.name;
            option.selected = selectedLevelId !== null
                && Number(selectedLevelId) === Number(level.id);
            levelSelect.appendChild(option);
        });
    }

    fillLevels(initiallySelectedLevel);

    subjectSelect.addEventListener('change', function () {
        fillLevels(null);
    });
});
</script>
@endpush
