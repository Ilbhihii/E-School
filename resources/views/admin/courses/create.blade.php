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
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Niveau</label>
                                <select class="adm-form-select" id="level_id" disabled>
                                    <option value="">Sélectionner d'abord une classe...</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                                <small style="color:var(--adm-text-muted);font-size:0.7rem;">Déduit automatiquement de la classe</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Classe <span style="color:var(--adm-danger);">*</span></label>
                                <select name="class_id" class="adm-form-select @error('class_id') error @enderror" id="class_id" required>
                                    <option value="">Sélectionner une classe</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" data-level-id="{{ $class->level_id }}" data-level-name="{{ $class->level->name ?? '' }}" {{ old('class_id', $selectedClassId ?? '') == $class->id ? 'selected' : '' }}>{{ $class->level->name ?? '' }} — {{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Matière <span style="color:var(--adm-danger);">*</span></label>
                                <select name="subject_id" class="adm-form-select @error('subject_id') error @enderror" id="subject_id" required>
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

<script>
function updateLevel() {
    const classSelect = document.getElementById('class_id');
    const levelSelect = document.getElementById('level_id');
    const selectedOption = classSelect.options[classSelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.levelId) {
        levelSelect.value = selectedOption.dataset.levelId;
    } else {
        levelSelect.value = '';
    }
}

function filterSubjectsByClass() {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const classId = classSelect.value;
    const preselectedSubject = '{{ $selectedSubjectId ?? '' }}';
    
    // Désactiver le temps du chargement
    subjectSelect.disabled = true;
    subjectSelect.innerHTML = '<option value="">Chargement...</option>';
    
    if (!classId) {
        // Aucune classe sélectionnée → montrer TOUTES les matières (vient du serveur)
        subjectSelect.innerHTML = '<option value="">Sélectionner une matière</option>';
        @foreach($subjects as $subject)
            subjectSelect.innerHTML += '<option value="{{ $subject->id }}" {{ old('subject_id', $selectedSubjectId ?? '') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>';
        @endforeach
        subjectSelect.disabled = false;
        return;
    }
    
    // Classe sélectionnée → filtrer par AJAX
    fetch('/admin/get-class-subjects/' + classId)
        .then(response => response.json())
        .then(subjects => {
            subjectSelect.innerHTML = '<option value="">Sélectionner une matière</option>';
            subjects.forEach(function(subject) {
                const selected = preselectedSubject == subject.id ? 'selected' : '';
                subjectSelect.innerHTML += '<option value="' + subject.id + '" ' + selected + '>' + escapeHtml(subject.name) + '</option>';
            });
            subjectSelect.disabled = false;
        })
        .catch(error => {
            subjectSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            subjectSelect.disabled = false;
            console.error('Erreur chargement matières:', error);
        });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    classSelect.addEventListener('change', function() {
        updateLevel();
        filterSubjectsByClass();
    });
    
    updateLevel();
    // Sur le chargement, utiliser les matières du serveur (pas d'AJAX)
    filterSubjectsByClass();
});
</script>
@endsection
