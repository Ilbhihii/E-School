@extends('layouts.admin')

@section('title', 'Créer un cours')
@section('page_title', 'Nouveau cours')
@section('breadcrumb', 'Créer un cours')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-book" style="color:rgba(255,255,255,0.35);"></i> Créer un nouveau cours</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="adm-form-control @error('title') error @enderror" placeholder="Ex: Les équations du 2ème degré">
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Description</label>
                        <textarea name="description" rows="4" class="adm-form-control adm-form-textarea @error('description') error @enderror" placeholder="Décrire le cours...">{{ old('description') }}</textarea>
                        @error('description') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Classe</label>
                                <select name="class_id" class="adm-form-select @error('class_id') error @enderror">
                                    <option value="">Sélectionner une classe</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $selectedClassId ?? '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Matière</label>
                                <select name="subject_id" class="adm-form-select @error('subject_id') error @enderror">
                                    <option value="">Sélectionner une matière</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id', $selectedSubjectId ?? '') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                @error('subject_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Lien du cours (optionnel)</label>
                        <input type="url" name="course_link" class="adm-form-control" placeholder="https://...">
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Vidéo</label>
                                <input type="file" name="video" accept="video/*" class="adm-form-control" style="padding:8px;">
                                @error('video') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">PDF</label>
                                <input type="file" name="pdf" accept=".pdf" class="adm-form-control" style="padding:8px;">
                                @error('pdf') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <a href="{{ route('admin.courses.index') }}" class="adm-btn adm-btn-ghost">
                            <i class="bi bi-x"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary">
                            <i class="bi bi-plus-lg"></i> Créer le cours
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
