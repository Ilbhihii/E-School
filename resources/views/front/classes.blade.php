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
        <div class="row g-4">
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
                        <p class="text-white-50 small mb-0">Voir les niveaux disponibles <i class="bi bi-arrow-right ms-1" style="color: var(--3d-gold);"></i></p>
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
    </div>
</section>

@endsection
