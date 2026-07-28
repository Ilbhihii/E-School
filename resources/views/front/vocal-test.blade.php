@extends('layouts.front')

@section('title', 'Test vocal — ' . $subject->name)

@section('content')

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
            <span class="badge vocal-badge @if($prompt->test_mode === 'tajwid') mode-tajwid @elseif($prompt->test_mode === 'hifd') mode-hifd @endif">
                @if ($prompt->test_mode === 'hifd')
                    <i class="bi bi-lock-fill"></i>
                @elseif ($prompt->test_mode === 'tajwid')
                    <i class="bi bi-music-note"></i>
                @else
                    <i class="bi bi-mic-fill"></i>
                @endif
                {{ $subject->name }} · {{ \App\Models\VocalTestPrompt::getModes()[$prompt->test_mode] ?? 'Lecture' }}
            </span>

            <h1 class="vocal-title">
                {{ $prompt->title }}
            </h1>

            <p class="vocal-subtitle">
                {{ $level->name }} · {{ $class->name }}
            </p>
        </div>

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
                        Durée maximale : {{ $prompt->maximum_duration }} secondes
                        @if ($prompt->preparation_seconds > 0)
                            · Préparation : {{ $prompt->preparation_seconds }}s
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
                        Mémorisation · Max {{ $prompt->maximum_duration }}s
                    @elseif ($prompt->test_mode === 'tajwid')
                        <i class="bi bi-music-note"></i>
                        Tajwid · Max {{ $prompt->maximum_duration }}s
                    @else
                        <i class="bi bi-clock"></i>
                        Max {{ $prompt->maximum_duration }} secondes
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

                <input type="file" name="audio" id="audioFile" accept="audio/*,video/webm" hidden required>
                <input type="hidden" name="duration_seconds" id="durationSeconds" value="">

                @error('audio')
                    <div class="alert mb-4" style="background: rgba(239, 68, 68, 0.14); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 12px;">
                        {{ $message }}
                    </div>
                @enderror

                <div class="text-center">

                    <!-- ═══ STATUS ═══ -->
                    <div id="recordingStatus" class="vocal-status">
                        @if ($prompt->test_mode === 'hifd')
                            Prenez le temps de mémoriser le texte, puis commencez l'enregistrement.
                        @else
                            Autorisez le microphone puis commencez l'enregistrement.
                        @endif
                    </div>

                    <div id="maxDurationWarning" class="vocal-max-warning">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Vous approchez de la durée maximale autorisée ({{ $prompt->maximum_duration }} secondes).
                    </div>

                    <div id="timer" class="vocal-timer">00:00</div>

                    <div class="vocal-actions">
                        <button type="button" id="startRecording" class="btn-3d btn-3d-gradient vocal-action-button">
                            <i class="bi bi-mic-fill me-2"></i>
                            @if ($prompt->test_mode === 'hifd')
                                Commencer la récitation
                            @else
                                Commencer
                            @endif
                        </button>

                        <button type="button" id="stopRecording" class="btn-3d vocal-stop-button" disabled>
                            <i class="bi bi-stop-fill me-2"></i> Arrêter
                        </button>
                    </div>

                    <div id="previewBlock" class="vocal-preview" hidden>
                        <p class="text-white-50 mb-2">
                            <i class="bi bi-check-circle-fill me-1" style="color: #4ade80;"></i>
                            Écoutez votre enregistrement avant de continuer.
                        </p>
                        <audio id="audioPreview" controls></audio>
                    </div>

                    <button type="submit" id="submitRecording" class="btn-3d btn-3d-gradient vocal-submit-button" disabled>
                        <i class="bi bi-send-fill me-2"></i>
                        Envoyer et continuer vers le rendez-vous
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>

                </div>
            </form>
        </div>

    </div>
</section>

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

@endsection