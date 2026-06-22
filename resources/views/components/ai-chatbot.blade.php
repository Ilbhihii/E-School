<!-- ══════════════════════════════════════════════════════
     AI CHATBOT — Smart School Academy
     ══════════════════════════════════════════════════════ -->

<style>
/* ── AI CHATBOT FLOATING BUTTON ── */
.ai-chatbot-btn {
    position: fixed;
    bottom: 156px;
    right: 30px;
    z-index: 9994;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7C3AED, #003A8F);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.35);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    animation: aiBotBounce 2.5s ease-in-out infinite;
}
.ai-chatbot-btn:hover {
    transform: scale(1.1) translateY(-4px);
    box-shadow: 0 12px 40px rgba(124, 58, 237, 0.5);
    color: white;
}
.ai-chatbot-btn .bot-tooltip {
    position: absolute;
    right: 66px;
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(10px);
    color: white;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.8rem;
    white-space: nowrap;
    opacity: 0;
    transform: translateX(10px);
    transition: all 0.3s ease;
    pointer-events: none;
    border: 1px solid rgba(255, 255, 255, 0.06);
}
.ai-chatbot-btn:hover .bot-tooltip {
    opacity: 1;
    transform: translateX(0);
}

/* ── PULSE DOT ── */
.ai-chatbot-btn .pulse-dot {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 14px;
    height: 14px;
    background: #22C55E;
    border: 2px solid #080c14;
    border-radius: 50%;
    animation: livePulse 2s ease-in-out infinite;
}

@keyframes aiBotBounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
@keyframes livePulse {
    0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); }
    50% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
    100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

/* ── AI CHAT WINDOW ── */
.ai-chat-window {
    position: fixed;
    bottom: 220px;
    right: 30px;
    z-index: 9993;
    width: 380px;
    max-height: 520px;
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(24px) saturate(1.5);
    -webkit-backdrop-filter: blur(24px) saturate(1.5);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(124, 58, 237, 0.1);
    display: none;
    flex-direction: column;
    overflow: hidden;
    transform-origin: bottom right;
    animation: chatWindowIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.ai-chat-window.open {
    display: flex;
}

@keyframes chatWindowIn {
    0% {
        opacity: 0;
        transform: scale(0.8) translateY(20px);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* ── CHAT HEADER ── */
.ai-chat-header {
    background: linear-gradient(135deg, #7C3AED, #003A8F);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}
.ai-chat-header-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.ai-chat-header-info {
    flex: 1;
    min-width: 0;
}
.ai-chat-header-info h6 {
    margin: 0;
    font-weight: 700;
    font-size: 0.9rem;
    color: white;
    font-family: 'Poppins', sans-serif;
}
.ai-chat-header-info small {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    gap: 4px;
}
.ai-chat-header-info small .status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #22C55E;
    display: inline-block;
    animation: livePulse 2s ease-in-out infinite;
}
.ai-chat-close-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: rgba(255, 255, 255, 0.6);
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: pointer;
    flex-shrink: 0;
}
.ai-chat-close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

/* ── CHAT MESSAGES ── */
.ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 280px;
    max-height: 340px;
    scroll-behavior: smooth;
}
.ai-chat-messages::-webkit-scrollbar { width: 4px; }
.ai-chat-messages::-webkit-scrollbar-track { background: transparent; }
.ai-chat-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 4px; }

/* ── MESSAGE BUBBLES ── */
.ai-msg {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 0.85rem;
    line-height: 1.6;
    animation: aiMsgIn 0.3s ease-out;
    word-wrap: break-word;
}
@keyframes aiMsgIn {
    0% { opacity: 0; transform: translateY(8px) scale(0.96); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}

.ai-msg.bot {
    align-self: flex-start;
    background: rgba(124, 58, 237, 0.12);
    border: 1px solid rgba(124, 58, 237, 0.15);
    color: rgba(255, 255, 255, 0.9);
    border-bottom-left-radius: 4px;
}
.ai-msg.user {
    align-self: flex-end;
    background: linear-gradient(135deg, #7C3AED, #003A8F);
    color: white;
    border-bottom-right-radius: 4px;
}

.ai-msg .msg-time {
    font-size: 0.6rem;
    opacity: 0.4;
    margin-top: 4px;
    display: block;
    text-align: right;
}

/* ── TYPING INDICATOR ── */
.ai-typing {
    align-self: flex-start;
    display: none;
    align-items: center;
    gap: 6px;
    padding: 12px 18px;
    background: rgba(124, 58, 237, 0.1);
    border: 1px solid rgba(124, 58, 237, 0.1);
    border-radius: 16px;
    border-bottom-left-radius: 4px;
}
.ai-typing.active {
    display: flex;
}
.ai-typing span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: rgba(124, 58, 237, 0.4);
    animation: typingBounce 1.4s ease-in-out infinite;
}
.ai-typing span:nth-child(2) { animation-delay: 0.2s; }
.ai-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingBounce {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-8px); background: rgba(124, 58, 237, 0.8); }
}

/* ── CHAT INPUT ── */
.ai-chat-input-area {
    padding: 12px 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    display: flex;
    gap: 8px;
    flex-shrink: 0;
    background: rgba(0, 0, 0, 0.15);
}
.ai-chat-input {
    flex: 1;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 10px 14px;
    color: white;
    font-size: 0.85rem;
    outline: none;
    transition: all 0.3s ease;
    font-family: inherit;
}
.ai-chat-input::placeholder {
    color: rgba(255, 255, 255, 0.25);
}
.ai-chat-input:focus {
    border-color: rgba(124, 58, 237, 0.4);
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}
.ai-chat-send-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #7C3AED, #003A8F);
    border: none;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    flex-shrink: 0;
}
.ai-chat-send-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.3);
}
.ai-chat-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* ── SUGGESTED QUESTIONS ── */
.ai-suggestions {
    padding: 8px 16px 4px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    flex-shrink: 0;
}
.ai-suggestion-chip {
    background: rgba(124, 58, 237, 0.08);
    border: 1px solid rgba(124, 58, 237, 0.12);
    color: rgba(255, 255, 255, 0.7);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}
.ai-suggestion-chip:hover {
    background: rgba(124, 58, 237, 0.2);
    border-color: rgba(124, 58, 237, 0.3);
    color: white;
}

/* ── WELCOME MESSAGE ── */
.ai-welcome {
    text-align: center;
    padding: 20px 16px;
}
.ai-welcome-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(124, 58, 237, 0.15), rgba(0, 58, 143, 0.15));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 12px;
    color: #A78BFA;
}
.ai-welcome h6 {
    font-weight: 700;
    font-size: 0.95rem;
    color: white;
    font-family: 'Poppins', sans-serif;
}
.ai-welcome p {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.5);
    margin: 4px 0 0;
    line-height: 1.5;
}

/* ── ERROR STATE ── */
.ai-msg.error {
    background: rgba(239, 68, 68, 0.1);
    border-color: rgba(239, 68, 68, 0.2);
    color: #FCA5A5;
}

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
    .ai-chatbot-btn {
        bottom: 140px;
        right: 16px;
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
    }
    .ai-chat-window {
        right: 8px;
        bottom: 196px;
        width: calc(100% - 16px);
        max-width: 400px;
        max-height: 480px;
    }
}
</style>

<!-- ═══ AI CHATBOT FLOATING BUTTON ═══ -->
<button class="ai-chatbot-btn" id="aiChatbotBtn" aria-label="Assistant IA" onclick="toggleAIChat()">
    <i class="bi bi-robot"></i>
    <span class="pulse-dot"></span>
    <span class="bot-tooltip">Posez-moi une question !</span>
</button>

<!-- ═══ AI CHAT WINDOW ═══ -->
<div class="ai-chat-window" id="aiChatWindow">
    <!-- Header -->
    <div class="ai-chat-header">
        <div class="ai-chat-header-avatar">
            <i class="bi bi-magic"></i>
        </div>
        <div class="ai-chat-header-info">
            <h6>Assistant IA</h6>
            <small>
                <span class="status-dot"></span>
                En ligne • Smart School Academy
            </small>
        </div>
        <button class="ai-chat-close-btn" onclick="toggleAIChat()" aria-label="Fermer">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Suggested Questions -->
    <div class="ai-suggestions">
        <span class="ai-suggestion-chip" onclick="askAI('Comment créer un compte ?')">📝 Créer un compte</span>
        <span class="ai-suggestion-chip" onclick="askAI('Quels sont les tarifs ?')">💰 Tarifs</span>
        <span class="ai-suggestion-chip" onclick="askAI('Comment se passe un test de niveau ?')">📋 Test de niveau</span>
        <span class="ai-suggestion-chip" onclick="askAI('Quels cours sont disponibles ?')">📚 Cours</span>
    </div>

    <!-- Messages -->
    <div class="ai-chat-messages" id="aiChatMessages">
        <div class="ai-welcome">
            <div class="ai-welcome-icon">
                <i class="bi bi-chat-dots"></i>
            </div>
            <h6>👋 Bonjour !</h6>
            <p>
                Je suis l'assistant IA de Smart School Academy.<br>
                Posez-moi une question sur la plateforme, les cours,<br>
                l'inscription ou les tarifs !
            </p>
        </div>
    </div>

    <!-- Typing Indicator -->
    <div class="ai-typing" id="aiTyping">
        <span></span><span></span><span></span>
    </div>

    <!-- Input -->
    <div class="ai-chat-input-area">
        <input type="text" class="ai-chat-input" id="aiChatInput"
               placeholder="Posez votre question..." 
               maxlength="500"
               onkeydown="if(event.key==='Enter') sendAI()">
        <button class="ai-chat-send-btn" id="aiChatSendBtn" onclick="sendAI()" aria-label="Envoyer">
            <i class="bi bi-send-fill"></i>
        </button>
    </div>
</div>

<script>
(function() {
    'use strict';

    let conversationHistory = [];
    let isProcessing = false;
    let chatInitialized = false;

    window.toggleAIChat = function() {
        const window = document.getElementById('aiChatWindow');
        const btn = document.getElementById('aiChatbotBtn');
        const isOpen = window.classList.contains('open');

        window.classList.toggle('open');
        btn.style.display = isOpen ? 'flex' : 'none';

        if (!isOpen && !chatInitialized) {
            chatInitialized = true;
            // Add welcome message to conversation context
            conversationHistory.push({
                role: 'assistant',
                content: '👋 Bonjour ! Je suis l\'assistant IA de Smart School Academy. Posez-moi une question sur la plateforme, les cours, l\'inscription ou les tarifs !'
            });
        }

        if (!isOpen) {
            setTimeout(() => {
                document.getElementById('aiChatInput')?.focus();
            }, 400);
        }
    };

    window.askAI = function(question) {
        // Open chat window if not already open
        const window = document.getElementById('aiChatWindow');
        if (!window.classList.contains('open')) {
            window.classList.add('open');
            document.getElementById('aiChatbotBtn').style.display = 'none';
        }

        document.getElementById('aiChatInput').value = question;
        sendAI();
    };

    window.sendAI = function() {
        if (isProcessing) return;

        const input = document.getElementById('aiChatInput');
        const message = input.value.trim();
        if (!message) return;

        input.value = '';
        isProcessing = true;
        document.getElementById('aiChatSendBtn').disabled = true;

        // Remove welcome if it's the first message
        const welcome = document.querySelector('.ai-welcome');
        if (welcome) welcome.remove();

        // Add user message
        addMessage(message, 'user');

        // Show typing indicator
        document.getElementById('aiTyping').classList.add('active');

        // Scroll to bottom
        scrollToBottom();

        // Call API
        fetch('{{ route("ai.chatbot") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                message: message,
                conversation: conversationHistory,
            }),
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('aiTyping').classList.remove('active');

            if (data.success) {
                addMessage(data.message, 'bot');
                conversationHistory.push({ role: 'user', content: message });
                conversationHistory.push({ role: 'assistant', content: data.message });
            } else {
                addMessage(data.message || 'Désolé, une erreur est survenue. Veuillez réessayer.', 'bot error');
            }

            scrollToBottom();
        })
        .catch(error => {
            document.getElementById('aiTyping').classList.remove('active');
            addMessage('Désolé, une erreur de connexion est survenue. Veuillez réessayer.', 'bot error');
            scrollToBottom();
        })
        .finally(() => {
            isProcessing = false;
            document.getElementById('aiChatSendBtn').disabled = false;
        });
    };

    function addMessage(text, type) {
        const container = document.getElementById('aiChatMessages');
        const time = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

        const div = document.createElement('div');
        div.className = `ai-msg ${type}`;
        // Escape HTML to prevent XSS, then convert basic markdown-like formatting
        const escaped = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        div.innerHTML = escaped + '<span class="msg-time">' + time + '</span>';
        container.appendChild(div);
    }

    function scrollToBottom() {
        const container = document.getElementById('aiChatMessages');
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 50);
    }
})();
</script>
