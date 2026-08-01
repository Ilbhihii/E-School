@extends('layouts.admin')

@section(
    'title',
    (
        $submission->isObservationSubmission()
            ? 'Observation vocale — '
            : (
                $submission->isCompletionSubmission()
                    ? 'Exercice de complétion — '
                    : 'Soumission vocale — '
            )
    )
    . $submission->user?->name
)
@section('page_title', 'Détail de la soumission')
@section('breadcrumb', 'Tests vocaux → Soumission')

@section('content')

<style>
.audio-player-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.5rem;
    text-align: center;
}
.audio-player-card audio {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    display: block;
}
.audio-file-meta {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    margin-top: 14px;
    color: var(--adm-text-muted);
    font-size: 0.75rem;
}
.audio-file-meta span {
    padding: 4px 9px;
    border-radius: 999px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.07);
}
.audio-error-box {
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid rgba(239,68,68,0.28);
    background: rgba(239,68,68,0.12);
    color: #FCA5A5;
    line-height: 1.6;
}
.audio-browser-error {
    display: none;
    margin-top: 12px;
    color: #FCA5A5;
    font-size: 0.82rem;
}
.audio-browser-error.show {
    display: block;
}
.audio-open-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-top: 14px;
    color: #C4B5FD;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 600;
}
.audio-open-link:hover {
    color: #FFFFFF;
}
.score-input-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
.score-input-group .adm-form-group {
    margin-bottom: 0;
}
.mode-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.mode-badge.reading { background: rgba(99,102,241,0.12); color: #A5B4FC; }
.mode-badge.tajwid { background: rgba(16,185,129,0.12); color: #6EE7B7; }
.mode-badge.hifd { background: rgba(251,191,36,0.12); color: #FCD34D; }
.completion-review-grid {
    display: grid;
    gap: 10px;
}

.completion-review-row {
    display: grid;
    grid-template-columns: 42px 1fr 1fr 90px;
    align-items: center;
    gap: 10px;
    padding: 11px;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    background: rgba(255,255,255,0.025);
}

.completion-review-word {
    direction: rtl;
    font-family: 'Amiri', 'Noto Naskh Arabic', serif;
    font-size: 1.05rem;
}

.completion-review-result {
    text-align: center;
    font-size: 0.78rem;
    font-weight: 700;
}

.completion-review-result.correct {
    color: #4ADE80;
}

.completion-review-result.incorrect {
    color: #FCA5A5;
}

.observation-admin-answer {
    padding: 1rem;
    color: rgba(255,255,255,0.86);
    background: rgba(6,182,212,0.07);
    border: 1px solid rgba(34,211,238,0.18);
    border-radius: 14px;
    font-family: 'Amiri','Noto Naskh Arabic',serif;
    font-size: 1.2rem;
    line-height: 2;
    white-space: pre-wrap;
    direction: rtl;
    text-align: right;
}

.observation-admin-image {
    width: 100%;
    max-height: 620px;
    object-fit: contain;
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.1);
    background: #ffffff;
}

.observation-admin-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
    color: var(--adm-text-muted);
    font-size: 0.75rem;
}

.observation-admin-meta span {
    padding: 5px 9px;
    border-radius: 999px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.07);
}

@media (max-width: 700px) {
    .completion-review-row {
        grid-template-columns: 36px 1fr;
    }

    .completion-review-result {
        text-align: left;
    }
}

</style>

<div class="adm-page-header">
    <div>
        <h1>
            <i
                class="bi {{
                    $submission->isObservationSubmission()
                        ? 'bi-eye-fill'
                        : (
                            $submission->isCompletionSubmission()
                                ? 'bi-puzzle-fill'
                                : 'bi-file-earmark-text'
                        )
                }} me-2"
                style="color:var(--adm-primary);"
            ></i>
            {{
                $submission->isObservationSubmission()
                    ? 'Test d’observation'
                    : (
                        $submission->isCompletionSubmission()
                            ? 'Exercice de complétion'
                            : 'Soumission vocale'
                    )
            }}
        </h1>
        <div class="subtitle">{{ $submission->user?->name }} — {{ $submission->subject?->name }} / {{ $submission->level?->name }} / {{ $submission->classRoom?->name }}</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.vocal-tests.submissions.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="row g-4">
    <!-- Info Élève & Texte -->
    <div class="col-lg-7">
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4><i class="bi bi-person-circle" style="color:rgba(255,255,255,0.35);"></i> Informations</h4>
            </div>
            <div class="adm-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Élève</small>
                        <strong>{{ $submission->user?->name ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Email</small>
                        <strong>{{ $submission->user?->email ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Matière</small>
                        <span class="adm-badge adm-badge-primary">{{ $submission->subject?->name ?? '-' }}</span>
                    </div>
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Mode de test</small>
                        @if($submission->isObservationSubmission())
                            <span class="mode-badge reading">
                                <i class="bi bi-eye-fill"></i>
                                Observation vocale
                            </span>
                        @elseif($submission->test_mode)
                            <span class="mode-badge {{ $submission->test_mode }}">
                                {{ \App\Models\VocalTestSubmission::getModes()[$submission->test_mode] ?? $submission->test_mode }}
                            </span>
                        @else
                            <span style="color:var(--adm-text-muted);">Lecture</span>
                        @endif
                    </div>
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Niveau</small>
                        <strong>{{ $submission->level?->name ?? '-' }}</strong>
                    </div>
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Classe</small>
                        <strong>{{ $submission->classRoom?->name ?? '-' }}</strong>
                    </div>
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Test</small>
                        <strong>{{ $submission->prompt?->title ?? 'Test Coran (original)' }}</strong>
                    </div>
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Durée</small>
                        <strong>{{ $submission->duration_seconds ? $submission->duration_seconds . ' secondes' : 'Non indiquée' }}</strong>
                    </div>
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Soumis le</small>
                        <strong>{{ $submission->submitted_at?->format('d/m/Y H:i') ?? $submission->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Statut</small>
                        <span class="status-badge {{ $submission->status }}">
                            {{ \App\Models\VocalTestSubmission::getStatuses()[$submission->status] ?? $submission->status }}
                        </span>
                    </div>
                    @if(($submission->final_score ?? $submission->score) !== null)
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Note finale</small>
                        @php $displayScore = $submission->final_score ?? $submission->score; @endphp
                        <strong style="color:{{ $displayScore >= 70 ? '#4ADE80' : ($displayScore >= 40 ? '#FCD34D' : '#FCA5A5') }};font-size:1.1rem;">
                            {{ $displayScore }}/100
                        </strong>
                    </div>
                    @endif
                </div>

                @if($submission->score_pronunciation !== null || $submission->score_tajwid !== null || $submission->score_memorization !== null || $submission->score_fluency !== null)
                <hr style="border-color:rgba(255,255,255,0.08);margin:1rem 0;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    @if($submission->score_pronunciation !== null)
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Prononciation</small>
                        <strong>{{ $submission->score_pronunciation }}/100</strong>
                    </div>
                    @endif
                    @if($submission->score_tajwid !== null)
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Tajwid</small>
                        <strong>{{ $submission->score_tajwid }}/100</strong>
                    </div>
                    @endif
                    @if($submission->score_memorization !== null)
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Mémorisation</small>
                        <strong>{{ $submission->score_memorization }}/100</strong>
                    </div>
                    @endif
                    @if($submission->score_fluency !== null)
                    <div>
                        <small style="color:var(--adm-text-muted);display:block;">Fluidité</small>
                        <strong>{{ $submission->score_fluency }}/100</strong>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Consigne du test -->
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4>
                    <i
                        class="bi {{
                            $isObservationSubmission
                                ? 'bi-eye-fill'
                                : 'bi-file-text'
                        }}"
                        style="color:rgba(255,255,255,0.35);"
                    ></i>
                    {{ $isObservationSubmission
                        ? 'Consigne d’observation'
                        : 'Texte à réciter' }}
                </h4>
            </div>
            <div class="adm-card-body">
                <div
                    dir="{{ $isObservationSubmission ? 'ltr' : 'rtl' }}"
                    lang="{{ $isObservationSubmission ? 'fr' : 'ar' }}"
                    style="
                        font-family:
                            {{ $isObservationSubmission
                                ? "'Inter',sans-serif"
                                : "'Amiri','Noto Naskh Arabic',serif" }};
                        font-size:1.15rem;
                        line-height:2;
                        padding:1rem;
                        background:rgba(255,255,255,0.03);
                        border-radius:12px;
                        text-align:center;
                    "
                >
                    {{ $submission->reading_text }}
                </div>
            </div>
        </div>

        @if(
            $isObservationSubmission
            && $observationReview
            && (
                $observationReview['text']
                || $observationReview['image_path']
            )
        )
            <div class="adm-card mb-4">
                <div class="adm-card-header">
                    <h4>
                        <i
                            class="bi bi-eye-fill"
                            style="color:rgba(255,255,255,0.35);"
                        ></i>
                        Réponse d’observation
                    </h4>
                </div>

                <div class="adm-card-body">
                    @if($observationReview['text'])
                        <small
                            style="
                                color:var(--adm-text-muted);
                                display:block;
                                margin-bottom:8px;
                            "
                        >
                            Texte rédigé par l’élève
                        </small>

                        <div class="observation-admin-answer">
                            {{ $observationReview['text'] }}
                        </div>
                    @endif

                    @if($observationReview['image_path'])
                        @if($observationReview['text'])
                            <hr
                                style="
                                    border-color:rgba(255,255,255,0.08);
                                    margin:1.25rem 0;
                                "
                            >
                        @endif

                        <small
                            style="
                                color:var(--adm-text-muted);
                                display:block;
                                margin-bottom:8px;
                            "
                        >
                            Photo de la réponse manuscrite
                        </small>

                        <a
                            href="{{ route(
                                'admin.vocal-tests.submissions.audio',
                                $submission
                            ) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            <img
                                src="{{ route(
                                    'admin.vocal-tests.submissions.audio',
                                    $submission
                                ) }}?v={{
                                    $submission->updated_at?->timestamp
                                    ?? $submission->id
                                }}"
                                alt="Réponse manuscrite de l’élève"
                                class="observation-admin-image"
                            >
                        </a>

                        <div class="observation-admin-meta">
                            @if($observationReview['image_original_name'])
                                <span>
                                    <i class="bi bi-file-image me-1"></i>
                                    {{ $observationReview['image_original_name'] }}
                                </span>
                            @endif

                            @if($observationReview['image_size'])
                                <span>
                                    <i class="bi bi-hdd me-1"></i>
                                    {{
                                        number_format(
                                            $observationReview['image_size']
                                                / 1024,
                                            1,
                                            ',',
                                            ' '
                                        )
                                    }}
                                    Ko
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($isCompletionSubmission && $completionReview)
            <div class="adm-card mb-4">
                <div class="adm-card-header">
                    <h4>
                        <i
                            class="bi bi-puzzle-fill"
                            style="color:rgba(255,255,255,0.35);"
                        ></i>
                        Réponses de l’élève
                    </h4>
                </div>

                <div class="adm-card-body">
                    <div class="completion-review-grid">
                        @foreach(
                            $completionReview['expected_answers']
                            as $index => $expectedAnswer
                        )
                            @php
                                $studentAnswer =
                                    $completionReview['answers'][$index]
                                    ?? '—';

                                $isCorrect =
                                    $completionReview['results'][$index]
                                    ?? false;
                            @endphp

                            <div class="completion-review-row">
                                <strong>{{ $index + 1 }}</strong>

                                <div>
                                    <small
                                        style="
                                            color:var(--adm-text-muted);
                                            display:block;
                                        "
                                    >
                                        Réponse choisie
                                    </small>

                                    <span class="completion-review-word">
                                        {{ $studentAnswer }}
                                    </span>
                                </div>

                                <div>
                                    <small
                                        style="
                                            color:var(--adm-text-muted);
                                            display:block;
                                        "
                                    >
                                        Réponse attendue
                                    </small>

                                    <span class="completion-review-word">
                                        {{ $expectedAnswer }}
                                    </span>
                                </div>

                                <span
                                    class="completion-review-result
                                        {{ $isCorrect
                                            ? 'correct'
                                            : 'incorrect' }}"
                                >
                                    <i
                                        class="bi {{ $isCorrect
                                            ? 'bi-check-circle-fill'
                                            : 'bi-x-circle-fill' }}"
                                    ></i>
                                    {{ $isCorrect
                                        ? 'Correct'
                                        : 'Incorrect' }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div
                        style="
                            margin-top:14px;
                            padding:12px;
                            border-radius:12px;
                            background:rgba(124,58,237,0.1);
                            color:#C4B5FD;
                            text-align:center;
                            font-weight:700;
                        "
                    >
                        Résultat automatique :
                        {{ $submission->auto_correct_count ?? 0 }}
                        /
                        {{ $submission->auto_total_questions ?? 0 }}
                        —
                        {{ $submission->final_score
                            ?? $submission->score
                            ?? 0 }}/100
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Audio & Évaluation -->
    <div class="col-lg-5">
        @if(
            !$isCompletionSubmission
            && (
                !$isObservationSubmission
                || $isObservationAudioSubmission
            )
        )
        <!-- Audio Player -->
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4>
                    <i
                        class="bi bi-headphones"
                        style="color:rgba(255,255,255,0.35);"
                    ></i>
                    {{ $isObservationSubmission
                        ? 'Description orale enregistrée'
                        : 'Enregistrement audio' }}
                </h4>
            </div>
            <div class="adm-card-body">
                <div class="audio-player-card">
                    @if($audioPlayable)
                        <audio
                            id="submissionAudioPlayer"
                            controls
                            preload="metadata"
                            src="{{ route('admin.vocal-tests.submissions.audio', $submission) }}?v={{ $submission->updated_at?->timestamp ?? $submission->id }}"
                        >
                            Votre navigateur ne peut pas lire cet audio.
                        </audio>

                        <div id="audioBrowserError" class="audio-browser-error">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Le navigateur n’arrive pas à décoder cet enregistrement. Ouvrez le fichier directement ou refaites l’enregistrement.
                        </div>

                        <div class="audio-file-meta">
                            <span>
                                <i class="bi bi-file-earmark-music me-1"></i>
                                {{ $audioMimeType }}
                            </span>
                            <span>
                                <i class="bi bi-hdd me-1"></i>
                                {{ number_format(($audioSize ?? 0) / 1024, 1, ',', ' ') }} Ko
                            </span>
                        </div>

                        <a
                            href="{{ route('admin.vocal-tests.submissions.audio', $submission) }}?v={{ $submission->updated_at?->timestamp ?? $submission->id }}"
                            target="_blank"
                            rel="noopener"
                            class="audio-open-link"
                        >
                            <i class="bi bi-box-arrow-up-right"></i>
                            Ouvrir l’enregistrement directement
                        </a>
                    @else
                        <div class="audio-error-box">
                            <i class="bi bi-exclamation-octagon-fill me-1"></i>
                            {{ $audioError ?? 'Le fichier audio est introuvable ou invalide.' }}
                        </div>

                        @if($audioExists && $audioSize !== null)
                            <div class="audio-file-meta">
                                <span>
                                    <i class="bi bi-hdd me-1"></i>
                                    Taille : {{ $audioSize }} octets
                                </span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        @endif

        <!-- Formulaire d'évaluation -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-star" style="color:rgba(255,255,255,0.35);"></i> Évaluation</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.vocal-tests.submissions.review', $submission) }}">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Statut</label>
                        <select name="status" class="adm-form-control" required>
                            @foreach(\App\Models\VocalTestSubmission::getStatuses() as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $submission->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Note globale (0-100)</label>
                        <input type="number" name="score" class="adm-form-control" value="{{ old('score', $submission->score) }}" min="0" max="100" placeholder="Optionnel si scores détaillés">
                    </div>

                    <hr style="border-color:rgba(255,255,255,0.08);">
                    <small style="color:var(--adm-text-muted);display:block;margin-bottom:10px;">
                        {{ $isObservationSubmission
                            ? 'Évaluation de la description orale'
                            : 'Scores détaillés (remplir au moins un)' }}
                    </small>

                    <div class="score-input-group">
                        <div class="adm-form-group">
                            <label class="adm-form-label">Prononciation</label>
                            <input type="number" name="score_pronunciation" class="adm-form-control" value="{{ old('score_pronunciation', $submission->score_pronunciation) }}" min="0" max="100" placeholder="0-100">
                        </div>

                        @unless($isObservationSubmission)
                            <div class="adm-form-group">
                                <label class="adm-form-label">Tajwid</label>
                                <input type="number" name="score_tajwid" class="adm-form-control" value="{{ old('score_tajwid', $submission->score_tajwid) }}" min="0" max="100" placeholder="0-100">
                            </div>
                            <div class="adm-form-group">
                                <label class="adm-form-label">Mémorisation</label>
                                <input type="number" name="score_memorization" class="adm-form-control" value="{{ old('score_memorization', $submission->score_memorization) }}" min="0" max="100" placeholder="0-100">
                            </div>
                        @endunless

                        <div class="adm-form-group">
                            <label class="adm-form-label">Fluidité</label>
                            <input type="number" name="score_fluency" class="adm-form-control" value="{{ old('score_fluency', $submission->score_fluency) }}" min="0" max="100" placeholder="0-100">
                        </div>
                    </div>

                    <div class="adm-form-group" style="margin-top:10px;">
                        <label class="adm-form-label">Commentaire professeur</label>
                        <textarea name="teacher_comment" class="adm-form-control" rows="3" placeholder="{{ $isObservationSubmission
    ? 'Donnez votre avis sur la description et le vocabulaire...'
    : 'Donnez votre avis sur la récitation...' }}">{{ old('teacher_comment', $submission->teacher_comment) }}</textarea>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-primary w-100">
                        <i class="bi bi-check2-circle me-1"></i> Enregistrer l'évaluation
                    </button>
                </form>

                @if($submission->teacher_comment)
                <hr style="border-color:rgba(255,255,255,0.08);margin:1.5rem 0;">
                <div>
                    <small style="color:var(--adm-text-muted);display:block;margin-bottom:6px;">Dernier commentaire :</small>
                    <div style="padding:12px 16px;background:rgba(255,255,255,0.03);border-radius:12px;color:rgba(255,255,255,0.7);font-style:italic;">
                        {{ $submission->teacher_comment }}
                    </div>
                    @if($submission->reviewed_at)
                    <small style="color:var(--adm-text-muted);display:block;margin-top:6px;">
                        Évalué le {{ $submission->reviewed_at->format('d/m/Y à H:i') }}
                    </small>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Rendez-vous lié -->
        @if($submission->appointment)
        <div class="adm-card mt-4">
            <div class="adm-card-header">
                <h4><i class="bi bi-calendar-check" style="color:rgba(255,255,255,0.35);"></i> Rendez-vous lié</h4>
            </div>
            <div class="adm-card-body" style="font-size:0.85rem;">
                <p><strong>Nom :</strong> {{ $submission->appointment->first_name }} {{ $submission->appointment->last_name }}</p>
                <p><strong>Email :</strong> {{ $submission->appointment->email }}</p>
                <p><strong>Téléphone :</strong> {{ $submission->appointment->phone }}</p>
                <p><strong>Statut :</strong>
                    <span class="adm-badge {{ $submission->appointment->status === 'confirmed' ? 'adm-badge-success' : ($submission->appointment->status === 'cancelled' ? 'adm-badge-danger' : 'adm-badge-warning') }}">
                        {{ $submission->appointment->status }}
                    </span>
                </p>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-badge.submitted { background: rgba(96,165,250,0.15); color: #93C5FD; }
.status-badge.under_review { background: rgba(251,191,36,0.15); color: #FCD34D; }
.status-badge.reviewed { background: rgba(148,163,184,0.15); color: #CBD5E1; }
.status-badge.accepted { background: rgba(34,197,94,0.15); color: #4ADE80; }
.status-badge.needs_improvement { background: rgba(239,68,68,0.15); color: #FCA5A5; }
</style>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const player = document.getElementById('submissionAudioPlayer');
    const errorBox = document.getElementById('audioBrowserError');

    if (!player || !errorBox) {
        return;
    }

    player.addEventListener('error', function () {
        errorBox.classList.add('show');
    });

    player.addEventListener('canplay', function () {
        errorBox.classList.remove('show');
    });
});
</script>

@endsection