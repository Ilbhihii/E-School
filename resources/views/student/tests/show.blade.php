@extends('layouts.student')
@section('title', $test->title)
@section('content')

<style>
.question-card {
    background: #1E293B;
    border: 1px solid rgba(255,255,255,0.04);
    border-radius: 10px;
    padding: 1.25rem;
    margin-bottom: 1rem;
}
.question-num {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(124,58,237,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.82rem;
    color: #7C3AED;
    flex-shrink: 0;
}
.option-item {
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.04);
    transition: all 0.2s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
}
.option-item:hover {
    background: rgba(255,255,255,0.03);
    border-color: rgba(255,255,255,0.08);
}
.option-item input[type="checkbox"] {
    width: 18px; height: 18px;
    accent-color: #7C3AED;
    cursor: pointer;
    flex-shrink: 0;
}
.option-item label {
    color: #94A3B8;
    font-size: 0.88rem;
    cursor: pointer;
    margin: 0;
    flex: 1;
}
.timer {
    font-weight: 700;
    font-size: 1.3rem;
    color: #0284C7;
    font-variant-numeric: tabular-nums;
}
.timer.warning { color: #DC2626; animation: blink 1s ease-in-out infinite; }
@keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }
</style>

<div class="page-header">
    <div>
        <h1 style="font-size:1.15rem;"><i class="bi bi-pencil-square me-2" style="color:#7C3AED;"></i> {{ $test->title }}</h1>
        <div class="subtitle">
            <span class="pr-badge pr-badge-info me-2"><i class="bi bi-clock me-1"></i> {{ $test->duration }} minutes</span>
            <span class="pr-badge pr-badge-purple"><i class="bi bi-question-circle me-1"></i> {{ $test->questions->count() }} questions</span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="timer" id="timerDisplay">{{ $test->duration }}:00</div>
    </div>
</div>

<form method="POST" action="{{ route('student.tests.submit', $test) }}" id="testForm">
    @csrf

    @foreach($test->questions as $index => $question)
    <div class="question-card">
        <div class="d-flex align-items-start gap-3 mb-3">
            <div class="question-num">{{ $index + 1 }}</div>
            <h5 style="font-weight:600;color:#F1F5F9;margin:0;font-size:0.9rem;">{{ $question->question }}</h5>
        </div>
        <div style="display:flex;flex-direction:column;gap:6px;padding-left:42px;">
            @foreach($question->answers as $answer)
            <div class="option-item">
                <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $answer->id }}" id="q{{ $question->id }}a{{ $answer->id }}">
                <label for="q{{ $question->id }}a{{ $answer->id }}">{{ $answer->answer }}</label>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div style="text-align:center;margin-top:1.5rem;">
        <button type="submit" class="pr-btn pr-btn-primary" style="padding:10px 36px;font-size:1rem;">
            <i class="bi bi-check2-circle me-2"></i> Envoyer les réponses
        </button>
    </div>
</form>

<script>
(function() {
    let timeLeft = {{ $test->duration * 60 }};
    const timer = document.getElementById('timerDisplay');
    const form = document.getElementById('testForm');
    const interval = setInterval(() => {
        const m = Math.floor(timeLeft / 60);
        const s = timeLeft % 60;
        timer.textContent = m + ':' + s.toString().padStart(2, '0');
        if (timeLeft <= 60) { timer.classList.add('warning'); timer.style.color = '#DC2626'; }
        timeLeft--;
        if (timeLeft < 0) { clearInterval(interval); form.submit(); }
    }, 1000);
})();
</script>

@endsection
