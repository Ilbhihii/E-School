@extends('layouts.admin')

@section('title', 'Créer un test')
@section('page_title', 'Nouveau test')
@section('breadcrumb', 'Évaluations → Tests → Nouveau')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-11">
        <form
            method="POST"
            action="{{ route('admin.flexible-tests.store') }}"
            enctype="multipart/form-data"
            id="flexibleTestForm"
        >
            @csrf

            <div class="adm-card mb-4">
                <div class="adm-card-header">
                    <h4>
                        <i class="bi bi-diagram-3-fill me-2"></i>
                        1. Structure pédagogique
                    </h4>
                    <p class="mb-0 text-secondary">
                        Matière active → Niveau → Classe
                    </p>
                </div>

                <div class="adm-card-body">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <label class="adm-form-label" for="subject_id">Matière *</label>
                            <select id="subject_id" name="subject_id" class="adm-form-select" required>
                                <option value="">Choisir une matière active</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4">
                            <label class="adm-form-label" for="level_id">Niveau *</label>
                            <select id="level_id" name="level_id" class="adm-form-select" required>
                                <option value="">Choisir un niveau</option>
                                @foreach($levels as $level)
                                    <option
                                        value="{{ $level->id }}"
                                        data-subject="{{ $level->subject_id }}"
                                        {{ old('level_id') == $level->id ? 'selected' : '' }}
                                    >
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4">
                            <label class="adm-form-label" for="class_id">Classe *</label>
                            <select id="class_id" name="class_id" class="adm-form-select" required>
                                <option value="">Choisir une classe</option>
                                @foreach($classes as $classRoom)
                                    <option
                                        value="{{ $classRoom->id }}"
                                        data-level="{{ $classRoom->level_id }}"
                                        {{ old('class_id') == $classRoom->id ? 'selected' : '' }}
                                    >
                                        {{ $classRoom->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="adm-card mb-4">
                <div class="adm-card-header">
                    <h4>
                        <i class="bi bi-file-earmark-richtext-fill me-2"></i>
                        2. Support du sujet
                    </h4>
                    <p class="mb-0 text-secondary">
                        Texte, dossier d'images/documents ou les deux.
                    </p>
                </div>

                <div class="adm-card-body">
                    <div class="mb-3">
                        <label class="adm-form-label" for="title">Titre *</label>
                        <input
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            class="adm-form-control"
                            placeholder="Ex : Compréhension — Anglais C1"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="adm-form-label" for="instructions">Consignes</label>
                        <textarea
                            id="instructions"
                            name="instructions"
                            rows="3"
                            class="adm-form-control adm-form-textarea"
                            placeholder="Expliquez ce que l'étudiant doit faire..."
                        >{{ old('instructions') }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-4">
                            <label class="adm-form-label" for="source_type">Support *</label>
                            <select id="source_type" name="source_type" class="adm-form-select" required>
                                <option value="text">Texte</option>
                                <option value="files">Images / PDF / DOCX</option>
                                <option value="mixed">Texte + fichiers</option>
                            </select>
                        </div>

                        <div class="col-lg-8" id="sourceFilesBox">
                            <label class="adm-form-label" for="source_files">
                                Images / documents
                            </label>
                            <input
                                id="source_files"
                                type="file"
                                name="source_files[]"
                                class="adm-form-control"
                                accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.doc,.docx,.txt"
                                multiple
                            >
                            <small class="text-secondary">
                                Jusqu'à 30 fichiers, 100 Mo maximum par fichier.
                            </small>
                        </div>
                    </div>

                    <div class="mt-3" id="sourceTextBox">
                        <label class="adm-form-label" for="source_text">Texte du sujet</label>
                        <textarea
                            id="source_text"
                            name="source_text"
                            rows="7"
                            class="adm-form-control adm-form-textarea"
                            placeholder="Collez ou écrivez le texte du sujet..."
                        >{{ old('source_text') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="adm-card mb-4">
                <div class="adm-card-header">
                    <h4>
                        <i class="bi bi-ui-checks-grid me-2"></i>
                        3. Type de test / réponse
                    </h4>
                    <p class="mb-0 text-secondary">
                        Vocal, écrit ou choix multiple.
                    </p>
                </div>

                <div class="adm-card-body">
                    <div class="mb-3">
                        <label class="adm-form-label" for="response_type">Type de test *</label>
                        <select id="response_type" name="response_type" class="adm-form-select" required>
                            <option value="vocal">🎙 Vocal</option>
                            <option value="written">✍️ Écrit</option>
                            <option value="qcm">☑️ QCM / choix multiple</option>
                        </select>
                    </div>

                    <div id="vocalOptions" class="test-option-panel">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="adm-form-label" for="vocal_mode">Mode vocal</label>
                                <select id="vocal_mode" name="vocal_mode" class="adm-form-select">
                                    <option value="reading">Lecture</option>
                                    <option value="tajwid">Tajwid</option>
                                    <option value="hifd">Hifd / mémorisation</option>
                                    <option value="free">Expression libre</option>
                                </select>
                            </div>

                            <div class="col-lg-3">
                                <label class="adm-form-label" for="preparation_seconds">Préparation</label>
                                <input
                                    id="preparation_seconds"
                                    type="number"
                                    name="preparation_seconds"
                                    value="{{ old('preparation_seconds', 0) }}"
                                    min="0"
                                    max="600"
                                    class="adm-form-control"
                                >
                            </div>

                            <div class="col-lg-3">
                                <label class="adm-form-label" for="maximum_duration">Durée max.</label>
                                <input
                                    id="maximum_duration"
                                    type="number"
                                    name="maximum_duration"
                                    value="{{ old('maximum_duration', 120) }}"
                                    min="15"
                                    max="7200"
                                    class="adm-form-control"
                                >
                            </div>
                        </div>
                    </div>

                    <div id="writtenOptions" class="test-option-panel" hidden>
                        <label class="adm-form-label" for="written_response_mode">
                            Réponse écrite autorisée
                        </label>

                        <select
                            id="written_response_mode"
                            name="written_response_mode"
                            class="adm-form-select"
                        >
                            <option value="text">Zone de texte</option>
                            <option value="file">Image / PDF / DOCX envoyé par l'étudiant</option>
                            <option value="both">Zone de texte + fichier</option>
                        </select>
                    </div>

                    <div id="qcmOptions" class="test-option-panel" hidden>
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                            <div>
                                <strong>Questions QCM</strong>
                                <div class="small text-secondary">
                                    4 choix par question, une bonne réponse.
                                </div>
                            </div>

                            <button type="button" class="adm-btn adm-btn-ghost" id="addQuestion">
                                <i class="bi bi-plus-lg"></i>
                                Ajouter une question
                            </button>
                        </div>

                        <div id="questionsContainer"></div>
                    </div>

                    <div class="mt-4 form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="is_active"
                            value="1"
                            id="is_active"
                            checked
                        >
                        <label class="form-check-label" for="is_active">
                            Test actif
                        </label>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Le test n'a pas été enregistré.</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex justify-content-between gap-3">
                <a href="{{ route('admin.flexible-tests.index') }}" class="adm-btn adm-btn-ghost">
                    Annuler
                </a>

                <button type="submit" class="adm-btn adm-btn-primary">
                    <i class="bi bi-check2-circle"></i>
                    Créer le test
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.test-option-panel {
    padding: 16px;
    border: 1px solid rgba(148,163,184,.13);
    border-radius: 14px;
    background: rgba(15,23,42,.38);
}
.qcm-question-card {
    margin-bottom: 14px;
    padding: 16px;
    border: 1px solid rgba(148,163,184,.13);
    border-radius: 14px;
    background: rgba(15,23,42,.34);
}
.qcm-question-head {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin-bottom:12px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const subject = document.getElementById('subject_id');
    const level = document.getElementById('level_id');
    const classSelect = document.getElementById('class_id');
    const sourceType = document.getElementById('source_type');
    const sourceTextBox = document.getElementById('sourceTextBox');
    const sourceFilesBox = document.getElementById('sourceFilesBox');
    const responseType = document.getElementById('response_type');
    const vocalOptions = document.getElementById('vocalOptions');
    const writtenOptions = document.getElementById('writtenOptions');
    const qcmOptions = document.getElementById('qcmOptions');
    const questionsContainer = document.getElementById('questionsContainer');
    const addQuestion = document.getElementById('addQuestion');

    const levelOptions = Array.from(level.options).map(o => o.cloneNode(true));
    const classOptions = Array.from(classSelect.options).map(o => o.cloneNode(true));

    function refillLevels() {
        const current = level.value;
        level.innerHTML = '';
        level.appendChild(levelOptions[0].cloneNode(true));

        levelOptions.slice(1).forEach(option => {
            if (!subject.value || String(option.dataset.subject) === String(subject.value)) {
                level.appendChild(option.cloneNode(true));
            }
        });

        if (Array.from(level.options).some(o => o.value === current)) {
            level.value = current;
        } else {
            level.value = '';
        }

        refillClasses();
    }

    function refillClasses() {
        const current = classSelect.value;
        classSelect.innerHTML = '';
        classSelect.appendChild(classOptions[0].cloneNode(true));

        classOptions.slice(1).forEach(option => {
            if (!level.value || String(option.dataset.level) === String(level.value)) {
                classSelect.appendChild(option.cloneNode(true));
            }
        });

        if (Array.from(classSelect.options).some(o => o.value === current)) {
            classSelect.value = current;
        } else {
            classSelect.value = '';
        }
    }

    function updateSource() {
        const type = sourceType.value;
        sourceTextBox.hidden = type === 'files';
        sourceFilesBox.hidden = type === 'text';
    }

    function updateResponse() {
        vocalOptions.hidden = responseType.value !== 'vocal';
        writtenOptions.hidden = responseType.value !== 'written';
        qcmOptions.hidden = responseType.value !== 'qcm';

        if (responseType.value === 'qcm' && questionsContainer.children.length === 0) {
            addQcmQuestion();
        }
    }

    function addQcmQuestion() {
        const index = questionsContainer.children.length;

        const card = document.createElement('div');
        card.className = 'qcm-question-card';

        card.innerHTML = `
            <div class="qcm-question-head">
                <strong>Question ${index + 1}</strong>
                <button type="button" class="adm-btn adm-btn-ghost btn-remove-question">
                    Supprimer
                </button>
            </div>

            <div class="mb-3">
                <label class="adm-form-label">Énoncé</label>
                <textarea
                    name="questions[${index}][prompt]"
                    rows="2"
                    class="adm-form-control adm-form-textarea"
                    placeholder="Écrivez la question..."
                ></textarea>
            </div>

            <div class="row g-2">
                ${['a','b','c','d'].map(letter => `
                    <div class="col-md-6">
                        <label class="adm-form-label">Choix ${letter.toUpperCase()}</label>
                        <input
                            name="questions[${index}][choice_${letter}]"
                            class="adm-form-control"
                            placeholder="Réponse ${letter.toUpperCase()}"
                        >
                    </div>
                `).join('')}
            </div>

            <div class="mt-3">
                <label class="adm-form-label">Bonne réponse</label>
                <select name="questions[${index}][correct]" class="adm-form-select">
                    <option value="a">A</option>
                    <option value="b">B</option>
                    <option value="c">C</option>
                    <option value="d">D</option>
                </select>
            </div>
        `;

        card.querySelector('.btn-remove-question').addEventListener('click', function () {
            card.remove();
        });

        questionsContainer.appendChild(card);
    }

    subject.addEventListener('change', refillLevels);
    level.addEventListener('change', refillClasses);
    sourceType.addEventListener('change', updateSource);
    responseType.addEventListener('change', updateResponse);
    addQuestion.addEventListener('click', addQcmQuestion);

    refillLevels();
    updateSource();
    updateResponse();
});
</script>
@endpush