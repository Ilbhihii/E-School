@extends('layouts.front')

@section('title', 'Matières')

@section('content')

<!-- HEADER -->
<section class="py-5 text-center text-white" style="background:#003A8F;">
    <div class="container">
        <h1 class="fw-bold">Nos Matières</h1>
        <p class="opacity-75">Choisissez une matière</p>
    </div>
</section>

<!-- LISTE MATIÈRES -->
<div class="container py-5">
    <div class="row g-4">

        @forelse($subjects as $subject)
        <div class="col-md-4">

            <a href="{{ route('front.subject.levels', $subject->id) }}"
               class="text-decoration-none">

                <div class="card subject-card text-center p-4 h-100 border-0 shadow-sm">

                    <div class="icon mb-3">
                        <i class="bi bi-book fs-1 text-primary"></i>
                    </div>

                    <h5 class="fw-bold text-dark">{{ $subject->name }}</h5>

                    <p class="text-muted small">
                        Voir les classes disponibles
                    </p>

                </div>

            </a>

        </div>
        @empty

        <div class="col-12 text-center">
            <div class="alert alert-danger">
                Aucune matière trouvée
            </div>
        </div>

        @endforelse

    </div>
</div>

<!-- STYLE -->
<style>
.subject-card {
    border-radius: 15px;
    transition: all 0.3s ease;
}

.subject-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.subject-card .icon {
    background: #F5F7FA;
    width: 80px;
    height: 80px;
    margin: auto;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.subject-card:hover .icon {
    background: #003A8F;
}

.subject-card:hover i {
    color: white !important;
}
</style>

@endsection
