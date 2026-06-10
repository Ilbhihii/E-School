@extends('layouts.front')

@section('content')
<div class="front-page">

    <section class="front-hero front-hero-sm">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-badge">
                <i class="bi bi-book"></i> Matières
            </div>
            <h1 class="hero-title"><span class="text-gradient">{{ $subject->name }}</span></h1>
            <p class="hero-subtitle">Choisissez une matière pour explorer les cours et ressources disponibles.</p>
        </div>
    </section>

    <section class="front-section">
        <div class="container">
            @if($subject->levels->count())
                <div class="subjects-grid">
                    @foreach($subject->levels as $level)
                        <a href="{{ route('front.level.courses', $level->id) }}" class="subject-card">
                            <div class="subject-card-bg"></div>
                            @php
                                $subjectIcons = ['bi-cpu','bi-calculator','bi-globe2','bi-flask','bi-book','bi-music-note','bi-palette','bi-translate','bi-activity','bi-star'];
                                $subjectGradients = [
                                    'linear-gradient(135deg, #2563eb, #1d4ed8)',
                                    'linear-gradient(135deg, #059669, #047857)',
                                    'linear-gradient(135deg, #7c3aed, #6d28d9)',
                                    'linear-gradient(135deg, #d97706, #b45309)',
                                    'linear-gradient(135deg, #dc2626, #b91c1c)',
                                    'linear-gradient(135deg, #0891b2, #0e7490)',
                                    'linear-gradient(135deg, #db2777, #be185d)',
                                    'linear-gradient(135deg, #4f46e5, #4338ca)',
                                    'linear-gradient(135deg, #0d9488, #0f766e)',
                                    'linear-gradient(135deg, #ea580c, #c2410c)',
                                ];
                                $idx = $loop->index % count($subjectIcons);
                                $count = $level->courses_count ?? $level->courses->count() ?? 0;
                            @endphp
                            <div class="subject-card-icon" style="background: {{ $subjectGradients[$idx] }};">
                                <i class="bi {{ $subjectIcons[$idx] }}"></i>
                            </div>
                            <h3 class="subject-card-title">{{ $level->name }}</h3>
                            <p class="subject-card-desc">{{ Str::limit($level->description ?? 'Explorez ce niveau.', 70) }}</p>
                            <span class="subject-card-count">
                                <i class="bi bi-collection"></i>
                                {{ $count }} cours
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="front-empty">
                    <div class="front-empty-icon"><i class="bi bi-book"></i></div>
                    <h3>Aucun niveau disponible</h3>
                    <p>Les niveaux seront bientôt ajoutés pour cette matière.</p>
                </div>
            @endif
        </div>
    </section>

</div>

<style>
.subjects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.25rem;
}

.subject-card {
    position: relative;
    background: #fff;
    border-radius: 18px;
    padding: 1.75rem 1.5rem;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 2px 16px rgba(15,23,42,.06);
    border: 1px solid rgba(226,232,240,.6);
    transition: all .35s cubic-bezier(.22,.61,.36,1);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: .6rem;
    overflow: hidden;
}

.subject-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(15,23,42,.14);
    border-color: transparent;
}

.subject-card-bg {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, rgba(37,99,235,.04) 0%, transparent 60%);
    opacity: 0;
    transition: opacity .4s ease;
    pointer-events: none;
}

.subject-card:hover .subject-card-bg { opacity: 1; }

.subject-card-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 22px;
    box-shadow: 0 6px 20px rgba(0,0,0,.12);
    transition: transform .3s ease, box-shadow .3s ease;
    position: relative;
}

.subject-card:hover .subject-card-icon {
    transform: scale(1.08) rotate(-3deg);
    box-shadow: 0 10px 28px rgba(0,0,0,.18);
}

.subject-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0; position: relative;
}

.subject-card-desc {
    font-size: 12.5px;
    color: #64748b;
    line-height: 1.5;
    margin: 0; position: relative;
}

.subject-card-count {
    font-size: 12px;
    color: #94a3b8;
    background: #f8fafc;
    padding: 4px 14px;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: auto;
    position: relative;
}

.subject-card-count i { font-size: 11px; }

@media(max-width:768px) {
    .subjects-grid { grid-template-columns: repeat(2, 1fr); gap: .85rem; }
    .subject-card { padding: 1.25rem 1rem; }
}
</style>
@endsection
