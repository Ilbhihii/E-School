@extends('layouts.front')

@section('content')
<div class="front-page">

    {{-- Hero Section --}}
    <section class="front-hero front-hero-sm">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-badge">
                <i class="bi bi-book"></i> Matières
            </div>
            <h1 class="hero-title">Nos <span class="text-gradient">matières</span></h1>
            <p class="hero-subtitle">Découvrez toutes les matières disponibles et commencez votre apprentissage.</p>
        </div>
    </section>

    {{-- Classes Grid --}}
    <section class="front-section">
        <div class="container">
            @if($subjects->count())
                <div class="subjects-grid-front">
                    @foreach($subjects->unique('name') as $subject)
                        @php
                            $frontColors = ['#2563eb','#059669','#7c3aed','#d97706','#dc2626','#0891b2','#db2777','#4f46e5'];
                            $color = $frontColors[$loop->index % count($frontColors)];
                            $icons = ['bi-book','bi-cpu','bi-calculator','bi-globe2','bi-flask','bi-music-note','bi-palette','bi-translate','bi-activity','bi-star'];
                            $icon = $icons[$loop->index % count($icons)];
                        @endphp
                        <a href="{{ route('front.subject.classes', $subject->id) }}" class="subject-card-front">
                            <div class="subject-card-front-icon" style="background: {{ $color }};">
                                <i class="bi {{ $icon }}"></i>
                            </div>
                            <div class="subject-card-front-body">
                                <h3 class="subject-card-front-title">{{ $subject->name }}</h3>
                                <p class="subject-card-front-desc">{{ Str::limit($subject->description ?? 'Explorez cette matière.', 80) }}</p>
                            </div>
                            <div class="subject-card-front-footer">
                                <span class="subject-card-front-link">
                                    Explorer <i class="bi bi-arrow-right"></i>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="front-empty">
                    <div class="front-empty-icon"><i class="bi bi-inbox"></i></div>
                    <h3>Aucune matière disponible</h3>
                    <p>Les matières seront bientôt ajoutées.</p>
                </div>
            @endif
        </div>
    </section>

</div>

<style>
:root {
    --class-card-radius: 18px;
}

.subjects-grid-front {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.25rem;
}

.subject-card-front {
    display: flex;
    gap: 1.125rem;
    background: #fff;
    padding: 1.125rem 1.25rem;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(15,23,42,.06);
    border: 1px solid rgba(226,232,240,.6);
    text-decoration: none;
    color: inherit;
    transition: all .35s cubic-bezier(.22,.61,.36,1);
}

.subject-card-front:hover {
    transform: translateX(6px);
    box-shadow: 0 8px 32px rgba(15,23,42,.12);
    border-color: transparent;
}

.subject-card-front-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 22px;
    flex-shrink: 0;
    box-shadow: 0 4px 16px rgba(0,0,0,.12);
    transition: transform .3s ease, box-shadow .3s ease;
}

.subject-card-front:hover .subject-card-front-icon {
    transform: scale(1.08) rotate(-3deg);
    box-shadow: 0 8px 24px rgba(0,0,0,.18);
}

.subject-card-front-body { flex: 1; min-width: 0; }

.subject-card-front-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .3rem;
}

.subject-card-front-desc {
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
    margin: 0;
}

.subject-card-front-footer {
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

.subject-card-front-link {
    font-size: 13px;
    font-weight: 600;
    color: #2563eb;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: gap .3s ease;
}

.subject-card-front:hover .subject-card-front-link { gap: 10px; }
.subject-card-front-link i { font-size: 12px; transition: transform .3s ease; }
.subject-card-front:hover .subject-card-front-link i { transform: translateX(3px); }

@media(max-width:768px) {
    .subjects-grid-front { grid-template-columns: 1fr; }
}
</style>
@endsection
