@extends('layouts.student')
@section('title', 'Cours - {{ $subject->name ?? '' }}')
@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('student.subjects.index') }}"><i class="bi bi-book me-1"></i>Matières</a><span class="sep">/</span>
            <span style="color:#94A3B8;">{{ $subject->name }}</span>
        </div>
        <h1><i class="bi bi-play-circle" style="color:#059669;"></i> Cours de {{ $subject->name }}</h1>
        <div class="subtitle">{{ $class->name }} — Explorez les leçons disponibles</div>
    </div>
</div>

@if($courses->count() > 0)
<div class="row g-3">
    @foreach($courses as $course)
        @php $hue = ($loop->index * 37 + 180) % 360; @endphp
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('student.course.show', $course->id) }}" style="text-decoration:none;display:block;">
                <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;overflow:hidden;transition:all 0.2s ease;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                    <div style="height:120px;background:linear-gradient(135deg,hsl({{ $hue }},55%,50%),hsl({{ $hue + 30 }},50%,35%));display:flex;align-items:center;justify-content:center;position:relative;">
                        <i class="bi bi-journal-bookmark-fill" style="font-size:2.5rem;color:rgba(255,255,255,0.25);"></i>
                    </div>
                    <div style="padding:1rem 1.25rem;">
                        <h5 style="font-weight:600;color:#F1F5F9;margin-bottom:0.5rem;font-size:0.9rem;">{{ Str::limit($course->title, 45) }}</h5>
                        <div style="display:flex;gap:6px;margin-bottom:0.5rem;">
                            @if($course->video)<span class="pr-badge pr-badge-danger" style="font-size:0.65rem;"><i class="bi bi-play-circle-fill me-1"></i> Vidéo</span>@endif
                            @if($course->pdf)<span class="pr-badge pr-badge-primary" style="font-size:0.65rem;"><i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF</span>@endif
                        </div>
                        <div style="font-size:0.78rem;color:#0284C7;font-weight:500;"><i class="bi bi-arrow-right-circle me-1"></i> Voir le cours</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@else
<div class="pr-empty"><div class="pr-empty-icon"><i class="bi bi-book"></i></div><h5>Aucun cours disponible</h5><p>Aucun cours n'est encore disponible pour cette matière.</p><a href="{{ route('student.my.subjects', $level) }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-arrow-left me-1"></i> Retour aux matières</a></div>
@endif

@endsection
