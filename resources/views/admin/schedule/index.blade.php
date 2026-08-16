@extends('layouts.admin')

@section('title', 'Emploi du temps')
@section('page_title', 'Emploi du temps')
@section('breadcrumb', 'Emploi du temps')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-schedule-v4.css') }}?v={{ file_exists(public_path('css/admin-schedule-v4.css')) ? filemtime(public_path('css/admin-schedule-v4.css')) : time() }}">
@endpush

@section('content')
@php
    $dayNames = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche',
    ];

    $displayMode = request('mode') === 'final' ? 'final' : 'build';

    $scheduleFilters = request()->only([
        'subject_id',
        'level_id',
        'class_id',
        'prof_id',
    ]);

    $modeQuery = request()->query();
    unset($modeQuery['mode']);

    $buildModeUrl = route(
        'admin.schedule.index',
        array_merge($modeQuery, ['mode' => 'build'])
    );

    $finalModeUrl = route(
        'admin.schedule.index',
        array_merge($modeQuery, ['mode' => 'final'])
    );

    $scheduleEditData = $schedules->mapWithKeys(function ($schedule) {
        return [$schedule->id => [
            'id' => $schedule->id,
            'subject_id' => $schedule->subject_id,
            'level_id' => $schedule->level_id ?: optional($schedule->classRoom)->level_id,
            'class_id' => $schedule->class_id,
            'slot_code' => $schedule->slot_code,
            'prof_id' => $schedule->prof_id,
            'day_of_week' => $schedule->day_of_week,
            'start_time' => optional($schedule->start_time)->format('H:i'),
            'end_time' => optional($schedule->end_time)->format('H:i'),
            'recurrence' => $schedule->recurrence ?: 'weekly',
            'valid_from' => optional($schedule->valid_from ?: $schedule->date)->format('Y-m-d'),
            'status' => $schedule->status ?: 'active',
        ]];
    });

    $activeFilterCount = collect($scheduleFilters)->filter(function ($value) {
        return $value !== null && $value !== '';
    })->count();

    /*
     * Vue finale demandée par l'équipe :
     * lignes = plages horaires, colonnes = jours, cartes = séances.
     * Seules les séances actives apparaissent dans cette synthèse.
     */
    $finalSchedules = $schedules
        ->filter(function ($schedule) {
            return ($schedule->status ?: 'active') === 'active';
        })
        ->values();

    $finalTimeRows = $finalSchedules
        ->map(function ($schedule) {
            $start = optional($schedule->start_time)->format('H:i') ?: '--:--';
            $end = optional($schedule->end_time)->format('H:i') ?: '--:--';

            return [
                'key' => $start . '|' . $end,
                'start' => $start,
                'end' => $end,
                'label' => $start . ' – ' . $end,
            ];
        })
        ->unique('key')
        ->sortBy(function ($row) {
            return $row['start'] . '|' . $row['end'];
        })
        ->values();

    $finalMatrix = $finalSchedules->groupBy(function ($schedule) {
        $start = optional($schedule->start_time)->format('H:i') ?: '--:--';
        $end = optional($schedule->end_time)->format('H:i') ?: '--:--';

        return (int) $schedule->day_of_week
            . '|'
            . $start
            . '|'
            . $end;
    });

    $usedProfessorIds = $finalSchedules
        ->flatMap(function ($schedule) use ($scheduleProfessors) {
            return collect($scheduleProfessors[$schedule->id] ?? [])
                ->pluck('id');
        })
        ->filter()
        ->unique()
        ->values();

    $usedProfessors = $teachers
        ->filter(function ($teacher) use ($usedProfessorIds) {
            return $usedProfessorIds->contains((int) $teacher->id);
        })
        ->values();

    $finalStats = [
        'sessions' => $finalSchedules->count(),
        'professors' => $usedProfessors->count(),
        'classes' => $finalSchedules->pluck('class_id')->filter()->unique()->count(),
        'days' => $finalSchedules->pluck('day_of_week')->filter()->unique()->count(),
    ];
@endphp

<div class="schedule-page">
    <section class="schedule-hero">
        <div class="schedule-hero-copy">
            <span class="schedule-kicker"><i class="bi bi-stars"></i> Organisation pédagogique</span>
            <h2>
                <span class="schedule-title-icon"><i class="bi bi-calendar3-week"></i></span>
                Emploi du temps des classes
            </h2>
            <p>Planifiez le parcours complet Matière → Niveau → Classe → Créneau.</p>
        </div>

        <div class="schedule-hero-actions schedule-view-switcher">
            <a
                href="{{ $buildModeUrl }}"
                class="adm-btn {{ $displayMode === 'build' ? 'adm-btn-primary' : 'adm-btn-ghost' }}"
            >
                <i class="bi bi-tools"></i> Construire le planning
            </a>

            <a
                href="{{ $finalModeUrl }}"
                class="adm-btn {{ $displayMode === 'final' ? 'adm-btn-primary' : 'adm-btn-ghost' }}"
            >
                <i class="bi bi-grid-3x3-gap-fill"></i> Vue planning final
            </a>

            @if($displayMode === 'build')
                <button type="button" class="adm-btn adm-btn-primary schedule-primary-action" onclick="resetPlanningForm(true)">
                    <i class="bi bi-plus-lg"></i> Nouvelle séance
                </button>
            @else
                <button type="button" class="adm-btn adm-btn-ghost" onclick="window.print()">
                    <i class="bi bi-printer"></i> Imprimer
                </button>
            @endif
        </div>
    </section>

    @if(session('success'))
        <div class="adm-alert adm-alert-success">
            <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="adm-alert adm-alert-danger">
            <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
            <ul style="margin:0;padding-left:1.1rem">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="adm-card schedule-card">
        <div class="adm-card-header schedule-card-head">
            <div class="schedule-card-title-wrap">
                <span class="schedule-card-icon is-violet"><i class="bi bi-funnel"></i></span>
                <div>
                    <h3>Filtrer l’emploi du temps</h3>
                    <p>Affinez les résultats par matière, niveau et classe.</p>
                </div>
            </div>

            @if($activeFilterCount > 0)
                <span class="schedule-filter-count"><i class="bi bi-sliders"></i> {{ $activeFilterCount }} filtre(s) actif(s)</span>
            @endif
        </div>
        <div class="adm-card-body schedule-card-body">
            <form method="GET" action="{{ route('admin.schedule.index') }}" class="schedule-filter-grid">
                <input type="hidden" name="mode" value="{{ $displayMode }}">
                <div class="adm-form-group mb-0">
                    <label class="adm-form-label">Matière</label>
                    <select
                        name="subject_id"
                        id="scheduleFilterSubject"
                        class="adm-form-select"
                    >
                        <option value="">Toutes</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ (string) request('subject_id') === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="adm-form-group mb-0">
                    <label class="adm-form-label">Niveau</label>
                    <select
                        name="level_id"
                        id="scheduleFilterLevel"
                        class="adm-form-select"
                        data-selected="{{ request('level_id') }}"
                        {{ request('subject_id') ? '' : 'disabled' }}
                    >
                        <option value="">Tous</option>
                    </select>
                </div>

                <div class="adm-form-group mb-0">
                    <label class="adm-form-label">Classe pédagogique</label>
                    <select
                        name="class_id"
                        id="scheduleFilterClass"
                        class="adm-form-select"
                        data-selected="{{ request('class_id') }}"
                        {{ request('level_id') ? '' : 'disabled' }}
                    >
                        <option value="">Toutes</option>
                    </select>
                </div>

                <div class="adm-form-group mb-0">
                    <label class="adm-form-label">Professeur</label>
                    <select name="prof_id" class="adm-form-select">
                        <option value="">Tous</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ (string) request('prof_id') === (string) $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="schedule-filter-actions">
                    <button class="adm-btn adm-btn-primary" type="submit"><i class="bi bi-search"></i> Filtrer</button>
                    <a href="{{ route('admin.schedule.index', ['mode' => $displayMode]) }}" class="adm-btn adm-btn-ghost schedule-reset-button" title="Réinitialiser les filtres"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </section>

    <div class="schedule-build-mode" style="{{ $displayMode === 'build' ? '' : 'display:none;' }}">
    <x-schedule-slot-matrix
        :items="$schedules"
        title="Organisation des créneaux"
        :show-teacher="false"
    />

    <div class="schedule-main-grid">
        <section class="adm-card schedule-card" id="planningFormCard">
            <div class="adm-card-header schedule-card-head schedule-table-meta">
                <div class="schedule-card-title-wrap">
                    <span class="schedule-card-icon"><i class="bi bi-calendar-plus"></i></span>
                    <div>
                        <h3 id="planningFormTitle">Nouvelle séance</h3>
                        <p>Complétez le parcours pédagogique et l’organisation horaire.</p>
                    </div>
                </div>
                <span class="schedule-count-badge"><i class="bi bi-stopwatch"></i> Durée personnalisable</span>
            </div>

            <div class="adm-card-body schedule-card-body">
                <div class="schedule-form-intro">
                    <i class="bi bi-info-circle"></i>
                    <span>Sélectionnez la matière, le niveau et la classe, puis choisissez le créneau.</span>
                </div>

                <form method="POST" action="{{ route('admin.schedule.store') }}" id="planningForm">
                    @csrf
                    <input type="hidden" name="_method" id="planningMethod" value="PUT" disabled>

                    <div class="schedule-form-sections">
                        <section class="schedule-form-panel">
                            <div class="schedule-panel-head">
                                <span class="schedule-panel-number">01</span>
                                <div>
                                    <h5>Parcours pédagogique</h5>
                                    <p>Matière → niveau → classe → créneau</p>
                                </div>
                            </div>

                            <div class="schedule-path-preview">
                                <strong id="pathSubject">Matière</strong>
                                <i class="bi bi-chevron-right"></i>
                                <strong id="pathLevel">Niveau</strong>
                                <i class="bi bi-chevron-right"></i>
                                <strong id="pathClass">Classe</strong>
                                <i class="bi bi-chevron-right"></i>
                                <strong id="pathSlot">Créneau</strong>
                            </div>

                            <div class="schedule-form-grid">
                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Matière <span class="schedule-required">*</span></label>
                                    <select name="subject_id" id="planningSubject" class="adm-form-select" required>
                                        <option value="">Choisir</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ (string) old('subject_id') === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Niveau <span class="schedule-required">*</span></label>
                                    <select name="level_id" id="planningLevel" class="adm-form-select" required disabled>
                                        <option value="">Choisir un niveau</option>
                                    </select>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Classe pédagogique <span class="schedule-required">*</span></label>
                                    <select name="class_id" id="planningClass" class="adm-form-select" required disabled>
                                        <option value="">Choisir le niveau</option>
                                    </select>
                                    <small class="schedule-help">Exemple : Débutant, Intermédiaire ou Avancé.</small>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">
                                        Créneau / groupe
                                        <span class="schedule-required">*</span>
                                    </label>

                                    <select
                                        name="slot_code"
                                        id="planningSlotCode"
                                        class="adm-form-select"
                                        required
                                        disabled
                                    >
                                        <option value="">
                                            Choisissez d’abord la classe
                                        </option>
                                    </select>

                                    <small class="schedule-help">
                                        Exemple :
                                        Débutant → D1, D2, D3, D4 ·
                                        Intermédiaire → I1, I2, I3, I4 ·
                                        Avancé → A1, A2, A3, A4.
                                    </small>
                                </div>

                            </div>
                        </section>

                        <section class="schedule-form-panel is-time">
                            <div class="schedule-panel-head">
                                <span class="schedule-panel-number">02</span>
                                <div>
                                    <h5>Créneau</h5>
                                    <p>Jour, horaires, répétition et statut</p>
                                </div>
                            </div>
                            <div class="schedule-form-grid">
                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Jour <span class="schedule-required">*</span></label>
                                    <select name="day_of_week" id="planningDay" class="adm-form-select" required>
                                        @foreach($dayNames as $dayNumber => $dayName)
                                            <option value="{{ $dayNumber }}" {{ (string) old('day_of_week', 7) === (string) $dayNumber ? 'selected' : '' }}>{{ $dayName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Répétition <span class="schedule-required">*</span></label>
                                    <select name="recurrence" id="planningRecurrence" class="adm-form-select" required>
                                        <option value="weekly" {{ old('recurrence', 'weekly') === 'weekly' ? 'selected' : '' }}>Toutes les semaines</option>
                                        <option value="once" {{ old('recurrence') === 'once' ? 'selected' : '' }}>Une seule fois</option>
                                    </select>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Heure de début <span class="schedule-required">*</span></label>
                                    <input type="time" name="start_time" id="planningStartTime" value="{{ old('start_time', '09:00') }}" class="adm-form-input" step="300" required>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Heure de fin <span class="schedule-required">*</span></label>
                                    <input type="time" name="end_time" id="planningEndTime" value="{{ old('end_time', '10:30') }}" class="adm-form-input" step="300" required>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">À partir du <span class="schedule-required">*</span></label>
                                    <input type="date" name="valid_from" id="planningValidFrom" value="{{ old('valid_from', now()->format('Y-m-d')) }}" class="adm-form-input" required>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Statut</label>
                                    <select name="status" id="planningStatus" class="adm-form-select">
                                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Désactivée</option>
                                    </select>
                                </div>
                            </div>

                            <div class="schedule-duration" id="planningDurationInfo">
                                <i class="bi bi-stopwatch"></i>
                                <div>
                                    <strong>Durée personnalisable</strong><br>
                                    <span id="planningDurationPreview">Choisissez l’heure de début et l’heure de fin.</span>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="schedule-form-footer">
                        <span class="schedule-form-note">
                            <i class="bi bi-info-circle"></i>
                            Tous les champs marqués d’un astérisque sont obligatoires.
                        </span>

                        <div class="schedule-form-buttons">
                            <button type="button" class="adm-btn adm-btn-ghost" id="planningCancelButton" onclick="resetPlanningForm()" style="display:none">
                                <i class="bi bi-x-lg"></i> Annuler
                            </button>
                            <button type="submit" class="adm-btn adm-btn-primary schedule-submit-button" id="planningSubmitButton">
                                <i class="bi bi-check-lg"></i> Ajouter au planning
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="adm-card schedule-card schedule-table-card">
            <div class="adm-card-header schedule-card-head schedule-table-meta">
                <div class="schedule-card-title-wrap">
                    <span class="schedule-card-icon is-green"><i class="bi bi-calendar-week"></i></span>
                    <div>
                        <h3>Emploi du temps</h3>
                        <p>Toutes les séances correspondant aux filtres sélectionnés.</p>
                    </div>
                </div>
                <span class="schedule-count-badge"><i class="bi bi-list-check"></i> {{ $schedules->count() }} séance(s)</span>
            </div>
            <div class="adm-card-body p-0">
                <div class="adm-table-wrap schedule-table-wrap">
                    <table class="adm-table schedule-table">
                        <thead>
                            <tr>
                                <th>Créneau</th>
                                <th>Parcours</th>
                                <th>Répétition</th>
                                <th>Statut</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $schedule)
                                <tr>
                                    <td data-label="Créneau">
                                        <div class="schedule-time-block">
                                            <span class="schedule-day-icon"><i class="bi bi-clock"></i></span>
                                            <div>
                                                <div class="schedule-day-name">
                                                    @if($schedule->slot_code)
                                                        <span class="schedule-slot-code">
                                                            {{ $schedule->slot_code }}
                                                        </span>
                                                    @endif
                                                    {{ $schedule->day_label }}
                                                </div>
                                                <small class="schedule-cell-muted">{{ $schedule->time_range_label }} · {{ $schedule->duration_label }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Parcours" class="schedule-route-cell">
                                        <strong>{{ optional($schedule->subjectModel)->name ?: $schedule->subject }}</strong>
                                        <small class="schedule-cell-muted">{{ optional($schedule->level)->name ?: optional(optional($schedule->classRoom)->level)->name ?: '-' }} → {{ optional($schedule->classRoom)->name ?: '-' }} @if($schedule->slot_code) → {{ $schedule->slot_code }} @endif</small>
                                        <small class="schedule-cell-muted" style="display:block;margin-top:3px;"><i class="bi bi-diagram-3"></i> Matière → Niveau → Classe → Créneau</small>
                                    </td>
                                    <td data-label="Répétition">
                                        @if(($schedule->recurrence ?: 'once') === 'weekly')
                                            <span class="adm-badge adm-badge-info">Chaque semaine</span><br>
                                            <small class="schedule-cell-muted">Depuis {{ optional($schedule->valid_from)->format('d/m/Y') ?: '-' }}</small>
                                        @else
                                            <span class="adm-badge">Une fois</span><br>
                                            <small class="schedule-cell-muted">{{ optional($schedule->date)->format('d/m/Y') ?: '-' }}</small>
                                        @endif
                                    </td>
                                    <td data-label="Statut"><span class="schedule-status {{ $schedule->status ?: 'active' }}">{{ ($schedule->status ?: 'active') === 'active' ? 'Active' : 'Désactivée' }}</span></td>
                                    <td data-label="Actions">
                                        <div class="schedule-row-actions">
                                            <button type="button" class="adm-btn adm-btn-ghost adm-btn-sm" onclick="editPlanning({{ $schedule->id }})" title="Modifier"><i class="bi bi-pencil"></i></button>
                                            <form method="POST" action="{{ route('admin.schedule.destroy', $schedule) }}" onsubmit="return confirm('Supprimer définitivement cette séance ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm" title="Supprimer"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="schedule-empty-cell">
                                        <div class="adm-empty">
                                            <div class="adm-empty-icon"><i class="bi bi-calendar-x"></i></div>
                                            <h5>Aucune séance</h5>
                                            <p>Ajoutez la première classe avec le formulaire.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
    </div>

    <section
        class="schedule-final-mode"
        style="{{ $displayMode === 'final' ? '' : 'display:none;' }}"
    >
        <div class="schedule-final-summary">
            <article class="schedule-final-stat">
                <span class="schedule-final-stat-icon"><i class="bi bi-calendar2-week"></i></span>
                <div><strong>{{ $finalStats['sessions'] }}</strong><small>Séances actives</small></div>
            </article>
            <article class="schedule-final-stat">
                <span class="schedule-final-stat-icon"><i class="bi bi-person-video3"></i></span>
                <div><strong>{{ $finalStats['professors'] }}</strong><small>Professeurs visibles</small></div>
            </article>
            <article class="schedule-final-stat">
                <span class="schedule-final-stat-icon"><i class="bi bi-collection"></i></span>
                <div><strong>{{ $finalStats['classes'] }}</strong><small>Classes concernées</small></div>
            </article>
            <article class="schedule-final-stat">
                <span class="schedule-final-stat-icon"><i class="bi bi-calendar-check"></i></span>
                <div><strong>{{ $finalStats['days'] }}</strong><small>Jours occupés</small></div>
            </article>
        </div>

        <section class="adm-card schedule-card schedule-final-card">
            <div class="adm-card-header schedule-card-head schedule-final-head">
                <div class="schedule-card-title-wrap">
                    <span class="schedule-card-icon is-green"><i class="bi bi-grid-3x3-gap-fill"></i></span>
                    <div>
                        <span class="schedule-final-kicker">RÉSULTAT FINAL ADMIN</span>
                        <h3>Planning hebdomadaire global</h3>
                        <p>Vue opérationnelle de toutes les séances actives correspondant aux filtres sélectionnés.</p>
                    </div>
                </div>

                <span class="schedule-count-badge">
                    <i class="bi bi-palette"></i> Couleur par professeur
                </span>
            </div>

            <div class="adm-card-body schedule-final-body">
                @if($finalSchedules->isEmpty())
                    <div class="adm-empty schedule-final-empty">
                        <div class="adm-empty-icon"><i class="bi bi-calendar-x"></i></div>
                        <h5>Aucun créneau actif à afficher</h5>
                        <p>Modifiez les filtres ou ajoutez des séances dans « Construire le planning ».</p>
                        <a href="{{ $buildModeUrl }}" class="adm-btn adm-btn-primary">
                            <i class="bi bi-tools"></i> Construire le planning
                        </a>
                    </div>
                @else
                    <div class="schedule-final-layout">
                        <div class="schedule-final-table-wrap">
                            <table class="schedule-final-table">
                                <thead>
                                    <tr>
                                        <th class="schedule-final-time-head">Horaires</th>
                                        @foreach($dayNames as $dayNumber => $dayName)
                                            <th>
                                                <span class="schedule-final-day-number">{{ str_pad($dayNumber, 2, '0', STR_PAD_LEFT) }}</span>
                                                {{ $dayName }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($finalTimeRows as $timeRow)
                                        <tr>
                                            <th class="schedule-final-time-cell">
                                                <strong>{{ $timeRow['start'] }}</strong>
                                                <span>{{ $timeRow['end'] }}</span>
                                            </th>

                                            @foreach($dayNames as $dayNumber => $dayName)
                                                @php
                                                    $cellKey = $dayNumber
                                                        . '|'
                                                        . $timeRow['start']
                                                        . '|'
                                                        . $timeRow['end'];

                                                    $cellSchedules = $finalMatrix->get(
                                                        $cellKey,
                                                        collect()
                                                    );
                                                @endphp

                                                <td class="schedule-final-cell {{ $cellSchedules->isEmpty() ? 'is-empty' : '' }}">
                                                    @forelse($cellSchedules as $schedule)
                                                        @php
                                                            $subjectName = optional($schedule->subjectModel)->name
                                                                ?: $schedule->subject
                                                                ?: 'Matière';

                                                            $levelName = optional($schedule->level)->name
                                                                ?: optional(optional($schedule->classRoom)->level)->name
                                                                ?: '-';

                                                            $className = optional($schedule->classRoom)->name
                                                                ?: 'Classe';

                                                            $professorsForSchedule = collect(
                                                                $scheduleProfessors[$schedule->id] ?? []
                                                            );

                                                            if ($professorsForSchedule->isEmpty()) {
                                                                $professorsForSchedule = collect([null]);
                                                            }
                                                        @endphp

                                                        @foreach($professorsForSchedule as $scheduleProfessor)
                                                            @php
                                                                $professorId = $scheduleProfessor?->id;
                                                                $color = $professorColors[$professorId] ?? [
                                                                    'hex' => '#64748B',
                                                                    'rgb' => '100,116,139',
                                                                    'label' => 'Non affecté',
                                                                ];

                                                                $teacherName = $scheduleProfessor?->name
                                                                    ?: 'À affecter';
                                                            @endphp

                                                            <article
                                                                class="schedule-final-event"
                                                                style="--prof-color: {{ $color['hex'] }}; --prof-rgb: {{ $color['rgb'] }};"
                                                                title="{{ $subjectName }} · {{ $levelName }} · {{ $className }} · {{ $teacherName }}"
                                                            >
                                                                <div class="schedule-final-event-top">
                                                                    @if($schedule->slot_code)
                                                                        <span class="schedule-final-slot">{{ $schedule->slot_code }}</span>
                                                                    @endif
                                                                    <span class="schedule-final-class">{{ $className }}</span>
                                                                </div>

                                                                <strong class="schedule-final-subject">{{ $subjectName }}</strong>
                                                                <span class="schedule-final-level">{{ $levelName }}</span>

                                                                <div class="schedule-final-professor">
                                                                    <span class="schedule-final-prof-dot"></span>
                                                                    <span>{{ $teacherName }}</span>
                                                                </div>
                                                            </article>
                                                        @endforeach
                                                    @empty
                                                        <span class="schedule-final-dash">·</span>
                                                    @endforelse
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <aside class="schedule-final-legend">
                            <div class="schedule-final-legend-head">
                                <i class="bi bi-palette2"></i>
                                <div>
                                    <strong>Professeurs</strong>
                                    <small>Légende des couleurs</small>
                                </div>
                            </div>

                            <div class="schedule-final-legend-list">
                                @forelse($usedProfessors as $teacher)
                                    @php
                                        $teacherColor = $professorColors[$teacher->id] ?? [
                                            'hex' => '#64748B',
                                            'rgb' => '100,116,139',
                                            'label' => 'Couleur',
                                        ];
                                    @endphp

                                    <div
                                        class="schedule-final-legend-item"
                                        style="--prof-color: {{ $teacherColor['hex'] }}; --prof-rgb: {{ $teacherColor['rgb'] }};"
                                    >
                                        <span class="schedule-final-legend-avatar">
                                            {{ strtoupper(mb_substr($teacher->name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <strong>{{ $teacher->name }}</strong>
                                            <small>{{ $teacherColor['label'] }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="schedule-final-legend-empty">Aucun professeur affecté.</div>
                                @endforelse
                            </div>

                            @if($finalSchedules->contains(function ($schedule) use ($scheduleProfessors) {
                                return collect($scheduleProfessors[$schedule->id] ?? [])->isEmpty();
                            }))
                                <div class="schedule-final-legend-item is-unassigned">
                                    <span class="schedule-final-legend-avatar">?</span>
                                    <div>
                                        <strong>À affecter</strong>
                                        <small>Professeur non défini</small>
                                    </div>
                                </div>
                            @endif
                        </aside>
                    </div>
                @endif
            </div>
        </section>
    </section>
</div>
@endsection

<style>
.schedule-slot-presets {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 12px;
    margin-bottom: 14px;
    border: 1px solid rgba(96, 165, 250, .13);
    border-radius: 12px;
    background: rgba(37, 99, 235, .05);
}
.schedule-slot-presets-copy {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.schedule-slot-presets-copy strong {
    color: #e2e8f0;
    font-size: .72rem;
}
.schedule-slot-presets-copy small {
    color: #64748b;
    font-size: .58rem;
}
.schedule-slot-presets-actions {
    display: flex;
    gap: 7px;
}
.schedule-slot-preset {
    display: flex;
    min-width: 128px;
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
    padding: 8px 10px;
    border: 1px solid rgba(148, 163, 184, .12);
    border-radius: 9px;
    color: #cbd5e1;
    background: rgba(255, 255, 255, .025);
    cursor: pointer;
    transition: .18s ease;
}
.schedule-slot-preset span {
    color: #60a5fa;
    font-size: .52rem;
    font-weight: 850;
    text-transform: uppercase;
}
.schedule-slot-preset strong {
    font-size: .64rem;
}
.schedule-slot-preset:hover,
.schedule-slot-preset.is-active {
    border-color: rgba(96, 165, 250, .35);
    background: rgba(37, 99, 235, .12);
    transform: translateY(-1px);
}
@media (max-width: 820px) {
    .schedule-slot-presets {
        align-items: stretch;
        flex-direction: column;
    }
    .schedule-slot-presets-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .schedule-slot-preset {
        min-width: 0;
    }
}


.schedule-slot-choice-field {
    grid-column: span 2;
    padding: 12px;
    border: 1px solid rgba(96,165,250,.14);
    border-radius: 12px;
    background: rgba(37,99,235,.035);
}

.schedule-slot-choice-field .adm-form-select:disabled {
    opacity: .58;
    cursor: not-allowed;
}

@media (max-width: 900px) {
    .schedule-slot-choice-field {
        grid-column: auto;
    }
}



.schedule-slot-code {
    display: inline-flex;
    min-width: 30px;
    min-height: 22px;
    align-items: center;
    justify-content: center;
    margin-right: 6px;
    padding: 2px 7px;
    color: #DBEAFE;
    border: 1px solid rgba(96,165,250,.22);
    border-radius: 7px;
    background: rgba(37,99,235,.12);
    font-size: .58rem;
    font-weight: 850;
    letter-spacing: .04em;
}



/* =========================================================
   VUE FINALE ADMIN — PLANNING HEBDOMADAIRE
   ========================================================= */
.schedule-view-switcher {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}

.schedule-final-mode {
    margin-top: 14px;
}

.schedule-final-summary {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 14px;
}

.schedule-final-stat {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 76px;
    padding: 14px 16px;
    border: 1px solid rgba(148, 163, 184, .12);
    border-radius: 14px;
    background: linear-gradient(145deg, rgba(15, 23, 42, .92), rgba(10, 18, 32, .92));
    box-shadow: 0 12px 30px rgba(2, 6, 23, .14);
}

.schedule-final-stat-icon {
    display: inline-flex;
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(96, 165, 250, .18);
    border-radius: 11px;
    color: #93c5fd;
    background: rgba(37, 99, 235, .10);
}

.schedule-final-stat strong {
    display: block;
    color: #f8fafc;
    font-size: 1.12rem;
    line-height: 1.1;
}

.schedule-final-stat small {
    display: block;
    margin-top: 4px;
    color: #64748b;
    font-size: .64rem;
}

.schedule-final-card {
    overflow: hidden;
}

.schedule-final-kicker {
    display: block;
    margin-bottom: 3px;
    color: #a78bfa;
    font-size: .56rem;
    font-weight: 900;
    letter-spacing: .09em;
}

.schedule-final-body {
    padding: 14px !important;
}

.schedule-final-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 190px;
    gap: 14px;
    align-items: start;
}

.schedule-final-table-wrap {
    overflow-x: auto;
    border: 1px solid rgba(148, 163, 184, .12);
    border-radius: 13px;
    background: #07101e;
}

.schedule-final-table {
    width: 100%;
    min-width: 1020px;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
}

.schedule-final-table th,
.schedule-final-table td {
    border-right: 1px solid rgba(148, 163, 184, .10);
    border-bottom: 1px solid rgba(148, 163, 184, .10);
}

.schedule-final-table tr:last-child th,
.schedule-final-table tr:last-child td {
    border-bottom: 0;
}

.schedule-final-table th:last-child,
.schedule-final-table td:last-child {
    border-right: 0;
}

.schedule-final-table thead th {
    height: 48px;
    padding: 9px 7px;
    color: #dbeafe;
    background: #0c1728;
    font-size: .63rem;
    font-weight: 850;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: .045em;
}

.schedule-final-day-number {
    display: block;
    margin-bottom: 2px;
    color: #475569;
    font-size: .49rem;
}

.schedule-final-time-head,
.schedule-final-time-cell {
    width: 94px;
    min-width: 94px;
    position: sticky;
    left: 0;
    z-index: 2;
}

.schedule-final-time-head {
    z-index: 4;
}

.schedule-final-time-cell {
    padding: 12px 8px;
    color: #e2e8f0;
    background: #0a1423;
    text-align: center;
    vertical-align: top;
}

.schedule-final-time-cell strong,
.schedule-final-time-cell span {
    display: block;
}

.schedule-final-time-cell strong {
    font-size: .72rem;
}

.schedule-final-time-cell span {
    margin-top: 3px;
    color: #64748b;
    font-size: .58rem;
}

.schedule-final-cell {
    min-width: 128px;
    height: 104px;
    padding: 6px;
    vertical-align: top;
    background: rgba(8, 16, 29, .72);
}

.schedule-final-cell.is-empty {
    text-align: center;
    vertical-align: middle;
}

.schedule-final-dash {
    color: #243247;
    font-size: 1rem;
}

.schedule-final-event {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-height: 91px;
    padding: 8px;
    margin-bottom: 5px;
    overflow: hidden;
    border: 1px solid rgba(var(--prof-rgb), .30);
    border-left: 3px solid var(--prof-color);
    border-radius: 9px;
    background: linear-gradient(145deg, rgba(var(--prof-rgb), .15), rgba(var(--prof-rgb), .055));
    box-shadow: inset 0 1px rgba(255, 255, 255, .025);
}

.schedule-final-event:last-child {
    margin-bottom: 0;
}

.schedule-final-event-top {
    display: flex;
    align-items: center;
    gap: 5px;
    min-width: 0;
}

.schedule-final-slot {
    display: inline-flex;
    min-width: 26px;
    height: 19px;
    align-items: center;
    justify-content: center;
    padding: 0 5px;
    border-radius: 5px;
    color: #fff;
    background: var(--prof-color);
    font-size: .48rem;
    font-weight: 900;
    letter-spacing: .03em;
}

.schedule-final-class {
    min-width: 0;
    overflow: hidden;
    color: #f8fafc;
    font-size: .58rem;
    font-weight: 850;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.schedule-final-subject {
    overflow: hidden;
    color: #e2e8f0;
    font-size: .58rem;
    line-height: 1.25;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.schedule-final-level {
    overflow: hidden;
    color: #94a3b8;
    font-size: .50rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.schedule-final-professor {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: auto;
    min-width: 0;
    color: #cbd5e1;
    font-size: .50rem;
    font-weight: 700;
}

.schedule-final-professor span:last-child {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.schedule-final-prof-dot {
    width: 7px;
    height: 7px;
    flex: 0 0 7px;
    border-radius: 50%;
    background: var(--prof-color);
    box-shadow: 0 0 0 3px rgba(var(--prof-rgb), .12);
}

.schedule-final-legend {
    position: sticky;
    top: 84px;
    padding: 12px;
    border: 1px solid rgba(148, 163, 184, .12);
    border-radius: 13px;
    background: #0a1423;
}

.schedule-final-legend-head {
    display: flex;
    align-items: center;
    gap: 9px;
    padding-bottom: 10px;
    margin-bottom: 9px;
    border-bottom: 1px solid rgba(148, 163, 184, .10);
    color: #93c5fd;
}

.schedule-final-legend-head strong,
.schedule-final-legend-head small {
    display: block;
}

.schedule-final-legend-head strong {
    color: #e2e8f0;
    font-size: .68rem;
}

.schedule-final-legend-head small {
    margin-top: 2px;
    color: #64748b;
    font-size: .50rem;
}

.schedule-final-legend-list {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.schedule-final-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    padding: 7px;
    border: 1px solid rgba(var(--prof-rgb), .16);
    border-radius: 9px;
    background: rgba(var(--prof-rgb), .065);
}

.schedule-final-legend-avatar {
    display: inline-flex;
    width: 27px;
    height: 27px;
    flex: 0 0 27px;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    color: #fff;
    background: var(--prof-color);
    font-size: .58rem;
    font-weight: 900;
}

.schedule-final-legend-item div {
    min-width: 0;
}

.schedule-final-legend-item strong,
.schedule-final-legend-item small {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.schedule-final-legend-item strong {
    color: #e2e8f0;
    font-size: .60rem;
}

.schedule-final-legend-item small {
    margin-top: 2px;
    color: #64748b;
    font-size: .48rem;
}

.schedule-final-legend-item.is-unassigned {
    --prof-color: #64748B;
    --prof-rgb: 100,116,139;
    margin-top: 7px;
}

.schedule-final-legend-empty {
    color: #64748b;
    font-size: .58rem;
    text-align: center;
}

.schedule-final-empty {
    padding: 34px 16px;
}

@media (max-width: 1180px) {
    .schedule-final-layout {
        grid-template-columns: 1fr;
    }

    .schedule-final-legend {
        position: static;
    }

    .schedule-final-legend-list {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 820px) {
    .schedule-final-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .schedule-view-switcher {
        width: 100%;
        justify-content: stretch;
    }

    .schedule-view-switcher .adm-btn {
        flex: 1 1 auto;
    }

    .schedule-final-legend-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 520px) {
    .schedule-final-summary,
    .schedule-final-legend-list {
        grid-template-columns: 1fr;
    }
}

@media print {
    body.admin-portal .admin-sidebar,
    body.admin-portal .admin-topbar,
    body.admin-portal .admin-mobile-header,
    .schedule-hero-actions,
    .schedule-card:has(.schedule-filter-grid),
    .schedule-final-summary {
        display: none !important;
    }

    body,
    body.admin-portal,
    body.admin-portal .admin-main,
    body.admin-portal .admin-content {
        background: #fff !important;
        color: #111827 !important;
    }

    .schedule-final-card,
    .schedule-final-table-wrap,
    .schedule-final-legend {
        border-color: #d1d5db !important;
        box-shadow: none !important;
    }

    .schedule-final-layout {
        grid-template-columns: minmax(0, 1fr) 160px !important;
    }

    .schedule-final-table {
        min-width: 0 !important;
    }

    .schedule-final-table thead th,
    .schedule-final-time-cell,
    .schedule-final-cell,
    .schedule-final-legend {
        background: #fff !important;
        color: #111827 !important;
    }
}
</style>

@push('scripts')
<script>
(function () {
    const hierarchy = @json($scheduleHierarchy);
    const schedules = @json($scheduleEditData);
    const filters = @json($scheduleFilters);
    const storeUrl = @json(route('admin.schedule.store'));
    const updateBaseUrl = @json(url('/admin/schedule'));

    const form = document.getElementById('planningForm');
    const method = document.getElementById('planningMethod');
    const subjectSelect = document.getElementById('planningSubject');
    const levelSelect = document.getElementById('planningLevel');
    const classSelect = document.getElementById('planningClass');
    const slotCodeSelect = document.getElementById('planningSlotCode');
    const filterSubjectSelect = document.getElementById('scheduleFilterSubject');
    const filterLevelSelect = document.getElementById('scheduleFilterLevel');
    const filterClassSelect = document.getElementById('scheduleFilterClass');
    const daySelect = document.getElementById('planningDay');
    const startTimeInput = document.getElementById('planningStartTime');
    const endTimeInput = document.getElementById('planningEndTime');
    const durationInfo = document.getElementById('planningDurationInfo');
    const durationPreview = document.getElementById('planningDurationPreview');

    function timeToMinutes(value) {
        if (!value || !value.includes(':')) {
            return null;
        }

        const parts = value.split(':').map(Number);

        if (parts.length !== 2 || parts.some(Number.isNaN)) {
            return null;
        }

        return (parts[0] * 60) + parts[1];
    }

    function formatDuration(minutes) {
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;

        if (hours === 0) {
            return minutes + ' min';
        }

        const hourLabel = hours === 1 ? '1 heure' : hours + ' heures';

        return remainingMinutes > 0
            ? hourLabel + ' ' + remainingMinutes + ' min'
            : hourLabel;
    }

    function updateDurationPreview() {
        const start = timeToMinutes(startTimeInput.value);
        const end = timeToMinutes(endTimeInput.value);

        if (start === null || end === null) {
            durationInfo.classList.remove('is-invalid');
            endTimeInput.setCustomValidity('');
            durationPreview.textContent = 'Choisissez l’heure de début et l’heure de fin.';
            return;
        }

        const duration = end - start;

        if (duration <= 0) {
            durationInfo.classList.add('is-invalid');
            endTimeInput.setCustomValidity(
                'L’heure de fin doit être postérieure à l’heure de début.'
            );
            durationPreview.textContent =
                'L’heure de fin doit être postérieure à l’heure de début.';
            return;
        }

        durationInfo.classList.remove('is-invalid');
        endTimeInput.setCustomValidity('');
        durationPreview.textContent =
            'Durée sélectionnée : ' + formatDuration(duration)
            + ' (' + startTimeInput.value + ' – ' + endTimeInput.value + ').';
    }

    startTimeInput.addEventListener('input', function () {
        updateDurationPreview();
        updatePath();
    });
    endTimeInput.addEventListener('input', function () {
        updateDurationPreview();
        updatePath();
    });
    daySelect.addEventListener('change', updatePath);

    function selectedText(select, fallback) {
        return select && select.selectedIndex > 0
            ? select.options[select.selectedIndex].text.split(' — ')[0]
            : fallback;
    }

    function updatePath() {
        document.getElementById('pathSubject').textContent = selectedText(subjectSelect, 'Matière');
        document.getElementById('pathLevel').textContent = selectedText(levelSelect, 'Niveau');
        document.getElementById('pathClass').textContent = selectedText(classSelect, 'Classe');

        document.getElementById('pathSlot').textContent =
            slotCodeSelect && slotCodeSelect.value
                ? slotCodeSelect.value
                : 'Créneau';
    }

    function findSubject(id) {
        return hierarchy.find(function (item) {
            return String(item.id) === String(id);
        });
    }

    function fillLevels(subjectId, selectedLevelId, selectedClassId) {
        const subject = findSubject(subjectId);
        levelSelect.innerHTML = '<option value="">Choisir un niveau</option>';
        classSelect.innerHTML = '<option value="">Choisir le niveau</option>';
        levelSelect.disabled = !subject;
        classSelect.disabled = true;

        slotCodeSelect.innerHTML =
            '<option value="">Choisissez d’abord la classe</option>';
        slotCodeSelect.disabled = true;

        if (!subject) {
            updatePath();
            return;
        }

        if (!subject.levels || subject.levels.length === 0) {
            levelSelect.innerHTML =
                '<option value="">Aucun niveau configuré pour cette matière</option>';
            levelSelect.disabled = true;
            updatePath();
            return;
        }

        levelSelect.disabled = false;

        subject.levels.forEach(function (level) {
            const option = new Option(level.name, level.id);
            option.selected = String(level.id) === String(selectedLevelId || '');
            levelSelect.add(option);
        });

        if (selectedLevelId) {
            fillClasses(subjectId, selectedLevelId, selectedClassId);
        }

        updatePath();
    }

    function fillClasses(subjectId, levelId, selectedClassId) {
        const subject = findSubject(subjectId);
        const level = subject
            ? subject.levels.find(function (item) {
                return String(item.id) === String(levelId);
            })
            : null;

        classSelect.innerHTML = '<option value="">Choisir une classe</option>';
        classSelect.disabled = !level;

        if (level) {
            if (!level.classes || level.classes.length === 0) {
                classSelect.innerHTML =
                    '<option value="">Aucune classe configurée pour ce niveau</option>';
                classSelect.disabled = true;
                updatePath();
                return;
            }

            level.classes.forEach(function (classRoom) {
                const option = new Option(classRoom.name, classRoom.id);
                option.selected = String(classRoom.id) === String(selectedClassId || '');
                classSelect.add(option);
            });
        }

        updatePath();
    }

    function findClass(subjectId, levelId, classId) {
        const subject = findSubject(subjectId);

        const level = subject
            ? subject.levels.find(function (item) {
                return String(item.id) === String(levelId);
            })
            : null;

        return level
            ? level.classes.find(function (item) {
                return String(item.id) === String(classId);
            })
            : null;
    }

    function fillSlotCodes(selectedSlotCode) {
        const classRoom = findClass(
            subjectSelect.value,
            levelSelect.value,
            classSelect.value
        );

        slotCodeSelect.innerHTML =
            '<option value="">Choisir un créneau</option>';

        slotCodeSelect.disabled = !classRoom;

        if (!classRoom) {
            slotCodeSelect.innerHTML =
                '<option value="">Choisissez d’abord la classe</option>';
            updatePath();
            return;
        }

        (classRoom.slot_codes || []).forEach(function (code) {
            const option = new Option(code, code);
            option.selected =
                String(code) === String(selectedSlotCode || '');
            slotCodeSelect.add(option);
        });

        if (selectedSlotCode) {
            slotCodeSelect.value = String(selectedSlotCode);
        }

        updatePath();
    }

    function fillFilterClasses(subjectId, levelId, selectedClassId) {
        const subject = findSubject(subjectId);
        const level = subject
            ? subject.levels.find(function (item) {
                return String(item.id) === String(levelId);
            })
            : null;

        filterClassSelect.innerHTML = '<option value="">Toutes</option>';
        filterClassSelect.disabled = !level;

        if (!level) {
            return;
        }

        level.classes.forEach(function (classRoom) {
            const option = new Option(classRoom.name, classRoom.id);
            option.selected = String(classRoom.id) === String(selectedClassId || '');
            filterClassSelect.add(option);
        });
    }

    function fillFilterLevels(subjectId, selectedLevelId, selectedClassId) {
        const subject = findSubject(subjectId);

        filterLevelSelect.innerHTML = '<option value="">Tous</option>';
        filterClassSelect.innerHTML = '<option value="">Toutes</option>';
        filterLevelSelect.disabled = !subject;
        filterClassSelect.disabled = true;

        if (!subject) {
            return;
        }

        subject.levels.forEach(function (level) {
            const option = new Option(level.name, level.id);
            option.selected = String(level.id) === String(selectedLevelId || '');
            filterLevelSelect.add(option);
        });

        if (selectedLevelId) {
            fillFilterClasses(
                subjectId,
                selectedLevelId,
                selectedClassId
            );
        }
    }

    filterSubjectSelect.addEventListener('change', function () {
        fillFilterLevels(filterSubjectSelect.value);
    });

    filterLevelSelect.addEventListener('change', function () {
        fillFilterClasses(
            filterSubjectSelect.value,
            filterLevelSelect.value
        );
    });

    subjectSelect.addEventListener('change', function () {
        fillLevels(subjectSelect.value);
    });

    levelSelect.addEventListener('change', function () {
        fillClasses(subjectSelect.value, levelSelect.value);
    });

    classSelect.addEventListener('change', function () {
        fillSlotCodes();
        updatePath();
    });

    slotCodeSelect.addEventListener(
        'change',
        updatePath
    );

    window.editPlanning = function (id) {
        const data = schedules[id];
        if (!data) {
            return;
        }

        form.action = updateBaseUrl + '/' + id;
        method.disabled = false;
        document.getElementById('planningFormTitle').textContent = 'Modifier la séance #' + id;
        document.getElementById('planningSubmitButton').innerHTML = '<i class="bi bi-check-lg"></i> Enregistrer les modifications';
        document.getElementById('planningCancelButton').style.display = 'inline-flex';

        subjectSelect.value = data.subject_id || '';
        fillLevels(data.subject_id, data.level_id, data.class_id);
        fillSlotCodes(data.slot_code || '');

        document.getElementById('planningDay').value =
            data.day_of_week || 7;

        startTimeInput.value =
            data.start_time || '09:00';

        endTimeInput.value =
            data.end_time || '10:30';

        updateDurationPreview();
        document.getElementById('planningRecurrence').value = data.recurrence || 'weekly';
        document.getElementById('planningValidFrom').value = data.valid_from || '';
        document.getElementById('planningStatus').value = data.status || 'active';

        document.getElementById('planningFormCard').scrollIntoView({behavior: 'smooth', block: 'start'});
    };

    window.resetPlanningForm = function (scrollToForm) {
        form.reset();
        form.action = storeUrl;
        method.disabled = true;
        document.getElementById('planningFormTitle').textContent = 'Nouvelle séance';
        document.getElementById('planningSubmitButton').innerHTML = '<i class="bi bi-check-lg"></i> Ajouter au planning';
        document.getElementById('planningCancelButton').style.display = 'none';
        levelSelect.innerHTML = '<option value="">Choisir un niveau</option>';
        classSelect.innerHTML = '<option value="">Choisir le niveau</option>';
        levelSelect.disabled = true;
        classSelect.disabled = true;

        slotCodeSelect.innerHTML =
            '<option value="">Choisissez d’abord la classe</option>';
        slotCodeSelect.disabled = true;

        document.getElementById('planningRecurrence').value = 'weekly';
        startTimeInput.value = '09:00';
        endTimeInput.value = '10:30';
        updateDurationPreview();
        document.getElementById('planningValidFrom').value = new Date().toISOString().slice(0, 10);
        document.getElementById('planningStatus').value = 'active';
        updatePath();

        if (scrollToForm) {
            document.getElementById('planningFormCard').scrollIntoView({behavior: 'smooth', block: 'start'});
        }
    };

    const selectedFilterSubject = String(filters.subject_id || '');
    const selectedFilterLevel = String(filters.level_id || '');
    const selectedFilterClass = String(filters.class_id || '');

    if (selectedFilterSubject) {
        filterSubjectSelect.value = selectedFilterSubject;
        fillFilterLevels(
            selectedFilterSubject,
            selectedFilterLevel,
            selectedFilterClass
        );
    } else {
        fillFilterLevels('');
    }

    const oldSubject = @json(old('subject_id'));
    const oldLevel = @json(old('level_id'));
    const oldClass = @json(old('class_id'));
    const oldSlotCode = @json(old('slot_code'));

    if (oldSubject) {
        subjectSelect.value = oldSubject;
        fillLevels(oldSubject, oldLevel, oldClass);

        if (oldClass) {
            fillSlotCodes(oldSlotCode);
        }
    } else {
        updatePath();
    }

    updateDurationPreview();

})();
</script>
@endpush
