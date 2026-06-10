@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📡 Gestion des Lives</span></h1>
                <p class="admin-header-subtitle">Tableau de gestion des lives, planning et statistiques</p>
            </div>
            <a href="{{ route('admin.lives.create') }}" class="adm-btn adm-btn-danger">
                <i class="bi bi-plus-lg"></i> Nouveau Live
            </a>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card red adm-fade-up">
                <div class="stat-card-icon red"><i class="bi bi-camera-video-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $totalLives }}</div>
                    <div class="stat-card-label">Total Lives</div>
                </div>
            </div>
            <div class="stat-card amber adm-fade-up">
                <div class="stat-card-icon amber"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-card-value">{{ $recentLives->count() }}</div>
                    <div class="stat-card-label">Lives récents</div>
                </div>
            </div>
            <div class="stat-card purple adm-fade-up">
                <div class="stat-card-icon purple"><i class="bi bi-calendar-week-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $lives->count() }}</div>
                    <div class="stat-card-label">Planifiés</div>
                </div>
            </div>
        </div>

        <!-- RECENT -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-header">
                <h3><i class="bi bi-lightning-fill"></i> Lives récents</h3>
            </div>
            <div class="adm-card-body">
                <div class="adm-grid-3">
                    @forelse($recentLives as $live)
                        <div class="adm-card" style="background:var(--adm-surface);border:1px solid var(--adm-border);backdrop-filter:none;">
                            <div class="adm-card-body" style="padding:1rem;">
                                <div class="adm-flex adm-flex-between adm-mb-1">
                                    <span style="font-weight:600;">{{ $live->title }}</span>
                                    <span style="font-size:0.78rem;color:var(--adm-text-secondary);">{{ $live->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="color:var(--adm-text-secondary);grid-column:1/-1;">Aucun live récent</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3>Tous les Lives</h3>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Classe</th>
                            <th>Lien</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lives as $live)
                        <tr>
                            <td><span style="font-weight:600;">{{ $live->title }}</span></td>
                            <td><span class="adm-badge adm-badge-danger">{{ $live->classRoom->name ?? '-' }}</span></td>
                            <td><a href="{{ $live->stream_url }}" target="_blank" class="adm-btn adm-btn-sm adm-btn-ghost"><i class="bi bi-box-arrow-up-right"></i> Ouvrir</a></td>
                            <td><span class="adm-badge adm-badge-gray">{{ $live->created_at->format('d/m/Y') }}</span></td>
                            <td>
                                <div class="adm-actions">
                                    <a href="{{ route('admin.lives.edit', $live) }}" class="adm-action-link adm-action-edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.lives.destroy',$live) }}" onsubmit="return confirm('Supprimer ?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-action-link adm-action-delete" style="border:none;cursor:pointer;">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CALENDAR -->
        <div class="adm-card adm-mt-3">
            <div class="adm-card-header">
                <h3><i class="bi bi-calendar3"></i> Calendrier des lives</h3>
            </div>
            <div class="adm-card-body">
                <div id="calendar"></div>
            </div>
        </div>

    </div>
</div>

<!-- FULLCALENDAR -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        events: [
            @foreach($lives as $live)
            {
                title: "{{ $live->title }}",
                start: "{{ $live->live_date }}T{{ $live->start_time }}",
                end: "{{ $live->live_date }}T{{ $live->end_time }}"
            },
            @endforeach
        ]
    });
    calendar.render();
});
</script>
@endsection
