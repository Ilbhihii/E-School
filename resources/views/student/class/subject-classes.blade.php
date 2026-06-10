@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">
    <div class="st-hero st-hero-green st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-mortarboard me-2"></i>{{ $subject->name }} — {{ $level->name }}</h1>
        <p>Sélectionnez une classe</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-mortarboard-fill"></i>
      </div>
    </div>

    <div class="row g-4">
      @foreach($classes as $class)
        <div class="col-md-4">
          <a href="{{ route('student.courses', [$subject->id, $class->id]) }}" class="st-item st-item-{{ ['blue','green','purple','yellow','red','teal'][$loop->index % 6] }} st-fade-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
            <div class="st-item-top">
              <div class="st-item-icon"><i class="bi bi-mortarboard"></i></div>
              <h5>{{ $class->name }}</h5>
            </div>
            <div class="st-item-bottom">
              <span><i class="bi bi-arrow-right me-1"></i>Voir les cours</span>
              <div class="st-arrow"><i class="bi bi-arrow-right"></i></div>
            </div>
          </a>
        </div>
      @endforeach
    </div>

    <div class="st-mt-4">
      <a href="{{ route('student.classes') }}" class="st-btn st-btn-outline">
        <i class="bi bi-arrow-left me-1"></i>Retour aux classes
      </a>
    </div>
  </div>
</div>
@endsection
