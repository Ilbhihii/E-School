@extends('layouts.front')

@section('title', 'Planning des cours — Smart School Academy')

@section('content')
<div class="public-schedule-page">
    <section class="public-schedule-hero">
        <div class="public-schedule-container">
            <div class="public-schedule-hero-card">
                <div class="public-schedule-hero-content">
                    <span class="public-schedule-kicker">
                        <i class="bi bi-mortarboard-fill"></i>
                        Smart School Academy
                    </span>

                    <h1>Planning hebdomadaire des cours</h1>

                    <p>
                        Consultez facilement les horaires publiés pour les cours d’Arabe,
                        de Coran et de Soutien Lycée.
                    </p>

                </div>

                <div class="public-schedule-hero-visual" aria-hidden="true">
                    <span class="public-schedule-hero-ring"></span>
                    <span class="public-schedule-hero-ring public-schedule-hero-ring-small"></span>
                    <div class="public-schedule-calendar-icon">
                        <i class="bi bi-calendar2-week-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="public-schedule-content">
        <div class="public-schedule-container">
            @if($schedules->isNotEmpty())
                <section class="public-schedule-day">
                    <header class="public-schedule-day-header">
                        <div class="public-schedule-day-heading">
                            <div class="public-schedule-day-icon">
                                <i class="bi bi-calendar-event"></i>
                            </div>

                            <div>
                                <span class="public-schedule-day-kicker">
                                    Programme du jour
                                </span>
                            </div>
                        </div>

                        <span class="public-schedule-count">
                            <i class="bi bi-book"></i>
                            {{ $schedules->count() }}
                            {{
                                $schedules->count() > 1
                                    ? 'cours planifiés'
                                    : 'cours planifié'
                            }}
                        </span>
                    </header>

                    <div class="public-schedule-grid public-schedule-single-row">
                        @foreach($schedules as $schedule)
                            @php
                                $subjectName =
                                    mb_strtolower(
                                        $schedule['subject'] ?? ''
                                    );

                                $subjectIcon =
                                    'bi-book-half';

                                if (
                                    mb_stripos(
                                        $subjectName,
                                        'arabe'
                                    ) !== false
                                ) {
                                    $subjectIcon =
                                        'bi-translate';
                                } elseif (
                                    mb_stripos(
                                        $subjectName,
                                        'coran'
                                    ) !== false
                                ) {
                                    $subjectIcon =
                                        'bi-book';
                                } elseif (
                                    mb_stripos(
                                        $subjectName,
                                        'math'
                                    ) !== false
                                ) {
                                    $subjectIcon =
                                        'bi-calculator';
                                } elseif (
                                    mb_stripos(
                                        $subjectName,
                                        'informatique'
                                    ) !== false
                                ) {
                                    $subjectIcon =
                                        'bi-laptop';
                                } elseif (
                                    mb_stripos(
                                        $subjectName,
                                        'français'
                                    ) !== false
                                ) {
                                    $subjectIcon =
                                        'bi-chat-square-text';
                                }
                            @endphp

                            <a
                                href="{{ route('public.schedule.index', ['schedule' => $schedule['schedule_id']]) }}"
                                class="public-schedule-card-link"
                                aria-label="Voir la fiche de {{ $schedule['subject'] }} — {{ $schedule['level'] }} — {{ $schedule['class_name'] }}"
                            >
                            <article class="public-schedule-card">
                                <div class="public-schedule-card-accent"></div>

                                <div class="public-schedule-card-top">
                                    <div class="public-schedule-time-block">
                                        <span class="public-schedule-time-icon">
                                            <i class="bi bi-clock-fill"></i>
                                        </span>

                                        <div>
                                            <strong>
                                                {{ $schedule['start_label'] }}
                                            </strong>

                                            <small>
                                                {{ $schedule['duration_label'] }}
                                            </small>
                                        </div>
                                    </div>

                                    <span class="public-schedule-status">
                                        <span></span>
                                        Publié
                                    </span>
                                </div>

                                <div class="public-schedule-subject-icon">
                                    <i class="bi {{ $subjectIcon }}"></i>
                                </div>

                                <span class="public-schedule-card-label">
                                    Matière
                                </span>

                                <h3>{{ $schedule['subject'] }}</h3>

                                <div class="public-schedule-path">
                                    <span>
                                        <i class="bi bi-bar-chart-fill"></i>
                                        {{ $schedule['level'] }}
                                    </span>

                                    <i
                                        class="
                                            bi bi-chevron-right
                                            public-schedule-path-arrow
                                        "
                                    ></i>

                                    <span>
                                        <i class="bi bi-people-fill"></i>
                                        {{ $schedule['class_name'] }}
                                    </span>

                                    @if(!empty($schedule['slot_code']))
                                        <i
                                            class="
                                                bi bi-chevron-right
                                                public-schedule-path-arrow
                                            "
                                        ></i>

                                        <span>
                                            <i class="bi bi-clock-fill"></i>
                                            {{ $schedule['slot_code'] }}
                                        </span>
                                    @endif
                                </div>

                                <footer class="public-schedule-card-footer">
                                    <span>
                                        <i class="bi bi-arrow-repeat"></i>
                                        {{
                                            $schedule[
                                                'recurrence_label'
                                            ]
                                        }}
                                    </span>

                                    <span>
                                        <i class="bi bi-clock-history"></i>
                                        {{ $schedule['time_label'] }}
                                    </span>

                                    @if(!empty($schedule['date_label']))
                                        <span class="public-schedule-full-date">
                                            <i class="bi bi-calendar-check"></i>
                                            {{ $schedule['date_label'] }}
                                        </span>
                                    @endif
                                </footer>

                                <div class="public-schedule-card-action">
                                    <span>Voir la fiche du cours</span>
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </article>
                            </a>
                        @endforeach
                    </div>
                </section>
            @else
                <div class="public-schedule-empty">
                    <div class="public-schedule-empty-icon">
                        <i class="bi bi-calendar2-x"></i>
                    </div>

                    <h2>Planning bientôt disponible</h2>

                    <p>
                        Les horaires seront affichés ici dès leur publication
                        par l’administration.
                    </p>

                    <a href="{{ route('home') }}">
                        <i class="bi bi-arrow-left"></i>
                        Retour à l’accueil
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .public-schedule-page {
        position: relative;
        min-height: 55vh;
        color: #ffffff;
        overflow: hidden;
    }

    .public-schedule-container {
        width: min(1120px, calc(100% - 32px));
        margin: 0 auto;
    }

    .public-schedule-hero {
        position: relative;
        padding: 28px 0 14px;
    }

    .public-schedule-hero-card {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 205px;
        padding: 28px 32px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 22px;
        background:
            radial-gradient(circle at 90% 12%, rgba(124, 58, 237, 0.28), transparent 34%),
            radial-gradient(circle at 65% 95%, rgba(37, 99, 235, 0.18), transparent 36%),
            linear-gradient(135deg, rgba(15, 23, 42, 0.96), rgba(8, 16, 30, 0.93));
        box-shadow: 0 30px 75px rgba(0, 0, 0, 0.30);
    }

    .public-schedule-hero-card::before {
        position: absolute;
        inset: 0;
        content: "";
        pointer-events: none;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.018) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.018) 1px, transparent 1px);
        background-size: 30px 30px;
        mask-image: linear-gradient(to right, #000, transparent 78%);
    }

    .public-schedule-hero-content {
        position: relative;
        z-index: 2;
        max-width: 690px;
    }

    .public-schedule-kicker {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 11px;
        margin-bottom: 11px;
        color: #FFD166;
        font-size: 0.66rem;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
        border: 1px solid rgba(255, 209, 102, 0.22);
        border-radius: 999px;
        background: rgba(255, 209, 102, 0.08);
    }

    .public-schedule-hero h1 {
        max-width: 780px;
        margin: 0 0 10px;
        color: #ffffff;
        font-family: 'Poppins', sans-serif;
        font-size: clamp(1.8rem, 3.6vw, 3rem);
        font-weight: 850;
        line-height: 1.08;
        letter-spacing: -0.045em;
    }

    .public-schedule-hero p {
        max-width: 720px;
        margin: 0;
        color: rgba(255, 255, 255, 0.62);
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .public-schedule-next {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        max-width: 100%;
        padding: 12px 16px;
        margin-top: 24px;
        border: 1px solid rgba(96, 165, 250, 0.16);
        border-radius: 15px;
        background: rgba(37, 99, 235, 0.09);
    }

    .public-schedule-next-icon {
        display: grid;
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        place-items: center;
        color: #93C5FD;
        border-radius: 10px;
        background: rgba(37, 99, 235, 0.18);
    }

    .public-schedule-next div {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 2px;
    }

    .public-schedule-next small {
        color: rgba(255, 255, 255, 0.42);
        font-size: 0.64rem;
    }

    .public-schedule-next strong {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.82rem;
        line-height: 1.4;
    }

    .public-schedule-hero-visual {
        position: relative;
        z-index: 1;
        display: grid;
        flex: 0 0 125px;
        width: 125px;
        height: 125px;
        place-items: center;
    }

    .public-schedule-calendar-icon {
        position: relative;
        z-index: 3;
        display: grid;
        width: 62px;
        height: 62px;
        place-items: center;
        color: #FFD166;
        font-size: 2.35rem;
        border: 1px solid rgba(255, 209, 102, 0.24);
        border-radius: 17px;
        background: linear-gradient(145deg, rgba(255, 209, 102, 0.14), rgba(124, 58, 237, 0.12));
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        transform: rotate(4deg);
    }

    .public-schedule-hero-ring {
        position: absolute;
        width: 118px;
        height: 118px;
        border: 1px solid rgba(167, 139, 250, 0.18);
        border-radius: 50%;
        animation: publicScheduleSpin 18s linear infinite;
    }

    .public-schedule-hero-ring::before,
    .public-schedule-hero-ring::after {
        position: absolute;
        width: 8px;
        height: 8px;
        content: "";
        border-radius: 50%;
        background: #A78BFA;
        box-shadow: 0 0 18px rgba(167, 139, 250, 0.8);
    }

    .public-schedule-hero-ring::before {
        top: 20px;
        right: 24px;
    }

    .public-schedule-hero-ring::after {
        bottom: 28px;
        left: 14px;
        background: #60A5FA;
        box-shadow: 0 0 18px rgba(96, 165, 250, 0.8);
    }

    .public-schedule-hero-ring-small {
        width: 96px;
        height: 96px;
        animation-direction: reverse;
        animation-duration: 13s;
    }

    @keyframes publicScheduleSpin {
        to { transform: rotate(360deg); }
    }

    .public-schedule-content {
        padding: 8px 0 46px;
    }

    .public-schedule-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 30px;
    }

    .public-schedule-stat-card {
        display: flex;
        align-items: center;
        gap: 15px;
        min-height: 96px;
        padding: 20px;
        border: 1px solid rgba(148, 163, 184, 0.13);
        border-radius: 19px;
        background: rgba(15, 23, 42, 0.68);
        box-shadow: 0 16px 38px rgba(0, 0, 0, 0.14);
        backdrop-filter: blur(12px);
        transition: transform 0.25s ease, border-color 0.25s ease;
    }

    .public-schedule-stat-card:hover {
        border-color: rgba(129, 140, 248, 0.32);
        transform: translateY(-3px);
    }

    .public-schedule-stat-icon {
        display: grid;
        flex: 0 0 36px;
        width: 36px;
        height: 36px;
        place-items: center;
        font-size: 1.25rem;
        border-radius: 10px;
    }

    .public-schedule-stat-icon-blue {
        color: #93C5FD;
        background: rgba(37, 99, 235, 0.14);
    }

    .public-schedule-stat-icon-purple {
        color: #C4B5FD;
        background: rgba(124, 58, 237, 0.14);
    }

    .public-schedule-stat-icon-yellow {
        color: #FCD34D;
        background: rgba(245, 158, 11, 0.11);
    }

    .public-schedule-stat-card div {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .public-schedule-stat-card small {
        color: rgba(255, 255, 255, 0.45);
        font-size: 0.76rem;
    }

    .public-schedule-stat-card strong {
        color: #ffffff;
        font-size: 1.4rem;
        font-weight: 850;
    }

    .public-schedule-day {
        padding: 18px;
        margin-bottom: 18px;
        border: 1px solid rgba(148, 163, 184, 0.13);
        border-radius: 19px;
        background: rgba(8, 17, 31, 0.86);
        box-shadow: 0 22px 55px rgba(0, 0, 0, 0.19);
        backdrop-filter: blur(12px);
    }

    .public-schedule-day-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-bottom: 12px;
        margin-bottom: 9px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.11);
    }

    .public-schedule-day-heading {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .public-schedule-day-icon {
        display: grid;
        flex: 0 0 36px;
        width: 36px;
        height: 36px;
        place-items: center;
        color: #93C5FD;
        font-size: 1rem;
        border: 1px solid rgba(96, 165, 250, 0.16);
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.22), rgba(124, 58, 237, 0.16));
    }

    .public-schedule-day-kicker {
        display: block;
        margin-bottom: 2px;
        color: #818CF8;
        font-size: 0.62rem;
        font-weight: 850;
        letter-spacing: 0.11em;
        text-transform: uppercase;
    }

    .public-schedule-day-header h2 {
        margin: 0;
        color: #ffffff;
        font-size: 1.52rem;
        font-weight: 850;
    }

    .public-schedule-count {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        color: rgba(255, 255, 255, 0.68);
        font-size: 0.68rem;
        font-weight: 750;
        border: 1px solid rgba(148, 163, 184, 0.13);
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.06);
    }

    .public-schedule-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 9px;
    }

    /*
     * Programme public sur UNE SEULE LIGNE.
     * Si plusieurs cours dépassent la largeur disponible,
     * la ligne reste unique et devient défilable horizontalement.
     */
    .public-schedule-single-row {
        display: flex;
        flex-wrap: nowrap;
        gap: 12px;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 2px 2px 10px;
        scroll-snap-type: x proximity;
        scrollbar-width: thin;
        scrollbar-color:
            rgba(129, 140, 248, 0.45)
            rgba(148, 163, 184, 0.06);
    }

    .public-schedule-single-row::-webkit-scrollbar {
        height: 7px;
    }

    .public-schedule-single-row::-webkit-scrollbar-track {
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.06);
    }

    .public-schedule-single-row::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: rgba(129, 140, 248, 0.45);
    }

    .public-schedule-single-row .public-schedule-card {
        flex: 0 0 310px;
        width: 310px;
        min-width: 310px;
        scroll-snap-align: start;
    }

    .public-schedule-full-date {
        color: rgba(255, 255, 255, 0.58);
        font-weight: 750;
    }

    .public-schedule-card-link {
        display: block;
        height: 100%;
        color: inherit;
        text-decoration: none;
        border-radius: 16px;
        outline: none;
    }

    .public-schedule-card-link:focus-visible {
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.35);
    }

    .public-schedule-card {
        position: relative;
        display: flex;
        min-height: 100%;
        flex-direction: column;
        padding: 15px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.13);
        border-radius: 16px;
        background: linear-gradient(145deg, rgba(21, 33, 54, 0.94), rgba(10, 19, 34, 0.97));
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18);
        transition: transform 0.28s ease, border-color 0.28s ease, box-shadow 0.28s ease;
    }

    .public-schedule-card:hover {
        border-color: rgba(129, 140, 248, 0.38);
        box-shadow: 0 25px 55px rgba(0, 0, 0, 0.28);
        transform: translateY(-6px);
    }

    .public-schedule-card-action {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding-top: 11px;
        margin-top: auto;
        color: #A5B4FC;
        font-size: 0.68rem;
        font-weight: 800;
        border-top: 1px solid rgba(148, 163, 184, 0.10);
    }

    .public-schedule-card-action i {
        color: #FCD34D;
        transition: transform 0.2s ease;
    }

    .public-schedule-card-link:hover .public-schedule-card-action i {
        transform: translateX(4px);
    }

    .public-schedule-card-accent {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, #2563EB, #7C3AED, #FFD166);
    }

    .public-schedule-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 9px;
        margin-bottom: 13px;
    }

    .public-schedule-time-block {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .public-schedule-time-icon {
        display: grid;
        flex: 0 0 36px;
        width: 36px;
        height: 36px;
        place-items: center;
        color: #FCD34D;
        border-radius: 10px;
        background: rgba(245, 158, 11, 0.10);
    }

    .public-schedule-time-block div {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .public-schedule-time-block strong {
        color: #ffffff;
        font-size: 0.9rem;
        font-weight: 850;
    }

    .public-schedule-time-block small {
        color: rgba(255, 255, 255, 0.42);
        font-size: 0.62rem;
    }

    .public-schedule-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 8px;
        color: #86EFAC;
        font-size: 0.6rem;
        font-weight: 850;
        border: 1px solid rgba(74, 222, 128, 0.20);
        border-radius: 999px;
        background: rgba(34, 197, 94, 0.08);
    }

    .public-schedule-status span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #22C55E;
        box-shadow: 0 0 10px rgba(34, 197, 94, 0.75);
    }

    .public-schedule-subject-icon {
        display: grid;
        width: 44px;
        height: 44px;
        margin-bottom: 11px;
        place-items: center;
        color: #C4B5FD;
        font-size: 1.08rem;
        border-radius: 10px;
        background: rgba(124, 58, 237, 0.14);
    }

    .public-schedule-card-label {
        margin-bottom: 4px;
        color: #818CF8;
        font-size: 0.6rem;
        font-weight: 850;
        letter-spacing: 0.10em;
        text-transform: uppercase;
    }

    .public-schedule-card h3 {
        margin: 0 0 9px;
        color: #ffffff;
        font-size: 1.08rem;
        font-weight: 850;
    }

    .public-schedule-path {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 9px;
    }

    .public-schedule-path > span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 7px;
        color: rgba(255, 255, 255, 0.66);
        font-size: 0.67rem;
        border-radius: 9px;
        background: rgba(148, 163, 184, 0.07);
    }

    .public-schedule-path > span i {
        color: #93C5FD;
    }

    .public-schedule-path-arrow {
        color: rgba(255, 255, 255, 0.26);
        font-size: 0.6rem;
    }

    .public-schedule-teacher {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 11px;
        color: rgba(255, 255, 255, 0.48);
        font-size: 0.76rem;
    }

    .public-schedule-teacher i {
        color: #A78BFA;
    }

    .public-schedule-card-footer {
        display: flex;
        align-items: flex-start;
        flex-direction: column;
        gap: 6px;
        padding-top: 10px;
        margin-top: auto;
        color: rgba(255, 255, 255, 0.42);
        font-size: 0.64rem;
        border-top: 1px solid rgba(148, 163, 184, 0.10);
    }

    .public-schedule-card-footer span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .public-schedule-card-footer i {
        color: #FCD34D;
    }

    .public-schedule-empty {
        padding: 42px 20px;
        text-align: center;
        border: 1px dashed rgba(148, 163, 184, 0.22);
        border-radius: 26px;
        background: rgba(15, 23, 42, 0.60);
    }

    .public-schedule-empty-icon {
        display: grid;
        width: 62px;
        height: 62px;
        margin: 0 auto 14px;
        place-items: center;
        color: #A78BFA;
        font-size: 1.3rem;
        border-radius: 17px;
        background: rgba(124, 58, 237, 0.14);
    }

    .public-schedule-empty h2 {
        margin: 0 0 8px;
        color: #ffffff;
        font-size: 1.3rem;
        font-weight: 850;
    }

    .public-schedule-empty p {
        max-width: 540px;
        margin: 0 auto;
        color: rgba(255, 255, 255, 0.48);
        line-height: 1.7;
    }

    .public-schedule-empty a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 11px 16px;
        margin-top: 22px;
        color: #ffffff;
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
        border-radius: 999px;
        background: linear-gradient(135deg, #2563EB, #7C3AED);
    }

    html.light-mode .public-schedule-page {
        color: #172033;
    }

    html.light-mode .public-schedule-hero-card {
        border-color: #E2E8F0;
        background:
            radial-gradient(circle at 90% 12%, rgba(124, 58, 237, 0.12), transparent 34%),
            radial-gradient(circle at 65% 95%, rgba(37, 99, 235, 0.10), transparent 36%),
            linear-gradient(135deg, #FFFFFF, #F8FAFC);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.10);
    }

    html.light-mode .public-schedule-hero h1,
    html.light-mode .public-schedule-stat-card strong,
    html.light-mode .public-schedule-day-header h2,
    html.light-mode .public-schedule-card h3,
    html.light-mode .public-schedule-empty h2 {
        color: #172033;
    }

    html.light-mode .public-schedule-hero p,
    html.light-mode .public-schedule-next small,
    html.light-mode .public-schedule-stat-card small,
    html.light-mode .public-schedule-time-block small,
    html.light-mode .public-schedule-teacher,
    html.light-mode .public-schedule-card-footer,
    html.light-mode .public-schedule-empty p {
        color: #64748B;
    }

    html.light-mode .public-schedule-next strong,
    html.light-mode .public-schedule-time-block strong {
        color: #1E293B;
    }

    html.light-mode .public-schedule-stat-card,
    html.light-mode .public-schedule-day,
    html.light-mode .public-schedule-card,
    html.light-mode .public-schedule-empty {
        border-color: #E2E8F0;
        background: #FFFFFF;
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.07);
    }

    html.light-mode .public-schedule-day-header,
    html.light-mode .public-schedule-card-footer {
        border-color: #EEF2F7;
    }

    html.light-mode .public-schedule-path > span {
        color: #475569;
        background: #F1F5F9;
    }

    @media (max-width: 980px) {
        .public-schedule-hero-card {
            min-height: 185px;
            padding: 24px 26px;
        }

        .public-schedule-hero-visual {
            flex-basis: 150px;
            width: 96px;
            height: 96px;
        }

        .public-schedule-calendar-icon {
            width: 76px;
            height: 76px;
            font-size: 2.15rem;
        }

        .public-schedule-hero-ring {
            width: 104px;
            height: 104px;
        }

        .public-schedule-hero-ring-small {
            width: 86px;
            height: 86px;
        }

        .public-schedule-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .public-schedule-hero {
            padding-top: 20px;
        }

        .public-schedule-hero-card {
            min-height: 0;
            padding: 22px 20px;
        }

        .public-schedule-hero-visual {
            display: none;
        }

        .public-schedule-stats {
            grid-template-columns: 1fr;
        }

        .public-schedule-stat-card {
            min-height: 62px;
        }

        .public-schedule-day {
            padding: 15px 13px;
        }

        .public-schedule-day-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .public-schedule-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 520px) {
        .public-schedule-container {
            width: min(100% - 24px, 1180px);
        }

        .public-schedule-hero-card {
            padding: 28px 20px;
            border-radius: 22px;
        }

        .public-schedule-hero h1 {
            font-size: 1.3rem;
        }

        .public-schedule-hero p {
            font-size: 0.9rem;
        }

        .public-schedule-next {
            align-items: flex-start;
            width: 100%;
        }

        .public-schedule-day-heading {
            align-items: flex-start;
        }

        .public-schedule-day-icon {
            width: 48px;
            height: 48px;
            flex-basis: 48px;
        }

        .public-schedule-card-top {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .public-schedule-hero-ring {
            animation: none;
        }

        .public-schedule-card,
        .public-schedule-stat-card {
            transition: none;
        }
    }
</style>

<style>
    /* Renfort compact pour la page publique du planning */
    .public-schedule-day-header h2 {
        font-size: 1.2rem;
    }

    .public-schedule-card {
        min-height: 0;
    }

    .public-schedule-card-footer {
        line-height: 1.35;
    }

    @media (min-width: 1100px) {
        .public-schedule-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 1099.98px) {
        .public-schedule-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 620px) {
        .public-schedule-grid {
            grid-template-columns: 1fr;
        }

        .public-schedule-card-top {
            align-items: center;
            flex-direction: row;
        }

        .public-schedule-count {
            padding: 6px 9px;
        }
    }

    @media (max-width: 640px) {
        .public-schedule-single-row .public-schedule-card {
            flex-basis: min(290px, calc(100vw - 64px));
            width: min(290px, calc(100vw - 64px));
            min-width: min(290px, calc(100vw - 64px));
        }

        .public-schedule-day-header {
            align-items: flex-start;
            flex-direction: column;
        }
    }

</style>

@endpush

{{-- Design global V12 : présentation uniquement, aucun contenu modifié. --}}
@push('scripts')
<link
    rel="stylesheet"
    href="{{ asset('css/front-design-v12.css?v=12.0') }}"
>
@endpush