@extends('layouts.front')

@section('content')
<div class="front-page">

    {{-- Course Hero --}}
    <section class="front-hero front-hero-sm">
        <div class="hero-bg-pattern"></div>
        <div class="container">
            <div class="hero-breadcrumb">
                <a href="{{ url('/') }}">Accueil</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('front.classes') }}">Matières</a>
                @if($course->subject)
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('front.subject.classes', $course->subject->id) }}">{{ $course->subject->name }}</a>
                @endif
                @if($course->classRoom)
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('front.courses', [$course->subject_id, $course->class_id]) }}">{{ $course->classRoom->name }}</a>
                @endif
                <i class="bi bi-chevron-right"></i>
                <span>{{ $course->title }}</span>
            </div>
            <div class="hero-badge"><i class="bi bi-play-circle"></i> Cours</div>
            <h1 class="hero-title">{{ $course->title ?? $course->name ?? '' }}</h1>
            <p class="hero-subtitle">{{ Str::limit($course->description ?? 'Cours détaillé avec ressources pédagogiques.', 120) }}</p>
            <div class="hero-meta">
                @if(isset($course->subject))
                    <span><i class="bi bi-book"></i> {{ $course->subject->name ?? '' }}</span>
                @endif
                @if(isset($course->duration))
                    <span><i class="bi bi-clock"></i> {{ $course->duration }}</span>
                @endif
                @if(isset($course->lessons_count))
                    <span><i class="bi bi-list-check"></i> {{ $course->lessons_count }} leçons</span>
                @endif
                @if(($course->is_free ?? false) || !($course->is_premium ?? true))
                    <span class="hero-badge-free"><i class="bi bi-unlock"></i> Gratuit</span>
                @else
                    <span class="hero-badge-premium"><i class="bi bi-star-fill"></i> Premium</span>
                @endif
            </div>
        </div>
    </section>

    {{-- Course Content --}}
    <section class="front-section">
        <div class="container">
            <div class="course-detail-layout">

                {{-- Main Content --}}
                <div class="course-detail-main">
                    @if(session('success'))
                        <div class="front-alert front-alert-success">
                            <i class="bi bi-check-circle-fill"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Video / Embed --}}
                    @if(isset($course->video_url) || isset($course->embed_url))
                        <div class="course-video-wrap">
                            <div class="course-video-container">
                                @if(isset($course->embed_url))
                                    {!! $course->embed_url !!}
                                @else
                                    <video controls class="course-video" poster="{{ $course->thumbnail ?? '' }}">
                                        <source src="{{ $course->video_url }}" type="video/mp4">
                                    </video>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Description --}}
                    <div class="course-section">
                        <h2 class="course-section-title">
                            <i class="bi bi-info-circle"></i> Description
                        </h2>
                        <div class="course-description">
                            {!! $course->description ?? '<p>Aucune description disponible pour ce cours.</p>' !!}
                        </div>
                    </div>

                    {{-- Lessons / Chapters --}}
                    @if(isset($course->lessons) && $course->lessons->count())
                        <div class="course-section">
                            <h2 class="course-section-title">
                                <i class="bi bi-list-ol"></i> Leçons
                                <span class="course-section-count">{{ $course->lessons->count() }}</span>
                            </h2>
                            <div class="lessons-list">
                                @foreach($course->lessons as $lesson)
                                    <div class="lesson-item">
                                        <div class="lesson-item-num">{{ $loop->iteration }}</div>
                                        <div class="lesson-item-content">
                                            <h4 class="lesson-item-title">{{ $lesson->title }}</h4>
                                            @if(isset($lesson->duration))
                                                <span class="lesson-item-duration"><i class="bi bi-clock"></i> {{ $lesson->duration }}</span>
                                            @endif
                                        </div>
                                        <div class="lesson-item-action">
                                            @if(isset($lesson->is_free) && $lesson->is_free)
                                                <span class="lesson-badge-free">Gratuit</span>
                                            @endif
                                            <i class="bi bi-play-circle-fill"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Resources --}}
                    @if(isset($course->resources) && $course->resources->count())
                        <div class="course-section">
                            <h2 class="course-section-title">
                                <i class="bi bi-paperclip"></i> Ressources
                            </h2>
                            <div class="resources-grid">
                                @foreach($course->resources as $resource)
                                    <a href="{{ $resource->url ?? '#' }}" class="resource-card" target="_blank">
                                        <div class="resource-card-icon">
                                            @php
                                                $ext = pathinfo($resource->url ?? '', PATHINFO_EXTENSION);
                                                $icon = match($ext) {
                                                    'pdf' => 'bi-file-pdf',
                                                    'doc','docx' => 'bi-file-word',
                                                    'xls','xlsx' => 'bi-file-excel',
                                                    'zip','rar' => 'bi-file-zip',
                                                    'mp4','avi' => 'bi-file-play',
                                                    default => 'bi-file-earmark'
                                                };
                                            @endphp
                                            <i class="bi {{ $icon }}"></i>
                                        </div>
                                        <span class="resource-card-name">{{ $resource->name ?? $resource->title ?? 'Ressource' }}</span>
                                        <i class="bi bi-download resource-card-dl"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="course-detail-sidebar">
                    <div class="sidebar-card sidebar-card-sticky">
                        <div class="sidebar-card-header">
                            <i class="bi bi-info-circle"></i> Détails du cours
                        </div>
                        <div class="sidebar-card-body">
                            <div class="sidebar-info-row">
                                <span class="sidebar-info-label"><i class="bi bi-person"></i> Professeur</span>
                                <span class="sidebar-info-value">{{ $course->teacher->name ?? $course->user->name ?? 'N/A' }}</span>
                            </div>
                            @if(isset($course->level))
                            <div class="sidebar-info-row">
                                <span class="sidebar-info-label"><i class="bi bi-layers"></i> Niveau</span>
                                <span class="sidebar-info-value">{{ $course->level->name ?? '' }}</span>
                            </div>
                            @endif
                            @if(isset($course->classe))
                            <div class="sidebar-info-row">
                                <span class="sidebar-info-label"><i class="bi bi-diagram-3"></i> Classe</span>
                                <span class="sidebar-info-value">{{ $course->classe->name ?? '' }}</span>
                            </div>
                            @endif
                            @if(isset($course->subject))
                            <div class="sidebar-info-row">
                                <span class="sidebar-info-label"><i class="bi bi-book"></i> Matière</span>
                                <span class="sidebar-info-value">{{ $course->subject->name ?? '' }}</span>
                            </div>
                            @endif
                            <div class="sidebar-info-row">
                                <span class="sidebar-info-label"><i class="bi bi-calendar"></i> Créé le</span>
                                <span class="sidebar-info-value">{{ $course->created_at?->format('d/m/Y') ?? 'N/A' }}</span>
                            </div>
                            @if(isset($course->updated_at))
                            <div class="sidebar-info-row">
                                <span class="sidebar-info-label"><i class="bi bi-arrow-repeat"></i> Mis à jour</span>
                                <span class="sidebar-info-value">{{ $course->updated_at->format('d/m/Y') }}</span>
                            </div>
                            @endif
                        </div>
                        @auth
                            <div class="sidebar-card-footer">
                                @if(isset($course->is_enrolled) && $course->is_enrolled)
                                    <button class="btn-sidebar btn-sidebar-success" disabled>
                                        <i class="bi bi-check-lg"></i> Inscrit
                                    </button>
                                @else
                                    <a href="{{ route('plans') }}" class="btn-sidebar btn-sidebar-primary">
                                        <i class="bi bi-plus-circle"></i> S'inscrire
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="sidebar-card-footer">
                                <a href="{{ route('login') }}" class="btn-sidebar btn-sidebar-primary">
                                    <i class="bi bi-box-arrow-in-right"></i> Connectez-vous pour vous inscrire
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<style>
/* ── Breadcrumb ── */
.hero-breadcrumb {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 1rem;
    font-size: 12.5px;
    flex-wrap: wrap;
}

.hero-breadcrumb a {
    color: rgba(255,255,255,.6);
    text-decoration: none;
    transition: color .2s;
}

.hero-breadcrumb a:hover { color: #fff; }
.hero-breadcrumb i { color: rgba(255,255,255,.3); font-size: 10px; }
.hero-breadcrumb span { color: rgba(255,255,255,.8); }

.hero-meta { margin-top: 1rem; justify-content: center; }
.hero-badge-free {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; color: #166534; background: rgba(34,197,94,.15);
    padding: 4px 12px; border-radius: 999px;
}
.hero-badge-premium {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; color: #92400e; background: rgba(251,191,36,.15);
    padding: 4px 12px; border-radius: 999px;
}

/* ── Layout ── */
.course-detail-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 2rem;
    align-items: start;
}

.course-detail-main { min-width: 0; }

/* ── Video ── */
.course-video-wrap {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(15,23,42,.08);
    margin-bottom: 2rem;
}

.course-video-container {
    position: relative;
    width: 100%;
    aspect-ratio: 16/9;
    background: #000;
}

.course-video { width: 100%; height: 100%; object-fit: contain; }

/* ── Section ── */
.course-section { margin-bottom: 2rem; }

.course-section-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.course-section-title i { color: #2563eb; font-size: 16px; }

.course-section-count {
    margin-left: auto;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 2px 10px;
    border-radius: 999px;
}

.course-description {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(15,23,42,.04);
    border: 1px solid #f1f5f9;
    color: #334155;
    font-size: 14px;
    line-height: 1.7;
}

.course-description p:last-child { margin-bottom: 0; }

/* ── Lessons ── */
.lessons-list { display: flex; flex-direction: column; gap: .5rem; }

.lesson-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #fff;
    padding: 1rem 1.25rem;
    border-radius: 14px;
    box-shadow: 0 1px 8px rgba(15,23,42,.04);
    border: 1px solid #f1f5f9;
    transition: all .25s ease;
    cursor: pointer;
}

.lesson-item:hover {
    border-color: #dbeafe;
    box-shadow: 0 4px 16px rgba(37,99,235,.08);
    transform: translateX(4px);
}

.lesson-item-num {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: #eff6ff;
    color: #2563eb;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px;
    flex-shrink: 0;
}

.lesson-item-content { flex: 1; }

.lesson-item-title {
    font-size: 14px; font-weight: 600; color: #0f172a;
    margin: 0 0 2px;
}

.lesson-item-duration {
    font-size: 12px; color: #94a3b8;
    display: inline-flex; align-items: center; gap: 3px;
}

.lesson-item-action {
    display: flex; align-items: center; gap: 8px;
    color: #2563eb; font-size: 20px;
    flex-shrink: 0;
}

.lesson-badge-free {
    font-size: 10px; font-weight: 600;
    color: #166534; background: #dcfce7;
    padding: 2px 8px; border-radius: 999px;
}

/* ── Resources ── */
.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: .75rem;
}

.resource-card {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    padding: .85rem 1rem;
    border-radius: 12px;
    box-shadow: 0 1px 8px rgba(15,23,42,.04);
    border: 1px solid #f1f5f9;
    text-decoration: none;
    color: inherit;
    transition: all .25s ease;
}

.resource-card:hover {
    border-color: #dbeafe;
    box-shadow: 0 4px 16px rgba(37,99,235,.08);
    transform: translateY(-2px);
}

.resource-card-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    background: #f8fafc;
    color: #2563eb;
    flex-shrink: 0;
}

.resource-card-name {
    flex: 1;
    font-size: 13px; font-weight: 500;
    color: #0f172a;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

.resource-card-dl { color: #94a3b8; font-size: 14px; flex-shrink: 0; }
.resource-card:hover .resource-card-dl { color: #2563eb; }

/* ── Sidebar ── */
.course-detail-sidebar { min-width: 0; }

.sidebar-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(15,23,42,.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.sidebar-card-sticky { position: sticky; top: 100px; }

.sidebar-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px; font-weight: 700; color: #0f172a;
    display: flex; align-items: center; gap: 8px;
}

.sidebar-card-header i { color: #2563eb; font-size: 15px; }

.sidebar-card-body { padding: 1rem 1.25rem; }

.sidebar-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .6rem 0;
    border-bottom: 1px solid #f8fafc;
}

.sidebar-info-row:last-child { border-bottom: none; }

.sidebar-info-label {
    font-size: 13px; color: #64748b;
    display: flex; align-items: center; gap: 6px;
}

.sidebar-info-label i { font-size: 12px; }

.sidebar-info-value {
    font-size: 13px; font-weight: 600; color: #0f172a;
    text-align: right;
}

.sidebar-card-footer {
    padding: 1rem 1.25rem;
    border-top: 1px solid #f1f5f9;
}

.btn-sidebar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: .75rem 1rem;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all .25s ease;
}

.btn-sidebar-primary {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    box-shadow: 0 4px 16px rgba(37,99,235,.3);
}

.btn-sidebar-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(37,99,235,.4);
}

.btn-sidebar-success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

@media(max-width:900px) {
    .course-detail-layout { grid-template-columns: 1fr; }
    .sidebar-card-sticky { position: static; }
}
</style>
@endsection
