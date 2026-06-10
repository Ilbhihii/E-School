@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    {{-- Hero --}}
    <div class="st-hero st-flex-between st-fade-up">
      <div>
        <div class="st-breadcrumb">
          <a href="{{ route('student.classes') }}"><i class="bi bi-building me-1"></i>Classes</a>
          @if($course->classRoom)
            <span>/</span>
            <a href="{{ route('student.subjects', $course->classRoom) }}">{{ $course->classRoom->name }}</a>
          @endif
          <span>/</span>
          <span class="current">{{ Str::limit($course->title, 30) }}</span>
        </div>
        <h1>{{ $course->title }}</h1>
        @if($course->description)
          <p>{{ $course->description }}</p>
        @endif
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-book-half"></i>
      </div>
    </div>

    {{-- Content badges --}}
    <div class="st-media-tags st-mb-4 st-fade-up st-fade-up-d1">
      @if($course->video)
        <span class="st-media-tag video"><i class="bi bi-play-circle-fill"></i> Vidéo</span>
      @endif
      @if($course->pdf)
        <span class="st-media-tag pdf"><i class="bi bi-file-earmark-pdf-fill"></i> PDF</span>
      @endif
      @if($course->course_link)
        <span class="st-media-tag link"><i class="bi bi-link-45deg"></i> Lien externe</span>
      @endif
    </div>

    <div class="row g-4">
      {{-- Main --}}
      <div class="col-lg-8">

        {{-- Video --}}
        @if($course->video)
          <div class="st-content-section st-fade-up st-fade-up-d1">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background: #fef3c7; color: #d97706;"><i class="bi bi-play-circle-fill"></i></div>
              <h5>🎥 Vidéo du cours</h5>
            </div>
            <div class="st-content-body">
              <div class="st-video-wrap mb-3">
                <video controls preload="metadata">
                  <source src="{{ asset('storage/videos/' . $course->video) }}" type="video/mp4">
                  Votre navigateur ne supporte pas la lecture vidéo.
                </video>
              </div>
              <div class="text-center">
                <a href="{{ asset('storage/' . $course->video) }}" download class="st-btn st-btn-primary">
                  <i class="bi bi-download"></i> Télécharger
                </a>
              </div>
            </div>
          </div>
        @endif

        {{-- PDF --}}
        @if($course->pdf)
          <div class="st-content-section st-fade-up st-fade-up-d2">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background: #fef2f2; color: var(--st-danger);"><i class="bi bi-file-earmark-pdf-fill"></i></div>
              <h5>📄 PDF du cours</h5>
            </div>
            <div class="st-content-body text-center">
              <a href="{{ asset('storage/' . $course->pdf) }}" target="_blank" class="st-btn st-btn-lg" style="background: linear-gradient(135deg,#dc2626,#f87171); color: white;">
                <i class="bi bi-download"></i> Télécharger PDF
              </a>
            </div>
          </div>
        @endif

        {{-- External link --}}
        @if($course->course_link)
          <div class="st-content-section st-fade-up st-fade-up-d3">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background: #f5f3ff; color: var(--st-accent);"><i class="bi bi-link-45deg"></i></div>
              <h5>🚀 Accéder au cours complet</h5>
            </div>
            <div class="st-content-body text-center">
              <a href="{{ $course->course_link }}" target="_blank" class="st-btn st-btn-primary st-btn-lg">
                🎯 Ouvrir la plateforme <i class="bi bi-arrow-up-right-square ms-1"></i>
              </a>
            </div>
          </div>
        @endif

      </div>

      {{-- Sidebar --}}
      <div class="col-lg-4">
        <div class="st-detail-sidebar st-fade-up st-fade-up-d1">
          <h5><i class="bi bi-card-list me-2" style="color: var(--st-primary);"></i>Informations rapides</h5>

          @if($course->subject->name ?? false)
            <div class="st-detail-row">
              <span class="st-detail-label"><i class="bi bi-book me-1"></i>Matière</span>
              <span class="st-detail-value">{{ $course->subject->name }}</span>
            </div>
          @endif
          @if($course->classRoom->name ?? false)
            <div class="st-detail-row">
              <span class="st-detail-label"><i class="bi bi-building me-1"></i>Classe</span>
              <span class="st-detail-value">{{ $course->classRoom->name }}</span>
            </div>
          @endif
          @if($course->is_free)
            <div class="st-detail-row">
              <span class="st-detail-label"><i class="bi bi-tag me-1"></i>Accès</span>
              <span class="st-detail-value"><span class="st-badge st-badge-success">Gratuit</span></span>
            </div>
          @endif

          <div class="text-center st-mt-3">
            <a href="{{ route('student.courses') }}" class="st-btn st-btn-ghost st-btn-sm">
              <i class="bi bi-list-ul me-1"></i> Tous les cours
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
