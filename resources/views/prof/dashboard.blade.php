@extends('layouts.prof')

@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')
@section('breadcrumb', 'Vue d’ensemble')

@section('content')
@php
    $dashboardStats = [
        ['label' => 'Étudiants', 'value' => $studentsCount ?? 0, 'icon' => 'people-fill', 'tone' => 'is-blue'],
        ['label' => 'Cours publiés', 'value' => $coursesCount ?? 0, 'icon' => 'journal-richtext', 'tone' => 'is-green'],
        ['label' => 'Copies reçues', 'value' => $assignmentsCount ?? 0, 'icon' => 'file-earmark-text-fill', 'tone' => 'is-yellow'],
        ['label' => 'À corriger', 'value' => $pendingCount ?? 0, 'icon' => 'hourglass-split', 'tone' => 'is-purple'],
        ['label' => 'Lives', 'value' => $livesCount ?? 0, 'icon' => 'camera-video-fill', 'tone' => 'is-red'],
        ['label' => 'Absences', 'value' => $absencesCount ?? 0, 'icon' => 'person-x-fill', 'tone' => 'is-cyan'],
    ];

    $indicators = [
        [
            'label' => 'Copies corrigées',
            'value' => ($correctedCount ?? 0).' / '.($assignmentsCount ?? 0),
            'percentage' => min(max((int) ($correctionRate ?? 0), 0), 100),
            'color' => '#4ade80',
        ],
        [
            'label' => 'Taux de présence',
            'value' => (int) ($presenceRate ?? 0).'%',
            'percentage' => min(max((int) ($presenceRate ?? 0), 0), 100),
            'color' => '#60a5fa',
        ],
        [
            'label' => 'Moyenne des étudiants',
            'value' => number_format((float) ($averageGrade ?? 0), 1).'/20',
            'percentage' => min(max(((float) ($averageGrade ?? 0)) * 5, 0), 100),
            'color' => '#fbbf24',
        ],
    ];
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-stars"></i> Espace enseignant</span>
        <h1 class="pp-page-title">Bonjour, {{ auth()->user()->name }}</h1>
        <p class="pp-page-description">
            Retrouvez vos indicateurs pédagogiques, vos prochaines actions et l’activité récente de vos classes.
        </p>
    </div>

    <div class="pp-page-actions">
        <span class="pp-date-chip">
            <i class="bi bi-calendar3"></i>
            {{ now()->translatedFormat('d F Y') }}
        </span>
        <a href="{{ route('prof.schedule') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-calendar3-week"></i>
            Emploi du temps
        </a>
    </div>
</section>

<div class="pp-summary-grid">
    @foreach($dashboardStats as $stat)
        <article class="pp-summary-card {{ $stat['tone'] }}">
            <span class="pp-summary-icon"><i class="bi bi-{{ $stat['icon'] }}"></i></span>
            <span class="pp-summary-copy">
                <strong class="pp-summary-value">{{ $stat['value'] }}</strong>
                <span class="pp-summary-label">{{ $stat['label'] }}</span>
            </span>
        </article>
    @endforeach
</div>

<div class="pp-layout-two">
    <section class="pp-panel">
        <header class="pp-panel-head">
            <div class="pp-panel-title-wrap">
                <h2 class="pp-panel-title"><i class="bi bi-graph-up-arrow"></i> Suivi pédagogique</h2>
                <p class="pp-panel-subtitle">Synthèse calculée à partir de vos classes assignées.</p>
            </div>
            <span class="pp-panel-meta">Mise à jour automatique</span>
        </header>
        <div class="pp-panel-body">
            <div class="pp-progress-list">
                @foreach($indicators as $indicator)
                    <div class="pp-progress-row" style="--progress:{{ $indicator['percentage'] }}%;--progress-color:{{ $indicator['color'] }};">
                        <div class="pp-progress-top">
                            <span class="pp-progress-label">{{ $indicator['label'] }}</span>
                            <strong class="pp-progress-value">{{ $indicator['value'] }}</strong>
                        </div>
                        <div class="pp-progress-track" aria-hidden="true">
                            <div class="pp-progress-bar"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="pp-panel">
        <header class="pp-panel-head">
            <div class="pp-panel-title-wrap">
                <h2 class="pp-panel-title"><i class="bi bi-diagram-3-fill"></i> Mes affectations</h2>
                <p class="pp-panel-subtitle">Matières, niveaux et classes attribués.</p>
            </div>
            <span class="pp-panel-meta">{{ ($profAssignments ?? collect())->count() }}</span>
        </header>
        <div class="pp-panel-body">
            @forelse(($profAssignments ?? collect())->take(6) as $assignment)
                <div class="pp-path-row">
                    <span class="pp-path-icon"><i class="bi bi-journal-bookmark-fill"></i></span>
                    <span class="pp-path-copy">
                        <strong>{{ $assignment->subject?->name ?? 'Matière' }}</strong>
                        <span>{{ $assignment->level?->name ?? 'Niveau' }} · {{ $assignment->classRoom?->name ?? 'Classe' }}</span>
                    </span>
                </div>
            @empty
                <div class="pp-empty" style="min-height:160px;padding:18px 10px;">
                    <div>
                        <span class="pp-empty-icon"><i class="bi bi-inbox"></i></span>
                        <h3>Aucune affectation</h3>
                        <p>Les affectations ajoutées par l’administration apparaîtront ici.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</div>

<section class="pp-panel pp-section-gap">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-lightning-charge-fill"></i> Actions rapides</h2>
            <p class="pp-panel-subtitle">Accédez directement aux tâches les plus fréquentes.</p>
        </div>
    </header>
    <div class="pp-panel-body">
        <div class="pp-quick-grid">
            <a href="{{ route('prof.subjects.list') }}" class="pp-quick-link" style="--quick-color:#a78bfa;">
                <span class="pp-quick-icon"><i class="bi bi-journals"></i></span>
                <span class="pp-quick-copy"><strong>Mes matières</strong><span>Parcourir niveaux et classes</span></span>
            </a>
            <a href="{{ route('prof.devoir.create') }}" class="pp-quick-link" style="--quick-color:#4ade80;">
                <span class="pp-quick-icon"><i class="bi bi-file-earmark-plus-fill"></i></span>
                <span class="pp-quick-copy"><strong>Nouveau devoir</strong><span>Créer une activité à rendre</span></span>
            </a>
            <a href="{{ route('prof.assignments') }}" class="pp-quick-link" style="--quick-color:#fbbf24;">
                <span class="pp-quick-icon"><i class="bi bi-journal-check"></i></span>
                <span class="pp-quick-copy"><strong>Corriger les copies</strong><span>{{ $pendingCount ?? 0 }} copie(s) en attente</span></span>
            </a>
            <a href="{{ route('prof.absences') }}" class="pp-quick-link" style="--quick-color:#60a5fa;">
                <span class="pp-quick-icon"><i class="bi bi-person-check-fill"></i></span>
                <span class="pp-quick-copy"><strong>Faire l’appel</strong><span>Enregistrer les présences</span></span>
            </a>
        </div>
    </div>
</section>

<section class="pp-panel pp-section-gap">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-activity"></i> Activité récente</h2>
            <p class="pp-panel-subtitle">Dernières copies envoyées par vos étudiants.</p>
        </div>
        <a href="{{ route('prof.assignments') }}" class="adm-btn adm-btn-ghost adm-btn-sm">Tout afficher</a>
    </header>
    <div class="pp-panel-body">
        @forelse($recentSubmissions ?? [] as $submission)
            <div class="pp-activity-row">
                <span class="pp-activity-state" style="--state-color:{{ $submission->grade === null ? '#fbbf24' : '#4ade80' }};"></span>
                <span class="pp-activity-copy">
                    <strong>{{ $submission->user?->name ?? 'Étudiant' }} — {{ $submission->title }}</strong>
                    <span>{{ $submission->subject?->name ?? 'Matière' }} · {{ $submission->created_at?->diffForHumans() }}</span>
                </span>
                <span class="adm-badge {{ $submission->grade === null ? 'adm-badge-warning' : 'adm-badge-success' }}">
                    {{ $submission->grade === null ? 'À corriger' : 'Corrigée' }}
                </span>
            </div>
        @empty
            <div class="pp-empty" style="min-height:170px;">
                <div>
                    <span class="pp-empty-icon"><i class="bi bi-clock-history"></i></span>
                    <h3>Aucune activité récente</h3>
                    <p>Les nouvelles soumissions apparaîtront automatiquement ici.</p>
                </div>
            </div>
        @endforelse
    </div>
</section>
@endsection
