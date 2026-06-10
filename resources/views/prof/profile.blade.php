@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:800px">

        <!-- AVATAR HEADER -->
        <div class="admin-header" style="text-align:center;flex-direction:column;">
            <div style="width:120px;height:120px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 15px 40px rgba(99,102,241,0.3);">
                <i class="bi bi-person-circle" style="font-size:3.5rem;color:white;"></i>
            </div>
            <div>
                <h1 class="admin-header-title"><span class="gradient">Mon Profil Professeur</span></h1>
                <span class="adm-badge adm-badge-purple" style="margin-top:0.5rem;"><i class="bi bi-patch-check"></i> Rôle Vérifié</span>
            </div>
        </div>

        <!-- INFOS -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-body">
                <div class="adm-detail-row">
                    <div class="adm-detail-item">
                        <label><i class="bi bi-person-fill"></i> Nom Complet</label>
                        <div class="value">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="adm-detail-item">
                        <label><i class="bi bi-envelope-fill"></i> Email</label>
                        <div class="value" style="font-family:monospace;">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="adm-detail-item">
                        <label><i class="bi bi-geo-alt-fill"></i> Location</label>
                        <div class="value">{{ auth()->user()->location ?? 'Non spécifiée' }}</div>
                    </div>
                    <div class="adm-detail-item">
                        <label><i class="bi bi-calendar-check-fill"></i> Inscrit le</label>
                        <div class="value">{{ auth()->user()->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-grid adm-mb-3">
            <div class="stat-card purple adm-fade-up" style="text-align:center;display:block;">
                <div class="stat-card-icon purple" style="margin:0 auto 0.75rem;"><i class="bi bi-building"></i></div>
                <div class="stat-card-value">3</div>
                <div class="stat-card-label">Classes</div>
            </div>
            <div class="stat-card blue adm-fade-up" style="text-align:center;display:block;">
                <div class="stat-card-icon blue" style="margin:0 auto 0.75rem;"><i class="bi bi-people-fill"></i></div>
                <div class="stat-card-value">45</div>
                <div class="stat-card-label">Étudiants</div>
            </div>
            <div class="stat-card green adm-fade-up" style="text-align:center;display:block;">
                <div class="stat-card-icon green" style="margin:0 auto 0.75rem;"><i class="bi bi-book-fill"></i></div>
                <div class="stat-card-value">12</div>
                <div class="stat-card-label">Cours</div>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="adm-flex adm-gap-2" style="justify-content:center;">
            <a href="{{ route('prof.settings') }}" class="adm-btn adm-btn-primary adm-btn-lg">
                <i class="bi bi-gear-fill"></i> Modifier Profil
            </a>
            <a href="{{ route('prof.dashboard') }}" class="adm-btn adm-btn-ghost adm-btn-lg">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </div>

    </div>
</div>
@endsection
