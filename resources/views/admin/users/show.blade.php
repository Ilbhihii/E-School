@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:900px">

        <!-- GRADIENT HEADER -->
        <div class="admin-gradient-header">
            <div class="adm-flex adm-flex-between adm-flex-wrap" style="gap:1rem;">
                <div class="adm-flex adm-gap-3">
                    <div class="adm-avatar adm-avatar-xl" style="border:3px solid rgba(255,255,255,0.3);">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h1>{{ $user->name }}</h1>
                        <p>{{ $user->email }}</p>
                    </div>
                </div>
                <div class="adm-flex adm-gap-2">
                    @if($user->is_active)
                        <span class="adm-badge" style="background:rgba(255,255,255,0.2);color:white;border:none;"><i class="bi bi-check-circle-fill"></i> Actif</span>
                    @else
                        <span class="adm-badge" style="background:rgba(239,68,68,0.3);color:#fca5a5;border:none;"><i class="bi bi-x-circle-fill"></i> Inactif</span>
                    @endif
                    @if($user->test_passed)
                        <span class="adm-badge" style="background:rgba(34,197,94,0.3);color:#86efac;border:none;"><i class="bi bi-award-fill"></i> Test Validé</span>
                    @else
                        <span class="adm-badge" style="background:rgba(251,191,36,0.3);color:#fde68a;border:none;"><i class="bi bi-hourglass"></i> Test en attente</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="adm-grid-2" style="grid-template-columns:2fr 1fr;">
            <!-- DETAILS -->
            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><i class="bi bi-person-vcard-fill"></i> Détails du profil</h3>
                </div>
                <div class="adm-card-body">
                    <div class="adm-detail-row">
                        <div class="adm-detail-item">
                            <label>ID</label>
                            <div class="value">#{{ $user->id }}</div>
                        </div>
                        <div class="adm-detail-item">
                            <label>Rôle</label>
                            <div class="value"><span class="adm-badge adm-badge-primary">Étudiant</span></div>
                        </div>
                        <div class="adm-detail-item">
                            <label>Niveau</label>
                            <div class="value">
                                @if($user->classRoom && $user->classRoom->level)
                                    <a href="{{ route('admin.levels.edit', $user->classRoom->level) }}" class="adm-badge adm-badge-purple">
                                        {{ $user->classRoom->level->name }}
                                    </a>
                                @else
                                    <span class="adm-badge adm-badge-gray">Non assigné</span>
                                @endif
                            </div>
                        </div>
                        <div class="adm-detail-item">
                            <label>Abonné</label>
                            <div class="value">
                                <span class="adm-badge {{ $user->is_paid ? 'adm-badge-success' : 'adm-badge-gray' }}">
                                    {{ $user->is_paid ? '✅ Oui' : '❌ Non' }}
                                </span>
                            </div>
                        </div>
                        <div class="adm-detail-item">
                            <label>Inscrit le</label>
                            <div class="value">{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                        <div class="adm-detail-item">
                            <label>Dernière connexion</label>
                            <div class="value">{{ $user->last_login ? $user->last_login->format('d/m/Y à H:i') : 'Jamais' }}</div>
                        </div>
                    </div>
                    <div class="adm-flex adm-gap-2 adm-mt-3">
                        <a href="{{ route('admin.users.test-results', $user) }}" class="adm-btn adm-btn-primary">
                            <i class="bi bi-bar-chart-fill"></i> Voir tests
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="adm-btn adm-btn-warning">
                            <i class="bi bi-pencil-fill"></i> Modifier
                        </a>
                    </div>
                </div>
            </div>

            <!-- STATS -->
            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><i class="bi bi-graph-up-arrow"></i> Statistiques</h3>
                </div>
                <div class="adm-card-body" style="text-align:center;">
                    <div class="adm-mb-3">
                        <div style="font-size:2.5rem;font-weight:800;color:var(--adm-primary);">{{ $testsCount ?? 0 }}</div>
                        <div style="font-size:0.8rem;color:var(--adm-text-secondary);text-transform:uppercase;letter-spacing:0.04em;font-weight:600;">Tests passés</div>
                    </div>
                    <div class="adm-mb-3">
                        <div style="font-size:2.5rem;font-weight:800;{{ ($avgScore ?? 0) >= 60 ? 'color:#16a34a' : 'color:#d97706' }}">{{ round($avgScore ?? 0) }}%</div>
                        <div style="font-size:0.8rem;color:var(--adm-text-secondary);text-transform:uppercase;letter-spacing:0.04em;font-weight:600;">Moyenne générale</div>
                    </div>
                    <div class="adm-progress" style="height:10px;">
                        <div class="adm-progress-bar {{ ($avgScore ?? 0) >= 60 ? 'success' : 'warning' }}" style="width:{{ ($avgScore ?? 0) }}%"></div>
                    </div>
                    <div style="font-size:0.78rem;color:var(--adm-text-secondary);margin-top:0.3rem;">{{ round($avgScore ?? 0) }}% de réussite</div>
                </div>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="adm-card adm-mt-3">
            <div class="adm-card-header">
                <h3><i class="bi bi-lightning-fill"></i> Actions rapides</h3>
            </div>
            <div class="adm-card-body">
                <div class="adm-grid-4">
                    <form action="{{ route('admin.users.activate', $user->id) }}" method="POST">
                        @csrf @method('PUT')
                        <button type="submit" class="adm-btn adm-btn-success" style="width:100%;{{ $user->is_active ? 'opacity:0.5;cursor:not-allowed;' : '' }}" {{ $user->is_active ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle-fill"></i> Activer compte
                        </button>
                    </form>
                    <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST">
                        @csrf @method('PUT')
                        <button type="submit" class="adm-btn adm-btn-danger" style="width:100%;{{ !$user->is_active ? 'opacity:0.5;cursor:not-allowed;' : '' }}" {{ !$user->is_active ? 'disabled' : '' }} onclick="return confirm('Désactiver ce compte?')">
                            <i class="bi bi-x-circle-fill"></i> Désactiver
                        </button>
                    </form>
                    <a href="{{ route('admin.users.without-class') }}" class="adm-btn adm-btn-primary" style="width:100%;text-align:center;">
                        <i class="bi bi-people-fill"></i> Gérer classes
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="adm-btn adm-btn-ghost" style="width:100%;text-align:center;">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
