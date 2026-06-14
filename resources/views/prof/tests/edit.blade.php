@extends('layouts.prof')

@section('title', 'Modifier ' . $test->title)
@section('page_title', 'Modifier le test')
@section('breadcrumb', 'Édition de test')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-pencil-square me-2" style="color:var(--adm-accent);"></i> {{ $test->title }}</h1>
        <div class="subtitle">Modifiez les informations et les questions du test</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.tests.show', $test) }}" class="adm-btn adm-btn-success adm-btn-sm">
            <i class="bi bi-bar-chart me-1"></i> Voir résultats
        </a>
        <a href="{{ route('prof.tests.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

<!-- Basic Info Form -->
<div class="adm-card mb-4">
    <div class="adm-card-header">
        <h4><i class="bi bi-info-circle" style="color:rgba(255,255,255,0.35);"></i> Informations générales</h4>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="{{ route('prof.tests.update', $test) }}" class="row g-3">
            @csrf @method('PUT')
            <div class="col-md-4">
                <div class="adm-form-group">
                    <label class="adm-form-label">Titre</label>
                    <input type="text" name="title" value="{{ old('title', $test->title) }}" class="adm-form-control" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="adm-form-group">
                    <label class="adm-form-label">Matière</label>
                    <select name="subject_id" class="adm-form-select" required>
                        <option value="">Sélectionner</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $test->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="adm-form-group">
                    <label class="adm-form-label">Durée (minutes)</label>
                    <input type="number" name="duration" value="{{ old('duration', $test->duration) }}" class="adm-form-control" min="1" required>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">
                    <i class="bi bi-save me-1"></i> Mettre à jour les infos
                </button>
            </div>
        </form>
    </div>
</div>

<!-- AI Regenerate Section -->
<div class="adm-card mb-4" style="border-color:rgba(124,58,237,0.15);">
    <div class="adm-card-header" style="background:linear-gradient(135deg,rgba(124,58,237,0.15),rgba(167,139,250,0.08));">
        <h4><i class="bi bi-magic" style="color:rgba(255,255,255,0.35);"></i> Régénération par IA</h4>
    </div>
    <div class="adm-card-body">
        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <p style="color:var(--adm-text-muted);font-size:0.85rem;margin:0;">Importez un nouveau fichier pour régénérer automatiquement toutes les questions.<br>
                <strong style="color:#FCA5A5;">⚠️ Cela effacera toutes les questions existantes.</strong></p>
            </div>
            <form method="POST" action="{{ route('prof.tests.generate-ai', $test) }}" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
                @csrf
                <div class="adm-form-group" style="margin:0;">
                    <input type="file" name="file" class="adm-form-control" accept=".pdf,.doc,.docx" required style="max-width:250px;">
                </div>
                <button type="submit" class="adm-btn adm-btn-accent adm-btn-sm" onclick="return confirm('⚠️ Cela supprimera toutes les questions existantes et les remplacera. Continuer ?')">
                    <i class="bi bi-magic me-1"></i> Régénérer
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Questions Management -->
<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-list-check" style="color:rgba(255,255,255,0.35);"></i> Questions ({{ $test->questions->count() }})</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $test->questions->count() }} question(s)</span>
        </div>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="{{ route('prof.tests.update', $test) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div id="existing-questions">
                @foreach($test->questions as $index => $question)
                <div class="question-group" data-q-id="{{ $question->id }}">
                    <div class="q-header">
                        <span class="q-number"><i class="bi bi-question-circle me-1"></i> Question {{ $index + 1 }}</span>
                        <button type="button" class="remove-question adm-btn adm-btn-danger adm-btn-sm" style="padding:4px 10px;font-size:0.72rem;">
                            <i class="bi bi-trash me-1"></i> Supprimer
                        </button>
                    </div>
                    <div class="adm-form-group">
                        <input type="text" name="questions[{{ $question->id }}][question]" value="{{ old('questions.' . $question->id . '.question', $question->question) }}" class="adm-form-control" required>
                    </div>
                    <div class="answer-group">
                        <label class="adm-form-label" style="font-size:0.75rem;color:var(--adm-text-muted);">Réponses (cochez la/les bonne(s))</label>
                        @foreach($question->answers as $aIndex => $answer)
                        <div class="answer-row">
                            <input type="text" name="questions[{{ $question->id }}][answers][{{ $aIndex }}][answer]" value="{{ old('questions.' . $question->id . '.answers.' . $aIndex . '.answer', $answer->answer) }}" class="adm-form-control" placeholder="Réponse {{ chr(65 + $aIndex) }}" required>
                            <input type="checkbox" name="questions[{{ $question->id }}][answers][{{ $aIndex }}][is_correct]" value="1" {{ old('questions.' . $question->id . '.answers.' . $aIndex . '.is_correct', $answer->is_correct) ? 'checked' : '' }}>
                            <span style="font-size:0.7rem;color:var(--adm-text-muted);">correcte</span>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="add-existing-answer adm-btn adm-btn-ghost adm-btn-sm" style="margin-top:6px;" data-q-id="{{ $question->id }}">
                        <i class="bi bi-plus-circle me-1"></i> Ajouter une réponse
                    </button>
                </div>
                @endforeach
            </div>

            <div id="questions-container" class="mt-3"></div>

            <div class="d-flex gap-3 mt-4" style="border-top:1px solid rgba(255,255,255,0.06);padding-top:1rem;">
                <button type="button" id="add-question" class="adm-btn adm-btn-ghost adm-btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Ajouter une nouvelle question
                </button>
                <button type="submit" class="adm-btn adm-btn-accent adm-btn-sm" style="margin-left:auto;">
                    <i class="bi bi-save me-1"></i> Enregistrer toutes les modifications
                </button>
            </div>
        </form>
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
</style>

<script>
let questionIndex = {{ $test->questions->count() }};

document.getElementById('add-question')?.addEventListener('click', function() {
    const html = `
        <div class="question-group" data-index="${questionIndex}">
            <div class="q-header">
                <span class="q-number"><i class="bi bi-question-circle me-1"></i> Nouvelle question ${questionIndex + 1}</span>
                <button type="button" class="remove-question adm-btn adm-btn-danger adm-btn-sm" style="padding:4px 10px;font-size:0.72rem;">
                    <i class="bi bi-trash me-1"></i> Supprimer
                </button>
            </div>
            <div class="adm-form-group">
                <input type="text" name="new_questions[${questionIndex}][question]" class="adm-form-control" placeholder="Saisissez votre question..." required>
            </div>
            <div class="answer-group">
                <label class="adm-form-label" style="font-size:0.75rem;color:var(--adm-text-muted);">Réponses (cochez la/les bonne(s))</label>
                ${[0,1,2,3].map(i => `
                    <div class="answer-row">
                        <input type="text" name="new_questions[${questionIndex}][answers][${i}][answer]" class="adm-form-control" placeholder="Réponse ${String.fromCharCode(65 + i)}" required>
                        <input type="checkbox" name="new_questions[${questionIndex}][answers][${i}][is_correct]" value="1">
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
                <input type="text" name="new_questions[${qIndex}][answers][${existing}][answer]" class="adm-form-control" placeholder="Réponse ${letter}" required>
                <input type="checkbox" name="new_questions[${qIndex}][answers][${existing}][is_correct]" value="1">
                <span style="font-size:0.7rem;color:var(--adm-text-muted);">correcte</span>
            </div>
        `;
        answerGroup.insertAdjacentHTML('beforeend', html);
    }
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.add-existing-answer')) {
        const btn = e.target.closest('.add-existing-answer');
        const group = btn.closest('.question-group');
        const qId = btn.dataset.qId;
        const answerGroup = group.querySelector('.answer-group');
        const existing = answerGroup.querySelectorAll('.answer-row').length;
        const letter = String.fromCharCode(65 + existing);
        const html = `
            <div class="answer-row">
                <input type="text" name="questions[${qId}][answers][${existing}][answer]" class="adm-form-control" placeholder="Réponse ${letter}" required>
                <input type="checkbox" name="questions[${qId}][answers][${existing}][is_correct]" value="1">
                <span style="font-size:0.7rem;color:var(--adm-text-muted);">correcte</span>
            </div>
        `;
        answerGroup.insertAdjacentHTML('beforeend', html);
    }
});
</script>

@endsection
