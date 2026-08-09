@extends('layouts.admin')

@section('title', 'Modifier le devoir')
@section('page_title', 'Modifier devoir')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Créneau → Modifier'
)

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="adm-page-header">
            <div>
                <h1>Modifier le devoir</h1>

                <div class="subtitle">
                    Le devoir reste rattaché à un créneau précis.
                </div>
            </div>
        </div>

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
                    'admin.devoirs.update',
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
                    'hierarchy' => $editHierarchy,
                    'prefix' => 'adminDevoirEdit',
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

            <div class="adm-card mb-4">
                <div class="adm-card-header">
                    <h4>
                        <i class="bi bi-file-earmark-text"></i>
                        Contenu du devoir
                    </h4>
                </div>

                <div class="adm-card-body">
                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="devoirTitle"
                        >
                            Titre
                        </label>

                        <input
                            id="devoirTitle"
                            type="text"
                            name="title"
                            value="{{
                                old(
                                    'title',
                                    $devoir->title
                                )
                            }}"
                            class="adm-form-control
                                @error('title') error @enderror"
                            maxlength="255"
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
                            for="devoirDescription"
                        >
                            Description
                        </label>

                        <textarea
                            id="devoirDescription"
                            name="description"
                            rows="5"
                            class="adm-form-control adm-form-textarea
                                @error('description') error @enderror"
                        >{{ old(
                            'description',
                            $devoir->description
                        ) }}</textarea>

                        @error('description')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="devoirCourse"
                                >
                                    Cours associé
                                </label>

                                <select
                                    id="devoirCourse"
                                    name="course_id"
                                    class="adm-form-select
                                        @error('course_id') error @enderror"
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
                                                        $courseOption
                                                            ->slot_code
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

                                <small
                                    style="
                                        display:block;
                                        margin-top:6px;
                                        color:var(--adm-text-muted);
                                        font-size:.7rem;
                                    "
                                >
                                    La liste est automatiquement limitée
                                    au créneau choisi.
                                </small>

                                @error('course_id')
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
                                    for="devoirDueDate"
                                >
                                    Date limite
                                </label>

                                <input
                                    id="devoirDueDate"
                                    type="date"
                                    name="due_date"
                                    value="{{
                                        old(
                                            'due_date',
                                            optional(
                                                $devoir->due_date
                                                    ? \Carbon\Carbon::parse(
                                                        $devoir->due_date
                                                    )
                                                    : null
                                            )->format('Y-m-d')
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('due_date') error @enderror"
                                    required
                                >

                                @error('due_date')
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
                            for="devoirFile"
                        >
                            Remplacer le PDF
                        </label>

                        <input
                            id="devoirFile"
                            type="file"
                            name="file"
                            accept="application/pdf,.pdf"
                            class="adm-form-control
                                @error('file') error @enderror"
                        >

                        @error('file')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3">
                <a
                    href="{{ route('admin.devoirs.index') }}"
                    class="adm-btn adm-btn-ghost flex-fill text-center"
                >
                    <i class="bi bi-arrow-left"></i>
                    Annuler
                </a>

                <button
                    type="submit"
                    class="adm-btn adm-btn-primary flex-fill"
                >
                    <i class="bi bi-save"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const subject =
            document.getElementById(
                'adminDevoirEditSubject'
            );

        const level =
            document.getElementById(
                'adminDevoirEditLevel'
            );

        const classroom =
            document.getElementById(
                'adminDevoirEditClass'
            );

        const slot =
            document.getElementById(
                'adminDevoirEditSlot'
            );

        const course =
            document.getElementById(
                'devoirCourse'
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
                    String(
                        option.dataset.subject
                    ) === String(subject.value)
                    && String(
                        option.dataset.level
                    ) === String(level.value)
                    && String(
                        option.dataset.class
                    ) === String(classroom.value)
                    && String(
                        option.dataset.slot || ''
                    ).toUpperCase() === slotText;

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
