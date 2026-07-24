@extends('layouts.prof')

@section('title', 'Navigation par Niveaux')
@section('page_title', 'Niveaux')
@section('breadcrumb', 'Navigation pédagogique')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-layers me-2" style="color:var(--adm-primary);"></i> Navigation par Niveaux</h1>
        <div class="subtitle">Parcourez la hiérarchie : Niveau → Classe → Matière → Cours</div>
    </div>
</div>

<div class="row g-4">
    @php
        $levelGradients = [
            'linear-gradient(135deg, #16A34A, #22C55E)',
            'linear-gradient(135deg, #003A8F, #60A5FA)',
            'linear-gradient(135deg, #D97706, #FFB347)',
            'linear-gradient(135deg, #7C3AED, #A78BFA)',
        ];
        $levelIcons = ['bi-emoji-smile', 'bi-emoji-neutral', 'bi-emoji-wink', 'bi-emoji-star-eyes'];
    @endphp

    @forelse($levels as $level)
        @php
            $idx = $loop->index % count($levelGradients);
            $gradient = $levelGradients[$idx];
            $icon = $levelIcons[$idx] ?? 'bi-layers-fill';
            $classCount = $level->classes->count();
        @endphp
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('prof.levels.classes', $level) }}" class="text-decoration-none">
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
                        <span class="adm-btn" style="background:{{ $gradient }};color:white;border:none;width:100%;">
                            <i class="bi bi-arrow-right me-1"></i> Voir les classes
                        </span>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="adm-card">
                <div class="adm-empty" style="padding:4rem 2rem;">
                    <div class="adm-empty-icon"><i class="bi bi-layers"></i></div>
                    <h5>Aucun niveau disponible</h5>
                    <p>Les niveaux seront disponibles une fois configurés par l'administration.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

@endsection
