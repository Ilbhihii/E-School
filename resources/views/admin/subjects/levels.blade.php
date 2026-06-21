@extends('layouts.admin')

@section('title', 'Niveaux - ' . $subject->name)
@section('page_title', $subject->name)
@section('breadcrumb', 'Matières → Niveaux')

@section('content')

<div class="adm-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:0.8rem;color:var(--adm-text-muted);">
            <a href="{{ route('admin.subjects.index') }}" style="color:var(--adm-text-muted);text-decoration:none;"><i class="bi bi-book me-1"></i>Matières</a>
            <span>/</span>
            <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ $subject->name }}</span>
        </div>
        <h1><i class="bi bi-layers me-2" style="color:var(--adm-primary);"></i> Niveaux — {{ $subject->name }}</h1>
        <div class="subtitle">Sélectionnez un niveau pour voir les classes disponibles</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.subjects.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour aux matières
        </a>
    </div>
</div>

@if($levels->isEmpty())
    <div class="adm-card">
        <div class="adm-empty" style="padding:4rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-layers"></i></div>
            <h5>Aucun niveau pour cette matière</h5>
            <p>Cette matière n'est pas encore liée à des classes.</p>
        </div>
    </div>
@else
    <div class="row g-4">
        @php
            $levelIcons = ['bi-emoji-smile', 'bi-emoji-neutral', 'bi-emoji-wink', 'bi-emoji-star-eyes'];
            $levelColors = ['#22C55E', '#60A5FA', '#FFB347', '#A78BFA'];
            $levelGradients = [
                'linear-gradient(135deg, #16A34A, #22C55E)',
                'linear-gradient(135deg, #003A8F, #60A5FA)',
                'linear-gradient(135deg, #D97706, #FFB347)',
                'linear-gradient(135deg, #7C3AED, #A78BFA)',
            ];
        @endphp
        @foreach($levels as $level)
            @php
                $idx = $loop->index % 4;
                $icon = $levelIcons[$idx];
                $gradient = $levelGradients[$idx];
                $classCount = $level->classes()->whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))->count();
            @endphp
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('admin.subjects.classes', [$subject, $level]) }}" class="text-decoration-none">
                    <div class="adm-card st-fade-up" style="cursor:pointer;height:100%;transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                        <div style="height:100px;background:{{ $gradient }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                            <div style="position:absolute;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06);top:-40px;right:-40px;"></div>
                            <i class="bi {{ $icon }}" style="font-size:2.5rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                        </div>
                        <div class="adm-card-body text-center" style="padding:1.5rem;">
                            <h5 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.5rem;">{{ $level->name }}</h5>
                            <p style="color:var(--adm-text-muted);font-size:0.8rem;margin-bottom:1rem;">
                                <i class="bi bi-building me-1"></i> {{ $classCount }} classe(s)
                            </p>
                            <span class="adm-btn" style="background:{{ $gradient }};color:white;border:none;width:100%;">
                                <i class="bi bi-building me-1"></i> Voir les classes
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif

@endsection
