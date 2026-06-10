@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:700px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">{{ $result->user->name }} - Résultat</span></h1>
                <p class="admin-header-subtitle">Score: <strong>{{ $result->score }} / {{ $test->questions->count() }}</strong></p>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card-body">
                @foreach($test->questions as $question)
                <div class="adm-mb-3" style="border-bottom:1px solid var(--adm-border);padding-bottom:1rem;">
                    <strong style="display:block;margin-bottom:0.5rem;">{{ $question->question }}</strong>

                    @php
                        $studentAnswers = collect($result->answers ?? [])->where('question_id', $question->id);
                    @endphp

                    @if($studentAnswers->count() > 0)
                        @foreach($studentAnswers as $ans)
                            <div class="adm-badge {{ $ans->answer && $ans->answer->is_correct ? 'adm-badge-success' : 'adm-badge-danger' }}" style="display:block;text-align:left;margin-bottom:0.3rem;padding:0.5rem 0.75rem;border-radius:8px;">
                                {{ $ans->answer->answer ?? 'Réponse inconnue' }}
                                @if($ans->answer && $ans->answer->is_correct)
                                    ✅ Bonne réponse
                                @else
                                    ❌ Mauvaise réponse
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="adm-badge adm-badge-gray" style="display:block;padding:0.5rem 0.75rem;border-radius:8px;">
                            ❌ N'a pas répondu à la question
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
