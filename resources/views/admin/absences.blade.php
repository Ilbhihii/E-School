@extends('layouts.admin')

@section('title', 'Gestion des absences')
@section('page_title', 'Absences')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Créneau'
)

@section('content')

<style>
.absence-filter-card {
    border-color: rgba(99, 102, 241, 0.18);
    background:
        linear-gradient(
            135deg,
            rgba(15, 23, 42, 0.92),
            rgba(30, 41, 59, 0.72)
        );
}

.absence-filter-heading {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1rem;
}

.absence-filter-icon {
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(129, 140, 248, .18);
    border-radius: 11px;
    color: #c4b5fd;
    background: rgba(99, 102, 241, .14);
}

.absence-filter-title {
    color: #f1f5f9;
    font-size: .92rem;
    font-weight: 800;
}

.absence-filter-help {
    margin-top: 2px;
    color: var(--adm-text-muted);
    font-size: .72rem;
}

.absence-full-path {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
}

.absence-path-part {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 7px;
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 8px;
    color: #cbd5e1;
    background: rgba(255,255,255,.04);
    font-size: .62rem;
    font-weight: 700;
}

.absence-path-arrow {
    color: #475569;
    font-size: .6rem;
}

.absence-slot-badge {
    display: inline-flex;
    min-width: 38px;
    min-height: 28px;
    align-items: center;
    justify-content: center;
    padding: 4px 9px;
    border: 1px solid rgba(139,92,246,.19);
    border-radius: 9px;
    color: #ddd6fe;
    background: rgba(124,58,237,.10);
    font-size: .65rem;
    font-weight: 850;
}

.absence-unknown {
    color: #64748b;
    font-size: .72rem;
}
</style>

<div class="adm-page-header">
    <div>
        <h1>Absences</h1>

        <div class="subtitle">
            Suivi par
            Matière → Niveau → Classe → Créneau.
        </div>
    </div>

    <div class="page-actions">
        <a
            href="{{
                route(
                    'admin.absences.create',
                    request()->only([
                        'subject_id',
                        'level_id',
                        'class_id',
                        'class_slot_id',
                    ])
                )
            }}"
            class="adm-btn adm-btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Nouvelle absence
        </a>
    </div>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="adm-card absence-filter-card mb-4">
    <div
        class="adm-card-body"
        style="padding:1.25rem;"
    >
        <div class="absence-filter-heading">
            <div class="absence-filter-icon">
                <i class="bi bi-funnel-fill"></i>
            </div>

            <div>
                <div class="absence-filter-title">
                    Filtrer les absences
                </div>

                <div class="absence-filter-help">
                    Choisissez
                    Matière → Niveau → Classe → Créneau,
                    ou utilisez « Afficher tout ».
                </div>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('admin.absences') }}"
            class="row g-3 align-items-end"
            id="absenceFilterForm"
        >
            <div class="col-xl-3 col-md-6">
                <label
                    class="adm-form-label"
                    for="absenceSubjectFilter"
                >
                    Matière
                </label>

                <select
                    name="subject_id"
                    id="absenceSubjectFilter"
                    class="adm-form-select"
                >
                    <option value="">
                        Toutes les matières
                    </option>

                    @foreach($subjects as $subject)
                        <option
                            value="{{ $subject->id }}"
                            {{
                                (string) request('subject_id')
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
            </div>

            <div class="col-xl-2 col-md-6">
                <label
                    class="adm-form-label"
                    for="absenceLevelFilter"
                >
                    Niveau
                </label>

                <select
                    name="level_id"
                    id="absenceLevelFilter"
                    class="adm-form-select"
                    data-selected="{{ request('level_id') }}"
                    {{
                        request('subject_id')
                            ? ''
                            : 'disabled'
                    }}
                >
                    <option value="">
                        Tous les niveaux
                    </option>
                </select>
            </div>

            <div class="col-xl-2 col-md-6">
                <label
                    class="adm-form-label"
                    for="absenceClassFilter"
                >
                    Classe
                </label>

                <select
                    name="class_id"
                    id="absenceClassFilter"
                    class="adm-form-select"
                    data-selected="{{ request('class_id') }}"
                    {{
                        request('level_id')
                            ? ''
                            : 'disabled'
                    }}
                >
                    <option value="">
                        Toutes les classes
                    </option>
                </select>
            </div>

            <div class="col-xl-2 col-md-6">
                <label
                    class="adm-form-label"
                    for="absenceSlotFilter"
                >
                    Créneau
                </label>

                <select
                    name="class_slot_id"
                    id="absenceSlotFilter"
                    class="adm-form-select"
                    data-selected="{{
                        request('class_slot_id')
                    }}"
                    {{
                        request('class_id')
                            ? ''
                            : 'disabled'
                    }}
                >
                    <option value="">
                        Tous les créneaux
                    </option>
                </select>
            </div>

            <div class="col-xl-3 d-grid gap-2">
                <button
                    type="submit"
                    class="adm-btn adm-btn-primary"
                >
                    <i class="bi bi-funnel"></i>
                    Filtrer
                </button>

                <a
                    href="{{ route('admin.absences') }}"
                    class="adm-btn adm-btn-ghost text-center"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Afficher tout
                </a>
            </div>
        </form>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4>
            <i
                class="bi bi-calendar-x"
                style="color:rgba(255,255,255,0.35);"
            ></i>
            Liste des absences
        </h4>

        <div class="card-actions">
            <span
                style="
                    color:var(--adm-text-muted);
                    font-size:0.8rem;
                "
            >
                {{ $absences->total() }}
                résultat{{ $absences->total() > 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Date</th>
                        <th>Parcours</th>
                        <th>Créneau</th>
                        <th style="text-align:center;">
                            Statut
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($absences as $absence)
                        <tr>
                            <td>
                                <div
                                    style="
                                        display:flex;
                                        align-items:center;
                                        gap:12px;
                                    "
                                >
                                    <div
                                        class="adm-avatar"
                                        style="
                                            background:
                                            var(--adm-gradient-primary);
                                        "
                                    >
                                        {{
                                            strtoupper(
                                                substr(
                                                    $absence->user?->name
                                                        ?? 'E',
                                                    0,
                                                    1
                                                )
                                            )
                                        }}
                                    </div>

                                    <div>
                                        <div style="font-weight:600;">
                                            {{
                                                $absence->user?->name
                                                    ?? 'Inconnu'
                                            }}
                                        </div>

                                        @if($absence->user?->email)
                                            <small
                                                style="
                                                    color:
                                                    var(--adm-text-muted);
                                                "
                                            >
                                                {{ $absence->user->email }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td
                                style="
                                    color:var(--adm-text-secondary);
                                    font-weight:600;
                                "
                            >
                                {{
                                    optional(
                                        $absence->date
                                    )->format('d/m/Y')
                                    ?? '-'
                                }}
                            </td>

                            <td>
                                <div class="absence-full-path">
                                    @if($absence->subject)
                                        <span class="absence-path-part">
                                            {{ $absence->subject->name }}
                                        </span>
                                    @else
                                        <span class="absence-unknown">
                                            Matière non définie
                                        </span>
                                    @endif

                                    <span class="absence-path-arrow">→</span>

                                    @if($absence->level)
                                        <span class="absence-path-part">
                                            {{ $absence->level->name }}
                                        </span>
                                    @else
                                        <span class="absence-unknown">
                                            Niveau non défini
                                        </span>
                                    @endif

                                    <span class="absence-path-arrow">→</span>

                                    @if($absence->classRoom)
                                        <span class="absence-path-part">
                                            {{ $absence->classRoom->name }}
                                        </span>
                                    @else
                                        <span class="absence-unknown">
                                            Classe non définie
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if($absence->classSlot)
                                    <span class="absence-slot-badge">
                                        {{ $absence->classSlot->code }}
                                    </span>
                                @else
                                    <span class="absence-unknown">
                                        Ancienne absence sans créneau
                                    </span>
                                @endif
                            </td>

                            <td style="text-align:center;">
                                @if($absence->present)
                                    <span
                                        class="
                                            adm-badge
                                            adm-badge-success
                                        "
                                    >
                                        <i
                                            class="
                                                bi
                                                bi-check-circle-fill
                                            "
                                        ></i>
                                        Présent
                                    </span>
                                @else
                                    <span
                                        class="
                                            adm-badge
                                            adm-badge-danger
                                        "
                                    >
                                        <i
                                            class="
                                                bi
                                                bi-x-circle-fill
                                            "
                                        ></i>
                                        Absent
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="adm-empty">
                                    <div class="adm-empty-icon">
                                        <i
                                            class="
                                                bi
                                                bi-calendar-check
                                            "
                                        ></i>
                                    </div>

                                    <h5>Aucune absence</h5>

                                    <p>
                                        Aucun résultat pour les critères
                                        sélectionnés.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($absences->hasPages())
        <div class="adm-card-footer">
            {{ $absences->links() }}
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const hierarchy = @json($absenceHierarchy);

    const subject =
        document.getElementById(
            'absenceSubjectFilter'
        );

    const level =
        document.getElementById(
            'absenceLevelFilter'
        );

    const classroom =
        document.getElementById(
            'absenceClassFilter'
        );

    const slot =
        document.getElementById(
            'absenceSlotFilter'
        );

    if (
        !subject
        || !level
        || !classroom
        || !slot
    ) {
        return;
    }

    const selectedLevel =
        String(level.dataset.selected || '');

    const selectedClass =
        String(classroom.dataset.selected || '');

    const selectedSlot =
        String(slot.dataset.selected || '');

    const option = (
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

    const currentSubject = () =>
        hierarchy.find(
            item =>
                String(item.id)
                === String(subject.value)
        );

    const currentLevel =
        subjectItem =>
            subjectItem?.levels?.find(
                item =>
                    String(item.id)
                    === String(level.value)
            );

    const currentClass = () => {
        const subjectItem =
            currentSubject();

        const levelItem =
            currentLevel(subjectItem);

        return levelItem?.classes?.find(
            item =>
                String(item.id)
                === String(classroom.value)
        );
    };

    const fillSlots = (
        wanted = ''
    ) => {
        slot.innerHTML = '';

        slot.appendChild(
            option(
                '',
                'Tous les créneaux'
            )
        );

        const classItem =
            currentClass();

        const slots =
            classItem?.slots || [];

        slots.forEach(item => {
            slot.appendChild(
                option(
                    item.id,
                    item.code,
                    String(item.id)
                    === String(wanted)
                )
            );
        });

        slot.disabled =
            !classItem;

        if (!classItem) {
            slot.value = '';
        }
    };

    const fillClasses = (
        wanted = '',
        wantedSlot = ''
    ) => {
        classroom.innerHTML = '';

        classroom.appendChild(
            option(
                '',
                'Toutes les classes'
            )
        );

        const subjectItem =
            currentSubject();

        const levelItem =
            currentLevel(subjectItem);

        const classes =
            levelItem?.classes || [];

        classes.forEach(item => {
            classroom.appendChild(
                option(
                    item.id,
                    item.name,
                    String(item.id)
                    === String(wanted)
                )
            );
        });

        classroom.disabled =
            !levelItem;

        if (!levelItem) {
            classroom.value = '';
        }

        fillSlots(wantedSlot);
    };

    const fillLevels = (
        wantedLevel = '',
        wantedClass = '',
        wantedSlot = ''
    ) => {
        level.innerHTML = '';

        level.appendChild(
            option(
                '',
                'Tous les niveaux'
            )
        );

        const subjectItem =
            currentSubject();

        const levels =
            subjectItem?.levels || [];

        levels.forEach(item => {
            level.appendChild(
                option(
                    item.id,
                    item.name,
                    String(item.id)
                    === String(wantedLevel)
                )
            );
        });

        level.disabled =
            !subjectItem;

        if (!subjectItem) {
            level.value = '';
        }

        fillClasses(
            wantedClass,
            wantedSlot
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

    fillLevels(
        selectedLevel,
        selectedClass,
        selectedSlot
    );
});
</script>

@endsection
