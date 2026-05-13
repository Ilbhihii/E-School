@extends('layouts.student')

@section('content')

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0f0f23 0%, #1a1a3e 50%, #2a1a4e 100%); min-height: 80vh; position: relative; overflow: hidden;">
    <!-- Animated particles background -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="pointer-events: none; z-index: 1;">
        <div class="position-absolute" style="top: 20%; left: 10%; width: 4px; height: 4px; background: rgba(139, 92, 246, 0.6); border-radius: 50%; animation: float 6s infinite linear;"></div>
        <div class="position-absolute" style="top: 60%; left: 80%; width: 6px; height: 6px; background: rgba(59, 130, 246, 0.6); border-radius: 50%; animation: float 8s infinite linear reverse;"></div>
        <div class="position-absolute" style="bottom: 30%; right: 20%; width: 3px; height: 3px; background: rgba(6, 182, 212, 0.6); border-radius: 50%; animation: float 5s infinite linear;"></div>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex align-items-center mb-5 position-relative z-2">
                <div class="neon-icon rounded-circle p-5 me-4 shadow-2xl border-4 border-indigo-500/30" style="width: 100px; height: 100px; background: linear-gradient(135deg, #8b5cf6, #3b82f6); box-shadow: 0 0 40px rgba(139, 92, 246, 0.7), inset 0 0 20px rgba(255,255,255,0.2); animation: neonPulse 2s ease-in-out infinite alternate;">
                    <i class="bi bi-easel3-fill text-white fs-1" style="filter: drop-shadow(0 0 10px rgba(255,255,255,1));"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1 nebula-gradient-text fs-1 lh-1 position-relative" style="background: linear-gradient(135deg, #8b5cf6, #3b82f6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 0 0 30px rgba(139, 92, 246, 0.9);">Cours de la classe</h2>
                    <h3 class="mb-0 text-white fw-bold fs-3 lh-1" style="background: linear-gradient(90deg, #ffffff, #a5b4fc, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 0 0 20px rgba(6, 182, 212, 0.7);">{{ $class->name }}</h3>
                </div>
            </div>

            <div class="row g-4 position-relative z-2">
                @foreach($class->courses as $course)
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('student.course.show', $course->id) }}"
                           class="text-decoration-none text-white h-100 d-block">

                            <div class="custom-card neumorphism-hover glass-card hover-lift shadow-xl h-100 rounded-5 border-0 overflow-hidden position-relative animate__animated animate__fadeInUp"
                                 style="animation-delay: {{ (loop.index - 1) * 0.15 }}s; background: rgba(15, 15, 35, 0.4); backdrop-filter: blur(25px); border: 1px solid rgba(139, 92, 246, 0.3);">

                                <!-- Nebula Gradient Header with Icon Overlay -->
                                <div class="position-relative" style="height: 220px;
                                    background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 50%, #06b6d4 100%);
                                    background-image: 
                                        radial-gradient(circle at 30% 20%, rgba(255,255,255,0.3) 0%, transparent 50%),
                                        radial-gradient(circle at 70% 80%, rgba(255,255,255,0.2) 0%, transparent 50%);
                                    box-shadow: inset 0 0 50px rgba(0,0,0,0.3);">
                                    
                                    <!-- Orbital Ring -->
                                    <div class="position-absolute top-50 start-50 translate-middle" style="width: 80px; height: 80px;">
                                        <div class="position-absolute inset-0 border-2 border-white/20 rounded-full animate-spin-slow"></div>
                                    </div>
                                    
                                    <!-- Course Icon -->
                                    <div class="position-absolute top-50 start-50 translate-middle text-white fs-1 opacity-95 neon-glow">
                                        <i class="bi bi-journal-bookmark-fill" style="filter: drop-shadow(0 0 15px rgba(255,255,255,0.9)); animation: iconFloat 3s ease-in-out infinite;"></i>
                                    </div>
                                    
                                    <!-- Multi-layered Prism Shine -->
                                    <div class="position-absolute top-0 end-0 w-60 h-60 bg-gradient-white opacity-15" 
                                         style="clip-path: polygon(0 0, 100% 0, 0 100%); filter: blur(10px);"></div>
                                    <div class="position-absolute top-10 end-10 w-40 h-40 bg-cyan-300/20 rounded-full" 
                                         style="filter: blur(20px); animation: shine 4s infinite linear;"></div>
                                </div>

                                <!-- Card Body -->
                                <div class="p-4 position-relative z-2" style="color: #f8fafc;">
                                    <h5 class="fw-bold mb-4 text-truncate lh-sm fs-5" style="color: #f8fafc; text-shadow: 0 1px 3px rgba(0,0,0,0.5);">{{ $course->title }}</h5>
                                    
                                    <div class="d-flex flex-wrap gap-2 mb-4">
                                        @if($course->pdf)
                                            <span class="badge nebula-badge-pdf px-3 py-2 fw-semibold fs-6 rounded-pill shadow-lg" style="background: linear-gradient(135deg, #a855f7, #d946ef); box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);">
                                                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                                            </span>
                                        @endif

                                        @if($course->video)
                                            <span class="badge nebula-badge-video px-3 py-2 fw-semibold fs-6 rounded-pill shadow-lg" style="background: linear-gradient(135deg, #0ea5e9, #14b8a6); box-shadow: 0 4px 15px rgba(6, 182, 212, 0.5); animation: badgePulse 2s infinite;">
                                                <i class="bi bi-play-circle me-1"></i>Vidéo
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Nebula Footer -->
                                    <div class="pt-3 border-top border-indigo-500/30">
                                        <div class="position-relative">
                                            <small class="text-indigo-200 d-flex align-items-center fw-medium">
                                                <i class="bi bi-arrow-right-circle-fill me-2 fs-5" style="color: #06b6d4; animation: arrowBounce 2s infinite;"></i>
                                                Cliquez pour voir le cours
                                            </small>
                                            <div class="position-absolute bottom-0 start-0 w-20 h-1 bg-gradient-to-r from-purple-400 to-cyan-400 rounded-full" style="box-shadow: 0 0 10px rgba(139, 92, 246, 0.8);"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --nebula-gradient: linear-gradient(135deg, #8b5cf6, #3b82f6, #06b6d4);
        --neon-glow: 0 0 20px rgba(139, 92, 246, 0.6);
        --nebula-shadow: 0 25px 80px rgba(139, 92, 246, 0.3);
    }

    .nebula-gradient-text {
        background: var(--nebula-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .custom-card:hover {
        transform: translateY(-12px) scale(1.05) !important;
        box-shadow: var(--nebula-shadow), inset 0 0 30px rgba(255,255,255,0.1) !important;
        border-color: rgba(139, 92, 246, 0.6) !important;
    }

    .custom-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--nebula-gradient);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .custom-card:hover::before {
        transform: scaleX(1);
    }

    @keyframes neonPulse {
        0%, 100% { box-shadow: 0 0 40px rgba(139, 92, 246, 0.7), inset 0 0 20px rgba(255,255,255,0.2); }
        50% { box-shadow: 0 0 60px rgba(139, 92, 246, 1), inset 0 0 30px rgba(255,255,255,0.3); }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); opacity: 0.6; }
        50% { transform: translateY(-20px); opacity: 1; }
    }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-8px) rotate(5deg); }
    }

    @keyframes shine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0; }
        50% { opacity: 1; }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0; }
    }

    @keyframes arrowBounce {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(5px); }
    }

    @keyframes badgePulse {
        0%, 100% { box-shadow: 0 4px 15px rgba(6, 182, 212, 0.5); }
        50% { box-shadow: 0 6px 25px rgba(6, 182, 212, 0.8); }
    }

    @keyframes animate-spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .neon-glow {
        text-shadow: 0 0 15px rgba(255,255,255,0.9), 0 0 25px rgba(139, 92, 246, 0.8);
    }

    .animate-spin-slow {
        animation-duration: 10s;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate__animated {
        animation-duration: 1s;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .col-lg-3 { margin-bottom: 1.5rem; }
        h2.fs-1 { font-size: 1.75rem !important; }
    }
</style>

@endsection

