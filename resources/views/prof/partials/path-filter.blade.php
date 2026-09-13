@php
    $pathFilterAction = $action ?? url()->current();
    $pathFilterButton = $buttonLabel ?? 'Afficher';
    $pathFilterShowGroup = (bool) ($showGroup ?? false);

    $pathFilterSubject =
        $selectedSubjectId ?? request('subject_id');

    $pathFilterLevel =
        $selectedLevelId ?? request('level_id');

    $pathFilterClass =
        $selectedClassId ?? request('class_id');

    $pathFilterSlot =
        $selectedSlotId ?? request('class_slot_id');

    $pathFilterExtra = $extraQuery ?? [];
@endphp

<section class="pp-panel pps-filter-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-diagram-3-fill"></i>
                Parcours pédagogique
            </h2>

            <p class="pp-panel-subtitle">
                Matière → Niveau → Classe
                @if($pathFilterShowGroup)
                    → Groupe
                @endif
            </p>
        </div>

        <span class="pp-panel-meta">
            <i class="bi bi-funnel-fill"></i>
            Filtre
        </span>
    </header>

    <div class="pp-panel-body">
        <form
            method="GET"
            action="{{ $pathFilterAction }}"
            class="pps-filter-grid"
            data-prof-path-filter
        >
            @foreach($pathFilterExtra as $name => $value)
                @if($value !== null && $value !== '')
                    <input
                        type="hidden"
                        name="{{ $name }}"
                        value="{{ $value }}"
                    >
                @endif
            @endforeach

            <div class="pp-field">
                <label for="profPathSubject" class="pp-label">
                    Matière
                </label>
                <select
                    name="subject_id"
                    id="profPathSubject"
                    class="adm-form-select"
                >
                    <option value="">Toutes les matières</option>
                </select>
            </div>

            <div class="pp-field">
                <label for="profPathLevel" class="pp-label">
                    Niveau
                </label>
                <select
                    name="level_id"
                    id="profPathLevel"
                    class="adm-form-select"
                    disabled
                >
                    <option value="">Tous les niveaux</option>
                </select>
            </div>

            <div class="pp-field">
                <label for="profPathClass" class="pp-label">
                    Classe
                </label>
                <select
                    name="class_id"
                    id="profPathClass"
                    class="adm-form-select"
                    disabled
                >
                    <option value="">Toutes les classes</option>
                </select>
            </div>

            @if($pathFilterShowGroup)
                <div class="pp-field">
                    <label for="profPathSlot" class="pp-label">
                        Groupe
                    </label>
                    <select
                        name="class_slot_id"
                        id="profPathSlot"
                        class="adm-form-select"
                        disabled
                    >
                        <option value="">Tous les groupes</option>
                    </select>
                </div>
            @endif

            <button
                type="submit"
                class="adm-btn adm-btn-primary pps-filter-submit"
            >
                <i class="bi bi-search"></i>
                {{ $pathFilterButton }}
            </button>

            @if(
                $pathFilterSubject
                || $pathFilterLevel
                || $pathFilterClass
                || ($pathFilterShowGroup && $pathFilterSlot)
            )
                <a
                    href="{{ $pathFilterAction }}"
                    class="adm-btn adm-btn-ghost pps-filter-reset"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Réinitialiser
                </a>
            @endif
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hierarchy = @json($profHierarchy ?? []);
    const subject = document.getElementById('profPathSubject');
    const level = document.getElementById('profPathLevel');
    const classroom = document.getElementById('profPathClass');
    const slot = document.getElementById('profPathSlot');

    if (!subject || !level || !classroom) return;

    const wantedSubject = @json((string) ($pathFilterSubject ?? ''));
    const wantedLevel = @json((string) ($pathFilterLevel ?? ''));
    const wantedClass = @json((string) ($pathFilterClass ?? ''));
    const wantedSlot = @json((string) ($pathFilterSlot ?? ''));

    const option = (value, label, selected = false) => {
        const item = document.createElement('option');
        item.value = String(value);
        item.textContent = label;
        item.selected = selected;
        return item;
    };

    const selectedSubject = () =>
        hierarchy.find(
            item => String(item.id) === String(subject.value)
        );

    const selectedLevel = subjectItem =>
        subjectItem?.levels?.find(
            item => String(item.id) === String(level.value)
        );

    const selectedClass = levelItem =>
        levelItem?.classes?.find(
            item => String(item.id) === String(classroom.value)
        );

    const fillSlots = (wanted = '') => {
        if (!slot) return;

        slot.innerHTML = '';
        slot.appendChild(option('', 'Tous les groupes'));

        const classItem =
            selectedClass(selectedLevel(selectedSubject()));

        (classItem?.slots || []).forEach(item => {
            slot.appendChild(
                option(
                    item.id,
                    item.code || 'Groupe',
                    String(item.id) === String(wanted)
                )
            );
        });

        slot.disabled = !classItem;

        if (wanted) {
            slot.value = String(wanted);
        }
    };

    const fillClasses = (
        wanted = '',
        wantedSlotId = ''
    ) => {
        classroom.innerHTML = '';
        classroom.appendChild(option('', 'Toutes les classes'));

        const subjectItem = selectedSubject();
        const levelItem = selectedLevel(subjectItem);
        const classes = levelItem?.classes || [];

        classes.forEach(item => {
            classroom.appendChild(
                option(
                    item.id,
                    item.name,
                    String(item.id) === String(wanted)
                )
            );
        });

        classroom.disabled = !levelItem;

        if (wanted) {
            classroom.value = String(wanted);
        }

        fillSlots(wantedSlotId);
    };

    const fillLevels = (
        wanted = '',
        wantedClass = '',
        wantedSlotId = ''
    ) => {
        level.innerHTML = '';
        level.appendChild(option('', 'Tous les niveaux'));

        const subjectItem = selectedSubject();
        const levels = subjectItem?.levels || [];

        levels.forEach(item => {
            level.appendChild(
                option(
                    item.id,
                    item.name,
                    String(item.id) === String(wanted)
                )
            );
        });

        level.disabled = !subjectItem;

        if (wanted) {
            level.value = String(wanted);
        }

        fillClasses(wantedClass, wantedSlotId);
    };

    hierarchy.forEach(item => {
        subject.appendChild(
            option(
                item.id,
                item.name,
                String(item.id) === String(wantedSubject)
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
});
</script>
@endpush
