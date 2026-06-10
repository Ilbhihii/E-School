@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">
    <div class="st-card st-fade-up">
      <div class="st-empty">
        <i class="bi bi-arrow-left"></i>
        <h5>Sélectionnez une matière</h5>
        <p>Veuillez d'abord choisir une matière pour accéder à cette page.</p>
        <a href="{{ route('student.classes') }}" class="st-btn st-btn-outline">
          <i class="bi bi-arrow-left me-1"></i>Retour aux classes
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
