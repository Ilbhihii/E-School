@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📘 Gestion des Cours</span></h1>
                <p class="admin-header-subtitle">Administration des contenus pédagogiques</p>
            </div>
            <a href="{{ route('admin.courses.create') }}" class="adm-btn adm-btn-primary">
                <i class="bi bi-plus-lg"></i> Ajouter cours
            </a>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card green adm-fade-up">
                <div class="stat-card-icon green"><i class="bi bi-book-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $courses->count() }}</div>
                    <div class="stat-card-label">Total cours</div>
                </div>
            </div>
            <div class="stat-card blue adm-fade-up">
                <div class="stat-card-icon blue"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-card-value">{{ $courses->where('created_at','>',now()->subDays(7))->count() }}</div>
                    <div class="stat-card-label">Cours récents (7j)</div>
                </div>
            </div>
            <div class="stat-card purple adm-fade-up">
                <div class="stat-card-icon purple"><i class="bi bi-bar-chart-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $courses->where('created_at','>',now()->subDays(30))->count() }}</div>
                    <div class="stat-card-label">Cours du mois</div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="adm-card">
            <div class="adm-card-header">
                <div>
                    <h3>Liste des cours</h3>
                    <p>Tous les cours enregistrés</p>
                </div>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Classe</th>
                            <th>Matière</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                        <tr>
                            <td><span style="font-weight:600;">{{ $course->title }}</span></td>
                            <td><span class="adm-badge adm-badge-primary">{{ $course->classRoom->name ?? '---' }}</span></td>
                            <td><span class="adm-badge adm-badge-purple">{{ $course->subject->name ?? '---' }}</span></td>
                            <td><span class="adm-badge adm-badge-gray">{{ $course->created_at->format('d/m/Y') }}</span></td>
                            <td>
                                <div class="adm-actions">
                                    <a href="{{ route('admin.courses.edit', $course->id) }}" class="adm-action-link adm-action-edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="{{ route('prof.devoir.create', $course->id) }}" class="adm-action-link adm-action-view">
                                        <i class="bi bi-file-text-fill"></i> Devoir
                                    </a>
                                    <a href="{{ route('admin.lives.create', $course->class_id) }}" class="adm-action-link" style="background:#fce7f3;color:#9d174d;">
                                        <i class="bi bi-camera-video-fill"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.courses.destroy',$course->id) }}" onsubmit="return confirm('Supprimer ce cours ?')">
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
                            <td colspan="5" class="adm-empty">Aucun cours disponible</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($courses, 'links'))
            <div class="adm-card-footer">
                <div class="adm-pagination">{{ $courses->links() }}</div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
