@extends('layouts.front')

@section('title', $subject->name . ' - ' . $level->name)

@section('content')

<section class="py-5">
    <div class="container">

        <!-- Breadcrumb -->
        <div class="d-flex align-items-center gap-2 mb-4 flex-wrap" style="font-size: 0.85rem;">
            <a href="{{ route('front.classes') }}" style="color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.3s;" 
               onmouseover="this.style.color='rgba(255,209,102,0.9)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                <i class="bi bi-grid-3x3-gap me-1"></i>Matières
            </a>
            <i class="bi bi-chevron-right" style="color: rgba(255,255,255,0.15); font-size: 0.7rem;"></i>
            <a href="{{ route('front.subject.levels', $subject->id) }}" style="color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.3s;"
               onmouseover="this.style.color='rgba(255,209,102,0.9)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                {{ $subject->name }}
            </a>
            <i class="bi bi-chevron-right" style="color: rgba(255,255,255,0.15); font-size: 0.7rem;"></i>
            <span style="color: rgba(255,255,255,0.6);">{{ $level->name }}</span>
            <span class="badge ms-2" style="background: rgba(255,209,102,0.12); color: #FFD166; border-radius: 20px; font-size: 0.65rem; padding: 3px 10px;">
                Classes
            </span>
        </div>

        <!-- Header -->
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 mb-3" style="background: rgba(124,58,237,0.15); color: #A78BFA; border-radius: 20px; font-weight: 500; font-size: 0.8rem;">
                {{ $level->name }}
            </span>
            <h2 class="section-title-3d">{{ $subject->name }}</h2>
            <p class="text-white-50" style="max-width: 500px; margin: 0 auto;">Choisissez une classe pour accéder aux cours</p>
        </div>
    </div>

    <!-- Classes Grid -->
    <div class="container">
        <div class="row g-4">
            @forelse($classes as $class)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('front.courses', [$subject->id, $level->id, $class->id]) }}" class="text-decoration-none">
                    <div class="card-3d text-center h-100 reveal-3d" style="cursor: pointer;">
                        <div class="card-3d-icon mx-auto" style="background: linear-gradient(135deg, 
                            @switch(($loop->index ?? 0) % 5)
                                @case(0) #06B6D4, #0891B2
                                @case(1) #7C3AED, #6D28D9
                                @case(2) #FFD166, #F59E0B
                                @case(3) #22C55E, #16A34A
                                @case(4) #EF4444, #DC2626
                            @endswitch
                        );">
                            <i class="bi bi-people-fill" style="font-size: 1.5rem; color: white;"></i>
                        </div>
                        <h5 class="fw-bold text-white mt-3 mb-1" style="font-family: 'Poppins', sans-serif;">{{ $class->name }}</h5>
                        <p class="text-white-50 small mb-0">
                            Voir les cours <i class="bi bi-arrow-right ms-1" style="color: var(--3d-gold);"></i>
                        </p>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="alert" style="background: rgba(239,68,68,0.15); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; display: inline-block;">
                    Aucune classe trouvée pour ce niveau
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

@endsection
