@extends('layouts.student')

@section('title', 'Lives')
@section('page_title', 'Lives')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Créneau'
)

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
    <link
        rel="stylesheet"
        href="{{ asset('css/student-live-hub-v2.css') }}"
    >
@endpush

@section('content')
@php
    $todayTotal =
        $todayOccurrences->count()
        + $todayLives->filter(
            fn ($live) =>
                !$todayOccurrences->contains(
                    fn ($occurrence) =>
                        !empty($occurrence['linked_live'])
                        && (int) $occurrence['linked_live']->id
                            === (int) $live->id
                )
        )->count();

    $futureLives = $lives
        ->filter(
            fn ($live) =>
                in_array(
                    $live->schedule_status,
                    ['live', 'upcoming'],
                    true
                )
        )
        ->values();
@endphp

<div class="slh-page">
    {{-- HERO --}}
    <section class="slh-hero">
        <div class="slh-hero-main">
            <span class="slh-hero-icon">
                <i class="bi bi-broadcast-pin"></i>
            </span>

            <div class="slh-hero-copy">
                <span class="slh-eyebrow">
                    Mon espace de cours
                </span>

                <h2>Planning & Lives</h2>

                <p>
                    Votre programme, vos séances et vos accès live
                    réunis dans une seule interface.
                </p>
            </div>
        </div>

        <div class="slh-live-status">
            <span
                class="slh-live-dot {{
                    $liveNowCount > 0
                        ? 'is-live'
                        : ''
                }}"
            ></span>

            <div>
                <strong>{{ $liveNowCount }}</strong>
                <small>
                    {{
                        $liveNowCount > 0
                            ? 'en direct'
                            : 'live en cours'
                    }}
                </small>
            </div>
        </div>
    </section>

    {{-- FILTRES --}}
    @if($paths->isNotEmpty())
        <section class="slh-card slh-filter-card">
            <div class="slh-section-title">
                <div class="slh-title-left">
                    <span class="slh-title-icon">
                        <i class="bi bi-funnel-fill"></i>
                    </span>

                    <div>
                        <h3>Filtrer mon programme</h3>
                        <p>
                            Matière → Niveau → Classe → Créneau
                        </p>
                    </div>
                </div>

                @if($hasActiveFilter)
                    <a
                        href="{{ route('student.lives') }}"
                        class="slh-reset"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Réinitialiser
                    </a>
                @endif
            </div>

            <form
                method="GET"
                action="{{ route('student.lives') }}"
                class="slh-filter-grid"
            >
                <label class="slh-field">
                    <span>Matière</span>

                    <div class="slh-select">
                        <i class="bi bi-journal-bookmark-fill"></i>

                        <select
                            name="subject_id"
                            id="liveSubject"
                        >
                            <option value="">
                                Toutes les matières
                            </option>

                            @foreach($subjects as $item)
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
                </label>

                <label class="slh-field">
                    <span>Niveau</span>

                    <div class="slh-select">
                        <i class="bi bi-layers-fill"></i>

                        <select
                            name="level_id"
                            id="liveLevel"
                            disabled
                        >
                            <option value="">
                                Tous les niveaux
                            </option>
                        </select>
                    </div>
                </label>

                <label class="slh-field">
                    <span>Classe</span>

                    <div class="slh-select">
                        <i class="bi bi-building-fill"></i>

                        <select
                            name="class_id"
                            id="liveClass"
                            disabled
                        >
                            <option value="">
                                Toutes les classes
                            </option>
                        </select>
                    </div>
                </label>

                <label class="slh-field">
                    <span>Créneau</span>

                    <div class="slh-select">
                        <i class="bi bi-clock-fill"></i>

                        <select
                            name="class_slot_id"
                            id="liveSlot"
                            disabled
                        >
                            <option value="">
                                Tous les créneaux
                            </option>
                        </select>
                    </div>
                </label>

                <button
                    type="submit"
                    class="slh-filter-button"
                >
                    <i class="bi bi-search"></i>
                    Afficher
                </button>
            </form>
        </section>
    @endif

    {{-- KPI --}}
    <section class="slh-stats">
        <article class="slh-stat red">
            <span>
                <i class="bi bi-broadcast-pin"></i>
            </span>

            <div>
                <small>En direct</small>
                <strong>{{ $liveNowCount }}</strong>
            </div>
        </article>

        <article class="slh-stat blue">
            <span>
                <i class="bi bi-calendar2-check-fill"></i>
            </span>

            <div>
                <small>Cours aujourd’hui</small>
                <strong>{{ $todayScheduleCount }}</strong>
            </div>
        </article>

        <article class="slh-stat green">
            <span>
                <i class="bi bi-camera-video-fill"></i>
            </span>

            <div>
                <small>Lives à venir</small>
                <strong>{{ $upcomingCount }}</strong>
            </div>
        </article>

        <article class="slh-stat violet">
            <span>
                <i class="bi bi-diagram-3-fill"></i>
            </span>

            <div>
                <small>Mes créneaux</small>
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

    {{-- PROGRAMME DU JOUR --}}
    <section class="slh-card slh-today-card">
        <header class="slh-card-header">
            <div class="slh-title-left">
                <span class="slh-title-icon blue">
                    <i class="bi bi-calendar-event-fill"></i>
                </span>

                <div>
                    <span class="slh-eyebrow">Aujourd’hui</span>
                    <h3>Programme du jour</h3>
                    <p>
                        Cours planifiés et accès live de la journée.
                    </p>
                </div>
            </div>

            <span class="slh-count-badge">
                {{ $todayTotal }}
                élément{{ $todayTotal > 1 ? 's' : '' }}
            </span>
        </header>

        @if(
            $todayOccurrences->isEmpty()
            && $todayLives->isEmpty()
        )
            <div class="slh-empty slh-empty-today">
                <span>
                    <i class="bi bi-calendar2-check"></i>
                </span>

                <div>
                    <strong>Aucune séance aujourd’hui</strong>
                    <small>
                        Consultez le calendrier pour voir
                        votre prochain cours.
                    </small>
                </div>
            </div>
        @else
            <div class="slh-today-list">
                @foreach($todayOccurrences as $occurrence)
                    @php
                        $linkedLive =
                            $occurrence['linked_live']
                            ?? null;
                    @endphp

                    <article class="slh-session">
                        <div class="slh-session-time">
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

                        <div class="slh-session-main">
                            <span class="slh-subject">
                                {{ $occurrence['subject'] }}
                            </span>

                            <h4>
                                {{ $occurrence['level'] }}

                                <i class="bi bi-chevron-right"></i>

                                {{ $occurrence['class_name'] }}

                                @if(
                                    !empty(
                                        $occurrence['slot_code']
                                    )
                                )
                                    <i class="bi bi-chevron-right"></i>

                                    <span class="slh-slot">
                                        {{
                                            $occurrence[
                                                'slot_code'
                                            ]
                                        }}
                                    </span>
                                @endif
                            </h4>

                            <p>
                                <i class="bi bi-diagram-3"></i>
                                {{ $occurrence['path'] }}
                            </p>
                        </div>

                        <div class="slh-session-meta">
                            <span>
                                <i class="bi bi-person-video3"></i>
                                {{ $occurrence['teacher'] }}
                            </span>

                            <span>
                                <i class="bi bi-door-open-fill"></i>
                                {{ $occurrence['room'] }}
                            </span>
                        </div>

                        <div class="slh-session-action">
                            @if(
                                $linkedLive
                                && $linkedLive->stream_url
                                && $linkedLive
                                    ->schedule_status
                                    !== 'ended'
                            )
                                <a
                                    href="{{
                                        route(
                                            'live.access.request',
                                            $linkedLive
                                        )
                                    }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="slh-live-button"
                                >
                                    <i class="bi bi-broadcast-pin"></i>

                                    {{
                                        $linkedLive
                                            ->schedule_status
                                        === 'live'
                                            ? 'Rejoindre'
                                            : 'Accès live'
                                    }}
                                </a>
                            @elseif(
                                $linkedLive
                                && $linkedLive
                                    ->schedule_status
                                    === 'ended'
                            )
                                <span class="slh-muted-action">
                                    <i class="bi bi-check-circle"></i>
                                    Live terminé
                                </span>
                            @else
                                <span class="slh-course-badge">
                                    <i class="bi bi-calendar-check"></i>
                                    Cours planifié
                                </span>
                            @endif
                        </div>
                    </article>
                @endforeach

                @foreach($todayLives as $live)
                    @php
                        $alreadyLinked =
                            $todayOccurrences
                            ->contains(
                                fn ($occurrence) =>
                                    !empty(
                                        $occurrence[
                                            'linked_live'
                                        ]
                                    )
                                    && (int) $occurrence[
                                        'linked_live'
                                    ]->id
                                    === (int) $live->id
                            );
                    @endphp

                    @if(!$alreadyLinked)
                        <article class="slh-session is-live-row">
                            <div class="slh-session-time">
                                <span class="live">
                                    <i class="bi bi-broadcast-pin"></i>
                                </span>

                                <div>
                                    <strong>
                                        {{
                                            $live
                                                ->start_date_time
                                                ?->format('H:i')
                                            ?? '--:--'
                                        }}
                                    </strong>

                                    <small>Live</small>
                                </div>
                            </div>

                            <div class="slh-session-main">
                                <span class="slh-subject live">
                                    Session live
                                </span>

                                <h4>
                                    {{ $live->title }}
                                </h4>

                                <p>
                                    <i class="bi bi-diagram-3"></i>

                                    {{
                                        collect([
                                            $live
                                                ->classSlot
                                                ?->subject
                                                ?->name,
                                            $live
                                                ->classSlot
                                                ?->level
                                                ?->name,
                                            $live
                                                ->classSlot
                                                ?->classRoom
                                                ?->name,
                                            $live
                                                ->classSlot
                                                ?->code,
                                        ])
                                            ->filter()
                                            ->implode(' → ')
                                    }}
                                </p>
                            </div>

                            <div class="slh-session-meta">
                                <span>
                                    <i class="bi bi-camera-video-fill"></i>

                                    {{
                                        $live
                                            ->schedule_status
                                        === 'live'
                                            ? 'En direct'
                                            : 'Session programmée'
                                    }}
                                </span>
                            </div>

                            <div class="slh-session-action">
                                @if(
                                    $live->stream_url
                                    && $live
                                        ->schedule_status
                                        !== 'ended'
                                )
                                    <a
                                        href="{{
                                            route(
                                                'live.access.request',
                                                $live
                                            )
                                        }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="slh-live-button"
                                    >
                                        <i class="bi bi-broadcast-pin"></i>

                                        {{
                                            $live
                                                ->schedule_status
                                            === 'live'
                                                ? 'Rejoindre'
                                                : 'Accès live'
                                        }}
                                    </a>
                                @else
                                    <span class="slh-muted-action">
                                        <i class="bi bi-clock"></i>

                                        {{
                                            $live
                                                ->schedule_status
                                            === 'ended'
                                                ? 'Terminé'
                                                : 'Lien à confirmer'
                                        }}
                                    </span>
                                @endif
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>
        @endif
    </section>

    {{-- MES LIVES --}}
    <section class="slh-card slh-lives-card">
        <header class="slh-card-header">
            <div class="slh-title-left">
                <span class="slh-title-icon red">
                    <i class="bi bi-camera-video-fill"></i>
                </span>

                <div>
                    <span class="slh-eyebrow">Cours en direct</span>
                    <h3>Mes lives</h3>
                    <p>
                        Sessions correspondant exactement
                        à vos groupes.
                    </p>
                </div>
            </div>

            <span class="slh-count-badge red">
                {{ $futureLives->count() }}
                session{{
                    $futureLives->count() > 1
                        ? 's'
                        : ''
                }}
            </span>
        </header>

        @if($futureLives->isNotEmpty())
            <div class="slh-live-grid">
                @foreach($futureLives as $live)
                    @php
                        $status =
                            $live->schedule_status;

                        $isLive =
                            $status === 'live';
                    @endphp

                    <article
                        class="
                            slh-live-card
                            {{ $isLive ? 'is-live' : '' }}
                        "
                    >
                        <div class="slh-live-card-top">
                            <span
                                class="
                                    slh-live-state
                                    {{ $isLive ? 'is-live' : '' }}
                                "
                            >
                                <i class="bi bi-circle-fill"></i>

                                {{
                                    $isLive
                                        ? 'En direct'
                                        : 'À venir'
                                }}
                            </span>

                            <span class="slh-slot slh-slot-large">
                                {{
                                    $live->classSlot?->code
                                    ?? '—'
                                }}
                            </span>
                        </div>

                        <div class="slh-live-card-body">
                            <span class="slh-live-icon">
                                <i class="bi bi-camera-video-fill"></i>
                            </span>

                            <div>
                                <h4>{{ $live->title }}</h4>

                                <p>
                                    {{
                                        collect([
                                            $live
                                                ->classSlot
                                                ?->subject
                                                ?->name,
                                            $live
                                                ->classSlot
                                                ?->level
                                                ?->name,
                                            $live
                                                ->classSlot
                                                ?->classRoom
                                                ?->name,
                                            $live
                                                ->classSlot
                                                ?->code,
                                        ])
                                            ->filter()
                                            ->implode(' → ')
                                    }}
                                </p>
                            </div>
                        </div>

                        <div class="slh-live-card-meta">
                            <span>
                                <i class="bi bi-calendar3"></i>

                                {{
                                    $live->start_date_time
                                        ? $live
                                            ->start_date_time
                                            ->format('d/m/Y')
                                        : 'Date à confirmer'
                                }}
                            </span>

                            <span>
                                <i class="bi bi-clock"></i>

                                {{
                                    $live->start_date_time
                                        ? $live
                                            ->start_date_time
                                            ->format('H:i')
                                        : '--:--'
                                }}

                                @if($live->end_date_time)
                                    –
                                    {{
                                        $live
                                            ->end_date_time
                                            ->format('H:i')
                                    }}
                                @endif
                            </span>
                        </div>

                        <footer class="slh-live-card-footer">
                            @if($live->stream_url)
                                <a
                                    href="{{
                                        route(
                                            'live.access.request',
                                            $live
                                        )
                                    }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="slh-live-button full"
                                >
                                    <i class="bi bi-shield-lock-fill"></i>

                                    {{
                                        $isLive
                                            ? 'Rejoindre maintenant'
                                            : 'Accès au live'
                                    }}
                                </a>
                            @else
                                <span class="slh-muted-action full">
                                    <i class="bi bi-clock"></i>
                                    Lien à confirmer
                                </span>
                            @endif
                        </footer>
                    </article>
                @endforeach
            </div>
        @else
            <div class="slh-empty slh-empty-live">
                <span>
                    <i class="bi bi-camera-video-off-fill"></i>
                </span>

                <div>
                    <strong>Aucun live programmé</strong>
                    <small>
                        Vos cours restent visibles dans
                        le calendrier ci-dessous.
                    </small>
                </div>
            </div>
        @endif
    </section>

    {{-- CALENDRIER --}}
    <section class="slh-card slh-calendar-card">
        <header class="slh-card-header slh-calendar-head">
            <div class="slh-title-left">
                <span class="slh-title-icon violet">
                    <i class="bi bi-calendar3"></i>
                </span>

                <div>
                    <span class="slh-eyebrow">Vue globale</span>
                    <h3>Calendrier des cours & lives</h3>
                    <p>
                        Consultez votre programme
                        par semaine ou par mois.
                    </p>
                </div>
            </div>

            <div class="slh-legend">
                <span>
                    <i class="course"></i>
                    Cours
                </span>

                <span>
                    <i class="live"></i>
                    Live
                </span>
            </div>
        </header>

        <div class="slh-calendar-body">
            <div id="livePlanningCalendar"></div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const subject =
            document.getElementById('liveSubject');

        const level =
            document.getElementById('liveLevel');

        const classroom =
            document.getElementById('liveClass');

        const slot =
            document.getElementById('liveSlot');

        if (
            !subject
            || !level
            || !classroom
            || !slot
        ) {
            return;
        }

        const levelsBySubject =
            @json($levelsBySubject);

        const classesBySubjectLevel =
            @json($classesBySubjectLevel);

        const slotsByPath =
            @json($slotsByPath);

        const selectedLevelId =
            @json(
                (string) (
                    $selectedLevelId
                    ?? ''
                )
            );

        const selectedClassId =
            @json(
                (string) (
                    $selectedClassId
                    ?? ''
                )
            );

        const selectedSlotId =
            @json(
                (string) (
                    $selectedSlotId
                    ?? ''
                )
            );

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

            const options =
                (
                    (
                        slotsByPath[
                            String(subject.value)
                        ]
                        || {}
                    )[
                        String(level.value)
                    ]
                    || {}
                )[
                    String(classroom.value)
                ]
                || [];

            options.forEach(item => {
                addOption(
                    slot,
                    item.id,
                    item.code,
                    String(item.id)
                    === String(wanted)
                );
            });

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

            const options =
                (
                    classesBySubjectLevel[
                        String(subject.value)
                    ]
                    || {}
                )[
                    String(level.value)
                ]
                || [];

            options.forEach(item => {
                addOption(
                    classroom,
                    item.id,
                    item.name,
                    String(item.id)
                    === String(wanted)
                );
            });

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
                ]
                || [];

            options.forEach(item => {
                addOption(
                    level,
                    item.id,
                    item.name,
                    String(item.id)
                    === String(wanted)
                );
            });

            level.disabled =
                !subject.value;

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
</script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const element =
            document.getElementById(
                'livePlanningCalendar'
            );

        if (
            !element
            || typeof FullCalendar === 'undefined'
        ) {
            return;
        }

        const events =
            @json($calendarEvents);

        const calendar =
            new FullCalendar.Calendar(
                element,
                {
                    initialView:
                        window.innerWidth < 768
                            ? 'listWeek'
                            : 'timeGridWeek',

                    locale: 'fr',
                    firstDay: 1,
                    height: 620,
                    contentHeight: 560,
                    expandRows: false,
                    nowIndicator: true,
                    allDaySlot: false,
                    slotMinTime: '08:00:00',
                    slotMaxTime: '20:00:00',
                    scrollTime: '08:00:00',
                    slotDuration: '00:30:00',

                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right:
                            'timeGridWeek,dayGridMonth',
                    },

                    buttonText: {
                        today: 'Aujourd’hui',
                        week: 'Semaine',
                        month: 'Mois',
                    },

                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                    },

                    events: events,

                    eventClick: function (info) {
                        if (
                            info.event
                                .extendedProps
                                .type
                            === 'live'
                            && info.event.url
                        ) {
                            info.jsEvent.preventDefault();

                            window.open(
                                info.event.url,
                                '_blank',
                                'noopener'
                            );
                        }
                    },
                }
            );

        calendar.render();
    }
);
</script>
@endpush
