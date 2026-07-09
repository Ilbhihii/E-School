@extends('layouts.prof')

@section('page_title', 'Tableau de bord')
@section('breadcrumb', 'Vue d\'ensemble')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>Tableau de bord Professeur</h1>
        <div class="subtitle">👋 Bienvenue {{ auth()->user()->name }}</div>
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
        <div class="stat-value">{{ $myDevoirsCount ?? 0 }}</div>
        <div class="stat-label">Mes Devoirs</div>
    </div>
    <div class="adm-stat orange">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
        </div>
        <div class="stat-value">{{ $assignmentsCount ?? 0 }}</div>
        <div class="stat-label">Total Devoirs</div>
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
                <div class="adm-activity-item">
                    <div class="adm-activity-dot" style="background:#4ADE80;"></div>
                    <div class="adm-activity-content">
                        <p><strong>Bienvenue</strong> sur votre espace professeur</p>
                        <div class="adm-activity-time">{{ now()->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
