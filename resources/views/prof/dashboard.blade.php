@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- WELCOME HEADER -->
        <div class="admin-gradient-header">
            <div class="text-center">
                <i class="bi bi-person-video3" style="font-size:2.5rem;margin-bottom:1rem;display:block;opacity:0.9;"></i>
                <h1>Dashboard Professeur</h1>
                <p style="font-size:1.1rem;">Bienvenue professeur <strong>{{ auth()->user()->name }}</strong></p>
                <p style="opacity:0.8;margin-top:0.5rem;">Gérez vos classes, devoirs et communications avec vos étudiants</p>
            </div>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card purple adm-fade-up">
                <div class="stat-card-icon purple"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $studentsCount ?? 0 }}</div>
                    <div class="stat-card-label">Étudiants</div>
                </div>
            </div>
            <div class="stat-card green adm-fade-up">
                <div class="stat-card-icon green"><i class="bi bi-building"></i></div>
                <div>
                    <div class="stat-card-value">{{ $classesCount ?? 0 }}</div>
                    <div class="stat-card-label">Classes</div>
                </div>
            </div>
            <div class="stat-card amber adm-fade-up">
                <div class="stat-card-icon amber"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $assignmentsCount ?? 0 }}</div>
                    <div class="stat-card-label">Devoirs</div>
                </div>
            </div>
            <div class="stat-card red adm-fade-up">
                <div class="stat-card-icon red"><i class="bi bi-calendar-x-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $absencesCount ?? 0 }}</div>
                    <div class="stat-card-label">Absences</div>
                </div>
            </div>
            <div class="stat-card blue adm-fade-up">
                <div class="stat-card-icon blue"><i class="bi bi-clipboard-check"></i></div>
                <div>
                    <div class="stat-card-value">{{ $testsCount ?? 0 }}</div>
                    <div class="stat-card-label">Tests</div>
                </div>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><i class="bi bi-lightning-fill"></i> Actions Rapides</h3>
            </div>
            <div class="adm-card-body">
                <div class="adm-grid-4">
                    <a href="{{ route('prof.absences') }}" class="adm-card" style="text-align:center;padding:1.5rem;text-decoration:none;border:1px solid var(--adm-border);backdrop-filter:none;">
                        <i class="bi bi-calendar-x" style="font-size:1.5rem;color:var(--adm-danger);margin-bottom:0.5rem;display:block;"></i>
                        <h5 style="font-weight:700;font-size:0.95rem;color:var(--adm-text);margin:0 0 0.25rem;">Absences</h5>
                        <p style="font-size:0.8rem;color:var(--adm-text-secondary);margin:0;">Marquer les présences</p>
                    </a>
                    <a href="{{ route('prof.assignments') }}" class="adm-card" style="text-align:center;padding:1.5rem;text-decoration:none;border:1px solid var(--adm-border);backdrop-filter:none;">
                        <i class="bi bi-file-earmark-text" style="font-size:1.5rem;color:var(--adm-primary);margin-bottom:0.5rem;display:block;"></i>
                        <h5 style="font-weight:700;font-size:0.95rem;color:var(--adm-text);margin:0 0 0.25rem;">Devoirs</h5>
                        <p style="font-size:0.8rem;color:var(--adm-text-secondary);margin:0;">Gérer les devoirs</p>
                    </a>
                    <a href="{{ route('prof.chat.subjects') }}" class="adm-card" style="text-align:center;padding:1.5rem;text-decoration:none;border:1px solid var(--adm-border);backdrop-filter:none;">
                        <i class="bi bi-chat-dots" style="font-size:1.5rem;color:var(--adm-success);margin-bottom:0.5rem;display:block;"></i>
                        <h5 style="font-weight:700;font-size:0.95rem;color:var(--adm-text);margin:0 0 0.25rem;">Messages</h5>
                        <p style="font-size:0.8rem;color:var(--adm-text-secondary);margin:0;">Répondre étudiants</p>
                    </a>
                    <a href="{{ route('prof.tests.index') }}" class="adm-card" style="text-align:center;padding:1.5rem;text-decoration:none;border:1px solid var(--adm-border);backdrop-filter:none;">
                        <i class="bi bi-clipboard-check" style="font-size:1.5rem;color:var(--adm-info);margin-bottom:0.5rem;display:block;"></i>
                        <h5 style="font-weight:700;font-size:0.95rem;color:var(--adm-text);margin:0 0 0.25rem;">Tests</h5>
                        <p style="font-size:0.8rem;color:var(--adm-text-secondary);margin:0;">Créer / Gérer tests</p>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
