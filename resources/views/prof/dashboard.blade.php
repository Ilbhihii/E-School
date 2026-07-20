@extends('layouts.prof')

@section('page_title', 'Tableau de bord')
@section('breadcrumb', 'Vue d\'ensemble')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>Tableau de bord Professeur</h1>
        <div class="subtitle">Bienvenue {{ auth()->user()->name }} — analyse de vos données pédagogiques</div>
    </div>
    <div class="page-actions">
        <span style="color:var(--adm-text-muted);font-size:0.85rem;">
            <i class="bi bi-calendar3 me-1"></i> {{ now()->format('d M Y') }}
        </span>
    </div>
</div>

<!-- Stats -->
<div class="adm-stats-grid">
    <div class="adm-stat blue">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        </div>
        <div class="stat-value">{{ $studentsCount ?? 0 }}</div>
        <div class="stat-label">Étudiants</div>
    </div>
    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-file-earmark-plus-fill"></i></div>
        </div>
        <div class="stat-value">{{ $coursesCount ?? 0 }}</div>
        <div class="stat-label">Mes cours</div>
    </div>
    <div class="adm-stat orange">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
        </div>
        <div class="stat-value">{{ $assignmentsCount ?? 0 }}</div>
        <div class="stat-label">Copies reçues</div>
    </div>
    <div class="adm-stat red">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-calendar-x-fill"></i></div>
        </div>
        <div class="stat-value">{{ $absencesCount ?? 0 }}</div>
        <div class="stat-label">Absences</div>
    </div>

    <div class="adm-stat cyan">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-camera-video"></i></div>
        </div>
        <div class="stat-value">{{ $livesCount ?? 0 }}</div>
        <div class="stat-label">Lives</div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="adm-card h-100">
            <div class="adm-card-header">
                <h4><i class="bi bi-bar-chart-fill" style="color:#60A5FA;"></i> Analyse pédagogique</h4>
                <span style="font-size:.75rem;color:var(--adm-text-muted);">Données de vos classes assignées</span>
            </div>
            <div class="adm-card-body">
                @foreach([
                    ['label' => 'Copies corrigées', 'value' => $correctedCount ?? 0, 'total' => $assignmentsCount ?? 0, 'pct' => $correctionRate ?? 0, 'color' => '#4ADE80'],
                    ['label' => 'Taux de présence', 'value' => ($presenceRate ?? 0).'%', 'total' => null, 'pct' => $presenceRate ?? 0, 'color' => '#38BDF8'],
                    ['label' => 'Moyenne des étudiants', 'value' => number_format($averageGrade ?? 0, 1).'/20', 'total' => null, 'pct' => min(($averageGrade ?? 0) * 5, 100), 'color' => '#F59E0B'],
                ] as $indicator)
                    <div style="margin-bottom:1.15rem;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="color:var(--adm-text);font-size:.86rem;font-weight:600;">{{ $indicator['label'] }}</span>
                            <span style="color:{{ $indicator['color'] }};font-weight:700;">
                                {{ $indicator['value'] }}@if($indicator['total'] !== null) / {{ $indicator['total'] }}@endif
                            </span>
                        </div>
                        <div style="height:7px;background:rgba(148,163,184,.12);border-radius:99px;overflow:hidden;">
                            <div style="width:{{ min($indicator['pct'], 100) }}%;height:100%;background:{{ $indicator['color'] }};border-radius:99px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="adm-card h-100">
            <div class="adm-card-header"><h4><i class="bi bi-diagram-3" style="color:#A78BFA;"></i> Mes affectations</h4></div>
            <div class="adm-card-body">
                @forelse(($profAssignments ?? collect())->take(5) as $assignment)
                    <div style="padding:.7rem 0;border-bottom:1px solid rgba(148,163,184,.1);">
                        <div style="font-weight:700;color:var(--adm-text);">{{ $assignment->subject?->name ?? 'Matière' }}</div>
                        <div style="font-size:.75rem;color:var(--adm-text-muted);margin-top:3px;">
                            {{ $assignment->level?->name ?? 'Niveau' }} · {{ $assignment->classRoom?->name ?? 'Classe' }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4" style="color:var(--adm-text-muted);">
                        <i class="bi bi-inbox" style="font-size:1.7rem;"></i>
                        <p class="mb-0 mt-2">Aucune affectation administrateur.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<h5 style="color:var(--adm-text-muted);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:1rem;text-align:center;">
    Actions Rapides
</h5>

<div class="row g-3 justify-content-center">
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('prof.absences') }}" class="adm-action-card">
            <div class="action-icon" style="background:rgba(217,4,41,0.15);color:#EF4444;">
                <i class="bi bi-calendar-x"></i>
            </div>
            <span class="action-title">Absences</span>
            <span class="action-count">Marquer les présences</span>
        </a>
    </div>
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('prof.assignments') }}" class="adm-action-card">
            <div class="action-icon" style="background:rgba(0,58,143,0.15);color:#60A5FA;">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <span class="action-title">Devoirs</span>
            <span class="action-count">Gérer les devoirs</span>
        </a>
    </div>
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('prof.chat.subjects') }}" class="adm-action-card">
            <div class="action-icon" style="background:rgba(22,163,74,0.15);color:#4ADE80;">
                <i class="bi bi-chat-dots"></i>
            </div>
            <span class="action-title">Messages</span>
            <span class="action-count">Répondre étudiants</span>
        </a>
    </div>

</div>

<!-- Recent Activity -->
<div class="adm-card mt-5">
    <div class="adm-card-header">
        <h4><i class="bi bi-activity" style="color:#4ADE80;"></i> Activité récente</h4>
    </div>
    <div class="adm-card-body p-0">
        <div style="padding:0 1.5rem;">
            <div class="adm-activity">
                @forelse($recentSubmissions ?? [] as $submission)
                    <div class="adm-activity-item">
                        <div class="adm-activity-dot" style="background:{{ $submission->grade === null ? '#F59E0B' : '#4ADE80' }};"></div>
                        <div class="adm-activity-content">
                            <p><strong>{{ $submission->user?->name ?? 'Étudiant' }}</strong> a envoyé « {{ $submission->title }} »</p>
                            <div class="adm-activity-time">{{ $submission->subject?->name ?? 'Matière' }} · {{ $submission->created_at?->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="adm-activity-item">
                        <div class="adm-activity-dot" style="background:#64748B;"></div>
                        <div class="adm-activity-content"><p>Aucune copie récente dans vos classes.</p></div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
