@extends('layouts.admin')

@section('title', 'Niveaux - ' . $subject->name)
@section('page_title', $subject->name)
@section('breadcrumb', 'Matières → Niveaux')

@section('content')

<div class="adm-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:0.8rem;color:var(--adm-text-muted);">
            <a href="{{ route('admin.subjects.index') }}" style="color:var(--adm-text-muted);text-decoration:none;"><i class="bi bi-book me-1"></i>Matières</a>
            <span>/</span>
            <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ $subject->name }}</span>
        </div>
        <h1><i class="bi bi-layers me-2" style="color:var(--adm-primary);"></i> Niveaux — {{ $subject->name }}</h1>
        <div class="subtitle">Gérez les niveaux et leurs classes pour cette matière</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.subjects.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour aux matières
        </a>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

@if($levels->isEmpty())
    <div class="adm-card">
        <div class="adm-empty" style="padding:4rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-layers"></i></div>
            <h5>Aucun niveau pour cette matière</h5>
            <p>Cette matière n'est pas encore liée à des classes.</p>
        </div>
    </div>
@else
    @php
        $levelGradients = [
            'linear-gradient(135deg, #059669, #34D399)',
            'linear-gradient(135deg, #7C3AED, #A78BFA)',
            'linear-gradient(135deg, #D97706, #FBBF24)',
            'linear-gradient(135deg, #1E40AF, #60A5FA)',
        ];
        $classGradients = [
            'linear-gradient(135deg, #16A34A, #22C55E)',
            'linear-gradient(135deg, #003A8F, #2563EB)',
            'linear-gradient(135deg, #D97706, #FFB347)',
            'linear-gradient(135deg, #7C3AED, #A78BFA)',
            'linear-gradient(135deg, #D90429, #EF4444)',
            'linear-gradient(135deg, #06B6D4, #0891B2)',
        ];
    @endphp

    @foreach($levels as $level)
        @php
            $idx = $loop->index % 4;
            $gradient = $levelGradients[$idx];
            $classes = $level->classes;
        @endphp
        <div class="adm-card mb-4">
            <div class="adm-card-header" style="background:{{ $gradient }};border-radius:18px 18px 0 0;">
                <h4 style="color:white;margin:0;">
                    <i class="bi bi-layers me-2"></i> {{ $level->name }}
                    <span style="font-size:0.85rem;opacity:0.7;margin-left:8px;">— {{ $classes->count() }} classe(s)</span>
                </h4>
                <div class="card-actions">
                    <button class="adm-btn" style="background:rgba(255,255,255,0.15);color:white;border:none;" onclick="document.getElementById('addClassModal{{ $level->id }}').style.display='flex'">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter une classe
                    </button>
                </div>
            </div>
            <div class="adm-card-body">
                @if($classes->isNotEmpty())
                    <div style="display:flex;flex-wrap:wrap;gap:12px;">
                        @foreach($classes as $class)
                            @php
                                $gIdx = $loop->index % count($classGradients);
                                $courseCount = $class->courses()->where('subject_id', $subject->id)->count();
                            @endphp
                            <div style="flex:1;min-width:250px;max-width:350px;">
                                <a href="{{ route('admin.levels.courses', [$level, $class, $subject]) }}" class="text-decoration-none">
                                    <div class="adm-card" style="cursor:pointer;transition:all 0.3s ease;height:100%;">
                                        <div class="adm-card-body" style="padding:1rem;display:flex;align-items:center;gap:12px;">
                                            <div style="width:48px;height:48px;border-radius:12px;background:{{ $classGradients[$gIdx] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <i class="bi bi-mortarboard-fill" style="font-size:1.3rem;color:rgba(255,255,255,0.4);"></i>
                                            </div>
                                            <div style="flex:1;min-width:0;">
                                                <h5 style="font-weight:700;color:rgba(255,255,255,0.9);margin:0 0 2px;font-size:0.95rem;">{{ $class->name }}</h5>
                                                <small style="color:var(--adm-text-muted);">
                                                    <i class="bi bi-play-circle me-1"></i> {{ $courseCount }} cours
                                                </small>
                                            </div>
                                            <i class="bi bi-chevron-right" style="color:rgba(255,255,255,0.2);flex-shrink:0;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="adm-empty" style="padding:2rem;">
                        <div class="adm-empty-icon" style="font-size:2rem;"><i class="bi bi-building"></i></div>
                        <h5 style="font-size:1rem;">Aucune classe</h5>
                        <p style="font-size:0.85rem;">Ajoutez une classe à ce niveau pour {{ $subject->name }}.</p>
                        <button class="adm-btn adm-btn-primary adm-btn-sm" onclick="document.getElementById('addClassModal{{ $level->id }}').style.display='flex'">
                            <i class="bi bi-plus-lg me-1"></i> Ajouter une classe
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- ═══ ADD CLASS MODAL for {{ $level->name }} ═══ -->
        <div class="adm-modal-overlay" id="addClassModal{{ $level->id }}" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
            <div class="adm-modal">
                <form method="POST" action="{{ route('admin.classes.store') }}">
                    @csrf
                    <input type="hidden" name="level_id" value="{{ $level->id }}">
                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                    <div class="adm-modal-header">
                        <h5><i class="bi bi-plus-circle me-2"></i> Ajouter une classe à {{ $level->name }}</h5>
                        <button type="button" class="adm-modal-close" onclick="document.getElementById('addClassModal{{ $level->id }}').style.display='none'">&times;</button>
                    </div>
                    <div class="adm-modal-body">
                        <div class="adm-form-group">
                            <label class="adm-form-label">Nom de la classe <span style="color:var(--adm-danger);">*</span></label>
                            <input type="text" name="name" class="adm-form-control" placeholder="Ex: Groupe A, Classe 1..." required>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Niveau</label>
                            <input type="text" class="adm-form-control" value="{{ $level->name }}" disabled>
                        </div>
                    </div>
                    <div class="adm-modal-footer">
                        <button type="button" class="adm-btn adm-btn-ghost" onclick="document.getElementById('addClassModal{{ $level->id }}').style.display='none'">Annuler</button>
                        <button type="submit" class="adm-btn adm-btn-primary">Créer la classe</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif

<!-- ═══════════════ MODAL SCRIPT ═══════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-adm-modal]').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = document.getElementById(this.getAttribute('data-adm-modal'));
            if (target) target.style.display = 'flex';
        });
    });
});
</script>

@endsection
