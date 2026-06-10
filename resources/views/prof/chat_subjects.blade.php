@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header" style="text-align:center;flex-direction:column;">
            <div style="width:72px;height:72px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-chat-dots-fill" style="font-size:1.75rem;color:white;"></i>
            </div>
            <div>
                <h1 class="admin-header-title"><span class="gradient">Liste des Matières</span></h1>
                <p class="admin-header-subtitle">Sélectionnez une matière pour discuter avec vos étudiants</p>
            </div>
        </div>

        <div class="adm-grid-4">
            @forelse($subjects as $subject)
                @if($subject->name !== 'Administration')                    <a href="{{ route('prof.chat', $subject->id) }}" style="text-decoration:none;display:block;">
                        <div class="adm-card" style="height:100%;overflow:hidden;">
                        <div class="adm-card-body" style="text-align:center;padding:2rem 1.5rem;background:linear-gradient(135deg,#667eea,#764ba2);">
                            <i class="bi bi-journal-text" style="font-size:2rem;color:white;margin-bottom:0.75rem;display:block;"></i>
                            <h4 style="font-weight:700;font-size:1.1rem;color:white;margin:0 0 0.75rem;">{{ $subject->name }}</h4>
                            <span class="adm-badge" style="background:rgba(255,255,255,0.2);color:white;border:none;">
                                Ouvrir Chat <i class="bi bi-chevron-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
                @endif
            @empty
                <div class="adm-empty" style="grid-column:1/-1;">
                    <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                    <h3>Aucune matière disponible</h3>
                    <p>Les matières apparaîtront ici une fois créées.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
