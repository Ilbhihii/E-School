@extends('layouts.prof')

@section('title', 'Chat Professeur')
@section('page_title', 'Chat')
@section('breadcrumb', 'Questions étudiants')

@section('content')

<style>
.chat-prof-container {
    max-width: 800px;
    margin: 0 auto;
}
.chat-prof-box {
    height: calc(100vh - 280px);
    display: flex;
    flex-direction: column;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: var(--adm-radius-lg, 16px);
    overflow: hidden;
}
.chat-prof-header {
    background: linear-gradient(135deg, rgba(99,102,241,0.3), rgba(139,92,246,0.15));
    border-bottom: 1px solid rgba(255,255,255,0.05);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
.chat-prof-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.chat-prof-messages::-webkit-scrollbar { width: 5px; }
.chat-prof-messages::-webkit-scrollbar-track { background: transparent; }
.chat-prof-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 3px; }
.msg-row {
    max-width: 78%;
    animation: msgIn 0.25s ease-out;
}
@keyframes msgIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.msg-row.prof { align-self: flex-end; }
.msg-row.student { align-self: flex-start; }
.msg-bubble {
    border-radius: 18px;
    padding: 12px 18px;
    position: relative;
}
.msg-bubble.prof {
    background: linear-gradient(135deg, rgba(99,102,241,0.3), rgba(139,92,246,0.15));
    border: 1px solid rgba(99,102,241,0.12);
    border-bottom-right-radius: 4px;
}
.msg-bubble.student {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    border-bottom-left-radius: 4px;
}
.msg-bubble .msg-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(99,102,241,0.8);
    margin-bottom: 4px;
}
.msg-bubble .msg-text {
    color: rgba(255,255,255,0.9);
    line-height: 1.6;
    font-size: 0.92rem;
    margin: 0 0 6px;
}
.msg-bubble .msg-text.deleted {
    text-decoration: line-through;
    color: rgba(255,255,255,0.4);
    font-style: italic;
}
.msg-bubble .msg-time {
    text-align: right;
    font-size: 0.65rem;
    color: rgba(255,255,255,0.3);
}
.chat-prof-input {
    background: rgba(15,23,42,0.5);
    backdrop-filter: blur(16px);
    border-top: 1px solid rgba(255,255,255,0.05);
    padding: 1.25rem 1.5rem;
}
.chat-prof-form {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}
.chat-prof-input-field {
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
.chat-prof-input-field:focus {
    border-color: rgba(99,102,241,0.3);
    background: rgba(255,255,255,0.06);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.06);
}
.chat-prof-input-field::placeholder { color: rgba(255,255,255,0.2); }
.chat-prof-checkbox {
    position: absolute;
    top: 50%; transform: translateY(-50%);
    display: none;
}
.msg-row.prof .chat-prof-checkbox { left: -24px; }
.msg-row.student .chat-prof-checkbox { right: -24px; }
.chat-prof-select-bar {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    background: rgba(15,23,42,0.9);
    backdrop-filter: blur(16px);
    border-top: 1px solid rgba(255,255,255,0.06);
    padding: 1rem 1.5rem;
    display: none;
    z-index: 1000;
}
@media (max-width: 768px) {
    .chat-prof-box { height: calc(100vh - 200px); border-radius: 0; margin: 0 -1rem; }
    .msg-row { max-width: 90%; }
}
</style>

<div class="chat-prof-container">
    <div class="chat-prof-box">
        <div class="chat-prof-header">
            <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:1.4rem;">💬</span>
                <div>
                    <div style="font-weight:700;color:rgba(255,255,255,0.9);">{{ $subject->name ?? 'Chat' }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.4);">Discussion avec les étudiants</div>
                </div>
            </div>
            <button id="selectToggleProf" class="adm-btn adm-btn-ghost adm-btn-sm">
                <i class="bi bi-check-square me-1"></i> <span id="toggleTextProf">Sélectionner</span>
            </button>
        </div>

        <div class="chat-prof-messages" id="profMsgList">
            @forelse($messages as $msg)
            <div class="msg-row {{ $msg->user_id == auth()->id() ? 'prof' : 'student' }}" data-id="{{ $msg->id }}" style="position:relative;">
                <div class="msg-bubble {{ $msg->user_id == auth()->id() ? 'prof' : 'student' }}">
                    @if($msg->user_id != auth()->id())
                    <div class="msg-name">{{ $msg->user->name ?? 'Étudiant' }}</div>
                    @endif
                    <p class="msg-text {{ $msg->deleted_at ? 'deleted' : '' }}">{{ $msg->message }}</p>
                    @if($msg->deleted_at)
                    <div style="color:#FCA5A5;font-style:italic;font-size:0.78rem;margin-top:4px;">✂️ Supprimé le {{ $msg->deleted_at->format('d/m H:i') }}</div>
                    @endif
                    <div class="msg-time">{{ $msg->created_at->diffForHumans() }}</div>
                </div>
                <div class="chat-prof-checkbox" style="position:absolute;{{ $msg->user_id == auth()->id() ? 'left:-28px' : 'right:-28px' }};top:50%;transform:translateY(-50%);display:none;">
                    <input type="checkbox" value="{{ $msg->id }}" class="msg-checkbox" style="width:18px;height:18px;cursor:pointer;accent-color:#6366F1;">
                </div>
            </div>
            @empty
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,0.3);text-align:center;padding:3rem 2rem;">
                <div style="width:72px;height:72px;background:rgba(255,255,255,0.04);border-radius:20px;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;font-size:1.75rem;">💬</div>
                <h5 style="color:rgba(255,255,255,0.4);font-weight:600;margin-bottom:0.5rem;">Aucun message</h5>
                <p style="color:rgba(255,255,255,0.3);max-width:400px;font-size:0.85rem;">Les étudiants n'ont pas encore envoyé de message.</p>
            </div>
            @endforelse
        </div>

        <div class="chat-prof-input">
            <form method="POST" action="{{ route('prof.chat.send') }}" class="chat-prof-form">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id ?? '' }}">
                <input type="text" name="message" class="chat-prof-input-field" placeholder="✍️ Tapez votre message..." required autocomplete="off">
                <button type="submit" class="adm-btn adm-btn-primary" style="padding:0.85rem 1.5rem;border-radius:12px;white-space:nowrap;">
                    <i class="bi bi-send-fill me-1"></i> Envoyer
                </button>
            </form>
        </div>
    </div>

    <div class="chat-prof-select-bar" id="deleteBarProf">
        <form method="POST" action="{{ route('prof.chat.delete') }}" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            @csrf @method('DELETE')
            <input type="hidden" name="subject_id" value="{{ $subject->id ?? '' }}">
            <span style="color:rgba(255,255,255,0.6);font-size:0.85rem;" id="countLabelProf">0 sélectionné(s)</span>
            <div style="display:flex;gap:8px;">
                <button type="button" id="selectAllProf" class="adm-btn adm-btn-ghost adm-btn-sm">Tout</button>
                <button type="button" id="cancelProf" class="adm-btn adm-btn-ghost adm-btn-sm">Annuler</button>
                <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm" id="confirmDeleteProf" disabled>
                    <i class="bi bi-trash me-1"></i> Supprimer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const msgList = document.getElementById('profMsgList');
    if (msgList) {
        msgList.scrollTop = msgList.scrollHeight;
        new MutationObserver(() => { msgList.scrollTop = msgList.scrollHeight; })
            .observe(msgList, { childList: true, subtree: true });
    }

    let selectMode = false;
    const toggle = document.getElementById('selectToggleProf');
    const toggleText = document.getElementById('toggleTextProf');
    const deleteBar = document.getElementById('deleteBarProf');
    const countLabel = document.getElementById('countLabelProf');
    const confirmBtn = document.getElementById('confirmDeleteProf');
    const checkboxes = () => document.querySelectorAll('.msg-checkbox');
    const containers = () => document.querySelectorAll('.chat-prof-checkbox');

    toggle.addEventListener('click', function() {
        selectMode = !selectMode;
        toggleText.textContent = selectMode ? 'Annuler' : 'Sélectionner';
        toggle.classList.toggle('adm-btn-primary', selectMode);
        toggle.classList.toggle('adm-btn-ghost', !selectMode);
        containers().forEach(c => c.style.display = selectMode ? 'block' : 'none');
        deleteBar.style.display = selectMode ? 'block' : 'none';
        if (!selectMode) {
            checkboxes().forEach(cb => cb.checked = false);
            updateCount();
        }
    });

    function updateCount() {
        const checked = Array.from(checkboxes()).filter(cb => cb.checked).length;
        countLabel.textContent = `${checked} sélectionné${checked > 1 ? 's' : ''}`;
        confirmBtn.disabled = checked === 0;
    }
    checkboxes().forEach(cb => cb.addEventListener('change', updateCount));

    document.getElementById('selectAllProf').addEventListener('click', () => {
        checkboxes().forEach(cb => cb.checked = true);
        updateCount();
    });
    document.getElementById('cancelProf').addEventListener('click', () => {
        checkboxes().forEach(cb => cb.checked = false);
        updateCount();
        selectMode = false;
        toggleText.textContent = 'Sélectionner';
        toggle.classList.add('adm-btn-ghost');
        toggle.classList.remove('adm-btn-primary');
        containers().forEach(c => c.style.display = 'none');
        deleteBar.style.display = 'none';
    });

    document.getElementById('deleteBarProf').querySelector('form').addEventListener('submit', function(e) {
        const count = Array.from(checkboxes()).filter(cb => cb.checked).length;
        if (!count || !confirm(`⚠️ Supprimer ${count} message${count > 1 ? 's' : ''} ?`)) e.preventDefault();
    });

    const input = document.querySelector('.chat-prof-input-field');
    const form = document.querySelector('.chat-prof-form');
    if (input && form) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.submit(); }
        });
    }
});
</script>

@endsection
