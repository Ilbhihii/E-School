@extends('layouts.front')

@section('content')
<div class="front-page">

    <section class="front-hero front-hero-sm">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-badge">
                <i class="bi bi-collection"></i> Classes
            </div>
            <h1 class="hero-title">
                <span class="text-gradient">{{ $subject->name ?? '' }}</span>
            </h1>
            <p class="hero-subtitle">{{ $subject->description ?? 'Explorez les classes disponibles pour cette matière.' }}</p>
            <div class="hero-meta">
                <span><i class="bi bi-collection"></i> {{ $subject->classes->count() }} classes</span>
            </div>
        </div>
    </section>

    <section class="front-section">
        <div class="container">
            @if($subject->classes->count())
                <div class="subj-classes-grid">
                    @foreach($subject->classes as $class)
                        <a href="{{ route('front.courses', [$subject->id, $class->id]) }}" class="subj-class-card">
                            <div class="subj-class-card-header">
                                @php
                                    $classColors = ['#2563eb','#059669','#7c3aed','#d97706','#dc2626','#0891b2','#db2777','#4f46e5'];
                                    $color = $classColors[$loop->index % count($classColors)];
                                @endphp
                                <div class="subj-class-card-num" style="background: {{ $color }};">
                                    {{ $loop->iteration }}
                                </div>
                            </div>
                            <div class="subj-class-card-body">
                                <h3 class="subj-class-card-title">{{ $class->name }}</h3>
                                <p class="subj-class-card-desc">{{ Str::limit($class->description ?? 'Accédez aux cours de cette classe.', 80) }}</p>
                            </div>
                            <div class="subj-class-card-footer">
                                <span class="subj-class-card-link">
                                    Accéder <i class="bi bi-arrow-right"></i>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="front-empty">
                    <div class="front-empty-icon"><i class="bi bi-inbox"></i></div>
                    <h3>Aucune classe disponible</h3>
                    <p>Les classes seront bientôt ajoutées pour cette matière.</p>
                </div>
            @endif
        </div>
    </section>

</div>

<style>
.hero-meta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.hero-meta span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: rgba(255,255,255,.7);
    background: rgba(255,255,255,.08);
    padding: 5px 14px;
    border-radius: 999px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.1);
}

.hero-meta i { font-size: 12px; }

.subj-classes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
}

.subj-class-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 2px 16px rgba(15,23,42,.06);
    border: 1px solid rgba(226,232,240,.6);
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: all .35s cubic-bezier(.22,.61,.36,1);
    display: flex;
    flex-direction: column;
}

.subj-class-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(15,23,42,.14);
    border-color: transparent;
}

.subj-class-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.subj-class-card-num {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
}

.subj-class-card-body { padding: 1.25rem 1.5rem; flex: 1; }
.subj-class-card-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 .5rem; }
.subj-class-card-desc { font-size: 13px; color: #64748b; line-height: 1.6; margin: 0; }
.subj-class-card-footer { padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; }

.subj-class-card-link {
    font-size: 13px; font-weight: 600; color: #2563eb;
    display: inline-flex; align-items: center; gap: 6px;
    transition: gap .3s ease;
}

.subj-class-card:hover .subj-class-card-link { gap: 10px; }
.subj-class-card-link i { font-size: 12px; transition: transform .3s ease; }
.subj-class-card:hover .subj-class-card-link i { transform: translateX(3px); }

@media(max-width:768px) {
    .subj-classes-grid { grid-template-columns: 1fr; }
    .hero-meta { gap: .75rem; }
}
</style>
@endsection
