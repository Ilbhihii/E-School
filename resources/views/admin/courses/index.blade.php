@extends('layouts.admin')

@section('title', 'Gestion des cours')
@section('page_title', 'Cours')
@section(
    'breadcrumb',
    'Cours → Validation professeur → Publication'
)

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

<div class="adm-page-header">
    <div>
        <h1>Cours</h1>

        <div class="subtitle">
            Créez vos cours directement ou validez
            les propositions envoyées par les professeurs.
        </div>
    </div>

    <div class="page-actions">
        <a
            href="{{ route('admin.courses.create') }}"
            class="adm-btn adm-btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Nouveau cours
        </a>
    </div>
</div>

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

<div class="adm-stats-grid">
    <div class="adm-stat blue">
        <div class="stat-top">
            <div class="stat-icon">
                <i class="bi bi-book-fill"></i>
            </div>
        </div>

        <div class="stat-value">
            {{ $courseStats['all'] }}
        </div>

        <div class="stat-label">
            Tous les cours
        </div>
    </div>

    <div class="adm-stat orange">
        <div class="stat-top">
            <div class="stat-icon">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <div class="stat-value">
            {{ $courseStats['pending'] }}
        </div>

        <div class="stat-label">
            En attente
        </div>
    </div>

    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>

        <div class="stat-value">
            {{ $courseStats['approved'] }}
        </div>

        <div class="stat-label">
            Publiés
        </div>
    </div>

    <div class="adm-stat red">
        <div class="stat-top">
            <div class="stat-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>
        </div>

        <div class="stat-value">
            {{ $courseStats['rejected'] }}
        </div>

        <div class="stat-label">
            Refusés
        </div>
    </div>
</div>

<div class="adm-card mb-4">
    <div class="adm-card-header">
        <h4>
            <i
                class="bi bi-funnel-fill"
                style="color:rgba(255,255,255,.35);"
            ></i>
            Filtrer
        </h4>
    </div>

    <div class="adm-card-body">
        <div
            style="
                display:flex;
                flex-wrap:wrap;
                gap:8px;
            "
        >
            <a
                href="{{ route('admin.courses.index') }}"
                class="adm-btn adm-btn-sm {{
                    !$status
                        ? 'adm-btn-primary'
                        : 'adm-btn-ghost'
                }}"
            >
                Tous
            </a>

            <a
                href="{{
                    route(
                        'admin.courses.index',
                        ['status' => 'pending']
                    )
                }}"
                class="adm-btn adm-btn-sm {{
                    $status === 'pending'
                        ? 'adm-btn-warning'
                        : 'adm-btn-ghost'
                }}"
            >
                <i class="bi bi-hourglass-split"></i>
                En attente
            </a>

            <a
                href="{{
                    route(
                        'admin.courses.index',
                        ['status' => 'approved']
                    )
                }}"
                class="adm-btn adm-btn-sm {{
                    $status === 'approved'
                        ? 'adm-btn-success'
                        : 'adm-btn-ghost'
                }}"
            >
                <i class="bi bi-check-circle-fill"></i>
                Publiés
            </a>

            <a
                href="{{
                    route(
                        'admin.courses.index',
                        ['status' => 'rejected']
                    )
                }}"
                class="adm-btn adm-btn-sm {{
                    $status === 'rejected'
                        ? 'adm-btn-danger'
                        : 'adm-btn-ghost'
                }}"
            >
                <i class="bi bi-x-circle-fill"></i>
                Refusés
            </a>
        </div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4>
            <i
                class="bi bi-collection"
                style="color:rgba(255,255,255,.35);"
            ></i>
            Liste des cours
        </h4>

        <div class="card-actions">
            <span
                style="
                    color:var(--adm-text-muted);
                    font-size:.8rem;
                "
            >
                {{ $courses->total() }} cours
            </span>
        </div>
    </div>

    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Cours</th>
                        <th>Parcours</th>
                        <th>Origine</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th style="text-align:right;">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($courses as $course)
                        @php
                            $meta =
                                $statusMeta[
                                    $course->approval_status
                                ]
                                ?? $statusMeta['pending'];

                            $creator =
                                $course->creator;

                            $fromProfessor =
                                $creator
                                && $creator->isProf();
                        @endphp

                        <tr>
                            <td>
                                <div style="font-weight:700;">
                                    {{ $course->title }}
                                </div>

                                @if($course->description)
                                    <small
                                        style="
                                            display:block;
                                            margin-top:4px;
                                            color:var(--adm-text-muted);
                                            max-width:280px;
                                        "
                                    >
                                        {{
                                            \Illuminate\Support\Str::limit(
                                                $course->description,
                                                80
                                            )
                                        }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                <div
                                    style="
                                        display:flex;
                                        flex-wrap:wrap;
                                        align-items:center;
                                        gap:5px;
                                        min-width:260px;
                                    "
                                >
                                    <span class="adm-badge adm-badge-primary">
                                        {{
                                            $course->subject?->name
                                            ?? 'Matière'
                                        }}
                                    </span>

                                    <i class="bi bi-chevron-right"></i>

                                    <span class="adm-badge adm-badge-info">
                                        {{
                                            $course->level?->name
                                            ?? 'Niveau'
                                        }}
                                    </span>

                                    <i class="bi bi-chevron-right"></i>

                                    <span class="adm-badge">
                                        {{
                                            $course->classRoom?->name
                                            ?? 'Classe'
                                        }}
                                    </span>

                                    <i class="bi bi-chevron-right"></i>

                                    <span class="adm-badge adm-badge-warning">
                                        {{ $course->slot_code ?? '—' }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                @if($fromProfessor)
                                    <span class="adm-badge adm-badge-accent">
                                        <i class="bi bi-person-video3"></i>
                                        Professeur
                                    </span>

                                    <small
                                        style="
                                            display:block;
                                            margin-top:5px;
                                            color:var(--adm-text-muted);
                                        "
                                    >
                                        {{ $creator->name }}
                                    </small>
                                @else
                                    <span class="adm-badge adm-badge-primary">
                                        <i class="bi bi-shield-check"></i>
                                        Administration
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="adm-badge {{ $meta['class'] }}">
                                    <i class="bi {{ $meta['icon'] }}"></i>
                                    {{ $meta['label'] }}
                                </span>

                                @if(
                                    $course->isRejected()
                                    && $course->rejection_reason
                                )
                                    <small
                                        style="
                                            display:block;
                                            margin-top:5px;
                                            color:#FDA4AF;
                                            max-width:220px;
                                        "
                                    >
                                        {{
                                            \Illuminate\Support\Str::limit(
                                                $course->rejection_reason,
                                                75
                                            )
                                        }}
                                    </small>
                                @endif
                            </td>

                            <td
                                style="
                                    color:var(--adm-text-muted);
                                    font-size:.8rem;
                                "
                            >
                                {{
                                    $course->created_at
                                        ->format('d/m/Y H:i')
                                }}
                            </td>

                            <td style="text-align:right;">
                                <div
                                    style="
                                        display:flex;
                                        flex-wrap:wrap;
                                        gap:6px;
                                        justify-content:flex-end;
                                    "
                                >
                                    <a
                                        href="{{
                                            route(
                                                'admin.courses.show',
                                                $course
                                            )
                                        }}"
                                        class="adm-btn adm-btn-sm"
                                        style="
                                            background:rgba(6,182,212,.15);
                                            color:#67E8F9;
                                            border:1px solid rgba(6,182,212,.15);
                                        "
                                        title="Voir"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a
                                        href="{{
                                            route(
                                                'admin.courses.edit',
                                                $course
                                            )
                                        }}"
                                        class="adm-btn adm-btn-warning adm-btn-sm"
                                        title="Modifier"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    @if($fromProfessor && !$course->isApproved())
                                        <form
                                            method="POST"
                                            action="{{
                                                route(
                                                    'admin.courses.approve',
                                                    $course
                                                )
                                            }}"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="adm-btn adm-btn-success adm-btn-sm"
                                                title="Accepter et publier"
                                            >
                                                <i class="bi bi-check-lg"></i>
                                                Accepter
                                            </button>
                                        </form>
                                    @endif

                                    @if($fromProfessor && !$course->isRejected())
                                        <details
                                            style="
                                                position:relative;
                                            "
                                        >
                                            <summary
                                                class="adm-btn adm-btn-danger adm-btn-sm"
                                                style="
                                                    list-style:none;
                                                    cursor:pointer;
                                                "
                                            >
                                                <i class="bi bi-x-lg"></i>
                                                Refuser
                                            </summary>

                                            <div
                                                style="
                                                    position:absolute;
                                                    right:0;
                                                    top:38px;
                                                    z-index:40;
                                                    width:310px;
                                                    padding:12px;
                                                    border:1px solid rgba(239,68,68,.2);
                                                    border-radius:12px;
                                                    background:#111827;
                                                    box-shadow:0 16px 40px rgba(0,0,0,.35);
                                                    text-align:left;
                                                "
                                            >
                                                <form
                                                    method="POST"
                                                    action="{{
                                                        route(
                                                            'admin.courses.reject',
                                                            $course
                                                        )
                                                    }}"
                                                >
                                                    @csrf

                                                    <label
                                                        class="adm-form-label"
                                                    >
                                                        Motif du refus
                                                    </label>

                                                    <textarea
                                                        name="rejection_reason"
                                                        rows="3"
                                                        class="adm-form-control"
                                                        placeholder="Ex. Le son de la vidéo doit être amélioré..."
                                                        required
                                                    ></textarea>

                                                    <button
                                                        type="submit"
                                                        class="adm-btn adm-btn-danger adm-btn-sm mt-2 w-100"
                                                    >
                                                        Confirmer le refus
                                                    </button>
                                                </form>
                                            </div>
                                        </details>
                                    @endif

                                    @if($course->isApproved())
                                        <a
                                            href="{{
                                                route(
                                                    'prof.devoir.create',
                                                    [
                                                        'course_id'
                                                            => $course->id,
                                                    ]
                                                )
                                            }}"
                                            class="adm-btn adm-btn-accent adm-btn-sm"
                                            title="Créer un devoir"
                                        >
                                            <i class="bi bi-file-text"></i>
                                        </a>
                                    @endif

                                    <form
                                        method="POST"
                                        action="{{
                                            route(
                                                'admin.courses.destroy',
                                                $course
                                            )
                                        }}"
                                        onsubmit="
                                            return confirm(
                                                'Supprimer ce cours ?'
                                            )
                                        "
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="adm-btn adm-btn-danger adm-btn-sm"
                                            type="submit"
                                            title="Supprimer"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="adm-empty">
                                    <div class="adm-empty-icon">
                                        <i class="bi bi-inbox"></i>
                                    </div>

                                    <h5>Aucun cours</h5>

                                    <p>
                                        Aucun cours ne correspond
                                        au filtre sélectionné.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($courses->hasPages())
        <div class="adm-card-footer">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection
