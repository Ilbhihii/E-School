@extends('layouts.prof')

@section('title', 'Niveaux - ' . $subject->name)
@section('page_title', $subject->name)
@section('breadcrumb', 'Matières → Niveaux')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;font-size:13px;color:var(--muted);">
                    <a href="{{ route('prof.subjects.list') }}" style="color:var(--muted);text-decoration:none;"><i class="bi bi-book me-1"></i>Matières</a>
                    <span>/</span>
                    <span style="color:var(--text);font-weight:600;">{{ $subject->name }}</span>
                </div>
                <h1 class="admin-header-title"><span class="gradient">🎯 Niveaux — {{ $subject->name }}</span></h1>
                <p class="admin-header-subtitle">Sélectionnez un niveau pour voir les classes</p>
            </div>
        </div>

        @if($levels->isEmpty())
            <div class="adm-card">
                <div class="adm-empty">
                    <i class="bi bi-inbox"></i>
                    <h3>Aucun niveau</h3>
                    <p>Aucun niveau n'est associé à cette matière.</p>
                </div>
            </div>
        @else
            <div class="prof-card-grid">
                @php
                    $levelGradients = [
                        'linear-gradient(135deg, #16A34A, #22C55E)',
                        'linear-gradient(135deg, #003A8F, #60A5FA)',
                        'linear-gradient(135deg, #D97706, #FFB347)',
                        'linear-gradient(135deg, #7C3AED, #A78BFA)',
                    ];
                    $levelIcons = ['bi-emoji-smile','bi-emoji-neutral','bi-emoji-wink','bi-emoji-star-eyes'];
                @endphp
                @foreach($levels as $level)
                    @php
                        $idx = $loop->index % 4;
                        $gradient = $levelGradients[$idx];
                        $icon = $levelIcons[$idx];
                    @endphp
                    <div>
                        <a href="{{ route('prof.subjects.classes', [$subject, $level]) }}" class="prof-path-card st-fade-up">
                            <div class="prof-path-cover" style="--path-gradient:{{ $gradient }};">
                                    <div style="position:absolute;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06);top:-40px;right:-40px;"></div>
                                    <i class="bi {{ $icon }}" style="font-size:2.5rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                            </div>
                            <div class="prof-path-body">
                                <span class="prof-path-kicker"><i class="bi bi-layers"></i> Niveau</span>
                                <h2>{{ $level->name }}</h2>
                                <div class="prof-path-action" style="--path-gradient:{{ $gradient }};"><span>Voir les classes</span><i class="bi bi-arrow-right"></i></div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection
