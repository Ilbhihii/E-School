@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-fade-up st-flex-between">
      <div>
        <div class="st-breadcrumb">
          <a href="{{ route('student.classes') }}"><i class="bi bi-building me-1"></i>Classes</a>
          <span>/</span>
          <span class="current">{{ $class->name }}</span>
        </div>
        <h1><i class="bi bi-book-half me-2"></i>Matières — {{ $class->name }}</h1>
        <p>Choisissez une matière pour voir les cours disponibles</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-book-half"></i>
      </div>
    </div>

    @if($subjects->isEmpty())
      <div class="st-card st-fade-up">
        <div class="st-empty">
          <i class="bi bi-inbox"></i>
          <h5>Aucune matière disponible</h5>
          <p>Aucune matière n'est associée à cette classe pour le moment.</p>
          <a href="{{ route('student.classes') }}" class="st-btn st-btn-outline">
            <i class="bi bi-arrow-left me-1"></i> Retour aux classes
          </a>
        </div>
      </div>
    @else
      <div class="row g-4">
        @foreach($subjects as $subject)
          @php $colors = ['bi-calculator','bi-flask','bi-translate','bi-globe','bi-palette','bi-music-note-beamed','bi-cpu','bi-graph-up']; @endphp
          <div class="col-sm-6 col-lg-4 col-xl-3">
            <a href="{{ route('student.courses', [$class, $subject]) }}" class="st-item st-item-{{ ['blue','green','purple','yellow','red','teal'][$loop->index % 6] }} st-fade-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
              <div class="st-item-top">
                <div class="st-item-icon"><i class="bi {{ $colors[$loop->index % count($colors)] }}"></i></div>
                <h5>{{ $subject->name }}</h5>
              </div>
              <div class="st-item-bottom">
                <span><i class="bi bi-collection me-1"></i>Voir les cours</span>
                <div class="st-arrow"><i class="bi bi-arrow-right"></i></div>
              </div>
            </a>
          </div>
        @endforeach
      </div>
    @endif

  </div>
</div>
@endsection
