@extends('layouts.front')

@section('title', $subject->name)

@section('content')

<!-- HEADER -->
<section class="py-5 text-center text-white" style="background: #003A8F;">
    <div class="container">
        <h1 class="fw-bold">{{ $subject->name }}</h1>
        <p class="opacity-75">Choisissez une classe</p>
    </div>
</section>

<!-- CLASSES -->
<div class="container py-5">
    <div class="row g-4">

        @forelse($subject->classes as $class)
        <div class="col-md-4">
            <a href="{{ route('front.courses', [$subject->id, $class->level_id, $class->id]) }}" 
               class="text-decoration-none">

                <div class="card class-card text-center p-4 h-100 border-0 shadow-sm">
                    
                    <div class="icon mb-3">
                        <i class="bi bi-mortarboard fs-1 text-primary"></i>
                    </div>

                    <h5 class="fw-bold text-dark">{{ $class->name }}</h5>

                    <p class="text-muted small">
                        Accéder aux cours de cette classe
                    </p>

                </div>

            </a>
        </div>
        @empty

        <div class="col-12 text-center">
            <div class="alert alert-danger">
                Aucune classe liée à cette matière
            </div>
        </div>

        @endforelse

    </div>
</div>

<!-- STYLE -->
<style>
.class-card {
    transition: all 0.3s ease;
    border-radius: 15px;
}

.class-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.class-card .icon {
    background: #F5F7FA;
    width: 80px;
    height: 80px;
    margin: auto;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.class-card:hover .icon {
    background: #003A8F;
}

.class-card:hover i {
    color: white !important;
}
</style>

@endsection
