@extends('layouts.student')

@section('title', 'Classes - ' . $subject->name . ' - ' . $level->name)

@section('page_title', $level->name . ' — ' . $subject->name)
@section('breadcrumb', 'Matières → Niveaux → Classes')

@section('content')

<div class="st-page-header">
    <div>
        <div class="breadcrumb d-flex align-items-center gap-2 mb-2" style="font-size:0.78rem;color:#64748B;">
            <a href="{{ route('student.subjects.index') }}" style="color:#64748B;text-decoration:none;"><i class="bi bi-book me-1"></i>Matières</a>
            <span style="color:rgba(255,255,255,0.12);">/</span>
            <a href="{{ route('student.subjects.levels', $subject->id) }}" style="color:#94A3B8;text-decoration:none;">{{ $subject->name }}</a>
            <span style="color:rgba(255,255,255,0.12);">/</span>
            <span style="color:#F1F5F9;font-weight:600;">{{ $level->name }}</span>
        </div>
        <h1><i class="bi bi-building" style="color:#0284C7;"></i> Classes — {{ $subject->name }} · {{ $level->name }}</h1>
        <div class="subtitle">Choisissez une classe pour accéder aux cours</div>
    </div>
</div>

@if($classes->isEmpty())
<div class="pr-empty">
    <div class="pr-empty-icon"><i class="bi bi-building"></i></div>
    <h5>Aucune classe disponible</h5>
    <p>Aucune classe n'est liée à cette matière pour ce niveau.</p>
    <a href="{{ route('student.subjects.levels', $subject->id) }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-arrow-left me-1"></i> Retour aux niveaux</a>
</div>
@else
<div class="row g-3">
    @foreach($classes as $class)
        @php
            $hue = ($loop->index * 60 + 200) % 360;
        @endphp
        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('student.subjects.courses', [$subject->id, $level->id, $class->id]) }}" style="text-decoration:none;display:block;">
                <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;overflow:hidden;transition:all 0.2s ease;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                    <div style="height:100px;background:linear-gradient(135deg,hsl({{ $hue }},55%,50%),hsl({{ $hue + 30 }},50%,35%));display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-mortarboard-fill" style="font-size:2.5rem;color:rgba(255,255,255,0.25);"></i>
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
@endif

@endsection
