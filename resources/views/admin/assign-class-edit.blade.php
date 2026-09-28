@extends('layouts.admin')

@section('title', 'Modifier une assignation')
@section('page_title', 'Modifier une assignation')
@section(
    'breadcrumb',
    'Étudiants → Assignations → Modifier'
)

@section('content')

<div class="adm-page-header">
    <div>
        <h1>
            <i
                class="bi bi-pencil-square"
                style="color:#F59E0B;"
            ></i>
            Modifier l’assignation
        </h1>

        <div class="subtitle">
            Modifiez l’étudiant, la matière, le niveau,
            la classe, le groupe ou le créneau horaire.
        </div>
    </div>

    <a
        href="{{ route('admin.assign.class') }}"
        class="adm-btn adm-btn-ghost"
    >
        <i class="bi bi-arrow-left"></i>
        Retour aux assignations
    </a>
</div>

@if($errors->any())
    <div class="adm-alert adm-alert-danger mb-3">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="adm-card">
            <div class="adm-card-header">
                <div>
                    <h4>
                        <i
                            class="bi bi-person-gear"
                            style="color:#FBBF24;"
                        ></i>
                        Assignation #{{ $assignment->pivot_id }}
                    </h4>

                    <p
                        style="
                            margin:3px 0 0;
                            color:var(--adm-text-muted);
                            font-size:.7rem;
                        "
                    >
                        {{ $assignment->student_name }}
                        ·
                        {{ $assignment->subject_name ?? 'Matière' }}
                        ·
                        {{ $assignment->class_name }}
                        ·
                        {{ $assignment->slot_code ?? 'Groupe' }}
                    </p>
                </div>
            </div>

            <div class="adm-card-body">
                <form
                    method="POST"
                    action="{{
                        route(
                            'admin.assign.class.update',
                            $assignment->pivot_id
                        )
                    }}"
                    id="dedicatedAssignmentEditForm"
                >
                    @csrf
                    @method('PATCH')

                    <div class="ssa-edit-grid">
                        <div class="adm-form-group ssa-edit-full">
                            <label
                                class="adm-form-label"
                                for="edit_page_user_id"
                            >
                                Étudiant
                                <span class="ssa-required">*</span>
                            </label>

                            <select
                                name="user_id"
                                id="edit_page_user_id"
                                class="adm-form-select"
                                required
                            >
                                @foreach($students as $student)
                                    <option
                                        value="{{ $student->id }}"
                                        {{
                                            (string)
                                                old(
                                                    'user_id',
                                                    $assignment->user_id
                                                )
                                            ===
                                            (string)
                                                $student->id
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="adm-form-group">
                            <label
                                class="adm-form-label"
                                for="edit_page_subject_id"
                            >
                                Matière
                                <span class="ssa-required">*</span>
                            </label>

                            <select
                                name="subject_id"
                                id="edit_page_subject_id"
                                class="adm-form-select"
                                required
                            >
                                <option value="">
                                    Choisir une matière
                                </option>

                                @foreach($subjects as $subject)
                                    <option
                                        value="{{ $subject->id }}"
                                        {{
                                            (string)
                                                old(
                                                    'subject_id',
                                                    $assignment->subject_id
                                                )
                                            ===
                                            (string)
                                                $subject->id
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="adm-form-group">
                            <label
                                class="adm-form-label"
                                for="edit_page_level_id"
                            >
                                Niveau
                                <span class="ssa-required">*</span>
                            </label>

                            <select
                                name="level_id"
                                id="edit_page_level_id"
                                class="adm-form-select"
                                required
                            >
                            </select>
                        </div>

                        <div class="adm-form-group">
                            <label
                                class="adm-form-label"
                                for="edit_page_class_id"
                            >
                                Classe
                                <span class="ssa-required">*</span>
                            </label>

                            <select
                                name="class_id"
                                id="edit_page_class_id"
                                class="adm-form-select"
                                required
                            >
                            </select>
                        </div>

                        <div class="adm-form-group">
                            <label
                                class="adm-form-label"
                                for="edit_page_group_id"
                            >
                                Groupe
                                <span class="ssa-required">*</span>
                            </label>

                            <select
                                name="class_slot_id"
                                id="edit_page_group_id"
                                class="adm-form-select"
                                required
                            >
                            </select>

                            <small class="ssa-help">
                                Un groupe complet reste disponible
                                uniquement s’il s’agit déjà du groupe
                                de cet étudiant.
                            </small>
                        </div>

                        <div class="adm-form-group ssa-edit-full">
                            <label
                                class="adm-form-label"
                                for="edit_page_schedule_id"
                            >
                                Créneau horaire
                                <span class="ssa-optional">
                                    (optionnel)
                                </span>
                            </label>

                            <select
                                name="schedule_id"
                                id="edit_page_schedule_id"
                                class="adm-form-select"
                                style="display:none;"
                                aria-hidden="true"
                                tabindex="-1"
                            >
                            </select>

                            <!-- ASSIGNATION_JOUR_HEURE_LIBRE_V6_EDIT_VIEW -->
                            <div class="ssa-free-time-grid">
                                <div>
                                    <label class="adm-form-label" for="edit_page_schedule_day">
                                        Jour
                                    </label>

                                    <select
                                        id="edit_page_schedule_day"
                                        class="adm-form-select"
                                    >
                                        <option value="">Choisir un jour</option>
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
                                    <label class="adm-form-label" for="edit_page_schedule_hour">
                                        Heure
                                    </label>

                                    <input
                                        type="time"
                                        id="edit_page_schedule_hour"
                                        class="adm-form-input"
                                        min="08:00"
                                        max="22:00"
                                        step="1800"
                                        placeholder="HH:MM"
                                        autocomplete="off"
                                    >

                                    <small class="assignment-help">
                                        Écrivez l’heure manuellement entre 08:00 et 22:00
                                        (par pas de 30 minutes).
                                    </small>
                                </div>
                            </div>

                            <!-- ASSIGNATION_RESULTAT_CODE_SEUL_V6_2_EDIT -->
                            <div
                                id="editPageGeneratedSlotCode"
                                class="ssa-edit-summary ssa-code-only-preview"
                                hidden
                                style="margin-top:10px;"
                            >
                                <i class="bi bi-tag"></i>

                                <div>
                                    <strong>Résultat final</strong>
                                    <span>—</span>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="ssa-edit-summary">
                        <i class="bi bi-info-circle"></i>

                        <div>
                            <strong>
                                Modification sécurisée
                            </strong>

                            <span>
                                La capacité du groupe est vérifiée
                                une deuxième fois par Laravel au moment
                                de l’enregistrement.
                            </span>
                        </div>
                    </div>

                    <div class="ssa-edit-actions">
                        <a
                            href="{{ route('admin.assign.class') }}"
                            class="adm-btn adm-btn-ghost"
                        >
                            Annuler
                        </a>

                        <button
                            type="submit"
                            class="adm-btn adm-btn-primary"
                        >
                            <i class="bi bi-check2-circle"></i>
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.ssa-edit-grid {
    display: grid;
    grid-template-columns: repeat(
        2,
        minmax(0,1fr)
    );
    gap: 16px;
}

.ssa-edit-full {
    grid-column: 1 / -1;
}

.ssa-required {
    color: #F87171;
}

.ssa-optional {
    color: var(--adm-text-muted);
    font-size: .62rem;
    font-weight: 600;
}

.ssa-help {
    display: block;
    margin-top: 6px;
    color: var(--adm-text-muted);
    font-size: .62rem;
}

.ssa-edit-summary {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin-top: 18px;
    padding: 12px 14px;
    border: 1px solid rgba(96,165,250,.16);
    border-radius: 12px;
    background: rgba(37,99,235,.055);
}

.ssa-edit-summary > i {
    margin-top: 2px;
    color: #60A5FA;
}

.ssa-edit-summary strong,
.ssa-edit-summary span {
    display: block;
}

.ssa-edit-summary strong {
    color: #DBEAFE;
    font-size: .72rem;
}

.ssa-edit-summary span {
    margin-top: 3px;
    color: #94A3B8;
    font-size: .63rem;
}

.ssa-edit-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

@media (max-width: 760px) {
    .ssa-edit-grid {
        grid-template-columns: 1fr;
    }

    .ssa-edit-full {
        grid-column: auto;
    }

    .ssa-edit-actions {
        flex-direction: column-reverse;
    }

    .ssa-edit-actions .adm-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {
        const hierarchy =
            @json($assignmentHierarchy);

        const scheduleMap =
            @json($studentScheduleMap ?? []);

        const selected = {
            subject:
                @json(
                    (string)
                        old(
                            'subject_id',
                            $assignment->subject_id
                        )
                ),
            level:
                @json(
                    (string)
                        old(
                            'level_id',
                            $assignment->level_id
                        )
                ),
            classRoom:
                @json(
                    (string)
                        old(
                            'class_id',
                            $assignment->class_id
                        )
                ),
            group:
                @json(
                    (string)
                        old(
                            'class_slot_id',
                            $assignment->class_slot_id
                        )
                ),
            time:
                @json(
                    (string)
                        old(
                            'schedule_id',
                            $assignment->student_slot_key ?? ''
                        )
                ),
        };

        const subject =
            document.getElementById(
                'edit_page_subject_id'
            );

        const level =
            document.getElementById(
                'edit_page_level_id'
            );

        const classRoom =
            document.getElementById(
                'edit_page_class_id'
            );

        const group =
            document.getElementById(
                'edit_page_group_id'
            );

        const time =
            document.getElementById(
                'edit_page_schedule_id'
            );

        const option = (
            value,
            label,
            isSelected = false,
            disabled = false
        ) => {
            const item =
                document.createElement(
                    'option'
                );

            item.value =
                String(value ?? '');

            item.textContent =
                String(label ?? '');

            item.selected =
                Boolean(isSelected);

            item.disabled =
                Boolean(disabled);

            return item;
        };

        const findSubject = id =>
            hierarchy.find(
                item =>
                    String(item.id)
                    === String(id)
            ) || null;

        const findLevel = (
            subjectItem,
            id
        ) =>
            subjectItem?.levels?.find(
                item =>
                    String(item.id)
                    === String(id)
            ) || null;

        const findClass = (
            levelItem,
            id
        ) =>
            levelItem?.classes?.find(
                item =>
                    String(item.id)
                    === String(id)
            ) || null;

        const fillTime = (
            selectedValue = ''
        ) => {
            const items =
                scheduleMap[
                    String(
                        subject.value
                    )
                ]
                || [];

            time.replaceChildren(
                option(
                    '',
                    'Horaire à définir',
                    !selectedValue
                )
            );

            const classText =
                classRoom?.value
                    ? (
                        classRoom.options[
                            classRoom.selectedIndex
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

            const s =
                findSubject(subject.value);

            const l =
                findLevel(s,level.value);

            const c =
                findClass(l,classRoom.value);

            const groupItem =
                (c?.slots || [])
                    .find(
                        item =>
                            String(item.id)
                            === String(group.value)
                    );

            const groupCode =
                String(
                    groupItem?.code
                    || groupItem?.name
                    || ''
                );

            const match =
                groupCode.match(/(\d+)$/);

            const groupNumber =
                match
                    ? match[1]
                    : '';

            items.forEach(item => {
                const fullCode =
                    classCode
                    && groupNumber
                        ? (
                            String(item.day_code || '')
                            + String(item.slot_number || '')
                            + String(item.subject_code || '')
                            + classCode
                            + groupNumber
                        )
                        : String(item.base_code || item.code || '');

                const label =
                    fullCode
                    + ' — '
                    + String(item.day || '')
                    + ' · '
                    + String(item.time || '');

                time.appendChild(
                    option(
                        item.id,
                        label,
                        String(item.id)
                        === String(selectedValue)
                    )
                );
            });

            time.disabled = false;

            if (selectedValue) {
                time.value = String(selectedValue);
            }
        };
        const fillGroups = (
            selectedValue = ''
        ) => {
            const s =
                findSubject(
                    subject.value
                );

            const l =
                findLevel(
                    s,
                    level.value
                );

            const c =
                findClass(
                    l,
                    classRoom.value
                );

            group.replaceChildren(
                option(
                    '',
                    c
                        ? 'Sélectionner un groupe'
                        : 'Choisissez d’abord une classe'
                )
            );

            if (!c) {
                group.disabled = true;
                return;
            }

            (c.slots || [])
                .forEach(item => {
                    const current =
                        Number(
                            item.current_count
                            || 0
                        );

                    const max =
                        Number(
                            item.max_students
                            || 12
                        );

                    const full =
                        Boolean(
                            item.is_full
                            || current >= max
                        );

                    const currentGroup =
                        String(item.id)
                        === String(
                            selectedValue
                        );

                    group.appendChild(
                        option(
                            item.id,
                            (
                                item.code
                                || item.name
                                || 'Groupe'
                            )
                            + ' — '
                            + current
                            + '/'
                            + max
                            + (
                                full
                                    ? ' — COMPLET'
                                    : ' places'
                            ),
                            currentGroup,
                            full
                                && !currentGroup
                        )
                    );
                });

            group.disabled = false;
        };

        const fillClasses = (
            selectedValue = '',
            selectedGroup = ''
        ) => {
            const s =
                findSubject(
                    subject.value
                );

            const l =
                findLevel(
                    s,
                    level.value
                );

            classRoom.replaceChildren(
                option(
                    '',
                    l
                        ? 'Sélectionner une classe'
                        : 'Choisissez d’abord un niveau'
                )
            );

            if (!l) {
                classRoom.disabled = true;

                fillGroups();
                return;
            }

            (l.classes || [])
                .forEach(item => {
                    classRoom.appendChild(
                        option(
                            item.id,
                            item.name,
                            String(item.id)
                            === String(
                                selectedValue
                            )
                        )
                    );
                });

            classRoom.disabled = false;

            if (selectedValue) {
                classRoom.value =
                    String(
                        selectedValue
                    );
            }

            fillGroups(
                selectedGroup
            );
        };

        const fillLevels = (
            selectedValue = '',
            selectedClass = '',
            selectedGroup = ''
        ) => {
            const s =
                findSubject(
                    subject.value
                );

            level.replaceChildren(
                option(
                    '',
                    s
                        ? 'Sélectionner un niveau'
                        : 'Choisissez d’abord une matière'
                )
            );

            if (!s) {
                level.disabled = true;

                fillClasses();
                return;
            }

            (s.levels || [])
                .forEach(item => {
                    level.appendChild(
                        option(
                            item.id,
                            item.name,
                            String(item.id)
                            === String(
                                selectedValue
                            )
                        )
                    );
                });

            level.disabled = false;

            if (selectedValue) {
                level.value =
                    String(
                        selectedValue
                    );
            }

            fillClasses(
                selectedClass,
                selectedGroup
            );
        };

        subject.addEventListener(
            'change',
            () => {
                fillLevels();
                fillTime();
            }
        );

        level.addEventListener(
            'change',
            () => {
                fillClasses();
            }
        );

        classRoom.addEventListener(
            'change',
            () => {
                fillGroups();
                fillTime(
                    time.value
                );
            }
        );

        group.addEventListener(
            'change',
            () => {
                fillTime(
                    time.value
                );
            }
        );

        /*
         * Préremplissage de l'assignation actuelle.
         */
        subject.value =
            selected.subject;

        fillLevels(
            selected.level,
            selected.classRoom,
            selected.group
        );

        fillTime(
            selected.time
        );
    }
);
</script>

<!-- ASSIGNATION_JOUR_HEURE_LIBRE_V6_EDIT_ADDON -->
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
                'edit_page_schedule_id'
            );

        const day =
            document.getElementById(
                'edit_page_schedule_day'
            );

        const hour =
            document.getElementById(
                'edit_page_schedule_hour'
            );

        const subject =
            document.getElementById(
                'edit_page_subject_id'
            );

        const level =
            document.getElementById(
                'edit_page_level_id'
            );

        const classRoom =
            document.getElementById(
                'edit_page_class_id'
            );

        const group =
            document.getElementById(
                'edit_page_group_id'
            );

        const preview =
            document.getElementById(
                'editPageGeneratedSlotCode'
            );

        if (
            !hiddenTime
            || !day
            || !hour
        ) {
            return;
        }

        const pad = value =>
            String(value)
                .padStart(
                    2,
                    '0'
                );

        const slotToHour =
            slot => {
                const total =
                    (8 * 60)
                    + (
                        (Number(slot) - 1)
                        * 30
                    );

                return (
                    pad(
                        Math.floor(
                            total / 60
                        )
                    )
                    + ':'
                    + pad(
                        total % 60
                    )
                );
            };

        const hourToSlot =
            value => {
                const parts =
                    String(value)
                        .split(':');

                if (
                    parts.length
                    !== 2
                ) {
                    return null;
                }

                const total =
                    (
                        Number(parts[0])
                        * 60
                    )
                    + Number(parts[1]);

                const diff =
                    total
                    - (8 * 60);

                if (
                    diff < 0
                    || diff > (14 * 60)
                    || diff % 30 !== 0
                ) {
                    return null;
                }

                return (
                    diff / 30
                ) + 1;
            };

        const ordinalSlot =
            number => {
                const n =
                    Number(
                        number
                    );

                return n === 1
                    ? '1er créneau'
                    : (
                        n
                        + 'e créneau'
                    );
            };

        const refreshPreview =
            () => {
                if (!preview) {
                    return;
                }

                const option =
                    hiddenTime.value
                        ? hiddenTime.options[
                            hiddenTime.selectedIndex
                        ]
                        : null;

                const code =
                    option
                        ? String(
                            option.textContent
                            || ''
                        ).split(
                            ' — '
                        )[0]
                        : '';

                preview.hidden =
                    !code;

                const span =
                    preview.querySelector(
                        'span'
                    );

                if (span) {
                    span.textContent =
                        code
                        || '—';
                }

                const explanation =
                    document.getElementById(
                        'editPageGeneratedSlotExplanation'
                    );

                if (
                    !explanation
                    || !code
                ) {
                    if (explanation) {
                        explanation.textContent = '';
                    }

                    return;
                }

                const dayLabel =
                    day.options[
                        day.selectedIndex
                    ]?.textContent
                    ?.trim()
                    || '';

                const slotNumber =
                    hourToSlot(
                        hour.value
                    );

                const subjectLabel =
                    subject?.value
                        ? (
                            subject.options[
                                subject.selectedIndex
                            ]?.textContent
                            || ''
                        ).trim()
                        : '';

                const classLabel =
                    classRoom?.value
                        ? (
                            classRoom.options[
                                classRoom.selectedIndex
                            ]?.textContent
                            || ''
                        ).trim()
                        : '';

                const groupLabelRaw =
                    group?.value
                        ? (
                            group.options[
                                group.selectedIndex
                            ]?.textContent
                            || ''
                        )
                        : '';

                const groupMatch =
                    String(
                        groupLabelRaw
                    ).match(
                        /(\d+)/
                    );

                const groupNumber =
                    groupMatch
                        ? groupMatch[1]
                        : '';

                const groupLabel =
                    classLabel
                    && groupNumber
                        ? (
                            classLabel
                            + ' '
                            + groupNumber
                        )
                        : groupLabelRaw;

                explanation.textContent =
                    code
                    + ' => '
                    + dayLabel
                    + ' -> '
                    + ordinalSlot(
                        slotNumber
                    )
                    + ' -> Matière ('
                    + subjectLabel
                    + ') -> Groupe ('
                    + groupLabel
                    + ')';
            };
        const syncHidden =
            () => {
                const selectedDay =
                    Number(
                        day.value
                    );

                const slot =
                    hourToSlot(
                        hour.value
                    );

                if (
                    !selectedDay
                    || !slot
                ) {
                    hiddenTime.value =
                        '';

                    hour.setCustomValidity(
                        hour.value
                            ? 'L’heure doit être comprise entre 08:00 et 22:00 par pas de 30 minutes.'
                            : ''
                    );

                    refreshPreview();
                    return;
                }

                hour.setCustomValidity('');

                const key =
                    selectedDay
                    + ':'
                    + slot;

                const exists =
                    Array
                        .from(
                            hiddenTime.options
                        )
                        .some(
                            item =>
                                String(
                                    item.value
                                )
                                === key
                        );

                hiddenTime.value =
                    exists
                        ? key
                        : '';

                refreshPreview();
            };

        const restoreVisible =
            () => {
                const value =
                    String(
                        hiddenTime.value
                        || ''
                    );

                const match =
                    value.match(
                        /^([1-7]):(\d{1,2})$/
                    );

                if (!match) {
                    refreshPreview();
                    return;
                }

                day.value =
                    match[1];

                hour.value =
                    slotToHour(
                        Number(
                            match[2]
                        )
                    );

                refreshPreview();
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
            group,
        ]
            .filter(Boolean)
            .forEach(
                element => {
                    element.addEventListener(
                        'change',
                        () => {
                            setTimeout(
                                syncHidden,
                                0
                            );
                        }
                    );
                }
            );

        setTimeout(
            restoreVisible,
            0
        );
    }
);
</script>

<!-- ASSIGNATION_RESULTAT_CODE_SEUL_V6_2_EDIT_STYLE -->
<style>
#editPageGeneratedSlotCode.ssa-code-only-preview {
    width: 100%;
    max-width: none;
    overflow: visible;
}

#editPageGeneratedSlotCode.ssa-code-only-preview span {
    display: inline-block;
    max-width: none !important;
    overflow: visible !important;
    text-overflow: clip !important;
    white-space: nowrap !important;
    font-size: .95rem;
    letter-spacing: .02em;
}
</style>

@endsection