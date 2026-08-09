@extends('layouts.admin')

@section('title', 'Modifier les affectations')
@section('page_title', 'Modifier les affectations')
@section(
    'breadcrumb',
    'Étudiant → Matière → Niveau → Classe → Créneau'
)

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-11">
        <div class="adm-page-header">
            <div>
                <h1>
                    Affectations de {{ $user->name }}
                </h1>

                <div class="subtitle">
                    Chaque affectation suit désormais :
                    Matière → Niveau → Classe → Créneau.
                </div>
            </div>

            <div class="page-actions">
                <a
                    href="{{ route('admin.assign.class') }}"
                    class="adm-btn adm-btn-ghost"
                >
                    <i class="bi bi-people"></i>
                    Toutes les affectations
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="adm-alert adm-alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="adm-alert adm-alert-info mb-4">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="adm-alert adm-alert-danger mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4>
                    <i class="bi bi-person-badge-fill"></i>
                    Étudiant
                </h4>
            </div>

            <div class="adm-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="adm-form-label">
                            Nom
                        </label>

                        <div class="adm-form-control">
                            {{ $user->name }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="adm-form-label">
                            Email
                        </label>

                        <div class="adm-form-control">
                            {{ $user->email }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="adm-form-label">
                            Affectations
                        </label>

                        <div class="adm-form-control">
                            {{ $assignments->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4>
                    <i class="bi bi-diagram-3-fill"></i>
                    Affectations actuelles
                </h4>

                <a
                    href="{{ route('admin.users.edit', $user) }}"
                    class="adm-btn adm-btn-primary adm-btn-sm"
                >
                    <i class="bi bi-plus-lg"></i>
                    Ajouter
                </a>
            </div>

            <div class="adm-card-body">
                @forelse($assignments as $assignment)
                    <div
                        style="
                            display:flex;
                            align-items:center;
                            justify-content:space-between;
                            gap:12px;
                            padding:12px 0;
                            border-bottom:1px solid rgba(255,255,255,.06);
                        "
                    >
                        <div
                            style="
                                display:flex;
                                flex-wrap:wrap;
                                align-items:center;
                                gap:6px;
                            "
                        >
                            <span class="adm-badge adm-badge-primary">
                                {{ $assignment->subject_name ?? 'Matière' }}
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span class="adm-badge">
                                {{ $assignment->level_name ?? 'Niveau' }}
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span class="adm-badge adm-badge-info">
                                {{ $assignment->class_name ?? 'Classe' }}
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span class="adm-badge adm-badge-warning">
                                {{ $assignment->slot_code ?? '—' }}
                            </span>
                        </div>

                        <div class="d-flex gap-2">
                            <a
                                href="{{
                                    route(
                                        'admin.users.edit',
                                        [
                                            'user' => $user,
                                            'assignment_id' =>
                                                $assignment->pivot_id,
                                        ]
                                    )
                                }}"
                                class="adm-btn adm-btn-ghost adm-btn-sm"
                            >
                                <i class="bi bi-pencil"></i>
                                Modifier
                            </a>

                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'admin.assign.class.destroy',
                                        $assignment->pivot_id
                                    )
                                }}"
                                onsubmit="return confirm('Supprimer cette affectation ?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="adm-btn adm-btn-danger adm-btn-sm"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div
                        style="
                            padding:18px;
                            border:1px dashed rgba(148,163,184,.18);
                            border-radius:12px;
                            color:var(--adm-text-muted);
                            text-align:center;
                        "
                    >
                        Aucune affectation structurelle pour cet étudiant.
                    </div>
                @endforelse
            </div>
        </div>

        @php
            $editingAssignment =
                $selectedAssignment !== null;
        @endphp

        <form
            method="POST"
            action="{{
                $editingAssignment
                    ? route(
                        'admin.assign.class.update',
                        $selectedAssignment->pivot_id
                    )
                    : route(
                        'admin.assign.class.store'
                    )
            }}"
        >
            @csrf

            @if($editingAssignment)
                @method('PATCH')
            @endif

            <input
                type="hidden"
                name="user_id"
                value="{{ $user->id }}"
            >

            @include(
                'components.pedagogical-path-edit',
                [
                    'hierarchy' =>
                        $assignmentHierarchy,
                    'prefix' =>
                        'studentAssignmentEdit',
                    'selectedSubject' =>
                        old(
                            'subject_id',
                            $selectedAssignment
                                ?->subject_id
                        ),
                    'selectedLevel' =>
                        old(
                            'level_id',
                            $selectedAssignment
                                ?->level_id
                        ),
                    'selectedClass' =>
                        old(
                            'class_id',
                            $selectedAssignment
                                ?->class_id
                        ),
                    'selectedSlot' =>
                        old(
                            'class_slot_id',
                            $selectedAssignment
                                ?->class_slot_id
                        ),
                ]
            )

            <div class="d-flex gap-3">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="adm-btn adm-btn-ghost flex-fill text-center"
                >
                    <i class="bi bi-arrow-left"></i>
                    Retour
                </a>

                <button
                    type="submit"
                    class="adm-btn adm-btn-success flex-fill"
                >
                    <i class="bi bi-check-lg"></i>

                    {{
                        $editingAssignment
                            ? 'Enregistrer la modification'
                            : 'Ajouter l’affectation'
                    }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
