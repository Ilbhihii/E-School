@extends('layouts.admin')

@section('title', 'Détails absence')
@section('page_title', 'Détails absence')
@section('breadcrumb', 'Détails de l\'absence')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-info-circle" style="color:rgba(255,255,255,0.35);"></i> Détails de l'absence</h4>
            </div>
            <div class="adm-card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="adm-form-label">Étudiant</label>
                        <div style="font-weight:600;color:rgba(255,255,255,0.85);">{{ $absence->user->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="adm-form-label">Classe</label>
                        <div><span class="adm-badge adm-badge-info">{{ $absence->user->classRoom->name ?? '-' }}</span></div>
                    </div>
                    <div class="col-md-6">
                        <label class="adm-form-label">Date</label>
                        <div style="color:var(--adm-text-secondary);">{{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="adm-form-label">Statut</label>
                        <div>
                            @if($absence->present)
                                <span class="adm-badge adm-badge-success">✅ Présent</span>
                            @else
                                <span class="adm-badge adm-badge-danger">❌ Absent</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-4">
                    <a href="{{ route('admin.absences') }}" class="adm-btn adm-btn-ghost">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                    <a href="{{ route('admin.absences.edit', $absence->id) }}" class="adm-btn adm-btn-warning">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
