@extends('layouts.student')

@section('title', 'Dashboard')
@section('content')

<style>
/* ── Dashboard specific styles ── */
.dash-hero {
    background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
    border: 1px solid rgba(255,255,255,0.04);
    border-radius: 16px;
    padding: 2rem 2.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.dash-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 500px 250px at 0% 50%, rgba(79,70,229,0.06), transparent),
                radial-gradient(ellipse 300px 300px at 100% 100%, rgba(2,132,199,0.04), transparent);
}

.avatar-ring {
    width: 64px; height: 64px;
    border-radius: 50%;
    padding: 3px;
    background: conic-gradient(#4F46E5, #0284C7, #7C3AED, #4F46E5);
    animation: spin 8s linear infinite;
    flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }
.avatar-inner {
    width: 100%; height: 100%;
    border-radius: 50%;
    background: #0F172A;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1.3rem; color: white;
}

.metrics-row {
    display: flex;
    gap: 1px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.04);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.metric-cell {
    flex: 1;
    padding: 0.85rem 0.5rem;
    text-align: center;
    transition: background 0.2s ease;
}
.metric-cell:hover { background: rgba(255,255,255,0.03); }
.mc-icon { font-size: 0.9rem; margin-bottom: 2px; }
.mc-value { font-weight: 700; font-size: 1.1rem; line-height: 1.2; }
.mc-label { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B; font-weight: 600; }

.dash-tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 1.25rem;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    padding-bottom: 0;
    overflow-x: auto;
}
.dash-tab {
    padding: 10px 16px;
    font-size: 0.82rem;
    font-weight: 500;
    color: #64748B;
    cursor: pointer;
    border: none;
    background: none;
    font-family: inherit;
    transition: all 0.2s ease;
    position: relative;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
}
.dash-tab:hover { color: #94A3B8; }
.dash-tab.active {
    color: #F1F5F9;
}
.dash-tab.active::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    background: #4F46E5;
    border-radius: 2px 2px 0 0;
}

.tab-panel { display: none; }
.tab-panel.active { display: block; }

.action-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 8px;
    transition: background 0.15s ease;
    text-decoration: none;
}
.action-link:hover { background: rgba(255,255,255,0.03); }
.action-link .al-icon {
    width: 38px; height: 38px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.activity-item {
    display: flex;
    gap: 12px;
    padding: 10px 0;
    position: relative;
}
.activity-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 11px; top: 36px;
    bottom: 0;
    width: 1px;
    background: rgba(255,255,255,0.04);
}
.activity-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    margin-top: 5px;
    flex-shrink: 0;
}

.progress-ring-wrap {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto;
}
.progress-ring-wrap canvas {
    width: 100%; height: 100%;
}
.progress-ring-text {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.progress-ring-text .pct {
    font-weight: 800;
    font-size: 1.6rem;
    color: #F1F5F9;
    line-height: 1;
}
.progress-ring-text .lbl {
    font-size: 0.65rem;
    color: #64748B;
    font-weight: 500;
    margin-top: 2px;
}
</style>

<!-- ═══════════ HERO ═══════════ -->
<div class="dash-hero">
    <div class="d-flex align-items-center gap-3 flex-wrap position-relative" style="z-index:1;">
        <div class="avatar-ring">
            <div class="avatar-inner">{{ strtoupper(substr(auth()->user()->name ?? 'E', 0, 1)) }}</div>
        </div>
        <div style="flex:1;min-width:180px;">
            <h1 style="font-size:1.5rem;font-weight:700;color:#F1F5F9;margin:0;display:flex;align-items:center;gap:8px;">
                Bonjour, {{ auth()->user()->name }}
            </h1>
            <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:4px;">
                <span style="font-size:0.8rem;color:#64748B;display:flex;align-items:center;gap:4px;">
                    <span style="width:5px;height:5px;border-radius:50%;background:#059669;display:inline-block;"></span>
                    {{ now()->format('l d F Y') }}
                </span>
                <a href="{{ route('student.levels') }}" style="font-size:0.8rem;color:#64748B;text-decoration:none;display:flex;align-items:center;gap:4px;transition:color 0.2s;" onmouseover="this.style.color='#94A3B8'" onmouseout="this.style.color='#64748B'">
                    <i class="bi bi-box-arrow-up-right" style="font-size:0.65rem;"></i> Accéder aux matières
                </a>
            </div>
        </div>
        <a href="{{ route('student.profile') }}" style="padding:8px 16px;border-radius:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);color:#94A3B8;text-decoration:none;font-size:0.82rem;font-weight:500;transition:all 0.2s;display:flex;align-items:center;gap:6px;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.04)'">
            <i class="bi bi-person-circle"></i> Profil
        </a>
    </div>
</div>

<!-- ═══════════ METRICS ═══════════ -->
<div class="metrics-row">
    @php
        $metrics = [
            ['value' => $coursesCount ?? 0, 'label' => 'Cours', 'icon' => 'book', 'color' => '#4F46E5'],
            ['value' => $lives ?? 0, 'label' => 'Lives', 'icon' => 'broadcast', 'color' => '#DC2626'],
            ['value' => $assignmentsSent ?? 0, 'label' => 'Devoirs', 'icon' => 'upload', 'color' => '#059669'],
            ['value' => number_format($average ?? 0, 1), 'label' => 'Moyenne', 'icon' => 'graph-up', 'color' => '#D97706', 'suffix' => '/20'],
            ['value' => $assignmentCompletion ?? 0, 'label' => 'Réussite', 'icon' => 'trophy', 'color' => '#7C3AED', 'suffix' => '%'],
        ];
    @endphp
    @foreach($metrics as $m)
    <div class="metric-cell">
        <div class="mc-icon" style="color:{{ $m['color'] }};"><i class="bi bi-{{ $m['icon'] }}"></i></div>
        <div class="mc-value" style="color:{{ $m['color'] }};">
            {{ $m['value'] }}
            @if(isset($m['suffix']))<span style="font-size:0.55rem;opacity:0.5;font-weight:600;">{{ $m['suffix'] }}</span>@endif
        </div>
        <div class="mc-label">{{ $m['label'] }}</div>
    </div>
    @endforeach
</div>

<!-- ═══════════ TABS ═══════════ -->
<div class="dash-tabs" id="dashTabs">
    <button class="dash-tab active" data-panel="courses"><i class="bi bi-book"></i> Cours</button>
    <button class="dash-tab" data-panel="performance"><i class="bi bi-bar-chart"></i> Performance</button>
    <button class="dash-tab" data-panel="activity"><i class="bi bi-clock-history"></i> Activité</button>
    <button class="dash-tab" data-panel="profile"><i class="bi bi-person"></i> Profil</button>
</div>

<!-- TAB: Cours -->
<div class="tab-panel active" id="panel-courses">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="pr-card">
                <div class="pr-card-header">
                    <h4><i class="bi bi-lightning" style="color:#4F46E5;"></i> Accès rapide</h4>
                </div>
                <div class="pr-card-body" style="padding:0.75rem;">
                    <div class="row g-1">
                        @foreach([
                            ['route' => 'student.subjects.index', 'icon' => 'book', 'title' => 'Matières', 'sub' => 'Voir les cours', 'color' => '#4F46E5', 'bg' => 'rgba(79,70,229,0.08)'],
                            ['route' => 'student.assignments', 'icon' => 'upload', 'title' => 'Devoirs', 'sub' => 'Soumettre & suivre', 'color' => '#059669', 'bg' => 'rgba(5,150,105,0.08)'],
                            ['route' => 'student.lives', 'icon' => 'broadcast', 'title' => 'Lives', 'sub' => 'Sessions en direct', 'color' => '#DC2626', 'bg' => 'rgba(220,38,38,0.08)'],
                            ['route' => 'student.chats', 'icon' => 'chat-dots', 'title' => 'Chat', 'sub' => 'Posez vos questions', 'color' => '#7C3AED', 'bg' => 'rgba(124,58,237,0.08)'],
                        ] as $item)
                        <div class="col-md-6">
                            <a href="{{ route($item['route']) }}" class="action-link">
                                <div class="al-icon" style="background:{{ $item['bg'] }};color:{{ $item['color'] }};"><i class="bi bi-{{ $item['icon'] }}"></i></div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:0.85rem;font-weight:600;color:#F1F5F9;">{{ $item['title'] }}</div>
                                    <div style="font-size:0.7rem;color:#64748B;">{{ $item['sub'] }}</div>
                                </div>
                                <i class="bi bi-chevron-right" style="color:#475569;font-size:0.65rem;"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="pr-card">
                <div class="pr-card-header">
                    <h4><i class="bi bi-check-circle" style="color:#059669;"></i> Progression</h4>
                </div>
                <div class="pr-card-body text-center">
                    <div class="progress-ring-wrap">
                        <canvas id="progressRing" width="120" height="120"></canvas>
                        <div class="progress-ring-text">
                            <div class="pct">{{ $assignmentCompletion ?? 0 }}%</div>
                            <div class="lbl">Complété</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;justify-content:center;margin-top:1rem;font-size:0.72rem;">
                        <span style="color:#64748B;"><span style="width:6px;height:6px;border-radius:50%;background:#4F46E5;display:inline-block;margin-right:4px;"></span> {{ $assignmentsCorrected ?? 0 }} corrigés</span>
                        <span style="color:#475569;"><span style="width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,0.08);display:inline-block;margin-right:4px;"></span> {{ max(($assignmentsSent ?? 0) - ($assignmentsCorrected ?? 0), 0) }} en attente</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TAB: Performance -->
<div class="tab-panel" id="panel-performance">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="pr-card">
                <div class="pr-card-header">
                    <h4><i class="bi bi-bar-chart-fill" style="color:#0284C7;"></i> Radar de performance</h4>
                </div>
                <div class="pr-card-body">
                    <div style="height:260px;"><canvas id="gradesChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="pr-card">
                <div class="pr-card-header">
                    <h4><i class="bi bi-trophy" style="color:#D97706;"></i> Statistiques</h4>
                </div>
                <div class="pr-card-body">
                    @php $stats = [
                        ['label' => 'Moyenne générale', 'value' => number_format($average ?? 0, 1) . '/20', 'color' => '#D97706', 'pct' => min(($average ?? 0) * 5, 100)],
                        ['label' => 'Devoirs envoyés', 'value' => ($assignmentsSent ?? 0) . ' devoirs', 'color' => '#059669', 'pct' => min(($sentPercent ?? 0), 100)],
                        ['label' => 'Taux de correction', 'value' => number_format($correctedPercent ?? 0, 0) . '%', 'color' => '#4F46E5', 'pct' => min(($correctedPercent ?? 0), 100)],
                        ['label' => 'Présence', 'value' => number_format($presencePercent ?? 0, 0) . '%', 'color' => '#0284C7', 'pct' => min(($presencePercent ?? 0), 100)],
                        ['label' => 'Engagement', 'value' => number_format($engagement ?? 0, 0) . '%', 'color' => '#7C3AED', 'pct' => min(($engagement ?? 0), 100)],
                    ]; @endphp
                    @foreach($stats as $s)
                    <div style="margin-bottom:0.85rem;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span style="font-size:0.75rem;color:#94A3B8;">{{ $s['label'] }}</span>
                            <span style="font-size:0.75rem;font-weight:600;color:{{ $s['color'] }};">{{ $s['value'] }}</span>
                        </div>
                        <div style="height:4px;border-radius:2px;background:rgba(255,255,255,0.04);overflow:hidden;">
                            <div style="height:100%;border-radius:2px;width:{{ $s['pct'] }}%;background:{{ $s['color'] }};transition:width 1s ease;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TAB: Activité -->
<div class="tab-panel" id="panel-activity">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="pr-card">
                <div class="pr-card-header">
                    <h4><i class="bi bi-clock-history" style="color:#059669;"></i> Activité récente</h4>
                </div>
                <div class="pr-card-body" style="padding:0.25rem 1.25rem;">
                    @php $hasActivity = false; @endphp
                    @if(isset($recentAssignments) && $recentAssignments->count() > 0)
                        @foreach($recentAssignments as $ra) @php $hasActivity = true; @endphp
                        <div class="activity-item">
                            <div class="activity-dot" style="background:#059669;"></div>
                            <div>
                                <div style="font-size:0.82rem;font-weight:600;color:#F1F5F9;">Devoir envoyé</div>
                                <div style="font-size:0.72rem;color:#64748B;">{{ $ra->title }}</div>
                                <div style="font-size:0.65rem;color:#475569;margin-top:1px;">{{ $ra->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                    @if(isset($recentCourses2) && $recentCourses2->count() > 0)
                        @foreach($recentCourses2 as $rc) @php $hasActivity = true; @endphp
                        <div class="activity-item">
                            <div class="activity-dot" style="background:#4F46E5;"></div>
                            <div>
                                <div style="font-size:0.82rem;font-weight:600;color:#F1F5F9;">Cours consulté</div>
                                <div style="font-size:0.72rem;color:#64748B;">{{ $rc->title }}</div>
                                <div style="font-size:0.65rem;color:#475569;margin-top:1px;">{{ $rc->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                    @if(isset($absences) && $absences->count() > 0)
                        @foreach($absences as $ab) @php $hasActivity = true; @endphp
                        <div class="activity-item">
                            <div class="activity-dot" style="background:#DC2626;"></div>
                            <div>
                                <div style="font-size:0.82rem;font-weight:600;color:#F1F5F9;">Absence signalée</div>
                                <div style="font-size:0.72rem;color:#64748B;">{{ \Carbon\Carbon::parse($ab->date)->format('d M Y') }}</div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                    @if(!$hasActivity)
                    <div style="text-align:center;padding:2rem;color:#475569;">
                        <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:0.5rem;"></i>
                        <span style="font-size:0.85rem;">Aucune activité récente</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="pr-card">
                <div class="pr-card-header">
                    <h4><i class="bi bi-calendar-week" style="color:#4F46E5;"></i> Calendrier</h4>
                </div>
                <div class="pr-card-body" style="padding:0.75rem;">
                    <div id="calendar" style="height:260px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TAB: Profil -->
<div class="tab-panel" id="panel-profile">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="pr-card text-center">
                <div class="pr-card-body" style="padding:2rem 1.5rem;">
                    <div style="width:80px;height:80px;border-radius:50%;margin:0 auto 1rem;padding:3px;background:conic-gradient(#4F46E5, #0284C7, #7C3AED, #4F46E5);animation:spin 8s linear infinite;">
                        <div style="width:100%;height:100%;border-radius:50%;background:#0F172A;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.6rem;color:white;">{{ strtoupper(substr(auth()->user()->name ?? 'E', 0, 1)) }}</div>
                    </div>
                    <h5 style="font-weight:700;color:#F1F5F9;margin-bottom:0.15rem;">{{ auth()->user()->name }}</h5>
                    <p style="font-size:0.75rem;color:#64748B;margin-bottom:1rem;">{{ auth()->user()->email }}</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('student.profile') }}" class="pr-btn pr-btn-primary pr-btn-sm">Mon profil</a>
                        <a href="{{ route('student.settings') }}" class="pr-btn pr-btn-ghost pr-btn-sm">Paramètres</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="pr-card">
                <div class="pr-card-header">
                    <h4><i class="bi bi-info-circle" style="color:#4F46E5;"></i> Informations</h4>
                </div>
                <div class="pr-card-body">
                    <div class="row g-3">
                        @foreach([
                            ['label' => 'Nom', 'value' => auth()->user()->name, 'icon' => 'person'],
                            ['label' => 'Email', 'value' => auth()->user()->email, 'icon' => 'envelope'],
                            ['label' => 'Rôle', 'value' => 'Étudiant', 'icon' => 'mortarboard'],
                            ['label' => 'Membre depuis', 'value' => auth()->user()->created_at->format('d/m/Y'), 'icon' => 'calendar3'],
                        ] as $info)
                        <div class="col-md-6">
                            <div style="padding:0.75rem;border-radius:8px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                                <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.04em;color:#64748B;font-weight:600;margin-bottom:2px;">
                                    <i class="bi bi-{{ $info['icon'] }} me-1" style="color:#4F46E5;"></i> {{ $info['label'] }}
                                </div>
                                <div style="font-weight:600;font-size:0.85rem;color:#F1F5F9;">{{ $info['value'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tab switching
    const tabs = document.querySelectorAll('.dash-tab');
    const panels = {};
    document.querySelectorAll('.tab-panel').forEach(p => { panels[p.id] = p; });
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            Object.values(panels).forEach(p => p.classList.remove('active'));
            const target = document.getElementById('panel-' + this.dataset.panel);
            if (target) {
                target.classList.add('active');
                if (this.dataset.panel === 'performance' && chartInstance) {
                    setTimeout(() => chartInstance.resize(), 50);
                }
            }
        });
    });

    // Progress Ring
    const ring = document.getElementById('progressRing');
    if (ring) {
        const ctx = ring.getContext('2d');
        const completion = Math.min({{ $assignmentCompletion ?? 0 }}, 100);
        const cx = 60, cy = 60, r = 50, start = -Math.PI / 2;
        const end = start + (completion / 100) * 2 * Math.PI;
        ctx.clearRect(0, 0, 120, 120);
        ctx.beginPath(); ctx.arc(cx, cy, r, 0, 2 * Math.PI);
        ctx.strokeStyle = 'rgba(255,255,255,0.05)';
        ctx.lineWidth = 6; ctx.stroke();
        ctx.beginPath(); ctx.arc(cx, cy, r, start, end);
        ctx.strokeStyle = '#4F46E5';
        ctx.lineWidth = 6; ctx.lineCap = 'round'; ctx.stroke();
    }

    // Chart
    let chartInstance = null;
    function initChart() {
        const ctx = document.getElementById('gradesChart')?.getContext('2d');
        if (!ctx || chartInstance) return;
        chartInstance = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Moyenne', 'Devoirs', 'Corrigés', 'Présence', 'Engagement'],
                datasets: [{
                    label: 'Performance',
                    data: [{{ min(($average ?? 0) * 5, 100) }}, {{ $sentPercent ?? 0 }}, {{ $correctedPercent ?? 0 }}, {{ $presencePercent ?? 0 }}, {{ $engagement ?? 0 }}],
                    backgroundColor: 'rgba(79,70,229,0.08)',
                    borderColor: '#4F46E5',
                    borderWidth: 2,
                    pointBackgroundColor: '#4F46E5',
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    r: {
                        min: 0, max: 100,
                        ticks: { stepSize: 25, backdropColor: 'transparent', color: '#64748B', font: { size: 9 } },
                        grid: { color: 'rgba(255,255,255,0.04)' },
                        angleLines: { color: 'rgba(255,255,255,0.04)' },
                        pointLabels: { color: '#94A3B8', font: { size: 10, weight: '600' } }
                    }
                }
            }
        });
    }

    // Calendar
    var calEl = document.getElementById('calendar');
    if (calEl && typeof FullCalendar !== 'undefined') {
        var cal = new FullCalendar.Calendar(calEl, {
            initialView: 'dayGridMonth',
            height: 260,
            headerToolbar: { left: 'prev', center: 'title', right: 'next' },
            events: [@foreach($assignments ?? [] as $a){
                title: '{{ \Str::limit($a->title, 20) }}',
                start: '{{ $a->created_at->format('Y-m-d') }}',
                backgroundColor: '#4F46E5',
                borderColor: 'transparent'
            },@endforeach]
        });
        cal.render();
    }
});
</script>
@endpush

<style>
.fc { color: #94A3B8; font-size: 0.7rem; }
.fc-toolbar-title { color: #F1F5F9 !important; font-size: 0.8rem !important; font-weight: 700; }
.fc-button-primary { background: #4F46E5 !important; border: none !important; padding: 2px 8px !important; font-size: 0.65rem !important; border-radius: 4px !important; }
.fc-col-header-cell { background: rgba(255,255,255,0.02); padding: 2px 0 !important; }
.fc-theme-standard td, .fc-theme-standard th { border-color: rgba(255,255,255,0.04); }
.fc-daygrid-day-number { color: #64748B; font-size: 0.6rem; }
.fc .fc-day-other { background: rgba(255,255,255,0.01); }

/* ══════════════════════════════════════════════════════════════
   MODE CLAIR — Student Dashboard (page-specific)
   ══════════════════════════════════════════════════════════════ */
html.light-mode .dash-hero {
    background: rgba(255,255,255,0.92) !important;
    border-color: rgba(0,0,0,0.06) !important;
}
html.light-mode .dash-hero h1 {
    color: #1e293b !important;
}
html.light-mode .dash-hero span[style*="color:#64748B"] {
    color: #94a3b8 !important;
}
html.light-mode .dash-hero a[style*="color:#64748B"] {
    color: #64748b !important;
}
html.light-mode .dash-hero a[style*="color:#64748B"]:hover {
    color: #1e293b !important;
}
html.light-mode .avatar-inner {
    background: #f0f2f5 !important;
    color: #1e293b !important;
}
html.light-mode .metrics-row {
    background: rgba(0,0,0,0.02) !important;
    border-color: rgba(0,0,0,0.06) !important;
}
html.light-mode .metric-cell:hover {
    background: rgba(0,0,0,0.02) !important;
}
html.light-mode .mc-label {
    color: #94a3b8 !important;
}
html.light-mode .dash-tabs {
    border-bottom-color: rgba(0,0,0,0.06) !important;
}
html.light-mode .dash-tab {
    color: #94a3b8 !important;
}
html.light-mode .dash-tab:hover {
    color: #64748b !important;
}
html.light-mode .dash-tab.active {
    color: #1e293b !important;
}
html.light-mode .dash-tab.active::after {
    background: #4F46E5 !important;
}
html.light-mode .action-link div[style*="color:#F1F5F9"] {
    color: #1e293b !important;
}
html.light-mode .action-link i[style*="color:#475569"] {
    color: #94a3b8 !important;
}
html.light-mode .progress-ring-text .pct {
    color: #1e293b !important;
}
html.light-mode .progress-ring-text .lbl {
    color: #64748b !important;
}
html.light-mode .activity-item div[style*="color:#F1F5F9"] {
    color: #1e293b !important;
}
html.light-mode .activity-item div[style*="color:#475569"] {
    color: #94a3b8 !important;
}
html.light-mode .action-link div[style*="color:#F1F5F9"] {
    color: #1e293b !important;
}
html.light-mode div[style*="border:1px solid rgba(255,255,255,0.04)"] {
    border-color: rgba(0,0,0,0.06) !important;
}
html.light-mode div[style*="background:rgba(255,255,255,0.02)"] {
    background: rgba(0,0,0,0.02) !important;
}
html.light-mode .pr-card-body div[style*="color:#F1F5F9"] {
    color: #1e293b !important;
}
html.light-mode .activity-item div[style*="color:#64748B"] {
    color: #94a3b8 !important;
}
html.light-mode .st-content div[style*="color:#64748B"]:not(.fc *) {
    color: #94a3b8 !important;
}
html.light-mode .dash-hero a[style*="background:rgba(255,255,255,0.04)"] {
    background: rgba(0,0,0,0.03) !important;
    border-color: rgba(0,0,0,0.08) !important;
    color: #64748b !important;
}
html.light-mode .dash-hero a[style*="background:rgba(255,255,255,0.04)"]:hover {
    background: rgba(0,0,0,0.06) !important;
}
html.light-mode .fc-toolbar-title {
    color: #1e293b !important;
}
html.light-mode .fc-col-header-cell {
    background: rgba(0,0,0,0.02) !important;
}
html.light-mode .fc-theme-standard td,
html.light-mode .fc-theme-standard th {
    border-color: rgba(0,0,0,0.06) !important;
}
html.light-mode .fc-daygrid-day-number {
    color: #64748b !important;
}
html.light-mode .fc .fc-day-other {
    background: rgba(0,0,0,0.01) !important;
}
html.light-mode .fc {
    color: #64748b !important;
}
</style>

@endsection
