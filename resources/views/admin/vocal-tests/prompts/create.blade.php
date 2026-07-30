@extends('layouts.admin')

@section('title', 'Créer un test vocal')
@section('page_title', 'Nouveau test vocal')
@section(
    'breadcrumb',
    'Tests vocaux → Matière → Niveau → Classe'
)

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="adm-card vocal-prompt-card">
            <div class="adm-card-header vocal-prompt-header">
                <div>
                    <h4>
                        <i
                            class="bi bi-mic-fill"
                            style="color:#A78BFA;"
                        ></i>

                        Créer un test vocal
                    </h4>

                    <p>
                        Choisissez la matière, puis son niveau,
                        puis la classe appartenant à ce niveau.
                    </p>
                </div>

                <div class="prompt-path-preview">
                    <span id="pathSubject">Matière</span>
                    <i class="bi bi-chevron-right"></i>
                    <span id="pathLevel">Niveau</span>
                    <i class="bi bi-chevron-right"></i>
                    <span id="pathClass">Classe</span>
                </div>
            </div>

            <div class="adm-card-body">
                @if($errors->any())
                    <div class="prompt-error-summary">
                        <i class="bi bi-exclamation-circle-fill"></i>

                        <div>
                            <strong>
                                Le test vocal n’a pas été enregistré.
                            </strong>

                            <span>
                                Vérifiez les champs indiqués en rouge.
                            </span>
                        </div>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{
                        route(
                            'admin.vocal-tests.prompts.store'
                        )
                    }}"
                    id="vocalPromptForm"
                >
                    @csrf

                    <!-- =====================================
                         MATIÈRE → NIVEAU → CLASSE
                         ===================================== -->
                    <section class="prompt-section">
                        <div class="prompt-section-heading">
                            <span>
                                <i class="bi bi-diagram-3-fill"></i>
                            </span>

                            <div>
                                <h5>Structure pédagogique</h5>

                                <p>
                                    Les niveaux et les classes sont
                                    filtrés automatiquement.
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- MATIÈRE -->
                            <div class="col-lg-4">
                                <div class="prompt-step">
                                    <span class="prompt-step-number">
                                        1
                                    </span>

                                    <div class="adm-form-group mb-0">
                                        <label
                                            class="adm-form-label"
                                            for="subject_id"
                                        >
                                            Matière
                                            <span
                                                class="prompt-required"
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

                                        <small class="prompt-help">
                                            Arabe ou Coran.
                                        </small>

                                        @error('subject_id')
                                            <div class="adm-form-error">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- NIVEAU -->
                            <div class="col-lg-4">
                                <div class="prompt-step">
                                    <span class="prompt-step-number">
                                        2
                                    </span>

                                    <div class="adm-form-group mb-0">
                                        <label
                                            class="adm-form-label"
                                            for="level_id"
                                        >
                                            Niveau
                                            <span
                                                class="prompt-required"
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

                                        <small class="prompt-help">
                                            Seuls les niveaux de la
                                            matière choisie apparaissent.
                                        </small>

                                        @error('level_id')
                                            <div class="adm-form-error">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- CLASSE -->
                            <div class="col-lg-4">
                                <div class="prompt-step">
                                    <span class="prompt-step-number">
                                        3
                                    </span>

                                    <div class="adm-form-group mb-0">
                                        <label
                                            class="adm-form-label"
                                            for="class_id"
                                        >
                                            Classe
                                            <span
                                                class="prompt-required"
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

                                        <small class="prompt-help">
                                            Les parcours exclus sans
                                            test vocal sont masqués.
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
                    </section>

                    <!-- =====================================
                         CONTENU DU TEST
                         ===================================== -->
                    <section class="prompt-section">
                        <div class="prompt-section-heading">
                            <span>
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </span>

                            <div>
                                <h5>Contenu du test</h5>

                                <p>
                                    Définissez le texte, les consignes
                                    et le mode d’évaluation.
                                </p>
                            </div>
                        </div>

                        <div class="adm-form-group">
                            <label
                                class="adm-form-label"
                                for="title"
                            >
                                Titre
                                <span class="prompt-required">*</span>
                            </label>

                            <input
                                id="title"
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                class="adm-form-control
                                    @error('title') error @enderror"
                                placeholder="Ex : Lecture intermédiaire — Arabe"
                                required
                            >

                            @error('title')
                                <div class="adm-form-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-lg-8">
                                <div class="adm-form-group">
                                    <label
                                        class="adm-form-label"
                                        for="test_mode"
                                    >
                                        Mode du test
                                        <span class="prompt-required">
                                            *
                                        </span>
                                    </label>

                                    <select
                                        name="test_mode"
                                        id="test_mode"
                                        class="adm-form-select
                                            @error('test_mode')
                                                error
                                            @enderror"
                                        required
                                    >
                                        <option value="">
                                            Sélectionner un mode
                                        </option>

                                        @foreach($modes as $mode => $label)
                                            <option
                                                value="{{ $mode }}"
                                                @selected(
                                                    old('test_mode')
                                                    === $mode
                                                )
                                            >
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('test_mode')
                                        <div class="adm-form-error">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="adm-form-group">
                                    <label
                                        class="adm-form-label"
                                        for="preparation_seconds"
                                    >
                                        Préparation
                                    </label>

                                    <div class="prompt-input-unit">
                                        <input
                                            id="preparation_seconds"
                                            type="number"
                                            name="preparation_seconds"
                                            value="{{
                                                old(
                                                    'preparation_seconds',
                                                    0
                                                )
                                            }}"
                                            min="0"
                                            max="300"
                                            class="adm-form-control
                                                @error(
                                                    'preparation_seconds'
                                                )
                                                    error
                                                @enderror"
                                        >

                                        <span>secondes</span>
                                    </div>

                                    @error('preparation_seconds')
                                        <div class="adm-form-error">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="adm-form-group">
                            <label
                                class="adm-form-label"
                                for="instructions"
                            >
                                Consignes
                            </label>

                            <textarea
                                id="instructions"
                                name="instructions"
                                rows="3"
                                class="adm-form-control adm-form-textarea
                                    @error('instructions')
                                        error
                                    @enderror"
                                placeholder="Ex : Lisez clairement, respectez les pauses..."
                            >{{ old('instructions') }}</textarea>

                            @error('instructions')
                                <div class="adm-form-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="adm-form-group">
                            <label
                                class="adm-form-label"
                                for="reading_text"
                            >
                                Texte à lire
                                <span class="prompt-required">*</span>
                            </label>

                            <textarea
                                id="reading_text"
                                name="reading_text"
                                rows="8"
                                dir="auto"
                                class="adm-form-control adm-form-textarea
                                    prompt-reading-text
                                    @error('reading_text')
                                        error
                                    @enderror"
                                placeholder="Saisissez ici le texte arabe ou les versets..."
                                required
                            >{{ old('reading_text') }}</textarea>

                            <div class="prompt-character-counter">
                                <span id="readingCharacterCount">
                                    0
                                </span>
                                caractère(s)
                            </div>

                            @error('reading_text')
                                <div class="adm-form-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </section>

                    <!-- =====================================
                         PARAMÈTRES
                         ===================================== -->
                    <section class="prompt-section">
                        <div class="prompt-section-heading">
                            <span>
                                <i class="bi bi-sliders"></i>
                            </span>

                            <div>
                                <h5>Paramètres</h5>

                                <p>
                                    Configurez la durée et la visibilité
                                    du texte.
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-lg-5">
                                <div class="adm-form-group mb-0">
                                    <label
                                        class="adm-form-label"
                                        for="maximum_duration"
                                    >
                                        Durée maximale
                                    </label>

                                    <div class="prompt-input-unit">
                                        <input
                                            id="maximum_duration"
                                            type="number"
                                            name="maximum_duration"
                                            value="{{
                                                old(
                                                    'maximum_duration',
                                                    120
                                                )
                                            }}"
                                            min="15"
                                            max="600"
                                            class="adm-form-control
                                                @error(
                                                    'maximum_duration'
                                                )
                                                    error
                                                @enderror"
                                        >

                                        <span>secondes</span>
                                    </div>

                                    @error('maximum_duration')
                                        <div class="adm-form-error">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="prompt-options">
                                    <label class="prompt-option">
                                        <input
                                            type="checkbox"
                                            name="hide_text_during_recording"
                                            value="1"
                                            @checked(
                                                old(
                                                    'hide_text_during_recording'
                                                )
                                            )
                                        >

                                        <span class="prompt-option-icon">
                                            <i class="bi bi-eye-slash-fill"></i>
                                        </span>

                                        <span>
                                            <strong>
                                                Masquer le texte
                                            </strong>

                                            <small>
                                                Le texte disparaît
                                                pendant l’enregistrement.
                                            </small>
                                        </span>
                                    </label>

                                    <label class="prompt-option">
                                        <input
                                            type="checkbox"
                                            name="is_active"
                                            value="1"
                                            @checked(
                                                old(
                                                    'is_active',
                                                    '1'
                                                ) === '1'
                                            )
                                        >

                                        <span class="prompt-option-icon active">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </span>

                                        <span>
                                            <strong>
                                                Test actif
                                            </strong>

                                            <small>
                                                Le test est disponible
                                                pour les étudiants.
                                            </small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="prompt-form-actions">
                        <a
                            href="{{
                                route(
                                    'admin.vocal-tests.prompts.index'
                                )
                            }}"
                            class="adm-btn adm-btn-ghost"
                        >
                            <i class="bi bi-x-lg"></i>
                            Annuler
                        </a>

                        <button
                            type="submit"
                            class="adm-btn adm-btn-primary"
                        >
                            <i class="bi bi-mic-fill"></i>
                            Créer le test vocal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.vocal-prompt-card {
    overflow: hidden;
}

.vocal-prompt-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.vocal-prompt-header h4 {
    margin-bottom: 4px;
}

.vocal-prompt-header p {
    margin: 0;
    color: var(--adm-text-muted);
    font-size: 0.72rem;
}

.prompt-path-preview {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
    padding: 8px 11px;
    border: 1px solid rgba(167,139,250,0.15);
    border-radius: 12px;
    color: rgba(255,255,255,0.48);
    background: rgba(124,58,237,0.065);
    font-size: 0.67rem;
}

.prompt-path-preview span.is-selected {
    color: #C4B5FD;
    font-weight: 750;
}

.prompt-path-preview i {
    color: rgba(255,255,255,0.2);
    font-size: 0.56rem;
}

.prompt-error-summary {
    display: flex;
    align-items: center;
    gap: 11px;
    margin-bottom: 1rem;
    padding: 12px 14px;
    border: 1px solid rgba(248,113,113,0.18);
    border-radius: 13px;
    color: #FCA5A5;
    background: rgba(127,29,29,0.13);
}

.prompt-error-summary > div {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.prompt-error-summary strong {
    font-size: 0.76rem;
}

.prompt-error-summary span {
    color: rgba(252,165,165,0.7);
    font-size: 0.66rem;
}

.prompt-section {
    margin-bottom: 1rem;
    padding: 1rem;
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 16px;
    background: rgba(255,255,255,0.018);
}

.prompt-section-heading {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 0.95rem;
}

.prompt-section-heading > span {
    width: 39px;
    height: 39px;
    flex: 0 0 39px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    color: #A78BFA;
    background: rgba(124,58,237,0.12);
}

.prompt-section-heading h5 {
    margin: 0;
    color: rgba(255,255,255,0.9);
    font-size: 0.82rem;
}

.prompt-section-heading p {
    margin: 2px 0 0;
    color: var(--adm-text-muted);
    font-size: 0.65rem;
}

.prompt-step {
    position: relative;
    height: 100%;
    padding: 0.82rem;
    border: 1px solid rgba(255,255,255,0.045);
    border-radius: 13px;
    background: rgba(7,15,30,0.27);
}

.prompt-step-number {
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
            #7C3AED,
            #4F46E5
        );
    font-size: 0.62rem;
    font-weight: 820;
}

.prompt-required {
    color: var(--adm-danger);
}

.prompt-help {
    display: block;
    margin-top: 6px;
    color: var(--adm-text-muted);
    font-size: 0.61rem;
    line-height: 1.4;
}

.prompt-input-unit {
    position: relative;
}

.prompt-input-unit .adm-form-control {
    padding-right: 76px;
}

.prompt-input-unit > span {
    position: absolute;
    top: 50%;
    right: 12px;
    color: rgba(255,255,255,0.32);
    font-size: 0.61rem;
    transform: translateY(-50%);
}

.prompt-reading-text {
    min-height: 170px;
    font-size: 1rem;
    line-height: 1.9;
}

.prompt-character-counter {
    margin-top: 5px;
    color: rgba(255,255,255,0.3);
    font-size: 0.59rem;
    text-align: right;
}

.prompt-options {
    height: 100%;
    display: grid;
    grid-template-columns: repeat(2, minmax(0,1fr));
    gap: 9px;
}

.prompt-option {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 10px;
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 13px;
    background: rgba(255,255,255,0.024);
    cursor: pointer;
}

.prompt-option > input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.prompt-option-icon {
    width: 35px;
    height: 35px;
    flex: 0 0 35px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    color: #FBBF24;
    background: rgba(245,158,11,0.11);
}

.prompt-option-icon.active {
    color: #4ADE80;
    background: rgba(34,197,94,0.11);
}

.prompt-option > span:last-child {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.prompt-option strong {
    color: rgba(255,255,255,0.82);
    font-size: 0.69rem;
}

.prompt-option small {
    color: rgba(255,255,255,0.36);
    font-size: 0.58rem;
    line-height: 1.35;
}

.prompt-option:has(input:checked) {
    border-color: rgba(167,139,250,0.2);
    background: rgba(124,58,237,0.07);
}

.prompt-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 0.4rem;
}

@media (max-width: 767px) {
    .vocal-prompt-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .prompt-path-preview {
        width: 100%;
    }

    .prompt-options {
        grid-template-columns: 1fr;
    }

    .prompt-form-actions {
        flex-direction: column-reverse;
    }

    .prompt-form-actions .adm-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const hierarchy = @json($promptHierarchy);

    const initialSubjectId = @json(
        (string) old('subject_id', '')
    );

    const initialLevelId = @json(
        (string) old('level_id', '')
    );

    const initialClassId = @json(
        (string) old('class_id', '')
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

    const readingText =
        document.getElementById('reading_text');

    const readingCharacterCount =
        document.getElementById(
            'readingCharacterCount'
        );

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
        selectedValue = ''
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
                placeholder
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

    const updateCharacterCount = () => {
        readingCharacterCount.textContent =
            String(readingText.value.length);
    };

    readingText.addEventListener(
        'input',
        updateCharacterCount
    );

    updateCharacterCount();

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
