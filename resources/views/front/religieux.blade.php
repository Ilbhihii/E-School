@extends('layouts.front')

@section('content')
<div class="front-page">

    <section class="front-hero front-hero-sm">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-badge"><i class="bi bi-bookmark-heart"></i> Éducation religieuse</div>
            <h1 class="hero-title">Enseignement <span class="text-gradient">religieux</span></h1>
            <p class="hero-subtitle">Découvrez nos ressources d'éducation religieuse pour enrichir votre spiritualité et vos connaissances.</p>
        </div>
    </section>

    <section class="front-section">
        <div class="container">
            <div class="religieux-intro">
                <div class="religieux-intro-icon">
                    <i class="bi bi-bookmark-heart-fill"></i>
                </div>
                <div class="religieux-intro-content">
                    <h2>Bienvenue dans l'espace d'éducation religieuse</h2>
                    <p>Cette section est dédiée à l'enseignement religieux. Vous y trouverez des cours, des ressources et des supports pédagogiques pour approfondir vos connaissances spirituelles.</p>
                </div>
            </div>

            @if($courses->count())
                <div class="section-label">
                    <i class="bi bi-play-circle-fill" style="color: #2563eb;"></i>
                    Cours disponibles
                    <span class="section-label-count">{{ $courses->count() }}</span>
                </div>
                <div class="courses-grid">
                    @foreach($courses as $course)
                        <a href="{{ route('front.course.show', $course->id) }}" class="course-card">
                            @php
                                $palette = [
                                    'bg' => 'linear-gradient(135deg, #0f172a, #1e3a5f)',
                                    'accent' => '#3b82f6'
                                ];
                            @endphp
                            <div class="course-card-banner" style="background: {{ $palette['bg'] }};">
                                <div class="course-card-shape"></div>
                                <i class="bi bi-bookmark-heart-fill course-card-banner-icon"></i>
                                <span class="course-card-banner-num">Cours {{ $loop->iteration }}</span>
                            </div>
                            <div class="course-card-body">
                                <h3 class="course-card-title">{{ $course->title ?? $course->name ?? '' }}</h3>
                                <p class="course-card-desc">{{ Str::limit($course->description ?? 'Cours d\'éducation religieuse.', 80) }}</p>
                                @if(isset($course->duration))
                                    <span class="course-card-duration"><i class="bi bi-clock"></i> {{ $course->duration }}</span>
                                @endif
                            </div>
                            <div class="course-card-footer">
                                <span class="course-card-link">
                                    Accéder au cours <i class="bi bi-arrow-right"></i>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="front-empty">
                    <div class="front-empty-icon"><i class="bi bi-bookmark-heart"></i></div>
                    <h3>Aucun cours disponible</h3>
                    <p>Les cours d'éducation religieuse seront bientôt disponibles.</p>
                </div>
            @endif
        </div>
    </section>

</div>

<style>
.religieux-intro {
    display: flex;
    gap: 1.5rem;
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2.5rem;
    border: 1px solid rgba(59,130,246,.1);
}

.religieux-intro-icon {
    width: 64px; height: 64px;
    border-radius: 18px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 28px;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(37,99,235,.2);
}

.religieux-intro-content h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .5rem;
}

.religieux-intro-content p {
    font-size: 14px;
    color: #475569;
    line-height: 1.6;
    margin: 0;
}

.section-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 1.25rem;
    padding-bottom: .75rem;
    border-bottom: 1px solid #e2e8f0;
}

.section-label-count {
    margin-left: auto;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 2px 10px;
    border-radius: 999px;
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
}

.course-card {
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

.course-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(15,23,42,.14);
    border-color: transparent;
}

.course-card-banner {
    position: relative;
    padding: 1.5rem;
    color: #fff;
    min-height: 90px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
}

.course-card-shape {
    position: absolute;
    width: 100px; height: 100px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    top: -30px; right: -25px;
}

.course-card-banner-icon { font-size: 24px; position: relative; z-index: 1; }
.course-card-banner-num {
    font-size: 12px;
    color: rgba(255,255,255,.6);
    font-weight: 600;
    position: relative; z-index: 1;
    margin-top: auto;
}

.course-card-body { padding: 1.25rem 1.5rem; flex: 1; }

.course-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .35rem;
}

.course-card-desc {
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
    margin: 0 0 .5rem;
}

.course-card-duration {
    font-size: 12px;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.course-card-duration i { font-size: 11px; }

.course-card-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f1f5f9;
}

.course-card-link {
    font-size: 13px; font-weight: 600; color: #2563eb;
    display: inline-flex; align-items: center; gap: 6px;
    transition: gap .3s ease;
}

.course-card:hover .course-card-link { gap: 10px; }
.course-card-link i { font-size: 12px; transition: transform .3s ease; }
.course-card:hover .course-card-link i { transform: translateX(3px); }

@media(max-width:768px) {
    .religieux-intro { flex-direction: column; align-items: center; text-align: center; padding: 1.5rem; }
    .courses-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
