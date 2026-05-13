@extends('layouts.prof')

@section('title', 'Créer un Test')

@section('content')
<div class="container">
    <h1>Créer un nouveau test</h1>

    <form method="POST" action="{{ route('prof.tests.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Titre</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Matière</label>
                <select name="subject_id" class="form-control" required>
                    <option value="">Sélectionner une matière</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
        </div>

        <div class="mb-3">
            <label>Durée (minutes)</label>
            <input type="number" name="duration" class="form-control" value="30" min="1" required>
        </div>

        {{-- File Upload Section --}}
        <div class="mb-4 p-4 border rounded bg-light">
            <h4>📄 Importer PDF / Word (Auto-génère QCM)</h4>
            <div class="mb-3">
                <label class="form-label fw-bold">Fichier PDF ou Word</label>
                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx">
                <div class="form-text">Système lit le contenu, détecte questions, crée QCM automatiquement.</div>
            </div>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-magic"></i> Générer QCM depuis fichier
            </button>
        </div>

<hr>

<div id="questions-container"></div>

        <button type="button" id="add-question" class="btn btn-secondary mb-3">Ajouter Question</button>
        <button type="submit" class="btn btn-primary mb-3">Créer Test Manuel</button>
    </form>
</div>

<script>
let questionIndex = 1;

$('#add-question').click(function() {
    const html = `
        <div class="question-group mb-4 p-3 border rounded">
            <div class="mb-3">
                <input type="text" name="questions[${questionIndex}][question]" placeholder="Question ${questionIndex + 1}" class="form-control" required>
            </div>
            <div class="answer-group mb-2">
                <div class="input-group mb-2">
                    <input type="text" name="questions[${questionIndex}][answers][0][answer]" placeholder="Réponse A" class="form-control" required>
                    <div class="input-group-text">
                        <input type="checkbox" name="questions[${questionIndex}][answers][0][is_correct]" value="1">
                    </div>
                </div>
                <div class="input-group mb-2">
                    <input type="text" name="questions[${questionIndex}][answers][1][answer]" placeholder="Réponse B" class="form-control" required>
                    <div class="input-group-text">
                        <input type="checkbox" name="questions[${questionIndex}][answers][1][is_correct]" value="1">
                    </div>
                </div>
                <div class="input-group mb-2">
                    <input type="text" name="questions[${questionIndex}][answers][2][answer]" placeholder="Réponse C" class="form-control" required>
                    <div class="input-group-text">
                        <input type="checkbox" name="questions[${questionIndex}][answers][2][is_correct]" value="1">
                    </div>
                </div>
                <div class="input-group mb-2">
                    <input type="text" name="questions[${questionIndex}][answers][3][answer]" placeholder="Réponse D" class="form-control" required>
                    <div class="input-group-text">
                        <input type="checkbox" name="questions[${questionIndex}][answers][3][is_correct]" value="1">
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
    const answerIndex = group.find('.answer-group .input-group').length;
    const qIndex = group.find('input[name^=questions]').attr('name').match(/\[(\d+)\]/)[1];
    const html = `
        <div class="input-group mb-2">
            <input type="text" name="questions[${qIndex}][answers][${answerIndex}][answer]" placeholder="Réponse ${String.fromCharCode(65 + answerIndex)}" class="form-control" required>
            <div class="input-group-text">
                <input type="checkbox" name="questions[${qIndex}][answers][${answerIndex}][is_correct]" value="1">
            </div>
        </div>
    `;
    group.find('.answer-group').append(html);
});

$(document).on('click', '.remove-answer', function() {
    $(this).closest('.answer-group').find('.input-group').last().remove();
});
</script>
@endsection

