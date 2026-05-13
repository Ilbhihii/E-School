@extends('layouts.prof')

@section('title', $test->title)

@section('content')
<div class="container">
    <h1>{{ $test->title }}</h1>
    <p>Matière: {{ $test->subject->name }}</p>
    <p>Durée: {{ $test->duration }} minutes</p>
    <p>Nombre de questions: {{ $test->questions->count() }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Question</th>
                <th>Réponses correctes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($test->questions as $question)
                <tr>
                    <td>{{ $question->question }}</td>
                    <td>
@php $correctAnswers = collect($question->answers ?? [])->filter(fn($a) => $a->is_correct ?? false)->values(); @endphp
                        @if($correctAnswers->count() === 1)
@php $correctAnswer = $correctAnswers->first(); @endphp
                            <span class="badge badge-success">
                                @if(strtolower($correctAnswer->answer) === 'vrai' || strtolower($correctAnswer->answer) === 'true')
                                    Vrai
                                @else
                                    Faux
                                @endif
                            </span>
                        @else
                            @foreach($correctAnswers as $answer)
                                <span class="badge badge-success">{{ $answer->answer }}</span>
                            @endforeach
                        @endif
                </td>
            </tr>

            @endforeach
        </tbody>
    </table>



    <a href="{{ route('prof.tests.index') }}" class="btn btn-secondary">Retour</a>

@if($test->results->count() > 0)
<h3 class="mt-5">Résumé des réponses des étudiants</h3>
    <table class="table">
        <thead>
<tr>
                <th>Étudiant</th>
                <th>Score</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($test->results as $result)
            <tr>
<td>{{ $result->user?->name ?? 'Inconnu' }}</td>
                <td>{{ $result->score ?? 0 }} / {{ $test->questions->count() }}</td>
                <td>
                    @if($result->score === null)
                        <span class="badge badge-warning text-warning">Non traité</span>
                    @elseif($result->score == 0)
                        <span class="badge badge-danger text-danger">N'a pas répondu</span>
                    @elseif($result->score >= ceil($test->questions->count() / 2))
                        <span class="badge badge-success text-success">Accepté</span>
@else
                        <span class="badge badge-danger text-danger">Refusé</span>
                    @endif
                </td>
                <td>
<a href="{{ route('prof.tests.result.details', [$test->id, $result->user?->id ?? 0]) }}" class="btn btn-primary btn-sm" {{ !$result->user?->id ? 'disabled' : '' }}>
                        Voir détails
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>


    @else
    <div class="alert alert-info mt-4">Aucun étudiant n'a encore passé ce test.</div>
    @endif
</div>
@endsection

