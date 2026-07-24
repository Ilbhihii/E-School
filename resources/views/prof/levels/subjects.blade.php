@extends('layouts.prof')

@section('title', 'Matières - ' . $class->name)
@section('page_title', $class->name)
@section('breadcrumb', 'Niveaux → Classes → Matières')

@section('content')

<div class="adm-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:0.8rem;color:var(--adm-text-muted);">
            <a href="{{ route('prof.levels.index') }}" style="color:var(--adm-text-muted);text-decoration:none;"><i class="bi bi-layers me-1"></i>Niveaux</a>
            <span>/</span>
            <a href="{{ route('prof.levels.classes', $level) }}" style="color:var(--adm-text-muted);text-decoration:none;">{{ $level->name }}</a>
            <span>/</span>
            <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ $class->name }}</span>
        </div>
        <h1><i class="bi bi-book me-2" style="color:var(--adm-accent);"></i> Matières — {{ $class->name }}</h1>
        <div class="subtitle">{{ $level->name }} — Sélectionnez une matière pour gérer ses cours</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.levels.classes', $level) }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour aux classes
        </a>
    </div>
</div>

@if($subjects->isEmpty())
    <div class="adm-card">
        <div class="adm-empty" style="padding:3rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-book"></i></div>
            <h5>Aucune matière liée à cette classe</h5>
            <p>Les matières seront disponibles une fois configurées par l'administration.</p>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach($subjects as $subject)
            @php
                $courseCount = $subject->course_count ?? 0;
            @endphp
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('prof.levels.courses', [$level, $class, $subject]) }}" class="text-decoration-none">
                    <div class="adm-card st-fade-up" style="cursor:pointer;height:100%;transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                        <div class="adm-card-body text-center" style="padding:2rem 1.5rem;">
                            <div style="width:64px;height:64px;border-radius:16px;background:rgba(124,58,237,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.75rem;color:#A78BFA;">
                                <i class="bi bi-book-fill"></i>
                            </div>
                            <h5 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.5rem;">{{ $subject->name }}</h5>
                            <p style="color:var(--adm-text-muted);font-size:0.8rem;margin-bottom:1rem;">
                                <i class="bi bi-play-circle me-1"></i> {{ $courseCount }} cours
                            </p>
                            <span class="adm-btn" style="background:linear-gradient(135deg,#7C3AED,#A78BFA);color:white;border:none;width:100%;">
                                <i class="bi bi-play-circle me-1"></i> Voir les cours
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif

@endsection
