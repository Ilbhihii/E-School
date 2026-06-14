@extends('layouts.prof')

@section('title', 'Mon Profil')
@section('page_title', 'Mon Profil')
@section('breadcrumb', 'Profil enseignant')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-person-circle me-2" style="color:var(--adm-accent);"></i> Mon Profil Professeur</h1>
        <div class="subtitle">Vos informations personnelles et statistiques</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.settings') }}" class="adm-btn adm-btn-accent">
            <i class="bi bi-gear me-1"></i> Modifier le profil
        </a>
    </div>
</div>

<!-- Hero Profile Card -->
<div class="adm-card mb-4" style="background:linear-gradient(135deg,rgba(124,58,237,0.15),rgba(167,139,250,0.05));border-color:rgba(124,58,237,0.1);overflow:visible;">
    <div class="adm-card-body" style="padding:2.5rem;">
        <div class="row align-items-center g-4">
            <div class="col-lg-3 col-md-4 text-center">
                <div class="prof-avatar-large">
                    {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 2)) }}
                </div>
                <span class="adm-badge adm-badge-accent mt-3" style="font-size:0.75rem;padding:5px 16px;">
                    <i class="bi bi-patch-check-fill me-1"></i> Professeur vérifié
                </span>
            </div>
            <div class="col-lg-5 col-md-8">
                <h3 style="font-weight:700;color:rgba(255,255,255,0.95);margin-bottom:0.25rem;">{{ auth()->user()->name }}</h3>
                <p style="color:var(--adm-text-muted);font-size:0.9rem;margin-bottom:1rem;">{{ auth()->user()->email }}</p>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <div style="display:flex;align-items:center;gap:6px;padding:6px 14px;background:rgba(255,255,255,0.04);border-radius:8px;font-size:0.8rem;color:rgba(255,255,255,0.6);">
                        <i class="bi bi-calendar3" style="color:#60A5FA;"></i> Inscrit le {{ auth()->user()->created_at->format('d/m/Y') }}
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;padding:6px 14px;background:rgba(255,255,255,0.04);border-radius:8px;font-size:0.8rem;color:rgba(255,255,255,0.6);">
                        <i class="bi bi-geo-alt" style="color:#FCD34D;"></i> {{ auth()->user()->location ?? 'Non spécifiée' }}
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-flex gap-3 justify-content-lg-end">
                    <a href="{{ route('prof.settings') }}" class="adm-btn adm-btn-accent">
                        <i class="bi bi-pencil me-1"></i> Modifier
                    </a>
                    <a href="{{ route('prof.dashboard') }}" class="adm-btn adm-btn-ghost">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats + Info Grid -->
<div class="row g-4">
    <!-- Stats Cards -->
    <div class="col-lg-4">
        <div class="adm-card h-100">
            <div class="adm-card-header">
                <h4><i class="bi bi-bar-chart-fill" style="color:#4ADE80;"></i> Statistiques</h4>
            </div>
            <div class="adm-card-body">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center justify-content-between p-3" style="background:rgba(96,165,250,0.08);border-radius:12px;border:1px solid rgba(96,165,250,0.1);">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:42px;height:42px;border-radius:10px;background:rgba(96,165,250,0.15);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#60A5FA;">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;color:rgba(255,255,255,0.85);">Classes</div>
                                <div style="font-size:0.75rem;color:var(--adm-text-muted);">Assignées</div>
                            </div>
                        </div>
                        <span style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.5rem;color:#60A5FA;">—</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3" style="background:rgba(167,139,250,0.08);border-radius:12px;border:1px solid rgba(167,139,250,0.1);">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:42px;height:42px;border-radius:10px;background:rgba(167,139,250,0.15);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#A78BFA;">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;color:rgba(255,255,255,0.85);">Étudiants</div>
                                <div style="font-size:0.75rem;color:var(--adm-text-muted);">Sous votre responsabilité</div>
                            </div>
                        </div>
                        <span style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.5rem;color:#A78BFA;">—</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3" style="background:rgba(74,222,128,0.08);border-radius:12px;border:1px solid rgba(74,222,128,0.1);">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:42px;height:42px;border-radius:10px;background:rgba(74,222,128,0.15);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#4ADE80;">
                                <i class="bi bi-book"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;color:rgba(255,255,255,0.85);">Cours</div>
                                <div style="font-size:0.75rem;color:var(--adm-text-muted);">Créés</div>
                            </div>
                        </div>
                        <span style="font-family:'Poppins',sans-serif;font-weight:800;font-size:1.5rem;color:#4ADE80;">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Info -->
    <div class="col-lg-8">
        <div class="adm-card h-100">
            <div class="adm-card-header">
                <h4><i class="bi bi-info-circle" style="color:var(--adm-text-muted);"></i> Informations personnelles</h4>
            </div>
            <div class="adm-card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-field">
                            <label class="info-label">Nom complet</label>
                            <div class="info-value">{{ auth()->user()->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-field">
                            <label class="info-label">Adresse Email</label>
                            <div class="info-value">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-field">
                            <label class="info-label">Rôle</label>
                            <div class="info-value">
                                <span class="adm-badge adm-badge-accent">
                                    <i class="bi bi-person-badge me-1"></i> Professeur
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-field">
                            <label class="info-label">Membre depuis</label>
                            <div class="info-value">{{ auth()->user()->created_at->format('d F Y') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-field">
                            <label class="info-label">Localisation</label>
                            <div class="info-value">{{ auth()->user()->location ?? 'Non spécifiée' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-field">
                            <label class="info-label">Statut du compte</label>
                            <div class="info-value">
                                <span class="adm-badge adm-badge-success">
                                    <i class="bi bi-check-circle-fill me-1"></i> Actif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.prof-avatar-large {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7C3AED, #A78BFA);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-family: 'Poppins', sans-serif;
    font-weight: 800;
    font-size: 2.2rem;
    margin: 0 auto;
    box-shadow: 0 15px 40px rgba(124, 58, 237, 0.35);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
}
.prof-avatar-large::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    border: 2px solid rgba(167, 139, 250, 0.2);
}
.prof-avatar-large:hover {
    transform: scale(1.05) rotate(-4deg);
    box-shadow: 0 20px 50px rgba(124, 58, 237, 0.45);
}

.info-field {
    padding: 1rem 1.25rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    transition: all 0.3s ease;
}
.info-field:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.08);
}
.info-label {
    display: block;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--adm-text-muted);
    margin-bottom: 4px;
    font-weight: 600;
}
.info-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.85);
}
</style>

@endsection
