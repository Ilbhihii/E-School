@extends('layouts.front')

@section('title', 'Planning des cours — Smart School Academy')

@section('content')
@php
    /*
     * Planning public demandé à partir du flyer Smart School Academy.
     * Cette grille est volontairement indépendante du planning personnel
     * des étudiants connectés.
     */
    $flyerDays = [
        ['key' => 'lundi', 'label' => 'Lundi', 'note' => 'Adultes'],
        ['key' => 'mardi', 'label' => 'Mardi', 'note' => 'Adultes'],
        ['key' => 'mercredi', 'label' => 'Mercredi', 'note' => 'Enfants & Adultes'],
        ['key' => 'jeudi', 'label' => 'Jeudi', 'note' => ''],
        ['key' => 'vendredi', 'label' => 'Vendredi', 'note' => ''],
        ['key' => 'samedi', 'label' => 'Samedi', 'note' => ''],
        ['key' => 'dimanche', 'label' => 'Dimanche', 'note' => ''],
    ];

    $flyerRows = [
        [
            'time' => '10h–11h30',
            'note' => '',
            'cells' => [
                'lundi' => [
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'women', 'label' => 'Sœurs'],
                ],
                'mardi' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'women', 'label' => 'Sœurs'],
                ],
                'mercredi' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                ],
                'jeudi' => [],
                'vendredi' => [],
                'samedi' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'english', 'label' => 'Anglais'],
                ],
                'dimanche' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'english', 'label' => 'Anglais'],
                ],
            ],
        ],
        [
            'time' => '11h45–13h',
            'note' => '',
            'cells' => [
                'lundi' => [],
                'mardi' => [],
                'mercredi' => [
                    ['type' => 'coran', 'label' => 'Coran'],
                ],
                'jeudi' => [],
                'vendredi' => [],
                'samedi' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'english', 'label' => 'Anglais'],
                ],
                'dimanche' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'english', 'label' => 'Anglais'],
                ],
            ],
        ],
        [
            'time' => '14h–15h30',
            'note' => '',
            'cells' => [
                'lundi' => [
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'women', 'label' => 'Sœurs'],
                ],
                'mardi' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'women', 'label' => 'Sœurs'],
                ],
                'mercredi' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'english', 'label' => 'Anglais'],
                ],
                'jeudi' => [],
                'vendredi' => [],
                'samedi' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'english', 'label' => 'Anglais'],
                ],
                'dimanche' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'english', 'label' => 'Anglais'],
                ],
            ],
        ],
        [
            'time' => '15h45–17h15',
            'note' => '',
            'cells' => [
                'lundi' => [],
                'mardi' => [],
                'mercredi' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'english', 'label' => 'Anglais'],
                ],
                'jeudi' => [],
                'vendredi' => [],
                'samedi' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'english', 'label' => 'Anglais'],
                ],
                'dimanche' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'english', 'label' => 'Anglais'],
                ],
            ],
        ],
        [
            'time' => '20h–21h30',
            'note' => 'Adultes',
            'cells' => [
                'lundi' => [
                    ['type' => 'coran', 'label' => 'Coran'],
                    ['type' => 'women', 'label' => 'Sœurs'],
                ],
                'mardi' => [
                    ['type' => 'arabe', 'label' => 'Arabe'],
                    ['type' => 'women', 'label' => 'Sœurs'],
                ],
                'mercredi' => [
                    ['type' => 'coran', 'label' => 'Coran — Frères'],
                    ['type' => 'toeic', 'label' => 'TOEIC'],
                ],
                'jeudi' => [
                    ['type' => 'arabe', 'label' => 'Arabe — Frères'],
                ],
                'vendredi' => [],
                'samedi' => [],
                'dimanche' => [],
            ],
        ],
    ];

    $typeIcons = [
        'arabe' => 'bi-translate',
        'coran' => 'bi-book',
        'english' => 'bi-flag',
        'toeic' => 'bi-mortarboard-fill',
        'women' => 'bi-person-heart',
        'men' => 'bi-person-fill',
    ];
@endphp

<div class="flyer-planning-page">
    <section class="flyer-planning-hero">
        <div class="flyer-planning-shell">
            <div class="flyer-planning-hero-card">
                <div class="flyer-planning-hero-copy">
                    <span class="flyer-planning-kicker">
                        <i class="bi bi-calendar2-week-fill"></i>
                        Planning public
                    </span>

                    <h1>Les créneaux proposés par Smart School Academy</h1>

                    <p>
                        Consultez nos créneaux avant même de vous connecter.
                        Le planning ci-dessous reprend les horaires proposés sur notre flyer :
                        Arabe, Coran, Anglais et préparation TOEIC.
                    </p>

                    <div class="flyer-planning-hero-actions">
                        @guest
                            <a href="{{ route('register') }}" class="flyer-btn flyer-btn-primary">
                                <i class="bi bi-person-plus-fill"></i>
                                S'inscrire
                            </a>
                        @endguest

                        <a href="{{ route('appointment.create') }}" class="flyer-btn flyer-btn-secondary">
                            <i class="bi bi-calendar-check-fill"></i>
                            Prendre rendez-vous
                        </a>
                    </div>
                </div>

                <div class="flyer-planning-hero-icon" aria-hidden="true">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="flyer-planning-section">
        <div class="flyer-planning-shell">
            <div class="flyer-planning-heading">
                <div>
                    <span class="flyer-planning-overline">Vos cours</span>
                    <h2>Planning hebdomadaire</h2>
                    <p>Créneaux indicatifs proposés par l'école, du lundi au dimanche.</p>
                </div>

                <span class="flyer-planning-badge">
                    <i class="bi bi-laptop"></i>
                    Cours 100 % en ligne
                </span>
            </div>

            <div class="flyer-planning-table-wrap">
                <table class="flyer-planning-table">
                    <thead>
                        <tr>
                            <th class="time-column">Horaire</th>
                            @foreach($flyerDays as $day)
                                <th>
                                    <span>{{ $day['label'] }}</span>
                                    @if($day['note'])
                                        <small>{{ $day['note'] }}</small>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($flyerRows as $row)
                            <tr>
                                <th class="time-cell">
                                    <strong>{{ $row['time'] }}</strong>
                                    @if($row['note'])
                                        <small>{{ $row['note'] }}</small>
                                    @endif
                                </th>

                                @foreach($flyerDays as $day)
                                    @php
                                        $items = $row['cells'][$day['key']] ?? [];
                                    @endphp

                                    <td>
                                        @if(count($items))
                                            <div class="flyer-planning-items">
                                                @foreach($items as $item)
                                                    <span class="flyer-planning-chip type-{{ $item['type'] }}">
                                                        <i class="bi {{ $typeIcons[$item['type']] ?? 'bi-book-half' }}"></i>
                                                        {{ $item['label'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="flyer-planning-empty">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flyer-mobile-planning">
                @foreach($flyerRows as $row)
                    <article class="flyer-mobile-time-card">
                        <header>
                            <div>
                                <i class="bi bi-clock-fill"></i>
                                <strong>{{ $row['time'] }}</strong>
                            </div>

                            @if($row['note'])
                                <span>{{ $row['note'] }}</span>
                            @endif
                        </header>

                        <div class="flyer-mobile-days">
                            @foreach($flyerDays as $day)
                                @php
                                    $items = $row['cells'][$day['key']] ?? [];
                                @endphp

                                @if(count($items))
                                    <div class="flyer-mobile-day">
                                        <div class="flyer-mobile-day-name">
                                            {{ $day['label'] }}
                                            @if($day['note'])
                                                <small>{{ $day['note'] }}</small>
                                            @endif
                                        </div>

                                        <div class="flyer-planning-items">
                                            @foreach($items as $item)
                                                <span class="flyer-planning-chip type-{{ $item['type'] }}">
                                                    <i class="bi {{ $typeIcons[$item['type']] ?? 'bi-book-half' }}"></i>
                                                    {{ $item['label'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="flyer-planning-legend">
                <article>
                    <span class="legend-icon arabe"><i class="bi bi-translate"></i></span>
                    <div>
                        <strong>Cours d'Arabe</strong>
                        <small>Lecture, grammaire, expression et compréhension.</small>
                    </div>
                </article>

                <article>
                    <span class="legend-icon coran"><i class="bi bi-book"></i></span>
                    <div>
                        <strong>Cours de Coran</strong>
                        <small>Tajwid, mémorisation (Hifd) et compréhension.</small>
                    </div>
                </article>

                <article>
                    <span class="legend-icon english"><i class="bi bi-flag"></i></span>
                    <div>
                        <strong>Anglais</strong>
                        <small>Groupes de niveau : débutant, intermédiaire et avancé.</small>
                    </div>
                </article>

                <article>
                    <span class="legend-icon toeic"><i class="bi bi-mortarboard-fill"></i></span>
                    <div>
                        <strong>Préparation TOEIC</strong>
                        <small>Créneau adulte indiqué sur le planning.</small>
                    </div>
                </article>
            </div>

            <div class="flyer-planning-note">
                <i class="bi bi-info-circle-fill"></i>
                <p>
                    Les créneaux affichés correspondent à l'offre générale de l'école.
                    Le groupe définitif et les horaires attribués à chaque étudiant
                    sont confirmés lors de l'inscription et peuvent être ajustés selon le niveau.
                </p>
            </div>
        </div>
    </section>

    @if(isset($schedules) && $schedules->isNotEmpty())
        <section class="flyer-published-section">
            <div class="flyer-planning-shell">
                <div class="flyer-planning-heading">
                    <div>
                        <span class="flyer-planning-overline">Plateforme</span>
                        <h2>Cours actuellement publiés</h2>
                        <p>Ces séances proviennent directement du planning administré sur la plateforme.</p>
                    </div>
                </div>

                <div class="flyer-published-grid">
                    @foreach($schedules as $schedule)
                        <a
                            href="{{ route('public.schedule.index', ['schedule' => $schedule['schedule_id']]) }}"
                            class="flyer-published-card"
                        >
                            <div class="flyer-published-time">
                                <i class="bi bi-clock-fill"></i>
                                <strong>{{ $schedule['time_label'] ?? $schedule['start_label'] }}</strong>
                            </div>

                            <h3>{{ $schedule['subject'] }}</h3>

                            <p>
                                {{ $schedule['level'] }}
                                <span>•</span>
                                {{ $schedule['class_name'] }}
                                @if(!empty($schedule['slot_code']))
                                    <span>•</span>
                                    Groupe {{ $schedule['slot_code'] }}
                                @endif
                            </p>

                            <span class="flyer-published-link">
                                Voir la fiche
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
@endsection

@push('styles')
<style>
    .flyer-planning-page {
        --fp-bg: #080d18;
        --fp-card: rgba(15, 23, 42, .86);
        --fp-card-strong: #111c31;
        --fp-border: rgba(148, 163, 184, .14);
        --fp-muted: #91a0b7;
        --fp-blue: #3b82f6;
        --fp-gold: #f7b733;
        --fp-violet: #7c3aed;
        --fp-orange: #f97316;
        --fp-green: #22c55e;
        color: #f8fafc;
        padding-bottom: 55px;
    }

    .flyer-planning-shell {
        width: min(1280px, calc(100% - 32px));
        margin: 0 auto;
    }

    .flyer-planning-hero {
        padding: 32px 0 20px;
    }

    .flyer-planning-hero-card {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 32px;
        min-height: 245px;
        overflow: hidden;
        padding: 36px 40px;
        border: 1px solid rgba(96,165,250,.18);
        border-radius: 26px;
        background:
            radial-gradient(circle at 90% 10%, rgba(124,58,237,.30), transparent 34%),
            radial-gradient(circle at 68% 100%, rgba(37,99,235,.19), transparent 42%),
            linear-gradient(135deg, #0b1220, #111b30 58%, #181b34);
        box-shadow: 0 25px 70px rgba(0,0,0,.27);
    }

    .flyer-planning-hero-card::before {
        position: absolute;
        inset: 0;
        content: "";
        pointer-events: none;
        background-image:
            linear-gradient(rgba(255,255,255,.018) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.018) 1px, transparent 1px);
        background-size: 30px 30px;
    }

    .flyer-planning-hero-copy {
        position: relative;
        z-index: 2;
        max-width: 790px;
    }

    .flyer-planning-kicker,
    .flyer-planning-overline {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #ffd166;
        font-size: .72rem;
        font-weight: 850;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .flyer-planning-kicker {
        padding: 7px 11px;
        margin-bottom: 14px;
        border: 1px solid rgba(255,209,102,.20);
        border-radius: 999px;
        background: rgba(255,209,102,.07);
    }

    .flyer-planning-hero h1 {
        max-width: 820px;
        margin: 0;
        color: #fff;
        font-family: Poppins, sans-serif;
        font-size: clamp(2rem, 4vw, 3.5rem);
        font-weight: 850;
        line-height: 1.08;
        letter-spacing: -.045em;
    }

    .flyer-planning-hero p {
        max-width: 760px;
        margin: 15px 0 0;
        color: #a7b3c7;
        font-size: .96rem;
        line-height: 1.75;
    }

    .flyer-planning-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 11px;
        margin-top: 24px;
    }

    .flyer-btn {
        min-height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 18px;
        border-radius: 12px;
        font-size: .82rem;
        font-weight: 800;
        text-decoration: none !important;
        transition: .2s ease;
    }

    .flyer-btn:hover {
        transform: translateY(-2px);
    }

    .flyer-btn-primary {
        color: #fff !important;
        border: 1px solid rgba(96,165,250,.20);
        background: linear-gradient(135deg, #2563eb, #4f46e5 60%, #6d28d9);
        box-shadow: 0 12px 26px rgba(37,99,235,.22);
    }

    .flyer-btn-secondary {
        color: #e2e8f0 !important;
        border: 1px solid rgba(148,163,184,.16);
        background: rgba(255,255,255,.045);
    }

    .flyer-planning-hero-icon {
        position: relative;
        z-index: 2;
        display: grid;
        flex: 0 0 126px;
        width: 126px;
        height: 126px;
        place-items: center;
        border: 1px solid rgba(255,209,102,.20);
        border-radius: 32px;
        color: #ffd166;
        font-size: 3.4rem;
        background:
            linear-gradient(145deg, rgba(255,209,102,.12), rgba(124,58,237,.14));
        box-shadow:
            0 22px 60px rgba(0,0,0,.30),
            inset 0 1px rgba(255,255,255,.08);
        transform: rotate(4deg);
    }

    .flyer-planning-section,
    .flyer-published-section {
        padding: 25px 0;
    }

    .flyer-planning-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
    }

    .flyer-planning-heading h2 {
        margin: 5px 0 4px;
        color: #fff;
        font-family: Poppins, sans-serif;
        font-size: clamp(1.35rem, 2.6vw, 2rem);
        font-weight: 800;
        letter-spacing: -.025em;
    }

    .flyer-planning-heading p {
        margin: 0;
        color: var(--fp-muted);
        font-size: .84rem;
    }

    .flyer-planning-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 13px;
        border: 1px solid rgba(34,197,94,.18);
        border-radius: 999px;
        color: #86efac;
        font-size: .75rem;
        font-weight: 750;
        background: rgba(34,197,94,.07);
        white-space: nowrap;
    }

    .flyer-planning-table-wrap {
        overflow-x: auto;
        border: 1px solid var(--fp-border);
        border-radius: 22px;
        background: rgba(10,18,32,.88);
        box-shadow: 0 20px 50px rgba(0,0,0,.18);
    }

    .flyer-planning-table {
        width: 100%;
        min-width: 1120px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .flyer-planning-table th,
    .flyer-planning-table td {
        border-right: 1px solid rgba(148,163,184,.10);
        border-bottom: 1px solid rgba(148,163,184,.10);
    }

    .flyer-planning-table tr:last-child th,
    .flyer-planning-table tr:last-child td {
        border-bottom: 0;
    }

    .flyer-planning-table th:last-child,
    .flyer-planning-table td:last-child {
        border-right: 0;
    }

    .flyer-planning-table thead th {
        padding: 15px 12px;
        text-align: center;
        color: #dbeafe;
        background:
            linear-gradient(180deg, rgba(37,99,235,.15), rgba(37,99,235,.06));
        font-size: .78rem;
        font-weight: 800;
    }

    .flyer-planning-table thead th span,
    .flyer-planning-table thead th small {
        display: block;
    }

    .flyer-planning-table thead th small {
        margin-top: 3px;
        color: #7f8da3;
        font-size: .63rem;
        font-weight: 650;
    }

    .flyer-planning-table .time-column {
        width: 135px;
        color: #fff;
        background:
            linear-gradient(135deg, rgba(124,58,237,.20), rgba(37,99,235,.14));
    }

    .flyer-planning-table .time-cell {
        padding: 17px 12px;
        text-align: center;
        vertical-align: middle;
        background: rgba(37,99,235,.055);
    }

    .flyer-planning-table .time-cell strong,
    .flyer-planning-table .time-cell small {
        display: block;
    }

    .flyer-planning-table .time-cell strong {
        color: #fff;
        font-size: .78rem;
        white-space: nowrap;
    }

    .flyer-planning-table .time-cell small {
        margin-top: 4px;
        color: #fbbf24;
        font-size: .65rem;
    }

    .flyer-planning-table td {
        min-width: 135px;
        height: 95px;
        padding: 11px;
        vertical-align: middle;
        background: rgba(255,255,255,.012);
    }

    .flyer-planning-table tbody tr:nth-child(even) td {
        background: rgba(255,255,255,.024);
    }

    .flyer-planning-items {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
    }

    .flyer-planning-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        max-width: 100%;
        padding: 6px 8px;
        border: 1px solid rgba(148,163,184,.13);
        border-radius: 8px;
        color: #cbd5e1;
        background: rgba(148,163,184,.055);
        font-size: .65rem;
        font-weight: 750;
        line-height: 1.2;
    }

    .flyer-planning-chip.type-arabe {
        color: #fdba74;
        border-color: rgba(249,115,22,.18);
        background: rgba(249,115,22,.08);
    }

    .flyer-planning-chip.type-coran {
        color: #c4b5fd;
        border-color: rgba(139,92,246,.20);
        background: rgba(124,58,237,.09);
    }

    .flyer-planning-chip.type-english {
        color: #93c5fd;
        border-color: rgba(59,130,246,.20);
        background: rgba(37,99,235,.08);
    }

    .flyer-planning-chip.type-toeic {
        color: #bfdbfe;
        border-color: rgba(96,165,250,.22);
        background: rgba(59,130,246,.10);
    }

    .flyer-planning-chip.type-women {
        color: #f9a8d4;
        border-color: rgba(236,72,153,.18);
        background: rgba(236,72,153,.07);
    }

    .flyer-planning-empty {
        display: block;
        color: #39465a;
        text-align: center;
        font-weight: 800;
    }

    .flyer-mobile-planning {
        display: none;
    }

    .flyer-planning-legend {
        display: grid;
        grid-template-columns: repeat(4, minmax(0,1fr));
        gap: 12px;
        margin-top: 18px;
    }

    .flyer-planning-legend article {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 90px;
        padding: 15px;
        border: 1px solid var(--fp-border);
        border-radius: 16px;
        background: var(--fp-card);
    }

    .legend-icon {
        display: grid;
        flex: 0 0 39px;
        width: 39px;
        height: 39px;
        place-items: center;
        border-radius: 11px;
        font-size: 1rem;
    }

    .legend-icon.arabe {
        color: #fdba74;
        background: rgba(249,115,22,.09);
    }

    .legend-icon.coran {
        color: #c4b5fd;
        background: rgba(124,58,237,.10);
    }

    .legend-icon.english,
    .legend-icon.toeic {
        color: #93c5fd;
        background: rgba(37,99,235,.10);
    }

    .flyer-planning-legend strong,
    .flyer-planning-legend small {
        display: block;
    }

    .flyer-planning-legend strong {
        color: #f8fafc;
        font-size: .79rem;
    }

    .flyer-planning-legend small {
        margin-top: 3px;
        color: var(--fp-muted);
        font-size: .66rem;
        line-height: 1.45;
    }

    .flyer-planning-note {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        margin-top: 15px;
        padding: 14px 16px;
        border: 1px solid rgba(59,130,246,.16);
        border-radius: 14px;
        color: #93c5fd;
        background: rgba(37,99,235,.055);
    }

    .flyer-planning-note p {
        margin: 0;
        color: #8898b0;
        font-size: .75rem;
        line-height: 1.6;
    }

    .flyer-published-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0,1fr));
        gap: 14px;
    }

    .flyer-published-card {
        display: block;
        min-height: 170px;
        padding: 18px;
        border: 1px solid var(--fp-border);
        border-radius: 18px;
        color: inherit !important;
        text-decoration: none !important;
        background: var(--fp-card);
        transition: .2s ease;
    }

    .flyer-published-card:hover {
        border-color: rgba(96,165,250,.28);
        background: var(--fp-card-strong);
        transform: translateY(-3px);
    }

    .flyer-published-time {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #93c5fd;
        font-size: .72rem;
    }

    .flyer-published-card h3 {
        margin: 15px 0 7px;
        color: #fff;
        font-size: 1rem;
        font-weight: 800;
    }

    .flyer-published-card p {
        margin: 0;
        color: var(--fp-muted);
        font-size: .72rem;
    }

    .flyer-published-card p span {
        margin: 0 4px;
        color: #475569;
    }

    .flyer-published-link {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 17px;
        color: #ffd166;
        font-size: .72rem;
        font-weight: 750;
    }

    html.light-mode .flyer-planning-page {
        color: #1e293b;
    }

    html.light-mode .flyer-planning-hero-card {
        border-color: rgba(15,23,42,.10);
        background:
            radial-gradient(circle at 90% 10%, rgba(124,58,237,.10), transparent 34%),
            linear-gradient(135deg, #fff, #f6f8fd);
        box-shadow: 0 20px 55px rgba(15,23,42,.08);
    }

    html.light-mode .flyer-planning-hero h1,
    html.light-mode .flyer-planning-heading h2,
    html.light-mode .flyer-planning-table .time-cell strong,
    html.light-mode .flyer-planning-legend strong,
    html.light-mode .flyer-published-card h3 {
        color: #0f172a;
    }

    html.light-mode .flyer-planning-hero p,
    html.light-mode .flyer-planning-heading p,
    html.light-mode .flyer-planning-note p,
    html.light-mode .flyer-planning-legend small,
    html.light-mode .flyer-published-card p {
        color: #64748b;
    }

    html.light-mode .flyer-planning-table-wrap,
    html.light-mode .flyer-planning-legend article,
    html.light-mode .flyer-published-card {
        border-color: rgba(15,23,42,.09);
        background: rgba(255,255,255,.90);
    }

    html.light-mode .flyer-planning-table thead th {
        color: #334155;
        background: #f5f7fb;
    }

    html.light-mode .flyer-planning-table .time-column {
        color: #0f172a;
        background: #eef2ff;
    }

    html.light-mode .flyer-planning-table .time-cell {
        background: #f8fafc;
    }

    html.light-mode .flyer-planning-table td {
        background: #fff;
    }

    html.light-mode .flyer-planning-table tbody tr:nth-child(even) td {
        background: #fbfcfe;
    }

    html.light-mode .flyer-planning-table th,
    html.light-mode .flyer-planning-table td {
        border-color: rgba(15,23,42,.08);
    }

    @media (max-width: 1050px) {
        .flyer-planning-legend {
            grid-template-columns: repeat(2, minmax(0,1fr));
        }

        .flyer-published-grid {
            grid-template-columns: repeat(2, minmax(0,1fr));
        }
    }

    @media (max-width: 760px) {
        .flyer-planning-shell {
            width: min(100% - 22px, 1280px);
        }

        .flyer-planning-hero {
            padding-top: 16px;
        }

        .flyer-planning-hero-card {
            min-height: auto;
            padding: 25px 20px;
            border-radius: 20px;
        }

        .flyer-planning-hero-icon {
            display: none;
        }

        .flyer-planning-hero h1 {
            font-size: 2rem;
        }

        .flyer-planning-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .flyer-planning-table-wrap {
            display: none;
        }

        .flyer-mobile-planning {
            display: grid;
            gap: 12px;
        }

        .flyer-mobile-time-card {
            overflow: hidden;
            border: 1px solid var(--fp-border);
            border-radius: 17px;
            background: var(--fp-card);
        }

        .flyer-mobile-time-card > header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 15px;
            border-bottom: 1px solid var(--fp-border);
            background: rgba(37,99,235,.07);
        }

        .flyer-mobile-time-card > header div {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #93c5fd;
        }

        .flyer-mobile-time-card > header span {
            color: #fbbf24;
            font-size: .68rem;
            font-weight: 750;
        }

        .flyer-mobile-days {
            padding: 5px 15px;
        }

        .flyer-mobile-day {
            display: grid;
            grid-template-columns: 105px 1fr;
            gap: 10px;
            align-items: center;
            padding: 11px 0;
            border-bottom: 1px solid rgba(148,163,184,.08);
        }

        .flyer-mobile-day:last-child {
            border-bottom: 0;
        }

        .flyer-mobile-day-name {
            color: #e2e8f0;
            font-size: .72rem;
            font-weight: 800;
        }

        .flyer-mobile-day-name small {
            display: block;
            margin-top: 2px;
            color: #64748b;
            font-size: .58rem;
            font-weight: 600;
        }

        .flyer-mobile-day .flyer-planning-items {
            justify-content: flex-start;
        }

        .flyer-planning-legend,
        .flyer-published-grid {
            grid-template-columns: 1fr;
        }

        .flyer-planning-note {
            font-size: .72rem;
        }

        html.light-mode .flyer-mobile-time-card {
            border-color: rgba(15,23,42,.09);
            background: #fff;
        }

        html.light-mode .flyer-mobile-time-card > header {
            border-color: rgba(15,23,42,.08);
            background: #f8fafc;
        }

        html.light-mode .flyer-mobile-day {
            border-color: rgba(15,23,42,.08);
        }

        html.light-mode .flyer-mobile-day-name {
            color: #334155;
        }
    }
</style>
@endpush
