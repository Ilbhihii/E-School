@extends('layouts.admin')

@section('title', 'Correction — ' . $submission->test_title)
@section('page_title', 'Correction du test')
@section('breadcrumb', 'Tests écrits → Correction')

@section('content')

<div class="adm-page-header">
    <div>
        <a
            href="{{ route('admin.written-tests.index') }}"
            class="review-back"
        >
            <i class="bi bi-arrow-left"></i>
            Retour aux tests
        </a>

        <h1>{{ $submission->test_title }}</h1>

        <div class="subtitle">
            {{ $submission->user?->name }}
            · {{ $submission->subject?->name }}
            · {{ $submission->level?->name }}
            · {{ $submission->classRoom?->name }}
        </div>
    </div>

    <a
        href="{{
            route(
                'admin.written-tests.report',
                $submission
            )
        }}"
        class="adm-btn adm-btn-ghost"
    >
        <i class="bi bi-file-earmark-pdf-fill"></i>
        Rapport PDF
    </a>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-3">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="adm-alert adm-alert-danger mb-3">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
@endif

<form
    method="POST"
    action="{{
        route(
            'admin.written-tests.update',
            $submission
        )
    }}"
>
    @csrf
    @method('PATCH')

    <div class="review-layout">
        <main class="review-images-panel">
            <div class="adm-card">
                <div class="adm-card-header">
                    <h4>
                        <i
                            class="bi bi-images"
                            style="color:#38BDF8;"
                        ></i>

                        Réponses envoyées
                    </h4>

                    <span class="image-total">
                        {{ count($submission->images()) }}
                        image(s)
                    </span>
                </div>

                <div class="adm-card-body">
                    <div class="answer-images-grid">
                        @foreach(
                            $submission->images()
                            as $imageIndex => $image
                        )
                            <article class="answer-image-card">
                                <a
                                    href="{{
                                        route(
                                            'high-school-test.image',
                                            [
                                                $submission,
                                                $imageIndex,
                                            ]
                                        )
                                    }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="answer-image-link"
                                >
                                    <img
                                        src="{{
                                            route(
                                                'high-school-test.image',
                                                [
                                                    $submission,
                                                    $imageIndex,
                                                ]
                                            )
                                        }}"
                                        alt="Réponse {{
                                            $imageIndex + 1
                                        }}"
                                    >

                                    <span>
                                        <i class="bi bi-arrows-fullscreen"></i>
                                        Ouvrir en grand
                                    </span>
                                </a>

                                <div class="answer-image-info">
                                    <strong>
                                        Feuille {{ $imageIndex + 1 }}
                                    </strong>

                                    <small>
                                        {{
                                            $image['original_name']
                                            ?? 'Image'
                                        }}
                                    </small>
                                </div>

                                <label>
                                    <span>
                                        Annotation de cette image
                                    </span>

                                    <textarea
                                        name="image_annotations[]"
                                        rows="3"
                                        class="adm-form-control"
                                        placeholder="Ex : méthode correcte, erreur à la question 2..."
                                    >{{ old(
                                        "image_annotations.$imageIndex",
                                        $submission->annotations()[$imageIndex]
                                            ?? ''
                                    ) }}</textarea>
                                </label>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </main>

        <aside class="review-form-panel">
            <div class="adm-card">
                <div class="adm-card-header">
                    <h4>
                        <i
                            class="bi bi-pencil-square"
                            style="color:#A78BFA;"
                        ></i>

                        Évaluation
                    </h4>
                </div>

                <div class="adm-card-body">
                    <div class="student-summary">
                        <span>
                            {{
                                mb_strtoupper(
                                    mb_substr(
                                        $submission->user?->name
                                        ?? '?',
                                        0,
                                        1
                                    )
                                )
                            }}
                        </span>

                        <div>
                            <strong>
                                {{ $submission->user?->name }}
                            </strong>

                            <small>
                                {{ $submission->user?->email }}
                            </small>
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">
                            Statut
                        </label>

                        <select
                            name="status"
                            class="adm-form-select
                                @error('status') error @enderror"
                            required
                        >
                            @foreach(
                                \App\Models\HighSchoolTestSubmission::statuses()
                                as $value => $label
                            )
                                <option
                                    value="{{ $value }}"
                                    @selected(
                                        old(
                                            'status',
                                            $submission->status
                                        ) === $value
                                    )
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">
                            Note sur 20
                        </label>

                        <div class="score-field">
                            <input
                                type="number"
                                name="score"
                                value="{{
                                    old(
                                        'score',
                                        $submission->score
                                    )
                                }}"
                                min="0"
                                max="20"
                                class="adm-form-control
                                    @error('score') error @enderror"
                                placeholder="Ex : 15"
                            >

                            <span>/20</span>
                        </div>

                        @error('score')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">
                            Commentaire général
                        </label>

                        <textarea
                            name="teacher_comment"
                            rows="7"
                            class="adm-form-control
                                @error('teacher_comment')
                                    error
                                @enderror"
                            placeholder="Points forts, erreurs, conseils et décision..."
                        >{{ old(
                            'teacher_comment',
                            $submission->teacher_comment
                        ) }}</textarea>

                        @error('teacher_comment')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="review-decision-help">
                        <p>
                            <strong>Validé :</strong>
                            la matière et la classe sont assignées
                            automatiquement à l’étudiant.
                        </p>

                        <p>
                            <strong>À refaire :</strong>
                            l’étudiant peut envoyer de nouvelles images.
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="adm-btn adm-btn-primary w-100"
                    >
                        <i class="bi bi-check2-circle"></i>
                        Enregistrer la correction
                    </button>

                    @if($submission->reviewed_at)
                        <div class="review-history">
                            Corrigé le
                            {{
                                $submission->reviewed_at
                                    ->format('d/m/Y à H:i')
                            }}

                            @if($submission->reviewer)
                                par
                                {{ $submission->reviewer->name }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($submission->appointment)
                <div class="adm-card mt-3">
                    <div class="adm-card-header">
                        <h4>
                            <i class="bi bi-calendar-check"></i>
                            Rendez-vous
                        </h4>
                    </div>

                    <div class="adm-card-body appointment-summary">
                        <p>
                            <span>Nom</span>
                            <strong>
                                {{
                                    $submission->appointment
                                        ->full_name
                                }}
                            </strong>
                        </p>

                        <p>
                            <span>Téléphone</span>
                            <strong>
                                {{
                                    $submission->appointment
                                        ->phone
                                }}
                            </strong>
                        </p>

                        <p>
                            <span>Statut</span>
                            <strong>
                                {{
                                    $submission->appointment
                                        ->status
                                }}
                            </strong>
                        </p>
                    </div>
                </div>
            @endif
        </aside>
    </div>
</form>

<style>
.review-back {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 8px;
    color: rgba(255,255,255,.45);
    font-size: .66rem;
    text-decoration: none;
}

.review-back:hover {
    color: #fff;
}

.review-layout {
    display: grid;
    grid-template-columns:
        minmax(0,1fr) 360px;
    align-items: start;
    gap: 18px;
}

.image-total {
    color: var(--adm-text-muted);
    font-size: .65rem;
}

.answer-images-grid {
    display: grid;
    grid-template-columns:
        repeat(2, minmax(0,1fr));
    gap: 15px;
}

.answer-image-card {
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.065);
    border-radius: 15px;
    background: rgba(255,255,255,.022);
}

.answer-image-link {
    position: relative;
    display: block;
    aspect-ratio: 4/3;
    overflow: hidden;
    background: #050A13;
}

.answer-image-link img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.answer-image-link > span {
    position: absolute;
    right: 8px;
    bottom: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 8px;
    border-radius: 8px;
    color: #fff;
    background: rgba(0,0,0,.72);
    font-size: .58rem;
}

.answer-image-info {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 10px 11px 0;
}

.answer-image-info strong {
    color: rgba(255,255,255,.8);
    font-size: .68rem;
}

.answer-image-info small {
    overflow: hidden;
    color: rgba(255,255,255,.32);
    font-size: .56rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.answer-image-card label {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 10px 11px 11px;
}

.answer-image-card label > span {
    color: rgba(255,255,255,.45);
    font-size: .61rem;
    font-weight: 700;
}

.review-form-panel {
    position: sticky;
    top: 85px;
}

.student-summary {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1rem;
    padding: 10px;
    border: 1px solid rgba(255,255,255,.05);
    border-radius: 12px;
    background: rgba(255,255,255,.025);
}

.student-summary > span {
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    color: #fff;
    background:
        linear-gradient(135deg,#2563EB,#7C3AED);
    font-weight: 800;
}

.student-summary > div {
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.student-summary strong {
    color: rgba(255,255,255,.85);
    font-size: .72rem;
}

.student-summary small {
    color: rgba(255,255,255,.35);
    font-size: .6rem;
}

.score-field {
    position: relative;
}

.score-field input {
    padding-right: 48px;
}

.score-field span {
    position: absolute;
    top: 50%;
    right: 13px;
    color: rgba(255,255,255,.35);
    transform: translateY(-50%);
    font-size: .67rem;
}

.review-decision-help {
    margin-bottom: 1rem;
    padding: 10px;
    border: 1px solid rgba(96,165,250,.1);
    border-radius: 11px;
    background: rgba(37,99,235,.05);
}

.review-decision-help p {
    margin: 0 0 5px;
    color: rgba(255,255,255,.43);
    font-size: .61rem;
    line-height: 1.45;
}

.review-decision-help p:last-child {
    margin-bottom: 0;
}

.review-history {
    margin-top: 9px;
    color: rgba(255,255,255,.32);
    font-size: .59rem;
    text-align: center;
}

.appointment-summary p {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
    font-size: .65rem;
}

.appointment-summary p:last-child {
    margin-bottom: 0;
}

.appointment-summary span {
    color: rgba(255,255,255,.38);
}

.appointment-summary strong {
    color: rgba(255,255,255,.72);
}

@media (max-width:1050px) {
    .review-layout {
        grid-template-columns: 1fr;
    }

    .review-form-panel {
        position: static;
    }
}

@media (max-width:680px) {
    .answer-images-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@endsection
