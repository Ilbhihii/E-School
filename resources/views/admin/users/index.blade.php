@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')
@section('page_title', 'Utilisateurs')
@section('breadcrumb', 'Gestion des utilisateurs')

@section('content')

<!-- Stats -->
<div class="adm-stats-grid">
    <div class="adm-stat blue">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        </div>
        <div class="stat-value">{{ $totalUsers ?? $users->total() ?? $users->count() }}</div>
        <div class="stat-label">Total utilisateurs</div>
    </div>
    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
        </div>
        <div class="stat-value">{{ $users->where('is_active', true)->count() ?? 0 }}</div>
        <div class="stat-label">Comptes actifs</div>
    </div>
    <div class="adm-stat orange">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-person-x-fill"></i></div>
        </div>
        <div class="stat-value">{{ $users->where('is_active', false)->count() ?? 0 }}</div>
        <div class="stat-label">Comptes inactifs</div>
    </div>
    <div class="adm-stat purple">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-person-plus-fill"></i></div>
        </div>
        <div class="stat-value">{{ $recentUsers->count() ?? 0 }}</div>
        <div class="stat-label">Nouveaux (30j)</div>
    </div>
</div>

<!-- Users table -->
<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-people" style="color:rgba(255,255,255,0.35);"></i> Tous les utilisateurs</h4>
        <div class="card-actions">
            <input type="text" placeholder="Rechercher..." class="adm-form-control" style="width:200px;padding:7px 12px;font-size:0.8rem;">
        </div>
    </div>
    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Test</th>
                        <th>Inscrit</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="adm-avatar" style="background:linear-gradient(135deg,#003A8F,#2563EB);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span style="font-weight:500;">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td style="color:var(--adm-text-secondary);">{{ $user->email }}</td>
                        <td>
                            <span class="adm-badge {{ $user->role == 'admin' ? 'adm-badge-primary' : ($user->role == 'prof' ? 'adm-badge-warning' : 'adm-badge-info') }}">
                                {{ ucfirst($user->role ?? 'student') }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="adm-badge adm-badge-success"><i class="bi bi-check-circle-fill" style="font-size:0.6rem;"></i> Actif</span>
                            @else
                                <span class="adm-badge adm-badge-danger"><i class="bi bi-x-circle-fill" style="font-size:0.6rem;"></i> Inactif</span>
                            @endif
                        </td>
                        <td>
                            @php $bestResult = $user->results()->max('percentage') ?? 0; @endphp
                            @if($bestResult > 0)
                                <span class="adm-badge {{ $bestResult >= 60 ? 'adm-badge-success' : ($bestResult >= 30 ? 'adm-badge-warning' : 'adm-badge-danger') }}">
                                    {{ round($bestResult) }}%
                                </span>
                            @else
                                <span class="adm-badge" style="background:rgba(255,255,255,0.05);color:var(--adm-text-muted);">—</span>
                            @endif
                        </td>
                        <td style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('admin.users.test-results', $user->id) }}" class="adm-btn adm-btn-ghost adm-btn-sm" title="Résultats">
                                    <i class="bi bi-bar-chart"></i>
                                </a>
                                @if(!$user->is_active)
                                <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('PUT')
                                    <button class="adm-btn adm-btn-success adm-btn-sm" type="submit" title="Activer">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Désactiver ce compte ?')">
                                    @csrf @method('PUT')
                                    <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit" title="Désactiver">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                                <h5>Aucun utilisateur</h5>
                                <p>Les utilisateurs inscrits apparaîtront ici.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($users, 'links'))
    <div class="adm-card-footer">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Recent Users Grid -->
@if(isset($recentUsers) && $recentUsers->count() > 0)
<div class="adm-card mt-4">
    <div class="adm-card-header">
        <h4><i class="bi bi-clock-history" style="color:rgba(255,255,255,0.35);"></i> Inscriptions récentes</h4>
    </div>
    <div class="adm-card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;">
            @foreach($recentUsers as $user)
            <div style="display:flex;align-items:center;gap:12px;padding:12px;border-radius:12px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.04);transition:all 0.2s ease;" onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                <div class="adm-avatar adm-avatar-sm" style="background:linear-gradient(135deg,#7C3AED,#A78BFA);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $user->name }}</div>
                    <div style="font-size:0.72rem;color:var(--adm-text-muted);">{{ $user->created_at->diffForHumans() }}</div>
                </div>
                <span class="adm-badge adm-badge-success" style="font-size:0.65rem;">Nouveau</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif


@endsection
