@extends('layouts.student')

@section('title', $test->title)

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-hero-violet st-fade-up st-flex-between">
      <div>
        <h1 id="testTitle">{{ $test->title }} <small id="timer" style="font-size: 1rem; opacity: .8;"></small></h1>
        <p>Durée: {{ $test->duration }} minutes — Répondez aux questions ci-dessous</p>
      </div>
      <div style="font-size: 2rem; opacity: .6;">
        <i class="bi bi-clock-history"></i>
      </div>
    </div>

    <form method="POST" action="{{ route('student.tests.submit', $test) }}">
      @csrf

      @foreach($test->questions as $question)
        <div class="st-card st-mb-4 st-fade-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
          <div class="st-card-header" style="background: #f8fafc;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--st-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0;">
              {{ $loop->iteration }}
            </div>
            <h5>{{ $question->question }}</h5>
          </div>
          <div class="st-card-body">
            @foreach($question->answers as $answer)
              <div class="form-check" style="padding: .6rem .75rem; border-radius: 10px; transition: background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <input class="form-check-input" type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $answer->id }}" id="q{{ $question->id }}a{{ $answer->id }}">
                <label class="form-check-label" for="q{{ $question->id }}a{{ $answer->id }}" style="font-size: 14px; cursor: pointer;">
                  {{ $answer->answer }}
                </label>
              </div>
            @endforeach
          </div>
        </div>
      @endforeach

      <div class="text-center st-mt-4">
        <button type="submit" class="st-btn st-btn-success st-btn-lg">
          <i class="bi bi-send me-1"></i> Envoyer les réponses
        </button>
      </div>
    </form>

  </div>
</div>

@push('scripts')
<script>
  let timeLeft = {{ $test->duration * 60 }};
  const timer = setInterval(() => {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('timer').innerHTML = '(' + minutes + ':' + seconds.toString().padStart(2, '0') + ')';
    timeLeft--;
    if (timeLeft < 0) {
      clearInterval(timer);
      document.querySelector('form').submit();
    }
  }, 1000);
</script>
@endpush
@endsection
