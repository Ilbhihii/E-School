@extends('layouts.prof')

@section('title', 'Créer un devoir')
@section('page_title', 'Nouveau devoir')
@section('breadcrumb', 'Matière → Niveau → Classe')

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
            Matière → Niveau → Classe.
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


<style>
.pp-assignment-number-wrap {
    display: grid;
    grid-template-columns: auto minmax(100px, 1fr);
    align-items: stretch;
    gap: 0;
}

.pp-assignment-prefix {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 1rem;
    border: 1px solid rgba(139, 92, 246, .32);
    border-right: 0;
    border-radius: 10px 0 0 10px;
    background: linear-gradient(
        135deg,
        rgba(124, 77, 255, .18),
        rgba(59, 130, 246, .10)
    );
    color: #c4b5fd;
    font-size: .78rem;
    font-weight: 900;
    letter-spacing: .08em;
}

.pp-assignment-number-input {
    border-radius: 0 10px 10px 0 !important;
    font-weight: 800;
}

.pp-assignment-preview {
    margin-top: .55rem;
    color: rgba(255, 255, 255, .47);
    font-size: .7rem;
}

.pp-assignment-preview strong {
    color: #a7f3d0;
    font-weight: 900;
}

.pp-auto-date-card {
    display: flex;
    align-items: center;
    gap: .8rem;
    min-height: 58px;
    padding: .75rem .9rem;
    border: 1px solid rgba(34, 197, 94, .20);
    border-radius: 11px;
    background: linear-gradient(
        135deg,
        rgba(34, 197, 94, .08),
        rgba(16, 185, 129, .035)
    );
}

.pp-auto-date-icon {
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    flex: 0 0 38px;
    border-radius: 10px;
    background: rgba(34, 197, 94, .12);
    color: #4ade80;
    font-size: 1rem;
}

.pp-auto-date-card strong {
    display: block;
    color: #f0fdf4;
    font-size: .85rem;
    font-weight: 900;
}

.pp-auto-date-card span {
    display: block;
    margin-top: .15rem;
    color: rgba(255, 255, 255, .45);
    font-size: .66rem;
    line-height: 1.45;
}

@media (max-width: 575px) {
    .pp-assignment-number-wrap {
        grid-template-columns: 90px 1fr;
    }
}
</style>

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
                        Le devoir sera visible par les étudiants
                        de la classe sélectionnée.
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
                            </option>
                        @endforeach
                    </select>

                    <small class="pp-help">
                        Seuls les cours correspondant à la matière,
                        au niveau et à la classe restent disponibles.
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
                        for="assignment_number"
                        class="pp-label"
                    >
                        Numéro du devoir *
                    </label>

                    <div class="pp-assignment-number-wrap">
                        <span class="pp-assignment-prefix">
                            DEVOIR
                        </span>

                        <input
                            type="number"
                            name="assignment_number"
                            id="assignment_number"
                            value="{{ old('assignment_number', 1) }}"
                            class="adm-form-control pp-assignment-number-input"
                            min="1"
                            max="999"
                            step="1"
                            inputmode="numeric"
                            required
                        >
                    </div>

                    <div class="pp-assignment-preview">
                        Titre généré automatiquement :
                        <strong id="assignmentTitlePreview">
                            DEVOIR {{ old('assignment_number', 1) }}
                        </strong>
                    </div>
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
                    <label class="pp-label">
                        Date limite
                    </label>

                    <div class="pp-auto-date-card">
                        <div class="pp-auto-date-icon">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>

                        <div>
                            <strong>
                                {{ now()->addDays(5)->format('d/m/Y') }}
                            </strong>

                            <span>
                                Calculée automatiquement à J+5
                                après la publication.
                            </span>
                        </div>
                    </div>
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
document.addEventListener('DOMContentLoaded', function () {
    const hierarchy = @json($profHierarchy);

    const subject = document.getElementById('devoirSubject');
    const level = document.getElementById('devoirLevel');
    const classroom = document.getElementById('devoirClass');
    const course = document.getElementById('course_id');

    const assignmentNumber =
        document.getElementById('assignment_number');

    const assignmentTitlePreview =
        document.getElementById('assignmentTitlePreview');

    const refreshAssignmentTitle = () => {
        if (!assignmentNumber || !assignmentTitlePreview) {
            return;
        }

        const number = Math.max(
            1,
            parseInt(assignmentNumber.value || '1', 10) || 1
        );

        assignmentTitlePreview.textContent =
            'DEVOIR ' + number;
    };

    assignmentNumber?.addEventListener(
        'input',
        refreshAssignmentTitle
    );

    refreshAssignmentTitle();

    const wantedSubject =
        @json((string) ($selectedSubjectId ?? ''));

    const wantedLevel =
        @json((string) ($selectedLevelId ?? ''));

    const wantedClass =
        @json((string) ($selectedClassId ?? ''));

    const makeOption = (
        value,
        label,
        selected = false
    ) => {
        const item = document.createElement('option');
        item.value = String(value);
        item.textContent = label;
        item.selected = selected;
        return item;
    };

    const subjectData = () =>
        hierarchy.find(
            item => String(item.id) === String(subject.value)
        );

    const levelData = () =>
        subjectData()?.levels?.find(
            item => String(item.id) === String(level.value)
        );

    function refreshCourses() {
        if (!course) return;

        Array.from(course.options).forEach(option => {
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
                    === String(classroom.value);

            option.hidden = !matches;
            option.disabled = !matches;
        });

        const current = course.options[course.selectedIndex];

        if (current && current.value && current.disabled) {
            course.value = '';
        }
    }

    function fillClasses(wanted = '') {
        classroom.innerHTML = '';
        classroom.appendChild(
            makeOption('', 'Choisir une classe')
        );

        (levelData()?.classes || []).forEach(item => {
            classroom.appendChild(
                makeOption(
                    item.id,
                    item.name,
                    String(item.id) === String(wanted)
                )
            );
        });

        classroom.disabled = !levelData();

        if (wanted) {
            classroom.value = String(wanted);
        }

        refreshCourses();
    }

    function fillLevels(
        wanted = '',
        wantedClassId = ''
    ) {
        level.innerHTML = '';
        level.appendChild(
            makeOption('', 'Choisir un niveau')
        );

        (subjectData()?.levels || []).forEach(item => {
            level.appendChild(
                makeOption(
                    item.id,
                    item.name,
                    String(item.id) === String(wanted)
                )
            );
        });

        level.disabled = !subjectData();

        if (wanted) {
            level.value = String(wanted);
        }

        fillClasses(wantedClassId);
    }

    hierarchy.forEach(item => {
        subject.appendChild(
            makeOption(
                item.id,
                item.name,
                String(item.id) === String(wantedSubject)
            )
        );
    });

    if (wantedSubject) {
        subject.value = wantedSubject;
        fillLevels(wantedLevel, wantedClass);
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
        refreshCourses
    );

    refreshCourses();
});
</script>
@endpush
