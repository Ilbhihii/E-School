@extends('layouts.prof')

@section('title', 'Navigation par Matières')
@section('page_title', 'Matières')
@section('breadcrumb', 'Navigation pédagogique')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📚 Navigation par Matières</span></h1>
                <p class="admin-header-subtitle">Parcourez par Matière → Niveau → Classe</p>
            </div>
        </div>

        <div class="row g-4">
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
                    ];
                    $gradient = $gradients[$loop->index % count($gradients)];
                @endphp
                <div class="col-md-4">
                    <a href="{{ route('prof.subjects.levels', $subject) }}" class="text-decoration-none">
                        <div class="adm-card st-fade-up" style="transition:all .2s;cursor:pointer;">
                            <div style="height:100px;background:{{ $gradient }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                                <div style="position:absolute;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06);top:-40px;right:-40px;"></div>
                                <i class="bi bi-{{ $icon }}" style="font-size:2.5rem;color:rgba(255,255,255,0.3);position:relative;z-index:1;"></i>
                            </div>
                            <div class="adm-card-body text-center" style="padding:1.5rem;">
                                <h4 style="font-weight:700;color:var(--text);margin-bottom:0.5rem;">{{ $subject->name }}</h4>
                                <p style="color:var(--muted);font-size:14px;">
                                    <i class="bi bi-layers me-1"></i> Voir les niveaux
                                </p>
                                <span class="adm-btn" style="background:{{ $gradient }};color:white;border:none;width:100%;">
                                    <i class="bi bi-layers me-1"></i> Niveaux
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="adm-card">
                        <div class="adm-empty">
                            <i class="bi bi-inbox"></i>
                            <h3>Aucune matière</h3>
                            <p>Aucune matière n'est disponible.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
