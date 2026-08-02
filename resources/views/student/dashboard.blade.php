@extends('layouts.student')

@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/student-dashboard-v2.css') }}">
@endpush

@section('content')
@php
    $student = auth()->user();
    $studentInitial = strtoupper(mb_substr($student->name ?? 'E', 0, 1));
    $pendingAssignments = max(($assignmentsSent ?? 0) - ($assignmentsCorrected ?? 0), 0);
    $completion = min(max((int) ($assignmentCompletion ?? 0), 0), 100);
    $correction = min(max((int) ($correctedPercent ?? 0), 0), 100);
    $attendance = min(max((int) ($presencePercent ?? 100), 0), 100);
    $studentAverage = (float) ($average ?? 0);
    $averagePercent = min(max((int) round($studentAverage * 5), 0), 100);

    $situationTone = ($totalAbsences ?? 0) <= 2
        ? 'success'
        : (($totalAbsences ?? 0) <= 4 ? 'warning' : 'danger');

    $quickActions = [
        [
            'route' => 'student.subjects.index',
            'icon' => 'bi-book',
            'title' => 'Mes matières',
            'description' => 'Accéder aux cours disponibles',
            'tone' => 'indigo',
        ],
        [
            'route' => 'student.schedule.index',
            'icon' => 'bi-calendar-week',
            'title' => 'Emploi du temps',
            'description' => 'Consulter les prochaines séances',
            'tone' => 'blue',
        ],
        [
            'route' => 'student.lives',
            'icon' => 'bi-camera-video',
            'title' => 'Lives',
            'description' => 'Rejoindre les cours en direct',
            'tone' => 'red',
        ],
        [
            'route' => 'student.assignments',
            'icon' => 'bi-file-earmark-arrow-up',
            'title' => 'Mes devoirs',
            'description' => 'Envoyer et suivre mes travaux',
            'tone' => 'green',
        ],
        [
            'route' => 'student.chats',
            'icon' => 'bi-chat-dots',
            'title' => 'Discussions',
            'description' => 'Poser une question au professeur',
            'tone' => 'purple',
        ],
        [
            'route' => 'student.absences',
            'icon' => 'bi-calendar-x',
            'title' => 'Mes absences',
            'description' => 'Consulter mon suivi de présence',
            'tone' => 'orange',
        ],
    ];
@endphp

<div class="sd-dashboard">
    <section class="sd-welcome-card">
        <div class="sd-welcome-main">
            <div class="sd-avatar" aria-hidden="true">{{ $studentInitial }}</div>

            <div class="sd-welcome-copy">
                <p class="sd-eyebrow">Espace étudiant</p>
                <h1>Bonjour, {{ $student->name }}</h1>
                <p class="sd-date">
                    <i class="bi bi-calendar3"></i>
                    {{ ucfirst(now()->locale('fr')->translatedFormat('l d F Y')) }}
                </p>

            </div>
        </div>

        <div class="sd-welcome-actions">
            <a href="{{ route('student.subjects.index') }}" class="sd-btn sd-btn-primary">
                <i class="bi bi-book"></i>
                Voir mes matières
            </a>
            <a href="{{ route('student.schedule.index') }}" class="sd-btn sd-btn-secondary">
                <i class="bi bi-calendar-week"></i>
                Emploi du temps
            </a>
        </div>
    </section>

    <section class="sd-stat-grid" aria-label="Résumé de votre activité">
        <article class="sd-stat-card sd-tone-indigo">
            <div class="sd-stat-icon"><i class="bi bi-journal-bookmark"></i></div>
            <div>
                <span class="sd-stat-label">Cours disponibles</span>
                <strong>{{ $coursesCount ?? 0 }}</strong>
            </div>
        </article>

        <article class="sd-stat-card sd-tone-red">
            <div class="sd-stat-icon"><i class="bi bi-broadcast"></i></div>
            <div>
                <span class="sd-stat-label">Lives</span>
                <strong>{{ $livesCount ?? 0 }}</strong>
            </div>
        </article>

        <article class="sd-stat-card sd-tone-green">
            <div class="sd-stat-icon"><i class="bi bi-file-earmark-check"></i></div>
            <div>
                <span class="sd-stat-label">Devoirs envoyés</span>
                <strong>{{ $assignmentsSent ?? 0 }}</strong>
            </div>
        </article>

        <article class="sd-stat-card sd-tone-amber">
            <div class="sd-stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <div>
                <span class="sd-stat-label">Moyenne générale</span>
                <strong>{{ number_format($studentAverage, 1) }}<small>/20</small></strong>
            </div>
        </article>
    </section>

    <div class="sd-main-grid">
        <section class="sd-panel sd-actions-panel">
            <div class="sd-panel-header">
                <div>
                    <p class="sd-panel-kicker">Navigation</p>
                    <h2>Accès rapide</h2>
                </div>
            </div>

            <div class="sd-action-grid">
                @foreach($quickActions as $action)
                    <a href="{{ route($action['route']) }}" class="sd-action-card sd-action-{{ $action['tone'] }}">
                        <span class="sd-action-icon"><i class="bi {{ $action['icon'] }}"></i></span>
                        <span class="sd-action-copy">
                            <strong>{{ $action['title'] }}</strong>
                            <small>{{ $action['description'] }}</small>
                        </span>
                        <i class="bi bi-chevron-right sd-action-arrow"></i>
                    </a>
                @endforeach
            </div>
        </section>

        <aside class="sd-panel sd-progress-panel">
            <div class="sd-panel-header">
                <div>
                    <p class="sd-panel-kicker">Suivi</p>
                    <h2>Ma progression</h2>
                </div>
                <span class="sd-score-badge">{{ $engagement ?? 0 }}%</span>
            </div>

            <div class="sd-progress-list">
                <div class="sd-progress-item">
                    <div class="sd-progress-meta">
                        <span>Devoirs réalisés</span>
                        <strong>{{ $completion }}%</strong>
                    </div>
                    <div class="sd-progress-track"><span style="width: {{ $completion }}%"></span></div>
                </div>

                <div class="sd-progress-item">
                    <div class="sd-progress-meta">
                        <span>Devoirs corrigés</span>
                        <strong>{{ $correction }}%</strong>
                    </div>
                    <div class="sd-progress-track sd-progress-purple"><span style="width: {{ $correction }}%"></span></div>
                </div>

                <div class="sd-progress-item">
                    <div class="sd-progress-meta">
                        <span>Présence</span>
                        <strong>{{ $attendance }}%</strong>
                    </div>
                    <div class="sd-progress-track sd-progress-green"><span style="width: {{ $attendance }}%"></span></div>
                </div>

                <div class="sd-progress-item">
                    <div class="sd-progress-meta">
                        <span>Moyenne</span>
                        <strong>{{ number_format($studentAverage, 1) }}/20</strong>
                    </div>
                    <div class="sd-progress-track sd-progress-amber"><span style="width: {{ $averagePercent }}%"></span></div>
                </div>
            </div>

            <div class="sd-progress-summary">
                <span><b>{{ $assignmentsCorrected ?? 0 }}</b> corrigés</span>
                <span><b>{{ $pendingAssignments }}</b> en attente</span>
            </div>
        </aside>
    </div>

    <div class="sd-content-grid">
        <section class="sd-panel">
            <div class="sd-panel-header sd-panel-header-link">
                <div>
                    <p class="sd-panel-kicker">Apprentissage</p>
                    <h2>Derniers cours</h2>
                </div>
                <a href="{{ route('student.subjects.index') }}">Tout afficher <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="sd-list">
                @forelse(($recentCourses ?? collect()) as $course)
                    <a href="{{ route('student.course.show', $course->id) }}" class="sd-list-row">
                        <span class="sd-list-icon sd-list-icon-course"><i class="bi bi-play-circle"></i></span>
                        <span class="sd-list-copy">
                            <strong>{{ \Illuminate\Support\Str::limit($course->title ?? 'Cours', 52) }}</strong>
                            <small>
                                @if($course->subject)
                                    {{ $course->subject->name }} ·
                                @endif
                                Ajouté {{ optional($course->created_at)->diffForHumans() }}
                            </small>
                        </span>
                        <i class="bi bi-chevron-right sd-list-arrow"></i>
                    </a>
                @empty
                    <div class="sd-empty-state">
                        <i class="bi bi-journal-x"></i>
                        <strong>Aucun cours récent</strong>
                        <p>Les nouveaux cours apparaîtront ici.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="sd-panel">
            <div class="sd-panel-header sd-panel-header-link">
                <div>
                    <p class="sd-panel-kicker">Travail personnel</p>
                    <h2>Derniers devoirs</h2>
                </div>
                <a href="{{ route('student.assignments') }}">Tout afficher <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="sd-list">
                @forelse(($recentAssignments ?? collect()) as $assignment)
                    <a href="{{ route('student.assignments') }}" class="sd-list-row">
                        <span class="sd-list-icon sd-list-icon-assignment"><i class="bi bi-file-earmark-text"></i></span>
                        <span class="sd-list-copy">
                            <strong>{{ \Illuminate\Support\Str::limit($assignment->title ?? 'Devoir', 52) }}</strong>
                            <small>
                                Envoyé {{ optional($assignment->created_at)->diffForHumans() }}
                            </small>
                        </span>
                        @if(!is_null($assignment->grade))
                            <span class="sd-grade-badge">{{ number_format((float) $assignment->grade, 1) }}/20</span>
                        @else
                            <span class="sd-pending-badge">En attente</span>
                        @endif
                    </a>
                @empty
                    <div class="sd-empty-state">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        <strong>Aucun devoir envoyé</strong>
                        <p>Vos devoirs soumis apparaîtront ici.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="sd-status-card sd-status-{{ $situationTone }}">
        <div class="sd-status-icon"><i class="bi bi-shield-check"></i></div>
        <div class="sd-status-copy">
            <p class="sd-panel-kicker">Présence et assiduité</p>
            <h2>{{ $situation ?? 'Situation normale' }}</h2>
            <p>
                Vous avez <strong>{{ $totalAbsences ?? 0 }}</strong>
                {{ ($totalAbsences ?? 0) > 1 ? 'absences enregistrées' : 'absence enregistrée' }}.
            </p>
        </div>
        <a href="{{ route('student.absences') }}" class="sd-btn sd-btn-secondary">
            Voir le détail
            <i class="bi bi-arrow-right"></i>
        </a>
    </section>
</div>
@endsection
