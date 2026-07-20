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
    flex: 0 1 280px;
    width: calc(50% - 8px);
    min-width: 0;
    max-width: 280px;
}
.subject-cards-grid {
    display: flex;
    flex-wrap: nowrap;
    justify-content: center;
    align-items: stretch;
    gap: 16px;
    width: 100%;
}
.subject-card-outer > a,
.subject-card-outer .adm-card { display: block; height: 100%; }
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
@media (max-width: 480px) {
    .subject-cards-grid { gap: 8px; }
    .subject-card-outer { width: calc(50% - 4px); }
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

<div class="subject-cards-grid">
    @forelse($subjects as $subject)
        @php
            $design = match (mb_strtolower($subject->name)) {
                'arabe' => ['icon' => 'bi-translate', 'gradient' => 'linear-gradient(135deg,#2563EB,#06B6D4)'],
                'coran' => ['icon' => 'bi-book-half', 'gradient' => 'linear-gradient(135deg,#7C3AED,#A855F7)'],
                default => ['icon' => 'bi-journal-bookmark-fill', 'gradient' => 'linear-gradient(135deg,#4F46E5,#7C3AED)'],
            };
            $gradient = $design['gradient'];
            $icon = $design['icon'];
            $levelCount = match (mb_strtolower($subject->name)) {
                'arabe' => 4,
                'coran' => 2,
                default => $subject->levels->count(),
            };
            $classCount = $subject->classes->count();
        @endphp
        <div class="subject-card-outer">
            <a href="{{ route('admin.subjects.levels', $subject) }}" class="text-decoration-none">
                <div class="adm-card" style="cursor:pointer;height:100%;transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                    <div style="height:76px;background:{{ $gradient }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                        <div style="position:absolute;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.07);top:-40px;right:-35px;"></div>
                        <div style="position:absolute;width:65px;height:65px;border-radius:50%;background:rgba(255,255,255,0.05);bottom:-28px;left:-22px;"></div>
                        <i class="bi {{ $icon }}" style="font-size:2rem;color:rgba(255,255,255,0.42);position:relative;z-index:1;"></i>
                    </div>
                    <div class="adm-card-body text-center" style="padding:0.9rem;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:0.35rem;">
                            <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;background:rgba(255,255,255,0.06);color:var(--adm-text-muted);">
                                {{ $subject->type ?? 'scolaire' }}
                            </span>
                        </div>
                        <h4 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.55rem;font-size:1rem;">{{ $subject->name }}</h4>
                        <div style="display:flex;justify-content:center;gap:10px;margin-bottom:0.75rem;flex-wrap:wrap;">
                            <span style="font-size:0.7rem;color:var(--adm-text-muted);">
                                <i class="bi bi-layers me-1"></i> {{ $levelCount }} niveaux
                            </span>
                            <span style="font-size:0.7rem;color:var(--adm-text-muted);">
                                <i class="bi bi-building me-1"></i> {{ $classCount }} classes
                            </span>
                        </div>
                        <span class="adm-btn adm-btn-sm" style="background:{{ $gradient }};color:white;border:none;width:100%;">
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
