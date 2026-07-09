@extends('layouts.admin')

@section('title', 'Navigation par Matières')
@section('page_title', 'Matières')
@section('breadcrumb', 'Navigation pédagogique')

@section('content')

<style>
/* ===== TREE STYLES ===== */
.tree-container {
    margin-bottom: 2rem;
}

/* Subject card */
.tree-subject {
    position: relative;
    background: var(--adm-bg-card);
    backdrop-filter: blur(16px);
    border: 1px solid var(--adm-border-card);
    border-radius: 18px;
    margin-bottom: 0.75rem;
    overflow: hidden;
    transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.tree-subject:hover {
    border-color: var(--adm-border-card-hover);
    box-shadow: 0 12px 32px rgba(0,0,0,0.25);
}
.tree-subject-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    cursor: pointer;
    user-select: none;
    position: relative;
    z-index: 1;
}
.tree-subject-header:active {
    transform: scale(0.995);
}
.tree-subject-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.tree-subject:hover .tree-subject-icon {
    transform: scale(1.06) rotate(-4deg);
}
.tree-subject-info {
    flex: 1;
    min-width: 0;
}
.tree-subject-info h3 {
    font-weight: 700;
    color: rgba(255,255,255,0.92);
    margin: 0 0 0.15rem;
    font-size: 1.1rem;
}
.tree-subject-info .tree-meta {
    color: var(--adm-text-muted);
    font-size: 0.82rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.tree-subject-info .tree-meta i {
    font-size: 0.75rem;
}
.tree-expand-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.25);
    flex-shrink: 0;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.tree-subject-header.expanded .tree-expand-icon {
    transform: rotate(90deg);
    color: rgba(255,255,255,0.6);
}
.tree-subject:hover .tree-expand-icon {
    color: rgba(255,255,255,0.5);
}
.tree-subject-color-bar {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 5px;
    border-radius: 18px 0 0 18px;
}

/* Subject body (children container) */
.tree-subject-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.55s cubic-bezier(0.4, 0, 0.2, 1);
}
.tree-subject-body.open {
    max-height: none; /* will be set by JS */
}
.tree-subject-body-inner {
    padding: 0 1.5rem 1.5rem 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.04);
}

/* Level items inside subject */
.tree-level {
    margin-top: 0.75rem;
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.02);
    overflow: hidden;
    transition: all 0.3s ease;
}
.tree-level:hover {
    border-color: rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.035);
}
.tree-level-header {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.9rem 1.25rem;
    cursor: pointer;
    user-select: none;
}
.tree-level-header:active {
    transform: scale(0.995);
}
.tree-level-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
}
.tree-level:hover .tree-level-icon {
    transform: scale(1.06) rotate(-4deg);
}
.tree-level-info {
    flex: 1;
    min-width: 0;
}
.tree-level-info h4 {
    font-weight: 600;
    color: rgba(255,255,255,0.88);
    margin: 0 0 0.1rem;
    font-size: 0.95rem;
}
.tree-level-info .tree-meta {
    color: var(--adm-text-muted);
    font-size: 0.78rem;
}
.tree-level-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.tree-level-body.open {
    max-height: none;
}
.tree-level-body-inner {
    padding: 0 1.25rem 1rem 1.25rem;
    border-top: 1px solid rgba(255,255,255,0.04);
    margin-top: 0;
}

/* Class items inside level */
.tree-class {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.75rem 1rem;
    margin-top: 0.5rem;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.04);
    background: rgba(255,255,255,0.015);
    cursor: pointer;
    user-select: none;
    transition: all 0.3s ease;
}
.tree-class:hover {
    border-color: rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.03);
}
.tree-class:active {
    transform: scale(0.995);
}
.tree-class-icon {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}
.tree-class-info {
    flex: 1;
    min-width: 0;
}
.tree-class-info h5 {
    font-weight: 600;
    color: rgba(255,255,255,0.85);
    margin: 0 0 0.1rem;
    font-size: 0.88rem;
}
.tree-class-info .tree-meta {
    color: var(--adm-text-muted);
    font-size: 0.75rem;
}
.tree-class-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.45s cubic-bezier(0.4, 0, 0.2, 1);
}
.tree-class-body.open {
    max-height: none;
}
.tree-class-body-inner {
    padding: 0.5rem 1rem 0.75rem 1rem;
    border-top: 1px solid rgba(255,255,255,0.04);
    margin-top: 0.5rem;
}

/* Course items inside class */
.tree-course {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.55rem 0.75rem;
    margin-top: 0.35rem;
    border-radius: 8px;
    background: rgba(99,102,241,0.06);
    border-left: 3px solid rgba(99,102,241,0.3);
    transition: all 0.25s ease;
}
.tree-course:hover {
    background: rgba(99,102,241,0.10);
    border-left-color: rgba(99,102,241,0.6);
}
.tree-course i {
    color: rgba(99,102,241,0.4);
    font-size: 0.8rem;
    flex-shrink: 0;
}
.tree-course span {
    font-size: 0.84rem;
    color: rgba(255,255,255,0.75);
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.tree-course .course-order {
    font-size: 0.7rem;
    color: rgba(255,255,255,0.25);
    flex-shrink: 0;
}

/* Empty state */
.tree-empty {
    text-align: center;
    padding: 1.5rem;
    color: var(--adm-text-muted);
    font-size: 0.85rem;
}
.tree-empty i {
    font-size: 1.5rem;
    opacity: 0.3;
    margin-bottom: 0.5rem;
    display: block;
}

/* Level badges inside each class */
.tree-subject-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 10px;
    border-radius: 6px;
    font-size: 0.72rem;
    background: rgba(124,58,237,0.12);
    color: #A78BFA;
}

/* Summary bar */
.tree-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-bottom: 1.75rem;
    padding: 1rem 1.5rem;
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border-card);
    border-radius: 16px;
}
.tree-summary-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.85rem;
    color: var(--adm-text-muted);
}
.tree-summary-item strong {
    color: rgba(255,255,255,0.85);
    font-size: 1.1rem;
}
.tree-summary-item i {
    font-size: 1.1rem;
}

/* Animation for tree items appearing */
@keyframes treeItemIn {
    from { opacity: 0; transform: translateY(12px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.tree-anim-in {
    animation: treeItemIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}
@media (prefers-reduced-motion: reduce) {
    .tree-subject-body, .tree-level-body, .tree-class-body,
    .tree-expand-icon, .tree-anim-in { transition: none; animation: none; }
}
</style>

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-diagram-3 me-2" style="color:var(--adm-primary);"></i> Hiérarchie Pédagogique</h1>
        <div class="subtitle">Arbre interactif : Matière → Niveau → Classe → Cours</div>
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

<!-- ═══════════════ SUMMARY BAR ═══════════════ -->
@php
    $totalSubjects = 0;
    $totalLevels = 0;
    $totalClasses = 0;
    $totalCourses = 0;
    foreach ($tree as $t) {
        $totalSubjects++;
        foreach ($t['levels'] as $tl) {
            $totalLevels++;
            foreach ($tl['classes'] as $tc) {
                $totalClasses++;
                $totalCourses += $tc['courses']->count();
            }
        }
    }
@endphp

<div class="tree-summary">
    <div class="tree-summary-item">
        <i class="bi bi-book" style="color:var(--adm-primary);"></i>
        <strong>{{ $totalSubjects }}</strong> matière(s)
    </div>
    <div class="tree-summary-item">
        <i class="bi bi-layers" style="color:#34D399;"></i>
        <strong>{{ $totalLevels }}</strong> niveau(x)
    </div>
    <div class="tree-summary-item">
        <i class="bi bi-building" style="color:#60A5FA;"></i>
        <strong>{{ $totalClasses }}</strong> classe(s)
    </div>
    <div class="tree-summary-item">
        <i class="bi bi-play-circle" style="color:#F472B6;"></i>
        <strong>{{ $totalCourses }}</strong> cours
    </div>
</div>

<!-- ═══════════════ TREE VIEW ═══════════════ -->
<div class="tree-container">
    @if(empty($tree) || collect($tree)->every(fn($t) => empty($t['levels'])))
        <div class="adm-card">
            <div class="adm-empty" style="padding:4rem 2rem;">
                <div class="adm-empty-icon"><i class="bi bi-diagram-3"></i></div>
                <h5>Aucune donnée hiérarchique</h5>
                <p>Créez des matières, niveaux et classes puis liez-les pour voir l'arbre apparaître.</p>
            </div>
        </div>
    @else
        @php
            $subjectGradients = [
                'linear-gradient(135deg, #7C3AED, #A78BFA)',
                'linear-gradient(135deg, #059669, #34D399)',
                'linear-gradient(135deg, #D97706, #FBBF24)',
                'linear-gradient(135deg, #2563EB, #60A5FA)',
                'linear-gradient(135deg, #DC2626, #EF4444)',
                'linear-gradient(135deg, #0891B2, #06B6D4)',
                'linear-gradient(135deg, #9333EA, #C084FC)',
                'linear-gradient(135deg, #16A34A, #4ADE80)',
            ];
            $subjectIconColors = ['#A78BFA','#34D399','#FBBF24','#60A5FA','#EF4444','#06B6D4','#C084FC','#4ADE80'];
            $subjectIconBgs = [
                'rgba(124,58,237,0.2)',
                'rgba(5,150,105,0.2)',
                'rgba(217,119,6,0.2)',
                'rgba(37,99,235,0.2)',
                'rgba(220,38,38,0.2)',
                'rgba(8,145,178,0.2)',
                'rgba(147,51,234,0.2)',
                'rgba(22,163,74,0.2)',
            ];
            $levelGradients = [
                'linear-gradient(135deg, #059669, #34D399)',
                'linear-gradient(135deg, #7C3AED, #A78BFA)',
                'linear-gradient(135deg, #D97706, #FBBF24)',
                'linear-gradient(135deg, #1E40AF, #60A5FA)',
            ];
            $levelIconColors = ['#34D399','#A78BFA','#FBBF24','#60A5FA'];
            $levelIconBgs = [
                'rgba(5,150,105,0.15)',
                'rgba(124,58,237,0.15)',
                'rgba(217,119,6,0.15)',
                'rgba(30,64,175,0.15)',
            ];
            $classGradients = [
                'linear-gradient(135deg, #16A34A, #22C55E)',
                'linear-gradient(135deg, #003A8F, #2563EB)',
                'linear-gradient(135deg, #D97706, #FFB347)',
                'linear-gradient(135deg, #7C3AED, #A78BFA)',
                'linear-gradient(135deg, #D90429, #EF4444)',
                'linear-gradient(135deg, #06B6D4, #0891B2)',
            ];
            $classIconColors = ['#4ADE80','#60A5FA','#FCD34D','#A78BFA','#FCA5A5','#67E8F9'];
            $classIconBgs = [
                'rgba(22,163,74,0.15)',
                'rgba(0,58,143,0.15)',
                'rgba(217,119,6,0.15)',
                'rgba(124,58,237,0.15)',
                'rgba(217,4,41,0.15)',
                'rgba(6,182,212,0.15)',
            ];
            $subjectIcons = ['bi-book','bi-calculator','bi-flask','bi-translate','bi-globe','bi-palette','bi-music-note-beamed','bi-cpu','bi-graph-up','bi-pencil','bi-journal','bi-robot'];
            $levelIcons = ['bi-layers','bi-bookmark-star','bi-stars','bi-mortarboard'];
            $classIcons = ['bi-mortarboard-fill','bi-easel','bi-people-fill','bi-person-workspace','bi-backpack','bi-house-door'];
        @endphp

        @foreach($tree as $treeIdx => $treeItem)
            @php
                $subject = $treeItem['subject'];
                $sIdx = $treeIdx % count($subjectGradients);
                $sIcon = $subjectIcons[$treeIdx % count($subjectIcons)];
                $sLevels = $treeItem['levels'];
                $sLevelCount = count($sLevels);
            @endphp
            <div class="tree-subject tree-anim-in" style="animation-delay:{{ $treeIdx * 0.06 }}s;">
                <div class="tree-subject-color-bar" style="background:{{ $subjectGradients[$sIdx] }};"></div>

                <!-- Subject Header -->
                <div class="tree-subject-header" onclick="toggleTree(this, 'subject')">
                    <div class="tree-subject-icon" style="background:{{ $subjectIconBgs[$sIdx] }};color:{{ $subjectIconColors[$sIdx] }};">
                        <i class="bi {{ $sIcon }}"></i>
                    </div>
                    <div class="tree-subject-info">
                        <h3>{{ $subject->name }}</h3>
                        <div class="tree-meta">
                            <i class="bi bi-layers"></i>
                            <span>{{ $sLevelCount }} niveau{{ $sLevelCount > 1 ? 'x' : '' }}</span>
                            @if($sLevelCount > 0)
                                @php
                                    $classCnt = collect($sLevels)->sum(fn($sl) => count($sl['classes']));
                                @endphp
                                <span style="opacity:0.4;">·</span>
                                <i class="bi bi-building"></i>
                                <span>{{ $classCnt }} classe{{ $classCnt > 1 ? 's' : '' }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="tree-expand-icon">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </div>

                <!-- Subject Body (Levels) -->
                <div class="tree-subject-body">
                    <div class="tree-subject-body-inner">
                        @foreach($sLevels as $lIdx => $levelItem)
                            @php
                                $level = $levelItem['level'];
                                $lGIdx = $lIdx % count($levelGradients);
                                $lIcon = $levelIcons[$lIdx % count($levelIcons)];
                                $lClasses = $levelItem['classes'];
                                $lClassCount = count($lClasses);
                            @endphp
                            <div class="tree-level tree-anim-in" style="animation-delay:{{ $lIdx * 0.04 }}s;">
                                <!-- Level Header -->
                                <div class="tree-level-header" onclick="toggleTree(this, 'level')">
                                    <div class="tree-level-icon" style="background:{{ $levelIconBgs[$lGIdx] }};color:{{ $levelIconColors[$lGIdx] }};">
                                        <i class="bi {{ $lIcon }}"></i>
                                    </div>
                                    <div class="tree-level-info">
                                        <h4>{{ $level->name }}</h4>
                                        <div class="tree-meta">
                                            <i class="bi bi-building me-1"></i>
                                            <span>{{ $lClassCount }} classe{{ $lClassCount > 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="tree-expand-icon" style="font-size:0.8rem;width:28px;height:28px;">
                                        <i class="bi bi-chevron-right"></i>
                                    </div>
                                </div>

                                <!-- Level Body (Classes) -->
                                <div class="tree-level-body">
                                    <div class="tree-level-body-inner">
                                        @foreach($lClasses as $cIdx => $classItem)
                                            @php
                                                $class = $classItem['class'];
                                                $courses = $classItem['courses'];
                                                $courseCount = $courses->count();
                                                $cgIdx = $cIdx % count($classGradients);
                                                $cIcon = $classIcons[$cIdx % count($classIcons)];
                                            @endphp
                                            <div class="tree-class tree-anim-in" style="animation-delay:{{ $cIdx * 0.03 }}s;">
                                                <div class="tree-class-icon" style="background:{{ $classIconBgs[$cgIdx] }};color:{{ $classIconColors[$cgIdx] }};">
                                                    <i class="bi {{ $cIcon }}"></i>
                                                </div>
                                                <div class="tree-class-info" onclick="toggleTree(this.closest('.tree-class'), 'class')" style="cursor:pointer;flex:1;">
                                                    <h5>{{ $class->name }}</h5>
                                                    <div class="tree-meta">
                                                        <i class="bi bi-play-circle me-1"></i>
                                                        <span>{{ $courseCount }} cours</span>
                                                    </div>
                                                </div>
                                                <div class="tree-expand-icon" style="font-size:0.75rem;width:26px;height:26px;cursor:pointer;" onclick="toggleTree(this.closest('.tree-class'), 'class')">
                                                    <i class="bi bi-chevron-right"></i>
                                                </div>
                                            </div>

                                            <!-- Class Body (Courses) -->
                                            <div class="tree-class-body">
                                                <div class="tree-class-body-inner">
                                                    @if($courses->isNotEmpty())
                                                        @foreach($courses as $course)
                                                            <a href="{{ route('admin.courses.edit', $course->id) }}" class="text-decoration-none">
                                                                <div class="tree-course">
                                                                    <i class="bi bi-play-circle-fill"></i>
                                                                    <span>{{ $course->title }}</span>
                                                                    @if($course->order)
                                                                        <span class="course-order">#{{ $course->order }}</span>
                                                                    @endif
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    @else
                                                        <div class="tree-empty">
                                                            <i class="bi bi-play-circle"></i>
                                                            Aucun cours pour cette matière dans cette classe
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<!-- ═══════════════ GESTION DES NIVEAUX ═══════════════ -->
<hr style="border-color:rgba(255,255,255,0.06);margin:2rem 0;">

<div class="d-flex align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div style="width:6px;height:28px;border-radius:4px;background:var(--adm-warning);flex-shrink:0;"></div>
        <h3 style="font-weight:700;color:rgba(255,255,255,0.9);margin:0;"><i class="bi bi-layers me-2"></i> Gestion des Niveaux</h3>
        <span style="color:var(--adm-text-muted);font-size:0.85rem;">{{ $levels->count() }} niveau(x)</span>
    </div>
    <button class="adm-btn adm-btn-primary" data-adm-modal="addLevelModal">
        <i class="bi bi-plus-lg"></i> Nouveau niveau
    </button>
</div>

<div class="row g-4">
    @php
        $levelMgrIcons = ['bi-book', 'bi-bookmark-star', 'bi-music-note-beamed', 'bi-stars'];
        $levelMgrGradients = [
            'linear-gradient(135deg, #059669, #34D399)',
            'linear-gradient(135deg, #7C3AED, #A78BFA)',
            'linear-gradient(135deg, #D97706, #FBBF24)',
            'linear-gradient(135deg, #1E40AF, #60A5FA)',
        ];
    @endphp

    @forelse($levels as $level)
        @php
            $idx = $loop->index % 4;
            $icon = $levelMgrIcons[$idx];
            $gradient = $levelMgrGradients[$idx];
            $classCount = $level->classes->count();
        @endphp
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.levels.classes', $level) }}" class="text-decoration-none">
                <div class="adm-card" style="cursor:pointer;height:100%;transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                    <div style="height:100px;background:{{ $gradient }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                        <div style="position:absolute;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06);top:-40px;right:-40px;"></div>
                        <i class="bi {{ $icon }}" style="font-size:2.5rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                    </div>
                    <div class="adm-card-body text-center" style="padding:1.25rem;">
                        <h4 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.5rem;">{{ $level->name }}</h4>
                        @if($level->description)
                            <p style="color:var(--adm-text-muted);font-size:0.8rem;margin-bottom:0.5rem;">{{ $level->description }}</p>
                        @endif
                        <p style="color:var(--adm-text-muted);font-size:0.85rem;margin-bottom:1rem;">
                            <i class="bi bi-building me-1"></i> {{ $classCount }} classe(s)
                        </p>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <span class="adm-btn" style="background:{{ $gradient }};color:white;border:none;flex:1;">
                                <i class="bi bi-arrow-right me-1"></i> Voir
                            </span>
                            <button onclick="event.preventDefault();event.stopPropagation();document.getElementById('editLevelModal{{ $level->id }}').style.display='flex'" class="adm-btn adm-btn-warning adm-btn-sm" style="padding:8px 12px;" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.levels.destroy', $level->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer le niveau {{ addslashes($level->name) }} ?')" onclick="event.stopPropagation();">
                                @csrf @method('DELETE')
                                <button class="adm-btn adm-btn-danger adm-btn-sm" style="padding:8px 12px;" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="adm-card">
                <div class="adm-empty" style="padding:4rem 2rem;">
                    <div class="adm-empty-icon"><i class="bi bi-layers"></i></div>
                    <h5>Aucun niveau</h5>
                    <p>Ajoutez des niveaux pour organiser la hiérarchie pédagogique.</p>
                    <button class="adm-btn adm-btn-primary" data-adm-modal="addLevelModal">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter un niveau
                    </button>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- ═══════════════ ADD LEVEL MODAL ═══════════════ -->
<div class="adm-modal-overlay" id="addLevelModal" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
    <div class="adm-modal">
        <form method="POST" action="{{ route('admin.levels.store') }}">
            @csrf
            <div class="adm-modal-header">
                <h5><i class="bi bi-plus-circle me-2"></i> Ajouter un niveau</h5>
                <button type="button" class="adm-modal-close" onclick="document.getElementById('addLevelModal').style.display='none'">&times;</button>
            </div>
            <div class="adm-modal-body">
                <div class="adm-form-group">
                    <label class="adm-form-label">Nom du niveau <span style="color:var(--adm-danger);">*</span></label>
                    <input type="text" name="name" class="adm-form-control" placeholder="Ex: N5 Le Hadith" required>
                </div>
                <div class="adm-form-group">
                    <label class="adm-form-label">Description (optionnelle)</label>
                    <input type="text" name="description" class="adm-form-control" placeholder="Ex: Étude des hadiths prophétiques">
                </div>
            </div>
            <div class="adm-modal-footer">
                <button type="button" class="adm-btn adm-btn-ghost" onclick="document.getElementById('addLevelModal').style.display='none'">Annuler</button>
                <button type="submit" class="adm-btn adm-btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════ EDIT LEVEL MODALS ═══════════════ -->
@foreach($levels as $level)
<div class="adm-modal-overlay" id="editLevelModal{{ $level->id }}" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
    <div class="adm-modal">
        <form method="POST" action="{{ route('admin.levels.update', $level->id) }}">
            @csrf @method('PUT')
            <div class="adm-modal-header">
                <h5><i class="bi bi-pencil me-2"></i> Modifier le niveau</h5>
                <button type="button" class="adm-modal-close" onclick="document.getElementById('editLevelModal{{ $level->id }}').style.display='none'">&times;</button>
            </div>
            <div class="adm-modal-body">
                <div class="adm-form-group">
                    <label class="adm-form-label">Nom du niveau <span style="color:var(--adm-danger);">*</span></label>
                    <input type="text" name="name" class="adm-form-control" value="{{ $level->name }}" placeholder="Nom du niveau" required>
                </div>
                <div class="adm-form-group">
                    <label class="adm-form-label">Description (optionnelle)</label>
                    <input type="text" name="description" class="adm-form-control" value="{{ $level->description }}" placeholder="Description du niveau">
                </div>
            </div>
            <div class="adm-modal-footer">
                <button type="button" class="adm-btn adm-btn-ghost" onclick="document.getElementById('editLevelModal{{ $level->id }}').style.display='none'">Annuler</button>
                <button type="submit" class="adm-btn adm-btn-primary">Sauvegarder</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- ═══════════════ JAVASCRIPT ═══════════════ -->
<script>
// Tree toggle function with smooth height animation
function toggleTree(header, level) {
    let container;
    if (level === 'subject') {
        container = header.closest('.tree-subject');
    } else if (level === 'level') {
        container = header.closest('.tree-level');
    } else {
        container = header.closest('.tree-class');
    }
    if (!container) return;

    const body = container.querySelector('.tree-subject-body, .tree-level-body, .tree-class-body');
    const headerEl = container.querySelector('.tree-subject-header, .tree-level-header') || container;
    if (!body) return;

    const isOpen = body.classList.contains('open');

    if (isOpen) {
        // Close
        body.style.maxHeight = body.scrollHeight + 'px';
        requestAnimationFrame(() => {
            body.classList.remove('open');
            body.style.maxHeight = '0';
        });
        if (headerEl) headerEl.classList.remove('expanded');

        // Update ancestor containers after closing (scrollHeight decreased)
        updateAncestorMaxHeights(container);
    } else {
        // Open
        body.classList.add('open');
        body.style.maxHeight = body.scrollHeight + 'px';
        if (headerEl) headerEl.classList.add('expanded');

        // Update ancestor containers after opening (scrollHeight increased)
        updateAncestorMaxHeights(container);

        // Animate children appearance
        const children = body.querySelectorAll('.tree-anim-in');
        children.forEach(child => {
            child.style.animation = 'none';
            child.offsetHeight; // reflow
            child.style.animation = '';
        });
    }
}

// Update max-height of all open ancestor containers
function updateAncestorMaxHeights(container) {
    let ancestor = container.parentElement.closest('.tree-subject-body.open, .tree-level-body.open, .tree-class-body.open');
    while (ancestor) {
        ancestor.style.maxHeight = ancestor.scrollHeight + 'px';
        ancestor = ancestor.parentElement.closest('.tree-subject-body.open, .tree-level-body.open, .tree-class-body.open');
    }
}

// Modal triggers
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-adm-modal]').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = document.getElementById(this.getAttribute('data-adm-modal'));
            if (target) target.style.display = 'flex';
        });
    });

    // Auto-expand first subject on load
    const firstSubject = document.querySelector('.tree-subject');
    if (firstSubject) {
        const header = firstSubject.querySelector('.tree-subject-header');
        if (header) {
            setTimeout(() => {
                toggleTree(header, 'subject');
            }, 300);
        }
    }

    // Handle dynamic height on window resize for open items
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            document.querySelectorAll('.tree-subject-body.open, .tree-level-body.open, .tree-class-body.open').forEach(el => {
                el.style.maxHeight = el.scrollHeight + 'px';
            });
        }, 200);
    });
});
</script>

@endsection
