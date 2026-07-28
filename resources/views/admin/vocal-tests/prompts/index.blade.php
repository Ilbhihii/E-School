@extends('layouts.admin')

@section('title', 'Tests vocaux — Textes')
@section('page_title', 'Tests vocaux')
@section('breadcrumb', 'Tests vocaux → Textes')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-mic-fill me-2" style="color:var(--adm-primary);"></i> Textes des tests vocaux</h1>
        <div class="subtitle">Gérez les textes à lire pour les tests vocaux (Arabe, Coran…)</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.vocal-tests.prompts.create') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-plus-lg"></i> Nouveau texte
        </a>
        <a href="{{ route('admin.vocal-tests.submissions.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-list-check me-1"></i> Voir les soumissions
        </a>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-file-text" style="color:rgba(255,255,255,0.35);"></i> Textes disponibles</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $prompts->total() }} texte(s)</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Matière</th>
                        <th>Niveau</th>
                        <th>Classe</th>
                        <th>Mode</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prompts as $prompt)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="adm-avatar" style="background:linear-gradient(135deg,#7C3AED,#2563EB);width:36px;height:36px;font-size:0.9rem;">
                                    <i class="bi bi-mic"></i>
                                </div>
                                <div>
                                    <strong style="font-size:0.9rem;">{{ $prompt->title }}</strong>
                                    <br>
                                    <small style="color:var(--adm-text-muted);font-size:0.75rem;">
                                        {{ Str::limit($prompt->reading_text, 60) }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td><span class="adm-badge adm-badge-primary">{{ $prompt->subject?->name ?? '-' }}</span></td>
                        <td style="color:var(--adm-text-muted);font-size:0.85rem;">{{ $prompt->level?->name ?? '-' }}</td>
                        <td style="color:var(--adm-text-muted);font-size:0.85rem;">{{ $prompt->classRoom?->name ?? '-' }}</td>
                        <td>
                            @if($prompt->test_mode)
                                <span class="adm-badge" style="background:{{ $prompt->test_mode === 'hifd' ? 'rgba(251,191,36,0.15)' : ($prompt->test_mode === 'tajwid' ? 'rgba(16,185,129,0.15)' : 'rgba(99,102,241,0.12)') }};color:{{ $prompt->test_mode === 'hifd' ? '#FCD34D' : ($prompt->test_mode === 'tajwid' ? '#6EE7B7' : '#A5B4FC') }};">
                                    {{ \App\Models\VocalTestPrompt::getModes()[$prompt->test_mode] ?? $prompt->test_mode }}
                                </span>
                            @else
                                <span style="color:var(--adm-text-muted);font-size:0.75rem;">Lecture</span>
                            @endif
                        </td>
                        <td><span class="adm-badge adm-badge-info">{{ $prompt->maximum_duration }}s</span></td>
                        <td>
                            @if($prompt->is_active)
                                <span class="adm-badge adm-badge-success">Actif</span>
                            @else
                                <span class="adm-badge adm-badge-danger">Inactif</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('admin.vocal-tests.prompts.edit', $prompt) }}" class="adm-btn adm-btn-warning adm-btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.vocal-tests.prompts.destroy', $prompt) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce texte ? Les soumissions associées ne seront pas supprimées.')">
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
                        <td colspan="8">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-mic-mute"></i></div>
                                <h5>Aucun texte de test vocal</h5>
                                <p>Créez votre premier texte pour les tests vocaux.</p>
                                <a href="{{ route('admin.vocal-tests.prompts.create') }}" class="adm-btn adm-btn-primary adm-btn-sm">
                                    <i class="bi bi-plus-lg"></i> Nouveau texte
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($prompts->hasPages())
    <div class="adm-card-footer">
        {{ $prompts->links() }}
    </div>
    @endif
</div>

@endsection
