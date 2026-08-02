@extends('layouts.prof')

@section('title', 'Créer un devoir')
@section('page_title', 'Nouveau devoir')
@section('breadcrumb', 'Création d’un devoir')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-file-earmark-plus-fill"></i> Nouvelle activité</span>
        <h1 class="pp-page-title">Créer un devoir</h1>
        <p class="pp-page-description">
            Renseignez les consignes, sélectionnez la classe concernée et définissez une date limite de remise.
        </p>
    </div>

    <div class="pp-page-actions">
        <a href="{{ route('prof.devoir.index', ['course_id' => $course_id ?? null]) }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left"></i>
            Retour aux devoirs
        </a>
    </div>
</section>

@if($errors->any())
    <div class="adm-alert adm-alert-danger">
        <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
        <div>
            <strong>Le formulaire contient des erreurs.</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if($course)
    <div class="pp-course-context">
        <span class="pp-course-context-icon"><i class="bi bi-book-fill"></i></span>
        <span>
            <strong>{{ $course->title }}</strong>
            <span>{{ $course->classRoom?->name ?? 'Classe' }} · {{ $course->subject?->name ?? 'Matière' }}</span>
        </span>
    </div>
@endif

<form method="POST" action="{{ route('prof.devoir.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="pp-form-grid">
        <section class="pp-panel">
            <header class="pp-panel-head">
                <div class="pp-panel-title-wrap">
                    <h2 class="pp-panel-title"><i class="bi bi-card-text"></i> Contenu du devoir</h2>
                    <p class="pp-panel-subtitle">Donnez un titre clair et des consignes compréhensibles.</p>
                </div>
            </header>

            <div class="pp-form-section">
                <div class="pp-field">
                    <label for="title" class="pp-label">
                        <i class="bi bi-type"></i> Titre du devoir <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        class="adm-form-control @error('title') is-invalid @enderror"
                        placeholder="Ex. Exercices du chapitre 3"
                        maxlength="255"
                        required
                        autofocus
                    >
                    @error('title') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>

                <div class="pp-field">
                    <label for="description" class="pp-label">
                        <i class="bi bi-text-paragraph"></i> Description et consignes
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="9"
                        class="adm-form-control @error('description') is-invalid @enderror"
                        placeholder="Précisez les exercices à réaliser, les objectifs et les consignes de remise..."
                    >{{ old('description') }}</textarea>
                    @error('description') <span class="adm-form-error">{{ $message }}</span> @enderror
                    <p class="pp-help">Une consigne précise aide les étudiants à rendre un travail conforme.</p>
                </div>
            </div>
        </section>

        <aside class="pp-panel">
            <header class="pp-panel-head">
                <div class="pp-panel-title-wrap">
                    <h2 class="pp-panel-title"><i class="bi bi-sliders2"></i> Affectation</h2>
                    <p class="pp-panel-subtitle">Classe, cours, échéance et document.</p>
                </div>
            </header>

            <div class="pp-form-section">
                <div class="pp-field">
                    <label for="class_room_id" class="pp-label">
                        <i class="bi bi-people-fill"></i> Classe <span class="required">*</span>
                    </label>
                    <select id="class_room_id" name="class_room_id" class="adm-form-select @error('class_room_id') is-invalid @enderror" required>
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ (string) old('class_room_id') === (string) $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_room_id') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>

                @if(isset($courses) && $courses->isNotEmpty())
                    <div class="pp-field">
                        <label for="course_id" class="pp-label"><i class="bi bi-book"></i> Cours associé</label>
                        <select id="course_id" name="course_id" class="adm-form-select @error('course_id') is-invalid @enderror">
                            <option value="">Aucun cours spécifique</option>
                            @foreach($courses as $courseOption)
                                <option value="{{ $courseOption->id }}" {{ (string) old('course_id', $course_id ?? '') === (string) $courseOption->id ? 'selected' : '' }}>
                                    {{ $courseOption->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id') <span class="adm-form-error">{{ $message }}</span> @enderror
                    </div>
                @elseif(!empty($course_id))
                    <input type="hidden" name="course_id" value="{{ $course_id }}">
                @endif

                <div class="pp-field">
                    <label for="due_date" class="pp-label">
                        <i class="bi bi-calendar-check"></i> Date limite <span class="required">*</span>
                    </label>
                    <input
                        type="date"
                        id="due_date"
                        name="due_date"
                        value="{{ old('due_date') }}"
                        min="{{ now()->addDay()->toDateString() }}"
                        class="adm-form-control @error('due_date') is-invalid @enderror"
                        required
                    >
                    @error('due_date') <span class="adm-form-error">{{ $message }}</span> @enderror
                    <p class="pp-help">La date doit être postérieure à aujourd’hui.</p>
                </div>

                <div class="pp-field">
                    <label for="file" class="pp-label"><i class="bi bi-paperclip"></i> Document PDF</label>
                    <div class="pp-upload">
                        <span class="pp-upload-icon"><i class="bi bi-cloud-arrow-up-fill"></i></span>
                        <input
                            type="file"
                            id="file"
                            name="file"
                            accept="application/pdf,.pdf"
                            class="adm-form-control @error('file') is-invalid @enderror"
                        >
                        <p class="pp-help">PDF uniquement · taille maximale 5 Mo.</p>
                    </div>
                    @error('file') <span class="adm-form-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </aside>
    </div>

    <div class="pp-panel pp-section-gap">
        <div class="pp-form-actions">
            <a href="{{ route('prof.devoir.index', ['course_id' => $course_id ?? null]) }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-x-lg"></i> Annuler
            </a>
            <button type="submit" class="adm-btn adm-btn-success">
                <i class="bi bi-check-circle-fill"></i> Publier le devoir
            </button>
        </div>
    </div>
</form>
@endsection
