@extends('layouts.admin')

@section('title', 'Correction des tests écrits')
@section('page_title', 'Tests écrits')
@section('breadcrumb', 'Tests écrits → Corrections')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>
            <i
                class="bi bi-file-earmark-check-fill"
                style="color:#38BDF8;"
            ></i>

            Centre de correction
        </h1>

        <div class="subtitle">
            Corrigez les tests BAC, ajoutez une note,
            des annotations et débloquez les cours.
        </div>
    </div>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-3">
        {{ session('success') }}
    </div>
@endif

<div class="review-statistics">
    @foreach([
        ['label' => 'Tous', 'value' => $statistics['all'], 'icon' => 'bi-inbox-fill'],
        ['label' => 'Soumis', 'value' => $statistics['submitted'], 'icon' => 'bi-send-fill'],
        ['label' => 'En correction', 'value' => $statistics['under_review'], 'icon' => 'bi-pencil-fill'],
        ['label' => 'Validés', 'value' => $statistics['approved'], 'icon' => 'bi-check-circle-fill'],
        ['label' => 'À refaire', 'value' => $statistics['revision_requested'], 'icon' => 'bi-arrow-repeat'],
        ['label' => 'Refusés', 'value' => $statistics['rejected'], 'icon' => 'bi-x-circle-fill'],
    ] as $stat)
        <div class="review-stat-card">
            <span>
                <i class="bi {{ $stat['icon'] }}"></i>
            </span>

            <div>
                <strong>{{ $stat['value'] }}</strong>
                <small>{{ $stat['label'] }}</small>
            </div>
        </div>
    @endforeach
</div>

<div class="adm-card mb-4">
    <div class="adm-card-body">
        <form
            method="GET"
            action="{{ route('admin.written-tests.index') }}"
            class="review-filters"
        >
            <label>
                <span>Recherche</span>

                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    class="adm-form-control"
                    placeholder="Étudiant, email ou test..."
                >
            </label>

            <label>
                <span>Statut</span>

                <select
                    name="status"
                    class="adm-form-select"
                >
                    <option value="all">Tous</option>

                    @foreach(
                        \App\Models\HighSchoolTestSubmission::statuses()
                        as $value => $label
                    )
                        <option
                            value="{{ $value }}"
                            {{ request('status') === $value ? 'selected' : '' }}
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Matière</span>

                <select
                    name="subject_id"
                    class="adm-form-select"
                >
                    <option value="">
                        Toutes les matières
                    </option>

                    @foreach($subjects as $subject)
                        <option
                            value="{{ $subject->id }}"
                            {{ (string) request('subject_id') === (string) $subject->id ? 'selected' : '' }}
                        >
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="review-filter-actions">
                <button
                    class="adm-btn adm-btn-primary"
                    type="submit"
                >
                    <i class="bi bi-funnel-fill"></i>
                    Filtrer
                </button>

                <a
                    href="{{ route('admin.written-tests.index') }}"
                    class="adm-btn adm-btn-ghost"
                >
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<div class="review-grid">
    @forelse($submissions as $submission)
        <article class="review-card">
            <div class="review-card-top">
                <span
                    class="review-status
                        status-{{ $submission->status }}"
                >
                    {{ $submission->statusLabel() }}
                </span>

                <span class="review-images-count">
                    <i class="bi bi-images"></i>
                    {{ count($submission->images()) }}
                </span>
            </div>

            <div class="review-student">
                <span class="review-avatar">
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
                        {{ $submission->user?->name ?? '—' }}
                    </strong>

                    <small>
                        {{ $submission->user?->email ?? '—' }}
                    </small>
                </div>
            </div>

            <h2>{{ $submission->test_title }}</h2>

            <div class="review-path">
                <span>{{ $submission->subject?->name }}</span>
                <i class="bi bi-chevron-right"></i>
                <span>{{ $submission->level?->name }}</span>
                <i class="bi bi-chevron-right"></i>
                <span>{{ $submission->classRoom?->name }}</span>
            </div>

            <div class="review-meta">
                <span>
                    <i class="bi bi-calendar3"></i>
                    {{
                        optional(
                            $submission->submitted_at
                        )->format('d/m/Y H:i')
                        ?? '—'
                    }}
                </span>

                <span>
                    <i class="bi bi-award"></i>

                    {{
                        $submission->score !== null
                            ? $submission->score . '/20'
                            : 'Non noté'
                    }}
                </span>
            </div>

            <a
                href="{{
                    route(
                        'admin.written-tests.show',
                        $submission
                    )
                }}"
                class="review-open-button"
            >
                <i class="bi bi-pencil-square"></i>
                Corriger le test
                <i class="bi bi-arrow-right"></i>
            </a>
        </article>
    @empty
        <div class="adm-card review-empty">
            <div class="adm-empty">
                <div class="adm-empty-icon">
                    <i class="bi bi-file-earmark-x"></i>
                </div>

                <h5>Aucun test trouvé</h5>

                <p>
                    Modifiez les filtres ou attendez une nouvelle
                    soumission.
                </p>
            </div>
        </div>
    @endforelse
</div>

@if($submissions->hasPages())
    <div class="mt-4">
        {{ $submissions->links() }}
    </div>
@endif

<style>
.review-statistics {
    display: grid;
    grid-template-columns:
        repeat(6, minmax(0,1fr));
    gap: 10px;
    margin-bottom: 1rem;
}

.review-stat-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 14px;
    background: rgba(255,255,255,.025);
}

.review-stat-card > span {
    width: 35px;
    height: 35px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    color: #7DD3FC;
    background: rgba(14,165,233,.1);
}

.review-stat-card > div {
    display: flex;
    flex-direction: column;
}

.review-stat-card strong {
    color: #fff;
    font-size: 1rem;
}

.review-stat-card small {
    color: var(--adm-text-muted);
    font-size: .6rem;
}

.review-filters {
    display: grid;
    grid-template-columns:
        1.4fr 1fr 1fr auto;
    align-items: end;
    gap: 12px;
}

.review-filters label {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.review-filters label > span {
    color: rgba(255,255,255,.55);
    font-size: .66rem;
    font-weight: 700;
}

.review-filter-actions {
    display: flex;
    gap: 8px;
}

.review-grid {
    display: grid;
    grid-template-columns:
        repeat(3, minmax(0,1fr));
    gap: 16px;
}

.review-card {
    min-width: 0;
    display: flex;
    flex-direction: column;
    padding: 1rem;
    border: 1px solid rgba(255,255,255,.065);
    border-radius: 17px;
    background:
        linear-gradient(
            145deg,
            rgba(17,27,47,.98),
            rgba(9,17,32,.99)
        );
}

.review-card-top,
.review-meta,
.review-path {
    display: flex;
    align-items: center;
}

.review-card-top {
    justify-content: space-between;
    gap: 10px;
    margin-bottom: .9rem;
}

.review-status {
    padding: 5px 8px;
    border-radius: 999px;
    font-size: .6rem;
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

.review-images-count {
    color: rgba(255,255,255,.4);
    font-size: .63rem;
}

.review-student {
    display: flex;
    align-items: center;
    gap: 9px;
}

.review-avatar {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    color: #fff;
    background:
        linear-gradient(135deg,#2563EB,#7C3AED);
    font-weight: 800;
}

.review-student > div {
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.review-student strong,
.review-student small {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.review-student strong {
    color: rgba(255,255,255,.88);
    font-size: .73rem;
}

.review-student small {
    color: rgba(255,255,255,.35);
    font-size: .6rem;
}

.review-card h2 {
    margin: .85rem 0 .55rem;
    color: #fff;
    font-size: .86rem;
}

.review-path {
    flex-wrap: wrap;
    gap: 5px;
    color: rgba(255,255,255,.42);
    font-size: .61rem;
}

.review-path i {
    font-size: .48rem;
}

.review-meta {
    justify-content: space-between;
    gap: 10px;
    margin: .9rem 0;
    padding-top: .8rem;
    border-top: 1px solid rgba(255,255,255,.05);
    color: rgba(255,255,255,.36);
    font-size: .61rem;
}

.review-open-button {
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

.review-open-button:hover {
    color: #fff;
    filter: brightness(1.07);
}

.review-empty {
    grid-column: 1/-1;
}

@media (max-width:1100px) {
    .review-statistics {
        grid-template-columns:
            repeat(3, minmax(0,1fr));
    }

    .review-grid {
        grid-template-columns:
            repeat(2, minmax(0,1fr));
    }
}

@media (max-width:760px) {
    .review-filters {
        grid-template-columns: 1fr;
    }

    .review-filter-actions {
        flex-direction: column;
    }

    .review-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@endsection
