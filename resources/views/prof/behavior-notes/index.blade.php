@extends('layouts.prof')

@section('title', 'Bloc-notes pédagogique')
@section('page_title', 'Bloc-notes pédagogique')
@section('breadcrumb', 'Matière → Niveau → Classe → Étudiant')

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/prof-behavior-notes.css') }}?v={{
        file_exists(public_path('css/prof-behavior-notes.css'))
            ? filemtime(public_path('css/prof-behavior-notes.css'))
            : 1
    }}"
>
@endpush

@section('content')
<div class="behavior-page">
    <section class="pp-page-head">
        <div class="pp-page-copy">
            <span class="pp-eyebrow">
                <i class="bi bi-journal-plus"></i>
                Suivi pédagogique
            </span>

            <h1 class="pp-page-title">
                Bloc-notes des étudiants
            </h1>

            <p class="pp-page-description">
                Ajoutez des points positifs ou négatifs
                en suivant le parcours
                Matière → Niveau → Classe.
            </p>
        </div>
    </section>

    @if(session('success'))
        <div class="adm-alert adm-alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @include(
        'prof.partials.path-filter',
        [
            'action' => route('prof.behavior-notes.index'),
            'buttonLabel' => 'Filtrer',
            'autoSubmit' => true,
        ]
    )

    @if($selectedAssignment)
        <div class="bn-selection-note">
            <i class="bi bi-lightning-charge-fill"></i>
            <span>
                Les étudiants sont affichés automatiquement.
                Utilisez Matière → Niveau → Classe uniquement
                pour filtrer un autre parcours.
            </span>
        </div>
    @endif

    <section class="pp-panel">
        <header class="pp-panel-head">
            <div class="pp-panel-title-wrap">
                <h2 class="pp-panel-title">
                    <i class="bi bi-people-fill"></i>
                    Étudiants
                </h2>

                <p class="pp-panel-subtitle">
                    Chaque observation reste rattachée
                    au professeur et au parcours sélectionné.
                </p>
            </div>

            @if($selectedAssignment)
                <span class="pp-panel-meta">
                    {{ $students->count() }} étudiant(s)
                </span>
            @endif
        </header>

        <div class="pp-panel-body">
            @if(!$selectedAssignment)
                <div class="bn-empty">
                    <i class="bi bi-diagram-3"></i>
                    Aucun parcours pédagogique ne vous est affecté.
                </div>
            @elseif($students->isEmpty())
                <div class="bn-empty">
                    <i class="bi bi-person-x"></i>
                    Aucun étudiant n'est affecté à ce parcours.
                </div>
            @else
                <div class="bn-student-grid">
                    @foreach($students as $student)
                        @php
                            $initials = collect(
                                preg_split('/\s+/u', trim($student->name))
                            )
                                ->filter()
                                ->take(2)
                                ->map(
                                    fn ($part) =>
                                        mb_strtoupper(
                                            mb_substr($part, 0, 1)
                                        )
                                )
                                ->implode('');
                        @endphp

                        <article class="bn-student-card">
                            <div class="bn-student-head">
                                <span class="bn-avatar">
                                    {{ $initials ?: 'E' }}
                                </span>

                                <span class="bn-student-copy">
                                    <strong>{{ $student->name }}</strong>
                                    <small>{{ $student->email }}</small>
                                </span>
                            </div>

                            <div class="bn-card-stats">
                                <div class="bn-mini-stat is-positive">
                                    <small>Positifs</small>
                                    <strong>
                                        +{{ $student->positive_points }}
                                    </strong>
                                </div>

                                <div class="bn-mini-stat is-negative">
                                    <small>Négatifs</small>
                                    <strong>
                                        -{{ $student->negative_points }}
                                    </strong>
                                </div>

                                <div class="bn-mini-stat is-balance">
                                    <small>Solde</small>
                                    <strong>
                                        {{
                                            $student->balance_points > 0
                                                ? '+' . $student->balance_points
                                                : $student->balance_points
                                        }}
                                    </strong>
                                </div>
                            </div>

                            <div class="bn-card-footer">
                                <span class="bn-last-note">
                                    @if($student->last_noted_at)
                                        Dernière note :
                                        {{
                                            \Carbon\Carbon::parse(
                                                $student->last_noted_at
                                            )->format('d/m/Y')
                                        }}
                                    @else
                                        Aucune observation
                                    @endif
                                </span>

                                <a
                                    href="{{
                                        route(
                                            'prof.behavior-notes.show',
                                            [
                                                'student' => $student->id,
                                                'subject_id' => $selectedSubjectId,
                                                'level_id' => $selectedLevelId,
                                                'class_id' => $selectedClassId,
                                            ]
                                        )
                                    }}"
                                    class="adm-btn adm-btn-primary"
                                >
                                    <i class="bi bi-journal-text"></i>
                                    Ouvrir
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
