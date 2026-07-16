@extends('layouts.front')

@section('title', 'Niveaux')

@push('head')
<style>
.level-hero {
    position: relative;
    padding: 4rem 0 3rem;
    overflow: hidden;
}
.level-hero-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #0a1628 0%, #1a1040 50%, #0f2027 100%);
    z-index: 0;
}
.level-hero-bg::before {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(124,58,237,0.12), transparent 70%);
    top: -150px;
    right: -100px;
    animation: heroDrift 12s ease-in-out infinite;
}
.level-hero-bg::after {
    content: '';
    position: absolute;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(2,132,199,0.1), transparent 70%);
    bottom: -100px;
    left: -80px;
    animation: heroDrift 15s ease-in-out infinite reverse;
}
@keyframes heroDrift {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(40px, -30px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.95); }
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
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.4rem;
    transition: all 0.3s ease;
}
.public-card:hover .public-card-icon {
    transform: scale(1.1) rotate(-6deg);
}

html.light-mode .level-hero-bg {
    background: linear-gradient(135deg, #f0f4ff 0%, #f5f0ff 50%, #f0f7fa 100%);
}
html.light-mode .level-hero-bg::before {
    background: radial-gradient(circle, rgba(124,58,237,0.06), transparent 70%);
}
html.light-mode .level-hero-bg::after {
    background: radial-gradient(circle, rgba(2,132,199,0.05), transparent 70%);
}
html.light-mode .level-hero h1 { color: #1e293b !important; }
html.light-mode .level-hero .text-white-50 { color: #64748b !important; }
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
html.light-mode .section-title-3d { color: #1e293b !important; }
html.light-mode .text-white-50 { color: #64748b !important; }
html.light-mode .card-3d {
    background: rgba(255,255,255,0.85);
    border-color: rgba(0,0,0,0.08);
}

.gradient-text {
    background: linear-gradient(135deg, #A78BFA, #38BDF8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endpush

@section('content')

<!-- HERO -->
<section class="level-hero">
    <div class="level-hero-bg"></div>
    <div class="container position-relative" style="z-index:1;">
        <div class="text-center">
            <span class="badge px-3 py-2 mb-3" style="background:rgba(255,209,102,0.12);color:#FFD166;border-radius:20px;font-weight:500;font-size:0.8rem;">
                Parcours d'apprentissage
            </span>
            <h1 class="fw-bold mb-3" style="font-size:2.5rem;color:white;font-family:'Poppins',sans-serif;">
                Choisissez votre niveau
            </h1>
            <p class="text-white-50" style="max-width:500px;margin:0 auto 1.5rem;font-size:1.05rem;">
                Sélectionnez le niveau qui correspond à votre parcours pour explorer les classes, les matières et les cours disponibles.
            </p>
        </div>

        <!-- Stats -->
        <div style="display:flex;justify-content:center;gap:2.5rem;margin-top:1.5rem;">
            <div class="text-center">
                <div class="gradient-text" style="font-size:1.8rem;font-weight:800;">{{ $levels->count() }}</div>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.4);font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Niveaux</div>
            </div>
            <div class="text-center">
                <div class="gradient-text" style="font-size:1.8rem;font-weight:800;">{{ $totalClasses }}</div>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.4);font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Classes</div>
            </div>
            <div class="text-center">
                <div class="gradient-text" style="font-size:1.8rem;font-weight:800;">{{ $totalCourses }}</div>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.4);font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Cours</div>
            </div>
        </div>
    </div>
</section>

<!-- NIVEAUX -->
<section class="py-5">
    <div class="container">
        @if($levels->count() > 0)
        <div class="row g-4">
            @foreach($levels as $level)
            @php
                $hue = ($loop->index * 60 + 260) % 360;
                $icons = ['bi-mortarboard-fill', 'bi-book-fill', 'bi-bar-chart-fill', 'bi-stars', 'bi-layers'];
                $icon = $icons[$loop->index % count($icons)];
                $classCount = $level->classes->count();
            @endphp
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('front.public.classes', $level->id) }}" class="public-card">
                    <div class="public-card-icon" style="background:hsla({{ $hue }},50%,50%,0.1);color:hsl({{ $hue }},60%,60%);">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;">{{ $level->name }}</h5>
                    @if($level->description)
                    <p class="text-white-50 small mb-3" style="line-height:1.6;">{{ $level->description }}</p>
                    @endif
                    <div style="display:flex;justify-content:center;gap:16px;font-size:0.75rem;color:rgba(255,255,255,0.3);">
                        <span><i class="bi bi-building me-1"></i>{{ $classCount }} classe(s)</span>
                        <span><i class="bi bi-arrow-right" style="color:hsl({{ $hue }},60%,60%);"></i></span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-emoji-neutral" style="font-size:2rem;color:rgba(255,255,255,0.15);"></i>
            </div>
            <h5 style="color:rgba(255,255,255,0.4);font-weight:600;">Aucun niveau disponible</h5>
            <p class="text-white-50 small">Les niveaux seront bientôt disponibles.</p>
        </div>
        @endif
    </div>
</section>



@endsection
