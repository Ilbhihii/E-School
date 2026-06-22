@extends('layouts.admin')

@section('title', 'Navigation par Matières')
@section('page_title', 'Matières')
@section('breadcrumb', 'Navigation pédagogique')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-book me-2" style="color:var(--adm-primary);"></i> Navigation par Matières</h1>
        <div class="subtitle">Parcourez la hiérarchie : Matière → Niveau → Classe → Cours</div>
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

<!-- ═══════════════ MATIÈRES ═══════════════ -->
<div class="d-flex align-items-center gap-3 mb-4">
    <div style="width:6px;height:28px;border-radius:4px;background:var(--adm-primary);flex-shrink:0;"></div>
    <h3 style="font-weight:700;color:rgba(255,255,255,0.9);margin:0;"><i class="bi bi-book me-2"></i> Matières</h3>
    <span style="color:var(--adm-text-muted);font-size:0.85rem;">{{ $subjects->count() }} matière(s)</span>
</div>

<div class="row g-4 mb-5">
    @forelse($subjects as $subject)
        @php
            $classCount = $subject->classes_count ?? $subject->classes->count();
            $icons = ['book','calculator','flask','translate','globe','palette','music-note-beamed','cpu','graph-up','pencil','journal','robot'];
            $icon = $icons[$loop->index % count($icons)];
            $gradients = [
                'linear-gradient(135deg, #7C3AED, #A78BFA)',
                'linear-gradient(135deg, #059669, #22C55E)',
                'linear-gradient(135deg, #D97706, #FFB347)',
                'linear-gradient(135deg, #2563EB, #60A5FA)',
                'linear-gradient(135deg, #DC2626, #EF4444)',
                'linear-gradient(135deg, #0891B2, #06B6D4)',
                'linear-gradient(135deg, #9333EA, #C084FC)',
                'linear-gradient(135deg, #16A34A, #4ADE80)',
            ];
            $gradient = $gradients[$loop->index % count($gradients)];
        @endphp
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.subjects.levels', $subject) }}" class="text-decoration-none">
                <div class="adm-card st-fade-up" style="cursor:pointer;height:100%;transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                    <div style="height:120px;background:{{ $gradient }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                        <div style="position:absolute;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.06);top:-50px;right:-50px;"></div>
                        <div style="position:absolute;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.04);bottom:-30px;left:-30px;"></div>
                        <i class="bi bi-{{ $icon }}" style="font-size:3rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                    </div>
                    <div class="adm-card-body text-center" style="padding:1.25rem;">
                        <h4 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.5rem;">{{ $subject->name }}</h4>
                        <p style="color:var(--adm-text-muted);font-size:0.85rem;margin-bottom:1rem;">
                            <i class="bi bi-building me-1"></i> {{ $classCount }} classe(s)
                        </p>
                        <span class="adm-btn" style="background:{{ $gradient }};color:white;border:none;width:100%;">
                            <i class="bi bi-layers me-1"></i> Voir les niveaux
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
                    <p>Aucune matière n'a été créée pour le moment.</p>
                </div>
            </div>
        </div>
    @endforelse
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
        $levelIcons = ['bi-book', 'bi-bookmark-star', 'bi-music-note-beamed', 'bi-stars'];
        $levelGradients = [
            'linear-gradient(135deg, #059669, #34D399)',
            'linear-gradient(135deg, #7C3AED, #A78BFA)',
            'linear-gradient(135deg, #D97706, #FBBF24)',
            'linear-gradient(135deg, #1E40AF, #60A5FA)',
        ];
    @endphp

    @forelse($levels as $level)
        @php
            $idx = $loop->index % 4;
            $icon = $levelIcons[$idx];
            $gradient = $levelGradients[$idx];
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
