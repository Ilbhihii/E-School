@extends('layouts.front')

@section('content')
<div class="front-page">

    <section class="front-hero front-hero-sm">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-badge"><i class="bi bi-collection-fill"></i> Tous les cours</div>
            <h1 class="hero-title">Tous les <span class="text-gradient">cours</span></h1>
            <p class="hero-subtitle">Explorez l'intégralité des cours disponibles, classés par niveau et par classe.</p>
        </div>
    </section>

    <section class="front-section">
        <div class="container">
            @if($classes->count())
                @foreach($classes as $class)
                    @if($class->courses->count())
                    <div class="acc-group">
                        <div class="acc-group-header">
                            <div class="acc-group-icon">
                                <i class="bi bi-layers-fill"></i>
                            </div>
                            <h2 class="acc-group-title">{{ $class->name }}</h2>
                            <span class="acc-group-count">{{ $class->courses->count() }} cours</span>
                        </div>
                        <div class="acc-grid">
                            @foreach($class->courses as $course)
                                <a href="{{ route('front.course.show', $course->id) }}" class="acc-card">
                                    @php
                                        $hue = ($loop->index * 47 + $loop->parent->index * 60) % 360;
                                    @endphp
                                    <div class="acc-card-thumb" style="background: hsl({{ $hue }}, 55%, 50%);">
                                        <i class="bi bi-play-circle-fill"></i>
                                    </div>
                                    <div class="acc-card-body">
                                        <h3 class="acc-card-title">{{ $course->title ?? $course->name ?? '' }}</h3>
                                        <p class="acc-card-desc">{{ Str::limit($course->description ?? 'Cours complet.', 60) }}</p>
                                        <div class="acc-card-meta">
                                            @if(isset($course->subject))
                                                <span class="acc-meta-tag">{{ $course->subject->name ?? '' }}</span>
                                            @endif
                                            @if(($course->is_free ?? false) || !($course->is_premium ?? true))
                                                <span class="acc-meta-tag acc-meta-free">Gratuit</span>
                                            @else
                                                <span class="acc-meta-tag acc-meta-premium">Premium</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            @else
                <div class="front-empty">
                    <div class="front-empty-icon"><i class="bi bi-collection"></i></div>
                    <h3>Aucun cours disponible</h3>
                    <p>Les cours seront bientôt publiés sur la plateforme.</p>
                </div>
            @endif
        </div>
    </section>

</div>

<style>
.acc-group { margin-bottom: 2rem; }

.acc-group-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1rem;
    padding-bottom: .75rem;
    border-bottom: 2px solid #f1f5f9;
}

.acc-group-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 16px;
    flex-shrink: 0;
}

.acc-group-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.acc-group-count {
    margin-left: auto;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 3px 12px;
    border-radius: 999px;
}

.acc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: .85rem;
}

.acc-card {
    display: flex;
    gap: .85rem;
    background: #fff;
    padding: .85rem 1rem;
    border-radius: 14px;
    box-shadow: 0 1px 8px rgba(15,23,42,.04);
    border: 1px solid #f1f5f9;
    text-decoration: none;
    color: inherit;
    transition: all .3s cubic-bezier(.22,.61,.36,1);
}

.acc-card:hover {
    transform: translateY(-3px);
    border-color: #dbeafe;
    box-shadow: 0 8px 24px rgba(37,99,235,.08);
}

.acc-card-thumb {
    width: 52px; height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
}

.acc-card-body { flex: 1; min-width: 0; }

.acc-card-title {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 .25rem;
    line-height: 1.3;
}

.acc-card-desc {
    font-size: 12px;
    color: #64748b;
    margin: 0 0 .5rem;
    line-height: 1.4;
}

.acc-card-meta { display: flex; gap: 4px; }

.acc-meta-tag {
    font-size: 10px;
    font-weight: 500;
    color: #64748b;
    background: #f8fafc;
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
}

.acc-meta-free { color: #166534; background: #dcfce7; border-color: #bbf7d0; }
.acc-meta-premium { color: #92400e; background: #fef3c7; border-color: #fde68a; }

@media(max-width:768px) {
    .acc-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
