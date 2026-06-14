@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;font-size:13px;color:var(--muted);">
                    <a href="{{ route('prof.levels') }}" style="color:var(--muted);text-decoration:none;"><i class="bi bi-layers me-1"></i>Niveaux</a>
                    <span>/</span>
                    <a href="{{ route('prof.levels.classes', $level) }}" style="color:var(--muted);text-decoration:none;">{{ $level->name }}</a>
                    <span>/</span>
                    <span style="color:var(--text);font-weight:600;">{{ $class->name }}</span>
                </div>
                <h1 class="admin-header-title"><span class="gradient">📚 Matières — {{ $class->name }}</span></h1>
                <p class="admin-header-subtitle">Choisissez une matière pour gérer les cours, lives et devoirs</p>
            </div>
        </div>

        @if($subjects->isEmpty())
            <div class="adm-card">
                <div class="adm-empty">
                    <i class="bi bi-inbox"></i>
                    <h3>Aucune matière</h3>
                    <p>Aucune matière n'est associée à cette classe.</p>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($subjects as $subject)
                    @php $colorMap = ['primary','success','danger','warning','info','purple','teal','orange']; @endphp
                    <div class="col-md-4">
                        <div class="adm-card st-fade-up">
                            <div class="adm-card-body text-center" style="padding:1.5rem;">
                                <div style="font-size:2.5rem;margin-bottom:.75rem;color:var(--{{ $colorMap[$loop->index % count($colorMap)] }});">
                                    <i class="bi bi-book-fill"></i>
                                </div>
                                <h5 style="font-weight:700;color:var(--text);margin-bottom:1rem;">{{ $subject->name }}</h5>
                                <div class="d-flex flex-wrap gap-2" style="justify-content:center;">
                                    <a href="{{ route('prof.browse.courses', [$level, $class, $subject]) }}" class="adm-btn adm-btn-primary adm-btn-sm">
                                        <i class="bi bi-play-circle me-1"></i> Cours
                                    </a>
                                    <a href="{{ route('prof.browse.lives', [$level, $class]) }}" class="adm-btn adm-btn-danger adm-btn-sm">
                                        <i class="bi bi-camera-video me-1"></i> Lives
                                    </a>
                                    <a href="{{ route('prof.browse.devoirs', [$level, $class, $subject]) }}" class="adm-btn adm-btn-success adm-btn-sm">
                                        <i class="bi bi-file-earmark-check me-1"></i> Devoirs
                                    </a>
                                </div>
                                <div style="margin-top:1rem;padding-top:.75rem;border-top:1px solid var(--adm-border);">
                                    <a href="{{ route('prof.courses.create') }}?class_id={{ $class->id }}&subject_id={{ $subject->id }}" class="adm-btn adm-btn-ghost adm-btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i> Créer un cours
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection
