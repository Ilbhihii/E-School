@extends('layouts.admin')

@section('title', 'Gestion des Cours')
@section('page_title', 'Cours')
@section('breadcrumb', 'Gestion des cours')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>Cours</h1>
        <div class="subtitle">Administration des contenus pédagogiques</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.courses.create') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-plus-lg"></i> Nouveau cours
        </a>
    </div>
</div>

<div class="adm-stats-grid">
    <div class="adm-stat blue">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-book-fill"></i></div>
        </div>
        <div class="stat-value">{{ $courses->count() }}</div>
        <div class="stat-label">Total cours</div>
    </div>
    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="stat-value">{{ $courses->where('created_at','>',now()->subDays(7))->count() }}</div>
        <div class="stat-label">Cours récents (7j)</div>
    </div>
    <div class="adm-stat purple">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-folder-fill"></i></div>
        </div>
        <div class="stat-value">{{ $courses->groupBy('class_id')->count() }}</div>
        <div class="stat-label">Classes concernées</div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-collection" style="color:rgba(255,255,255,0.35);"></i> Liste des cours</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $courses->count() }} cours</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Niveau</th>
                        <th>Classe</th>
                        <th>Matière</th>
                        <th>Date</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    <tr>
                        <td><span style="font-weight:500;">{{ $course->title }}</span></td>
                        <td><span class="adm-badge adm-badge-info">{{ $course->level->name ?? '—' }}</span></td>
                        <td><span class="adm-badge" style="background:rgba(6,182,212,0.12);color:#67E8F9;">{{ $course->classRoom->name ?? '—' }}</span></td>
                        <td><span class="adm-badge adm-badge-primary">{{ $course->subject->name ?? '—' }}</span></td>
                        <td style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $course->created_at->format('d/m/Y') }}</td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('admin.courses.show', $course->id) }}" class="adm-btn adm-btn-sm" style="background:rgba(6,182,212,0.15);color:#67E8F9;border:1px solid rgba(6,182,212,0.15);" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="adm-btn adm-btn-warning adm-btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('prof.devoir.create', $course->id) }}" class="adm-btn adm-btn-accent adm-btn-sm">
                                    <i class="bi bi-file-text"></i> Devoir
                                </a>
                                <a href="{{ route('admin.lives.create', $course->class_id) }}" class="adm-btn adm-btn-danger adm-btn-sm">
                                    <i class="bi bi-camera-video"></i> Live
                                </a>
                                <form method="POST" action="{{ route('admin.courses.destroy',$course->id) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce cours ?')">
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
                        <td colspan="6">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                                <h5>Aucun cours</h5>
                                <p>Créez votre premier cours pour commencer.</p>
                                <a href="{{ route('admin.courses.create') }}" class="adm-btn adm-btn-primary adm-btn-sm">
                                    <i class="bi bi-plus-lg"></i> Créer un cours
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($courses, 'links'))
    <div class="adm-card-footer">
        {{ $courses->links() }}
    </div>
    @endif
</div>
@endsection
