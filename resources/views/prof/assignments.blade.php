@extends('layouts.prof')

@section('title', 'Copies des étudiants')
@section('page_title', 'Copies des étudiants')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

@section('content')
@php
    $totalSubmissions = $assignments->count();
    $pendingSubmissions =
        $assignments->whereNull('grade')->count();
    $correctedSubmissions =
        $totalSubmissions - $pendingSubmissions;
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-journal-check"></i>
            Évaluation
        </span>

        <h1 class="pp-page-title">
            Copies des étudiants
        </h1>

        <p class="pp-page-description">
            Les copies sont limitées aux étudiants
            de vos créneaux pédagogiques exacts.
        </p>
    </div>
</section>

@include(
    'prof.partials.path-filter',
    [
        'action' => route('prof.assignments'),
        'buttonLabel' => 'Afficher les copies',
    ]
)

<div class="pp-summary-grid">
    <article class="pp-summary-card is-blue">
        <span class="pp-summary-icon">
            <i class="bi bi-inbox-fill"></i>
        </span>

        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $totalSubmissions }}
            </strong>
            <span class="pp-summary-label">
                Soumissions
            </span>
        </span>
    </article>

    <article class="pp-summary-card is-yellow">
        <span class="pp-summary-icon">
            <i class="bi bi-hourglass-split"></i>
        </span>

        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $pendingSubmissions }}
            </strong>
            <span class="pp-summary-label">
                À corriger
            </span>
        </span>
    </article>

    <article class="pp-summary-card is-green">
        <span class="pp-summary-icon">
            <i class="bi bi-check2-circle"></i>
        </span>

        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $correctedSubmissions }}
            </strong>
            <span class="pp-summary-label">
                Corrigées
            </span>
        </span>
    </article>
</div>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-list-check"></i>
                Soumissions
            </h2>
        </div>
    </header>

    <div class="pp-panel-body">
        @forelse($assignments as $assignment)
            @php
                $studentName =
                    $assignment->user?->name
                    ?? 'Étudiant';

                $statusClass =
                    match ($assignment->grade) {
                        20 => 'adm-badge-success',
                        10 => 'adm-badge-warning',
                        0 => 'adm-badge-danger',
                        default => 'adm-badge-gray',
                    };

                $statusLabel =
                    match ($assignment->grade) {
                        20 => 'Acquis',
                        10 => 'En cours',
                        0 => 'Non acquis',
                        default => 'À corriger',
                    };
            @endphp

            <article class="pp-submission-card mb-3">
                <div class="pp-submission-summary">
                    <div class="pp-student-main">
                        <span class="pp-student-avatar">
                            {{
                                mb_strtoupper(
                                    mb_substr(
                                        $studentName,
                                        0,
                                        1
                                    )
                                )
                            }}
                        </span>

                        <div class="pp-student-copy">
                            <strong class="pp-student-name">
                                {{ $studentName }}
                            </strong>

                            <span class="pp-student-assignment">
                                {{ $assignment->title }}
                            </span>

                            <div class="pps-path-line mt-2">
                                <span class="pps-path-chip">
                                    {{
                                        $assignment
                                            ->classSlot
                                            ?->subject
                                            ?->name
                                        ?? 'Matière'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-path-chip">
                                    {{
                                        $assignment
                                            ->classSlot
                                            ?->level
                                            ?->name
                                        ?? 'Niveau'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-path-chip">
                                    {{
                                        $assignment
                                            ->classSlot
                                            ?->classRoom
                                            ?->name
                                        ?? 'Classe'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-slot-badge">
                                    {{
                                        $assignment
                                            ->classSlot
                                            ?->code
                                        ?? '—'
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pp-submission-actions">
                        <span class="adm-badge {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>

                        @if($assignment->file)
                            <a
                                href="{{
                                    asset(
                                        'storage/'
                                        . $assignment->file
                                    )
                                }}"
                                target="_blank"
                                rel="noopener"
                                class="adm-btn adm-btn-primary adm-btn-sm"
                            >
                                Voir la copie
                            </a>
                        @endif
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('prof.grade') }}"
                    class="pp-correction"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="id"
                        value="{{ $assignment->id }}"
                    >

                    <div class="pp-correction-grid">
                        <div>
                            <div class="pp-status-options">
                                <label class="pp-status-option">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="acquis"
                                        required
                                        {{
                                            $assignment->grade === 20
                                                ? 'checked'
                                                : ''
                                        }}
                                    >
                                    <span>Acquis</span>
                                </label>

                                <label class="pp-status-option">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="en_cours"
                                        {{
                                            $assignment->grade === 10
                                                ? 'checked'
                                                : ''
                                        }}
                                    >
                                    <span>En cours</span>
                                </label>

                                <label class="pp-status-option">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="non_acquis"
                                        {{
                                            $assignment->grade === 0
                                                ? 'checked'
                                                : ''
                                        }}
                                    >
                                    <span>Non acquis</span>
                                </label>
                            </div>

                            <textarea
                                name="comment"
                                rows="2"
                                class="adm-form-control"
                                maxlength="2000"
                                placeholder="Commentaire..."
                            >{{ $assignment->comment }}</textarea>
                        </div>

                        <button
                            type="submit"
                            class="adm-btn adm-btn-success"
                        >
                            Enregistrer
                        </button>
                    </div>
                </form>
            </article>
        @empty
            <div class="pps-empty">
                Aucune copie dans le parcours sélectionné.
            </div>
        @endforelse
    </div>
</section>
@endsection
