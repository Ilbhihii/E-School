@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:800px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📘 Créer un cours</span></h1>
                <p class="admin-header-subtitle">Ajoutez du contenu pédagogique facilement</p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="adm-form-input" placeholder="Ex: Les équations du 2ème degré">
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Description</label>
                        <textarea name="description" rows="4" class="adm-form-textarea" placeholder="Décrire le cours...">{{ old('description') }}</textarea>
                        @error('description') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-grid-2 adm-mb-2">
                        <div class="adm-form-group">
                            <label class="adm-form-label">Classe</label>
                            <select name="class_id" class="adm-form-select">
                                <option value="">Sélectionner une classe</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Matière</label>
                            <select name="subject_id" class="adm-form-select">
                                <option value="">Sélectionner une matière</option>
                                @foreach($subjects->unique('name') as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Lien du cours (optionnel)</label>
                        <input type="url" name="course_link" class="adm-form-input" placeholder="https://...">
                    </div>

                    <div class="adm-grid-2 adm-mb-2">
                        <div class="adm-form-file">
                            <label class="adm-form-label">Vidéo</label>
                            <input type="file" name="video" accept="video/*" class="w-100">
                            @error('video') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-file">
                            <label class="adm-form-label">PDF</label>
                            <input type="file" name="pdf" accept=".pdf" class="w-100">
                            @error('pdf') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="adm-flex adm-flex-between adm-mt-3">
                        <a href="{{ route('admin.courses.index') }}" class="adm-btn adm-btn-ghost">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg">
                            <i class="bi bi-plus-lg"></i> Créer le cours
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
