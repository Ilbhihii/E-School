@extends('layouts.admin')

@section('title', 'Classes - ' . $subject->name . ' - ' . $level->name)
@section('page_title', $level->name . ' — ' . $subject->name)
@section('breadcrumb', 'Lives → ' . $subject->name . ' → ' . $level->name)

@section('content')

<div class="adm-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:0.8rem;color:var(--adm-text-muted);">
            <a href="{{ route('admin.lives.index') }}" style="color:var(--adm-text-muted);text-decoration:none;"><i class="bi bi-camera-video me-1"></i>Lives</a>
            <span>/</span>
            <a href="{{ route('admin.lives.subject-levels', $subject) }}" style="color:var(--adm-text-muted);text-decoration:none;">{{ $subject->name }}</a>
            <span>/</span>
            <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ $level->name }}</span>
        </div>
        <h1><i class="bi bi-building me-2" style="color:var(--adm-danger);"></i> Classes — {{ $subject->name }} · {{ $level->name }}</h1>
        <div class="subtitle">Sélectionnez une classe pour voir ses lives</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.lives.subject-levels', $subject) }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour aux niveaux
        </a>
    </div>
</div>

@if($classes->isEmpty())
    <div class="adm-card">
        <div class="adm-empty" style="padding:4rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-building"></i></div>
            <h5>Aucune classe avec des lives</h5>
            <p>Aucune classe n'a de live programmé pour cette matière et ce niveau.</p>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach($classes as $class)
            @php
                $gradients = [
                    'linear-gradient(135deg, #16A34A, #22C55E)',
                    'linear-gradient(135deg, #003A8F, #2563EB)',
                    'linear-gradient(135deg, #D97706, #FFB347)',
                    'linear-gradient(135deg, #7C3AED, #A78BFA)',
                    'linear-gradient(135deg, #D90429, #EF4444)',
                    'linear-gradient(135deg, #06B6D4, #0891B2)',
                ];
                $gIdx = $loop->index % count($gradients);
                $liveCount = $class->lives_count;
            @endphp
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('admin.lives.class-lives', [$subject, $level, $class]) }}" class="text-decoration-none">
                    <div class="adm-card st-fade-up" style="cursor:pointer;height:100%;transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                        <div style="height:100px;background:{{ $gradients[$gIdx] }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                            <div style="position:absolute;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06);top:-40px;right:-40px;"></div>
                            <i class="bi bi-mortarboard-fill" style="font-size:2.5rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                        </div>
                        <div class="adm-card-body text-center" style="padding:1.5rem;">
                            <h5 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.5rem;">{{ $class->name }}</h5>
                            <div style="margin-bottom:1rem;">
                                <span style="color:var(--adm-text-muted);font-size:0.8rem;">
                                    <i class="bi bi-camera-video me-1" style="color:#EF4444;"></i> {{ $liveCount }} live(s)
                                </span>
                            </div>
                            <span class="adm-btn" style="background:{{ $gradients[$gIdx] }};color:white;border:none;width:100%;">
                                <i class="bi bi-camera-video me-1"></i> Voir les lives
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif

@endsection
