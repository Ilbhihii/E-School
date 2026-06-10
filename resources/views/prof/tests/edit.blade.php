@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:800px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Modifier: {{ $test->title }}</span></h1>
                <p class="admin-header-subtitle">Mettez à jour votre test</p>
            </div>
            <a href="{{ route('prof.tests.index') }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <!-- BASIC INFO FORM -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('prof.tests.update', $test) }}">
                    @csrf @method('PUT')
                    <div class="adm-grid-3">
                        <div class="adm-form-group">
                            <label class="adm-form-label">Titre</label>
                            <input type="text" name="title" value="{{ old('title', $test->title) }}" class="adm-form-input" required>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Matière</label>
                            <select name="subject_id" class="adm-form-select" required>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $test->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Durée (minutes)</label>
                            <input type="number" name="duration" value="{{ old('duration', $test->duration) }}" class="adm-form-input" min="1" required>
                        </div>
                    </div>
                    <button type="submit" class="adm-btn adm-btn-primary">Mettre à jour les infos</button>
                </form>
            </div>
        </div>

        <!-- AI REGENERATE -->
        <div class="adm-card adm-mb-3" style="background:#f0fdf4;border:2px dashed #86efac;backdrop-filter:none;">
            <div class="adm-card-header">
                <h3 style="color:#166534;">🤖 Régénérer QCM avec IA</h3>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('prof.tests.generate-ai', $test) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="adm-form-group">
                        <label class="adm-form-label">Nouveau fichier PDF / Word</label>
                        <input type="file" name="file" class="adm-form-input" accept=".pdf,.doc,.docx" required>
                        <small style="color:var(--adm-text-secondary);">Remplace toutes les questions par QCM généré automatiquement.</small>
                    </div>
                    <button type="submit" class="adm-btn adm-btn-success" onclick="return confirm('⚠️ Cela effacera toutes les questions existantes!')">
                        <i class="bi bi-magic"></i> Régénérer QCM IA
                    </button>
                </form>
            </div>
        </div>

        <!-- MANUAL QUESTIONS -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3>Questions manuelles ({{ $test->questions->count() }} existantes)</h3>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('prof.tests.update', $test) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    @foreach($test->questions as $index => $question)
                    <div class="question-group adm-card adm-mb-2" style="backdrop-filter:none;border:1px solid var(--adm-border);" data-q-index="{{ $question->id }}">
                        <div class="adm-card-body" style="padding:1rem;">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Question {{ $index + 1 }}</label>
                                <input type="text" name="questions[{{ $question->id }}][question]" value="{{ old('questions.' . $question->id . '.question', $question->question) }}" class="adm-form-input" required>
                            </div>
                            <div class="answer-group">
                                @foreach($question->answers as $aIndex => $answer)
                                <div class="adm-flex adm-gap-1 adm-mb-1" style="align-items:center;">
                                    <input type="text" name="questions[{{ $question->id }}][answers][{{ $aIndex }}][answer]" value="{{ old('questions.' . $question->id . '.answers.' . $aIndex . '.answer', $answer->answer) }}" placeholder="Réponse {{ chr(65 + $aIndex) }}" class="adm-form-input" style="flex:1;" required>
                                    <label style="display:flex;align-items:center;gap:0.3rem;white-space:nowrap;font-size:0.85rem;">
                                        <input type="checkbox" name="questions[{{ $question->id }}][answers][{{ $aIndex }}][is_correct]" value="1" {{ $answer->is_correct ? 'checked' : '' }}> Correcte
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="adm-btn adm-btn-sm adm-btn-danger remove-question" style="margin-top:0.5rem;">
                                <i class="bi bi-trash-fill"></i> Supprimer
                            </button>
                        </div>
                    </div>
                    @endforeach

                    <div id="questions-container"></div>

                    <div class="adm-flex adm-gap-2 adm-mt-2">
                        <button type="button" id="add-question" class="adm-btn adm-btn-ghost">
                            <i class="bi bi-plus-lg"></i> Ajouter question
                        </button>
                        <button type="submit" class="adm-btn adm-btn-primary" style="margin-left:auto;">
                            <i class="bi bi-save-fill"></i> Mettre à jour le test
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
let questionIndex = {{ $test->questions->count() }};

document.getElementById('add-question')?.addEventListener('click', function() {
    const container = document.getElementById('questions-container');
    const html = `
        <div class="question-group adm-card adm-mb-2" style="backdrop-filter:none;border:1px solid var(--adm-border);">
            <div class="adm-card-body" style="padding:1rem;">
                <div class="adm-form-group">
                    <label class="adm-form-label">Nouvelle Question ${questionIndex + 1}</label>
                    <input type="text" name="new_questions[${questionIndex}][question]" placeholder="Question ${questionIndex + 1}" class="adm-form-input" required>
                </div>
                <div class="answer-group">
                    ${['A','B','C','D'].map((letter, i) => `
                        <div class="adm-flex adm-gap-1 adm-mb-1" style="align-items:center;">
                            <input type="text" name="new_questions[${questionIndex}][answers][${i}][answer]" placeholder="Réponse ${letter}" class="adm-form-input" style="flex:1;" required>
                            <label style="display:flex;align-items:center;gap:0.3rem;white-space:nowrap;font-size:0.85rem;">
                                <input type="checkbox" name="new_questions[${questionIndex}][answers][${i}][is_correct]" value="1"> Correcte
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
