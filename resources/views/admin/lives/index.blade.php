@extends('layouts.admin')

@section('title', 'Gestion des Lives')
@section('page_title', 'Lives')
@section('breadcrumb', 'Gestion des lives')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>Lives</h1>
        <div class="subtitle">Gérez les sessions live de votre plateforme</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.lives.create') }}" class="adm-btn adm-btn-danger">
            <i class="bi bi-plus-lg"></i> Nouveau live
        </a>
    </div>
</div>

<div class="adm-stats-grid">
    <div class="adm-stat red">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-camera-video-fill"></i></div>
        </div>
        <div class="stat-value">{{ $totalLives ?? $lives->count() }}</div>
        <div class="stat-label">Total lives</div>
    </div>
    <div class="adm-stat orange">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="stat-value">{{ isset($recentLives) ? $recentLives->count() : 0 }}</div>
        <div class="stat-label">Lives récents</div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-collection" style="color:rgba(255,255,255,0.35);"></i> Tous les lives</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $lives->count() }} live(s)</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Classe</th>
                        <th>Lien</th>
                        <th>Date</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lives as $live)
                    <tr>
                        <td><span style="font-weight:500;">{{ $live->title }}</span></td>
                        <td><span class="adm-badge adm-badge-danger">{{ $live->classRoom->name ?? '-' }}</span></td>
                        <td>
                            <a href="{{ $live->stream_url }}" target="_blank" class="adm-btn adm-btn-ghost adm-btn-sm">
                                <i class="bi bi-box-arrow-up-right"></i> Ouvrir
                            </a>
                        </td>
                        <td style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $live->created_at->format('d/m/Y') }}</td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('admin.lives.edit', $live) }}" class="adm-btn adm-btn-warning adm-btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.lives.destroy',$live) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce live ?')">
                                    @csrf @method('DELETE')
                                    <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-camera-video"></i></div>
                                <h5>Aucun live</h5>
                                <p>Créez votre premier live pour commencer.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- FullCalendar -->
<div class="adm-card mt-4">
    <div class="adm-card-header">
        <h4><i class="bi bi-calendar-event" style="color:#FCA5A5;"></i> Calendrier des lives</h4>
    </div>
    <div class="adm-card-body">
        <div id="calendar"></div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/fr.global.min.js"></script>

<style>
/* FullCalendar dark theme overrides */
#calendar {
    max-width: 100%;
}
.fc {
    background: transparent;
    color: rgba(255,255,255,0.85);
}
.fc .fc-toolbar-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.15rem !important;
    color: rgba(255,255,255,0.9);
}
.fc .fc-button {
    background: rgba(255,255,255,0.08) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    color: rgba(255,255,255,0.7) !important;
    border-radius: 9px !important;
    padding: 6px 14px !important;
    font-weight: 500 !important;
    font-size: 0.8rem !important;
    transition: all 0.2s ease !important;
    text-transform: capitalize !important;
    box-shadow: none !important;
}
.fc .fc-button:hover {
    background: rgba(255,255,255,0.12) !important;
    color: white !important;
}
.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active {
    background: rgba(239,68,68,0.2) !important;
    border-color: rgba(239,68,68,0.3) !important;
    color: #FCA5A5 !important;
}
.fc .fc-daygrid-day {
    border-color: rgba(255,255,255,0.05) !important;
}
.fc .fc-daygrid-day-number {
    color: rgba(255,255,255,0.6);
    font-size: 0.85rem;
    padding: 6px 8px !important;
}
.fc .fc-col-header-cell {
    border-color: rgba(255,255,255,0.06) !important;
}
.fc .fc-col-header-cell-cushion {
    color: rgba(255,255,255,0.5);
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 10px 4px;
}
.fc .fc-day-today {
    background: rgba(239,68,68,0.06) !important;
}
.fc .fc-day-today .fc-daygrid-day-number {
    color: #FCA5A5;
    font-weight: 700;
}
.fc .fc-day-other .fc-daygrid-day-top {
    opacity: 0.3;
}
.fc .fc-daygrid-event {
    background: rgba(239,68,68,0.15) !important;
    border: 1px solid rgba(239,68,68,0.2) !important;
    border-left: 3px solid #EF4444 !important;
    border-radius: 6px !important;
    padding: 3px 6px !important;
    font-size: 0.78rem !important;
    color: #FCA5A5 !important;
    transition: all 0.2s ease;
}
.fc .fc-daygrid-event:hover {
    background: rgba(239,68,68,0.25) !important;
    transform: translateY(-1px);
}
.fc .fc-daygrid-event .fc-event-title {
    font-weight: 600;
    padding: 0 2px;
}
.fc .fc-scrollgrid {
    border-color: rgba(255,255,255,0.06) !important;
}
.fc .fc-scrollgrid-section > td {
    border-color: rgba(255,255,255,0.06) !important;
}
.fc .fc-timegrid-axis {
    border-color: rgba(255,255,255,0.06) !important;
}
.fc .fc-timegrid-slot {
    border-color: rgba(255,255,255,0.04) !important;
}
.fc .fc-timegrid-axis-cushion {
    color: rgba(255,255,255,0.4);
    font-size: 0.75rem;
}
.fc .fc-timegrid-slot-label-cushion {
    color: rgba(255,255,255,0.4);
    font-size: 0.75rem;
}
.fc .fc-popover {
    background: rgba(15,23,42,0.98) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    border-radius: 12px !important;
    box-shadow: 0 12px 40px rgba(0,0,0,0.4) !important;
}
.fc .fc-popover-header {
    background: rgba(255,255,255,0.04) !important;
    padding: 10px 14px !important;
    border-bottom: 1px solid rgba(255,255,255,0.06) !important;
    border-radius: 12px 12px 0 0 !important;
}
.fc .fc-popover-title {
    color: rgba(255,255,255,0.8) !important;
    font-weight: 600;
    font-size: 0.9rem;
}
.fc .fc-popover-close {
    color: rgba(255,255,255,0.4) !important;
}
.fc .fc-more-popover .fc-daygrid-day-events {
    padding: 8px;
}
.fc .fc-non-business {
    background: transparent !important;
}
.fc .fc-highlight {
    background: rgba(124,58,237,0.1) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine'
        },
        locale: 'fr',
        firstDay: 1,
        events: [
            @foreach($lives as $live)
            {
                title: "{{ $live->title }}",
                start: "{{ $live->live_date }}T{{ $live->start_time }}",
                end: "{{ $live->live_date }}T{{ $live->end_time }}",
                url: "{{ $live->stream_url }}",
                className: 'fc-live-event'
            },
            @endforeach
        ],
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.open(info.event.url, '_blank');
            }
        },
        loading: function(isLoading) {
            if (!isLoading) {
                // Calendar loaded
            }
        }
    });

    calendar.render();
});
</script>
@endsection
