@extends('layouts.prof')

@section('title', 'Modifier le devoir')
@section('page_title', 'Modifier devoir')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Créneau → Modifier'
)

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-pencil-square"></i>
            Modification
        </span>

        <h1 class="pp-page-title">
            Modifier le devoir
        </h1>

        <p class="pp-page-description">
            Vous pouvez déplacer le devoir uniquement
            vers un créneau qui vous est réellement affecté.
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
        <strong>
            La modification n’a pas été enregistrée.
        </strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    method="POST"
    action="{{
        route(
            'prof.devoir.update',
            $devoir
        )
    }}"
    enctype="multipart/form-data"
>
    @csrf
    @method('PUT')

    @include(
        'components.pedagogical-path-edit',
        [
            'hierarchy' => $profHierarchy,
            'prefix' => 'profDevoirEdit',
            'selectedSubject' =>
                $selectedSubjectId,
            'selectedLevel' =>
                $selectedLevelId,
            'selectedClass' =>
                $selectedClassId,
            'selectedSlot' =>
                $selectedSlotId,
        ]
    )

    <section class="pp-panel">
        <header class="pp-panel-head">
            <div class="pp-panel-title-wrap">
                <h2 class="pp-panel-title">
                    <i class="bi bi-file-earmark-text"></i>
                    Contenu du devoir
                </h2>
            </div>
        </header>

        <div class="pp-form-section">
            <div class="pp-field">
                <label
                    for="profEditTitle"
                    class="pp-label"
                >
                    Titre
                </label>

                <input
                    id="profEditTitle"
                    type="text"
                    name="title"
                    value="{{
                        old(
                            'title',
                            $devoir->title
                        )
                    }}"
                    class="adm-form-control"
                    maxlength="255"
                    required
                >
            </div>

            <div class="pp-field">
                <label
                    for="profEditDescription"
                    class="pp-label"
                >
                    Description
                </label>

                <textarea
                    id="profEditDescription"
                    name="description"
                    rows="6"
                    class="adm-form-control"
                >{{ old(
                    'description',
                    $devoir->description
                ) }}</textarea>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="pp-field">
                        <label
                            for="profEditCourse"
                            class="pp-label"
                        >
                            Cours associé
                        </label>

                        <select
                            id="profEditCourse"
                            name="course_id"
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
                                    data-slot="{{
                                        strtoupper(
                                            trim(
                                                (string)
                                                $courseOption->slot_code
                                            )
                                        )
                                    }}"
                                    {{
                                        (string) old(
                                            'course_id',
                                            $devoir->course_id
                                        )
                                        ===
                                        (string) $courseOption->id
                                            ? 'selected'
                                            : ''
                                    }}
                                >
                                    {{ $courseOption->title }}
                                    @if($courseOption->slot_code)
                                        — {{ $courseOption->slot_code }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="pp-field">
                        <label
                            for="profEditDue"
                            class="pp-label"
                        >
                            Date limite
                        </label>

                        <input
                            id="profEditDue"
                            type="date"
                            name="due_date"
                            value="{{
                                old(
                                    'due_date',
                                    $devoir->due_date
                                        ? \Carbon\Carbon::parse(
                                            $devoir->due_date
                                        )->format('Y-m-d')
                                        : ''
                                )
                            }}"
                            class="adm-form-control"
                            required
                        >
                    </div>
                </div>
            </div>

            <div class="pp-field">
                <label
                    for="profEditFile"
                    class="pp-label"
                >
                    Remplacer le PDF
                </label>

                <input
                    id="profEditFile"
                    type="file"
                    name="file"
                    accept="application/pdf,.pdf"
                    class="adm-form-control"
                >
            </div>
        </div>
    </section>

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
                <i class="bi bi-save"></i>
                Enregistrer
            </button>
        </div>
    </section>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const subject =
            document.getElementById(
                'profDevoirEditSubject'
            );

        const level =
            document.getElementById(
                'profDevoirEditLevel'
            );

        const classroom =
            document.getElementById(
                'profDevoirEditClass'
            );

        const slot =
            document.getElementById(
                'profDevoirEditSlot'
            );

        const course =
            document.getElementById(
                'profEditCourse'
            );

        if (
            !subject
            || !level
            || !classroom
            || !slot
            || !course
        ) {
            return;
        }

        function refreshCourses() {
            const slotText =
                slot.options[
                    slot.selectedIndex
                ]?.textContent
                ?.trim()
                ?.split(/\s+/)[0]
                ?.toUpperCase()
                ?? '';

            Array.from(
                course.options
            ).forEach(option => {
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
                        === String(classroom.value)
                    && String(
                        option.dataset.slot || ''
                    ).toUpperCase()
                        === slotText;

                option.hidden = !matches;
                option.disabled = !matches;
            });

            const selected =
                course.options[
                    course.selectedIndex
                ];

            if (
                selected
                && selected.value
                && selected.disabled
            ) {
                course.value = '';
            }
        }

        [
            subject,
            level,
            classroom,
            slot,
        ].forEach(element => {
            element.addEventListener(
                'change',
                () => setTimeout(
                    refreshCourses,
                    0
                )
            );
        });

        setTimeout(
            refreshCourses,
            0
        );
    }
);
</script>
@endpush
