@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container-lg">

    <div class="st-hero st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-collection-fill me-2"></i>Cours de la classe</h1>
        <p>{{ $class->name }} — Complétez les cours pour progresser</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-easel3-fill"></i>
      </div>
    </div>

    <div class="row g-4">
      @foreach($class->courses as $course)
        <div class="col-md-6 col-lg-3">
          <a href="{{ route('student.course.show', $course->id) }}" class="text-decoration-none" style="display: block; height: 100%;">
            <div class="st-course-card st-fade-up" style="animation-delay: {{ ($loop->index) * 0.1 }}s;">
              <div class="st-course-banner" style="background: linear-gradient(135deg, {{ ['#8b5cf6','#3b82f6','#06b6d4','#10b981','#f59e0b','#ef4444'][$loop->index % 6] }}, {{ ['#7c3aed','#2563eb','#0891b2','#059669','#d97706','#dc2626'][$loop->index % 6] }});">
                <i class="bi bi-journal-bookmark-fill"></i>
              </div>
              <div class="st-course-body">
                <h5>{{ $course->title }}</h5>
                <div class="st-media-tags st-mb-2">
                  @if($course->pdf)
                    <span class="st-media-tag pdf"><i class="bi bi-file-earmark-pdf-fill"></i> PDF</span>
                  @endif
                  @if($course->video)
                    <span class="st-media-tag video"><i class="bi bi-play-circle-fill"></i> Vidéo</span>
                  @endif
                </div>
                <div style="font-size: 12px; color: var(--st-primary); font-weight: 600;">
                  <i class="bi bi-arrow-right-circle me-1"></i>Cliquez pour voir
                </div>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>

  </div>
</div>
@endsection
