@extends('layouts.admin')

@section('title', 'Modifier le cours')
@section('page_title', 'Modifier cours')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau → Modifier')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4>
                    <i
                        class="bi bi-pencil"
                        style="color:rgba(255,255,255,0.35);"
                    ></i>
                    Modifier : {{ $course->title }}
                </h4>
            </div>

            <div class="adm-card-body">
                @if($errors->any())
                    <div
                        class="adm-card mb-4"
                        style="
                            background:rgba(220,38,38,0.10);
                            border-color:rgba(248,113,113,0.35);
                        "
                    >
                        <div class="adm-card-body" style="padding:1rem;">
                            <strong style="color:#FCA5A5;">
                                La modification n’a pas été enregistrée.
                            </strong>

                            <ul
                                style="
                                    margin:0.65rem 0 0;
                                    padding-left:1.25rem;
                                    color:#FECACA;
                                "
                            >
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form
                    id="courseUpdateForm"
                    method="POST"
                    action="{{ route('admin.courses.update', $course->id) }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="title">
                            Titre
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title', $course->title) }}"
                            class="adm-form-control @error('title') error @enderror"
                            placeholder="Ex : Les équations du 2ème degré"
                            required
                        >

                        @error('title')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="description">
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="adm-form-control adm-form-textarea @error('description') error @enderror"
                            placeholder="Description du cours..."
                        >{{ old('description', $course->description) }}</textarea>

                        @error('description')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    @include(
                        'components.pedagogical-path-edit',
                        [
                            'hierarchy' => $editHierarchy,
                            'prefix' => 'courseEditPath',
                            'subjectName' => 'subject_id',
                            'levelName' => 'level_id',
                            'className' => 'class_id',
                            'slotName' => 'slot_code',
                            'slotValueKey' => 'code',
                            'slotLabelKey' => 'label',
                            'selectedSubject' =>
                                old(
                                    'subject_id',
                                    $course->subject_id
                                ),
                            'selectedLevel' =>
                                old(
                                    'level_id',
                                    $course->level_id
                                ),
                            'selectedClass' =>
                                old(
                                    'class_id',
                                    $course->class_id
                                ),
                            'selectedSlot' =>
                                old(
                                    'slot_code',
                                    $course->slot_code
                                ),
                        ]
                    )

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="course_link">
                            Lien du cours (optionnel)
                        </label>

                        <input
                            id="course_link"
                            type="url"
                            name="course_link"
                            value="{{ old('course_link', $course->course_link) }}"
                            class="adm-form-control @error('course_link') error @enderror"
                            placeholder="https://..."
                        >

                        @error('course_link')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($course->video || $course->video_url)
                        <div
                            class="adm-card mb-4"
                            style="
                                background:rgba(0,58,143,0.08);
                                border-color:rgba(0,58,143,0.15);
                            "
                        >
                            <div class="adm-card-body" style="padding:1rem;">
                                <label class="adm-form-label">
                                    Vidéo actuelle
                                </label>

                                @if(!empty($resourceUrls['video']))
                                    <video
                                        src="{{ $resourceUrls['video'] }}"
                                        controls
                                        preload="metadata"
                                        style="
                                            display:block;
                                            width:100%;
                                            max-height:420px;
                                            border-radius:8px;
                                            margin-top:8px;
                                            background:#000;
                                        "
                                    ></video>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($course->pdf)
                        <div
                            class="adm-card mb-4"
                            style="
                                background:rgba(22,163,74,0.08);
                                border-color:rgba(22,163,74,0.15);
                            "
                        >
                            <div class="adm-card-body" style="padding:1rem;">
                                <label class="adm-form-label">
                                    PDF actuel
                                </label>

                                @if(!empty($resourceUrls['pdf']))
                                    <div>
                                        <a
                                            href="{{ $resourceUrls['pdf'] }}"
                                            target="_blank"
                                            rel="noopener"
                                            style="color:#4ADE80;"
                                        >
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            Voir le PDF
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label" for="video">
                                    Nouvelle vidéo (remplace l’ancienne)
                                </label>

                                <input
                                    id="video"
                                    type="file"
                                    name="video"
                                    accept="video/mp4,video/quicktime,video/x-msvideo,video/webm,video/x-m4v"
                                    class="adm-form-control @error('video') error @enderror"
                                    style="padding:8px;"
                                >

                                <small
                                    style="
                                        display:block;
                                        margin-top:6px;
                                        color:var(--adm-text-muted);
                                        font-size:0.72rem;
                                    "
                                >
                                    MP4, MOV, AVI, WEBM ou M4V — maximum 1 Go.
                                    Laissez vide pour conserver la vidéo actuelle.
                                </small>

                                @error('video')
                                    <div class="adm-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label" for="pdf">
                                    Nouveau PDF (remplace l’ancien)
                                </label>

                                <input
                                    id="pdf"
                                    type="file"
                                    name="pdf"
                                    accept="application/pdf,.pdf"
                                    class="adm-form-control @error('pdf') error @enderror"
                                    style="padding:8px;"
                                >

                                <small
                                    style="
                                        display:block;
                                        margin-top:6px;
                                        color:var(--adm-text-muted);
                                        font-size:0.72rem;
                                    "
                                >
                                    PDF — maximum 50 Mo. Laissez vide pour
                                    conserver le document actuel.
                                </small>

                                @error('pdf')
                                    <div class="adm-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div
                        id="uploadNotice"
                        class="adm-card mt-4"
                        style="
                            display:none;
                            background:rgba(59,130,246,0.10);
                            border-color:rgba(96,165,250,0.30);
                        "
                    >
                        <div class="adm-card-body" style="padding:1rem;">
                            <strong style="color:#93C5FD;">
                                Envoi en cours…
                            </strong>
                            <div
                                style="
                                    margin-top:5px;
                                    color:var(--adm-text-muted);
                                    font-size:0.8rem;
                                "
                            >
                                Pour une grande vidéo, gardez cette page ouverte
                                jusqu’à la fin de la mise à jour.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <a
                            href="{{ route('admin.courses.index') }}"
                            class="adm-btn adm-btn-ghost flex-fill text-center"
                        >
                            <i class="bi bi-arrow-left"></i>
                            Annuler
                        </a>

                        <button
                            id="submitCourseButton"
                            type="submit"
                            class="adm-btn adm-btn-primary flex-fill"
                        >
                            <i class="bi bi-save"></i>
                            <span>Mettre à jour</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const form =
            document.getElementById(
                'courseUpdateForm'
            );

        const submitButton =
            document.getElementById(
                'submitCourseButton'
            );

        const uploadNotice =
            document.getElementById(
                'uploadNotice'
            );

        if (
            !form
            || !submitButton
        ) {
            return;
        }

        form.addEventListener(
            'submit',
            function () {
                submitButton.disabled = true;

                submitButton.setAttribute(
                    'aria-disabled',
                    'true'
                );

                const label =
                    submitButton.querySelector(
                        'span'
                    );

                if (label) {
                    label.textContent =
                        'Envoi en cours…';
                }

                if (uploadNotice) {
                    uploadNotice.style.display =
                        'block';
                }
            }
        );
    }
);
</script>
@endsection
