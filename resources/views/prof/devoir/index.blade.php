@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📚 Gestion des Devoirs</span></h1>
                <p class="admin-header-subtitle">Organisez et suivez les devoirs de vos classes</p>
            </div>
            <a href="{{ route('prof.devoir.create') }}" class="adm-btn adm-btn-success">
                <i class="bi bi-plus-lg"></i> Nouveau Devoir
            </a>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card green adm-fade-up">
                <div class="stat-card-icon green"><i class="bi bi-file-earmark-check"></i></div>
                <div>
                    <div class="stat-card-value">{{ $devoirs->count() }}</div>
                    <div class="stat-card-label">Total Devoirs</div>
                </div>
            </div>
            <div class="stat-card blue adm-fade-up">
                <div class="stat-card-icon blue"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-card-value">{{ $devoirs->where('due_date', '>', now()->format('Y-m-d'))->count() }}</div>
                    <div class="stat-card-label">À Venir</div>
                </div>
            </div>
            <div class="stat-card red adm-fade-up">
                <div class="stat-card-icon red"><i class="bi bi-file-earmark-x"></i></div>
                <div>
                    <div class="stat-card-value">{{ $devoirs->where('due_date', '<=', now()->format('Y-m-d'))->count() }}</div>
                    <div class="stat-card-label">Expirés</div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="adm-card">
            <div class="adm-card-header" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;border-radius:var(--adm-radius-lg) var(--adm-radius-lg) 0 0;">
                <h3 style="color:white;"><i class="bi bi-grid-3x3-gap"></i> Liste des Devoirs</h3>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Classe</th>
                            <th>Date limite</th>
                            <th>Fichier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devoirs as $devoir)
                        <tr>
                            <td><span style="font-weight:600;">{{ Str::limit($devoir->title, 40) }}</span></td>
                            <td><span class="adm-badge adm-badge-primary">{{ $devoir->classRoom->name ?? '-' }}</span></td>
                            <td>
                                @php $isPast = $devoir->due_date <= now()->format('Y-m-d'); @endphp
                                <span class="adm-badge {{ $isPast ? 'adm-badge-danger' : 'adm-badge-success' }}">
                                    {{ \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                @if($devoir->file)
                                <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="adm-btn adm-btn-sm adm-btn-ghost">
                                    <i class="bi bi-download"></i>
                                </a>
                                @else
                                <span class="adm-badge adm-badge-gray">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="adm-actions">
                                    <a href="{{ route('prof.devoir.edit', $devoir) }}" class="adm-action-link adm-action-edit"><i class="bi bi-pencil-fill"></i></a>
                                    <form method="POST" action="{{ route('prof.devoir.destroy', $devoir) }}" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-action-link adm-action-delete" style="border:none;cursor:pointer;"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                                <h3>Aucun devoir trouvé</h3>
                                <p>Commencez par ajouter votre premier devoir</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($devoirs, 'links'))
            <div class="adm-card-footer">
                <div class="adm-pagination">{{ $devoirs->appends(request()->query())->links() }}</div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
