@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;font-size:13px;color:var(--muted);">
                    <a href="{{ route('prof.subjects.list') }}" style="color:var(--muted);text-decoration:none;"><i class="bi bi-book me-1"></i>Matières</a>
                    <span>/</span>
                    <span style="color:var(--muted);">{{ $level->name }}</span>
                    <span>/</span>
                    <span style="color:var(--muted);">{{ $class->name }}</span>
                    <span>/</span>
                    <span style="color:var(--text);font-weight:600;">{{ $subject->name }}</span>
                </div>
                <h1 class="admin-header-title"><span class="gradient">📘 Cours — {{ $subject->name }}</span></h1>
                <p class="admin-header-subtitle">{{ $class->name }} — Gérez les cours de cette matière</p>
            </div>
            <a href="{{ route('prof.courses.create') }}" class="adm-btn adm-btn-primary">
                <i class="bi bi-plus-lg"></i> Créer un cours
            </a>
        </div>

        @if($courses->isEmpty())
            <div class="adm-card">
                <div class="adm-empty">
                    <i class="bi bi-inbox"></i>
                    <h3>Aucun cours</h3>
                    <p>Aucun cours n'a encore été créé pour cette matière dans cette classe.</p>
                    <a href="{{ route('prof.courses.create') }}" class="adm-btn adm-btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Créer le premier cours
                    </a>
                </div>
            </div>
        @else
            <div class="adm-card">
                <div class="adm-card-header" style="background:linear-gradient(135deg,#1e1b4b,#312e81);color:white;border-radius:var(--adm-radius-lg) var(--adm-radius-lg) 0 0;">
                    <h3 style="color:white;"><i class="bi bi-list"></i> Liste des cours</h3>
                    <span class="adm-badge adm-badge-light" style="margin-left:auto;">{{ $courses->count() }} cours</span>
                </div>
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Niveau</th>
                                <th>Média</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            <tr>
                                <td><span style="font-weight:600;">{{ Str::limit($course->title, 40) }}</span></td>
                                <td>{{ $course->level->name ?? '---' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if($course->video) <span class="adm-badge adm-badge-info"><i class="bi bi-camera-video"></i></span> @endif
                                        @if($course->pdf) <span class="adm-badge adm-badge-danger"><i class="bi bi-file-pdf"></i></span> @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="adm-actions">
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
            </div>
        @endif

        <div class="st-mt-3">
            <a href="{{ route('prof.dashboard') }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-arrow-left me-1"></i> Retour au tableau de bord
            </a>
        </div>

    </div>
</div>
@endsection
