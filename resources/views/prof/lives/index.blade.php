@extends('layouts.prof')

@section('title', 'Mes lives')
@section('page_title', 'Lives')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

@section('content')
@php
    $calendarEvents = collect($lives->items())
        ->filter(
            fn ($live) =>
                $live->start_date_time
        )
        ->map(
            fn ($live) => [
                'id' => $live->id,
                'title' => collect([
                    $live->classSlot?->code,
                    $live->title,
                ])->filter()->implode(' · '),
                'start' =>
                    $live->start_date_time
                        ?->toIso8601String(),
                'end' =>
                    $live->end_date_time
                        ?->toIso8601String(),
                'url' =>
                    $live->stream_url,
            ]
        )
        ->values();
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-broadcast-pin"></i>
            Sessions en direct
        </span>

        <h1 class="pp-page-title">Mes lives</h1>

        <p class="pp-page-description">
            Les lives sont limités aux créneaux
            Matière → Niveau → Classe → Créneau
            qui vous sont assignés.
        </p>
    </div>
</section>

@include(
    'prof.partials.path-filter',
    [
        'action' => route('prof.lives.index'),
        'buttonLabel' => 'Afficher les lives',
    ]
)

<div class="pp-summary-grid">
    <article class="pp-summary-card is-red">
        <span class="pp-summary-icon">
            <i class="bi bi-camera-video-fill"></i>
        </span>

        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $totalLives }}
            </strong>

            <span class="pp-summary-label">
                Lives visibles
            </span>
        </span>
    </article>

    <article class="pp-summary-card is-green">
        <span class="pp-summary-icon">
            <i class="bi bi-calendar-check-fill"></i>
        </span>

        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $upcomingLives }}
            </strong>

            <span class="pp-summary-label">
                À venir
            </span>
        </span>
    </article>
</div>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-camera-video-fill"></i>
                Sessions disponibles
            </h2>
        </div>

        <span class="pp-panel-meta">
            {{ $lives->total() }} résultat(s)
        </span>
    </header>

    <div class="pp-panel-body">
        @forelse($lives as $live)
            <article class="pp-live-row">
                <div class="pp-live-main">
                    <span class="pp-live-icon">
                        <i class="bi bi-camera-video-fill"></i>
                    </span>

                    <div class="pp-live-copy">
                        <strong class="pp-live-title">
                            {{ $live->title }}
                        </strong>

                        <div class="pps-path-line mt-2">
                            <span class="pps-path-chip">
                                {{
                                    $live
                                        ->classSlot
                                        ?->subject
                                        ?->name
                                    ?? 'Matière'
                                }}
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span class="pps-path-chip">
                                {{
                                    $live
                                        ->classSlot
                                        ?->level
                                        ?->name
                                    ?? 'Niveau'
                                }}
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span class="pps-path-chip">
                                {{
                                    $live
                                        ->classSlot
                                        ?->classRoom
                                        ?->name
                                    ?? 'Classe'
                                }}
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span class="pps-slot-badge">
                                {{
                                    $live
                                        ->classSlot
                                        ?->code
                                    ?? '—'
                                }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pps-inline-actions">
                    @if($live->start_date_time)
                        <span class="pp-soft-chip">
                            <i class="bi bi-calendar3"></i>
                            {{
                                $live
                                    ->start_date_time
                                    ->format('d/m/Y H:i')
                            }}
                        </span>
                    @endif

                    @if($live->stream_url)
                        <a
                            href="{{ $live->stream_url }}"
                            target="_blank"
                            rel="noopener"
                            class="adm-btn adm-btn-danger adm-btn-sm"
                        >
                            <i class="bi bi-box-arrow-up-right"></i>
                            Ouvrir
                        </a>
                    @endif
                </div>
            </article>
        @empty
            <div class="pps-empty">
                Aucun live pour le parcours sélectionné.
            </div>
        @endforelse

        @if($lives->hasPages())
            <div class="pp-pagination mt-3">
                {{ $lives->links() }}
            </div>
        @endif
    </div>
</section>

<section class="pp-panel pp-section-gap">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-calendar3"></i>
                Calendrier des lives
            </h2>
        </div>
    </header>

    <div class="pp-calendar-shell">
        <div id="livesCalendar"></div>
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
            document.getElementById(
                'livesCalendar'
            );

        if (
            !element
            || typeof FullCalendar === 'undefined'
        ) {
            return;
        }

        const calendar =
            new FullCalendar.Calendar(
                element,
                {
                    initialView: 'dayGridMonth',
                    locale: 'fr',
                    firstDay: 1,
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right:
                            'dayGridMonth,listWeek',
                    },
                    buttonText: {
                        today: "Aujourd'hui",
                        month: 'Mois',
                        list: 'Liste',
                    },
                    events:
                        @json($calendarEvents),
                    eventClick: function (info) {
                        if (!info.event.url) {
                            return;
                        }

                        info.jsEvent
                            .preventDefault();

                        window.open(
                            info.event.url,
                            '_blank',
                            'noopener'
                        );
                    },
                }
            );

        calendar.render();
    }
);
</script>
@endpush
@endsection
