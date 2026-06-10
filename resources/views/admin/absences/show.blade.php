@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:700px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📌 Détails de l'absence</span></h1>
                <p class="admin-header-subtitle">Informations complètes sur l'absence</p>
            </div>
        </div>

        <!-- CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <div class="adm-detail-row">
                    <div class="adm-detail-item">
                        <label>Étudiant</label>
                        <div class="value">{{ $absence->user->name }}</div>
                    </div>
                    <div class="adm-detail-item">
                        <label>Classe</label>
                        <div class="value">{{ $absence->user->classRoom->name ?? '-' }}</div>
                    </div>
                    <div class="adm-detail-item">
                        <label>Date</label>
                        <div class="value">{{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}</div>
                    </div>
                    <div class="adm-detail-item">
                        <label>Statut</label>
                        <div class="value">
                            @if($absence->present)
                                <span class="adm-badge adm-badge-success"><i class="bi bi-check-circle-fill"></i> Présent</span>
                            @else
                                <span class="adm-badge adm-badge-danger"><i class="bi bi-x-circle-fill"></i> Absent</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="adm-flex adm-gap-2 adm-mt-3">
                    <a href="{{ route('admin.absences') }}" class="adm-btn adm-btn-ghost">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                    <a href="{{ route('admin.absences.edit', $absence->id) }}" class="adm-btn adm-btn-warning">
                        <i class="bi bi-pencil-fill"></i> Modifier
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
