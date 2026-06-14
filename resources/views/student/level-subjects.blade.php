@extends('layouts.student')
@section('title', 'Matières - {{ $level->name ?? '' }}')
@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('student.subjects.index') }}"><i class="bi bi-book me-1"></i>Matières</a>
            <span class="sep">/</span>
            <span style="color:#94A3B8;">{{ $level->name }} — {{ $class->name }}</span>
        </div>
        <h1><i class="bi bi-book-half" style="color:#0284C7;"></i> Mes Matières</h1>
        <div class="subtitle">{{ $class->name }} — Choisissez une matière pour voir les cours disponibles</div>
    </div>
</div>

@if($subjects->isEmpty())
<div class="pr-empty"><div class="pr-empty-icon"><i class="bi bi-book"></i></div><h5>Aucune matière disponible</h5><p>Aucune matière n'est associée à votre classe pour le moment.</p><a href="{{ route('student.subjects.index') }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-arrow-left me-1"></i> Retour aux matières</a></div>
@else
<div class="row g-3">
    @foreach($subjects as $subject)
        @php
            $colors = ['#0284C7','#059669','#7C3AED','#D97706','#DC2626','#0891B2','#4F46E5','#9333EA'];
            $icons = ['calculator','flask','translate','globe','palette','music-note-beamed','cpu','graph-up','book','pencil','journal','robot'];
            $color = $colors[$loop->index % count($colors)];
            $icon = $icons[$loop->index % count($icons)];
            list($r,$g,$b) = sscanf($color, '#%02x%02x%02x');
        @endphp
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <a href="{{ route('student.my.courses', [$level, $subject]) }}" style="text-decoration:none;display:block;">
                <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;padding:1.5rem 1.25rem;text-align:center;transition:all 0.2s ease;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                    <div style="width:56px;height:56px;border-radius:50%;background:rgba({{ $r }},{{ $g }},{{ $b }},0.1);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:{{ $color }};margin-bottom:0.75rem;"><i class="bi bi-{{ $icon }}"></i></div>
                    <h5 style="font-weight:700;color:#F1F5F9;margin-bottom:0.35rem;font-size:0.95rem;">{{ $subject->name }}</h5>
                    <p style="font-size:0.78rem;color:#64748B;margin:0;"><i class="bi bi-play-circle me-1"></i> Voir les cours <i class="bi bi-arrow-right ms-1"></i></p>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endif

@endsection
