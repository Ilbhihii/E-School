@extends('layouts.prof')

@section('title', 'Gestion des Devoirs')
@section('page_title', 'Devoirs')
@section('breadcrumb', 'Gestion des devoirs')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-file-earmark-check me-2" style="color:var(--adm-success);"></i> Gestion des Devoirs</h1>
        <div class="subtitle">Organisez et suivez les devoirs de vos classes</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.devoir.create') }}" class="adm-btn adm-btn-success">
            <i class="bi bi-plus-lg me-1"></i> Nouveau devoir
        </a>
    </div>
</div>

{{-- Stats Cards --}}
<div class="adm-stats-grid">
    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-file-earmark-check-fill"></i></div>
        </div>
        <div class="stat-value">{{ $devoirs->count() }}</div>
        <div class="stat-label">Total Devoirs</div>
    </div>
    <div class="adm-stat cyan">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="stat-value">{{ $devoirs->where('due_date', '>', now()->format('Y-m-d'))->count() }}</div>
        <div class="stat-label">À venir</div>
    </div>
    <div class="adm-stat orange">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-file-earmark-x-fill"></i></div>
        </div>
        <div class="stat-value">{{ $devoirs->where('due_date', '<=', now()->format('Y-m-d'))->count() }}</div>
        <div class="stat-label">Expirés</div>
    </div>
    <div class="adm-stat green" style="display:flex;align-items:center;justify-content:center;">
        <a href="{{ route('prof.devoir.create') }}" class="adm-btn adm-btn-success" style="padding:12px 28px;font-size:0.95rem;">
            <i class="bi bi-plus-circle me-2"></i> Nouveau devoir
        </a>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-grid-3x3-gap" style="color:rgba(255,255,255,0.35);"></i> Liste des devoirs</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $devoirs->count() }} devoir(s)</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        @forelse($devoirs as $devoir)
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Classe</th>
                        <th>Date limite</th>
                        <th>Fichier</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span style="font-weight:500;">{{ Str::limit($devoir->title, 40) }}</span></td>
                        <td><span class="adm-badge adm-badge-info">{{ $devoir->classRoom->name ?? '-' }}</span></td>
                        <td>
                            @php $isPast = $devoir->due_date <= now()->format('Y-m-d'); @endphp
                            <span class="adm-badge {{ $isPast ? 'adm-badge-danger' : 'adm-badge-success' }}">
                                {{ \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            @if($devoir->file)
                            <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="adm-btn adm-btn-success adm-btn-sm">
                                <i class="bi bi-download me-1"></i>
                            </a>
                            @else
                            <span style="color:var(--adm-text-muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('prof.devoir.edit', $devoir) }}" class="adm-btn adm-btn-warning adm-btn-sm" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('prof.devoir.destroy', $devoir) }}" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @if(isset($devoir) && method_exists($devoirs, 'links'))
        <div class="adm-card-footer">
            {{ $devoirs->appends(request()->query())->links() }}
        </div>
        @endif
        @empty
        <div class="adm-empty" style="padding:3rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-file-earmark-x"></i></div>
            <h5>Aucun devoir trouvé</h5>
            <p>Commencez par <a href="{{ route('prof.devoir.create') }}" style="color:var(--adm-success);font-weight:600;">créer votre premier devoir</a></p>
        </div>
        @endforelse
    </div>
</div>

@endsection
