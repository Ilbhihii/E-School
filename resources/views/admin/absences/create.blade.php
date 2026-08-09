@extends('layouts.admin')

@section('title', 'Nouvelle absence')
@section('page_title', 'Nouvelle absence')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau → Étudiant')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="adm-page-header">
            <div>
                <h1>Nouvelle absence</h1>
                <div class="subtitle">
                    Sélectionnez le groupe exact :
                    Matière → Niveau → Classe → Créneau.
                </div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <h4>
                    <i
                        class="bi bi-calendar-plus"
                        style="color:rgba(255,255,255,0.35);"
                    ></i>
                    Enregistrer une présence
                </h4>
            </div>

            <div class="adm-card-body">
                <form
                    method="POST"
                    action="{{ route('admin.absences.store') }}"
                    id="absenceCreateForm"
                >
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="absenceSubject"
                                >
                                    Matière
                                </label>

                                <select
                                    name="subject_id"
                                    id="absenceSubject"
                                    class="adm-form-select
                                        @error('subject_id') error @enderror"
                                    required
                                >
                                    <option value="">
                                        -- Sélectionner --
                                    </option>

                                    @foreach($subjects as $subject)
                                        <option
                                            value="{{ $subject->id }}"
                                            {{
                                                (string) old(
                                                    'subject_id',
                                                    request('subject_id')
                                                )
                                                ===
                                                (string) $subject->id
                                                    ? 'selected'
                                                    : ''
                                            }}
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

                        <div class="col-md-3">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="absenceLevel"
                                >
                                    Niveau
                                </label>

                                <select
                                    name="level_id"
                                    id="absenceLevel"
                                    class="adm-form-select
                                        @error('level_id') error @enderror"
                                    data-selected="{{
                                        old(
                                            'level_id',
                                            request('level_id')
                                        )
                                    }}"
                                    required
                                    disabled
                                >
                                    <option value="">
                                        -- Sélectionner --
                                    </option>
                                </select>

                                @error('level_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="absenceClass"
                                >
                                    Classe
                                </label>

                                <select
                                    name="class_id"
                                    id="absenceClass"
                                    class="adm-form-select
                                        @error('class_id') error @enderror"
                                    data-selected="{{
                                        old(
                                            'class_id',
                                            request('class_id')
                                        )
                                    }}"
                                    required
                                    disabled
                                >
                                    <option value="">
                                        -- Sélectionner --
                                    </option>
                                </select>

                                @error('class_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="absenceSlot"
                                >
                                    Créneau
                                </label>

                                <select
                                    name="class_slot_id"
                                    id="absenceSlot"
                                    class="adm-form-select
                                        @error('class_slot_id') error @enderror"
                                    data-selected="{{
                                        old(
                                            'class_slot_id',
                                            request('class_slot_id')
                                        )
                                    }}"
                                    required
                                    disabled
                                >
                                    <option value="">
                                        -- Choisissez d’abord la classe --
                                    </option>
                                </select>

                                <small
                                    style="
                                        display:block;
                                        margin-top:6px;
                                        color:#64748B;
                                        font-size:.7rem;
                                    "
                                >
                                    D1/D2/D3/D4, I1-I4 ou A1-A4
                                    viennent directement de la structure
                                    pédagogique.
                                </small>

                                @error('class_slot_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="absenceStudent"
                                >
                                    Étudiant
                                </label>

                                <select
                                    name="user_id"
                                    id="absenceStudent"
                                    class="adm-form-select
                                        @error('user_id') error @enderror"
                                    data-selected="{{ old('user_id') }}"
                                    required
                                    disabled
                                >
                                    <option value="">
                                        -- Choisissez d’abord le créneau --
                                    </option>
                                </select>

                                <small
                                    style="
                                        display:block;
                                        margin-top:6px;
                                        color:#64748B;
                                        font-size:.7rem;
                                    "
                                >
                                    Seuls les étudiants affectés à ce
                                    créneau sont proposés.
                                </small>

                                @error('user_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="adm-form-group">
                                <label class="adm-form-label">
                                    Date
                                </label>

                                <input
                                    type="date"
                                    name="date"
                                    value="{{
                                        old(
                                            'date',
                                            now()->toDateString()
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('date') error @enderror"
                                    required
                                >

                                @error('date')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="adm-form-group">
                                <label class="adm-form-label">
                                    Statut
                                </label>

                                <select
                                    name="present"
                                    class="adm-form-select
                                        @error('present') error @enderror"
                                    required
                                >
                                    <option
                                        value="0"
                                        {{
                                            old('present', '0') === '0'
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        Absent
                                    </option>

                                    <option
                                        value="1"
                                        {{
                                            old('present') === '1'
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        Présent
                                    </option>
                                </select>

                                @error('present')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button
                            type="submit"
                            class="adm-btn adm-btn-primary flex-fill"
                            id="absenceSubmit"
                        >
                            <i class="bi bi-save"></i>
                            Enregistrer
                        </button>

                        <a
                            href="{{ route('admin.absences') }}"
                            class="adm-btn adm-btn-ghost flex-fill text-center"
                        >
                            <i class="bi bi-arrow-left"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const hierarchy = @json($absenceHierarchy);
    const studentsByPath = @json($studentsByPath);

    const subject =
        document.getElementById('absenceSubject');

    const level =
        document.getElementById('absenceLevel');

    const classroom =
        document.getElementById('absenceClass');

    const slot =
        document.getElementById('absenceSlot');

    const student =
        document.getElementById('absenceStudent');

    if (
        !subject
        || !level
        || !classroom
        || !slot
        || !student
    ) {
        return;
    }

    const wantedLevel =
        String(level.dataset.selected || '');

    const wantedClass =
        String(classroom.dataset.selected || '');

    const wantedSlot =
        String(slot.dataset.selected || '');

    const wantedStudent =
        String(student.dataset.selected || '');

    const makeOption = (
        value,
        label,
        selected = false
    ) => {
        const element =
            document.createElement('option');

        element.value = value;
        element.textContent = label;
        element.selected = selected;

        return element;
    };

    const selectedSubjectData = () =>
        hierarchy.find(
            item =>
                String(item.id)
                === String(subject.value)
        );

    const selectedLevelData =
        subjectData =>
            subjectData?.levels?.find(
                item =>
                    String(item.id)
                    === String(level.value)
            );

    const selectedClassData = () => {
        const subjectData =
            selectedSubjectData();

        const levelData =
            selectedLevelData(subjectData);

        return levelData?.classes?.find(
            item =>
                String(item.id)
                === String(classroom.value)
        );
    };

    const fillStudents = (
        wanted = ''
    ) => {
        student.innerHTML = '';

        const key = [
            subject.value,
            level.value,
            classroom.value,
            slot.value,
        ].join(':');

        const students =
            studentsByPath[key] || [];

        student.appendChild(
            makeOption(
                '',
                students.length
                    ? '-- Sélectionner un étudiant --'
                    : 'Aucun étudiant assigné à ce créneau'
            )
        );

        students.forEach(item => {
            const label =
                item.email
                    ? `${item.name} — ${item.email}`
                    : item.name;

            student.appendChild(
                makeOption(
                    item.id,
                    label,
                    String(item.id)
                    === String(wanted)
                )
            );
        });

        student.disabled =
            !slot.value
            || students.length === 0;
    };

    const fillSlots = (
        wanted = '',
        wantedStudentId = ''
    ) => {
        slot.innerHTML = '';

        const classData =
            selectedClassData();

        const slots =
            classData?.slots || [];

        slot.appendChild(
            makeOption(
                '',
                slots.length
                    ? '-- Sélectionner un créneau --'
                    : 'Aucun créneau configuré'
            )
        );

        slots.forEach(item => {
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
            !classData
            || slots.length === 0;

        if (!classData) {
            slot.value = '';
        }

        fillStudents(
            wantedStudentId
        );
    };

    const fillClasses = (
        wanted = '',
        wantedSlotId = '',
        wantedStudentId = ''
    ) => {
        classroom.innerHTML = '';

        classroom.appendChild(
            makeOption(
                '',
                '-- Sélectionner une classe --'
            )
        );

        const levelData =
            selectedLevelData(
                selectedSubjectData()
            );

        const classes =
            levelData?.classes || [];

        classes.forEach(item => {
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
            !levelData;

        if (!levelData) {
            classroom.value = '';
        }

        fillSlots(
            wantedSlotId,
            wantedStudentId
        );
    };

    const fillLevels = (
        wanted = '',
        wantedClassId = '',
        wantedSlotId = '',
        wantedStudentId = ''
    ) => {
        level.innerHTML = '';

        level.appendChild(
            makeOption(
                '',
                '-- Sélectionner un niveau --'
            )
        );

        const subjectData =
            selectedSubjectData();

        const levels =
            subjectData?.levels || [];

        levels.forEach(item => {
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
            !subjectData;

        if (!subjectData) {
            level.value = '';
        }

        fillClasses(
            wantedClassId,
            wantedSlotId,
            wantedStudentId
        );
    };

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
        () => fillStudents()
    );

    if (subject.value) {
        fillLevels(
            wantedLevel,
            wantedClass,
            wantedSlot,
            wantedStudent
        );
    } else {
        fillLevels();
    }
});
</script>

@endsection
