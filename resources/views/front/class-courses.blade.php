@extends('layouts.front')

@section('title', 'Cours')

@section('content')

<section class="py-5">
    <div class="container text-center mb-5">
        <span class="badge px-3 py-2 mb-3" style="background: rgba(0,58,143,0.15); color: #2563EB; border-radius: 20px; font-weight: 500; font-size: 0.8rem;">
            Cours disponibles
        </span>
        <h2 class="section-title-3d">Les Cours Disponibles</h2>
        <p class="text-white-50" style="max-width: 500px; margin: 0 auto;">Choisissez un cours pour commencer votre apprentissage</p>
    </div>

    <div class="container">
        <div class="row g-4">
            @forelse($courses as $course)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('front.course.show', $course->id) }}" class="text-decoration-none">
                    <div class="card-3d overflow-hidden p-0 reveal-3d" style="border-radius: 20px; cursor: pointer;">
                        <!-- Gradient banner -->
                        <div style="height: 140px; background: linear-gradient(135deg, 
                            @switch(($loop->index ?? 0) % 3)
                                @case(0) hsl(200,70%,40%), hsl(200,70%,25%)
                                @case(1) hsl(320,70%,40%), hsl(320,70%,25%)
                                @case(2) hsl(40,70%,40%), hsl(40,70%,25%)
                            @endswitch
                        ); display: flex; align-items: flex-end; justify-content: center; padding: 1rem;">
                            <h5 class="fw-bold text-white text-center mb-0" style="text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                                {{ $course->title }}
                            </h5>
                        </div>
                        <div class="p-3 text-center">
                            <small style="color: rgba(255,255,255,0.4);">
                                <i class="bi bi-arrow-right-circle me-1" style="color: var(--3d-gold);"></i>
                                Accéder au cours
                            </small>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="alert" style="background: rgba(239,68,68,0.15); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; display: inline-block;">
                    Aucun cours disponible
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

@endsection
