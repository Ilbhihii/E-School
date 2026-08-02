@extends('layouts.front')

@section('title', 'Matières')

@section('content')

<style>
    /* Grille et icônes harmonisées des matières */
    #subjectsGrid {
        justify-content: center;
    }

    #subjectsGrid > .subject-card {
        display: flex;
    }

    .subject-card-panel {
        width: 100%;
        min-height: 390px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding: 1.65rem 1.35rem !important;
    }

    .subject-icon-box {
        width: 82px !important;
        height: 82px !important;
        flex: 0 0 82px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.9rem;
        border-radius: 21px !important;
        color: #ffffff;
        font-size: 2.05rem;
        border: 1px solid rgba(255, 255, 255, 0.14);
        box-shadow: 0 13px 28px rgba(0, 0, 0, 0.22);
        transform: translateZ(28px);
        transition:
            transform 0.3s ease,
            box-shadow 0.3s ease;
    }

    .subject-card-panel:hover .subject-icon-box {
        transform: translateZ(44px) translateY(-4px) scale(1.06);
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.3);
    }

    .subject-card-heading {
        min-height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .subject-levels-block {
        margin-top: auto;
    }

    @media (min-width: 1200px) {
        #subjectsGrid > .subject-card {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }

    @media (max-width: 1199.98px) {
        .subject-card-panel {
            min-height: 370px;
        }
    }

    @media (max-width: 575.98px) {
        .subject-card-panel {
            min-height: auto;
        }

        .subject-icon-box {
            width: 70px !important;
            height: 70px !important;
            flex-basis: 70px;
            border-radius: 18px !important;
            font-size: 1.8rem;
        }
    }
</style>

<section class="py-5">
    <div class="container text-center mb-5">
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

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="subjectsGrid">
            @forelse($subjects as $subject)
            @php
                $normalizedSubjectName = mb_strtolower(
                    trim($subject->name ?? '')
                );

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
            <div class="col subject-card" data-type="{{ $subject->type }}">
                <div class="card-3d text-center h-100 reveal-3d subject-card-panel">
                    <a href="{{ route('front.subject.levels', $subject->id) }}" class="text-decoration-none">
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
                        <span class="badge px-3 py-1 mb-2" style="background: {{ $subject->status_bg }}; color: {{ $subject->status_color }}; border: 1px solid {{ $subject->status_border }}; border-radius: 20px; font-weight: 500; font-size: 0.75rem;">
                            <i class="bi {{ $subject->status_icon }} me-1"></i> {{ $subject->status_label }}
                        </span>
                        <p class="text-white-50 small mb-3">
                            <span class="badge" style="background: {{ $subject->type === 'religieux' ? 'rgba(155,89,182,0.2)' : 'rgba(52,152,219,0.2)' }}; color: {{ $subject->type === 'religieux' ? '#D7A1F9' : '#7DD3FC' }}; border-radius: 20px; font-size: 0.7rem;">
                                {{ $subject->type === 'religieux' ? '🕌 Religieux' : '📚 Scolaire' }}
                            </span>
                        </p>

                        <div class="pt-3 subject-levels-block" style="border-top:1px solid rgba(255,255,255,0.08);">
                            <div class="small fw-semibold mb-2" style="color:rgba(255,255,255,0.65);">
                                <i class="bi bi-layers me-1"></i>
                                {{ $subject->is_high_school_support
                                    ? 'Parcours disponible'
                                    : 'Niveaux disponibles' }}
                            </div>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                @forelse($subject->available_levels as $level)
                                    <a href="{{ route('front.subject.levels', $subject->id) }}?open={{ $level->id }}"
                                       class="badge text-decoration-none px-3 py-2"
                                       style="background:rgba(124,58,237,0.16);color:#C4B5FD;border:1px solid rgba(167,139,250,0.25);border-radius:20px;">
                                        {{ $level->name }} <i class="bi bi-chevron-right ms-1"></i>
                                    </a>
                                @empty
                                    <span class="text-white-50 small">Aucun niveau disponible</span>
                                @endforelse
                            </div>
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

