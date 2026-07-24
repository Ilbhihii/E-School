@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">🎯 Navigation par Niveaux</span></h1>
                <p class="admin-header-subtitle">Parcourez vos cours, lives et devoirs par niveau d'études</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach($levels as $lvl)
                @php
                    $levelFirst = \App\Models\Level::where('name', $lvl->name)->first();
                    $classCount = \App\Models\ClassRoom::whereHas('levels', function($q) use ($lvl) {
                        $q->where('name', $lvl->name);
                    })->count();
                @endphp
                @if($levelFirst)
                <div class="col-md-4">
                    <a href="{{ route('prof.levels.classes', $levelFirst) }}" class="text-decoration-none">
                        <div class="adm-card st-fade-up" style="transition:all .2s;cursor:pointer;">
                            <div class="adm-card-body text-center" style="padding:2rem;">
                                <div style="font-size:3rem;margin-bottom:1rem;color:var(--primary);">
                                    <i class="bi bi-layers-fill"></i>
                                </div>
                                <h4 style="font-weight:700;color:var(--text);">{{ $lvl->name }}</h4>
                                <p style="color:var(--muted);font-size:14px;">{{ $classCount }} classe(s) associée(s)</p>
                                <span class="adm-btn adm-btn-primary adm-btn-sm">
                                    <i class="bi bi-arrow-right me-1"></i> Voir les classes
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
            @endforeach
        </div>

    </div>
</div>
@endsection
