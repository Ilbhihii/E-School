@extends('layouts.student')

@section('title', 'Mes tests écrits')
@section('page_title', 'Mes tests écrits')
@section('breadcrumb', 'Tests Soutien Lycée')

@section('content')

<div class="page-header">
    <div>
        <h1>
            <i
                class="bi bi-file-earmark-check-fill"
                style="color:#38BDF8;"
            ></i>

            Historique des tests écrits
        </h1>

        <div class="subtitle">
            Suivez la correction, votre note et l’accès aux cours.
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info">
        {{ session('info') }}
    </div>
@endif

<div class="student-test-grid">
    @forelse($submissions as $submission)
        <article class="student-test-card">
            <div class="student-test-top">
                <span
                    class="student-status
                        status-{{ $submission->status }}"
                >
                    {{ $submission->statusLabel() }}
                </span>

                <span class="student-test-date">
                    {{
                        optional(
                            $submission->submitted_at
                        )->format('d/m/Y')
                        ?? '—'
                    }}
                </span>
            </div>

            <span class="student-test-icon">
                <i
                    class="bi {{
                        str_contains(
                            mb_strtolower(
                                $submission
                                    ->classRoom
                                    ?->name
                                ?? ''
                            ),
                            'math'
                        )
                            ? 'bi-calculator-fill'
                            : 'bi-lightning-charge-fill'
                    }}"
                ></i>
            </span>

            <h2>{{ $submission->test_title }}</h2>

            <p>
                {{ $submission->subject?->name }}
                · {{ $submission->level?->name }}
                · {{ $submission->classRoom?->name }}
            </p>

            <div class="student-test-result">
                <span>
                    <small>Note</small>

                    <strong>
                        {{
                            $submission->score !== null
                                ? $submission->score . '/20'
                                : '—'
                        }}
                    </strong>
                </span>

                <span>
                    <small>Images</small>

                    <strong>
                        {{ count($submission->images()) }}
                    </strong>
                </span>

                <span>
                    <small>Cours</small>

                    <strong>
                        {{
                            $submission->isApproved()
                                ? 'Ouverts'
                                : 'Bloqués'
                        }}
                    </strong>
                </span>
            </div>

            <a
                href="{{
                    route(
                        'student.written-tests.show',
                        $submission
                    )
                }}"
                class="student-test-action"
            >
                Voir le suivi
                <i class="bi bi-arrow-right"></i>
            </a>
        </article>
    @empty
        <div class="pr-empty student-tests-empty">
            <div class="pr-empty-icon">
                <i class="bi bi-file-earmark-text"></i>
            </div>

            <h5>Aucun test écrit envoyé</h5>

            <p>
                Vos tests Mathématiques et Physique-Chimie
                apparaîtront ici après leur envoi.
            </p>
        </div>
    @endforelse
</div>

@if($submissions->hasPages())
    <div class="mt-4">
        {{ $submissions->links() }}
    </div>
@endif

<style>
.student-test-grid {
    display: grid;
    grid-template-columns:
        repeat(3, minmax(0,1fr));
    gap: 16px;
}

.student-test-card {
    min-width: 0;
    display: flex;
    flex-direction: column;
    padding: 1rem;
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 17px;
    background:
        linear-gradient(
            145deg,
            rgba(17,27,47,.98),
            rgba(9,17,32,.99)
        );
}

.student-test-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.student-status {
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

.student-test-date {
    color: rgba(255,255,255,.3);
    font-size: .6rem;
}

.student-test-icon {
    width: 48px;
    height: 48px;
    display: grid;
    place-items: center;
    margin: 1rem 0 .8rem;
    border-radius: 14px;
    color: #fff;
    background:
        linear-gradient(135deg,#0284C7,#7C3AED);
    font-size: 1.15rem;
}

.student-test-card h2 {
    margin: 0 0 .35rem;
    color: #fff;
    font-size: .9rem;
}

.student-test-card > p {
    min-height: 38px;
    margin: 0;
    color: rgba(255,255,255,.4);
    font-size: .65rem;
    line-height: 1.5;
}

.student-test-result {
    display: grid;
    grid-template-columns:
        repeat(3,1fr);
    gap: 6px;
    margin: .9rem 0;
}

.student-test-result > span {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    padding: 8px 5px;
    border: 1px solid rgba(255,255,255,.05);
    border-radius: 10px;
    background: rgba(255,255,255,.025);
}

.student-test-result small {
    color: rgba(255,255,255,.3);
    font-size: .54rem;
}

.student-test-result strong {
    color: rgba(255,255,255,.76);
    font-size: .66rem;
}

.student-test-action {
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: auto;
    border-radius: 11px;
    color: #fff;
    background:
        linear-gradient(135deg,#0284C7,#2563EB);
    font-size: .69rem;
    font-weight: 780;
    text-decoration: none;
}

.student-test-action:hover {
    color: #fff;
}

.student-tests-empty {
    grid-column: 1/-1;
}

@media (max-width:980px) {
    .student-test-grid {
        grid-template-columns:
            repeat(2, minmax(0,1fr));
    }
}

@media (max-width:620px) {
    .student-test-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@endsection
