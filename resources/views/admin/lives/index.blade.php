@extends('layouts.admin')

@section('title', 'Gestion des Lives')
@section('page_title', 'Lives')
@section('breadcrumb', 'Gestion des lives')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-camera-video me-2" style="color:var(--adm-danger);"></i> Lives</h1>
        <div class="subtitle">Parcourez la hiérarchie : Matière → Niveau → Classe → Lives</div>
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
        <div class="stat-value">{{ $totalLives ?? 0 }}</div>
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
        <h4><i class="bi bi-book" style="color:var(--adm-primary);"></i> Matières avec des lives</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $subjects->count() }} matière(s)</span>
        </div>
    </div>
</div>

@if($subjects->isEmpty())
    <div class="adm-card mt-3">
        <div class="adm-empty" style="padding:4rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-camera-video"></i></div>
            <h5>Aucun live</h5>
            <p>Créez votre premier live pour commencer.</p>
            <a href="{{ route('admin.lives.create') }}" class="adm-btn adm-btn-danger mt-3">
                <i class="bi bi-plus-lg"></i> Créer un live
            </a>
        </div>
    </div>
@else
    <div class="row g-4 mt-2">
        @php
            $icons = ['book','calculator','flask','translate','globe','palette','music-note-beamed','cpu','graph-up','pencil','journal','robot'];
            $gradients = [
                'linear-gradient(135deg, #7C3AED, #A78BFA)',
                'linear-gradient(135deg, #059669, #22C55E)',
                'linear-gradient(135deg, #D97706, #FFB347)',
                'linear-gradient(135deg, #2563EB, #60A5FA)',
                'linear-gradient(135deg, #DC2626, #EF4444)',
                'linear-gradient(135deg, #0891B2, #06B6D4)',
                'linear-gradient(135deg, #9333EA, #C084FC)',
                'linear-gradient(135deg, #16A34A, #4ADE80)',
            ];
        @endphp
        @foreach($subjects as $subject)
            @php
                $icon = $icons[$loop->index % count($icons)];
                $gradient = $gradients[$loop->index % count($gradients)];
                $liveCount = $subjectLiveCounts[$subject->id] ?? 0;
                $classCount = $subject->classes_count ?? $subject->classes->count();
            @endphp
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('admin.lives.subject-levels', $subject) }}" class="text-decoration-none">
                    <div class="adm-card st-fade-up" style="cursor:pointer;height:100%;transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                        <div style="height:120px;background:{{ $gradient }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                            <div style="position:absolute;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.06);top:-50px;right:-50px;"></div>
                            <div style="position:absolute;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.04);bottom:-30px;left:-30px;"></div>
                            <i class="bi bi-{{ $icon }}" style="font-size:3rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                        </div>
                        <div class="adm-card-body text-center" style="padding:1.25rem;">
                            <h4 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.5rem;">{{ $subject->name }}</h4>
                            <div style="display:flex;gap:12px;justify-content:center;margin-bottom:1rem;">
                                <span style="color:var(--adm-text-muted);font-size:0.8rem;">
                                    <i class="bi bi-camera-video me-1" style="color:#EF4444;"></i> {{ $liveCount }} live(s)
                                </span>
                                <span style="color:var(--adm-text-muted);font-size:0.8rem;">
                                    <i class="bi bi-building me-1"></i> {{ $classCount }} classe(s)
                                </span>
                            </div>
                            <span class="adm-btn" style="background:{{ $gradient }};color:white;border:none;width:100%;">
                                <i class="bi bi-layers me-1"></i> Voir les niveaux
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif

<!-- ALL LIVES TABLE (optional overview) -->
@if($allLives->count() > 0)
<div class="adm-card mt-4">
    <div class="adm-card-header">
        <h4><i class="bi bi-collection" style="color:rgba(255,255,255,0.35);"></i> Tous les lives <span style="font-size:0.75rem;color:var(--adm-text-muted);font-weight:400;margin-left:6px;">(vue d'ensemble)</span></h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $allLives->count() }} live(s)</span>
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
                    @forelse($allLives->take(10) as $live)
                    <tr>
                        <td><span style="font-weight:500;">{{ $live->title }}</span></td>
                        <td>
                            @if($live->classRoom && $live->classRoom->level)
                                <span class="adm-badge adm-badge-danger">{{ $live->classRoom->name ?? '-' }}</span>
                                <span style="color:var(--adm-text-muted);font-size:0.7rem;margin-left:4px;">({{ $live->classRoom->level->name ?? '' }})</span>
                            @else
                                <span class="adm-badge adm-badge-danger">{{ $live->classRoom->name ?? '-' }}</span>
                            @endif
                        </td>
                        <td>
                            @if($live->stream_url)
                            <a href="{{ $live->stream_url }}" target="_blank" class="adm-btn adm-btn-ghost adm-btn-sm">
                                <i class="bi bi-box-arrow-up-right"></i> Ouvrir
                            </a>
                            @else
                            <span style="color:var(--adm-text-muted);font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td style="color:var(--adm-text-muted);font-size:0.8rem;">
                            @if($live->live_date)
                                {{ \Carbon\Carbon::parse($live->live_date)->format('d/m/Y') }}
                            @else
                                {{ $live->created_at->format('d/m/Y') }}
                            @endif
                        </td>
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
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

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
            @foreach($allLives as $live)
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
