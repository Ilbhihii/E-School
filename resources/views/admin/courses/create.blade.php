@extends('layouts.admin')

@section('title', 'Créer un cours')
@section('page_title', 'Nouveau cours')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Cours'
)

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="adm-card course-create-card">
            <div class="adm-card-header course-create-header">
                <div>
                    <h4>
                        <i
                            class="bi bi-journal-plus"
                            style="
                                color:var(--adm-accent);
                            "
                        ></i>

                        Créer un nouveau cours
                    </h4>

                    <p>
                        Choisissez d’abord la matière, puis son niveau,
                        puis la classe appartenant à ce niveau.
                    </p>
                </div>

                <div class="course-path-preview">
                    <span id="pathSubject">Matière</span>
                    <i class="bi bi-chevron-right"></i>
                    <span id="pathLevel">Niveau</span>
                    <i class="bi bi-chevron-right"></i>
                    <span id="pathClass">Classe</span>
                </div>
            </div>

            <div class="adm-card-body">
                @if($errors->any())
                    <div class="course-form-alert">
                        <i class="bi bi-exclamation-circle-fill"></i>

                        <div>
                            <strong>
                                Certaines informations sont incorrectes.
                            </strong>

                            <span>
                                Vérifiez les champs indiqués en rouge.
                            </span>
                        </div>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('admin.courses.store') }}"
                    enctype="multipart/form-data"
                    id="courseCreateForm"
                >
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="title">
                            Titre
                            <span class="course-required">*</span>
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            class="adm-form-control
                                @error('title') error @enderror"
                            placeholder="Ex : Les équations du 2ème degré"
                            required
                        >

                        @error('title')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="description"
                        >
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="adm-form-control adm-form-textarea
                                @error('description') error @enderror"
                            placeholder="Décrivez le contenu du cours..."
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- =========================================
                         MATIÈRE → NIVEAU → CLASSE
                         ========================================= -->
                    <div class="course-hierarchy-box">
                        <div class="course-hierarchy-title">
                            <span>
                                <i class="bi bi-diagram-3-fill"></i>
                            </span>

                            <div>
                                <strong>
                                    Structure pédagogique
                                </strong>

                                <small>
                                    Les choix suivants se filtrent
                                    automatiquement.
                                </small>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- 1. MATIÈRE -->
                            <div class="col-lg-4">
                                <div class="course-step">
                                    <span class="course-step-number">
                                        1
                                    </span>

                                    <div class="adm-form-group mb-0">
                                        <label
                                            class="adm-form-label"
                                            for="subject_id"
                                        >
                                            Matière
                                            <span
                                                class="course-required"
                                            >
                                                *
                                            </span>
                                        </label>

                                        <select
                                            name="subject_id"
                                            id="subject_id"
                                            class="adm-form-select
                                                @error('subject_id')
                                                    error
                                                @enderror"
                                            required
                                        >
                                            <option value="">
                                                Sélectionner une matière
                                            </option>

                                            @foreach($subjects as $subject)
                                                <option
                                                    value="{{ $subject->id }}"
                                                >
                                                    {{ $subject->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <small class="course-field-help">
                                            Première étape : choisissez
                                            la matière.
                                        </small>

                                        @error('subject_id')
                                            <div class="adm-form-error">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- 2. NIVEAU -->
                            <div class="col-lg-4">
                                <div class="course-step">
                                    <span class="course-step-number">
                                        2
                                    </span>

                                    <div class="adm-form-group mb-0">
                                        <label
                                            class="adm-form-label"
                                            for="level_id"
                                        >
                                            Niveau
                                            <span
                                                class="course-required"
                                            >
                                                *
                                            </span>
                                        </label>

                                        <select
                                            name="level_id"
                                            id="level_id"
                                            class="adm-form-select
                                                @error('level_id')
                                                    error
                                                @enderror"
                                            disabled
                                            required
                                        >
                                            <option value="">
                                                Choisissez d’abord
                                                une matière
                                            </option>
                                        </select>

                                        <small class="course-field-help">
                                            Seuls les niveaux de la
                                            matière seront affichés.
                                        </small>

                                        @error('level_id')
                                            <div class="adm-form-error">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- 3. CLASSE -->
                            <div class="col-lg-4">
                                <div class="course-step">
                                    <span class="course-step-number">
                                        3
                                    </span>

                                    <div class="adm-form-group mb-0">
                                        <label
                                            class="adm-form-label"
                                            for="class_id"
                                        >
                                            Classe
                                            <span
                                                class="course-required"
                                            >
                                                *
                                            </span>
                                        </label>

                                        <select
                                            name="class_id"
                                            id="class_id"
                                            class="adm-form-select
                                                @error('class_id')
                                                    error
                                                @enderror"
                                            disabled
                                            required
                                        >
                                            <option value="">
                                                Choisissez d’abord
                                                un niveau
                                            </option>
                                        </select>

                                        <small class="course-field-help">
                                            Seules les classes du
                                            niveau seront affichées.
                                        </small>

                                        @error('class_id')
                                            <div class="adm-form-error">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="course_link"
                        >
                            Lien du cours
                            <span class="course-optional">
                                Optionnel
                            </span>
                        </label>

                        <div class="course-input-icon">
                            <i class="bi bi-link-45deg"></i>

                            <input
                                id="course_link"
                                type="url"
                                name="course_link"
                                value="{{ old('course_link') }}"
                                class="adm-form-control
                                    @error('course_link') error @enderror"
                                placeholder="https://..."
                            >
                        </div>

                        @error('course_link')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="course-upload-box">
                                <div class="course-upload-icon video">
                                    <i class="bi bi-camera-video-fill"></i>
                                </div>

                                <div class="course-upload-content">
                                    <label
                                        class="adm-form-label"
                                        for="video"
                                    >
                                        Vidéo
                                    </label>

                                    <span>
                                        MP4, MOV ou AVI — 200 Mo maximum
                                    </span>

                                    <input
                                        id="video"
                                        type="file"
                                        name="video"
                                        accept="video/mp4,video/quicktime,video/x-msvideo"
                                        class="adm-form-control
                                            @error('video') error @enderror"
                                    >

                                    @error('video')
                                        <div class="adm-form-error">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="course-upload-box">
                                <div class="course-upload-icon pdf">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>

                                <div class="course-upload-content">
                                    <label
                                        class="adm-form-label"
                                        for="pdf"
                                    >
                                        Document PDF
                                    </label>

                                    <span>
                                        PDF — 20 Mo maximum
                                    </span>

                                    <input
                                        id="pdf"
                                        type="file"
                                        name="pdf"
                                        accept="application/pdf"
                                        class="adm-form-control
                                            @error('pdf') error @enderror"
                                    >

                                    @error('pdf')
                                        <div class="adm-form-error">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="course-form-actions">
                        <a
                            href="{{ route('admin.courses.index') }}"
                            class="adm-btn adm-btn-ghost"
                        >
                            <i class="bi bi-x-lg"></i>
                            Annuler
                        </a>

                        <button
                            type="submit"
                            class="adm-btn adm-btn-primary"
                        >
                            <i class="bi bi-plus-lg"></i>
                            Créer le cours
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.course-create-card {
    overflow: hidden;
}

.course-create-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.course-create-header h4 {
    margin-bottom: 4px;
}

.course-create-header p {
    margin: 0;
    color: var(--adm-text-muted);
    font-size: 0.76rem;
}

.course-path-preview {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
    padding: 8px 11px;
    border: 1px solid rgba(96,165,250,0.13);
    border-radius: 12px;
    color: rgba(255,255,255,0.55);
    background: rgba(37,99,235,0.06);
    font-size: 0.69rem;
}

.course-path-preview span.is-selected {
    color: #93C5FD;
    font-weight: 700;
}

.course-path-preview i {
    color: rgba(255,255,255,0.22);
    font-size: 0.58rem;
}

.course-required {
    color: var(--adm-danger);
}

.course-optional {
    margin-left: 5px;
    color: var(--adm-text-muted);
    font-size: 0.64rem;
    font-weight: 500;
}

.course-form-alert {
    display: flex;
    align-items: center;
    gap: 11px;
    margin-bottom: 1.15rem;
    padding: 12px 14px;
    border: 1px solid rgba(248,113,113,0.18);
    border-radius: 13px;
    color: #FCA5A5;
    background: rgba(127,29,29,0.14);
}

.course-form-alert > i {
    font-size: 1rem;
}

.course-form-alert > div {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.course-form-alert strong {
    font-size: 0.78rem;
}

.course-form-alert span {
    color: rgba(252,165,165,0.72);
    font-size: 0.69rem;
}

.course-hierarchy-box {
    margin: 0 0 1.25rem;
    padding: 1rem;
    border: 1px solid rgba(96,165,250,0.12);
    border-radius: 16px;
    background:
        linear-gradient(
            145deg,
            rgba(37,99,235,0.055),
            rgba(15,23,42,0.15)
        );
}

.course-hierarchy-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 0.9rem;
}

.course-hierarchy-title > span {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    color: #60A5FA;
    background: rgba(37,99,235,0.12);
}

.course-hierarchy-title > div {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.course-hierarchy-title strong {
    color: rgba(255,255,255,0.9);
    font-size: 0.82rem;
}

.course-hierarchy-title small {
    color: var(--adm-text-muted);
    font-size: 0.68rem;
}

.course-step {
    position: relative;
    height: 100%;
    padding: 0.85rem;
    border: 1px solid rgba(255,255,255,0.045);
    border-radius: 13px;
    background: rgba(7,15,30,0.28);
}

.course-step-number {
    position: absolute;
    top: -8px;
    right: 10px;
    width: 23px;
    height: 23px;
    display: grid;
    place-items: center;
    border: 2px solid #111C30;
    border-radius: 50%;
    color: #ffffff;
    background:
        linear-gradient(
            135deg,
            #2563EB,
            #4F46E5
        );
    font-size: 0.64rem;
    font-weight: 800;
}

.course-field-help {
    display: block;
    margin-top: 6px;
    color: var(--adm-text-muted);
    font-size: 0.64rem;
    line-height: 1.4;
}

.course-input-icon {
    position: relative;
}

.course-input-icon > i {
    position: absolute;
    z-index: 2;
    top: 50%;
    left: 13px;
    color: var(--adm-text-muted);
    transform: translateY(-50%);
}

.course-input-icon .adm-form-control {
    padding-left: 38px;
}

.course-upload-box {
    height: 100%;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 0.95rem;
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 14px;
    background: rgba(255,255,255,0.02);
}

.course-upload-icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    font-size: 1rem;
}

.course-upload-icon.video {
    color: #60A5FA;
    background: rgba(37,99,235,0.12);
}

.course-upload-icon.pdf {
    color: #F87171;
    background: rgba(220,38,38,0.12);
}

.course-upload-content {
    min-width: 0;
    flex: 1;
}

.course-upload-content > span {
    display: block;
    margin: -2px 0 8px;
    color: var(--adm-text-muted);
    font-size: 0.64rem;
}

.course-upload-content .adm-form-control {
    padding: 7px;
}

.course-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 1.4rem;
    padding-top: 1.15rem;
    border-top: 1px solid rgba(255,255,255,0.055);
}

@media (max-width: 767.98px) {
    .course-create-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .course-path-preview {
        width: 100%;
    }

    .course-form-actions {
        flex-direction: column-reverse;
    }

    .course-form-actions .adm-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const hierarchy = @json($courseHierarchy);

    const initialSubjectId = @json(
        (string) old(
            'subject_id',
            $selectedSubjectId ?? ''
        )
    );

    const initialLevelId = @json(
        (string) old(
            'level_id',
            $selectedLevelId ?? ''
        )
    );

    const initialClassId = @json(
        (string) old(
            'class_id',
            $selectedClassId ?? ''
        )
    );

    const subjectSelect =
        document.getElementById('subject_id');

    const levelSelect =
        document.getElementById('level_id');

    const classSelect =
        document.getElementById('class_id');

    const pathSubject =
        document.getElementById('pathSubject');

    const pathLevel =
        document.getElementById('pathLevel');

    const pathClass =
        document.getElementById('pathClass');

    const findSubject = subjectId =>
        hierarchy.find(
            subject =>
                String(subject.id)
                === String(subjectId)
        );

    const findLevel = (
        subject,
        levelId
    ) => {
        if (!subject) {
            return null;
        }

        return subject.levels.find(
            level =>
                String(level.id)
                === String(levelId)
        );
    };

    const createOption = (
        value,
        label,
        selectedValue
    ) => {
        const option =
            document.createElement('option');

        option.value = String(value);
        option.textContent = label;
        option.selected =
            String(value)
            === String(selectedValue);

        return option;
    };

    const resetSelect = (
        select,
        placeholder,
        disabled = true
    ) => {
        select.innerHTML = '';

        select.appendChild(
            createOption(
                '',
                placeholder,
                ''
            )
        );

        select.disabled = disabled;
        select.value = '';
    };

    const updatePath = () => {
        const subjectOption =
            subjectSelect.options[
                subjectSelect.selectedIndex
            ];

        const levelOption =
            levelSelect.options[
                levelSelect.selectedIndex
            ];

        const classOption =
            classSelect.options[
                classSelect.selectedIndex
            ];

        pathSubject.textContent =
            subjectSelect.value
                ? subjectOption.textContent
                : 'Matière';

        pathLevel.textContent =
            levelSelect.value
                ? levelOption.textContent
                : 'Niveau';

        pathClass.textContent =
            classSelect.value
                ? classOption.textContent
                : 'Classe';

        pathSubject.classList.toggle(
            'is-selected',
            Boolean(subjectSelect.value)
        );

        pathLevel.classList.toggle(
            'is-selected',
            Boolean(levelSelect.value)
        );

        pathClass.classList.toggle(
            'is-selected',
            Boolean(classSelect.value)
        );
    };

    const populateClasses = (
        subject,
        levelId,
        selectedClassId = ''
    ) => {
        const level = findLevel(
            subject,
            levelId
        );

        resetSelect(
            classSelect,
            level
                ? 'Sélectionner une classe'
                : 'Choisissez d’abord un niveau',
            !level
        );

        if (!level) {
            updatePath();
            return;
        }

        level.classes.forEach(classRoom => {
            classSelect.appendChild(
                createOption(
                    classRoom.id,
                    classRoom.name,
                    selectedClassId
                )
            );
        });

        classSelect.disabled = false;

        if (selectedClassId) {
            classSelect.value =
                String(selectedClassId);
        }

        updatePath();
    };

    const populateLevels = (
        subjectId,
        selectedLevelId = '',
        selectedClassId = ''
    ) => {
        const subject = findSubject(
            subjectId
        );

        resetSelect(
            levelSelect,
            subject
                ? 'Sélectionner un niveau'
                : 'Choisissez d’abord une matière',
            !subject
        );

        resetSelect(
            classSelect,
            'Choisissez d’abord un niveau',
            true
        );

        if (!subject) {
            updatePath();
            return;
        }

        subject.levels.forEach(level => {
            levelSelect.appendChild(
                createOption(
                    level.id,
                    level.name,
                    selectedLevelId
                )
            );
        });

        levelSelect.disabled = false;

        if (selectedLevelId) {
            levelSelect.value =
                String(selectedLevelId);

            populateClasses(
                subject,
                selectedLevelId,
                selectedClassId
            );
        }

        updatePath();
    };

    subjectSelect.addEventListener(
        'change',
        () => {
            populateLevels(
                subjectSelect.value
            );
        }
    );

    levelSelect.addEventListener(
        'change',
        () => {
            populateClasses(
                findSubject(
                    subjectSelect.value
                ),
                levelSelect.value
            );
        }
    );

    classSelect.addEventListener(
        'change',
        updatePath
    );

    /*
     * Restaurer les choix après validation ou préremplissage.
     */
    if (initialSubjectId) {
        subjectSelect.value =
            String(initialSubjectId);

        populateLevels(
            initialSubjectId,
            initialLevelId,
            initialClassId
        );
    } else {
        resetSelect(
            levelSelect,
            'Choisissez d’abord une matière',
            true
        );

        resetSelect(
            classSelect,
            'Choisissez d’abord un niveau',
            true
        );

        updatePath();
    }
});
</script>

@endsection
