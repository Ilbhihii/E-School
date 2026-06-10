@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📘 Gestion des Cours</span></h1>
                <p class="admin-header-subtitle">Gérez efficacement vos cours et ressources pédagogiques</p>
            </div>
            <a href="{{ route('prof.courses.create') }}" class="adm-btn adm-btn-primary">
                <i class="bi bi-plus-lg"></i> Créer un cours
            </a>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card green adm-fade-up">
                <div class="stat-card-icon green"><i class="bi bi-book-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $courses->count() }}</div>
                    <div class="stat-card-label">Total Cours</div>
                </div>
            </div>
            <div class="stat-card blue adm-fade-up">
                <div class="stat-card-icon blue"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-card-value">{{ $courses->where('created_at','>', now()->subDays(7))->count() }}</div>
                    <div class="stat-card-label">Cours récents (7j)</div>
                </div>
            </div>
        </div>

        <!-- RECENTS -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-header">
                <h3><i class="bi bi-clock-fill"></i> Cours récents</h3>
            </div>
            <div class="adm-card-body" style="padding:0;">
                @forelse($courses->take(5) as $course)
                    <div class="adm-flex adm-flex-between" style="padding:0.85rem 1.5rem;border-bottom:1px solid var(--adm-border);">
                        <span style="font-weight:600;">{{ Str::limit($course->title, 60) }}</span>
                        <small style="color:var(--adm-text-secondary);">{{ $course->created_at->diffForHumans() }}</small>
                    </div>
                @empty
                    <div class="adm-empty">Aucun cours récent</div>
                @endforelse
            </div>
        </div>

        <!-- TABLE -->
        <div class="adm-card">
            <div class="adm-card-header" style="background:linear-gradient(135deg,#1e1b4b,#312e81);color:white;border-radius:var(--adm-radius-lg) var(--adm-radius-lg) 0 0;">
                <h3 style="color:white;"><i class="bi bi-table"></i> Tous les cours</h3>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Niveau</th>
                            <th>Matière</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                        <tr>
                            <td><span style="font-weight:600;">{{ Str::limit($course->title, 40) }}</span></td>
                            <td>{{ $course->level->name ?? '---' }}</td>
                            <td><span class="adm-badge adm-badge-purple">{{ $course->subject->name ?? '---' }}</span></td>
                            <td><span class="adm-badge adm-badge-gray">{{ $course->created_at->format('d/m/Y') }}</span></td>
                            <td>
                                <div class="adm-actions">
                                    <a href="{{ route('prof.devoir.create', ['course_id'=>$course->id]) }}" class="adm-action-link adm-action-view"><i class="bi bi-plus-lg"></i></a>
                                    <a href="{{ route('prof.courses.edit', $course->id) }}" class="adm-action-link adm-action-edit"><i class="bi bi-pencil-fill"></i></a>
                                    <form method="POST" action="{{ route('prof.courses.destroy', $course->id) }}" onsubmit="return confirm('Confirmer ?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-action-link adm-action-delete" style="border:none;cursor:pointer;"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
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
