@extends('layouts.front')

@section('title', 'Matières')

@section('content')

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
                            style="background: #ffffff; color: #1E293B; border-radius: 50px; padding: 8px 32px 8px 16px; font-size: 0.85rem; cursor: pointer; min-width: 180px; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%231E293B' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center;">
                        <option value="all" selected>Toutes les matières</option>
                        <option value="scolaire">📚 Matières scolaires</option>
                        <option value="religieux">🕌 Matières religieuses</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row g-4" id="subjectsGrid">
            @forelse($subjects as $subject)
            <div class="col-md-4 subject-card" data-type="{{ $subject->type }}">
                <a href="{{ route('front.subject.levels', $subject->id) }}" class="text-decoration-none">
                    <div class="card-3d text-center h-100 reveal-3d" style="cursor: pointer;">
                        <div class="card-3d-icon mx-auto">
                            <i class="bi bi-book"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">{{ $subject->name }}</h5>
                        <span class="badge px-3 py-1 mb-2" style="background: {{ $subject->status_bg }}; color: {{ $subject->status_color }}; border: 1px solid {{ $subject->status_border }}; border-radius: 20px; font-weight: 500; font-size: 0.75rem;">
                            <i class="bi {{ $subject->status_icon }} me-1"></i> {{ $subject->status_label }}
                        </span>
                        <p class="text-white-50 small mb-0">
                            <span class="badge" style="background: {{ $subject->type === 'religieux' ? 'rgba(155,89,182,0.2)' : 'rgba(52,152,219,0.2)' }}; color: {{ $subject->type === 'religieux' ? '#D7A1F9' : '#7DD3FC' }}; border-radius: 20px; font-size: 0.7rem;">
                                {{ $subject->type === 'religieux' ? '🕌 Religieux' : '📚 Scolaire' }}
                            </span>
                            <span class="ms-2">Voir les niveaux <i class="bi bi-arrow-right ms-1" style="color: var(--3d-gold);"></i></span>
                        </p>
                    </div>
                </a>
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
