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
            Affectez directement le professeur à un groupe pédagogique
            D1, D2, I1, A1… sans créer d’abord une séance dans l’emploi du temps.
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

                        Nouvelle assignation
                    </h4>

                    <p class="prof-assignment-subtitle">
                        Matière → Niveau → Classe → Créneau
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
                    id="profAssignmentForm"
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
                                    {{ (string) old('prof_id') === (string) $professor->id ? 'selected' : '' }}
                                >
                                    {{ $professor->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('prof_id')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="prof-assignment-hierarchy">
                        <div class="prof-path-preview">
                            <span id="profPathSubject">
                                Matière
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span id="profPathLevel">
                                Niveau
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span id="profPathClass">
                                Classe
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span id="profPathSlot">
                                Créneau
                            </span>
                        </div>

                        <div class="prof-assignment-step">
                            <span class="prof-step-number">
                                1
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="prof_assignment_subject_id"
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
                                    id="prof_assignment_subject_id"
                                    class="adm-form-select
                                        @error('subject_id')
                                            error
                                        @enderror"
                                    required
                                >
                                    <option value="">
                                        Sélectionner une matière
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

                        <div class="prof-assignment-step">
                            <span class="prof-step-number">
                                2
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="prof_assignment_level_id"
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
                                    id="prof_assignment_level_id"
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

                        <div class="prof-assignment-step">
                            <span class="prof-step-number">
                                3
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="prof_assignment_class_id"
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
                                    id="prof_assignment_class_id"
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
                                    Seules les classes liées à cette
                                    matière et à ce niveau apparaissent.
                                </small>

                                @error('class_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="prof-assignment-step">
                            <span class="prof-step-number">
                                4
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="prof_assignment_class_slot_id"
                                >
                                    Créneau / groupe
                                    <span class="assignment-required">*</span>
                                </label>

                                <select
                                    name="class_slot_id"
                                    id="prof_assignment_class_slot_id"
                                    class="adm-form-select
                                        @error('class_slot_id') error @enderror"
                                    disabled
                                    required
                                >
                                    <option value="">
                                        Choisissez d’abord une classe
                                    </option>
                                </select>

                                <small class="assignment-help">
                                    Les créneaux D1/D2/D3/D4, I1… et A1…
                                    viennent directement de la structure pédagogique.
                                    Aucun emploi du temps n’est requis.
                                </small>

                                @error('class_slot_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="prof-structural-slot-note">
                        <i class="bi bi-diagram-3-fill"></i>

                        <div>
                            <strong>
                                Assignation indépendante de l’emploi du temps
                            </strong>

                            <span>
                                Vous pouvez affecter un professeur à D1, D2, I1…
                                immédiatement. Le jour et les heures pourront être
                                ajoutés ensuite dans l’emploi du temps.
                            </span>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="adm-btn adm-btn-accent w-100"
                        style="padding:12px;"
                    >
                        <i class="bi bi-check-circle"></i>
                        Assigner le professeur
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
                    <span class="prof-assignment-subtitle">
                        {{ $assignments->count() }}
                        assignation(s)
                    </span>
                </div>
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
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($assignments as $assignment)
                                    <tr>
                                        <td>
                                            <div class="professor-cell">
                                                <span
                                                    class="adm-avatar
                                                        adm-avatar-sm"
                                                >
                                                    {{
                                                        mb_strtoupper(
                                                            mb_substr(
                                                                $assignment
                                                                    ->prof
                                                                    ?->name
                                                                ?? '?',
                                                                0,
                                                                1
                                                            )
                                                        )
                                                    }}
                                                </span>

                                                <strong>
                                                    {{
                                                        $assignment
                                                            ->prof
                                                            ?->name
                                                        ?? '—'
                                                    }}
                                                </strong>
                                            </div>
                                        </td>

                                        <td>
                                            <span
                                                class="adm-badge
                                                    adm-badge-accent"
                                            >
                                                {{
                                                    $assignment
                                                        ->subject
                                                        ?->name
                                                    ?? '—'
                                                }}
                                            </span>
                                        </td>

                                        <td>
                                            <span
                                                class="adm-badge
                                                    adm-badge-info"
                                            >
                                                {{
                                                    $assignment
                                                        ->level
                                                        ?->name
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
                                                        ->classRoom
                                                        ?->name
                                                    ?? '—'
                                                }}
                                            </span>
                                        </td>

                                        <td>
                                            @if($assignment->classSlot)
                                                <span class="prof-slot-badge">
                                                    {{ $assignment->classSlot->code }}
                                                </span>
                                            @else
                                                <span class="prof-no-schedule">
                                                    Ancienne assignation
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            @php
                                                $scheduleKey =
                                                    (int) $assignment->subject_id
                                                    . ':'
                                                    . (int) $assignment->level_id
                                                    . ':'
                                                    . (int) $assignment->class_id
                                                    . ':'
                                                    . strtoupper(
                                                        trim(
                                                            (string) optional(
                                                                $assignment->classSlot
                                                            )->code
                                                        )
                                                    );

                                                $linkedSchedule =
                                                    $assignment->classSlot
                                                        ? $scheduleMap->get($scheduleKey)
                                                        : null;
                                            @endphp

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
                                            <form
                                                method="POST"
                                                action="{{
                                                    route(
                                                        'admin.users.destroy-prof-assignment',
                                                        $assignment->id
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
                                                    title="Supprimer"
                                                >
                                                    <i
                                                        class="bi
                                                            bi-trash"
                                                    ></i>
                                                </button>
                                            </form>
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
                            un professeur.
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
    font-size: 0.68rem;
}

.prof-assignment-hierarchy {
    margin-bottom: 1rem;
    padding: 0.9rem;
    border: 1px solid rgba(167,139,250,0.12);
    border-radius: 15px;
    background: rgba(124,58,237,0.035);
}

.prof-path-preview {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
    margin-bottom: 0.8rem;
    padding: 8px 10px;
    border: 1px solid rgba(167,139,250,0.1);
    border-radius: 11px;
    color: rgba(255,255,255,0.45);
    background: rgba(255,255,255,0.02);
    font-size: 0.64rem;
}

.prof-path-preview span.is-selected {
    color: #C4B5FD;
    font-weight: 750;
}

.prof-path-preview i {
    color: rgba(255,255,255,0.2);
    font-size: 0.55rem;
}

.prof-assignment-step {
    position: relative;
    margin-bottom: 0.7rem;
    padding: 0.75rem;
    border: 1px solid rgba(255,255,255,0.045);
    border-radius: 12px;
    background: rgba(7,15,30,0.27);
}

.prof-assignment-step:last-child {
    margin-bottom: 0;
}

.prof-step-number {
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
        linear-gradient(135deg,#7C3AED,#4F46E5);
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

.professor-cell {
    display: flex;
    align-items: center;
    gap: 9px;
}

.professor-cell .adm-avatar {
    width: 33px;
    height: 33px;
    background:
        linear-gradient(135deg,#7C3AED,#A78BFA);
    font-size: 0.68rem;
}

.prof-assignment-schedule {
    margin: 0 0 1rem;
    padding: 0.9rem;
    border: 1px solid rgba(56,189,248,0.13);
    border-radius: 15px;
    background:
        linear-gradient(
            145deg,
            rgba(14,116,144,0.055),
            rgba(59,130,246,0.035)
        );
}

.prof-schedule-heading {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 0.85rem;
}

.prof-schedule-icon {
    display: grid;
    width: 38px;
    height: 38px;
    flex: 0 0 auto;
    place-items: center;
    color: #67E8F9;
    border: 1px solid rgba(34,211,238,0.14);
    border-radius: 11px;
    background: rgba(8,145,178,0.10);
}

.prof-schedule-heading > div {
    display: flex;
    min-width: 0;
    flex-direction: column;
    gap: 2px;
}

.prof-schedule-heading strong {
    color: var(--adm-text);
    font-size: 0.74rem;
}

.prof-schedule-heading small {
    color: var(--adm-text-muted);
    font-size: 0.61rem;
    line-height: 1.45;
}

.prof-time-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.75rem;
}

.prof-schedule-note {
    display: flex;
    align-items: flex-start;
    gap: 7px;
    margin-top: 0.75rem;
    padding: 8px 10px;
    color: rgba(255,255,255,0.43);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 10px;
    background: rgba(255,255,255,0.018);
    font-size: 0.59rem;
    line-height: 1.5;
}

.prof-schedule-note i {
    flex: 0 0 auto;
    margin-top: 1px;
    color: #60A5FA;
}

.prof-day-badge,
.prof-time-badge {
    display: inline-flex;
    min-height: 28px;
    align-items: center;
    gap: 6px;
    padding: 0 9px;
    border-radius: 9px;
    font-size: 0.63rem;
    font-weight: 750;
    white-space: nowrap;
}

.prof-day-badge {
    color: #93C5FD;
    border: 1px solid rgba(59,130,246,0.15);
    background: rgba(59,130,246,0.08);
}

.prof-time-badge {
    color: #67E8F9;
    border: 1px solid rgba(34,211,238,0.15);
    background: rgba(8,145,178,0.08);
}

.prof-no-schedule {
    color: var(--adm-text-muted);
    font-size: 0.62rem;
    font-style: italic;
}

@media (max-width: 575.98px) {
    .prof-time-grid {
        grid-template-columns: 1fr;
    }
}


.prof-structural-slot-note {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin: 0 0 1rem;
    padding: 12px;
    color: #94A3B8;
    border: 1px solid rgba(34,197,94,0.13);
    border-radius: 13px;
    background: rgba(34,197,94,0.045);
}

.prof-structural-slot-note > i {
    margin-top: 1px;
    color: #4ADE80;
    font-size: 1rem;
}

.prof-structural-slot-note > div {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.prof-structural-slot-note strong {
    color: #DCFCE7;
    font-size: 0.68rem;
}

.prof-structural-slot-note span {
    font-size: 0.6rem;
    line-height: 1.5;
}

.prof-slot-badge {
    display: inline-flex;
    min-width: 38px;
    min-height: 29px;
    align-items: center;
    justify-content: center;
    padding: 4px 9px;
    color: #DDD6FE;
    border: 1px solid rgba(139,92,246,0.18);
    border-radius: 9px;
    background: rgba(124,58,237,0.10);
    font-size: 0.65rem;
    font-weight: 850;
}

.prof-linked-schedule {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const hierarchy = @json($assignmentHierarchy);

    const subjectSelect =
        document.getElementById('prof_assignment_subject_id');

    const levelSelect =
        document.getElementById('prof_assignment_level_id');

    const classSelect =
        document.getElementById('prof_assignment_class_id');

    const slotSelect =
        document.getElementById('prof_assignment_class_slot_id');

    const pathSubject =
        document.getElementById('profPathSubject');

    const pathLevel =
        document.getElementById('profPathLevel');

    const pathClass =
        document.getElementById('profPathClass');

    const pathSlot =
        document.getElementById('profPathSlot');

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
            String(value) === String(selectedValue);

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

    const findLevel = (
        subject,
        levelId
    ) => {
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

    const updatePath = () => {
        const items = [
            [subjectSelect, pathSubject, 'Matière'],
            [levelSelect, pathLevel, 'Niveau'],
            [classSelect, pathClass, 'Classe'],
            [slotSelect, pathSlot, 'Créneau'],
        ];

        items.forEach(
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

    const fillSlots = (
        selectedSlotId = ''
    ) => {
        const subject =
            findSubject(subjectSelect.value);

        const level =
            findLevel(subject, levelSelect.value);

        const classRoom =
            findClass(
                subject,
                level,
                classSelect.value
            );

        resetSelect(
            slotSelect,
            classRoom
                ? 'Sélectionner un créneau'
                : 'Choisissez d’abord une classe',
            !classRoom
        );

        if (!classRoom) {
            updatePath();
            return;
        }

        (classRoom.slots || []).forEach(slot => {
            slotSelect.appendChild(
                createOption(
                    slot.id,
                    slot.code || slot.name,
                    selectedSlotId
                )
            );
        });

        slotSelect.disabled = false;

        if (selectedSlotId) {
            slotSelect.value =
                String(selectedSlotId);
        }

        updatePath();
    };

    const fillClasses = (
        selectedClassId = '',
        selectedSlotId = ''
    ) => {
        const subject =
            findSubject(subjectSelect.value);

        const level =
            findLevel(subject, levelSelect.value);

        resetSelect(
            classSelect,
            level
                ? 'Sélectionner une classe'
                : 'Choisissez d’abord un niveau',
            !level
        );

        resetSelect(
            slotSelect,
            'Choisissez d’abord une classe',
            true
        );

        if (!level) {
            updatePath();
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

        if (selectedClassId) {
            classSelect.value =
                String(selectedClassId);

            fillSlots(selectedSlotId);
        }

        updatePath();
    };

    const fillLevels = (
        selectedLevelId = '',
        selectedClassId = '',
        selectedSlotId = ''
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
            slotSelect,
            'Choisissez d’abord une classe',
            true
        );

        if (!subject) {
            updatePath();
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
                selectedClassId,
                selectedSlotId
            );
        }

        updatePath();
    };

    subjectSelect.addEventListener(
        'change',
        () => fillLevels()
    );

    levelSelect.addEventListener(
        'change',
        () => fillClasses()
    );

    classSelect.addEventListener(
        'change',
        () => fillSlots()
    );

    slotSelect.addEventListener(
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

    const oldSlotId = @json(
        (string) old('class_slot_id', '')
    );

    if (oldSubjectId) {
        subjectSelect.value =
            oldSubjectId;

        fillLevels(
            oldLevelId,
            oldClassId,
            oldSlotId
        );
    } else {
        fillLevels();
    }
});
</script>

@endsection
