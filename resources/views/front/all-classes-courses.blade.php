@extends('layouts.front')

@section('title', 'Toutes les Classes - Cours et Matières')

@section('content')

<!-- HERO GLOBAL -->
<section class="py-5 text-center" style="background: linear-gradient(135deg,#28a745,#20c997); color:white;">
    <div class="container">
        <h1 class="display-4 fw-bold mb-2">Toutes les Classes et leurs Cours</h1>
        <p class="lead">Explorez tous les cours disponibles par classe et commencez votre apprentissage maintenant.</p>
    </div>
</section>

@foreach($classes as $class)
<!-- CLASS HERO -->
<section class="py-3 text-center" style="background: linear-gradient(135deg, rgba(40,167,69,0.1), rgba(32,201,151,0.1)); border-bottom: 1px solid rgba(40,167,69,0.2);">
    <div class="container">
        <h2 class="h3 fw-bold mb-1">Cours de {{ $class->name }}</h2>
        <p class="text-muted mb-0">{{ $class->courses->count() }} cours disponibles</p>
    </div>
</section>

<!-- COURSES CARDS FOR {{ $class->name }} -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row g-4">

            @foreach($class->courses as $course)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('front.course.show',$course->id) }}" class="text-decoration-none text-dark">
                    <div class="card course-card h-100 rounded-4 shadow position-relative overflow-hidden">

                        <!-- Floating shapes -->
                        <span class="shape shape-top"></span>
                        <span class="shape shape-bottom"></span>

                        <!-- Banner dégradé -->
                        <div class="course-banner" style="height:180px;
                             background: linear-gradient(135deg,
                             hsl({{ rand(0,360) }},70%,60%),
                             hsl({{ rand(0,360) }},70%,40%));
                             transition: all 0.5s ease;">
                        </div>

                        <div class="card-body text-center">
                            <h5 class="fw-bold">{{ $course->title }}</h5>
                            <small class="text-muted">Classe {{ $class->name }}</small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach

            @if($class->courses->isEmpty())
            <div class="col-12 text-center py-5">
                <p class="text-muted">Aucun cours disponible pour cette classe pour le moment.</p>
            </div>
            @endif

        </div>
    </div>
</section>

@if($class->subjects && $class->subjects->count() > 0)
<!-- SUBJECTS / MATIÈRES CARDS FOR {{ $class->name }} -->
<section class="py-4">
    <div class="container">
        <h3 class="text-center mb-4 fw-bold">Matières Associées à {{ $class->name }}</h3>
        <div class="row g-4">
            @foreach($class->subjects as $subject)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('front.class.courses', $subject->class_id) }}" class="text-decoration-none text-dark">
                    <div class="card course-card h-100 rounded-4 shadow position-relative overflow-hidden">
                        <span class="shape shape-top"></span>
                        <span class="shape shape-bottom"></span>
                        <div class="course-banner" style="height:180px; background: linear-gradient(135deg, hsl({{ rand(0,360) }},70%,60%), hsl({{ rand(0,360) }},70%,40%)); transition: all 0.5s ease;"></div>
                        <div class="card-body text-center">
                            <h5 class="fw-bold">{{ $subject->name }}</h5>
                            <span class="badge bg-primary">{{ $subject->courses->count() ?? 0 }} cours</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endforeach

<!-- CUSTOM CSS (same as original) -->
<style>
.course-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: rgba(255,255,255,0.95);
}

.course-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 40px rgba(0,0,0,0.1);
}

.course-card .course-banner {
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
}

/* Floating shapes */
.shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.15;
    z-index:0;
}

.shape-top {
    width: 60px;
    height: 60px;
    background: #28a745;
    top: -15px;
    right: -15px;
}

.shape-bottom {
    width: 40px;
    height: 40px;
    background: #20c997;
    bottom: -10px;
    left: -10px;
}

/* Hover banner */
.course-card:hover .course-banner {
    transform: scale(1.05);
    filter: brightness(1.1);
    transition: all 0.5s ease;
}

/* Animation au scroll */
.course-card {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s ease-out;
}

.course-card.visible {
    opacity: 1;
    transform: translateY(0);
}
</style>

<!-- JS pour animation au scroll -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.course-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting){
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.2 });

    cards.forEach(card => observer.observe(card));
});
</script>

@endsection

