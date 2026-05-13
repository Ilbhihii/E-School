@extends('layouts.front')

@section('title', 'Cours')

@section('content')

<!-- HERO -->
<section class="py-5 text-center text-white" style="background:#003A8F;">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Les Cours Disponibles</h1>
        <p class="lead">Choisissez un cours pour commencer votre apprentissage</p>
    </div>
</section>

<!-- COURSES -->
<section class="py-5" style="background:#F5F7FA;">
    <div class="container">
        <div class="row g-4">

            @forelse($courses as $course)
            <div class="col-md-6 col-lg-4">

                <a href="{{ route('front.course.show', $course->id) }}" 
                   class="text-decoration-none">

                    <div class="card course-card border-0 shadow-sm h-100">

                        <!-- Banner -->
                        <div class="course-banner position-relative">

                            <div class="overlay"></div>

                            <h5 class="course-title text-white fw-bold">
                                {{ $course->title }}
                            </h5>

                        </div>

                        <!-- Body -->
                        <div class="card-body text-center">
                            <p class="text-muted small">
                                Cliquez pour accéder au cours
                            </p>
                        </div>

                    </div>

                </a>

            </div>
            @empty

            <div class="text-center py-5">
                <div class="alert alert-danger">
                    Aucun cours disponible
                </div>
            </div>

            @endforelse

        </div>
    </div>
</section>

<!-- STYLE -->
<style>
.course-card {
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.course-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

/* Banner style Netflix */
.course-banner {
    height: 180px;
    position: relative;
    display: flex;
    align-items: flex-end;
    justify-content: center;

    background: linear-gradient(135deg, hsl(200, 70%, 50%), #003A8F);
}

.course-card:nth-of-type(3n+1) .course-banner {
    background: linear-gradient(135deg, hsl(200, 70%, 50%), #003A8F);
}

.course-card:nth-of-type(3n+2) .course-banner {
    background: linear-gradient(135deg, hsl(320, 70%, 50%), #003A8F);
}

.course-card:nth-of-type(3n+3) .course-banner {
    background: linear-gradient(135deg, hsl(40, 70%, 50%), #003A8F);
}

/* Overlay sombre */
.overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.4);
}

/* Title */
.course-title {
    position: relative;
    z-index: 2;
    padding: 10px;
    text-align: center;
}

/* Hover effet */
.course-card:hover .course-banner {
    transform: scale(1.05);
}

/* Animation apparition */
.course-card {
    opacity: 0;
    transform: translateY(30px);
}

.course-card.visible {
    opacity: 1;
    transform: translateY(0);
    transition: all 0.6s ease;
}
</style>

<!-- JS Animation -->
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
