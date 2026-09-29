@extends('layouts.admin')

@section('title', 'Modifier le live')
@section('page_title', 'Modifier live')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Groupe → Créneau horaire → Modifier'
)

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="adm-page-header">
            <div>
                <h1>Modifier le live</h1>
                <div class="subtitle">
                    Conservez le live dans une structure exacte :
                    Matière → Niveau → Classe → Groupe, puis un créneau horaire indépendant.
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="adm-alert adm-alert-danger mb-4">
                <strong>
                    La modification n’a pas été enregistrée.
                </strong>

                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.lives.update', $live) }}"
        >
            @csrf
            @method('PUT')

            @include(
                'components.pedagogical-path-edit',
                [
                    'hierarchy' => $editHierarchy,
                    'prefix' => 'adminLiveEdit',
                    'selectedSubject' =>
                        $selectedSubjectId,
                    'selectedLevel' =>
                        $selectedLevelId,
                    'selectedClass' =>
                        $selectedClassId,
                    'selectedSlot' =>
                        $selectedSlotId,
                    'slotTitle' =>
                        'Groupe',
                    'slotPlaceholder' =>
                        'Choisir un groupe',
                    'slotTitle' =>
                        'Groupe',
                    'slotPlaceholder' =>
                        'Choisir un groupe',
                ]
            )

            <div class="adm-card mb-4">
                <div class="adm-card-header">
                    <h4>
                        <i
                            class="bi bi-broadcast-pin"
                            style="color:#FB7185;"
                        ></i>
                        Informations du live
                    </h4>
                </div>

                <div class="adm-card-body">
                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="liveTitle"
                        >
                            Titre du live
                        </label>

                        <input
                            id="liveTitle"
                            type="text"
                            name="title"
                            value="{{
                                old(
                                    'title',
                                    $live->title
                                )
                            }}"
                            class="adm-form-control
                                @error('title') error @enderror"
                            maxlength="255"
                            required
                        >

                        @error('title')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">
                            Créneau horaire
                            <span
                                style="
                                    color:#64748B;
                                    font-weight:400;
                                "
                            >
                                (optionnel)
                            </span>
                        </label>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label
                                    class="adm-form-label"
                                    for="liveAssignmentDay"
                                >
                                    Jour
                                </label>

                                <select
                                    id="liveAssignmentDay"
                                    name="assignment_day_of_week"
                                    class="adm-form-select"
                                >
                                    <option value="">Choisir un jour</option>

                                    @foreach([
                                        1 => 'Lundi',
                                        2 => 'Mardi',
                                        3 => 'Mercredi',
                                        4 => 'Jeudi',
                                        5 => 'Vendredi',
                                        6 => 'Samedi',
                                        7 => 'Dimanche',
                                    ] as $dayNumber => $dayLabel)
                                        <option
                                            value="{{ $dayNumber }}"
                                            {{
                                                (string) old(
                                                    'assignment_day_of_week',
                                                    $live->assignment_day_of_week
                                                ) === (string) $dayNumber
                                                    ? 'selected'
                                                    : ''
                                            }}
                                        >
                                            {{ $dayLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label
                                    class="adm-form-label"
                                    for="liveAssignmentStart"
                                >
                                    Heure
                                </label>

                                <input
                                    id="liveAssignmentStart"
                                    type="time"
                                    name="assignment_start_time"
                                    value="{{
                                        old(
                                            'assignment_start_time',
                                            $live->assignment_start_time
                                                ? substr(
                                                    (string) $live->assignment_start_time,
                                                    0,
                                                    5
                                                )
                                                : ''
                                        )
                                    }}"
                                    class="adm-form-control"
                                    min="08:00"
                                    max="22:00"
                                    step="900"
                                >
                            </div>
                        </div>

                        <small
                            style="
                                display:block;
                                margin-top:6px;
                                color:#64748B;
                                font-size:.7rem;
                            "
                        >
                            Le Groupe reste indépendant du Jour et de l’Heure.
                        </small>
                    </div>
                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="professorId"
                        >
                            Personne / professeur affecté au lien
                        </label>

                        <select
                            id="professorId"
                            name="professor_id"
                            class="adm-form-select @error('professor_id') error @enderror"
                            required
                        >
                            <option value="">
                                Choisir un professeur...
                            </option>

                            @foreach($professors as $professor)
                                <option
                                    value="{{ $professor->id }}"
                                    {{ (string) old('professor_id', $selectedProfessorId) === (string) $professor->id ? 'selected' : '' }}
                                >
                                    {{ $professor->name }}
                                    @if($professor->email)
                                        — {{ $professor->email }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        @error('professor_id')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="streamUrl"
                        >
                            Lien du live
                        </label>

                        <input
                            id="streamUrl"
                            type="url"
                            name="stream_url"
                            value="{{
                                old(
                                    'stream_url',
                                    $live->stream_url
                                )
                            }}"
                            class="adm-form-control
                                @error('stream_url') error @enderror"
                            placeholder="https://..."
                            required
                        >

                        @error('stream_url')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="liveDate"
                                >
                                    Date
                                </label>

                                <input
                                    id="liveDate"
                                    type="date"
                                    name="live_date"
                                    value="{{
                                        old(
                                            'live_date',
                                            optional(
                                                $live->live_date
                                            )->format('Y-m-d')
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('live_date') error @enderror"
                                    required
                                >

                                @error('live_date')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="liveStart"
                                >
                                    Heure de début
                                </label>

                                <input
                                    id="liveStart"
                                    type="time"
                                    name="start_time"
                                    min="08:00"
                                    max="22:00"
                                    step="900"
                                    value="{{
                                        old(
                                            'start_time',
                                            substr(
                                                (string) $live->start_time,
                                                0,
                                                5
                                            )
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('start_time') error @enderror"
                                    required
                                >

                                @error('start_time')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="liveEnd"
                                >
                                    Heure de fin
                                </label>

                                <input
                                    id="liveEnd"
                                    type="time"
                                    name="end_time"
                                    value="{{
                                        old(
                                            'end_time',
                                            substr(
                                                (string) $live->end_time,
                                                0,
                                                5
                                            )
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('end_time') error @enderror"
                                    required
                                >

                                @error('end_time')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div
                        id="admin_live_edit_code_preview"
                        style="
                            display:none;
                            margin-top:12px;
                            padding:10px 12px;
                            border-radius:10px;
                            border:1px solid rgba(99,102,241,.18);
                            background:rgba(99,102,241,.06);
                            color:#C4B5FD;
                            font-size:.8rem;
                            font-weight:700;
                        "
                    >
                        Résultat final :
                        <strong id="admin_live_edit_code">—</strong>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3">
                <a
                    href="{{ route('admin.lives.index') }}"
                    class="adm-btn adm-btn-ghost flex-fill text-center"
                >
                    <i class="bi bi-arrow-left"></i>
                    Annuler
                </a>

                <button
                    type="submit"
                    class="adm-btn adm-btn-primary flex-fill"
                >
                    <i class="bi bi-save"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
<!-- LIVE_PEDAGOGICAL_CODE_ADMIN_PROF_V1_EDIT -->
<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {
        const hierarchy =
            @json($editHierarchy);

        const subject =
            document.getElementById(
                'adminLiveEditSubject'
            );

        const level =
            document.getElementById(
                'adminLiveEditLevel'
            );

        const classroom =
            document.getElementById(
                'adminLiveEditClass'
            );

        const slot =
            document.getElementById(
                'adminLiveEditSlot'
            );

        const date =
            document.getElementById(
                'liveDate'
            );

        const start =
            document.getElementById(
                'liveStart'
            );

        const assignmentDay =
            document.getElementById(
                'liveAssignmentDay'
            );

        const assignmentStart =
            document.getElementById(
                'liveAssignmentStart'
            );

        const assignmentDay =
            document.getElementById(
                'liveAssignmentDay'
            );

        const assignmentStart =
            document.getElementById(
                'liveAssignmentStart'
            );

        const end =
            document.getElementById(
                'liveEnd'
            );

        const output =
            document.getElementById(
                'admin_live_edit_code'
            );

        const preview =
            document.getElementById(
                'admin_live_edit_code_preview'
            );

        if (
            !subject
            || !level
            || !classroom
            || !slot
            || !date
            || !start
            || !output
            || !preview
        ) {
            return;
        }

        const strip =
            value =>
                String(value || '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '');

        const dayCode =
            value => {
                if (!value) {
                    return '';
                }

                const d =
                    new Date(
                        value
                        + 'T12:00:00'
                    );

                return {
                    0:'D',
                    1:'L',
                    2:'MA',
                    3:'M',
                    4:'J',
                    5:'V',
                    6:'S',
                }[d.getDay()] || '';
            };

        const slotNumber =
            value => {
                const match =
                    String(value || '')
                        .match(
                            /^(\d{2}):(\d{2})$/
                        );

                if (!match) {
                    return null;
                }

                const diff =
                    (
                        Number(match[1])
                        * 60
                        + Number(match[2])
                    )
                    - (8 * 60);

                if (
                    diff < 0
                    || diff > 14 * 60
                    || diff % 30 !== 0
                ) {
                    return null;
                }

                return (
                    diff / 30
                ) + 1;
            };

        const add90 =
            value => {
                const match =
                    String(value || '')
                        .match(
                            /^(\d{2}):(\d{2})$/
                        );

                if (!match) {
                    return '';
                }

                let total =
                    Number(match[1]) * 60
                    + Number(match[2])
                    + 90;

                total %= 24 * 60;

                return (
                    String(
                        Math.floor(total / 60)
                    ).padStart(2, '0')
                    + ':'
                    + String(total % 60)
                        .padStart(2, '0')
                );
            };

        const update =
            () => {
                const s =
                    hierarchy.find(
                        item =>
                            String(item.id)
                            === String(subject.value)
                    );

                const l =
                    s?.levels?.find(
                        item =>
                            String(item.id)
                            === String(level.value)
                    );

                const c =
                    l?.classes?.find(
                        item =>
                            String(item.id)
                            === String(classroom.value)
                    );

                const g =
                    c?.slots?.find(
                        item =>
                            String(item.id)
                            === String(slot.value)
                    );

                const subjectCode =
                    strip(s?.name)
                        .toUpperCase()
                        .replace(
                            /[^A-Z0-9]/g,
                            ''
                        )
                        .slice(0, 2);

                const normalizedClass =
                    strip(c?.name)
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
                        strip(c?.name)
                            .replace(
                                /[^A-Za-z0-9]/g,
                                ''
                            )
                            .charAt(0)
                            .toUpperCase();
                }

                const groupMatch =
                    String(g?.code || '')
                        .match(
                            /(\d+)$/
                        );

                const groupNumber =
                    groupMatch
                        ? groupMatch[1]
                        : '';

                const dayMap = {
                    1:'L',
                    2:'MA',
                    3:'M',
                    4:'J',
                    5:'V',
                    6:'S',
                    7:'D',
                };

                const scopeDayCode =
                    assignmentDay?.value
                        ? dayMap[
                            Number(
                                assignmentDay.value
                            )
                        ]
                        : dayCode(
                            date.value
                        );

                const scopeNumber =
                    slotNumber(
                        assignmentStart?.value
                        || start.value
                    );

                const code =
                    scopeDayCode
                    && scopeNumber
                    && subjectCode
                    && classCode
                    && groupNumber
                        ? (
                            scopeDayCode
                            + scopeNumber
                            + subjectCode
                            + classCode
                            + groupNumber
                        )
                        : '';

                output.textContent =
                    code || '—';

                preview.style.display =
                    code
                        ? 'block'
                        : 'none';

                if (
                    start.value
                    && end
                ) {
                    end.value =
                        add90(
                            start.value
                        );
                }
            };

        [
            subject,
            level,
            classroom,
            slot,
            assignmentDay,
            assignmentStart,
            date,
            start,
        ].forEach(
            element => {
                element.addEventListener(
                    'change',
                    () =>
                        setTimeout(
                            update,
                            0
                        )
                );

                element.addEventListener(
                    'input',
                    () =>
                        setTimeout(
                            update,
                            0
                        )
                );
            }
        );

        setTimeout(
            update,
            0
        );
    }
);
</script>


<!-- TIME_SLOT_RANK_LIVE_EDIT_V1 -->
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

        const form =
            document.querySelector(
                'form[action*="/admin/lives/"]'
            );

        const group =
            document.getElementById(
                'adminLiveEditSlot'
            );

        if (
            form
            && group
            && !document.getElementById(
                'liveAssignmentDay'
            )
        ) {
            const wrapper =
                document.createElement(
                    'div'
                );

            wrapper.className =
                'adm-form-group';

            wrapper.innerHTML = `
                <label class="adm-form-label">
                    Créneau horaire
                    <span style="color:#64748B;font-weight:400;">
                        (optionnel)
                    </span>
                </label>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="adm-form-label">
                            Jour
                        </label>

                        <select
                            id="liveAssignmentDay"
                            name="assignment_day_of_week"
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

                    <div class="col-md-6">
                        <label class="adm-form-label">
                            Heure
                        </label>

                        <input
                            id="liveAssignmentStart"
                            name="assignment_start_time"
                            type="time"
                            class="adm-form-control"
                            min="08:00"
                            max="22:00"
                            step="60"
                        >
                    </div>
                </div>

                <small style="display:block;margin-top:6px;color:#64748B;font-size:.7rem;">
                    Groupe indépendant. Heure libre à la minute près.
                </small>
            `;

            const groupBlock =
                group.closest(
                    '.adm-form-group'
                )
                || group.parentElement;

            groupBlock
                ?.insertAdjacentElement(
                    'afterend',
                    wrapper
                );
        }

        const day =
            document.getElementById(
                'liveAssignmentDay'
            );

        const hour =
            document.getElementById(
                'liveAssignmentStart'
            );

        if (day) {
            day.value =
                @json(
                    (string) old(
                        'assignment_day_of_week',
                        $live->assignment_day_of_week ?? ''
                    )
                );
        }

        if (hour) {
            hour.value =
                @json(
                    (string) old(
                        'assignment_start_time',
                        !empty($live->assignment_start_time)
                            ? substr(
                                (string) $live->assignment_start_time,
                                0,
                                5
                            )
                            : ''
                    )
                );
        }

        const liveStart =
            document.getElementById(
                'liveStart'
            );

        if (liveStart) {
            liveStart.step = '60';
        }

        const subject =
            document.getElementById(
                'adminLiveEditSubject'
            );

        const classroom =
            document.getElementById(
                'adminLiveEditClass'
            );

        const slot =
            document.getElementById(
                'adminLiveEditSlot'
            );

        const date =
            document.getElementById(
                'liveDate'
            );

        const output =
            document.getElementById(
                'admin_live_edit_code'
            );

        const preview =
            document.getElementById(
                'admin_live_edit_code_preview'
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

        const dayFromDate =
            value => {
                if (!value) {
                    return null;
                }

                const d =
                    new Date(
                        value
                        + 'T12:00:00'
                    );

                return d.getDay() === 0
                    ? 7
                    : d.getDay();
            };

        const numberFor =
            (
                selectedDay,
                selectedHour
            ) => {
                const dayKey =
                    String(
                        selectedDay
                        || ''
                    );

                const time =
                    String(
                        selectedHour
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

        const refresh =
            () => {
                if (
                    !subject
                    || !classroom
                    || !slot
                    || !date
                    || !liveStart
                    || !output
                    || !preview
                ) {
                    return;
                }

                const selectedDay =
                    Number(
                        day?.value
                        || dayFromDate(
                            date.value
                        )
                    );

                const selectedHour =
                    String(
                        hour?.value
                        || liveStart.value
                        || ''
                    )
                        .slice(
                            0,
                            5
                        );

                const number =
                    numberFor(
                        selectedDay,
                        selectedHour
                    );

                const subjectText =
                    subject.value
                        ? (
                            subject.options[
                                subject.selectedIndex
                            ]?.textContent
                            || ''
                        )
                        : '';

                let sCode =
                    normalize(
                        subjectText
                    )
                        .toUpperCase()
                        .replace(
                            /[^A-Z0-9]/g,
                            ''
                        )
                        .slice(
                            0,
                            2
                        );

                if (!sCode) {
                    sCode = 'MT';
                }

                if (
                    sCode.length === 1
                ) {
                    sCode += 'X';
                }

                const classText =
                    normalize(
                        classroom.value
                            ? (
                                classroom.options[
                                    classroom.selectedIndex
                                ]?.textContent
                                || ''
                            )
                            : ''
                    )
                        .toLowerCase();

                let cCode = '';

                if (
                    classText.includes(
                        'debut'
                    )
                ) {
                    cCode = 'D';
                } else if (
                    classText.includes(
                        'inter'
                    )
                ) {
                    cCode = 'I';
                } else if (
                    classText.includes(
                        'avance'
                    )
                    || classText.includes(
                        'adulte'
                    )
                ) {
                    cCode = 'A';
                } else {
                    cCode =
                        classText
                            .replace(
                                /[^a-z0-9]/g,
                                ''
                            )
                            .charAt(0)
                            .toUpperCase();
                }

                const groupText =
                    slot.value
                        ? (
                            slot.options[
                                slot.selectedIndex
                            ]?.textContent
                            || ''
                        )
                        : '';

                const match =
                    String(
                        groupText
                    )
                        .match(
                            /(\d+)/
                        );

                const gNumber =
                    match
                        ? match[1]
                        : '';

                const code =
                    dayCodes[
                        selectedDay
                    ]
                    && number
                    && sCode
                    && cCode
                    && gNumber
                        ? (
                            dayCodes[
                                selectedDay
                            ]
                            + number
                            + sCode
                            + cCode
                            + gNumber
                        )
                        : '';

                output.textContent =
                    code
                    || '—';

                preview.style.display =
                    code
                        ? 'block'
                        : 'none';
            };

        [
            day,
            hour,
            subject,
            classroom,
            slot,
            date,
            liveStart,
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

        setTimeout(
            refresh,
            0
        );
    }
);
</script>

@endsection

<!-- TIME_SLOT_CHRONOLOGICAL_RANK_V2 -->
