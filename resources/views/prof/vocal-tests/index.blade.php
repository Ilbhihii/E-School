@extends('layouts.prof')

@section('title', 'Tests reçus')
@section('page_title', 'Tests reçus')
@section('breadcrumb', 'Évaluations → Tests reçus')

@section('content')
@php
    $total = $submissions->total();

    $visibleSubmitted =
        collect($submissions->items())
            ->where('status', 'submitted')
            ->count();

    $visibleReviewed =
        collect($submissions->items())
            ->whereNotNull('final_score')
            ->count();
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-mic-fill"></i>
            Évaluations affectées
        </span>

        <h1 class="pp-page-title">
            Tests reçus
        </h1>

        <p class="pp-page-description">
            Consultez uniquement les tests que
            l’administration vous a explicitement affectés.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{ route('prof.dashboard') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-grid-1x2-fill"></i>
            Tableau de bord
        </a>
    </div>
</section>

<div class="pp-summary-grid">
    <article class="pp-summary-card is-blue">
        <span class="pp-summary-icon">
            <i class="bi bi-inbox-fill"></i>
        </span>

        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $total }}
            </strong>

            <span class="pp-summary-label">
                Tests affectés
            </span>
        </span>
    </article>

    <article class="pp-summary-card is-yellow">
        <span class="pp-summary-icon">
            <i class="bi bi-hourglass-split"></i>
        </span>

        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $visibleSubmitted }}
            </strong>

            <span class="pp-summary-label">
                Soumis
            </span>
        </span>
    </article>

    <article class="pp-summary-card is-green">
        <span class="pp-summary-icon">
            <i class="bi bi-check2-circle"></i>
        </span>

        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $visibleReviewed }}
            </strong>

            <span class="pp-summary-label">
                Déjà évalués
            </span>
        </span>
    </article>
</div>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-funnel-fill"></i>
                Filtres
            </h2>

            <p class="pp-panel-subtitle">
                Filtrez les tests partagés avec votre compte.
            </p>
        </div>
    </header>

    <div class="pp-panel-body">
        <form
            method="GET"
            action="{{ route('prof.vocal-tests.index') }}"
            class="row g-3 align-items-end"
        >
            <div class="col-lg-4 col-md-6">
                <label class="pp-label">
                    Statut
                </label>

                <select
                    name="status"
                    class="adm-form-select"
                    onchange="this.form.submit()"
                >
                    <option value="all">
                        Tous les statuts
                    </option>

                    @foreach(
                        \App\Models\VocalTestSubmission::getStatuses()
                        as $value => $label
                    )
                        <option
                            value="{{ $value }}"
                            {{ request('status') === $value ? 'selected' : '' }}
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-4 col-md-6">
                <label class="pp-label">
                    Mode
                </label>

                <select
                    name="test_mode"
                    class="adm-form-select"
                    onchange="this.form.submit()"
                >
                    <option value="all">
                        Tous les modes
                    </option>

                    @foreach(
                        \App\Models\VocalTestSubmission::getModes()
                        as $value => $label
                    )
                        <option
                            value="{{ $value }}"
                            {{ request('test_mode') === $value ? 'selected' : '' }}
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-4">
                <a
                    href="{{ route('prof.vocal-tests.index') }}"
                    class="adm-btn adm-btn-ghost w-100"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>
</section>

<section class="pp-panel pp-section-gap">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-person-check-fill"></i>
                Tests partagés avec moi
            </h2>

            <p class="pp-panel-subtitle">
                Vous n’avez accès à aucun autre test.
            </p>
        </div>

        <span class="pp-panel-meta">
            {{ $submissions->total() }} test(s)
        </span>
    </header>

    <div class="pp-panel-body">
        @forelse($submissions as $submission)
            @php
                $score =
                    $submission->final_score
                    ?? $submission->score;

                $studentName =
                    $submission->user?->name
                    ?? 'Nouveau candidat';

                $initial =
                    mb_strtoupper(
                        mb_substr(
                            $studentName,
                            0,
                            1
                        )
                    );

                $typeLabel =
                    $submission->isObservationSubmission()
                        ? 'Observation'
                        : (
                            $submission->isCompletionSubmission()
                                ? 'Complétion'
                                : (
                                    \App\Models\VocalTestSubmission::getModes()[
                                        $submission->test_mode
                                    ]
                                    ?? 'Test vocal'
                                )
                        );

                $statusLabel =
                    \App\Models\VocalTestSubmission::getStatuses()[
                        $submission->status
                    ]
                    ?? $submission->status;
            @endphp

            <article class="pp-submission-card mb-3">
                <div class="pp-submission-summary">
                    <div class="pp-student-main">
                        <span class="pp-student-avatar">
                            {{ $initial }}
                        </span>

                        <div class="pp-student-copy">
                            <strong class="pp-student-name">
                                {{ $studentName }}
                            </strong>

                            <span class="pp-student-assignment">
                                {{ $typeLabel }}
                            </span>

                            <div class="pps-path-line mt-2">
                                <span class="pps-path-chip">
                                    {{
                                        $submission
                                            ->subject
                                            ?->name
                                        ?? 'Matière'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-path-chip">
                                    {{
                                        $submission
                                            ->level
                                            ?->name
                                        ?? 'Niveau'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-path-chip">
                                    {{
                                        $submission
                                            ->classRoom
                                            ?->name
                                        ?? 'Classe'
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pp-submission-actions">
                        <span class="adm-badge adm-badge-primary">
                            {{ $statusLabel }}
                        </span>

                        @if($score !== null)
                            <span class="adm-badge adm-badge-success">
                                {{ $score }}/100
                            </span>
                        @endif

                        <span class="adm-badge">
                            {{
                                $submission
                                    ->submitted_at
                                    ?->format('d/m/Y H:i')
                                ?? $submission
                                    ->created_at
                                    ?->format('d/m/Y H:i')
                            }}
                        </span>

                        <a
                            href="{{
                                route(
                                    'prof.vocal-tests.show',
                                    $submission
                                )
                            }}"
                            class="adm-btn adm-btn-primary adm-btn-sm"
                        >
                            <i class="bi bi-eye-fill"></i>
                            Voir le test
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="pps-empty">
                <i class="bi bi-inbox me-2"></i>

                Aucun test ne vous a encore été affecté.
                Lorsqu’un administrateur vous partagera un test,
                il apparaîtra automatiquement ici.
            </div>
        @endforelse

        @if($submissions->hasPages())
            <div class="mt-4">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
