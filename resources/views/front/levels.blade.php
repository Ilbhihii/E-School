@extends('layouts.front')

@section('content')
<div class="front-page">

    {{-- Hero Section --}}
    <section class="front-hero front-hero-sm">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-badge">
                <i class="bi bi-layers"></i> Niveaux
            </div>
            <h1 class="hero-title">Choisissez votre <span class="text-gradient">niveau</span></h1>
            <p class="hero-subtitle">Sélectionnez votre niveau d'études pour accéder aux cours et ressources adaptés.</p>
        </div>
    </section>

    {{-- Levels Grid --}}
    <section class="front-section">
        <div class="container">
            @if($levels->count())
                <div class="levels-grid">
                    @foreach($levels as $level)
                        <a href="{{ route('front.level.courses', $level->id) }}" class="level-card">
                            <div class="level-card-bg">
                                <div class="level-card-shape level-card-shape-1"></div>
                                <div class="level-card-shape level-card-shape-2"></div>
                            </div>
                            <div class="level-card-icon">
                                @php
                                    $icons = ['bi-book','bi-mortarboard','bi-briefcase','bi-cpu','bi-heart-pulse','bi-graph-up'];
                                    $gradients = [
                                        '#2563eb, #1d4ed8',
                                        '#059669, #047857',
                                        '#7c3aed, #6d28d9',
                                        '#d97706, #b45309',
                                        '#dc2626, #b91c1c',
                                        '#0891b2, #0e7490',
                                    ];
                                    $idx = $loop->index % count($icons);
                                @endphp
                                <div class="level-icon-circle" style="background: linear-gradient(135deg, {{ $gradients[$idx] }});">
                                    <i class="bi {{ $icons[$idx] }}"></i>
                                </div>
                            </div>
                            <h3 class="level-card-title">{{ $level->name }}</h3>
                            <p class="level-card-desc">{{ $level->description ?? 'Explorez les cours et ressources de ce niveau.' }}</p>
                            <span class="level-card-action">
                                Explorer
                                <i class="bi bi-arrow-right"></i>
                            </span>
                            <div class="level-card-count">
                                <i class="bi bi-collection"></i>
                                {{ $level->classes_count ?? $level->classes->count() ?? 0 }} classes
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="front-empty">
                    <div class="front-empty-icon"><i class="bi bi-search"></i></div>
                    <h3>Aucun niveau disponible</h3>
                    <p>Les niveaux seront bientôt ajoutés. Revenez plus tard.</p>
                </div>
            @endif
        </div>
    </section>

</div>

<style>
:root {
    --level-card-radius: 20px;
    --level-card-shadow: 0 4px 24px rgba(15,23,42,.08);
    --level-card-shadow-hover: 0 20px 60px rgba(15,23,42,.18);
}

.levels-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.level-card {
    position: relative;
    background: #fff;
    border-radius: var(--level-card-radius);
    padding: 2rem 1.5rem;
    text-decoration: none;
    color: inherit;
    box-shadow: var(--level-card-shadow);
    transition: all .4s cubic-bezier(.22,.61,.36,1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: .75rem;
    border: 1px solid rgba(226,232,240,.5);
}

.level-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--level-card-shadow-hover);
    border-color: transparent;
}

.level-card-bg {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
    opacity: 0;
    transition: opacity .4s ease;
}

.level-card:hover .level-card-bg {
    opacity: 1;
}

.level-card-shape {
    position: absolute;
    border-radius: 50%;
}

.level-card-shape-1 {
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(37,99,235,.06) 0%, transparent 70%);
    top: -80px; right: -60px;
}

.level-card-shape-2 {
    width: 150px; height: 150px;
    background: radial-gradient(circle, rgba(99,102,241,.04) 0%, transparent 70%);
    bottom: -40px; left: -40px;
}

.level-card-icon { position: relative; z-index: 1; }

.level-icon-circle {
    width: 64px; height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 26px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    transition: transform .3s ease, box-shadow .3s ease;
}

.level-card:hover .level-icon-circle {
    transform: scale(1.08);
    box-shadow: 0 12px 32px rgba(0,0,0,.2);
}

.level-card-title {
    position: relative; z-index: 1;
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.level-card-desc {
    position: relative; z-index: 1;
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
    margin: 0;
}

.level-card-action {
    position: relative; z-index: 1;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #2563eb;
    margin-top: auto;
    transition: gap .3s ease;
}

.level-card:hover .level-card-action {
    gap: 10px;
}

.level-card-action i { font-size: 12px; transition: transform .3s ease; }
.level-card:hover .level-card-action i { transform: translateX(2px); }

.level-card-count {
    position: relative; z-index: 1;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: #94a3b8;
    background: #f8fafc;
    padding: 4px 12px;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
    margin-top: 4px;
}

.level-card-count i { font-size: 11px; }

/* Responsive */
@media(max-width:768px) {
    .levels-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem; }
    .level-card { padding: 1.5rem 1rem; }
}
</style>
@endsection
