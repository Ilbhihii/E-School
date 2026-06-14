@extends('layouts.prof')

@section('title', 'Questions Étudiants')
@section('page_title', 'Questions Étudiants')
@section('breadcrumb', 'Chat par matière')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-chat-dots-fill me-2" style="color:var(--adm-primary);"></i> Questions Étudiants</h1>
        <div class="subtitle">Sélectionnez une matière pour répondre aux questions</div>
    </div>
</div>

@if(isset($subjects) && $subjects->count() > 0)
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;">
    @forelse($subjects as $subject)
        @if($subject->name !== 'Administration')
        <a href="{{ route('prof.chat', $subject->id) }}" style="text-decoration:none;">
            <div class="adm-card text-center" style="transition:all 0.3s ease;cursor:pointer;overflow:hidden;">
                <div style="height:80px;background:linear-gradient(135deg,#6366F1,#8B5CF6);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-journal-text" style="font-size:2rem;color:rgba(255,255,255,0.3);"></i>
                </div>
                <div class="adm-card-body" style="padding:1.5rem;">
                    <h5 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.75rem;">{{ $subject->name }}</h5>
                    <span class="adm-btn adm-btn-primary adm-btn-sm" style="width:100%;">
                        Ouvrir Chat <i class="bi bi-chevron-right ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        @endif
    @empty
        <div style="grid-column:1/-1;">
            <div class="adm-empty" style="padding:4rem 2rem;">
                <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                <h5>Aucune matière disponible</h5>
                <p>Les matières apparaîtront ici une fois créées.</p>
            </div>
        </div>
    @endforelse
</div>
@else
<div class="adm-empty" style="padding:4rem 2rem;">
    <div class="adm-empty-icon"><i class="bi bi-chat-dots"></i></div>
    <h5>Aucune matière disponible</h5>
    <p>Les matières apparaîtront ici une fois créées.</p>
</div>
@endif

<style>
.adm-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.25);
}
</style>

@endsection
