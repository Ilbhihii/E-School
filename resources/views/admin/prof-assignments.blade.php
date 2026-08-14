@extends('layouts.admin')

@section('title', 'Assignation des professeurs')
@section('page_title', 'Assignation professeurs')
@section(
    'breadcrumb',
    'Professeur → Matière → Niveau → Classe → Créneau'
)

@section('content')

<div class="adm-page-header">
    <div>
        <h1>
            <i
                class="bi bi-person-badge-fill me-2"
                style="color:var(--adm-accent);"
            ></i>
            Assignation des professeurs
        </h1>

        <div class="subtitle">
            Affectez un même professeur à plusieurs matières, niveaux,
            classes et groupes D1, D2, I1, A1… en une seule opération.
        </div>
    </div>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-3">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="adm-alert adm-alert-danger mb-3">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="adm-alert adm-alert-danger mb-3">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
@endif

@php
    $initialAssignments = old('assignments');

    if (!is_array($initialAssignments) || empty($initialAssignments)) {
        $initialAssignments = [[
            'subject_id' => old('subject_id', ''),
            'level_id' => old('level_id', ''),
            'class_id' => old('class_id', ''),
            'class_slot_id' => old('class_slot_id', ''),
        ]];
    }
@endphp

<div class="row g-4">
    <div class="col-xl-5">
        <div class="adm-card">
            <div class="adm-card-header">
                <div>
                    <h4>
                        <i
                            class="bi bi-plus-circle-fill"
                            style="color:#4ADE80;"
                        ></i>
                        Nouvelles assignations
                    </h4>

                    <p class="prof-assignment-subtitle">
                        Un professeur → plusieurs parcours
                    </p>
                </div>
            </div>

            <div class="adm-card-body">
                <form
                    method="POST"
                    action="{{
                        route(
                            'admin.users.store-prof-assignment'
                        )
                    }}"
                >
                    @csrf

                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="prof_assignment_prof_id"
                        >
                            Professeur
                            <span class="assignment-required">*</span>
                        </label>

                        <select
                            name="prof_id"
                            id="prof_assignment_prof_id"
                            class="adm-form-select
                                @error('prof_id') error @enderror"
                            required
                        >
                            <option value="">
                                Sélectionner un professeur
                            </option>

                            @foreach($professors as $professor)
                                <option
                                    value="{{ $professor->id }}"
                                    {{
                                        (string) old('prof_id')
                                        === (string) $professor->id
                                            ? 'selected'
                                            : ''
                                    }}
                                >
                                    {{ $professor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @include(
                        'admin.partials.prof-assignment-builder',
                        [
                            'builderId' => 'profCreateAssignmentBuilder',
                            'assignmentHierarchy' => $assignmentHierarchy,
                            'initialAssignments' => $initialAssignments,
                        ]
                    )

                    <button
                        type="submit"
                        class="adm-btn adm-btn-accent w-100"
                        style="padding:12px;margin-top:14px;"
                    >
                        <i class="bi bi-check-circle"></i>
                        Enregistrer les assignations
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4>
                    <i
                        class="bi bi-list-check"
                        style="color:rgba(255,255,255,.35);"
                    ></i>
                    Assignations existantes
                </h4>

                <span class="prof-assignment-subtitle">
                    {{ $assignments->count() }} assignation(s)
                </span>
            </div>

            <div class="adm-card-body p-0">
                @if($assignments->isNotEmpty())
                    <div class="adm-table-wrap">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th>Professeur</th>
                                    <th>Matière</th>
                                    <th>Niveau</th>
                                    <th>Classe</th>
                                    <th>Créneau</th>
                                    <th>Horaire</th>
                                    <th style="text-align:right;">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($assignments as $assignment)
                                    @php
                                        $slotCode = strtoupper(
                                            trim(
                                                (string) (
                                                    $assignment->classSlot?->code
                                                    ?? ''
                                                )
                                            )
                                        );

                                        $scheduleKey =
                                            (int) $assignment->subject_id
                                            . ':'
                                            . (int) $assignment->level_id
                                            . ':'
                                            . (int) $assignment->class_id
                                            . ':'
                                            . $slotCode;

                                        $linkedSchedule =
                                            $scheduleMap->get($scheduleKey);
                                    @endphp

                                    <tr>
                                        <td>
                                            <div class="professor-cell">
                                                <span class="adm-avatar adm-avatar-sm">
                                                    {{
                                                        mb_strtoupper(
                                                            mb_substr(
                                                                $assignment->prof?->name
                                                                ?? '?',
                                                                0,
                                                                1
                                                            )
                                                        )
                                                    }}
                                                </span>

                                                <strong>
                                                    {{ $assignment->prof?->name ?? '—' }}
                                                </strong>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="adm-badge adm-badge-accent">
                                                {{ $assignment->subject?->name ?? '—' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="adm-badge adm-badge-info">
                                                {{ $assignment->level?->name ?? '—' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="adm-badge adm-badge-primary">
                                                {{ $assignment->classRoom?->name ?? '—' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="prof-slot-badge">
                                                {{ $slotCode ?: '—' }}
                                            </span>
                                        </td>

                                        <td>
                                            @if($linkedSchedule)
                                                <div class="prof-linked-schedule">
                                                    <span class="prof-day-badge">
                                                        <i class="bi bi-calendar3"></i>
                                                        {{ $linkedSchedule->day_label }}
                                                    </span>

                                                    <span class="prof-time-badge">
                                                        <i class="bi bi-clock"></i>
                                                        {{ $linkedSchedule->time_range_label }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="prof-no-schedule">
                                                    <i class="bi bi-calendar-plus"></i>
                                                    Horaire à définir
                                                </span>
                                            @endif
                                        </td>

                                        <td style="text-align:right;">
                                            <div class="prof-assignment-actions">
                                                <a
                                                    href="{{
                                                        route(
                                                            'admin.users.edit-prof-assignments',
                                                            $assignment->prof_id
                                                        )
                                                    }}"
                                                    class="adm-btn adm-btn-ghost adm-btn-sm"
                                                    title="Modifier les assignations de ce professeur"
                                                >
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <form
                                                    method="POST"
                                                    action="{{
                                                        route(
                                                            'admin.users.destroy-prof-assignment',
                                                            $assignment->id
                                                        )
                                                    }}"
                                                    onsubmit="return confirm('Supprimer cette assignation ?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="adm-btn adm-btn-danger adm-btn-sm"
                                                        title="Supprimer"
                                                    >
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="adm-empty">
                        <div class="adm-empty-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>

                        <h5>Aucune assignation</h5>
                        <p>
                            Utilisez le formulaire pour assigner
                            un professeur à un ou plusieurs parcours.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.prof-assignment-subtitle {
    margin: 2px 0 0;
    color: var(--adm-text-muted);
    font-size: .68rem;
}

.assignment-required {
    color: var(--adm-danger);
}

.professor-cell {
    display: flex;
    align-items: center;
    gap: 9px;
}

.professor-cell .adm-avatar {
    width: 33px;
    height: 33px;
    background: linear-gradient(135deg,#7C3AED,#A78BFA);
    font-size: .68rem;
}

.prof-slot-badge,
.prof-day-badge,
.prof-time-badge {
    display: inline-flex;
    min-height: 28px;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 0 9px;
    border-radius: 9px;
    font-size: .62rem;
    font-weight: 800;
    white-space: nowrap;
}

.prof-slot-badge {
    color: #DDD6FE;
    border: 1px solid rgba(139,92,246,.18);
    background: rgba(124,58,237,.10);
}

.prof-day-badge {
    color: #93C5FD;
    border: 1px solid rgba(59,130,246,.15);
    background: rgba(59,130,246,.08);
}

.prof-time-badge {
    color: #67E8F9;
    border: 1px solid rgba(34,211,238,.15);
    background: rgba(8,145,178,.08);
}

.prof-linked-schedule {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
}

.prof-no-schedule {
    color: var(--adm-text-muted);
    font-size: .61rem;
    font-style: italic;
}

.prof-assignment-actions {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
}

.prof-assignment-actions form {
    margin: 0;
}
</style>
@endsection
