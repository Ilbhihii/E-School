@extends('layouts.admin')

@section('title', 'Assignation des Professeurs')
@section('page_title', 'Assignation Profs')
@section('breadcrumb', 'Professeurs → Niveaux → Classes → Matières')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-person-badge me-2" style="color:var(--adm-accent);"></i> Assignation des Professeurs</h1>
        <div class="subtitle">Attribuez à chaque professeur son niveau, sa classe et sa matière</div>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="adm-alert adm-alert-danger mb-4">{{ session('error') }}</div>
@endif

<div class="row g-4">
    <!-- Assign Form -->
    <div class="col-lg-5">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-plus-circle" style="color:#4ADE80;"></i> Nouvelle assignation</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.users.store-prof-assignment') }}">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Professeur <span style="color:var(--adm-danger);">*</span></label>
                        <select name="prof_id" class="adm-form-select" required>
                            <option value="">Sélectionner un professeur</option>
                            @foreach($professors as $prof)
                                <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Niveau <span style="color:var(--adm-danger);">*</span></label>
                        <select name="level_id" class="adm-form-select" required>
                            <option value="">Sélectionner un niveau</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe <span style="color:var(--adm-danger);">*</span></label>
                        <select name="class_id" class="adm-form-select" required>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Matière <span style="color:var(--adm-danger);">*</span></label>
                        <select name="subject_id" class="adm-form-select" required>
                            <option value="">Sélectionner une matière</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-accent w-100" style="padding:12px;">
                        <i class="bi bi-check-circle me-2"></i> Assigner le professeur
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="col-lg-7">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-list-check" style="color:rgba(255,255,255,0.35);"></i> Assignations existantes</h4>
                <div class="card-actions">
                    <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $assignments->count() }} assignation(s)</span>
                </div>
            </div>
            <div class="adm-card-body p-0">
                @if($assignments->isNotEmpty())
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Professeur</th>
                                <th>Niveau</th>
                                <th>Classe</th>
                                <th>Matière</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $a)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div class="adm-avatar adm-avatar-sm" style="background:linear-gradient(135deg,#7C3AED,#A78BFA);width:32px;height:32px;font-size:0.7rem;">
                                            {{ strtoupper(substr($a->prof?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span style="font-weight:500;">{{ $a->prof?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td><span class="adm-badge adm-badge-info">{{ $a->level?->name ?? '—' }}</span></td>
                                <td><span class="adm-badge adm-badge-primary">{{ $a->classRoom?->name ?? '—' }}</span></td>
                                <td><span class="adm-badge adm-badge-accent">{{ $a->subject?->name ?? '—' }}</span></td>
                                <td style="text-align:right;">
                                    <form method="POST" action="{{ route('admin.users.destroy-prof-assignment', $a->id) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette assignation ?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="adm-empty" style="padding:3rem 2rem;">
                    <div class="adm-empty-icon"><i class="bi bi-person-badge"></i></div>
                    <h5>Aucune assignation</h5>
                    <p>Assignez un professeur à un niveau, une classe et une matière via le formulaire.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
