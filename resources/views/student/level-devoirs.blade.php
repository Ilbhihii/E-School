@extends('layouts.student')
@section('title', 'Devoirs - {{ $subject->name ?? '' }}')
@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('student.subjects.index') }}"><i class="bi bi-book me-1"></i>Matières</a><span class="sep">/</span>
            <span style="color:#94A3B8;">{{ $subject->name }}</span>
        </div>
        <h1><i class="bi bi-clipboard-check" style="color:#059669;"></i> Devoirs — {{ $subject->name }}</h1>
        <div class="subtitle">{{ $class->name }} — Consultez et soumettez vos devoirs</div>
    </div>
</div>

@if(session('success'))
<div class="pr-alert pr-alert-success mb-3"><i class="bi bi-check-circle-fill" style="flex-shrink:0;margin-top:1px;"></i> <span>{{ session('success') }}</span></div>
@endif

@if($courses->count() > 0)
    @foreach($courses as $course)
        @if($course->devoirs->count() > 0)
        <div class="pr-card mb-3">
            <div class="pr-card-header">
                <h4><i class="bi bi-book-fill" style="color:#4F46E5;"></i> {{ $course->title }}</h4>
                <span class="pr-badge pr-badge-primary">{{ $course->devoirs->count() }} devoir{{ $course->devoirs->count() > 1 ? 's' : '' }}</span>
            </div>
            <div class="pr-card-body">
                @foreach($course->devoirs as $devoir)
                <div style="border:1px solid rgba(255,255,255,0.04);border-radius:8px;padding:0.85rem;margin-bottom:0.5rem;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)'">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <h6 style="font-weight:600;color:#F1F5F9;margin-bottom:0.15rem;font-size:0.88rem;">{{ $devoir->title }}</h6>
                            @if($devoir->description)<p style="font-size:0.78rem;color:#64748B;margin-bottom:0.25rem;">{{ $devoir->description }}</p>@endif
                            @if($devoir->due_date)<small style="color:#D97706;font-size:0.72rem;"><i class="bi bi-calendar me-1"></i>À rendre avant le {{ \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') }}</small>@endif
                        </div>
                        @if($devoir->file)<a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="pr-btn pr-btn-ghost pr-btn-sm" style="font-size:0.7rem;"><i class="bi bi-eye"></i> Voir</a>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
@else
<div class="pr-empty"><div class="pr-empty-icon"><i class="bi bi-inbox"></i></div><h5>Aucun devoir disponible</h5><p>Aucun devoir n'est encore associé à cette matière.</p><a href="{{ route('student.courses', [$level, $class, $subject]) }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-arrow-left me-1"></i> Voir les cours</a></div>
@endif

@endsection
