@extends('layouts.prof')

@section('title', 'Classes - ' . $subject->name . ' - ' . $level->name)
@section('page_title', $level->name . ' — ' . $subject->name)
@section('breadcrumb', 'Matières → Niveaux → Classes')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;font-size:13px;color:var(--muted);">
                    <a href="{{ route('prof.subjects.list') }}" style="color:var(--muted);text-decoration:none;"><i class="bi bi-book me-1"></i>Matières</a>
                    <span>/</span>
                    <a href="{{ route('prof.subjects.levels', $subject) }}" style="color:var(--muted);text-decoration:none;">{{ $subject->name }}</a>
                    <span>/</span>
                    <span style="color:var(--text);font-weight:600;">{{ $level->name }}</span>
                </div>
                <h1 class="admin-header-title"><span class="gradient">🏫 Classes — {{ $subject->name }} · {{ $level->name }}</span></h1>
                <p class="admin-header-subtitle">Sélectionnez une classe pour gérer cours, lives et devoirs</p>
            </div>
        </div>

        @if($classes->isEmpty())
            <div class="adm-card">
                <div class="adm-empty">
                    <i class="bi bi-inbox"></i>
                    <h3>Aucune classe</h3>
                    <p>Aucune classe n'est liée à cette matière pour ce niveau.</p>
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
                        $colorMap = ['primary','success','danger','warning','info','purple'];
                    @endphp
                    <div class="col-md-4">
                        <div class="adm-card st-fade-up" style="transition:all .2s;cursor:pointer;">
                            <div style="height:100px;background:{{ $gradients[$gIdx] }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                                <div style="position:absolute;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06);top:-40px;right:-40px;"></div>
                                <i class="bi bi-mortarboard-fill" style="font-size:2.5rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                            </div>
                            <div class="adm-card-body text-center" style="padding:1.5rem;">
                                <h4 style="font-weight:700;color:var(--text);margin-bottom:1rem;">{{ $class->name }}</h4>
                                <div class="d-flex flex-wrap gap-2" style="justify-content:center;">
                                    <a href="{{ route('prof.browse.courses', [$level, $class, $subject]) }}" class="adm-btn adm-btn-{{ $colorMap[$gIdx % count($colorMap)] }} adm-btn-sm">
                                        <i class="bi bi-play-circle me-1"></i> Cours
                                    </a>
                                    <a href="{{ route('prof.browse.lives', [$level, $class]) }}" class="adm-btn adm-btn-danger adm-btn-sm">
                                        <i class="bi bi-camera-video me-1"></i> Lives
                                    </a>
                                    <a href="{{ route('prof.browse.devoirs', [$level, $class, $subject]) }}" class="adm-btn adm-btn-success adm-btn-sm">
                                        <i class="bi bi-file-earmark-check me-1"></i> Devoirs
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('prof.subjects.levels', $subject) }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-arrow-left me-1"></i> Retour aux niveaux
            </a>
        </div>

    </div>
</div>
@endsection
