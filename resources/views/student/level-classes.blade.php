@extends('layouts.student')
@section('title', 'Classes - {{ $levelName ?? $level->name ?? '' }}')
@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('student.subjects.index') }}"><i class="bi bi-book me-1"></i>Matières</a>
            <span class="sep">/</span>
            <span style="color:#94A3B8;">{{ $levelName ?? $level->name ?? '' }}</span>
        </div>
        <h1><i class="bi bi-building" style="color:#0284C7;"></i> Classes — {{ $levelName ?? $level->name ?? '' }}</h1>
        <div class="subtitle">Sélectionnez une classe pour voir les matières disponibles</div>
    </div>
</div>

@if($classes->isEmpty())
<div class="pr-empty"><div class="pr-empty-icon"><i class="bi bi-building"></i></div><h5>Aucune classe disponible</h5><p>Aucune classe n'est associée à ce niveau pour le moment.</p><a href="{{ route('student.subjects.index') }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-arrow-left me-1"></i> Retour aux matières</a></div>
@else
<div class="row g-3">
    @foreach($classes as $class)
    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('student.subjects', [$level->id, $class]) }}" style="text-decoration:none;display:block;">
            <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;padding:1.5rem 1.25rem;text-align:center;transition:all 0.2s ease;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(2,132,199,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;font-size:1.3rem;color:#0284C7;"><i class="bi bi-building"></i></div>
                <h5 style="font-weight:700;color:#F1F5F9;margin-bottom:0.35rem;">{{ $class->name }}</h5>
                <p style="font-size:0.78rem;color:#64748B;margin:0;"><i class="bi bi-book me-1"></i> Voir les matières <i class="bi bi-arrow-right ms-1"></i></p>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endif

@endsection
