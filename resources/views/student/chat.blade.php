@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-hero-dark st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-chat-dots-fill me-2"></i>💬 {{ $subject->name }}</h1>
        <p>Discutez avec vos professeurs et camarades</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-chat-dots-fill"></i>
      </div>
    </div>

    {{-- Messages --}}
    <div class="st-card st-fade-up st-fade-up-d1">
      <div class="st-card-body" style="max-height: 450px; overflow-y: auto; display: flex; flex-direction: column; gap: .75rem;">
        @forelse($messages as $msg)
          <div style="padding: .75rem 1rem; border-radius: 12px; {{ $msg->user_id === auth()->id() ? 'background: #eff6ff; border: 1px solid #bfdbfe; align-self: flex-end; max-width: 80%;' : 'background: #f8fafc; border: 1px solid #e2e8f0; align-self: flex-start; max-width: 80%;' }}">
            <div style="font-weight: 700; font-size: 13px; margin-bottom: 4px; {{ $msg->user_id === auth()->id() ? 'color: var(--st-primary);' : 'color: var(--st-text);' }}">
              {{ $msg->user->name }}
            </div>
            <div style="font-size: 14px; color: var(--st-text);">{{ $msg->message }}</div>
          </div>
        @empty
          <div class="st-empty">
            <i class="bi bi-chat-dots"></i>
            <h5>Aucun message</h5>
            <p>Soyez le premier à écrire !</p>
          </div>
        @endforelse
      </div>
    </div>

    {{-- Form --}}
    <div class="st-card st-mt-3 st-fade-up st-fade-up-d2">
      <div class="st-card-body">
        <form method="POST" action="{{ route('student.chat.send') }}">
          @csrf
          <input type="hidden" name="subject_id" value="{{ $subject->id }}">
          <div class="st-flex st-gap-2">
            <input type="text" name="message" class="st-form-input" placeholder="Écrire un message..." required style="flex: 1;">
            <button type="submit" class="st-btn st-btn-primary">
              <i class="bi bi-send"></i> Envoyer
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection
