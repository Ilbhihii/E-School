@extends('layouts.prof')

@section('title', 'Proposer un cours')
@section('page_title', 'Proposer un cours')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Groupe → Validation admin'
)

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-cloud-arrow-up-fill"></i>
            Proposition pédagogique
        </span>

        <h1 class="pp-page-title">
            Proposer un nouveau cours
        </h1>

        <p class="pp-page-description">
            Vous pouvez proposer un cours uniquement pour les classes
            qui vous ont été affectées par l’administration.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{ route('prof.courses.index') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-arrow-left"></i>
            Mes propositions
        </a>
    </div>
</section>

@if($errors->any())
    <div class="adm-alert adm-alert-danger mb-4">
        <strong>Le formulaire contient des erreurs.</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    method="POST"
    action="{{ route('prof.courses.store') }}"
    enctype="multipart/form-data"
>
    @csrf

    @include(
        'components.pedagogical-path-class-edit',
        [
            'hierarchy' => $profHierarchy,
            'prefix' => 'profCourseCreate',
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
                    <i class="bi bi-journal-text"></i>
                    Contenu du cours
                </h2>

                <p class="pp-panel-subtitle">
                    Après l’envoi, le statut sera
                    « En attente ».
                </p>
            </div>
        </header>

        <div class="pp-form-section">
            <div class="pp-field">
                <label
                    for="courseTitle"
                    class="pp-label"
                >
                    Titre du cours *
                </label>

                <input
                    id="courseTitle"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    class="adm-form-control"
                    maxlength="255"
                    required
                >
            </div>

            <div class="pp-field">
                <label
                    for="courseDescription"
                    class="pp-label"
                >
                    Description
                </label>

                <textarea
                    id="courseDescription"
                    name="description"
                    rows="6"
                    class="adm-form-control"
                    placeholder="Objectifs, contenu du cours..."
                >{{ old('description') }}</textarea>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="pp-field">
                        <label
                            for="courseVideo"
                            class="pp-label"
                        >
                            Vidéo
                        </label>

                        <input
                            id="courseVideo"
                            type="file"
                            name="video"
                            accept="video/mp4,video/quicktime,video/x-msvideo,video/webm"
                            class="adm-form-control"
                        >

                        <small class="pp-help">
                            MP4, MOV, AVI, WEBM · 1 Go maximum
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="pp-field">
                        <label
                            for="coursePdf"
                            class="pp-label"
                        >
                            Document PDF
                        </label>

                        <input
                            id="coursePdf"
                            type="file"
                            name="pdf"
                            accept="application/pdf,.pdf"
                            class="adm-form-control"
                        >

                        <small class="pp-help">
                            PDF · 1 Go maximum
                        </small>
                    </div>
                </div>
            </div>

            <div class="pp-field">
                <label class="pp-label">
                    Autres fichiers
                </label>

                <button
                    type="button"
                    class="adm-btn adm-btn-ghost"
                    data-extra-files-target="courseExtraFiles"
                >
                    <i class="bi bi-plus-circle"></i>
                    Ajouter d'autres fichiers (optionnel)
                </button>

                <input
                    type="file"
                    name="attachments[]"
                    id="courseExtraFiles"
                    multiple
                    accept=".pdf,.doc,.docx,.odt,.rtf,.txt,.xls,.xlsx,.csv,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.webp,.heic,.mp3,.wav,.m4a,.aac,.ogg,.mp4,.mov,.m4v,.avi,.webm,.mkv,.zip,.rar,.7z"
                    style="display:none"
                >

                <div
                    data-extra-files-list="courseExtraFiles"
                    style="margin-top:7px"
                ></div>

                <small class="pp-help">
                    Vous pouvez sélectionner 1, 2 ou plusieurs fichiers.
                    10 fichiers maximum · 100 Mo maximum par fichier.
                </small>
            </div>
            <div class="pp-field">
                <label
                    for="courseLink"
                    class="pp-label"
                >
                    Lien externe
                </label>

                <input
                    id="courseLink"
                    type="url"
                    name="course_link"
                    value="{{ old('course_link') }}"
                    class="adm-form-control"
                    placeholder="https://..."
                >
            </div>

            <div class="adm-alert adm-alert-info mb-0">
                <i class="bi bi-info-circle-fill"></i>
                Le cours sera envoyé directement à
                <strong>/admin/courses</strong>.
                Il ne sera pas visible aux étudiants
                avant l’acceptation de l’administration.
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
                Envoyer pour validation
            </button>
        </div>
    </section>
</form>
<script src="{{ asset('js/prof-extra-files-v2-1.js') }}?v=21"></script>
<script src="{{ asset('js/course-upload-1gb-v1.js') }}?v=1"></script>

@endsection
