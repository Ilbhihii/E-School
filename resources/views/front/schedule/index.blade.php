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
            @forelse($days as $dayNumber => $daySchedules)
                <section class="public-schedule-day">
                    <header class="public-schedule-day-header">
                        <div class="public-schedule-day-heading">
                            <div class="public-schedule-day-icon">
                                <i class="bi bi-calendar-event"></i>
                            </div>

                            <div>
                                <span class="public-schedule-day-kicker">Programme du jour</span>
                            </div>
                        </div>

                        <span class="public-schedule-count">
                            <i class="bi bi-book"></i>
                            {{ $daySchedules->count() }}
                            {{ $daySchedules->count() > 1 ? 'cours planifiés' : 'cours planifié' }}
                        </span>
                    </header>

                    <div class="public-schedule-grid">
                        @foreach($daySchedules as $schedule)
                            @php
                                $subjectName = mb_strtolower($schedule['subject'] ?? '');
                                $subjectIcon = 'bi-book-half';

                                if (mb_stripos($subjectName, 'arabe') !== false) {
                                    $subjectIcon = 'bi-translate';
                                } elseif (mb_stripos($subjectName, 'coran') !== false) {
                                    $subjectIcon = 'bi-book';
                                } elseif (mb_stripos($subjectName, 'math') !== false) {
                                    $subjectIcon = 'bi-calculator';
                                } elseif (mb_stripos($subjectName, 'informatique') !== false) {
                                    $subjectIcon = 'bi-laptop';
                                } elseif (mb_stripos($subjectName, 'français') !== false) {
                                    $subjectIcon = 'bi-chat-square-text';
                                }
                            @endphp

                            <article class="public-schedule-card">
                                <div class="public-schedule-card-accent"></div>

                                <div class="public-schedule-card-top">
                                    <div class="public-schedule-time-block">
                                        <span class="public-schedule-time-icon">
                                            <i class="bi bi-clock-fill"></i>
                                        </span>
                                        <div>
                                            <strong>{{ $schedule['start_label'] }}</strong>
                                            <small>{{ $schedule['duration_label'] }}</small>
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

                                <span class="public-schedule-card-label">Matière</span>
                                <h3>{{ $schedule['subject'] }}</h3>

                                <div class="public-schedule-path">
                                    <span>
                                        <i class="bi bi-bar-chart-fill"></i>
                                        {{ $schedule['level'] }}
                                    </span>
                                    <i class="bi bi-chevron-right public-schedule-path-arrow"></i>
                                    <span>
                                        <i class="bi bi-people-fill"></i>
                                        {{ $schedule['class_name'] }}
                                    </span>
                                </div>

                                <footer class="public-schedule-card-footer">
                                    <span>
                                        <i class="bi bi-arrow-repeat"></i>
                                        {{ $schedule['recurrence_label'] }}
                                    </span>

                                    <span>
                                        <i class="bi bi-clock-history"></i>
                                        {{ $schedule['time_label'] }}
                                    </span>

                                    @if(!empty($schedule['date_label']))
                                        <span>
                                            <i class="bi bi-calendar-check"></i>
                                            {{ $schedule['date_label'] }}
                                        </span>
                                    @endif
                                </footer>
                            </article>
                        @endforeach
                    </div>
                </section>
            @empty
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
            @endforelse
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .public-schedule-page {
        position: relative;
        min-height: 72vh;
        color: #ffffff;
        overflow: hidden;
    }

    .public-schedule-container {
        width: min(1180px, calc(100% - 36px));
        margin: 0 auto;
    }

    .public-schedule-hero {
        position: relative;
        padding: 52px 0 24px;
    }

    .public-schedule-hero-card {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 330px;
        padding: 46px 50px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 30px;
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
        max-width: 760px;
    }

    .public-schedule-kicker {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 8px 13px;
        margin-bottom: 18px;
        color: #FFD166;
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
        border: 1px solid rgba(255, 209, 102, 0.22);
        border-radius: 999px;
        background: rgba(255, 209, 102, 0.08);
    }

    .public-schedule-hero h1 {
        max-width: 780px;
        margin: 0 0 16px;
        color: #ffffff;
        font-family: 'Poppins', sans-serif;
        font-size: clamp(2.2rem, 5vw, 4.35rem);
        font-weight: 850;
        line-height: 1.07;
        letter-spacing: -0.045em;
    }

    .public-schedule-hero p {
        max-width: 720px;
        margin: 0;
        color: rgba(255, 255, 255, 0.62);
        font-size: 1rem;
        line-height: 1.8;
    }

    .public-schedule-next {
        display: inline-flex;
        align-items: center;
        gap: 12px;
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
        border-radius: 12px;
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
        font-size: 0.69rem;
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
        flex: 0 0 190px;
        width: 190px;
        height: 190px;
        place-items: center;
    }

    .public-schedule-calendar-icon {
        position: relative;
        z-index: 3;
        display: grid;
        width: 124px;
        height: 124px;
        place-items: center;
        color: #FFD166;
        font-size: 3.6rem;
        border: 1px solid rgba(255, 209, 102, 0.24);
        border-radius: 34px;
        background: linear-gradient(145deg, rgba(255, 209, 102, 0.14), rgba(124, 58, 237, 0.12));
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        transform: rotate(4deg);
    }

    .public-schedule-hero-ring {
        position: absolute;
        width: 180px;
        height: 180px;
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
        width: 150px;
        height: 150px;
        animation-direction: reverse;
        animation-duration: 13s;
    }

    @keyframes publicScheduleSpin {
        to { transform: rotate(360deg); }
    }

    .public-schedule-content {
        padding: 10px 0 82px;
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
        flex: 0 0 54px;
        width: 54px;
        height: 54px;
        place-items: center;
        font-size: 1.25rem;
        border-radius: 16px;
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
        padding: 28px;
        margin-bottom: 28px;
        border: 1px solid rgba(148, 163, 184, 0.13);
        border-radius: 25px;
        background: rgba(8, 17, 31, 0.86);
        box-shadow: 0 22px 55px rgba(0, 0, 0, 0.19);
        backdrop-filter: blur(12px);
    }

    .public-schedule-day-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding-bottom: 20px;
        margin-bottom: 22px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.11);
    }

    .public-schedule-day-heading {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .public-schedule-day-icon {
        display: grid;
        flex: 0 0 54px;
        width: 54px;
        height: 54px;
        place-items: center;
        color: #93C5FD;
        font-size: 1.2rem;
        border: 1px solid rgba(96, 165, 250, 0.16);
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.22), rgba(124, 58, 237, 0.16));
    }

    .public-schedule-day-kicker {
        display: block;
        margin-bottom: 4px;
        color: #818CF8;
        font-size: 0.68rem;
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
        gap: 8px;
        padding: 9px 13px;
        color: rgba(255, 255, 255, 0.68);
        font-size: 0.75rem;
        font-weight: 750;
        border: 1px solid rgba(148, 163, 184, 0.13);
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.06);
    }

    .public-schedule-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .public-schedule-card {
        position: relative;
        display: flex;
        min-height: 100%;
        flex-direction: column;
        padding: 22px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.13);
        border-radius: 21px;
        background: linear-gradient(145deg, rgba(21, 33, 54, 0.94), rgba(10, 19, 34, 0.97));
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18);
        transition: transform 0.28s ease, border-color 0.28s ease, box-shadow 0.28s ease;
    }

    .public-schedule-card:hover {
        border-color: rgba(129, 140, 248, 0.38);
        box-shadow: 0 25px 55px rgba(0, 0, 0, 0.28);
        transform: translateY(-6px);
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
        gap: 12px;
        margin-bottom: 21px;
    }

    .public-schedule-time-block {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .public-schedule-time-icon {
        display: grid;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        place-items: center;
        color: #FCD34D;
        border-radius: 12px;
        background: rgba(245, 158, 11, 0.10);
    }

    .public-schedule-time-block div {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .public-schedule-time-block strong {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 850;
    }

    .public-schedule-time-block small {
        color: rgba(255, 255, 255, 0.42);
        font-size: 0.68rem;
    }

    .public-schedule-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 9px;
        color: #86EFAC;
        font-size: 0.64rem;
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
        width: 56px;
        height: 56px;
        margin-bottom: 17px;
        place-items: center;
        color: #C4B5FD;
        font-size: 1.35rem;
        border-radius: 16px;
        background: rgba(124, 58, 237, 0.14);
    }

    .public-schedule-card-label {
        margin-bottom: 6px;
        color: #818CF8;
        font-size: 0.65rem;
        font-weight: 850;
        letter-spacing: 0.10em;
        text-transform: uppercase;
    }

    .public-schedule-card h3 {
        margin: 0 0 14px;
        color: #ffffff;
        font-size: 1.35rem;
        font-weight: 850;
    }

    .public-schedule-path {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 14px;
    }

    .public-schedule-path > span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 9px;
        color: rgba(255, 255, 255, 0.66);
        font-size: 0.73rem;
        border-radius: 9px;
        background: rgba(148, 163, 184, 0.07);
    }

    .public-schedule-path > span i {
        color: #93C5FD;
    }

    .public-schedule-path-arrow {
        color: rgba(255, 255, 255, 0.26);
        font-size: 0.65rem;
    }

    .public-schedule-teacher {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 18px;
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
        gap: 8px;
        padding-top: 15px;
        margin-top: auto;
        color: rgba(255, 255, 255, 0.42);
        font-size: 0.69rem;
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
        padding: 64px 24px;
        text-align: center;
        border: 1px dashed rgba(148, 163, 184, 0.22);
        border-radius: 26px;
        background: rgba(15, 23, 42, 0.60);
    }

    .public-schedule-empty-icon {
        display: grid;
        width: 82px;
        height: 82px;
        margin: 0 auto 20px;
        place-items: center;
        color: #A78BFA;
        font-size: 2rem;
        border-radius: 23px;
        background: rgba(124, 58, 237, 0.14);
    }

    .public-schedule-empty h2 {
        margin: 0 0 8px;
        color: #ffffff;
        font-size: 1.55rem;
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
        gap: 8px;
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
            min-height: 300px;
            padding: 38px;
        }

        .public-schedule-hero-visual {
            flex-basis: 150px;
            width: 150px;
            height: 150px;
        }

        .public-schedule-calendar-icon {
            width: 105px;
            height: 105px;
            font-size: 3rem;
        }

        .public-schedule-hero-ring {
            width: 145px;
            height: 145px;
        }

        .public-schedule-hero-ring-small {
            width: 120px;
            height: 120px;
        }

        .public-schedule-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .public-schedule-hero {
            padding-top: 30px;
        }

        .public-schedule-hero-card {
            min-height: 0;
            padding: 32px 26px;
        }

        .public-schedule-hero-visual {
            display: none;
        }

        .public-schedule-stats {
            grid-template-columns: 1fr;
        }

        .public-schedule-stat-card {
            min-height: 82px;
        }

        .public-schedule-day {
            padding: 22px 18px;
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
            font-size: 2rem;
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
@endpush

{{-- Design global V12 : présentation uniquement, aucun contenu modifié. --}}
@push('scripts')
<link
    rel="stylesheet"
    href="{{ asset('css/front-design-v12.css?v=12.0') }}"
>
@endpush

