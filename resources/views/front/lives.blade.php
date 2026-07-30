@extends('layouts.front')

@section('title', 'Lives en direct')

@section('content')

@php
    $statusOrder = [
        'live' => 1,
        'upcoming' => 2,
        'unscheduled' => 3,
        'ended' => 4,
    ];

    $orderedLives = $lives
        ->sortBy(function ($live) use ($statusOrder) {
            $statusPosition =
                $statusOrder[$live->schedule_status] ?? 9;

            $datePosition = $live->start_date_time
                ? $live->start_date_time->timestamp
                : PHP_INT_MAX;

            if ($live->schedule_status === 'ended') {
                $datePosition *= -1;
            }

            return sprintf(
                '%02d-%020d',
                $statusPosition,
                $datePosition
            );
        })
        ->values();

    $liveCount = $orderedLives
        ->where('schedule_status', 'live')
        ->count();

    $upcomingCount = $orderedLives
        ->where('schedule_status', 'upcoming')
        ->count();

    $endedCount = $orderedLives
        ->where('schedule_status', 'ended')
        ->count();

    $hasLiveNow = $liveCount > 0;
@endphp

<!-- ══════════════════════════════════════════════════════
     HERO SECTION
     ══════════════════════════════════════════════════════ -->
<section class="py-5 text-center text-white position-relative overflow-hidden"
         style="background: linear-gradient(135deg, #0a1628 0%, #1a0a2e 40%, #0a1628 100%);">
    <!-- Animated gradient overlay -->
    <div style="position:absolute;inset:0;background:radial-gradient(circle at 30% 50%, rgba(220,38,38,0.08), transparent 60%),
                radial-gradient(circle at 70% 50%, rgba(124,58,237,0.08), transparent 60%);pointer-events:none;"></div>
    <div class="container position-relative" style="z-index:2;">
        <div
            class="live-hero-status-pill
                {{ $hasLiveNow ? 'is-live' : 'is-scheduled' }}"
        >
            <span class="live-hero-status-badge">
                <span
                    class="live-red-lamp"
                    aria-hidden="true"
                ></span>

                {{ $hasLiveNow
                    ? 'EN DIRECT'
                    : 'SESSIONS LIVE' }}
            </span>

            <span>
                {{ $hasLiveNow
                    ? 'Sessions interactives en temps réel'
                    : 'Cours interactifs programmés' }}
            </span>
        </div>
        <h1 class="fw-bold mb-3" style="font-family: 'Poppins', sans-serif; font-size: 3rem; text-shadow: 0 0 40px rgba(220,38,38,0.2);">
            Lives <span style="background: linear-gradient(135deg, #DC2626, #EF4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">en direct</span>
        </h1>
        <p class="text-white-50 mb-0" style="font-size: 1.15rem; max-width: 550px; margin: 0 auto;">
            Participez aux sessions interactives avec vos enseignants et progressez en temps réel.
        </p>
    </div>
</section>

<!-- SECURITY CHECK -->
@auth

<!-- ══════════════════════════════════════════════════════
     LIVES GRID
     ══════════════════════════════════════════════════════ -->
<section class="py-5">
    <div class="container">

        @if($orderedLives->count() > 0)
        <!-- STATS ROW -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="reveal-3d" style="background: linear-gradient(135deg, rgba(220,38,38,0.1), rgba(220,38,38,0.02)); border: 1px solid rgba(220,38,38,0.1); border-radius: 16px; padding: 1.25rem; text-align: center;">
                    <span style="font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, #DC2626, #EF4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        {{ $liveCount }}
                    </span>
                    <div style="color: rgba(255,255,255,0.4); font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">En direct</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="reveal-3d" style="background: linear-gradient(135deg, rgba(2,132,199,0.1), rgba(2,132,199,0.02)); border: 1px solid rgba(2,132,199,0.1); border-radius: 16px; padding: 1.25rem; text-align: center; transition-delay: 0.1s;">
                    <span style="font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, #0284C7, #38BDF8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        {{ $upcomingCount }}
                    </span>
                    <div style="color: rgba(255,255,255,0.4); font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">À venir</div>
                </div>
            </div>
            <div class="col-md-4">
                <div
                    class="reveal-3d"
                    style="
                        background:
                            linear-gradient(
                                135deg,
                                rgba(100,116,139,0.1),
                                rgba(100,116,139,0.02)
                            );
                        border:
                            1px solid rgba(100,116,139,0.12);
                        border-radius:16px;
                        padding:1.25rem;
                        text-align:center;
                        transition-delay:0.2s;
                    "
                >
                    <span
                        style="
                            font-size:1.8rem;
                            font-weight:800;
                            background:
                                linear-gradient(
                                    135deg,
                                    #64748B,
                                    #CBD5E1
                                );
                            -webkit-background-clip:text;
                            -webkit-text-fill-color:transparent;
                            background-clip:text;
                        "
                    >
                        {{ $endedCount }}
                    </span>

                    <div
                        style="
                            color:rgba(255,255,255,0.4);
                            font-size:0.8rem;
                            letter-spacing:0.05em;
                            text-transform:uppercase;
                        "
                    >
                        Terminées
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4">

            @forelse($orderedLives as $live)
            @php
                $startDateTime = $live->start_date_time;
                $endDateTime = $live->end_date_time;

                $liveDate = $startDateTime;
                $startTime = $startDateTime
                    ? $startDateTime->format('H:i')
                    : null;

                $endTime = $endDateTime
                    ? $endDateTime->format('H:i')
                    : null;

                $isLive = $live->is_live;
                $isUpcoming = $live->is_upcoming;
                $isEnded = $live->is_ended;
                $status = $live->schedule_status;
                $statusText = $live->status_label;

                if ($isLive) {
                    $themeColor = '#DC2626';
                    $themeGradient =
                        'linear-gradient(135deg,#DC2626,#EF4444)';
                    $statusBackground = 'rgba(220,38,38,0.2)';
                    $statusBorder = 'rgba(220,38,38,0.25)';
                    $statusColor = '#F87171';
                } elseif ($isUpcoming) {
                    $themeColor = '#0284C7';
                    $themeGradient =
                        'linear-gradient(135deg,#0284C7,#38BDF8)';
                    $statusBackground = 'rgba(2,132,199,0.15)';
                    $statusBorder = 'rgba(2,132,199,0.22)';
                    $statusColor = '#38BDF8';
                } elseif ($isEnded) {
                    $themeColor = '#64748B';
                    $themeGradient =
                        'linear-gradient(135deg,#475569,#64748B)';
                    $statusBackground = 'rgba(71,85,105,0.2)';
                    $statusBorder = 'rgba(100,116,139,0.24)';
                    $statusColor = '#CBD5E1';
                } else {
                    $themeColor = '#D97706';
                    $themeGradient =
                        'linear-gradient(135deg,#D97706,#F59E0B)';
                    $statusBackground = 'rgba(217,119,6,0.16)';
                    $statusBorder = 'rgba(245,158,11,0.22)';
                    $statusColor = '#FBBF24';
                }

                $startDt = $startDateTime
                    ? $startDateTime
                        ->copy()
                        ->utc()
                        ->format('Y-m-d\TH:i:s\Z')
                    : null;

                $endDt = $endDateTime
                    ? $endDateTime
                        ->copy()
                        ->utc()
                        ->format('Y-m-d\TH:i:s\Z')
                    : null;

                $outlookUrl =
                    'https://outlook.live.com/calendar/0/'
                    . 'deeplink/compose?path=/calendar/action/'
                    . 'compose&rru=addevent';

                $outlookUrl .=
                    '&subject=' . urlencode($live->title);

                if ($startDt && $endDt) {
                    $outlookUrl .=
                        '&startdt=' . $startDt;

                    $outlookUrl .=
                        '&enddt=' . $endDt;
                }

                $outlookUrl .=
                    '&body='
                    . urlencode(
                        ($live->description
                            ?? 'Session en direct')
                        . "\n\nLien : "
                        . ($live->stream_url ?? '')
                    );

                $outlookUrl .=
                    '&location='
                    . urlencode($live->stream_url ?? '');
            @endphp
            <div
                class="col-md-6 col-lg-4"
                data-live-boundary
                data-start-at="{{
                    $startDateTime
                        ? $startDateTime->toIso8601String()
                        : ''
                }}"
                data-end-at="{{
                    $endDateTime
                        ? $endDateTime->toIso8601String()
                        : ''
                }}"
            >
                <div class="card-3d text-center h-100 reveal-3d" style="padding: 0; overflow: hidden;">

                    <!-- Top colored banner -->
                    <div style="height: 6px; background: {{ $themeGradient }}; width: 100%;"></div>

                    <!-- Status badge -->
                    <div style="position: absolute; top: 16px; right: 16px; z-index: 2;">
                        <span style="display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.03em;
                            background: {{ $statusBackground }};
                            color: {{ $statusColor }};
                            border: 1px solid {{ $statusBorder }};
                            {{ $isLive ? 'animation: livePulseBadge 2s ease-in-out infinite;' : '' }}">
                            {{ $statusText }}
                        </span>
                    </div>

                    <!-- Icon area -->
                    <div style="padding: 2rem 1.5rem 1rem;">
                        <div style="width: 72px; height: 72px; border-radius: 20px; margin: 0 auto 1rem;
                            background: {{ $themeGradient }};
                            display: flex; align-items: center; justify-content: center;
                            box-shadow:
                                0 8px 30px
                                {{ $isLive
                                    ? 'rgba(220,38,38,0.25)'
                                    : (
                                        $isUpcoming
                                            ? 'rgba(2,132,199,0.2)'
                                            : 'rgba(71,85,105,0.15)'
                                    ) }};
                            transition: transform 0.3s ease;">
                            <i class="bi bi-camera-video-fill" style="font-size: 1.6rem; color: white;"></i>
                        </div>

                        <h5 class="fw-bold text-white mb-2" style="font-family: 'Poppins', sans-serif; font-size: 1rem;">
                            {{ $live->title }}
                        </h5>

                        @if($startDateTime)
                            <div
                                style="
                                    color:rgba(255,255,255,0.4);
                                    font-size:0.78rem;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    flex-wrap:wrap;
                                    gap:4px;
                                    margin-bottom:0.5rem;
                                "
                            >
                                <i
                                    class="bi bi-calendar-event"
                                    style="
                                        color:{{ $themeColor }};
                                        font-size:0.7rem;
                                    "
                                ></i>

                                {{ $startDateTime->format('d/m/Y') }}

                                <span
                                    style="
                                        color:rgba(255,255,255,0.3);
                                    "
                                >
                                    •
                                </span>

                                <i
                                    class="bi bi-clock"
                                    style="
                                        color:{{ $themeColor }};
                                        font-size:0.7rem;
                                    "
                                ></i>

                                {{ $startTime }}

                                @if($endTime)
                                    – {{ $endTime }}
                                @endif
                            </div>
                        @else
                            <div
                                style="
                                    color:#FBBF24;
                                    font-size:0.78rem;
                                    margin-bottom:0.5rem;
                                "
                            >
                                <i class="bi bi-calendar-question"></i>
                                Date à confirmer
                            </div>
                        @endif

                        <p class="text-white-50 small mb-0" style="line-height: 1.6; font-size: 0.8rem;">
                            {{ \Illuminate\Support\Str::limit($live->description ?? 'Session en direct', 80) }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div style="padding: 0 1.5rem 1.5rem;">
                        @if($isEnded)
                            <div
                                style="
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    gap:8px;
                                    width:100%;
                                    padding:11px 20px;
                                    border-radius:12px;
                                    background:rgba(71,85,105,0.16);
                                    border:
                                        1px solid rgba(100,116,139,0.22);
                                    color:#CBD5E1;
                                    font-size:0.86rem;
                                    font-weight:700;
                                "
                            >
                                <i class="bi bi-check-circle-fill"></i>
                                Session terminée
                            </div>
                        @elseif($live->stream_url)
                            <a
                                href="{{ $live->stream_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                style="
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    gap:8px;
                                    width:100%;
                                    padding:11px 20px;
                                    border-radius:12px;
                                    font-weight:600;
                                    font-size:0.9rem;
                                    background:{{ $themeGradient }};
                                    color:white;
                                    text-decoration:none;
                                    transition:all 0.3s ease;
                                    border:none;
                                "
                            >
                                <i class="bi bi-play-circle-fill"></i>

                                {{ $isLive
                                    ? 'Rejoindre le live'
                                    : 'Ouvrir le lien de la session' }}
                            </a>

                            @if($isUpcoming && $startDt && $endDt)
                                <a
                                    href="{{ $outlookUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    style="
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        gap:6px;
                                        width:100%;
                                        padding:9px 20px;
                                        border-radius:12px;
                                        font-weight:500;
                                        font-size:0.8rem;
                                        background:
                                            rgba(255,255,255,0.04);
                                        color:
                                            rgba(255,255,255,0.5);
                                        text-decoration:none;
                                        border:
                                            1px solid
                                            rgba(255,255,255,0.06);
                                        margin-top:8px;
                                    "
                                >
                                    <i
                                        class="bi bi-calendar-plus"
                                        style="color:{{ $themeColor }};"
                                    ></i>
                                    Ajouter à Outlook
                                </a>
                            @endif
                        @else
                            <div
                                style="
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    gap:8px;
                                    width:100%;
                                    padding:11px 20px;
                                    border-radius:12px;
                                    background:rgba(255,255,255,0.04);
                                    border:
                                        1px dashed
                                        rgba(255,255,255,0.08);
                                    color:rgba(255,255,255,0.3);
                                    font-size:0.85rem;
                                "
                            >
                                <i class="bi bi-hourglass-split"></i>
                                Lien à venir
                            </div>
                        @endif
                    </div>

                </div>
            </div>
            @empty

            <div class="col-12">
                <div class="text-center py-5">
                    <div style="width: 80px; height: 80px; border-radius: 20px; background: rgba(255,255,255,0.03); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; border: 1px solid rgba(255,255,255,0.04);">
                        <i class="bi bi-camera-video-off" style="font-size: 2rem; color: rgba(255,255,255,0.15);"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2" style="opacity: 0.5;">Aucun live disponible</h5>
                    <p class="text-white-50 small mb-0">Revenez bientôt pour découvrir les prochaines sessions.</p>
                </div>
            </div>

            @endforelse

        </div>
    </div>
</section>

@else

<!-- ══════════════════════════════════════════════════════
     NOT LOGGED IN
     ══════════════════════════════════════════════════════ -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card-3d text-center p-5 reveal-3d">
                    <div style="width: 90px; height: 90px; border-radius: 24px; margin: 0 auto 1.25rem;
                        background: linear-gradient(135deg, rgba(220,38,38,0.15), rgba(124,58,237,0.1));
                        display: flex; align-items: center; justify-content: center;
                        border: 1px solid rgba(220,38,38,0.08);">
                        <i class="bi bi-camera-video" style="font-size: 2.2rem; color: #EF4444;"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-3" style="font-family: 'Poppins', sans-serif;">
                        Connectez-vous pour accéder aux Lives
                    </h3>
                    <p class="text-white-50 mb-4" style="max-width: 420px; margin: 0 auto; line-height: 1.7;">
                        Les sessions lives sont accessibles uniquement aux étudiants inscrits.
                        Créez votre compte ou connectez-vous pour rejoindre les cours en direct.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('login') }}" class="btn-3d btn-3d-gradient" style="padding: 13px 32px; font-size: 0.95rem;">
                            <i class="bi bi-person me-1"></i> Se connecter
                        </a>
                        <a href="{{ route('register') }}" class="btn-3d btn-3d-outline" style="padding: 13px 32px; font-size: 0.95rem;">
                            <i class="bi bi-person-plus me-1"></i> S'inscrire gratuitement
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endauth

<!-- STYLE -->
<style>
.live-hero-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 1.15rem;
    padding: 5px 11px;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 999px;
    background: rgba(255,255,255,0.035);
    color: rgba(255,255,255,0.58);
    font-size: 0.72rem;
    line-height: 1;
}

.live-hero-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 999px;
    font-size: 0.57rem;
    font-weight: 800;
    letter-spacing: 0.045em;
    text-transform: uppercase;
}

.live-hero-status-pill.is-live
.live-hero-status-badge,
.live-hero-status-pill.is-scheduled
.live-hero-status-badge {
    background:
        linear-gradient(135deg,#B91C1C,#EF4444);
    color: #ffffff;
    border: 1px solid rgba(248,113,113,0.34);
    box-shadow: 0 5px 18px rgba(220,38,38,0.24);
}

.live-hero-status-pill.is-live
.live-hero-status-badge {
    animation:
        livePulseBadge 2s ease-in-out infinite;
}

.live-red-lamp {
    position: relative;
    width: 8px;
    height: 8px;
    flex: 0 0 8px;
    display: inline-block;
    border-radius: 50%;
    background: #FCA5A5;
    box-shadow:
        0 0 0 3px rgba(248,113,113,0.16),
        0 0 10px rgba(248,113,113,0.95);
}

.live-red-lamp::after {
    content: '';
    position: absolute;
    inset: -4px;
    border: 1px solid rgba(252,165,165,0.55);
    border-radius: 50%;
    animation: liveLampPulse 1.5s ease-out infinite;
}

@keyframes liveLampPulse {
    0% {
        opacity: 0.85;
        transform: scale(0.7);
    }

    70% {
        opacity: 0;
        transform: scale(1.8);
    }

    100% {
        opacity: 0;
        transform: scale(1.8);
    }
}

.card-3d:hover .bi-camera-video-fill {
    transform: scale(1.1);
}

.pulse-badge {
    animation: livePulseBadge 2s ease-in-out infinite;
}

@keyframes livePulseBadge {
    0%, 100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4); }
    50% { box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); }
}

/* Stat cards hover */
.reveal-3d:hover {
    transform: translateY(-3px);
    transition: transform 0.3s ease;
}

@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 2rem !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const now = Date.now();

    const boundaries = Array.from(
        document.querySelectorAll('[data-live-boundary]')
    )
        .flatMap(element => [
            element.dataset.startAt,
            element.dataset.endAt,
        ])
        .filter(Boolean)
        .map(value => Date.parse(value))
        .filter(value =>
            Number.isFinite(value)
            && value > now
        )
        .sort((first, second) => first - second);

    if (!boundaries.length) {
        return;
    }

    const nextBoundary = boundaries[0];

    const maximumTimeout = 2147480000;

    const delay = Math.min(
        Math.max(1500, nextBoundary - now + 1500),
        maximumTimeout
    );

    window.setTimeout(() => {
        window.location.reload();
    }, delay);
});
</script>

@endsection