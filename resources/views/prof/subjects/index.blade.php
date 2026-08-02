@extends('layouts.prof')

@section('title', 'Mes matières')
@section('page_title', 'Matières')
@section('breadcrumb', 'Navigation pédagogique')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-diagram-3"></i> Parcours pédagogique</span>
        <h1 class="pp-page-title">Mes matières</h1>
        <p class="pp-page-description">
            Sélectionnez une matière pour accéder aux niveaux, aux classes, aux cours, aux lives et aux devoirs associés.
        </p>
    </div>

    <div class="pp-page-actions">
        <span class="pp-soft-chip"><i class="bi bi-journals"></i> {{ $subjects->count() }} matière(s)</span>
    </div>
</section>

@if($subjects->isNotEmpty())
    <div class="pp-toolbar">
        <div class="pp-search">
            <i class="bi bi-search"></i>
            <input
                type="search"
                id="subjectSearch"
                class="adm-form-control"
                placeholder="Rechercher une matière..."
                autocomplete="off"
            >
        </div>
        <span class="pp-panel-meta">Matière → Niveau → Classe</span>
    </div>
@endif

<div class="pp-subject-grid" id="subjectsGrid">
    @forelse($subjects as $subject)
        @php
            $icons = ['book', 'calculator', 'flask', 'translate', 'globe2', 'palette', 'music-note-beamed', 'cpu', 'graph-up', 'pencil-square', 'journal-text', 'stars'];
            $themes = [
                ['#a78bfa', 'linear-gradient(135deg,#6d28d9,#a78bfa)'],
                ['#4ade80', 'linear-gradient(135deg,#047857,#34d399)'],
                ['#fbbf24', 'linear-gradient(135deg,#b45309,#fbbf24)'],
                ['#60a5fa', 'linear-gradient(135deg,#1d4ed8,#60a5fa)'],
                ['#f87171', 'linear-gradient(135deg,#b91c1c,#f87171)'],
                ['#67e8f9', 'linear-gradient(135deg,#0e7490,#22d3ee)'],
            ];
            [$color, $gradient] = $themes[$loop->index % count($themes)];
            $levelCount = (int) ($subject->assigned_levels_count ?? 0);
            $classCount = (int) ($subject->assigned_classes_count ?? 0);
        @endphp

        <div class="pp-subject-item" data-name="{{ Str::lower($subject->name) }}">
            <a
                href="{{ route('prof.subjects.levels', $subject) }}"
                class="pp-subject-card"
                style="--subject-color:{{ $color }};--subject-gradient:{{ $gradient }};"
            >
                <div class="pp-subject-cover">
                    <span class="pp-subject-icon"><i class="bi bi-{{ $icons[$loop->index % count($icons)] }}"></i></span>
                    <span class="pp-subject-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="pp-subject-body">
                    <h2>{{ $subject->name }}</h2>
                    <div class="pp-subject-meta">
                        <span class="subject-chip"><i class="bi bi-layers"></i> {{ $levelCount }} niveau(x)</span>
                        <span class="subject-chip"><i class="bi bi-people"></i> {{ $classCount }} classe(s)</span>
                    </div>
                    <span class="pp-subject-action">
                        <span>Explorer les niveaux</span>
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </div>
            </a>
        </div>
    @empty
        <div class="pp-panel" style="grid-column:1/-1;">
            <div class="pp-empty">
                <div>
                    <span class="pp-empty-icon"><i class="bi bi-inbox"></i></span>
                    <h3>Aucune matière assignée</h3>
                    <p>Contactez l’administration pour vérifier vos affectations pédagogiques.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="pp-no-results" id="subjectsEmpty">
    <i class="bi bi-search fs-2 d-block mb-2"></i>
    Aucune matière ne correspond à votre recherche.
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('subjectSearch');
    const empty = document.getElementById('subjectsEmpty');

    if (!search || !empty) return;

    search.addEventListener('input', function () {
        const query = this.value.trim().toLocaleLowerCase('fr');
        let visible = 0;

        document.querySelectorAll('.pp-subject-item').forEach(function (item) {
            const show = (item.dataset.name || '').includes(query);
            item.style.display = show ? '' : 'none';
            if (show) visible += 1;
        });

        empty.style.display = visible ? 'none' : 'block';
    });
});
</script>
@endpush
@endsection
