@extends('layouts.prof')

@section('title', 'Modifier le devoir')
@section('page_title', 'Modifier devoir')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Modifier'
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
            vers une classe qui vous est réellement affectée.
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
        'components.pedagogical-path-class-edit',
        [
            'hierarchy' => $profHierarchy,
            'prefix' => 'profDevoirEdit',
            'selectedSubject' =>
                $selectedSubjectId,
            'selectedLevel' =>
                $selectedLevelId,
            'selectedClass' =>
                $selectedClassId,
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
                <label class="pp-label">
                    Semaine pédagogique
                </label>

                <div class="pp-week-auto-card">
                    <div class="pp-week-auto-icon">
                        <i class="bi bi-calendar-week-fill"></i>
                    </div>

                    <div class="pp-week-auto-copy">
                        <strong>
                            SEMAINE {{ $weekNumber }}
                        </strong>

                        <span>
                            Le numéro est conservé pendant la modification.
                            Si vous déplacez le devoir vers une autre classe,
                            la semaine sera recalculée automatiquement.
                        </span>
                    </div>

                    <span class="pp-week-auto-badge">
                        Automatique
                    </span>
                </div>
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
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
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
                                    {{
                                        $automaticDueDate
                                            ->format('d/m/Y')
                                    }}
                                </strong>

                                <span>
                                    Date fixée automatiquement
                                    à J+5 lors de la création.
                                    Elle reste inchangée pendant
                                    la modification.
                                </span>
                            </div>
                        </div>
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
document.addEventListener('DOMContentLoaded', function () {
    const subject =
        document.getElementById('profDevoirEditSubject');

    const level =
        document.getElementById('profDevoirEditLevel');

    const classroom =
        document.getElementById('profDevoirEditClass');

    const course =
        document.getElementById('profEditCourse');

    if (!subject || !level || !classroom || !course) {
        return;
    }

    function refreshCourses() {
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

        const selected =
            course.options[course.selectedIndex];

        if (
            selected
            && selected.value
            && selected.disabled
        ) {
            course.value = '';
        }
    }

    [subject, level, classroom].forEach(element => {
        element.addEventListener(
            'change',
            () => setTimeout(refreshCourses, 0)
        );
    });

    setTimeout(refreshCourses, 0);
});
</script>
@endpush
