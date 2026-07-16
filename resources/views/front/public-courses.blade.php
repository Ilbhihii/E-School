@extends('layouts.front')

@section('title', $subject->name . ' — Cours')

@push('head')
<style>
.level-hero {
    position: relative;
    padding: 3rem 0 2rem;
    overflow: hidden;
}
.level-hero-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #0a1628 0%, #1a1040 50%, #0f2027 100%);
    z-index: 0;
}
.public-card {
    background: rgba(255,255,255,0.04);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 20px;
    padding: 2rem 1.5rem;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    text-decoration: none;
    display: block;
    height: 100%;
    position: relative;
    overflow: hidden;
}
.public-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.04), transparent);
    transition: left 0.6s;
}
.public-card:hover::before { left: 100%; }
.public-card:hover {
    transform: translateY(-8px) scale(1.02);
    border-color: rgba(124,58,237,0.2);
    box-shadow: 0 20px 50px rgba(0,0,0,0.2);
}
.public-card-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}
.public-card:hover .public-card-icon {
    transform: scale(1.1) rotate(-6deg);
}
html.light-mode .level-hero-bg {
    background: linear-gradient(135deg, #f0f4ff 0%, #f5f0ff 50%, #f0f7fa 100%);
}
html.light-mode .level-hero h1 { color: #1e293b !important; }
html.light-mode .text-white-50 { color: #64748b !important; }
html.light-mode .public-card {
    background: rgba(255,255,255,0.85);
    border-color: rgba(0,0,0,0.08);
}
html.light-mode .public-card:hover {
    border-color: rgba(124,58,237,0.2);
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
}
html.light-mode .public-card h5 { color: #1e293b !important; }
html.light-mode .public-card .text-white-50 { color: #64748b !important; }
</style>
@endpush

@section('content')

<section class="level-hero">
    <div class="level-hero-bg"></div>
    <div class="container position-relative" style="z-index:1;">
        <nav style="display:flex;align-items:center;gap:8px;margin-bottom:1rem;font-size:0.82rem;color:rgba(255,255,255,0.35);flex-wrap:wrap;">
            <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                <i class="bi bi-house me-1"></i>Accueil
            </a>
            <span style="color:rgba(255,255,255,0.12);">/</span>
            <a href="{{ route('front.niveaux') }}" style="color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                Niveaux
            </a>
            <span style="color:rgba(255,255,255,0.12);">/</span>
            <a href="{{ route('front.public.classes', $level->id) }}" style="color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                {{ $level->name }}
            </a>
            <span style="color:rgba(255,255,255,0.12);">/</span>
            <a href="{{ route('front.public.subjects', [$level->id, $class->id]) }}" style="color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                {{ $class->name }}
            </a>
            <span style="color:rgba(255,255,255,0.12);">/</span>
            <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ $subject->name }}</span>
        </nav>

        <div class="text-center">
            <h1 class="fw-bold" style="font-size:2rem;color:white;font-family:'Poppins',sans-serif;">
                <i class="bi bi-play-circle me-2" style="color:rgba(255,255,255,0.2);"></i>{{ $subject->name }}
            </h1>
            <p class="text-white-50">{{ $class->name }} — {{ $level->name }}</p>
            <p class="text-white-50 small mt-2">{{ $courses->count() }} cours disponible(s)</p>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if($courses->count() > 0)
        <div class="row g-4">
            @foreach($courses as $course)
            @php
                $hue = ($loop->index * 70 + 190) % 360;
            @endphp
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('front.course.show', $course->id) }}" class="public-card" style="text-align:left;">
                    <div class="public-card-icon" style="width:50px;height:50px;font-size:1.2rem;margin:0 0 0.75rem;background:hsla({{ $hue }},50%,50%,0.1);color:hsl({{ $hue }},60%,60%);">
                        <i class="bi bi-play-circle-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:1rem;">
                        {{ $course->title }}
                    </h5>
                    @if($course->description)
                    <p class="text-white-50 small mb-3" style="line-height:1.5;font-size:0.8rem;">{{ Str::limit($course->description, 80) }}</p>
                    @endif
                    <div style="display:flex;flex-wrap:wrap;gap:8px;font-size:0.72rem;">
                        @if(!$course->is_free)
                        <span style="background:rgba(255,209,102,0.12);color:#FFD166;padding:3px 10px;border-radius:6px;">
                            <i class="bi bi-lock me-1"></i>Premium
                        </span>
                        @endif
                        @if($course->learning_tests_count > 0)
                        <span style="background:rgba(124,58,237,0.12);color:#A78BFA;padding:3px 10px;border-radius:6px;">
                            <i class="bi bi-pencil-square me-1"></i>{{ $course->learning_tests_count }} test(s)
                        </span>
                        @endif
                        <span style="color:rgba(255,255,255,0.3);padding:3px 0;">
                            <i class="bi bi-arrow-right"></i> Voir le cours
                        </span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-play-circle" style="font-size:2rem;color:rgba(255,255,255,0.15);"></i>
            </div>
            <h5 style="color:rgba(255,255,255,0.4);font-weight:600;">Aucun cours pour cette matière</h5>
            <p class="text-white-50 small">Les cours seront bientôt disponibles.</p>
        </div>
        @endif
    </div>
</section>

@endsection
