@extends('layouts.admin')

@section('title', 'Assignation des étudiants')
@section('page_title', 'Assignation étudiants')
@section(
    'breadcrumb',
    'Étudiants → Matière → Niveau → Classe'
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
            Choisissez la matière, puis son niveau,
            puis la classe appartenant à ce niveau.
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
                        Matière → Niveau → Classe
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
                                    <td colspan="5">
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
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const hierarchy = @json($assignmentHierarchy);

    const createOption = (
        value,
        label,
        selectedValue = ''
    ) => {
        const option =
            document.createElement('option');

        option.value = String(value);
        option.textContent = label;
        option.selected =
            String(value)
            === String(selectedValue);

        return option;
    };

    const findSubject = subjectId =>
        hierarchy.find(
            subject =>
                String(subject.id)
                === String(subjectId)
        );

    const findLevel = (
        subject,
        levelId
    ) => {
        if (!subject) {
            return null;
        }

        return subject.levels.find(
            level =>
                String(level.id)
                === String(levelId)
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

    const fillClasses = (
        subjectSelect,
        levelSelect,
        classSelect,
        selectedClassId = ''
    ) => {
        const subject = findSubject(
            subjectSelect.value
        );

        const level = findLevel(
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
    };

    const fillLevels = (
        subjectSelect,
        levelSelect,
        classSelect,
        selectedLevelId = '',
        selectedClassId = ''
    ) => {
        const subject = findSubject(
            subjectSelect.value
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
            levelSelect.value =
                String(selectedLevelId);

            fillClasses(
                subjectSelect,
                levelSelect,
                classSelect,
                selectedClassId
            );
        }
    };

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

    const updatePath = () => {
        const values = [
            [mainSubject, pathSubject, 'Matière'],
            [mainLevel, pathLevel, 'Niveau'],
            [mainClass, pathClass, 'Classe'],
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
    };

    mainSubject.addEventListener(
        'change',
        () => {
            fillLevels(
                mainSubject,
                mainLevel,
                mainClass
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
                mainClass
            );

            updatePath();
        }
    );

    mainClass.addEventListener(
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

    if (oldSubjectId) {
        mainSubject.value =
            oldSubjectId;

        fillLevels(
            mainSubject,
            mainLevel,
            mainClass,
            oldLevelId,
            oldClassId
        );
    }

    updatePath();

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

    editSubject.addEventListener(
        'change',
        () => {
            fillLevels(
                editSubject,
                editLevel,
                editClass
            );
        }
    );

    editLevel.addEventListener(
        'change',
        () => {
            fillClasses(
                editSubject,
                editLevel,
                editClass
            );
        }
    );

    window.openStudentAssignmentEdit = (
        userId,
        subjectId,
        levelId,
        classId,
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
            levelId
                ? String(levelId)
                : '',
            classId
                ? String(classId)
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

@endsection
