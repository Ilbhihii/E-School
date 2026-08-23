@extends('layouts.front')

@section('title', ($selectedSchedule['subject'] ?? 'Cours') . ' — Planning Smart School Academy')

@section('content')
@php
    $subjectName = trim((string) ($selectedSchedule['subject'] ?? 'Cours'));
    $normalizedSubject = mb_strtolower($subjectName);

    $theme = 'indigo';
    $subjectIcon = 'bi-book-half';

    if (mb_stripos($normalizedSubject, 'arabe') !== false) {
        $theme = 'blue';
        $subjectIcon = 'bi-translate';
    } elseif (mb_stripos($normalizedSubject, 'coran') !== false) {
        $theme = 'purple';
        $subjectIcon = 'bi-book';
    } elseif (
        mb_stripos($normalizedSubject, 'soutien') !== false
        || mb_stripos($normalizedSubject, 'math') !== false
        || mb_stripos($normalizedSubject, 'physique') !== false
    ) {
        $theme = 'amber';
        $subjectIcon = 'bi-mortarboard-fill';
    } elseif (mb_stripos($normalizedSubject, 'informatique') !== false) {
        $theme = 'cyan';
        $subjectIcon = 'bi-laptop';
    }

    $modeLabel = match ($admissionMode) {
        'contact' => 'Prise en contact',
        'vocal_test' => 'Test vocal',
        default => 'Accès au parcours',
    };

    $modeIcon = match ($admissionMode) {
        'contact' => 'bi-calendar-check-fill',
        'vocal_test' => 'bi-mic-fill',
        default => 'bi-box-arrow-in-right',
    };
@endphp

<div class="schedule-detail-page schedule-theme-{{ $theme }}">
    <div class="schedule-detail-shell">
        <div class="schedule-detail-back-row">
            <a href="{{ route('public.schedule.index') }}" class="schedule-detail-back">
                <i class="bi bi-arrow-left"></i>
                Retour au planning
            </a>

            <button type="button" class="schedule-detail-share" id="shareScheduleButton">
                <i class="bi bi-share-fill"></i>
                Partager
            </button>
        </div>

        <section class="schedule-detail-hero">
            <div class="schedule-flyer-wrap">
                <div class="schedule-flyer">
                    <div class="schedule-flyer-grid"></div>
                    <div class="schedule-flyer-glow"></div>

                    <div class="schedule-flyer-top">
                        <span class="schedule-flyer-brand">
                            <i class="bi bi-mortarboard-fill"></i>
                            Smart School Academy
                        </span>

                        <span class="schedule-flyer-published">
                            <span></span>
                            Publié
                        </span>
                    </div>

                    <div class="schedule-flyer-icon">
                        <i class="bi {{ $subjectIcon }}"></i>
                    </div>

                    <div class="schedule-flyer-content">
                        <span class="schedule-flyer-kicker">Cours en ligne</span>
                        <h1>{{ $subjectName }}</h1>

                        <div class="schedule-flyer-path">
                            <strong>{{ $selectedSchedule['level'] ?? 'Niveau' }}</strong>
                            <span>•</span>
                            <strong>{{ $selectedSchedule['class_name'] ?? 'Classe' }}</strong>
                        </div>

                        @if(!empty($selectedSchedule['slot_code']))
                            <span class="schedule-flyer-slot">
                                Créneau {{ $selectedSchedule['slot_code'] }}
                            </span>
                        @endif
                    </div>

                    <div class="schedule-flyer-time">
                        <div>
                            <small>Jour</small>
                            <strong>{{ $selectedSchedule['day_label'] ?? '-' }}</strong>
                        </div>

                        <div>
                            <small>Horaire</small>
                            <strong>{{ $selectedSchedule['time_label'] ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="schedule-detail-info">
                <span class="schedule-detail-kicker">
                    <i class="bi bi-stars"></i>
                    Fiche du cours
                </span>

                <h2>{{ $subjectName }}</h2>
                <p>
                    Consultez les informations du groupe, choisissez le créneau qui vous convient,
                    puis poursuivez avec le mode d'inscription prévu pour cette classe.
                </p>

                <div class="schedule-detail-facts">
                    <div class="schedule-detail-fact">
                        <span><i class="bi bi-diagram-3-fill"></i></span>
                        <div>
                            <small>Parcours</small>
                            <strong>{{ $selectedSchedule['level'] ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="schedule-detail-fact">
                        <span><i class="bi bi-people-fill"></i></span>
                        <div>
                            <small>Classe</small>
                            <strong>{{ $selectedSchedule['class_name'] ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="schedule-detail-fact">
                        <span><i class="bi bi-clock-fill"></i></span>
                        <div>
                            <small>Durée</small>
                            <strong>{{ $selectedSchedule['duration_label'] ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="schedule-detail-fact">
                        <span><i class="bi bi-arrow-repeat"></i></span>
                        <div>
                            <small>Fréquence</small>
                            <strong>{{ $selectedSchedule['recurrence_label'] ?? 'Chaque semaine' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="schedule-detail-mode">
                    <span class="schedule-detail-mode-icon">
                        <i class="bi {{ $modeIcon }}"></i>
                    </span>
                    <div>
                        <small>Mode d'accès</small>
                        <strong>{{ $modeLabel }}</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="schedule-real-flyer-section">
            <div class="schedule-real-flyer-media">
                <a
                    href="{{ asset('images/flyers/smart-school-academy-flyer.png') }}"
                    target="_blank"
                    rel="noopener"
                    class="schedule-real-flyer-image-link"
                    aria-label="Voir le flyer Smart School Academy en grand"
                >
                    <img
                        src="{{ asset('images/flyers/smart-school-academy-flyer.png') }}"
                        alt="Flyer Smart School Academy"
                        class="schedule-real-flyer-image"
                        loading="lazy"
                    >
                    <span class="schedule-real-flyer-zoom">
                        <i class="bi bi-arrows-fullscreen"></i>
                        Voir en grand
                    </span>
                </a>
            </div>

            <div class="schedule-real-flyer-copy">
                <span class="schedule-section-kicker">
                    <i class="bi bi-image-fill"></i>
                    Flyer Smart School Academy
                </span>

                <h2>Découvrez le programme complet</h2>

                <p>
                    Retrouvez sur le flyer les cours proposés, les horaires,
                    les offres et les informations de contact de Smart School Academy.
                </p>

                <div class="schedule-real-flyer-current">
                    <span>
                        <i class="bi {{ $subjectIcon }}"></i>
                    </span>
                    <div>
                        <small>Vous consultez actuellement</small>
                        <strong>{{ $subjectName }}</strong>
                        <p>
                            {{ $selectedSchedule['level'] ?? '-' }}
                            · {{ $selectedSchedule['class_name'] ?? '-' }}
                            @if(!empty($selectedSchedule['slot_code']))
                                · Créneau {{ $selectedSchedule['slot_code'] }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="schedule-real-flyer-actions">
                    <a
                        href="{{ asset('images/flyers/smart-school-academy-flyer.png') }}"
                        target="_blank"
                        rel="noopener"
                        class="schedule-real-flyer-button schedule-real-flyer-button-primary"
                    >
                        <i class="bi bi-arrows-fullscreen"></i>
                        Voir le flyer en grand
                    </a>

                    <a
                        href="{{ asset('images/flyers/smart-school-academy-flyer.png') }}"
                        download="flyer-smart-school-academy.png"
                        class="schedule-real-flyer-button"
                    >
                        <i class="bi bi-download"></i>
                        Télécharger le flyer
                    </a>
                </div>
            </div>
        </section>

        <section class="schedule-slots-section">
            <header class="schedule-section-header">
                <div>
                    <span class="schedule-section-kicker">Disponibilités</span>
                    <h2>Créneaux disponibles</h2>
                    <p>
                        Choisissez une séance publiée pour ce même parcours et cette même classe.
                    </p>
                </div>

                <span class="schedule-slots-count">
                    <i class="bi bi-calendar2-week"></i>
                    {{ $availableSchedules->count() }}
                    {{ $availableSchedules->count() > 1 ? 'créneaux' : 'créneau' }}
                </span>
            </header>

            <div class="schedule-slots-grid">
                @foreach($availableSchedules as $item)
                    @php
                        $isSelected = (int) ($item['schedule_id'] ?? 0) === (int) $schedule->id;
                    @endphp

                    <a
                        href="{{ route('public.schedule.index', ['schedule' => $item['schedule_id']]) }}"
                        class="schedule-slot-card {{ $isSelected ? 'is-selected' : '' }}"
                    >
                        <div class="schedule-slot-card-top">
                            <span class="schedule-slot-day">
                                {{ $item['day_label'] ?? '-' }}
                            </span>

                            @if($isSelected)
                                <span class="schedule-slot-selected">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Sélectionné
                                </span>
                            @else
                                <span class="schedule-slot-choose">
                                    Choisir
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            @endif
                        </div>

                        <strong class="schedule-slot-time">
                            {{ $item['time_label'] ?? '-' }}
                        </strong>

                        <div class="schedule-slot-meta">
                            @if(!empty($item['slot_code']))
                                <span>
                                    <i class="bi bi-grid-3x3-gap-fill"></i>
                                    {{ $item['slot_code'] }}
                                </span>
                            @endif

                            <span>
                                <i class="bi bi-hourglass-split"></i>
                                {{ $item['duration_label'] ?? '-' }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
    .schedule-detail-page {
        --detail-accent: #6366F1;
        --detail-accent-2: #8B5CF6;
        --detail-soft: rgba(99, 102, 241, .14);
        min-height: 70vh;
        padding: 30px 0 56px;
        color: #F8FAFC;
        background:
            radial-gradient(circle at 13% 10%, rgba(59, 130, 246, .10), transparent 27%),
            radial-gradient(circle at 85% 18%, rgba(124, 58, 237, .12), transparent 30%);
    }

    .schedule-theme-blue {
        --detail-accent: #2563EB;
        --detail-accent-2: #06B6D4;
        --detail-soft: rgba(37, 99, 235, .14);
    }

    .schedule-theme-purple {
        --detail-accent: #7C3AED;
        --detail-accent-2: #A855F7;
        --detail-soft: rgba(124, 58, 237, .14);
    }

    .schedule-theme-amber {
        --detail-accent: #F59E0B;
        --detail-accent-2: #F97316;
        --detail-soft: rgba(245, 158, 11, .13);
    }

    .schedule-theme-cyan {
        --detail-accent: #0891B2;
        --detail-accent-2: #2563EB;
        --detail-soft: rgba(8, 145, 178, .14);
    }

    .schedule-detail-shell {
        width: min(1160px, calc(100% - 32px));
        margin: 0 auto;
    }

    .schedule-detail-back-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .schedule-detail-back,
    .schedule-detail-share {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        gap: 8px;
        padding: 0 13px;
        color: rgba(255,255,255,.72);
        border: 1px solid rgba(148,163,184,.14);
        border-radius: 12px;
        background: rgba(15,23,42,.60);
        font-size: .75rem;
        font-weight: 800;
        text-decoration: none;
        transition: .2s ease;
    }

    .schedule-detail-share {
        cursor: pointer;
    }

    .schedule-detail-back:hover,
    .schedule-detail-share:hover {
        color: #fff;
        border-color: color-mix(in srgb, var(--detail-accent) 45%, transparent);
        transform: translateY(-2px);
    }

    .schedule-detail-hero {
        display: grid;
        grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr);
        gap: 18px;
        align-items: stretch;
    }

    .schedule-flyer-wrap,
    .schedule-detail-info,
    .schedule-real-flyer-section,
    .schedule-slots-section,
    .schedule-detail-cta {
        border: 1px solid rgba(148,163,184,.14);
        border-radius: 22px;
        background: rgba(8,17,31,.86);
        box-shadow: 0 24px 60px rgba(0,0,0,.20);
        backdrop-filter: blur(14px);
    }

    .schedule-flyer-wrap {
        padding: 14px;
    }

    .schedule-flyer {
        position: relative;
        display: flex;
        min-height: 430px;
        flex-direction: column;
        overflow: hidden;
        padding: 24px;
        border-radius: 17px;
        background:
            radial-gradient(circle at 90% 10%, color-mix(in srgb, var(--detail-accent-2) 42%, transparent), transparent 34%),
            linear-gradient(145deg, #17223A, #091321 78%);
    }

    .schedule-flyer-grid {
        position: absolute;
        inset: 0;
        opacity: .32;
        background-image:
            linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
        background-size: 30px 30px;
        mask-image: linear-gradient(135deg, #000, transparent 78%);
    }

    .schedule-flyer-glow {
        position: absolute;
        right: -80px;
        bottom: -80px;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: color-mix(in srgb, var(--detail-accent) 24%, transparent);
        filter: blur(12px);
    }

    .schedule-flyer-top,
    .schedule-flyer-content,
    .schedule-flyer-time,
    .schedule-flyer-icon {
        position: relative;
        z-index: 2;
    }

    .schedule-flyer-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .schedule-flyer-brand,
    .schedule-flyer-published {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: .67rem;
        font-weight: 850;
    }

    .schedule-flyer-brand {
        color: #F8FAFC;
    }

    .schedule-flyer-brand i {
        color: #FCD34D;
    }

    .schedule-flyer-published {
        color: #86EFAC;
    }

    .schedule-flyer-published span {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #22C55E;
        box-shadow: 0 0 12px rgba(34,197,94,.65);
    }

    .schedule-flyer-icon {
        display: grid;
        width: 62px;
        height: 62px;
        place-items: center;
        margin-top: 46px;
        color: #fff;
        font-size: 1.8rem;
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 18px;
        background: linear-gradient(135deg, var(--detail-accent), var(--detail-accent-2));
        box-shadow: 0 18px 35px color-mix(in srgb, var(--detail-accent) 25%, transparent);
    }

    .schedule-flyer-content {
        margin-top: 20px;
    }

    .schedule-flyer-kicker {
        color: #C7D2FE;
        font-size: .68rem;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .schedule-flyer h1 {
        margin: 7px 0 8px;
        color: #fff;
        font-family: 'Poppins', sans-serif;
        font-size: clamp(2rem, 5vw, 3.2rem);
        font-weight: 900;
        line-height: 1.02;
        letter-spacing: -.05em;
    }

    .schedule-flyer-path {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        color: rgba(255,255,255,.68);
        font-size: .82rem;
    }

    .schedule-flyer-slot {
        display: inline-flex;
        padding: 6px 10px;
        margin-top: 14px;
        color: #fff;
        border: 1px solid color-mix(in srgb, var(--detail-accent) 40%, transparent);
        border-radius: 999px;
        background: var(--detail-soft);
        font-size: .7rem;
        font-weight: 850;
    }

    .schedule-flyer-time {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: auto;
        padding-top: 28px;
    }

    .schedule-flyer-time > div {
        padding: 12px;
        border: 1px solid rgba(255,255,255,.10);
        border-radius: 13px;
        background: rgba(255,255,255,.05);
    }

    .schedule-flyer-time small,
    .schedule-detail-fact small,
    .schedule-detail-mode small,
    .schedule-detail-cta small {
        display: block;
        margin-bottom: 4px;
        color: rgba(255,255,255,.44);
        font-size: .64rem;
        font-weight: 750;
    }

    .schedule-flyer-time strong {
        color: #fff;
        font-size: .84rem;
    }

    .schedule-detail-info {
        padding: 28px;
    }

    .schedule-detail-kicker,
    .schedule-section-kicker {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #A5B4FC;
        font-size: .66rem;
        font-weight: 900;
        letter-spacing: .11em;
        text-transform: uppercase;
    }

    .schedule-detail-info h2,
    .schedule-section-header h2,
    .schedule-detail-cta h2 {
        margin: 8px 0;
        color: #fff;
        font-family: 'Poppins', sans-serif;
        font-weight: 850;
        letter-spacing: -.035em;
    }

    .schedule-detail-info > p,
    .schedule-section-header p,
    .schedule-detail-cta p {
        margin: 0;
        color: rgba(255,255,255,.52);
        font-size: .79rem;
        line-height: 1.7;
    }

    .schedule-detail-facts {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 10px;
        margin-top: 24px;
    }

    .schedule-detail-fact {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 13px;
        border: 1px solid rgba(148,163,184,.11);
        border-radius: 14px;
        background: rgba(15,23,42,.58);
    }

    .schedule-detail-fact > span,
    .schedule-detail-mode-icon,
    .schedule-detail-cta-icon {
        display: grid;
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        place-items: center;
        color: #fff;
        border-radius: 11px;
        background: linear-gradient(135deg, var(--detail-accent), var(--detail-accent-2));
    }

    .schedule-detail-fact strong,
    .schedule-detail-mode strong {
        color: #F8FAFC;
        font-size: .77rem;
    }

    .schedule-detail-mode {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        margin-top: 12px;
        border: 1px solid color-mix(in srgb, var(--detail-accent) 28%, transparent);
        border-radius: 15px;
        background: var(--detail-soft);
    }



    .schedule-real-flyer-section {
        display: grid;
        grid-template-columns: minmax(280px, .72fr) minmax(0, 1.28fr);
        gap: 22px;
        align-items: center;
        padding: 20px;
        margin-top: 18px;
        overflow: hidden;
        background:
            radial-gradient(circle at 12% 18%, var(--detail-soft), transparent 34%),
            rgba(8,17,31,.90);
    }

    .schedule-real-flyer-media {
        min-width: 0;
    }

    .schedule-real-flyer-image-link {
        position: relative;
        display: block;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 18px;
        background: rgba(255,255,255,.035);
        box-shadow: 0 22px 50px rgba(0,0,0,.30);
    }

    .schedule-real-flyer-image {
        display: block;
        width: 100%;
        height: auto;
        max-height: 690px;
        object-fit: contain;
        background: #fff;
        transition: transform .28s ease, filter .28s ease;
    }

    .schedule-real-flyer-image-link:hover .schedule-real-flyer-image {
        transform: scale(1.015);
        filter: brightness(.86);
    }

    .schedule-real-flyer-zoom {
        position: absolute;
        right: 14px;
        bottom: 14px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 12px;
        color: #fff;
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 11px;
        background: rgba(2,6,23,.80);
        backdrop-filter: blur(10px);
        font-size: .68rem;
        font-weight: 850;
        opacity: .94;
    }

    .schedule-real-flyer-copy {
        padding: 10px 14px 10px 2px;
    }

    .schedule-real-flyer-copy h2 {
        margin: 8px 0;
        color: #fff;
        font-family: 'Poppins', sans-serif;
        font-size: clamp(1.45rem, 3vw, 2rem);
        font-weight: 850;
        letter-spacing: -.035em;
    }

    .schedule-real-flyer-copy > p {
        max-width: 650px;
        margin: 0;
        color: rgba(255,255,255,.55);
        font-size: .82rem;
        line-height: 1.75;
    }

    .schedule-real-flyer-current {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 14px;
        margin-top: 20px;
        border: 1px solid color-mix(in srgb, var(--detail-accent) 30%, transparent);
        border-radius: 15px;
        background: var(--detail-soft);
    }

    .schedule-real-flyer-current > span {
        display: grid;
        flex: 0 0 44px;
        width: 44px;
        height: 44px;
        place-items: center;
        color: #fff;
        border-radius: 13px;
        background: linear-gradient(135deg, var(--detail-accent), var(--detail-accent-2));
    }

    .schedule-real-flyer-current small {
        display: block;
        margin-bottom: 2px;
        color: rgba(255,255,255,.45);
        font-size: .64rem;
        font-weight: 750;
    }

    .schedule-real-flyer-current strong {
        display: block;
        color: #fff;
        font-size: .91rem;
    }

    .schedule-real-flyer-current p {
        margin: 3px 0 0;
        color: rgba(255,255,255,.62);
        font-size: .71rem;
    }

    .schedule-real-flyer-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .schedule-real-flyer-button {
        display: inline-flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 15px;
        color: #E2E8F0;
        border: 1px solid rgba(148,163,184,.16);
        border-radius: 12px;
        background: rgba(15,23,42,.66);
        font-size: .71rem;
        font-weight: 850;
        text-decoration: none;
        transition: .2s ease;
    }

    .schedule-real-flyer-button:hover {
        color: #fff;
        border-color: color-mix(in srgb, var(--detail-accent) 45%, transparent);
        transform: translateY(-2px);
    }

    .schedule-real-flyer-button-primary {
        color: #fff;
        border-color: transparent;
        background: linear-gradient(135deg, var(--detail-accent), var(--detail-accent-2));
        box-shadow: 0 12px 28px color-mix(in srgb, var(--detail-accent) 19%, transparent);
    }

    .schedule-slots-section {
        padding: 22px;
        margin-top: 18px;
    }

    .schedule-section-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 16px;
    }

    .schedule-section-header h2 {
        margin-bottom: 4px;
        font-size: 1.35rem;
    }

    .schedule-slots-count {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 11px;
        color: rgba(255,255,255,.68);
        border: 1px solid rgba(148,163,184,.13);
        border-radius: 999px;
        background: rgba(148,163,184,.06);
        font-size: .68rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .schedule-slots-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0,1fr));
        gap: 10px;
    }

    .schedule-slot-card {
        display: block;
        padding: 14px;
        color: inherit;
        border: 1px solid rgba(148,163,184,.12);
        border-radius: 15px;
        background: rgba(15,23,42,.62);
        text-decoration: none;
        transition: .2s ease;
    }

    .schedule-slot-card:hover {
        border-color: color-mix(in srgb, var(--detail-accent) 38%, transparent);
        transform: translateY(-3px);
    }

    .schedule-slot-card.is-selected {
        border-color: color-mix(in srgb, var(--detail-accent) 55%, transparent);
        background: var(--detail-soft);
        box-shadow: 0 12px 30px color-mix(in srgb, var(--detail-accent) 10%, transparent);
    }

    .schedule-slot-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .schedule-slot-day {
        color: #E2E8F0;
        font-size: .72rem;
        font-weight: 850;
    }

    .schedule-slot-selected,
    .schedule-slot-choose {
        color: #86EFAC;
        font-size: .61rem;
        font-weight: 850;
    }

    .schedule-slot-choose {
        color: #A5B4FC;
    }

    .schedule-slot-time {
        display: block;
        margin-top: 12px;
        color: #fff;
        font-size: 1.05rem;
        font-weight: 900;
    }

    .schedule-slot-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 10px;
    }

    .schedule-slot-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        color: rgba(255,255,255,.55);
        border: 1px solid rgba(148,163,184,.10);
        border-radius: 999px;
        font-size: .61rem;
    }

    .schedule-detail-cta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 22px;
        margin-top: 18px;
        background:
            radial-gradient(circle at 85% 15%, var(--detail-soft), transparent 36%),
            rgba(8,17,31,.90);
    }

    .schedule-detail-cta-copy {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .schedule-detail-cta h2 {
        margin: 1px 0 3px;
        font-size: 1.15rem;
    }

    .schedule-detail-primary-action {
        display: inline-flex;
        min-height: 48px;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 0 18px;
        color: #fff;
        border-radius: 13px;
        background: linear-gradient(135deg, var(--detail-accent), var(--detail-accent-2));
        box-shadow: 0 16px 34px color-mix(in srgb, var(--detail-accent) 22%, transparent);
        font-size: .76rem;
        font-weight: 900;
        text-decoration: none;
        transition: .2s ease;
        white-space: nowrap;
    }

    .schedule-detail-primary-action:hover {
        color: #fff;
        transform: translateY(-3px);
        filter: brightness(1.08);
    }

    @media (max-width: 900px) {
        .schedule-detail-hero,
        .schedule-real-flyer-section {
            grid-template-columns: 1fr;
        }

        .schedule-real-flyer-copy {
            padding: 4px 2px 2px;
        }

        .schedule-real-flyer-image {
            max-height: none;
        }

        .schedule-slots-grid {
            grid-template-columns: repeat(2, minmax(0,1fr));
        }

        .schedule-detail-cta {
            align-items: stretch;
            flex-direction: column;
        }

        .schedule-detail-primary-action {
            width: 100%;
        }
    }

    @media (max-width: 620px) {
        .schedule-detail-page {
            padding-top: 18px;
        }

        .schedule-detail-shell {
            width: min(100% - 22px, 1160px);
        }

        .schedule-detail-back-row,
        .schedule-section-header {
            align-items: stretch;
            flex-direction: column;
        }

        .schedule-detail-share {
            justify-content: center;
        }

        .schedule-flyer {
            min-height: 390px;
            padding: 19px;
        }

        .schedule-flyer-top {
            align-items: flex-start;
            flex-direction: column;
        }

        .schedule-flyer-icon {
            margin-top: 28px;
        }

        .schedule-flyer-time,
        .schedule-detail-facts,
        .schedule-slots-grid {
            grid-template-columns: 1fr;
        }

        .schedule-detail-info,
        .schedule-real-flyer-section,
        .schedule-slots-section,
        .schedule-detail-cta {
            padding: 17px;
        }

        .schedule-real-flyer-actions {
            flex-direction: column;
        }

        .schedule-real-flyer-button {
            width: 100%;
        }

        .schedule-detail-cta-copy {
            align-items: flex-start;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const button = document.getElementById('shareScheduleButton');
    if (!button) return;

    button.addEventListener('click', async function () {
        const shareData = {
            title: @json($subjectName . ' — Smart School Academy'),
            text: @json(($selectedSchedule['level'] ?? '') . ' · ' . ($selectedSchedule['class_name'] ?? '') . ' · ' . ($selectedSchedule['time_label'] ?? '')),
            url: window.location.href
        };

        try {
            if (navigator.share) {
                await navigator.share(shareData);
                return;
            }

            await navigator.clipboard.writeText(window.location.href);
            const original = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check-circle-fill"></i> Lien copié';
            setTimeout(function () {
                button.innerHTML = original;
            }, 1800);
        } catch (error) {
            // L'utilisateur peut fermer la fenêtre de partage : aucune erreur à afficher.
        }
    });
})();
</script>
@endpush
