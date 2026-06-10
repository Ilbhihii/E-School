@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Gestion des Utilisateurs</span></h1>
                <p class="admin-header-subtitle">Tableau de bord complet des utilisateurs</p>
            </div>
            <div class="adm-flex adm-gap-2">
                <span class="adm-badge adm-badge-primary"><i class="bi bi-people-fill"></i> {{ $totalUsers }} total</span>
            </div>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card blue adm-fade-up">
                <div class="stat-card-icon blue"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $totalUsers }}</div>
                    <div class="stat-card-label">Total Utilisateurs</div>
                </div>
            </div>
            <div class="stat-card purple adm-fade-up">
                <div class="stat-card-icon purple"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-card-value">{{ $recentUsers->count() }}</div>
                    <div class="stat-card-label">Utilisateurs Récents</div>
                </div>
            </div>
            <div class="stat-card green adm-fade-up">
                <div class="stat-card-icon green"><i class="bi bi-lightning-fill"></i></div>
                <div>
                    <div class="stat-card-value">✨</div>
                    <div class="stat-card-label">Actions Disponibles</div>
                </div>
            </div>
        </div>

        <!-- RECENT USERS -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-header">
                <div>
                    <h3>Utilisateurs Récents</h3>
                    <p>Les 10 derniers inscrits</p>
                </div>
            </div>
            <div class="adm-card-body">
                <div class="adm-grid-4">
                    @forelse($recentUsers as $user)
                        <div class="adm-card" style="background:var(--adm-surface);border:1px solid var(--adm-border);backdrop-filter:none;">
                            <div class="adm-card-body" style="padding:1rem;">
                                <div class="adm-flex adm-gap-2 adm-mb-1">
                                    <div class="adm-avatar">{{ strtoupper(substr($user->name,0,2)) }}</div>
                                    <div>
                                        <div class="adm-user-cell-name">{{ $user->name }}</div>
                                        <div class="adm-user-cell-email">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="adm-flex adm-flex-between">
                                    <span class="adm-badge adm-badge-success">Nouveau</span>
                                    <span style="font-size:0.78rem;color:var(--adm-text-secondary)">{{ $user->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="adm-empty" style="grid-column:1/-1;">
                            <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                            <h3>Aucun utilisateur récent</h3>
                            <p>Les nouveaux utilisateurs apparaîtront ici</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- USERS TABLE -->
        <div class="adm-card">
            <div class="adm-card-header">
                <div>
                    <h3>Tous les Utilisateurs</h3>
                    <p>Vue complète avec actions</p>
                </div>
                <div class="adm-flex adm-gap-2">
                    <div class="adm-search-wrap" style="min-width:200px;">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" placeholder="Rechercher..." id="userSearch">
                    </div>
                </div>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th>Test</th>
                            <th>Activation</th>
                            <th>Inscrit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="adm-user-cell">
                                    <div class="adm-avatar">{{ strtoupper(substr($user->name,0,2)) }}</div>
                                    <div class="adm-user-cell-info">
                                        <div class="adm-user-cell-name">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="adm-user-cell-email">{{ $user->email }}</span></td>
                            <td>
                                @if($user->is_active)
                                    <span class="adm-badge adm-badge-success"><i class="bi bi-check-circle-fill"></i> Actif</span>
                                @else
                                    <span class="adm-badge adm-badge-danger"><i class="bi bi-x-circle-fill"></i> Inactif</span>
                                @endif
                            </td>
                            <td>
                                @php $bestResult = $user->results()->max('percentage') ?? 0; @endphp
                                @if($bestResult > 0)
                                    <span class="adm-badge {{ $bestResult >= 60 ? 'adm-badge-success' : ($bestResult >= 30 ? 'adm-badge-warning' : 'adm-badge-gray') }}">
                                        {{ round($bestResult) }}%
                                    </span>
                                @else
                                    <span class="adm-badge adm-badge-gray">⏳ Aucun test</span>
                                @endif
                            </td>
                            <td>
                                @if(!$user->is_active)
                                    <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="adm-btn adm-btn-success adm-btn-sm">
                                            <i class="bi bi-check-lg"></i> Activer
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm"
                                                onclick="return confirm('Désactiver ce compte ?')">
                                            <i class="bi bi-x-lg"></i> Désactiver
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td><span class="adm-badge adm-badge-primary">{{ $user->created_at->format('d/m/Y') }}</span></td>
                            <td>
                                <div class="adm-actions">
                                    <a href="{{ route('admin.users.test-results', $user->id) }}" class="adm-action-link adm-action-view">
                                        <i class="bi bi-bar-chart-fill"></i> Résultats
                                    </a>
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="adm-action-link adm-action-view">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
