@extends('layouts.prof')

@section('title', 'Bloc-notes — ' . $student->name)
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

    $indexUrl = route(
        'prof.behavior-notes.index',
        [
            'subject_id' => $assignment->subject_id,
            'level_id' => $assignment->level_id,
            'class_id' => $assignment->class_id,
        ]
    );
@endphp

<div class="behavior-page">
    @if(session('success'))
        <div class="adm-alert adm-alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="adm-alert adm-alert-danger mb-4">
            <strong>Veuillez corriger :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a
        href="{{ $indexUrl }}"
        class="adm-btn adm-btn-ghost mb-3"
    >
        <i class="bi bi-arrow-left"></i>
        Retour aux étudiants
    </a>

    <section class="bn-profile-head">
        <div class="bn-profile-main">
            <span class="bn-avatar">
                {{ $initials ?: 'E' }}
            </span>

            <div>
                <h2>{{ $student->name }}</h2>

                <div class="bn-profile-path">
                    <span class="bn-path-chip">
                        {{ $assignment->subject?->name ?? 'Matière' }}
                    </span>
                    <i class="bi bi-chevron-right"></i>
                    <span class="bn-path-chip">
                        {{ $assignment->level?->name ?? 'Niveau' }}
                    </span>
                    <i class="bi bi-chevron-right"></i>
                    <span class="bn-path-chip">
                        {{ $assignment->classRoom?->name ?? 'Classe' }}
                    </span>
                </div>
            </div>
        </div>

        <span class="pp-panel-meta">
            <i class="bi bi-lock-fill"></i>
            Note privée professeur
        </span>
    </section>

    <div class="bn-summary">
        <article class="bn-summary-card is-positive">
            <span class="bn-summary-icon">
                <i class="bi bi-plus-circle-fill"></i>
            </span>
            <span class="bn-summary-copy">
                <small>Points positifs</small>
                <strong>+{{ $positivePoints }}</strong>
            </span>
        </article>

        <article class="bn-summary-card is-negative">
            <span class="bn-summary-icon">
                <i class="bi bi-dash-circle-fill"></i>
            </span>
            <span class="bn-summary-copy">
                <small>Points négatifs</small>
                <strong>-{{ $negativePoints }}</strong>
            </span>
        </article>

        <article class="bn-summary-card">
            <span class="bn-summary-icon">
                <i class="bi bi-calculator-fill"></i>
            </span>
            <span class="bn-summary-copy">
                <small>Solde</small>
                <strong>
                    {{
                        $balancePoints > 0
                            ? '+' . $balancePoints
                            : $balancePoints
                    }}
                </strong>
            </span>
        </article>

        <article class="bn-summary-card">
            <span class="bn-summary-icon">
                <i class="bi bi-journal-text"></i>
            </span>
            <span class="bn-summary-copy">
                <small>Observations</small>
                <strong>{{ $notesCount }}</strong>
            </span>
        </article>
    </div>

    <section class="pp-panel mb-4">
        <header class="pp-panel-head">
            <div class="pp-panel-title-wrap">
                <h2 class="pp-panel-title">
                    <i class="bi bi-plus-square-fill"></i>
                    Ajouter une observation
                </h2>

                <p class="pp-panel-subtitle">
                    Les points vont de 1 à 5.
                    Le signe est calculé automatiquement.
                </p>
            </div>
        </header>

        <div class="pp-panel-body">
            <form
                method="POST"
                action="{{
                    route(
                        'prof.behavior-notes.store',
                        [
                            'student' => $student->id,
                            'subject_id' => $assignment->subject_id,
                            'level_id' => $assignment->level_id,
                            'class_id' => $assignment->class_id,
                        ]
                    )
                }}"
            >
                @csrf

                <input
                    type="hidden"
                    name="subject_id"
                    value="{{ $assignment->subject_id }}"
                >
                <input
                    type="hidden"
                    name="level_id"
                    value="{{ $assignment->level_id }}"
                >
                <input
                    type="hidden"
                    name="class_id"
                    value="{{ $assignment->class_id }}"
                >

                <div class="bn-form-grid">
                    <div class="pp-field">
                        <label class="pp-label">
                            Type
                        </label>

                        <div class="bn-type-choice">
                            <label class="bn-type-option positive">
                                <input
                                    type="radio"
                                    name="type"
                                    value="positive"
                                    {{ old('type', 'positive') === 'positive' ? 'checked' : '' }}
                                >
                                <span>
                                    <i class="bi bi-plus-circle-fill"></i>
                                    Positif
                                </span>
                            </label>

                            <label class="bn-type-option negative">
                                <input
                                    type="radio"
                                    name="type"
                                    value="negative"
                                    {{ old('type') === 'negative' ? 'checked' : '' }}
                                >
                                <span>
                                    <i class="bi bi-dash-circle-fill"></i>
                                    Négatif
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="pp-field">
                        <label
                            for="behaviorPoints"
                            class="pp-label"
                        >
                            Points
                        </label>

                        <select
                            name="points"
                            id="behaviorPoints"
                            class="adm-form-select"
                            required
                        >
                            @for($point = 1; $point <= 5; $point++)
                                <option
                                    value="{{ $point }}"
                                    {{ (int) old('points', 1) === $point ? 'selected' : '' }}
                                >
                                    {{ $point }} point{{ $point > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="pp-field">
                        <label
                            for="behaviorDate"
                            class="pp-label"
                        >
                            Date
                        </label>

                        <input
                            type="date"
                            name="noted_at"
                            id="behaviorDate"
                            value="{{ old('noted_at', now()->toDateString()) }}"
                            class="adm-form-control"
                            required
                        >
                    </div>
                </div>

                <div class="pp-field mt-3">
                    <label
                        for="behaviorNote"
                        class="pp-label"
                    >
                        Observation
                    </label>

                    <textarea
                        name="note"
                        id="behaviorNote"
                        rows="4"
                        maxlength="2000"
                        class="adm-form-control"
                        placeholder="Ex. Très bonne participation en cours..."
                        required
                    >{{ old('note') }}</textarea>
                </div>

                <div class="pp-form-actions mt-3">
                    <button
                        type="submit"
                        class="adm-btn adm-btn-primary"
                    >
                        <i class="bi bi-check2-circle"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="pp-panel">
        <header class="pp-panel-head">
            <div class="pp-panel-title-wrap">
                <h2 class="pp-panel-title">
                    <i class="bi bi-clock-history"></i>
                    Historique
                </h2>

                <p class="pp-panel-subtitle">
                    Les observations les plus récentes
                    apparaissent en premier.
                </p>
            </div>

            <span class="pp-panel-meta">
                {{ $notes->total() }} observation(s)
            </span>
        </header>

        <div class="pp-panel-body">
            @forelse($notes as $behaviorNote)
                <article
                    class="bn-note-row {{
                        $behaviorNote->is_positive
                            ? 'is-positive'
                            : 'is-negative'
                    }}"
                >
                    <div class="bn-note-head">
                        <div class="bn-note-meta">
                            <span
                                class="bn-note-badge {{
                                    $behaviorNote->is_positive
                                        ? 'is-positive'
                                        : 'is-negative'
                                }}"
                            >
                                <i
                                    class="bi {{
                                        $behaviorNote->is_positive
                                            ? 'bi-plus-circle-fill'
                                            : 'bi-dash-circle-fill'
                                    }}"
                                ></i>

                                {{
                                    $behaviorNote->is_positive
                                        ? 'Point positif'
                                        : 'Point négatif'
                                }}
                            </span>

                            <span class="bn-note-points">
                                {{
                                    $behaviorNote->signed_points > 0
                                        ? '+' . $behaviorNote->signed_points
                                        : $behaviorNote->signed_points
                                }}
                            </span>

                            <span class="bn-note-date">
                                <i class="bi bi-calendar3"></i>
                                {{ $behaviorNote->noted_at->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="bn-note-actions">
                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'prof.behavior-notes.destroy',
                                        [
                                            'student' => $student->id,
                                            'behaviorNote' => $behaviorNote->id,
                                        ]
                                    )
                                }}"
                                onsubmit="return confirm('Supprimer cette observation ?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    title="Supprimer"
                                >
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="bn-note-text">{{ $behaviorNote->note }}</p>

                    <details class="bn-edit-box">
                        <summary class="adm-btn adm-btn-ghost">
                            <i class="bi bi-pencil"></i>
                            Modifier cette observation
                        </summary>

                        <form
                            method="POST"
                            action="{{
                                route(
                                    'prof.behavior-notes.update',
                                    [
                                        'student' => $student->id,
                                        'behaviorNote' => $behaviorNote->id,
                                    ]
                                )
                            }}"
                            class="bn-edit-grid mt-3"
                        >
                            @csrf
                            @method('PATCH')

                            <div class="pp-field">
                                <label class="pp-label">Type</label>
                                <select
                                    name="type"
                                    class="adm-form-select"
                                    required
                                >
                                    <option
                                        value="positive"
                                        {{ $behaviorNote->type === 'positive' ? 'selected' : '' }}
                                    >
                                        Positif
                                    </option>
                                    <option
                                        value="negative"
                                        {{ $behaviorNote->type === 'negative' ? 'selected' : '' }}
                                    >
                                        Négatif
                                    </option>
                                </select>
                            </div>

                            <div class="pp-field">
                                <label class="pp-label">Points</label>
                                <select
                                    name="points"
                                    class="adm-form-select"
                                    required
                                >
                                    @for($point = 1; $point <= 5; $point++)
                                        <option
                                            value="{{ $point }}"
                                            {{ (int) $behaviorNote->points === $point ? 'selected' : '' }}
                                        >
                                            {{ $point }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="pp-field">
                                <label class="pp-label">Date</label>
                                <input
                                    type="date"
                                    name="noted_at"
                                    value="{{ $behaviorNote->noted_at->format('Y-m-d') }}"
                                    class="adm-form-control"
                                    required
                                >
                            </div>

                            <div class="pp-field">
                                <label class="pp-label">Observation</label>
                                <input
                                    type="text"
                                    name="note"
                                    value="{{ $behaviorNote->note }}"
                                    maxlength="2000"
                                    class="adm-form-control"
                                    required
                                >
                            </div>

                            <button
                                type="submit"
                                class="adm-btn adm-btn-primary"
                            >
                                <i class="bi bi-check2"></i>
                                Enregistrer
                            </button>
                        </form>
                    </details>
                </article>
            @empty
                <div class="bn-empty">
                    <i class="bi bi-journal"></i>
                    Aucune observation pour cet étudiant.
                </div>
            @endforelse

            @if($notes->hasPages())
                <div class="pp-pagination mt-3">
                    {{ $notes->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
