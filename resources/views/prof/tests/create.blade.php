@extends('layouts.prof')

@section('title', 'Créer un Test')
@section('page_title', 'Créer un Test')
@section('breadcrumb', 'Création de test')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-clipboard-check me-2" style="color:var(--adm-accent);"></i> Créer un nouveau test</h1>
        <div class="subtitle">Créez un QCM manuellement ou importez un fichier PDF/Word</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.tests.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

@if ($errors->any())
<div class="adm-alert adm-alert-danger mb-4">
    <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
    <span>
        <ul class="mb-0" style="list-style:none;padding-left:0;">
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </span>
</div>
@endif

<div class="row g-4">
    <!-- Manual Creation Form -->
    <div class="col-lg-7">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-pencil-square" style="color:rgba(255,255,255,0.35);"></i> Création manuelle</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('prof.tests.store') }}" enctype="multipart/form-data" id="testForm">
                    @csrf

                    <!-- Basic Info -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Titre du test <span style="color:var(--adm-danger);">*</span></label>
                                <input type="text" name="title" class="adm-form-control" placeholder="Ex: Évaluation Chapitre 3" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Matière <span style="color:var(--adm-danger);">*</span></label>
                                <select name="subject_id" class="adm-form-select" required>
                                    <option value="">Sélectionner une matière</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Durée (minutes) <span style="color:var(--adm-danger);">*</span></label>
                                <input type="number" name="duration" class="adm-form-control" value="30" min="1" required>
                            </div>
                        </div>
                    </div>

                    <!-- Questions Container -->
                    <div id="questions-container">
                        <div class="adm-form-group mb-3">
                            <label class="adm-form-label">Questions</label>
                            <p style="color:var(--adm-text-muted);font-size:0.8rem;margin:0;">Cliquez sur "Ajouter une question" pour commencer</p>
                        </div>
                    </div>

                    <!-- Add Question Button -->
                    <button type="button" id="add-question" class="adm-btn adm-btn-ghost adm-btn-sm mb-4">
                        <i class="bi bi-plus-circle me-1"></i> Ajouter une question
                    </button>

                    <div class="d-flex gap-3 pt-3 border-t" style="border-top:1px solid rgba(255,255,255,0.06);">
                        <a href="{{ route('prof.tests.index') }}" class="adm-btn adm-btn-ghost">
                            <i class="bi bi-x me-1"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-accent">
                            <i class="bi bi-check-circle me-1"></i> Créer le test
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AI Import Section -->
    <div class="col-lg-5">
        <div class="adm-card">
            <div class="adm-card-header" style="background:linear-gradient(135deg,rgba(124,58,237,0.2),rgba(167,139,250,0.1));">
                <h4><i class="bi bi-magic" style="color:rgba(255,255,255,0.35);"></i> Import automatique</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('prof.tests.store') }}" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div style="text-align:center;margin-bottom:1.5rem;">
                        <div style="width:72px;height:72px;border-radius:18px;background:linear-gradient(135deg,rgba(124,58,237,0.2),rgba(167,139,250,0.1));display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem;">
                            🤖
                        </div>
                        <h5 style="color:rgba(255,255,255,0.85);font-weight:600;">Génération par IA</h5>
                        <p style="color:var(--adm-text-muted);font-size:0.82rem;">
                            Importez un fichier PDF ou Word. Le système analysera le contenu et générera automatiquement un QCM.
                        </p>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du test <span style="color:var(--adm-danger);">*</span></label>
                        <input type="text" name="title" class="adm-form-control" placeholder="Ex: Test Chapitre 3 - Auto-généré" required>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Matière <span style="color:var(--adm-danger);">*</span></label>
                        <select name="subject_id" class="adm-form-select" required>
                            <option value="">Sélectionner une matière</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Durée (minutes) <span style="color:var(--adm-danger);">*</span></label>
                        <input type="number" name="duration" class="adm-form-control" value="30" min="1" required>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Fichier PDF ou Word</label>
                        <div style="border:2px dashed rgba(124,58,237,0.2);border-radius:12px;padding:1.5rem;text-align:center;transition:all 0.3s;">
                            <i class="bi bi-cloud-upload" style="font-size:2rem;color:rgba(124,58,237,0.4);display:block;margin-bottom:0.75rem;"></i>
                            <input type="file" name="file" class="adm-form-control" accept=".pdf,.doc,.docx">
                            <div style="color:var(--adm-text-muted);font-size:0.75rem;margin-top:0.5rem;">Formats acceptés : PDF, DOC, DOCX</div>
                        </div>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-accent w-100" style="padding:12px;">
                        <i class="bi bi-magic me-2"></i> Générer QCM depuis le fichier
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.question-group {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}
.question-group:hover {
    border-color: rgba(124,58,237,0.15);
    background: rgba(255,255,255,0.04);
}
.question-group .q-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}
.question-group .q-number {
    font-weight: 600;
    color: var(--adm-accent);
    font-size: 0.85rem;
}
.answer-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}
.answer-row input[type="text"] {
    flex: 1;
}
.answer-row input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #7C3AED;
    cursor: pointer;
}
.correct-badge {
    font-size: 0.65rem;
    background: rgba(22,163,74,0.15);
    color: #4ADE80;
    padding: 2px 8px;
    border-radius: 20px;
}
</style>

<script>
let questionIndex = 0;

document.getElementById('add-question')?.addEventListener('click', function() {
    const html = `
        <div class="question-group" data-index="${questionIndex}">
            <div class="q-header">
                <span class="q-number"><i class="bi bi-question-circle me-1"></i> Question ${questionIndex + 1}</span>
                <button type="button" class="remove-question adm-btn adm-btn-danger adm-btn-sm" style="padding:4px 10px;font-size:0.72rem;">
                    <i class="bi bi-trash me-1"></i> Supprimer
                </button>
            </div>
            <div class="adm-form-group">
                <input type="text" name="questions[${questionIndex}][question]" class="adm-form-control" placeholder="Saisissez votre question..." required>
            </div>
            <div class="answer-group">
                <label class="adm-form-label" style="font-size:0.75rem;color:var(--adm-text-muted);">Réponses <span style="color:var(--adm-text-muted);font-weight:400;">(cochez la/les bonne(s) réponse(s))</span></label>
                ${[0,1,2,3].map(i => `
                    <div class="answer-row">
                        <input type="text" name="questions[${questionIndex}][answers][${i}][answer]" class="adm-form-control" placeholder="Réponse ${String.fromCharCode(65 + i)}" required>
                        <input type="checkbox" name="questions[${questionIndex}][answers][${i}][is_correct]" value="1" title="Bonne réponse">
                        <span style="font-size:0.7rem;color:var(--adm-text-muted);">correcte</span>
                    </div>
                `).join('')}
            </div>
            <button type="button" class="add-answer adm-btn adm-btn-ghost adm-btn-sm" style="margin-top:6px;">
                <i class="bi bi-plus-circle me-1"></i> Ajouter une réponse
            </button>
        </div>
    `;
    document.getElementById('questions-container').insertAdjacentHTML('beforeend', html);
    questionIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-question')) {
        e.target.closest('.question-group').remove();
    }
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.add-answer')) {
        const group = e.target.closest('.question-group');
        const answerGroup = group.querySelector('.answer-group');
        const existing = answerGroup.querySelectorAll('.answer-row').length;
        const qIndex = group.dataset.index;
        const letter = String.fromCharCode(65 + existing);
        const html = `
            <div class="answer-row">
                <input type="text" name="questions[${qIndex}][answers][${existing}][answer]" class="adm-form-control" placeholder="Réponse ${letter}" required>
                <input type="checkbox" name="questions[${qIndex}][answers][${existing}][is_correct]" value="1" title="Bonne réponse">
                <span style="font-size:0.7rem;color:var(--adm-text-muted);">correcte</span>
            </div>
        `;
        answerGroup.insertAdjacentHTML('beforeend', html);
    }
});
</script>

@endsection
