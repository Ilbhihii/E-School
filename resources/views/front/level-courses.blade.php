@extends('layouts.front')

@section('content')
<div class="front-page">

    <section class="front-hero front-hero-sm">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-breadcrumb">
                <a href="{{ url('/') }}">Accueil</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('levels') }}">Niveaux</a>
                <i class="bi bi-chevron-right"></i>
                <span>{{ $level->name ?? '' }}</span>
            </div>
            <div class="hero-badge"><i class="bi bi-play-circle"></i> Cours du niveau</div>
            <h1 class="hero-title">{{ $level->name ?? '' }}</h1>
            <p class="hero-subtitle">{{ $level->description ?? 'Explorez tous les cours disponibles pour ce niveau.' }}</p>
            @if($level->courses->count())
            <div class="hero-meta">
                <span><i class="bi bi-play-circle"></i> {{ $level->courses->count() }} cours</span>
            </div>
            @endif
        </div>
    </section>

    <section class="front-section">
        <div class="container">
            @if($level->courses->count())
                <div class="lc-grid">
                    @foreach($level->courses as $course)
                        <a href="{{ route('front.course.show', $course->id) }}" class="lc-card">
                            @php
                                $palettes = [
                                    ['bg'=>'#eff6ff','icon'=>'#2563eb','num'=>'#dbeafe'],
                                    ['bg'=>'#f0fdf4','icon'=>'#059669','num'=>'#dcfce7'],
                                    ['bg'=>'#faf5ff','icon'=>'#7c3aed','num'=>'#f3e8ff'],
                                    ['bg'=>'#fffbeb','icon'=>'#d97706','num'=>'#fef3c7'],
                                    ['bg'=>'#fef2f2','icon'=>'#dc2626','num'=>'#fee2e2'],
                                    ['bg'=>'#ecfeff','icon'=>'#0891b2','num'=>'#cffafe'],
                                ];
                                $p = $palettes[$loop->index % count($palettes)];
                            @endphp
                            <div class="lc-card-num" style="background: {{ $p['num'] }}; color: {{ $p['icon'] }};">{{ $loop->iteration }}</div>
                            <div class="lc-card-body">
                                <h3 class="lc-card-title">{{ $course->title ?? $course->name ?? '' }}</h3>
                                <p class="lc-card-desc">{{ Str::limit($course->description ?? 'Cours complet avec ressources.', 70) }}</p>
                                <div class="lc-card-tags">
                                    @if(($course->is_free ?? false) || !($course->is_premium ?? true))
                                        <span class="lc-tag lc-tag-free">Gratuit</span>
                                    @else
                                        <span class="lc-tag lc-tag-premium">Premium</span>
                                    @endif
                                </div>
                            </div>
                            <div class="lc-card-action" style="background: {{ $p['bg'] }};">
                                <i class="bi bi-arrow-right" style="color: {{ $p['icon'] }};"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="front-empty">
                    <div class="front-empty-icon"><i class="bi bi-play-circle"></i></div>
                    <h3>Aucun cours disponible</h3>
                    <p>Les cours pour ce niveau seront bientôt ajoutés.</p>
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

.lc-grid { display: flex; flex-direction: column; gap: .75rem; }

.lc-card {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    background: #fff;
    padding: 1rem 1.25rem;
    border-radius: 14px;
    box-shadow: 0 1px 8px rgba(15,23,42,.04);
    border: 1px solid #f1f5f9;
    text-decoration: none;
    color: inherit;
    transition: all .3s cubic-bezier(.22,.61,.36,1);
}

.lc-card:hover {
    transform: translateX(6px);
    border-color: #dbeafe;
    box-shadow: 0 6px 24px rgba(37,99,235,.08);
}

.lc-card-num {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 15px;
    flex-shrink: 0;
    transition: transform .3s ease;
}

.lc-card:hover .lc-card-num { transform: scale(1.1); }

.lc-card-body { flex: 1; min-width: 0; }

.lc-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .25rem;
}

.lc-card-desc {
    font-size: 13px;
    color: #64748b;
    margin: 0 0 .5rem;
    line-height: 1.4;
}

.lc-card-tags { display: flex; gap: 4px; }

.lc-tag {
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 999px;
}

.lc-tag-free { color: #166534; background: #dcfce7; }
.lc-tag-premium { color: #92400e; background: #fef3c7; }

.lc-card-action {
    width: 36px; height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
    transition: all .3s ease;
}

.lc-card:hover .lc-card-action {
    transform: scale(1.1) rotate(-45deg);
}
</style>
@endsection
