@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    {{-- Hero with breadcrumb --}}
    <div class="st-hero st-hero-dark st-fade-up">
      <div style="position:relative; z-index:1;">
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
        <div class="st-flex st-gap-2 st-mt-2">
          @if($course->is_free)
            <span class="st-tag st-tag-free"><i class="bi bi-unlock me-1"></i>Gratuit</span>
          @else
            <span class="st-tag st-tag-premium"><i class="bi bi-lock me-1"></i>Premium</span>
          @endif
          @if($course->classRoom)
            <span class="st-tag" style="background:rgba(255,255,255,0.12); color:white;"><i class="bi bi-building me-1"></i>{{ $course->classRoom->name }}</span>
          @endif
          @if($course->subject)
            <span class="st-tag" style="background:rgba(255,255,255,0.12); color:white;"><i class="bi bi-book me-1"></i>{{ $course->subject->name }}</span>
          @endif
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        @if($course->description)
          <div class="st-content-section st-fade-up st-fade-up-d1">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background:#eff6ff; color:var(--st-primary);"><i class="bi bi-info-circle-fill"></i></div>
              <h5>Description du cours</h5>
            </div>
            <div class="st-content-body">
              <p style="color:var(--st-text-light); line-height:1.7; margin:0;">{{ $course->description }}</p>
            </div>
          </div>
        @endif
        @if($course->video)
          <div class="st-content-section st-fade-up st-fade-up-d2">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background:#fef3c7; color:#d97706;"><i class="bi bi-play-circle-fill"></i></div>
              <h5>Vidéo du cours</h5>
            </div>
            <div class="st-content-body">
              @php $videoFilename = basename($course->video); @endphp
              <div class="st-video-wrap">
                <video controls preload="metadata" controlsList="nodownload">
                  <source src="{{ route('video.stream', $videoFilename) }}" type="video/mp4">
                  Votre navigateur ne supporte pas la lecture vidéo.
                </video>
              </div>
            </div>
          </div>
        @endif
        @if($course->pdf)
          <div class="st-content-section st-fade-up st-fade-up-d3">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background:#fef2f2; color:var(--st-danger);"><i class="bi bi-file-earmark-pdf-fill"></i></div>
              <h5>Document PDF</h5>
            </div>
            <div class="st-content-body">
              <div class="st-flex st-gap-3">
                <a href="{{ asset('storage/'.$course->pdf) }}" target="_blank" class="st-btn" style="background:linear-gradient(135deg,#dc2626,#f87171); color:white;">
                  <i class="bi bi-eye"></i> Voir le PDF
                </a>
                <a href="{{ asset('storage/'.$course->pdf) }}" download class="st-btn st-btn-outline">
                  <i class="bi bi-download"></i> Télécharger
                </a>
              </div>
            </div>
          </div>
        @endif
        @if($course->devoirs->count() > 0)
          <div class="st-content-section st-fade-up st-fade-up-d4">
            <div class="st-content-header">
              <div class="st-cta-icon" style="background:#f0fdf4; color:var(--st-success);"><i class="bi bi-pencil-fill"></i></div>
              <h5>Devoirs <span style="font-size:12px; font-weight:500; color:var(--st-text-light);">({{ $course->devoirs->count() }})</span></h5>
            </div>
            <div class="st-content-body">
              @foreach($course->devoirs as $devoir)
                <div class="st-devoir-card">
                  <h6>{{ $devoir->title }}</h6>
                  @if($devoir->description)<p>{{ $devoir->description }}</p>@endif
                  @if($devoir->file)
                    <div class="st-flex st-gap-2">
                      <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="st-btn st-btn-success st-btn-sm"><i class="bi bi-eye"></i> Voir</a>
                      <a href="{{ asset('storage/'.$devoir->file) }}" download class="st-btn st-btn-outline st-btn-sm"><i class="bi bi-download"></i> Télécharger</a>
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>
      <div class="col-lg-4">
        <div class="st-detail-sidebar st-fade-up st-fade-up-d2">
          <h5><i class="bi bi-card-list me-2" style="color:var(--st-primary);"></i>Informations</h5>
          @if($course->classRoom)
            <div class="st-detail-row"><span class="st-detail-label"><i class="bi bi-building me-1"></i>Classe</span><span class="st-detail-value">{{ $course->classRoom->name }}</span></div>
          @endif
          @if($course->subject)
            <div class="st-detail-row"><span class="st-detail-label"><i class="bi bi-book me-1"></i>Matière</span><span class="st-detail-value">{{ $course->subject->name }}</span></div>
          @endif
          <div class="st-detail-row"><span class="st-detail-label"><i class="bi bi-tag me-1"></i>Accès</span><span class="st-detail-value">@if($course->is_free)<span class="st-badge st-badge-success">Gratuit</span>@else<span class="st-badge st-badge-warning">Premium</span>@endif</span></div>
          <div class="st-detail-row"><span class="st-detail-label"><i class="bi bi-calendar me-1"></i>Ajouté le</span><span class="st-detail-value">{{ optional($course->created_at)->format('d/m/Y') ?? '-' }}</span></div>
          @php $user = auth()->user(); @endphp
          <div class="st-mt-4" style="padding-top:1rem; border-top:1px solid var(--st-border);">
            <h5><i class="bi bi-person-check me-2" style="color:var(--st-primary);"></i>Mon abonnement</h5>
            @if($user->hasActiveSubscription())
              <div class="text-center st-mt-2">
                <i class="bi bi-check-circle-fill" style="font-size:2rem; color:var(--st-success); display:block; margin-bottom:.5rem;"></i>
                <p style="font-size:14px; color:var(--st-success); font-weight:600;">Abonnement actif</p>
                <small style="color:var(--st-text-light);">Accès complet à tous les cours</small>
              </div>
            @else
              <div class="text-center st-mt-2">
                <i class="bi bi-exclamation-circle-fill" style="font-size:2rem; color:var(--st-warning); display:block; margin-bottom:.5rem;"></i>
                <p style="font-size:14px; color:var(--st-warning); font-weight:600;">Accès limité</p>
                <small style="color:var(--st-text-light); display:block; margin-bottom:.75rem;">Abonnez-vous pour débloquer tous les cours premium</small>
                <a href="{{ route('student.payment') }}" class="st-btn st-btn-primary w-100">Voir les offres</a>
              </div>
            @endif
          </div>
        </div>
        <div class="text-center st-mt-2">
          <a href="javascript:history.back()" class="st-btn st-btn-ghost st-btn-sm"><i class="bi bi-arrow-left"></i> Retour aux cours</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection