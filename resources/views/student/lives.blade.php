@extends('layouts.student')

@section('title', 'Lives')
@section('page_title', 'Lives')
@section('breadcrumb', 'Lives')

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
    $liveNowCount = $lives
        ->where('schedule_status', 'live')
        ->count();

    $upcomingCount = $lives
        ->where('schedule_status', 'upcoming')
        ->count();

    $endedCount = $lives
        ->where('schedule_status', 'ended')
        ->count();
@endphp

<div class="sp-page sp-lives-page">

    <section class="sp-hero sp-hero-live">
        <div class="sp-hero-icon">
            <i class="bi bi-broadcast-pin"></i>
        </div>

        <div class="sp-hero-copy">
            <span class="sp-kicker">
                Sessions en direct
            </span>

            <h2>Mes lives</h2>

            <p>
                Consultez les sessions programmées pour votre
                niveau et votre classe, puis rejoignez-les
                depuis un accès sécurisé.
            </p>
        </div>

        <div class="sp-live-indicator">
            <span class="{{ $liveNowCount > 0 ? 'active' : '' }}"></span>

            <div>
                <strong>{{ $liveNowCount }}</strong>
                <small>en direct</small>
            </div>
        </div>
    </section>

    @if(
        $assignedLevels->count() > 1
        || $assignedClasses->count() > 1
    )
        <section class="sp-filter-card">
            <div class="sp-card-heading">
                <div class="sp-card-heading-icon red">
                    <i class="bi bi-funnel-fill"></i>
                </div>

                <div>
                    <h3>Filtrer les lives</h3>

                    <p>
                        Sélectionnez votre niveau et votre classe.
                    </p>
                </div>

                @if($hasActiveFilter)
                    <a
                        href="{{ route('student.lives') }}"
                        class="sp-reset-link"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Tout afficher
                    </a>
                @endif
            </div>

            <form
                method="GET"
                action="{{ route('student.lives') }}"
                id="studentLiveFilterForm"
                class="sp-filter-grid"
            >
                <div class="sp-field">
                    <label for="studentLiveLevel">
                        Niveau
                    </label>

                    <div class="sp-select-wrap">
                        <i class="bi bi-mortarboard-fill"></i>

                        <select
                            name="level_id"
                            id="studentLiveLevel"
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

                <div class="sp-field">
                    <label for="studentLiveClass">
                        Classe
                    </label>

                    <div class="sp-select-wrap">
                        <i class="bi bi-building-fill"></i>

                        <select
                            name="class_id"
                            id="studentLiveClass"
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

                <button
                    type="submit"
                    class="sp-primary-button red"
                >
                    <i class="bi bi-search"></i>
                    Afficher
                </button>
            </form>
        </section>
    @endif

    <section class="sp-metrics sp-metrics-four">
        <article class="sp-metric-card">
            <span class="sp-metric-icon red">
                <i class="bi bi-broadcast-pin"></i>
            </span>

            <div>
                <small>En direct</small>
                <strong>{{ $liveNowCount }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon blue">
                <i class="bi bi-calendar-event-fill"></i>
            </span>

            <div>
                <small>À venir</small>
                <strong>{{ $upcomingCount }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon green">
                <i class="bi bi-check-circle-fill"></i>
            </span>

            <div>
                <small>Terminés</small>
                <strong>{{ $endedCount }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon violet">
                <i class="bi bi-building-fill"></i>
            </span>

            <div>
                <small>Classes affichées</small>
                <strong>{{ $visibleClassCount }}</strong>
            </div>
        </article>
    </section>

    <section class="sp-current-filter">
        <div>
            <span class="sp-current-filter-icon red">
                <i class="bi bi-diagram-3-fill"></i>
            </span>

            <div>
                <small>Parcours affiché</small>

                <strong>
                    {{
                        $selectedLevel
                            ? $selectedLevel->name
                            : 'Tous les niveaux'
                    }}
                    <i class="bi bi-chevron-right"></i>
                    {{
                        $selectedClass
                            ? $selectedClass->name
                            : 'Toutes les classes'
                    }}
                </strong>
            </div>
        </div>

        <span class="sp-soft-badge">
            {{ $lives->count() }}
            session{{ $lives->count() > 1 ? 's' : '' }}
        </span>
    </section>

    @if($lives->isNotEmpty())
        <section class="sp-calendar-card">
            <header class="sp-section-header">
                <div>
                    <span class="sp-section-icon red">
                        <i class="bi bi-calendar3"></i>
                    </span>

                    <div>
                        <h3>Calendrier des lives</h3>

                        <p>
                            Cliquez sur une session pour ouvrir
                            son accès sécurisé.
                        </p>
                    </div>
                </div>

                <span class="sp-status-badge red">
                    <i class="bi bi-shield-lock-fill"></i>
                    Accès protégé
                </span>
            </header>

            <div class="sp-calendar-body">
                <div id="livesCalendar"></div>
            </div>
        </section>

        <section>
            <header class="sp-list-header">
                <div>
                    <span class="sp-kicker">
                        Sessions disponibles
                    </span>

                    <h3>Liste des lives</h3>
                </div>

                <span class="sp-soft-badge">
                    {{ $lives->count() }}
                    résultat{{ $lives->count() > 1 ? 's' : '' }}
                </span>
            </header>

            <div class="sp-live-grid">
                @foreach($lives as $live)
                    @php
                        $status = $live->schedule_status;

                        $statusClass = match ($status) {
                            'live' => 'live',
                            'upcoming' => 'upcoming',
                            'ended' => 'ended',
                            default => 'unscheduled',
                        };

                        $statusLabel = match ($status) {
                            'live' => 'En direct',
                            'upcoming' => 'À venir',
                            'ended' => 'Terminée',
                            default => 'À programmer',
                        };

                        $meetingHost = strtolower(
                            (string) parse_url(
                                $live->stream_url,
                                PHP_URL_HOST
                            )
                        );

                        $isTeams =
                            $live->provider === 'teams'
                            || in_array(
                                $meetingHost,
                                [
                                    'teams.microsoft.com',
                                    'teams.live.com',
                                ],
                                true
                            );

                        $providerName = $isTeams
                            ? 'Microsoft Teams'
                            : 'Google Meet';

                        $providerIcon = $isTeams
                            ? 'microsoft-teams'
                            : 'camera-video-fill';
                    @endphp

                    <article class="sp-live-card {{ $statusClass }}">
                        <div class="sp-live-card-cover">
                            <span class="sp-live-card-status">
                                <i class="bi bi-circle-fill"></i>
                                {{ $statusLabel }}
                            </span>

                            <span class="sp-live-card-icon">
                                <i class="bi bi-camera-video-fill"></i>
                            </span>

                            <span class="sp-live-provider">
                                <i class="bi bi-{{ $providerIcon }}"></i>
                                {{ $providerName }}
                            </span>
                        </div>

                        <div class="sp-live-card-body">
                            <h4>{{ $live->title }}</h4>

                            @if($live->description)
                                <p>
                                    {{
                                        \Illuminate\Support\Str::limit(
                                            $live->description,
                                            115
                                        )
                                    }}
                                </p>
                            @else
                                <p>
                                    Session en direct programmée pour
                                    votre parcours pédagogique.
                                </p>
                            @endif

                            <div class="sp-live-path">
                                <span>
                                    <i class="bi bi-mortarboard-fill"></i>
                                    {{
                                        $live->classRoom?->level?->name
                                        ?? 'Niveau à confirmer'
                                    }}
                                </span>

                                <span>
                                    <i class="bi bi-building-fill"></i>
                                    {{
                                        $live->classRoom?->name
                                        ?? 'Classe à confirmer'
                                    }}
                                </span>
                            </div>

                            <div class="sp-live-date">
                                <span>
                                    <i class="bi bi-calendar3"></i>
                                    {{
                                        $live->start_date_time
                                            ? $live->start_date_time
                                                ->format('d/m/Y')
                                            : 'Date à confirmer'
                                    }}
                                </span>

                                <span>
                                    <i class="bi bi-clock"></i>
                                    {{
                                        $live->start_date_time
                                            ? $live->start_date_time
                                                ->format('H:i')
                                            : '--:--'
                                    }}
                                    @if($live->end_date_time)
                                        –
                                        {{
                                            $live->end_date_time
                                                ->format('H:i')
                                        }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <footer class="sp-live-card-footer">
                            @if($status === 'ended')
                                <span class="sp-disabled-button">
                                    <i class="bi bi-check-circle"></i>
                                    Session terminée
                                </span>
                            @elseif($live->stream_url)
                                <a
                                    href="{{
                                        route(
                                            'live.access.request',
                                            $live
                                        )
                                    }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="sp-primary-button red"
                                >
                                    <i class="bi bi-shield-lock-fill"></i>
                                    {{
                                        $status === 'live'
                                            ? 'Rejoindre maintenant'
                                            : 'Accès sécurisé'
                                    }}
                                </a>
                            @else
                                <span class="sp-disabled-button">
                                    <i class="bi bi-clock"></i>
                                    Lien à confirmer
                                </span>
                            @endif
                        </footer>
                    </article>
                @endforeach
            </div>
        </section>
    @else
        <section class="sp-empty-state">
            <span class="sp-empty-icon red">
                <i class="bi bi-camera-video-off-fill"></i>
            </span>

            <h3>Aucun live disponible</h3>

            <p>
                Les sessions apparaîtront ici dès qu’elles seront
                programmées pour votre parcours.
            </p>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById(
        'studentLiveFilterForm'
    );

    const levelSelect = document.getElementById(
        'studentLiveLevel'
    );

    const classSelect = document.getElementById(
        'studentLiveClass'
    );

    const classesByLevel = @json($classOptionsByLevel);

    if (filterForm && levelSelect && classSelect) {
        function appendClassOption(
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

                appendClassOption(
                    '',
                    'Choisissez d’abord un niveau',
                    true
                );

                filterForm.submit();
                return;
            }

            const options = classesByLevel[levelId] || [];

            classSelect.disabled = false;

            if (options.length === 0) {
                appendClassOption(
                    '',
                    'Aucune classe assignée',
                    true
                );

                filterForm.submit();
                return;
            }

            options.forEach(function (classRoom, index) {
                appendClassOption(
                    classRoom.id,
                    classRoom.name,
                    index === 0
                );
            });

            filterForm.submit();
        });

        classSelect.addEventListener('change', function () {
            filterForm.submit();
        });
    }

    const calendarElement = document.getElementById(
        'livesCalendar'
    );

    if (!calendarElement || typeof FullCalendar === 'undefined') {
        return;
    }

    const calendar = new FullCalendar.Calendar(
        calendarElement,
        {
            initialView:
                window.innerWidth < 768
                    ? 'listMonth'
                    : 'dayGridMonth',

            locale: 'fr',
            firstDay: 1,
            height: 'auto',
            expandRows: true,
            dayMaxEvents: 3,
            navLinks: true,
            nowIndicator: true,

            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },

            buttonText: {
                today: "Aujourd’hui",
                month: 'Mois',
                week: 'Semaine',
                list: 'Liste'
            },

            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },

            events: [
                @foreach($lives as $live)
                    @if($live->start_date_time)
                        {
                            id: '{{ $live->id }}',
                            title: @json(
                                \Illuminate\Support\Str::limit(
                                    $live->title,
                                    30
                                )
                            ),
                            start: '{{
                                $live->start_date_time
                                    ->format('Y-m-d\TH:i:s')
                            }}',
                            end: '{{
                                $live->end_date_time
                                    ->format('Y-m-d\TH:i:s')
                            }}',
                            url: @json(
                                $live->stream_url
                                    ? route(
                                        'live.access.request',
                                        $live
                                    )
                                    : ''
                            ),
                            backgroundColor:
                                '{{ $live->schedule_status === "live"
                                    ? "#dc3545"
                                    : ($live->schedule_status === "upcoming"
                                        ? "#4169f5"
                                        : "#475569") }}',
                            borderColor: 'transparent',
                            textColor: '#ffffff'
                        },
                    @endif
                @endforeach
            ],

            eventClick: function (info) {
                if (info.event.url) {
                    info.jsEvent.preventDefault();

                    window.open(
                        info.event.url,
                        '_blank',
                        'noopener'
                    );
                }
            }
        }
    );

    calendar.render();
});
</script>
@endpush
