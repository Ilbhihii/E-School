@extends('layouts.student')
@section('title', 'Lives')

@push('head')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<style>
#livesCalendar { min-height: 420px; }
.fc { color: rgba(255,255,255,0.85); font-family: 'Inter', sans-serif; }
.fc-toolbar-title { font-family: 'Poppins', sans-serif; font-weight: 700 !important; font-size: 1.1rem !important; color: rgba(255,255,255,0.9) !important; }
.fc .fc-button-primary {
    background: rgba(255,255,255,0.06) !important;
    border: 1px solid rgba(255,255,255,0.08) !important;
    color: rgba(255,255,255,0.8) !important;
    border-radius: 8px !important;
    font-weight: 500 !important;
    transition: all 0.2s ease;
}
.fc .fc-button-primary:hover { background: rgba(255,255,255,0.1) !important; }
.fc .fc-button-primary:not(:disabled).fc-button-active { background: linear-gradient(135deg,#DC2626,#EF4444) !important; border-color: transparent !important; color: white !important; }
.fc-daygrid-day { background: rgba(255,255,255,0.02); transition: background 0.2s; cursor: pointer; }
.fc-daygrid-day:hover { background: rgba(255,255,255,0.05); }
.fc .fc-day-other { background: rgba(255,255,255,0.01); }
.fc-col-header-cell { background: rgba(255,255,255,0.04); }
.fc-theme-standard td, .fc-theme-standard th { border-color: rgba(255,255,255,0.06); }
.fc .fc-daygrid-day-number { color: rgba(255,255,255,0.7); padding: 6px 8px; font-size: 0.85rem; }
.fc .fc-col-header-cell-cushion { color: rgba(255,255,255,0.6); font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 10px 4px; }
.fc .fc-scrollgrid { border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; overflow: hidden; }
.fc .fc-today-button { font-weight: 600 !important; }
.fc-daygrid-event { border-radius: 6px !important; padding: 2px 6px !important; font-size: 0.75rem !important; font-weight: 500 !important; border: none !important; }
.fc-h-event .fc-event-title { font-weight: 600 !important; }
.fc .fc-day-today { background: rgba(220,38,38,0.04) !important; }
.fc .fc-popover { background: #1E293B !important; border: 1px solid rgba(255,255,255,0.06) !important; border-radius: 10px !important; }
.fc .fc-popover-header { background: rgba(255,255,255,0.04) !important; color: rgba(255,255,255,0.85) !important; padding: 8px 12px !important; border-radius: 10px 10px 0 0 !important; }
.fc .fc-popover-body { padding: 4px !important; }
@media (max-width: 768px) { .fc-toolbar { flex-direction: column; gap: 0.75rem; } #livesCalendar { min-height: 300px; } }
</style>
@endpush

@section('content')

<style>
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

@if($lives->count() > 0)

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="pr-stat red">
            <div class="pr-stat-icon"><i class="bi bi-broadcast"></i></div>
            <div class="pr-stat-value">{{ $lives->count() }}</div>
            <div class="pr-stat-label">Total sessions</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pr-stat green">
            <div class="pr-stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="pr-stat-value">{{ $lives->filter(fn($l) => now()->gt(\Carbon\Carbon::parse($l->live_date)))->count() }}</div>
            <div class="pr-stat-label">Terminés</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pr-stat blue">
            <div class="pr-stat-icon"><i class="bi bi-clock"></i></div>
            <div class="pr-stat-value">{{ $lives->filter(fn($l) => now()->lt(\Carbon\Carbon::parse($l->live_date)))->count() }}</div>
            <div class="pr-stat-label">À venir</div>
        </div>
    </div>
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
                    @if($live->description)
                    <p style="font-size:0.78rem;color:#64748B;margin-bottom:0.75rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $live->description }}</p>
                    @endif
                    <div class="d-flex align-items-center justify-content-between pt-2" style="border-top:1px solid rgba(255,255,255,0.04);">
                        <small style="color:#475569;font-size:0.7rem;"><i class="bi bi-calendar3 me-1"></i>{{ $isLive ? 'En cours...' : $liveDate->format('d/m/Y H:i') }}</small>
                        @if($isLive || $isUpcoming)
                        <a href="{{ $live->stream_url }}" target="_blank" class="pr-btn {{ $isLive ? 'pr-btn-danger' : 'pr-btn-ghost' }} pr-btn-sm" style="font-size:0.75rem;">
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
                </div>
            </div>
            @if($live->stream_url)
            <a href="{{ $live->stream_url }}" target="_blank" rel="noopener noreferrer"
               style="flex-shrink:0;padding:7px 16px;border-radius:8px;background:{{ $providerColor }}22;border:1px solid {{ $providerColor }}55;color:{{ $providerColor }};font-size:0.75rem;text-decoration:none;transition:all 0.2s;white-space:nowrap;">
                <i class="bi {{ $providerIcon }} me-1"></i> {{ $providerName }}
            </a>
            @endif
        </div>
        @endforeach
    </div>
</div>

<!-- CALENDAR -->
<div class="pr-card mb-4">
    <div class="pr-card-header">
        <h4><i class="bi bi-calendar3" style="color:#EF4444;"></i> Calendrier des Lives</h4>
        <span style="color:#64748B;font-size:0.75rem;"><i class="bi bi-info-circle me-1"></i> Cliquez sur un live pour rejoindre</span>
    </div>
    <div class="pr-card-body">
        <div id="livesCalendar"></div>
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
    let calEl = document.getElementById('livesCalendar');
    if (!calEl) return;
    let calendar = new FullCalendar.Calendar(calEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        firstDay: 1,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,listWeek'
        },
        buttonText: { today: "Aujourd'hui", month: 'Mois', week: 'Semaine', list: 'Liste' },
        events: [
            @foreach($lives as $live)
            @if($live->live_date)
            {
                id: '{{ $live->id }}',
                title: '{{ \Illuminate\Support\Str::limit($live->title, 30) }}',
                start: '{{ \Carbon\Carbon::parse($live->live_date)->format('Y-m-d') }}' + 'T' + '{{ $live->start_time ?? '00:00' }}',
                end: '{{ \Carbon\Carbon::parse($live->live_date)->format('Y-m-d') }}' + 'T' + '{{ $live->end_time ?? date('H:i', strtotime(($live->start_time ?? '00:00') . ' +1 hour')) }}',
                url: '{{ $live->stream_url ?? '#' }}',
                backgroundColor: '#DC2626',
                borderColor: '#EF4444',
                textColor: '#FFF',
                extendedProps: {
                    class: '{{ $live->classRoom?->name ?? "-" }}',
                    stream: '{{ $live->stream_url ?? "" }}'
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
