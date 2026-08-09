@php
    $pathHierarchy = $hierarchy ?? [];
    $pathPrefix = $prefix ?? 'pathEdit';

    $pathSubjectName = $subjectName ?? 'subject_id';
    $pathLevelName = $levelName ?? 'level_id';
    $pathClassName = $className ?? 'class_id';
    $pathSlotName = $slotName ?? 'class_slot_id';

    $pathSlotValueKey = $slotValueKey ?? 'id';
    $pathSlotLabelKey = $slotLabelKey ?? 'label';

    $pathSelectedSubject =
        (string) ($selectedSubject ?? '');

    $pathSelectedLevel =
        (string) ($selectedLevel ?? '');

    $pathSelectedClass =
        (string) ($selectedClass ?? '');

    $pathSelectedSlot =
        (string) ($selectedSlot ?? '');

    $pathRequired = $required ?? true;
@endphp

<div
    class="adm-card mb-4"
    style="
        background:rgba(59,130,246,0.04);
        border-color:rgba(99,102,241,0.16);
    "
>
    <div class="adm-card-header">
        <h4>
            <i
                class="bi bi-diagram-3-fill"
                style="color:#818CF8;"
            ></i>
            Parcours pédagogique
        </h4>

        <span
            style="
                color:var(--adm-text-muted);
                font-size:.72rem;
            "
        >
            Matière → Niveau → Classe → Créneau
        </span>
    </div>

    <div class="adm-card-body">
        <div
            id="{{ $pathPrefix }}Preview"
            style="
                display:flex;
                flex-wrap:wrap;
                gap:6px;
                align-items:center;
                margin-bottom:14px;
                padding:10px 12px;
                border:1px dashed rgba(99,102,241,.18);
                border-radius:10px;
                color:#A5B4FC;
                background:rgba(99,102,241,.045);
                font-size:.72rem;
                font-weight:700;
            "
        >
            <span>Matière</span>
            <i class="bi bi-chevron-right"></i>
            <span>Niveau</span>
            <i class="bi bi-chevron-right"></i>
            <span>Classe</span>
            <i class="bi bi-chevron-right"></i>
            <span>Créneau</span>
        </div>

        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="adm-form-group" style="margin-bottom:0;">
                    <label
                        for="{{ $pathPrefix }}Subject"
                        class="adm-form-label"
                    >
                        Matière
                        @if($pathRequired)
                            <span style="color:var(--adm-danger);">*</span>
                        @endif
                    </label>

                    <select
                        id="{{ $pathPrefix }}Subject"
                        name="{{ $pathSubjectName }}"
                        class="adm-form-select"
                        @if($pathRequired) required @endif
                    >
                        <option value="">
                            Choisir une matière
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="adm-form-group" style="margin-bottom:0;">
                    <label
                        for="{{ $pathPrefix }}Level"
                        class="adm-form-label"
                    >
                        Niveau
                        @if($pathRequired)
                            <span style="color:var(--adm-danger);">*</span>
                        @endif
                    </label>

                    <select
                        id="{{ $pathPrefix }}Level"
                        name="{{ $pathLevelName }}"
                        class="adm-form-select"
                        disabled
                        @if($pathRequired) required @endif
                    >
                        <option value="">
                            Choisir un niveau
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="adm-form-group" style="margin-bottom:0;">
                    <label
                        for="{{ $pathPrefix }}Class"
                        class="adm-form-label"
                    >
                        Classe
                        @if($pathRequired)
                            <span style="color:var(--adm-danger);">*</span>
                        @endif
                    </label>

                    <select
                        id="{{ $pathPrefix }}Class"
                        name="{{ $pathClassName }}"
                        class="adm-form-select"
                        disabled
                        @if($pathRequired) required @endif
                    >
                        <option value="">
                            Choisir une classe
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="adm-form-group" style="margin-bottom:0;">
                    <label
                        for="{{ $pathPrefix }}Slot"
                        class="adm-form-label"
                    >
                        Créneau
                        @if($pathRequired)
                            <span style="color:var(--adm-danger);">*</span>
                        @endif
                    </label>

                    <select
                        id="{{ $pathPrefix }}Slot"
                        name="{{ $pathSlotName }}"
                        class="adm-form-select"
                        disabled
                        @if($pathRequired) required @endif
                    >
                        <option value="">
                            Choisir un créneau
                        </option>
                    </select>
                </div>
            </div>
        </div>

        @error($pathSubjectName)
            <div class="adm-form-error mt-2">
                {{ $message }}
            </div>
        @enderror

        @error($pathLevelName)
            <div class="adm-form-error mt-2">
                {{ $message }}
            </div>
        @enderror

        @error($pathClassName)
            <div class="adm-form-error mt-2">
                {{ $message }}
            </div>
        @enderror

        @error($pathSlotName)
            <div class="adm-form-error mt-2">
                {{ $message }}
            </div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const hierarchy =
            @json($pathHierarchy);

        const subject =
            document.getElementById(
                @json($pathPrefix . 'Subject')
            );

        const level =
            document.getElementById(
                @json($pathPrefix . 'Level')
            );

        const classroom =
            document.getElementById(
                @json($pathPrefix . 'Class')
            );

        const slot =
            document.getElementById(
                @json($pathPrefix . 'Slot')
            );

        const preview =
            document.getElementById(
                @json($pathPrefix . 'Preview')
            );

        if (
            !subject
            || !level
            || !classroom
            || !slot
        ) {
            return;
        }

        const wantedSubject =
            @json($pathSelectedSubject);

        const wantedLevel =
            @json($pathSelectedLevel);

        const wantedClass =
            @json($pathSelectedClass);

        const wantedSlot =
            @json($pathSelectedSlot);

        const slotValueKey =
            @json($pathSlotValueKey);

        const slotLabelKey =
            @json($pathSlotLabelKey);

        const makeOption = (
            value,
            label,
            selected = false
        ) => {
            const item =
                document.createElement('option');

            item.value =
                value === null
                    || typeof value === 'undefined'
                    ? ''
                    : String(value);

            item.textContent =
                String(label ?? value ?? '');

            item.selected = selected;

            return item;
        };

        const subjectData = () =>
            hierarchy.find(
                item =>
                    String(item.id)
                    === String(subject.value)
            );

        const levelData = () =>
            subjectData()
                ?.levels
                ?.find(
                    item =>
                        String(item.id)
                        === String(level.value)
                );

        const classData = () =>
            levelData()
                ?.classes
                ?.find(
                    item =>
                        String(item.id)
                        === String(classroom.value)
                );

        const selectedSlotData = () =>
            (classData()?.slots || [])
                .find(
                    item =>
                        String(item[slotValueKey])
                        === String(slot.value)
                );

        function updatePreview() {
            if (!preview) {
                return;
            }

            const subjectItem =
                subjectData();

            const levelItem =
                levelData();

            const classItem =
                classData();

            const slotItem =
                selectedSlotData();

            const labels = [
                subjectItem?.name || 'Matière',
                levelItem?.name || 'Niveau',
                classItem?.name || 'Classe',
                slotItem?.code
                    || slotItem?.label
                    || 'Créneau',
            ];

            preview.innerHTML =
                labels
                    .map(
                        (label, index) =>
                            (
                                index === 0
                                    ? ''
                                    : '<i class="bi bi-chevron-right"></i>'
                            )
                            + '<span>'
                            + String(label)
                                .replace(/&/g, '&amp;')
                                .replace(/</g, '&lt;')
                                .replace(/>/g, '&gt;')
                                .replace(/"/g, '&quot;')
                            + '</span>'
                    )
                    .join('');
        }

        function fillSlots(
            wanted = ''
        ) {
            slot.innerHTML = '';

            slot.appendChild(
                makeOption(
                    '',
                    'Choisir un créneau'
                )
            );

            const items =
                classData()?.slots || [];

            items.forEach(item => {
                const value =
                    item[slotValueKey];

                const label =
                    item[slotLabelKey]
                    ?? item.code
                    ?? value;

                slot.appendChild(
                    makeOption(
                        value,
                        label,
                        String(value)
                        === String(wanted)
                    )
                );
            });

            slot.disabled =
                !classData()
                || items.length === 0;

            if (wanted) {
                slot.value =
                    String(wanted);
            }

            updatePreview();
        }

        function fillClasses(
            wanted = '',
            wantedSlot = ''
        ) {
            classroom.innerHTML = '';

            classroom.appendChild(
                makeOption(
                    '',
                    'Choisir une classe'
                )
            );

            const items =
                levelData()?.classes || [];

            items.forEach(item => {
                classroom.appendChild(
                    makeOption(
                        item.id,
                        item.name,
                        String(item.id)
                        === String(wanted)
                    )
                );
            });

            classroom.disabled =
                !levelData()
                || items.length === 0;

            if (wanted) {
                classroom.value =
                    String(wanted);
            }

            fillSlots(
                wantedSlot
            );
        }

        function fillLevels(
            wanted = '',
            wantedClass = '',
            wantedSlot = ''
        ) {
            level.innerHTML = '';

            level.appendChild(
                makeOption(
                    '',
                    'Choisir un niveau'
                )
            );

            const items =
                subjectData()?.levels || [];

            items.forEach(item => {
                level.appendChild(
                    makeOption(
                        item.id,
                        item.name,
                        String(item.id)
                        === String(wanted)
                    )
                );
            });

            level.disabled =
                !subjectData()
                || items.length === 0;

            if (wanted) {
                level.value =
                    String(wanted);
            }

            fillClasses(
                wantedClass,
                wantedSlot
            );
        }

        hierarchy.forEach(item => {
            subject.appendChild(
                makeOption(
                    item.id,
                    item.name,
                    String(item.id)
                    === String(wantedSubject)
                )
            );
        });

        if (wantedSubject) {
            subject.value =
                String(wantedSubject);

            fillLevels(
                wantedLevel,
                wantedClass,
                wantedSlot
            );
        } else {
            fillLevels();
        }

        subject.addEventListener(
            'change',
            () => fillLevels()
        );

        level.addEventListener(
            'change',
            () => fillClasses()
        );

        classroom.addEventListener(
            'change',
            () => fillSlots()
        );

        slot.addEventListener(
            'change',
            updatePreview
        );
    }
);
</script>
@endpush
