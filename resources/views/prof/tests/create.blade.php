@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:800px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Créer un nouveau test</span></h1>
                <p class="admin-header-subtitle">Créez un QCM pour évaluer vos étudiants</p>
            </div>
        </div>

        <div class="adm-card adm-mb-3">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('prof.tests.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="adm-grid-3">
                        <div class="adm-form-group">
                            <label class="adm-form-label">Titre</label>
                            <input type="text" name="title" class="adm-form-input" required>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Matière</label>
                            <select name="subject_id" class="adm-form-select" required>
                                <option value="">Sélectionner</option>
                                @foreach($subjects->unique('name') as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Durée (minutes)</label>
                            <input type="number" name="duration" class="adm-form-input" value="30" min="1" required>
                        </div>
                    </div>

                    <!-- FILE IMPORT -->
                    <div class="adm-card" style="background:#f0fdf4;border:2px dashed #86efac;backdrop-filter:none;">
                        <div class="adm-card-header">
                            <h3 style="color:#166534;">📄 Importer PDF / Word (Auto-génère QCM)</h3>
                        </div>
                        <div class="adm-card-body">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Fichier PDF ou Word</label>
                                <input type="file" name="file" class="adm-form-input" accept=".pdf,.doc,.docx">
                                <small style="color:var(--adm-text-secondary);">Le système lit le contenu, détecte les questions et crée le QCM automatiquement.</small>
                            </div>
                            <button type="submit" class="adm-btn adm-btn-success adm-btn-lg" style="width:100%;">
                                <i class="bi bi-magic"></i> Générer QCM depuis fichier
                            </button>
                        </div>
                    </div>

                    <hr class="adm-mb-3 adm-mt-3">

                    <div id="questions-container"></div>

                    <div class="adm-flex adm-gap-2">
                        <button type="button" id="add-question" class="adm-btn adm-btn-ghost">
                            <i class="bi bi-plus-lg"></i> Ajouter Question
                        </button>
                        <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg" style="margin-left:auto;">
                            <i class="bi bi-check-circle"></i> Créer Test Manuel
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
let questionIndex = 1;

document.getElementById('add-question').addEventListener('click', function() {
    const container = document.getElementById('questions-container');
    const html = `
        <div class="question-group adm-card adm-mb-2" style="backdrop-filter:none;border:1px solid var(--adm-border);">
            <div class="adm-card-body" style="padding:1.25rem;">
                <div class="adm-form-group">
                    <label class="adm-form-label">Question ${questionIndex}</label>
                    <input type="text" name="questions[${questionIndex}][question]" placeholder="Question ${questionIndex}" class="adm-form-input" required>
                </div>
                <div class="answer-group">
                    ${['A','B','C','D'].map((letter, i) => `
                        <div class="adm-flex adm-gap-1 adm-mb-1" style="align-items:center;">
                            <input type="text" name="questions[${questionIndex}][answers][${i}][answer]" placeholder="Réponse ${letter}" class="adm-form-input" style="flex:1;" required>
                            <label style="display:flex;align-items:center;gap:0.3rem;white-space:nowrap;font-size:0.85rem;">
                                <input type="checkbox" name="questions[${questionIndex}][answers][${i}][is_correct]" value="1"> Correcte
                            </label>
                        </div>
                    `).join('')}
                </div>
                <button type="button" class="adm-btn adm-btn-sm adm-btn-danger remove-question" style="margin-top:0.5rem;">
                    <i class="bi bi-trash-fill"></i> Supprimer
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    questionIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-question')) {
        e.target.closest('.question-group').remove();
    }
});
</script>
@endsection
