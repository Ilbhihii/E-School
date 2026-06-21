@extends('layouts.student')

@section('title', 'Cours - ' . $subject->name)

@section('page_title', $subject->name)
@section('breadcrumb', 'Matières → Niveaux → Classes → Cours')

@section('content')

<div class="breadcrumb" style="display:flex;align-items:center;gap:8px;margin-bottom:1rem;font-size:0.78rem;color:#64748B;flex-wrap:wrap;">
    <a href="{{ route('student.dashboard') }}" style="color:#64748B;text-decoration:none;"><i class="bi bi-house me-1"></i>Accueil</a>
    <span style="color:rgba(255,255,255,0.12);">/</span>
    <a href="{{ route('student.subjects.index') }}" style="color:#64748B;text-decoration:none;">Matières</a>
    <span style="color:rgba(255,255,255,0.12);">/</span>
    <a href="{{ route('student.subjects.levels', $subject->id) }}" style="color:#94A3B8;text-decoration:none;">{{ $subject->name }}</a>
    <span style="color:rgba(255,255,255,0.12);">/</span>
    <a href="{{ route('student.subjects.classes', [$subject->id, $level->id]) }}" style="color:#94A3B8;text-decoration:none;">{{ $level->name }}</a>
    <span style="color:rgba(255,255,255,0.12);">/</span>
    <span style="color:#94A3B8;font-weight:500;">{{ $class->name }}</span>
    <span style="color:rgba(255,255,255,0.12);">/</span>
    <span style="color:#F1F5F9;font-weight:600;">Cours</span>
</div>

<div class="st-page-header">
    <div>
        <h1><i class="bi bi-play-circle" style="color:#059669;"></i> Cours de {{ $subject->name }}</h1>
        <div class="subtitle">{{ $level->name }} · {{ $class->name }} — Explorez les leçons disponibles</div>
    </div>
</div>

@if($courses->count() > 0)
<div class="row g-3">
    @foreach($courses as $course)
        @php $hue = ($loop->index * 60 + 180) % 360; @endphp
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('student.course.show', $course->id) }}" style="text-decoration:none;display:block;">
                <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;overflow:hidden;transition:all 0.2s ease;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                    <div style="height:120px;background:linear-gradient(135deg,hsl({{ $hue }},55%,50%),hsl({{ $hue + 30 }},50%,35%));display:flex;align-items:center;justify-content:center;position:relative;">
                        <i class="bi bi-play-circle" style="font-size:2.5rem;color:rgba(255,255,255,0.25);"></i>
                    </div>
                    <div style="padding:1rem 1.25rem;text-align:center;">
                        <h4 style="font-weight:600;color:#F1F5F9;margin-bottom:0.5rem;font-size:0.95rem;">{{ Str::limit($course->title, 40) }}</h4>
                        <span class="pr-badge pr-badge-purple"><i class="bi bi-file-earmark-text me-1"></i>{{ $course->devoirs_count ?? 0 }} devoir{{ ($course->devoirs_count ?? 0) > 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@else
<div class="pr-empty">
    <div class="pr-empty-icon"><i class="bi bi-book"></i></div>
    <h5>Aucun cours disponible</h5>
    <p>Aucun cours n'est encore disponible pour cette matière, niveau et classe.</p>
    <a href="{{ route('student.subjects.classes', [$subject->id, $level->id]) }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-arrow-left me-2"></i> Retour aux classes</a>
</div>
@endif

@endsection
