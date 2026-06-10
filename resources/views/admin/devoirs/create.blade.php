@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:800px">

        <!-- BACK LINK -->
        <div class="adm-mb-2">
            <a href="{{ route('admin.devoirs.index', ['course_id' => $course_id]) }}" class="adm-btn adm-btn-ghost adm-btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        @if($course)
        <div class="adm-alert adm-alert-info">
            <i class="bi bi-info-circle-fill"></i> Devoir pour: <strong>{{ $course->title }}</strong> — {{ $course->classRoom->name }} • {{ $course->subject->name }}
        </div>
        @endif

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">➕ Nouveau Devoir</span></h1>
                <p class="admin-header-subtitle">Ajouter un nouveau devoir pour les étudiants</p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.devoirs.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="adm-grid-2">
                        <div class="adm-form-group">
                            <label class="adm-form-label">Titre *</label>
                            <input type="text" name="title" required value="{{ old('title') }}" class="adm-form-input" placeholder="Ex: Exercices sur les équations">
                            @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Classe *</label>
                            <select name="class_room_id" required class="adm-form-select">
                                <option value="">Sélectionner</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($courses->count() > 0)
                        <div class="adm-form-group">
                            <label class="adm-form-label">Cours *</label>
                            <select name="course_id" required class="adm-form-select">
                                <option value="">Sélectionner un cours</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}" {{ old('course_id', $course_id) == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="adm-form-group">
                            <label class="adm-form-label">Date limite *</label>
                            <input type="date" name="due_date" required min="{{ now()->format('Y-m-d') }}" value="{{ old('due_date') }}" class="adm-form-input">
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Description</label>
                        <textarea name="description" rows="4" class="adm-form-textarea" placeholder="Description du devoir...">{{ old('description') }}</textarea>
                    </div>

                    <div class="adm-form-file adm-mb-2">
                        <label class="adm-form-label">Fichier (PDF optionnel)</label>
                        <input type="file" name="file" accept=".pdf" class="w-100">
                    </div>

                    <div class="adm-flex adm-flex-between adm-mt-3">
                        <a href="{{ route('admin.devoirs.index', ['course_id' => $course_id]) }}" class="adm-btn adm-btn-ghost">
                            <i class="bi bi-x-lg"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg">
                            <i class="bi bi-plus-lg"></i> Créer Devoir
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
