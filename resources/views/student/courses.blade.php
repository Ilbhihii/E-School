@extends('layouts.student')
@section('title', 'Mes Cours')
@section('content')

<div class="page-header">
    <div>
        <h1><i class="bi bi-book-half" style="color:#059669;"></i> Mes Cours</h1>
        <div class="subtitle">
            @if(isset($classes) && $classes->count() > 0)
                Choisissez une classe pour voir les cours
            @else
                Aucune classe assignée. Contactez l'administrateur.
            @endif
        </div>
    </div>
</div>

@if(isset($classes) && $classes->count() > 0)
<div class="row g-3">
    @foreach($classes as $class)
        @php $hue = ($loop->index * 60 + 180) % 360; @endphp
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('student.subjects.index') }}" style="text-decoration:none;display:block;">
                <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;overflow:hidden;transition:all 0.2s ease;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                    <div style="height:120px;background:linear-gradient(135deg,hsl({{ $hue }},55%,50%),hsl({{ $hue + 30 }},50%,35%));display:flex;align-items:center;justify-content:center;position:relative;">
                        <i class="bi bi-book-half" style="font-size:2.5rem;color:rgba(255,255,255,0.25);"></i>
                    </div>
                    <div style="padding:1rem 1.25rem;text-align:center;">
                        <h4 style="font-weight:700;color:#F1F5F9;margin-bottom:0.5rem;font-size:1rem;">{{ $class->name }}</h4>
                        <span class="pr-badge pr-badge-success"><i class="bi bi-play-circle me-1"></i>{{ $class->courses_count ?? 0 }} cours</span>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@else
<div class="pr-empty">
    <div class="pr-empty-icon"><i class="bi bi-inbox"></i></div>
    <h5>Aucune classe disponible</h5>
    <p>Vous n'êtes assigné à aucune classe pour le moment.</p>
    <a href="{{ route('student.dashboard') }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-house me-2"></i> Retour au Dashboard</a>
</div>
@endif

@endsection
