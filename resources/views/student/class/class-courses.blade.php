@extends('layouts.student')

@section('content')

<div class="page-header">
    <div>
        <h1><i class="bi bi-play-circle me-2" style="color:#06B6D4;"></i> Cours de la classe</h1>
        <div class="subtitle">{{ $class->name ?? '' }} — Explorez les leçons disponibles</div>
    </div>
</div>

@if(isset($class->courses) && $class->courses->count() > 0)
<div class="row g-4">
    @foreach($class->courses as $course)
        @php
            $hue = ($loop->index * 47 + 180) % 360;
        @endphp
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('student.course.show', $course->id) }}" style="text-decoration:none;display:block;height:100%;">
                <div class="pr-card h-100" style="transition:all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);overflow:hidden;">
                    <div style="height:140px;background:linear-gradient(135deg,hsl({{ $hue }},55%,50%),hsl({{ $hue + 30 }},50%,35%));display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                        <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;background:rgba(255,255,255,0.08);border-radius:50%;"></div>
                        <div style="position:absolute;bottom:-30px;left:-10px;width:80px;height:80px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
                        <i class="bi bi-journal-bookmark-fill" style="font-size:3rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                        <div style="position:absolute;bottom:0;left:0;right:0;height:50%;background:linear-gradient(transparent,rgba(0,0,0,0.4));"></div>
                    </div>
                    <div class="pr-card-body" style="padding:1.25rem;">
                        <h5 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.75rem;font-size:0.95rem;">{{ Str::limit($course->title, 45) }}</h5>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            @if($course->video)
                                <span class="pr-badge pr-badge-danger" style="font-size:0.7rem;"><i class="bi bi-play-circle-fill me-1"></i> Vidéo</span>
                            @endif
                            @if($course->pdf)
                                <span class="pr-badge pr-badge-primary" style="font-size:0.7rem;"><i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF</span>
                            @endif
                        </div>
                        <div style="font-size:0.8rem;color:#06B6D4;font-weight:600;">
                            <i class="bi bi-arrow-right-circle me-1"></i> Voir le cours
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@else
<div class="pr-empty" style="padding:4rem 2rem;">
    <div class="pr-empty-icon"><i class="bi bi-book"></i></div>
    <h5>Aucun cours disponible</h5>
    <p>Aucun cours n'est encore disponible pour cette classe.</p>
    <a href="{{ route('student.subjects.index') }}" class="pr-btn pr-btn-primary pr-btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Retour aux matières
    </a>
</div>
@endif

<style>
.pr-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.25);
}
</style>

@endsection
