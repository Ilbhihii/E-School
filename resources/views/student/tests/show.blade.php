@extends('layouts.student')

@section('title', $test->title)

@section('content')
<div class="container">
    <h1>{{ $test->title }}</h1>
    <p>Durée: {{ $test->duration }} minutes</p>

    <form method="POST" action="{{ route('student.tests.submit', $test) }}">
        @csrf
        @foreach($test->questions as $question)
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ $question->question }}</h5>
                </div>
                <div class="card-body">
                    @foreach($question->answers as $answer)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $answer->id }}" id="q{{ $question->id }}a{{ $answer->id }}">
                            <label class="form-check-label" for="q{{ $question->id }}a{{ $answer->id }}">
                                {{ $answer->answer }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-success btn-lg">Envoyer les réponses</button>
    </form>

    <script>
        // Simple timer bonus
        let timeLeft = {{ $test->duration * 60 }};
        const timer = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.querySelector('h1').innerHTML = '{{ $test->title }} <small>(' + minutes + ':' + seconds.toString().padStart(2, '0') + ')</small>';
            timeLeft--;
            if (timeLeft < 0) {
                clearInterval(timer);
                document.querySelector('form').submit();
            }
        }, 1000);
    </script>
</div>
@endsection

