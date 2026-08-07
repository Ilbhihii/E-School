@extends('layouts.prof')

@section('title', 'Créer un devoir')
@section('page_title', 'Nouveau devoir')
@section('breadcrumb', 'Création d’un devoir')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-file-earmark-plus-fill"></i> Nouvelle activité</span>
        <h1 class="pp-page-title">Créer un devoir</h1>
        <p class="pp-page-description">
            Suivez le parcours <strong>Matière → Niveau → Classe</strong>, puis renseignez les consignes et la date limite.
        </p>
    </div>

    <div class="pp-page-actions">
        <a href="{{ route('prof.devoir.index', ['course_id' => $course_id ?? null]) }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left"></i>
            Retour aux devoirs
        </a>
    </div>
</section>

@if($errors->any())
    <div class="adm-alert adm-alert-danger">
        <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
        <div>
            <strong>Le formulaire contient des erreurs.</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if($course)
    <div class="pp-course-context">
        <span class="pp-course-context-icon"><i class="bi bi-book-fill"></i></span>
        <span>
            <strong>{{ $course->title }}</strong>
            <span>
                {{ $course->subject?->name ?? 'Matière' }}
                → {{ $course->classRoom?->level?->name ?? 'Niveau' }}
                → {{ $course->classRoom?->name ?? 'Classe' }}
            </span>
        </span>
    </div>
@endif

<form method="POST" action="{{ route('prof.devoir.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="pp-form-grid">
        <section class="pp-panel">
            <header class="pp-panel-head">
                <div class="pp-panel-title-wrap">
                    <h2 class="pp-panel-title"><i class="bi bi-card-text"></i> Contenu du devoir</h2>
                    <p class="pp-panel-subtitle">Donnez un titre clair et des consignes compréhensibles.</p>
                </div>
            </header>

            <div class="pp-form-section">
                <div class="pp-field">
                    <label for="title" class="pp-label">
                        <i class="bi bi-type"></i> Titre du devoir <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        class="adm-form-control @error('title') is-invalid @enderror"
                        placeholder="Ex. Exercices du chapitre 3"
                        maxlength="255"
                        required
                        autofocus
                    >
                    @error('title') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>

                <div class="pp-field">
                    <label for="description" class="pp-label">
                        <i class="bi bi-text-paragraph"></i> Description et consignes
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="9"
                        class="adm-form-control @error('description') is-invalid @enderror"
                        placeholder="Précisez les exercices à réaliser, les objectifs et les consignes de remise..."
                    >{{ old('description') }}</textarea>
                    @error('description') <span class="adm-form-error">{{ $message }}</span> @enderror
                    <p class="pp-help">Une consigne précise aide les étudiants à rendre un travail conforme.</p>
                </div>
            </div>
        </section>

        <aside class="pp-panel">
            <header class="pp-panel-head">
                <div class="pp-panel-title-wrap">
                    <h2 class="pp-panel-title"><i class="bi bi-diagram-3-fill"></i> Affectation</h2>
                    <p class="pp-panel-subtitle">Matière → Niveau → Classe → Cours.</p>
                </div>
            </header>

            <div class="pp-form-section">
                <div class="pp-field">
                    <label for="subject_id" class="pp-label">
                        <i class="bi bi-journal-bookmark-fill"></i> Matière <span class="required">*</span>
                    </label>
                    <select id="subject_id" name="subject_id" class="adm-form-select @error('subject_id') is-invalid @enderror" required>
                        <option value="">Sélectionner une matière</option>
                    </select>
                    @error('subject_id') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>

                <div class="pp-field">
                    <label for="level_id" class="pp-label">
                        <i class="bi bi-layers-fill"></i> Niveau <span class="required">*</span>
                    </label>
                    <select id="level_id" name="level_id" class="adm-form-select @error('level_id') is-invalid @enderror" required disabled>
                        <option value="">Sélectionner d’abord une matière</option>
                    </select>
                    @error('level_id') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>

                <div class="pp-field">
                    <label for="class_room_id" class="pp-label">
                        <i class="bi bi-people-fill"></i> Classe <span class="required">*</span>
                    </label>
                    <select id="class_room_id" name="class_room_id" class="adm-form-select @error('class_room_id') is-invalid @enderror" required disabled>
                        <option value="">Sélectionner d’abord un niveau</option>
                    </select>
                    @error('class_room_id') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>

                <div class="pp-field">
                    <label for="course_id" class="pp-label"><i class="bi bi-book"></i> Cours associé</label>
                    <select id="course_id" name="course_id" class="adm-form-select @error('course_id') is-invalid @enderror" disabled>
                        <option value="">Aucun cours spécifique</option>
                    </select>
                    @error('course_id') <span class="adm-form-error">{{ $message }}</span> @enderror
                    <p class="pp-help">Les cours proposés correspondent uniquement à la matière, au niveau et à la classe choisis.</p>
                </div>

                <div class="pp-field">
                    <label for="due_date" class="pp-label">
                        <i class="bi bi-calendar-check"></i> Date limite <span class="required">*</span>
                    </label>
                    <input
                        type="date"
                        id="due_date"
                        name="due_date"
                        value="{{ old('due_date') }}"
                        min="{{ now()->addDay()->toDateString() }}"
                        class="adm-form-control @error('due_date') is-invalid @enderror"
                        required
                    >
                    @error('due_date') <span class="adm-form-error">{{ $message }}</span> @enderror
                    <p class="pp-help">La date doit être postérieure à aujourd’hui.</p>
                </div>

                <div class="pp-field">
                    <label for="file" class="pp-label"><i class="bi bi-paperclip"></i> Document PDF</label>
                    <div class="pp-upload">
                        <span class="pp-upload-icon"><i class="bi bi-cloud-arrow-up-fill"></i></span>
                        <input
                            type="file"
                            id="file"
                            name="file"
                            accept="application/pdf,.pdf"
                            class="adm-form-control @error('file') is-invalid @enderror"
                        >
                        <p class="pp-help">PDF uniquement · taille maximale 5 Mo.</p>
                    </div>
                    @error('file') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </aside>
    </div>

    <div class="pp-panel pp-section-gap">
        <div class="pp-form-actions">
            <a href="{{ route('prof.devoir.index', ['course_id' => $course_id ?? null]) }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-x-lg"></i> Annuler
            </a>
            <button type="submit" class="adm-btn adm-btn-success">
                <i class="bi bi-check-circle-fill"></i> Publier le devoir
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paths = @json($teachingPaths ?? []);
    const courses = @json($courseOptions ?? []);

    const subjectSelect = document.getElementById('subject_id');
    const levelSelect = document.getElementById('level_id');
    const classSelect = document.getElementById('class_room_id');
    const courseSelect = document.getElementById('course_id');

    const initial = {
        subject: String(@json($selectedSubjectId ?? '')),
        level: String(@json($selectedLevelId ?? '')),
        classRoom: String(@json($selectedClassId ?? '')),
        course: String(@json($selectedCourseId ?? ''))
    };

    function unique(items, idKey, nameKey) {
        const seen = new Map();
        items.forEach(function (item) {
            const id = String(item[idKey]);
            if (!seen.has(id)) seen.set(id, item[nameKey]);
        });
        return Array.from(seen, function ([id, name]) { return { id, name }; });
    }

    function setOptions(select, placeholder, items, selectedValue) {
        select.innerHTML = '';
        select.add(new Option(placeholder, ''));
        items.forEach(function (item) {
            select.add(new Option(item.name, item.id));
        });
        select.disabled = items.length === 0;
        if (selectedValue && items.some(function (item) { return item.id === String(selectedValue); })) {
            select.value = String(selectedValue);
        }
    }

    function fillSubjects(selectedValue = '') {
        const subjects = unique(paths, 'subject_id', 'subject_name');
        setOptions(subjectSelect, 'Sélectionner une matière', subjects, selectedValue);
    }

    function fillLevels(selectedValue = '') {
        const subjectId = subjectSelect.value;
        const levels = unique(
            paths.filter(function (path) { return String(path.subject_id) === subjectId; }),
            'level_id',
            'level_name'
        );
        setOptions(levelSelect, subjectId ? 'Sélectionner un niveau' : 'Sélectionner d’abord une matière', levels, selectedValue);
    }

    function fillClasses(selectedValue = '') {
        const subjectId = subjectSelect.value;
        const levelId = levelSelect.value;
        const classes = unique(
            paths.filter(function (path) {
                return String(path.subject_id) === subjectId && String(path.level_id) === levelId;
            }),
            'class_id',
            'class_name'
        );
        setOptions(classSelect, levelId ? 'Sélectionner une classe' : 'Sélectionner d’abord un niveau', classes, selectedValue);
    }

    function fillCourses(selectedValue = '') {
        const subjectId = subjectSelect.value;
        const levelId = levelSelect.value;
        const classId = classSelect.value;
        const matchingCourses = courses
            .filter(function (course) {
                return String(course.subject_id) === subjectId
                    && String(course.level_id) === levelId
                    && String(course.class_id) === classId;
            })
            .map(function (course) { return { id: String(course.id), name: course.title }; });

        courseSelect.innerHTML = '';
        courseSelect.add(new Option('Aucun cours spécifique', ''));
        matchingCourses.forEach(function (course) {
            courseSelect.add(new Option(course.name, course.id));
        });
        courseSelect.disabled = !classId;

        if (selectedValue && matchingCourses.some(function (course) { return course.id === String(selectedValue); })) {
            courseSelect.value = String(selectedValue);
        }
    }

    subjectSelect.addEventListener('change', function () {
        fillLevels();
        fillClasses();
        fillCourses();
    });

    levelSelect.addEventListener('change', function () {
        fillClasses();
        fillCourses();
    });

    classSelect.addEventListener('change', function () {
        fillCourses();
    });

    fillSubjects(initial.subject);
    fillLevels(initial.level);
    fillClasses(initial.classRoom);
    fillCourses(initial.course);
});
</script>
@endpush
@endsection
