

<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-fade-up">
      <div style="position:relative; z-index:1;">
        <div class="st-breadcrumb">
          <a href="{{ route('student.classes') }}"><i class="bi bi-building me-1"></i>Classes</a>
          <span>/</span>
          <a href="{{ route('student.subjects', $class) }}">{{ $class->name }}</a>
          <span>/</span>
          <span class="current">{{ $subject->name }}</span>
        </div>
        <h1><i class="bi bi-collection-fill me-2"></i>{{ $subject->name }}</h1>
        <p>Classe : {{ $class->name }} — Complétez les cours pour progresser</p>
      </div>
    </div>

    <div class="st-stats-row">
      <div class="st-stat-pill"><i class="bi bi-collection"></i> {{ $courses->count() }} cours</div>
      <div class="st-stat-pill"><i class="bi bi-file-earmark-pdf" style="color:#dc2626;"></i> Supports PDF</div>
      <div class="st-stat-pill"><i class="bi bi-play-circle" style="color:var(--st-primary);"></i> Vidéos incluses</div>
    </div>

    @if($courses->isEmpty())
      <div class="st-card st-fade-up">
        <div class="st-empty">
          <i class="bi bi-inbox"></i>
          <h5>Aucun cours disponible</h5>
          <p>Aucun cours n'est encore associé à cette matière.</p>
          <a href="{{ route('student.subjects', $class) }}" class="st-btn st-btn-outline">
            <i class="bi bi-arrow-left me-1"></i> Retour aux matières
          </a>
        </div>
      </div>
    @else
      <div class="row g-4">
        @foreach($courses as $course)
          <div class="col-md-6 col-lg-4">
            <div class="st-course-card st-fade-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
              <div class="st-course-banner" style="background: linear-gradient(135deg, {{ ['#0066CC','#16a34a','#7c3aed','#d97706','#dc2626','#0891b2'][$loop->index % 6] }}, {{ ['#0ea5e9','#22c55e','#a78bfa','#fbbf24','#f87171','#22d3ee'][$loop->index % 6] }});">
                <i class="bi bi-journal-bookmark-fill"></i>
              </div>
              <div class="st-course-body">
                <h5>{{ $course->title }}</h5>
                <p>{{ Str::limit($course->description, 90) }}</p>
                <div class="st-media-tags st-mb-3">
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
                <a href="{{ route('student.course.show', $course) }}" class="st-btn st-btn-primary w-100" style="text-align: center;">
                  <i class="bi bi-play-circle me-1"></i> Voir le cours
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

  </div>
</div>
@endsection