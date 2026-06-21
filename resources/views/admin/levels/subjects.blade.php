@extends('layouts.admin')

@section('title', 'Matières - ' . $class->name)
@section('page_title', $class->name)
@section('breadcrumb', 'Niveaux → Classes → Matières')

@section('content')

<div class="adm-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:0.8rem;color:var(--adm-text-muted);">
            <a href="{{ route('admin.levels.index') }}" style="color:var(--adm-text-muted);text-decoration:none;"><i class="bi bi-layers me-1"></i>Niveaux</a>
            <span>/</span>
            <a href="{{ route('admin.levels.classes', $level) }}" style="color:var(--adm-text-muted);text-decoration:none;">{{ $level->name }}</a>
            <span>/</span>
            <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ $class->name }}</span>
        </div>
        <h1><i class="bi bi-book me-2" style="color:var(--adm-accent);"></i> Matières — {{ $class->name }}</h1>
        <div class="subtitle">{{ $level->name }} — Sélectionnez une matière pour gérer ses cours</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.levels.classes', $level) }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour aux classes
        </a>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

<!-- Matières en ligne unique -->
<style>
.subject-row {
    display: flex;
    flex-wrap: nowrap;
    gap: 1.5rem;
    overflow-x: auto;
    padding: 0.5rem 0.25rem 1rem;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.08) transparent;
    -webkit-overflow-scrolling: touch;
}
.subject-row::-webkit-scrollbar { height: 6px; }
.subject-row::-webkit-scrollbar-track { background: transparent; }
.subject-row::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 10px; }
.subject-row::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.15); }

@keyframes subjectFadeIn {
    from { opacity: 0; transform: translateY(24px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.subject-card-wrap {
    flex: 1 1 0;
    min-width: 280px;
    max-width: 440px;
    opacity: 0;
    animation: subjectFadeIn 0.55s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    will-change: transform, opacity;
}
.subject-card-wrap:nth-child(1) { animation-delay: 0.05s; }
.subject-card-wrap:nth-child(2) { animation-delay: 0.15s; }
.subject-card-wrap:nth-child(3) { animation-delay: 0.25s; }
.subject-card-wrap:nth-child(4) { animation-delay: 0.35s; }
.subject-card-wrap:nth-child(5) { animation-delay: 0.45s; }
.subject-card-wrap:nth-child(6) { animation-delay: 0.55s; }
.subject-card-wrap:nth-child(7) { animation-delay: 0.65s; }
.subject-card-wrap:nth-child(8) { animation-delay: 0.75s; }
@media (prefers-reduced-motion: reduce) {
    .subject-card-wrap { animation: none; opacity: 1; }
}

.subject-card {
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
.subject-card:hover {
    transform: translateY(-4px) scale(1.02);
    border-color: var(--adm-border-card-hover);
    box-shadow: 0 20px 48px rgba(0,0,0,0.4);
}
.subject-card:active {
    transform: translateY(-1px) scale(1.01);
}

.subject-card-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.7rem;
    flex-shrink: 0;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.subject-card:hover .subject-card-icon {
    transform: scale(1.08) rotate(-4deg);
}

.subject-card-content {
    flex: 1;
    min-width: 0;
}
.subject-card-content h4 {
    font-weight: 700;
    color: rgba(255,255,255,0.92);
    margin: 0 0 0.3rem;
    font-size: 1.15rem;
    line-height: 1.3;
}
.subject-card-content .subject-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--adm-text-muted);
    font-size: 0.85rem;
}
.subject-card-content .subject-meta i {
    font-size: 0.75rem;
}

.subject-arrow {
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
.subject-card:hover .subject-arrow {
    color: rgba(255,255,255,0.6);
    transform: translateX(4px);
}
</style>

@if($subjects->isEmpty())
    <div class="adm-card mb-4">
        <div class="adm-empty" style="padding:3rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-book"></i></div>
            <h5>Aucune matière liée à cette classe</h5>
            <p>Ajoutez des matières pour commencer à créer des cours.</p>
        </div>
    </div>
@else
    @php
        $subjectGradients = [
            'linear-gradient(135deg, #7C3AED, #A78BFA)',
            'linear-gradient(135deg, #16A34A, #4ADE80)',
            'linear-gradient(135deg, #D97706, #FCD34D)',
            'linear-gradient(135deg, #D90429, #FCA5A5)',
            'linear-gradient(135deg, #003A8F, #60A5FA)',
            'linear-gradient(135deg, #06B6D4, #67E8F9)',
            'linear-gradient(135deg, #BE185D, #F472B6)',
            'linear-gradient(135deg, #065F46, #34D399)',
        ];
        $subjectIconColors = ['#A78BFA', '#4ADE80', '#FCD34D', '#FCA5A5', '#60A5FA', '#67E8F9', '#F472B6', '#34D399'];
        $subjectIconBgs = [
            'rgba(124,58,237,0.2)',
            'rgba(22,163,74,0.2)',
            'rgba(217,119,6,0.2)',
            'rgba(217,4,41,0.2)',
            'rgba(0,58,143,0.2)',
            'rgba(6,182,212,0.2)',
            'rgba(190,24,93,0.2)',
            'rgba(6,95,70,0.2)',
        ];
    @endphp
    <div class="subject-row" style="margin-bottom:1.5rem;">
        @foreach($subjects as $subject)
            @php
                $courseCount = $subject->course_count ?? 0;
                $sIdx = $loop->index % count($subjectGradients);
            @endphp
            <div class="subject-card-wrap">
                <a href="{{ route('admin.levels.courses', [$level, $class, $subject]) }}" class="subject-card">
                    <div class="subject-card-icon" style="background:{{ $subjectIconBgs[$sIdx] }};color:{{ $subjectIconColors[$sIdx] }};">
                        <i class="bi bi-book-fill"></i>
                    </div>
                    <div class="subject-card-content">
                        <h4>{{ $subject->name }}</h4>
                        <div class="subject-meta">
                            <i class="bi bi-play-circle"></i>
                            <span>{{ $courseCount }} cours</span>
                        </div>
                    </div>
                    <div class="subject-arrow">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <!-- Barre latérale colorée -->
                    <div style="position:absolute;left:0;top:0;bottom:0;width:5px;border-radius:18px 0 0 18px;background:{{ $subjectGradients[$sIdx] }};"></div>
                </a>
            </div>
        @endforeach
    </div>
@endif

<!-- Link Subjects to Class -->
<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-link-45deg" style="color:rgba(255,255,255,0.35);"></i> Gérer les matières liées</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $subjects->count() }} matière(s)</span>
        </div>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="{{ route('admin.levels.subjects.attach', [$level, $class]) }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div class="adm-form-group" style="flex:1;min-width:200px;margin-bottom:0;">
                <label class="adm-form-label">Lier une matière à {{ $class->name }}</label>
                <select name="subject_id" class="adm-form-select" required>
                    <option value="">Choisir une matière...</option>
                    @foreach($allSubjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="adm-btn adm-btn-primary">
                <i class="bi bi-link me-1"></i> Lier
            </button>
        </form>

        @if($subjects->isNotEmpty())
        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.06);">
            <label class="adm-form-label">Matières liées</label>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($subjects as $subject)
                <div style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(124,58,237,0.1);border-radius:8px;">
                    <span style="font-size:0.85rem;">{{ $subject->name }}</span>
                    <form method="POST" action="{{ route('admin.levels.subjects.detach', [$level, $class, $subject]) }}" style="display:inline;" onsubmit="return confirm('Retirer {{ $subject->name }} de {{ $class->name }} ?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:#FCA5A5;cursor:pointer;padding:2px;font-size:0.85rem;">&times;</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
