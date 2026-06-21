@extends('layouts.admin')

@section('title', 'Navigation par Niveaux')
@section('page_title', 'Niveaux')
@section('breadcrumb', 'Navigation pédagogique')

@section('content')

<style>
@keyframes levelFadeIn {
    from {
        opacity: 0;
        transform: translateY(24px) scale(0.96);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.level-card-outer {
    opacity: 0;
    animation: levelFadeIn 0.55s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    will-change: transform, opacity;
}

.level-card-outer:nth-child(1) { animation-delay: 0.05s; }
.level-card-outer:nth-child(2) { animation-delay: 0.15s; }
.level-card-outer:nth-child(3) { animation-delay: 0.25s; }
.level-card-outer:nth-child(4) { animation-delay: 0.35s; }
.level-card-outer:nth-child(5) { animation-delay: 0.45s; }
.level-card-outer:nth-child(6) { animation-delay: 0.55s; }
.level-card-outer:nth-child(7) { animation-delay: 0.65s; }
.level-card-outer:nth-child(8) { animation-delay: 0.75s; }

@media (prefers-reduced-motion: reduce) {
    .level-card-outer {
        animation: none;
        opacity: 1;
    }
}
</style>

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-layers me-2" style="color:var(--adm-primary);"></i> Navigation par Niveaux</h1>
        <div class="subtitle">Parcourez la hiérarchie : Niveau → Classe → Matière → Cours</div>
    </div>
    <div class="page-actions">
        <button class="adm-btn adm-btn-primary" data-adm-modal="addModal">
            <i class="bi bi-plus-lg"></i> Nouveau niveau
        </button>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="row g-4">
    @php
        $levelNames = ['Débutant', 'Pré-intermédiaire', 'Intermédiaire', 'Avancé'];
        $levelIcons = ['bi-emoji-smile', 'bi-emoji-neutral', 'bi-emoji-wink', 'bi-emoji-star-eyes'];
        $levelColors = ['#22C55E', '#60A5FA', '#FFB347', '#A78BFA'];
        $levelGradients = [
            'linear-gradient(135deg, #16A34A, #22C55E)',
            'linear-gradient(135deg, #003A8F, #60A5FA)',
            'linear-gradient(135deg, #D97706, #FFB347)',
            'linear-gradient(135deg, #7C3AED, #A78BFA)',
        ];
    @endphp

    @forelse($levels as $level)
        @php
            $levelKey = strtolower($level->name);
            $idx = array_search($level->name, $levelNames);
            $icon = $idx !== false ? $levelIcons[$idx] : 'bi-layers-fill';
            $color = $idx !== false ? $levelColors[$idx] : '#2563EB';
            $gradient = $idx !== false ? $levelGradients[$idx] : 'linear-gradient(135deg, #003A8F, #2563EB)';
            $classCount = $level->classes->count();
        @endphp
        <div class="col-lg-3 col-md-6 level-card-outer">
            <a href="{{ route('admin.levels.classes', $level) }}" class="text-decoration-none">
                <div class="adm-card st-fade-up" style="cursor:pointer;height:100%;transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                    <div style="height:120px;background:{{ $gradient }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                        <div style="position:absolute;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.06);top:-50px;right:-50px;"></div>
                        <div style="position:absolute;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.04);bottom:-30px;left:-30px;"></div>
                        <i class="bi {{ $icon }}" style="font-size:3rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                    </div>
                    <div class="adm-card-body text-center" style="padding:1.25rem;">
                        <h4 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.5rem;">{{ $level->name }}</h4>
                        <p style="color:var(--adm-text-muted);font-size:0.85rem;margin-bottom:1rem;">
                            <i class="bi bi-building me-1"></i> {{ $classCount }} classe(s)
                        </p>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <span class="adm-btn" style="background:{{ $gradient }};color:white;border:none;flex:1;">
                                <i class="bi bi-arrow-right me-1"></i> Voir
                            </span>
                            <button onclick="event.preventDefault();event.stopPropagation();document.getElementById('editModal{{ $level->id }}').style.display='flex'" class="adm-btn adm-btn-warning adm-btn-sm" style="padding:8px 12px;" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.levels.destroy', $level->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer le niveau {{ $level->name }} ?')" onclick="event.stopPropagation();">
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
                    <p>Ajoutez des niveaux pour commencer la navigation hiérarchique.</p>
                    <button class="adm-btn adm-btn-primary" data-adm-modal="addModal">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter un niveau
                    </button>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Add Modal -->
<div class="adm-modal-overlay" id="addModal" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
    <div class="adm-modal">
        <form method="POST" action="{{ route('admin.levels.store') }}">
            @csrf
            <div class="adm-modal-header">
                <h5><i class="bi bi-plus-circle me-2"></i> Ajouter un niveau</h5>
                <button type="button" class="adm-modal-close" onclick="document.getElementById('addModal').style.display='none'">&times;</button>
            </div>
            <div class="adm-modal-body">
                <div class="adm-form-group">
                    <label class="adm-form-label">Nom du niveau</label>
                    <select name="name" class="adm-form-select" required>
                        <option value="">Choisir un niveau...</option>
                        <option value="Débutant">Débutant</option>
                        <option value="Pré-intermédiaire">Pré-intermédiaire</option>
                        <option value="Intermédiaire">Intermédiaire</option>
                        <option value="Avancé">Avancé</option>
                    </select>
                </div>
                <div class="adm-form-group">
                    <label class="adm-form-label">Description (optionnelle)</label>
                    <input type="text" name="description" class="adm-form-control" placeholder="Ex: Niveau pour débutants">
                </div>
            </div>
            <div class="adm-modal-footer">
                <button type="button" class="adm-btn adm-btn-ghost" onclick="document.getElementById('addModal').style.display='none'">Annuler</button>
                <button type="submit" class="adm-btn adm-btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modals -->
@foreach($levels as $level)
<div class="adm-modal-overlay" id="editModal{{ $level->id }}" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
    <div class="adm-modal">
        <form method="POST" action="{{ route('admin.levels.update', $level->id) }}">
            @csrf @method('PUT')
            <div class="adm-modal-header">
                <h5><i class="bi bi-pencil me-2"></i> Modifier le niveau</h5>
                <button type="button" class="adm-modal-close" onclick="document.getElementById('editModal{{ $level->id }}').style.display='none'">&times;</button>
            </div>
            <div class="adm-modal-body">
                <div class="adm-form-group">
                    <label class="adm-form-label">Nom du niveau</label>
                    <select name="name" class="adm-form-select" required>
                        <option value="Débutant" {{ $level->name == 'Débutant' ? 'selected' : '' }}>Débutant</option>
                        <option value="Pré-intermédiaire" {{ $level->name == 'Pré-intermédiaire' ? 'selected' : '' }}>Pré-intermédiaire</option>
                        <option value="Intermédiaire" {{ $level->name == 'Intermédiaire' ? 'selected' : '' }}>Intermédiaire</option>
                        <option value="Avancé" {{ $level->name == 'Avancé' ? 'selected' : '' }}>Avancé</option>
                    </select>
                </div>
                <div class="adm-form-group">
                    <label class="adm-form-label">Description (optionnelle)</label>
                    <input type="text" name="description" class="adm-form-control" value="{{ $level->description }}" placeholder="Description du niveau">
                </div>
            </div>
            <div class="adm-modal-footer">
                <button type="button" class="adm-btn adm-btn-ghost" onclick="document.getElementById('editModal{{ $level->id }}').style.display='none'">Annuler</button>
                <button type="submit" class="adm-btn adm-btn-primary">Sauvegarder</button>
            </div>
        </form>
    </div>
</div>
@endforeach

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
