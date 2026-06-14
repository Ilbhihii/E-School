@extends('layouts.prof')

@section('title', 'Modifier le cours')
@section('page_title', 'Modifier le cours')
@section('breadcrumb', 'Édition de cours')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-pencil-square me-2" style="color:var(--adm-warning);"></i> Modifier le cours</h1>
        <div class="subtitle">{{ $course->title }}</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.courses.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-info-circle" style="color:rgba(255,255,255,0.35);"></i> Informations du cours</h4>
    </div>
    <div class="adm-card-body">
        <form action="{{ route('prof.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Title -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du cours <span style="color:var(--adm-danger);">*</span></label>
                        <input type="text" class="adm-form-control @error('title') error @enderror" id="title" name="title" value="{{ old('title', $course->title) }}" required placeholder="Titre du cours">
                        @error('title')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Description</label>
                        <textarea class="adm-form-control adm-form-textarea @error('description') error @enderror" id="description" name="description" style="min-height:120px;resize:vertical;" required placeholder="Description du cours">{{ old('description', $course->description) }}</textarea>
                        @error('description')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Level -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Niveau <span style="color:var(--adm-danger);">*</span></label>
                        <select class="adm-form-select @error('level_id') error @enderror" id="level_id" name="level_id" required>
                            <option value="">Choisir niveau</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ old('level_id', $course->level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }} ({{ $level->subject->name ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('level_id')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Matière <span style="color:var(--adm-danger);">*</span></label>
                        <select class="adm-form-select @error('subject_id') error @enderror" id="subject_id" name="subject_id" required>
                            <option value="">Choisir une matière</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $course->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Media Section -->
            <div class="adm-card" style="margin-bottom:1.25rem;border-color:rgba(0,58,143,0.1);">
                <div class="adm-card-header" style="background:linear-gradient(135deg,rgba(0,58,143,0.1),rgba(37,99,235,0.05));">
                    <h4><i class="bi bi-file-earmark-play" style="color:rgba(255,255,255,0.35);"></i> Médias du cours</h4>
                </div>
                <div class="adm-card-body">
                    <div class="row g-4">
                        <!-- Video -->
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label"><i class="bi bi-play-circle me-1" style="color:#EF4444;"></i> Vidéo du cours</label>
                                <div style="border:2px dashed rgba(239,68,68,0.15);border-radius:12px;padding:1rem;text-align:center;">
                                    <i class="bi bi-cloud-upload" style="font-size:1.5rem;color:rgba(239,68,68,0.3);display:block;margin-bottom:0.5rem;"></i>
                                    <input type="file" class="adm-form-control @error('video') error @enderror" name="video" accept="video/*">
                                    <div style="color:var(--adm-text-muted);font-size:0.7rem;margin-top:0.4rem;">MP4, MOV, AVI</div>
                                </div>
                                @if($course->video)
                                <div style="margin-top:8px;padding:8px 12px;background:rgba(239,68,68,0.1);border-radius:8px;display:flex;align-items:center;gap:8px;">
                                    <i class="bi bi-camera-video" style="color:#FCA5A5;"></i>
                                    <span style="font-size:0.8rem;flex:1;">Vidéo actuelle</span>
                                    <a href="{{ asset('storage/' . $course->video) }}" target="_blank" class="adm-btn adm-btn-danger adm-btn-sm">
                                        <i class="bi bi-eye me-1"></i> Voir
                                    </a>
                                </div>
                                @endif
                                @error('video')
                                    <div class="adm-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- PDF -->
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label"><i class="bi bi-file-earmark-pdf me-1" style="color:#22C55E;"></i> PDF du cours</label>
                                <div style="border:2px dashed rgba(22,163,74,0.15);border-radius:12px;padding:1rem;text-align:center;">
                                    <i class="bi bi-cloud-upload" style="font-size:1.5rem;color:rgba(22,163,74,0.3);display:block;margin-bottom:0.5rem;"></i>
                                    <input type="file" class="adm-form-control @error('pdf') error @enderror" name="pdf" accept=".pdf">
                                    <div style="color:var(--adm-text-muted);font-size:0.7rem;margin-top:0.4rem;">Format PDF uniquement</div>
                                </div>
                                @if($course->pdf)
                                <div style="margin-top:8px;padding:8px 12px;background:rgba(22,163,74,0.1);border-radius:8px;display:flex;align-items:center;gap:8px;">
                                    <i class="bi bi-file-pdf" style="color:#4ADE80;"></i>
                                    <span style="font-size:0.8rem;flex:1;">PDF actuel</span>
                                    <a href="{{ asset('storage/' . $course->pdf) }}" target="_blank" class="adm-btn adm-btn-success adm-btn-sm">
                                        <i class="bi bi-eye me-1"></i> Voir
                                    </a>
                                </div>
                                @endif
                                @error('pdf')
                                    <div class="adm-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Assignments -->
            @if($course->assignments->count() > 0)
            <div class="adm-card" style="margin-bottom:1.25rem;border-color:rgba(22,163,74,0.1);">
                <div class="adm-card-header" style="background:linear-gradient(135deg,rgba(22,163,74,0.1),rgba(34,197,94,0.05));">
                    <h4><i class="bi bi-file-earmark-text" style="color:rgba(255,255,255,0.35);"></i> Devoirs liés au cours</h4>
                    <div class="card-actions">
                        <a href="{{ route('prof.devoir.create', ['course_id'=>$course->id]) }}" class="adm-btn adm-btn-success adm-btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Ajouter
                        </a>
                    </div>
                </div>
                <div class="adm-card-body p-0">
                    <div class="adm-table-wrap">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Date limite</th>
                                    <th style="text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->assignments as $devoir)
                                <tr>
                                    <td><span style="font-weight:500;">{{ $devoir->title }}</span></td>
                                    <td><span class="adm-badge {{ $devoir->due_date <= now()->format('Y-m-d') ? 'adm-badge-danger' : 'adm-badge-success' }}">{{ $devoir->due_date }}</span></td>
                                    <td style="text-align:right;">
                                        <div style="display:flex;gap:6px;justify-content:flex-end;">
                                            <a href="{{ route('prof.devoir.edit', $devoir->id) }}" class="adm-btn adm-btn-warning adm-btn-sm" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('prof.devoir.destroy', $devoir->id) }}" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?')">
                                                @csrf @method('DELETE')
                                                <button class="adm-btn adm-btn-danger adm-btn-sm" title="Supprimer">
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
                </div>
            </div>
            @endif

            <div class="d-flex gap-3 pt-4" style="border-top:1px solid rgba(255,255,255,0.06);">
                <a href="{{ route('prof.courses.index') }}" class="adm-btn adm-btn-ghost">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
                <button type="submit" class="adm-btn adm-btn-warning" style="margin-left:auto;">
                    <i class="bi bi-save me-1"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.adm-form-textarea {
    min-height: 120px;
    resize: vertical;
}
</style>

@endsection
