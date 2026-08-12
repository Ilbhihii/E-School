@guest
<style>
.ssa-ai-launcher {
    position: fixed;
    right: 30px;
    bottom: 92px;
    z-index: 9995;
    width: 58px;
    height: 58px;
    border: 0;
    border-radius: 18px;
    color: #fff;
    cursor: pointer;
    display: grid;
    place-items: center;
    font-size: 1.35rem;
    background:
        linear-gradient(
            135deg,
            #4f6ff5 0%,
            #7656e8 52%,
            #ff7a45 100%
        );
    box-shadow:
        0 18px 45px rgba(79, 111, 245, .33);
    transition:
        transform .2s ease,
        box-shadow .2s ease;
}

.ssa-ai-launcher:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow:
        0 22px 55px rgba(79, 111, 245, .42);
}

.ssa-ai-launcher-dot {
    position: absolute;
    top: -3px;
    right: -3px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid #080c14;
    background: #22c55e;
}

.ssa-ai-tooltip {
    position: absolute;
    right: 70px;
    top: 50%;
    transform:
        translate(8px, -50%);
    padding: 8px 12px;
    border-radius: 10px;
    color: #fff;
    font-size: .77rem;
    font-weight: 600;
    white-space: nowrap;
    pointer-events: none;
    opacity: 0;
    background: rgba(15, 23, 42, .96);
    border: 1px solid rgba(255,255,255,.08);
    transition: .2s ease;
}

.ssa-ai-launcher:hover .ssa-ai-tooltip {
    opacity: 1;
    transform:
        translate(0, -50%);
}

.ssa-ai-panel {
    position: fixed;
    right: 30px;
    bottom: 165px;
    z-index: 9996;
    width: min(390px, calc(100vw - 32px));
    height: min(610px, calc(100vh - 210px));
    min-height: 480px;
    display: none;
    flex-direction: column;
    overflow: hidden;
    border-radius: 24px;
    background: #0c1422;
    border: 1px solid rgba(255,255,255,.09);
    box-shadow:
        0 28px 80px rgba(0,0,0,.48);
}

.ssa-ai-panel.is-open {
    display: flex;
    animation: ssaAiOpen .22s ease-out;
}

@keyframes ssaAiOpen {
    from {
        opacity: 0;
        transform:
            translateY(12px)
            scale(.98);
    }
    to {
        opacity: 1;
        transform:
            translateY(0)
            scale(1);
    }
}

.ssa-ai-head {
    position: relative;
    flex: 0 0 auto;
    padding: 18px 18px 16px;
    color: #fff;
    background:
        radial-gradient(
            circle at top right,
            rgba(255,122,69,.18),
            transparent 42%
        ),
        linear-gradient(
            135deg,
            #111c31,
            #10182a
        );
    border-bottom: 1px solid rgba(255,255,255,.07);
}

.ssa-ai-head-top {
    display: flex;
    align-items: center;
    gap: 12px;
}

.ssa-ai-logo {
    width: 42px;
    height: 42px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    border-radius: 14px;
    font-size: 1.05rem;
    background:
        linear-gradient(
            135deg,
            #4f6ff5,
            #805ad5,
            #ff7a45
        );
}

.ssa-ai-title {
    min-width: 0;
    flex: 1;
}

.ssa-ai-title strong {
    display: block;
    font-size: .96rem;
    line-height: 1.2;
}

.ssa-ai-status {
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #94a3b8;
    font-size: .71rem;
}

.ssa-ai-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #22c55e;
    box-shadow:
        0 0 0 4px rgba(34,197,94,.08);
}

.ssa-ai-close {
    width: 34px;
    height: 34px;
    border: 0;
    border-radius: 10px;
    cursor: pointer;
    color: #cbd5e1;
    background: rgba(255,255,255,.05);
}

.ssa-ai-close:hover {
    color: #fff;
    background: rgba(255,255,255,.09);
}

.ssa-ai-notice {
    margin-top: 12px;
    padding: 9px 11px;
    border-radius: 11px;
    color: #94a3b8;
    font-size: .68rem;
    line-height: 1.45;
    background: rgba(255,255,255,.035);
}

.ssa-ai-messages {
    flex: 1 1 auto;
    overflow-y: auto;
    padding: 18px 14px;
    scroll-behavior: smooth;
}

.ssa-ai-message-row {
    display: flex;
    margin-bottom: 13px;
}

.ssa-ai-message-row.user {
    justify-content: flex-end;
}

.ssa-ai-bubble {
    max-width: 84%;
    padding: 11px 13px;
    border-radius: 16px;
    font-size: .82rem;
    line-height: 1.55;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.ssa-ai-message-row.assistant
.ssa-ai-bubble {
    color: #dbe7f7;
    background: #151f30;
    border:
        1px solid rgba(255,255,255,.055);
    border-bottom-left-radius: 5px;
}

.ssa-ai-message-row.user
.ssa-ai-bubble {
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #4f6ff5,
            #6855d9
        );
    border-bottom-right-radius: 5px;
}

.ssa-ai-quick {
    display: flex;
    gap: 7px;
    overflow-x: auto;
    padding: 0 14px 12px;
    flex: 0 0 auto;
}

.ssa-ai-quick button {
    flex: 0 0 auto;
    padding: 7px 10px;
    border-radius: 999px;
    cursor: pointer;
    color: #cbd5e1;
    font-size: .69rem;
    border:
        1px solid rgba(255,255,255,.08);
    background: rgba(255,255,255,.04);
}

.ssa-ai-quick button:hover {
    color: #fff;
    border-color: rgba(79,111,245,.45);
    background: rgba(79,111,245,.10);
}

.ssa-ai-composer {
    flex: 0 0 auto;
    padding: 12px;
    border-top:
        1px solid rgba(255,255,255,.07);
    background: #0a111d;
}

.ssa-ai-input-wrap {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    padding: 7px 7px 7px 12px;
    border-radius: 16px;
    background: #121d2c;
    border:
        1px solid rgba(255,255,255,.075);
}

.ssa-ai-input {
    width: 100%;
    min-height: 38px;
    max-height: 108px;
    resize: none;
    border: 0;
    outline: 0;
    padding: 9px 0;
    color: #f8fafc;
    font: inherit;
    font-size: .8rem;
    line-height: 1.45;
    background: transparent;
}

.ssa-ai-input::placeholder {
    color: #64748b;
}

.ssa-ai-send {
    width: 40px;
    height: 40px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    border: 0;
    border-radius: 12px;
    cursor: pointer;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #4f6ff5,
            #7656e8
        );
}

.ssa-ai-send:disabled {
    cursor: wait;
    opacity: .55;
}

.ssa-ai-footnote {
    margin-top: 7px;
    color: #526176;
    text-align: center;
    font-size: .61rem;
}

.ssa-ai-typing {
    display: inline-flex;
    gap: 4px;
    align-items: center;
}

.ssa-ai-typing span {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #94a3b8;
    animation: ssaAiTyping 1s infinite;
}

.ssa-ai-typing span:nth-child(2) {
    animation-delay: .15s;
}

.ssa-ai-typing span:nth-child(3) {
    animation-delay: .3s;
}

@keyframes ssaAiTyping {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: .45;
    }
    30% {
        transform: translateY(-3px);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .ssa-ai-launcher {
        right: 16px;
        bottom: 78px;
        width: 52px;
        height: 52px;
        border-radius: 16px;
    }

    .ssa-ai-panel {
        right: 10px;
        bottom: 142px;
        width: calc(100vw - 20px);
        height: min(610px, calc(100vh - 160px));
    }

    .ssa-ai-tooltip {
        display: none;
    }
}
</style>

<button
    type="button"
    class="ssa-ai-launcher"
    id="ssaAiLauncher"
    aria-label="Ouvrir l'Assistant 2SA"
>
    <i class="bi bi-stars"></i>
    <span class="ssa-ai-launcher-dot"></span>
    <span class="ssa-ai-tooltip">
        Assistant IA 2SA
    </span>
</button>

<section
    class="ssa-ai-panel"
    id="ssaAiPanel"
    aria-label="Assistant IA 2SA"
>
    <header class="ssa-ai-head">
        <div class="ssa-ai-head-top">
            <div class="ssa-ai-logo">
                <i class="bi bi-stars"></i>
            </div>

            <div class="ssa-ai-title">
                <strong>Assistant IA 2SA</strong>
                <div class="ssa-ai-status">
                    <span class="ssa-ai-status-dot"></span>
                    Assistant visiteurs
                </div>
            </div>

            <button
                type="button"
                class="ssa-ai-close"
                id="ssaAiClose"
                aria-label="Fermer"
            >
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="ssa-ai-notice">
            Je peux vous renseigner sur l'inscription,
            les cours, le planning et les rendez-vous.
            Ne partagez pas d'informations sensibles.
        </div>
    </header>

    <div
        class="ssa-ai-messages"
        id="ssaAiMessages"
        aria-live="polite"
    >
        <div class="ssa-ai-message-row assistant">
            <div class="ssa-ai-bubble">
Bonjour 👋 Je suis l'Assistant 2SA.
Comment puis-je vous aider concernant Smart School Academy ?
            </div>
        </div>
    </div>

    <div
        class="ssa-ai-quick"
        id="ssaAiQuick"
    >
        <button
            type="button"
            data-question="Comment s'inscrire ?"
        >
            S'inscrire
        </button>

        <button
            type="button"
            data-question="Comment prendre rendez-vous ?"
        >
            Rendez-vous
        </button>

        <button
            type="button"
            data-question="Quels cours proposez-vous ?"
        >
            Les cours
        </button>

        <button
            type="button"
            data-question="Où voir le planning ?"
        >
            Planning
        </button>
    </div>

    <footer class="ssa-ai-composer">
        <div class="ssa-ai-input-wrap">
            <textarea
                id="ssaAiInput"
                class="ssa-ai-input"
                maxlength="1200"
                rows="1"
                placeholder="Écrivez votre question..."
                aria-label="Votre question"
            ></textarea>

            <button
                type="button"
                class="ssa-ai-send"
                id="ssaAiSend"
                aria-label="Envoyer"
            >
                <i class="bi bi-arrow-up"></i>
            </button>
        </div>

        <div class="ssa-ai-footnote">
            Réponses générées par IA · Vérifiez les informations importantes
        </div>
    </footer>
</section>

<script>
(function () {
    const launcher = document.getElementById(
        'ssaAiLauncher'
    );
    const panel = document.getElementById(
        'ssaAiPanel'
    );
    const close = document.getElementById(
        'ssaAiClose'
    );
    const messages = document.getElementById(
        'ssaAiMessages'
    );
    const input = document.getElementById(
        'ssaAiInput'
    );
    const send = document.getElementById(
        'ssaAiSend'
    );
    const quick = document.getElementById(
        'ssaAiQuick'
    );

    if (
        !launcher
        || !panel
        || !messages
        || !input
        || !send
    ) {
        return;
    }

    const endpoint =
        @json(route('visitor.ai-chatbot.chat'));

    const csrf =
        document
            .querySelector(
                'meta[name="csrf-token"]'
            )
            ?.getAttribute('content') || '';

    let conversation = [];
    let sending = false;

    function openPanel() {
        panel.classList.add('is-open');

        window.setTimeout(
            () => input.focus(),
            80
        );
    }

    function closePanel() {
        panel.classList.remove('is-open');
    }

    function scrollBottom() {
        messages.scrollTop =
            messages.scrollHeight;
    }

    function addMessage(
        role,
        content
    ) {
        const row = document.createElement('div');
        row.className =
            'ssa-ai-message-row ' + role;

        const bubble =
            document.createElement('div');
        bubble.className = 'ssa-ai-bubble';
        bubble.textContent = content;

        row.appendChild(bubble);
        messages.appendChild(row);
        scrollBottom();

        return row;
    }

    function addTyping() {
        const row = document.createElement('div');
        row.className =
            'ssa-ai-message-row assistant';
        row.id = 'ssaAiTypingRow';

        const bubble =
            document.createElement('div');
        bubble.className = 'ssa-ai-bubble';

        const typing =
            document.createElement('span');
        typing.className = 'ssa-ai-typing';

        for (let i = 0; i < 3; i++) {
            typing.appendChild(
                document.createElement('span')
            );
        }

        bubble.appendChild(typing);
        row.appendChild(bubble);
        messages.appendChild(row);
        scrollBottom();
    }

    function removeTyping() {
        document
            .getElementById('ssaAiTypingRow')
            ?.remove();
    }

    function autoResize() {
        input.style.height = 'auto';

        input.style.height =
            Math.min(
                input.scrollHeight,
                108
            ) + 'px';
    }

    async function sendMessage(
        preset = null
    ) {
        if (sending) {
            return;
        }

        const message =
            String(
                preset ?? input.value
            ).trim();

        if (!message) {
            return;
        }

        sending = true;
        send.disabled = true;

        addMessage(
            'user',
            message
        );

        const historyToSend =
            conversation.slice(-10);

        conversation.push({
            role: 'user',
            content: message,
        });

        input.value = '';
        autoResize();
        addTyping();

        try {
            const response = await fetch(
                endpoint,
                {
                    method: 'POST',
                    headers: {
                        'Accept':
                            'application/json',
                        'Content-Type':
                            'application/json',
                        'X-CSRF-TOKEN':
                            csrf,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        message: message,
                        conversation:
                            historyToSend,
                    }),
                }
            );

            let data = {};

            try {
                data = await response.json();
            } catch (error) {
                data = {};
            }

            removeTyping();

            if (
                !response.ok
                || !data.success
            ) {
                addMessage(
                    'assistant',
                    data.message
                    || 'L’assistant est momentanément indisponible.'
                );
                return;
            }

            const answer =
                String(
                    data.message || ''
                ).trim();

            if (!answer) {
                throw new Error(
                    'Empty chatbot response'
                );
            }

            conversation.push({
                role: 'assistant',
                content: answer,
            });

            /*
             * Historique court uniquement.
             * Rien n'est stocké dans le navigateur
             * après rechargement de la page.
             */
            conversation =
                conversation.slice(-10);

            addMessage(
                'assistant',
                answer
            );
        } catch (error) {
            removeTyping();

            addMessage(
                'assistant',
                'Je rencontre un problème de connexion. '
                + 'Vous pouvez nous écrire à '
                + 'contact.smartschoolacademy@gmail.com.'
            );
        } finally {
            sending = false;
            send.disabled = false;
            input.focus();
        }
    }

    launcher.addEventListener(
        'click',
        openPanel
    );

    close.addEventListener(
        'click',
        closePanel
    );

    send.addEventListener(
        'click',
        () => sendMessage()
    );

    input.addEventListener(
        'input',
        autoResize
    );

    input.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key === 'Enter'
                && !event.shiftKey
            ) {
                event.preventDefault();
                sendMessage();
            }
        }
    );

    quick?.addEventListener(
        'click',
        function (event) {
            const button =
                event.target.closest(
                    '[data-question]'
                );

            if (!button) {
                return;
            }

            openPanel();

            sendMessage(
                button.dataset.question
            );
        }
    );

    document.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key === 'Escape'
                && panel.classList.contains(
                    'is-open'
                )
            ) {
                closePanel();
            }
        }
    );
})();
</script>
@endguest
