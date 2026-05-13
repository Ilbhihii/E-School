@extends('layouts.student')

@section('content')

<div class="container py-5">
    <div class="glassmorphism-hover mb-5 p-4 rounded-5 text-center">
        <h1 class="display-5 fw-bold mb-3" style="background: var(--gradient-green); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            <i class="bi bi-book-half me-3"></i>Mes Cours
        </h1>
        <p class="lead text-muted mb-0 animate-fade-in">
            @if($classes->count() > 0)
                Choisissez une classe pour voir les cours
            @else
                Aucune classe assignée. Contactez l'administrateur.
            @endif
        </p>
    </div>

    @if($classes->count() > 0)
    <div class="row g-4">
        @foreach($classes as $class)
        <div class="col-lg-4 col-md-6 col-12">
<a href="{{ route('student.subjects.index') }}" class="text-decoration-none">
</xai:function_call.




<xai:function_call name="edit_file">
<parameter name="path">resources/views/student/class/courses.blade.php
                <div class="card h-100 class-card-modern hover-lift rounded-5 border-0 overflow-hidden position-relative">

                    <!-- Enhanced Banner with Icon Overlay -->
                    <div class="banner-modern position-relative overflow-hidden" style="height: 220px;">
                        <div style="height:100%;
                            background: linear-gradient(135deg,
                            hsl({{ rand(0,360) }},70%,60%),
                            hsl({{ rand(0,360) }},70%,40%) ),
                            linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
                            background-size: 200% 200%;
                            animation: shimmer 3s infinite;">
                        </div>
                        <div class="banner-overlay">
                            <i class="bi bi-book-half display-1 text-white opacity-75"></i>
                        </div>
                    </div>

                    <div class="card-body p-4 text-center">
                        <h4 class="fw-bold mb-3 class-title" style="color: var(--text-dark);">
                            {{ $class->name }}
                        </h4>
                        <span class="badge course-badge px-3 py-2 fw-semibold animate-pulse">
                            <i class="bi bi-play-circle me-1"></i>{{ $class->courses_count }} cours
                        </span>
                    </div>

                </div>
            </a>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-10">
        <i class="bi bi-inbox display-1 text-muted mb-4"></i>
        <h3 class="text-muted mb-3">Aucune classe disponible</h3>
        <p class="text-muted mb-4">Vous n'êtes assigné à aucune classe pour le moment.</p>
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary btn-lg">
            <i class="bi bi-house me-2"></i>Retour au Dashboard
        </a>
    </div>
    @endif
</div>

<style>
:root {
    --primary-green: #20c997;
    --dark-green: #198754;
    --gradient-green: linear-gradient(135deg, #20c997 0%, #198754 100%);
    --text-dark: #2c3e50;
    --card-shadow: 0 10px 40px rgba(0,0,0,0.08);
    --hover-shadow: 0 20px 60px rgba(0,0,0,0.15);
}

.glassmorphism-hover {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: var(--card-shadow);
}

.animate-fade-in {
    animation: fadeInUp 0.8s ease-out;
}

.class-card-modern {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.class-card-modern:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: var(--hover-shadow), 0 0 0 1px rgba(32, 201, 151, 0.3);
    border-color: var(--primary-green);
}

.banner-modern {
    transition: transform 0.4s ease;
}

.class-card-modern:hover .banner-modern {
    transform: scale(1.05);
}

.banner-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.class-card-modern:hover .banner-overlay {
    opacity: 1;
}

@keyframes shimmer {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.course-badge {
    background: var(--gradient-green) !important;
    color: white !important;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(32, 201, 151, 0.4);
}

.class-title {
    background: linear-gradient(135deg, var(--text-dark), #4a5568);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.row > div {
    opacity: 0;
    animation: fadeInUp 0.6s ease forwards;
}

.row > div:nth-child(1) { animation-delay: 0.1s; }
.row > div:nth-child(2) { animation-delay: 0.2s; }
.row > div:nth-child(3) { animation-delay: 0.3s; }
.row > div:nth-child(4) { animation-delay: 0.4s; }
.row > div:nth-child(5) { animation-delay: 0.5s; }
.row > div:nth-child(6) { animation-delay: 0.6s; }

/* Mobile */
@media (max-width: 768px) {
    .display-5 { font-size: 2rem !important; }
    .banner-modern { height: 180px !important; }
}
</style>

@endsection

