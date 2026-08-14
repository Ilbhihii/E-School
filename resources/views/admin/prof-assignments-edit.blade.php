@extends('layouts.admin')

@section('title', 'Modifier les assignations')
@section('page_title', 'Modifier les assignations')
@section(
    'breadcrumb',
    'Professeurs → Assignations → Modification'
)

@section('content')
<div class="adm-page-header">
    <div>
        <h1>
            <i
                class="bi bi-pencil-square me-2"
                style="color:var(--adm-accent);"
            ></i>
            Modifier les assignations
        </h1>

        <div class="subtitle">
            {{ $professor->name }} — ajoutez, modifiez ou retirez
            ses parcours pédagogiques actifs.
        </div>
    </div>

    <a
        href="{{ route('admin.users.prof-assignments') }}"
        class="adm-btn adm-btn-ghost"
    >
        <i class="bi bi-arrow-left"></i>
        Retour
    </a>
</div>

@if($errors->any())
    <div class="adm-alert adm-alert-danger mb-3">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
@endif

@php
    $initialAssignments = old(
        'assignments',
        $selectedAssignments
    );
@endphp

<div class="row g-4">
    <div class="col-xl-4">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4>
                    <i class="bi bi-person-badge"></i>
                    Professeur
                </h4>
            </div>

            <div class="adm-card-body">
                <div class="prof-edit-profile">
                    <span class="adm-avatar">
                        {{
                            mb_strtoupper(
                                mb_substr(
                                    $professor->name,
                                    0,
                                    1
                                )
                            )
                        }}
                    </span>

                    <div>
                        <strong>{{ $professor->name }}</strong>
                        <small>{{ $professor->email }}</small>
                    </div>
                </div>

                <div class="prof-edit-note">
                    <i class="bi bi-shield-check"></i>
                    <span>
                        La sauvegarde remplace uniquement les affectations
                        des matières actuellement actives. Les anciennes
                        matières inactives restent conservées en historique.
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4>
                    <i class="bi bi-diagram-3"></i>
                    Parcours affectés
                </h4>
            </div>

            <div class="adm-card-body">
                <form
                    method="POST"
                    action="{{
                        route(
                            'admin.users.update-prof-assignments',
                            $professor
                        )
                    }}"
                >
                    @csrf
                    @method('PATCH')

                    @include(
                        'admin.partials.prof-assignment-builder',
                        [
                            'builderId' => 'profEditAssignmentBuilder',
                            'assignmentHierarchy' => $assignmentHierarchy,
                            'initialAssignments' => $initialAssignments,
                        ]
                    )

                    <div class="prof-edit-actions">
                        <a
                            href="{{ route('admin.users.prof-assignments') }}"
                            class="adm-btn adm-btn-ghost"
                        >
                            Annuler
                        </a>

                        <button
                            type="submit"
                            class="adm-btn adm-btn-accent"
                        >
                            <i class="bi bi-check-circle"></i>
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.prof-edit-profile {
    display: flex;
    align-items: center;
    gap: 11px;
}

.prof-edit-profile .adm-avatar {
    width: 46px;
    height: 46px;
    background: linear-gradient(135deg,#7C3AED,#A78BFA);
}

.prof-edit-profile strong,
.prof-edit-profile small {
    display: block;
}

.prof-edit-profile strong {
    font-size: .78rem;
}

.prof-edit-profile small {
    margin-top: 3px;
    color: var(--adm-text-muted);
    font-size: .61rem;
}

.prof-edit-note {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-top: 14px;
    padding: 10px;
    color: var(--adm-text-muted);
    border: 1px solid rgba(59,130,246,.11);
    border-radius: 10px;
    background: rgba(59,130,246,.035);
    font-size: .59rem;
    line-height: 1.55;
}

.prof-edit-note i {
    margin-top: 1px;
    color: #60A5FA;
}

.prof-edit-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 15px;
}
</style>
@endsection
