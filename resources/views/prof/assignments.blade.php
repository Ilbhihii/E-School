@extends('layouts.prof')

@section('title', 'Copies des étudiants')
@section('page_title', 'Copies des étudiants')
@section('breadcrumb', 'Correction des devoirs')

@section('content')
@php
    $totalSubmissions = $assignments->count();
    $pendingSubmissions = $assignments->whereNull('grade')->count();
    $correctedSubmissions = $totalSubmissions - $pendingSubmissions;
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-journal-check"></i> Évaluation</span>
        <h1 class="pp-page-title">Copies des étudiants</h1>
        <p class="pp-page-description">
            Consultez les fichiers remis, attribuez un niveau d’acquisition et ajoutez un commentaire pédagogique.
        </p>
    </div>

    <div class="pp-page-actions">
        <a href="{{ route('prof.devoir.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-file-earmark-text"></i> Mes devoirs
        </a>
    </div>
</section>

<div class="pp-summary-grid">
    <article class="pp-summary-card is-blue">
        <span class="pp-summary-icon"><i class="bi bi-inbox-fill"></i></span>
        <span class="pp-summary-copy"><strong class="pp-summary-value">{{ $totalSubmissions }}</strong><span class="pp-summary-label">Soumissions reçues</span></span>
    </article>
    <article class="pp-summary-card is-yellow">
        <span class="pp-summary-icon"><i class="bi bi-hourglass-split"></i></span>
        <span class="pp-summary-copy"><strong class="pp-summary-value">{{ $pendingSubmissions }}</strong><span class="pp-summary-label">À corriger</span></span>
    </article>
    <article class="pp-summary-card is-green">
        <span class="pp-summary-icon"><i class="bi bi-check2-circle"></i></span>
        <span class="pp-summary-copy"><strong class="pp-summary-value">{{ $correctedSubmissions }}</strong><span class="pp-summary-label">Corrigées</span></span>
    </article>
</div>

@if($assignments->isNotEmpty())
    <div class="pp-toolbar">
        <div class="pp-search">
            <i class="bi bi-search"></i>
            <input type="search" id="submissionSearch" class="adm-form-control" placeholder="Rechercher un étudiant ou un devoir..." autocomplete="off">
        </div>
        <select id="submissionStatus" class="adm-form-select pp-filter-select" aria-label="Filtrer par statut">
            <option value="all">Tous les statuts</option>
            <option value="pending">Non corrigées</option>
            <option value="corrected">Corrigées</option>
        </select>
    </div>
@endif

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-list-check"></i> Soumissions</h2>
            <p class="pp-panel-subtitle">Chaque correction est enregistrée individuellement.</p>
        </div>
        <span class="pp-panel-meta" id="submissionVisibleCount">{{ $totalSubmissions }} copie(s)</span>
    </header>

    <div class="pp-panel-body">
        <div class="pp-submission-list" id="submissionList">
            @forelse($assignments as $assignment)
                @php
                    $studentName = $assignment->user?->name ?? 'Étudiant inconnu';
                    $initials = collect(preg_split('/\s+/', trim($studentName)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                    $statusKey = $assignment->grade === null ? 'pending' : 'corrected';
                    $statusClass = match ($assignment->grade) {
                        20 => 'adm-badge-success',
                        10 => 'adm-badge-warning',
                        0 => 'adm-badge-danger',
                        default => 'adm-badge-gray',
                    };
                    $statusLabel = match ($assignment->grade) {
                        20 => 'Acquis',
                        10 => 'En cours',
                        0 => 'Non acquis',
                        default => 'À corriger',
                    };
                @endphp

                <article
                    class="pp-submission-card pp-submission-item"
                    data-status="{{ $statusKey }}"
                    data-search="{{ Str::lower($studentName.' '.$assignment->title.' '.($assignment->subject?->name ?? '')) }}"
                >
                    <div class="pp-submission-summary">
                        <div class="pp-student-main">
                            <span class="pp-student-avatar">{{ $initials ?: 'ET' }}</span>
                            <div class="pp-student-copy">
                                <strong class="pp-student-name">{{ $studentName }}</strong>
                                <span class="pp-student-assignment">
                                    {{ $assignment->title }}
                                    @if($assignment->subject?->name)
                                        · {{ $assignment->subject->name }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="pp-submission-actions">
                            <span class="adm-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            @if($assignment->file)
                                <a href="{{ asset('storage/'.$assignment->file) }}" target="_blank" rel="noopener" class="adm-btn adm-btn-primary adm-btn-sm">
                                    <i class="bi bi-eye"></i> Voir la copie
                                </a>
                            @else
                                <span class="adm-badge adm-badge-gray"><i class="bi bi-file-earmark-x"></i> Aucun fichier</span>
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('prof.grade') }}" class="pp-correction">
                        @csrf
                        <input type="hidden" name="id" value="{{ $assignment->id }}">

                        <div class="pp-correction-grid">
                            <div>
                                <div class="pp-status-options" role="radiogroup" aria-label="Niveau d’acquisition">
                                    <label class="pp-status-option" style="--option-color:#4ade80;">
                                        <input type="radio" name="status" value="acquis" required {{ $assignment->grade === 20 ? 'checked' : '' }}>
                                        <span><i class="bi bi-check-circle-fill"></i> Acquis</span>
                                    </label>
                                    <label class="pp-status-option" style="--option-color:#fbbf24;">
                                        <input type="radio" name="status" value="en_cours" {{ $assignment->grade === 10 ? 'checked' : '' }}>
                                        <span><i class="bi bi-arrow-repeat"></i> En cours</span>
                                    </label>
                                    <label class="pp-status-option" style="--option-color:#f87171;">
                                        <input type="radio" name="status" value="non_acquis" {{ $assignment->grade === 0 ? 'checked' : '' }}>
                                        <span><i class="bi bi-x-circle-fill"></i> Non acquis</span>
                                    </label>
                                </div>

                                <label for="comment-{{ $assignment->id }}" class="visually-hidden">Commentaire de correction</label>
                                <textarea
                                    id="comment-{{ $assignment->id }}"
                                    name="comment"
                                    class="adm-form-control"
                                    rows="2"
                                    maxlength="2000"
                                    placeholder="Ajouter un commentaire de correction..."
                                >{{ $assignment->comment }}</textarea>
                            </div>

                            <button type="submit" class="adm-btn adm-btn-success">
                                <i class="bi bi-check-lg"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </article>
            @empty
                <div class="pp-empty">
                    <div>
                        <span class="pp-empty-icon"><i class="bi bi-inbox"></i></span>
                        <h3>Aucune soumission</h3>
                        <p>Les copies envoyées par les étudiants de vos classes apparaîtront ici.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="pp-no-results" id="submissionEmpty">
            <i class="bi bi-search fs-2 d-block mb-2"></i>
            Aucune copie ne correspond aux filtres sélectionnés.
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('submissionSearch');
    const status = document.getElementById('submissionStatus');
    const items = Array.from(document.querySelectorAll('.pp-submission-item'));
    const empty = document.getElementById('submissionEmpty');
    const counter = document.getElementById('submissionVisibleCount');

    if (!items.length || !empty || !counter) return;

    function applyFilters() {
        const query = (search?.value || '').trim().toLocaleLowerCase('fr');
        const selectedStatus = status?.value || 'all';
        let visible = 0;

        items.forEach(function (item) {
            const matchesSearch = (item.dataset.search || '').includes(query);
            const matchesStatus = selectedStatus === 'all' || item.dataset.status === selectedStatus;
            const show = matchesSearch && matchesStatus;
            item.style.display = show ? '' : 'none';
            if (show) visible += 1;
        });

        empty.style.display = visible ? 'none' : 'block';
        counter.textContent = visible + ' copie(s)';
    }

    search?.addEventListener('input', applyFilters);
    status?.addEventListener('change', applyFilters);
});
</script>
@endpush
@endsection
