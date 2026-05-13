@extends('layouts.student')

@section('content')
@push('styles')
<style>
:root {
    --blue-main: #003A8F;
    --blue-light: #4DA3FF;
    --red-accent: #D90429;
    --yellow-soft: #FFD166;
    --gray-dark: #2B2D42;
    --gray-light: #F5F7FA;
    --white: #FFFFFF;
}

.edu-header {
    background: var(--blue-main);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.live-card {
    background: var(--white);
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    transition: 0.3s;
}

.live-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}

.live-badge {
    font-size: 12px;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 20px;
}

.bg-danger {
    background: var(--red-accent) !important;
}

.bg-warning {
    background: var(--yellow-soft) !important;
}

.btn-primary {
    background: var(--blue-main);
    border: none;
    border-radius: 30px;
}

.btn-primary:hover {
    background: var(--blue-light);
}

.calendar-modern {
    background: var(--white);
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 10px;
}

body {
    background: var(--gray-light);
    color: var(--gray-dark);
}

.live-now {
    animation: pulse 2s infinite;
    box-shadow: 0 0 20px rgba(217, 4, 41, 0.3);
}

@keyframes pulse {
    0% { box-shadow: 0 0 20px rgba(217, 4, 41, 0.3); }
    50% { box-shadow: 0 0 30px rgba(217, 4, 41, 0.6); }
    100% { box-shadow: 0 0 20px rgba(217, 4, 41, 0.3); }
}

.btn-live-pulse {
    animation: pulse-btn 1.5s infinite;
}

@keyframes pulse-btn {
    0% { box-shadow: 0 0 0 0 rgba(0, 58, 143, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(0, 58, 143, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 58, 143, 0); }
}
</style>
@endpush


<div class="container py-5">




<div class="edu-header mb-5 p-5 text-center text-primary rounded-4">
    <h1 class="fw-bold mb-3">
        <i class="bi bi-broadcast me-2"></i> Mes Lives
    </h1>
    <p class="mb-0 ">
        Accédez à vos sessions en direct avec vos enseignants
    </p>
</div>



@if($lives->count() > 0)

    <div class="row g-4">
        @foreach($lives as $live)
            <div class="col-md-4 col-12 mb-4">
                <div class="live-card h-100 position-relative overflow-hidden hover-lift">
                    <div class="position-absolute top-0 end-0 p-2">
@php
    $liveDate = \Carbon\Carbon::parse($live->live_date);
    $isLive = now()->greaterThanOrEqualTo($liveDate);
    $isUpcoming = now()->lt($liveDate->copy()->addHours(24)); // Within next 24h
    $isPast = now()->gt($liveDate);
@endphp
<span class="live-badge {{ $isLive ? 'bg-danger live-now' : ($isUpcoming ? 'bg-warning text-dark' : 'bg-secondary') }}" aria-label="Status: {{ $isLive ? 'En direct' : ($isUpcoming ? 'Bientôt' : 'Terminé') }}">
    {{ $isLive ? '🔴 En direct' : ($isUpcoming ? '⏳ Bientôt' : '✅ Terminé') }}
</span>
                    </div>
                    <div class="p-4 h-100 d-flex flex-column">
                        <h5 class="title-gradient mb-3 text-primary">{{ $live->title }}</h5>
                        <p class="text-muted flex-grow-1 mb-3 lh-sm" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ $live->description }}</p>
                        
                            <div class="d-flex justify-content-between align-items-end flex-column flex-md-row mt-auto gap-2 pt-3 border-top border-primary-subtle">
                            <div class="text-start">
<small class="text-muted d-block mb-1">
    {{ $isLive ? 'En cours depuis ' . now()->diffForHumans($liveDate, true) : ($isPast ? $liveDate->format('d/m/Y H:i') . ' (terminé)' : 'Début le ' . $liveDate->format('d/m/Y H:i') ) }}
</small>
<small class="text-primary fw-bold">{{ $isLive ? '🔴 En direct' : ($isUpcoming ? '⏳ Bientôt' : '✅ Terminé') }}</small>
                            </div>
@if($isLive || $isUpcoming)
<a href="{{ $live->stream_url }}" target="_blank" class="btn {{ $isLive ? 'btn-live-pulse live-now' : 'btn-outline-primary' }} position-relative overflow-hidden flex-shrink-0 ms-md-auto" {{ !$isLive ? 'disabled' : '' }}>
    {{ $isLive ? 'Rejoindre le Live' : 'Rejoindre bientôt' }}
    <i class="bi bi-arrow-right ms-1"></i>
</a>
@else
<button class="btn btn-secondary position-relative overflow-hidden flex-shrink-0 ms-md-auto" disabled>
    Live terminé
    <i class="bi bi-check-circle ms-1"></i>
</button>
@endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body">
            <h5 class="fw-bold mb-4 text-success">
                📅 Calendrier des Lives
            </h5>
            <div id="calendar" class="calendar-modern" style="height:500px;"></div>
        </div>
    </div>



@else



    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
        <i class="bi bi-camera-video-off display-1 text-muted mb-3"></i>
        <h4 class="fw-bold mb-2">Aucun live disponible</h4>
        <p class="text-muted mb-4">
            Les sessions apparaîtront ici dès qu'elles seront programmées
        </p>
        <button class="btn btn-primary px-4">
            <i class="bi bi-bell me-2"></i>Activer les notifications
        </button>
        <a href="{{ route('student.settings') }}" class="btn btn-outline-primary mt-2">Gérer les paramètres</a>
    </div>



@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',

        events: [
        @foreach($lives as $live)

        @if($live->live_date)

        {
            title: "{{ $live->title }}",
            start: "{{ $live->live_date }}",
            url: "{{ $live->stream_url }}",
        },

        @else

        {
            title: "{{ $live->title }}",
            daysOfWeek: [{{ $live->day_of_week }}],
            startTime: "{{ $live->start_time }}",
            endTime: "{{ $live->end_time }}",
            url: "{{ $live->stream_url }}",
        },

        @endif

        @endforeach
        ],



        eventClick: function(info) {
            info.jsEvent.preventDefault();
            window.open(info.event.url, "_blank");
        }
    });

    calendar.render();
});
</script>
@endpush


</div>

@endsection
