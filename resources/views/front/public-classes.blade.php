@extends('layouts.front')

@section('title', $level->name . ' — Classes')

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
            <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ $level->name }}</span>
        </nav>

        <div class="text-center">
            <h1 class="fw-bold" style="font-size:2rem;color:white;font-family:'Poppins',sans-serif;">
                <i class="bi bi-building me-2" style="color:rgba(255,255,255,0.2);"></i>{{ $level->name }}
            </h1>
            @if($level->description)
            <p class="text-white-50" style="max-width:500px;margin:0 auto;">{{ $level->description }}</p>
            @endif
            <p class="text-white-50 small mt-2">{{ $classes->count() }} classe(s) disponible(s)</p>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if($classes->count() > 0)
        <div class="row g-4">
            @foreach($classes as $class)
            @php
                $hue = ($loop->index * 70 + 160) % 360;
                $classGradients = ['#16A34A','#2563EB','#D97706','#7C3AED','#DC2626','#0891B2'];
                $gIdx = $loop->index % count($classGradients);
            @endphp
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('front.public.subjects', [$level->id, $class->id]) }}" class="public-card">
                    <div class="public-card-icon" style="background:{{ $classGradients[$gIdx] }}22;color:{{ $classGradients[$gIdx] }};">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;">{{ $class->name }}</h5>
                    <p class="text-white-50 small mb-3">Accédez aux matières de cette classe</p>
                    <div style="display:flex;justify-content:center;gap:12px;font-size:0.75rem;color:rgba(255,255,255,0.3);">
                        <span><i class="bi bi-book me-1"></i>{{ $class->subjects_count ?? 0 }} matière(s)</span>
                        <span><i class="bi bi-arrow-right" style="color:{{ $classGradients[$gIdx] }};"></i></span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-building" style="font-size:2rem;color:rgba(255,255,255,0.15);"></i>
            </div>
            <h5 style="color:rgba(255,255,255,0.4);font-weight:600;">Aucune classe pour ce niveau</h5>
            <p class="text-white-50 small">Les classes seront bientôt disponibles.</p>
        </div>
        @endif
    </div>
</section>

@endsection
