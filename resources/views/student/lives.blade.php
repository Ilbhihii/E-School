@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-broadcast me-2"></i>Mes Lives</h1>
        <p>Accédez à vos sessions en direct avec vos enseignants</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-camera-video-fill"></i>
      </div>
    </div>

    @if($lives->count() > 0)
      <div class="row g-4 st-fade-up st-fade-up-d1">
        @foreach($lives as $live)
          @php
            $liveDate = \Carbon\Carbon::parse($live->live_date);
            $isLive = now()->greaterThanOrEqualTo($liveDate);
            $isUpcoming = now()->lt($liveDate->copy()->addHours(24));
            $isPast = now()->gt($liveDate);
          @endphp
          <div class="col-md-4">
            <div class="st-card h-100 position-relative">
              <div class="position-absolute top-0 end-0 p-2" style="z-index: 2;">
                <span class="st-badge {{ $isLive ? 'st-badge-danger' : ($isUpcoming ? 'st-badge-warning' : 'st-badge-gray') }}" style="{{ $isLive ? 'animation: pulseLive 1.5s infinite;' : '' }}">
                  {{ $isLive ? '🔴 En direct' : ($isUpcoming ? '⏳ Bientôt' : '✅ Terminé') }}
                </span>
              </div>
              <div style="padding: 1.25rem 1.5rem; display: flex; flex-direction: column; height: 100%;">
                <h5 style="font-weight: 700; margin-bottom: .5rem; color: var(--st-primary);">{{ $live->title }}</h5>
                <p style="font-size: 13px; color: var(--st-text-light); flex: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                  {{ $live->description }}
                </p>
                <div class="st-flex st-flex-between st-mt-2" style="padding-top: .75rem; border-top: 1px solid var(--st-border);">
                  <small style="color: var(--st-text-light);">
                    {{ $isLive ? 'En cours depuis ' . now()->diffForHumans($liveDate, true) : ($isPast ? $liveDate->format('d/m/Y H:i') . ' (terminé)' : 'Début le ' . $liveDate->format('d/m/Y H:i')) }}
                  </small>
                  @if($isLive || $isUpcoming)
                    <a href="{{ $live->stream_url }}" target="_blank" class="st-btn {{ $isLive ? 'st-btn-danger' : 'st-btn-outline' }} st-btn-sm" {{ !$isLive ? 'disabled' : '' }}>
                      {{ $isLive ? 'Rejoindre' : 'Bientôt' }}
                    </a>
                  @else
                    <span class="st-badge st-badge-gray">Terminé</span>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Calendar --}}
      <div class="st-card st-mt-4 st-fade-up st-fade-up-d3">
        <div class="st-card-header">
          <i class="bi bi-calendar-event-fill" style="color: var(--st-success);"></i>
          <h5>📅 Calendrier des Lives</h5>
        </div>
        <div class="st-card-body" style="padding: .75rem;">
          <div id="calendar" class="st-calendar" style="height: 500px;"></div>
        </div>
      </div>
    @else
      <div class="st-card st-fade-up st-fade-up-d1">
        <div class="st-empty">
          <i class="bi bi-camera-video-off"></i>
          <h5>Aucun live disponible</h5>
          <p>Les sessions apparaîtront ici dès qu'elles seront programmées</p>
          <button class="st-btn st-btn-primary"><i class="bi bi-bell me-1"></i>Activer les notifications</button>
          <div class="st-mt-3">
            <a href="{{ route('student.settings') }}" class="st-btn st-btn-ghost">Gérer les paramètres</a>
          </div>
        </div>
      </div>
    @endif

  </div>
</div>

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
          { title: "{{ $live->title }}", start: "{{ $live->live_date }}", url: "{{ $live->stream_url }}" },
        @else
          { title: "{{ $live->title }}", daysOfWeek: [{{ $live->day_of_week }}], startTime: "{{ $live->start_time }}", endTime: "{{ $live->end_time }}", url: "{{ $live->stream_url }}" },
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
@endsection
