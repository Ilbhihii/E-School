@extends('layouts.admin')

@section('title', 'Modifier le cours')
@section('page_title', 'Modifier cours')
@section('breadcrumb', 'Modifier le cours')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-pencil" style="color:rgba(255,255,255,0.35);"></i> Modifier: {{ $course->title }}</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.courses.update', $course->id) }}" enctype="multipart/form-data">
                    @method('PUT') @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre</label>
                        <input type="text" name="title" value="{{ old('title', $course->title) }}" class="adm-form-control @error('title') error @enderror" placeholder="Ex: Les équations du 2ème degré">
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Description</label>
                        <textarea name="description" rows="4" class="adm-form-control adm-form-textarea @error('description') error @enderror" placeholder="Description du cours...">{{ old('description', $course->description) }}</textarea>
                        @error('description') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
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
                        </div>
                        <div class="col-md-6">
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
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Lien du cours (optionnel)</label>
                        <input type="url" name="course_link" value="{{ old('course_link', $course->course_link) }}" class="adm-form-control" placeholder="https://...">
                    </div>

                    @if($course->video)
                    <div class="adm-card mb-4" style="background:rgba(0,58,143,0.08);border-color:rgba(0,58,143,0.15);">
                        <div class="adm-card-body" style="padding:1rem;">
                            <label class="adm-form-label">Vidéo actuelle</label>
                            <video src="{{ Storage::url($course->video) }}" controls style="max-width:100%;border-radius:8px;margin-top:4px;"></video>
                        </div>
                    </div>
                    @endif

                    @if($course->pdf)
                    <div class="adm-card mb-4" style="background:rgba(22,163,74,0.08);border-color:rgba(22,163,74,0.15);">
                        <div class="adm-card-body" style="padding:1rem;">
                            <label class="adm-form-label">PDF actuel</label>
                            <div><a href="{{ Storage::url($course->pdf) }}" target="_blank" style="color:#4ADE80;">Voir le PDF</a></div>
                        </div>
                    </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Nouvelle vidéo (remplace)</label>
                                <input type="file" name="video" accept="video/*" class="adm-form-control" style="padding:8px;">
                                @error('video') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Nouveau PDF (remplace)</label>
                                <input type="file" name="pdf" accept=".pdf" class="adm-form-control" style="padding:8px;">
                                @error('pdf') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <a href="{{ route('admin.courses.index') }}" class="adm-btn adm-btn-ghost flex-fill text-center">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary flex-fill">
                            <i class="bi bi-save"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
