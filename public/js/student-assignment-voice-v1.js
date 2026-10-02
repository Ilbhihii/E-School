(() => {
    'use strict';

    const MAX_BYTES = 2 * 1024 * 1024 * 1024;

    function init() {
        const root = document.getElementById('assignmentVoiceRecorder');
        const form = root ? root.closest('form') : null;
        const startButton = document.getElementById('assignmentVoiceStart');
        const stopButton = document.getElementById('assignmentVoiceStop');
        const clearButton = document.getElementById('assignmentVoiceClear');
        const status = document.getElementById('assignmentVoiceStatus');
        const preview = document.getElementById('assignmentVoicePreview');
        const voiceInput = document.getElementById('assignmentVoiceInput');
        const durationInput = document.getElementById('assignmentVoiceDuration');
        const fileInput = document.getElementById('assignmentFile');

        if (!root || !form || !startButton || !stopButton || !voiceInput) {
            return;
        }

        if (!navigator.mediaDevices || !window.MediaRecorder) {
            startButton.disabled = true;
            status.textContent = 'Enregistrement vocal non pris en charge par ce navigateur.';
            return;
        }

        let recorder = null;
        let stream = null;
        let chunks = [];
        let startedAt = 0;
        let timer = null;
        let previewUrl = null;

        const formatTime = seconds => {
            const safe = Math.max(0, Number(seconds || 0));
            const minutes = Math.floor(safe / 60);
            const secs = safe % 60;
            return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        };

        const updateTimer = () => {
            if (!startedAt) return;
            const seconds = Math.floor((Date.now() - startedAt) / 1000);
            status.textContent = `Enregistrement en cours · ${formatTime(seconds)}`;
        };

        const pickMimeType = () => {
            const candidates = [
                'audio/webm;codecs=opus',
                'audio/webm',
                'audio/ogg;codecs=opus',
                'audio/mp4',
            ];

            return candidates.find(type => MediaRecorder.isTypeSupported(type)) || '';
        };

        const extensionFor = mime => {
            if (mime.includes('ogg')) return 'ogg';
            if (mime.includes('mp4')) return 'm4a';
            if (mime.includes('mpeg')) return 'mp3';
            return 'webm';
        };

        const stopTracks = () => {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        };

        const clearVoice = () => {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }

            voiceInput.value = '';
            if (durationInput) durationInput.value = '';
            if (preview) {
                preview.removeAttribute('src');
                preview.hidden = true;
                preview.load();
            }
            clearButton.hidden = true;
            status.textContent = 'Aucun vocal enregistré.';
            status.classList.remove('is-recording');
        };

        startButton.addEventListener('click', async () => {
            try {
                clearVoice();
                stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                const mimeType = pickMimeType();
                recorder = mimeType
                    ? new MediaRecorder(stream, { mimeType })
                    : new MediaRecorder(stream);

                chunks = [];
                startedAt = Date.now();

                recorder.addEventListener('dataavailable', event => {
                    if (event.data && event.data.size > 0) {
                        chunks.push(event.data);
                    }
                });

                recorder.addEventListener('stop', () => {
                    window.clearInterval(timer);
                    timer = null;
                    stopTracks();

                    const duration = Math.max(
                        1,
                        Math.round((Date.now() - startedAt) / 1000)
                    );
                    startedAt = 0;

                    const mime = recorder.mimeType || 'audio/webm';
                    const blob = new Blob(chunks, { type: mime });
                    chunks = [];

                    if (blob.size > MAX_BYTES) {
                        clearVoice();
                        alert('Le vocal dépasse 2 Go. Il ne peut pas être envoyé.');
                        return;
                    }

                    const extension = extensionFor(mime);
                    const file = new File(
                        [blob],
                        `devoir-vocal-${Date.now()}.${extension}`,
                        { type: mime }
                    );

                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    voiceInput.files = transfer.files;

                    if (durationInput) durationInput.value = String(duration);

                    previewUrl = URL.createObjectURL(blob);
                    preview.src = previewUrl;
                    preview.hidden = false;
                    clearButton.hidden = false;
                    status.textContent = `Vocal prêt · ${formatTime(duration)}`;
                    status.classList.remove('is-recording');

                    startButton.disabled = false;
                    stopButton.disabled = true;
                });

                recorder.start(1000);
                startButton.disabled = true;
                stopButton.disabled = false;
                clearButton.hidden = true;
                status.classList.add('is-recording');
                updateTimer();
                timer = window.setInterval(updateTimer, 1000);
            } catch (error) {
                stopTracks();
                startButton.disabled = false;
                stopButton.disabled = true;
                status.classList.remove('is-recording');
                status.textContent = 'Microphone indisponible ou autorisation refusée.';
            }
        });

        stopButton.addEventListener('click', () => {
            if (recorder && recorder.state !== 'inactive') {
                recorder.stop();
            }
        });

        clearButton.addEventListener('click', clearVoice);

        form.addEventListener('submit', event => {
            const hasFile = Boolean(fileInput && fileInput.files && fileInput.files.length);
            const hasVoice = Boolean(voiceInput.files && voiceInput.files.length);

            if (!hasFile && !hasVoice) {
                event.preventDefault();
                alert('Ajoutez un fichier ou enregistrez un vocal avant l’envoi.');
                return;
            }

            if (hasFile && fileInput.files[0].size > MAX_BYTES) {
                event.preventDefault();
                alert('Le fichier dépasse 2 Go.');
                return;
            }

            if (hasVoice && voiceInput.files[0].size > MAX_BYTES) {
                event.preventDefault();
                alert('Le vocal dépasse 2 Go.');
            }
        });

        window.addEventListener('beforeunload', stopTracks);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
