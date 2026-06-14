@extends('layouts.admin')

@section('title', 'Gestion des Classes')
@section('page_title', 'Classes')
@section('breadcrumb', 'Gestion des classes')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>Classes</h1>
        <div class="subtitle">Gérez les classes de votre plateforme éducative</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.classes.create') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-plus-lg"></i> Nouvelle classe
        </a>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success">
    <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-mortarboard" style="color:rgba(255,255,255,0.35);"></i> Liste des classes</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $classes->count() }} classe(s)</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Classe</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="adm-avatar" style="background:linear-gradient(135deg,#16A34A,#22C55E);">
                                    <i class="bi bi-mortarboard" style="font-size:1rem;"></i>
                                </div>
                                <span style="font-weight:500;">{{ $class->name }}</span>
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('admin.classes.edit', $class->id) }}" class="adm-btn adm-btn-warning adm-btn-sm">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                                <form method="POST" action="{{ route('admin.classes.destroy', $class->id) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette classe ?')">
                                    @csrf @method('DELETE')
                                    <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                                <h5>Aucune classe</h5>
                                <p>Créez votre première classe pour commencer.</p>
                                <a href="{{ route('admin.classes.create') }}" class="adm-btn adm-btn-primary adm-btn-sm">
                                    <i class="bi bi-plus-lg"></i> Créer une classe
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
