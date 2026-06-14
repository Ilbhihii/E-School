@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;font-size:13px;color:var(--muted);">
                    <a href="{{ route('prof.levels') }}" style="color:var(--muted);text-decoration:none;"><i class="bi bi-layers me-1"></i>Niveaux</a>
                    <span>/</span>
                    <span style="color:var(--text);font-weight:600;">{{ $level->name }}</span>
                </div>
                <h1 class="admin-header-title"><span class="gradient">🏫 Classes — {{ $level->name }}</span></h1>
                <p class="admin-header-subtitle">Sélectionnez une classe pour gérer ses matières</p>
            </div>
        </div>

        @if($classes->isEmpty())
            <div class="adm-card">
                <div class="adm-empty">
                    <i class="bi bi-inbox"></i>
                    <h3>Aucune classe</h3>
                    <p>Aucune classe n'est associée à ce niveau.</p>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($classes as $class)
                    <div class="col-md-4">
                        <a href="{{ route('prof.subjects', [$level, $class]) }}" class="text-decoration-none">
                            <div class="adm-card st-fade-up" style="transition:all .2s;cursor:pointer;">
                                <div class="adm-card-body text-center" style="padding:2rem;">
                                    <div style="font-size:3rem;margin-bottom:1rem;color:var(--blue);">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <h4 style="font-weight:700;color:var(--text);">{{ $class->name }}</h4>
                                    <span class="adm-btn adm-btn-primary adm-btn-sm">
                                        <i class="bi bi-book me-1"></i> Voir les matières
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection
