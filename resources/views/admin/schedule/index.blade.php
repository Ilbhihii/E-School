@extends('layouts.admin')

@section('title', 'Gestion du Planning')
@section('page_title', 'Planning')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Séance'
)

@section('content')

<div class="adm-page-header">
    <div>
        <h1>
            <i
                class="bi bi-calendar3"
                style="color:#60A5FA;"
            ></i>

            Planning
        </h1>

        <div class="subtitle">
            Ajoutez les séances selon la structure
            Matière → Niveau → Classe
        </div>
    </div>
</div>

@if($errors->any())
    <div class="adm-alert adm-alert-danger">
        <span class="adm-alert-icon">
            <i class="bi bi-exclamation-circle-fill"></i>
        </span>

        <ul style="margin:0;padding-left:1.25rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="row g-4">
    <!-- =====================================================
         FORMULAIRE
         ===================================================== -->
    <div class="col-xl-5">
        <div class="adm-card schedule-form-card">
            <div class="adm-card-header schedule-card-header">
                <div>
                    <h4>
                        <i
                            class="bi bi-calendar-plus"
                            style="color:#4ADE80;"
                        ></i>

                        Ajouter une séance
                    </h4>

                    <p>
                        Les niveaux et les classes seront filtrés
                        automatiquement.
                    </p>
                </div>
            </div>

            <div class="adm-card-body">
                <form
                    method="POST"
                    action="{{ route('admin.schedule.store') }}"
                    id="scheduleCreateForm"
                >
                    @csrf

                    <div class="schedule-hierarchy-box">
                        <div class="schedule-path-preview">
                            <span id="schedulePathSubject">
                                Matière
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span id="schedulePathLevel">
                                Niveau
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span id="schedulePathClass">
                                Classe
                            </span>
                        </div>

                        <!-- 1. MATIÈRE -->
                        <div class="schedule-step">
                            <span class="schedule-step-number">
                                1
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="schedule_subject_id"
                                >
                                    Matière
                                    <span class="schedule-required">
                                        *
                                    </span>
                                </label>

                                <select
                                    name="subject_id"
                                    id="schedule_subject_id"
                                    class="adm-form-select
                                        @error('subject_id')
                                            error
                                        @enderror"
                                    required
                                >
                                    <option value="">
                                        Choisir une matière
                                    </option>

                                    @foreach($subjects as $subject)
                                        <option
                                            value="{{ $subject->id }}"
                                        >
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <small class="schedule-field-help">
                                    Choisissez d’abord la matière.
                                </small>

                                @error('subject_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- 2. NIVEAU -->
                        <div class="schedule-step">
                            <span class="schedule-step-number">
                                2
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="schedule_level_id"
                                >
                                    Niveau
                                    <span class="schedule-required">
                                        *
                                    </span>
                                </label>

                                <select
                                    name="level_id"
                                    id="schedule_level_id"
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

                                <small class="schedule-field-help">
                                    Seuls les niveaux de cette matière
                                    seront affichés.
                                </small>

                                @error('level_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- 3. CLASSE -->
                        <div class="schedule-step">
                            <span class="schedule-step-number">
                                3
                            </span>

                            <div class="adm-form-group mb-0">
                                <label
                                    class="adm-form-label"
                                    for="schedule_class_id"
                                >
                                    Classe
                                    <span class="schedule-required">
                                        *
                                    </span>
                                </label>

                                <select
                                    name="class_id"
                                    id="schedule_class_id"
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

                                <small class="schedule-field-help">
                                    Seules les classes de ce niveau
                                    liées à la matière apparaîtront.
                                </small>

                                @error('class_id')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- PROFESSEUR -->
                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="schedule_prof_id"
                        >
                            Professeur
                            <span class="schedule-required">*</span>
                        </label>

                        <select
                            name="prof_id"
                            id="schedule_prof_id"
                            class="adm-form-select
                                @error('prof_id') error @enderror"
                            required
                        >
                            <option value="">
                                Choisir un professeur
                            </option>

                            @foreach($teachers as $teacher)
                                <option
                                    value="{{ $teacher->id }}"
                                    @selected(
                                        (string) old('prof_id')
                                        === (string) $teacher->id
                                    )
                                >
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('prof_id')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- DATE -->
                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="schedule_date"
                        >
                            Date
                            <span class="schedule-required">*</span>
                        </label>

                        <input
                            id="schedule_date"
                            type="date"
                            name="date"
                            value="{{ old('date') }}"
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

                    <!-- HEURES -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="schedule_start_time"
                                >
                                    Début
                                    <span class="schedule-required">
                                        *
                                    </span>
                                </label>

                                <input
                                    id="schedule_start_time"
                                    type="time"
                                    name="start_time"
                                    value="{{ old('start_time') }}"
                                    class="adm-form-control
                                        @error('start_time')
                                            error
                                        @enderror"
                                    required
                                >

                                @error('start_time')
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
                                    for="schedule_end_time"
                                >
                                    Fin
                                    <span class="schedule-required">
                                        *
                                    </span>
                                </label>

                                <input
                                    id="schedule_end_time"
                                    type="time"
                                    name="end_time"
                                    value="{{ old('end_time') }}"
                                    class="adm-form-control
                                        @error('end_time')
                                            error
                                        @enderror"
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

                    <button
                        type="submit"
                        class="adm-btn adm-btn-primary
                            w-100 mt-2"
                    >
                        <i class="bi bi-plus-lg"></i>
                        Ajouter au planning
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- =====================================================
         TABLEAU
         ===================================================== -->
    <div class="col-xl-7">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4>
                    <i
                        class="bi bi-calendar-week"
                        style="
                            color:rgba(255,255,255,0.35);
                        "
                    ></i>

                    Planning complet
                </h4>

                <div class="card-actions">
                    <span class="schedule-count">
                        {{ $schedules->count() }}
                        séance(s)
                    </span>
                </div>
            </div>

            <div class="adm-card-body p-0">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Matière</th>
                                <th>Niveau</th>
                                <th>Classe</th>
                                <th>Professeur</th>
                                <th>Date</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th style="text-align:right;">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($schedules as $schedule)
                                <tr>
                                    <td>
                                        <span
                                            class="adm-badge
                                                adm-badge-primary"
                                        >
                                            {{ $schedule->subject }}
                                        </span>
                                    </td>

                                    <td>
                                        {{
                                            $schedule
                                                ->classRoom
                                                ?->level
                                                ?->name
                                            ?? '-'
                                        }}
                                    </td>

                                    <td>
                                        <span
                                            style="font-weight:600;"
                                        >
                                            {{
                                                $schedule
                                                    ->classRoom
                                                    ?->name
                                                ?? 'N/A'
                                            }}
                                        </span>
                                    </td>

                                    <td>
                                        {{
                                            $schedule->prof?->name
                                            ?? 'N/A'
                                        }}
                                    </td>

                                    <td
                                        class="schedule-muted-cell"
                                    >
                                        {{
                                            $schedule->date
                                                ?->format('d/m/Y')
                                            ?? '-'
                                        }}
                                    </td>

                                    <td
                                        class="schedule-muted-cell"
                                    >
                                        {{
                                            $schedule->start_time
                                                ?->format('H:i')
                                            ?? '-'
                                        }}
                                    </td>

                                    <td
                                        class="schedule-muted-cell"
                                    >
                                        {{
                                            $schedule->end_time
                                                ?->format('H:i')
                                            ?? '-'
                                        }}
                                    </td>

                                    <td style="text-align:right;">
                                        <form
                                            method="POST"
                                            action="{{
                                                route(
                                                    'admin.schedule.destroy',
                                                    $schedule->id
                                                )
                                            }}"
                                            style="display:inline;"
                                            onsubmit="
                                                return confirm(
                                                    'Supprimer cette séance ?'
                                                )
                                            "
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                class="adm-btn
                                                    adm-btn-danger
                                                    adm-btn-sm"
                                                type="submit"
                                            >
                                                <i
                                                    class="bi bi-trash"
                                                ></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="adm-empty">
                                            <div
                                                class="adm-empty-icon"
                                            >
                                                <i
                                                    class="bi
                                                        bi-calendar"
                                                ></i>
                                            </div>

                                            <h5>Aucune séance</h5>

                                            <p>
                                                Ajoutez votre première
                                                séance dans le formulaire.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.schedule-form-card {
    overflow: hidden;
}

.schedule-card-header h4 {
    margin-bottom: 4px;
}

.schedule-card-header p {
    margin: 0;
    color: var(--adm-text-muted);
    font-size: 0.72rem;
}

.schedule-hierarchy-box {
    margin-bottom: 1.15rem;
    padding: 0.9rem;
    border: 1px solid rgba(96,165,250,0.12);
    border-radius: 15px;
    background:
        linear-gradient(
            145deg,
            rgba(37,99,235,0.055),
            rgba(15,23,42,0.15)
        );
}

.schedule-path-preview {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
    margin-bottom: 0.9rem;
    padding: 8px 10px;
    border: 1px solid rgba(96,165,250,0.11);
    border-radius: 11px;
    color: rgba(255,255,255,0.48);
    background: rgba(37,99,235,0.055);
    font-size: 0.67rem;
}

.schedule-path-preview span.is-selected {
    color: #93C5FD;
    font-weight: 750;
}

.schedule-path-preview i {
    color: rgba(255,255,255,0.2);
    font-size: 0.56rem;
}

.schedule-step {
    position: relative;
    margin-bottom: 0.75rem;
    padding: 0.78rem;
    border: 1px solid rgba(255,255,255,0.045);
    border-radius: 12px;
    background: rgba(7,15,30,0.24);
}

.schedule-step:last-child {
    margin-bottom: 0;
}

.schedule-step-number {
    position: absolute;
    top: -8px;
    right: 10px;
    width: 22px;
    height: 22px;
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
    font-size: 0.61rem;
    font-weight: 800;
}

.schedule-required {
    color: var(--adm-danger);
}

.schedule-field-help {
    display: block;
    margin-top: 6px;
    color: var(--adm-text-muted);
    font-size: 0.62rem;
    line-height: 1.4;
}

.schedule-count,
.schedule-muted-cell {
    color: var(--adm-text-muted);
    font-size: 0.78rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const hierarchy = @json($scheduleHierarchy);

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
        document.getElementById(
            'schedule_subject_id'
        );

    const levelSelect =
        document.getElementById(
            'schedule_level_id'
        );

    const classSelect =
        document.getElementById(
            'schedule_class_id'
        );

    const pathSubject =
        document.getElementById(
            'schedulePathSubject'
        );

    const pathLevel =
        document.getElementById(
            'schedulePathLevel'
        );

    const pathClass =
        document.getElementById(
            'schedulePathClass'
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
                ? 'Choisir une classe'
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
                ? 'Choisir un niveau'
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
     * Restaurer les choix après une erreur de validation.
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
