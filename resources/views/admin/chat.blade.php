@extends('layouts.admin')

@section('title', 'Chat Administration')
@section('page_title', 'Chat')
@section('breadcrumb', 'Chat administration')

@section('content')

<style>
/* Dark Premium Chat Styles */
.chat-container {
  height: calc(100vh - 200px);
  display: flex;
  flex-direction: column;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: var(--adm-radius-lg);
  overflow: hidden;
}

.chat-header {
  background: linear-gradient(135deg, rgba(0,58,143,0.3), rgba(124,58,237,0.15));
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
  font-size: 1.15rem;
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
  max-width: 72%;
  animation: chatFadeIn 0.25s ease-out;
}

@keyframes chatFadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

.chat-msg.own { align-self: flex-end; }
.chat-msg.other { align-self: flex-start; }

.chat-msg-own {
  background: linear-gradient(135deg, rgba(0,58,143,0.4), rgba(0,58,143,0.2));
  border: 1px solid rgba(0,58,143,0.15);
  border-bottom-right-radius: 4px;
}

.chat-msg-other {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.06);
  border-bottom-left-radius: 4px;
}

.chat-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: rgba(255,255,255,0.3);
  text-align: center;
  padding: 3rem 2rem;
}

.chat-empty-icon {
  width: 72px; height: 72px;
  background: rgba(255,255,255,0.04);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.25rem;
  font-size: 1.75rem;
}

.chat-empty h5 {
  color: rgba(255,255,255,0.4);
  font-weight: 600;
  margin-bottom: 0.5rem;
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
  align-items: flex-end;
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
  border-color: rgba(124,58,237,0.3);
  background: rgba(255,255,255,0.06);
  box-shadow: 0 0 0 3px rgba(124,58,237,0.06);
}

.chat-input::placeholder {
  color: rgba(255,255,255,0.2);
}

.btn-send {
  padding: 0.85rem 1.5rem;
  background: var(--adm-gradient-primary);
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
  box-shadow: 0 8px 25px rgba(0,58,143,0.35);
}

.btn-send:active {
  transform: translateY(0);
}

@media (max-width: 768px) {
  .chat-container {
    height: calc(100vh - 160px);
    border-radius: 0;
    margin: 0 -1rem;
  }
  .chat-msg {
    max-width: 90%;
  }
}
</style>

<div class="chat-container">
  <!-- Header -->
  <div class="chat-header">
    <div class="chat-header-icon">💬</div>
    <div>
      <h1>Chat Administration</h1>
      <p>
        Conversations privées avec les étudiants et les professeurs
        @if(isset($subject))
          • {{ $subject->name ?? 'Sujet non spécifié' }}
        @endif
      </p>
    </div>
  </div>

  @if(session('success'))
  <div class="adm-alert adm-alert-success" style="margin:1rem 1.5rem 0;">
    <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
    <span>{{ session('success') }}</span>
  </div>
  @endif

  <!-- Messages -->
  <div class="chat-messages" id="chat-box">
    @if(isset($messages) && $messages->count() > 0)
      @foreach($messages as $msg)
        <div class="chat-msg {{ $msg->user_id == auth()->id() ? 'own' : 'other' }}">
          @if($isAdministration)
            <div style="font-size:.7rem;color:rgba(255,255,255,.4);margin:0 8px 4px;{{ $msg->user_id == auth()->id() ? 'text-align:right;' : '' }}">
              <i class="bi bi-person me-1"></i>{{ $msg->conversationUser->name ?? 'Conversation non attribuée' }}
            </div>
          @endif
          @if($msg->user_id == auth()->id())
            <x-user-message :message="$msg" />
          @else
            <x-other-message :message="$msg" />
          @endif
        </div>
      @endforeach
    @else
      <div class="chat-empty">
        <div class="chat-empty-icon">💬</div>
        <h5>Aucune conversation</h5>
        <p style="color:rgba(255,255,255,0.3);max-width:400px;font-size:0.85rem;">
          Commencez une conversation privée en envoyant un premier message.
        </p>
      </div>
    @endif
  </div>

  <!-- Input -->
  <div class="chat-input-area">
    <form method="POST" action="{{ route('admin.chat.send') }}" class="chat-input-group">
      @csrf
      <input type="hidden" name="subject_id" value="{{ $subject->id ?? '' }}">
      @if($isAdministration)
        <select name="conversation_user_id" class="chat-input" required style="flex:0 0 230px;">
          <option value="">Choisir un étudiant ou professeur</option>
          @foreach($conversationUsers as $conversationUser)
            <option value="{{ $conversationUser->id }}" {{ (string) old('conversation_user_id', request('student')) === (string) $conversationUser->id ? 'selected' : '' }}>
              {{ $conversationUser->role === 'prof' ? 'Professeur' : 'Étudiant' }} — {{ $conversationUser->name }}
            </option>
          @endforeach
        </select>
      @endif
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
    function scrollToBottom() { chatBox.scrollTop = chatBox.scrollHeight; }

    scrollToBottom();

    const observer = new MutationObserver(function() { scrollToBottom(); });
    observer.observe(chatBox, { childList: true, subtree: true, attributes: true });

    setTimeout(() => {
      chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
    }, 100);
  }

  const form = document.querySelector('.chat-input-group');
  const input = document.querySelector('.chat-input');
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
