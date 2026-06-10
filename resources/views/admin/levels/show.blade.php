@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:700px">

        <!-- GRADIENT HEADER -->
        <div class="admin-gradient-header">
            <h1>Détail du Niveau</h1>
            <p>Informations complètes sur le niveau</p>
        </div>

        <div class="adm-card">
            <div class="adm-card-body">
                <div class="adm-detail-row">
                    <div class="adm-detail-item">
                        <label>Niveau</label>
                        <div class="value">{{ $level->name }}</div>
                    </div>
                    <div class="adm-detail-item">
                        <label>Matière associée</label>
                        <div class="value">
                            <span class="adm-badge adm-badge-purple">{{ $level->subject->name ?? 'Non définie' }}</span>
                        </div>
                    </div>
                </div>

                <div class="adm-flex adm-gap-2 adm-mt-3">
                    <a href="{{ route('admin.levels.edit', $level) }}" class="adm-btn adm-btn-warning">
                        <i class="bi bi-pencil-square"></i> Modifier
                    </a>
                    <a href="{{ route('admin.levels.index') }}" class="adm-btn adm-btn-ghost">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
