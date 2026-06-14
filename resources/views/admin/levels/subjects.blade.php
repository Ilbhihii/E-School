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

@if($subjects->isEmpty())
    <div class="adm-card mb-4">
        <div class="adm-empty" style="padding:3rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-book"></i></div>
            <h5>Aucune matière liée à cette classe</h5>
            <p>Ajoutez des matières pour commencer à créer des cours.</p>
        </div>
    </div>
@else
    <div class="row g-4 mb-4">
        @foreach($subjects as $subject)
            @php
                $courseCount = $subject->course_count ?? 0;
                $colorMap = ['primary','success','danger','warning','info','purple','teal','orange'];
            @endphp
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('admin.levels.courses', [$level, $class, $subject]) }}" class="text-decoration-none">
                    <div class="adm-card st-fade-up" style="cursor:pointer;height:100%;transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                        <div class="adm-card-body text-center" style="padding:2rem 1.5rem;">
                            <div style="width:64px;height:64px;border-radius:16px;background:rgba(124,58,237,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.75rem;color:#A78BFA;">
                                <i class="bi bi-book-fill"></i>
                            </div>
                            <h5 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.5rem;">{{ $subject->name }}</h5>
                            <p style="color:var(--adm-text-muted);font-size:0.8rem;margin-bottom:1rem;">
                                <i class="bi bi-play-circle me-1"></i> {{ $courseCount }} cours
                            </p>
                            <span class="adm-btn adm-btn-accent" style="width:100%;">
                                <i class="bi bi-play-circle me-1"></i> Voir les cours
                            </span>
                        </div>
                    </div>
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
