@extends('layouts.student')
@section('title', 'Lives')
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
        <h4><i class="bi bi-calendar-week" style="color:#64748B;"></i> Calendrier des Lives</h4>
    </div>
    <div class="pr-card-body">
        <div id="calendar" style="height:350px;"></div>
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
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('calendar');
    if (!el) return;
    new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        locale: 'fr', height: 350,
        events: [@foreach($lives as $live) @if($live->live_date){
            title: "{{ $live->title }}", start: "{{ $live->live_date }}",
            url: "{{ $live->stream_url }}",
            color: '{{ now()->gt(\Carbon\Carbon::parse($live->live_date)) ? "#475569" : (now()->lt(\Carbon\Carbon::parse($live->live_date)->subHours(2)) ? "#0284C7" : "#DC2626") }}'
        },@endif @endforeach],
        eventClick: function(i) { i.jsEvent.preventDefault(); if (i.event.url) window.open(i.event.url, '_blank'); }
    }).render();
});
</script>
<style>
.fc { color: #94A3B8; font-size: 0.72rem; }
.fc-toolbar-title { color: #F1F5F9 !important; font-size: 0.9rem !important; font-weight: 700; }
.fc-button-primary { background: #4F46E5 !important; border: none !important; }
</style>
@endpush

@endsection
