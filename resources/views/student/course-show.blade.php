@extends('layouts.student')

@section('content')

<style>
.course-show-hero {
    background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
    border-radius: 20px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.05);
    margin-bottom: 1.5rem;
}
.course-show-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(6,182,212,0.08) 0%, transparent 70%);
    border-radius: 50%;
}
.course-show-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(139,92,246,0.06) 0%, transparent 70%);
    border-radius: 50%;
}
.course-show-hero > * { position: relative; z-index: 1; }

.content-section {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    transition: all 0.3s ease;
}
.content-section:hover {
    border-color: rgba(255,255,255,0.1);
}

.section-title-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    font-weight: 700;
    font-size: 0.95rem;
}

.btn-course-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 12px 28px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    cursor: pointer;
}
.btn-course-action:hover {
    transform: translateY(-2px);
    text-decoration: none;
}
.btn-course-action.primary {
    background: linear-gradient(135deg, #06B6D4, #0891B2);
    color: white;
    box-shadow: 0 8px 25px rgba(6,182,212,0.3);
}
.btn-course-action.primary:hover {
    box-shadow: 0 12px 35px rgba(6,182,212,0.45);
}
.btn-course-action.danger {
    background: linear-gradient(135deg, #EF4444, #DC2626);
    color: white;
    box-shadow: 0 8px 25px rgba(239,68,68,0.3);
}
.btn-course-action.success {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
    box-shadow: 0 8px 25px rgba(16,185,129,0.3);
}
.btn-course-action.outline {
    background: transparent;
    color: rgba(255,255,255,0.7);
    border: 1px solid rgba(255,255,255,0.12);
}
.btn-course-action.outline:hover {
    background: rgba(255,255,255,0.05);
    color: rgba(255,255,255,0.9);
}

.video-wrapper {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}
.video-wrapper video {
    width: 100%;
    max-height: 480px;
    object-fit: cover;
}

.info-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.8rem;
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.6);
}

.devoir-item {
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.25s ease;
}
.devoir-item:hover {
    border-color: rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.02);
}
</style>

<div class="course-show-hero">
    <div style="display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;">
        <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#06B6D4,#0891B2);display:flex;align-items:center;justify-content:center;font-size:1.75rem;color:white;box-shadow:0 10px 30px rgba(6,182,212,0.3);flex-shrink:0;">
            <i class="bi bi-journal-bookmark-fill"></i>
        </div>
        <div style="flex:1;min-width:200px;">
            <h1 style="font-weight:800;color:rgba(255,255,255,0.95);margin:0 0 0.25rem;font-size:1.5rem;">{{ $course->title }}</h1>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @if($course->classRoom ?? false)
                <span class="info-chip"><i class="bi bi-building" style="color:#06B6D4;"></i> {{ $course->classRoom->name }}</span>
                @endif
                @if($course->subject ?? false)
                <span class="info-chip"><i class="bi bi-book" style="color:#8B5CF6;"></i> {{ $course->subject->name }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        @if($course->description)
        <div class="content-section">
            <div style="display:flex;align-items:start;gap:0.75rem;">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(6,182,212,0.1);display:flex;align-items:center;justify-content:center;font-size:1rem;color:#06B6D4;flex-shrink:0;margin-top:2px;">
                    <i class="bi bi-info-circle"></i>
                </div>
                <div>
                    <h6 style="font-weight:700;color:rgba(255,255,255,0.85);margin-bottom:0.5rem;">Description</h6>
                    <p style="color:rgba(255,255,255,0.6);line-height:1.7;margin:0;font-size:0.92rem;">{{ $course->description }}</p>
                </div>
            </div>
        </div>
        @endif

        @if($course->video)
        <div class="content-section">
            <div class="section-title-bar" style="background:rgba(239,68,68,0.1);color:#FCA5A5;">
                <i class="bi bi-play-circle-fill"></i> Vidéo du cours
            </div>
            <div class="video-wrapper mb-3">
                <video controls preload="metadata">
                    <source src="{{ asset('storage/'.$course->video) }}">
                    Votre navigateur ne supporte pas la vidéo.
                </video>
            </div>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                <a href="{{ asset('storage/'.$course->video) }}" target="_blank" class="btn-course-action primary">
                    <i class="bi bi-eye"></i> Voir en plein écran
                </a>
                <a href="{{ asset('storage/'.$course->video) }}" download class="btn-course-action outline">
                    <i class="bi bi-download"></i> Télécharger
                </a>
            </div>
        </div>
        @endif

        @if($course->pdf)
        <div class="content-section">
            <div class="section-title-bar" style="background:rgba(239,68,68,0.1);color:#FCA5A5;">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF du cours
            </div>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;justify-content:center;">
                <a href="{{ asset('storage/'.$course->pdf) }}" target="_blank" class="btn-course-action danger">
                    <i class="bi bi-eye"></i> Voir le PDF
                </a>
                <a href="{{ asset('storage/'.$course->pdf) }}" download class="btn-course-action outline">
                    <i class="bi bi-download"></i> Télécharger
                </a>
            </div>
        </div>
        @endif

        @if(isset($course->devoirs) && $course->devoirs->count() > 0)
        <div class="content-section">
            <div class="section-title-bar" style="background:rgba(16,185,129,0.1);color:#6EE7B7;">
                <i class="bi bi-clipboard-check"></i> Devoirs associés
            </div>
            @foreach($course->devoirs as $devoir)
            <div class="devoir-item mb-3">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <h6 style="font-weight:700;color:rgba(255,255,255,0.85);margin-bottom:0.25rem;">{{ $devoir->title }}</h6>
                        @if($devoir->description)
                        <p style="font-size:0.82rem;color:rgba(255,255,255,0.5);margin-bottom:0.25rem;">{{ $devoir->description }}</p>
                        @endif
                        @if($devoir->due_date)
                        <small style="color:#F59E0B;font-size:0.75rem;">
                            <i class="bi bi-calendar me-1"></i> À rendre avant le {{ \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') }}
                        </small>
                        @endif
                    </div>
                    @if($devoir->file)
                    <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="btn-course-action outline" style="padding:8px 16px;font-size:0.8rem;">
                        <i class="bi bi-eye"></i> Voir
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="content-section sticky-top" style="top:1.5rem;">
            <h6 style="font-weight:700;color:rgba(255,255,255,0.85);margin-bottom:1rem;text-align:center;">
                <i class="bi bi-info-circle me-1" style="color:#06B6D4;"></i> Informations
            </h6>
            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                @if($course->subject ?? false)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0.75rem;background:rgba(255,255,255,0.03);border-radius:8px;">
                    <span style="font-size:0.78rem;color:rgba(255,255,255,0.5);">Matière</span>
                    <span style="font-size:0.85rem;font-weight:600;color:rgba(255,255,255,0.8);">{{ $course->subject->name }}</span>
                </div>
                @endif
                @if($course->classRoom ?? false)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0.75rem;background:rgba(255,255,255,0.03);border-radius:8px;">
                    <span style="font-size:0.78rem;color:rgba(255,255,255,0.5);">Classe</span>
                    <span style="font-size:0.85rem;font-weight:600;color:rgba(255,255,255,0.8);">{{ $course->classRoom->name }}</span>
                </div>
                @endif
                <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0.75rem;background:rgba(255,255,255,0.03);border-radius:8px;">
                    <span style="font-size:0.78rem;color:rgba(255,255,255,0.5);">Contenu</span>
                    <div style="display:flex;gap:4px;">
                        @if($course->video) <span class="pr-badge pr-badge-danger" style="font-size:0.6rem;">Vidéo</span> @endif
                        @if($course->pdf) <span class="pr-badge pr-badge-primary" style="font-size:0.6rem;">PDF</span> @endif
                    </div>
                </div>
            </div>
            <div style="text-align:center;margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.05);">
                <a href="{{ route('student.courses') }}" class="btn-course-action outline" style="width:100%;padding:10px;font-size:0.85rem;">
                    <i class="bi bi-arrow-left"></i> Retour aux cours
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
