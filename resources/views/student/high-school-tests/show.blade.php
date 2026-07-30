@extends('layouts.student')

@section('title', $submission->test_title)
@section('page_title', 'Suivi du test')
@section('breadcrumb', 'Mes tests → Résultat')

@section('content')

<div class="page-header">
    <div>
        <a
            href="{{ route('student.written-tests.index') }}"
            class="student-review-back"
        >
            <i class="bi bi-arrow-left"></i>
            Mes tests
        </a>

        <h1>{{ $submission->test_title }}</h1>

        <div class="subtitle">
            {{ $submission->subject?->name }}
            · {{ $submission->level?->name }}
            · {{ $submission->classRoom?->name }}
        </div>
    </div>

    @if($submission->reviewed_at)
        <a
            href="{{
                route(
                    'student.written-tests.report',
                    $submission
                )
            }}"
            class="pr-btn pr-btn-primary"
        >
            <i class="bi bi-file-earmark-pdf-fill"></i>
            Rapport PDF
        </a>
    @endif
</div>

@if(session('info'))
    <div class="alert alert-info">
        {{ session('info') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="student-review-layout">
    <main>
        <div class="student-result-card">
            <div>
                <span
                    class="student-result-status
                        status-{{ $submission->status }}"
                >
                    {{ $submission->statusLabel() }}
                </span>

                <h2>
                    @if($submission->isApproved())
                        Test validé
                    @elseif(
                        $submission->status
                        === \App\Models\HighSchoolTestSubmission
                            ::STATUS_REVISION_REQUESTED
                    )
                        Une nouvelle réponse est demandée
                    @elseif(
                        $submission->status
                        === \App\Models\HighSchoolTestSubmission
                            ::STATUS_REJECTED
                    )
                        Test non validé
                    @else
                        Correction en attente
                    @endif
                </h2>

                <p>
                    @if($submission->isApproved())
                        Votre accès à cette matière est autorisé.
                    @elseif($submission->canSubmitAgain())
                        Consultez le commentaire puis envoyez
                        de nouvelles réponses.
                    @else
                        L’administration examine actuellement
                        votre travail.
                    @endif
                </p>
            </div>

            <span class="student-result-score">
                <small>Note</small>

                <strong>
                    {{
                        $submission->score !== null
                            ? $submission->score . '/20'
                            : '—'
                    }}
                </strong>
            </span>
        </div>

        <div class="student-review-card">
            <h3>
                <i class="bi bi-chat-square-text-fill"></i>
                Commentaire de correction
            </h3>

            <div class="student-general-comment">
                {{
                    $submission->teacher_comment
                    ?: 'Aucun commentaire pour le moment.'
                }}
            </div>
        </div>

        <div class="student-review-card">
            <h3>
                <i class="bi bi-images"></i>
                Mes réponses et annotations
            </h3>

            <div class="student-answer-grid">
                @foreach(
                    $submission->images()
                    as $imageIndex => $image
                )
                    <article>
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
                                alt="Feuille {{
                                    $imageIndex + 1
                                }}"
                            >
                        </a>

                        <div>
                            <strong>
                                Feuille {{ $imageIndex + 1 }}
                            </strong>

                            <p>
                                {{
                                    $submission
                                        ->annotations()[
                                            $imageIndex
                                        ]
                                    ?? 'Aucune annotation.'
                                }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </main>

    <aside>
        <div class="student-review-card">
            <h3>
                <i class="bi bi-info-circle-fill"></i>
                Informations
            </h3>

            <dl class="student-review-details">
                <div>
                    <dt>Date d’envoi</dt>
                    <dd>
                        {{
                            optional(
                                $submission->submitted_at
                            )->format('d/m/Y H:i')
                            ?? '—'
                        }}
                    </dd>
                </div>

                <div>
                    <dt>Date de correction</dt>
                    <dd>
                        {{
                            optional(
                                $submission->reviewed_at
                            )->format('d/m/Y H:i')
                            ?? 'En attente'
                        }}
                    </dd>
                </div>

                <div>
                    <dt>Correcteur</dt>
                    <dd>
                        {{
                            $submission->reviewer?->name
                            ?? '—'
                        }}
                    </dd>
                </div>

                <div>
                    <dt>Rendez-vous</dt>
                    <dd>
                        {{
                            $submission->appointment?->status
                            ?? '—'
                        }}
                    </dd>
                </div>
            </dl>
        </div>

        @if($submission->isApproved())
            <a
                href="{{
                    route(
                        'student.subjects.courses',
                        [
                            $submission->subject,
                            $submission->level,
                            $submission->classRoom,
                        ]
                    )
                }}"
                class="student-access-button"
            >
                <i class="bi bi-unlock-fill"></i>
                Accéder aux cours
            </a>
        @elseif($submission->canSubmitAgain())
            <a
                href="{{
                    route(
                        'high-school-test.show',
                        [
                            $submission->subject,
                            $submission->level,
                            $submission->classRoom,
                        ]
                    )
                }}"
                class="student-access-button retry"
            >
                <i class="bi bi-arrow-repeat"></i>
                Refaire le test
            </a>
        @endif
    </aside>
</div>

<style>
.student-review-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 7px;
    color: rgba(255,255,255,.4);
    font-size: .63rem;
    text-decoration: none;
}

.student-review-back:hover {
    color: #fff;
}

.student-review-layout {
    display: grid;
    grid-template-columns:
        minmax(0,1fr) 310px;
    align-items: start;
    gap: 17px;
}

.student-result-card,
.student-review-card {
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 17px;
    background:
        linear-gradient(
            145deg,
            rgba(17,27,47,.98),
            rgba(9,17,32,.99)
        );
}

.student-result-card {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 15px;
    padding: 1.1rem;
}

.student-result-status {
    display: inline-flex;
    padding: 5px 8px;
    border-radius: 999px;
    font-size: .59rem;
    font-weight: 800;
}

.status-submitted {
    color: #93C5FD;
    background: rgba(37,99,235,.12);
}

.status-under_review {
    color: #FCD34D;
    background: rgba(245,158,11,.12);
}

.status-approved {
    color: #4ADE80;
    background: rgba(34,197,94,.12);
}

.status-revision_requested {
    color: #FBBF24;
    background: rgba(245,158,11,.12);
}

.status-rejected {
    color: #FCA5A5;
    background: rgba(239,68,68,.12);
}

.status-reviewed {
    color: #CBD5E1;
    background: rgba(148,163,184,.12);
}

.student-result-card h2 {
    margin: .7rem 0 .3rem;
    color: #fff;
    font-size: 1rem;
}

.student-result-card p {
    margin: 0;
    color: rgba(255,255,255,.43);
    font-size: .68rem;
}

.student-result-score {
    width: 82px;
    height: 82px;
    flex: 0 0 82px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(96,165,250,.14);
    border-radius: 20px;
    background: rgba(37,99,235,.08);
}

.student-result-score small {
    color: rgba(255,255,255,.35);
    font-size: .56rem;
}

.student-result-score strong {
    color: #93C5FD;
    font-size: 1.2rem;
}

.student-review-card {
    margin-bottom: 15px;
    padding: 1rem;
}

.student-review-card h3 {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 .9rem;
    color: rgba(255,255,255,.85);
    font-size: .78rem;
}

.student-review-card h3 i {
    color: #38BDF8;
}

.student-general-comment {
    padding: 12px;
    border-left: 3px solid #38BDF8;
    border-radius: 0 11px 11px 0;
    color: rgba(255,255,255,.6);
    background: rgba(14,165,233,.06);
    font-size: .7rem;
    line-height: 1.65;
    white-space: pre-wrap;
}

.student-answer-grid {
    display: grid;
    grid-template-columns:
        repeat(2,minmax(0,1fr));
    gap: 12px;
}

.student-answer-grid article {
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.055);
    border-radius: 13px;
    background: rgba(255,255,255,.02);
}

.student-answer-grid a {
    display: block;
    aspect-ratio: 4/3;
    background: #050A13;
}

.student-answer-grid img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.student-answer-grid article > div {
    padding: 10px;
}

.student-answer-grid strong {
    color: rgba(255,255,255,.75);
    font-size: .65rem;
}

.student-answer-grid p {
    margin: 5px 0 0;
    color: rgba(255,255,255,.4);
    font-size: .62rem;
    line-height: 1.5;
}

.student-review-details {
    margin: 0;
}

.student-review-details > div {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,.05);
}

.student-review-details > div:last-child {
    border-bottom: 0;
}

.student-review-details dt {
    color: rgba(255,255,255,.36);
    font-size: .62rem;
    font-weight: 500;
}

.student-review-details dd {
    margin: 0;
    color: rgba(255,255,255,.68);
    font-size: .62rem;
    text-align: right;
}

.student-access-button {
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 13px;
    color: #fff;
    background:
        linear-gradient(135deg,#059669,#10B981);
    font-size: .72rem;
    font-weight: 800;
    text-decoration: none;
}

.student-access-button.retry {
    background:
        linear-gradient(135deg,#D97706,#F59E0B);
}

.student-access-button:hover {
    color: #fff;
    filter: brightness(1.06);
}

@media (max-width:900px) {
    .student-review-layout {
        grid-template-columns: 1fr;
    }
}

@media (max-width:620px) {
    .student-answer-grid {
        grid-template-columns: 1fr;
    }

    .student-result-card {
        align-items: flex-start;
    }
}
</style>

@endsection
