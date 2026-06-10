@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-hero-violet st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-layers-fill me-2"></i>Choisir un niveau</h1>
        <p>Sélectionnez votre niveau d'études</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-layers"></i>
      </div>
    </div>

    <div class="row g-4">
      @foreach($levels->unique('name') as $level)
        <div class="col-md-4">
          <a href="{{ route('student.subjects', $level->id) }}" class="st-item st-item-{{ ['blue','green','purple','yellow','red','teal'][$loop->index % 6] }} st-fade-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
            <div class="st-item-top">
              <div class="st-item-icon"><i class="bi bi-mortarboard"></i></div>
              <h5>{{ $level->name }}</h5>
            </div>
            <div class="st-item-bottom">
              <span><i class="bi bi-arrow-right me-1"></i>Voir les matières</span>
              <div class="st-arrow"><i class="bi bi-arrow-right"></i></div>
            </div>
          </a>
        </div>
      @endforeach
    </div>

  </div>
</div>
@endsection
