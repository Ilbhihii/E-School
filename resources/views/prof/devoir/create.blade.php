@extends('layouts.prof')

@section('title', 'Créer un devoir')
@section('page_title', 'Nouveau devoir')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-file-earmark-plus-fill"></i>
            Nouvelle activité
        </span>

        <h1 class="pp-page-title">Créer un devoir</h1>

        <p class="pp-page-description">
            Choisissez d’abord le parcours exact
            Matière → Niveau → Classe → Créneau.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{ route('prof.devoir.index') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-arrow-left"></i>
            Retour
        </a>
    </div>
</section>

@if($errors->any())
    <div class="adm-alert adm-alert-danger mb-4">
        <strong>Le formulaire contient des erreurs.</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    method="POST"
    action="{{ route('prof.devoir.store') }}"
    enctype="multipart/form-data"
    id="profDevoirCreate"
>
    @csrf

    <div class="pp-form-grid">
        <section class="pp-panel">
            <header class="pp-panel-head">
                <div class="pp-panel-title-wrap">
                    <h2 class="pp-panel-title">
                        <i class="bi bi-diagram-3-fill"></i>
                        Affectation
                    </h2>

                    <p class="pp-panel-subtitle">
                        Le devoir sera visible uniquement
                        par les étudiants de ce créneau.
                    </p>
                </div>
            </header>

            <div class="pp-form-section">
                <div class="pps-form-path">
                    <div class="pp-field">
                        <label
                            for="devoirSubject"
                            class="pp-label"
                        >
                            Matière *
                        </label>

                        <select
                            name="subject_id"
                            id="devoirSubject"
                            class="adm-form-select"
                            required
                        >
                            <option value="">
                                Choisir une matière
                            </option>
                        </select>
                    </div>

                    <div class="pp-field">
                        <label
                            for="devoirLevel"
                            class="pp-label"
                        >
                            Niveau *
                        </label>

                        <select
                            name="level_id"
                            id="devoirLevel"
                            class="adm-form-select"
                            disabled
                            required
                        >
                            <option value="">
                                Choisir un niveau
                            </option>
                        </select>
                    </div>

                    <div class="pp-field">
                        <label
                            for="devoirClass"
                            class="pp-label"
                        >
                            Classe *
                        </label>

                        <select
                            name="class_id"
                            id="devoirClass"
                            class="adm-form-select"
                            disabled
                            required
                        >
                            <option value="">
                                Choisir une classe
                            </option>
                        </select>
                    </div>

                    <div class="pp-field">
                        <label
                            for="devoirSlot"
                            class="pp-label"
                        >
                            Créneau *
                        </label>

                        <select
                            name="class_slot_id"
                            id="devoirSlot"
                            class="adm-form-select"
                            disabled
                            required
                        >
                            <option value="">
                                Choisir un créneau
                            </option>
                        </select>
                    </div>
                </div>

                <div class="pp-field mt-3">
                    <label
                        for="course_id"
                        class="pp-label"
                    >
                        Cours associé
                    </label>

                    <select
                        name="course_id"
                        id="course_id"
                        class="adm-form-select"
                    >
                        <option value="">
                            Aucun cours spécifique
                        </option>

                        @foreach($courses as $courseOption)
                            <option
                                value="{{ $courseOption->id }}"
                                data-subject="{{
                                    $courseOption->subject_id
                                }}"
                                data-level="{{
                                    $courseOption->level_id
                                }}"
                                data-class="{{
                                    $courseOption->class_id
                                }}"
                                data-slot="{{
                                    strtoupper(
                                        trim(
                                            (string)
                                            $courseOption->slot_code
                                        )
                                    )
                                }}"
                                {{
                                    (string) old(
                                        'course_id',
                                        $courseId ?? ''
                                    )
                                    ===
                                    (string) $courseOption->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $courseOption->title }}
                                @if($courseOption->slot_code)
                                    — {{ $courseOption->slot_code }}
                                @endif
                            </option>
                        @endforeach
                    </select>

                    <small class="pp-help">
                        Seuls les cours correspondant au créneau
                        sélectionné restent disponibles.
                    </small>
                </div>
            </div>
        </section>

        <section class="pp-panel">
            <header class="pp-panel-head">
                <div class="pp-panel-title-wrap">
                    <h2 class="pp-panel-title">
                        <i class="bi bi-card-text"></i>
                        Contenu du devoir
                    </h2>
                </div>
            </header>

            <div class="pp-form-section">
                <div class="pp-field">
                    <label
                        for="title"
                        class="pp-label"
                    >
                        Titre *
                    </label>

                    <input
                        type="text"
                        name="title"
                        id="title"
                        value="{{ old('title') }}"
                        class="adm-form-control"
                        maxlength="255"
                        required
                    >
                </div>

                <div class="pp-field">
                    <label
                        for="description"
                        class="pp-label"
                    >
                        Description
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        rows="7"
                        class="adm-form-control"
                    >{{ old('description') }}</textarea>
                </div>

                <div class="pp-field">
                    <label
                        for="due_date"
                        class="pp-label"
                    >
                        Date limite *
                    </label>

                    <input
                        type="date"
                        name="due_date"
                        id="due_date"
                        value="{{ old('due_date') }}"
                        min="{{ now()->addDay()->toDateString() }}"
                        class="adm-form-control"
                        required
                    >
                </div>

                <div class="pp-field">
                    <label
                        for="file"
                        class="pp-label"
                    >
                        Document PDF
                    </label>

                    <input
                        type="file"
                        name="file"
                        id="file"
                        accept="application/pdf,.pdf"
                        class="adm-form-control"
                    >
                </div>
            </div>
        </section>
    </div>

    <section class="pp-panel pp-section-gap">
        <div class="pp-form-actions">
            <a
                href="{{ route('prof.devoir.index') }}"
                class="adm-btn adm-btn-ghost"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="adm-btn adm-btn-success"
            >
                <i class="bi bi-check-circle-fill"></i>
                Publier le devoir
            </button>
        </div>
    </section>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const hierarchy =
            @json($profHierarchy);

        const subject =
            document.getElementById('devoirSubject');

        const level =
            document.getElementById('devoirLevel');

        const classroom =
            document.getElementById('devoirClass');

        const slot =
            document.getElementById('devoirSlot');

        const course =
            document.getElementById('course_id');

        const wantedSubject =
            @json((string) ($selectedSubjectId ?? ''));

        const wantedLevel =
            @json((string) ($selectedLevelId ?? ''));

        const wantedClass =
            @json((string) ($selectedClassId ?? ''));

        const wantedSlot =
            @json((string) ($selectedSlotId ?? ''));

        const makeOption = (
            value,
            label,
            selected = false
        ) => {
            const item =
                document.createElement('option');

            item.value = String(value);
            item.textContent = label;
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

        function refreshCourses() {
            if (!course) {
                return;
            }

            const slotItem =
                classData()
                    ?.slots
                    ?.find(
                        item =>
                            String(item.id)
                            === String(slot.value)
                    );

            const selectedSlotCode =
                String(
                    slotItem?.code || ''
                ).toUpperCase();

            Array.from(
                course.options
            ).forEach(option => {
                if (!option.value) {
                    option.hidden = false;
                    option.disabled = false;
                    return;
                }

                const matches =
                    String(option.dataset.subject)
                        === String(subject.value)
                    && String(option.dataset.level)
                        === String(level.value)
                    && String(option.dataset.class)
                        === String(classroom.value)
                    && String(option.dataset.slot || '')
                        .toUpperCase()
                        === selectedSlotCode;

                option.hidden = !matches;
                option.disabled = !matches;
            });

            const current =
                course.options[
                    course.selectedIndex
                ];

            if (
                current
                && current.value
                && current.disabled
            ) {
                course.value = '';
            }
        }

        function fillSlots(wanted = '') {
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
                slot.appendChild(
                    makeOption(
                        item.id,
                        item.code,
                        String(item.id)
                        === String(wanted)
                    )
                );
            });

            slot.disabled =
                !classData();

            refreshCourses();
        }

        function fillClasses(
            wanted = '',
            wantedSlotId = ''
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
                !levelData();

            fillSlots(wantedSlotId);
        }

        function fillLevels(
            wanted = '',
            wantedClassId = '',
            wantedSlotId = ''
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
                !subjectData();

            fillClasses(
                wantedClassId,
                wantedSlotId
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
            subject.value = wantedSubject;

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
            refreshCourses
        );

        refreshCourses();
    }
);
</script>
@endpush
