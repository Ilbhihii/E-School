@extends('layouts.front')

@section('title', 'Test vocal — ' . $subject->name)

@section('content')

@php
    $isCompletionTest = $isCompletionTest
        ?? \App\Models\VocalTestPrompt::isInteractiveCompletionPath(
            $subject,
            $level,
            $class,
            $prompt
        );

    $completionDefinition = $completionDefinition
        ?? (
            $isCompletionTest
                ? \App\Models\VocalTestPrompt::completionDefinition()
                : null
        );
@endphp

<style>
    .vocal-test-section {
        padding-top: 4rem;
        padding-bottom: 4rem;
    }

    .vocal-test-container {
        max-width: 900px;
    }

    .vocal-header {
        margin-bottom: 2.75rem;
        padding-top: 0.5rem;
    }

    .vocal-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.65rem 1.15rem;
        margin-bottom: 1.35rem;

        background: rgba(124, 58, 237, 0.15);
        color: #c4b5fd;
        border: 1px solid rgba(167, 139, 250, 0.25);
        border-radius: 999px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .vocal-badge.mode-tajwid {
        background: rgba(16, 185, 129, 0.15);
        color: #6ee7b7;
        border-color: rgba(16, 185, 129, 0.25);
    }

    .vocal-badge.mode-hifd {
        background: rgba(251, 191, 36, 0.15);
        color: #fcd34d;
        border-color: rgba(251, 191, 36, 0.25);
    }

    .vocal-badge.mode-completion {
        background: rgba(245, 158, 11, 0.14);
        color: #fcd34d;
        border-color: rgba(245, 158, 11, 0.3);
    }

    .vocal-badge.mode-observation {
        background: rgba(6, 182, 212, 0.14);
        color: #67e8f9;
        border-color: rgba(34, 211, 238, 0.3);
    }

    .vocal-title {
        margin: 0 0 0.5rem;
        padding: 0;

        font-family: 'Poppins', 'Inter', sans-serif;
        font-size: clamp(1.8rem, 4vw, 2.8rem);
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.03em;

        color: #ffffff !important;
        background: none !important;
        background-image: none !important;
        -webkit-background-clip: initial !important;
        background-clip: initial !important;
        -webkit-text-fill-color: #ffffff !important;

        text-align: center;
    }

    .vocal-subtitle {
        margin: 0;
        color: rgba(255, 255, 255, 0.58);
        font-size: 1rem;
        font-weight: 500;
        text-align: center;
    }

    .vocal-recitation-card,
    .vocal-recording-card {
        margin-bottom: 1.75rem;
    }

    .vocal-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .vocal-card-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin: 0;
        background: linear-gradient(135deg, #7c3aed, #2563eb);
    }

    .vocal-instructions {
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.75);
        background: rgba(255, 209, 102, 0.06);
        border: 1px solid rgba(255, 209, 102, 0.12);
        border-radius: 14px;
    }

    .vocal-instructions i {
        color: #FFD166;
        margin-right: 0.5rem;
    }

    .vocal-reading-text {
        padding: 1.75rem;

        font-family: 'Amiri', 'Noto Naskh Arabic', serif;
        font-size: 1.8rem;
        line-height: 2.25;

        text-align: center;
        white-space: pre-line;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.035);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;

        transition: opacity 0.4s ease;
    }

    .vocal-reading-text.hidden-text {
        opacity: 0.08;
        user-select: none;
        pointer-events: none;
    }

    .vocal-reading-text.hidden-text::after {
        content: '🔒 Texte masqué — récitation de mémoire';
        display: block;
        margin-top: 1rem;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.25);
        user-select: none;
        pointer-events: none;
    }

    .vocal-duration-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 1rem;
        padding: 0.4rem 1rem;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.5);
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 999px;
    }

    .vocal-status {
        margin-bottom: 1rem;
        color: rgba(255, 255, 255, 0.58);
    }

    .vocal-timer {
        margin-bottom: 1.75rem;
        font-size: 2rem;
        font-weight: 700;
        color: #ffffff;
        font-variant-numeric: tabular-nums;
    }

    .vocal-actions {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .vocal-action-button {
        min-width: 190px;
    }

    .vocal-stop-button {
        min-width: 190px;
        color: #ffffff;
        border: 0;
        background: linear-gradient(135deg, #dc2626, #ef4444);
    }

    .vocal-preview {
        margin-top: 1.75rem;
    }

    .vocal-preview audio {
        width: min(100%, 520px);
    }

    .vocal-submit-button {
        width: 100%;
        margin-top: 1.75rem;
        padding: 14px;
    }

    .vocal-max-warning {
        display: none;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        color: #fbbf24;
        background: rgba(251, 191, 36, 0.1);
        border: 1px solid rgba(251, 191, 36, 0.2);
        border-radius: 12px;
    }

    .vocal-max-warning.show {
        display: block;
    }

    .completion-form-card {
        margin-bottom: 1.75rem;
    }

    .completion-verses {
        display: grid;
        gap: 1.2rem;
        margin-bottom: 1.5rem;
    }

    .completion-verse {
        padding: 1.4rem;
        font-family: 'Amiri', 'Noto Naskh Arabic', serif;
        font-size: clamp(1.35rem, 2.6vw, 1.85rem);
        line-height: 2.4;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.035);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        text-align: center;
    }

    .completion-slot {
        min-width: 135px;
        min-height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
        margin: 0 0.3rem;
        padding: 0.35rem 0.8rem;
        cursor: pointer;
        color: #fcd34d;
        background: rgba(245, 158, 11, 0.08);
        border: 2px dashed rgba(251, 191, 36, 0.5);
        border-radius: 12px;
        font-family: 'Amiri', 'Noto Naskh Arabic', serif;
        font-size: 1.25rem;
        transition:
            transform 0.2s ease,
            border-color 0.2s ease,
            background 0.2s ease;
    }

    .completion-slot.is-selected,
    .completion-slot.drag-over {
        border-color: #a78bfa;
        background: rgba(124, 58, 237, 0.18);
        transform: translateY(-2px);
    }

    .completion-slot.is-filled {
        border-style: solid;
        border-color: rgba(74, 222, 128, 0.5);
        background: rgba(34, 197, 94, 0.12);
        color: #bbf7d0;
    }

    .completion-slot-placeholder {
        color: rgba(255, 255, 255, 0.38);
        font-family: 'Inter', sans-serif;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .completion-bank-title {
        margin-bottom: 0.8rem;
        color: rgba(255, 255, 255, 0.72);
        font-size: 0.9rem;
        font-weight: 700;
        text-align: center;
    }

    .completion-word-bank {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 1.1rem;
        background: rgba(255, 255, 255, 0.025);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 16px;
    }

    .completion-word-card {
        min-width: 110px;
        padding: 0.7rem 1rem;
        cursor: grab;
        color: #ffffff;
        background: linear-gradient(
            135deg,
            rgba(37, 99, 235, 0.9),
            rgba(124, 58, 237, 0.9)
        );
        border: 1px solid rgba(196, 181, 253, 0.32);
        border-radius: 12px;
        font-family: 'Amiri', 'Noto Naskh Arabic', serif;
        font-size: 1.15rem;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.12);
        transition:
            transform 0.2s ease,
            opacity 0.2s ease,
            filter 0.2s ease;
    }

    .completion-word-card:hover {
        transform: translateY(-2px);
    }

    .completion-word-card:active {
        cursor: grabbing;
    }

    .completion-word-card.is-used {
        opacity: 0.25;
        filter: grayscale(0.7);
        pointer-events: none;
        transform: none;
    }

    .completion-progress {
        margin-bottom: 1rem;
        color: rgba(255, 255, 255, 0.65);
        font-size: 0.86rem;
        text-align: center;
    }

    .completion-actions {
        display: flex;
        gap: 0.8rem;
        flex-wrap: wrap;
    }

    .completion-reset-button {
        min-width: 170px;
        color: #e2e8f0;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .completion-submit-button {
        flex: 1;
        min-width: 240px;
    }

    .completion-error {
        margin-bottom: 1rem;
        padding: 0.85rem 1rem;
        color: #fca5a5;
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.25);
        border-radius: 12px;
    }

    .observation-card {
        margin-bottom: 1.75rem;
    }

    .observation-image-frame {
        overflow: hidden;
        margin-bottom: 1.25rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.22);
    }

    .observation-image-frame img {
        width: 100%;
        max-height: 560px;
        display: block;
        object-fit: contain;
        background: #ffffff;
    }

    .observation-question {
        margin-bottom: 1rem;
        padding: 1rem 1.2rem;
        color: rgba(255, 255, 255, 0.82);
        background: rgba(6, 182, 212, 0.08);
        border: 1px solid rgba(34, 211, 238, 0.2);
        border-radius: 14px;
        line-height: 1.7;
    }

    .observation-arabic-title {
        display: block;
        margin-top: 0.45rem;
        color: #ffffff;
        font-family: 'Amiri', 'Noto Naskh Arabic', serif;
        font-size: 1.25rem;
        direction: rtl;
    }

    .observation-options {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin: 1.25rem 0;
    }

    .observation-option {
        position: relative;
        cursor: pointer;
    }

    .observation-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .observation-option-card {
        min-height: 120px;
        display: flex;
        align-items: flex-start;
        gap: 0.9rem;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.035);
        transition:
            transform 0.2s ease,
            border-color 0.2s ease,
            background 0.2s ease;
    }

    .observation-option input:checked
        + .observation-option-card {
        transform: translateY(-2px);
        border-color: rgba(34, 211, 238, 0.7);
        background: rgba(6, 182, 212, 0.13);
        box-shadow: 0 12px 28px rgba(6, 182, 212, 0.12);
    }

    .observation-option-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        color: #ffffff;
        background: linear-gradient(135deg, #0891b2, #2563eb);
        font-size: 1rem;
    }

    .observation-option-card strong {
        display: block;
        margin-bottom: 0.3rem;
        color: #ffffff;
        font-size: 0.92rem;
    }

    .observation-option-card small {
        display: block;
        color: rgba(255, 255, 255, 0.5);
        line-height: 1.5;
    }

    .observation-panel {
        display: none;
        margin-top: 1rem;
    }

    .observation-panel.is-active {
        display: block;
    }

    .observation-label {
        display: block;
        margin-bottom: 0.55rem;
        color: rgba(255, 255, 255, 0.78);
        font-weight: 700;
    }

    .observation-textarea {
        width: 100%;
        min-height: 190px;
        resize: vertical;
        padding: 1rem;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        outline: none;
        font-family: 'Amiri', 'Noto Naskh Arabic', serif;
        font-size: 1.15rem;
        line-height: 1.9;
        direction: rtl;
    }

    .observation-textarea:focus {
        border-color: rgba(34, 211, 238, 0.7);
        box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.1);
    }

    .observation-char-count {
        margin-top: 0.45rem;
        color: rgba(255, 255, 255, 0.42);
        font-size: 0.78rem;
        text-align: right;
    }

    .observation-upload {
        position: relative;
        display: grid;
        place-items: center;
        min-height: 190px;
        padding: 1.25rem;
        cursor: pointer;
        border: 2px dashed rgba(34, 211, 238, 0.35);
        border-radius: 16px;
        background: rgba(6, 182, 212, 0.05);
        text-align: center;
    }

    .observation-upload:hover {
        border-color: rgba(34, 211, 238, 0.75);
        background: rgba(6, 182, 212, 0.09);
    }

    .observation-upload input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
        opacity: 0;
    }

    .observation-upload i {
        margin-bottom: 0.7rem;
        color: #67e8f9;
        font-size: 2rem;
    }

    .observation-upload strong {
        display: block;
        color: #ffffff;
    }

    .observation-upload small {
        display: block;
        margin-top: 0.4rem;
        color: rgba(255, 255, 255, 0.45);
    }

    .observation-preview {
        display: none;
        margin-top: 1rem;
        text-align: center;
    }

    .observation-preview.is-visible {
        display: block;
    }

    .observation-preview img {
        max-width: 100%;
        max-height: 380px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .observation-submit {
        width: 100%;
        margin-top: 1.25rem;
        padding: 14px;
    }

    .observation-error {
        margin-bottom: 1rem;
        padding: 0.85rem 1rem;
        color: #fca5a5;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.22);
        border-radius: 12px;
    }

    @media (max-width: 768px) {
        .observation-options {
            grid-template-columns: 1fr;
        }

        .observation-image-frame img {
            max-height: 420px;
        }
    }

    @media (max-width: 768px) {
        .vocal-test-section {
            padding-top: 2.5rem;
            padding-bottom: 3rem;
        }

        .vocal-header {
            margin-bottom: 2rem;
            padding-top: 0;
        }

        .vocal-title { font-size: 1.7rem; }

        .vocal-reading-text {
            padding: 1.25rem;
            font-size: 1.45rem;
            line-height: 2;
        }

        .vocal-action-button,
        .vocal-stop-button {
            width: 100%;
            min-width: 0;
        }
    }
</style>

<section class="vocal-test-section">
    <div class="container vocal-test-container">

        <div class="text-center vocal-header">
            <span class="badge vocal-badge @if($isObservationTest) mode-observation @elseif($isCompletionTest) mode-completion @elseif($prompt->test_mode === 'tajwid') mode-tajwid @elseif($prompt->test_mode === 'hifd') mode-hifd @endif">
                @if ($isObservationTest)
                    <i class="bi bi-eye-fill"></i>
                @elseif ($isCompletionTest)
                    <i class="bi bi-puzzle-fill"></i>
                @elseif ($prompt->test_mode === 'hifd')
                    <i class="bi bi-lock-fill"></i>
                @elseif ($prompt->test_mode === 'tajwid')
                    <i class="bi bi-music-note"></i>
                @else
                    <i class="bi bi-mic-fill"></i>
                @endif

                {{ $subject->name }} ·
                @if ($isObservationTest)
                    Test d’observation
                @elseif ($isCompletionTest)
                    Complétion des versets
                @else
                    {{ \App\Models\VocalTestPrompt::getModes()[$prompt->test_mode] ?? 'Lecture' }}
                @endif
            </span>

            <h1 class="vocal-title">
                {{ $isObservationTest
                    ? $observationDefinition['title']
                    : $prompt->title }}
            </h1>

            <p class="vocal-subtitle">
                {{ $level->name }} · {{ $class->name }}
            </p>
        </div>

        @if($isObservationTest)
            <form
                method="POST"
                action="{{ route('vocal-test.store', [$subject, $level, $class]) }}"
                enctype="multipart/form-data"
                id="observationTestForm"
            >
                @csrf

                <div class="card-3d p-4 p-md-5 observation-card">
                    <div class="vocal-card-header">
                        <div class="card-3d-icon vocal-card-icon">
                            <i class="bi bi-eye-fill text-white"></i>
                        </div>

                        <div>
                            <h5 class="text-white mb-1">
                                Observez puis décrivez
                            </h5>
                            <small class="text-white-50">
                                Communication · {{ $class->name }}
                            </small>
                        </div>
                    </div>

                    <div class="observation-question">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        {{ $observationDefinition['question'] }}

                        <span class="observation-arabic-title">
                            {{ $observationDefinition['arabic_title'] }}
                        </span>
                    </div>

                    <div class="vocal-instructions">
                        <i class="bi bi-lightbulb-fill"></i>
                        {{ $observationDefinition['instructions'] }}
                    </div>

                    <div class="observation-image-frame">
                        <img
                            src="{{ asset($observationDefinition['image']) }}"
                            alt="Image du test d’observation : une ferme avec des animaux et des véhicules agricoles"
                        >
                    </div>

                    @if(
                        $errors->has('response_mode')
                        || $errors->has('observation_text')
                        || $errors->has('observation_image')
                    )
                        <div class="observation-error">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            {{ $errors->first('response_mode')
                                ?: $errors->first('observation_text')
                                ?: $errors->first('observation_image') }}
                        </div>
                    @endif

                    <h5 class="text-white mb-2">
                        Choisissez une façon de répondre
                    </h5>

                    <div class="observation-options">
                        <label class="observation-option">
                            <input
                                type="radio"
                                name="response_mode"
                                value="text"
                                {{ old('response_mode', 'text') === 'text'
                                    ? 'checked'
                                    : '' }}
                            >

                            <span class="observation-option-card">
                                <span class="observation-option-icon">
                                    <i class="bi bi-pencil-square"></i>
                                </span>

                                <span>
                                    <strong>Écrire ma réponse</strong>
                                    <small>
                                        Rédigez directement en arabe
                                        dans la zone de texte.
                                    </small>
                                </span>
                            </span>
                        </label>

                        <label class="observation-option">
                            <input
                                type="radio"
                                name="response_mode"
                                value="image"
                                {{ old('response_mode') === 'image'
                                    ? 'checked'
                                    : '' }}
                            >

                            <span class="observation-option-card">
                                <span class="observation-option-icon">
                                    <i class="bi bi-image-fill"></i>
                                </span>

                                <span>
                                    <strong>Importer une photo</strong>
                                    <small>
                                        Écrivez sur une feuille puis
                                        prenez une photo lisible.
                                    </small>
                                </span>
                            </span>
                        </label>
                    </div>

                    <div
                        id="observationTextPanel"
                        class="observation-panel"
                    >
                        <label
                            for="observationText"
                            class="observation-label"
                        >
                            Votre observation en arabe
                        </label>

                        <textarea
                            name="observation_text"
                            id="observationText"
                            class="observation-textarea"
                            maxlength="3000"
                            placeholder="اكتب هنا ما تراه في الصورة..."
                        >{{ old('observation_text') }}</textarea>

                        <div class="observation-char-count">
                            <span id="observationCharCount">0</span>
                            / 3000 caractères · minimum
                            {{ $observationDefinition['minimum_characters'] }}
                        </div>
                    </div>

                    <div
                        id="observationImagePanel"
                        class="observation-panel"
                    >
                        <label class="observation-upload">
                            <input
                                type="file"
                                name="observation_image"
                                id="observationImage"
                                accept="image/jpeg,image/png,image/webp"
                            >

                            <span>
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <strong>
                                    Sélectionner la photo de la feuille
                                </strong>
                                <small>
                                    JPG, PNG ou WEBP · 8 Mo maximum
                                </small>
                            </span>
                        </label>

                        <div
                            id="observationPreview"
                            class="observation-preview"
                        >
                            <img
                                id="observationPreviewImage"
                                src=""
                                alt="Aperçu de la réponse manuscrite"
                            >
                        </div>
                    </div>

                    <button
                        type="submit"
                        id="submitObservation"
                        class="btn-3d btn-3d-gradient observation-submit"
                    >
                        <i class="bi bi-send-fill me-2"></i>
                        Envoyer mon observation et continuer
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        @elseif($isCompletionTest)
            <form
                method="POST"
                action="{{ route('vocal-test.store', [$subject, $level, $class]) }}"
                id="completionTestForm"
            >
                @csrf

                <div class="card-3d p-4 p-md-5 completion-form-card">
                    <div class="vocal-card-header">
                        <div class="card-3d-icon vocal-card-icon">
                            <i class="bi bi-puzzle-fill text-white"></i>
                        </div>

                        <div>
                            <h5 class="text-white mb-1">
                                Complétez les quatre espaces
                            </h5>
                            <small class="text-white-50">
                                Cliquez sur une carte ou glissez-la vers
                                l’emplacement correspondant.
                            </small>
                        </div>
                    </div>

                    @if ($prompt->instructions)
                        <div class="vocal-instructions">
                            <i class="bi bi-info-circle-fill"></i>
                            {{ $prompt->instructions }}
                        </div>
                    @endif

                    @if(
                        $errors->has('answers')
                        || $errors->has('answers.*')
                    )
                        <div class="completion-error">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            {{ $errors->first('answers')
                                ?: $errors->first('answers.*') }}
                        </div>
                    @endif

                    <div class="completion-verses" dir="rtl" lang="ar">
                        @foreach($completionDefinition['verses'] as $verse)
                            <div class="completion-verse">
                                @foreach($verse as $part)
                                    @if(array_key_exists('text', $part))
                                        <span>{{ $part['text'] }}</span>
                                    @else
                                        @php
                                            $slotIndex = $part['slot'];
                                            $oldValue = old(
                                                'answers.' . $slotIndex,
                                                ''
                                            );
                                        @endphp

                                        <button
                                            type="button"
                                            class="completion-slot {{ $oldValue ? 'is-filled' : '' }}"
                                            data-slot="{{ $slotIndex }}"
                                            aria-label="Emplacement {{ $slotIndex + 1 }}"
                                        >
                                            <span class="completion-slot-value">
                                                @if($oldValue)
                                                    {{ $oldValue }}
                                                @else
                                                    <span class="completion-slot-placeholder">
                                                        Choisir un mot
                                                    </span>
                                                @endif
                                            </span>

                                            <input
                                                type="hidden"
                                                name="answers[{{ $slotIndex }}]"
                                                value="{{ $oldValue }}"
                                                data-answer-input
                                            >
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <div class="completion-bank-title">
                        <i class="bi bi-hand-index-thumb me-1"></i>
                        Cartes disponibles
                    </div>

                    <div
                        class="completion-word-bank"
                        id="completionWordBank"
                        dir="rtl"
                        lang="ar"
                    >
                        @foreach($completionDefinition['choices'] as $choice)
                            <button
                                type="button"
                                class="completion-word-card"
                                data-word="{{ $choice }}"
                                draggable="true"
                            >
                                {{ $choice }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="card-3d p-4 p-md-5">
                    <div
                        id="completionProgress"
                        class="completion-progress"
                    >
                        0 / {{ count($completionDefinition['expected_answers']) }}
                        espaces complétés
                    </div>

                    <div class="completion-actions">
                        <button
                            type="button"
                            id="resetCompletion"
                            class="btn-3d completion-reset-button"
                        >
                            <i class="bi bi-arrow-counterclockwise me-2"></i>
                            Réinitialiser
                        </button>

                        <button
                            type="submit"
                            id="submitCompletion"
                            class="btn-3d btn-3d-gradient completion-submit-button"
                            disabled
                        >
                            <i class="bi bi-check2-circle me-2"></i>
                            Valider mes réponses et continuer
                            <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="card-3d p-4 p-md-5 vocal-recitation-card">
                <div class="vocal-card-header">
                    <div class="card-3d-icon vocal-card-icon">
                        @if ($prompt->test_mode === 'hifd')
                            <i class="bi bi-lock text-white"></i>
                        @else
                            <i class="bi bi-book-half text-white"></i>
                        @endif
                    </div>

                    <div>
                        <h5 class="text-white mb-1">
                            @if ($prompt->test_mode === 'hifd')
                                Texte à mémoriser
                            @else
                                Texte à réciter
                            @endif
                        </h5>
                        <small class="text-white-50">
                            Durée maximale :
                            {{ $prompt->maximum_duration }} secondes
                            @if ($prompt->preparation_seconds > 0)
                                · Préparation :
                                {{ $prompt->preparation_seconds }}s
                            @endif
                        </small>
                    </div>
                </div>

                @if ($prompt->instructions)
                    <div class="vocal-instructions">
                        <i class="bi bi-info-circle-fill"></i>
                        {{ $prompt->instructions }}
                    </div>
                @endif

                <div
                    id="recitationText"
                    dir="rtl"
                    lang="ar"
                    class="vocal-reading-text"
                >
                    {{ $prompt->reading_text }}
                </div>

                <div class="text-center">
                    <span class="vocal-duration-badge">
                        @if ($prompt->test_mode === 'hifd')
                            <i class="bi bi-lock"></i>
                            Mémorisation · Max
                            {{ $prompt->maximum_duration }}s
                        @elseif ($prompt->test_mode === 'tajwid')
                            <i class="bi bi-music-note"></i>
                            Tajwid · Max
                            {{ $prompt->maximum_duration }}s
                        @else
                            <i class="bi bi-clock"></i>
                            Max {{ $prompt->maximum_duration }}
                            secondes
                        @endif
                    </span>
                </div>
            </div>

            <div class="card-3d p-4 p-md-5 vocal-recording-card">
                <form
                    method="POST"
                    action="{{ route('vocal-test.store', [$subject, $level, $class]) }}"
                    enctype="multipart/form-data"
                    id="vocalTestForm"
                >
                    @csrf

                    <input
                        type="file"
                        name="audio"
                        id="audioFile"
                        accept="audio/*,video/webm"
                        hidden
                        required
                    >

                    <input
                        type="hidden"
                        name="duration_seconds"
                        id="durationSeconds"
                        value=""
                    >

                    @error('audio')
                        <div
                            class="alert mb-4"
                            style="
                                background:rgba(239,68,68,0.14);
                                color:#fca5a5;
                                border:1px solid rgba(239,68,68,0.25);
                                border-radius:12px;
                            "
                        >
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="text-center">
                        <div
                            id="recordingStatus"
                            class="vocal-status"
                        >
                            @if ($prompt->test_mode === 'hifd')
                                Prenez le temps de mémoriser le texte,
                                puis commencez l’enregistrement.
                            @else
                                Autorisez le microphone puis commencez
                                l’enregistrement.
                            @endif
                        </div>

                        <div
                            id="maxDurationWarning"
                            class="vocal-max-warning"
                        >
                            <i
                                class="bi bi-exclamation-triangle-fill me-1"
                            ></i>
                            Vous approchez de la durée maximale autorisée
                            ({{ $prompt->maximum_duration }} secondes).
                        </div>

                        <div id="timer" class="vocal-timer">
                            00:00
                        </div>

                        <div class="vocal-actions">
                            <button
                                type="button"
                                id="startRecording"
                                class="btn-3d btn-3d-gradient vocal-action-button"
                            >
                                <i class="bi bi-mic-fill me-2"></i>
                                @if ($prompt->test_mode === 'hifd')
                                    Commencer la récitation
                                @else
                                    Commencer
                                @endif
                            </button>

                            <button
                                type="button"
                                id="stopRecording"
                                class="btn-3d vocal-stop-button"
                                disabled
                            >
                                <i class="bi bi-stop-fill me-2"></i>
                                Arrêter
                            </button>
                        </div>

                        <div
                            id="previewBlock"
                            class="vocal-preview"
                            hidden
                        >
                            <p class="text-white-50 mb-2">
                                <i
                                    class="bi bi-check-circle-fill me-1"
                                    style="color:#4ade80;"
                                ></i>
                                Écoutez votre enregistrement avant
                                de continuer.
                            </p>

                            <audio
                                id="audioPreview"
                                controls
                            ></audio>
                        </div>

                        <button
                            type="submit"
                            id="submitRecording"
                            class="btn-3d btn-3d-gradient vocal-submit-button"
                            disabled
                        >
                            <i class="bi bi-send-fill me-2"></i>
                            Envoyer et continuer vers le rendez-vous
                            <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>
</section>

@if($isObservationTest)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById(
        'observationTestForm'
    );
    const modeInputs = document.querySelectorAll(
        'input[name="response_mode"]'
    );
    const textPanel = document.getElementById(
        'observationTextPanel'
    );
    const imagePanel = document.getElementById(
        'observationImagePanel'
    );
    const textarea = document.getElementById(
        'observationText'
    );
    const charCount = document.getElementById(
        'observationCharCount'
    );
    const imageInput = document.getElementById(
        'observationImage'
    );
    const preview = document.getElementById(
        'observationPreview'
    );
    const previewImage = document.getElementById(
        'observationPreviewImage'
    );
    const submitButton = document.getElementById(
        'submitObservation'
    );
    const minimumCharacters = Number(
        @json($observationDefinition['minimum_characters'])
    );

    let previewUrl = null;

    const selectedMode = () => {
        const selected = document.querySelector(
            'input[name="response_mode"]:checked'
        );

        return selected
            ? selected.value
            : 'text';
    };

    const updatePanels = () => {
        const mode = selectedMode();

        textPanel.classList.toggle(
            'is-active',
            mode === 'text'
        );
        imagePanel.classList.toggle(
            'is-active',
            mode === 'image'
        );

        textarea.required = mode === 'text';
        imageInput.required = mode === 'image';
    };

    const updateCharacterCount = () => {
        charCount.textContent =
            textarea.value.length;
    };

    modeInputs.forEach(input => {
        input.addEventListener(
            'change',
            updatePanels
        );
    });

    textarea.addEventListener(
        'input',
        updateCharacterCount
    );

    imageInput.addEventListener('change', () => {
        const file = imageInput.files
            && imageInput.files[0];

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }

        if (!file) {
            preview.classList.remove(
                'is-visible'
            );
            previewImage.src = '';
            return;
        }

        if (!file.type.startsWith('image/')) {
            imageInput.value = '';
            preview.classList.remove(
                'is-visible'
            );
            alert(
                'Sélectionnez une image JPG, PNG ou WEBP.'
            );
            return;
        }

        if (file.size > 8 * 1024 * 1024) {
            imageInput.value = '';
            preview.classList.remove(
                'is-visible'
            );
            alert(
                'La photo ne doit pas dépasser 8 Mo.'
            );
            return;
        }

        previewUrl = URL.createObjectURL(file);
        previewImage.src = previewUrl;
        preview.classList.add('is-visible');
    });

    form.addEventListener('submit', event => {
        const mode = selectedMode();

        if (
            mode === 'text'
            && textarea.value.trim().length
                < minimumCharacters
        ) {
            event.preventDefault();
            textarea.focus();
            alert(
                'Votre observation doit contenir au moins '
                + minimumCharacters
                + ' caractères.'
            );
            return;
        }

        if (
            mode === 'image'
            && !(
                imageInput.files
                && imageInput.files[0]
            )
        ) {
            event.preventDefault();
            alert(
                'Importez la photo de votre réponse manuscrite.'
            );
            return;
        }

        submitButton.disabled = true;
        submitButton.innerHTML =
            '<i class="bi bi-hourglass-split me-2"></i>'
            + 'Envoi en cours…';
    });

    window.addEventListener('beforeunload', () => {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }
    });

    updatePanels();
    updateCharacterCount();
});
</script>
@elseif($isCompletionTest)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('completionTestForm');
    const slots = Array.from(
        document.querySelectorAll('.completion-slot')
    );
    const cards = Array.from(
        document.querySelectorAll('.completion-word-card')
    );
    const progress = document.getElementById(
        'completionProgress'
    );
    const submitButton = document.getElementById(
        'submitCompletion'
    );
    const resetButton = document.getElementById(
        'resetCompletion'
    );

    let selectedSlot = null;
    let draggedWord = null;

    const inputOf = slot =>
        slot.querySelector('[data-answer-input]');

    const valueOf = slot =>
        (inputOf(slot)?.value || '').trim();

    const cardForWord = word =>
        cards.find(card => card.dataset.word === word);

    const renderSlot = slot => {
        const word = valueOf(slot);
        const valueBlock = slot.querySelector(
            '.completion-slot-value'
        );

        if (!valueBlock) return;

        if (word) {
            valueBlock.textContent = word;
            slot.classList.add('is-filled');
        } else {
            valueBlock.innerHTML =
                '<span class="completion-slot-placeholder">'
                + 'Choisir un mot'
                + '</span>';
            slot.classList.remove('is-filled');
        }
    };

    const updateState = () => {
        const usedWords = slots
            .map(valueOf)
            .filter(Boolean);

        cards.forEach(card => {
            card.classList.toggle(
                'is-used',
                usedWords.includes(card.dataset.word)
            );
        });

        slots.forEach(renderSlot);

        const completed = usedWords.length;
        const total = slots.length;

        progress.textContent =
            `${completed} / ${total} espaces complétés`;

        submitButton.disabled = completed !== total;
    };

    const clearSlot = slot => {
        const input = inputOf(slot);

        if (input) {
            input.value = '';
        }

        slot.classList.remove('is-selected');
        selectedSlot = null;
        updateState();
    };

    const assignWord = (word, targetSlot = null) => {
        if (!word) return;

        const existingSlot = slots.find(
            slot => valueOf(slot) === word
        );

        if (existingSlot) {
            const existingInput = inputOf(existingSlot);
            existingInput.value = '';
        }

        let slot = targetSlot;

        if (!slot && selectedSlot && !valueOf(selectedSlot)) {
            slot = selectedSlot;
        }

        if (!slot) {
            slot = slots.find(item => !valueOf(item));
        }

        if (!slot) {
            progress.textContent =
                'Tous les espaces sont remplis. '
                + 'Cliquez sur un espace pour le vider.';
            return;
        }

        const input = inputOf(slot);
        input.value = word;

        slots.forEach(item =>
            item.classList.remove('is-selected')
        );

        selectedSlot = null;
        updateState();
    };

    slots.forEach(slot => {
        slot.addEventListener('click', () => {
            if (valueOf(slot)) {
                clearSlot(slot);
                return;
            }

            slots.forEach(item =>
                item.classList.remove('is-selected')
            );

            slot.classList.add('is-selected');
            selectedSlot = slot;
        });

        slot.addEventListener('dragover', event => {
            event.preventDefault();
            slot.classList.add('drag-over');
        });

        slot.addEventListener('dragleave', () => {
            slot.classList.remove('drag-over');
        });

        slot.addEventListener('drop', event => {
            event.preventDefault();
            slot.classList.remove('drag-over');

            const word =
                event.dataTransfer.getData('text/plain')
                || draggedWord;

            assignWord(word, slot);
            draggedWord = null;
        });
    });

    cards.forEach(card => {
        card.addEventListener('click', () => {
            assignWord(card.dataset.word);
        });

        card.addEventListener('dragstart', event => {
            draggedWord = card.dataset.word;
            event.dataTransfer.setData(
                'text/plain',
                draggedWord
            );
            event.dataTransfer.effectAllowed = 'move';
        });

        card.addEventListener('dragend', () => {
            draggedWord = null;
            slots.forEach(slot =>
                slot.classList.remove('drag-over')
            );
        });
    });

    resetButton.addEventListener('click', () => {
        slots.forEach(slot => {
            const input = inputOf(slot);
            input.value = '';
            slot.classList.remove(
                'is-selected',
                'drag-over'
            );
        });

        selectedSlot = null;
        updateState();
    });

    form.addEventListener('submit', event => {
        const completed = slots.every(
            slot => Boolean(valueOf(slot))
        );

        if (!completed) {
            event.preventDefault();
            progress.textContent =
                'Complétez les quatre espaces avant '
                + 'de valider.';
            return;
        }

        submitButton.disabled = true;
        submitButton.innerHTML =
            '<span class="spinner-border '
            + 'spinner-border-sm me-2"></span>'
            + 'Validation en cours…';
    });

    updateState();
});
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('vocalTestForm');
    const startButton = document.getElementById('startRecording');
    const stopButton = document.getElementById('stopRecording');
    const submitButton = document.getElementById('submitRecording');
    const status = document.getElementById('recordingStatus');
    const timer = document.getElementById('timer');
    const previewBlock = document.getElementById('previewBlock');
    const preview = document.getElementById('audioPreview');
    const audioFile = document.getElementById('audioFile');
    const durationSecondsInput = document.getElementById('durationSeconds');
    const maxDurationWarning = document.getElementById('maxDurationWarning');
    const maxDuration = {{ $prompt->maximum_duration }};

    const textBlock = document.getElementById('recitationText');
    const hideTextDuringRecording = @json($prompt->hide_text_during_recording ?? false);

    let recorder = null;
    let stream = null;
    let chunks = [];
    let timerInterval = null;
    let startedAt = null;
    let previewUrl = null;
    let recordedFileReady = false;

    const stopMicrophone = () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    };

    const restoreText = () => {
        if (textBlock && hideTextDuringRecording) {
            textBlock.classList.remove('hidden-text');
        }
    };

    const resetButtonsAfterStop = () => {
        startButton.disabled = false;
        stopButton.disabled = true;
    };

    const showRecordingError = message => {
        recordedFileReady = false;
        submitButton.disabled = true;
        previewBlock.hidden = true;
        status.textContent = message;
        durationSecondsInput.value = '';
        audioFile.value = '';
        resetButtonsAfterStop();
        restoreText();
        stopMicrophone();
    };

    const chooseRecorderMimeType = () => {
        if (typeof MediaRecorder.isTypeSupported !== 'function') {
            return '';
        }

        const candidates = [
            'audio/webm;codecs=opus',
            'audio/ogg;codecs=opus',
            'audio/mp4',
        ];

        return candidates.find(type => MediaRecorder.isTypeSupported(type)) || '';
    };

    const extensionForMimeType = mimeType => {
        const normalized = (mimeType || '').toLowerCase();

        if (normalized.includes('mp4')) return 'm4a';
        if (normalized.includes('ogg')) return 'ogg';
        if (normalized.includes('wav')) return 'wav';
        if (normalized.includes('mpeg')) return 'mp3';
        if (normalized.includes('aac')) return 'aac';

        return 'webm';
    };

    const updateTimer = () => {
        if (!startedAt) return;

        const seconds = Math.floor((Date.now() - startedAt) / 1000);
        const minutes = String(Math.floor(seconds / 60)).padStart(2, '0');
        const remainingSeconds = String(seconds % 60).padStart(2, '0');

        timer.textContent = `${minutes}:${remainingSeconds}`;

        if (seconds >= maxDuration * 0.8 && seconds < maxDuration) {
            maxDurationWarning.classList.add('show');
        }

        if (seconds >= maxDuration && recorder && recorder.state === 'recording') {
            status.textContent = 'Durée maximale atteinte. Enregistrement arrêté automatiquement.';
            recorder.stop();
            stopButton.disabled = true;
        }
    };

    startButton.addEventListener('click', async () => {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia || !window.MediaRecorder) {
            status.textContent = 'Votre navigateur ne prend pas en charge l’enregistrement vocal.';
            return;
        }

        try {
            recordedFileReady = false;
            submitButton.disabled = true;
            previewBlock.hidden = true;
            audioFile.value = '';
            durationSecondsInput.value = '';
            chunks = [];

            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }

            stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true
                }
            });

            const preferredType = chooseRecorderMimeType();

            recorder = new MediaRecorder(
                stream,
                preferredType ? { mimeType: preferredType } : undefined
            );

            recorder.ondataavailable = event => {
                if (event.data && event.data.size > 0) {
                    chunks.push(event.data);
                }
            };

            recorder.onerror = () => {
                clearInterval(timerInterval);
                showRecordingError('Une erreur est survenue pendant l’enregistrement. Veuillez recommencer.');
            };

            recorder.onstop = () => {
                clearInterval(timerInterval);

                const elapsedSeconds = Math.max(
                    1,
                    Math.floor((Date.now() - startedAt) / 1000)
                );

                const mimeType = recorder.mimeType || preferredType || 'audio/webm';
                const blob = new Blob(chunks, { type: mimeType });

                if (!chunks.length || blob.size < 1024) {
                    showRecordingError(
                        'L’enregistrement est vide ou trop court. Vérifiez votre microphone et enregistrez au moins quelques secondes.'
                    );
                    return;
                }

                const extension = extensionForMimeType(mimeType);
                const file = new File(
                    [blob],
                    `enregistrement-${Date.now()}.${extension}`,
                    { type: mimeType }
                );

                try {
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    audioFile.files = transfer.files;
                } catch (error) {
                    showRecordingError(
                        'Votre navigateur ne permet pas de préparer le fichier audio. Utilisez une version récente de Chrome, Edge ou Safari.'
                    );
                    return;
                }

                durationSecondsInput.value = Math.min(elapsedSeconds, maxDuration);

                previewUrl = URL.createObjectURL(blob);
                preview.src = previewUrl;
                preview.load();

                previewBlock.hidden = false;
                recordedFileReady = false;
                submitButton.disabled = true;
                status.textContent = 'Vérification de l’enregistrement…';

                let previewValidated = false;

                const validatePreview = () => {
                    if (previewValidated) return;

                    previewValidated = true;
                    recordedFileReady = true;
                    submitButton.disabled = false;
                    status.textContent = `Enregistrement valide (${(blob.size / 1024).toFixed(1)} Ko). Écoutez-le avant l’envoi.`;
                };

                preview.addEventListener('loadedmetadata', validatePreview, { once: true });
                preview.addEventListener('canplay', validatePreview, { once: true });

                // Force Chrome/Edge à analyser réellement le conteneur WebM.
                preview.load();

                resetButtonsAfterStop();
                restoreText();
                stopMicrophone();
            };

            // Un seul Blob final : évite les WebM fragmentés ou illisibles.
            recorder.start();

            startedAt = Date.now();
            timer.textContent = '00:00';
            maxDurationWarning.classList.remove('show');
            timerInterval = setInterval(updateTimer, 500);

            if (hideTextDuringRecording && textBlock) {
                textBlock.classList.add('hidden-text');
                status.textContent = '🔒 Texte masqué — récitation de mémoire en cours…';
            } else {
                status.textContent = 'Enregistrement en cours… lisez le texte affiché.';
            }

            startButton.disabled = true;
            stopButton.disabled = false;
        } catch (error) {
            showRecordingError(
                'Accès au microphone refusé. Autorisez le microphone dans les paramètres du navigateur, puis recommencez.'
            );
        }
    });

    stopButton.addEventListener('click', () => {
        if (recorder && recorder.state === 'recording') {
            recorder.stop();
        }

        stopButton.disabled = true;
    });

    preview.addEventListener('error', () => {
        showRecordingError(
            'L’aperçu audio est illisible. Veuillez refaire l’enregistrement avec un autre navigateur.'
        );
    });

    form.addEventListener('submit', event => {
        const selectedFile = audioFile.files && audioFile.files[0];

        if (!recordedFileReady || !selectedFile || selectedFile.size < 1024) {
            event.preventDefault();
            status.textContent = 'Aucun enregistrement vocal valide n’est prêt à être envoyé.';
            submitButton.disabled = true;
        }
    });

    window.addEventListener('beforeunload', () => {
        clearInterval(timerInterval);
        stopMicrophone();

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }
    });
});

</script>
@endif

@endsection