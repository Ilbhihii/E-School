@extends('layouts.front')

@section('title', 'Matières')

@section('content')

<style>
    /* =========================================================
       PAGE /CLASSES — VERSION PLUS COMPACTE
       ========================================================= */

    #classesPage {
        padding-top: 3.2rem !important;
        padding-bottom: 3.2rem !important;
    }

    .classes-page-header {
        margin-bottom: 1.8rem !important;
    }

    #classesPage .section-title-3d {
        margin-bottom: 0.55rem;
        font-size: clamp(1.75rem, 2.8vw, 2.45rem);
        line-height: 1.14;
    }

    #classesPage .classes-page-header > .badge {
        padding: 0.42rem 0.8rem !important;
        margin-bottom: 0.75rem !important;
        font-size: 0.72rem !important;
    }

    #classesPage .classes-page-header > p {
        max-width: 460px !important;
        font-size: 0.9rem;
        line-height: 1.55;
    }

    #subjectsGrid {
        justify-content: center;
    }

    #subjectsGrid > .subject-card {
        display: flex;
    }

    .subject-card-panel {
        width: 100%;
        min-height: 305px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding: 1.2rem 1rem !important;
        border-radius: 18px !important;
    }

    .subject-icon-box {
        width: 62px !important;
        height: 62px !important;
        flex: 0 0 62px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.7rem;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 17px !important;
        color: #ffffff;
        font-size: 1.55rem;
        box-shadow: 0 10px 23px rgba(0, 0, 0, 0.22);
        transform: translateZ(20px);
        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease;
    }

    .subject-card-panel:hover .subject-icon-box {
        transform: translateZ(30px) translateY(-3px) scale(1.04);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.28);
    }

    .subject-card-heading {
        min-height: 27px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem !important;
        font-size: 1rem;
    }

    .subject-card-panel > .badge {
        padding: 0.3rem 0.65rem !important;
        margin-bottom: 0.5rem !important;
        font-size: 0.68rem !important;
    }

    .subject-card-panel > p {
        margin-bottom: 0.65rem !important;
    }

    .subject-card-panel > p .badge {
        padding: 0.3rem 0.65rem !important;
        font-size: 0.64rem !important;
    }

    .subject-levels-block {
        margin-top: auto;
        padding-top: 0.75rem !important;
    }

    .subject-levels-block > .small {
        margin-bottom: 0.55rem !important;
        font-size: 0.74rem;
    }

    .subject-levels-block .gap-2 {
        gap: 0.45rem !important;
    }

    .subject-levels-block a.badge {
        padding: 0.45rem 0.75rem !important;
        font-size: 0.67rem;
    }

    /* Filtre */
    #classesPage .row.mb-4 {
        margin-bottom: 1.25rem !important;
    }

    #classesPage .d-inline-flex.align-items-center {
        gap: 0.65rem !important;
        padding: 0.42rem 0.8rem !important;
    }

    #classesPage label[for="subjectFilter"] {
        font-size: 0.76rem !important;
    }

    #subjectFilter {
        min-width: 160px !important;
        padding: 0.4rem 1.85rem 0.4rem 0.8rem !important;
        font-size: 0.76rem !important;
    }

    #noResults {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
    }

    @media (min-width: 1200px) {
        #subjectsGrid > .subject-card {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }

    @media (max-width: 1199.98px) {
        .subject-card-panel {
            min-height: 290px;
        }
    }

    @media (max-width: 767.98px) {
        #classesPage {
            padding-top: 2.6rem !important;
            padding-bottom: 2.6rem !important;
        }

        .classes-page-header {
            margin-bottom: 1.5rem !important;
        }

        .subject-card-panel {
            min-height: 280px;
        }
    }

    @media (max-width: 575.98px) {
        #classesPage {
            padding-top: 2.2rem !important;
            padding-bottom: 2.2rem !important;
        }

        .subject-card-panel {
            min-height: auto;
            padding: 1rem 0.9rem !important;
        }

        .subject-icon-box {
            width: 56px !important;
            height: 56px !important;
            flex-basis: 56px;
            border-radius: 15px !important;
            font-size: 1.4rem;
        }

        #classesPage .d-inline-flex.align-items-center {
            width: min(100%, 340px);
            align-items: stretch !important;
            flex-direction: column;
            border-radius: 16px !important;
        }

        #subjectFilter {
            width: 100%;
            min-width: 0 !important;
        }
    }

    .subject-card-panel.is-coming-soon {
        border-color: rgba(251,146,60,0.28) !important;
        background:
            radial-gradient(
                circle at 50% 0%,
                rgba(249,115,22,0.08),
                transparent 42%
            ),
            rgba(15,23,42,0.78) !important;
        cursor: default;
    }

    .subject-card-panel.is-coming-soon:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    .subject-card-panel.is-coming-soon .subject-icon-box {
        opacity: 0.82;
        filter: saturate(0.82);
    }

    .subject-coming-soon-note {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 36px;
        padding: 8px 12px;
        border: 1px dashed rgba(251,146,60,0.24);
        border-radius: 12px;
        color: #FDBA74;
        background: rgba(249,115,22,0.07);
        font-size: 0.72rem;
        font-weight: 650;
        line-height: 1.35;
    }

    @media (prefers-reduced-motion: reduce) {
        .subject-card-panel,
        .subject-icon-box {
            transition: none !important;
            animation: none !important;
        }
    }
</style>

<section class="py-5" id="classesPage">
    <div class="container text-center mb-4 classes-page-header">
        <span class="badge px-3 py-2 mb-3" style="background: rgba(255,209,102,0.12); color: #FFD166; border-radius: 20px; font-weight: 500; font-size: 0.8rem;">
            Matières
        </span>
        <h2 class="section-title-3d">Nos Matières</h2>
        <p class="text-white-50" style="max-width: 500px; margin: 0 auto;">Choisissez une matière pour commencer votre apprentissage</p>
    </div>

    <div class="container">

        <!-- FILTER SELECT -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-center">
                <div class="d-inline-flex align-items-center gap-3 px-4 py-2 rounded-pill"
                     style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(10px);">
                    <label for="subjectFilter" class="text-white-50 small fw-semibold" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                        <i class="bi bi-funnel me-1"></i> Filtrer par :
                    </label>
                    <select id="subjectFilter" class="form-select form-select-sm border-0" aria-label="Filtrer les matières par type"
                            style="background: #ffffff; color: #1E293B; border-radius: 50px; padding: 8px 32px 8px 16px; font-size: 0.85rem; cursor: pointer; min-width: 180px; appearance: none; background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22%231E293B%22 viewBox=%220 0 16 16%22%3E%3Cpath d=%22M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 12px center;">
                        <option value="all" selected>Toutes les matières</option>
                        <option value="scolaire">les Matières scolaires</option>
                        <option value="religieux">les Matières religieuses</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3" id="subjectsGrid">
            @forelse($subjects as $subject)
            @php
                $normalizedSubjectName = mb_strtolower(
                    trim($subject->name ?? '')
                );

                $isComingSoon =
                    ($subject->status ?? 'active') === 'coming_soon';

                $subjectDesign = match ($normalizedSubjectName) {
                    'arabe' => [
                        'icon' => 'bi-translate',
                        'gradient' =>
                            'linear-gradient(135deg,#2563EB,#06B6D4)',
                    ],

                    'coran', 'quran', 'couran', 'القرآن', 'القران' => [
                        'icon' => 'bi-book-half',
                        'gradient' =>
                            'linear-gradient(135deg,#7C3AED,#A855F7)',
                    ],

                    'soutien lycée', 'soutien lycee' => [
                        'icon' => 'bi-mortarboard-fill',
                        'gradient' =>
                            'linear-gradient(135deg,#F59E0B,#EA580C)',
                    ],

                    default => [
                        'icon' => 'bi-journal-bookmark-fill',
                        'gradient' =>
                            'linear-gradient(135deg,#4F46E5,#7C3AED)',
                    ],
                };
            @endphp
            <div
                class="col subject-card"
                data-type="{{ $subject->type }}"
                data-status="{{ $subject->status }}"
            >
                <div
                    class="card-3d text-center h-100 reveal-3d subject-card-panel {{ $isComingSoon ? 'is-coming-soon' : '' }}"
                >
                    @if($isComingSoon)
                        <div aria-disabled="true">
                            <div
                                class="subject-icon-box"
                                style="background:{{ $subjectDesign['gradient'] }};"
                                aria-hidden="true"
                            >
                                <i class="bi {{ $subjectDesign['icon'] }}"></i>
                            </div>

                            <h5
                                class="fw-bold text-white mb-2 subject-card-heading"
                                style="font-family:'Poppins',sans-serif;"
                            >
                                {{ $subject->name }}
                            </h5>
                        </div>
                    @else
                        <a
                            href="{{ route('front.subject.levels', $subject->id) }}"
                            class="text-decoration-none"
                        >
                            <div
                                class="subject-icon-box"
                                style="background:{{ $subjectDesign['gradient'] }};"
                                aria-hidden="true"
                            >
                                <i class="bi {{ $subjectDesign['icon'] }}"></i>
                            </div>

                            <h5
                                class="fw-bold text-white mb-2 subject-card-heading"
                                style="font-family:'Poppins',sans-serif;"
                            >
                                {{ $subject->name }}
                            </h5>
                        </a>
                    @endif

                    <span
                        class="badge px-3 py-1 mb-2"
                        style="background: {{ $subject->status_bg }}; color: {{ $subject->status_color }}; border: 1px solid {{ $subject->status_border }}; border-radius: 20px; font-weight: 500; font-size: 0.75rem;"
                    >
                        <i class="bi {{ $subject->status_icon }} me-1"></i>
                        {{ $subject->status_label }}
                    </span>

                    <p class="text-white-50 small mb-3">
                        <span
                            class="badge"
                            style="background: {{ $subject->type === 'religieux' ? 'rgba(155,89,182,0.2)' : 'rgba(52,152,219,0.2)' }}; color: {{ $subject->type === 'religieux' ? '#D7A1F9' : '#7DD3FC' }}; border-radius: 20px; font-size: 0.7rem;"
                        >
                            {{ $subject->type === 'religieux' ? '🕌 Religieux' : '📚 Scolaire' }}
                        </span>
                    </p>

                    <div
                        class="pt-3 subject-levels-block"
                        style="border-top:1px solid rgba(255,255,255,0.08);"
                    >
                        @if($isComingSoon)
                            <div class="subject-coming-soon-note">
                                <i class="bi bi-hourglass-split"></i>
                                <span>Les niveaux seront accessibles prochainement.</span>
                            </div>
                        @else
                            <div
                                class="small fw-semibold mb-2"
                                style="color:rgba(255,255,255,0.65);"
                            >
                                <i class="bi bi-layers me-1"></i>
                                {{ $subject->is_high_school_support
                                    ? 'Parcours disponible'
                                    : 'Niveaux disponibles' }}
                            </div>

                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                @forelse($subject->available_levels as $level)
                                    <a
                                        href="{{ route('front.subject.levels', $subject->id) }}?open={{ $level->id }}"
                                        class="badge text-decoration-none px-3 py-2"
                                        style="background:rgba(124,58,237,0.16);color:#C4B5FD;border:1px solid rgba(167,139,250,0.25);border-radius:20px;"
                                    >
                                        {{ $level->name }}
                                        <i class="bi bi-chevron-right ms-1"></i>
                                    </a>
                                @empty
                                    <span class="text-white-50 small">Aucun niveau disponible</span>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <div class="alert" style="background: rgba(239,68,68,0.15); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.2); border-radius: 12px;">
                    Aucune matière trouvée
                </div>
            </div>
            @endforelse
        </div>

        <!-- EMPTY STATE (hidden by default) -->
        <div id="noResults" class="text-center py-5" style="display: none;">
            <div style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;">🔍</div>
            <h5 class="text-white-50">Aucune matière trouvée</h5>
            <p class="text-white-50 small">Essayez de changer le filtre pour voir plus de matières.</p>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
(function() {
    const filter = document.getElementById('subjectFilter');
    const grid = document.getElementById('subjectsGrid');
    const noResults = document.getElementById('noResults');
    const cards = grid ? grid.querySelectorAll('.subject-card') : [];

    if (!filter || !grid) return;

    filter.addEventListener('change', function() {
        const selected = this.value;
        let visibleCount = 0;

        cards.forEach(function(card) {
            const type = card.getAttribute('data-type');
            const match = selected === 'all' || type === selected;
            card.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        // Show/hide empty state
        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Re-trigger reveal-3d animations for visible cards
        cards.forEach(function(card) {
            if (card.style.display !== 'none') {
                const revealEl = card.querySelector('.reveal-3d');
                if (revealEl && !revealEl.classList.contains('revealed')) {
                    revealEl.classList.add('revealed');
                }
            }
        });
    });
})();
</script>
@endpush

{{-- Design global V12 : présentation uniquement, aucun contenu modifié. --}}
@push('scripts')
<link
    rel="stylesheet"
    href="{{ asset('css/front-design-v12.css?v=12.0') }}"
>
@endpush