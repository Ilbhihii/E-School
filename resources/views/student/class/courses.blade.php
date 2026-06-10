@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-hero-green st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-play-circle me-2"></i>Cours de {{ $class->name }}</h1>
        <p>{{ $subject->name }} — Explorez les leçons disponibles</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-book-half"></i>
      </div>
    </div>

    @if($courses->count() > 0)
      <div class="row g-4">
        @foreach($courses as $course)
          <div class="col-lg-4 col-md-6">
            <a href="{{ route('student.course.show', $course->id) }}" class="text-decoration-none" style="display: block; height: 100%;">
              <div class="st-course-card st-fade-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                <div class="st-course-banner" style="background: linear-gradient(135deg, {{ ['#20c997','#198754','#0ea5e9','#8b5cf6','#f59e0b','#ef4444'][$loop->index % 6] }}, {{ ['#0d9488','#15803d','#2563eb','#7c3aed','#d97706','#dc2626'][$loop->index % 6] }});">
                  <i class="bi bi-journal-bookmark-fill"></i>
                </div>
                <div class="st-course-body">
                  <h5>{{ Str::limit($course->title, 40) }}</h5>
                  <div class="st-media-tags st-mb-2">
                    @if($course->video)
                      <span class="st-media-tag video"><i class="bi bi-play-circle-fill"></i> Vidéo</span>
                    @endif
                    @if($course->pdf)
                      <span class="st-media-tag pdf"><i class="bi bi-file-earmark-pdf-fill"></i> PDF</span>
                    @endif
                    @if(isset($course->devoirs_count) && $course->devoirs_count > 0)
                      <span class="st-media-tag devoir"><i class="bi bi-pencil-fill"></i> {{ $course->devoirs_count }} devoir(s)</span>
                    @endif
                  </div>
                  <div style="font-size: 12px; color: var(--st-primary); font-weight: 600;">
                    <i class="bi bi-arrow-right-circle me-1"></i>Voir le cours
                  </div>
                </div>
              </div>
            </a>
          </div>
        @endforeach
      </div>
    @else
      <div class="st-card st-fade-up">
        <div class="st-empty">
          <i class="bi bi-book"></i>
          <h5>Aucun cours disponible</h5>
          <p>Aucun cours n'est encore disponible pour cette classe et matière.</p>
          <a href="{{ route('student.subjects', $class->id) }}" class="st-btn st-btn-outline">
            <i class="bi bi-arrow-left me-1"></i> Retour aux matières
          </a>
        </div>
      </div>
    @endif

  </div>
</div>
@endsection
