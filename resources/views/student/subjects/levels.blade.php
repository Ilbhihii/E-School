@extends('layouts.student')

@section('title', 'Niveaux - ' . $subject->name)

@section('page_title', $subject->name)
@section('breadcrumb', 'Matières → Niveaux')

@section('content')

<div class="st-page-header">
    <div>
        <div class="breadcrumb d-flex align-items-center gap-2 mb-2" style="font-size:0.78rem;color:#64748B;">
            <a href="{{ route('student.subjects.index') }}" style="color:#64748B;text-decoration:none;"><i class="bi bi-book me-1"></i>Matières</a>
            <span style="color:rgba(255,255,255,0.12);">/</span>
            <span style="color:#F1F5F9;font-weight:600;">{{ $subject->name }}</span>
        </div>
        <h1><i class="bi bi-layers" style="color:#7C3AED;"></i> Niveaux — {{ $subject->name }}</h1>
        <div class="subtitle">Choisissez un niveau pour voir les classes disponibles</div>
    </div>
</div>

@if($levels->isEmpty())
<div class="pr-empty">
    <div class="pr-empty-icon"><i class="bi bi-layers"></i></div>
    <h5>Aucun niveau disponible</h5>
    <p>Cette matière n'a pas encore de niveaux associés.</p>
    <a href="{{ route('student.subjects.index') }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-arrow-left me-1"></i> Retour aux matières</a>
</div>
@else
<div class="row g-3">
    @foreach($levels as $level)
        @php
            $levelColors = ['#22C55E','#60A5FA','#FFB347','#A78BFA'];
            $levelIcons = ['bi-emoji-smile','bi-emoji-neutral','bi-emoji-wink','bi-emoji-star-eyes'];
            $color = $levelColors[$loop->index % count($levelColors)];
            $icon = $levelIcons[$loop->index % count($levelIcons)];
            list($r,$g,$b) = sscanf($color, '#%02x%02x%02x');
        @endphp
        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('student.subjects.classes', [$subject->id, $level->id]) }}" style="text-decoration:none;display:block;">
                <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;padding:1.5rem 1.25rem;text-align:center;transition:all 0.2s ease;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                    <div style="width:56px;height:56px;border-radius:50%;background:rgba({{ $r }},{{ $g }},{{ $b }},0.1);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:{{ $color }};margin-bottom:0.75rem;">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <h5 style="font-weight:700;color:#F1F5F9;margin-bottom:0.35rem;">{{ $level->name }}</h5>
                    <p style="font-size:0.78rem;color:#64748B;margin:0;"><i class="bi bi-building me-1"></i> Voir les classes <i class="bi bi-arrow-right ms-1"></i></p>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endif

@endsection
