@extends('layouts.front')

@section('title', 'Cours Scolaires')

@section('content')

<section class="py-5">
    <div class="container text-center mb-5">
        <span class="badge px-3 py-2 mb-3" style="background: rgba(52,152,219,0.15); color: #7DD3FC; border-radius: 20px; font-weight: 500; font-size: 0.8rem;">
            📚 Matières scolaires
        </span>
        <h2 class="section-title-3d">Cours Scolaires</h2>
        <p class="text-white-50" style="max-width: 500px; margin: 0 auto;">Maîtrisez les matières fondamentales avec des cours adaptés</p>
    </div>

    <div class="container">

        <div class="row g-4" id="subjectsGrid">
            @forelse($subjects as $subject)
            <div class="col-md-4">
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
                            <span class="badge" style="background: rgba(52,152,219,0.2); color: #7DD3FC; border-radius: 20px; font-size: 0.7rem;">
                                📚 Scolaire
                            </span>
                            <span class="ms-2">Voir les niveaux <i class="bi bi-arrow-right ms-1" style="color: var(--3d-gold);"></i></span>
                        </p>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center">
                <div class="alert" style="background: rgba(239,68,68,0.15); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.2); border-radius: 12px;">
                    Aucune matière scolaire trouvée
                </div>
            </div>
            @endforelse
        </div>

    </div>
</section>

@endsection
