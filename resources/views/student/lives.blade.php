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

<style>
#livesCalendar {
    min-height: 560px;
}

.fc {
    color: rgba(255, 255, 255, 0.88);
    font-family: 'Inter', sans-serif;
}

.fc .fc-toolbar {
    gap: 0.8rem;
    margin-bottom: 1.2rem !important;
}

.fc-toolbar-title {
    color: #f8fafc !important;
    font-family: 'Poppins', sans-serif;
    font-size: 1.15rem !important;
    font-weight: 800 !important;
    letter-spacing: -0.02em;
}

.fc .fc-button-primary {
    min-height: 36px;
    padding: 0.45rem 0.78rem !important;
    color: #cbd5e1 !important;
    background: rgba(255, 255, 255, 0.045) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 9px !important;
    box-shadow: none !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    transition:
        background 0.2s ease,
        border-color 0.2s ease,
        transform 0.2s ease;
}

.fc .fc-button-primary:hover {
    transform: translateY(-1px);
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.085) !important;
    border-color: rgba(255, 255, 255, 0.14) !important;
}

.fc .fc-button-primary:not(:disabled).fc-button-active {
    color: #ffffff !important;
    background:
        linear-gradient(
            135deg,
            #dc2626,
            #ef4444
        ) !important;
    border-color: transparent !important;
}

.fc .fc-daygrid-day {
    min-height: 92px;
    background: rgba(255, 255, 255, 0.018);
    transition: background 0.2s ease;
}

.fc .fc-daygrid-day:hover {
    background: rgba(255, 255, 255, 0.045);
}

.fc .fc-day-other {
    background: rgba(255, 255, 255, 0.008);
}

.fc .fc-day-today {
    background: rgba(220, 38, 38, 0.065) !important;
    box-shadow:
        inset 0 0 0 1px rgba(248, 113, 113, 0.14);
}

.fc .fc-col-header-cell {
    background: rgba(255, 255, 255, 0.035);
}

.fc-theme-standard td,
.fc-theme-standard th {
    border-color: rgba(255, 255, 255, 0.055);
}

.fc .fc-daygrid-day-number {
    padding: 7px 9px;
    color: #cbd5e1;
    font-size: 0.78rem;
    font-weight: 700;
}

.fc .fc-col-header-cell-cushion {
    padding: 11px 4px;
    color: #64748b;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.075em;
    text-transform: uppercase;
}

.fc .fc-scrollgrid {
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.065);
    border-radius: 14px;
}

.fc-daygrid-event {
    margin: 2px 4px !important;
    padding: 4px 7px !important;
    border: none !important;
    border-radius: 7px !important;
    box-shadow:
        0 6px 16px rgba(220, 38, 38, 0.16);
    font-size: 0.68rem !important;
    font-weight: 700 !important;
}

.fc-h-event .fc-event-title {
    font-weight: 700 !important;
}

.fc .fc-list {
    overflow: hidden;
    border-color: rgba(255, 255, 255, 0.07);
    border-radius: 12px;
}

.fc .fc-list-day-cushion {
    background: rgba(255, 255, 255, 0.04);
}

.fc .fc-list-event:hover td {
    background: rgba(255, 255, 255, 0.035);
}

.fc .fc-popover {
    overflow: hidden;
    background: #111c2f !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 12px !important;
    box-shadow: 0 18px 45px rgba(0, 0, 0, 0.32);
}

.fc .fc-popover-header {
    padding: 9px 12px !important;
    color: #f8fafc !important;
    background: rgba(255, 255, 255, 0.045) !important;
}

.fc .fc-popover-body {
    padding: 5px !important;
}

@media (max-width: 768px) {
    #livesCalendar {
        min-height: 420px;
    }

    .fc .fc-toolbar {
        align-items: stretch;
        flex-direction: column;
    }

    .fc .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }

    .fc-toolbar-title {
        text-align: center;
    }
}
</style>
@endpush

@section('content')

<style>
.live-filter-panel {
    margin-bottom: 1.35rem;
    padding: 1.15rem 1.25rem;
    border: 1px solid rgba(220, 38, 38, 0.13);
    border-radius: 14px;
    background:
        linear-gradient(
            135deg,
            rgba(15, 23, 42, 0.94),
            rgba(30, 41, 59, 0.72)
        );
}

.live-filter-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}

.live-filter-heading {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #f1f5f9;
    font-size: 0.9rem;
    font-weight: 800;
}

.live-filter-heading span {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    color: #fca5a5;
    background: rgba(220, 38, 38, 0.12);
    border: 1px solid rgba(248, 113, 113, 0.16);
    border-radius: 10px;
}

.live-filter-help {
    margin-top: 4px;
    color: #64748b;
    font-size: 0.72rem;
    line-height: 1.5;
}

.live-filter-grid {
    display: grid;
    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr)
        auto;
    gap: 0.85rem;
    align-items: end;
}

.live-filter-field label {
    display: block;
    margin-bottom: 0.45rem;
    color: #94a3b8;
    font-size: 0.66rem;
    font-weight: 750;
    letter-spacing: 0.045em;
    text-transform: uppercase;
}

.live-filter-select {
    width: 100%;
    min-height: 44px;
    padding: 0 2.35rem 0 0.85rem;
    color: #e2e8f0;
    background-color: rgba(15, 23, 42, 0.86);
    border: 1px solid rgba(148, 163, 184, 0.15);
    border-radius: 10px;
    outline: none;
    font-size: 0.8rem;
    font-weight: 650;
}

.live-filter-select:focus {
    border-color: rgba(248, 113, 113, 0.68);
    box-shadow:
        0 0 0 4px rgba(220, 38, 38, 0.09);
}

.live-filter-select:disabled {
    cursor: not-allowed;
    color: #475569;
    opacity: 0.7;
}

.live-filter-reset {
    min-height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 0 1rem;
    color: #cbd5e1;
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    font-size: 0.73rem;
    font-weight: 750;
    text-decoration: none;
    white-space: nowrap;
}

.live-filter-reset:hover {
    color: #ffffff;
    border-color: rgba(248, 113, 113, 0.32);
    background: rgba(220, 38, 38, 0.08);
}

.live-filter-summary {
    margin-bottom: 1.3rem;
    padding: 0.9rem 1rem;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.8rem;
    color: #64748b;
    background: rgba(30, 41, 59, 0.48);
    border: 1px solid rgba(255, 255, 255, 0.045);
    border-radius: 11px;
    font-size: 0.72rem;
}

.live-filter-summary strong {
    color: #e2e8f0;
}

.live-filter-summary-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px;
    color: #cbd5e1;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.055);
    border-radius: 999px;
}

.live-path-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 0.7rem;
}

.live-path-badge {
    max-width: 100%;
    padding: 4px 7px;
    overflow: hidden;
    color: #94a3b8;
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.055);
    border-radius: 999px;
    font-size: 0.58rem;
    font-weight: 650;
    text-overflow: ellipsis;
    white-space: nowrap;
}

@media (max-width: 900px) {
    .live-filter-grid {
        grid-template-columns: 1fr 1fr;
    }

    .live-filter-reset {
        grid-column: 1 / -1;
    }
}

@media (max-width: 650px) {
    .live-filter-grid {
        grid-template-columns: 1fr;
    }

    .live-filter-reset {
        grid-column: auto;
    }
}

.live-calendar-shell {
    position: relative;
    overflow: hidden;
    margin-bottom: 1.6rem;
    border: 1px solid rgba(248, 113, 113, 0.13);
    border-radius: 18px;
    background:
        radial-gradient(
            circle at 92% 0%,
            rgba(220, 38, 38, 0.11),
            transparent 31%
        ),
        linear-gradient(
            145deg,
            rgba(15, 23, 42, 0.98),
            rgba(17, 28, 47, 0.94)
        );
    box-shadow:
        0 22px 55px rgba(0, 0, 0, 0.23);
}

.live-calendar-shell::before {
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    content: '';
    background:
        linear-gradient(
            180deg,
            #ef4444,
            #7c3aed
        );
}

.live-calendar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    padding: 1.15rem 1.35rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.055);
}

.live-calendar-title-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.live-calendar-icon {
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    display: grid;
    place-items: center;
    color: #ffffff;
    background:
        linear-gradient(
            135deg,
            #dc2626,
            #ef4444
        );
    border-radius: 12px;
    box-shadow:
        0 10px 24px rgba(220, 38, 38, 0.22);
}

.live-calendar-title {
    margin: 0;
    color: #f8fafc;
    font-size: 0.95rem;
    font-weight: 850;
}

.live-calendar-subtitle {
    margin-top: 3px;
    color: #64748b;
    font-size: 0.69rem;
    line-height: 1.5;
}

.live-calendar-kpis {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 7px;
}

.live-calendar-kpi {
    min-width: 92px;
    padding: 8px 10px;
    color: #94a3b8;
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 10px;
    font-size: 0.6rem;
    text-align: center;
}

.live-calendar-kpi strong {
    display: block;
    margin-bottom: 2px;
    color: #f8fafc;
    font-size: 0.95rem;
    font-weight: 850;
}

.live-calendar-kpi.live strong {
    color: #f87171;
}

.live-calendar-kpi.upcoming strong {
    color: #60a5fa;
}

.live-calendar-kpi.ended strong {
    color: #34d399;
}

.live-calendar-body {
    padding: 1.2rem 1.35rem 1.35rem;
}

.live-section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin: 1.7rem 0 0.85rem;
}

.live-section-heading-main {
    display: flex;
    align-items: center;
    gap: 10px;
}

.live-section-heading-icon {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    color: #fca5a5;
    background: rgba(220, 38, 38, 0.1);
    border: 1px solid rgba(248, 113, 113, 0.13);
    border-radius: 10px;
}

.live-section-heading h3 {
    margin: 0;
    color: #f1f5f9;
    font-size: 0.9rem;
    font-weight: 820;
}

.live-section-heading p {
    margin: 3px 0 0;
    color: #64748b;
    font-size: 0.68rem;
}

.live-section-count {
    padding: 5px 9px;
    color: #94a3b8;
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.055);
    border-radius: 999px;
    font-size: 0.64rem;
    font-weight: 700;
}

@media (max-width: 820px) {
    .live-calendar-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .live-calendar-kpis {
        width: 100%;
        justify-content: flex-start;
    }

    .live-calendar-kpi {
        flex: 1;
    }
}

@media (max-width: 560px) {
    .live-calendar-header,
    .live-calendar-body {
        padding-right: 0.9rem;
        padding-left: 0.9rem;
    }

    .live-calendar-kpis {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
    }

    .live-calendar-kpi {
        min-width: 0;
    }
}

.live-card {
    background: #1E293B;
    border: 1px solid rgba(255,255,255,0.04);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
    height: 100%;
}
.live-card:hover {
    border-color: rgba(255,255,255,0.08);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.live-banner {
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
.live-banner i { font-size: 2.5rem; opacity: 0.2; }
.live-banner::after {
    content: ''; position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 40%;
    background: linear-gradient(transparent, rgba(0,0,0,0.3));
}
</style>

<div class="page-header">
    <div>
        <h1><i class="bi bi-broadcast" style="color:#DC2626;"></i> Mes Lives</h1>
        <div class="subtitle">Accédez à vos sessions en direct</div>
    </div>
</div>

@if(
    $assignedLevels->count() > 1
    || $assignedClasses->count() > 1
)
    <div class="live-filter-panel">
        <div class="live-filter-header">
            <div>
                <div class="live-filter-heading">
                    <span>
                        <i class="bi bi-funnel-fill"></i>
                    </span>

                    Filtrer mes lives
                </div>

                <div class="live-filter-help">
                    Choisissez un niveau. Les classes
                    correspondantes apparaissent automatiquement.
                </div>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('student.lives') }}"
            id="studentLiveFilterForm"
            class="live-filter-grid"
        >
            <div class="live-filter-field">
                <label for="studentLiveLevel">
                    Niveau
                </label>

                <select
                    name="level_id"
                    id="studentLiveLevel"
                    class="live-filter-select"
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

            <div class="live-filter-field">
                <label for="studentLiveClass">
                    Classe
                </label>

                <select
                    name="class_id"
                    id="studentLiveClass"
                    class="live-filter-select"
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

            <a
                href="{{ route('student.lives') }}"
                class="live-filter-reset"
            >
                <i class="bi bi-arrow-counterclockwise"></i>
                Tout afficher
            </a>
        </form>
    </div>
@endif

<div class="live-filter-summary">
    <span>
        Affichage :
    </span>

    <span class="live-filter-summary-badge">
        <i class="bi bi-mortarboard"></i>

        <strong>
            {{ $selectedLevel
                ? $selectedLevel->name
                : 'Tous les niveaux' }}
        </strong>
    </span>

    <span class="live-filter-summary-badge">
        <i class="bi bi-building"></i>

        <strong>
            {{ $selectedClass
                ? $selectedClass->name
                : 'Toutes les classes' }}
        </strong>
    </span>

    <span>
        {{ $lives->count() }}
        session{{ $lives->count() > 1 ? 's' : '' }}
        ·
        {{ $visibleClassCount }}
        classe{{ $visibleClassCount > 1 ? 's' : '' }}
    </span>
</div>

@if($lives->count() > 0)

@php
    $liveNowCount = $lives
        ->filter(
            fn ($live) =>
                $live->schedule_status === 'live'
        )
        ->count();

    $upcomingCount = $lives
        ->filter(
            fn ($live) =>
                $live->schedule_status === 'upcoming'
        )
        ->count();

    $endedCount = $lives
        ->filter(
            fn ($live) =>
                $live->schedule_status === 'ended'
        )
        ->count();
@endphp

<div class="live-calendar-shell">
    <div class="live-calendar-header">
        <div class="live-calendar-title-wrap">
            <div class="live-calendar-icon">
                <i class="bi bi-calendar3"></i>
            </div>

            <div>
                <h2 class="live-calendar-title">
                    Calendrier de mes lives
                </h2>

                <div class="live-calendar-subtitle">
                    Vue principale de votre planning.
                    Cliquez sur une session pour ouvrir
                    l’accès sécurisé.
                </div>
            </div>
        </div>

        <div class="live-calendar-kpis">
            <div class="live-calendar-kpi live">
                <strong>{{ $liveNowCount }}</strong>
                En direct
            </div>

            <div class="live-calendar-kpi upcoming">
                <strong>{{ $upcomingCount }}</strong>
                À venir
            </div>

            <div class="live-calendar-kpi ended">
                <strong>{{ $endedCount }}</strong>
                Terminés
            </div>
        </div>
    </div>

    <div class="live-calendar-body">
        <div id="livesCalendar"></div>
    </div>
</div>

<div class="live-section-heading">
    <div class="live-section-heading-main">
        <div class="live-section-heading-icon">
            <i class="bi bi-camera-video-fill"></i>
        </div>

        <div>
            <h3>Mes sessions</h3>
            <p>
                Consultez les détails et rejoignez
                les sessions accessibles.
            </p>
        </div>
    </div>

    <span class="live-section-count">
        {{ $lives->count() }}
        session{{ $lives->count() > 1 ? 's' : '' }}
    </span>
</div>

<div class="row g-3 mb-4">
    @foreach($lives as $live)
        @php
            $liveDate = \Carbon\Carbon::parse($live->live_date);
            $isLive = now()->gte($liveDate) && now()->lt($liveDate->copy()->addHours(2));
            $isUpcoming = now()->lt($liveDate);
            $bannerColor = $isLive ? '#DC2626' : ($isUpcoming ? '#0284C7' : '#475569');
            $status = $isLive ? '🔴 En direct' : ($isUpcoming ? '⏳ À venir' : '✅ Terminé');
            $badgeClass = $isLive ? 'pr-badge-danger' : ($isUpcoming ? 'pr-badge-info' : 'pr-badge-warning');
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="live-card">
                <div class="live-banner" style="background:linear-gradient(135deg, {{ $bannerColor }}, {{ $bannerColor }}77);">
                    <i class="bi bi-camera-video-fill"></i>
                </div>
                <div style="padding:1rem 1.25rem;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 style="font-weight:600;color:#F1F5F9;margin:0;font-size:0.9rem;">{{ Str::limit($live->title, 35) }}</h5>
                        <span class="pr-badge {{ $badgeClass }}" style="font-size:0.65rem;">{{ $status }}</span>
                    </div>
                    <div class="live-path-badges">
                        @if($live->classRoom?->level)
                            <span class="live-path-badge">
                                <i class="bi bi-mortarboard me-1"></i>
                                {{
                                    $live
                                        ->classRoom
                                        ->level
                                        ->name
                                }}
                            </span>
                        @endif

                        @if($live->classRoom)
                            <span class="live-path-badge">
                                <i class="bi bi-building me-1"></i>
                                {{ $live->classRoom->name }}
                            </span>
                        @endif
                    </div>

                    @if($live->description)
                    <p style="font-size:0.78rem;color:#64748B;margin-bottom:0.75rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $live->description }}</p>
                    @endif
                    <div class="d-flex align-items-center justify-content-between pt-2" style="border-top:1px solid rgba(255,255,255,0.04);">
                        <small style="color:#475569;font-size:0.7rem;"><i class="bi bi-calendar3 me-1"></i>{{ $isLive ? 'En cours...' : $liveDate->format('d/m/Y H:i') }}</small>
                        @if($isLive || $isUpcoming)
                        <a href="{{ route('live.access.request', $live) }}" target="_blank" rel="noopener noreferrer" class="pr-btn {{ $isLive ? 'pr-btn-danger' : 'pr-btn-ghost' }} pr-btn-sm" style="font-size:0.75rem;">
                            {{ $isLive ? 'Rejoindre' : 'Programmé' }} <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        @else
                        <span class="pr-badge pr-badge-warning" style="font-size:0.65rem;">Terminé</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="pr-card">
    <div class="pr-card-header">
        <h4><i class="bi bi-camera-video" style="color:#64748B;"></i> Plateforme des lives</h4>
    </div>
    <div class="pr-card-body p-0">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid rgba(255,255,255,0.04);display:flex;align-items:center;gap:10px;color:rgba(255,255,255,0.35);font-size:0.78rem;">
            <i class="bi bi-info-circle"></i>
            <span>Cliquez sur la plateforme indiquée pour ouvrir la réunion.</span>
        </div>
        @foreach($lives as $live)
        @php
            $liveDate = $live->live_date ? \Carbon\Carbon::parse($live->live_date) : null;
            $meetingHost = strtolower((string) parse_url($live->stream_url, PHP_URL_HOST));
            $isTeams = $live->provider === 'teams' || in_array($meetingHost, ['teams.microsoft.com', 'teams.live.com']);
            $providerName = $isTeams ? 'Microsoft Teams' : 'Google Meet';
            $providerIcon = $isTeams ? 'bi-microsoft-teams' : 'bi-camera-video-fill';
            $providerColor = $isTeams ? '#6264A7' : '#0F9D58';
        @endphp
        <div style="padding:0.85rem 1.25rem;border-bottom:1px solid rgba(255,255,255,0.03);display:flex;align-items:center;justify-content:space-between;gap:12px;transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
            <div style="display:flex;align-items:center;gap:12px;min-width:0;flex:1;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg, #DC2626, #EF4444);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-camera-video-fill" style="font-size:0.85rem;color:white;"></i>
                </div>
                <div style="min-width:0;">
                    <div style="color:#F1F5F9;font-weight:600;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $live->title }}</div>
                    @if($liveDate)
                    <div style="color:#475569;font-size:0.7rem;margin-top:2px;">
                        <i class="bi bi-calendar3 me-1"></i>{{ $liveDate->format('d/m/Y') }}
                        @if($live->start_time) <i class="bi bi-clock ms-2 me-1"></i>{{ $live->start_time }} @endif
                    </div>
                    @endif

                    @if($live->classRoom)
                        <div
                            style="
                                color:#64748B;
                                font-size:0.65rem;
                                margin-top:3px;
                            "
                        >
                            <i class="bi bi-mortarboard me-1"></i>
                            {{
                                $live->classRoom?->level?->name
                                ?? 'Niveau non défini'
                            }}

                            <span style="margin:0 5px;">→</span>

                            <i class="bi bi-building me-1"></i>
                            {{ $live->classRoom->name }}
                        </div>
                    @endif
                </div>
            </div>
            @if($live->stream_url)
            <a href="{{ route('live.access.request', $live) }}" target="_blank" rel="noopener noreferrer"
               style="flex-shrink:0;padding:7px 16px;border-radius:8px;background:{{ $providerColor }}22;border:1px solid {{ $providerColor }}55;color:{{ $providerColor }};font-size:0.75rem;text-decoration:none;transition:all 0.2s;white-space:nowrap;">
                <i class="bi {{ $providerIcon }} me-1"></i> {{ $providerName }}
            </a>
            @endif
        </div>
        @endforeach
    </div>
</div>

@else
<div class="pr-empty">
    <div class="pr-empty-icon"><i class="bi bi-camera-video-off"></i></div>
    <h5>Aucun live disponible</h5>
    <p>Les sessions apparaîtront ici dès qu'elles seront programmées.</p>
</div>
@endif

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

    const classesByLevel =
        @json($classOptionsByLevel);

    if (
        filterForm
        && levelSelect
        && classSelect
    ) {
        const addClassOption = (
            value,
            label,
            selected = false
        ) => {
            const option =
                document.createElement('option');

            option.value = value;
            option.textContent = label;
            option.selected = selected;

            classSelect.appendChild(option);
        };

        levelSelect.addEventListener(
            'change',
            () => {
                const levelId =
                    levelSelect.value;

                classSelect.innerHTML = '';

                if (!levelId) {
                    classSelect.disabled = true;

                    addClassOption(
                        '',
                        'Choisissez d’abord un niveau',
                        true
                    );

                    filterForm.submit();
                    return;
                }

                const classOptions =
                    classesByLevel[levelId]
                    || [];

                classSelect.disabled = false;

                if (classOptions.length === 0) {
                    addClassOption(
                        '',
                        'Aucune classe assignée',
                        true
                    );

                    filterForm.submit();
                    return;
                }

                classOptions.forEach(
                    (
                        classRoom,
                        index
                    ) => {
                        addClassOption(
                            classRoom.id,
                            classRoom.name,
                            index === 0
                        );
                    }
                );

                filterForm.submit();
            }
        );

        classSelect.addEventListener(
            'change',
            () => {
                filterForm.submit();
            }
        );
    }

    let calEl = document.getElementById('livesCalendar');
    if (!calEl) return;
    let calendar = new FullCalendar.Calendar(calEl, {
        initialView: window.innerWidth < 768
            ? 'listMonth'
            : 'dayGridMonth',
        locale: 'fr',
        firstDay: 1,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            list: 'Liste'
        },
        height: 'auto',
        expandRows: true,
        dayMaxEvents: 3,
        navLinks: true,
        nowIndicator: true,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        events: [
            @foreach($lives as $live)
            @if($live->live_date)
            {
                id: '{{ $live->id }}',
                title: '{{ \Illuminate\Support\Str::limit($live->title, 30) }}',
                start: '{{ \Carbon\Carbon::parse($live->live_date)->format('Y-m-d') }}' + 'T' + '{{ $live->start_time ?? '00:00' }}',
                end: '{{ \Carbon\Carbon::parse($live->live_date)->format('Y-m-d') }}' + 'T' + '{{ $live->end_time ?? date('H:i', strtotime(($live->start_time ?? '00:00') . ' +1 hour')) }}',
                url: '{{ $live->stream_url
                    ? route('live.access.request', $live)
                    : '#' }}',
                backgroundColor: '#DC2626',
                borderColor: '#EF4444',
                textColor: '#FFF',
                extendedProps: {
                    class: '{{ $live->classRoom?->name ?? "-" }}',
                    stream: '{{ $live->stream_url
                        ? route('live.access.request', $live)
                        : "" }}'
                }
            },
            @endif
            @endforeach
        ],
        eventClick: function(info) {
            if (info.event.url && info.event.url !== '#') {
                window.open(info.event.url, '_blank');
            }
        }
    });
    calendar.render();
});
</script>
@endpush

@endsection
