@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">{{ $test->title }}</span></h1>
                <p class="admin-header-subtitle">
                    Matière: <strong>{{ $test->subject->name }}</strong> • 
                    Durée: <strong>{{ $test->duration }} minutes</strong> • 
                    Questions: <strong>{{ $test->questions->count() }}</strong>
                </p>
            </div>
            <a href="{{ route('prof.tests.index') }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <!-- QUESTIONS LIST -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-header">
                <h3>Questions du test</h3>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Réponses correctes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($test->questions as $question)
                        <tr>
                            <td><span style="font-weight:600;">{{ $question->question }}</span></td>
                            <td>
                                @php $correctAnswers = collect($question->answers ?? [])->filter(fn($a) => $a->is_correct ?? false)->values(); @endphp
                                @foreach($correctAnswers as $answer)
                                    <span class="adm-badge adm-badge-success">{{ $answer->answer }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- STUDENT RESULTS -->
        @if($test->results->count() > 0)
        <div class="adm-card">
            <div class="adm-card-header">
                <h3>Résultats des étudiants</h3>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
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
                            <td><span style="font-weight:600;">{{ $result->user?->name ?? 'Inconnu' }}</span></td>
                            <td><span class="adm-badge adm-badge-gray">{{ $result->score ?? 0 }} / {{ $test->questions->count() }}</span></td>
                            <td>
                                @if($result->score === null)
                                    <span class="adm-badge adm-badge-gray">Non traité</span>
                                @elseif($result->score == 0)
                                    <span class="adm-badge adm-badge-danger">N'a pas répondu</span>
                                @elseif($result->score >= ceil($test->questions->count() / 2))
                                    <span class="adm-badge adm-badge-success">Accepté</span>
                                @else
                                    <span class="adm-badge adm-badge-danger">Refusé</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('prof.tests.result.details', [$test->id, $result->user?->id ?? 0]) }}" class="adm-btn adm-btn-sm adm-btn-primary" {{ !$result->user?->id ? 'disabled' : '' }}>
                                    Voir détails
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="adm-alert adm-alert-info">Aucun étudiant n'a encore passé ce test.</div>
        @endif

    </div>
</div>
@endsection
