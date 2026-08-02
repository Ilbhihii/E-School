@extends('layouts.prof')

@section('title', 'Emploi du temps')
@section('page_title', 'Emploi du temps')
@section('breadcrumb', 'Planning hebdomadaire')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-calendar3-week-fill"></i>
            Organisation
        </span>

        <h1 class="pp-page-title">
            Mon emploi du temps
        </h1>

        <p class="pp-page-description">
            Consultez les jours et les horaires définis
            par l’administration pour vos classes.
        </p>
    </div>

    <div class="pp-page-actions">
        <span class="pp-soft-chip">
            <i class="bi bi-people"></i>
            {{ $classes->count() }} classe(s)
        </span>

        <span class="pp-soft-chip">
            <i class="bi bi-calendar-check"></i>
            {{ $assignments->count() }} séance(s)
        </span>
    </div>
</section>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-list-check"></i>
                Mes séances hebdomadaires
            </h2>

            <p class="pp-panel-subtitle">
                Ces horaires sont enregistrés lors
                de votre assignation par l’administration.
            </p>
        </div>

        <span class="pp-panel-meta">
            Lecture seule
        </span>
    </header>

    <div class="pp-panel-body">
        @if($assignments->isNotEmpty())
            <div class="prof-weekly-assignment-grid">
                @foreach($assignments as $assignment)
                    <article
                        class="prof-weekly-assignment"
                    >
                        <div class="prof-weekly-day">
                            <i class="bi bi-calendar3"></i>
                            {{ $assignment->day_label }}
                        </div>

                        <div class="prof-weekly-copy">
                            <strong>
                                {{
                                    $assignment
                                        ->subject
                                        ?->name
                                    ?? 'Matière'
                                }}
                            </strong>

                            <span>
                                {{
                                    $assignment
                                        ->level
                                        ?->name
                                    ?? 'Niveau'
                                }}
                                →
                                {{
                                    $assignment
                                        ->classRoom
                                        ?->name
                                    ?? 'Classe'
                                }}
                            </span>
                        </div>

                        <div class="prof-weekly-time">
                            <i class="bi bi-clock-fill"></i>
                            {{
                                $assignment
                                    ->time_range_label
                            }}
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="prof-schedule-empty">
                <span>
                    <i class="bi bi-calendar-x"></i>
                </span>

                <div>
                    <h3>
                        Aucun horaire défini
                    </h3>

                    <p>
                        L’administration doit ajouter
                        un jour et une heure à votre
                        assignation.
                    </p>
                </div>
            </div>
        @endif
    </div>
</section>

<section class="pp-panel pp-section-gap">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-funnel-fill"></i>
                Affichage du calendrier
            </h2>

            <p class="pp-panel-subtitle">
                Filtrez le calendrier selon
                les classes qui vous sont assignées.
            </p>
        </div>

        <span
            class="pp-panel-meta"
            id="scheduleState"
        >
            Chargement…
        </span>
    </header>

    <div class="pp-panel-body">
        <div class="pp-calendar-toolbar">
            <div class="pp-calendar-filter">
                <label
                    for="classFilter"
                    class="pp-label"
                >
                    <i class="bi bi-people"></i>
                    Filtrer par classe
                </label>

                <select
                    id="classFilter"
                    class="adm-form-select"
                >
                    <option value="">
                        Toutes les classes
                    </option>

                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pp-calendar-hints">
                <span class="pp-soft-chip">
                    <i class="bi bi-shield-lock"></i>
                    Horaire défini par l’administration
                </span>

                <span class="pp-soft-chip">
                    <i class="bi bi-calendar-day"></i>
                    Semaine, jour ou mois
                </span>
            </div>
        </div>
    </div>
</section>

<section class="pp-panel pp-section-gap">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-calendar-week"></i>
                Calendrier hebdomadaire
            </h2>

            <p class="pp-panel-subtitle">
                Le calendrier est affiché en lecture seule.
            </p>
        </div>
    </header>

    <div class="pp-calendar-shell">
        <div id="calendar"></div>
    </div>
</section>

@push('head')
<link
    href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css"
    rel="stylesheet"
>
<script
    src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"
></script>

<style>
.prof-weekly-assignment-grid {
    display: grid;
    grid-template-columns:
        repeat(2, minmax(0, 1fr));
    gap: 0.75rem;
}

.prof-weekly-assignment {
    display: grid;
    grid-template-columns:
        auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 12px;
    min-height: 82px;
    padding: 13px;
    border: 1px solid rgba(139,92,246,0.13);
    border-radius: 14px;
    background:
        linear-gradient(
            145deg,
            rgba(124,58,237,0.055),
            rgba(14,165,233,0.025)
        );
}

.prof-weekly-day {
    display: inline-flex;
    min-height: 35px;
    align-items: center;
    gap: 6px;
    padding: 0 10px;
    color: #C4B5FD;
    border: 1px solid rgba(167,139,250,0.14);
    border-radius: 10px;
    background: rgba(124,58,237,0.09);
    font-size: 0.64rem;
    font-weight: 800;
    white-space: nowrap;
}

.prof-weekly-copy {
    display: flex;
    min-width: 0;
    flex-direction: column;
    gap: 3px;
}

.prof-weekly-copy strong {
    overflow: hidden;
    color: var(--pp-text);
    font-size: 0.73rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.prof-weekly-copy span {
    overflow: hidden;
    color: var(--pp-muted);
    font-size: 0.61rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.prof-weekly-time {
    display: inline-flex;
    min-height: 35px;
    align-items: center;
    gap: 6px;
    padding: 0 10px;
    color: #67E8F9;
    border: 1px solid rgba(34,211,238,0.14);
    border-radius: 10px;
    background: rgba(8,145,178,0.08);
    font-size: 0.64rem;
    font-weight: 800;
    white-space: nowrap;
}

.prof-schedule-empty {
    display: flex;
    min-height: 150px;
    align-items: center;
    justify-content: center;
    gap: 14px;
    padding: 24px;
    text-align: left;
    border: 1px dashed rgba(148,163,184,0.17);
    border-radius: 15px;
    background: rgba(15,23,42,0.22);
}

.prof-schedule-empty > span {
    display: grid;
    width: 50px;
    height: 50px;
    flex: 0 0 auto;
    place-items: center;
    color: #A78BFA;
    border-radius: 14px;
    background: rgba(124,58,237,0.10);
    font-size: 1.15rem;
}

.prof-schedule-empty h3 {
    margin: 0 0 4px;
    color: var(--pp-text);
    font-size: 0.82rem;
}

.prof-schedule-empty p {
    margin: 0;
    color: var(--pp-muted);
    font-size: 0.65rem;
    line-height: 1.55;
}

@media (max-width: 900px) {
    .prof-weekly-assignment-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 575.98px) {
    .prof-weekly-assignment {
        grid-template-columns:
            minmax(0, 1fr) auto;
    }

    .prof-weekly-day {
        grid-column: 1 / -1;
        width: fit-content;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const calendarElement =
            document.getElementById(
                'calendar'
            );

        const classFilter =
            document.getElementById(
                'classFilter'
            );

        const state =
            document.getElementById(
                'scheduleState'
            );

        if (
            !calendarElement
            || !classFilter
            || typeof FullCalendar === 'undefined'
        ) {
            return;
        }

        const dataUrl =
            @json(route('prof.schedule.data'));

        const calendar =
            new FullCalendar.Calendar(
                calendarElement,
                {
                    initialView:
                        window.innerWidth < 768
                            ? 'timeGridDay'
                            : 'timeGridWeek',
                    locale: 'fr',
                    firstDay: 1,
                    editable: false,
                    selectable: false,
                    eventStartEditable: false,
                    eventDurationEditable: false,
                    nowIndicator: true,
                    allDaySlot: false,
                    height: 'auto',
                    slotMinTime: '07:00:00',
                    slotMaxTime: '22:00:00',
                    slotDuration: '00:30:00',
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                    },
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right:
                            'dayGridMonth,'
                            + 'timeGridWeek,'
                            + 'timeGridDay',
                    },
                    buttonText: {
                        today: "Aujourd'hui",
                        month: 'Mois',
                        week: 'Semaine',
                        day: 'Jour',
                    },
                    events: function (
                        fetchInfo,
                        successCallback,
                        failureCallback
                    ) {
                        const params =
                            new URLSearchParams({
                                start:
                                    fetchInfo
                                        .startStr,
                                end:
                                    fetchInfo
                                        .endStr,
                            });

                        if (classFilter.value) {
                            params.set(
                                'class_id',
                                classFilter.value
                            );
                        }

                        state.textContent =
                            'Chargement…';

                        fetch(
                            dataUrl
                            + '?'
                            + params.toString(),
                            {
                                headers: {
                                    Accept:
                                        'application/json',
                                },
                            }
                        )
                            .then(function (response) {
                                if (!response.ok) {
                                    throw new Error(
                                        'Erreur de chargement'
                                    );
                                }

                                return response.json();
                            })
                            .then(function (events) {
                                state.textContent =
                                    events.length
                                    + ' séance(s)';

                                successCallback(
                                    events
                                );
                            })
                            .catch(function (error) {
                                state.textContent =
                                    'Erreur de chargement';

                                failureCallback(
                                    error
                                );
                            });
                    },
                    eventDidMount: function (info) {
                        const details =
                            info.event.extendedProps;

                        info.el.title = [
                            details.subject,
                            details.level,
                            details.class,
                            details.day,
                            details.time,
                        ]
                            .filter(Boolean)
                            .join(' · ');
                    },
                }
            );

        classFilter.addEventListener(
            'change',
            function () {
                calendar.refetchEvents();
            }
        );

        calendar.render();
    }
);
</script>
@endpush
@endsection
