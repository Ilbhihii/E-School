@extends('layouts.prof')

@section('title', 'Modifier le cours')
@section('page_title', 'Modifier le cours')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Créneau → Renvoyer'
)

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-pencil-square"></i>
            Révision du cours
        </span>

        <h1 class="pp-page-title">
            Modifier « {{ $course->title }} »
        </h1>

        <p class="pp-page-description">
            Toute modification renvoie le cours à l’administration
            pour une nouvelle validation.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{ route('prof.courses.index') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-arrow-left"></i>
            Retour
        </a>
    </div>
</section>

@if($course->isRejected() && $course->rejection_reason)
    <div class="adm-alert adm-alert-danger mb-4">
        <strong>Motif du refus :</strong>
        {{ $course->rejection_reason }}
    </div>
@endif

@if($errors->any())
    <div class="adm-alert adm-alert-danger mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    method="POST"
    action="{{ route('prof.courses.update', $course) }}"
    enctype="multipart/form-data"
>
    @csrf
    @method('PUT')

    @include(
        'components.pedagogical-path-edit',
        [
            'hierarchy' => $profHierarchy,
            'prefix' => 'profCourseEdit',
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
                    <i class="bi bi-journal-text"></i>
                    Contenu
                </h2>
            </div>
        </header>

        <div class="pp-form-section">
            <div class="pp-field">
                <label class="pp-label">
                    Titre *
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{
                        old(
                            'title',
                            $course->title
                        )
                    }}"
                    class="adm-form-control"
                    required
                >
            </div>

            <div class="pp-field">
                <label class="pp-label">
                    Description
                </label>

                <textarea
                    name="description"
                    rows="6"
                    class="adm-form-control"
                >{{ old(
                    'description',
                    $course->description
                ) }}</textarea>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="pp-field">
                        <label class="pp-label">
                            Nouvelle vidéo
                        </label>

                        <input
                            type="file"
                            name="video"
                            class="adm-form-control"
                            accept="video/mp4,video/quicktime,video/x-msvideo,video/webm"
                        >

                        @if($course->video)
                            <small class="pp-help">
                                Une vidéo est déjà enregistrée.
                                Laissez vide pour la conserver.
                            </small>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="pp-field">
                        <label class="pp-label">
                            Nouveau PDF
                        </label>

                        <input
                            type="file"
                            name="pdf"
                            class="adm-form-control"
                            accept="application/pdf,.pdf"
                        >

                        <small class="pp-help">
                            PDF · 1 Go maximum.
                        </small>

                        @if($course->pdf)
                            <small class="pp-help">
                                Un PDF est déjà enregistré.
                                Laissez vide pour le conserver.
                            </small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="pp-field">
                <label class="pp-label">
                    Lien externe
                </label>

                <input
                    type="url"
                    name="course_link"
                    value="{{
                        old(
                            'course_link',
                            $course->course_link
                        )
                    }}"
                    class="adm-form-control"
                    placeholder="https://..."
                >
            </div>
        </div>
    </section>

    <section class="pp-panel pp-section-gap">
        <div class="pp-form-actions">
            <a
                href="{{ route('prof.courses.index') }}"
                class="adm-btn adm-btn-ghost"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="adm-btn adm-btn-primary"
            >
                <i class="bi bi-send-fill"></i>
                Enregistrer et renvoyer
            </button>
        </div>
    </section>
</form>
<script src="{{ asset('js/course-upload-1gb-v1.js') }}?v=1"></script>

@endsection
