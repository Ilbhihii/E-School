@extends('layouts.admin')

@section('content')
<style>
.chat-container {
  height: calc(100vh - 180px);
  display: flex;
  flex-direction: column;
  background: var(--adm-surface);
  border-radius: var(--adm-radius-lg);
  box-shadow: var(--adm-shadow-lg);
  overflow: hidden;
  margin: 1rem 0;
}
.chat-header {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white;
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  background: #f8fafc;
}
.chat-messages::-webkit-scrollbar { width: 6px; }
.chat-messages::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
.chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.chat-msg { max-width: 70%; animation: msgIn 0.3s ease; }
@keyframes msgIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
.chat-msg.own { align-self: flex-end; }
.chat-msg.other { align-self: flex-start; }
.chat-bubble {
  padding: 0.9rem 1.25rem;
  border-radius: 18px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.chat-bubble.own { background: linear-gradient(135deg,#059669,#047857); color: white; border-bottom-right-radius: 6px; }
.chat-bubble.other { background: white; color: #1f2937; border: 1px solid #e5e7eb; border-bottom-left-radius: 6px; }
.chat-input-area {
  background: white;
  padding: 1.25rem 1.5rem;
  border-top: 1px solid var(--adm-border);
}
.chat-input-row {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}
.chat-input {
  flex: 1;
  padding: 0.8rem 1.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.2s;
  outline: none;
}
.chat-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
.chat-send {
  padding: 0.8rem 1.5rem;
  background: #6366f1;
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  white-space: nowrap;
}
.chat-send:hover { background: #4f46e5; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.3); }
@media (max-width:768px) {
  .chat-container { margin:0; border-radius:0; height:calc(100vh - 120px); }
  .chat-msg { max-width: 90%; }
}
</style>

<div class="chat-container">
  <!-- Header -->
  <div class="chat-header">
    <div style="background:rgba(255,255,255,0.15);border-radius:10px;padding:0.5rem 0.75rem;">
      <i class="bi bi-chat-text-fill"></i>
    </div>
    <div>
      <h4 style="font-weight:700;margin:0;font-size:1.1rem;">Chat Administration</h4>
      <p style="margin:0;font-size:0.85rem;opacity:0.85;">
        @if(isset($subject))
          {{ $subject->name ?? 'Sujet' }}
        @endif
      </p>
    </div>
  </div>

  @if(session('success'))
  <div class="adm-alert adm-alert-success" style="margin:1rem 1.5rem 0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
  </div>
  @endif

  <!-- Messages -->
  <div class="chat-messages" id="chat-box">
    @if(isset($messages) && $messages->count() > 0)
      @foreach($messages as $msg)
        @if($msg->user_id == auth()->id())
          <div class="chat-msg own">
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.25rem;justify-content:flex-end;">
              <span style="font-size:0.75rem;color:#9ca3af;">{{ $msg->created_at->format('H:i') }}</span>
            </div>
            <div class="chat-bubble own">{{ $msg->message }}</div>
          </div>
        @else
          <div class="chat-msg other">
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.25rem;">
              <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);color:white;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;">
                {{ strtoupper(substr($msg->user->name ?? 'E', 0, 1)) }}
              </div>
              <span style="font-weight:600;font-size:0.85rem;color:#374151;">{{ $msg->user->name ?? 'Étudiant' }}</span>
              <span style="font-size:0.75rem;color:#9ca3af;">{{ $msg->created_at->format('H:i') }}</span>
            </div>
            <div class="chat-bubble other">{{ $msg->message }}</div>
          </div>
        @endif
      @endforeach
    @else
      <div class="adm-empty" style="height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;">
        <div class="adm-empty-icon"><i class="bi bi-chat-dots"></i></div>
        <h3>Aucune conversation</h3>
        <p>Commencez la conversation avec vos étudiants.</p>
      </div>
    @endif
  </div>

  <!-- Input -->
  <div class="chat-input-area">
    <form method="POST" action="{{ route('admin.chat.send') }}" class="chat-input-row">
      @csrf
      <input type="hidden" name="subject_id" value="{{ $subject->id ?? '' }}">
      <input type="text" name="message" class="chat-input" placeholder="Tapez votre message..." required autocomplete="off">
      <button type="submit" class="chat-send">
        <i class="bi bi-send-fill"></i> Envoyer
      </button>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const chatBox = document.getElementById('chat-box');
  if (chatBox) {
    chatBox.scrollTop = chatBox.scrollHeight;
    
    const observer = new MutationObserver(function() {
      chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
    });
    observer.observe(chatBox, { childList: true, subtree: true });

    setTimeout(() => {
      chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
    }, 100);
  }

  const input = document.querySelector('.chat-input');
  const form = document.querySelector('.chat-input-row');
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
