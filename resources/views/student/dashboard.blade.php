@extends('layouts.student')

@section('content')

<!-- HERO -->
<div class="hero-student text-white p-5 rounded-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h2 class="fw-bold">
                👋 Bienvenue {{ auth()->user()->name }}
            </h2>
            <p class="mb-0 opacity-75">
                Voici votre espace étudiant
            </p>
        </div>

        <div class="profile-avatar">
            <i class="bi bi-person-circle"></i>
        </div>
    </div>
</div>

<div class="container-fluid">

<!-- STATS -->
<div class="row g-4 mb-4">

    <!-- Cours -->
    <div class="col-md-6">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-book fs-1 text-primary"></i>
                <h2 class="fw-bold mt-2">{{ $coursesCount }}</h2>
                <p class="text-muted">Mes Cours</p>

                <a href="{{ route('student.levels') }}" class="btn btn-outline-primary btn-sm">
                    Voir
                </a>
            </div>
        </div>
    </div>

    <!-- Lives -->
    <div class="col-md-6">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-camera-video fs-1 text-success"></i>
                <h2 class="fw-bold mt-2">{{ $lives ?? 0 }}</h2>
                <p class="text-muted">Lives</p>

                <a href="{{ route('student.lives') }}" class="btn btn-outline-success btn-sm">
                    Voir
                </a>
            </div>
        </div>
    </div>

</div>

<!-- METRICS -->
<div class="row g-4 mb-4">

    <div class="col-md-4">
        <div class="metric bg-primary text-white p-4 rounded-4 text-center">
            <i class="bi bi-upload fs-2"></i>
            <h3 class="fw-bold mt-2">{{ $assignmentsSent }}</h3>
            <small>Devoirs envoyés</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="metric bg-success text-white p-4 rounded-4 text-center">
            <i class="bi bi-check-circle fs-2"></i>
            <h3 class="fw-bold mt-2">{{ $assignmentsCorrected }}</h3>
            <small>Devoirs corrigés</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="metric bg-warning text-white p-4 rounded-4 text-center">
            <i class="bi bi-graph-up fs-2"></i>
            <h3 class="fw-bold mt-2">{{ number_format($average,1) }}/20</h3>
            <small>Moyenne</small>
        </div>
    </div>

</div>

<!-- CHART -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">
            📊 Évolution des notes
        </h5>
        <div class="chart-container">
            <canvas id="gradesChart"></canvas>
        </div>
    </div>
</div>

<!-- CALENDAR -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3">
            📅 Calendrier des devoirs
        </h5>
        <div id="calendar" style="height:500px;"></div>
    </div>
</div>

</div>

<!-- STYLE -->
<style>

/* HERO */
.hero-student{
    background: linear-gradient(135deg,#003A8F,#4DA3FF);
}

/* AVATAR */
.profile-avatar{
    font-size:60px;
}

/* CARDS */
.stat-card{
    border-radius:15px;
    transition:0.3s;
}
.stat-card:hover{
    transform:translateY(-5px);
}

/* METRIC */
.metric{
    border-radius:15px;
    transition:0.3s;
}
.metric:hover{
    transform:scale(1.05);
}

/* CALENDAR */
#calendar {
    height: 600px;
    max-width: 900px;
    margin: auto;
    background: #fff;
    border-radius: 15px;
    padding: 10px;
}

.fc {
    font-family: 'Segoe UI', sans-serif;
}

.fc-toolbar-title {
    font-weight: bold;
    color: #003A8F;
}

.fc-button {
    background: #003A8F !important;
    border: none !important;
    border-radius: 8px !important;
}

.fc-button:hover {
    background: #0056d2 !important;
}

.fc-daygrid-event {
    background: #4DA3FF;
    border: none;
    padding: 2px 5px;
    border-radius: 6px;
}

/* CHART CONTAINER */
.chart-container{
    height:350px;
    max-width:600px;
    margin:auto;
}



</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){


    var calendarEl = document.getElementById('calendar');

    if(calendarEl){
        var calendar = new FullCalendar.Calendar(calendarEl,{
            initialView:'dayGridMonth',
            height:500,
            events:[
                {
                    title: 'Test Event',
                    start: '2026-04-17'
                },
                @foreach($assignments as $a)
                {
                    title:'{{ $a->title }}',
                    start:'{{ $a->created_at->format('Y-m-d') }}'
                },
                @endforeach
            ]
        });

        calendar.render();
    }

});
</script>

<!-- Existing Chart.js -->
<script>
const ctx = document.getElementById('gradesChart').getContext('2d');

new Chart(ctx, {
    type: 'radar',
    data: {
        labels: [
            'Moyenne',
            'Devoirs envoyés',
            'Devoirs corrigés',
            'Présence',
            'Engagement'
        ],
        datasets: [{
            label: 'Performance',
            data: [
                {{ $average }},
                {{ $sentPercent }},
                {{ $correctedPercent }},
                {{ $presencePercent }},
                {{ $engagement }}
            ],
            backgroundColor: 'rgba(0,58,143,0.2)', // bleu institution
            borderColor: '#003A8F',
            borderWidth: 2,
            pointBackgroundColor: '#003A8F',
            pointBorderColor: '#fff',
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,

        scales: {
            r: {
                min: 0,
                max: 100,

                ticks: {
                    stepSize: 20,
                    backdropColor: 'transparent'
                },

                grid: {
                    color: 'rgba(0,0,0,0.1)'
                },

                angleLines: {
                    color: 'rgba(0,0,0,0.1)'
                },

                pointLabels: {
                    color: '#2B2D42',
                    font: {
                        size: 13,
                        weight: 'bold'
                    }
                }
            }
        },

        plugins: {
            legend: {
                labels: {
                    color: '#2B2D42',
                    font: {
                        size: 14
                    }
                }
            }
        }
    }
});
</script>
@endpush

@endsection
