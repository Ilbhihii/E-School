@extends('layouts.prof')

@section('title', 'Modifier le devoir')
@section('page_title', 'Modifier le devoir')
@section('breadcrumb', 'Édition de devoir')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-pencil-square me-2" style="color:var(--adm-warning);"></i> Modifier le devoir</h1>
        <div class="subtitle">{{ $devoir->title }}</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.devoir.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-file-earmark-text" style="color:rgba(255,255,255,0.35);"></i> Détails du devoir</h4>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="{{ route('prof.devoir.update', $devoir) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Title -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du devoir <span style="color:var(--adm-danger);">*</span></label>
                        <input type="text" name="title" value="{{ $devoir->title }}" class="adm-form-control" placeholder="Entrez un titre descriptif..." required>
                    </div>

                    <!-- Description -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Description détaillée</label>
                        <textarea name="description" rows="5" class="adm-form-control adm-form-textarea" placeholder="Décrivez les instructions du devoir...">{{ $devoir->description }}</textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Class -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe <span style="color:var(--adm-danger);">*</span></label>
                        <select name="class_room_id" class="adm-form-select" required>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $class->id == $devoir->class_room_id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Due Date -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Date limite <span style="color:var(--adm-danger);">*</span></label>
                        <input type="date" name="due_date" value="{{ $devoir->due_date }}" class="adm-form-control" required>
                    </div>

                    <!-- File -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Fichier (optionnel)</label>
                        <div style="border:2px dashed rgba(255,255,255,0.1);border-radius:12px;padding:1rem;text-align:center;transition:all 0.3s;">
                            <i class="bi bi-cloud-upload" style="font-size:1.5rem;color:rgba(255,255,255,0.2);display:block;margin-bottom:0.5rem;"></i>
                            <input type="file" name="file" class="adm-form-control" accept=".pdf,.doc,.docx,.zip">
                            <div style="color:var(--adm-text-muted);font-size:0.7rem;margin-top:0.4rem;">PDF, DOC, ZIP - Max 10 Mo</div>
                        </div>
                        @if($devoir->file)
                        <div style="margin-top:8px;padding:8px 12px;background:rgba(22,163,74,0.1);border-radius:8px;display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-paperclip" style="color:#4ADE80;"></i>
                            <span style="font-size:0.8rem;flex:1;">Fichier actuel</span>
                            <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="adm-btn adm-btn-success adm-btn-sm">
                                <i class="bi bi-eye me-1"></i> Voir
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 pt-4" style="border-top:1px solid rgba(255,255,255,0.06);">
                <a href="{{ route('prof.devoir.index') }}" class="adm-btn adm-btn-ghost">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
                <button type="submit" class="adm-btn adm-btn-warning" style="margin-left:auto;">
                    <i class="bi bi-save me-1"></i> Modifier le devoir
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.adm-form-textarea {
    min-height: 120px;
    resize: vertical;
}
</style>

@endsection
