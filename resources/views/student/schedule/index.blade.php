@extends('layouts.student')

@section('title', 'Emploi du temps')
@section('page_title', 'Emploi du temps')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau → Planning')

@push('head')
<link
    href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css"
    rel="stylesheet"
>
<script
    src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"
></script>
@endpush

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

    /*
     * Le bloc placé au-dessus du calendrier doit représenter
     * uniquement le programme d'aujourd'hui.
     */
    $todayKey = now()->toDateString();
    $todayOccurrences = $days->get(
        $todayKey,
        collect()
    );
@endphp

<div class="sp-page sp-schedule-page">
    <section class="sp-hero sp-hero-schedule">
        <div class="sp-hero-icon">
            <i class="bi bi-calendar-week-fill"></i>
        </div>

        <div class="sp-hero-copy">
            <span class="sp-kicker">Organisation des cours</span>
            <h2>Mon emploi du temps</h2>
            <p>
                Consultez vos séances par semaine ou par mois,
                uniquement pour votre Matière → Niveau → Classe → Créneau.
            </p>
        </div>

        <div class="sp-hero-summary">
            <strong>{{ $sessionCount }}</strong>
            <span>
                séance{{ $sessionCount > 1 ? 's' : '' }}
                dans les 35 prochains jours
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
                <p>Matière → Niveau → Classe → Créneau.</p>
            </div>

            @if(
                $selectedSubjectId
                || $selectedLevelId
                || $selectedClassId
                || $selectedSlotId
            )
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
                <label>Matière</label>
                <div class="sp-select-wrap">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <select
                        name="subject_id"
                        id="scheduleSubject"
                    >
                        <option value="">Toutes les matières</option>

                        @foreach($subjectsOptions as $item)
                            <option
                                value="{{ $item['id'] }}"
                                {{
                                    (string) $selectedSubjectId
                                    === (string) $item['id']
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $item['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="sp-field">
                <label>Niveau</label>
                <div class="sp-select-wrap">
                    <i class="bi bi-layers-fill"></i>
                    <select
                        name="level_id"
                        id="scheduleLevel"
                        disabled
                    >
                        <option value="">Tous les niveaux</option>
                    </select>
                </div>
            </div>

            <div class="sp-field">
                <label>Classe</label>
                <div class="sp-select-wrap">
                    <i class="bi bi-building-fill"></i>
                    <select
                        name="class_id"
                        id="scheduleClass"
                        disabled
                    >
                        <option value="">Toutes les classes</option>
                    </select>
                </div>
            </div>

            <div class="sp-field">
                <label>Créneau</label>
                <div class="sp-select-wrap">
                    <i class="bi bi-clock-fill"></i>
                    <select
                        name="class_slot_id"
                        id="scheduleSlot"
                        disabled
                    >
                        <option value="">Tous les créneaux</option>
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
                <small>Séances à venir</small>
                <strong>{{ $sessionCount }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon blue">
                <i class="bi bi-diagram-3-fill"></i>
            </span>
            <div>
                <small>Parcours assignés</small>
                <strong>{{ $paths->count() }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon green">
                <i class="bi bi-clock-fill"></i>
            </span>
            <div>
                <small>Créneaux visibles</small>
                <strong>
                    {{
                        $visiblePaths
                            ->pluck('class_slot_id')
                            ->unique()
                            ->count()
                    }}
                </strong>
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
                    {{ $selectedSubject?->name ?? 'Toutes les matières' }}
                    <i class="bi bi-chevron-right"></i>
                    {{ $selectedLevel?->name ?? 'Tous les niveaux' }}
                    <i class="bi bi-chevron-right"></i>
                    {{ $selectedClass?->name ?? 'Toutes les classes' }}
                    <i class="bi bi-chevron-right"></i>
                    {{ $selectedSlot?->code ?? 'Tous les créneaux' }}
                </strong>
            </div>
        </div>

        <span class="sp-soft-badge">
            {{ $dayCount }} jour{{ $dayCount > 1 ? 's' : '' }} à venir
        </span>
    </section>


    <div class="sp-schedule-days sp-today-schedule">
        @if($todayOccurrences->isNotEmpty())
            @php
                $firstToday = $todayOccurrences->first();
            @endphp

            <section class="sp-day-card">
                <header class="sp-day-header">
                    <div class="sp-day-date">
                        <span class="sp-day-short">
                            {{ $firstToday['day_short'] }}
                        </span>

                        <div>
                            <h3>{{ $firstToday['date_label'] }}</h3>
                            <p>
                                {{ $todayOccurrences->count() }}
                                séance{{
                                    $todayOccurrences->count() > 1
                                        ? 's'
                                        : ''
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
                    @foreach($todayOccurrences as $occurrence)
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
                                        {{ $occurrence['duration_label'] }}
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

                                    @if(!empty($occurrence['slot_code']))
                                        <i class="bi bi-chevron-right"></i>
                                        {{ $occurrence['slot_code'] }}
                                    @endif
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
        @else
            <section class="sp-empty-state">
                <span class="sp-empty-icon">
                    <i class="bi bi-calendar2-check"></i>
                </span>

                <h3>Aucune séance aujourd’hui</h3>

                <p>
                    Consultez le calendrier ci-dessous pour voir
                    votre prochaine séance.
                </p>
            </section>
        @endif
    </div>

    <section class="sp-calendar-card sp-schedule-calendar-card">
        <header class="sp-section-header sp-schedule-calendar-head">
            <div>
                <span class="sp-section-icon violet">
                    <i class="bi bi-calendar3"></i>
                </span>

                <div>
                    <h3>Mon calendrier</h3>
                    <p>
                        Consultez votre emploi du temps par
                        <strong>semaine</strong> ou par
                        <strong>mois</strong>.
                    </p>
                </div>
            </div>

            <span
                class="sp-calendar-loading"
                id="scheduleCalendarState"
            >
                <i class="bi bi-cloud-check"></i>
                Planning chargé
            </span>
        </header>

        <div class="sp-calendar-body">
            <div id="scheduleCalendar"></div>
        </div>
    </section>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const subject =
            document.getElementById('scheduleSubject');

        const level =
            document.getElementById('scheduleLevel');

        const classroom =
            document.getElementById('scheduleClass');

        const slot =
            document.getElementById('scheduleSlot');

        if (!subject || !level || !classroom || !slot) {
            return;
        }

        const levelsBySubject =
            @json($levelsBySubject);

        const classesBySubjectLevel =
            @json($classesBySubjectLevel);

        const slotsByPath =
            @json($slotsByPath);

        const selectedLevelId =
            @json((string) ($selectedLevelId ?? ''));

        const selectedClassId =
            @json((string) ($selectedClassId ?? ''));

        const selectedSlotId =
            @json((string) ($selectedSlotId ?? ''));

        function addOption(
            select,
            value,
            label,
            selected = false
        ) {
            const option =
                document.createElement('option');

            option.value = String(value);
            option.textContent = label;
            option.selected = selected;

            select.appendChild(option);
        }

        function fillSlots(wanted = '') {
            slot.innerHTML = '';
            addOption(
                slot,
                '',
                'Tous les créneaux'
            );

            const options = (
                ((
                    slotsByPath[
                        String(subject.value)
                    ] || {}
                )[
                    String(level.value)
                ] || {})[
                    String(classroom.value)
                ] || []
            );

            options.forEach(item => addOption(
                slot,
                item.id,
                item.code,
                String(item.id) === String(wanted)
            ));

            slot.disabled =
                !subject.value
                || !level.value
                || !classroom.value;
        }

        function fillClasses(
            wanted = '',
            wantedSlot = ''
        ) {
            classroom.innerHTML = '';

            addOption(
                classroom,
                '',
                'Toutes les classes'
            );

            const options = (
                (
                    classesBySubjectLevel[
                        String(subject.value)
                    ] || {}
                )[
                    String(level.value)
                ] || []
            );

            options.forEach(item => addOption(
                classroom,
                item.id,
                item.name,
                String(item.id) === String(wanted)
            ));

            classroom.disabled =
                !subject.value
                || !level.value;

            fillSlots(wantedSlot);
        }

        function fillLevels(
            wanted = '',
            wantedClass = '',
            wantedSlot = ''
        ) {
            level.innerHTML = '';

            addOption(
                level,
                '',
                'Tous les niveaux'
            );

            const options =
                levelsBySubject[
                    String(subject.value)
                ] || [];

            options.forEach(item => addOption(
                level,
                item.id,
                item.name,
                String(item.id) === String(wanted)
            ));

            level.disabled = !subject.value;

            fillClasses(
                wantedClass,
                wantedSlot
            );
        }

        subject.addEventListener(
            'change',
            () => fillLevels()
        );

        level.addEventListener(
            'change',
            () => fillClasses()
        );

        classroom.addEventListener(
            'change',
            () => fillSlots()
        );

        fillLevels(
            selectedLevelId,
            selectedClassId,
            selectedSlotId
        );
    }
);

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const calendarElement =
            document.getElementById(
                'scheduleCalendar'
            );

        const calendarState =
            document.getElementById(
                'scheduleCalendarState'
            );

        if (
            !calendarElement
            || typeof FullCalendar === 'undefined'
        ) {
            return;
        }

        const dataUrl =
            @json(route('student.schedule.index'));

        const selectedFilters = {
            subject_id:
                @json($selectedSubjectId),
            level_id:
                @json($selectedLevelId),
            class_id:
                @json($selectedClassId),
            class_slot_id:
                @json($selectedSlotId),
        };

        const savedView =
            window.localStorage.getItem(
                'studentScheduleCalendarView'
            );

        const allowedViews = [
            'timeGridWeek',
            'dayGridMonth',
        ];

        const initialView =
            allowedViews.includes(savedView)
                ? savedView
                : (
                    window.innerWidth < 768
                        ? 'dayGridMonth'
                        : 'timeGridWeek'
                );

        const calendar =
            new FullCalendar.Calendar(
                calendarElement,
                {
                    initialView,
                    locale: 'fr',
                    firstDay: 1,
                    height: 'auto',
                    expandRows: true,
                    nowIndicator: true,
                    allDaySlot: false,
                    editable: false,
                    selectable: false,
                    eventStartEditable: false,
                    eventDurationEditable: false,
                    slotMinTime: '07:00:00',
                    slotMaxTime: '22:00:00',
                    slotDuration: '00:30:00',
                    dayMaxEvents: 3,
                    displayEventEnd: true,
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                    },
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right:
                            'timeGridWeek,dayGridMonth',
                    },
                    buttonText: {
                        today: "Aujourd’hui",
                        week: 'Semaine',
                        month: 'Mois',
                    },
                    views: {
                        timeGridWeek: {
                            dayHeaderFormat: {
                                weekday: 'short',
                                day: '2-digit',
                                month: '2-digit',
                            },
                        },
                    },
                    loading: function (isLoading) {
                        if (!calendarState) {
                            return;
                        }

                        calendarState.classList.toggle(
                            'is-loading',
                            isLoading
                        );

                        calendarState.innerHTML =
                            isLoading
                                ? '<i class="bi bi-arrow-repeat"></i> Chargement…'
                                : '<i class="bi bi-cloud-check"></i> Planning chargé';
                    },
                    datesSet: function (info) {
                        if (
                            allowedViews.includes(
                                info.view.type
                            )
                        ) {
                            window.localStorage.setItem(
                                'studentScheduleCalendarView',
                                info.view.type
                            );
                        }
                    },
                    events: function (
                        fetchInfo,
                        successCallback,
                        failureCallback
                    ) {
                        const params =
                            new URLSearchParams({
                                calendar: '1',
                                start: fetchInfo.startStr,
                                end: fetchInfo.endStr,
                            });

                        Object.entries(
                            selectedFilters
                        ).forEach(
                            ([key, value]) => {
                                if (value) {
                                    params.set(
                                        key,
                                        String(value)
                                    );
                                }
                            }
                        );

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
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(
                                        'Erreur de chargement du calendrier.'
                                    );
                                }

                                return response.json();
                            })
                            .then(successCallback)
                            .catch(failureCallback);
                    },
                    eventDidMount: function (info) {
                        const props =
                            info.event.extendedProps;

                        info.el.setAttribute(
                            'title',
                            [
                                props.path,
                                props.time_label,
                                props.teacher,
                            ]
                                .filter(Boolean)
                                .join(' · ')
                        );
                    },
                }
            );

        calendar.render();
    }
);
</script>
@endpush
