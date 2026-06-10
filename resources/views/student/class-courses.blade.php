@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">
    <div class="st-card st-fade-up">
      <div class="st-empty">
        <i class="bi bi-arrow-left-circle"></i>
        <h5>Redirection...</h5>
        <p>Cette page a été déplacée. Cliquez ci-dessous pour accéder aux cours.</p>
        <a href="{{ route('student.classes') }}" class="st-btn st-btn-primary">
          <i class="bi bi-building me-1"></i> Voir les classes
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  setTimeout(function() { window.location.href = '{{ route('student.classes') }}'; }, 3000);
</script>
@endsection
