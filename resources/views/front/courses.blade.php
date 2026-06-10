@extends('layouts.front')

@section('content')
<div class="front-page">

    <section class="front-hero front-hero-sm">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-badge"><i class="bi bi-list-check"></i> Cours</div>
            <h1 class="hero-title">Nos <span class="text-gradient">cours</span></h1>
            <p class="hero-subtitle">Parcourez l'ensemble de nos cours disponibles. Progressez à votre rythme.</p>
        </div>
    </section>

    <section class="front-section">
        <div class="container">
            @if($courses->count())
                <div class="courses-progress-grid">
                    @foreach($courses as $course)
                        <a href="{{ route('front.course.show', $course->id) }}" class="course-progress-card">
                            <div class="course-progress-banner">
                                @php
                                    $courseColors = ['2563eb','059669','7c3aed','d97706','dc2626','0891b2'];
                                    $color = $courseColors[$loop->index % count($courseColors)];
                                @endphp
                                <div class="course-progress-bg" style="background: linear-gradient(135deg, #{{ $color }}, #{{ dechex(hexdec($color) - 0x111111 < 0 ? 0 : hexdec($color) - 0x111111) }})">
                                    <div class="course-progress-shape"></div>
                                </div>
                                <div class="course-progress-icon">
                                    <i class="bi bi-play-circle"></i>
                                </div>
                                <div class="course-progress-num">Cours {{ $loop->iteration }}</div>
                            </div>
                            <div class="course-progress-body">
                                <h3 class="course-progress-title">{{ $course->title ?? $course->name ?? '' }}</h3>
                                <p class="course-progress-desc">{{ Str::limit($course->description ?? 'Cours complet avec ressources pédagogiques.', 90) }}</p>
                                <div class="course-progress-meta">
                                    @if(isset($course->subject))
                                        <span class="course-progress-tag">{{ $course->subject->name ?? '' }}</span>
                                    @endif
                                    @if(isset($course->duration))
                                        <span class="course-progress-tag"><i class="bi bi-clock"></i> {{ $course->duration }}</span>
                                    @endif
                                </div>
                                @if(isset($course->progress))
                                    <div class="course-progress-bar-wrap">
                                        <div class="course-progress-bar">
                                            <div class="course-progress-fill" style="width: {{ $course->progress }}%;"></div>
                                        </div>
                                        <span class="course-progress-pct">{{ $course->progress }}%</span>
                                    </div>
                                @endif
                            </div>
                            <div class="course-progress-footer">
                                <span class="course-progress-link">
                                    Voir le cours <i class="bi bi-arrow-right"></i>
                                </span>
                                @if(($course->is_free ?? false) || !($course->is_premium ?? true))
                                    <span class="cp-badge cp-badge-free">Gratuit</span>
                                @else
                                    <span class="cp-badge cp-badge-premium">Premium</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="front-empty">
                    <div class="front-empty-icon"><i class="bi bi-play-circle"></i></div>
                    <h3>Aucun cours disponible</h3>
                    <p>Les cours seront bientôt publiés. Revenez prochainement.</p>
                </div>
            @endif
        </div>
    </section>

</div>

<style>
.courses-progress-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.25rem;
}

.course-progress-card {
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

.course-progress-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(15,23,42,.14);
    border-color: transparent;
}

.course-progress-banner {
    position: relative;
    padding: 1.5rem;
    color: #fff;
    overflow: hidden;
    min-height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.course-progress-bg {
    position: absolute;
    inset: 0;
}

.course-progress-shape {
    position: absolute;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
    top: -30px; right: -20px;
}

.course-progress-icon { font-size: 28px; position: relative; z-index: 1; }
.course-progress-num {
    font-size: 12px; font-weight: 600;
    color: rgba(255,255,255,.7);
    position: relative; z-index: 1;
    margin-top: auto;
}

.course-progress-body { padding: 1.25rem 1.5rem; flex: 1; }

.course-progress-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .4rem;
    line-height: 1.3;
}

.course-progress-desc {
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
    margin: 0 0 .75rem;
}

.course-progress-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: .75rem;
}

.course-progress-tag {
    font-size: 11.5px;
    font-weight: 500;
    color: #64748b;
    background: #f8fafc;
    padding: 3px 10px;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.course-progress-tag i { font-size: 10px; }

.course-progress-bar-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
}

.course-progress-bar {
    flex: 1;
    height: 6px;
    background: #f1f5f9;
    border-radius: 999px;
    overflow: hidden;
}

.course-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #2563eb, #1d4ed8);
    border-radius: 999px;
    transition: width .6s cubic-bezier(.22,.61,.36,1);
}

.course-progress-pct {
    font-size: 12px;
    font-weight: 700;
    color: #2563eb;
    flex-shrink: 0;
}

.course-progress-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.course-progress-link {
    font-size: 13px; font-weight: 600; color: #2563eb;
    display: inline-flex; align-items: center; gap: 6px;
    transition: gap .3s ease;
}

.course-progress-card:hover .course-progress-link { gap: 10px; }
.course-progress-link i { font-size: 12px; transition: transform .3s ease; }
.course-progress-card:hover .course-progress-link i { transform: translateX(3px); }

.cp-badge {
    font-size: 11px; font-weight: 600;
    padding: 3px 10px; border-radius: 999px;
}

.cp-badge-free { color: #166534; background: #dcfce7; }
.cp-badge-premium { color: #92400e; background: #fef3c7; }

@media(max-width:768px) {
    .courses-progress-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
