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
.pp-week-auto-card {
    display: flex;
    align-items: center;
    gap: .85rem;
    min-height: 68px;
    padding: .85rem .95rem;
    border: 1px solid rgba(139, 92, 246, .22);
    border-radius: 12px;
    background: linear-gradient(
        135deg,
        rgba(124, 58, 237, .11),
        rgba(59, 130, 246, .045)
    );
}

.pp-week-auto-icon {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    flex: 0 0 42px;
    border-radius: 11px;
    background: rgba(124, 58, 237, .14);
    color: #c4b5fd;
    font-size: 1rem;
}

.pp-week-auto-copy {
    flex: 1;
    min-width: 0;
}

.pp-week-auto-copy strong {
    display: block;
    color: #f5f3ff;
    font-size: .92rem;
    font-weight: 900;
    letter-spacing: .025em;
}

.pp-week-auto-copy span {
    display: block;
    margin-top: .2rem;
    color: rgba(255, 255, 255, .45);
    font-size: .66rem;
    line-height: 1.45;
}

.pp-week-auto-badge {
    display: inline-flex;
    align-items: center;
    padding: .35rem .55rem;
    border-radius: 999px;
    color: #ddd6fe;
    background: rgba(124, 58, 237, .12);
    border: 1px solid rgba(139, 92, 246, .18);
    font-size: .58rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .045em;
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
                        <label class="pp-label">
                            Semaine pédagogique
                        </label>

                        <div class="pp-week-auto-card">
                            <div class="pp-week-auto-icon">
                                <i class="bi bi-calendar-week-fill"></i>
                            </div>

                            <div class="pp-week-auto-copy">
                                <strong id="assignmentWeekPreview">
                                    SEMAINE —
                                </strong>

                                <span id="assignmentWeekHint">
                                    Choisissez la matière, le niveau
                                    et la classe. Le numéro sera attribué
                                    automatiquement à la publication.
                                </span>
                            </div>

                            <span class="pp-week-auto-badge">
                                Automatique
                            </span>
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

    const weekSuggestions =
        @json($weekSuggestions ?? []);

    const assignmentWeekPreview =
        document.getElementById('assignmentWeekPreview');

    const assignmentWeekHint =
        document.getElementById('assignmentWeekHint');

    function refreshWeekPreview() {
        if (
            !assignmentWeekPreview
            || !assignmentWeekHint
        ) {
            return;
        }

        if (!subject.value || !classroom.value) {
            assignmentWeekPreview.textContent =
                'SEMAINE —';

            assignmentWeekHint.textContent =
                'Choisissez la matière, le niveau et la classe. '
                + 'Le numéro sera attribué automatiquement '
                + 'à la publication.';

            return;
        }

        const key =
            String(subject.value)
            + ':'
            + String(classroom.value);

        const weekNumber =
            parseInt(
                weekSuggestions[key] || 1,
                10
            );

        assignmentWeekPreview.textContent =
            'SEMAINE ' + weekNumber;

        assignmentWeekHint.textContent =
            'Titre généré automatiquement pour cette classe. '
            + 'Un devoir créé dans une nouvelle semaine '
            + 'passera au numéro suivant.';
    }

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
        refreshWeekPreview();
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
    refreshWeekPreview();
});
</script>
@endpush
