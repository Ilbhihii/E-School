@extends('layouts.student')

@section('title')
    Chat - {{ $subject->name ?? '' }}
@endsection
@section('page_title', 'Chat')

@section('content')

<style>
.chat-container {
    height: calc(100vh - 240px);
    display: flex;
    flex-direction: column;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: var(--adm-radius-lg, 16px);
    overflow: hidden;
}
.chat-header {
    background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(8,145,178,0.1));
    border-bottom: 1px solid rgba(255,255,255,0.05);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.chat-header-icon {
    width: 44px; height: 44px;
    background: rgba(255,255,255,0.08);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.chat-header h1 {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.1rem;
    color: rgba(255,255,255,0.9);
    margin: 0;
}
.chat-header p {
    color: rgba(255,255,255,0.45);
    font-size: 0.82rem;
    margin: 2px 0 0;
}
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.chat-messages::-webkit-scrollbar { width: 5px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 3px; }
.chat-msg {
    max-width: 75%;
    animation: chatFadeIn 0.25s ease-out;
}
@keyframes chatFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.chat-msg.own { align-self: flex-end; }
.chat-msg.other { align-self: flex-start; }
.chat-msg-bubble {
    border-radius: 18px;
    padding: 12px 18px;
    position: relative;
}
.chat-msg-bubble.own {
    background: linear-gradient(135deg, rgba(6,182,212,0.3), rgba(6,182,212,0.15));
    border: 1px solid rgba(6,182,212,0.12);
    border-bottom-right-radius: 4px;
}
.chat-msg-bubble.other {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    border-bottom-left-radius: 4px;
}
.chat-msg-bubble .name {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(6,182,212,0.8);
    margin-bottom: 4px;
}
.chat-msg-bubble .text {
    color: rgba(255,255,255,0.9);
    line-height: 1.6;
    font-size: 0.92rem;
    margin: 0 0 6px;
}
.chat-msg-bubble .time {
    text-align: right;
    font-size: 0.65rem;
    color: rgba(255,255,255,0.3);
}
.chat-input-area {
    background: rgba(15,23,42,0.5);
    backdrop-filter: blur(16px);
    border-top: 1px solid rgba(255,255,255,0.05);
    padding: 1.25rem 1.5rem;
}
.chat-input-group {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}
.chat-input {
    flex: 1;
    padding: 0.85rem 1.25rem;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    font-size: 0.92rem;
    transition: all 0.25s ease;
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.85);
    font-family: inherit;
    outline: none;
}
.chat-input:focus {
    border-color: rgba(6,182,212,0.3);
    background: rgba(255,255,255,0.06);
    box-shadow: 0 0 0 3px rgba(6,182,212,0.06);
}
.chat-input::placeholder { color: rgba(255,255,255,0.2); }
.btn-send {
    padding: 0.85rem 1.5rem;
    background: linear-gradient(135deg, #06B6D4, #0891B2);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.88rem;
    cursor: pointer;
    transition: all 0.25s ease;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-family: inherit;
}
.btn-send:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(6,182,212,0.35);
}
@media (max-width: 768px) {
    .chat-container { height: calc(100vh - 180px); border-radius: 0; margin: 0 -1rem; }
    .chat-msg { max-width: 90%; }
}
</style>

<div class="chat-container">
    <div class="chat-header">
        <div class="chat-header-icon">💬</div>
        <div>
            <h1>{{ $subject->name ?? 'Chat' }}</h1>
            <p>{{ mb_strtolower($subject->name ?? '') === 'administration' ? 'Conversation privée avec l’administration' : 'Posez vos questions aux professeurs' }}</p>
        </div>
    </div>

    <div class="chat-messages" id="chat-box">
        @forelse($messages as $msg)
        <div class="chat-msg {{ $msg->user_id == auth()->id() ? 'own' : 'other' }}">
            <div class="chat-msg-bubble {{ $msg->user_id == auth()->id() ? 'own' : 'other' }}">
                @if($msg->user_id != auth()->id())
                <div class="name">{{ $msg->user->name ?? 'Utilisateur' }}</div>
                @endif
                <p class="text">{{ $msg->message }}</p>
                <div class="time">{{ $msg->created_at->format('H:i') }}</div>
            </div>
        </div>
        @empty
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,0.3);text-align:center;padding:3rem 2rem;">
            <div style="width:72px;height:72px;background:rgba(255,255,255,0.04);border-radius:20px;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;font-size:1.75rem;">💬</div>
            <h5 style="color:rgba(255,255,255,0.4);font-weight:600;margin-bottom:0.5rem;">Aucun message</h5>
            <p style="color:rgba(255,255,255,0.3);max-width:400px;font-size:0.85rem;">Commencez la conversation en envoyant un premier message.</p>
        </div>
        @endforelse
    </div>

    <div class="chat-input-area">
        <form method="POST" action="{{ route('student.chat.send') }}" class="chat-input-group">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $subject->id ?? '' }}">
            <input type="text" name="message" class="chat-input" placeholder="Tapez votre message..." required autocomplete="off">
            <button type="submit" class="btn-send">
                <i class="bi bi-send" style="font-size:0.9rem;"></i> Envoyer
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.getElementById('chat-box');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
        const observer = new MutationObserver(function() { chatBox.scrollTop = chatBox.scrollHeight; });
        observer.observe(chatBox, { childList: true, subtree: true });
    }
    const input = document.querySelector('.chat-input');
    const form = document.querySelector('.chat-input-group');
    if (input && form) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.submit();
            }
        });
    }
});
</script>

@endsection
