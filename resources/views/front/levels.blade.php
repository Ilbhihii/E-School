@extends('layouts.front')

@section('content')

<section class="py-5">
    <div class="container text-center mb-5">
        <span class="badge px-3 py-2 mb-3" style="background: rgba(124,58,237,0.15); color: #A78BFA; border-radius: 20px; font-weight: 500; font-size: 0.8rem;">
            Orientation
        </span>
        <h2 class="section-title-3d">Choisissez votre niveau</h2>
        <p class="text-white-50" style="max-width: 500px; margin: 0 auto;">Sélectionnez votre niveau scolaire pour accéder aux cours correspondants</p>
    </div>

    <div class="container">
        <div class="row g-4 justify-content-center">
            @foreach($levels as $level)
            <div class="col-md-4">
                <a href="{{ route('levels.courses', $level->id) }}" class="text-decoration-none">
                    <div class="card-3d text-center h-100 reveal-3d" style="cursor: pointer;">
                        <div class="card-3d-icon mx-auto">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <h4 class="fw-bold text-white mt-3 mb-1" style="font-family: 'Poppins', sans-serif;">{{ $level->name }}</h4>
                        <p class="text-white-50 small mb-0">Voir les cours disponibles <i class="bi bi-arrow-right ms-1" style="color: var(--3d-gold);"></i></p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
