@extends('layouts.admin')

@section('title', 'Historique bloc-notes')
@section('page_title', 'Historique bloc-notes')
@section(
    'breadcrumb',
    'Pédagogie → Bloc-notes élèves → '
    . $student->name
)

@push('styles')
<link
    rel="stylesheet"
    href="{{
        asset('css/admin-behavior-notes.css')
    }}?v={{
        file_exists(
            public_path(
                'css/admin-behavior-notes.css'
            )
        )
            ? filemtime(
                public_path(
                    'css/admin-behavior-notes.css'
                )
            )
            : 1
    }}"
>
@endpush

@section('content')
<div class="admin-behavior-notes">
    <a
        href="{{
            route(
                'admin.behavior-notes.index'
            )
        }}"
        class="abn-back"
    >
        <i class="bi bi-arrow-left"></i>
        Toutes les observations
    </a>

    <section class="abn-hero">
        <div class="abn-hero-main">
            <span class="abn-hero-icon">
                <i class="bi bi-person-lines-fill"></i>
            </span>

            <div>
                <span class="abn-kicker">
                    Historique individuel
                </span>

                <h1>{{ $student->name }}</h1>

                <p>
                    Toutes les observations enregistrées
                    par les professeurs pour cet élève.
                </p>
            </div>
        </div>

        <span class="abn-readonly">
            <i class="bi bi-shield-check"></i>
            Administration
        </span>
    </section>

    <section
        class="abn-stats"
        style="
            grid-template-columns:
                repeat(4,minmax(0,1fr));
        "
    >
        <article class="abn-stat">
            <small>Observations</small>
            <strong>
                {{ $summary['notes_count'] }}
            </strong>
        </article>

        <article class="abn-stat positive">
            <small>Points positifs</small>
            <strong>
                +{{ $summary['positive_points'] }}
            </strong>
        </article>

        <article class="abn-stat negative">
            <small>Points négatifs</small>
            <strong>
                -{{ $summary['negative_points'] }}
            </strong>
        </article>

        <article class="abn-stat balance">
            <small>Solde</small>
            <strong>
                {{
                    $summary['balance'] > 0
                        ? '+'
                        : ''
                }}{{ $summary['balance'] }}
            </strong>
        </article>
    </section>

    <section class="abn-panel">
        <div class="abn-panel-head">
            <div>
                <h2>
                    <i class="bi bi-clock-history me-1"></i>
                    Historique
                </h2>

                <p>
                    Les observations ne sont pas modifiables
                    depuis l'espace administration.
                </p>
            </div>
        </div>

        @if($notes->count())
            <div class="abn-table-wrap">
                <table class="abn-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Professeur</th>
                            <th>Parcours</th>
                            <th>Type</th>
                            <th>Points</th>
                            <th>Observation</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($notes as $note)
                            @php
                                $positive =
                                    $note->type
                                    ===
                                    \App\Models\StudentBehaviorNote::TYPE_POSITIVE;
                            @endphp

                            <tr>
                                <td>
                                    {{
                                        $note
                                            ->noted_at
                                            ->format('d/m/Y')
                                    }}
                                </td>

                                <td>
                                    <strong>
                                        {{
                                            optional(
                                                $note->professor
                                            )->name
                                            ?? 'Professeur supprimé'
                                        }}
                                    </strong>
                                </td>

                                <td>
                                    <div class="abn-path">
                                        {{
                                            optional(
                                                $note->subject
                                            )->name
                                            ?? '—'
                                        }}
                                        →
                                        {{
                                            optional(
                                                $note->level
                                            )->name
                                            ?? '—'
                                        }}
                                        →
                                        {{
                                            optional(
                                                $note->classRoom
                                            )->name
                                            ?? '—'
                                        }}
                                    </div>
                                </td>

                                <td>
                                    <span
                                        class="
                                            abn-type
                                            {{
                                                $positive
                                                    ? 'positive'
                                                    : 'negative'
                                            }}
                                        "
                                    >
                                        {{
                                            $positive
                                                ? 'Positif'
                                                : 'Négatif'
                                        }}
                                    </span>
                                </td>

                                <td>
                                    <span
                                        class="
                                            abn-points
                                            {{
                                                $positive
                                                    ? 'positive'
                                                    : 'negative'
                                            }}
                                        "
                                    >
                                        {{
                                            $positive
                                                ? '+'
                                                : '-'
                                        }}{{ $note->points }}
                                    </span>
                                </td>

                                <td>
                                    <div class="abn-note">
                                        {{ $note->note }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="abn-footer">
                {{ $notes->links() }}
            </div>
        @else
            <div class="abn-empty">
                <i class="bi bi-journal-x"></i>
                Aucune observation pour cet élève.
            </div>
        @endif
    </section>
</div>
@endsection
