@extends('layouts.front')

@section('title', 'Lives en direct')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Préparation des sessions
    |--------------------------------------------------------------------------
    */

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

            /*
             * Pour les sessions terminées, afficher les plus récentes
             * avant les plus anciennes.
             */
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

    $currentUser = auth()->user();
    $accessRestricted = $accessRestricted ?? false;

    /*
     * Le visiteur peut voir les informations publiques des sessions.
     * Le véritable lien externe reste toujours protégé.
     */
    $canDisplaySessions =
        !auth()->check()
        || !$accessRestricted;
@endphp

<!-- =========================================================
     HERO
     ========================================================= -->
<section class="live-page-hero">
    <div class="live-hero-decoration live-hero-decoration-one"></div>
    <div class="live-hero-decoration live-hero-decoration-two"></div>

    <div class="container position-relative">
        <div
            class="live-signal-bar
                {{ $hasLiveNow ? 'is-live' : 'is-scheduled' }}"
        >
            <span
                class="live-red-lamp"
                aria-hidden="true"
            ></span>

            <strong class="live-signal-title">
                {{ $hasLiveNow
                    ? 'EN DIRECT'
                    : 'SESSIONS LIVE' }}
            </strong>

            <span class="live-signal-divider"></span>

            <span class="live-signal-description">
                @if($hasLiveNow)
                    {{ $liveCount }}
                    {{ $liveCount > 1
                        ? 'sessions actives'
                        : 'session active' }}
                @else
                    Cours interactifs programmés
                @endif
            </span>
        </div>

        <h1 class="live-page-title">
            Lives
            <span>en direct</span>
        </h1>

        <p class="live-page-subtitle">
            Participez aux sessions interactives avec vos
            enseignants et progressez en temps réel.
        </p>
    </div>
</section>

<!-- =========================================================
     COMPTEURS
     ========================================================= -->
<section class="live-stats-section">
    <div class="container">
        <div class="live-stats-grid">
            <article class="live-stat-card stat-live">
                <div class="live-stat-icon">
                    <i class="bi bi-broadcast"></i>
                </div>

                <div class="live-stat-content">
                    <strong>{{ $liveCount }}</strong>
                    <span>En direct</span>
                </div>
            </article>

            <article class="live-stat-card stat-upcoming">
                <div class="live-stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>

                <div class="live-stat-content">
                    <strong>{{ $upcomingCount }}</strong>
                    <span>À venir</span>
                </div>
            </article>

            <article class="live-stat-card stat-ended">
                <div class="live-stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>

                <div class="live-stat-content">
                    <strong>{{ $endedCount }}</strong>
                    <span>Terminées</span>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- =========================================================
     CONTENU PRINCIPAL
     ========================================================= -->
<section class="live-content-section">
    <div class="container">

        @if(session('error'))
            <div class="live-alert live-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>

                <span>
                    {{ session('error') }}
                </span>
            </div>
        @endif

        @if(session('success'))
            <div class="live-alert live-alert-success">
                <i class="bi bi-check-circle-fill"></i>

                <span>
                    {{ session('success') }}
                </span>
            </div>
        @endif

        <!-- Étudiant connecté mais compte non activé -->
        @auth
            @if($accessRestricted)
                <div class="live-access-card">
                    <div class="live-access-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>

                    <div class="live-access-information">
                        <h2>Compte en attente d’activation</h2>

                        <p>
                            Votre compte doit être activé par
                            l’administration pour consulter les
                            sessions affectées à votre classe.
                        </p>
                    </div>

                    <a
                        href="{{ route('student.waiting') }}"
                        class="live-button live-button-primary"
                    >
                        <i class="bi bi-hourglass-split"></i>
                        Voir l’état du compte
                    </a>
                </div>
            @endif
        @endauth

        <!-- Message destiné uniquement aux visiteurs -->
        @guest
            <div class="live-visitor-notice">
                <div class="live-visitor-notice-icon">
                    <i class="bi bi-shield-check"></i>
                </div>

                <div class="live-visitor-notice-content">
                    <strong>
                        Les informations des sessions sont publiques
                    </strong>

                    <span>
                        Le lien de connexion reste protégé. Connectez-vous
                        avec un compte actif et affecté à la bonne classe
                        pour rejoindre un live.
                    </span>
                </div>

                <a
                    href="{{ route('login') }}"
                    class="live-button live-button-outline"
                >
                    <i class="bi bi-box-arrow-in-right"></i>
                    Se connecter
                </a>
            </div>
        @endguest

        @if($canDisplaySessions)
            <div class="live-cards-grid">
                @forelse($orderedLives as $live)
                    @php
                        $startDateTime = $live->start_date_time;
                        $endDateTime = $live->end_date_time;

                        $startTime = $startDateTime
                            ? $startDateTime->format('H:i')
                            : null;

                        $endTime = $endDateTime
                            ? $endDateTime->format('H:i')
                            : null;

                        $isLive = $live->is_live;
                        $isUpcoming = $live->is_upcoming;
                        $isEnded = $live->is_ended;
                        $isUnscheduled =
                            $live->schedule_status === 'unscheduled';

                        $statusText = $live->status_label;

                        $joinEarlyMinutes = max(
                            0,
                            (int) config(
                                'live.join_early_minutes',
                                15
                            )
                        );

                        $joinOpensAt = $startDateTime
                            ? $startDateTime
                                ->copy()
                                ->subMinutes($joinEarlyMinutes)
                            : null;

                        $canRequestAccess =
                            $isLive
                            || (
                                $isUpcoming
                                && $joinOpensAt
                                && now()->gte($joinOpensAt)
                            );

                        if ($isLive) {
                            $cardClass = 'session-live';
                            $iconClass = 'bi-broadcast';
                        } elseif ($isUpcoming) {
                            $cardClass = 'session-upcoming';
                            $iconClass = 'bi-camera-video-fill';
                        } elseif ($isEnded) {
                            $cardClass = 'session-ended';
                            $iconClass = 'bi-check-circle-fill';
                        } else {
                            $cardClass = 'session-unscheduled';
                            $iconClass = 'bi-calendar-question-fill';
                        }

                        $className = optional(
                            $live->classRoom
                        )->name;

                        /*
                         * Le calendrier Outlook contient uniquement
                         * la route sécurisée interne, jamais stream_url.
                         */
                        $outlookUrl =
                            'https://outlook.live.com/calendar/0/'
                            . 'deeplink/compose'
                            . '?path=/calendar/action/compose'
                            . '&rru=addevent';

                        $outlookUrl .=
                            '&subject='
                            . urlencode($live->title);

                        if ($startDateTime && $endDateTime) {
                            $outlookUrl .=
                                '&startdt='
                                . $startDateTime
                                    ->copy()
                                    ->utc()
                                    ->format('Y-m-d\TH:i:s\Z');

                            $outlookUrl .=
                                '&enddt='
                                . $endDateTime
                                    ->copy()
                                    ->utc()
                                    ->format('Y-m-d\TH:i:s\Z');
                        }

                        $secureInternalUrl = route(
                            'live.access.request',
                            $live
                        );

                        $outlookUrl .=
                            '&body='
                            . urlencode(
                                (
                                    $live->description
                                    ?? 'Session interactive en direct'
                                )
                                . "\n\nAccès sécurisé : "
                                . $secureInternalUrl
                            );

                        $outlookUrl .=
                            '&location='
                            . urlencode($secureInternalUrl);
                    @endphp

                    <article
                        class="live-session-card {{ $cardClass }}"
                        data-live-boundary
                        data-open-at="{{
                            $joinOpensAt
                                ? $joinOpensAt->toIso8601String()
                                : ''
                        }}"
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
                        <div class="live-session-accent"></div>

                        <div class="live-session-header">
                            <div class="live-session-icon">
                                <i class="bi {{ $iconClass }}"></i>
                            </div>

                            <span class="live-session-status">
                                @if($isLive)
                                    <span
                                        class="live-card-dot"
                                        aria-hidden="true"
                                    ></span>
                                @endif

                                {{ $statusText }}
                            </span>
                        </div>

                        <div class="live-session-body">
                            <h2 class="live-session-title">
                                {{ $live->title }}
                            </h2>

                            <div class="live-session-meta">
                                @if($className)
                                    <div class="live-meta-item">
                                        <i class="bi bi-mortarboard"></i>

                                        <span>
                                            {{ $className }}
                                        </span>
                                    </div>
                                @endif

                                @if($startDateTime)
                                    <div class="live-meta-item">
                                        <i class="bi bi-calendar-event"></i>

                                        <span>
                                            {{
                                                $startDateTime
                                                    ->format('d/m/Y')
                                            }}
                                        </span>
                                    </div>

                                    <div class="live-meta-item">
                                        <i class="bi bi-clock"></i>

                                        <span>
                                            {{ $startTime }}

                                            @if($endTime)
                                                – {{ $endTime }}
                                            @endif
                                        </span>
                                    </div>
                                @else
                                    <div class="live-meta-item">
                                        <i class="bi bi-calendar-question"></i>

                                        <span>
                                            Date à confirmer
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="live-session-description">
                                <i class="bi bi-chat-square-text"></i>

                                <span>
                                    {{
                                        \Illuminate\Support\Str::limit(
                                            $live->description
                                                ?? 'Session interactive avec votre enseignant.',
                                            110
                                        )
                                    }}
                                </span>
                            </div>
                        </div>

                        <div class="live-session-footer">
                            @if($isEnded)
                                <div class="live-button live-button-disabled">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Session terminée
                                </div>

                            @elseif($isUnscheduled || !$live->stream_url)
                                <div class="live-button live-button-disabled">
                                    <i class="bi bi-hourglass-split"></i>
                                    Lien à venir
                                </div>

                            @else
                                @guest
                                <a
                                    href="{{ route('login') }}"
                                    class="live-button live-button-primary"
                                >
                                    <i class="bi bi-shield-lock-fill"></i>

                                    Se connecter pour accéder
                                </a>

                                <p class="live-security-note">
                                    Le lien Meet ou Teams n’est jamais
                                    affiché aux visiteurs.
                                </p>
                            @else
                                @if($canRequestAccess)
                                    <a
                                        href="{{
                                            route(
                                                'live.access.request',
                                                $live
                                            )
                                        }}"
                                        class="live-button
                                            live-button-primary"
                                    >
                                        <i
                                            class="bi
                                                bi-play-circle-fill"
                                        ></i>

                                        {{ $isLive
                                            ? 'Rejoindre maintenant'
                                            : 'Accéder à la session' }}
                                    </a>
                                @else
                                    <div
                                        class="live-button
                                            live-button-disabled"
                                    >
                                        <i class="bi bi-lock-fill"></i>

                                        Disponible
                                        {{ $joinEarlyMinutes }}
                                        min avant
                                    </div>

                                    @if($joinOpensAt)
                                        <p class="live-security-note">
                                            Accès à partir du
                                            {{
                                                $joinOpensAt
                                                    ->format('d/m/Y à H:i')
                                            }}.
                                        </p>
                                    @endif
                                @endif

                                @if(
                                    $isUpcoming
                                    && $startDateTime
                                    && $endDateTime
                                )
                                    <a
                                        href="{{ $outlookUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="live-button
                                            live-button-calendar"
                                    >
                                        <i class="bi bi-calendar-plus"></i>
                                        Ajouter à Outlook
                                    </a>
                                @endif

                                <p class="live-security-note">
                                    Accès contrôlé selon votre compte,
                                    votre classe et l’horaire du live.
                                </p>
                                @endguest
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="live-empty-state">
                        <div class="live-empty-icon">
                            <i class="bi bi-camera-video-off"></i>
                        </div>

                        <h2>Aucun live disponible</h2>

                        <p>
                            Revenez bientôt pour découvrir les
                            prochaines sessions programmées.
                        </p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</section>

<style>
/* =========================================================
   HERO
   ========================================================= */

.live-page-hero {
    position: relative;
    overflow: hidden;
    padding: 2.8rem 0 4.4rem;
    text-align: center;
    color: #ffffff;
    background:
        linear-gradient(
            135deg,
            #0A1628 0%,
            #1A0A2E 42%,
            #0A1628 100%
        );
}

.live-page-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        radial-gradient(
            circle at 28% 45%,
            rgba(220,38,38,0.09),
            transparent 36%
        ),
        radial-gradient(
            circle at 73% 40%,
            rgba(124,58,237,0.09),
            transparent 38%
        );
}

.live-hero-decoration {
    position: absolute;
    border-radius: 50%;
    border: 2px solid rgba(245,158,11,0.48);
    pointer-events: none;
}

.live-hero-decoration-one {
    width: 28px;
    height: 28px;
    top: 54px;
    left: 19%;
}

.live-hero-decoration-two {
    width: 10px;
    height: 10px;
    right: 28%;
    bottom: 45px;
    background: #F59E0B;
    box-shadow: 0 0 18px rgba(245,158,11,0.55);
}

.live-signal-bar {
    width: fit-content;
    max-width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    margin: 0 auto 1.25rem;
    padding: 6px 11px;
    border: 1px solid rgba(248,113,113,0.18);
    border-radius: 999px;
    background:
        linear-gradient(
            135deg,
            rgba(127,29,29,0.25),
            rgba(255,255,255,0.035)
        );
    box-shadow: 0 8px 26px rgba(127,29,29,0.13);
    color: rgba(255,255,255,0.66);
    font-size: 0.69rem;
    line-height: 1;
    backdrop-filter: blur(12px);
}

.live-signal-title {
    color: #F87171;
    font-size: 0.66rem;
    font-weight: 900;
    letter-spacing: 0.055em;
    white-space: nowrap;
}

.live-signal-divider {
    width: 1px;
    height: 13px;
    background: rgba(248,113,113,0.22);
}

.live-signal-description {
    white-space: nowrap;
}

.live-red-lamp,
.live-card-dot {
    position: relative;
    display: inline-block;
    border-radius: 50%;
    background: #EF4444;
    box-shadow:
        0 0 0 3px rgba(239,68,68,0.13),
        0 0 11px rgba(239,68,68,0.95);
}

.live-red-lamp {
    width: 8px;
    height: 8px;
    flex: 0 0 8px;
}

.live-card-dot {
    width: 7px;
    height: 7px;
    flex: 0 0 7px;
}

.live-red-lamp::after,
.live-card-dot::after {
    content: "";
    position: absolute;
    inset: -4px;
    border: 1px solid rgba(248,113,113,0.48);
    border-radius: 50%;
    animation: liveLampPulse 1.5s ease-out infinite;
}

.live-signal-bar.is-live {
    border-color: rgba(248,113,113,0.3);
    background:
        linear-gradient(
            135deg,
            rgba(153,27,27,0.34),
            rgba(220,38,38,0.08)
        );
}

.live-page-title {
    margin: 0 0 1rem;
    color: #ffffff;
    font-family: "Poppins", sans-serif;
    font-size: clamp(2.45rem, 5vw, 3.2rem);
    font-weight: 800;
    line-height: 1.12;
    text-shadow: 0 0 40px rgba(220,38,38,0.18);
}

.live-page-title span {
    color: transparent;
    background:
        linear-gradient(
            135deg,
            #DC2626,
            #EF4444
        );
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.live-page-subtitle {
    max-width: 680px;
    margin: 0 auto;
    color: rgba(255,255,255,0.55);
    font-size: 1.06rem;
    line-height: 1.7;
}

/* =========================================================
   COMPTEURS
   ========================================================= */

.live-stats-section {
    position: relative;
    z-index: 5;
    margin-top: -4.15rem;
}

.live-stats-grid {
    max-width: 1220px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}

.live-stat-card {
    min-height: 104px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    padding: 1.05rem 1.35rem;
    border: 1px solid rgba(255,255,255,0.065);
    border-radius: 19px;
    background:
        linear-gradient(
            145deg,
            rgba(15,23,42,0.96),
            rgba(8,16,31,0.98)
        );
    box-shadow: 0 17px 42px rgba(0,0,0,0.25);
    backdrop-filter: blur(16px);
    transition:
        transform 0.28s ease,
        border-color 0.28s ease;
}

.live-stat-card:hover {
    transform: translateY(-3px);
}

.live-stat-icon {
    width: 43px;
    height: 43px;
    flex: 0 0 43px;
    display: grid;
    place-items: center;
    border-radius: 13px;
    font-size: 1.05rem;
}

.live-stat-content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
}

.live-stat-content strong {
    font-size: 1.72rem;
    font-weight: 850;
    line-height: 1;
}

.live-stat-content span {
    color: rgba(255,255,255,0.48);
    font-size: 0.74rem;
    font-weight: 750;
    letter-spacing: 0.055em;
    text-transform: uppercase;
}

.stat-live {
    border-color: rgba(239,68,68,0.18);
}

.stat-live .live-stat-icon {
    color: #F87171;
    background: rgba(220,38,38,0.12);
}

.stat-live .live-stat-content strong {
    color: #EF4444;
}

.stat-upcoming {
    border-color: rgba(56,189,248,0.16);
}

.stat-upcoming .live-stat-icon {
    color: #38BDF8;
    background: rgba(2,132,199,0.13);
}

.stat-upcoming .live-stat-content strong {
    color: #38BDF8;
}

.stat-ended {
    border-color: rgba(148,163,184,0.13);
}

.stat-ended .live-stat-icon {
    color: #CBD5E1;
    background: rgba(100,116,139,0.14);
}

.stat-ended .live-stat-content strong {
    color: #CBD5E1;
}

/* =========================================================
   CONTENU
   ========================================================= */

.live-content-section {
    padding: 1.05rem 0 4.5rem;
}

.live-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1.25rem;
    padding: 13px 16px;
    border-radius: 14px;
    font-size: 0.88rem;
}

.live-alert-danger {
    color: #FCA5A5;
    border: 1px solid rgba(248,113,113,0.19);
    background: rgba(127,29,29,0.18);
}

.live-alert-success {
    color: #86EFAC;
    border: 1px solid rgba(74,222,128,0.18);
    background: rgba(20,83,45,0.18);
}

.live-access-card,
.live-visitor-notice {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 1.5rem;
    padding: 1.05rem 1.15rem;
    border: 1px solid rgba(255,255,255,0.065);
    border-radius: 18px;
    background:
        linear-gradient(
            145deg,
            rgba(15,23,42,0.9),
            rgba(8,16,31,0.94)
        );
}

.live-access-icon,
.live-visitor-notice-icon {
    width: 48px;
    height: 48px;
    flex: 0 0 48px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    font-size: 1.15rem;
}

.live-access-icon {
    color: #F87171;
    background: rgba(220,38,38,0.12);
}

.live-visitor-notice-icon {
    color: #4ADE80;
    background: rgba(34,197,94,0.11);
}

.live-access-information,
.live-visitor-notice-content {
    min-width: 0;
    flex: 1;
}

.live-access-information h2 {
    margin: 0 0 4px;
    color: #ffffff;
    font-size: 1rem;
    font-weight: 750;
}

.live-access-information p {
    margin: 0;
    color: rgba(255,255,255,0.52);
    font-size: 0.84rem;
    line-height: 1.55;
}

.live-visitor-notice-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.live-visitor-notice-content strong {
    color: rgba(255,255,255,0.9);
    font-size: 0.88rem;
}

.live-visitor-notice-content span {
    color: rgba(255,255,255,0.48);
    font-size: 0.8rem;
    line-height: 1.5;
}

/* =========================================================
   CARTES DES SESSIONS
   ========================================================= */

.live-cards-grid {
    margin-top: 0.25rem;
    display: grid;
    grid-template-columns:
        repeat(
            auto-fit,
            minmax(min(100%, 330px), 430px)
        );
    justify-content: center;
    align-items: start;
    gap: 22px;
}

.live-session-card {
    position: relative;
    width: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 22px;
    background:
        linear-gradient(
            150deg,
            rgba(17,27,47,0.98),
            rgba(9,17,32,0.99)
        );
    box-shadow:
        0 18px 46px rgba(0,0,0,0.24);
    transition:
        transform 0.3s ease,
        border-color 0.3s ease,
        box-shadow 0.3s ease;
}

.live-session-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255,255,255,0.11);
    box-shadow:
        0 23px 56px rgba(0,0,0,0.31);
}

.live-session-accent {
    width: 100%;
    height: 4px;
}

.session-live .live-session-accent {
    background:
        linear-gradient(
            90deg,
            #B91C1C,
            #EF4444
        );
}

.session-upcoming .live-session-accent {
    background:
        linear-gradient(
            90deg,
            #0369A1,
            #38BDF8
        );
}

.session-ended .live-session-accent {
    background:
        linear-gradient(
            90deg,
            #475569,
            #94A3B8
        );
}

.session-unscheduled .live-session-accent {
    background:
        linear-gradient(
            90deg,
            #B45309,
            #F59E0B
        );
}

.live-session-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 1.05rem 1.15rem 0;
}

.live-session-icon {
    width: 54px;
    height: 54px;
    flex: 0 0 54px;
    display: grid;
    place-items: center;
    border-radius: 16px;
    color: #ffffff;
    font-size: 1.22rem;
}

.session-live .live-session-icon {
    background:
        linear-gradient(
            135deg,
            #B91C1C,
            #EF4444
        );
    box-shadow:
        0 9px 22px rgba(220,38,38,0.2);
}

.session-upcoming .live-session-icon {
    background:
        linear-gradient(
            135deg,
            #0369A1,
            #38BDF8
        );
    box-shadow:
        0 9px 22px rgba(2,132,199,0.18);
}

.session-ended .live-session-icon {
    background:
        linear-gradient(
            135deg,
            #52627A,
            #71839C
        );
    box-shadow:
        0 9px 22px rgba(71,85,105,0.16);
}

.session-unscheduled .live-session-icon {
    background:
        linear-gradient(
            135deg,
            #B45309,
            #F59E0B
        );
}

.live-session-status {
    max-width: 190px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 6px 11px;
    border-radius: 999px;
    font-size: 0.62rem;
    font-weight: 850;
    letter-spacing: 0.035em;
    line-height: 1.1;
    text-align: center;
    white-space: nowrap;
}

.session-live .live-session-status {
    color: #FCA5A5;
    border: 1px solid rgba(248,113,113,0.2);
    background: rgba(220,38,38,0.14);
}

.session-upcoming .live-session-status {
    color: #7DD3FC;
    border: 1px solid rgba(56,189,248,0.18);
    background: rgba(2,132,199,0.13);
}

.session-ended .live-session-status {
    color: #E2E8F0;
    border: 1px solid rgba(148,163,184,0.18);
    background: rgba(100,116,139,0.15);
}

.session-unscheduled .live-session-status {
    color: #FCD34D;
    border: 1px solid rgba(245,158,11,0.18);
    background: rgba(180,83,9,0.13);
}

.live-session-body {
    padding: 1rem 1.15rem 0.75rem;
}

.live-session-title {
    margin: 0 0 0.75rem;
    color: #F8FAFC;
    font-family: "Poppins", sans-serif;
    font-size: 1.08rem;
    font-weight: 760;
    line-height: 1.35;
}

.live-session-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    margin-bottom: 0.8rem;
}

.live-meta-item {
    min-height: 30px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 9px;
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 10px;
    color: rgba(255,255,255,0.61);
    background: rgba(255,255,255,0.03);
    font-size: 0.72rem;
    line-height: 1;
}

.live-meta-item i {
    font-size: 0.74rem;
}

.session-live .live-meta-item i {
    color: #F87171;
}

.session-upcoming .live-meta-item i {
    color: #38BDF8;
}

.session-ended .live-meta-item i {
    color: #A8B5C7;
}

.session-unscheduled .live-meta-item i {
    color: #FBBF24;
}

.live-session-description {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    margin: 0;
    padding: 10px 11px;
    border: 1px solid rgba(255,255,255,0.045);
    border-radius: 11px;
    color: rgba(255,255,255,0.49);
    background: rgba(255,255,255,0.022);
    font-size: 0.77rem;
    line-height: 1.55;
}

.live-session-description i {
    flex: 0 0 auto;
    margin-top: 2px;
    color: rgba(255,255,255,0.3);
}

.live-session-footer {
    padding: 0.15rem 1.15rem 1.15rem;
}

.live-button {
    width: 100%;
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 9px 15px;
    border-radius: 12px;
    font-size: 0.81rem;
    font-weight: 720;
    line-height: 1.2;
    text-align: center;
    text-decoration: none;
    transition:
        transform 0.24s ease,
        opacity 0.24s ease,
        border-color 0.24s ease;
}

.live-button:hover {
    transform: translateY(-2px);
}

.live-button-primary {
    color: #ffffff;
    border: 1px solid rgba(255,255,255,0.06);
    background:
        linear-gradient(
            135deg,
            #4338CA,
            #7C3AED
        );
    box-shadow:
        0 9px 22px rgba(79,70,229,0.19);
}

.session-live .live-button-primary {
    background:
        linear-gradient(
            135deg,
            #B91C1C,
            #EF4444
        );
    box-shadow:
        0 9px 22px rgba(220,38,38,0.19);
}

.live-button-primary:hover {
    color: #ffffff;
    opacity: 0.95;
}

.live-button-outline {
    width: auto;
    flex: 0 0 auto;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.045);
}

.live-button-outline:hover {
    color: #ffffff;
    border-color: rgba(255,255,255,0.18);
}

.live-button-disabled {
    color: #D7DFEA;
    border: 1px solid rgba(148,163,184,0.16);
    background:
        linear-gradient(
            135deg,
            rgba(71,85,105,0.22),
            rgba(100,116,139,0.14)
        );
    cursor: not-allowed;
}

.live-button-disabled:hover {
    transform: none;
}

.live-button-calendar {
    margin-top: 8px;
    color: rgba(255,255,255,0.66);
    border: 1px solid rgba(255,255,255,0.07);
    background: rgba(255,255,255,0.035);
}

.live-button-calendar:hover {
    color: #ffffff;
    border-color: rgba(255,255,255,0.12);
}

.live-security-note {
    margin: 8px 0 0;
    color: rgba(255,255,255,0.35);
    font-size: 0.67rem;
    line-height: 1.45;
    text-align: center;
}

.live-empty-state {
    grid-column: 1 / -1;
    max-width: 620px;
    width: 100%;
    margin: 0 auto;
    padding: 3rem 1.5rem;
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 22px;
    text-align: center;
    background:
        linear-gradient(
            145deg,
            rgba(15,23,42,0.86),
            rgba(8,16,31,0.94)
        );
}

.live-empty-icon {
    width: 74px;
    height: 74px;
    display: grid;
    place-items: center;
    margin: 0 auto 1rem;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 20px;
    color: rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.03);
    font-size: 1.75rem;
}

.live-empty-state h2 {
    margin: 0 0 0.5rem;
    color: rgba(255,255,255,0.78);
    font-size: 1.05rem;
    font-weight: 750;
}

.live-empty-state p {
    margin: 0;
    color: rgba(255,255,255,0.42);
    font-size: 0.84rem;
}

/* =========================================================
   ANIMATIONS
   ========================================================= */

@keyframes liveLampPulse {
    0% {
        opacity: 0.82;
        transform: scale(0.72);
    }

    72% {
        opacity: 0;
        transform: scale(1.8);
    }

    100% {
        opacity: 0;
        transform: scale(1.8);
    }
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 767.98px) {
    .live-page-hero {
        padding: 2.45rem 0 4.2rem;
    }

    .live-stats-section {
        margin-top: -3.15rem;
    }

    .live-stats-grid {
        gap: 9px;
    }

    .live-stat-card {
        min-height: 83px;
        gap: 9px;
        padding: 0.8rem 0.65rem;
        border-radius: 16px;
    }

    .live-stat-icon {
        width: 35px;
        height: 35px;
        flex-basis: 35px;
        border-radius: 11px;
        font-size: 0.88rem;
    }

    .live-stat-content strong {
        font-size: 1.4rem;
    }

    .live-stat-content span {
        font-size: 0.62rem;
    }

    .live-content-section {
        padding-top: 1rem;
    }

    .live-access-card,
    .live-visitor-notice {
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .live-access-card .live-button,
    .live-visitor-notice .live-button {
        width: 100%;
    }
}

@media (max-width: 575.98px) {
    .live-page-title {
        font-size: 2.35rem;
    }

    .live-page-subtitle {
        max-width: 335px;
        font-size: 0.93rem;
    }

    .live-signal-description,
    .live-signal-divider {
        display: none;
    }

    .live-stats-grid {
        grid-template-columns: 1fr;
    }

    .live-stat-card {
        min-height: 72px;
        justify-content: flex-start;
        padding-inline: 1rem;
    }

    .live-stat-content {
        flex: 1;
    }

    .live-cards-grid {
        grid-template-columns: 1fr;
    }

    .live-session-card {
        border-radius: 19px;
    }

    .live-session-header {
        padding: 0.95rem 1rem 0;
    }

    .live-session-body {
        padding: 0.9rem 1rem 0.7rem;
    }

    .live-session-footer {
        padding: 0.1rem 1rem 1rem;
    }

    .live-session-icon {
        width: 50px;
        height: 50px;
        flex-basis: 50px;
        border-radius: 15px;
    }

    .live-session-status {
        padding: 6px 9px;
        font-size: 0.58rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const now = Date.now();

    /*
     * La page se recharge automatiquement :
     * - lorsque l'accès anticipé s'ouvre ;
     * - lorsque la session commence ;
     * - lorsque la session se termine.
     */
    const boundaries = Array.from(
        document.querySelectorAll('[data-live-boundary]')
    )
        .flatMap(element => [
            element.dataset.openAt,
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
        Math.max(
            1500,
            nextBoundary - now + 1500
        ),
        maximumTimeout
    );

    window.setTimeout(() => {
        window.location.reload();
    }, delay);
});
</script>

@endsection