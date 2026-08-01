@extends('layouts.admin')

@section('title', 'Emploi du temps')
@section('page_title', 'Emploi du temps')
@section('breadcrumb', 'Emploi du temps')

@push('head')
<style>
.planning-shell{
    display:grid;
    gap:1.35rem;
}

.planning-toolbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    flex-wrap:wrap;
    padding:.2rem 0;
}

.planning-toolbar h1{
    display:flex;
    align-items:center;
    gap:.65rem;
    margin:0;
    font-size:1.55rem;
}

.planning-toolbar p{
    margin:.4rem 0 0;
    color:var(--adm-text-muted);
    font-size:.82rem;
}

.planning-filter-grid{
    display:grid;
    grid-template-columns:repeat(4,minmax(150px,1fr)) auto;
    gap:.8rem;
    align-items:end;
}

.planning-filter-actions{
    display:flex;
    align-items:center;
    gap:.5rem;
    min-width:max-content;
}

.planning-main-grid{
    display:grid;
    grid-template-columns:minmax(0,1fr);
    gap:1.35rem;
}

.planning-intro{
    margin:0 0 1rem;
    color:var(--adm-text-muted);
    font-size:.75rem;
    line-height:1.6;
}

.planning-form-sections{
    display:grid;
    grid-template-columns:minmax(0,1.12fr) minmax(0,.88fr);
    gap:1rem;
    align-items:stretch;
}

.planning-form-panel{
    min-width:0;
    padding:1rem;
    border:1px solid var(--adm-border);
    border-radius:16px;
    background:rgba(15,23,42,.23);
}

.planning-panel-heading{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:.8rem;
    margin-bottom:.9rem;
}

.planning-panel-title{
    display:flex;
    align-items:center;
    gap:.6rem;
    margin:0;
    color:var(--adm-text);
    font-size:.9rem;
    font-weight:750;
}

.planning-panel-title i{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:30px;
    height:30px;
    border-radius:9px;
    background:rgba(96,165,250,.1);
    color:#60a5fa;
}

.planning-panel-note{
    margin:.25rem 0 0;
    color:var(--adm-text-muted);
    font-size:.68rem;
    line-height:1.45;
}

.planning-form-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:.85rem;
}

.planning-path{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:.45rem;
    min-height:42px;
    padding:.65rem .8rem;
    margin-bottom:.9rem;
    border:1px solid rgba(96,165,250,.18);
    border-radius:12px;
    background:rgba(37,99,235,.06);
    color:var(--adm-text-muted);
    font-size:.72rem;
}

.planning-path strong{
    color:#93c5fd;
}

.planning-duration-info{
    display:flex;
    align-items:flex-start;
    gap:.7rem;
    min-height:70px;
    padding:.8rem .9rem;
    margin-top:.9rem;
    border:1px solid rgba(74,222,128,.17);
    border-radius:13px;
    background:rgba(22,163,74,.07);
    color:#bbf7d0;
    font-size:.75rem;
    line-height:1.55;
}

.planning-duration-info i{
    margin-top:.08rem;
    color:#4ade80;
    font-size:1rem;
}

.planning-duration-info.is-invalid{
    border-color:rgba(248,113,113,.28);
    background:rgba(220,38,38,.08);
    color:#fecaca;
}

.planning-duration-info.is-invalid i{
    color:#f87171;
}

.planning-form-footer{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    padding-top:1rem;
    margin-top:1rem;
    border-top:1px solid var(--adm-border);
}

.planning-form-footer-note{
    display:flex;
    align-items:center;
    gap:.5rem;
    color:var(--adm-text-muted);
    font-size:.68rem;
}

.planning-form-buttons{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:.6rem;
    flex-wrap:wrap;
}

.planning-submit{
    min-width:190px;
}

.planning-table-card{
    min-width:0;
    overflow:hidden;
}

.planning-table-meta{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.75rem;
    flex-wrap:wrap;
}

.planning-table-wrap{
    overflow-x:auto;
}

.planning-table-wrap .adm-table{
    min-width:940px;
}

.planning-table-wrap .adm-table th,
.planning-table-wrap .adm-table td{
    vertical-align:middle;
}

.planning-table-wrap .adm-table tbody tr{
    transition:background .18s ease;
}

.planning-table-wrap .adm-table tbody tr:hover{
    background:rgba(96,165,250,.035);
}

.planning-status{
    display:inline-flex;
    align-items:center;
    padding:.28rem .55rem;
    border-radius:999px;
    font-size:.68rem;
    font-weight:700;
}

.planning-status.active{
    color:#86efac;
    background:rgba(22,163,74,.14);
}

.planning-status.inactive{
    color:#cbd5e1;
    background:rgba(148,163,184,.13);
}

.planning-day{
    font-weight:750;
    color:#dbeafe;
}

.planning-actions{
    display:flex;
    justify-content:flex-end;
    gap:.4rem;
}

.planning-help{
    display:block;
    margin-top:.35rem;
    color:var(--adm-text-muted);
    font-size:.66rem;
    line-height:1.4;
}

.planning-required{
    color:#f87171;
}

@media(max-width:1180px){
    .planning-filter-grid{
        grid-template-columns:repeat(2,minmax(180px,1fr));
    }

    .planning-filter-actions{
        grid-column:1/-1;
        justify-content:flex-end;
    }
}

@media(max-width:1020px){
    .planning-form-sections{
        grid-template-columns:1fr;
    }
}

@media(max-width:720px){
    .planning-toolbar{
        align-items:flex-start;
    }

    .planning-toolbar .adm-btn{
        width:100%;
    }

    .planning-filter-grid,
    .planning-form-grid{
        grid-template-columns:1fr;
    }

    .planning-filter-actions{
        grid-column:auto;
        display:grid;
        grid-template-columns:1fr auto;
    }

    .planning-filter-actions .adm-btn:first-child{
        width:100%;
    }

    .planning-form-panel{
        padding:.85rem;
    }

    .planning-form-footer{
        align-items:stretch;
        flex-direction:column;
    }

    .planning-form-buttons{
        display:grid;
        grid-template-columns:1fr;
    }

    .planning-form-buttons .adm-btn,
    .planning-submit{
        width:100%;
        min-width:0;
    }
}
</style>
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

    $scheduleFilters = request()->only([
        'subject_id',
        'level_id',
        'class_id',
        'prof_id',
    ]);

    $scheduleEditData = $schedules->mapWithKeys(function ($schedule) {
        return [$schedule->id => [
            'id' => $schedule->id,
            'subject_id' => $schedule->subject_id,
            'level_id' => $schedule->level_id ?: optional($schedule->classRoom)->level_id,
            'class_id' => $schedule->class_id,
            'prof_id' => $schedule->prof_id,
            'day_of_week' => $schedule->day_of_week,
            'start_time' => optional($schedule->start_time)->format('H:i'),
            'end_time' => optional($schedule->end_time)->format('H:i'),
            'recurrence' => $schedule->recurrence ?: 'weekly',
            'valid_from' => optional($schedule->valid_from ?: $schedule->date)->format('Y-m-d'),
            'status' => $schedule->status ?: 'active',
        ]];
    });
@endphp

<div class="planning-shell">
    <div class="planning-toolbar">
        <div>
            <h1><i class="bi bi-calendar3" style="color:#60a5fa"></i> Emploi du temps des classes</h1>
            <p>Organisez les séances par matière, niveau, classe et professeur.</p>
        </div>
        <button type="button" class="adm-btn adm-btn-primary" onclick="resetPlanningForm(true)">
            <i class="bi bi-plus-lg"></i> Planifier une classe
        </button>
    </div>

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

    <div class="adm-card">
        <div class="adm-card-header">
            <h4><i class="bi bi-funnel" style="color:#a78bfa"></i> Filtres</h4>
        </div>
        <div class="adm-card-body">
            <form method="GET" action="{{ route('admin.schedule.index') }}" class="planning-filter-grid">
                <div class="adm-form-group mb-0">
                    <label class="adm-form-label">Matière</label>
                    <select name="subject_id" class="adm-form-select">
                        <option value="">Toutes</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ (string) request('subject_id') === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="adm-form-group mb-0">
                    <label class="adm-form-label">Niveau</label>
                    <select name="level_id" class="adm-form-select">
                        <option value="">Tous</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}" {{ (string) request('level_id') === (string) $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="adm-form-group mb-0">
                    <label class="adm-form-label">Classe pédagogique</label>
                    <select name="class_id" class="adm-form-select">
                        <option value="">Toutes</option>
                        @foreach($classes as $classRoom)
                            <option value="{{ $classRoom->id }}" {{ (string) request('class_id') === (string) $classRoom->id ? 'selected' : '' }}>{{ $classRoom->name }}</option>
                        @endforeach
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

                <div class="planning-filter-actions">
                    <button class="adm-btn adm-btn-primary" type="submit"><i class="bi bi-search"></i> Filtrer</button>
                    <a href="{{ route('admin.schedule.index') }}" class="adm-btn adm-btn-ghost" title="Réinitialiser"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="planning-main-grid">
        <div class="adm-card" id="planningFormCard">
            <div class="adm-card-header planning-table-meta">
                <h4 id="planningFormTitle">
                    <i class="bi bi-calendar-plus" style="color:#60a5fa"></i>
                    Nouvelle séance
                </h4>
                <span class="adm-badge adm-badge-info">
                    <i class="bi bi-clock"></i> Durée libre
                </span>
            </div>

            <div class="adm-card-body">
                <p class="planning-intro">
                    Sélectionnez le parcours pédagogique, puis définissez le jour et les horaires. Une répétition hebdomadaire recrée automatiquement la séance chaque semaine.
                </p>

                <form method="POST" action="{{ route('admin.schedule.store') }}" id="planningForm">
                    @csrf
                    <input type="hidden" name="_method" id="planningMethod" value="PUT" disabled>

                    <div class="planning-form-sections">
                        <section class="planning-form-panel">
                            <div class="planning-panel-heading">
                                <div>
                                    <h5 class="planning-panel-title">
                                        <i class="bi bi-diagram-3"></i>
                                        Parcours pédagogique
                                    </h5>
                                    <p class="planning-panel-note">Matière → niveau → classe → professeur</p>
                                </div>
                            </div>

                            <div class="planning-path">
                                <strong id="pathSubject">Matière</strong>
                                <i class="bi bi-chevron-right"></i>
                                <strong id="pathLevel">Niveau</strong>
                                <i class="bi bi-chevron-right"></i>
                                <strong id="pathClass">Classe</strong>
                            </div>

                            <div class="planning-form-grid">
                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Matière <span class="planning-required">*</span></label>
                                    <select name="subject_id" id="planningSubject" class="adm-form-select" required>
                                        <option value="">Choisir</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ (string) old('subject_id') === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Niveau <span class="planning-required">*</span></label>
                                    <select name="level_id" id="planningLevel" class="adm-form-select" required disabled>
                                        <option value="">Choisir un niveau</option>
                                    </select>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Classe pédagogique <span class="planning-required">*</span></label>
                                    <select name="class_id" id="planningClass" class="adm-form-select" required disabled>
                                        <option value="">Choisir le niveau</option>
                                    </select>
                                    <small class="planning-help">Exemple : Débutant, Intermédiaire ou Avancé.</small>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Professeur <span class="planning-required">*</span></label>
                                    <select name="prof_id" id="planningProfessor" class="adm-form-select" required>
                                        <option value="">Choisir un professeur</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ (string) old('prof_id') === (string) $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </section>

                        <section class="planning-form-panel">
                            <div class="planning-panel-heading">
                                <div>
                                    <h5 class="planning-panel-title">
                                        <i class="bi bi-clock-history"></i>
                                        Organisation de la séance
                                    </h5>
                                    <p class="planning-panel-note">Jour, répétition, horaires et statut</p>
                                </div>
                            </div>

                            <div class="planning-form-grid">
                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Jour <span class="planning-required">*</span></label>
                                    <select name="day_of_week" id="planningDay" class="adm-form-select" required>
                                        @foreach($dayNames as $dayNumber => $dayName)
                                            <option value="{{ $dayNumber }}" {{ (string) old('day_of_week', 1) === (string) $dayNumber ? 'selected' : '' }}>{{ $dayName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Répétition <span class="planning-required">*</span></label>
                                    <select name="recurrence" id="planningRecurrence" class="adm-form-select" required>
                                        <option value="weekly" {{ old('recurrence', 'weekly') === 'weekly' ? 'selected' : '' }}>Toutes les semaines</option>
                                        <option value="once" {{ old('recurrence') === 'once' ? 'selected' : '' }}>Une seule fois</option>
                                    </select>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Heure de début <span class="planning-required">*</span></label>
                                    <input type="time" name="start_time" id="planningStartTime" value="{{ old('start_time', '13:00') }}" class="adm-form-input" step="300" required>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">Heure de fin <span class="planning-required">*</span></label>
                                    <input type="time" name="end_time" id="planningEndTime" value="{{ old('end_time', '14:00') }}" class="adm-form-input" step="300" required>
                                </div>

                                <div class="adm-form-group mb-0">
                                    <label class="adm-form-label">À partir du <span class="planning-required">*</span></label>
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

                            <div class="planning-duration-info" id="planningDurationInfo">
                                <i class="bi bi-stopwatch"></i>
                                <div>
                                    <strong>Durée personnalisable</strong><br>
                                    <span id="planningDurationPreview">Choisissez l’heure de début et l’heure de fin.</span>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="planning-form-footer">
                        <span class="planning-form-footer-note">
                            <i class="bi bi-info-circle"></i>
                            Tous les champs marqués d’un astérisque sont obligatoires.
                        </span>

                        <div class="planning-form-buttons">
                            <button type="button" class="adm-btn adm-btn-ghost" id="planningCancelButton" onclick="resetPlanningForm()" style="display:none">
                                <i class="bi bi-x-lg"></i> Annuler
                            </button>
                            <button type="submit" class="adm-btn adm-btn-primary planning-submit" id="planningSubmitButton">
                                <i class="bi bi-check-lg"></i> Ajouter au planning
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="adm-card planning-table-card">
            <div class="adm-card-header planning-table-meta">
                <div>
                    <h4 style="margin:0"><i class="bi bi-calendar-week" style="color:#4ade80"></i> Emploi du temps</h4>
                    <small style="display:block;margin-top:.3rem;color:var(--adm-text-muted)">Toutes les séances correspondant aux filtres sélectionnés.</small>
                </div>
                <span class="adm-badge adm-badge-primary">{{ $schedules->count() }} séance(s) planifiée(s)</span>
            </div>
            <div class="adm-card-body p-0">
                <div class="adm-table-wrap planning-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Jour / heure</th>
                                <th>Parcours</th>
                                <th>Professeur</th>
                                <th>Répétition</th>
                                <th>Statut</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $schedule)
                                <tr>
                                    <td>
                                        <div class="planning-day">{{ $schedule->day_label }}</div>
                                        <small style="color:var(--adm-text-muted)">{{ $schedule->time_range_label }} · {{ $schedule->duration_label }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ optional($schedule->subjectModel)->name ?: $schedule->subject }}</strong><br>
                                        <small style="color:var(--adm-text-muted)">{{ optional($schedule->level)->name ?: optional(optional($schedule->classRoom)->level)->name ?: '-' }} → {{ optional($schedule->classRoom)->name ?: '-' }}</small>
                                    </td>
                                    <td>{{ optional($schedule->prof)->name ?: '-' }}</td>
                                    <td>
                                        @if(($schedule->recurrence ?: 'once') === 'weekly')
                                            <span class="adm-badge adm-badge-info">Chaque semaine</span><br>
                                            <small style="color:var(--adm-text-muted)">Depuis {{ optional($schedule->valid_from)->format('d/m/Y') ?: '-' }}</small>
                                        @else
                                            <span class="adm-badge">Une fois</span><br>
                                            <small style="color:var(--adm-text-muted)">{{ optional($schedule->date)->format('d/m/Y') ?: '-' }}</small>
                                        @endif
                                    </td>
                                    <td><span class="planning-status {{ $schedule->status ?: 'active' }}">{{ ($schedule->status ?: 'active') === 'active' ? 'Active' : 'Désactivée' }}</span></td>
                                    <td>
                                        <div class="planning-actions">
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
                                    <td colspan="6">
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
        </div>
    </div>
</div>
@endsection

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

    startTimeInput.addEventListener('input', updateDurationPreview);
    endTimeInput.addEventListener('input', updateDurationPreview);

    function selectedText(select, fallback) {
        return select && select.selectedIndex > 0
            ? select.options[select.selectedIndex].text.split(' — ')[0]
            : fallback;
    }

    function updatePath() {
        document.getElementById('pathSubject').textContent = selectedText(subjectSelect, 'Matière');
        document.getElementById('pathLevel').textContent = selectedText(levelSelect, 'Niveau');
        document.getElementById('pathClass').textContent = selectedText(classSelect, 'Classe');
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

        if (!subject) {
            updatePath();
            return;
        }

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
            level.classes.forEach(function (classRoom) {
                const option = new Option(classRoom.name, classRoom.id);
                option.selected = String(classRoom.id) === String(selectedClassId || '');
                classSelect.add(option);
            });
        }

        updatePath();
    }

    subjectSelect.addEventListener('change', function () {
        fillLevels(subjectSelect.value);
    });

    levelSelect.addEventListener('change', function () {
        fillClasses(subjectSelect.value, levelSelect.value);
    });

    classSelect.addEventListener('change', updatePath);

    window.editPlanning = function (id) {
        const data = schedules[id];
        if (!data) {
            return;
        }

        form.action = updateBaseUrl + '/' + id;
        method.disabled = false;
        document.getElementById('planningFormTitle').innerHTML = '<i class="bi bi-pencil-square" style="color:#fbbf24"></i> Modifier la séance #' + id;
        document.getElementById('planningSubmitButton').innerHTML = '<i class="bi bi-check-lg"></i> Enregistrer les modifications';
        document.getElementById('planningCancelButton').style.display = 'inline-flex';

        subjectSelect.value = data.subject_id || '';
        fillLevels(data.subject_id, data.level_id, data.class_id);
        document.getElementById('planningProfessor').value = data.prof_id || '';
        document.getElementById('planningDay').value = data.day_of_week || 1;
        startTimeInput.value = data.start_time || '13:00';
        endTimeInput.value = data.end_time || '14:00';
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
        document.getElementById('planningFormTitle').innerHTML = '<i class="bi bi-calendar-plus" style="color:#60a5fa"></i> Nouvelle séance';
        document.getElementById('planningSubmitButton').innerHTML = '<i class="bi bi-check-lg"></i> Ajouter au planning';
        document.getElementById('planningCancelButton').style.display = 'none';
        levelSelect.innerHTML = '<option value="">Choisir un niveau</option>';
        classSelect.innerHTML = '<option value="">Choisir le niveau</option>';
        levelSelect.disabled = true;
        classSelect.disabled = true;
        document.getElementById('planningRecurrence').value = 'weekly';
        startTimeInput.value = '13:00';
        endTimeInput.value = '14:00';
        updateDurationPreview();
        document.getElementById('planningValidFrom').value = new Date().toISOString().slice(0, 10);
        document.getElementById('planningStatus').value = 'active';
        updatePath();

        if (scrollToForm) {
            document.getElementById('planningFormCard').scrollIntoView({behavior: 'smooth', block: 'start'});
        }
    };

    const oldSubject = @json(old('subject_id'));
    const oldLevel = @json(old('level_id'));
    const oldClass = @json(old('class_id'));

    if (oldSubject) {
        subjectSelect.value = oldSubject;
        fillLevels(oldSubject, oldLevel, oldClass);
    } else {
        updatePath();
    }

    updateDurationPreview();

})();
</script>
@endpush
