@extends('layouts.front')

@section('title', 'Test vocal — Coran')

@section('content')
<section class="py-5">
    <div class="container" style="max-width:900px;">
        <div class="text-center mb-4">
            <span class="badge px-3 py-2 mb-3" style="background:rgba(124,58,237,.15);color:#C4B5FD;border-radius:20px;">
                <i class="bi bi-mic-fill me-1"></i> Test vocal Coran
            </span>
            <h1 class="section-title-3d">Lisez puis enregistrez votre récitation</h1>
            <p class="text-white-50">{{ $level->name }} · {{ $class->name }}</p>
        </div>

        <div class="card-3d p-4 p-md-5 mb-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="card-3d-icon" style="width:48px;height:48px;background:linear-gradient(135deg,#7C3AED,#2563EB);margin:0;">
                    <i class="bi bi-book-half text-white"></i>
                </div>
                <div>
                    <h5 class="text-white mb-1">Texte à réciter</h5>
                    <small class="text-white-50">Lisez calmement et distinctement.</small>
                </div>
            </div>

            <div dir="rtl" lang="ar" style="font-family:'Amiri','Noto Naskh Arabic',serif;font-size:1.8rem;line-height:2.25;text-align:center;color:#fff;padding:1.5rem;background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.08);border-radius:18px;">
                {{ $recitationText }}
            </div>
        </div>

        <div class="card-3d p-4 p-md-5">
            <form method="POST" action="{{ route('vocal-test.store', [$subject, $level, $class]) }}" enctype="multipart/form-data" id="vocalTestForm">
                @csrf
                <input type="file" name="audio" id="audioFile" accept="audio/*,video/webm" hidden required>

                @error('audio')
                    <div class="alert mb-4" style="background:rgba(239,68,68,.14);color:#FCA5A5;border:1px solid rgba(239,68,68,.25);border-radius:12px;">{{ $message }}</div>
                @enderror

                <div class="text-center">
                    <div id="recordingStatus" class="text-white-50 mb-3">Autorisez le microphone puis commencez l’enregistrement.</div>
                    <div id="timer" class="fw-bold mb-4" style="font-size:2rem;color:#fff;font-variant-numeric:tabular-nums;">00:00</div>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <button type="button" id="startRecording" class="btn-3d btn-3d-gradient" style="min-width:190px;">
                            <i class="bi bi-mic-fill me-2"></i> Commencer
                        </button>
                        <button type="button" id="stopRecording" class="btn-3d" disabled style="min-width:190px;background:linear-gradient(135deg,#DC2626,#EF4444);color:#fff;border:0;">
                            <i class="bi bi-stop-fill me-2"></i> Arrêter
                        </button>
                    </div>

                    <div id="previewBlock" class="mt-4" hidden>
                        <p class="text-white-50 mb-2"><i class="bi bi-check-circle-fill me-1" style="color:#4ADE80;"></i> Écoutez votre récitation avant de continuer.</p>
                        <audio id="audioPreview" controls style="width:min(100%,520px);"></audio>
                    </div>

                    <button type="submit" id="submitRecording" class="btn-3d btn-3d-gradient w-100 mt-4" disabled style="padding:14px;">
                        Continuer vers le rendez-vous <i class="bi bi-arrow-right ms-2"></i>
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

    const updateTimer = () => {
        const seconds = Math.floor((Date.now() - startedAt) / 1000);
        timer.textContent = `${String(Math.floor(seconds / 60)).padStart(2, '0')}:${String(seconds % 60).padStart(2, '0')}`;
    };

    startButton.addEventListener('click', async () => {
        if (!navigator.mediaDevices || !window.MediaRecorder) {
            status.textContent = 'Votre navigateur ne prend pas en charge l’enregistrement vocal.';
            return;
        }

        try {
            stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            const preferredType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ? 'audio/webm;codecs=opus' : '';
            recorder = new MediaRecorder(stream, preferredType ? { mimeType: preferredType } : undefined);
            chunks = [];
            recorder.ondataavailable = event => { if (event.data.size) chunks.push(event.data); };
            recorder.onstop = () => {
                const mimeType = recorder.mimeType || 'audio/webm';
                const blob = new Blob(chunks, { type: mimeType });
                const file = new File([blob], `recitation-${Date.now()}.webm`, { type: mimeType });
                const transfer = new DataTransfer();
                transfer.items.add(file);
                audioFile.files = transfer.files;
                preview.src = URL.createObjectURL(blob);
                previewBlock.hidden = false;
                submitButton.disabled = false;
                status.textContent = 'Enregistrement terminé et prêt à être envoyé.';
                stream.getTracks().forEach(track => track.stop());
            };
            recorder.start(250);
            startedAt = Date.now();
            timer.textContent = '00:00';
            timerInterval = setInterval(updateTimer, 500);
            status.textContent = 'Enregistrement en cours… lisez le texte affiché.';
            startButton.disabled = true;
            stopButton.disabled = false;
            submitButton.disabled = true;
            previewBlock.hidden = true;
        } catch (error) {
            status.textContent = 'Accès au microphone refusé. Autorisez le microphone dans votre navigateur.';
        }
    });

    stopButton.addEventListener('click', () => {
        if (recorder && recorder.state === 'recording') recorder.stop();
        clearInterval(timerInterval);
        startButton.disabled = false;
        stopButton.disabled = true;
    });
});
</script>
@endsection
