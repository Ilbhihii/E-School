@extends('layouts.admin')

@section('title', 'Nouveau devoir')
@section('page_title', 'Nouveau devoir')
@section('breadcrumb', 'Créer un devoir')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-3">
            <a href="{{ route('admin.devoirs.index', ['course_id' => $course_id]) }}" class="adm-btn adm-btn-ghost adm-btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        @if($course ?? false)
        <div class="adm-card mb-4" style="background:rgba(0,58,143,0.08);border-color:rgba(0,58,143,0.15);">
            <div class="adm-card-body" style="padding:1.25rem;">
                <strong style="color:rgba(255,255,255,0.85);font-size:1.1rem;">{{ $course->title }}</strong>
                <div style="color:var(--adm-text-secondary);font-size:0.85rem;margin-top:4px;">
                    {{ $course->classRoom->name ?? '' }} • {{ $course->subject->name ?? '' }}
                </div>
            </div>
        </div>
        @endif

        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-plus-circle" style="color:rgba(255,255,255,0.35);"></i> Nouveau devoir</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.devoirs.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Titre *</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="adm-form-control" placeholder="Ex: Exercices sur les équations" required>
                                @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Classe *</label>
                                <select name="class_room_id" class="adm-form-select" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if($courses->count() > 0)
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Cours *</label>
                                <select name="course_id" class="adm-form-select" required>
                                    <option value="">Sélectionner un cours</option>
                                    @foreach($courses as $c)
                                        <option value="{{ $c->id }}" {{ old('course_id', $course_id) == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Date limite *</label>
                                <input type="date" name="due_date" min="{{ now()->format('Y-m-d') }}" value="{{ old('due_date') }}" class="adm-form-control" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Description</label>
                                <textarea name="description" rows="4" class="adm-form-control adm-form-textarea" placeholder="Description du devoir...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Fichier (PDF optionnel)</label>
                                <input type="file" name="file" accept=".pdf" class="adm-form-control" style="padding:8px;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <a href="{{ route('admin.devoirs.index', ['course_id' => $course_id]) }}" class="adm-btn adm-btn-ghost flex-fill text-center">
                            <i class="bi bi-x"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary flex-fill">
                            <i class="bi bi-plus-lg"></i> Créer le devoir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
