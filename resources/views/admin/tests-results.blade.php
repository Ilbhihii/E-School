@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div class="adm-flex adm-gap-3" style="align-items:center;">
                <div style="width:56px;height:56px;background:linear-gradient(135deg,#10b981,#059669);border-radius:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-award-fill" style="font-size:1.5rem;color:white;"></i>
                </div>
                <div>
                    <h1 class="admin-header-title"><span class="gradient">Résultats de Tests</span></h1>
                    <p class="admin-header-subtitle">Performance académique de <strong>{{ $user->name }}</strong></p>
                </div>
            </div>
            <a href="{{ route('admin.users') }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <!-- SUMMARY -->
        <div class="stats-grid">
            <div class="stat-card green adm-fade-up">
                <div class="stat-card-icon green"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $testsCount }}</div>
                    <div class="stat-card-label">Tests Passés</div>
                </div>
            </div>
            <div class="stat-card blue adm-fade-up">
                <div class="stat-card-icon blue"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <div class="stat-card-value">{{ $testsCount > 0 ? number_format($avgPercentage ?? 0, 1) . '%' : '0%' }}</div>
                    <div class="stat-card-label">Moyenne Générale</div>
                </div>
            </div>
            <div class="stat-card purple adm-fade-up">
                <div class="stat-card-icon purple"><i class="bi bi-star-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $testsCount > 0 ? number_format((($avgPercentage ?? 0) / 100) * 20, 1) . ' / 20' : 'Non noté' }}</div>
                    <div class="stat-card-label">Note Moyenne</div>
                </div>
            </div>
        </div>

        @if($user->results->count() > 0)
        <div class="adm-card">
            <div class="adm-card-header" style="background:linear-gradient(135deg,#f0fdf4,#ecfdf5);">
                <h3><i class="bi bi-clock-history"></i> Historique des Tests</h3>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Test</th>
                            <th>Matière</th>
                            <th>Score</th>
                            <th>Note /20</th>
                            <th>Pourcentage</th>
                            <th>Date</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->results as $result)
                        <tr>
                            <td><span style="font-weight:600;">{{ $result->test->title ?? 'Test supprimé' }}</span></td>
                            <td><span class="adm-badge adm-badge-purple">{{ $result->test->subject->name ?? '-' }}</span></td>
                            <td>
                                <span class="adm-badge adm-badge-success">{{ $result->score }} / {{ $result->total_questions }}</span>
                            </td>
                            <td>
                                <span class="adm-badge adm-badge-primary">
                                    {{ $result->total_questions > 0 ? number_format(($result->score / $result->total_questions) * 20, 2) . ' / 20' : '0 / 20' }}
                                </span>
                            </td>
                            <td><span style="font-weight:700;color:var(--adm-primary);font-size:1.05rem;">{{ $result->percentage }}%</span></td>
                            <td><span class="adm-badge adm-badge-gray">{{ $result->created_at->format('d/m/Y H:i') }}</span></td>
                            <td>
                                <a href="{{ route('admin.users.test-result', [$user->id, $result->test_id]) }}" class="adm-btn adm-btn-sm adm-btn-primary">
                                    <i class="bi bi-eye-fill"></i> Détails
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="adm-card" style="text-align:center;padding:3rem;">
            <div class="adm-empty-icon" style="margin:0 auto 1.25rem;">
                <i class="bi bi-inbox"></i>
            </div>
            <h3>Aucun résultat de test</h3>
            <p style="color:var(--adm-text-secondary);margin-bottom:1.25rem;">Ce étudiant n'a pas encore passé de tests</p>
            <a href="{{ route('admin.users.index') }}" class="adm-btn adm-btn-primary">Voir tous les étudiants</a>
        </div>
        @endif

    </div>
</div>
@endsection
