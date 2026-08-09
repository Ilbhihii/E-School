@extends('layouts.admin')

@section('title', 'Assignation des étudiants')
@section('page_title', 'Assignation étudiants')
@section(
    'breadcrumb',
    'Étudiants → Matière → Niveau → Classe → Créneau'
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
            Choisissez la matière, le niveau et la classe.
            Le créneau est généré automatiquement depuis la structure Matière → Niveau → Classe. Aucun emploi du temps n’est nécessaire pour assigner l’étudiant.
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
                        Matière → Niveau → Classe → Créneau
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
                                    @selected(
                                        (string) old('user_id')
                                        === (string) $student->id
                                    )
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

                            <span id="studentPathSchedule">
                                Créneau
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

                        <div class="assignment-step assignment-slot-step">
                            <span class="assignment-step-number">
                                4
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="assignment_class_slot_id"
                                >
                                    Créneau / groupe
                                    <span class="assignment-required">*</span>
                                </label>

                                <select
                                    name="class_slot_id"
                                    id="assignment_class_slot_id"
                                    class="adm-form-select
                                        @error('class_slot_id') error @enderror"
                                    disabled
                                    required
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
                                    Ces créneaux sont créés avec la classe :
                                    Débutant → D1 à D4,
                                    Intermédiaire → I1 à I4,
                                    Avancé → A1 à A4.
                                    Ils existent même si aucun horaire n’est encore défini.
                                </small>

                                @error('class_slot_id')
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
                                <th>Créneau</th>
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
                                                Créneau non défini
                                            </span>
                                        @endif
                                    </td>

                                    <td style="text-align:right;">
                                        <div class="assignment-actions">
                                            <button
                                                type="button"
                                                class="adm-btn
                                                    adm-btn-warning
                                                    adm-btn-sm"
                                                onclick="openStudentAssignmentEdit(
                                                    {{ $assignment->user_id }},
                                                    {{ $assignment->subject_id ?: 'null' }},
                                                    {{ $assignment->level_id ?: 'null' }},
                                                    {{ $assignment->class_id }},
                                                    {{ $assignment->class_slot_id ?: 'null' }},
                                                    {{ $assignment->pivot_id }}
                                                )"
                                            >
                                                <i
                                                    class="bi bi-pencil"
                                                ></i>
                                                Modifier
                                            </button>

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
                                    <td colspan="6">
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
                        Créneau
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
                        Créneau structurel de la classe. Il ne dépend pas encore du jour ni de l’heure.
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

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const hierarchy = @json($assignmentHierarchy);

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

    const findSubject = subjectId =>
        hierarchy.find(
            subject =>
                String(subject.id) === String(subjectId)
        );

    const findLevel = (subject, levelId) => {
        if (!subject) {
            return null;
        }

        return subject.levels.find(
            level =>
                String(level.id) === String(levelId)
        );
    };

    const findClass = (
        subject,
        level,
        classId
    ) => {
        if (!subject || !level) {
            return null;
        }

        return level.classes.find(
            classRoom =>
                String(classRoom.id) === String(classId)
        );
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

    const fillSlots = (
        subjectSelect,
        levelSelect,
        classSelect,
        scheduleSelect,
        selectedScheduleId = ''
    ) => {
        const subject = findSubject(subjectSelect.value);
        const level = findLevel(subject, levelSelect.value);
        const classRoom = findClass(
            subject,
            level,
            classSelect.value
        );

        resetSelect(
            scheduleSelect,
            classRoom
                ? 'Sélectionner un créneau'
                : 'Choisissez d’abord une classe',
            !classRoom
        );

        if (!classRoom) {
            return;
        }

        const slots = classRoom.slots || [];

        if (slots.length === 0) {
            resetSelect(
                scheduleSelect,
                'Aucun créneau généré pour cette classe',
                true
            );

            return;
        }

        slots.forEach(slot => {
            scheduleSelect.appendChild(
                createOption(
                    slot.id,
                    slot.code || slot.name,
                    selectedScheduleId,
                    {
                        code: slot.code || slot.name,
                    }
                )
            );
        });

        scheduleSelect.disabled = false;
        scheduleSelect.value =
            selectedScheduleId
                ? String(selectedScheduleId)
                : '';
    };

    const fillClasses = (
        subjectSelect,
        levelSelect,
        classSelect,
        scheduleSelect,
        selectedClassId = '',
        selectedScheduleId = ''
    ) => {
        const subject = findSubject(subjectSelect.value);
        const level = findLevel(subject, levelSelect.value);

        resetSelect(
            classSelect,
            level
                ? 'Sélectionner une classe'
                : 'Choisissez d’abord un niveau',
            !level
        );

        resetSelect(
            scheduleSelect,
            'Choisissez d’abord une classe',
            true
        );

        if (!level) {
            return;
        }

        level.classes.forEach(classRoom => {
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
            fillSlots(
                subjectSelect,
                levelSelect,
                classSelect,
                scheduleSelect,
                selectedScheduleId
            );
        }
    };

    const fillLevels = (
        subjectSelect,
        levelSelect,
        classSelect,
        scheduleSelect,
        selectedLevelId = '',
        selectedClassId = '',
        selectedScheduleId = ''
    ) => {
        const subject = findSubject(subjectSelect.value);

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
            scheduleSelect,
            'Choisissez d’abord une classe',
            true
        );

        if (!subject) {
            return;
        }

        subject.levels.forEach(level => {
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
            levelSelect.value = String(selectedLevelId);

            fillClasses(
                subjectSelect,
                levelSelect,
                classSelect,
                scheduleSelect,
                selectedClassId,
                selectedScheduleId
            );
        }
    };

    const mainSubject =
        document.getElementById('assignment_subject_id');

    const mainLevel =
        document.getElementById('assignment_level_id');

    const mainClass =
        document.getElementById('assignment_class_id');

    const mainSchedule =
        document.getElementById('assignment_class_slot_id');

    const pathSubject =
        document.getElementById('studentPathSubject');

    const pathLevel =
        document.getElementById('studentPathLevel');

    const pathClass =
        document.getElementById('studentPathClass');

    const pathSchedule =
        document.getElementById('studentPathSchedule');

    const slotPreview =
        document.getElementById('assignmentSlotPreview');

    const slotCode =
        document.getElementById('assignmentSlotCode');

    const updateSlotPreview = () => {
        const option =
            mainSchedule.options[
                mainSchedule.selectedIndex
            ];

        const hasSlot = Boolean(mainSchedule.value);

        slotPreview.hidden = !hasSlot;

        if (!hasSlot || !option) {
            slotCode.textContent = '—';
            return;
        }

        slotCode.textContent =
            option.dataset.code
            || option.textContent
            || '—';
    };

    const updatePath = () => {
        const values = [
            [mainSubject, pathSubject, 'Matière'],
            [mainLevel, pathLevel, 'Niveau'],
            [mainClass, pathClass, 'Classe'],
            [mainSchedule, pathSchedule, 'Créneau'],
        ];

        values.forEach(
            ([select, target, fallback]) => {
                target.textContent =
                    select.value
                        ? select.options[
                            select.selectedIndex
                        ].textContent
                        : fallback;

                target.classList.toggle(
                    'is-selected',
                    Boolean(select.value)
                );
            }
        );

        updateSlotPreview();
    };

    mainSubject.addEventListener('change', () => {
        fillLevels(
            mainSubject,
            mainLevel,
            mainClass,
            mainSchedule
        );

        updatePath();
    });

    mainLevel.addEventListener('change', () => {
        fillClasses(
            mainSubject,
            mainLevel,
            mainClass,
            mainSchedule
        );

        updatePath();
    });

    mainClass.addEventListener('change', () => {
        fillSlots(
            mainSubject,
            mainLevel,
            mainClass,
            mainSchedule
        );

        updatePath();
    });

    mainSchedule.addEventListener(
        'change',
        updatePath
    );

    const oldSubjectId = @json(
        (string) old('subject_id', '')
    );

    const oldLevelId = @json(
        (string) old('level_id', '')
    );

    const oldClassId = @json(
        (string) old('class_id', '')
    );

    const oldScheduleId = @json(
        (string) old('class_slot_id', '')
    );

    if (oldSubjectId) {
        mainSubject.value = oldSubjectId;

        fillLevels(
            mainSubject,
            mainLevel,
            mainClass,
            mainSchedule,
            oldLevelId,
            oldClassId,
            oldScheduleId
        );
    }

    updatePath();

    const editSubject =
        document.getElementById('edit_assignment_subject_id');

    const editLevel =
        document.getElementById('edit_assignment_level_id');

    const editClass =
        document.getElementById('edit_assignment_class_id');

    const editSchedule =
        document.getElementById('edit_assignment_class_slot_id');

    editSubject.addEventListener('change', () => {
        fillLevels(
            editSubject,
            editLevel,
            editClass,
            editSchedule
        );
    });

    editLevel.addEventListener('change', () => {
        fillClasses(
            editSubject,
            editLevel,
            editClass,
            editSchedule
        );
    });

    editClass.addEventListener('change', () => {
        fillSlots(
            editSubject,
            editLevel,
            editClass,
            editSchedule
        );
    });

    window.openStudentAssignmentEdit = (
        userId,
        subjectId,
        levelId,
        classId,
        scheduleId,
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
            editSchedule,
            levelId
                ? String(levelId)
                : '',
            classId
                ? String(classId)
                : '',
            scheduleId
                ? String(scheduleId)
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

        document.body.style.overflow = 'hidden';
    };

    window.closeStudentAssignmentEdit = () => {
        document.getElementById(
            'studentAssignmentModal'
        ).style.display = 'none';

        document.body.style.overflow = '';
    };
});
</script>

@endsection
