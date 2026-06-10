@extends('layouts.admin')

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
                <form method="POST" action="{{ route('admin.courses.update', $course->id) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre</label>
                        <input type="text" name="title" value="{{ old('title', $course->title) }}" class="adm-form-input @error('title') error @enderror" placeholder="Ex: Les équations du 2ème degré">
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Description</label>
                        <textarea name="description" rows="4" class="adm-form-textarea @error('description') error @enderror" placeholder="Description du cours...">{{ old('description', $course->description) }}</textarea>
                        @error('description') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-grid-2 adm-mb-2">
                        <div class="adm-form-group">
                            <label class="adm-form-label">Classe</label>
                            <select name="class_id" class="adm-form-select @error('class_id') error @enderror">
                                <option value="">-- Choisir --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $course->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Matière</label>
                            <select name="subject_id" class="adm-form-select @error('subject_id') error @enderror">
                                <option value="">-- Choisir --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $course->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Lien du cours (optionnel)</label>
                        <input type="url" name="course_link" value="{{ old('course_link', $course->course_link) }}" class="adm-form-input" placeholder="https://...">
                    </div>

                    @if($course->video)
                    <div class="adm-alert adm-alert-info">
                        <i class="bi bi-camera-video-fill"></i> Vidéo actuelle disponible
                    </div>
                    @endif

                    @if($course->pdf)
                    <div class="adm-alert adm-alert-success">
                        <i class="bi bi-file-earmark-pdf-fill"></i> <a href="{{ Storage::url($course->pdf) }}" target="_blank" style="color:#166534;font-weight:600;">Voir PDF actuel</a>
                    </div>
                    @endif

                    <div class="adm-grid-2 adm-mb-2">
                        <div class="adm-form-file">
                            <label class="adm-form-label">Nouvelle vidéo</label>
                            <input type="file" name="video" accept="video/*" class="w-100">
                            @error('video') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-file">
                            <label class="adm-form-label">Nouveau PDF</label>
                            <input type="file" name="pdf" accept=".pdf" class="w-100">
                            @error('pdf') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="adm-flex adm-gap-2 adm-mt-3">
                        <a href="{{ route('admin.courses.index') }}" class="adm-btn adm-btn-ghost" style="flex:1;text-align:center;">
                            <i class="bi bi-arrow-left"></i> Annuler
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
