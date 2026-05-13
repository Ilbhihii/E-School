@extends('layouts.prof')

@section('content')
<div class="container-fluid py-4 min-vh-100" style="background: linear-gradient(135deg, #eef2ff, #f8fafc);">

    <div class="container">

        <!-- TITLE -->
        <h1 class="fw-bold mb-4 text-danger d-flex align-items-center">
            📡 <span class="ms-2">Gestion des Lives</span>
        </h1>

        <!-- STATS -->
        <div class="row g-4 mb-4">

            <!-- TOTAL -->
            <div class="col-md-4">
                <div class="card shadow border-0 rounded-4 p-4 hover-card">
                    <h5>Total Lives</h5>
                    <h2 class="fw-bold text-danger">{{ $totalLives }}</h2>
                </div>
            </div>

            <!-- RECENT -->
            <div class="col-md-4">
                <div class="card shadow border-0 rounded-4 p-4 hover-card">
                    <h5>Lives Récents</h5>
                    <h2 class="fw-bold text-warning">{{ $recentLives->count() }}</h2>
                </div>
            </div>

            <!-- ACTION -->
            <div class="col-md-4 d-flex align-items-center">
                <a href="{{ route('prof.lives.create') }}" 
                   class="btn btn-danger w-100 py-3 rounded-3 shadow">
                    ➕ Nouveau Live
                </a>
            </div>

        </div>

        <!-- RECENT LIST -->
        <div class="card shadow border-0 rounded-4 mb-4 p-4">
            <h5 class="mb-3">📡 Lives récents</h5>

            @forelse($recentLives as $live)
                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-2 hover-row">
                    <span>{{ $live->title }}</span>
                    <small class="text-muted">{{ $live->created_at->diffForHumans() }}</small>
                </div>
            @empty
                <div class="text-muted text-center">Aucun live</div>
            @endforelse
        </div>

        <!-- TABLE -->
        <div class="card shadow border-0 rounded-4 p-4">

            <h5 class="mb-3">📋 Tous les Lives</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-light">
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

                            <td>{{ $live->title }}</td>

                            <td>
                                <span class="badge bg-danger">
                                    {{ $live->classRoom?->name ?? 'Non assignée' }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ $live->stream_url }}" target="_blank" class="btn btn-sm btn-primary">
                                    🔗 Ouvrir
                                </a>
                            </td>

                            <td>{{ $live->created_at->format('d/m/Y') }}</td>

                            <td>
                                <a href="{{ route('prof.lives.edit', $live) }}" class="btn btn-sm btn-warning">
                                    ✏️
                                </a>

                                <form method="POST" action="{{ route('prof.lives.destroy',$live) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Supprimer ?')" class="btn btn-sm btn-danger">
                                        🗑️
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
            
            <!-- Pagination -->
            @if (method_exists($lives, 'links'))
            <div class="mt-6 flex justify-center">
                {{ $lives->appends(request()->query())->links() }}
            </div>
            @endif

        </div>

        <!-- CALENDAR -->
        <div class="card shadow border-0 rounded-4 p-4 mt-4">
            <h5 class="mb-3">📅 Calendrier des lives</h5>
            <div id="calendar"></div>
        </div>

    </div>

</div>

<!-- FLOAT BUTTON -->
<a href="{{ route('prof.lives.create') }}" 
   class="btn btn-danger position-fixed bottom-0 end-0 m-4 rounded-circle shadow-lg"
   style="width:60px;height:60px;font-size:24px;">
   ➕
</a>

<!-- FULLCALENDAR -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function () {



    // CALENDAR
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        themeSystem: 'bootstrap5',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },

        events: [
            @foreach($lives as $live)
            @if($live->live_date && $live->start_time && $live->end_time)
            {
                title: "🎥 {{ $live->title }} ({{ $live->classRoom?->name ?? '' }})",
                start: "{{ $live->live_date }}T{{ $live->start_time }}",
                end: "{{ $live->live_date }}T{{ $live->end_time }}",
                color: "#dc3545"
            },
            @endif
            @endforeach
        ]
    });

    calendar.render();
});
</script>

<!-- STYLE -->
<style>

/* HOVER CARD */
.hover-card:hover {
    transform: translateY(-5px);
    transition: 0.3s;
}

/* HOVER ROW */
.hover-row:hover {
    background: #f1f5f9 !important;
    transition: 0.3s;
}

/* DARK MODE AUTO */
@media (prefers-color-scheme: dark) {

    body {
        background: #0f172a !important;
        color: white;
    }

    .card {
        background: #1e293b !important;
        color: white;
    }

    .table {
        color: white;
    }

    .table-light {
        background: #334155 !important;
    }

    .bg-light {
        background: #334155 !important;
    }

}

</style>

@endsection
