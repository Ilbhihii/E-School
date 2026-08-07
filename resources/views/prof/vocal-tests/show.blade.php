@extends('layouts.prof')

@section('title', 'Consultation du test')

@section('content')
<style>
.vt-reading {
    direction:rtl;
    text-align:right;
    white-space:pre-wrap;
    font-family:'Amiri','Noto Naskh Arabic',serif;
    font-size:1.25rem;
    line-height:2;
    padding:1rem;
    border:1px solid rgba(255,255,255,.08);
    border-radius:14px;
    background:rgba(255,255,255,.025);
}
.vt-meta-grid {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:14px;
}
.vt-meta-item {
    padding:12px;
    border:1px solid rgba(255,255,255,.07);
    border-radius:12px;
    background:rgba(255,255,255,.025);
}
.vt-meta-item small {
    display:block;
    color:var(--adm-text-muted);
    margin-bottom:4px;
}
.vt-completion-row {
    display:grid;
    grid-template-columns:40px 1fr 1fr 100px;
    gap:10px;
    align-items:center;
    padding:10px;
    border-bottom:1px solid rgba(255,255,255,.07);
}
@media(max-width:700px){
    .vt-meta-grid{grid-template-columns:1fr;}
    .vt-completion-row{grid-template-columns:35px 1fr;}
}
</style>

<div class="adm-page-header">
    <div>
        <h1>
            <i class="bi bi-file-earmark-play-fill me-2" style="color:var(--adm-primary);"></i>
            Test du nouvel étudiant
        </h1>
        <div class="subtitle">
            {{ $submission->user?->name ?? 'Nouveau candidat' }}
            —
            {{ $submission->subject?->name ?? '-' }}
            →
            {{ $submission->level?->name ?? '-' }}
            →
            {{ $submission->classRoom?->name ?? '-' }}
        </div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.vocal-tests.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i>
            Retour
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4><i class="bi bi-person-lines-fill"></i> Informations</h4>
            </div>
            <div class="adm-card-body">
                <div class="vt-meta-grid">
                    <div class="vt-meta-item">
                        <small>Étudiant</small>
                        <strong>{{ $submission->user?->name ?? 'Nouveau candidat' }}</strong>
                    </div>
                    <div class="vt-meta-item">
                        <small>Email</small>
                        <strong>{{ $submission->user?->email ?? 'Compte non créé' }}</strong>
                    </div>
                    <div class="vt-meta-item">
                        <small>Matière</small>
                        <strong>{{ $submission->subject?->name ?? '-' }}</strong>
                    </div>
                    <div class="vt-meta-item">
                        <small>Niveau → Classe</small>
                        <strong>
                            {{ $submission->level?->name ?? '-' }}
                            →
                            {{ $submission->classRoom?->name ?? '-' }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        @if($isCompletionSubmission)
            <div class="adm-card">
                <div class="adm-card-header">
                    <h4><i class="bi bi-puzzle-fill"></i> Réponses de complétion</h4>
                </div>
                <div class="adm-card-body">
                    @php
                        $answers = $submission->completionAnswers();
                        $expected = $submission->completionExpectedAnswers();
                        $results = $submission->completionResults();
                    @endphp

                    @forelse($answers as $index => $answer)
                        <div class="vt-completion-row">
                            <strong>{{ $index + 1 }}</strong>
                            <div>
                                <small style="color:var(--adm-text-muted);">Réponse</small>
                                <div dir="rtl">{{ $answer }}</div>
                            </div>
                            <div>
                                <small style="color:var(--adm-text-muted);">Attendu</small>
                                <div dir="rtl">{{ $expected[$index] ?? '—' }}</div>
                            </div>
                            <div>
                                {{ ($results[$index] ?? false) ? 'Correct' : 'À vérifier' }}
                            </div>
                        </div>
                    @empty
                        <p style="color:var(--adm-text-muted);margin:0;">Aucune réponse enregistrée.</p>
                    @endforelse
                </div>
            </div>
        @elseif($isObservationSubmission && !$submission->audio_path)
            <div class="adm-card">
                <div class="adm-card-header">
                    <h4><i class="bi bi-eye-fill"></i> Réponse d’observation</h4>
                </div>
                <div class="adm-card-body">
                    @if($submission->observationResponseMode() === 'image')
                        <img
                            src="{{ route('prof.vocal-tests.audio', $submission) }}"
                            alt="Réponse d’observation"
                            style="width:100%;max-height:650px;object-fit:contain;border-radius:14px;background:#fff;"
                        >
                    @else
                        <div class="vt-reading">
                            {{ $submission->observationText() ?: 'Aucune réponse textuelle.' }}
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="adm-card">
                <div class="adm-card-header">
                    <h4><i class="bi bi-card-text"></i> Texte du test</h4>
                </div>
                <div class="adm-card-body">
                    <div class="vt-reading">
                        {{ $submission->reading_text ?: 'Aucun texte enregistré.' }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-5">
        @if(!$isCompletionSubmission && $submission->audio_path)
            <div class="adm-card mb-4">
                <div class="adm-card-header">
                    <h4><i class="bi bi-headphones"></i> Enregistrement</h4>
                </div>
                <div class="adm-card-body">
                    <audio
                        controls
                        preload="metadata"
                        style="width:100%;"
                        src="{{ route('prof.vocal-tests.audio', $submission) }}?v={{ $submission->updated_at?->timestamp ?? $submission->id }}"
                    >
                        Votre navigateur ne peut pas lire cet audio.
                    </audio>

                    @if($submission->duration_seconds)
                        <small style="display:block;color:var(--adm-text-muted);margin-top:10px;">
                            Durée : {{ $submission->duration_seconds }} seconde(s)
                        </small>
                    @endif
                </div>
            </div>
        @endif

        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-shield-check"></i> État du test</h4>
            </div>
            <div class="adm-card-body">
                @php($score = $submission->final_score ?? $submission->score)

                <div class="vt-meta-grid">
                    <div class="vt-meta-item">
                        <small>Statut</small>
                        <strong>
                            {{ \App\Models\VocalTestSubmission::getStatuses()[$submission->status] ?? $submission->status }}
                        </strong>
                    </div>
                    <div class="vt-meta-item">
                        <small>Note</small>
                        <strong>{{ $score !== null ? $score . '/100' : 'Non évalué' }}</strong>
                    </div>
                </div>

                @if($submission->teacher_comment)
                    <div style="margin-top:14px;">
                        <small style="display:block;color:var(--adm-text-muted);margin-bottom:5px;">
                            Commentaire enregistré
                        </small>
                        <div style="padding:12px;border-radius:12px;background:rgba(255,255,255,.035);">
                            {{ $submission->teacher_comment }}
                        </div>
                    </div>
                @endif

                <div
                    style="
                        margin-top:14px;
                        padding:12px;
                        border:1px solid rgba(56,189,248,.16);
                        border-radius:12px;
                        background:rgba(14,165,233,.06);
                        color:#BAE6FD;
                        font-size:.8rem;
                    "
                >
                    <i class="bi bi-info-circle me-1"></i>
                    Cette interface est en consultation uniquement.
                    L’administrateur reste responsable de l’évaluation finale.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
