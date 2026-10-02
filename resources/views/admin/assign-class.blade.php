@extends('layouts.admin')

@section('title', 'Assignation des étudiants')
@section('page_title', 'Assignation étudiants')
@section(
    'breadcrumb',
    'Étudiants → Matière → Niveau → Classe → Jour/Heure → Groupe automatique'
)

@section('content')

<div class="adm-page-header assignment-page-header">
    <div>
        <h1>
            <span class="assignment-page-icon">
                <i class="bi bi-person-check-fill"></i>
            </span>

            Assignation des étudiants
        </h1>

        <div class="subtitle">
            Choisissez la matière, le niveau et la classe, puis le jour et l’heure.
            Le groupe est calculé automatiquement à partir de l’horaire, sans limite D1-D4.
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

@if(session('info'))
    <div class="adm-alert adm-alert-info mb-3">
        {{ session('info') }}
    </div>
@endif

@if($errors->any())
    <div class="adm-alert adm-alert-danger mb-3">
        <i class="bi bi-exclamation-circle-fill"></i>

        {{ $errors->first() }}
    </div>
@endif

<div class="row g-4">
    <div class="col-xl-5">
        <div class="adm-card assignment-form-card">
            <div class="adm-card-header">
                <div>
                    <h4>
                        <i
                            class="bi bi-person-plus-fill"
                            style="color:#4ADE80;"
                        ></i>

                        Nouvelle assignation
                    </h4>

                    <p class="assignment-card-subtitle">
                        Matière → Niveau → Classe → Jour/Heure → Groupe automatique
                    </p>
                </div>
            </div>

            <div class="adm-card-body">
                <form
                    method="POST"
                    action="{{
                        route(
                            'admin.assign.class.store'
                        )
                    }}"
                    id="studentAssignmentForm"
                >
                    @csrf

                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="assignment_user_id"
                        >
                            Étudiant
                            <span class="assignment-required">*</span>
                        </label>

                        <select
                            name="user_id"
                            id="assignment_user_id"
                            class="adm-form-select
                                @error('user_id') error @enderror"
                            required
                        >
                            <option value="">
                                Sélectionner un étudiant
                            </option>

                            @foreach($students as $student)
                                @php
                                    $studentName = trim(
                                        preg_replace(
                                            '/^\s*\([^)]*\)\s*>\s*/',
                                            '',
                                            $student->name
                                        )
                                    );
                                @endphp

                                <option
                                    value="{{ $student->id }}"
                                    {{ (string) old('user_id') === (string) $student->id ? 'selected' : '' }}
                                >
                                    {{ $studentName }}
                                </option>
                            @endforeach
                        </select>

                        @error('user_id')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="assignment-hierarchy">
                        <div class="assignment-path-preview">
                            <span id="studentPathSubject">
                                Matière
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span id="studentPathLevel">
                                Niveau
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span id="studentPathClass">
                                Classe
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span id="studentPathTime">
                                Jour / heure
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span id="studentPathGroup">
                                Groupe automatique
                            </span>
                        </div>

                        <div class="assignment-step">
                            <span class="assignment-step-number">
                                1
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="assignment_subject_id"
                                >
                                    Matière
                                    <span
                                        class="assignment-required"
                                    >
                                        *
                                    </span>
                                </label>

                                <select
                                    name="subject_id"
                                    id="assignment_subject_id"
                                    class="adm-form-select
                                        @error('subject_id')
                                            error
                                        @enderror"
                                    required
                                >
                                    <option value="">
                                        Choisir une matière
                                    </option>

                                    @foreach($subjects as $subject)
                                        <option
                                            value="{{ $subject->id }}"
                                        >
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('subject_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="assignment-step">
                            <span class="assignment-step-number">
                                2
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="assignment_level_id"
                                >
                                    Niveau
                                    <span
                                        class="assignment-required"
                                    >
                                        *
                                    </span>
                                </label>

                                <select
                                    name="level_id"
                                    id="assignment_level_id"
                                    class="adm-form-select
                                        @error('level_id')
                                            error
                                        @enderror"
                                    disabled
                                    required
                                >
                                    <option value="">
                                        Choisissez d’abord
                                        une matière
                                    </option>
                                </select>

                                @error('level_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="assignment-step">
                            <span class="assignment-step-number">
                                3
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="assignment_class_id"
                                >
                                    Classe
                                    <span
                                        class="assignment-required"
                                    >
                                        *
                                    </span>
                                </label>

                                <select
                                    name="class_id"
                                    id="assignment_class_id"
                                    class="adm-form-select
                                        @error('class_id')
                                            error
                                        @enderror"
                                    disabled
                                    required
                                >
                                    <option value="">
                                        Choisissez d’abord
                                        un niveau
                                    </option>
                                </select>

                                <small class="assignment-help">
                                    Seules les classes liées à la matière
                                    et au niveau sélectionnés sont affichées.
                                </small>

                                @error('class_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div
                            class="assignment-step assignment-slot-step"
                            style="display:none;"
                            aria-hidden="true"
                        >
                            <span class="assignment-step-number">
                                4
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="assignment_class_slot_id"
                                >
                                    Groupe
                                    <span class="assignment-required">*</span>
                                </label>

                                <select
                                    name="class_slot_id"
                                    id="assignment_class_slot_id"
                                    class="adm-form-select"
                                    disabled
                                >
                                    <option value="">
                                        Choisissez d’abord une classe
                                    </option>
                                </select>

                                <div
                                    class="assignment-slot-preview"
                                    id="assignmentSlotPreview"
                                    hidden
                                >
                                    <span>
                                        <i class="bi bi-grid-1x2-fill"></i>
                                        Groupe sélectionné :
                                        <strong id="assignmentSlotCode">—</strong>
                                    </span>
                                </div>

                                <small class="assignment-help">
                                    Le groupe est calculé automatiquement à partir du jour et de l’heure.
                                    Les groupes D1, D2, D3… sont sans limite de places.
                                </small>



                                @error('class_slot_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="assignment-step assignment-time-step">
                            <span class="assignment-step-number">
                                4
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="assignment_schedule_id"
                                >
                                    Jour et heure
                                    <span class="assignment-required">*</span>
                                </label>

                                <select
                                    name="schedule_id"
                                    id="assignment_schedule_id"
                                    class="adm-form-select"
                                    style="display:none;"
                                    aria-hidden="true"
                                    tabindex="-1"
                                >
                                    <option value=""></option>
                                </select>

                                <!-- ASSIGNATION_JOUR_HEURE_LIBRE_V6_VIEW -->
                                <div
                                    class="ssa-free-time-grid"
                                >
                                    <div>
                                        <label
                                            class="adm-form-label"
                                            for="assignment_schedule_day"
                                        >
                                            Jour
                                        </label>

                                        <select
                                            id="assignment_schedule_day"
                                            class="adm-form-select"
                                            required
                                        >
                                            <option value="">
                                                Choisir un jour
                                            </option>

                                            <option value="1">Lundi</option>
                                            <option value="2">Mardi</option>
                                            <option value="3">Mercredi</option>
                                            <option value="4">Jeudi</option>
                                            <option value="5">Vendredi</option>
                                            <option value="6">Samedi</option>
                                            <option value="7">Dimanche</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label
                                            class="adm-form-label"
                                            for="assignment_schedule_hour"
                                        >
                                            Heure
                                        </label>

                                        <!-- ASSIGNATION_HEURE_MANUELLE_RESULTAT_V6_1 -->
                                        <input
                                            type="time"
                                            id="assignment_schedule_hour"
                                            class="adm-form-input"
                                            min="08:00"
                                            max="22:00"
                                            step="60"
                                            placeholder="HH:MM"
                                            autocomplete="off"
                                            required
                                        >

                                        <small class="assignment-help">
                                            Choisissez librement l’heure entre 08:00 et 22:00, à la minute près.
                                            Exemples : 08:15, 08:20, 10:10, 13:45, 15:20…
                                        </small>
                                    </div>
                                </div>

                                <div
                                    class="assignment-slot-preview"
                                    id="assignmentAutoGroupPreview"
                                    hidden
                                    style="margin-top:10px;"
                                >
                                    <span>
                                        Groupe automatique :
                                        <strong id="assignmentAutoGroupCode">—</strong>
                                    </span>
                                </div>

                                <div
                                    class="assignment-capacity-control"
                                    id="assignmentCapacityControl"
                                    hidden
                                >
                                    <div>
                                        <span class="assignment-capacity-title">
                                            <i class="bi bi-people-fill"></i>
                                            Capacité du groupe
                                        </span>

                                        <strong id="assignmentCapacityOccupancy">
                                            0 / 12
                                        </strong>
                                    </div>

                                    <div class="assignment-capacity-actions">
                                        <select
                                            id="assignmentCapacityMax"
                                            class="adm-form-select"
                                            aria-label="Capacité maximale du groupe"
                                        >
                                            <option value="10">
                                                Maximum 10
                                            </option>
                                            <option value="12">
                                                Maximum 12
                                            </option>
                                        </select>

                                        <button
                                            type="button"
                                            class="adm-btn adm-btn-ghost adm-btn-sm"
                                            id="assignmentCapacitySave"
                                        >
                                            <i class="bi bi-check2"></i>
                                            Enregistrer
                                        </button>
                                    </div>

                                    <small
                                        id="assignmentCapacityStatus"
                                        class="assignment-capacity-status"
                                    ></small>
                                </div>

                                <!-- ASSIGNATION_RESULTAT_CODE_SEUL_V6_2 -->
                                <div
                                    id="assignmentGeneratedSlotCode"
                                    class="assignment-slot-preview ssa-code-only-preview"
                                    hidden
                                    style="margin-top:10px;"
                                >
                                    <span>
                                        Résultat final :
                                        <strong>—</strong>
                                    </span>
                                </div>

                                <small class="assignment-help">
                                    Le groupe n’est plus choisi manuellement : il est déterminé automatiquement par le jour et l’heure.
                                    Exemple Arabe → Lecture & Écriture → Débutant → Dimanche :
                                    08:00 → D1ARLED1, 08:30 → D1ARLED2, 08:45 → D1ARLED3, 09:00 → D1ARLED4, puis D5, D6… sans limite fixe.
                                </small>

                                @error('schedule_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="adm-btn adm-btn-primary w-100"
                    >
                        <i class="bi bi-plus-lg"></i>
                        Assigner la matière
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
                        style="
                            color:rgba(255,255,255,0.35);
                        "
                    ></i>

                    Assignations existantes
                </h4>

                <div class="card-actions">
                    <span class="assignment-counter">
                        {{ $assignments->count() }}
                        assignation(s)
                    </span>
                </div>
            </div>

            <div class="adm-card-body p-0">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Matière</th>
                                <th>Niveau</th>
                                <th>Classe</th>
                                <th>Groupe</th>
                                <th>Créneau horaire</th>
                                <th style="text-align:right;">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($assignments as $assignment)
                                <tr>
                                    <td>
                                        <strong>
                                            {{
                                                $assignment
                                                    ->student_name
                                            }}
                                        </strong>
                                    </td>

                                    <td>
                                        <span
                                            class="adm-badge
                                                adm-badge-accent"
                                        >
                                            {{
                                                $assignment
                                                    ->subject_name
                                                ?? '—'
                                            }}
                                        </span>
                                    </td>

                                    <td>
                                        <span
                                            class="adm-badge
                                                adm-badge-success"
                                        >
                                            {{
                                                $assignment
                                                    ->level_name
                                                ?? '—'
                                            }}
                                        </span>
                                    </td>

                                    <td>
                                        <span
                                            class="adm-badge
                                                adm-badge-primary"
                                        >
                                            {{
                                                $assignment
                                                    ->class_name
                                            }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($assignment->class_slot_id)
                                            <span
                                                class="assignment-structural-slot"
                                            >
                                                <i class="bi bi-grid-1x2-fill"></i>
                                                {{ $assignment->slot_code }}
                                            </span>
                                        @else
                                            <span class="assignment-slot-missing">
                                                Groupe non défini
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($assignment->schedule_label)
                                            <span class="assignment-time-slot">
                                                <i class="bi bi-clock"></i>
                                                {{ $assignment->schedule_label }}
                                            </span>
                                        @else
                                            <span class="assignment-slot-missing">
                                                Horaire à définir
                                            </span>
                                        @endif
                                    </td>

                                    <td style="text-align:right;">
                                        <div class="assignment-actions">
                                            <a
                                                href="{{
                                                    route(
                                                        'admin.assign.class.edit',
                                                        $assignment->pivot_id
                                                    )
                                                }}"
                                                class="adm-btn
                                                    adm-btn-warning
                                                    adm-btn-sm"
                                            >
                                                <i
                                                    class="bi bi-pencil"
                                                ></i>
                                                Modifier
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{
                                                    route(
                                                        'admin.assign.class.destroy',
                                                        $assignment
                                                            ->pivot_id
                                                    )
                                                }}"
                                                onsubmit="
                                                    return confirm(
                                                        'Supprimer cette assignation ?'
                                                    )
                                                "
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="adm-btn
                                                        adm-btn-danger
                                                        adm-btn-sm"
                                                >
                                                    <i
                                                        class="bi
                                                            bi-trash"
                                                    ></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="adm-empty">
                                            <div
                                                class="adm-empty-icon"
                                            >
                                                <i
                                                    class="bi
                                                        bi-people"
                                                ></i>
                                            </div>

                                            <h5>Aucune assignation</h5>

                                            <p>
                                                Utilisez le formulaire
                                                pour assigner un étudiant.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL MODIFICATION -->
<div
    class="adm-modal-overlay"
    id="studentAssignmentModal"
    style="display:none;"
    onclick="
        if (event.target === this) {
            closeStudentAssignmentEdit();
        }
    "
>
    <div class="adm-modal">
        <form
            method="POST"
            action="{{
                route(
                    'admin.assign.class.update',
                    '__PIVOT_ID__'
                )
            }}"
            data-action-template="{{
                route(
                    'admin.assign.class.update',
                    '__PIVOT_ID__'
                )
            }}"
            id="studentAssignmentEditForm"
        >
            @csrf
            @method('PATCH')

            <div class="adm-modal-header">
                <h5>
                    <i class="bi bi-pencil"></i>
                    Modifier l’assignation
                </h5>

                <button
                    type="button"
                    class="adm-modal-close"
                    onclick="closeStudentAssignmentEdit()"
                >
                    &times;
                </button>
            </div>

            <div class="adm-modal-body">
                <div class="adm-form-group">
                    <label
                        class="adm-form-label"
                        for="edit_assignment_user_id"
                    >
                        Étudiant
                    </label>

                    <select
                        name="user_id"
                        id="edit_assignment_user_id"
                        class="adm-form-select"
                        required
                    >
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="adm-form-group">
                    <label
                        class="adm-form-label"
                        for="edit_assignment_subject_id"
                    >
                        Matière
                    </label>

                    <select
                        name="subject_id"
                        id="edit_assignment_subject_id"
                        class="adm-form-select"
                        required
                    >
                        <option value="">
                            Choisir une matière
                        </option>

                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="adm-form-group">
                    <label
                        class="adm-form-label"
                        for="edit_assignment_level_id"
                    >
                        Niveau
                    </label>

                    <select
                        name="level_id"
                        id="edit_assignment_level_id"
                        class="adm-form-select"
                        disabled
                        required
                    >
                        <option value="">
                            Choisissez d’abord une matière
                        </option>
                    </select>
                </div>

                <div class="adm-form-group">
                    <label
                        class="adm-form-label"
                        for="edit_assignment_class_id"
                    >
                        Classe
                    </label>

                    <select
                        name="class_id"
                        id="edit_assignment_class_id"
                        class="adm-form-select"
                        disabled
                        required
                    >
                        <option value="">
                            Choisissez d’abord un niveau
                        </option>
                    </select>
                </div>

                <div class="adm-form-group">
                    <label
                        class="adm-form-label"
                        for="edit_assignment_class_slot_id"
                    >
                        Groupe
                    </label>

                    <select
                        name="class_slot_id"
                        id="edit_assignment_class_slot_id"
                        class="adm-form-select"
                        disabled
                        required
                    >
                        <option value="">
                            Choisissez d’abord une classe
                        </option>
                    </select>

                    <small class="assignment-help">
                        Groupe pédagogique : A1, A2, D1, I1…
                    </small>
                </div>

                <div class="adm-form-group">
                    <label
                        class="adm-form-label"
                        for="edit_assignment_schedule_id"
                    >
                        Créneau horaire
                        <span class="assignment-optional">
                            (optionnel)
                        </span>
                    </label>

                    <select
                        name="schedule_id"
                        id="edit_assignment_schedule_id"
                        class="adm-form-select"
                        disabled
                    >
                        <option value="">
                            Choisissez d’abord une matière
                        </option>
                    </select>

                    <small class="assignment-help">
                        Créneaux de la matière sélectionnée uniquement, du lundi au dimanche entre 08:00 et 23:00.
                    </small>
                </div>
            </div>

            <div class="adm-modal-footer">
                <button
                    type="button"
                    class="adm-btn adm-btn-ghost"
                    onclick="closeStudentAssignmentEdit()"
                >
                    Annuler
                </button>

                <button
                    type="submit"
                    class="adm-btn adm-btn-primary"
                >
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.assignment-page-header h1 {
    display: flex;
    align-items: center;
    gap: 10px;
}

.assignment-page-icon {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border-radius: 13px;
    color: #4ADE80;
    background: rgba(34,197,94,0.11);
}

.assignment-card-subtitle,
.assignment-counter {
    margin: 2px 0 0;
    color: var(--adm-text-muted);
    font-size: 0.68rem;
}

.assignment-hierarchy {
    margin-bottom: 1rem;
    padding: 0.9rem;
    border: 1px solid rgba(96,165,250,0.11);
    border-radius: 15px;
    background: rgba(37,99,235,0.035);
}

.assignment-path-preview {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
    margin-bottom: 0.8rem;
    padding: 8px 10px;
    border: 1px solid rgba(96,165,250,0.1);
    border-radius: 11px;
    color: rgba(255,255,255,0.45);
    background: rgba(255,255,255,0.02);
    font-size: 0.64rem;
}

.assignment-path-preview span.is-selected {
    color: #93C5FD;
    font-weight: 750;
}

.assignment-path-preview i {
    color: rgba(255,255,255,0.2);
    font-size: 0.55rem;
}

.assignment-step {
    position: relative;
    margin-bottom: 0.7rem;
    padding: 0.75rem;
    border: 1px solid rgba(255,255,255,0.045);
    border-radius: 12px;
    background: rgba(7,15,30,0.27);
}

.assignment-step:last-child {
    margin-bottom: 0;
}

.assignment-step-number {
    position: absolute;
    top: -8px;
    right: 10px;
    width: 22px;
    height: 22px;
    display: grid;
    place-items: center;
    border: 2px solid #111C30;
    border-radius: 50%;
    color: #ffffff;
    background:
        linear-gradient(135deg,#2563EB,#7C3AED);
    font-size: 0.61rem;
    font-weight: 800;
}

.assignment-required {
    color: var(--adm-danger);
}

.assignment-help {
    display: block;
    margin-top: 5px;
    color: var(--adm-text-muted);
    font-size: 0.61rem;
}

.assignment-actions {
    display: flex;
    justify-content: flex-end;
    gap: 6px;
}

.assignment-actions form {
    margin: 0;
}

.assignment-slot-step {
    border-color: rgba(34,197,94,0.14);
    background: rgba(34,197,94,0.035);
}

.assignment-slot-preview {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 7px;
    margin-top: 8px;
}

.assignment-slot-preview[hidden] {
    display: none;
}

.assignment-slot-preview span {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 6px;
    padding: 7px 8px;
    color: #94A3B8;
    border: 1px solid rgba(148,163,184,0.10);
    border-radius: 9px;
    background: rgba(255,255,255,0.025);
    font-size: 0.58rem;
}

.assignment-slot-preview span i {
    color: #60A5FA;
}

.assignment-slot-preview strong {
    overflow: hidden;
    color: #E2E8F0;
    font-size: 0.59rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.assignment-table-slot {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
}

.assignment-table-slot strong,
.assignment-table-slot span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.assignment-table-slot strong {
    color: #BFDBFE;
    font-size: 0.64rem;
}

.assignment-table-slot span {
    color: #E2E8F0;
    font-size: 0.62rem;
}

.assignment-table-slot small {
    color: #64748B;
    font-size: 0.54rem;
}

.assignment-slot-missing {
    display: inline-flex;
    padding: 4px 7px;
    color: #FBBF24;
    border-radius: 8px;
    background: rgba(245,158,11,0.08);
    font-size: 0.56rem;
    font-weight: 750;
}

.assignment-help a {
    color: #93C5FD;
    font-weight: 700;
    text-decoration: none;
}

.assignment-capacity-control {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 10px;
    align-items: center;
    margin-top: 10px;
    padding: 10px;
    border: 1px solid rgba(96,165,250,0.14);
    border-radius: 11px;
    background: rgba(37,99,235,0.055);
}

.assignment-capacity-control[hidden] {
    display: none;
}

.assignment-capacity-title {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #93C5FD;
    font-size: .59rem;
    font-weight: 750;
}

.assignment-capacity-control strong {
    display: block;
    margin-top: 3px;
    color: #E2E8F0;
    font-size: .72rem;
}

.assignment-capacity-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.assignment-capacity-actions .adm-form-select {
    min-width: 118px;
    padding-top: 6px;
    padding-bottom: 6px;
    font-size: .61rem;
}

.assignment-capacity-status {
    grid-column: 1 / -1;
    min-height: 14px;
    color: #94A3B8;
    font-size: .56rem;
}

.assignment-capacity-status.is-success {
    color: #86EFAC;
}

.assignment-capacity-status.is-error {
    color: #FCA5A5;
}

@media (max-width: 720px) {
    .assignment-capacity-control {
        grid-template-columns: 1fr;
    }

    .assignment-capacity-actions {
        justify-content: stretch;
    }

    .assignment-capacity-actions .adm-form-select,
    .assignment-capacity-actions .adm-btn {
        flex: 1;
    }
}

@media (max-width: 720px) {
    .assignment-slot-preview {
        grid-template-columns: 1fr;
    }
}


.assignment-structural-slot {
    display: inline-flex;
    min-width: 48px;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 6px 10px;
    color: #DBEAFE;
    border: 1px solid rgba(96,165,250,0.20);
    border-radius: 10px;
    background: rgba(37,99,235,0.10);
    font-size: 0.64rem;
    font-weight: 850;
    letter-spacing: 0.04em;
}

.assignment-optional {
    color: var(--adm-text-muted);
    font-size: .56rem;
    font-weight: 600;
}

.assignment-time-step {
    border-color: rgba(168,85,247,0.14);
    background: rgba(126,34,206,0.035);
}

.assignment-time-slot {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 9px;
    color: #C4B5FD;
    border: 1px solid rgba(167,139,250,0.18);
    border-radius: 9px;
    background: rgba(124,58,237,0.08);
    font-size: .60rem;
    font-weight: 750;
    white-space: nowrap;
}
</style>

<!-- STUDENT_ALL_DAYS_TIME_SLOTS_V2_VIEW -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const hierarchy = @json($assignmentHierarchy);
    const scheduleMap = @json($studentScheduleMap ?? []);

    const createOption = (
        value,
        label,
        selectedValue = '',
        dataset = {}
    ) => {
        const option = document.createElement('option');

        option.value = String(value);
        option.textContent = label;
        option.selected =
            String(value) === String(selectedValue);

        Object.entries(dataset).forEach(([key, value]) => {
            option.dataset[key] = value ?? '';
        });

        return option;
    };

    const resetSelect = (
        select,
        placeholder,
        disabled = true
    ) => {
        select.replaceChildren(
            createOption('', placeholder)
        );

        select.disabled = disabled;
    };

    const findSubject = subjectId =>
        hierarchy.find(
            subject =>
                String(subject.id) === String(subjectId)
        );

    const findLevel = (subject, levelId) =>
        subject?.levels?.find(
            level =>
                String(level.id) === String(levelId)
        ) || null;

    const findClass = (
        subject,
        level,
        classId
    ) =>
        level?.classes?.find(
            classRoom =>
                String(classRoom.id) === String(classId)
        ) || null;

    /*
     * ASSIGNATION_CODES_7J_10_CRENEAUX_V5
     * D1ARD1 = D + 1 + AR + D + 1
     */
    const buildFullStudentSlotCode = (
        item,
        timeSelect
    ) => {
        const isMain =
            timeSelect.id
            === 'assignment_schedule_id';

        const classSelect =
            document.getElementById(
                isMain
                    ? 'assignment_class_id'
                    : 'edit_assignment_class_id'
            );

        const groupSelect =
            document.getElementById(
                isMain
                    ? 'assignment_class_slot_id'
                    : 'edit_assignment_class_slot_id'
            );

        const classText =
            classSelect?.value
                ? (
                    classSelect.options[
                        classSelect.selectedIndex
                    ]?.textContent
                    || ''
                ).trim()
                : '';

        const normalizedClass =
            classText
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g,'')
                .toLowerCase();

        let classCode = '';

        if (normalizedClass.includes('debut')) {
            classCode = 'D';
        } else if (normalizedClass.includes('inter')) {
            classCode = 'I';
        } else if (normalizedClass.includes('avance')) {
            classCode = 'A';
        } else if (normalizedClass) {
            classCode =
                normalizedClass
                    .replace(/[^a-z0-9]/g,'')
                    .charAt(0)
                    .toUpperCase();
        }

        const groupOption =
            groupSelect?.value
                ? groupSelect.options[
                    groupSelect.selectedIndex
                ]
                : null;

        const groupCode =
            groupOption?.dataset?.code
            || '';

        const match =
            String(groupCode).match(/(\d+)$/);

        const groupNumber =
            match
                ? match[1]
                : '';

        if (!classCode || !groupNumber) {
            return String(item.base_code || item.code || '');
        }

        return (
            String(item.day_code || '')
            + String(item.slot_number || '')
            + String(item.subject_code || '')
            + classCode
            + groupNumber
        );
    };

    /*
     * Groupe et créneau restent indépendants.
     * Le groupe intervient seulement dans le code final.
     */
    const fillTimeSlots = (
        subjectSelect,
        timeSelect,
        selectedTimeId = ''
    ) => {
        const subjectId = String(
            subjectSelect.value || ''
        );

        if (!subjectId) {
            resetSelect(
                timeSelect,
                'Choisissez d’abord une matière',
                true
            );
            return;
        }

        const times =
            scheduleMap[subjectId] || [];

        timeSelect.replaceChildren(
            createOption(
                '',
                times.length
                    ? 'Horaire à définir'
                    : 'Aucun créneau planifié pour cette matière'
            )
        );

        times.forEach(item => {
            const fullCode =
                buildFullStudentSlotCode(
                    item,
                    timeSelect
                );

            const label =
                fullCode
                + ' — '
                + String(item.day || '')
                + ' · '
                + String(item.time || '');

            timeSelect.appendChild(
                createOption(
                    item.id,
                    label,
                    selectedTimeId,
                    {
                        code: fullCode,
                        day: item.day || '',
                        time: item.time || '',
                    }
                )
            );
        });

        /*
         * Même sans créneau existant, l'assignation au Groupe
         * reste possible avec "Horaire à définir".
         */
        timeSelect.disabled = false;
        timeSelect.value =
            selectedTimeId
                ? String(selectedTimeId)
                : '';

        if (
            selectedTimeId
            && !timeSelect.value
        ) {
            timeSelect.value = '';
        }
    };

    const fillGroups = (
        subjectSelect,
        levelSelect,
        classSelect,
        groupSelect,
        timeSelect,
        selectedGroupId = '',
        selectedTimeId = ''
    ) => {
        const subject =
            findSubject(subjectSelect.value);

        const level =
            findLevel(
                subject,
                levelSelect.value
            );

        const classRoom =
            findClass(
                subject,
                level,
                classSelect.value
            );

        resetSelect(
            groupSelect,
            classRoom
                ? 'Sélectionner un groupe'
                : 'Choisissez d’abord une classe',
            !classRoom
        );

        if (!classRoom) {
            fillTimeSlots(
                subjectSelect,
                timeSelect,
                selectedTimeId
            );
            return;
        }

        const groups =
            classRoom.slots || [];

        if (!groups.length) {
            resetSelect(
                groupSelect,
                'Aucun groupe pour cette classe',
                true
            );

            fillTimeSlots(
                subjectSelect,
                timeSelect,
                selectedTimeId
            );
            return;
        }

        groups.forEach(group => {
            const code =
                group.code
                || group.name
                || 'Groupe';

            const option =
                createOption(
                    group.id,
                    code,
                    selectedGroupId,
                    {
                        code: code,
                        unlimited: '1',
                    }
                );

            // Aucun groupe étudiant n'est bloqué par une capacité maximale.
            option.disabled = false;

            groupSelect.appendChild(
                option
            );
        });

        groupSelect.disabled = false;
        groupSelect.value =
            selectedGroupId
                ? String(selectedGroupId)
                : '';

        fillTimeSlots(
            subjectSelect,
            timeSelect,
            selectedTimeId
        );
    };

    const fillClasses = (
        subjectSelect,
        levelSelect,
        classSelect,
        groupSelect,
        timeSelect,
        selectedClassId = '',
        selectedGroupId = '',
        selectedTimeId = ''
    ) => {
        const subject =
            findSubject(subjectSelect.value);

        const level =
            findLevel(
                subject,
                levelSelect.value
            );

        resetSelect(
            classSelect,
            level
                ? 'Sélectionner une classe'
                : 'Choisissez d’abord un niveau',
            !level
        );

        resetSelect(
            groupSelect,
            'Choisissez d’abord une classe',
            true
        );

        fillTimeSlots(
            subjectSelect,
            timeSelect,
            selectedTimeId
        );

        if (!level) {
            return;
        }

        (level.classes || []).forEach(classRoom => {
            classSelect.appendChild(
                createOption(
                    classRoom.id,
                    classRoom.name,
                    selectedClassId
                )
            );
        });

        classSelect.disabled = false;
        classSelect.value =
            selectedClassId
                ? String(selectedClassId)
                : '';

        if (selectedClassId) {
            fillGroups(
                subjectSelect,
                levelSelect,
                classSelect,
                groupSelect,
                timeSelect,
                selectedGroupId,
                selectedTimeId
            );
        }
    };

    const fillLevels = (
        subjectSelect,
        levelSelect,
        classSelect,
        groupSelect,
        timeSelect,
        selectedLevelId = '',
        selectedClassId = '',
        selectedGroupId = '',
        selectedTimeId = ''
    ) => {
        const subject =
            findSubject(subjectSelect.value);

        resetSelect(
            levelSelect,
            subject
                ? 'Sélectionner un niveau'
                : 'Choisissez d’abord une matière',
            !subject
        );

        resetSelect(
            classSelect,
            'Choisissez d’abord un niveau',
            true
        );

        resetSelect(
            groupSelect,
            'Choisissez d’abord une classe',
            true
        );

        fillTimeSlots(
            subjectSelect,
            timeSelect,
            selectedTimeId
        );

        if (!subject) {
            return;
        }

        (subject.levels || []).forEach(level => {
            levelSelect.appendChild(
                createOption(
                    level.id,
                    level.name,
                    selectedLevelId
                )
            );
        });

        levelSelect.disabled = false;

        if (selectedLevelId) {
            levelSelect.value =
                String(selectedLevelId);

            fillClasses(
                subjectSelect,
                levelSelect,
                classSelect,
                groupSelect,
                timeSelect,
                selectedClassId,
                selectedGroupId,
                selectedTimeId
            );
        }
    };

    /*
     * FORMULAIRE PRINCIPAL
     */
    const mainSubject =
        document.getElementById(
            'assignment_subject_id'
        );

    const mainLevel =
        document.getElementById(
            'assignment_level_id'
        );

    const mainClass =
        document.getElementById(
            'assignment_class_id'
        );

    const mainGroup =
        document.getElementById(
            'assignment_class_slot_id'
        );

    const mainTime =
        document.getElementById(
            'assignment_schedule_id'
        );

    const pathSubject =
        document.getElementById(
            'studentPathSubject'
        );

    const pathLevel =
        document.getElementById(
            'studentPathLevel'
        );

    const pathClass =
        document.getElementById(
            'studentPathClass'
        );

    const pathGroup =
        document.getElementById(
            'studentPathGroup'
        );

    const pathTime =
        document.getElementById(
            'studentPathTime'
        );

    const slotPreview =
        document.getElementById(
            'assignmentSlotPreview'
        );

    const slotCode =
        document.getElementById(
            'assignmentSlotCode'
        );

    /*
     * STUDENT_GROUP_CAPACITY_V1_JS
     */
    const capacityControl =
        document.getElementById(
            'assignmentCapacityControl'
        );

    const capacityOccupancy =
        document.getElementById(
            'assignmentCapacityOccupancy'
        );

    const capacityMax =
        document.getElementById(
            'assignmentCapacityMax'
        );

    const capacitySave =
        document.getElementById(
            'assignmentCapacitySave'
        );

    const capacityStatus =
        document.getElementById(
            'assignmentCapacityStatus'
        );

    const capacityUrlTemplate =
        @json(
            route(
                'admin.assign.class.capacity.update',
                '__SLOT_ID__'
            )
        );

    const csrfToken =
        @json(csrf_token());

    const currentMainGroup = () => {
        const subject =
            findSubject(
                mainSubject.value
            );

        const level =
            findLevel(
                subject,
                mainLevel.value
            );

        const classRoom =
            findClass(
                subject,
                level,
                mainClass.value
            );

        return (
            classRoom?.slots || []
        ).find(
            group =>
                String(group.id)
                === String(
                    mainGroup.value
                )
        ) || null;
    };

    const updateCapacityControl = () => {
        // STUDENT_GROUP_UNLIMITED_CAPACITY_V1
        // La capacité n'est plus une règle métier sur l'assignation étudiant.
        if (capacityControl) {
            capacityControl.hidden = true;
        }

        if (capacityStatus) {
            capacityStatus.textContent = '';
        }
    };

    const updatePath = () => {
        const values = [
            [mainSubject, pathSubject, 'Matière'],
            [mainLevel, pathLevel, 'Niveau'],
            [mainClass, pathClass, 'Classe'],
            [mainGroup, pathGroup, 'Groupe'],
            [mainTime, pathTime, 'Créneau horaire'],
        ];

        values.forEach(
            ([select, target, fallback]) => {
                target.textContent =
                    select?.value
                        ? select.options[
                            select.selectedIndex
                        ].textContent
                        : fallback;

                target.classList.toggle(
                    'is-selected',
                    Boolean(select?.value)
                );
            }
        );

        const option =
            mainGroup.options[
                mainGroup.selectedIndex
            ];

        const hasGroup =
            Boolean(mainGroup.value);

        slotPreview.hidden =
            !hasGroup;

        slotCode.textContent =
            hasGroup && option
                ? (
                    option.dataset.code
                    || option.textContent
                    || '—'
                )
                : '—';

        updateCapacityControl();
    };

    mainSubject.addEventListener(
        'change',
        () => {
            fillLevels(
                mainSubject,
                mainLevel,
                mainClass,
                mainGroup,
                mainTime
            );
            updatePath();
        }
    );

    mainLevel.addEventListener(
        'change',
        () => {
            fillClasses(
                mainSubject,
                mainLevel,
                mainClass,
                mainGroup,
                mainTime
            );
            updatePath();
        }
    );

    mainClass.addEventListener(
        'change',
        () => {
            fillGroups(
                mainSubject,
                mainLevel,
                mainClass,
                mainGroup,
                mainTime
            );
            updatePath();
        }
    );

    /*
     * Le changement de Groupe recalcule seulement le code visible.
     */
    mainGroup.addEventListener(
        'change',
        () => {
            const currentTime =
                mainTime.value;

            fillTimeSlots(
                mainSubject,
                mainTime,
                currentTime
            );

            updatePath();
        }
    );

    mainTime.addEventListener(
        'change',
        updatePath
    );

    capacitySave.addEventListener(
        'click',
        async () => {
            const group =
                currentMainGroup();

            if (!group) {
                return;
            }

            capacitySave.disabled = true;
            capacityStatus.textContent =
                'Enregistrement…';
            capacityStatus.classList.remove(
                'is-success',
                'is-error'
            );

            try {
                const response =
                    await fetch(
                        capacityUrlTemplate
                            .replace(
                                '__SLOT_ID__',
                                String(group.id)
                            ),
                        {
                            method: 'PATCH',
                            credentials:
                                'same-origin',
                            headers: {
                                'Accept':
                                    'application/json',
                                'Content-Type':
                                    'application/json',
                                'X-CSRF-TOKEN':
                                    csrfToken,
                            },
                            body:
                                JSON.stringify({
                                    max_students:
                                        Number(
                                            capacityMax.value
                                        ),
                                }),
                        }
                    );

                const data =
                    await response.json();

                if (!response.ok) {
                    const message =
                        data?.errors
                            ?.max_students
                            ?.[0]
                        || data?.message
                        || 'Impossible de modifier la capacité.';

                    throw new Error(
                        message
                    );
                }

                group.max_students =
                    Number(
                        data.max_students
                    );

                group.current_count =
                    Number(
                        data.current_count
                    );

                group.available_places =
                    Number(
                        data.available_places
                    );

                group.is_full =
                    Boolean(
                        data.is_full
                    );

                const selectedGroupId =
                    String(
                        mainGroup.value
                    );

                fillGroups(
                    mainSubject,
                    mainLevel,
                    mainClass,
                    mainGroup,
                    mainTime,
                    selectedGroupId,
                    String(
                        mainTime.value
                        || ''
                    )
                );

                capacityStatus.textContent =
                    data.message;

                capacityStatus.classList.add(
                    'is-success'
                );

                updatePath();
            } catch (error) {
                capacityStatus.textContent =
                    error?.message
                    || 'Erreur lors de l’enregistrement.';

                capacityStatus.classList.add(
                    'is-error'
                );
            } finally {
                capacitySave.disabled = false;
            }
        }
    );

    const oldSubjectId =
        @json((string) old('subject_id', ''));

    const oldLevelId =
        @json((string) old('level_id', ''));

    const oldClassId =
        @json((string) old('class_id', ''));

    const oldGroupId =
        @json((string) old('class_slot_id', ''));

    const oldTimeId =
        @json((string) old('schedule_id', ''));

    if (oldSubjectId) {
        mainSubject.value =
            oldSubjectId;

        fillLevels(
            mainSubject,
            mainLevel,
            mainClass,
            mainGroup,
            mainTime,
            oldLevelId,
            oldClassId,
            oldGroupId,
            oldTimeId
        );
    } else {
        fillTimeSlots(
            mainSubject,
            mainTime
        );
    }

    updatePath();

    /*
     * MODAL ÉDITION
     */
    const editSubject =
        document.getElementById(
            'edit_assignment_subject_id'
        );

    const editLevel =
        document.getElementById(
            'edit_assignment_level_id'
        );

    const editClass =
        document.getElementById(
            'edit_assignment_class_id'
        );

    const editGroup =
        document.getElementById(
            'edit_assignment_class_slot_id'
        );

    const editTime =
        document.getElementById(
            'edit_assignment_schedule_id'
        );

    editSubject.addEventListener(
        'change',
        () => {
            fillLevels(
                editSubject,
                editLevel,
                editClass,
                editGroup,
                editTime
            );
        }
    );

    editLevel.addEventListener(
        'change',
        () => {
            fillClasses(
                editSubject,
                editLevel,
                editClass,
                editGroup,
                editTime
            );
        }
    );

    editClass.addEventListener(
        'change',
        () => {
            fillGroups(
                editSubject,
                editLevel,
                editClass,
                editGroup,
                editTime
            );
        }
    );

    /*
     * Le Groupe ne modifie pas le créneau étudiant.
     */
    editGroup.addEventListener(
        'change',
        () => {}
    );

    window.openStudentAssignmentEdit = (
        userId,
        subjectId,
        levelId,
        classId,
        groupId,
        timeId,
        pivotId
    ) => {
        document.getElementById(
            'edit_assignment_user_id'
        ).value = String(userId);

        editSubject.value =
            subjectId
                ? String(subjectId)
                : '';

        fillLevels(
            editSubject,
            editLevel,
            editClass,
            editGroup,
            editTime,
            levelId
                ? String(levelId)
                : '',
            classId
                ? String(classId)
                : '',
            groupId
                ? String(groupId)
                : '',
            timeId
                ? String(timeId)
                : ''
        );

        const form =
            document.getElementById(
                'studentAssignmentEditForm'
            );

        form.action =
            form.dataset.actionTemplate.replace(
                '__PIVOT_ID__',
                String(pivotId)
            );

        document.getElementById(
            'studentAssignmentModal'
        ).style.display = 'flex';

        document.body.style.overflow =
            'hidden';
    };

    window.closeStudentAssignmentEdit = () => {
        document.getElementById(
            'studentAssignmentModal'
        ).style.display = 'none';

        document.body.style.overflow =
            '';
    };
});
</script>

<!-- FIX_ASSIGN_CLASS_MODIFIER_V1 -->
<script>
(() => {
    /*
     * Correctif indépendant du script principal.
     *
     * Il est volontairement déclaré dans un second <script>.
     * Ainsi, même si le script principal rencontre une erreur
     * JavaScript avant de créer openStudentAssignmentEdit(),
     * le bouton Modifier reste fonctionnel.
     */

    const hierarchy =
        @json($assignmentHierarchy ?? []);

    const scheduleMap =
        @json($studentScheduleMap ?? []);

    const byId = id =>
        document.getElementById(id);

    const createOption = (
        value,
        label,
        selectedValue = '',
        disabled = false,
        dataset = {}
    ) => {
        const option =
            document.createElement(
                'option'
            );

        option.value =
            String(value ?? '');

        option.textContent =
            String(label ?? '');

        option.selected =
            String(value ?? '')
            === String(
                selectedValue ?? ''
            );

        option.disabled =
            Boolean(disabled);

        Object.entries(
            dataset || {}
        ).forEach(
            ([key, value]) => {
                option.dataset[key] =
                    value ?? '';
            }
        );

        return option;
    };

    const resetSelect = (
        select,
        placeholder,
        disabled = true
    ) => {
        if (!select) {
            return;
        }

        select.replaceChildren(
            createOption(
                '',
                placeholder
            )
        );

        select.disabled =
            disabled;
    };

    const findSubject =
        subjectId =>
            hierarchy.find(
                subject =>
                    String(subject.id)
                    === String(subjectId)
            ) || null;

    const findLevel = (
        subject,
        levelId
    ) =>
        subject?.levels?.find(
            level =>
                String(level.id)
                === String(levelId)
        ) || null;

    const findClass = (
        level,
        classId
    ) =>
        level?.classes?.find(
            classRoom =>
                String(classRoom.id)
                === String(classId)
        ) || null;

    const fillTimeSlots = (
        subjectSelect,
        timeSelect,
        selectedTimeId = ''
    ) => {
        if (
            !subjectSelect
            || !timeSelect
        ) {
            return;
        }

        const subjectId =
            String(
                subjectSelect.value
                || ''
            );

        if (!subjectId) {
            resetSelect(
                timeSelect,
                'Choisissez d’abord une matière',
                true
            );
            return;
        }

        const times =
            scheduleMap[subjectId]
            || [];

        timeSelect.replaceChildren(
            createOption(
                '',
                times.length
                    ? 'Horaire à définir'
                    : 'Aucun créneau disponible'
            )
        );

        times.forEach(item => {
            timeSelect.appendChild(
                createOption(
                    item.id,
                    item.label,
                    selectedTimeId,
                    false,
                    {
                        code:
                            item.code || '',
                        day:
                            item.day || '',
                        time:
                            item.time || '',
                    }
                )
            );
        });

        timeSelect.disabled = false;

        timeSelect.value =
            selectedTimeId
                ? String(
                    selectedTimeId
                )
                : '';

        if (
            selectedTimeId
            && !timeSelect.value
        ) {
            timeSelect.value = '';
        }
    };

    const fillGroups = (
        subjectSelect,
        levelSelect,
        classSelect,
        groupSelect,
        timeSelect,
        selectedGroupId = '',
        selectedTimeId = ''
    ) => {
        const subject =
            findSubject(
                subjectSelect?.value
            );

        const level =
            findLevel(
                subject,
                levelSelect?.value
            );

        const classRoom =
            findClass(
                level,
                classSelect?.value
            );

        resetSelect(
            groupSelect,
            classRoom
                ? 'Sélectionner un groupe'
                : 'Choisissez d’abord une classe',
            !classRoom
        );

        if (!classRoom) {
            fillTimeSlots(
                subjectSelect,
                timeSelect,
                selectedTimeId
            );
            return;
        }

        const groups =
            classRoom.slots || [];

        if (!groups.length) {
            resetSelect(
                groupSelect,
                'Aucun groupe pour cette classe',
                true
            );

            fillTimeSlots(
                subjectSelect,
                timeSelect,
                selectedTimeId
            );
            return;
        }

        groups.forEach(group => {
            const code =
                group.code
                || group.name
                || 'Groupe';

            groupSelect.appendChild(
                createOption(
                    group.id,
                    code,
                    selectedGroupId,
                    false,
                    {
                        code: code,
                        unlimited: '1',
                    }
                )
            );
        });

        groupSelect.disabled = false;

        groupSelect.value =
            selectedGroupId
                ? String(
                    selectedGroupId
                )
                : '';

        fillTimeSlots(
            subjectSelect,
            timeSelect,
            selectedTimeId
        );
    };

    const fillClasses = (
        subjectSelect,
        levelSelect,
        classSelect,
        groupSelect,
        timeSelect,
        selectedClassId = '',
        selectedGroupId = '',
        selectedTimeId = ''
    ) => {
        const subject =
            findSubject(
                subjectSelect?.value
            );

        const level =
            findLevel(
                subject,
                levelSelect?.value
            );

        resetSelect(
            classSelect,
            level
                ? 'Sélectionner une classe'
                : 'Choisissez d’abord un niveau',
            !level
        );

        resetSelect(
            groupSelect,
            'Choisissez d’abord une classe',
            true
        );

        fillTimeSlots(
            subjectSelect,
            timeSelect,
            selectedTimeId
        );

        if (!level) {
            return;
        }

        (level.classes || [])
            .forEach(
                classRoom => {
                    classSelect.appendChild(
                        createOption(
                            classRoom.id,
                            classRoom.name,
                            selectedClassId
                        )
                    );
                }
            );

        classSelect.disabled =
            false;

        classSelect.value =
            selectedClassId
                ? String(
                    selectedClassId
                )
                : '';

        if (selectedClassId) {
            fillGroups(
                subjectSelect,
                levelSelect,
                classSelect,
                groupSelect,
                timeSelect,
                selectedGroupId,
                selectedTimeId
            );
        }
    };

    const fillLevels = (
        subjectSelect,
        levelSelect,
        classSelect,
        groupSelect,
        timeSelect,
        selectedLevelId = '',
        selectedClassId = '',
        selectedGroupId = '',
        selectedTimeId = ''
    ) => {
        const subject =
            findSubject(
                subjectSelect?.value
            );

        resetSelect(
            levelSelect,
            subject
                ? 'Sélectionner un niveau'
                : 'Choisissez d’abord une matière',
            !subject
        );

        resetSelect(
            classSelect,
            'Choisissez d’abord un niveau',
            true
        );

        resetSelect(
            groupSelect,
            'Choisissez d’abord une classe',
            true
        );

        fillTimeSlots(
            subjectSelect,
            timeSelect,
            selectedTimeId
        );

        if (!subject) {
            return;
        }

        (subject.levels || [])
            .forEach(
                level => {
                    levelSelect.appendChild(
                        createOption(
                            level.id,
                            level.name,
                            selectedLevelId
                        )
                    );
                }
            );

        levelSelect.disabled =
            false;

        levelSelect.value =
            selectedLevelId
                ? String(
                    selectedLevelId
                )
                : '';

        if (selectedLevelId) {
            fillClasses(
                subjectSelect,
                levelSelect,
                classSelect,
                groupSelect,
                timeSelect,
                selectedClassId,
                selectedGroupId,
                selectedTimeId
            );
        }
    };

    const elements = () => ({
        modal:
            byId(
                'studentAssignmentModal'
            ),
        form:
            byId(
                'studentAssignmentEditForm'
            ),
        user:
            byId(
                'edit_assignment_user_id'
            ),
        subject:
            byId(
                'edit_assignment_subject_id'
            ),
        level:
            byId(
                'edit_assignment_level_id'
            ),
        classRoom:
            byId(
                'edit_assignment_class_id'
            ),
        group:
            byId(
                'edit_assignment_class_slot_id'
            ),
        time:
            byId(
                'edit_assignment_schedule_id'
            ),
    });

    /*
     * Déclaration IMMÉDIATE de la fonction globale utilisée
     * par onclick="openStudentAssignmentEdit(...)".
     */
    window.openStudentAssignmentEdit = (
        userId,
        subjectId,
        levelId,
        classId,
        groupId,
        timeId,
        pivotId
    ) => {
        const el =
            elements();

        if (
            !el.modal
            || !el.form
            || !el.user
            || !el.subject
            || !el.level
            || !el.classRoom
            || !el.group
            || !el.time
        ) {
            console.error(
                '[SSA] Modal de modification incomplet.'
            );

            alert(
                'Le formulaire de modification ne peut pas être ouvert. Rechargez la page.'
            );

            return;
        }

        el.user.value =
            String(userId ?? '');

        el.subject.value =
            subjectId
                ? String(subjectId)
                : '';

        fillLevels(
            el.subject,
            el.level,
            el.classRoom,
            el.group,
            el.time,
            levelId
                ? String(levelId)
                : '',
            classId
                ? String(classId)
                : '',
            groupId
                ? String(groupId)
                : '',
            timeId
                ? String(timeId)
                : ''
        );

        const template =
            el.form.dataset
                .actionTemplate
            || '';

        if (
            template
            && pivotId
        ) {
            el.form.action =
                template.replace(
                    '__PIVOT_ID__',
                    String(pivotId)
                );
        }

        el.modal.style.display =
            'flex';

        el.modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.style.overflow =
            'hidden';

        /*
         * Focus sur le premier champ du modal pour confirmer
         * visuellement son ouverture.
         */
        window.setTimeout(
            () => {
                el.user.focus();
            },
            20
        );
    };

    window.closeStudentAssignmentEdit = () => {
        const modal =
            byId(
                'studentAssignmentModal'
            );

        if (modal) {
            modal.style.display =
                'none';

            modal.setAttribute(
                'aria-hidden',
                'true'
            );
        }

        document.body.style.overflow =
            '';
    };

    /*
     * Les événements du modal sont aussi installés ici,
     * indépendamment du script principal.
     */
    const bindEditEvents = () => {
        const el =
            elements();

        if (
            !el.subject
            || el.subject.dataset
                .ssaEditFallbackBound
                === '1'
        ) {
            return;
        }

        el.subject.dataset
            .ssaEditFallbackBound =
                '1';

        el.subject.addEventListener(
            'change',
            () => {
                fillLevels(
                    el.subject,
                    el.level,
                    el.classRoom,
                    el.group,
                    el.time
                );
            }
        );

        el.level.addEventListener(
            'change',
            () => {
                fillClasses(
                    el.subject,
                    el.level,
                    el.classRoom,
                    el.group,
                    el.time
                );
            }
        );

        el.classRoom.addEventListener(
            'change',
            () => {
                fillGroups(
                    el.subject,
                    el.level,
                    el.classRoom,
                    el.group,
                    el.time
                );
            }
        );

        el.modal?.addEventListener(
            'click',
            event => {
                if (
                    event.target
                    === el.modal
                ) {
                    window
                        .closeStudentAssignmentEdit();
                }
            }
        );

        document.addEventListener(
            'keydown',
            event => {
                if (
                    event.key
                    === 'Escape'
                    && el.modal
                    && el.modal.style
                        .display
                        !== 'none'
                ) {
                    window
                        .closeStudentAssignmentEdit();
                }
            }
        );
    };

    if (
        document.readyState
        === 'loading'
    ) {
        document.addEventListener(
            'DOMContentLoaded',
            bindEditEvents,
            {
                once: true,
            }
        );
    } else {
        bindEditEvents();
    }
})();
</script>
<!-- AUTO_GROUP_FROM_TIME_V1 -->
<style>
.ssa-free-time-grid {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:10px;
}

@media (max-width:650px) {
    .ssa-free-time-grid {
        grid-template-columns:1fr;
    }
}
</style>

<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {
        const hiddenTime =
            document.getElementById(
                'assignment_schedule_id'
            );

        const day =
            document.getElementById(
                'assignment_schedule_day'
            );

        const hour =
            document.getElementById(
                'assignment_schedule_hour'
            );

        const subject =
            document.getElementById(
                'assignment_subject_id'
            );

        const level =
            document.getElementById(
                'assignment_level_id'
            );

        const classRoom =
            document.getElementById(
                'assignment_class_id'
            );

        /*
         * Le select Groupe est conservé uniquement pour compatibilité
         * avec l'ancien JavaScript. Il n'est plus un choix utilisateur.
         */
        const group =
            document.getElementById(
                'assignment_class_slot_id'
            );

        const preview =
            document.getElementById(
                'assignmentGeneratedSlotCode'
            );

        const groupPreview =
            document.getElementById(
                'assignmentAutoGroupPreview'
            );

        const groupCodeTarget =
            document.getElementById(
                'assignmentAutoGroupCode'
            );

        const form =
            document.getElementById(
                'studentAssignmentForm'
            );

        const knownTimeMap =
            @json($studentTimeSlotMap ?? []);

        const initialScheduleKey =
            @json((string) old('schedule_id', ''));

        if (
            !hiddenTime
            || !day
            || !hour
        ) {
            return;
        }

        const clean =
            value =>
                String(value || '')
                    .normalize('NFD')
                    .replace(
                        /[\u0300-\u036f]/g,
                        ''
                    );

        const selectedText =
            select =>
                select?.value
                    ? (
                        select.options[
                            select.selectedIndex
                        ]?.textContent
                        || ''
                    ).trim()
                    : '';

        const twoLetterCode =
            (
                value,
                fallback
            ) => {
                const normalized =
                    clean(value)
                        .toUpperCase()
                        .replace(
                            /[^A-Z0-9]/g,
                            ''
                        );

                let code =
                    normalized.slice(
                        0,
                        2
                    );

                if (!code) {
                    return fallback;
                }

                if (code.length === 1) {
                    code += 'X';
                }

                return code;
            };

        const classPrefix =
            () => {
                const normalized =
                    clean(
                        selectedText(
                            classRoom
                        )
                    )
                        .toLowerCase();

                if (
                    normalized.includes(
                        'debut'
                    )
                ) {
                    return 'D';
                }

                if (
                    normalized.includes(
                        'inter'
                    )
                ) {
                    return 'I';
                }

                if (
                    normalized.includes(
                        'avance'
                    )
                    || normalized.includes(
                        'adulte'
                    )
                ) {
                    return 'A';
                }

                const fallback =
                    normalized
                        .replace(
                            /[^a-z0-9]/g,
                            ''
                        )
                        .charAt(0)
                        .toUpperCase();

                return fallback || 'G';
            };

        const dayCode =
            value =>
                ({
                    1:'L',
                    2:'MA',
                    3:'M',
                    4:'J',
                    5:'V',
                    6:'S',
                    7:'D',
                })[
                    Number(value)
                ]
                || '';

        const validTime =
            value => {
                const match =
                    String(value || '')
                        .match(
                            /^(\d{2}):(\d{2})$/
                        );

                if (!match) {
                    return false;
                }

                const h =
                    Number(match[1]);

                const m =
                    Number(match[2]);

                const total =
                    h * 60
                    + m;

                return (
                    h >= 0
                    && h <= 23
                    && m >= 0
                    && m <= 59
                    && total >= 8 * 60
                    && total <= 22 * 60
                );
            };

        /*
         * Prévisualisation du rang chronologique.
         *
         * On utilise les heures déjà connues en base, on ajoute
         * l'heure saisie si elle est nouvelle, puis on trie.
         * Le backend refait exactement le calcul et reste la source
         * de vérité au moment de l'enregistrement.
         */
        const previewSlotNumber =
            (
                selectedDay,
                selectedTime
            ) => {
                const dayMap =
                    knownTimeMap[
                        String(
                            selectedDay
                        )
                    ]
                    || knownTimeMap[
                        Number(
                            selectedDay
                        )
                    ]
                    || {};

                const times =
                    Object.keys(
                        dayMap
                    );

                /*
                 * 08:00 est la base minimale du système.
                 */
                times.push(
                    '08:00'
                );

                times.push(
                    selectedTime
                );

                const unique =
                    Array.from(
                        new Set(
                            times
                                .filter(
                                    validTime
                                )
                        )
                    )
                        .sort();

                const index =
                    unique.indexOf(
                        selectedTime
                    );

                return index >= 0
                    ? index + 1
                    : null;
            };

        const ensureHiddenOption =
            (
                key,
                label,
                code
            ) => {
                let option =
                    Array.from(
                        hiddenTime.options
                    )
                        .find(
                            item =>
                                String(
                                    item.value
                                )
                                === String(
                                    key
                                )
                        );

                if (!option) {
                    option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        String(
                            key
                        );

                    hiddenTime.appendChild(
                        option
                    );
                }

                option.textContent =
                    label;

                option.dataset.code =
                    code;

                return option;
            };

        const setAutomaticGroup =
            groupCode => {
                if (!group) {
                    return;
                }

                let option =
                    Array.from(
                        group.options
                    )
                        .find(
                            item =>
                                String(
                                    item.dataset?.code
                                    || ''
                                ).toUpperCase()
                                === String(
                                    groupCode
                                ).toUpperCase()
                        );

                /*
                 * Si D5/D6/... n'existe pas encore en base,
                 * on ajoute une option visuelle temporaire.
                 * Laravel créera réellement ce groupe au submit.
                 */
                if (!option) {
                    option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        'AUTO:'
                        + groupCode;

                    option.textContent =
                        groupCode
                        + ' — automatique';

                    option.dataset.code =
                        groupCode;

                    option.dataset.auto =
                        '1';

                    group.appendChild(
                        option
                    );
                }

                group.disabled =
                    false;

                group.value =
                    option.value;

                if (
                    groupPreview
                    && groupCodeTarget
                ) {
                    groupPreview.hidden =
                        false;

                    groupCodeTarget.textContent =
                        groupCode;
                }
            };

        const clearAutomaticGroup =
            () => {
                if (
                    groupPreview
                    && groupCodeTarget
                ) {
                    groupPreview.hidden =
                        true;

                    groupCodeTarget.textContent =
                        '—';
                }
            };

        const refreshPreview =
            code => {
                if (!preview) {
                    return;
                }

                preview.hidden =
                    !code;

                const strong =
                    preview.querySelector(
                        'strong'
                    );

                if (strong) {
                    strong.textContent =
                        code || '—';
                }
            };

        const syncHidden =
            () => {
                const selectedDay =
                    Number(
                        day.value
                    );

                const selectedHour =
                    String(
                        hour.value
                        || ''
                    );

                if (
                    !selectedDay
                    || !selectedHour
                ) {
                    hiddenTime.value =
                        '';

                    hour.setCustomValidity(
                        ''
                    );

                    clearAutomaticGroup();
                    refreshPreview('');

                    hiddenTime.dispatchEvent(
                        new Event(
                            'change',
                            {
                                bubbles:true,
                            }
                        )
                    );

                    return;
                }

                if (
                    !validTime(
                        selectedHour
                    )
                ) {
                    hiddenTime.value =
                        '';

                    hour.setCustomValidity(
                        'L’heure doit être comprise entre 08:00 et 22:00.'
                    );

                    clearAutomaticGroup();
                    refreshPreview('');

                    return;
                }

                hour.setCustomValidity(
                    ''
                );

                const number =
                    previewSlotNumber(
                        selectedDay,
                        selectedHour
                    );

                const prefix =
                    classPrefix();

                const groupCode =
                    number
                        ? (
                            prefix
                            + number
                        )
                        : '';

                const code =
                    (
                        number
                        && subject?.value
                        && level?.value
                        && classRoom?.value
                    )
                        ? (
                            dayCode(
                                selectedDay
                            )
                            + '1'
                            + twoLetterCode(
                                selectedText(
                                    subject
                                ),
                                'MT'
                            )
                            + twoLetterCode(
                                selectedText(
                                    level
                                ),
                                'NV'
                            )
                            + groupCode
                        )
                        : '';

                const key =
                    selectedDay
                    + '|'
                    + selectedHour;

                const dayLabel =
                    day.options[
                        day.selectedIndex
                    ]?.textContent
                    ?.trim()
                    || '';

                ensureHiddenOption(
                    key,
                    (
                        code
                        ? code + ' — '
                        : ''
                    )
                    + dayLabel
                    + ' · '
                    + selectedHour,
                    code
                );

                hiddenTime.value =
                    key;

                if (groupCode) {
                    setAutomaticGroup(
                        groupCode
                    );
                } else {
                    clearAutomaticGroup();
                }

                refreshPreview(
                    code
                );

                hiddenTime.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles:true,
                        }
                    )
                );
            };

        const restoreVisible =
            () => {
                const value =
                    String(
                        hiddenTime.value
                        || initialScheduleKey
                        || ''
                    );

                let match =
                    value.match(
                        /^([1-7])\|(\d{2}:\d{2})$/
                    );

                if (match) {
                    day.value =
                        match[1];

                    hour.value =
                        match[2];

                    return;
                }

                /*
                 * Compatibilité ancienne clé "jour:numéro".
                 */
                match =
                    value.match(
                        /^([1-7]):(\d{1,2})$/
                    );

                if (!match) {
                    return;
                }

                day.value =
                    match[1];

                const slot =
                    Number(
                        match[2]
                    );

                const total =
                    8 * 60
                    + (
                        slot - 1
                    )
                    * 30;

                if (
                    total >= 8 * 60
                    && total <= 22 * 60
                ) {
                    hour.value =
                        String(
                            Math.floor(
                                total / 60
                            )
                        ).padStart(
                            2,
                            '0'
                        )
                        + ':'
                        + String(
                            total % 60
                        ).padStart(
                            2,
                            '0'
                        );
                }
            };

        day.addEventListener(
            'change',
            syncHidden
        );

        hour.addEventListener(
            'input',
            syncHidden
        );

        hour.addEventListener(
            'change',
            syncHidden
        );

        [
            subject,
            level,
            classRoom,
        ]
            .filter(Boolean)
            .forEach(
                element => {
                    element.addEventListener(
                        'change',
                        () =>
                            setTimeout(
                                syncHidden,
                                0
                            )
                    );
                }
            );

        form?.addEventListener(
            'submit',
            event => {
                syncHidden();

                if (
                    !day.value
                    || !hour.value
                    || !hiddenTime.value
                ) {
                    event.preventDefault();

                    if (!day.value) {
                        day.focus();
                    } else {
                        hour.focus();
                    }
                }
            }
        );

        setTimeout(
            () => {
                restoreVisible();

                if (
                    day.value
                    && hour.value
                ) {
                    syncHidden();
                }
            },
            0
        );
    }
);
</script>

<!-- ASSIGNATION_RESULTAT_CODE_SEUL_V6_2_STYLE -->
<style>
#assignmentGeneratedSlotCode.ssa-code-only-preview {
    width: 100%;
    max-width: none;
    overflow: visible;
}

#assignmentGeneratedSlotCode.ssa-code-only-preview span {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    min-width: 0;
    white-space: nowrap;
}

#assignmentGeneratedSlotCode.ssa-code-only-preview strong {
    display: inline-block;
    max-width: none !important;
    overflow: visible !important;
    text-overflow: clip !important;
    white-space: nowrap !important;
    font-size: .95rem;
    letter-spacing: .02em;
}
</style>

<!-- TIME_SLOT_RANK_ASSIGNMENT_LIVE_V1 -->
<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {
        const registry =
            @json(
                app(
                    \App\Services\PedagogicalTimeSlotService::class
                )->map()
            );

        const dayCodes = {
            1:'L',
            2:'MA',
            3:'M',
            4:'J',
            5:'V',
            6:'S',
            7:'D',
        };

        const normalize =
            value =>
                String(value || '')
                    .normalize('NFD')
                    .replace(
                        /[\u0300-\u036f]/g,
                        ''
                    );

        const subjectCode =
            select => {
                const text =
                    select?.value
                        ? (
                            select.options[
                                select.selectedIndex
                            ]?.textContent
                            || ''
                        ).trim()
                        : '';

                let code =
                    normalize(text)
                        .toUpperCase()
                        .replace(
                            /[^A-Z0-9]/g,
                            ''
                        )
                        .slice(
                            0,
                            2
                        );

                if (!code) {
                    code = 'MT';
                }

                if (
                    code.length === 1
                ) {
                    code += 'X';
                }

                return code;
            };

        const classCode =
            select => {
                const text =
                    normalize(
                        select?.value
                            ? (
                                select.options[
                                    select.selectedIndex
                                ]?.textContent
                                || ''
                            )
                            : ''
                    )
                        .toLowerCase();

                if (
                    text.includes(
                        'debut'
                    )
                ) {
                    return 'D';
                }

                if (
                    text.includes(
                        'inter'
                    )
                ) {
                    return 'I';
                }

                if (
                    text.includes(
                        'avance'
                    )
                    || text.includes(
                        'adulte'
                    )
                ) {
                    return 'A';
                }

                return text
                    .replace(
                        /[^a-z0-9]/g,
                        ''
                    )
                    .charAt(0)
                    .toUpperCase();
            };

        const groupNumber =
            select => {
                const text =
                    select?.value
                        ? (
                            select.options[
                                select.selectedIndex
                            ]?.dataset?.code
                            || select.options[
                                select.selectedIndex
                            ]?.textContent
                            || ''
                        )
                        : '';

                const match =
                    String(text)
                        .match(
                            /(\d+)/
                        );

                return match
                    ? match[1]
                    : '';
            };

        const slotNumber =
            (
                day,
                hour
            ) => {
                const dayKey =
                    String(
                        day
                        || ''
                    );

                const time =
                    String(
                        hour
                        || ''
                    )
                        .slice(
                            0,
                            5
                        );

                if (
                    !dayKey
                    || !time
                    || time < '08:00'
                    || time > '22:00'
                ) {
                    return null;
                }

                if (
                    registry[
                        dayKey
                    ]?.[
                        time
                    ]
                ) {
                    return Number(
                        registry[
                            dayKey
                        ][
                            time
                        ]
                    );
                }

                const knownTimes =
                    Object.keys(
                        registry[
                            dayKey
                        ]
                        || {}
                    )
                        .map(
                            value =>
                                String(value)
                                    .slice(0,5)
                        )
                        .concat(
                            ['08:00']
                        )
                        .filter(
                            (
                                value,
                                index,
                                all
                            ) =>
                                all.indexOf(
                                    value
                                )
                                === index
                        )
                        .sort();

                return (
                    knownTimes
                        .filter(
                            value =>
                                value < time
                        )
                        .length
                    + 1
                );
            };

        const ensureHiddenValue =
            (
                hidden,
                day,
                hour
            ) => {
                if (!hidden) {
                    return;
                }

                if (
                    !day
                    || !hour
                ) {
                    hidden.value = '';
                    return;
                }

                const value =
                    String(day)
                    + '|'
                    + String(hour)
                        .slice(
                            0,
                            5
                        );

                let option =
                    Array.from(
                        hidden.options
                    )
                        .find(
                            item =>
                                String(
                                    item.value
                                )
                                === value
                        );

                if (!option) {
                    option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        value;

                    option.textContent =
                        value;

                    hidden.appendChild(
                        option
                    );
                }

                hidden.value =
                    value;
            };

        const bind =
            ({
                hiddenId,
                dayId,
                hourId,
                subjectId,
                classId,
                groupId,
                previewId,
                previewSelector,
            }) => {
                const hidden =
                    document.getElementById(
                        hiddenId
                    );

                const day =
                    document.getElementById(
                        dayId
                    );

                const hour =
                    document.getElementById(
                        hourId
                    );

                const subject =
                    document.getElementById(
                        subjectId
                    );

                const classroom =
                    document.getElementById(
                        classId
                    );

                const group =
                    document.getElementById(
                        groupId
                    );

                const preview =
                    document.getElementById(
                        previewId
                    );

                if (
                    !hidden
                    || !day
                    || !hour
                ) {
                    return;
                }

                /*
                 * Heure libre à la minute.
                 */
                hour.step = '60';
                hour.min = '08:00';
                hour.max = '22:00';

                const help =
                    hour.parentElement
                        ?.querySelector(
                            '.assignment-help'
                        );

                if (help) {
                    help.textContent =
                        'Choisissez librement l’heure entre 08:00 et 22:00, à la minute près.';
                }

                const refresh =
                    () => {
                        const selectedDay =
                            Number(
                                day.value
                            );

                        const selectedHour =
                            String(
                                hour.value
                                || ''
                            )
                                .slice(
                                    0,
                                    5
                                );

                        hour.setCustomValidity('');

                        if (
                            !selectedDay
                            || !selectedHour
                        ) {
                            hidden.value = '';

                            if (preview) {
                                preview.hidden = true;
                            }

                            return;
                        }

                        if (
                            selectedHour < '08:00'
                            || selectedHour > '22:00'
                        ) {
                            hidden.value = '';

                            hour.setCustomValidity(
                                'Choisissez une heure entre 08:00 et 22:00.'
                            );

                            if (preview) {
                                preview.hidden = true;
                            }

                            return;
                        }

                        const number =
                            slotNumber(
                                selectedDay,
                                selectedHour
                            );

                        ensureHiddenValue(
                            hidden,
                            selectedDay,
                            selectedHour
                        );

                        const code =
                            dayCodes[
                                selectedDay
                            ]
                            && number
                            && subjectCode(
                                subject
                            )
                            && classCode(
                                classroom
                            )
                            && groupNumber(
                                group
                            )
                                ? (
                                    dayCodes[
                                        selectedDay
                                    ]
                                    + number
                                    + subjectCode(
                                        subject
                                    )
                                    + classCode(
                                        classroom
                                    )
                                    + groupNumber(
                                        group
                                    )
                                )
                                : '';

                        if (preview) {
                            preview.hidden =
                                !code;

                            const target =
                                previewSelector
                                    ? preview.querySelector(
                                        previewSelector
                                    )
                                    : null;

                            if (target) {
                                target.textContent =
                                    code
                                    || '—';
                            }
                        }
                    };

                [
                    day,
                    hour,
                    subject,
                    classroom,
                    group,
                ]
                    .filter(Boolean)
                    .forEach(
                        element => {
                            element.addEventListener(
                                'change',
                                () =>
                                    setTimeout(
                                        refresh,
                                        0
                                    )
                            );

                            element.addEventListener(
                                'input',
                                () =>
                                    setTimeout(
                                        refresh,
                                        0
                                    )
                            );
                        }
                    );

                /*
                 * Nouveau format déjà sauvegardé :
                 * 2|08:45
                 */
                const initial =
                    String(
                        hidden.value
                        || ''
                    );

                const match =
                    initial.match(
                        /^([1-7])\|(\d{2}:\d{2})$/
                    );

                if (match) {
                    day.value =
                        match[1];

                    hour.value =
                        match[2];
                }

                setTimeout(
                    refresh,
                    0
                );
            };

        bind({
            hiddenId:
                'assignment_schedule_id',
            dayId:
                'assignment_schedule_day',
            hourId:
                'assignment_schedule_hour',
            subjectId:
                'assignment_subject_id',
            classId:
                'assignment_class_id',
            groupId:
                'assignment_class_slot_id',
            previewId:
                'assignmentGeneratedSlotCode',
            previewSelector:
                'strong',
        });

        bind({
            hiddenId:
                'edit_page_schedule_id',
            dayId:
                'edit_page_schedule_day',
            hourId:
                'edit_page_schedule_hour',
            subjectId:
                'edit_page_subject_id',
            classId:
                'edit_page_class_id',
            groupId:
                'edit_page_group_id',
            previewId:
                'editPageGeneratedSlotCode',
            previewSelector:
                'span',
        });
    }
);
</script>

@endsection

<!-- TIME_SLOT_CHRONOLOGICAL_RANK_V2 -->
