@extends('layouts.prof')

@section('title', 'Mes Tests')
@section('page_title', 'Tests')
@section('breadcrumb', 'Gestion des tests')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-clipboard-check me-2" style="color:var(--adm-accent);"></i> Mes Tests</h1>
        <div class="subtitle">Créez et gérez les QCM et évaluations</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.tests.create') }}" class="adm-btn adm-btn-accent">
            <i class="bi bi-plus-lg"></i> Créer un test
        </a>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-list-check" style="color:rgba(255,255,255,0.35);"></i> Tous les tests</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $tests->count() }} tests</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>matière</th>
                        <th>Durée</th>
                        <th>Questions</th>
                        <th>Note moyenne</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tests as $test)
                    <tr>
                        <td><span style="font-weight:500;">{{ $test->title }}</span></td>
                        <td><span class="adm-badge adm-badge-primary">{{ $test->subject->name }}</span></td>
                        <td><span class="adm-badge adm-badge-info">{{ $test->duration }} min</span></td>
                        <td><span class="adm-badge adm-badge-accent">{{ $test->questions_count }}</span></td>
                        <td>
                            @if($test->average_score)
                                <span class="adm-badge {{ $test->average_score >= 10 ? 'adm-badge-success' : 'adm-badge-danger' }}">
                                    {{ number_format($test->average_score, 2) }} / 20
                                </span>
                            @else
                                <span style="color:var(--adm-text-muted);font-size:0.8rem;">Non noté</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('prof.tests.show', $test) }}" class="adm-btn adm-btn-success adm-btn-sm" title="Voir les résultats">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                                <a href="{{ route('prof.tests.edit', $test) }}" class="adm-btn adm-btn-warning adm-btn-sm" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('prof.tests.destroy', $test) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce test ?')">
                                    @csrf @method('DELETE')
                                    <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-clipboard-x"></i></div>
                                <h5>Aucun test</h5>
                                <p>Créez votre premier test pour commencer.</p>
                                <a href="{{ route('prof.tests.create') }}" class="adm-btn adm-btn-accent adm-btn-sm">
                                    <i class="bi bi-plus-lg"></i> Créer un test
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
