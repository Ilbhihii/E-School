@extends('layouts.admin')

@section('title', 'Classes - ' . $level->name)
@section('page_title', $level->name)
@section('breadcrumb', 'Niveaux → Classes')

@section('content')

<div class="adm-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:0.8rem;color:var(--adm-text-muted);">
            <a href="{{ route('admin.levels.index') }}" style="color:var(--adm-text-muted);text-decoration:none;"><i class="bi bi-layers me-1"></i>Niveaux</a>
            <span>/</span>
            <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ $level->name }}</span>
        </div>
        <h1><i class="bi bi-building me-2" style="color:var(--adm-primary);"></i> Classes — {{ $level->name }}</h1>
        <div class="subtitle">Sélectionnez une classe pour voir ses matières</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.classes.create') }}?level_id={{ $level->id }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-plus-lg"></i> Nouvelle classe
        </a>
        <a href="{{ route('admin.levels.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

<!-- Classes en ligne unique -->
<style>
.class-row {
    display: flex;
    flex-wrap: nowrap;
    gap: 1.5rem;
    overflow-x: auto;
    padding: 0.5rem 0.25rem 1rem;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.08) transparent;
    -webkit-overflow-scrolling: touch;
}
.class-row::-webkit-scrollbar { height: 6px; }
.class-row::-webkit-scrollbar-track { background: transparent; }
.class-row::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 10px; }
.class-row::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.15); }

@keyframes classFadeIn {
    from { opacity: 0; transform: translateY(24px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.class-card-wrap {
    flex: 1 1 0;
    min-width: 280px;
    max-width: 440px;
    opacity: 0;
    animation: classFadeIn 0.55s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    will-change: transform, opacity;
}
.class-card-wrap:nth-child(1) { animation-delay: 0.05s; }
.class-card-wrap:nth-child(2) { animation-delay: 0.15s; }
.class-card-wrap:nth-child(3) { animation-delay: 0.25s; }
.class-card-wrap:nth-child(4) { animation-delay: 0.35s; }
.class-card-wrap:nth-child(5) { animation-delay: 0.45s; }
.class-card-wrap:nth-child(6) { animation-delay: 0.55s; }
.class-card-wrap:nth-child(7) { animation-delay: 0.65s; }
.class-card-wrap:nth-child(8) { animation-delay: 0.75s; }
@media (prefers-reduced-motion: reduce) {
    .class-card-wrap { animation: none; opacity: 1; }
}

.class-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.35rem 1.5rem;
    background: var(--adm-bg-card);
    backdrop-filter: blur(16px);
    border: 1px solid var(--adm-border-card);
    border-radius: 18px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    height: 100%;
    overflow: hidden;
}
.class-card:hover {
    transform: translateY(-4px) scale(1.02);
    border-color: var(--adm-border-card-hover);
    box-shadow: 0 20px 48px rgba(0,0,0,0.4);
}
.class-card:active {
    transform: translateY(-1px) scale(1.01);
}

.class-card-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.7rem;
    flex-shrink: 0;
    position: relative;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.class-card:hover .class-card-icon {
    transform: scale(1.08) rotate(-4deg);
}

.class-card-content {
    flex: 1;
    min-width: 0;
}
.class-card-content h4 {
    font-weight: 700;
    color: rgba(255,255,255,0.92);
    margin: 0 0 0.3rem;
    font-size: 1.15rem;
    line-height: 1.3;
}
.class-card-content .class-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--adm-text-muted);
    font-size: 0.85rem;
}
.class-card-content .class-meta i {
    font-size: 0.75rem;
}

.class-arrow {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: rgba(255,255,255,0.2);
    flex-shrink: 0;
    transition: all 0.3s ease;
}
.class-card:hover .class-arrow {
    color: rgba(255,255,255,0.6);
    transform: translateX(4px);
}
</style>

@if($classes->isEmpty())
    <div class="adm-card">
        <div class="adm-empty" style="padding:4rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-building"></i></div>
            <h5>Aucune classe dans ce niveau</h5>
            <p>Créez une classe pour ce niveau pour commencer.</p>
            <a href="{{ route('admin.classes.create') }}" class="adm-btn adm-btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Créer une classe
            </a>
        </div>
    </div>
@else
    @php
        $gradients = [
            'linear-gradient(135deg, #16A34A, #22C55E)',
            'linear-gradient(135deg, #003A8F, #2563EB)',
            'linear-gradient(135deg, #D97706, #FFB347)',
            'linear-gradient(135deg, #7C3AED, #A78BFA)',
            'linear-gradient(135deg, #D90429, #EF4444)',
            'linear-gradient(135deg, #06B6D4, #0891B2)',
        ];
        $iconColors = ['#4ADE80', '#60A5FA', '#FCD34D', '#A78BFA', '#FCA5A5', '#67E8F9'];
        $iconBgs = [
            'rgba(22,163,74,0.2)',
            'rgba(0,58,143,0.2)',
            'rgba(217,119,6,0.2)',
            'rgba(124,58,237,0.2)',
            'rgba(217,4,41,0.2)',
            'rgba(6,182,212,0.2)',
        ];
    @endphp
    <div class="class-row">
        @foreach($classes as $class)
            @php
                $subjectCount = $class->subjects->count();
                $gIdx = $loop->index % count($gradients);
            @endphp
            <div class="class-card-wrap">
                <a href="{{ route('admin.levels.subjects', [$level, $class]) }}" class="class-card">
                    <div class="class-card-icon" style="background:{{ $iconBgs[$gIdx] }};color:{{ $iconColors[$gIdx] }};">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="class-card-content">
                        <h4>{{ $class->name }}</h4>
                        <div class="class-meta">
                            <i class="bi bi-book"></i>
                            <span>{{ $subjectCount }} matière{{ $subjectCount > 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                    <div class="class-arrow">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <!-- Barre latérale colorée -->
                    <div style="position:absolute;left:0;top:0;bottom:0;width:5px;border-radius:18px 0 0 18px;background:{{ $gradients[$gIdx] }};"></div>
                </a>
            </div>
        @endforeach
    </div>
@endif

@endsection
