@extends('layouts.prof')

@section('content')
<h2>{{ $result->user->name }} - Résultat</h2>
<p>Score: {{ $result->score }} / {{ $test->questions->count() }}</p>

@foreach($test->questions as $question)
    <div class="mb-4">
        <strong>{{ $question->question }}</strong>

        @php
            $studentAnswers = collect($result->answers ?? [])
                ->where('question_id', $question->id);
        @endphp

        @if($studentAnswers->count() > 0)

            @foreach($studentAnswers as $ans)
                <div class="p-2 rounded mb-1 
                    {{ $ans->answer && $ans->answer->is_correct ? 'bg-success text-white' : 'bg-danger text-white' }}">

                    {{ $ans->answer->answer ?? 'Réponse inconnue' }}

                    @if($ans->answer && $ans->answer->is_correct)
                        ✅ Bonne réponse
                    @else
                        ❌ Mauvaise réponse
                    @endif
                </div>
            @endforeach

        @else

            <div class="p-2 rounded bg-secondary text-white">
                ❌ N'a pas répondu à la question
            </div>

        @endif
    </div>
@endforeach

@endsection
