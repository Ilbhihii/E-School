@extends('layouts.prof')

@section('title', 'Mes Cours')
@section('page_title', 'Cours')
@section('breadcrumb', 'Gestion des cours')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-book-half me-2" style="color:var(--adm-primary);"></i> Mes Cours</h1>
        <div class="subtitle">Gérez vos ressources pédagogiques</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.courses.create') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-plus-lg"></i> Nouveau cours
        </a>
    </div>
</div>

<div class="adm-stats-grid">
    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-book-fill"></i></div>
        </div>
        <div class="stat-value">{{ $courses->count() }}</div>
        <div class="stat-label">Total cours</div>
    </div>
    <div class="adm-stat cyan">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="stat-value">{{ $courses->where('created_at','>', now()->subDays(7))->count() }}</div>
        <div class="stat-label">Cours récents (7j)</div>
    </div>
    <div class="adm-stat blue" style="display:flex;align-items:center;justify-content:center;">
        <a href="{{ route('prof.courses.create') }}" class="adm-btn adm-btn-primary" style="padding:12px 28px;font-size:0.95rem;">
            <i class="bi bi-plus-circle me-2"></i> Nouveau cours
        </a>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-collection" style="color:rgba(255,255,255,0.35);"></i> Tous les cours</h4>
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
                        <th>matière</th>
                        <th>Date</th>
                        <th>Devoirs</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    <tr>
                        <td><span style="font-weight:500;">{{ Str::limit($course->title, 45) }}</span></td>
                        <td><span class="adm-badge adm-badge-info">{{ $course->level->name ?? '---' }}</span></td>
                        <td><span class="adm-badge adm-badge-primary">{{ $course->subject->name ?? '---' }}</span></td>
                        <td style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $course->created_at->format('d/m/Y') }}</td>
                        <td>
                            @forelse($course->assignments as $devoir)
                                <span class="adm-badge adm-badge-accent mb-1" style="font-size:0.7rem;">{{ Str::limit($devoir->title, 18) }}</span>
                            @empty
                                <span style="color:var(--adm-text-muted);font-size:0.8rem;">Aucun</span>
                            @endforelse
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('prof.devoir.create', ['course_id'=>$course->id]) }}" class="adm-btn adm-btn-success adm-btn-sm" title="Ajouter un devoir">
                                    <i class="bi bi-plus"></i>
                                </a>
                                <a href="{{ route('prof.courses.edit', $course->id) }}" class="adm-btn adm-btn-warning adm-btn-sm" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('prof.courses.destroy', $course->id) }}" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?')">
                                    @csrf @method('DELETE')
                                    <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit" title="Supprimer">
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
                                <a href="{{ route('prof.courses.create') }}" class="adm-btn adm-btn-primary adm-btn-sm">
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
