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
                                        step="60"
                                        placeholder="HH:MM"
                                        autocomplete="off"
                                    >

                                    <small class="assignment-help">
                                        Choisissez librement l’heure entre 08:00 et 22:00, à la minute près.
                                        Exemples : 08:15, 08:20, 13:45, 15:20…
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

<!-- ASSIGNATION_HEURE_LIBRE_MINUTE_V1_EDIT -->
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

        const clean =
            value =>
                String(value || '')
                    .normalize('NFD')
                    .replace(
                        /[\u0300-\u036f]/g,
                        ''
                    );

        const parseHour =
            value => {
                const match =
                    String(value || '')
                        .match(
                            /^(\d{2}):(\d{2})$/
                        );

                if (!match) {
                    return null;
                }

                const h =
                    Number(match[1]);

                const m =
                    Number(match[2]);

                const total =
                    h * 60
                    + m;

                if (
                    h < 0
                    || h > 23
                    || m < 0
                    || m > 59
                    || total < 8 * 60
                    || total > 22 * 60
                ) {
                    return null;
                }

                const diff =
                    total
                    - 8 * 60;

                const aligned =
                    diff % 30 === 0;

                const part =
                    aligned
                        ? String(
                            (diff / 30)
                            + 1
                        )
                        : (
                            String(h)
                                .padStart(
                                    2,
                                    '0'
                                )
                            + String(m)
                                .padStart(
                                    2,
                                    '0'
                                )
                        );

                return {
                    keyPart: part,
                    codePart: part,
                };
            };

        const codeForCurrent =
            parsed => {
                const dayCode =
                    ({
                        1:'L',
                        2:'MA',
                        3:'M',
                        4:'J',
                        5:'V',
                        6:'S',
                        7:'D',
                    })[
                        Number(
                            day.value
                        )
                    ]
                    || '';

                const subjectText =
                    subject?.value
                        ? subject.options[
                            subject.selectedIndex
                        ]?.textContent
                        : '';

                let subjectCode =
                    clean(subjectText)
                        .toUpperCase()
                        .replace(
                            /[^A-Z0-9]/g,
                            ''
                        )
                        .slice(
                            0,
                            2
                        );

                if (
                    subjectCode.length
                    === 1
                ) {
                    subjectCode += 'X';
                }

                const classText =
                    classRoom?.value
                        ? classRoom.options[
                            classRoom.selectedIndex
                        ]?.textContent
                        : '';

                const normalizedClass =
                    clean(classText)
                        .toLowerCase();

                let classCode = '';

                if (
                    normalizedClass.includes(
                        'debut'
                    )
                ) {
                    classCode = 'D';
                } else if (
                    normalizedClass.includes(
                        'inter'
                    )
                ) {
                    classCode = 'I';
                } else if (
                    normalizedClass.includes(
                        'avance'
                    )
                ) {
                    classCode = 'A';
                } else {
                    classCode =
                        normalizedClass
                            .replace(
                                /[^a-z0-9]/g,
                                ''
                            )
                            .charAt(0)
                            .toUpperCase();
                }

                const groupText =
                    group?.value
                        ? (
                            group.options[
                                group.selectedIndex
                            ]?.textContent
                            || ''
                        )
                        : '';

                const groupMatch =
                    String(groupText)
                        .match(
                            /(\d+)/
                        );

                const groupNumber =
                    groupMatch
                        ? groupMatch[1]
                        : '';

                if (
                    !dayCode
                    || !parsed
                    || !subjectCode
                    || !classCode
                    || !groupNumber
                ) {
                    return '';
                }

                return (
                    dayCode
                    + parsed.codePart
                    + subjectCode
                    + classCode
                    + groupNumber
                );
            };

        const ensureOption =
            (
                key,
                code
            ) => {
                let option =
                    Array
                        .from(
                            hiddenTime.options
                        )
                        .find(
                            item =>
                                String(
                                    item.value
                                )
                                === String(key)
                        );

                if (!option) {
                    option =
                        document
                            .createElement(
                                'option'
                            );

                    option.value =
                        String(key);

                    hiddenTime
                        .appendChild(
                            option
                        );
                }

                option.dataset.code =
                    code;

                option.textContent =
                    code;

                return option;
            };

        const refreshPreview =
            code => {
                if (!preview) {
                    return;
                }

                preview.hidden =
                    !code;

                const span =
                    preview.querySelector(
                        'span'
                    );

                if (span) {
                    span.textContent =
                        code || '—';
                }
            };

        const syncHidden =
            () => {
                const selectedDay =
                    Number(
                        day.value
                    );

                if (
                    !selectedDay
                    || !hour.value
                ) {
                    hiddenTime.value =
                        '';

                    hour.setCustomValidity('');

                    refreshPreview('');

                    return;
                }

                const parsed =
                    parseHour(
                        hour.value
                    );

                if (!parsed) {
                    hiddenTime.value =
                        '';

                    hour.setCustomValidity(
                        'L’heure doit être comprise entre 08:00 et 22:00.'
                    );

                    refreshPreview('');

                    return;
                }

                hour.setCustomValidity('');

                const key =
                    selectedDay
                    + ':'
                    + parsed.keyPart;

                const code =
                    codeForCurrent(
                        parsed
                    );

                ensureOption(
                    key,
                    code
                );

                hiddenTime.value =
                    key;

                refreshPreview(
                    code
                );
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
                        /^([1-7]):([0-9]{1,4})$/
                    );

                if (!match) {
                    return;
                }

                day.value =
                    match[1];

                const raw =
                    match[2];

                if (
                    raw.length === 4
                ) {
                    hour.value =
                        raw.slice(
                            0,
                            2
                        )
                        + ':'
                        + raw.slice(
                            2,
                            4
                        );
                } else {
                    const slot =
                        Number(raw);

                    const total =
                        8 * 60
                        + (
                            slot - 1
                        )
                        * 30;

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

                refreshPreview(
                    codeForCurrent(
                        parseHour(
                            hour.value
                        )
                    )
                );
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
            classRoom,
            group,
        ]
            .filter(Boolean)
            .forEach(
                element => {
                    element
                        .addEventListener(
                            'change',
                            () =>
                                setTimeout(
                                    syncHidden,
                                    0
                                )
                        );
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
