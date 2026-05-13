@extends('layouts.admin')

@section('content')
<style>
/* Professional Admin Chat Styles */
.admin-chat-container {
  height: calc(100vh - 140px);
  display: flex;
  flex-direction: column;
  background: #f9fafb;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  overflow: hidden;
  margin: 1rem 0;
}

.admin-chat-header {
  background: linear-gradient(135deg, #b389ef 0%, #220a3f 100%);
  color: white;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.admin-chat-header-icon {
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  font-weight: 600;
}

.admin-chat-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
}

.admin-chat-subtitle {
  font-size: 0.875rem;
  opacity: 0.9;
  margin: 0;
}

.admin-messages-area {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  background: white;
}

.admin-message {
  max-width: 70%;
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.message-own {
  align-self: flex-end;
}

.message-other {
  align-self: flex-start;
}

.admin-message-bubble {
  padding: 1rem 1.25rem;
  border-radius: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  position: relative;
}

.admin-message-own {
  background: linear-gradient(135deg, #059669, #047857);
  color: white;
  border-bottom-right-radius: 6px;
}

.admin-message-other {
  background: #ffffff;
  color: #1f2937;
  border: 1px solid #e5e7eb;
  border-bottom-left-radius: 6px;
}

.message-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  font-weight: 600;
  flex-shrink: 0;
}

.avatar-admin {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white;
}

.avatar-student {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
}

.message-meta {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.25rem;
}

.message-author {
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
}

.message-time {
  font-size: 0.75rem;
  color: #9ca3af;
}

.message-text {
  line-height: 1.6;
  font-size: 0.95rem;
  margin: 0;
}

.admin-input-container {
  background: white;
  padding: 1.5rem 1.5rem 1.5rem;
  border-top: 1px solid #e5e7eb;
  box-shadow: 0 -2px 4px rgba(153, 119, 208, 0.05);
}

.admin-input-group {
  display: flex;
  gap: 0.75rem;
  align-items: end;
}

.admin-input {
  flex: 1;
  padding: 0.875rem 1.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  background: #fafbfc;
}

.admin-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  background: white;
}

.admin-send-btn {
  padding: 0.875rem 1.75rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.admin-send-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.success-alert {
  background: #ecfdf5;
  border: 1px solid #bbf7d0;
  color: #166534;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  margin: 0 1.5rem 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
}

.no-messages {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #6b7280;
  text-align: center;
  padding: 3rem 2rem;
}

.empty-chat-icon {
  width: 72px;
  height: 72px;
  background: linear-gradient(135deg, #e5e7eb, #d1d5db);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
  font-size: 1.75rem;
}

/* Scrollbar */
.admin-messages-area::-webkit-scrollbar {
  width: 6px;
}

.admin-messages-area::-webkit-scrollbar-track {
  background: #f3f4f6;
  border-radius: 3px;
}

.admin-messages-area::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

.admin-messages-area::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.deleted-message {
  text-decoration: line-through !important;
}

/* Responsive */
@media (max-width: 768px) {
  .admin-chat-container {
    height: calc(100vh - 120px);
    margin: 0;
    border-radius: 0;
  }
  
  .admin-chat-header {
    padding: 1.25rem 1.25rem;
  }
  
  .admin-messages-area {
    padding: 1rem;
  }
  
  .admin-message {
    max-width: 90%;
  }
}
</style>

<div class="admin-chat-container">
  <!-- Header -->
  <div class="admin-chat-header">
    <div class="admin-chat-header-icon">
      💬
    </div>
    <div>
      <h1 class="admin-chat-title">Chat Administration</h1>
      <p class="admin-chat-subtitle">
        Conversations avec les étudiants 
        @if(isset($subject))
          • {{ $subject->name ?? 'Sujet non spécifié' }}
        @endif
      </p>
    </div>
  </div>

  @if(session('success'))
  <div class="success-alert">
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
    </svg>
    {{ session('success') }}
  </div>
  @endif

  <!-- Messages Area -->
  <div class="admin-messages-area" id="chat-box">
    @if(isset($messages) && $messages->count() > 0)
      @foreach($messages as $msg)
        @if($msg->user_id == auth()->id())
          <div class="admin-message message-own">
            <x-user-message :message="$msg" />
          </div>
        @else
          <div class="admin-message message-other">
            <x-other-message :message="$msg" />
          </div>
        @endif
      @endforeach
    @else
      <div class="no-messages">
        <div class="empty-chat-icon">💬</div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucune conversation</h3>
        <p class="text-gray-500 max-w-md">Commencez la conversation avec vos étudiants en envoyant un premier message.</p>
      </div>
    @endif
  </div>

  <!-- Input Form -->
  <div class="admin-input-container">
    <form method="POST" action="{{ route('admin.chat.send') }}" class="admin-input-group">
      @csrf
      <input type="hidden" name="subject_id" value="{{ $subject->id ?? '' }}">
      <input 
        type="text"
        name="message"
        class="admin-input"
        placeholder="Tapez votre message..."
        required
        autocomplete="off">
      <button type="submit" class="admin-send-btn">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-2-9-9 2 2 9z"></path>
        </svg>
        Envoyer
      </button>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const chatBox = document.getElementById('chat-box');
  if (chatBox) {
    // Scroll to bottom
    function scrollToBottom() {
      chatBox.scrollTop = chatBox.scrollHeight;
    }
    
    scrollToBottom();
    
    // Auto-scroll when new messages arrive
    const observer = new MutationObserver(function() {
      scrollToBottom();
    });
    
    observer.observe(chatBox, { 
      childList: true, 
      subtree: true,
      attributes: true 
    });
    
    // Smooth initial scroll
    setTimeout(() => {
      chatBox.scrollTo({
        top: chatBox.scrollHeight,
        behavior: 'smooth'
      });
    }, 100);
  }

  // Enter key to send (with shift for newline)
  const form = document.querySelector('.admin-input-group');
  const input = document.querySelector('.admin-input');
  
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

