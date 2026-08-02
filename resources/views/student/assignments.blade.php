@extends('layouts.student')

@section('title', 'Mes devoirs')
@section('page_title', 'Mes devoirs')
@section('breadcrumb', 'Devoirs')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/student-pages-v6.css') }}"
    >
@endpush

@section('content')
@php
    $submittedCount = $assignments->count();

    $gradedCount = $assignments
        ->whereNotNull('grade')
        ->count();

    $pendingCorrectionCount =
        $submittedCount - $gradedCount;
@endphp

<div class="sp-page sp-assignments-page">

    <section class="sp-hero sp-hero-assignment">
        <div class="sp-hero-icon">
            <i class="bi bi-file-earmark-check-fill"></i>
        </div>

        <div class="sp-hero-copy">
            <span class="sp-kicker">
                Travaux et évaluations
            </span>

            <h2>Mes devoirs</h2>

            <p>
                Consultez les devoirs demandés, envoyez votre
                travail et suivez les corrections.
            </p>
        </div>

        <a
            href="#sendAssignmentPanel"
            class="sp-primary-button"
        >
            <i class="bi bi-cloud-arrow-up-fill"></i>
            Envoyer un devoir
        </a>
    </section>

    <section class="sp-metrics sp-metrics-four">
        <article class="sp-metric-card">
            <span class="sp-metric-icon violet">
                <i class="bi bi-journal-bookmark-fill"></i>
            </span>

            <div>
                <small>Demandés</small>
                <strong>{{ $profAssignments->count() }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon blue">
                <i class="bi bi-send-check-fill"></i>
            </span>

            <div>
                <small>Envoyés</small>
                <strong>{{ $submittedCount }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon green">
                <i class="bi bi-patch-check-fill"></i>
            </span>

            <div>
                <small>Corrigés</small>
                <strong>{{ $gradedCount }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon amber">
                <i class="bi bi-hourglass-split"></i>
            </span>

            <div>
                <small>À corriger</small>
                <strong>{{ $pendingCorrectionCount }}</strong>
            </div>
        </article>
    </section>

    @if($profAssignments->isNotEmpty())
        <section>
            <header class="sp-list-header">
                <div>
                    <span class="sp-kicker">
                        Travail demandé
                    </span>

                    <h3>Devoirs du professeur</h3>
                </div>

                <span class="sp-soft-badge">
                    {{ $profAssignments->count() }}
                    devoir{{
                        $profAssignments->count() > 1
                            ? 's'
                            : ''
                    }}
                </span>
            </header>

            <div class="sp-prof-assignment-grid">
                @foreach($profAssignments as $assignment)
                    @php
                        $dueDate = $assignment->due_date
                            ? \Carbon\Carbon::parse(
                                $assignment->due_date
                            )
                            : null;

                        $isOverdue =
                            $dueDate
                            && now()->gt($dueDate)
                            && !$assignment->student_submitted;

                        $statusClass = match (
                            $assignment->student_grade_status
                        ) {
                            'acqui' => 'success',
                            'en_cours' => 'warning',
                            default => 'danger',
                        };

                        $statusLabel = match (
                            $assignment->student_grade_status
                        ) {
                            'acqui' => 'Acquis',
                            'en_cours' => 'En cours',
                            default => 'Non acquis',
                        };

                        $statusIcon = match (
                            $assignment->student_grade_status
                        ) {
                            'acqui' => 'check-circle-fill',
                            'en_cours' => 'hourglass-split',
                            default => 'x-circle-fill',
                        };
                    @endphp

                    <article class="sp-prof-assignment-card">
                        <header>
                            <span class="sp-assignment-card-icon">
                                <i class="bi bi-journal-text"></i>
                            </span>

                            <span
                                class="sp-status-badge {{
                                    $statusClass
                                }}"
                            >
                                <i class="bi bi-{{ $statusIcon }}"></i>
                                {{ $statusLabel }}
                            </span>
                        </header>

                        <div class="sp-prof-assignment-body">
                            <h4>{{ $assignment->title }}</h4>

                            @if($assignment->description)
                                <p>
                                    {{
                                        \Illuminate\Support\Str::limit(
                                            $assignment->description,
                                            115
                                        )
                                    }}
                                </p>
                            @else
                                <p>
                                    Consultez le fichier fourni par le
                                    professeur avant d’envoyer votre travail.
                                </p>
                            @endif

                            <div class="sp-assignment-meta">
                                <span>
                                    <i class="bi bi-person-fill"></i>
                                    {{
                                        $assignment->user?->name
                                        ?? 'Professeur'
                                    }}
                                </span>

                                <span class="{{ $isOverdue ? 'late' : '' }}">
                                    <i class="bi bi-calendar-event-fill"></i>

                                    @if($dueDate)
                                        À rendre le
                                        {{ $dueDate->format('d/m/Y') }}
                                    @else
                                        Sans date limite
                                    @endif
                                </span>
                            </div>

                            @if($assignment->student_grade !== null)
                                <div class="sp-grade-box">
                                    <span>Note obtenue</span>
                                    <strong>
                                        {{
                                            $assignment->student_grade
                                        }}/20
                                    </strong>
                                </div>
                            @endif
                        </div>

                        <footer>
                            @if($assignment->file)
                                <a
                                    href="{{
                                        asset(
                                            'storage/'
                                            . $assignment->file
                                        )
                                    }}"
                                    target="_blank"
                                    class="sp-secondary-button"
                                >
                                    <i class="bi bi-download"></i>
                                    Télécharger
                                </a>
                            @else
                                <span class="sp-disabled-button compact">
                                    <i class="bi bi-file-earmark-x"></i>
                                    Aucun fichier
                                </span>
                            @endif

                            @if($assignment->student_submitted)
                                <span class="sp-status-action success">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Soumis
                                </span>
                            @elseif($assignment->is_locked)
                                <span class="sp-status-action danger">
                                    <i class="bi bi-lock-fill"></i>
                                    {{
                                        !$assignment->has_file
                                            ? 'Non disponible'
                                            : 'Verrouillé'
                                    }}
                                </span>
                            @else
                                <a
                                    href="#sendAssignmentPanel"
                                    class="sp-primary-button compact"
                                >
                                    <i class="bi bi-send-fill"></i>
                                    Soumettre
                                </a>
                            @endif
                        </footer>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section
        class="sp-upload-card"
        id="sendAssignmentPanel"
    >
        <header class="sp-section-header">
            <div>
                <span class="sp-section-icon violet">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                </span>

                <div>
                    <h3>Envoyer un devoir</h3>

                    <p>
                        Ajoutez un titre, puis choisissez
                        Matière → Niveau → Classe avant
                        d’importer votre fichier.
                    </p>
                </div>
            </div>

            <span class="sp-status-badge blue">
                PDF, DOC ou DOCX · 10 Mo
            </span>
        </header>

        <form
            method="POST"
            action="{{ route('student.assignments.send') }}"
            enctype="multipart/form-data"
            class="sp-upload-form"
        >
            @csrf

            @php
                $defaultSubjectId = old('subject_id');

                if (
                    !$defaultSubjectId
                    && $subjects->count() === 1
                ) {
                    $defaultSubjectId =
                        $subjects->first()->id;
                }
            @endphp

            @if($assignmentPaths->isEmpty())
                <div class="sp-assignment-form-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <span>
                        Aucun parcours Matière → Niveau → Classe
                        n’est assigné à votre compte.
                    </span>
                </div>
            @endif

            <div class="sp-upload-title-field">
                <div class="sp-field">
                    <label for="assignmentTitle">
                        Titre du devoir
                    </label>

                    <div class="sp-input-wrap">
                        <i class="bi bi-journal-text"></i>

                        <input
                            type="text"
                            name="title"
                            id="assignmentTitle"
                            value="{{ old('title') }}"
                            placeholder="Exemple : Exercice d’algèbre"
                            required
                        >
                    </div>

                    @error('title')
                        <small class="sp-field-error">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>

            <div class="sp-assignment-path-fields">
                <div class="sp-field">
                    <label for="assignmentSubject">
                        Matière
                    </label>

                    <div class="sp-select-wrap">
                        <i class="bi bi-journal-bookmark-fill"></i>

                        <select
                            name="subject_id"
                            id="assignmentSubject"
                            required
                            {{
                                $assignmentPaths->isEmpty()
                                    ? 'disabled'
                                    : ''
                            }}
                        >
                            <option value="">
                                Choisir une matière
                            </option>

                            @foreach($subjects as $subject)
                                <option
                                    value="{{ $subject->id }}"
                                    {{
                                        (string) $defaultSubjectId
                                        === (string) $subject->id
                                            ? 'selected'
                                            : ''
                                    }}
                                >
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @error('subject_id')
                        <small class="sp-field-error">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="assignmentLevel">
                        Niveau
                    </label>

                    <div class="sp-select-wrap">
                        <i class="bi bi-layers-fill"></i>

                        <select
                            name="level_id"
                            id="assignmentLevel"
                            required
                            disabled
                        >
                            <option value="">
                                Choisissez d’abord une matière
                            </option>
                        </select>
                    </div>

                    @error('level_id')
                        <small class="sp-field-error">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="assignmentClass">
                        Classe
                    </label>

                    <div
                        class="sp-select-wrap"
                        id="assignmentClassSelectWrap"
                    >
                        <i class="bi bi-building-fill"></i>

                        <select
                            name="class_id"
                            id="assignmentClass"
                            required
                            disabled
                        >
                            <option value="">
                                Choisissez d’abord un niveau
                            </option>
                        </select>
                    </div>

                    <div
                        class="sp-readonly-field sp-auto-class-field"
                        id="assignmentClassAuto"
                        hidden
                    >
                        <i class="bi bi-magic"></i>

                        <span id="assignmentClassAutoName">
                            Classe détectée automatiquement
                        </span>

                        <small>Automatique</small>
                    </div>

                    <input
                        type="hidden"
                        name="class_id"
                        id="assignmentClassAutoInput"
                        disabled
                    >

                    @error('class_id')
                        <small class="sp-field-error">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>

            <div
                class="sp-assignment-path-preview"
                id="assignmentPathPreview"
                hidden
            >
                <span>
                    <i class="bi bi-diagram-3-fill"></i>
                    Parcours sélectionné
                </span>

                <strong>
                    <span id="assignmentPathSubject">—</span>
                    <i class="bi bi-chevron-right"></i>
                    <span id="assignmentPathLevel">—</span>
                    <i class="bi bi-chevron-right"></i>
                    <span id="assignmentPathClass">—</span>
                </strong>
            </div>

            <label
                class="sp-drop-zone"
                id="assignmentDropZone"
                for="assignmentFile"
            >
                <span class="sp-drop-zone-icon">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                </span>

                <strong>
                    Glissez votre fichier ici ou cliquez
                    pour parcourir
                </strong>

                <small id="assignmentFileName">
                    PDF, DOC ou DOCX — maximum 10 Mo
                </small>

                <input
                    type="file"
                    name="file"
                    id="assignmentFile"
                    accept=".pdf,.doc,.docx"
                    required
                >
            </label>

            @error('file')
                <small class="sp-field-error">
                    {{ $message }}
                </small>
            @enderror

            <button
                type="submit"
                class="sp-primary-button sp-submit-button"
                {{
                    $assignmentPaths->isEmpty()
                        ? 'disabled'
                        : ''
                }}
            >
                <i class="bi bi-send-fill"></i>
                Envoyer le devoir
            </button>
        </form>
    </section>

    <section class="sp-table-card">
        <header class="sp-section-header">
            <div>
                <span class="sp-section-icon blue">
                    <i class="bi bi-send-check-fill"></i>
                </span>

                <div>
                    <h3>Mes devoirs envoyés</h3>

                    <p>
                        Retrouvez vos fichiers, notes et commentaires.
                    </p>
                </div>
            </div>

            <span class="sp-soft-badge">
                {{ $submittedCount }}
                envoi{{ $submittedCount > 1 ? 's' : '' }}
            </span>
        </header>

        @if($assignments->isNotEmpty())
            <div class="sp-responsive-table">
                <table>
                    <thead>
                        <tr>
                            <th>Devoir</th>
                            <th>Matière</th>
                            <th>Date d’envoi</th>
                            <th>Fichier</th>
                            <th>Correction</th>
                            <th>Note</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($assignments as $assignment)
                            @php
                                $assignmentSubject =
                                    $assignment->subject
                                    ?? $assignment->course?->subject;
                            @endphp

                            <tr>
                                <td data-label="Devoir">
                                    <div class="sp-table-title">
                                        <span>
                                            <i class="bi bi-file-text-fill"></i>
                                        </span>

                                        <div>
                                            <strong>
                                                {{ $assignment->title }}
                                            </strong>

                                            @if($assignment->course)
                                                <small>
                                                    {{
                                                        $assignment
                                                            ->course
                                                            ->title
                                                    }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Matière">
                                    {{
                                        $assignmentSubject?->name
                                        ?? 'Non définie'
                                    }}
                                </td>

                                <td data-label="Date d’envoi">
                                    <strong class="sp-date-main">
                                        {{
                                            $assignment
                                                ->created_at
                                                ->format('d/m/Y')
                                        }}
                                    </strong>

                                    <small class="sp-date-sub">
                                        {{
                                            $assignment
                                                ->created_at
                                                ->format('H:i')
                                        }}
                                    </small>
                                </td>

                                <td data-label="Fichier">
                                    <a
                                        href="{{
                                            asset(
                                                'storage/'
                                                . $assignment->file
                                            )
                                        }}"
                                        target="_blank"
                                        class="sp-secondary-button compact"
                                    >
                                        <i class="bi bi-eye-fill"></i>
                                        Voir
                                    </a>
                                </td>

                                <td data-label="Correction">
                                    @if($assignment->grade !== null)
                                        <span class="sp-status-badge success">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Corrigé
                                        </span>
                                    @else
                                        <span class="sp-status-badge warning">
                                            <i class="bi bi-hourglass-split"></i>
                                            En attente
                                        </span>
                                    @endif
                                </td>

                                <td data-label="Note">
                                    @if($assignment->grade !== null)
                                        <div class="sp-table-grade">
                                            <strong>
                                                {{ $assignment->grade }}/20
                                            </strong>

                                            @if($assignment->comment)
                                                <small
                                                    title="{{
                                                        $assignment->comment
                                                    }}"
                                                >
                                                    <i class="bi bi-chat-dots-fill"></i>
                                                    {{
                                                        \Illuminate\Support\Str::limit(
                                                            $assignment->comment,
                                                            22
                                                        )
                                                    }}
                                                </small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="sp-muted-value">
                                            —
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="sp-empty-state compact">
                <span class="sp-empty-icon">
                    <i class="bi bi-inbox-fill"></i>
                </span>

                <h3>Aucun devoir envoyé</h3>

                <p>
                    Votre premier envoi apparaîtra ici.
                </p>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
window.studentAssignmentPathData = {
    levelsBySubject: @json($levelsBySubject),
    classesBySubjectLevel: @json($classesBySubjectLevel),
    selectedSubjectId: @json(
        (string) $defaultSubjectId
    ),
    selectedLevelId: @json(
        (string) old('level_id')
    ),
    selectedClassId: @json(
        (string) old('class_id')
    )
};
</script>

<script src="{{ asset('js/student-assignments-path-v9-1.js?v=9.1') }}"></script>
@endpush
