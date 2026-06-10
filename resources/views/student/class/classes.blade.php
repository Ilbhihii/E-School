@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-building me-2"></i>Mes Classes</h1>
        <p>Sélectionnez une classe pour accéder aux cours disponibles</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-building"></i>
      </div>
    </div>

    @if($classes->isEmpty())
      <div class="st-card st-fade-up">
        <div class="st-empty">
          <i class="bi bi-inbox"></i>
          <h5>Aucune classe disponible</h5>
          <p>Vous n'êtes assigné à aucune classe pour le moment.</p>
          <a href="{{ route('student.dashboard') }}" class="st-btn st-btn-outline">
            <i class="bi bi-house me-1"></i> Retour au dashboard
          </a>
        </div>
      </div>
    @else
      <div class="row g-4">
        @foreach($classes as $class)
          <div class="col-sm-6 col-lg-4">
            <a href="{{ route('student.subjects', $class) }}" class="st-item st-item-blue st-fade-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
              <div class="st-item-top">
                <div class="st-item-icon"><i class="bi bi-building"></i></div>
                <h5>{{ $class->name }}</h5>
              </div>
              <div class="st-item-bottom">
                <span><i class="bi bi-book me-1"></i>Voir les matières</span>
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
