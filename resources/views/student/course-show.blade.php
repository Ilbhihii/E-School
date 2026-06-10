@extends('layouts.student')

@section('content')
<style>
.st-course-video video {
  width: 100%;
  border-radius: 14px;
  max-height: 480px;
  outline: none;
}
</style>

<div class="st-page">
  <div class="st-container-sm">

    {{-- Hero --}}
    <div class="st-hero st-flex-between st-fade-up">
      <div>
        <div class="st-breadcrumb">
          <a href="{{ route('student.classes') }}"><i class="bi bi-building me-1"></i>Classes</a>
          @if($course->classRoom)
            <span>/</span>
            <a href="{{ route('student.subjects', $course->classRoom) }}">{{ $course->classRoom->name }}</a>
          @endif
          @if($course->subject && $course->classRoom)
            <span>/</span>
            <a href="{{ route('student.courses', [$course->classRoom, $course->subject]) }}">{{ $course->subject->name }}</a>
          @endif
          <span>/</span>
          <span class="current">{{ Str::limit($course->title, 30) }}</span>
        </div>
        <h1>{{ $course->title }}</h1>
        @if($course->description)
          <p>{{ $course->description }}</p>
        @endif
      </div>
    </div>

    {{-- Tags --}}
    <div class="st-tags-row st-fade-up st-fade-up-d1">
      @if($course->is_free)
        <span class="st-tag st-tag-free"><i class="bi bi-unlock me-1"></i>Gratuit</span>
      @else
        <span class="st-tag st-tag-premium"><i class="bi bi-lock me-1"></i>Premium</span>
      @endif
      @if($course->classRoom)
        <span class="st-tag" style="background: rgba(255,255,255,0.12); color: white;">
          <i class="bi bi-building me-1"></i>{{ $course->classRoom->name }}
        </span>
      @endif
      @if($course->subject)
        <span class="st-tag" style="background: rgba(255,255,255,0.12); color: white;">
          <i class="bi bi-book me-1"></i>{{ $course->subject->name }}
        </span>
      @endif
    </div>

    <div class="row g-4">
      {{-- Main --}}
      <div class="col-lg-8">

        {{-- Description --}}
        @if($course->description)
          <div class="st-content-section st-fade-up st-fade-up-d1">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background: #eff6ff; color: var(--st-primary);"><i class="bi bi-info-circle-fill"></i></div>
              <h5>Description</h5>
            </div>
            <div class="st-content-body">
              <p style="color: var(--st-text-light); line-height: 1.7; margin: 0;">{{ $course->description }}</p>
            </div>
          </div>
        @endif

        {{-- Video --}}
        @if($course->video)
          <div class="st-content-section st-fade-up st-fade-up-d2">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background: #fef3c7; color: #d97706;"><i class="bi bi-play-circle-fill"></i></div>
              <h5>Vidéo du cours</h5>
            </div>
            <div class="st-content-body">
              @php $videoFilename = basename($course->video); @endphp
              <div class="st-video-wrap">
                <video controls preload="metadata">
                  <source src="{{ route('video.stream', $videoFilename) }}" type="video/mp4">
                  Votre navigateur ne supporte pas la lecture vidéo.
                </video>
              </div>
            </div>
          </div>
        @endif

        {{-- PDF --}}
        @if($course->pdf)
          <div class="st-content-section st-fade-up st-fade-up-d3">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background: #fef2f2; color: var(--st-danger);"><i class="bi bi-file-earmark-pdf-fill"></i></div>
              <h5>Document PDF</h5>
            </div>
            <div class="st-content-body">
              <div class="st-flex st-gap-3">
                <a href="{{ asset('storage/'.$course->pdf) }}" target="_blank" class="st-btn" style="background: linear-gradient(135deg,#dc2626,#f87171); color: white;">
                  <i class="bi bi-eye"></i> Voir le PDF
                </a>
                <a href="{{ asset('storage/'.$course->pdf) }}" download class="st-btn st-btn-outline">
                  <i class="bi bi-download"></i> Télécharger
                </a>
              </div>
            </div>
          </div>
        @endif

        {{-- Devoirs --}}
        @if($course->devoirs->count() > 0)
          <div class="st-content-section st-fade-up st-fade-up-d4">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background: #f0fdf4; color: var(--st-success);"><i class="bi bi-pencil-fill"></i></div>
              <h5>Devoirs <span style="font-size: 12px; font-weight: 500; color: var(--st-text-light);">({{ $course->devoirs->count() }})</span></h5>
            </div>
            <div class="st-content-body">
              @foreach($course->devoirs as $devoir)
                <div class="st-devoir-card">
                  <h6>{{ $devoir->title }}</h6>
                  @if($devoir->description)
                    <p>{{ $devoir->description }}</p>
                  @endif
                  @if($devoir->file)
                    <div class="st-flex st-gap-2">
                      <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="st-btn st-btn-success st-btn-sm">
                        <i class="bi bi-eye"></i> Voir
                      </a>
                      <a href="{{ asset('storage/'.$devoir->file) }}" download class="st-btn st-btn-outline st-btn-sm">
                        <i class="bi bi-download"></i> Télécharger
                      </a>
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        @endif

      </div>

      {{-- Sidebar --}}
      <div class="col-lg-4">
        <div class="st-detail-sidebar st-fade-up st-fade-up-d2">
          <h5><i class="bi bi-card-list me-2" style="color: var(--st-primary);"></i>Informations</h5>

          @if($course->classRoom)
            <div class="st-detail-row"><span class="st-detail-label"><i class="bi bi-building me-1"></i>Classe</span><span class="st-detail-value">{{ $course->classRoom->name }}</span></div>
          @endif
          @if($course->subject)
            <div class="st-detail-row"><span class="st-detail-label"><i class="bi bi-book me-1"></i>Matière</span><span class="st-detail-value">{{ $course->subject->name }}</span></div>
          @endif
          <div class="st-detail-row">
            <span class="st-detail-label"><i class="bi bi-tag me-1"></i>Accès</span>
            <span class="st-detail-value">
              @if($course->is_free)
                <span class="st-badge st-badge-success">Gratuit</span>
              @else
                <span class="st-badge st-badge-warning">Premium</span>
              @endif
            </span>
          </div>
          @if($course->video)
            <div class="st-detail-row"><span class="st-detail-label"><i class="bi bi-camera-video me-1"></i>Vidéo</span><span class="st-detail-value" style="color: var(--st-success);">✓ Disponible</span></div>
          @endif
          @if($course->pdf)
            <div class="st-detail-row"><span class="st-detail-label"><i class="bi bi-file-pdf me-1"></i>PDF</span><span class="st-detail-value" style="color: var(--st-success);">✓ Disponible</span></div>
          @endif
          <div class="st-detail-row"><span class="st-detail-label"><i class="bi bi-calendar me-1"></i>Ajouté le</span><span class="st-detail-value">{{ optional($course->created_at)->format('d/m/Y') ?? '-' }}</span></div>

          <div class="st-mt-3">
            <a href="javascript:history.back()" class="st-btn st-btn-ghost st-btn-sm w-100">
              <i class="bi bi-arrow-left"></i> Retour
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
