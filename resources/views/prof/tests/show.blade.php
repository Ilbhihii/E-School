@extends('layouts.prof')

@section('title', $test->title)
@section('page_title', 'Résultats du test')
@section('breadcrumb', 'Détails du test')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-clipboard-data me-2" style="color:var(--adm-accent);"></i> {{ $test->title }}</h1>
        <div class="subtitle">
            {{ $test->subject->name }} · {{ $test->duration }} min · {{ $test->questions->count() }} question(s)
        </div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.tests.edit', $test) }}" class="adm-btn adm-btn-warning adm-btn-sm">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <a href="{{ route('prof.tests.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="adm-stats-grid">
    <div class="adm-stat purple">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-question-circle-fill"></i></div>
        </div>
        <div class="stat-value">{{ $test->questions->count() }}</div>
        <div class="stat-label">Questions</div>
    </div>
    <div class="adm-stat cyan">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
        </div>
        <div class="stat-value">{{ $test->duration }}</div>
        <div class="stat-label">Minutes</div>
    </div>
    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        </div>
        <div class="stat-value">{{ $test->results->count() }}</div>
        <div class="stat-label">Participants</div>
    </div>
    <div class="adm-stat blue">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-trophy-fill"></i></div>
        </div>
        <div class="stat-value">
            @if($test->results->avg('score'))
                {{ number_format($test->results->avg('score'), 1) }}
            @else
                —
            @endif
        </div>
        <div class="stat-label">Moyenne /{{ $test->questions->count() }}</div>
    </div>
</div>

<!-- Questions Preview -->
<div class="adm-card mb-4">
    <div class="adm-card-header">
        <h4><i class="bi bi-eye" style="color:rgba(255,255,255,0.35);"></i> Aperçu des questions</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $test->questions->count() }} question(s)</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Question</th>
                        <th>Réponses correctes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($test->questions as $index => $question)
                    <tr>
                        <td><span class="adm-badge adm-badge-primary">{{ $index + 1 }}</span></td>
                        <td><span style="font-weight:500;">{{ $question->question }}</span></td>
                        <td>
                            @php $correctAnswers = collect($question->answers ?? [])->filter(fn($a) => $a->is_correct ?? false)->values(); @endphp
                            @if($correctAnswers->count() === 1)
                                @php $ca = $correctAnswers->first(); @endphp
                                <span class="adm-badge adm-badge-success">
                                    @if(strtolower($ca->answer ?? '') === 'vrai' || strtolower($ca->answer ?? '') === 'true')
                                        Vrai
                                    @else
                                        {{ Str::limit($ca->answer ?? '', 30) }}
                                    @endif
                                </span>
                            @else
                                <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                    @foreach($correctAnswers as $ca)
                                        <span class="adm-badge adm-badge-success">{{ Str::limit($ca->answer ?? '', 25) }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Student Results -->
<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-bar-chart-fill" style="color:rgba(255,255,255,0.35);"></i> Résultats des étudiants</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $test->results->count() }} participation(s)</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        @if($test->results->count() > 0)
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Score</th>
                        <th>Note /20</th>
                        <th>Statut</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($test->results as $result)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="adm-avatar" style="background:linear-gradient(135deg,#7C3AED,#A78BFA);width:32px;height:32px;font-size:0.7rem;border-radius:8px;">
                                    {{ strtoupper(substr($result->user?->name ?? '?', 0, 1)) }}
                                </div>
                                <span style="font-weight:500;">{{ $result->user?->name ?? 'Inconnu' }}</span>
                            </div>
                        </td>
                        <td><span class="adm-badge adm-badge-info">{{ $result->score ?? 0 }} / {{ $test->questions->count() }}</span></td>
                        <td>
                            @php
                                $score = $test->questions->count() > 0 ? round(($result->score ?? 0) / $test->questions->count() * 20, 1) : 0;
                            @endphp
                            <span class="adm-badge {{ $score >= 10 ? 'adm-badge-success' : 'adm-badge-danger' }}">{{ $score }} / 20</span>
                        </td>
                        <td>
                            @if($result->score === null)
                                <span class="adm-badge adm-badge-warning">Non traité</span>
                            @elseif($result->score == 0)
                                <span class="adm-badge adm-badge-danger">Pas répondu</span>
                            @elseif($result->score >= ceil($test->questions->count() / 2))
                                <span class="adm-badge adm-badge-success">Accepté ✓</span>
                            @else
                                <span class="adm-badge adm-badge-danger">Refusé ✗</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if($result->user?->id)
                            <a href="{{ route('prof.tests.result.details', [$test->id, $result->user->id]) }}" class="adm-btn adm-btn-accent adm-btn-sm">
                                <i class="bi bi-eye me-1"></i> Détails
                            </a>
                            @else
                            <span class="adm-badge adm-badge-gray">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="adm-empty" style="padding:3rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
            <h5>Aucune participation</h5>
            <p>Aucun étudiant n'a encore passé ce test.</p>
        </div>
        @endif
    </div>
</div>

@endsection
