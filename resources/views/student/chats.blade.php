@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-hero-violet st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-chat-dots-fill me-2"></i>Chat par matière</h1>
        <p>Discutez avec vos professeurs et camarades</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-chat-dots-fill"></i>
      </div>
    </div>

    <div class="row g-4">
      @foreach($subjects->unique('name') as $subject)
        <div class="col-lg-4 col-md-6">
          <a href="{{ route('student.student.chat', $subject->id) }}" class="st-chat-card st-fade-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
            <div style="padding: 2rem 1.5rem;">
              <div class="st-chat-icon">
                <i class="bi bi-book-half"></i>
              </div>
              <h5 style="font-weight: 700; text-align: center; margin-bottom: .5rem;">{{ $subject->name }}</h5>
              <p style="font-size: 13px; color: var(--st-text-light); text-align: center; margin-bottom: 1rem;">
                Discutez avec vos professeurs et camarades
              </p>
              <div class="st-btn w-100" style="background: linear-gradient(135deg, var(--st-primary), var(--st-secondary)); color: white; text-align: center;">
                <i class="bi bi-chat-dots me-1"></i> Ouvrir Chat
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>

  </div>
</div>
@endsection
