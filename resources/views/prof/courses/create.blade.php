@extends('layouts.prof')

@section('title', 'Créer un cours')
@section('page_title', 'Créer un cours')
@section('breadcrumb', 'Création de cours')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-plus-circle me-2" style="color:var(--adm-primary);"></i> Créer un nouveau cours</h1>
        <div class="subtitle">Remplissez les informations pour ajouter votre cours</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.courses.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-book" style="color:rgba(255,255,255,0.35);"></i> Informations du cours</h4>
    </div>
    <div class="adm-card-body">
        <form action="{{ route('prof.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Title -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du cours <span style="color:var(--adm-danger);">*</span></label>
                        <input type="text" class="adm-form-control @error('title') error @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Ex: Introduction à la physique quantique" required>
                        @error('title')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Description</label>
                        <textarea class="adm-form-control adm-form-textarea @error('description') error @enderror" id="description" name="description" rows="5" placeholder="Décrivez le contenu du cours...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Niveau (lecture seule via classe) -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Niveau <span style="color:var(--adm-danger);">*</span></label>
                        <select class="adm-form-select" id="level_id" disabled>
                            <option value="">Choisir d'abord une classe...</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                        <small style="color:var(--adm-text-muted);font-size:0.7rem;">Déduit automatiquement de la classe sélectionnée</small>
                    </div>

                    <!-- Classe -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe <span style="color:var(--adm-danger);">*</span></label>
                        <select class="adm-form-select @error('class_id') error @enderror" id="class_id" name="class_id" required>
                            <option value="">Choisir une classe...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" data-level-id="{{ $class->level_id }}" data-level-name="{{ $class->level->name ?? '' }}" {{ old('class_id', $selectedClassId ?? '') == $class->id ? 'selected' : '' }}>{{ $class->level->name ?? '' }} — {{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Matière -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Matière <span style="color:var(--adm-danger);">*</span></label>
                        <select class="adm-form-select @error('subject_id') error @enderror" id="subject_id" name="subject_id" required>
                            <option value="">Choisir une matière...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $selectedSubjectId ?? '') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
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
                                <label class="adm-form-label"><i class="bi bi-play-circle me-1" style="color:#EF4444;"></i> Vidéo introductrice</label>
                                <div style="border:2px dashed rgba(239,68,68,0.15);border-radius:12px;padding:1rem;text-align:center;">
                                    <i class="bi bi-cloud-upload" style="font-size:1.5rem;color:rgba(239,68,68,0.3);display:block;margin-bottom:0.5rem;"></i>
                                    <input type="file" class="adm-form-control @error('video') error @enderror" id="video" name="video" accept="video/*" onchange="previewVideo(event)">
                                    <div style="color:var(--adm-text-muted);font-size:0.7rem;margin-top:0.4rem;">MP4, MOV, AVI</div>
                                </div>
                                @error('video')
                                    <div class="adm-form-error">{{ $message }}</div>
                                @enderror
                                <div id="videoPreview" class="mt-3" style="display:none;border-radius:10px;overflow:hidden;border:1px solid rgba(239,68,68,0.2);">
                                    <video class="w-100" controls preload="metadata" style="height:200px;display:block;"></video>
                                </div>
                            </div>
                        </div>

                        <!-- PDF -->
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label"><i class="bi bi-file-earmark-pdf me-1" style="color:#22C55E;"></i> Document PDF (optionnel)</label>
                                <div style="border:2px dashed rgba(22,163,74,0.15);border-radius:12px;padding:1rem;text-align:center;">
                                    <i class="bi bi-cloud-upload" style="font-size:1.5rem;color:rgba(22,163,74,0.3);display:block;margin-bottom:0.5rem;"></i>
                                    <input type="file" class="adm-form-control @error('pdf') error @enderror" id="pdf" name="pdf" accept=".pdf" onchange="previewPDF(event)">
                                    <div style="color:var(--adm-text-muted);font-size:0.7rem;margin-top:0.4rem;">Format PDF uniquement</div>
                                </div>
                                @error('pdf')
                                    <div class="adm-form-error">{{ $message }}</div>
                                @enderror
                                <div id="pdfPreview" class="mt-3" style="display:none;border-radius:10px;overflow:hidden;border:1px solid rgba(22,163,74,0.2);">
                                    <iframe class="w-100 rounded" style="height:200px;border:none;display:block;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Section -->
            <div class="adm-card" style="margin-bottom:1.25rem;border-color:rgba(22,163,74,0.1);">
                <div class="adm-card-header" style="background:linear-gradient(135deg,rgba(22,163,74,0.1),rgba(34,197,94,0.05));">
                    <h4><i class="bi bi-file-earmark-text" style="color:rgba(255,255,255,0.35);"></i> Devoir lié au cours (optionnel)</h4>
                </div>
                <div class="adm-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Titre du devoir</label>
                                <input type="text" class="adm-form-control" name="assignment_title" placeholder="Ex: Exercices chapitre 3">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Date limite</label>
                                <input type="date" name="assignment_due_date" class="adm-form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Fichier PDF</label>
                                <input type="file" name="assignment_file" class="adm-form-control" accept=".pdf">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Description</label>
                                <textarea class="adm-form-control adm-form-textarea" name="assignment_description" placeholder="Description du devoir..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 pt-4" style="border-top:1px solid rgba(255,255,255,0.06);">
                <a href="{{ route('prof.courses.index') }}" class="adm-btn adm-btn-ghost">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
                <button type="submit" class="adm-btn adm-btn-primary" style="margin-left:auto;">
                    <i class="bi bi-rocket-takeoff me-1"></i> Créer le cours
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

<script>
function previewVideo(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('videoPreview');
    if (file) {
        preview.querySelector('video').src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}

function previewPDF(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('pdfPreview');
    if (file) {
        preview.querySelector('iframe').src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}

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
    
    subjectSelect.disabled = true;
    subjectSelect.innerHTML = '<option value="">Chargement...</option>';
    
    if (!classId) {
        // Aucune classe → toutes les matières (serveur)
        subjectSelect.innerHTML = '<option value="">Choisir une matière...</option>';
        @foreach($subjects as $subject)
            subjectSelect.innerHTML += '<option value="{{ $subject->id }}" {{ old('subject_id', $selectedSubjectId ?? '') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>';
        @endforeach
        subjectSelect.disabled = false;
        return;
    }
    
    fetch('/admin/get-class-subjects/' + classId)
        .then(response => response.json())
        .then(subjects => {
            subjectSelect.innerHTML = '<option value="">Choisir une matière...</option>';
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

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    classSelect.addEventListener('change', function() {
        updateLevel();
        filterSubjectsByClass();
    });
    
    updateLevel();
    filterSubjectsByClass();
});
</script>

@endsection
