@extends('layouts.student')

@section('title', 'Mes Matières')

@section('page_title', 'Matières')
@section('breadcrumb', 'Matières')

@section('content')

<div class="st-page-header">
    <div>
        <h1><i class="bi bi-book" style="color:#818CF8;"></i> Mes Matières</h1>
        <div class="subtitle">Choisissez une matière pour accéder directement à vos cours</div>
    </div>
</div>

<!-- Info niveau + classe -->
@if(isset($classRoom))
<div style="background:linear-gradient(135deg, rgba(79,70,229,0.1), rgba(124,58,237,0.05));border:1px solid rgba(124,58,237,0.1);border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;flex-wrap:wrap;gap:1.25rem;">
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#4F46E5,#7C3AED);display:flex;align-items:center;justify-content:center;color:white;font-size:1.1rem;flex-shrink:0;">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div>
            <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748B;font-weight:600;">Niveau</div>
            <div style="font-weight:700;color:#F1F5F9;font-size:1rem;">{{ $classRoom->level->name ?? $level->name ?? 'Non défini' }}</div>
        </div>
    </div>
    <div style="width:1px;height:32px;background:rgba(255,255,255,0.08);"></div>
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#059669,#10B981);display:flex;align-items:center;justify-content:center;color:white;font-size:1.1rem;flex-shrink:0;">
            <i class="bi bi-building"></i>
        </div>
        <div>
            <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748B;font-weight:600;">Classe</div>
            <div style="font-weight:700;color:#F1F5F9;font-size:1rem;">{{ $classRoom->name }}</div>
        </div>
    </div>
    <div style="margin-left:auto;font-size:0.78rem;color:#64748B;display:flex;align-items:center;gap:6px;">
        <span style="width:6px;height:6px;border-radius:50%;background:#10B981;display:inline-block;"></span>
        {{ $subjects->count() }} matière{{ $subjects->count() > 1 ? 's' : '' }} disponible{{ $subjects->count() > 1 ? 's' : '' }}
    </div>
</div>
@endif

@if($subjects->count() > 0)
<div class="row g-3">
    @foreach($subjects as $subject)
        @php
            $icons = ['calculator','flask','translate','globe','palette','music-note-beamed','cpu','graph-up','book','pencil','journal','robot'];
            $colors = ['#4F46E5','#059669','#7C3AED','#D97706','#DC2626','#0284C7','#0891B2','#9333EA'];
            $color = $colors[$loop->index % count($colors)];
            $icon = $icons[$loop->index % count($icons)];
            list($r,$g,$b) = sscanf($color, '#%02x%02x%02x');
        @endphp
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <a href="{{ $classRoom && $level
                ? route('student.subjects.courses', [$subject->id, $level->id, $classRoom->id])
                : route('student.subjects.levels', $subject->id) }}"
               style="text-decoration:none;display:block;height:100%;">
                <div style="background:linear-gradient(135deg,#1E293B,#1a2332);border:1px solid rgba(255,255,255,0.04);border-radius:12px;padding:1.5rem 1.25rem;text-align:center;transition:all 0.2s ease;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative;overflow:hidden;" onmouseover="this.style.borderColor='rgba(255,255,255,0.12)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.2)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                    <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;border-radius:50%;background:rgba({{ $r }},{{ $g }},{{ $b }},0.06);"></div>
                    <div style="width:56px;height:56px;border-radius:50%;background:rgba({{ $r }},{{ $g }},{{ $b }},0.1);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:{{ $color }};margin-bottom:0.75rem;transition:transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="bi bi-{{ $icon }}"></i>
                    </div>
                    <h5 style="font-weight:700;color:#F1F5F9;margin-bottom:0.35rem;font-size:0.95rem;">{{ $subject->name }}</h5>
                    <p style="font-size:0.78rem;color:#64748B;margin:0;display:flex;align-items:center;gap:5px;">
                        <i class="bi bi-play-circle me-1" style="color:#4F46E5;"></i> Voir mes cours
                    </p>
                </div>
            </a>
        </div>
    @endforeach
</div>
@else
<div class="pr-empty">
    <div class="pr-empty-icon"><i class="bi bi-book"></i></div>
    <h5>Aucune matière disponible</h5>
    <p>Aucune matière n'est disponible pour le moment.</p>
    <a href="{{ route('student.dashboard') }}" class="st-btn st-btn-ghost st-btn-sm"><i class="bi bi-arrow-left me-1"></i> Retour au tableau de bord</a>
</div>
@endif

@endsection
