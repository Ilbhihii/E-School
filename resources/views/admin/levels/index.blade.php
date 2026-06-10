@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- GRADIENT HEADER -->
        <div class="admin-gradient-header">
            <div class="adm-flex adm-flex-between adm-flex-wrap" style="gap:1rem;">
                <div>
                    <h1>Gestion des Niveaux</h1>
                    <p>Ajoutez, modifiez ou supprimez les niveaux et leurs matières en un seul endroit.</p>
                </div>
                <a href="{{ route('admin.levels.create') }}" class="adm-btn adm-btn-primary">
                    <i class="bi bi-plus-circle"></i> Nouveau niveau
                </a>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="stats-grid">
            <div class="stat-card blue adm-fade-up">
                <div class="stat-card-icon blue"><i class="bi bi-layers-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $levels->count() }}</div>
                    <div class="stat-card-label">Total Niveaux</div>
                </div>
            </div>
            <div class="stat-card green adm-fade-up">
                <div class="stat-card-icon green"><i class="bi bi-book-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $subjects->count() }}</div>
                    <div class="stat-card-label">Matières associées</div>
                </div>
            </div>
            <div class="stat-card amber adm-fade-up">
                <div class="stat-card-icon amber"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $levels->whereNotNull('subject_id')->count() }}</div>
                    <div class="stat-card-label">Niveaux actifs</div>
                </div>
            </div>
            <div class="stat-card red adm-fade-up">
                <div class="stat-card-icon red"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-card-value" style="font-size:0.95rem;">Aujourd'hui</div>
                    <div class="stat-card-label">Dernière mise à jour</div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="adm-card">
            <div class="adm-card-header">
                <div>
                    <h3>Liste des Niveaux</h3>
                    <p>Tous les niveaux enregistrés avec leur matière.</p>
                </div>
                <span class="adm-badge adm-badge-primary">{{ $levels->count() }} niveaux</span>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Niveau</th>
                            <th>Matière</th>
                            <th>Date de création</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($levels as $level)
                        <tr>
                            <td><span style="color:var(--adm-text-secondary);font-size:0.85rem;">{{ $loop->iteration }}</span></td>
                            <td style="font-weight:600;">{{ $level->name }}</td>
                            <td>
                                <span class="adm-badge adm-badge-purple">{{ $level->subject->name ?? 'Non défini' }}</span>
                            </td>
                            <td><span style="color:var(--adm-text-secondary);font-size:0.85rem;">{{ optional($level->created_at)->format('d/m/Y à H:i') ?? '-' }}</span></td>
                            <td style="text-align:right;">
                                <div class="adm-actions" style="justify-content:flex-end;">
                                    <a href="{{ route('admin.levels.edit', $level) }}" class="adm-action-link adm-action-edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.levels.destroy', $level) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce niveau ?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-action-link adm-action-delete" style="border:none;cursor:pointer;">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="adm-empty">Aucun niveau trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
