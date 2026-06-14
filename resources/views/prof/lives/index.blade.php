@extends('layouts.prof')

@section('title', 'Mes Lives')
@section('page_title', 'Lives')
@section('breadcrumb', 'Gestion des lives')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-camera-video me-2" style="color:var(--adm-danger);"></i> Mes Lives</h1>
        <div class="subtitle">Créez et gérez vos sessions en direct</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.lives.create') }}" class="adm-btn adm-btn-danger">
            <i class="bi bi-plus-lg"></i> Nouveau Live
        </a>
    </div>
</div>

<div class="adm-stats-grid">
    <div class="adm-stat red">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-broadcast"></i></div>
        </div>
        <div class="stat-value">{{ $totalLives }}</div>
        <div class="stat-label">Total Lives</div>
    </div>
    <div class="adm-stat orange">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="stat-value">{{ $recentLives->count() }}</div>
        <div class="stat-label">Lives récents</div>
    </div>
    <div class="adm-stat red" style="display:flex;align-items:center;justify-content:center;">
        <a href="{{ route('prof.lives.create') }}" class="adm-btn adm-btn-danger" style="padding:12px 28px;font-size:0.95rem;">
            <i class="bi bi-plus-circle me-2"></i> Nouveau Live
        </a>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-camera-video" style="color:rgba(255,255,255,0.35);"></i> Tous les Lives</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $lives->count() }} lives</span>
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
                        <td><span class="adm-badge adm-badge-danger">{{ $live->classRoom?->name ?? 'Non assignée' }}</span></td>
                        <td>
                            <a href="{{ $live->stream_url }}" target="_blank" class="adm-btn adm-btn-primary adm-btn-sm">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Ouvrir
                            </a>
                        </td>
                        <td style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $live->created_at->format('d/m/Y') }}</td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('prof.lives.edit', $live) }}" class="adm-btn adm-btn-warning adm-btn-sm" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('prof.lives.destroy', $live) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce live ?')">
                                    @csrf @method('DELETE')
                                    <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit" title="Supprimer">
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
                                <p>Créez votre première session en direct.</p>
                                <a href="{{ route('prof.lives.create') }}" class="adm-btn adm-btn-danger adm-btn-sm">
                                    <i class="bi bi-plus-lg"></i> Créer un live
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($lives, 'links'))
    <div class="adm-card-footer">
        {{ $lives->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- CALENDAR -->
<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-calendar-week" style="color:rgba(255,255,255,0.35);"></i> Calendrier des lives</h4>
    </div>
    <div class="adm-card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- FLOAT BUTTON -->
<a href="{{ route('prof.lives.create') }}" class="adm-btn adm-btn-danger position-fixed bottom-0 end-0 m-4 rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width:60px;height:60px;font-size:24px;z-index:999;">
    <i class="bi bi-plus-lg"></i>
</a>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/fr.global.min.js'></script>

<style>
/* FullCalendar Dark Theme for Prof */
#calendar {
    min-height: 500px;
}
.fc {
    color: rgba(255,255,255,0.85);
    font-family: 'Inter', sans-serif;
    background: transparent;
}
.fc-toolbar-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700 !important;
    font-size: 1.25rem !important;
    color: rgba(255,255,255,0.9) !important;
}
.fc .fc-button-primary {
    background: rgba(255,255,255,0.06) !important;
    border: 1px solid rgba(255,255,255,0.08) !important;
    color: rgba(255,255,255,0.8) !important;
    border-radius: 8px !important;
    font-weight: 500 !important;
    transition: all 0.2s ease;
}
.fc .fc-button-primary:hover {
    background: rgba(255,255,255,0.1) !important;
    border-color: rgba(255,255,255,0.15) !important;
    box-shadow: 0 4px 15px rgba(220,53,69,0.2) !important;
}
.fc .fc-button-primary:not(:disabled).fc-button-active {
    background: linear-gradient(135deg, #DC2626, #EF4444) !important;
    border-color: transparent !important;
    color: white !important;
}
.fc .fc-button-primary:disabled {
    opacity: 0.4 !important;
}
.fc-daygrid-day {
    background: rgba(255,255,255,0.02);
    transition: background 0.2s;
}
.fc-daygrid-day:hover {
    background: rgba(255,255,255,0.05);
}
.fc .fc-day-other {
    background: rgba(255,255,255,0.01);
}
.fc-col-header-cell {
    background: rgba(255,255,255,0.04);
}
.fc-theme-standard td,
.fc-theme-standard th {
    border-color: rgba(255,255,255,0.06);
}
.fc .fc-daygrid-day-number {
    color: rgba(255,255,255,0.7);
    padding: 6px 8px;
    font-size: 0.85rem;
}
.fc .fc-col-header-cell-cushion {
    color: rgba(255,255,255,0.6);
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 10px 4px;
}
.fc .fc-daygrid-event {
    border-radius: 6px;
    padding: 2px 8px;
    font-size: 0.75rem;
    font-weight: 500;
    border: none !important;
}
.fc .fc-daygrid-more-link {
    color: #EF4444 !important;
    font-weight: 600;
}
.fc .fc-scrollgrid {
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 10px;
    overflow: hidden;
}
.fc .fc-today-button {
    font-weight: 600 !important;
}
.fc .fc-non-business {
    background: transparent !important;
}
@media (max-width: 768px) {
    .fc-toolbar {
        flex-direction: column;
        gap: 0.75rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        themeSystem: 'standard',
        locale: 'fr',
        firstDay: 1,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine'
        },
        events: [
            @foreach($lives as $live)
            @if($live->live_date && $live->start_time && $live->end_time)
            {
                title: "🎥 {{ $live->title }} ({{ $live->classRoom?->name ?? '' }})",
                start: "{{ $live->live_date }}T{{ $live->start_time }}",
                end: "{{ $live->live_date }}T{{ $live->end_time }}",
                color: "#dc3545",
                textColor: "#fff",
                borderColor: "transparent"
            },
            @endif
            @endforeach
        ],
        eventClick: function(info) {
            if (info.event.url) {
                window.open(info.event.url, '_blank');
                info.jsEvent.preventDefault();
            }
        }
    });
    calendar.render();
});
</script>

@endsection
