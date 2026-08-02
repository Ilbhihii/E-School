@extends('layouts.prof')

@section('title', 'Mes lives')
@section('page_title', 'Lives')
@section('breadcrumb', 'Sessions en direct')

@section('content')
@php
    $calendarEvents = $lives->getCollection()->filter(fn ($live) => !empty($live->live_date))->map(function ($live) {
        $date = \Carbon\Carbon::parse($live->live_date)->format('Y-m-d');
        $start = $live->start_time ?: '00:00';
        $end = $live->end_time ?: date('H:i', strtotime($start.' +1 hour'));

        return [
            'id' => $live->id,
            'title' => \Illuminate\Support\Str::limit($live->title, 34),
            'start' => $date.'T'.$start,
            'end' => $date.'T'.$end,
            'url' => $live->stream_url ?: null,
            'extendedProps' => [
                'class' => $live->classRoom?->name ?? 'Classe non définie',
            ],
        ];
    })->values();
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-broadcast-pin"></i> Sessions en direct</span>
        <h1 class="pp-page-title">Mes lives</h1>
        <p class="pp-page-description">
            Consultez les sessions programmées, leurs classes et les liens de connexion aux plateformes de visioconférence.
        </p>
    </div>

    <div class="pp-page-actions">
        <span class="pp-soft-chip"><i class="bi bi-camera-video-fill"></i> {{ $totalLives }} live(s)</span>
    </div>
</section>

<div class="pp-summary-grid">
    <article class="pp-summary-card is-red">
        <span class="pp-summary-icon"><i class="bi bi-broadcast"></i></span>
        <span class="pp-summary-copy">
            <strong class="pp-summary-value">{{ $totalLives }}</strong>
            <span class="pp-summary-label">Total des lives</span>
        </span>
    </article>
    <article class="pp-summary-card is-green">
        <span class="pp-summary-icon"><i class="bi bi-calendar-check-fill"></i></span>
        <span class="pp-summary-copy">
            <strong class="pp-summary-value">{{ $upcomingLives ?? 0 }}</strong>
            <span class="pp-summary-label">À venir</span>
        </span>
    </article>
    <article class="pp-summary-card is-purple">
        <span class="pp-summary-icon"><i class="bi bi-clock-history"></i></span>
        <span class="pp-summary-copy">
            <strong class="pp-summary-value">{{ $recentLives->count() }}</strong>
            <span class="pp-summary-label">Lives récents</span>
        </span>
    </article>
</div>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-camera-video-fill"></i> Sessions disponibles</h2>
            <p class="pp-panel-subtitle">Ouvrez chaque session sur sa plateforme de réunion.</p>
        </div>
        <span class="pp-panel-meta">{{ method_exists($lives, 'total') ? $lives->total() : $lives->count() }} résultat(s)</span>
    </header>

    <div class="pp-panel-body is-flush">
        <div class="pp-live-list">
            @forelse($lives as $live)
                @php
                    $liveDate = $live->live_date ? \Carbon\Carbon::parse($live->live_date) : null;
                    $meetingHost = strtolower((string) parse_url((string) $live->stream_url, PHP_URL_HOST));
                    $isTeams = ($live->provider ?? null) === 'teams' || in_array($meetingHost, ['teams.microsoft.com', 'teams.live.com'], true);
                    $providerName = $isTeams ? 'Microsoft Teams' : 'Google Meet';
                    $providerIcon = $isTeams ? 'bi-microsoft-teams' : 'bi-camera-video-fill';
                    $providerColor = $isTeams ? '#818cf8' : '#4ade80';
                @endphp

                <article class="pp-live-row">
                    <div class="pp-live-main">
                        <span class="pp-live-icon"><i class="bi bi-camera-video-fill"></i></span>
                        <div class="pp-live-copy">
                            <strong class="pp-live-title">{{ $live->title }}</strong>
                            <div class="pp-live-meta">
                                <span><i class="bi bi-people"></i> {{ $live->classRoom?->name ?? 'Classe non assignée' }}</span>
                                @if($liveDate)
                                    <span><i class="bi bi-calendar3"></i> {{ $liveDate->format('d/m/Y') }}</span>
                                @endif
                                @if($live->start_time)
                                    <span><i class="bi bi-clock"></i> {{ $live->start_time }}@if($live->end_time) – {{ $live->end_time }}@endif</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($live->stream_url)
                        <a
                            href="{{ $live->stream_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="pp-provider"
                            style="--provider-color:{{ $providerColor }};"
                        >
                            <i class="bi {{ $providerIcon }}"></i>
                            {{ $providerName }}
                        </a>
                    @else
                        <span class="adm-badge adm-badge-gray"><i class="bi bi-hourglass-split"></i> Lien à venir</span>
                    @endif
                </article>
            @empty
                <div class="pp-empty">
                    <div>
                        <span class="pp-empty-icon"><i class="bi bi-camera-video-off"></i></span>
                        <h3>Aucun live programmé</h3>
                        <p>Les sessions créées par l’administration apparaîtront ici avec leur lien de connexion.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if(method_exists($lives, 'links') && $lives->hasPages())
            <div class="pp-pagination">{{ $lives->appends(request()->query())->links() }}</div>
        @endif
    </div>
</section>

<section class="pp-panel pp-section-gap">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-calendar3"></i> Calendrier des lives</h2>
            <p class="pp-panel-subtitle">Cliquez sur une session pour ouvrir son lien dans un nouvel onglet.</p>
        </div>
        <span class="pp-panel-meta"><i class="bi bi-info-circle me-1"></i> Vue mensuelle</span>
    </header>
    <div class="pp-calendar-shell">
        <div id="livesCalendar"></div>
    </div>
</section>

@push('head')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarElement = document.getElementById('livesCalendar');
    if (!calendarElement || typeof FullCalendar === 'undefined') return;

    const calendar = new FullCalendar.Calendar(calendarElement, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        firstDay: 1,
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listWeek'
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            list: 'Liste'
        },
        events: @json($calendarEvents),
        eventClick: function (info) {
            if (!info.event.url) return;
            info.jsEvent.preventDefault();
            window.open(info.event.url, '_blank', 'noopener');
        }
    });

    calendar.render();
});
</script>
@endpush
@endsection
