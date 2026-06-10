@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    {{-- Hero --}}
    <div class="st-hero st-flex-between st-fade-up">
      <div>
        <h1>👋 Bienvenue {{ auth()->user()->name }}</h1>
        <p>Voici votre espace étudiant</p>
      </div>
      <div style="font-size: 3rem; opacity: .7;">
        <i class="bi bi-person-circle"></i>
      </div>
    </div>

    {{-- Stats --}}
    <div class="st-stats st-stats-3 st-fade-up st-fade-up-d1">
      <div class="st-stat">
        <div class="st-stat-icon blue"><i class="bi bi-book"></i></div>
        <h3>{{ $assignmentsSent ?? 0 }}</h3>
        <small>Devoirs envoyés</small>
        <div class="st-mt-3">
          <a href="{{ route('student.assignments') }}" class="st-btn st-btn-outline st-btn-sm">Voir</a>
        </div>
      </div>
      <div class="st-stat">
        <div class="st-stat-icon green"><i class="bi bi-check-circle"></i></div>
        <h3>{{ $assignmentsCorrected ?? 0 }}</h3>
        <small>Devoirs corrigés</small>
        <div class="st-mt-3">
          <a href="{{ route('student.assignments') }}" class="st-btn st-btn-outline st-btn-sm st-btn-success">Voir</a>
        </div>
      </div>
      <div class="st-stat">
        <div class="st-stat-icon purple"><i class="bi bi-graph-up"></i></div>
        <h3>{{ number_format($average ?? 0, 1) }}/20</h3>
        <small>Moyenne</small>
      </div>
    </div>

    {{-- Metrics --}}
    <div class="st-stats st-stats-3 st-fade-up st-fade-up-d2">
      <div class="st-metric blue">
        <i class="bi bi-upload"></i>
        <h3>{{ $assignmentsSent ?? 0 }}</h3>
        <small>Devoirs envoyés</small>
      </div>
      <div class="st-metric green">
        <i class="bi bi-check-circle"></i>
        <h3>{{ $assignmentsCorrected ?? 0 }}</h3>
        <small>Devoirs corrigés</small>
      </div>
      <div class="st-metric yellow">
        <i class="bi bi-graph-up"></i>
        <h3>{{ number_format($average ?? 0, 1) }}/20</h3>
        <small>Moyenne</small>
      </div>
    </div>

    {{-- Chart --}}
    <div class="st-card mb-4 st-fade-up st-fade-up-d3">
      <div class="st-card-header">
        <i class="bi bi-bar-chart-fill" style="color: var(--st-primary);"></i>
        <h5>📊 Évolution des notes</h5>
      </div>
      <div class="st-card-body">
        <div style="height: 300px; max-width: 600px; margin: auto;">
          <canvas id="gradesChart"></canvas>
        </div>
      </div>
    </div>

    {{-- Calendar --}}
    <div class="st-card st-fade-up st-fade-up-d4">
      <div class="st-card-header">
        <i class="bi bi-calendar-event-fill" style="color: var(--st-success);"></i>
        <h5>📅 Calendrier des devoirs</h5>
      </div>
      <div class="st-card-body" style="padding: .75rem;">
        <div id="calendar" style="height: 500px;"></div>
      </div>
    </div>

  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // FullCalendar
  var calendarEl = document.getElementById('calendar');
  if (calendarEl) {
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      height: 500,
      events: [
        { title: 'Test Event', start: '2026-04-17' },
        @foreach($assignments as $a)
        { title: '{{ $a->title }}', start: '{{ $a->created_at->format('Y-m-d') }}' },
        @endforeach
      ]
    });
    calendar.render();
  }

  // Chart
  var ctx = document.getElementById('gradesChart');
  if (ctx) {
    new Chart(ctx, {
      type: 'radar',
      data: {
        labels: ['Moyenne', 'Devoirs envoyés', 'Devoirs corrigés', 'Présence', 'Engagement'],
        datasets: [{
          label: 'Performance',
          data: [
            {{ $average ?? 0 }},
            {{ $sentPercent ?? 0 }},
            {{ $correctedPercent ?? 0 }},
            {{ $presencePercent ?? 0 }},
            {{ $engagement ?? 0 }}
          ],
          backgroundColor: 'rgba(0,102,204,0.15)',
          borderColor: '#0066cc',
          borderWidth: 2,
          pointBackgroundColor: '#0066cc',
          pointBorderColor: '#fff',
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          r: {
            min: 0, max: 100,
            ticks: { stepSize: 20, backdropColor: 'transparent' },
            grid: { color: 'rgba(0,0,0,0.08)' },
            angleLines: { color: 'rgba(0,0,0,0.08)' },
            pointLabels: { color: '#2B2D42', font: { size: 13, weight: 'bold' } }
          }
        },
        plugins: {
          legend: { labels: { color: '#2B2D42', font: { size: 13 } } }
        }
      }
    });
  }
});
</script>
@endpush
@endsection
