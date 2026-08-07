@extends('layouts.prof')

@section('title', 'Historique des absences')
@section('page_title', 'Historique des absences')
@section('breadcrumb', 'Suivi des présences')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-clock-history"></i> Suivi des présences</span>
        <h1 class="pp-page-title">Historique des absences</h1>
        <p class="pp-page-description">
            Filtrez l’historique selon la structure <strong>Matière → Niveau → Classe</strong>, puis corrigez un statut si nécessaire.
        </p>
    </div>

    <div class="pp-page-actions">
        <a href="{{ route('prof.absences') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-person-check-fill"></i> Faire l’appel
        </a>
    </div>
</section>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-4">
        <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
        <span>{{ session('success') }}</span>
    </div>
@endif

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-funnel-fill"></i> Filtrer l’historique</h2>
            <p class="pp-panel-subtitle">Choisissez une matière, puis un niveau et enfin une classe.</p>
        </div>
        <span class="pp-panel-meta"><i class="bi bi-diagram-3"></i> Matière → Niveau → Classe</span>
    </header>

    <div class="pp-panel-body">
        <form method="GET" action="{{ route('prof.absences.list') }}" class="pp-history-filter" id="historyFilterForm">
            <div class="pp-history-filter-field">
                <label for="historySubject" class="pp-label"><i class="bi bi-journal-bookmark-fill"></i> Matière</label>
                <select id="historySubject" name="subject_id" class="adm-form-select">
                    <option value="">Toutes les matières</option>
                </select>
            </div>

            <div class="pp-history-filter-field">
                <label for="historyLevel" class="pp-label"><i class="bi bi-layers-fill"></i> Niveau</label>
                <select id="historyLevel" name="level_id" class="adm-form-select" disabled>
                    <option value="">Tous les niveaux</option>
                </select>
            </div>

            <div class="pp-history-filter-field">
                <label for="historyClass" class="pp-label"><i class="bi bi-people-fill"></i> Classe</label>
                <select id="historyClass" name="class_id" class="adm-form-select" disabled>
                    <option value="">Toutes les classes</option>
                </select>
            </div>

            <div class="pp-history-filter-actions">
                <button type="submit" class="adm-btn adm-btn-primary">
                    <i class="bi bi-funnel"></i> Appliquer
                </button>

                @if(request()->filled('subject_id') || request()->filled('level_id') || request()->filled('class_id'))
                    <a href="{{ route('prof.absences.list') }}" class="adm-btn adm-btn-ghost">
                        <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>
</section>

<section class="pp-panel pp-section-gap pp-history-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-list-check"></i> Présences enregistrées</h2>
            <p class="pp-panel-subtitle">
                {{ $absences->total() ?? $absences->count() }} enregistrement{{ ($absences->total() ?? $absences->count()) > 1 ? 's' : '' }} trouvé{{ ($absences->total() ?? $absences->count()) > 1 ? 's' : '' }}.
            </p>
        </div>

        <span class="pp-student-count">
            <i class="bi bi-database-check"></i> Historique
        </span>
    </header>

    @if($absences->count() > 0)
        <div class="pp-history-list">
            @foreach($absences as $absence)
                @php
                    $studentName = $absence->user?->name ?? 'Étudiant inconnu';
                    $initials = collect(preg_split('/\s+/', trim($studentName)))
                        ->filter()
                        ->take(2)
                        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                        ->implode('');
                @endphp

                <article class="pp-history-row">
                    <div class="pp-history-student">
                        <span class="pp-history-avatar">{{ $initials ?: 'ET' }}</span>
                        <span class="pp-history-student-copy">
                            <strong>{{ $studentName }}</strong>
                            <small>
                                <i class="bi bi-diagram-3-fill"></i>
                                {{ $absence->subject?->name ?? 'Matière' }}
                                → {{ $absence->level?->name ?? 'Niveau' }}
                                → {{ $absence->classRoom?->name ?? 'Classe' }}
                            </small>
                        </span>
                    </div>

                    <div class="pp-history-date">
                        <span class="pp-history-label">Date</span>
                        <strong>{{ optional($absence->date)->format('d/m/Y') ?? $absence->created_at->format('d/m/Y') }}</strong>
                        <small>Enregistré à {{ $absence->created_at->format('H:i') }}</small>
                    </div>

                    <div class="pp-history-status">
                        <span class="pp-history-label">Statut actuel</span>
                        @if($absence->present)
                            <span class="pp-history-badge is-present"><i class="bi bi-check-circle-fill"></i> Présent</span>
                        @else
                            <span class="pp-history-badge is-absent"><i class="bi bi-x-circle-fill"></i> Absent</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('prof.absences.update', $absence->id) }}" class="pp-history-action">
                        @csrf
                        @method('PUT')

                        <label for="presence-{{ $absence->id }}" class="pp-history-label">Modifier</label>
                        <select
                            id="presence-{{ $absence->id }}"
                            name="present"
                            class="adm-form-select"
                            onchange="this.form.submit()"
                            aria-label="Modifier le statut de {{ $studentName }}"
                        >
                            <option value="1" {{ $absence->present ? 'selected' : '' }}>Présent</option>
                            <option value="0" {{ !$absence->present ? 'selected' : '' }}>Absent</option>
                        </select>
                    </form>
                </article>
            @endforeach
        </div>

        @if(method_exists($absences, 'links'))
            <footer class="pp-history-pagination">
                {{ $absences->appends(request()->query())->links() }}
            </footer>
        @endif
    @else
        <div class="pp-empty pp-history-empty">
            <div>
                <span class="pp-empty-icon"><i class="bi bi-calendar2-check"></i></span>
                <h3>Aucune absence trouvée</h3>
                <p>Aucun enregistrement ne correspond au parcours sélectionné.</p>
                @if(request()->filled('subject_id') || request()->filled('level_id') || request()->filled('class_id'))
                    <a href="{{ route('prof.absences.list') }}" class="adm-btn adm-btn-ghost mt-3">
                        <i class="bi bi-arrow-counterclockwise"></i> Afficher tout l’historique
                    </a>
                @endif
            </div>
        </div>
    @endif
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paths = @json($teachingPaths ?? []);
    const subjectSelect = document.getElementById('historySubject');
    const levelSelect = document.getElementById('historyLevel');
    const classSelect = document.getElementById('historyClass');

    const initial = {
        subject: String(@json(request('subject_id', ''))),
        level: String(@json(request('level_id', ''))),
        classRoom: String(@json(request('class_id', '')))
    };

    function unique(items, idKey, nameKey) {
        const seen = new Map();
        items.forEach(function (item) {
            const id = String(item[idKey]);
            if (!seen.has(id)) seen.set(id, item[nameKey]);
        });
        return Array.from(seen, function ([id, name]) { return { id, name }; });
    }

    function setOptions(select, placeholder, items, selectedValue, allowAll = true) {
        select.innerHTML = '';
        select.add(new Option(placeholder, ''));
        items.forEach(function (item) { select.add(new Option(item.name, item.id)); });
        select.disabled = items.length === 0;

        if (selectedValue && items.some(function (item) { return item.id === String(selectedValue); })) {
            select.value = String(selectedValue);
        }
    }

    function fillSubjects(selected = '') {
        setOptions(subjectSelect, 'Toutes les matières', unique(paths, 'subject_id', 'subject_name'), selected);
    }

    function fillLevels(selected = '') {
        const subjectId = subjectSelect.value;
        const levels = subjectId
            ? unique(paths.filter(function (path) { return String(path.subject_id) === subjectId; }), 'level_id', 'level_name')
            : [];
        setOptions(levelSelect, subjectId ? 'Tous les niveaux' : 'Choisissez d’abord une matière', levels, selected);
    }

    function fillClasses(selected = '') {
        const subjectId = subjectSelect.value;
        const levelId = levelSelect.value;
        const classes = subjectId && levelId
            ? unique(paths.filter(function (path) {
                return String(path.subject_id) === subjectId && String(path.level_id) === levelId;
            }), 'class_id', 'class_name')
            : [];
        setOptions(classSelect, levelId ? 'Toutes les classes' : 'Choisissez d’abord un niveau', classes, selected);
    }

    subjectSelect.addEventListener('change', function () {
        fillLevels();
        fillClasses();
    });

    levelSelect.addEventListener('change', function () {
        fillClasses();
    });

    fillSubjects(initial.subject);
    fillLevels(initial.level);
    fillClasses(initial.classRoom);
});
</script>
@endpush
@endsection
