@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:800px">

        <div class="adm-mb-2">
            <a href="{{ route('prof.devoir.index', ['course_id' => $course_id ?? '']) }}" class="adm-btn adm-btn-ghost adm-btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        @if($course)
        <div class="adm-alert adm-alert-info">
            <i class="bi bi-info-circle-fill"></i> Devoir pour: <strong>{{ $course->title }}</strong>
        </div>
        @endif

        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Ajouter un Devoir</span></h1>
                <p class="admin-header-subtitle">{{ $course ? 'pour le cours: ' . $course->title : 'Créez et assignez un nouveau devoir' }}</p>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('prof.devoir.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="adm-grid-2">
                        <div class="adm-form-group">
                            <label class="adm-form-label"><i class="bi bi-file-earmark-text"></i> Titre du devoir <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="adm-form-input @error('title') error @enderror" placeholder="Ex: Devoir de mathématiques" required>
                            @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label"><i class="bi bi-building"></i> Classe *</label>
                            <select name="class_room_id" required class="adm-form-select @error('class_room_id') error @enderror">
                                <option value="">Choisir une classe...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_room_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_room_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        @if(isset($courses) && $courses->count() > 0)
                        <div class="adm-form-group">
                            <label class="adm-form-label"><i class="bi bi-book"></i> Cours *</label>
                            <select name="course_id" class="adm-form-select @error('course_id') error @enderror">
                                <option value="">Sélectionner un cours</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}" {{ old('course_id', $course_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                                @endforeach
                            </select>
                            @error('course_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        @endif
                        <div class="adm-form-group">
                            <label class="adm-form-label"><i class="bi bi-calendar-check"></i> Date limite</label>
                            <input type="date" name="due_date" class="adm-form-input" value="{{ old('due_date') }}">
                            @error('due_date') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label"><i class="bi bi-card-text"></i> Description détaillée</label>
                        <textarea name="description" rows="5" class="adm-form-textarea" placeholder="Expliquez les consignes, les objectifs et les critères d'évaluation..."></textarea>
                    </div>

                    <div class="adm-form-file adm-mb-2">
                        <label class="adm-form-label"><i class="bi bi-file-earmark-pdf"></i> Fichier PDF (Optionnel)</label>
                        <input type="file" name="file" accept=".pdf" class="w-100">
                        <small style="color:var(--adm-text-secondary);">Maximum 10 Mo - Format PDF uniquement</small>
                        @error('file') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <hr class="adm-mt-3 adm-mb-3">

                    <div class="adm-flex adm-gap-2" style="justify-content:flex-end;">
                        <a href="{{ route('prof.devoir.index', ['course_id' => $course_id ?? '']) }}" class="adm-btn adm-btn-ghost">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg">
                            <i class="bi bi-check-circle-fill"></i> {{ $course ? 'Créer pour ' . $course->title : 'Enregistrer le devoir' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
