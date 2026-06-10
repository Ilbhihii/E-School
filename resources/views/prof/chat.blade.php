@extends('layouts.prof')

@section('content')
<style>
:root {
  --glass-bg: rgb(255, 255, 255);
  --glass-border: rgba(255, 255, 255, 0.18);
  --gradient-primary: linear-gradient(135deg, #949fd4 0%, #4b62a2 100%);
  --gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  --shadow-lg: 0 25px 45px -12px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 35px 60px -12px rgba(0, 0, 0, 0.15);
}

.glass-card {
  background: var(--glass-bg);
  backdrop-filter: blur(20px);
  border: 1px solid var(--glass-border);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(var(--gradient-primary));
  border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #4f46e5, #7c3aed);
}

.message-bubble {
  max-width: 75%;
  word-wrap: break-word;
  animation: slideIn 0.3s ease-out;
}
.message-prof {
  background: var(--gradient-primary) !important;
  margin-left: auto;
  border-bottom-right-radius: 8px !important;
}
.message-student {
  background: white !important;
  border-radius: 18px !important;
  border-bottom-left-radius: 8px !important;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

#delete-form {
  box-shadow: var(--shadow-xl);
}

.deleted-message {
  text-decoration: line-through !important;
}
</style>

<div class="container-fluid py-3 px-2 px-md-4">
  <div class="row justify-content-center g-0">
    <div class="col-12 col-md-11 col-lg-9 col-xl-7">
      
      <!-- HEADER -->
      <div class="glass-card mb-4 p-0 rounded-3 shadow-lg overflow-hidden">
        <div class="bg-gradient-primary text-black p-4 p-md-5 position-relative rounded-top-3">
          <div class="position-absolute inset-0 bg-white opacity-5"></div>
          <div class="position-relative">
            <h1 class="h3 fw-black mb-2 mb-md-3 d-flex align-items-center gap-2">
              💬 Chat Professor
            </h1>
            <div class="d-flex flex-column flex-md-row gap-2">
              <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-semibold shadow-sm">
                📚 {{ $subject->name }}
              </span>
              <span class="badge bg-success rounded-circle" style="width: 10px; height: 10px;"></span>
              <small class="text-black-50">Discussion étudiants</small>
            </div>
          </div>
        </div>
      </div>

      <!-- SELECT MODE TOGGLE -->
      <button id="selectToggle" class="btn btn-outline-primary btn-sm rounded-pill px-4 py-2 mb-3 shadow-sm w-100 w-md-auto">
        <i class="bi bi-check-square me-2"></i><span id="toggleText">Sélectionner</span>
      </button>

      <!-- MESSAGES -->
      <div id="messagesContainer" class="card border-0 shadow-lg rounded-3 glass-card mb-4 h-75vh overflow-hidden custom-scrollbar">
        <div class="card-body p-3 p-md-4 h-100 d-flex flex-column">
          <div id="messagesList" class="flex-grow-1 overflow-y-auto pb-3">
            @forelse($messages as $msg)
              <div class="message-item py-2 {{ $msg->user_id == auth()->id() ? 'justify-content-end' : '' }}" data-id="{{ $msg->id }}">
                @if($msg->user_id == auth()->id())
                  <!-- PROF MESSAGE -->
                  <div class="message-bubble message-prof p-3 rounded-3 shadow-sm position-relative" style="max-width: 80%;">
                    <div class="message-text {{ $msg->deleted_at ? 'deleted-message text-muted opacity-75' : '' }}">{{ $msg->message }}</div>
@if($msg->deleted_at)
                    <div class="small text-danger fst-italic mt-1">
                      ✂️ Supprimé le {{ $msg->deleted_at->format('d/m H:i') }}
                    </div>
@endif
                    <small class="d-block mt-1 opacity-75 text-end">{{ $msg->created_at->diffForHumans() }}</small>
                    <div class="checkbox-container position-absolute top-50 start-0 translate-middle ms-n3 d-none">
                      <input type="checkbox" name="messages[]" value="{{ $msg->id }}" class="form-check-input rounded-circle shadow">
                    </div>
                  </div>
                @else
                  <!-- STUDENT MESSAGE -->
                  <div class="d-flex gap-3 align-items-start">
                    <div class="flex-shrink-0 mt-1">
                      <div class="avatar bg-gradient-info text-black rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 42px; height: 42px; font-weight: 600;">
                        {{ substr($msg->user->name ?? 'E', 0, 1) }}
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <div class="name-time small text-muted mb-1 fw-semibold d-flex align-items-center gap-1">
                        {{ $msg->user->name ?? 'Étudiant' }}
                        <span class="badge bg-success rounded-pill" style="font-size: 0.65em;"></span>
                      </div>
                      <div class="message-bubble message-student p-3 rounded-3 shadow-sm position-relative" style="max-width: 80%;">
                        <div class="message-text {{ $msg->deleted_at ? 'deleted-message text-muted opacity-75' : '' }}">{{ $msg->message }}</div>
@if($msg->deleted_at)
                        <div class="small text-danger fst-italic mt-1">
                          ✂️ Supprimé le {{ $msg->deleted_at->format('d/m H:i') }}
                        </div>
@endif
                        <small class="d-block mt-1 opacity-75">{{ $msg->created_at->diffForHumans() }}</small>
                        <div class="checkbox-container position-absolute top-50 end-0 translate-middle me-n3 d-none">
                          <input type="checkbox" name="messages[]" value="{{ $msg->id }}" class="form-check-input rounded-circle shadow">
                        </div>
                      </div>
                    </div>
                  </div>
                @endif
              </div>
            @empty
              <div class="text-center py-5 text-muted">
                <i class="bi bi-chat-square-text fs-1 opacity-50 mb-3 d-block"></i>
                <p>Aucun message pour le moment</p>
              </div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- SEND FORM -->
      <form method="POST" action="{{ route('prof.chat.send') }}" class="card border-0 glass-card shadow-lg rounded-3 p-0 overflow-hidden">
        @csrf
        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
        <div class="input-group input-group-lg">
          <input type="text" 
                 name="message" 
                 class="form-control border-0 ps-4 bg-transparent" 
                 placeholder="✍️ Tapez votre message..." 
                 required 
                 autocomplete="off"
                 style="font-size: 1.1rem;">
          <button type="submit" class="btn btn-success btn-lg px-4 shadow-none">
            <i class="bi bi-send-fill fs-5"></i>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- DELETE FORM - Full width sticky bottom -->
<form id="deleteForm" method="POST" action="{{ route('prof.chat.delete') }}" class="card border-0 shadow-lg rounded-0 position-fixed bottom-0 start-0 end-0 m-0 p-3 glass-card d-none">
  @csrf
  @method('DELETE')
  <input type="hidden" name="subject_id" value="{{ $subject->id }}">
  <div class="row align-items-center g-2">
    <div class="col">
      <small class="text-black fw-semibold" id="countLabel">0 sélectionné(s)</small>
    </div>
    <div class="col-auto">
      <button type="button" id="selectAllBtn" class="btn btn-outline-light btn-sm me-2">Tout</button>
      <button type="button" id="cancelBtn" class="btn btn-outline-light btn-sm me-2">Annuler</button>
      <button type="submit" id="confirmDelete" class="btn btn-danger btn-sm px-3">
        <i class="bi bi-trash me-1"></i>Supprimer
      </button>
    </div>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const selectToggle = document.getElementById('selectToggle');
  const messagesContainer = document.getElementById('messagesList');
  const deleteForm = document.getElementById('deleteForm');
  const checkboxes = document.querySelectorAll('input[name="messages[]"]');
  const countLabel = document.getElementById('countLabel');
  const toggleText = document.getElementById('toggleText');
  
  let selectMode = false;

  // Toggle select mode
  selectToggle.addEventListener('click', function() {
    selectMode = !selectMode;
    toggleText.textContent = selectMode ? 'Annuler' : 'Sélectionner';
    selectToggle.classList.toggle('btn-primary', selectMode);
    selectToggle.classList.toggle('btn-outline-primary', !selectMode);
    
    document.querySelectorAll('.checkbox-container').forEach(cb => {
      cb.classList.toggle('d-none', !selectMode);
    });
    
    deleteForm.classList.toggle('d-none', !selectMode);
    if (!selectMode) {
      checkboxes.forEach(cb => cb.checked = false);
      updateCount();
    }
  });

  // Update selection count
  function updateCount() {
    const checked = Array.from(checkboxes).filter(cb => cb.checked).length;
    countLabel.textContent = `${checked} sélectionné${checked === 1 ? '' : 's'}`;
    document.getElementById('confirmDelete').disabled = checked === 0;
  }

  // Checkbox events
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateCount);
  });

  // Select all
  document.getElementById('selectAllBtn').addEventListener('click', function() {
    checkboxes.forEach(cb => cb.checked = true);
    updateCount();
  });

  // Cancel selection
  document.getElementById('cancelBtn').addEventListener('click', function() {
    checkboxes.forEach(cb => cb.checked = false);
    updateCount();
    selectMode = false;
    toggleText.textContent = 'Sélectionner';
    selectToggle.classList.remove('btn-primary');
    selectToggle.classList.add('btn-outline-primary');
    document.querySelectorAll('.checkbox-container').forEach(cb => cb.classList.add('d-none'));
    deleteForm.classList.add('d-none');
  });

  // Auto-scroll to bottom
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
  
  // Delete confirmation
  document.getElementById('deleteForm').addEventListener('submit', function(e) {
    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
    if (!confirm(`⚠️ Supprimer ${checkedCount} message${checkedCount > 1 ? 's' : ''} ? Irreversible.`)) {
      e.preventDefault();
    }
  });
});
</script>

@endsection

