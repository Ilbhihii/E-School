@extends('layouts.front')

@section('content')
<div class="front-page">

    <section class="front-hero front-hero-xs">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-badge"><i class="bi bi-broadcast"></i> Sessions Live</div>
            <h1 class="hero-title">Cours en <span class="text-gradient">direct</span></h1>
            <p class="hero-subtitle">Participez à nos sessions live interactives avec des professeurs en temps réel.</p>
        </div>
    </section>

    <section class="front-section">
        <div class="container">

            {{-- Live Now Section --}}
            @if($lives->count())
                <div class="section-label">
                    <span class="section-label-dot section-label-dot-live"></span>
                    En direct maintenant
                    <span class="section-label-count">{{ $lives->count() }}</span>
                </div>
                <div class="lives-grid">
                    @foreach($lives as $live)
                        <div class="live-card @if($live->is_live ?? false) live-card-active @endif">
                            <div class="live-card-header">
                                @if($live->is_live ?? false)
                                    <span class="live-badge live-badge-live">
                                        <span class="live-badge-dot"></span>
                                        EN DIRECT
                                    </span>
                                @else
                                    <span class="live-badge live-badge-scheduled">
                                        <i class="bi bi-calendar"></i>
                                        Planifié
                                    </span>
                                @endif
                                @php
                                    $liveColors = ['#2563eb','#059669','#7c3aed','#d97706','#dc2626','#0891b2'];
                                    $lc = $liveColors[$loop->index % count($liveColors)];
                                @endphp
                                <div class="live-card-icon" style="background: {{ $lc }};">
                                    <i class="bi bi-broadcast"></i>
                                </div>
                            </div>
                            <div class="live-card-body">
                                <h3 class="live-card-title">{{ $live->title ?? $live->name ?? 'Session Live' }}</h3>
                                <p class="live-card-desc">{{ Str::limit($live->description ?? 'Rejoignez cette session interactive.', 80) }}</p>
                                <div class="live-card-meta">
                                    @if(isset($live->subject))
                                        <span class="live-tag"><i class="bi bi-book"></i> {{ $live->subject->name ?? '' }}</span>
                                    @endif
                                    @if(isset($live->teacher))
                                        <span class="live-tag"><i class="bi bi-person"></i> {{ $live->teacher->name ?? '' }}</span>
                                    @endif
                                    @if(isset($live->scheduled_at))
                                        <span class="live-tag"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($live->scheduled_at)->format('d/m/Y H:i') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="live-card-footer">
                                @if($live->is_live ?? false)
                                    @if(isset($live->meeting_url) || isset($live->zoom_url) || isset($live->url))
                                        <a href="{{ $live->meeting_url ?? $live->zoom_url ?? $live->url ?? '#' }}" class="btn-live btn-live-primary" target="_blank">
                                            <i class="bi bi-camera-video-fill"></i> Rejoindre le live
                                        </a>
                                    @else
                                        <span class="btn-live btn-live-disabled">
                                            <i class="bi bi-camera-video-off"></i> Lien non disponible
                                        </span>
                                    @endif
                                @else
                                    <span class="btn-live btn-live-ghost">
                                        <i class="bi bi-alarm"></i> À venir
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="front-empty">
                    <div class="front-empty-icon"><i class="bi bi-broadcast"></i></div>
                    <h3>Aucune session live</h3>
                    <p>Aucune session live n'est programmée pour le moment. Revenez bientôt.</p>
                </div>
            @endif
        </div>
    </section>

</div>

<style>
.section-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 1.25rem;
    padding-bottom: .75rem;
    border-bottom: 1px solid #e2e8f0;
}

.section-label-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
}

.section-label-dot-live {
    background: #dc2626;
    animation: pulse-dot 1.5s infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: .4; }
}

.section-label-count {
    margin-left: auto;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 2px 10px;
    border-radius: 999px;
}

.lives-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.25rem; }

.live-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 2px 16px rgba(15,23,42,.06);
    border: 1px solid rgba(226,232,240,.6);
    overflow: hidden;
    transition: all .35s cubic-bezier(.22,.61,.36,1);
}

.live-card:hover { transform: translateY(-5px); box-shadow: 0 16px 48px rgba(15,23,42,.14); border-color: transparent; }

.live-card-active {
    border-color: rgba(220,38,38,.15);
    box-shadow: 0 4px 24px rgba(220,38,38,.08);
}

.live-card-header {
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid #f1f5f9;
}

.live-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: .03em;
}

.live-badge-live { color: #dc2626; background: #fef2f2; }
.live-badge-scheduled { color: #d97706; background: #fffbeb; }

.live-badge-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #dc2626;
    animation: pulse-dot 1.5s infinite;
}

.live-card-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 18px;
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
}

.live-card-body { padding: 1.25rem 1.5rem; }

.live-card-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .4rem;
}

.live-card-desc {
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
    margin: 0 0 .75rem;
}

.live-card-meta { display: flex; flex-wrap: wrap; gap: 5px; }

.live-tag {
    font-size: 11.5px;
    font-weight: 500;
    color: #64748b;
    background: #f8fafc;
    padding: 3px 10px;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.live-tag i { font-size: 10px; }

.live-card-footer { padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; }

.btn-live {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: .7rem 1rem;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all .25s ease;
    border: none;
    cursor: pointer;
}

.btn-live-primary {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    box-shadow: 0 4px 16px rgba(220,38,38,.3);
}

.btn-live-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(220,38,38,.4);
}

.btn-live-disabled {
    background: #f1f5f9;
    color: #94a3b8;
    cursor: not-allowed;
}

.btn-live-ghost {
    background: transparent;
    color: #64748b;
    border: 1px solid #e2e8f0;
}

@media(max-width:768px) {
    .lives-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
