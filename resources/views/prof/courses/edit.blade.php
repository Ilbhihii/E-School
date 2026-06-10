@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:800px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">✏️ Modifier le cours</span></h1>
                <p class="admin-header-subtitle">{{ $course->title }}</p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form action="{{ route('prof.courses.update',$course->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du cours</label>
                        <input type="text" name="title" value="{{ old('title', $course->title) }}" class="adm-form-input @error('title') error @enderror" required>
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Description</label>
                        <textarea name="description" class="adm-form-textarea @error('description') error @enderror" style="height:120px;" required>{{ old('description', $course->description) }}</textarea>
                        @error('description') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-grid-2">
                        <div class="adm-form-group">
                            <label class="adm-form-label">Niveau</label>
                            <select name="level_id" class="adm-form-select @error('level_id') error @enderror" required>
                                <option value="">Choisir niveau</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ old('level_id', $course->level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }} ({{ $level->subject->name ?? '' }})</option>
                                @endforeach
                            </select>
                            @error('level_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Matière</label>
                            <select name="subject_id" class="adm-form-select @error('subject_id') error @enderror" required>
                                <option value="">Choisir une matière</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $course->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    @if($course->video)
                    <div class="adm-alert adm-alert-info">
                        <i class="bi bi-play-circle"></i> Vidéo actuelle: <a href="{{ asset('storage/' . $course->video) }}" target="_blank" style="color:#1e40af;font-weight:600;">Voir la vidéo</a>
                    </div>
                    @endif
                    @if($course->pdf)
                    <div class="adm-alert adm-alert-success">
                        <i class="bi bi-file-earmark-pdf"></i> PDF actuel: <a href="{{ asset('storage/' . $course->pdf) }}" target="_blank" style="color:#166534;font-weight:600;">Voir le PDF</a>
                    </div>
                    @endif

                    <div class="adm-grid-2">
                        <div class="adm-form-file">
                            <label class="adm-form-label"><i class="bi bi-play-circle"></i> Nouvelle vidéo</label>
                            <input type="file" name="video" accept="video/*" class="w-100">
                            @error('video') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-file">
                            <label class="adm-form-label"><i class="bi bi-file-earmark-pdf"></i> Nouveau PDF</label>
                            <input type="file" name="pdf" accept=".pdf" class="w-100">
                            @error('pdf') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- DEVOIRS LIES -->
                    @if($course->assignments->count() > 0)
                    <div class="adm-card adm-mt-3" style="background:#f8fafc;backdrop-filter:none;border:1px solid var(--adm-border);">
                        <div class="adm-card-header">
                            <h3><i class="bi bi-file-earmark-text"></i> Devoirs liés au cours</h3>
                        </div>
                        <div class="adm-card-body" style="padding:0;">
                            @foreach($course->assignments as $devoir)
                            <div class="adm-flex adm-flex-between" style="padding:0.75rem 1.25rem;border-bottom:1px solid var(--adm-border);">
                                <div>
                                    <strong>{{ $devoir->title }}</strong>
                                    <small style="color:var(--adm-text-secondary);margin-left:0.5rem;">{{ $devoir->due_date }}</small>
                                </div>
                                <div class="adm-actions">
                                    <a href="{{ route('prof.devoir.edit', $devoir->id) }}" class="adm-action-link adm-action-edit"><i class="bi bi-pencil-fill"></i></a>
                                    <form method="POST" action="{{ route('prof.devoir.destroy', $devoir->id) }}" class="d-inline" onsubmit="return confirm('Supprimer?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-action-link adm-action-delete" style="border:none;cursor:pointer;"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="adm-flex adm-gap-2 adm-mt-3">
                        <a href="{{ route('prof.courses.index') }}" class="adm-btn adm-btn-ghost" style="flex:1;text-align:center;">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <a href="{{ route('prof.devoir.create', ['course_id'=>$course->id]) }}" class="adm-btn adm-btn-success" style="flex:1;text-align:center;">
                            <i class="bi bi-plus-lg"></i> Ajouter devoir
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary" style="flex:1;">
                            <i class="bi bi-save-fill"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
