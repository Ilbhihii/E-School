@extends('layouts.front')

@section('title', 'Lives en direct')

@section('content')

<!-- ══════════════════════════════════════════════════════
     HERO SECTION
     ══════════════════════════════════════════════════════ -->
<section class="py-5 text-center text-white position-relative overflow-hidden"
         style="background: linear-gradient(135deg, #0a1628 0%, #1a0a2e 40%, #0a1628 100%);">
    <!-- Animated gradient overlay -->
    <div style="position:absolute;inset:0;background:radial-gradient(circle at 30% 50%, rgba(220,38,38,0.08), transparent 60%),
                radial-gradient(circle at 70% 50%, rgba(124,58,237,0.08), transparent 60%);pointer-events:none;"></div>
    <div class="container position-relative" style="z-index:2;">
        <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill mb-4"
             style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); font-size: 0.85rem; color: rgba(255,255,255,0.6);">
            <span class="badge" style="background: linear-gradient(135deg, #DC2626, #EF4444); font-size: 0.65rem; padding: 3px 10px; border-radius: 20px; letter-spacing: 0.05em; text-transform: uppercase; animation: livePulseBadge 2s ease-in-out infinite;">
                🔴 EN DIRECT
            </span>
            Sessions interactives en temps réel
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

        @if($lives->count() > 0)
        <!-- STATS ROW -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="reveal-3d" style="background: linear-gradient(135deg, rgba(220,38,38,0.1), rgba(220,38,38,0.02)); border: 1px solid rgba(220,38,38,0.1); border-radius: 16px; padding: 1.25rem; text-align: center;">
                    <span style="font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, #DC2626, #EF4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        {{ $lives->filter(fn($l) => $l->live_date && now()->gte($l->live_date) && now()->lt(\Carbon\Carbon::parse($l->live_date)->copy()->addHours(2)))->count() }}
                    </span>
                    <div style="color: rgba(255,255,255,0.4); font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">En direct</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="reveal-3d" style="background: linear-gradient(135deg, rgba(2,132,199,0.1), rgba(2,132,199,0.02)); border: 1px solid rgba(2,132,199,0.1); border-radius: 16px; padding: 1.25rem; text-align: center; transition-delay: 0.1s;">
                    <span style="font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, #0284C7, #38BDF8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        {{ $lives->filter(fn($l) => $l->live_date && now()->lt($l->live_date))->count() }}
                    </span>
                    <div style="color: rgba(255,255,255,0.4); font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">À venir</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="reveal-3d" style="background: linear-gradient(135deg, rgba(74,222,128,0.1), rgba(74,222,128,0.02)); border: 1px solid rgba(74,222,128,0.1); border-radius: 16px; padding: 1.25rem; text-align: center; transition-delay: 0.2s;">
                    <span style="font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, #16A34A, #4ADE80); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        {{ $lives->count() }}
                    </span>
                    <div style="color: rgba(255,255,255,0.4); font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">Total sessions</div>
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4">

            @forelse($lives as $live)
            @php
                $liveDate = $live->live_date ? \Carbon\Carbon::parse($live->live_date) : null;
                $startTime = $live->start_time ? $live->start_time : ($liveDate ? $liveDate->format('H:i') : '00:00');
                $endTime = $live->end_time ? $live->end_time : date('H:i', strtotime($startTime . ' +1 hour'));
                $startDt = $liveDate ? $liveDate->format('Ymd\THis') : now()->format('Ymd\THis');

                $endDt = $liveDate
                    ? \Carbon\Carbon::parse($liveDate->format('Y-m-d') . ' ' . $endTime)->format('Ymd\THis')
                    : now()->addHour()->format('Ymd\THis');

                $isLive = $liveDate && now()->gte($liveDate) && now()->lt($liveDate->copy()->addHours(2));
                $isUpcoming = $liveDate && now()->lt($liveDate);

                $outlookUrl = 'https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent';
                $outlookUrl .= '&subject=' . urlencode($live->title);
                $outlookUrl .= '&startdt=' . $startDt;
                $outlookUrl .= '&enddt=' . $endDt;
                $outlookUrl .= '&body=' . urlencode(($live->description ?? 'Session en direct') . "\n\nLien : " . ($live->stream_url ?? ''));
                $outlookUrl .= '&location=' . urlencode($live->stream_url ?? '');

                // Définir la couleur du thème selon le statut
                if ($isLive) {
                    $themeColor = '#DC2626';
                    $themeGradient = 'linear-gradient(135deg, #DC2626, #EF4444)';
                    $statusText = '🔴 EN DIRECT';
                } elseif ($isUpcoming) {
                    $themeColor = '#0284C7';
                    $themeGradient = 'linear-gradient(135deg, #0284C7, #38BDF8)';
                    $statusText = '⏳ À VENIR';
                } else {
                    $themeColor = '#475569';
                    $themeGradient = 'linear-gradient(135deg, #475569, #64748B)';
                    $statusText = '✅ TERMINÉ';
                }
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card-3d text-center h-100 reveal-3d" style="padding: 0; overflow: hidden;">

                    <!-- Top colored banner -->
                    <div style="height: 6px; background: {{ $themeGradient }}; width: 100%;"></div>

                    <!-- Status badge -->
                    <div style="position: absolute; top: 16px; right: 16px; z-index: 2;">
                        <span style="display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.03em;
                            background: {{ $isLive ? 'rgba(220,38,38,0.2)' : ($isUpcoming ? 'rgba(2,132,199,0.15)' : 'rgba(71,85,105,0.2)') }};
                            color: {{ $isLive ? '#EF4444' : ($isUpcoming ? '#38BDF8' : '#94A3B8') }};
                            border: 1px solid {{ $isLive ? 'rgba(220,38,38,0.2)' : ($isUpcoming ? 'rgba(2,132,199,0.2)' : 'rgba(71,85,105,0.2)') }};
                            {{ $isLive ? 'animation: livePulseBadge 2s ease-in-out infinite;' : '' }}">
                            {{ $statusText }}
                        </span>
                    </div>

                    <!-- Icon area -->
                    <div style="padding: 2rem 1.5rem 1rem;">
                        <div style="width: 72px; height: 72px; border-radius: 20px; margin: 0 auto 1rem;
                            background: {{ $themeGradient }};
                            display: flex; align-items: center; justify-content: center;
                            box-shadow: 0 8px 30px {{ $isLive ? 'rgba(220,38,38,0.25)' : ($isUpcoming ? 'rgba(2,132,199,0.2)' : 'rgba(71,85,105,0.15)') }};
                            transition: transform 0.3s ease;">
                            <i class="bi bi-camera-video-fill" style="font-size: 1.6rem; color: white;"></i>
                        </div>

                        <h5 class="fw-bold text-white mb-2" style="font-family: 'Poppins', sans-serif; font-size: 1rem;">
                            {{ $live->title }}
                        </h5>

                        @if($liveDate)
                        <div style="color: rgba(255,255,255,0.4); font-size: 0.78rem; display: flex; align-items: center; justify-content: center; gap: 4px; margin-bottom: 0.5rem;">
                            <i class="bi bi-calendar-event" style="color: {{ $themeColor }}; font-size: 0.7rem;"></i>
                            {{ $liveDate->format('d/m/Y') }}
                            @if($live->start_time)
                            <span style="color: rgba(255,255,255,0.3);">•</span>
                            <i class="bi bi-clock" style="color: {{ $themeColor }}; font-size: 0.7rem;"></i>
                            {{ $live->start_time }}
                            @endif
                        </div>
                        @endif

                        <p class="text-white-50 small mb-0" style="line-height: 1.6; font-size: 0.8rem;">
                            {{ \Illuminate\Support\Str::limit($live->description ?? 'Session en direct', 80) }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div style="padding: 0 1.5rem 1.5rem;">
                        @if($live->stream_url)
                        <a href="{{ $live->stream_url }}" target="_blank"
                           style="display: flex; align-items: center; justify-content: center; gap: 8px;
                               width: 100%; padding: 11px 20px; border-radius: 12px; font-weight: 600; font-size: 0.9rem;
                               background: {{ $themeGradient }}; color: white; text-decoration: none;
                               transition: all 0.3s ease; border: none;
                               box-shadow: 0 4px 15px {{ $isLive ? 'rgba(220,38,38,0.35)' : ($isUpcoming ? 'rgba(2,132,199,0.25)' : 'rgba(71,85,105,0.2)') }};"
                           onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px {{ $isLive ? 'rgba(220,38,38,0.45)' : ($isUpcoming ? 'rgba(2,132,199,0.35)' : 'rgba(71,85,105,0.3)') }}'"
                           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px {{ $isLive ? 'rgba(220,38,38,0.35)' : ($isUpcoming ? 'rgba(2,132,199,0.25)' : 'rgba(71,85,105,0.2)') }}'">
                            <i class="bi bi-play-circle-fill"></i>
                            Rejoindre le live
                        </a>

                        <a href="{{ $outlookUrl }}" target="_blank"
                           style="display: flex; align-items: center; justify-content: center; gap: 6px;
                               width: 100%; padding: 9px 20px; border-radius: 12px; font-weight: 500; font-size: 0.8rem;
                               background: rgba(255,255,255,0.04); color: rgba(255,255,255,0.5); text-decoration: none;
                               border: 1px solid rgba(255,255,255,0.06); margin-top: 8px;
                               transition: all 0.3s ease;"
                           onmouseover="this.style.background='rgba(255,255,255,0.08)';this.style.color='rgba(255,255,255,0.7)'"
                           onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.color='rgba(255,255,255,0.5)'">
                            <i class="bi bi-calendar-plus" style="color: {{ $themeColor }};"></i>
                            Ajouter à Outlook
                        </a>
                        @else
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 11px 20px; border-radius: 12px; background: rgba(255,255,255,0.04); border: 1px dashed rgba(255,255,255,0.08); color: rgba(255,255,255,0.3); font-size: 0.85rem;">
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

@endsection
