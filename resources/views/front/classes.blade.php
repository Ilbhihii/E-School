@extends('layouts.front')

@section('title', 'Matières')

@section('content')

<style>
    /* Image du livre coranique dans un cadre bleu/violet */
    .quran-icon-wrapper {
        width: 96px !important;
        height: 96px !important;
        margin: 0 auto 1rem;
        padding: 12px;

        display: flex !important;
        align-items: center;
        justify-content: center;

        border-radius: 24px !important;
        overflow: hidden !important;

        background: linear-gradient(
            135deg,
            #2563EB 0%,
            #4F46E5 48%,
            #7C3AED 100%
        ) !important;

        border: 1px solid rgba(255, 255, 255, 0.16);

        box-shadow:
            0 12px 28px rgba(37, 99, 235, 0.28),
            0 0 22px rgba(124, 58, 237, 0.22) !important;

        transform: translateZ(30px);
        transition:
            transform 0.3s ease,
            box-shadow 0.3s ease;
    }

    .quran-subject-image {
        width: 76px;
        height: 76px;
        display: block;
        object-fit: contain;

        filter: drop-shadow(
            0 7px 10px rgba(0, 0, 0, 0.32)
        );

        transition: transform 0.3s ease;
    }

    .card-3d:hover .quran-icon-wrapper {
        transform: translateZ(50px) scale(1.08);

        box-shadow:
            0 16px 34px rgba(37, 99, 235, 0.36),
            0 0 30px rgba(124, 58, 237, 0.32) !important;
    }

    .card-3d:hover .quran-subject-image {
        transform: scale(1.06);
    }

    @media (max-width: 576px) {
        .quran-icon-wrapper {
            width: 82px !important;
            height: 82px !important;
            padding: 10px;
            border-radius: 20px !important;
        }

        .quran-subject-image {
            width: 64px;
            height: 64px;
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

        <div class="row g-4" id="subjectsGrid">
            @forelse($subjects as $subject)
            @php
                $normalizedSubjectName = mb_strtolower(trim($subject->name ?? ''));

                $isQuran = in_array($normalizedSubjectName, [
                    'coran',
                    'quran',
                    'couran',
                    'القرآن',
                    'القران',
                ], true);

                $subjectDesign = match ($normalizedSubjectName) {
                    'arabe' => [
                        'icon' => 'bi-translate',
                        'gradient' => 'linear-gradient(135deg,#2563EB,#06B6D4)',
                    ],
                    default => [
                        'icon' => 'bi-journal-bookmark-fill',
                        'gradient' => 'linear-gradient(135deg,#4F46E5,#7C3AED)',
                    ],
                };
            @endphp
            <div class="col-md-4 subject-card" data-type="{{ $subject->type }}">
                <div class="card-3d text-center h-100 reveal-3d">
                    <a href="{{ route('front.subject.levels', $subject->id) }}" class="text-decoration-none">
                        <div
                            class="card-3d-icon mx-auto {{ $isQuran ? 'quran-icon-wrapper' : '' }}"
                            @if (!$isQuran)
                                style="background: {{ $subjectDesign['gradient'] }};"
                            @endif
                        >
                            @if ($isQuran)
                                <img
                                    src="{{ asset('images/alquran.png') }}"
                                    alt="Livre du Coran"
                                    class="quran-subject-image"
                                    loading="lazy"
                                >
                            @else
                                <i class="bi {{ $subjectDesign['icon'] }}"></i>
                            @endif
                        </div>
                        <h5 class="fw-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">{{ $subject->name }}</h5>
                    </a>
                        <span class="badge px-3 py-1 mb-2" style="background: {{ $subject->status_bg }}; color: {{ $subject->status_color }}; border: 1px solid {{ $subject->status_border }}; border-radius: 20px; font-weight: 500; font-size: 0.75rem;">
                            <i class="bi {{ $subject->status_icon }} me-1"></i> {{ $subject->status_label }}
                        </span>
                        <p class="text-white-50 small mb-3">
                            <span class="badge" style="background: {{ $subject->type === 'religieux' ? 'rgba(155,89,182,0.2)' : 'rgba(52,152,219,0.2)' }}; color: {{ $subject->type === 'religieux' ? '#D7A1F9' : '#7DD3FC' }}; border-radius: 20px; font-size: 0.7rem;">
                                {{ $subject->type === 'religieux' ? '🕌 Religieux' : '📚 Scolaire' }}
                            </span>
                        </p>

                        <div class="pt-3" style="border-top:1px solid rgba(255,255,255,0.08);">
                            <div class="small fw-semibold mb-2" style="color:rgba(255,255,255,0.65);">
                                <i class="bi bi-layers me-1"></i>Niveaux disponibles
                            </div>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                @forelse($subject->available_levels as $level)
                                    <a href="{{ route('front.subject.level.classes', [$subject->id, $level->id]) }}"
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