@extends('layouts.admin')

@section('title', 'Gestion des Devoirs')
@section('page_title', 'Devoirs')
@section('breadcrumb', 'Gestion des devoirs')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>Devoirs</h1>
        <div class="subtitle">Gestion des devoirs et exercices</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.devoir.create', ['course_id' => $course_id ?? '']) }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-plus-lg"></i> Nouveau devoir
        </a>
    </div>
</div>

@if($course ?? false)
<div class="adm-card mb-4" style="background:rgba(0,58,143,0.08);border-color:rgba(0,58,143,0.15);">
    <div class="adm-card-body" style="padding:1.25rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <strong style="color:rgba(255,255,255,0.85);">Devoirs du cours:</strong>
            <span style="color:var(--adm-text-secondary);margin-left:8px;">{{ $course->title }}</span>
        </div>
        <a href="{{ route('admin.devoirs.index') }}" class="adm-btn adm-btn-ghost adm-btn-sm">
            <i class="bi bi-x"></i> Voir tout
        </a>
    </div>
</div>
@endif

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-file-text" style="color:rgba(255,255,255,0.35);"></i> Liste des devoirs</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $devoirs->count() }} devoir(s)</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Classe</th>
                        <th>Date limite</th>
                        <th>Fichier</th>
                        <th>Professeur</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devoirs as $devoir)
                    <tr>
                        <td><span style="font-weight:500;">{{ Str::limit($devoir->title, 50) }}</span></td>
                        <td><span class="adm-badge adm-badge-info">{{ $devoir->classRoom->name ?? '---' }}</span></td>
                        <td>
                            <span class="adm-badge {{ $devoir->due_date < now()->format('Y-m-d') ? 'adm-badge-danger' : 'adm-badge-success' }}">
                                {{ \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            @if($devoir->file)
                                <a href="{{ Storage::url($devoir->file) }}" target="_blank" class="adm-btn adm-btn-ghost adm-btn-sm">
                                    <i class="bi bi-paperclip"></i> Fichier
                                </a>
                            @else
                                <span style="color:var(--adm-text-muted);">Aucun</span>
                            @endif
                        </td>
                        <td style="color:var(--adm-text-secondary);">{{ $devoir->user->name ?? 'Admin' }}</td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('admin.devoirs.edit', $devoir) }}" class="adm-btn adm-btn-warning adm-btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.devoirs.destroy', $devoir) }}" style="display:inline;" onsubmit="return confirm('Confirmer ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-file-text"></i></div>
                                <h5>Aucun devoir</h5>
                                <p>Aucun devoir trouvé pour ce cours.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($devoirs, 'links'))
    <div class="adm-card-footer">
        {{ $devoirs->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
