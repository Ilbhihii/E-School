@extends('layouts.admin')

@section('title', 'Bloc-notes élèves')
@section('page_title', 'Bloc-notes élèves')
@section(
    'breadcrumb',
    'Pédagogie → Observations des professeurs'
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
@php
    $initials = function ($name) {
        return collect(
            preg_split(
                '/\s+/u',
                trim($name ?? '')
            )
        )
            ->filter()
            ->take(2)
            ->map(
                function ($part) {
                    return mb_strtoupper(
                        mb_substr($part, 0, 1)
                    );
                }
            )
            ->implode('');
    };
@endphp

<div class="admin-behavior-notes">
    <section class="abn-hero">
        <div class="abn-hero-main">
            <span class="abn-hero-icon">
                <i class="bi bi-journal-check"></i>
            </span>

            <div>
                <span class="abn-kicker">
                    Suivi pédagogique
                </span>

                <h1>
                    Bloc-notes des élèves
                </h1>

                <p>
                    Toutes les observations positives et
                    négatives ajoutées par les professeurs.
                </p>
            </div>
        </div>

        <span class="abn-readonly">
            <i class="bi bi-eye-fill"></i>
            Consultation administration
        </span>
    </section>

    <section class="abn-stats">
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

        <article class="abn-stat">
            <small>
                Élèves / Professeurs
            </small>
            <strong>
                {{ $summary['students_count'] }}
                /
                {{ $summary['professors_count'] }}
            </strong>
        </article>
    </section>

    <section class="abn-panel">
        <div class="abn-panel-head">
            <div>
                <h2>
                    <i class="bi bi-funnel me-1"></i>
                    Filtres
                </h2>

                <p>
                    Retrouvez une observation par professeur,
                    élève ou parcours pédagogique.
                </p>
            </div>
        </div>

        <form
            method="GET"
            action="{{
                route(
                    'admin.behavior-notes.index'
                )
            }}"
        >
            <div class="abn-filter">
                <div class="abn-field">
                    <label>Recherche</label>
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="
                            Élève, professeur ou observation...
                        "
                    >
                </div>

                <div class="abn-field">
                    <label>Professeur</label>
                    <select name="professor_id">
                        <option value="">
                            Tous les professeurs
                        </option>

                        @foreach($professors as $professor)
                            <option
                                value="{{ $professor->id }}"
                                @if(
                                    (string)
                                    request('professor_id')
                                    ===
                                    (string) $professor->id
                                )
                                    selected
                                @endif
                            >
                                {{ $professor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="abn-field">
                    <label>Élève</label>
                    <select name="student_id">
                        <option value="">
                            Tous les élèves
                        </option>

                        @foreach($students as $student)
                            <option
                                value="{{ $student->id }}"
                                @if(
                                    (string)
                                    request('student_id')
                                    ===
                                    (string) $student->id
                                )
                                    selected
                                @endif
                            >
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="abn-field">
                    <label>Matière</label>
                    <select name="subject_id">
                        <option value="">
                            Toutes les matières
                        </option>

                        @foreach($subjects as $subject)
                            <option
                                value="{{ $subject->id }}"
                                @if(
                                    (string)
                                    request('subject_id')
                                    ===
                                    (string) $subject->id
                                )
                                    selected
                                @endif
                            >
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="abn-field">
                    <label>Type</label>
                    <select name="type">
                        <option value="">
                            Positif et négatif
                        </option>
                        <option
                            value="positive"
                            @if(
                                request('type')
                                === 'positive'
                            )
                                selected
                            @endif
                        >
                            Point positif
                        </option>
                        <option
                            value="negative"
                            @if(
                                request('type')
                                === 'negative'
                            )
                                selected
                            @endif
                        >
                            Point négatif
                        </option>
                    </select>
                </div>
            </div>

            <div class="abn-filter-row-2">
                <div class="abn-field">
                    <label>Niveau</label>
                    <select name="level_id">
                        <option value="">
                            Tous les niveaux
                        </option>

                        @foreach($levels as $level)
                            <option
                                value="{{ $level->id }}"
                                @if(
                                    (string)
                                    request('level_id')
                                    ===
                                    (string) $level->id
                                )
                                    selected
                                @endif
                            >
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="abn-field">
                    <label>Classe</label>
                    <select name="class_room_id">
                        <option value="">
                            Toutes les classes
                        </option>

                        @foreach($classes as $class)
                            <option
                                value="{{ $class->id }}"
                                @if(
                                    (string)
                                    request(
                                        'class_room_id'
                                    )
                                    ===
                                    (string) $class->id
                                )
                                    selected
                                @endif
                            >
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="abn-field">
                    <label>Du</label>
                    <input
                        type="date"
                        name="date_from"
                        value="{{
                            request('date_from')
                        }}"
                    >
                </div>

                <div class="abn-field">
                    <label>Au</label>
                    <input
                        type="date"
                        name="date_to"
                        value="{{
                            request('date_to')
                        }}"
                    >
                </div>

                <button
                    type="submit"
                    class="abn-btn"
                >
                    <i class="bi bi-search"></i>
                    Filtrer
                </button>

                <a
                    href="{{
                        route(
                            'admin.behavior-notes.index'
                        )
                    }}"
                    class="abn-btn secondary"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </section>

    <section class="abn-panel">
        <div class="abn-panel-head">
            <div>
                <h2>
                    <i class="bi bi-clock-history me-1"></i>
                    Observations des professeurs
                </h2>

                <p>
                    Les observations les plus récentes
                    apparaissent en premier.
                </p>
            </div>
        </div>

        @if($notes->count())
            <div class="abn-table-wrap">
                <table class="abn-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Élève</th>
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
                                    <strong>
                                        {{
                                            $note->noted_at
                                                ->format(
                                                    'd/m/Y'
                                                )
                                        }}
                                    </strong>

                                    <div
                                        style="
                                            margin-top:.12rem;
                                            color:#66758a;
                                            font-size:.47rem;
                                        "
                                    >
                                        Saisi
                                        {{
                                            $note
                                                ->created_at
                                                ->format(
                                                    'd/m · H:i'
                                                )
                                        }}
                                    </div>
                                </td>

                                <td>
                                    <a
                                        href="{{
                                            route(
                                                'admin.behavior-notes.student',
                                                $note->student_id
                                            )
                                        }}"
                                        class="abn-student-link"
                                    >
                                        <div class="abn-person">
                                            <span class="abn-avatar">
                                                {{
                                                    $initials(
                                                        optional(
                                                            $note->student
                                                        )->name
                                                    ) ?: 'E'
                                                }}
                                            </span>

                                            <span>
                                                <strong>
                                                    {{
                                                        optional(
                                                            $note->student
                                                        )->name
                                                        ?? 'Élève supprimé'
                                                    }}
                                                </strong>

                                                <small>
                                                    Voir l'historique
                                                </small>
                                            </span>
                                        </div>
                                    </a>
                                </td>

                                <td>
                                    <div class="abn-person">
                                        <span class="abn-avatar">
                                            {{
                                                $initials(
                                                    optional(
                                                        $note->professor
                                                    )->name
                                                ) ?: 'P'
                                            }}
                                        </span>

                                        <span>
                                            <strong>
                                                {{
                                                    optional(
                                                        $note->professor
                                                    )->name
                                                    ?? 'Professeur supprimé'
                                                }}
                                            </strong>

                                            <small>Enseignant</small>
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <div class="abn-path">
                                        <strong>
                                            {{
                                                optional(
                                                    $note->subject
                                                )->name
                                                ?? '—'
                                            }}
                                        </strong>
                                        <br>
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
                                        <i
                                            class="
                                                bi
                                                {{
                                                    $positive
                                                        ? 'bi-plus-circle-fill'
                                                        : 'bi-dash-circle-fill'
                                                }}
                                            "
                                        ></i>

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
                Aucune observation ne correspond
                aux filtres sélectionnés.
            </div>
        @endif
    </section>
</div>
@endsection
