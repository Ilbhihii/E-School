@extends('layouts.front')

@section('title', 'Test vocal — Coran')

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

        padding: 0.65rem 1.15rem;
        margin-bottom: 1.35rem;

        background: rgba(124, 58, 237, 0.15);
        color: #c4b5fd;

        border: 1px solid rgba(167, 139, 250, 0.25);
        border-radius: 999px;

        font-size: 0.9rem;
        font-weight: 600;
    }

    .vocal-title {
        margin: 0 0 1rem;
        padding: 0;

        font-family: 'Poppins', 'Inter', sans-serif;
        font-size: clamp(2rem, 5vw, 3.4rem);
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.04em;

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

    .vocal-recitation-text {
        padding: 1.75rem;

        font-family: 'Amiri', 'Noto Naskh Arabic', serif;
        font-size: 1.8rem;
        line-height: 2.25;

        text-align: center;
        color: #ffffff;

        background: rgba(255, 255, 255, 0.035);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
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

    @media (max-width: 768px) {
        .vocal-test-section {
            padding-top: 2.5rem;
            padding-bottom: 3rem;
        }

        .vocal-header {
            margin-bottom: 2rem;
            padding-top: 0;
        }

        .vocal-badge {
            margin-bottom: 1rem;
        }

        .vocal-title {
            font-size: 2rem;
            line-height: 1.25;
            letter-spacing: -0.025em;
        }

        .vocal-subtitle {
            font-size: 0.95rem;
        }

        .vocal-recitation-text {
            padding: 1.25rem;

            font-size: 1.45rem;
            line-height: 2;
        }

        .vocal-card-header {
            align-items: flex-start;
            gap: 0.85rem;
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
            <span class="badge vocal-badge">
                <i class="bi bi-mic-fill me-1"></i>
                Test vocal Coran
            </span>

            <h1 class="vocal-title">
                Lisez puis enregistrez votre récitation
            </h1>

            <p class="vocal-subtitle">
                {{ $level->name }} · {{ $class->name }}
            </p>
        </div>

        <div class="card-3d p-4 p-md-5 vocal-recitation-card">
            <div class="vocal-card-header">

                <div class="card-3d-icon vocal-card-icon">
                    <i class="bi bi-book-half text-white"></i>
                </div>

                <div>
                    <h5 class="text-white mb-1">
                        Texte à réciter
                    </h5>

                    <small class="text-white-50">
                        Lisez calmement et distinctement.
                    </small>
                </div>
            </div>

            <div
                dir="rtl"
                lang="ar"
                class="vocal-recitation-text"
            >
                {{ $recitationText }}
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

                @error('audio')
                    <div
                        class="alert mb-4"
                        style="
                            background: rgba(239, 68, 68, 0.14);
                            color: #fca5a5;
                            border: 1px solid rgba(239, 68, 68, 0.25);
                            border-radius: 12px;
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
                        Autorisez le microphone puis commencez l’enregistrement.
                    </div>

                    <div
                        id="timer"
                        class="vocal-timer"
                    >
                        00:00
                    </div>

                    <div class="vocal-actions">

                        <button
                            type="button"
                            id="startRecording"
                            class="btn-3d btn-3d-gradient vocal-action-button"
                        >
                            <i class="bi bi-mic-fill me-2"></i>
                            Commencer
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
                                style="color: #4ade80;"
                            ></i>

                            Écoutez votre récitation avant de continuer.
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
                        Continuer vers le rendez-vous

                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>

                </div>
            </form>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const startButton = document.getElementById('startRecording');
    const stopButton = document.getElementById('stopRecording');
    const submitButton = document.getElementById('submitRecording');
    const status = document.getElementById('recordingStatus');
    const timer = document.getElementById('timer');
    const previewBlock = document.getElementById('previewBlock');
    const preview = document.getElementById('audioPreview');
    const audioFile = document.getElementById('audioFile');

    let recorder;
    let stream;
    let chunks = [];
    let timerInterval;
    let startedAt;
    let previewUrl = null;

    const updateTimer = () => {
        const seconds = Math.floor(
            (Date.now() - startedAt) / 1000
        );

        const minutes = String(
            Math.floor(seconds / 60)
        ).padStart(2, '0');

        const remainingSeconds = String(
            seconds % 60
        ).padStart(2, '0');

        timer.textContent = `${minutes}:${remainingSeconds}`;
    };

    startButton.addEventListener('click', async () => {
        if (
            !navigator.mediaDevices ||
            !navigator.mediaDevices.getUserMedia ||
            !window.MediaRecorder
        ) {
            status.textContent =
                'Votre navigateur ne prend pas en charge l’enregistrement vocal.';

            return;
        }

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                audio: true
            });

            let preferredType = '';

            if (
                typeof MediaRecorder.isTypeSupported === 'function' &&
                MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
            ) {
                preferredType = 'audio/webm;codecs=opus';
            }

            recorder = new MediaRecorder(
                stream,
                preferredType
                    ? { mimeType: preferredType }
                    : undefined
            );

            chunks = [];

            recorder.ondataavailable = event => {
                if (event.data && event.data.size > 0) {
                    chunks.push(event.data);
                }
            };

            recorder.onerror = () => {
                status.textContent =
                    'Une erreur est survenue pendant l’enregistrement.';

                clearInterval(timerInterval);

                startButton.disabled = false;
                stopButton.disabled = true;

                if (stream) {
                    stream
                        .getTracks()
                        .forEach(track => track.stop());
                }
            };

            recorder.onstop = () => {
                clearInterval(timerInterval);

                const mimeType =
                    recorder.mimeType || 'audio/webm';

                const blob = new Blob(chunks, {
                    type: mimeType
                });

                const file = new File(
                    [blob],
                    `recitation-${Date.now()}.webm`,
                    { type: mimeType }
                );

                const transfer = new DataTransfer();
                transfer.items.add(file);

                audioFile.files = transfer.files;

                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                }

                previewUrl = URL.createObjectURL(blob);
                preview.src = previewUrl;

                previewBlock.hidden = false;
                submitButton.disabled = false;

                status.textContent =
                    'Enregistrement terminé et prêt à être envoyé.';

                if (stream) {
                    stream
                        .getTracks()
                        .forEach(track => track.stop());
                }

                startButton.disabled = false;
                stopButton.disabled = true;
            };

            recorder.start(250);

            startedAt = Date.now();
            timer.textContent = '00:00';

            timerInterval = setInterval(
                updateTimer,
                500
            );

            status.textContent =
                'Enregistrement en cours… lisez le texte affiché.';

            startButton.disabled = true;
            stopButton.disabled = false;
            submitButton.disabled = true;
            previewBlock.hidden = true;

        } catch (error) {
            status.textContent =
                'Accès au microphone refusé. Autorisez le microphone dans votre navigateur.';

            startButton.disabled = false;
            stopButton.disabled = true;
        }
    });

    stopButton.addEventListener('click', () => {
        if (
            recorder &&
            recorder.state === 'recording'
        ) {
            recorder.stop();
        }

        clearInterval(timerInterval);

        stopButton.disabled = true;
    });

    window.addEventListener('beforeunload', () => {
        clearInterval(timerInterval);

        if (stream) {
            stream
                .getTracks()
                .forEach(track => track.stop());
        }

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }
    });
});
</script>

@endsection