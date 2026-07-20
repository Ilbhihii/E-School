@extends('layouts.prof')

@section('title', 'Cours - ' . $subject->name . ' - ' . $level->name)
@section('page_title', $subject->name)
@section('breadcrumb', 'Matières → Niveaux → Classes → Cours')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;font-size:13px;color:var(--muted);">
                    <a href="{{ route('prof.subjects.list') }}" style="color:var(--muted);text-decoration:none;"><i class="bi bi-book me-1"></i>Matières</a>
                    <span>/</span>
                    <a href="{{ route('prof.subjects.levels', $subject) }}" style="color:var(--muted);text-decoration:none;">{{ $subject->name }}</a>
                    <span>/</span>
                    <a href="{{ route('prof.subjects.classes', [$subject, $level]) }}" style="color:var(--muted);text-decoration:none;">{{ $level->name }}</a>
                    <span>/</span>
                    <span style="color:var(--text);font-weight:600;">{{ $class->name }}</span>
                </div>
                <h1 class="admin-header-title"><span class="gradient">📘 Cours — {{ $subject->name }}</span></h1>
                <p class="admin-header-subtitle">{{ $class->name }} · {{ $level->name }} — {{ $courses->count() }} cours</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.courses.create', ['class_id' => $class->id, 'subject_id' => $subject->id]) }}" class="adm-btn adm-btn-primary">
                    <i class="bi bi-plus-lg"></i> Nouveau cours
                </a>
            </div>
        </div>

        <div class="adm-stats-grid">
            <div class="adm-stat green">
                <div class="stat-top">
                    <div class="stat-icon"><i class="bi bi-play-circle-fill"></i></div>
                </div>
                <div class="stat-value">{{ $courses->count() }}</div>
                <div class="stat-label">Total cours</div>
            </div>
            <div class="adm-stat blue">
                <div class="stat-top">
                    <div class="stat-icon"><i class="bi bi-camera-video-fill"></i></div>
                </div>
                <div class="stat-value">{{ $courses->whereNotNull('video')->count() }}</div>
                <div class="stat-label">Avec vidéo</div>
            </div>
            <div class="adm-stat purple">
                <div class="stat-top">
                    <div class="stat-icon"><i class="bi bi-file-pdf-fill"></i></div>
                </div>
                <div class="stat-value">{{ $courses->whereNotNull('pdf')->count() }}</div>
                <div class="stat-label">Avec PDF</div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-collection" style="color:rgba(255,255,255,0.35);"></i> Cours de {{ $subject->name }} — {{ $class->name }}</h4>
                <div class="card-actions">
                    <span style="color:var(--muted);font-size:0.8rem;">{{ $courses->count() }} cours</span>
                </div>
            </div>
            <div class="adm-card-body p-0">
                @if($courses->isNotEmpty())
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Média</th>
                                <th>Date</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            <tr>
                                <td><span style="font-weight:500;">{{ $course->title }}</span></td>
                                <td style="color:var(--muted);font-size:0.82rem;max-width:250px;">
                                    <span class="text-truncate">{{ Str::limit($course->description, 60) }}</span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:4px;">
                                        @if($course->video)
                                            <span class="adm-badge adm-badge-danger" title="Vidéo"><i class="bi bi-camera-video"></i></span>
                                        @endif
                                        @if($course->pdf)
                                            <span class="adm-badge adm-badge-success" title="PDF"><i class="bi bi-file-pdf"></i></span>
                                        @endif
                                        @if(!$course->video && !$course->pdf)
                                            <span style="color:var(--muted);font-size:0.75rem;">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="color:var(--muted);font-size:0.8rem;">{{ $course->created_at->format('d/m/Y') }}</td>
                                <td style="text-align:right;">
                                    <div style="display:flex;gap:6px;justify-content:flex-end;">
                                        <a href="{{ route('admin.courses.show', $course->id) }}" class="adm-btn adm-btn-sm" style="background:rgba(6,182,212,0.15);color:#67E8F9;border:1px solid rgba(6,182,212,0.15);" title="Voir le cours">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course->id) }}" class="adm-btn adm-btn-warning adm-btn-sm" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.courses.destroy', $course->id) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce cours ?')">
                                            @csrf @method('DELETE')
                                            <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="adm-empty" style="padding:3rem 2rem;">
                    <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                    <h5>Aucun cours pour cette matière</h5>
                    <p>Créez votre premier cours pour {{ $subject->name }} dans la classe {{ $class->name }}.</p>
                    <a href="{{ route('admin.courses.create', ['class_id' => $class->id, 'subject_id' => $subject->id]) }}" class="adm-btn adm-btn-primary adm-btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Créer un cours
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('prof.subjects.classes', [$subject, $level]) }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-arrow-left me-1"></i> Retour aux classes
            </a>
        </div>

    </div>
</div>
@endsection
