@extends('layouts.student')
@section('title', 'Chat par matière')
@section('content')

<div class="page-header">
    <div>
        <h1><i class="bi bi-chat-dots-fill" style="color:#0284C7;"></i> Chat par matière</h1>
        <div class="subtitle">Discutez avec vos professeurs et camarades</div>
    </div>
</div>

@if(isset($subjects) && $subjects->count() > 0)
<div class="row g-3">
    @foreach($subjects->unique('name') as $subject)
    <div class="col-md-4 col-sm-6">
        <a href="{{ route('student.student.chat', $subject->id) }}" style="text-decoration:none;display:block;">
            <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;padding:1.5rem;text-align:center;transition:all 0.2s ease;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(2,132,199,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;font-size:1.3rem;color:#0284C7;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="bi {{ mb_strtolower($subject->name) === 'administration' ? 'bi-headset' : 'bi-book-half' }}"></i>
                </div>
                <h5 style="font-weight:700;color:#F1F5F9;margin-bottom:0.35rem;">{{ $subject->name }}</h5>
                <p style="font-size:0.78rem;color:#64748B;margin-bottom:0.75rem;">
                    {{ mb_strtolower($subject->name) === 'administration'
                        ? 'Conversation privée avec l’administration'
                        : 'Posez vos questions aux professeurs' }}
                </p>
                <span class="pr-btn pr-btn-primary pr-btn-sm" style="width:100%;justify-content:center;"><i class="bi bi-chat-dots me-1"></i> Ouvrir Chat</span>
            </div>
        </a>
    </div>
    @endforeach
</div>
@else
<div class="pr-empty">
    <div class="pr-empty-icon"><i class="bi bi-chat-dots"></i></div>
    <h5>Aucune matière disponible</h5>
    <p>Vous n'avez accès à aucun chat pour le moment.</p>
</div>
@endif

@endsection
