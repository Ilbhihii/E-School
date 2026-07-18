@extends('layouts.admin')

@section('title', 'Gestion des Matières')
@section('page_title', 'Matières')
@section('breadcrumb', 'Matières')

@section('content')

<style>
@keyframes subjFadeIn {
    from { opacity: 0; transform: translateY(24px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.subject-card-outer {
    opacity: 0;
    animation: subjFadeIn 0.55s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    will-change: transform, opacity;
}
.subject-card-outer:nth-child(1) { animation-delay: 0.05s; }
.subject-card-outer:nth-child(2) { animation-delay: 0.10s; }
.subject-card-outer:nth-child(3) { animation-delay: 0.15s; }
.subject-card-outer:nth-child(4) { animation-delay: 0.20s; }
.subject-card-outer:nth-child(5) { animation-delay: 0.25s; }
.subject-card-outer:nth-child(6) { animation-delay: 0.30s; }
.subject-card-outer:nth-child(7) { animation-delay: 0.35s; }
.subject-card-outer:nth-child(8) { animation-delay: 0.40s; }
@media (prefers-reduced-motion: reduce) {
    .subject-card-outer { animation: none; opacity: 1; }
}
</style>

<div class="adm-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:0.8rem;color:var(--adm-text-muted);">
            <span style="font-weight:600;color:rgba(255,255,255,0.6);">Matières</span>
            <span>/</span>
            <span style="color:var(--adm-text-muted);">Niveaux → Classes → Cours</span>
        </div>
        <h1><i class="bi bi-book me-2" style="color:var(--adm-primary);"></i> Gestion des Matières</h1>
        <div class="subtitle">Sélectionnez une matière pour parcourir ses niveaux et classes</div>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif
@if(session('info'))
<div class="adm-alert mb-4" style="background:rgba(6,182,212,0.1);border:1px solid rgba(6,182,212,0.2);color:#67E8F9;border-radius:12px;padding:12px 16px;">
    <i class="bi bi-info-circle me-2"></i> {{ session('info') }}
</div>
@endif

<div class="row g-4">
    @php
        $subjectGradients = [
            'linear-gradient(135deg, #7C3AED, #A78BFA)',
            'linear-gradient(135deg, #059669, #34D399)',
            'linear-gradient(135deg, #D97706, #FBBF24)',
            'linear-gradient(135deg, #1E40AF, #60A5FA)',
            'linear-gradient(135deg, #D90429, #EF4444)',
            'linear-gradient(135deg, #06B6D4, #67E8F9)',
            'linear-gradient(135deg, #BE185D, #F472B6)',
            'linear-gradient(135deg, #065F46, #34D399)',
        ];
        $subjectIcons = [
            'bi-book', 'bi-calculator', 'bi-globe2', 'bi-flask',
            'bi-pencil-square', 'bi-music-note-beamed', 'bi-brush', 'bi-stars'
        ];
    @endphp

    @forelse($subjects as $subject)
        @php
            $idx = $loop->index % count($subjectGradients);
            $gradient = $subjectGradients[$idx];
            $icon = $subjectIcons[$idx];
            $levelCount = $subject->levels->count();
            $classCount = $subject->classes->count();
            $courseCount = $subject->courses_count ?? 0;
        @endphp
        <div class="col-lg-3 col-md-6 subject-card-outer">
            <a href="{{ route('admin.subjects.levels', $subject) }}" class="text-decoration-none">
                <div class="adm-card" style="cursor:pointer;height:100%;transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                    <div style="height:110px;background:{{ $gradient }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                        <div style="position:absolute;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.06);top:-50px;right:-50px;"></div>
                        <div style="position:absolute;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.04);bottom:-30px;left:-30px;"></div>
                        <i class="bi {{ $icon }}" style="font-size:2.8rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                    </div>
                    <div class="adm-card-body text-center" style="padding:1.25rem;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:0.5rem;">
                            <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;background:rgba(255,255,255,0.06);color:var(--adm-text-muted);">
                                {{ $subject->type ?? 'scolaire' }}
                            </span>
                        </div>
                        <h4 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.75rem;">{{ $subject->name }}</h4>
                        <div style="display:flex;justify-content:center;gap:16px;margin-bottom:1rem;">
                            <span style="font-size:0.8rem;color:var(--adm-text-muted);">
                                <i class="bi bi-layers me-1"></i> {{ $levelCount }} niveaux
                            </span>
                            <span style="font-size:0.8rem;color:var(--adm-text-muted);">
                                <i class="bi bi-building me-1"></i> {{ $classCount }} classes
                            </span>
                            <span style="font-size:0.8rem;color:var(--adm-text-muted);">
                                <i class="bi bi-play-circle me-1"></i> {{ $courseCount }} cours
                            </span>
                        </div>
                        <span class="adm-btn" style="background:{{ $gradient }};color:white;border:none;width:100%;">
                            <i class="bi bi-arrow-right me-1"></i> Voir les niveaux
                        </span>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="adm-card">
                <div class="adm-empty" style="padding:4rem 2rem;">
                    <div class="adm-empty-icon"><i class="bi bi-book"></i></div>
                    <h5>Aucune matière</h5>
                    <p>Les matières apparaîtront ici une fois créées dans le système.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

@endsection
