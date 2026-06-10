@extends('layouts.front')

@section('content')
<div class="front-page">

    <section class="front-hero front-hero-sm">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-breadcrumb">
                <a href="{{ url('/') }}">Accueil</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('front.classes') }}">Matières</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('front.subject.classes', $subject->id) }}">{{ $subject->name }}</a>
                <i class="bi bi-chevron-right"></i>
                <span>{{ $classe->name }}</span>
            </div>
            <div class="hero-badge"><i class="bi bi-collection"></i> Cours de la classe</div>
            <h1 class="hero-title">{{ $classe->name }}</h1>
            <p class="hero-subtitle">{{ $classe->description ?? 'Explorez tous les cours disponibles pour cette classe.' }}</p>
            @if($courses->count())
            <div class="hero-meta">
                <span><i class="bi bi-play-circle"></i> {{ $courses->count() }} cours</span>
                <span><i class="bi bi-book"></i> {{ $subject->name }}</span>
            </div>
            @endif
        </div>
    </section>

    <section class="front-section">
        <div class="container">
            @if($courses->count())
                <div class="class-courses-grid">
                    @foreach($courses as $course)
                        <a href="{{ route('front.course.show', $course->id) }}" class="cc-card">
                            <div class="cc-card-top">
                                @php
                                    $colors = [['#2563eb','#1d4ed8'],['#059669','#047857'],['#7c3aed','#6d28d9'],['#d97706','#b45309'],['#dc2626','#b91c1c'],['#0891b2','#0e7490']];
                                    $c = $colors[$loop->index % count($colors)];
                                @endphp
                                <div class="cc-card-thumb" style="background: linear-gradient(135deg, {{ $c[0] }}, {{ $c[1] }});">
                                    <div class="cc-card-thumb-shape"></div>
                                    <i class="bi bi-play-circle-fill"></i>
                                </div>
                                <div class="cc-card-info">
                                    <h3 class="cc-card-title">{{ $course->title ?? $course->name ?? '' }}</h3>
                                    <p class="cc-card-desc">{{ Str::limit($course->description ?? 'Cours complet avec ressources pédagogiques.', 80) }}</p>
                                </div>
                            </div>
                            <div class="cc-card-bottom">
                                <div class="cc-card-tags">
                                    @if(isset($course->subject))
                                        <span class="cc-tag">{{ $course->subject->name ?? '' }}</span>
                                    @endif
                                    @if(($course->is_free ?? false) || !($course->is_premium ?? true))
                                        <span class="cc-tag cc-tag-free">Gratuit</span>
                                    @else
                                        <span class="cc-tag cc-tag-premium">Premium</span>
                                    @endif
                                </div>
                                <span class="cc-card-action">
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="front-empty">
                    <div class="front-empty-icon"><i class="bi bi-collection"></i></div>
                    <h3>Aucun cours disponible</h3>
                    <p>Les cours pour cette classe seront bientôt ajoutés.</p>
                </div>
            @endif
        </div>
    </section>

</div>

<style>
.hero-breadcrumb {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 1rem;
    font-size: 12.5px;
    flex-wrap: wrap;
}
.hero-breadcrumb a { color: rgba(255,255,255,.6); text-decoration: none; transition: color .2s; }
.hero-breadcrumb a:hover { color: #fff; }
.hero-breadcrumb i { color: rgba(255,255,255,.3); font-size: 10px; }
.hero-breadcrumb span { color: rgba(255,255,255,.8); }

.hero-meta { margin-top: 1rem; justify-content: center; }
.hero-meta span {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; color: rgba(255,255,255,.7);
    background: rgba(255,255,255,.08);
    padding: 5px 14px; border-radius: 999px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.1);
}
.hero-meta i { font-size: 12px; }

.class-courses-grid { display: flex; flex-direction: column; gap: .85rem; }

.cc-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(15,23,42,.05);
    border: 1px solid rgba(226,232,240,.6);
    text-decoration: none;
    color: inherit;
    transition: all .3s cubic-bezier(.22,.61,.36,1);
    overflow: hidden;
}

.cc-card:hover {
    transform: translateX(6px);
    box-shadow: 0 8px 32px rgba(15,23,42,.12);
    border-color: transparent;
}

.cc-card-top { display: flex; gap: 1.25rem; padding: 1.25rem 1.5rem; }

.cc-card-thumb {
    width: 80px; height: 80px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 28px;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,.12);
    transition: transform .3s ease;
}

.cc-card:hover .cc-card-thumb { transform: scale(1.05); }

.cc-card-thumb-shape {
    position: absolute;
    width: 60px; height: 60px;
    border-radius: 50%;
    background: rgba(255,255,255,.1);
    bottom: -20px; right: -15px;
}

.cc-card-thumb i { position: relative; z-index: 1; }

.cc-card-info { flex: 1; min-width: 0; }

.cc-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .35rem;
    line-height: 1.3;
}

.cc-card-desc {
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
    margin: 0;
}

.cc-card-bottom {
    padding: .85rem 1.5rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cc-card-tags { display: flex; gap: 5px; }

.cc-tag {
    font-size: 11.5px;
    font-weight: 500;
    color: #64748b;
    background: #f8fafc;
    padding: 3px 10px;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
}

.cc-tag-free { color: #166534; background: #dcfce7; border-color: #bbf7d0; }
.cc-tag-premium { color: #92400e; background: #fef3c7; border-color: #fde68a; }

.cc-card-action {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    color: #94a3b8;
    font-size: 14px;
    transition: all .3s ease;
}

.cc-card:hover .cc-card-action {
    background: #2563eb;
    color: #fff;
    box-shadow: 0 4px 12px rgba(37,99,235,.3);
}

@media(max-width:768px) {
    .cc-card-top { flex-direction: column; align-items: flex-start; gap: .85rem; }
    .cc-card-thumb { width: 60px; height: 60px; font-size: 22px; }
}
</style>
@endsection
