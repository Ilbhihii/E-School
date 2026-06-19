@extends('layouts.prof')

@section('title', $course->title . ' — Détail du cours')
@section('page_title', 'Détail du cours')
@section('breadcrumb', 'Cours → Détail')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-eye me-2" style="color:var(--adm-info);"></i> {{ $course->title }}</h1>
        <div class="subtitle">
            @if($course->level ?? false)
                <span class="info-chip me-3"><i class="bi bi-layers me-1" style="color:#22C55E;"></i> {{ $course->level->name }}</span>
            @endif
            @if($course->subject ?? false)
                <span class="info-chip me-3"><i class="bi bi-book me-1" style="color:#A78BFA;"></i> {{ $course->subject->name }}</span>
            @endif
            @if($course->classRoom ?? false)
                <span class="info-chip"><i class="bi bi-building me-1" style="color:#06B6D4;"></i> {{ $course->classRoom->name }}</span>
            @endif
        </div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.courses.edit', $course->id) }}" class="adm-btn adm-btn-warning">
            <i class="bi bi-pencil"></i> Modifier
        </a>
        <a href="{{ url()->previous() }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Description -->
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4><i class="bi bi-info-circle" style="color:rgba(255,255,255,0.35);"></i> Description</h4>
            </div>
            <div class="adm-card-body">
                <p style="color:rgba(255,255,255,0.7);line-height:1.8;margin:0;">
                    {{ $course->description ?? 'Aucune description disponible.' }}
                </p>
            </div>
        </div>

        <!-- Video -->
        @if($course->video)
        <div class="adm-card mb-4">
            <div class="adm-card-header" style="border-bottom-color:rgba(239,68,68,0.15);">
                <h4><i class="bi bi-play-circle-fill" style="color:#EF4444;"></i> Vidéo du cours</h4>
            </div>
            <div class="adm-card-body">
                <div style="border-radius:12px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.3);">
                    <video controls preload="metadata" style="width:100%;max-height:480px;display:block;">
                        <source src="{{ asset('storage/'.$course->video) }}">
                    </video>
                </div>
                <div style="display:flex;gap:0.75rem;margin-top:1rem;">
                    <a href="{{ asset('storage/'.$course->video) }}" target="_blank" class="adm-btn adm-btn-danger adm-btn-sm">
                        <i class="bi bi-eye me-1"></i> Plein écran
                    </a>
                    <a href="{{ asset('storage/'.$course->video) }}" download class="adm-btn adm-btn-ghost adm-btn-sm">
                        <i class="bi bi-download me-1"></i> Télécharger
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- PDF -->
        @if($course->pdf)
        <div class="adm-card mb-4">
            <div class="adm-card-header" style="border-bottom-color:rgba(34,197,94,0.15);">
                <h4><i class="bi bi-file-earmark-pdf-fill" style="color:#22C55E;"></i> Document PDF</h4>
            </div>
            <div class="adm-card-body">
                <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:0.75rem;padding:1rem;background:rgba(22,163,74,0.08);border-radius:12px;flex:1;">
                        <i class="bi bi-file-pdf-fill" style="font-size:2rem;color:#22C55E;"></i>
                        <div>
                            <div style="font-weight:600;color:rgba(255,255,255,0.85);">Document du cours</div>
                            <div style="font-size:0.78rem;color:var(--adm-text-muted);">PDF — {{ $course->title }}</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:0.5rem;">
                        <a href="{{ asset('storage/'.$course->pdf) }}" target="_blank" class="adm-btn adm-btn-success">
                            <i class="bi bi-eye me-1"></i> Voir
                        </a>
                        <a href="{{ asset('storage/'.$course->pdf) }}" download class="adm-btn adm-btn-ghost">
                            <i class="bi bi-download me-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Assignments liés -->
        @if($course->assignments->count() > 0)
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4><i class="bi bi-clipboard-check" style="color:#4ADE80;"></i> Devoirs associés ({{ $course->assignments->count() }})</h4>
            </div>
            <div class="adm-card-body p-0">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date limite</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($course->assignments as $devoir)
                            <tr>
                                <td><span style="font-weight:500;">{{ $devoir->title }}</span></td>
                                <td>
                                    <span class="adm-badge {{ $devoir->due_date <= now()->format('Y-m-d') ? 'adm-badge-danger' : 'adm-badge-success' }}">
                                        {{ $devoir->due_date ? \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') : '—' }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    @if($devoir->file)
                                    <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="adm-btn adm-btn-sm adm-btn-accent">
                                        <i class="bi bi-eye"></i> Voir
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Sidebar info -->
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4><i class="bi bi-info-circle" style="color:rgba(255,255,255,0.35);"></i> Informations</h4>
            </div>
            <div class="adm-card-body">
                <div style="display:flex;flex-direction:column;gap:0.75rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.6rem 0.75rem;background:rgba(255,255,255,0.03);border-radius:8px;">
                        <span style="font-size:0.78rem;color:var(--adm-text-muted);">Créé le</span>
                        <span style="font-size:0.85rem;font-weight:600;color:rgba(255,255,255,0.8);">{{ $course->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($course->level ?? false)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.6rem 0.75rem;background:rgba(255,255,255,0.03);border-radius:8px;">
                        <span style="font-size:0.78rem;color:var(--adm-text-muted);">Niveau</span>
                        <span style="font-size:0.85rem;font-weight:600;color:rgba(255,255,255,0.8);">{{ $course->level->name }}</span>
                    </div>
                    @endif
                    @if($course->classRoom ?? false)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.6rem 0.75rem;background:rgba(255,255,255,0.03);border-radius:8px;">
                        <span style="font-size:0.78rem;color:var(--adm-text-muted);">Classe</span>
                        <span style="font-size:0.85rem;font-weight:600;color:rgba(255,255,255,0.8);">{{ $course->classRoom->name }}</span>
                    </div>
                    @endif
                    @if($course->subject ?? false)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.6rem 0.75rem;background:rgba(255,255,255,0.03);border-radius:8px;">
                        <span style="font-size:0.78rem;color:var(--adm-text-muted);">Matière</span>
                        <span style="font-size:0.85rem;font-weight:600;color:rgba(255,255,255,0.8);">{{ $course->subject->name }}</span>
                    </div>
                    @endif
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.6rem 0.75rem;background:rgba(255,255,255,0.03);border-radius:8px;">
                        <span style="font-size:0.78rem;color:var(--adm-text-muted);">Contenu</span>
                        <div style="display:flex;gap:4px;">
                            @if($course->video) <span class="adm-badge adm-badge-danger" style="font-size:0.6rem;">Vidéo</span> @endif
                            @if($course->pdf) <span class="adm-badge adm-badge-success" style="font-size:0.6rem;">PDF</span> @endif
                            @if(!$course->video && !$course->pdf) <span style="font-size:0.8rem;color:var(--adm-text-muted);">Aucun</span> @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($course->course_link)
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-link-45deg" style="color:#60A5FA;"></i> Lien externe</h4>
            </div>
            <div class="adm-card-body">
                <a href="{{ $course->course_link }}" target="_blank" class="adm-btn adm-btn-primary" style="width:100%;">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Accéder au lien
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.info-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 4px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.6);
}
</style>

@endsection
