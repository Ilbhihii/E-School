@extends('layouts.prof')

@section('title', 'Emploi du temps')
@section('page_title', 'Emploi du temps')
@section('breadcrumb', 'Matière → Niveau → Classe')

@push('head')
<link
    href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css"
    rel="stylesheet"
>

<style>
/* =========================================================
   PROCHAINES SÉANCES
========================================================= */

.prof-next-sessions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.prof-next-session {
    position: relative;
    display: flex;
    align-items: stretch;
    min-height: 118px;
    overflow: hidden;

    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 18px;

    background:
        radial-gradient(
            circle at 100% 0%,
            rgba(124, 58, 237, 0.10),
            transparent 42%
        ),
        linear-gradient(
            145deg,
            rgba(19, 28, 53, 0.98),
            rgba(11, 18, 37, 0.98)
        );

    transition:
        transform .22s ease,
        border-color .22s ease,
        box-shadow .22s ease;
}

.prof-next-session:hover {
    transform: translateY(-3px);

    border-color:
        rgba(124, 92, 255, 0.30);

    box-shadow:
        0 14px 35px
        rgba(0, 0, 0, 0.20);
}

/* petite barre décorative */
.prof-next-session::before {
    content: "";
    position: absolute;

    top: 16px;
    bottom: 16px;
    left: 0;

    width: 3px;

    border-radius:
        0 999px 999px 0;

    background:
        linear-gradient(
            180deg,
            #8b5cf6,
            #38bdf8
        );
}

/* =========================================================
   DATE
========================================================= */

.prof-session-date {
    width: 105px;
    min-width: 105px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    padding: 17px 12px;

    border-right:
        1px solid
        rgba(255, 255, 255, 0.055);
}

.prof-session-date-icon {
    width: 38px;
    height: 38px;

    display: grid;
    place-items: center;

    margin-bottom: 9px;

    border-radius: 11px;

    color: #c4b5fd;

    background:
        rgba(124, 58, 237, 0.13);

    border:
        1px solid
        rgba(139, 92, 246, 0.18);

    font-size: .95rem;
}

.prof-session-date-value {
    text-align: center;

    color: #ffffff;

    font-size: .79rem;
    font-weight: 800;
    line-height: 1.35;
}

/* =========================================================
   CONTENU
========================================================= */

.prof-session-content {
    flex: 1;
    min-width: 0;

    display: flex;
    flex-direction: column;
    justify-content: center;

    padding: 17px 18px;
}

.prof-session-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    gap: 12px;
}

.prof-session-copy {
    min-width: 0;
}

.prof-session-subject {
    display: block;

    margin: 0 0 7px;

    color: #f8fafc;

    font-size: .98rem;
    font-weight: 850;
    line-height: 1.3;
}

.prof-session-path {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 6px;
}

.prof-session-path-item {
    display: inline-flex;
    align-items: center;

    min-height: 25px;

    padding: 4px 9px;

    border-radius: 999px;

    color:
        rgba(255, 255, 255, 0.66);

    background:
        rgba(255, 255, 255, 0.035);

    border:
        1px solid
        rgba(255, 255, 255, 0.06);

    font-size: .68rem;
    font-weight: 700;
}

.prof-session-arrow {
    color:
        rgba(255, 255, 255, 0.22);

    font-size: .62rem;
}

/* =========================================================
   HORAIRE
========================================================= */

.prof-session-time {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    flex-shrink: 0;

    padding: 8px 11px;

    border-radius: 999px;

    color: #86efac;

    background:
        rgba(34, 197, 94, 0.08);

    border:
        1px solid
        rgba(34, 197, 94, 0.17);

    font-size: .70rem;
    font-weight: 850;
    white-space: nowrap;
}

.prof-session-time i {
    font-size: .72rem;
}

/* =========================================================
   PREMIÈRE SÉANCE
========================================================= */

.prof-next-session.is-next {
    border-color:
        rgba(139, 92, 246, 0.22);
}

.prof-session-next-badge {
    position: absolute;

    top: 9px;
    right: 11px;

    display: inline-flex;
    align-items: center;

    gap: 5px;

    padding: 4px 8px;

    border-radius: 999px;

    color: #ddd6fe;

    background:
        rgba(124, 58, 237, 0.11);

    border:
        1px solid
        rgba(139, 92, 246, 0.17);

    font-size: .57rem;
    font-weight: 850;
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* =========================================================
   COMPTEUR
========================================================= */

.prof-session-counter {
    display: inline-flex;
    align-items: center;

    gap: 7px;

    padding: 7px 11px;

    border-radius: 999px;

    color:
        rgba(255, 255, 255, 0.66);

    background:
        rgba(255, 255, 255, 0.035);

    border:
        1px solid
        rgba(255, 255, 255, 0.06);

    font-size: .69rem;
    font-weight: 750;
}

.prof-session-counter strong {
    color: #ffffff;
}

/* =========================================================
   ÉTAT VIDE
========================================================= */

.prof-sessions-empty {
    min-height: 170px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    text-align: center;

    padding: 26px;

    border:
        1px dashed
        rgba(255, 255, 255, 0.08);

    border-radius: 16px;

    background:
        rgba(255, 255, 255, 0.015);
}

.prof-sessions-empty-icon {
    width: 50px;
    height: 50px;

    display: grid;
    place-items: center;

    margin-bottom: 12px;

    border-radius: 14px;

    color: #a78bfa;

    background:
        rgba(124, 58, 237, 0.09);

    border:
        1px solid
        rgba(139, 92, 246, 0.13);

    font-size: 1.15rem;
}

.prof-sessions-empty strong {
    display: block;

    margin-bottom: 5px;

    color: #f8fafc;

    font-size: .87rem;
}

.prof-sessions-empty span {
    color:
        rgba(255, 255, 255, 0.42);

    font-size: .72rem;
}

/* =========================================================
   CALENDRIER
========================================================= */

.pp-calendar-shell {
    padding: 18px;
}

#calendar {
    color: #f8fafc;
}

#calendar .fc-toolbar-title {
    color: #ffffff;

    font-size: 1rem;
    font-weight: 850;
}

#calendar .fc-button {
    border:
        1px solid
        rgba(255, 255, 255, 0.08) !important;

    border-radius: 9px !important;

    background:
        rgba(255, 255, 255, 0.045) !important;

    color:
        rgba(255, 255, 255, 0.76) !important;

    box-shadow: none !important;

    font-size: .68rem !important;
    font-weight: 750 !important;
}

#calendar .fc-button:hover {
    background:
        rgba(124, 58, 237, 0.16) !important;

    border-color:
        rgba(139, 92, 246, 0.22) !important;

    color: #ffffff !important;
}

#calendar .fc-button-active {
    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #3b82f6
        ) !important;

    color: #ffffff !important;

    border-color:
        transparent !important;
}

#calendar .fc-scrollgrid,
#calendar td,
#calendar th {
    border-color:
        rgba(255, 255, 255, 0.055) !important;
}

#calendar .fc-col-header-cell {
    background:
        rgba(255, 255, 255, 0.025);
}

#calendar .fc-col-header-cell-cushion {
    padding: 10px 6px;

    color:
        rgba(255, 255, 255, 0.62);

    font-size: .65rem;
    font-weight: 800;

    text-decoration: none;
}

#calendar .fc-timegrid-slot-label-cushion {
    color:
        rgba(255, 255, 255, 0.38);

    font-size: .63rem;
}

#calendar .fc-day-today {
    background:
        rgba(124, 58, 237, 0.04) !important;
}

#calendar .fc-event {
    border: 0 !important;

    border-radius: 8px !important;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #3b82f6
        ) !important;

    box-shadow:
        0 5px 14px
        rgba(59, 130, 246, 0.14);

    padding: 2px 4px;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1100px) {
    .prof-next-sessions {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .prof-next-session {
        flex-direction: column;
    }

    .prof-session-date {
        width: 100%;
        min-width: 100%;

        flex-direction: row;

        justify-content: flex-start;

        gap: 10px;

        padding: 13px 15px;

        border-right: 0;

        border-bottom:
            1px solid
            rgba(255, 255, 255, 0.055);
    }

    .prof-session-date-icon {
        margin-bottom: 0;
    }

    .prof-session-top {
        flex-direction: column;
    }

    .prof-session-next-badge {
        position: static;

        width: fit-content;

        margin:
            0 0 10px 15px;
    }
}
</style>
@endpush


@section('content')

{{-- =========================================================
     EN-TÊTE
========================================================= --}}

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
            Le planning affiche uniquement les séances
            correspondant à vos classes assignées.
        </p>

    </div>
</section>


{{-- =========================================================
     FILTRES
========================================================= --}}

@include(
    'prof.partials.path-filter',
    [
        'action' =>
            route('prof.schedule'),

        'buttonLabel' =>
            'Afficher le planning',
    ]
)


{{-- =========================================================
     PROCHAINES SÉANCES
========================================================= --}}

<section class="pp-panel">

    <header class="pp-panel-head">

        <div class="pp-panel-title-wrap">

            <h2 class="pp-panel-title">
                <i class="bi bi-calendar-event-fill"></i>
                Prochaines séances
            </h2>

            <p class="pp-panel-subtitle">
                Matière → Niveau → Classe
            </p>

        </div>


        <div class="prof-session-counter">

            <i class="bi bi-calendar-check"></i>

            <strong>
                {{ $occurrences->count() }}
            </strong>

            séance(s)

        </div>

    </header>


    <div class="pp-panel-body">

        @if($occurrences->isNotEmpty())

            <div class="prof-next-sessions">

                @foreach(
                    $occurrences->take(8)
                    as $occurrence
                )

                    <article
                        class="
                            prof-next-session
                            {{ $loop->first ? 'is-next' : '' }}
                        "
                    >

                        {{-- Première séance --}}

                        @if($loop->first)

                            <span
                                class="
                                    prof-session-next-badge
                                "
                            >
                                <i
                                    class="
                                        bi
                                        bi-lightning-charge-fill
                                    "
                                ></i>

                                Prochaine
                            </span>

                        @endif


                        {{-- Date --}}

                        <div class="prof-session-date">

                            <div
                                class="
                                    prof-session-date-icon
                                "
                            >
                                <i
                                    class="
                                        bi
                                        bi-calendar3
                                    "
                                ></i>
                            </div>

                            <div
                                class="
                                    prof-session-date-value
                                "
                            >
                                {{
                                    $occurrence[
                                        'date_label'
                                    ]
                                }}
                            </div>

                        </div>


                        {{-- Informations --}}

                        <div class="prof-session-content">

                            <div class="prof-session-top">

                                <div class="prof-session-copy">

                                    <strong
                                        class="
                                            prof-session-subject
                                        "
                                    >
                                        {{
                                            $occurrence[
                                                'subject'
                                            ]
                                        }}
                                    </strong>


                                    <div
                                        class="
                                            prof-session-path
                                        "
                                    >

                                        <span
                                            class="
                                                prof-session-path-item
                                            "
                                        >
                                            <i
                                                class="
                                                    bi
                                                    bi-layers
                                                "
                                            ></i>

                                            {{
                                                $occurrence[
                                                    'level'
                                                ]
                                            }}
                                        </span>


                                        <i
                                            class="
                                                bi
                                                bi-chevron-right
                                                prof-session-arrow
                                            "
                                        ></i>


                                        <span
                                            class="
                                                prof-session-path-item
                                            "
                                        >
                                            <i
                                                class="
                                                    bi
                                                    bi-people
                                                "
                                            ></i>

                                            {{
                                                $occurrence[
                                                    'class_name'
                                                ]
                                            }}
                                        </span>

                                    </div>

                                </div>


                                {{-- Horaire --}}

                                <div class="prof-session-time">

                                    <i
                                        class="
                                            bi
                                            bi-clock-fill
                                        "
                                    ></i>

                                    {{
                                        $occurrence[
                                            'time_label'
                                        ]
                                    }}

                                </div>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>


        @else

            <div class="prof-sessions-empty">

                <div
                    class="
                        prof-sessions-empty-icon
                    "
                >
                    <i
                        class="
                            bi
                            bi-calendar2-x
                        "
                    ></i>
                </div>

                <strong>
                    Aucune séance à venir
                </strong>

                <span>
                    Aucun horaire n'est disponible
                    pour le parcours sélectionné.
                </span>

            </div>

        @endif

    </div>

</section>


{{-- =========================================================
     CALENDRIER
========================================================= --}}

<section class="pp-panel pp-section-gap">

    <header class="pp-panel-head">

        <div class="pp-panel-title-wrap">

            <h2 class="pp-panel-title">
                <i class="bi bi-calendar-week"></i>
                Calendrier
            </h2>

            <p class="pp-panel-subtitle">
                Vue semaine ou mois · lecture seule
            </p>

        </div>

    </header>


    <div class="pp-calendar-shell">

        <div id="calendar"></div>

    </div>

</section>

@endsection


{{-- =========================================================
     FULL CALENDAR
========================================================= --}}

@push('scripts')

<script
    src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"
></script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const element =
            document.getElementById('calendar');


        if (
            !element
            || typeof FullCalendar === 'undefined'
        ) {
            return;
        }


        const baseUrl =
            @json(
                route(
                    'prof.schedule.data'
                )
            );


        const query = {

            subject_id:
                @json(
                    $selectedSubjectId
                ),

            level_id:
                @json(
                    $selectedLevelId
                ),

            class_id:
                @json(
                    $selectedClassId
                ),

        };


        const calendar =
            new FullCalendar.Calendar(
                element,
                {

                    initialView:
                        'timeGridWeek',

                    locale:
                        'fr',

                    firstDay:
                        1,

                    height:
                        'auto',

                    allDaySlot:
                        false,

                    slotMinTime:
                        '07:00:00',

                    slotMaxTime:
                        '22:00:00',

                    nowIndicator:
                        true,

                    expandRows:
                        true,


                    headerToolbar: {

                        left:
                            'prev,next today',

                        center:
                            'title',

                        right:
                            'timeGridWeek,dayGridMonth',

                    },


                    buttonText: {

                        today:
                            "Aujourd'hui",

                        week:
                            'Semaine',

                        month:
                            'Mois',

                    },


                    events:
                        function (
                            info,
                            success,
                            failure
                        ) {

                            const params =
                                new URLSearchParams();


                            params.set(
                                'start',
                                info.startStr
                            );


                            params.set(
                                'end',
                                info.endStr
                            );


                            Object.entries(
                                query
                            )
                            .forEach(
                                ([key, value]) => {

                                    if (value) {

                                        params.set(
                                            key,
                                            value
                                        );

                                    }

                                }
                            );


                            fetch(
                                `${baseUrl}?${params.toString()}`,
                                {

                                    headers: {

                                        'Accept':
                                            'application/json',

                                    },

                                }
                            )
                            .then(
                                response => {

                                    if (
                                        !response.ok
                                    ) {

                                        throw new Error(
                                            'Erreur planning'
                                        );

                                    }


                                    return response
                                        .json();

                                }
                            )
                            .then(success)
                            .catch(failure);

                        },


                    eventDidMount:
                        function(info) {

                            if (
                                info.event.title
                            ) {

                                info.el.setAttribute(
                                    'title',
                                    info.event.title
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