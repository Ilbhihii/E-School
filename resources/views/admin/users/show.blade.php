@extends('layouts.admin')

@section('title', 'Détails utilisateur')
@section('page_title', 'Profil étudiant')
@section('breadcrumb', 'Détails utilisateur')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Header -->
        <div class="adm-card mb-4">
            <div class="adm-card-body" style="text-align:center;padding:2rem;">
                <div class="adm-avatar" style="width:80px;height:80px;font-size:1.8rem;margin:0 auto 1rem;background:var(--adm-gradient-primary);border-radius:50%;">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <h3 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.25rem;">{{ $user->name }}</h3>
                <div style="color:var(--adm-text-secondary);margin-bottom:0.75rem;">{{ $user->email }}</div>
                <div style="display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;">
                    @if($user->is_active)
                        <span class="adm-badge adm-badge-success">✅ Actif</span>
                    @else
                        <span class="adm-badge adm-badge-danger">❌ Inactif</span>
                    @endif
                    @if($user->test_passed)
                        <span class="adm-badge adm-badge-success">🎓 Test Validé</span>
                    @else
                        <span class="adm-badge adm-badge-warning">⏳ Test en attente</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Profile Details -->
            <div class="col-lg-8">
                <div class="adm-card">
                    <div class="adm-card-header">
                        <h4><i class="bi bi-info-circle" style="color:rgba(255,255,255,0.35);"></i> Détails du profil</h4>
                    </div>
                    <div class="adm-card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">ID</label>
                                    <div style="font-family:monospace;font-weight:600;color:var(--adm-text-muted);">#{{ $user->id }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Rôle</label>
                                    <span class="adm-badge adm-badge-primary">Étudiant</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Niveau</label>
                                    @if($user->classRoom && $user->classRoom->level)
                                        <a href="{{ route('admin.levels.edit', $user->classRoom->level) }}" class="adm-badge adm-badge-success" style="text-decoration:none;">
                                            {{ $user->classRoom->level->name }}
                                        </a>
                                    @else
                                        <span class="adm-badge" style="background:rgba(255,255,255,0.06);color:var(--adm-text-muted);">Non assigné</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Abonné</label>
                                    <span class="adm-badge {{ $user->is_paid ? 'adm-badge-success' : '' }}" style="{{ !$user->is_paid ? 'background:rgba(255,255,255,0.06);color:var(--adm-text-muted);' : '' }}">
                                        {{ $user->is_paid ? '✅ Oui' : '❌ Non' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Inscrit le</label>
                                    <div style="color:var(--adm-text-secondary);">{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Dernière connexion</label>
                                    <div style="color:var(--adm-text-secondary);">{{ $user->last_login ? $user->last_login->format('d/m/Y à H:i') : 'Jamais' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="col-lg-4">
                <div class="adm-card">
                    <div class="adm-card-header">
                        <h4><i class="bi bi-bar-chart" style="color:rgba(255,255,255,0.35);"></i> Statistiques</h4>
                    </div>
                    <div class="adm-card-body text-center">
                        <div style="margin-bottom:1.25rem;">
                            <div style="font-family:'Poppins',sans-serif;font-weight:800;font-size:2rem;color:rgba(255,255,255,0.9);">{{ $testsCount ?? 0 }}</div>
                            <div style="color:var(--adm-text-muted);font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;">Tests passés</div>
                        </div>
                        <div style="margin-bottom:1.25rem;">
                            <div style="font-family:'Poppins',sans-serif;font-weight:800;font-size:2rem;color:{{ ($avgScore ?? 0) >= 60 ? '#4ADE80' : '#FCD34D' }};">{{ round($avgScore ?? 0) }}%</div>
                            <div style="color:var(--adm-text-muted);font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;">Moyenne générale</div>
                        </div>
                        <div style="height:6px;border-radius:3px;background:rgba(255,255,255,0.06);overflow:hidden;">
                            <div style="height:100%;border-radius:3px;width:{{ ($avgScore ?? 0) }}%;background:linear-gradient(135deg,#16A34A,#22C55E);transition:width 0.6s ease;"></div>
                        </div>
                        <div style="color:var(--adm-text-muted);font-size:0.7rem;margin-top:4px;">{{ round($avgScore ?? 0) }}% de réussite</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="adm-card mt-4">
            <div class="adm-card-header">
                <h4><i class="bi bi-lightning" style="color:#FFD166;"></i> Actions rapides</h4>
            </div>
            <div class="adm-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <form action="{{ route('admin.users.activate', $user->id) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="adm-btn adm-btn-success w-100 {{ $user->is_active ? 'adm-btn-ghost' : '' }}" {{ $user->is_active ? 'disabled' : '' }}>
                                <i class="bi bi-check-circle"></i> Activer
                            </button>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" onsubmit="return confirm('Désactiver ce compte?')">
                            @csrf @method('PUT')
                            <button type="submit" class="adm-btn adm-btn-danger w-100 {{ !$user->is_active ? 'adm-btn-ghost' : '' }}" {{ !$user->is_active ? 'disabled' : '' }}>
                                <i class="bi bi-x-circle"></i> Désactiver
                            </button>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.users.test-results', $user) }}" class="adm-btn adm-btn-accent w-100 text-center" style="justify-content:center;">
                            <i class="bi bi-bar-chart"></i> Tests
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.users.index') }}" class="adm-btn adm-btn-ghost w-100 text-center" style="justify-content:center;">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
