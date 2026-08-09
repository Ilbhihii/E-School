@extends('layouts.prof')

@section('title', 'Présences et absences')
@section('page_title', 'Présences et absences')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-person-check-fill"></i>
            Suivi des étudiants
        </span>

        <h1 class="pp-page-title">Faire l’appel</h1>

        <p class="pp-page-description">
            Sélectionnez le parcours exact
            Matière → Niveau → Classe → Créneau,
            puis marquez les présences.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{ route('prof.absences.list') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-clock-history"></i>
            Historique
        </a>
    </div>
</section>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-diagram-3-fill"></i>
                Parcours et date
            </h2>

            <p class="pp-panel-subtitle">
                Les étudiants sont chargés uniquement
                depuis le créneau sélectionné.
            </p>
        </div>
    </header>

    <div class="pp-panel-body">
        <form
            method="POST"
            action="{{ route('prof.absences.store') }}"
            id="attendanceForm"
        >
            @csrf

            <div class="pps-form-path">
                <div class="pp-field">
                    <label
                        for="attendanceSubject"
                        class="pp-label"
                    >
                        Matière
                    </label>

                    <select
                        name="subject_id"
                        id="attendanceSubject"
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
                        for="attendanceLevel"
                        class="pp-label"
                    >
                        Niveau
                    </label>

                    <select
                        name="level_id"
                        id="attendanceLevel"
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
                        for="attendanceClass"
                        class="pp-label"
                    >
                        Classe
                    </label>

                    <select
                        name="class_id"
                        id="attendanceClass"
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
                        for="attendanceSlot"
                        class="pp-label"
                    >
                        Créneau
                    </label>

                    <select
                        name="class_slot_id"
                        id="attendanceSlot"
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
                    for="attendanceDate"
                    class="pp-label"
                >
                    Date
                </label>

                <input
                    type="date"
                    id="attendanceDate"
                    name="date"
                    value="{{ old('date', now()->toDateString()) }}"
                    class="adm-form-control"
                    required
                >
            </div>

            <div
                id="studentsList"
                class="mt-4"
            >
                <div class="pps-empty">
                    Choisissez Matière → Niveau → Classe → Créneau.
                </div>
            </div>

            <div class="pp-form-actions mt-4">
                <button
                    type="submit"
                    id="attendanceSubmit"
                    class="adm-btn adm-btn-success"
                    disabled
                >
                    <i class="bi bi-check2-all"></i>
                    Enregistrer les présences
                </button>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const hierarchy =
            @json($profHierarchy);

        const subject =
            document.getElementById(
                'attendanceSubject'
            );

        const level =
            document.getElementById(
                'attendanceLevel'
            );

        const classroom =
            document.getElementById(
                'attendanceClass'
            );

        const slot =
            document.getElementById(
                'attendanceSlot'
            );

        const list =
            document.getElementById(
                'studentsList'
            );

        const submit =
            document.getElementById(
                'attendanceSubmit'
            );

        const baseUrl =
            @json(
                url('/prof/class-students')
            );

        const option = (
            value,
            label
        ) => {
            const item =
                document.createElement('option');

            item.value = String(value);
            item.textContent = label;

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

        function fillSlots() {
            slot.innerHTML = '';
            slot.appendChild(
                option(
                    '',
                    'Choisir un créneau'
                )
            );

            (classData()?.slots || [])
                .forEach(item => {
                    slot.appendChild(
                        option(
                            item.id,
                            item.code
                        )
                    );
                });

            slot.disabled =
                !classData();

            clearStudents();
        }

        function fillClasses() {
            classroom.innerHTML = '';
            classroom.appendChild(
                option(
                    '',
                    'Choisir une classe'
                )
            );

            (levelData()?.classes || [])
                .forEach(item => {
                    classroom.appendChild(
                        option(
                            item.id,
                            item.name
                        )
                    );
                });

            classroom.disabled =
                !levelData();

            fillSlots();
        }

        function fillLevels() {
            level.innerHTML = '';
            level.appendChild(
                option(
                    '',
                    'Choisir un niveau'
                )
            );

            (subjectData()?.levels || [])
                .forEach(item => {
                    level.appendChild(
                        option(
                            item.id,
                            item.name
                        )
                    );
                });

            level.disabled =
                !subjectData();

            fillClasses();
        }

        function clearStudents() {
            submit.disabled = true;

            list.innerHTML = `
                <div class="pps-empty">
                    Choisissez le créneau pour charger les étudiants.
                </div>
            `;
        }

        function initials(name) {
            return String(name || 'E')
                .trim()
                .split(/\s+/)
                .slice(0, 2)
                .map(part => part.charAt(0).toUpperCase())
                .join('');
        }

        function escapeHtml(value) {
            const div =
                document.createElement('div');

            div.textContent = String(value || '');

            return div.innerHTML;
        }

        async function loadStudents() {
            if (
                !subject.value
                || !level.value
                || !classroom.value
                || !slot.value
            ) {
                clearStudents();
                return;
            }

            list.innerHTML = `
                <div class="pps-empty">
                    Chargement des étudiants...
                </div>
            `;

            const params =
                new URLSearchParams({
                    subject_id:
                        subject.value,
                    level_id:
                        level.value,
                    class_slot_id:
                        slot.value,
                });

            try {
                const response = await fetch(
                    `${baseUrl}/${encodeURIComponent(classroom.value)}?${params.toString()}`,
                    {
                        headers: {
                            'Accept':
                                'application/json',
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error();
                }

                const students =
                    await response.json();

                if (!students.length) {
                    list.innerHTML = `
                        <div class="pps-empty">
                            Aucun étudiant assigné à ce créneau.
                        </div>
                    `;
                    return;
                }

                list.innerHTML =
                    students
                        .map(student => `
                            <div class="pp-attendance-row">
                                <div class="pp-attendance-student">
                                    <span class="pp-attendance-avatar">
                                        ${escapeHtml(initials(student.name))}
                                    </span>

                                    <strong class="pp-attendance-name">
                                        ${escapeHtml(student.name)}
                                    </strong>
                                </div>

                                <div class="pp-attendance-options">
                                    <label class="pp-attendance-option">
                                        <input
                                            type="radio"
                                            name="students[${Number(student.id)}]"
                                            value="1"
                                            checked
                                            required
                                        >
                                        <span>Présent</span>
                                    </label>

                                    <label class="pp-attendance-option">
                                        <input
                                            type="radio"
                                            name="students[${Number(student.id)}]"
                                            value="0"
                                            required
                                        >
                                        <span>Absent</span>
                                    </label>
                                </div>
                            </div>
                        `)
                        .join('');

                submit.disabled = false;
            } catch (error) {
                list.innerHTML = `
                    <div class="pps-empty">
                        Impossible de charger les étudiants.
                    </div>
                `;
            }
        }

        hierarchy.forEach(item => {
            subject.appendChild(
                option(
                    item.id,
                    item.name
                )
            );
        });

        subject.addEventListener(
            'change',
            fillLevels
        );

        level.addEventListener(
            'change',
            fillClasses
        );

        classroom.addEventListener(
            'change',
            fillSlots
        );

        slot.addEventListener(
            'change',
            loadStudents
        );
    }
);
</script>
@endpush
