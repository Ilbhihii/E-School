@extends('layouts.prof')

@section('title', 'Mes propositions de cours')
@section('page_title', 'Cours')
@section('breadcrumb', 'Proposer → Validation admin → Publication')

@section('content')
@php
    $statusMeta = [
        'pending' => [
            'label' => 'En attente',
            'class' => 'adm-badge-warning',
            'icon' => 'bi-hourglass-split',
        ],
        'approved' => [
            'label' => 'Publié',
            'class' => 'adm-badge-success',
            'icon' => 'bi-check-circle-fill',
        ],
        'rejected' => [
            'label' => 'Refusé',
            'class' => 'adm-badge-danger',
            'icon' => 'bi-x-circle-fill',
        ],
    ];
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-cloud-arrow-up-fill"></i>
            Validation pédagogique
        </span>

        <h1 class="pp-page-title">
            Mes propositions de cours
        </h1>

        <p class="pp-page-description">
            Créez votre cours puis envoyez-le à l’administration.
            Il sera visible aux étudiants uniquement après validation.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{ route('prof.courses.create') }}"
            class="adm-btn adm-btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Proposer un cours
        </a>
    </div>
</section>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="adm-alert adm-alert-danger mb-4">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="pp-summary-grid">
    <article class="pp-summary-card is-blue">
        <span class="pp-summary-icon">
            <i class="bi bi-collection-fill"></i>
        </span>
        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $stats['all'] }}
            </strong>
            <span class="pp-summary-label">
                Total
            </span>
        </span>
    </article>

    <article class="pp-summary-card is-yellow">
        <span class="pp-summary-icon">
            <i class="bi bi-hourglass-split"></i>
        </span>
        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $stats['pending'] }}
            </strong>
            <span class="pp-summary-label">
                En attente
            </span>
        </span>
    </article>

    <article class="pp-summary-card is-green">
        <span class="pp-summary-icon">
            <i class="bi bi-check-circle-fill"></i>
        </span>
        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $stats['approved'] }}
            </strong>
            <span class="pp-summary-label">
                Publiés
            </span>
        </span>
    </article>

    <article class="pp-summary-card is-red">
        <span class="pp-summary-icon">
            <i class="bi bi-x-circle-fill"></i>
        </span>
        <span class="pp-summary-copy">
            <strong class="pp-summary-value">
                {{ $stats['rejected'] }}
            </strong>
            <span class="pp-summary-label">
                Refusés
            </span>
        </span>
    </article>
</div>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-filter-circle-fill"></i>
                Mes cours
            </h2>
        </div>

        <div class="pps-inline-actions">
            <a
                href="{{ route('prof.courses.index') }}"
                class="adm-btn adm-btn-sm {{
                    !$status ? 'adm-btn-primary' : 'adm-btn-ghost'
                }}"
            >
                Tous
            </a>

            <a
                href="{{
                    route(
                        'prof.courses.index',
                        ['status' => 'pending']
                    )
                }}"
                class="adm-btn adm-btn-sm {{
                    $status === 'pending'
                        ? 'adm-btn-warning'
                        : 'adm-btn-ghost'
                }}"
            >
                En attente
            </a>

            <a
                href="{{
                    route(
                        'prof.courses.index',
                        ['status' => 'approved']
                    )
                }}"
                class="adm-btn adm-btn-sm {{
                    $status === 'approved'
                        ? 'adm-btn-success'
                        : 'adm-btn-ghost'
                }}"
            >
                Publiés
            </a>

            <a
                href="{{
                    route(
                        'prof.courses.index',
                        ['status' => 'rejected']
                    )
                }}"
                class="adm-btn adm-btn-sm {{
                    $status === 'rejected'
                        ? 'adm-btn-danger'
                        : 'adm-btn-ghost'
                }}"
            >
                Refusés
            </a>
        </div>
    </header>

    <div class="pp-panel-body">
        <div class="row g-3">
            @forelse($courses as $course)
                @php
                    $meta =
                        $statusMeta[
                            $course->approval_status
                        ]
                        ?? $statusMeta['pending'];
                @endphp

                <div class="col-xl-6">
                    <article class="prof-path-card h-100">
                        <div class="prof-path-body">
                            <div
                                class="d-flex justify-content-between
                                    align-items-start gap-3 mb-3"
                            >
                                <div>
                                    <span class="prof-path-kicker">
                                        <i class="bi bi-book-half"></i>
                                        Proposition de cours
                                    </span>

                                    <h2 class="mt-2 mb-0">
                                        {{ $course->title }}
                                    </h2>
                                </div>

                                <span
                                    class="adm-badge {{
                                        $meta['class']
                                    }}"
                                >
                                    <i
                                        class="bi {{
                                            $meta['icon']
                                        }}"
                                    ></i>
                                    {{ $meta['label'] }}
                                </span>
                            </div>

                            <div class="pps-path-line mb-3">
                                <span class="pps-path-chip">
                                    {{
                                        $course->subject?->name
                                        ?? 'Matière'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-path-chip">
                                    {{
                                        $course->level?->name
                                        ?? 'Niveau'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-path-chip">
                                    {{
                                        $course->classRoom?->name
                                        ?? 'Classe'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-slot-badge">
                                    {{ $course->slot_code ?? '—' }}
                                </span>
                            </div>

                            @if(
                                $course->isRejected()
                                && $course->rejection_reason
                            )
                                <div
                                    class="adm-alert adm-alert-danger mb-3"
                                    style="font-size:.75rem;"
                                >
                                    <strong>Motif du refus :</strong>
                                    {{ $course->rejection_reason }}
                                </div>
                            @elseif($course->isPending())
                                <div
                                    class="adm-alert adm-alert-warning mb-3"
                                    style="font-size:.75rem;"
                                >
                                    L’administration doit valider ce cours
                                    avant sa publication.
                                </div>
                            @else
                                <div
                                    class="adm-alert adm-alert-success mb-3"
                                    style="font-size:.75rem;"
                                >
                                    Ce cours est publié et visible
                                    aux étudiants concernés.
                                </div>
                            @endif

                            <div class="pps-inline-actions">
                                <a
                                    href="{{
                                        route(
                                            'prof.courses.show',
                                            $course
                                        )
                                    }}"
                                    class="adm-btn adm-btn-ghost adm-btn-sm"
                                >
                                    <i class="bi bi-eye"></i>
                                    Voir
                                </a>

                                <a
                                    href="{{
                                        route(
                                            'prof.courses.edit',
                                            $course
                                        )
                                    }}"
                                    class="adm-btn adm-btn-warning adm-btn-sm"
                                >
                                    <i class="bi bi-pencil"></i>
                                    {{
                                        $course->isRejected()
                                            ? 'Corriger et renvoyer'
                                            : 'Modifier'
                                    }}
                                </a>

                                @if(!$course->isApproved())
                                    <form
                                        method="POST"
                                        action="{{
                                            route(
                                                'prof.courses.destroy',
                                                $course
                                            )
                                        }}"
                                        onsubmit="
                                            return confirm(
                                                'Supprimer cette proposition ?'
                                            )
                                        "
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="adm-btn adm-btn-danger adm-btn-sm"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="pps-empty">
                        Aucun cours dans cette catégorie.
                    </div>
                </div>
            @endforelse
        </div>

        @if($courses->hasPages())
            <div class="pp-pagination mt-3">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
