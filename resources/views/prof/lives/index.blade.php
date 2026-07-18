@extends('layouts.prof')

@section('title', 'Mes Lives')
@section('page_title', 'Lives')
@section('breadcrumb', 'Gestion des lives')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-camera-video me-2" style="color:var(--adm-danger);"></i> Lives</h1>
        <div class="subtitle">Consultez les sessions en direct programmées</div>
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
    <div class="adm-stat red">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
        </div>
        <div class="stat-value">{{ $upcomingLives ?? 0 }}</div>
        <div class="stat-label">À venir</div>
    </div>
</div>

<!-- TABLE -->
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
                            @if($live->stream_url)
                            <a href="{{ $live->stream_url }}" target="_blank" class="adm-btn adm-btn-primary adm-btn-sm">
                                <i class="bi bi-box-arrow-up-right"></i> Rejoindre
                            </a>
                            @else
                            <span class="adm-badge adm-badge-gray" style="font-size:0.72rem;">Lien à venir</span>
                            @endif
                            @if($live->live_date)
                            @php
                                $liveDate = \Carbon\Carbon::parse($live->live_date);
                                $startTime = $live->start_time ? $live->start_time : '00:00';
                                $endTime = $live->end_time ? $live->end_time : date('H:i', strtotime($startTime . ' +1 hour'));
                                $startDt = $liveDate->format('Y-m-d') . 'T' . $startTime . ':00Z';
                                $endDt = $liveDate->format('Y-m-d') . 'T' . $endTime . ':00Z';
                                $outlookUrl = 'https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent';
                                $outlookUrl .= '&subject=' . urlencode($live->title);
                                $outlookUrl .= '&startdt=' . $startDt;
                                $outlookUrl .= '&enddt=' . $endDt;
                                $outlookUrl .= '&body=' . urlencode(($live->description ?? 'Session en direct') . "\n\nLien : " . ($live->stream_url ?? ''));
                                $outlookUrl .= '&location=' . urlencode($live->stream_url ?? '');
                            @endphp
                            <a href="{{ $outlookUrl }}" target="_blank" class="adm-btn adm-btn-sm" style="background:rgba(2,132,199,0.12);color:#38BDF8;border:1px solid rgba(2,132,199,0.15);"
                               onmouseover="this.style.background='rgba(2,132,199,0.25)'" onmouseout="this.style.background='rgba(2,132,199,0.12)'">
                                <i class="bi bi-calendar-plus"></i> Outlook
                            </a>
                            @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-camera-video"></i></div>
                                <h5>Aucun live</h5>
                                <p>Aucune session en direct n'a été programmée pour le moment.</p>
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

<!-- Outlook Calendar List -->
<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-calendar-plus" style="color:rgba(255,255,255,0.35);"></i> Ajouter au calendrier Outlook</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.78rem;"><i class="bi bi-info-circle me-1"></i> Chaque live peut être ajouté à Outlook</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        @forelse($lives as $live)
        @php
            $liveDate = $live->live_date ? \Carbon\Carbon::parse($live->live_date) : null;
            $startTime = $live->start_time ? $live->start_time : '00:00';
            $endTime = $live->end_time ? $live->end_time : date('H:i', strtotime($startTime . ' +1 hour'));
            $startDt = $liveDate ? $liveDate->format('Y-m-d') . 'T' . $startTime . ':00Z' : '';
            $endDt = $liveDate ? $liveDate->format('Y-m-d') . 'T' . $endTime . ':00Z' : '';
            $outlookUrl = 'https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent';
            $outlookUrl .= '&subject=' . urlencode($live->title);
            $outlookUrl .= '&startdt=' . $startDt;
            $outlookUrl .= '&enddt=' . $endDt;
            $outlookUrl .= '&body=' . urlencode(($live->description ?? 'Session en direct') . "\n\nLien : " . ($live->stream_url ?? ''));
            $outlookUrl .= '&location=' . urlencode($live->stream_url ?? '');
        @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.85rem 1.25rem;border-bottom:1px solid rgba(255,255,255,0.04);gap:12px;transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
            <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#DC2626,#EF4444);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-camera-video-fill" style="font-size:0.85rem;color:white;"></i>
                </div>
                <div style="min-width:0;">
                    <div style="font-weight:500;font-size:0.85rem;color:rgba(255,255,255,0.85);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $live->title }}</div>
                    @if($liveDate)
                    <div style="color:var(--adm-text-muted);font-size:0.72rem;margin-top:2px;">
                        <i class="bi bi-calendar3 me-1"></i>{{ $liveDate->format('d/m/Y') }}
                        @if($live->start_time) <i class="bi bi-clock ms-2 me-1"></i>{{ $live->start_time }} @endif
                        <span class="adm-badge adm-badge-danger" style="font-size:0.6rem;margin-left:8px;">{{ $live->classRoom?->name ?? '-' }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @if($live->live_date)
            <a href="{{ $outlookUrl }}" target="_blank"
               style="flex-shrink:0;display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);color:rgba(255,255,255,0.5);font-size:0.78rem;text-decoration:none;transition:all 0.2s;white-space:nowrap;"
               onmouseover="this.style.background='rgba(2,132,199,0.15)';this.style.borderColor='rgba(2,132,199,0.2)';this.style.color='#38BDF8'"
               onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.borderColor='rgba(255,255,255,0.06)';this.style.color='rgba(255,255,255,0.5)'">
                <i class="bi bi-calendar-plus"></i> Outlook
            </a>
            @endif
        </div>
        @empty
        <div class="adm-empty">
            <div class="adm-empty-icon"><i class="bi bi-calendar-plus"></i></div>
            <h5>Aucun live à ajouter</h5>
            <p>Les lives apparaîtront ici avec un bouton pour les ajouter à Outlook.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- CALENDAR -->
<div class="adm-card mt-4">
    <div class="adm-card-header">
        <h4><i class="bi bi-calendar3" style="color:#EF4444;"></i> Calendrier des Lives</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.78rem;"><i class="bi bi-info-circle me-1"></i> Cliquez sur un live pour rejoindre</span>
        </div>
    </div>
    <div class="adm-card-body">
        <div id="livesCalendar"></div>
    </div>
</div>

@push('head')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<style>
#livesCalendar { min-height: 480px; }
.fc { color: rgba(255,255,255,0.85); font-family: 'Inter', sans-serif; }
.fc-toolbar-title { font-family: 'Poppins', sans-serif; font-weight: 700 !important; font-size: 1.2rem !important; color: rgba(255,255,255,0.9) !important; }
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
@media (max-width: 768px) { .fc-toolbar { flex-direction: column; gap: 0.75rem; } #livesCalendar { min-height: 320px; } }
</style>
@endpush

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