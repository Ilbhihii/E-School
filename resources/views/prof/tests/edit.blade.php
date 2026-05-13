@extends('layouts.prof')

@section('title', 'Modifier ' . $test->title)

@section('content')
<div class="container">
    <h1>Modifier le test: {{ $test->title }}</h1>
    <a href="{{ route('prof.tests.index') }}" class="btn btn-secondary mb-3">← Retour à la liste</a>

    {{-- Basic Info Form --}}
    <form method="POST" action="{{ route('prof.tests.update', $test) }}" class="mb-5">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label>Titre</label>
                    <input type="text" name="title" value="{{ old('title', $test->title) }}" class="form-control" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label>Matière</label>
                    <select name="subject_id" class="form-control" required>
                        <option value="">Sélectionner une matière</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $test->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label>Durée (minutes)</label>
                    <input type="number" name="duration" value="{{ old('duration', $test->duration) }}" class="form-control" min="1" required>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour les infos</button>
    </form>

    <hr>

    {{-- AI Regenerate Section --}}
    <div class="mb-4 p-4 border rounded bg-light">
        <h4>🤖 Régénérer QCM avec IA (efface questions existantes)</h4>       <form method="POST" action="{{ route('prof.tests.generate-ai', $test) }}" enctype="multipart/form-data" class="d-inline">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Nouveau fichier PDF / Word</label>
                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx" required>
                <div class="form-text">Remplace toutes les questions par QCM généré automatiquement.</div>
            </div>
            <button type="submit" class="btn btn-success" onclick="return confirm('⚠️ Cela effacera toutes les questions existantes!')">
                <i class="bi bi-magic"></i> Régénérer QCM IA
            </button>
        </form>
    </div>

    <hr>

    {{-- Manual Questions Management --}}
    <h4>Questions manuelles ({{ $test->questions->count() }} existantes)</h4>
    <form method="POST" action="{{ route('prof.tests.update', $test) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        {{-- Existing Questions --}}
        @foreach($test->questions as $index => $question)
        <div class="question-group mb-4 p-3 border rounded" data-q-index="{{ $question->id }}">
            <div class="mb-3">
                <label>Question {{ $index + 1 }}</label>
                <input type="text" name="questions[{{ $question->id }}][question]" value="{{ old('questions.' . $question->id . '.question', $question->question) }}" class="form-control" required>
            </div>
            <div class="answer-group mb-2">
                @foreach($question->answers as $aIndex => $answer)
                <div class="input-group mb-2">
                    <input type="text" name="questions[{{ $question->id }}][answers][{{ $aIndex }}][answer]" 
                           value="{{ old('questions.' . $question->id . '.answers.' . $aIndex . '.answer', $answer->answer) }}" 
                           placeholder="Réponse {{ chr(65 + $aIndex) }}" class="form-control" required>
                    <div class="input-group-text">
                        <input type="checkbox" name="questions[{{ $question->id }}][answers][{{ $aIndex }}][is_correct]" 
                               value="1" {{ old('questions.' . $question->id . '.answers.' . $aIndex . '.is_correct', $answer->is_correct) ? 'checked' : '' }}>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-sm btn-danger remove-question">Supprimer question</button>
            <button type="button" class="btn btn-sm btn-success add-answer">Ajouter réponse</button>
        </div>
        @endforeach

        {{-- New Questions Container --}}
        <div id="questions-container"></div>
        <button type="button" id="add-question" class="btn btn-secondary mb-3">Ajouter nouvelle question</button>
        <button type="submit" class="btn btn-primary mb-3">Mettre à jour test complet</button>
    </form>
</div>

<script>
let questionIndex = {{ $test->questions->count() }};  // Start after existing questions

// Existing create.js functionality
$('#add-question').click(function() {
    const html = `
        <div class="question-group mb-4 p-3 border rounded">
            <div class="mb-3">
                <label>Nouvelle Question ${questionIndex + 1}</label>
                <input type="text" name="new_questions[${questionIndex}][question]" placeholder="Question ${questionIndex + 1}" class="form-control" required>
            </div>
            <div class="answer-group mb-2">
                <div class="input-group mb-2">
                    <input type="text" name="new_questions[${questionIndex}][answers][0][answer]" placeholder="Réponse A" class="form-control" required>
                    <div class="input-group-text">
                        <input type="checkbox" name="new_questions[${questionIndex}][answers][0][is_correct]" value="1">
                    </div>
                </div>
                <div class="input-group mb-2">
                    <input type="text" name="new_questions[${questionIndex}][answers][1][answer]" placeholder="Réponse B" class="form-control" required>
                    <div class="input-group-text">
                        <input type="checkbox" name="new_questions[${questionIndex}][answers][1][is_correct]" value="1">
                    </div>
                </div>
                <div class="input-group mb-2">
                    <input type="text" name="new_questions[${questionIndex}][answers][2][answer]" placeholder="Réponse C" class="form-control" required>
                    <div class="input-group-text">
                        <input type="checkbox" name="new_questions[${questionIndex}][answers][2][is_correct]" value="1">
                    </div>
                </div>
                <div class="input-group mb-2">
                    <input type="text" name="new_questions[${questionIndex}][answers][3][answer]" placeholder="Réponse D" class="form-control" required>
                    <div class="input-group-text">
                        <input type="checkbox" name="new_questions[${questionIndex}][answers][3][is_correct]" value="1">
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger remove-question">Supprimer question</button>
            <button type="button" class="btn btn-sm btn-success add-answer">Ajouter réponse</button>
        </div>
    `;
    $('#questions-container').append(html);
    questionIndex++;
});

$(document).on('click', '.remove-question', function() {
    $(this).closest('.question-group').remove();
});

$(document).on('click', '.add-answer', function() {
    const group = $(this).closest('.question-group');
    const answerInputs = group.find('.answer-group .input-group input[name*="[answer]"]');
    const answerIndex = answerInputs.length;
    const qNameMatch = group.find('input[name*="[question]"]').attr('name').match(/\[(\d+)\]/);
    const qIndex = qNameMatch ? qNameMatch[1] : 'new_' + questionIndex;
    const html = `
        <div class="input-group mb-2">
            <input type="text" name="${group.find('input[name*="[question]"]').attr('name').replace('[question]', '[answers][' + answerIndex + '][answer]')}" 
                   placeholder="Réponse ${String.fromCharCode(65 + answerIndex)}" class="form-control" required>
            <div class="input-group-text">
                <input type="checkbox" name="${group.find('input[name*="[question]"]').attr('name').replace('[question]', '[answers][' + answerIndex + '][is_correct]')}" value="1">
            </div>
        </div>
    `;
    group.find('.answer-group').append(html);
});
</script>
@endsection

