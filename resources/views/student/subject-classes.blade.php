@extends('layouts.student')
@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('student.subjects.index') }}"><i class="bi bi-book me-1"></i>Matières</a><span class="sep">/</span>
            <span style="color:#94A3B8;">{{ $subject->name }}</span>
        </div>
        <h1><i class="bi bi-building" style="color:#7C3AED;"></i> Classes — {{ $subject->name }}</h1>
        <div class="subtitle">Choisissez une classe pour voir les cours</div>
    </div>
</div>

@if(isset($classes) && $classes->count() > 0)
<div class="row g-3">
    @foreach($classes as $class)
    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('student.courses', [$subject->id, $class->id]) }}" style="text-decoration:none;display:block;">
            <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;overflow:hidden;transition:all 0.2s ease;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                <div style="height:100px;background:linear-gradient(135deg,hsl({{ ($loop->index * 60 + 240) % 360 }},55%,50%),hsl({{ ($loop->index * 60 + 270) % 360 }},50%,35%));display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-building" style="font-size:2.5rem;color:rgba(255,255,255,0.25);"></i>
                </div>
                <div style="padding:1rem 1.25rem;text-align:center;">
                    <h5 style="font-weight:700;color:#F1F5F9;margin-bottom:0.25rem;">{{ $class->name }}</h5>
                    <p style="font-size:0.78rem;color:#64748B;margin:0;"><i class="bi bi-play-circle me-1"></i> Voir les cours <i class="bi bi-arrow-right ms-1"></i></p>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@else
<div class="pr-empty"><div class="pr-empty-icon"><i class="bi bi-building"></i></div><h5>Aucune classe disponible</h5><p>Aucune classe n'est associée à cette matière pour ce niveau.</p><a href="{{ route('student.subjects', $level->id) }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-arrow-left me-1"></i> Retour aux matières</a></div>
@endif

@endsection
