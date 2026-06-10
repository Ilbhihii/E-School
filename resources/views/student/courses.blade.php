@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-hero-green st-fade-up">
      <h1><i class="bi bi-book-half me-2"></i>Mes Cours</h1>
      <p>
        @if($classes->count() > 0)
          Choisissez une classe pour voir les cours
        @else
          Aucune classe assignée. Contactez l'administrateur.
        @endif
      </p>
    </div>

    @if($classes->count() > 0)
      <div class="row g-4">
        @foreach($classes as $class)
          <div class="col-lg-4 col-md-6">
            <a href="{{ route('student.classes') }}" class="st-item st-item-green st-fade-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
              <div class="st-item-top">
                <div class="st-item-icon"><i class="bi bi-building"></i></div>
                <h5>{{ $class->name }}</h5>
              </div>
              <div class="st-item-bottom">
                <span><i class="bi bi-play-circle me-1"></i>{{ $class->courses_count }} cours</span>
                <div class="st-arrow"><i class="bi bi-arrow-right"></i></div>
              </div>
            </a>
          </div>
        @endforeach
      </div>
    @else
      <div class="st-card st-fade-up">
        <div class="st-empty">
          <i class="bi bi-inbox"></i>
          <h5>Aucune classe disponible</h5>
          <p>Vous n'êtes assigné à aucune classe pour le moment.</p>
          <a href="{{ route('student.dashboard') }}" class="st-btn st-btn-outline">
            <i class="bi bi-house me-1"></i> Retour au Dashboard
          </a>
        </div>
      </div>
    @endif

  </div>
</div>
@endsection
