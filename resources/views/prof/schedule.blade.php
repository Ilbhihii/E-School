@extends('layouts.prof')

@section('title', 'Emploi du temps')
@section('page_title', 'Emploi du temps')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

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
            Le planning affiche uniquement les séances
            correspondant à vos créneaux assignés.
        </p>
    </div>
</section>

@include(
    'prof.partials.path-filter',
    [
        'action' => route('prof.schedule'),
        'buttonLabel' => 'Afficher le planning',
    ]
)

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-list-check"></i>
                Prochaines séances
            </h2>

            <p class="pp-panel-subtitle">
                Matière → Niveau → Classe → Créneau
            </p>
        </div>

        <span class="pp-panel-meta">
            {{ $occurrences->count() }} séance(s)
        </span>
    </header>

    <div class="pp-panel-body">
        @if($occurrences->isNotEmpty())
            <div class="prof-weekly-assignment-grid">
                @foreach(
                    $occurrences->take(8)
                    as $occurrence
                )
                    <article class="prof-weekly-assignment">
                        <div class="prof-weekly-day">
                            <i class="bi bi-calendar3"></i>
                            {{ $occurrence['date_label'] }}
                        </div>

                        <div class="prof-weekly-copy">
                            <strong>
                                {{ $occurrence['subject'] }}
                            </strong>

                            <span>
                                {{ $occurrence['level'] }}
                                →
                                {{ $occurrence['class_name'] }}
                                →
                                {{ $occurrence['slot_code'] }}
                            </span>
                        </div>

                        <div class="prof-weekly-time">
                            <i class="bi bi-clock-fill"></i>
                            {{ $occurrence['time_label'] }}
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="pps-empty">
                Aucun horaire pour le parcours sélectionné.
            </div>
        @endif
    </div>
</section>

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

@push('head')
<link
    href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css"
    rel="stylesheet"
>
<script
    src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"
></script>
@endpush

@push('scripts')
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
            @json(route('prof.schedule.data'));

        const query = {
            subject_id:
                @json($selectedSubjectId),
            level_id:
                @json($selectedLevelId),
            class_id:
                @json($selectedClassId),
            class_slot_id:
                @json($selectedSlotId),
        };

        const calendar =
            new FullCalendar.Calendar(
                element,
                {
                    initialView: 'timeGridWeek',
                    locale: 'fr',
                    firstDay: 1,
                    height: 'auto',
                    allDaySlot: false,
                    slotMinTime: '07:00:00',
                    slotMaxTime: '22:00:00',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right:
                            'timeGridWeek,dayGridMonth',
                    },
                    buttonText: {
                        today: "Aujourd'hui",
                        week: 'Semaine',
                        month: 'Mois',
                    },
                    events: function (
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

                        Object.entries(query)
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
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error();
                                }

                                return response.json();
                            })
                            .then(success)
                            .catch(failure);
                    },
                }
            );

        calendar.render();
    }
);
</script>
@endpush
@endsection
