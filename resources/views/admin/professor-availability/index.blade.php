@extends('layouts.admin')

@section('title', 'Disponibilités professeurs')
@section('page_title', 'Disponibilités professeurs')
@section('breadcrumb', 'Professeurs → Disponibilités → Construction du planning')

@section('content')
<div class="prof-av-page">
    <div class="prof-av-hero">
        <div>
            <span class="prof-av-kicker">
                <i class="bi bi-stars"></i>
                Outil de préparation du planning
            </span>

            <h1>Disponibilités des professeurs</h1>

            <p>
                Enregistrez les disponibilités reçues. Si le professeur est déjà
                affecté à une Matière → Niveau → Classe → Créneau, le système
                crée automatiquement les séances manquantes dans l'emploi du temps.
            </p>
        </div>

        <div class="prof-av-hero-actions">
            <a
                href="{{ route('admin.users.prof-assignments') }}"
                class="prof-av-btn prof-av-btn-soft"
            >
                <i class="bi bi-person-badge"></i>
                Affectations
            </a>

            <a
                href="{{ route('admin.schedule.index', ['mode' => 'final']) }}"
                class="prof-av-btn prof-av-btn-primary"
            >
                <i class="bi bi-calendar3-week"></i>
                Voir le planning final
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="prof-av-alert success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="prof-av-alert warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="prof-av-alert danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    @php
        $receivedProfessors = $professors
            ->filter(function ($professor) use ($availabilityByProfessor) {
                return $availabilityByProfessor
                    ->get($professor->id, collect())
                    ->isNotEmpty();
            })
            ->values();

        $pendingProfessors = $professors
            ->filter(function ($professor) use ($availabilityByProfessor) {
                return $availabilityByProfessor
                    ->get($professor->id, collect())
                    ->isEmpty();
            })
            ->values();

        $availabilityProgressPercent = $stats['total_professors'] > 0
            ? (int) round(
                ($stats['completed'] / $stats['total_professors']) * 100
            )
            : 0;
    @endphp

    <div class="prof-av-stats">
        <article class="prof-av-stat">
            <div class="prof-av-stat-icon"><i class="bi bi-people-fill"></i></div>
            <div>
                <span>Professeurs</span>
                <strong>{{ $stats['total_professors'] }}</strong>
                <small>Total enregistré</small>
            </div>
        </article>

        <article class="prof-av-stat is-success">
            <div class="prof-av-stat-icon"><i class="bi bi-calendar2-check-fill"></i></div>
            <div>
                <span>Disponibilités reçues</span>
                <strong>{{ $stats['completed'] }}</strong>
                <small>Professeurs renseignés</small>
            </div>
        </article>

        <article class="prof-av-stat is-warning">
            <div class="prof-av-stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <span>En attente</span>
                <strong>{{ $stats['pending'] }}</strong>
                <small>Retour à récupérer</small>
            </div>
        </article>

        <article class="prof-av-stat is-info">
            <div class="prof-av-stat-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            <div>
                <span>Créneaux disponibles</span>
                <strong>{{ $stats['availability_slots'] }}</strong>
                <small>Blocs de 1h30</small>
            </div>
        </article>
    </div>

    <div class="prof-av-progress-banner {{ $stats['pending'] === 0 && $stats['total_professors'] > 0 ? 'is-complete' : 'is-building' }}">
        <div class="prof-av-progress-main">
            <span class="prof-av-progress-icon">
                <i class="bi {{ $stats['pending'] === 0 && $stats['total_professors'] > 0 ? 'bi-check2-circle' : 'bi-hourglass-split' }}"></i>
            </span>

            <div class="prof-av-progress-copy">
                <strong>
                    {{ $stats['pending'] === 0 && $stats['total_professors'] > 0
                        ? 'Toutes les disponibilités ont été reçues'
                        : 'Planning en construction' }}
                </strong>
                <span>
                    {{ $stats['completed'] }} professeur(s) sur {{ $stats['total_professors'] }}
                    ont transmis leurs disponibilités.
                </span>

                @if($pendingProfessors->isNotEmpty())
                    <small>
                        <i class="bi bi-clock-history"></i>
                        En attente : {{ $pendingProfessors->pluck('name')->implode(', ') }}
                    </small>
                @elseif($receivedProfessors->isNotEmpty())
                    <small>
                        <i class="bi bi-check-circle-fill"></i>
                        Le planning global peut maintenant être finalisé.
                    </small>
                @endif
            </div>
        </div>

        <div class="prof-av-progress-side">
            <strong>{{ $availabilityProgressPercent }}%</strong>
            <span>Retours reçus</span>
            <div class="prof-av-progress-track" aria-label="Progression des retours">
                <span style="width: {{ $availabilityProgressPercent }}%;"></span>
            </div>
        </div>
    </div>

    <div class="prof-av-tabs" role="tablist">
        <button type="button" class="prof-av-tab {{ $activeTab === 'editor' ? 'active' : '' }}" data-av-tab="editor">
            <i class="bi bi-pencil-square"></i>
            Saisir les disponibilités
        </button>
        <button type="button" class="prof-av-tab {{ $activeTab === 'week' ? 'active' : '' }}" data-av-tab="week">
            <i class="bi bi-calendar-week"></i>
            Vue hebdomadaire
        </button>
        <button type="button" class="prof-av-tab {{ $activeTab === 'summary' ? 'active' : '' }}" data-av-tab="summary">
            <i class="bi bi-table"></i>
            Tableau récapitulatif
        </button>
    </div>

    <section class="prof-av-panel {{ $activeTab === 'editor' ? 'active' : '' }}" data-av-panel="editor">
        @if($selectedProfessor)
            <div class="prof-av-editor-head">
                <div>
                    <span class="prof-av-section-kicker">Disponibilités déclarées</span>
                    <h2>Renseigner un professeur</h2>
                    <p>
                        Cochez uniquement les créneaux pendant lesquels
                        le professeur peut réellement assurer un cours.
                    </p>
                </div>

                <form method="GET" action="{{ route('admin.professor-availability.index') }}" class="prof-av-prof-select-form">
                    <input type="hidden" name="tab" value="editor">
                    <label for="professor_id">Professeur</label>
                    <select name="professor_id" id="professor_id" class="prof-av-select" onchange="this.form.submit()">
                        @foreach($professors as $professor)
                            <option
                                value="{{ $professor->id }}"
                                {{ (int) $selectedProfessor->id === (int) $professor->id ? 'selected' : '' }}
                            >
                                {{ $professor->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @php
                $selectedProfessorColor = $professorColors[$selectedProfessor->id]
                    ?? [
                        'hex' => '#7C3AED',
                        'rgb' => '124,58,237',
                        'label' => 'Violet',
                    ];
            @endphp

            <div
                class="prof-av-selected-prof"
                style="
                    --prof-color: {{ $selectedProfessorColor['hex'] }};
                    --prof-rgb: {{ $selectedProfessorColor['rgb'] }};
                "
            >
                <div class="prof-av-avatar">
                    {{ mb_strtoupper(mb_substr($selectedProfessor->name ?: '?', 0, 1)) }}
                </div>
                <div class="prof-av-selected-main">
                    <strong>{{ $selectedProfessor->name }}</strong>
                    <span>{{ $selectedProfessor->email }}</span>
                </div>

                <div class="prof-av-teaching-inline">
                    <span>Peut intervenir sur</span>
                    <div>
                        @forelse(($teachingSummary[$selectedProfessor->id] ?? collect())->take(4) as $teaching)
                            <span class="prof-av-path-chip">{{ $teaching }}</span>
                        @empty
                            <span class="prof-av-empty-chip">Aucune affectation pédagogique définie</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('admin.professor-availability.update', $selectedProfessor) }}"
                id="profAvailabilityForm"
            >
                @csrf
                @method('PUT')

                <div class="prof-av-grid-toolbar">
                    <div>
                        <strong>Créneaux de 1h30</strong>
                        <span>09:00 → 19:30</span>
                    </div>
                    <button type="button" class="prof-av-text-btn" id="clearAvailability">
                        <i class="bi bi-eraser"></i>
                        Tout décocher
                    </button>
                </div>

                <div class="prof-av-scroll">
                    <div class="prof-av-editor-grid">
                        <div class="prof-av-grid-corner">
                            Horaire
                        </div>

                        @foreach($days as $dayNumber => $dayLabel)
                            <div class="prof-av-day-header">
                                <strong>{{ mb_substr($dayLabel, 0, 3) }}</strong>
                                <button
                                    type="button"
                                    class="prof-av-day-toggle"
                                    data-day="{{ $dayNumber }}"
                                    title="Sélectionner / désélectionner la journée"
                                >
                                    Tout
                                </button>
                            </div>
                        @endforeach

                        @foreach($timeSlots as $slot)
                            <div class="prof-av-time-label">
                                <span>C{{ $slot['index'] }}</span>
                                <strong>{{ $slot['start'] }}</strong>
                                <small>{{ $slot['end'] }}</small>
                            </div>

                            @foreach($days as $dayNumber => $dayLabel)
                                @php
                                    $key = $dayNumber . '|' . $slot['start'] . '|' . $slot['end'];
                                    $checked = $selectedAvailabilityKeys->contains($key);
                                @endphp

                                <label
                                    class="prof-av-slot-check {{ $checked ? 'is-checked' : '' }}"
                                    data-day-cell="{{ $dayNumber }}"
                                >
                                    <input
                                        type="checkbox"
                                        name="slots[]"
                                        value="{{ $key }}"
                                        data-day-checkbox="{{ $dayNumber }}"
                                        {{ $checked ? 'checked' : '' }}
                                    >
                                    <span class="prof-av-check-icon">
                                        <i class="bi bi-check-lg"></i>
                                    </span>
                                    <small>Disponible</small>
                                </label>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <div class="prof-av-form-footer">
                    <div class="prof-av-form-hint">
                        <i class="bi bi-info-circle"></i>
                        Si aucune disponibilité n'est encore reçue,
                        laissez le professeur vide et revenez plus tard.
                    </div>

                    <button type="submit" class="prof-av-btn prof-av-btn-primary">
                        <i class="bi bi-cloud-check-fill"></i>
                        Enregistrer les disponibilités
                    </button>
                </div>
            </form>

            @if(($availabilityByProfessor->get($selectedProfessor->id, collect()))->isNotEmpty())
                <form
                    method="POST"
                    action="{{ route('admin.professor-availability.destroy', $selectedProfessor) }}"
                    class="prof-av-clear-form"
                    onsubmit="return confirm('Effacer toutes les disponibilités de ce professeur ?')"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="prof-av-danger-link">
                        <i class="bi bi-trash3"></i>
                        Effacer toutes les disponibilités de {{ $selectedProfessor->name }}
                    </button>
                </form>
            @endif
        @else
            <div class="prof-av-empty-state">
                <i class="bi bi-person-plus"></i>
                <h3>Aucun professeur</h3>
                <p>Créez d'abord un compte professeur pour renseigner ses disponibilités.</p>
                <a href="{{ route('admin.professors.create') }}" class="prof-av-btn prof-av-btn-primary">
                    Ajouter un professeur
                </a>
            </div>
        @endif
    </section>

    <section class="prof-av-panel {{ $activeTab === 'week' ? 'active' : '' }}" data-av-panel="week">
        <div class="prof-av-section-head prof-av-week-head">
            <div>
                <span class="prof-av-section-kicker">Vue type agenda</span>
                <h2>
                    @if($weekProfessor)
                        Disponibilités de {{ $weekProfessor->name }}
                    @else
                        Qui est disponible à quel moment ?
                    @endif
                </h2>
                <p>
                    Chaque professeur possède sa propre couleur.
                    Vous pouvez afficher tout le monde ou isoler un professeur.
                </p>
            </div>

            <form
                method="GET"
                action="{{ route('admin.professor-availability.index') }}"
                class="prof-av-week-filter"
            >
                <input type="hidden" name="tab" value="week">

                <label for="week_professor_id">
                    Voir les disponibilités de
                </label>

                <select
                    name="week_professor_id"
                    id="week_professor_id"
                    class="prof-av-select"
                    onchange="this.form.submit()"
                >
                    <option value="0">
                        Tous les professeurs
                    </option>

                    @foreach($professors as $professor)
                        <option
                            value="{{ $professor->id }}"
                            {{ (int) $weekProfessorId === (int) $professor->id ? 'selected' : '' }}
                        >
                            {{ $professor->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="prof-av-legend-wrap">
            <div class="prof-av-legend-title">
                <i class="bi bi-palette-fill"></i>
                Couleurs des professeurs
            </div>

            <div class="prof-av-legend">
                <a
                    href="{{ route('admin.professor-availability.index', ['tab' => 'week']) }}"
                    class="prof-av-legend-item is-all {{ $weekProfessorId === 0 ? 'active' : '' }}"
                >
                    <span class="prof-av-legend-all-icon">
                        <i class="bi bi-people-fill"></i>
                    </span>
                    <strong>Tous</strong>
                </a>

                @foreach($professors as $professor)
                    @php
                        $color = $professorColors[$professor->id]
                            ?? [
                                'hex' => '#7C3AED',
                                'rgb' => '124,58,237',
                                'label' => 'Violet',
                            ];

                        $availabilityCount = $availabilityByProfessor
                            ->get($professor->id, collect())
                            ->count();
                    @endphp

                    <a
                        href="{{ route('admin.professor-availability.index', [
                            'tab' => 'week',
                            'week_professor_id' => $professor->id,
                        ]) }}"
                        class="prof-av-legend-item {{ (int) $weekProfessorId === (int) $professor->id ? 'active' : '' }}"
                        style="
                            --prof-color: {{ $color['hex'] }};
                            --prof-rgb: {{ $color['rgb'] }};
                        "
                        title="{{ $color['label'] }} — {{ $availabilityCount }} créneau(x)"
                    >
                        <span class="prof-av-color-dot"></span>
                        <strong>{{ $professor->name }}</strong>
                        <span class="prof-av-legend-state {{ $availabilityCount > 0 ? 'is-received' : 'is-pending' }}">
                            <i class="bi {{ $availabilityCount > 0 ? 'bi-check-circle-fill' : 'bi-hourglass-split' }}"></i>
                            {{ $availabilityCount > 0 ? 'Reçu' : 'En attente' }}
                        </span>
                        <small>{{ $availabilityCount }}</small>
                    </a>
                @endforeach
            </div>
        </div>

        @if($weekProfessor)
            @php
                $weekColor = $professorColors[$weekProfessor->id]
                    ?? [
                        'hex' => '#7C3AED',
                        'rgb' => '124,58,237',
                        'label' => 'Violet',
                    ];

                $weekAvailabilityCount = $availabilityByProfessor
                    ->get($weekProfessor->id, collect())
                    ->count();
            @endphp

            <div
                class="prof-av-week-focus"
                style="
                    --prof-color: {{ $weekColor['hex'] }};
                    --prof-rgb: {{ $weekColor['rgb'] }};
                "
            >
                <span class="prof-av-week-focus-avatar">
                    {{ mb_strtoupper(mb_substr($weekProfessor->name ?: '?', 0, 1)) }}
                </span>

                <div>
                    <strong>{{ $weekProfessor->name }}</strong>
                    <span>
                        {{ $weekAvailabilityCount }} créneau(x) disponible(s)
                        · {{ $weekColor['label'] }}
                    </span>
                </div>

                <a
                    href="{{ route('admin.professor-availability.index', [
                        'tab' => 'editor',
                        'professor_id' => $weekProfessor->id,
                    ]) }}"
                    class="prof-av-focus-edit"
                >
                    <i class="bi bi-pencil-square"></i>
                    Modifier
                </a>
            </div>
        @endif

        <div class="prof-av-scroll">
            <div class="prof-av-week-grid">
                <div class="prof-av-week-corner">Horaire</div>

                @foreach($days as $dayLabel)
                    <div class="prof-av-week-day">
                        {{ $dayLabel }}
                    </div>
                @endforeach

                @foreach($timeSlots as $slot)
                    <div class="prof-av-week-time">
                        <strong>{{ $slot['start'] }}</strong>
                        <span>{{ $slot['end'] }}</span>
                    </div>

                    @foreach($days as $dayNumber => $dayLabel)
                        @php
                            $cell = $availabilityMatrix->get(
                                $dayNumber . '|' . $slot['start'],
                                collect()
                            );

                            if ($weekProfessorId > 0) {
                                $cell = $cell
                                    ->where(
                                        'prof_id',
                                        $weekProfessorId
                                    )
                                    ->values();
                            }
                        @endphp

                        <div
                            class="prof-av-week-cell {{ $cell->isNotEmpty() ? 'has-professors' : '' }}"
                        >
                            @if($cell->isEmpty())
                                <span class="prof-av-free-empty">—</span>
                            @else
                                @foreach($cell as $availability)
                                    @php
                                        $color = $professorColors[$availability->prof_id]
                                            ?? [
                                                'hex' => '#3B82F6',
                                                'rgb' => '59,130,246',
                                                'label' => 'Bleu',
                                            ];
                                    @endphp

                                    <a
                                        href="{{ route('admin.professor-availability.index', [
                                            'tab' => 'editor',
                                            'professor_id' => $availability->prof_id,
                                        ]) }}"
                                        class="prof-av-prof-chip"
                                        style="
                                            --prof-color: {{ $color['hex'] }};
                                            --prof-rgb: {{ $color['rgb'] }};
                                        "
                                        title="Modifier les disponibilités de {{ optional($availability->professor)->name ?: 'ce professeur' }}"
                                    >
                                        <span class="prof-av-color-dot"></span>
                                        <span>
                                            {{ optional($availability->professor)->name ?: 'Professeur' }}
                                        </span>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>

    <section class="prof-av-panel {{ $activeTab === 'summary' ? 'active' : '' }}" data-av-panel="summary">
        <div class="prof-av-section-head prof-av-summary-head">
            <div>
                <span class="prof-av-section-kicker">Récapitulatif opérationnel</span>
                <h2>Qui peut faire quoi et quand ?</h2>
                <p>
                    Tous les professeurs sont visibles dans ce tableau.
                    La couleur permet de les reconnaître immédiatement,
                    puis « Voir les disponibilités » affiche le planning du professeur choisi.
                </p>
            </div>

            <div class="prof-av-search-wrap">
                <i class="bi bi-search"></i>
                <input
                    type="search"
                    id="profAvailabilitySearch"
                    placeholder="Rechercher un professeur..."
                >
            </div>
        </div>

        <div class="prof-av-table-wrap">
            <table class="prof-av-table">
                <thead>
                    <tr>
                        <th>Professeur</th>
                        <th>Peut intervenir sur</th>
                        <th>Disponibilités</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="profAvailabilitySummaryBody">
                    @foreach($professors as $professor)
                        @php
                            $profAvailabilities = $availabilityByProfessor->get($professor->id, collect());
                            $profTeaching = $teachingSummary[$professor->id] ?? collect();
                            $color = $professorColors[$professor->id]
                                ?? [
                                    'hex' => '#7C3AED',
                                    'rgb' => '124,58,237',
                                    'label' => 'Violet',
                                ];
                        @endphp

                        <tr
                            data-prof-row
                            data-search="{{ mb_strtolower($professor->name . ' ' . $professor->email) }}"
                            style="
                                --prof-color: {{ $color['hex'] }};
                                --prof-rgb: {{ $color['rgb'] }};
                            "
                        >
                            <td>
                                <div class="prof-av-person-cell">
                                    <span class="prof-av-mini-avatar">
                                        {{ mb_strtoupper(mb_substr($professor->name ?: '?', 0, 1)) }}
                                    </span>
                                    <div>
                                        <strong>
                                            <span class="prof-av-inline-dot"></span>
                                            {{ $professor->name }}
                                        </strong>
                                        <span>
                                            {{ $professor->email }}
                                            · {{ $color['label'] }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="prof-av-path-list">
                                    @forelse($profTeaching->take(3) as $teaching)
                                        <span>{{ $teaching }}</span>
                                    @empty
                                        <em>À définir</em>
                                    @endforelse
                                    @if($profTeaching->count() > 3)
                                        <small>+{{ $profTeaching->count() - 3 }} autre(s)</small>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if($profAvailabilities->isEmpty())
                                    <span class="prof-av-pending-text">
                                        <i class="bi bi-clock-history"></i>
                                        En attente du retour
                                    </span>
                                @else
                                    <div class="prof-av-day-summary">
                                        @foreach($days as $dayNumber => $dayLabel)
                                            @php
                                                $dayItems = $profAvailabilities
                                                    ->where('day_of_week', $dayNumber)
                                                    ->sortBy('start_time');
                                            @endphp
                                            @if($dayItems->isNotEmpty())
                                                <span
                                                    title="{{ $dayItems->map(function($item){ return $item->range_label; })->implode(', ') }}"
                                                >
                                                    {{ mb_substr($dayLabel, 0, 3) }}
                                                    <strong>{{ $dayItems->count() }}</strong>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if($profAvailabilities->isNotEmpty())
                                    <span class="prof-av-status is-complete">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Reçu
                                    </span>
                                @else
                                    <span class="prof-av-status is-pending">
                                        <i class="bi bi-hourglass-split"></i>
                                        En attente
                                    </span>
                                @endif
                            </td>

                            <td class="prof-av-action-cell">
                                <div class="prof-av-row-actions">
                                    <a
                                        href="{{ route('admin.professor-availability.index', [
                                            'tab' => 'week',
                                            'week_professor_id' => $professor->id,
                                        ]) }}"
                                        class="prof-av-see-btn"
                                        title="Voir uniquement les disponibilités de {{ $professor->name }}"
                                    >
                                        <i class="bi bi-calendar2-week"></i>
                                        Voir les disponibilités
                                    </a>

                                    <a
                                        href="{{ route('admin.professor-availability.index', [
                                            'tab' => 'editor',
                                            'professor_id' => $professor->id,
                                        ]) }}"
                                        class="prof-av-icon-btn"
                                        title="Modifier les disponibilités"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="prof-av-no-result" id="profAvailabilityNoResult" hidden>
            Aucun professeur ne correspond à cette recherche.
        </div>
    </section>
</div>

<style>
.prof-av-page{display:flex;flex-direction:column;gap:1.15rem}.prof-av-hero{position:relative;display:flex;justify-content:space-between;gap:2rem;align-items:center;padding:1.6rem;border:1px solid rgba(124,58,237,.16);border-radius:22px;background:radial-gradient(circle at 88% 18%,rgba(124,58,237,.18),transparent 34%),linear-gradient(145deg,rgba(15,23,42,.96),rgba(9,15,28,.95));overflow:hidden}.prof-av-hero:after{content:"";position:absolute;width:190px;height:190px;right:-70px;bottom:-110px;border-radius:50%;background:rgba(59,130,246,.08);filter:blur(4px)}.prof-av-kicker,.prof-av-section-kicker{display:inline-flex;align-items:center;gap:.45rem;color:#a78bfa;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em}.prof-av-hero h1{margin:.45rem 0 .45rem;font-size:clamp(1.45rem,2vw,2rem);color:#fff}.prof-av-hero p,.prof-av-section-head p,.prof-av-editor-head p{max-width:760px;margin:0;color:var(--adm-text-muted);font-size:.82rem;line-height:1.6}.prof-av-hero-actions{position:relative;z-index:2;display:flex;gap:.6rem;flex-wrap:wrap}.prof-av-btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;min-height:42px;padding:.65rem .95rem;border:0;border-radius:12px;text-decoration:none;font-size:.75rem;font-weight:800;cursor:pointer;transition:.2s ease}.prof-av-btn:hover{transform:translateY(-1px)}.prof-av-btn-primary{color:#fff;background:linear-gradient(135deg,#7c3aed,#4f46e5);box-shadow:0 10px 24px rgba(79,70,229,.22)}.prof-av-btn-soft{color:#dbeafe;background:rgba(255,255,255,.055);border:1px solid rgba(255,255,255,.075)}.prof-av-alert{display:flex;align-items:center;gap:.6rem;padding:.8rem 1rem;border-radius:13px;font-size:.76rem}.prof-av-alert.success{color:#bbf7d0;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2)}.prof-av-alert.warning{color:#fde68a;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2)}.prof-av-alert.danger{color:#fecaca;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2)}.prof-av-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.8rem}.prof-av-stat{display:flex;gap:.8rem;align-items:center;padding:1rem;border-radius:17px;border:1px solid rgba(255,255,255,.055);background:rgba(15,23,42,.74)}.prof-av-stat-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:13px;color:#c4b5fd;background:rgba(124,58,237,.12);font-size:1rem}.prof-av-stat.is-success .prof-av-stat-icon{color:#86efac;background:rgba(34,197,94,.1)}.prof-av-stat.is-warning .prof-av-stat-icon{color:#fde68a;background:rgba(245,158,11,.1)}.prof-av-stat.is-info .prof-av-stat-icon{color:#93c5fd;background:rgba(59,130,246,.1)}.prof-av-stat div:last-child{min-width:0;display:grid}.prof-av-stat span{color:var(--adm-text-muted);font-size:.64rem}.prof-av-stat strong{color:#fff;font-size:1.3rem;line-height:1.15}.prof-av-stat small{color:rgba(255,255,255,.34);font-size:.58rem}.prof-av-tabs{display:flex;gap:.45rem;padding:.35rem;border:1px solid rgba(255,255,255,.05);border-radius:14px;background:rgba(15,23,42,.58);width:max-content;max-width:100%;overflow:auto}.prof-av-tab{display:flex;align-items:center;gap:.45rem;padding:.6rem .8rem;border:0;border-radius:10px;color:var(--adm-text-muted);background:transparent;font-size:.7rem;font-weight:800;white-space:nowrap;cursor:pointer}.prof-av-tab.active{color:#fff;background:rgba(124,58,237,.18);box-shadow:inset 0 0 0 1px rgba(167,139,250,.12)}.prof-av-panel{display:none;padding:1.15rem;border:1px solid rgba(255,255,255,.055);border-radius:19px;background:rgba(10,17,31,.77)}.prof-av-panel.active{display:block}.prof-av-editor-head,.prof-av-section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1rem}.prof-av-editor-head h2,.prof-av-section-head h2{margin:.2rem 0 .2rem;color:#fff;font-size:1.05rem}.prof-av-prof-select-form{min-width:250px}.prof-av-prof-select-form label{display:block;margin-bottom:.3rem;color:var(--adm-text-muted);font-size:.61rem;font-weight:700}.prof-av-select{width:100%;min-height:42px;padding:.55rem .75rem;border:1px solid rgba(255,255,255,.08);border-radius:11px;color:#fff;background:#101a2d;outline:none;font-size:.75rem}.prof-av-selected-prof{display:grid;grid-template-columns:auto minmax(150px,.7fr) minmax(280px,1.8fr);gap:.8rem;align-items:center;margin-bottom:1rem;padding:.8rem;border-radius:14px;background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.05)}.prof-av-avatar,.prof-av-mini-avatar{display:grid;place-items:center;border-radius:13px;color:#fff;background:linear-gradient(135deg,#7c3aed,#2563eb);font-weight:900}.prof-av-avatar{width:48px;height:48px;font-size:1rem}.prof-av-selected-main{display:grid}.prof-av-selected-main strong{color:#fff;font-size:.84rem}.prof-av-selected-main span{color:var(--adm-text-muted);font-size:.65rem}.prof-av-teaching-inline>span{display:block;margin-bottom:.35rem;color:var(--adm-text-muted);font-size:.58rem;text-transform:uppercase;letter-spacing:.05em}.prof-av-teaching-inline>div{display:flex;gap:.35rem;flex-wrap:wrap}.prof-av-path-chip,.prof-av-empty-chip{display:inline-flex;padding:.3rem .48rem;border-radius:8px;font-size:.58rem}.prof-av-path-chip{color:#bfdbfe;background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.13)}.prof-av-empty-chip{color:#94a3b8;background:rgba(148,163,184,.07)}.prof-av-grid-toolbar{display:flex;justify-content:space-between;align-items:center;gap:1rem;margin:.4rem 0 .65rem}.prof-av-grid-toolbar>div{display:grid}.prof-av-grid-toolbar strong{color:#fff;font-size:.75rem}.prof-av-grid-toolbar span{color:var(--adm-text-muted);font-size:.6rem}.prof-av-text-btn{border:0;background:transparent;color:#a78bfa;font-size:.66rem;font-weight:800;cursor:pointer}.prof-av-scroll{width:100%;overflow:auto;border-radius:14px}.prof-av-editor-grid,.prof-av-week-grid{display:grid;grid-template-columns:100px repeat(7,minmax(112px,1fr));min-width:900px;border:1px solid rgba(255,255,255,.05);border-radius:14px;overflow:hidden}.prof-av-grid-corner,.prof-av-day-header,.prof-av-time-label,.prof-av-slot-check,.prof-av-week-corner,.prof-av-week-day,.prof-av-week-time,.prof-av-week-cell{border-right:1px solid rgba(255,255,255,.045);border-bottom:1px solid rgba(255,255,255,.045)}.prof-av-grid-corner,.prof-av-week-corner{display:grid;place-items:center;color:#64748b;background:#0d1627;font-size:.58rem;text-transform:uppercase;letter-spacing:.07em}.prof-av-day-header{display:flex;align-items:center;justify-content:space-between;gap:.3rem;padding:.6rem;background:#0d1627}.prof-av-day-header strong{color:#e2e8f0;font-size:.68rem;text-transform:capitalize}.prof-av-day-toggle{padding:.2rem .35rem;border:0;border-radius:6px;color:#a78bfa;background:rgba(124,58,237,.09);font-size:.52rem;cursor:pointer}.prof-av-time-label{min-height:66px;display:grid;align-content:center;padding:.45rem .55rem;background:rgba(13,22,39,.78)}.prof-av-time-label span{color:#a78bfa;font-size:.5rem;font-weight:900}.prof-av-time-label strong{color:#fff;font-size:.7rem}.prof-av-time-label small{color:#64748b;font-size:.58rem}.prof-av-slot-check{position:relative;min-height:66px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.2rem;color:#64748b;background:rgba(255,255,255,.012);cursor:pointer;transition:.18s ease}.prof-av-slot-check:hover{background:rgba(124,58,237,.05)}.prof-av-slot-check input{position:absolute;opacity:0;pointer-events:none}.prof-av-check-icon{width:23px;height:23px;display:grid;place-items:center;border:1px solid rgba(148,163,184,.18);border-radius:7px;background:rgba(148,163,184,.04);font-size:.7rem}.prof-av-slot-check small{font-size:.52rem}.prof-av-slot-check.is-checked{color:#bbf7d0;background:linear-gradient(145deg,rgba(34,197,94,.11),rgba(16,185,129,.045))}.prof-av-slot-check.is-checked .prof-av-check-icon{color:#052e16;background:#4ade80;border-color:#4ade80}.prof-av-form-footer{display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-top:.85rem}.prof-av-form-hint{display:flex;gap:.4rem;align-items:flex-start;max-width:620px;color:var(--adm-text-muted);font-size:.61rem;line-height:1.45}.prof-av-clear-form{margin-top:.65rem;text-align:right}.prof-av-danger-link{border:0;background:transparent;color:#fca5a5;font-size:.61rem;cursor:pointer}.prof-av-week-grid{grid-template-columns:86px repeat(7,minmax(145px,1fr))}.prof-av-week-day{padding:.65rem;text-align:center;color:#e2e8f0;background:#0d1627;font-size:.67rem;font-weight:800}.prof-av-week-time{min-height:82px;display:grid;align-content:center;padding:.55rem;background:#0d1627}.prof-av-week-time strong{color:#fff;font-size:.68rem}.prof-av-week-time span{color:#64748b;font-size:.56rem}.prof-av-week-cell{min-height:82px;display:flex;align-content:flex-start;flex-wrap:wrap;gap:.3rem;padding:.45rem;background:rgba(255,255,255,.01)}.prof-av-week-cell.has-professors{background:rgba(59,130,246,.025)}.prof-av-free-empty{margin:auto;color:rgba(148,163,184,.18)}.prof-av-prof-chip,.prof-av-more-chip{height:max-content;padding:.33rem .42rem;border-radius:8px;text-decoration:none;font-size:.57rem;font-weight:750}.prof-av-prof-chip{color:#dbeafe;background:rgba(59,130,246,.14);border:1px solid rgba(96,165,250,.13)}.prof-av-prof-chip:hover{color:#fff;background:rgba(59,130,246,.22)}.prof-av-more-chip{color:#c4b5fd;background:rgba(124,58,237,.1)}.prof-av-summary-head{align-items:center}.prof-av-search-wrap{position:relative;min-width:260px}.prof-av-search-wrap i{position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#64748b;font-size:.75rem}.prof-av-search-wrap input{width:100%;min-height:40px;padding:.55rem .7rem .55rem 2rem;border:1px solid rgba(255,255,255,.07);border-radius:10px;color:#fff;background:#0d1627;outline:none;font-size:.7rem}.prof-av-table-wrap{overflow:auto;border:1px solid rgba(255,255,255,.045);border-radius:14px}.prof-av-table{width:100%;min-width:900px;border-collapse:collapse}.prof-av-table th{padding:.7rem;text-align:left;color:#64748b;background:#0d1627;font-size:.57rem;text-transform:uppercase;letter-spacing:.055em}.prof-av-table td{padding:.75rem;border-top:1px solid rgba(255,255,255,.04);vertical-align:middle}.prof-av-person-cell{display:flex;align-items:center;gap:.55rem}.prof-av-mini-avatar{width:34px;height:34px;font-size:.7rem;border-radius:10px}.prof-av-person-cell>div{display:grid}.prof-av-person-cell strong{color:#fff;font-size:.7rem}.prof-av-person-cell span{color:#64748b;font-size:.58rem}.prof-av-path-list{display:flex;gap:.3rem;flex-wrap:wrap;max-width:430px}.prof-av-path-list span{padding:.27rem .38rem;border-radius:7px;color:#bfdbfe;background:rgba(59,130,246,.07);font-size:.54rem}.prof-av-path-list em{color:#64748b;font-size:.58rem;font-style:normal}.prof-av-path-list small{align-self:center;color:#a78bfa;font-size:.52rem}.prof-av-day-summary{display:flex;gap:.28rem;flex-wrap:wrap}.prof-av-day-summary>span{display:inline-flex;align-items:center;gap:.24rem;padding:.27rem .36rem;border-radius:7px;color:#cbd5e1;background:rgba(255,255,255,.035);font-size:.54rem}.prof-av-day-summary strong{color:#86efac}.prof-av-pending-text{display:inline-flex;align-items:center;gap:.3rem;color:#fbbf24;font-size:.58rem}.prof-av-status{display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .43rem;border-radius:999px;font-size:.55rem;font-weight:800}.prof-av-status.is-complete{color:#86efac;background:rgba(34,197,94,.08)}.prof-av-status.is-pending{color:#fde68a;background:rgba(245,158,11,.08)}.prof-av-action-cell{text-align:right}.prof-av-icon-btn{width:32px;height:32px;display:inline-grid;place-items:center;border-radius:9px;color:#c4b5fd;background:rgba(124,58,237,.08);text-decoration:none}.prof-av-no-result{text-align:center;padding:1rem;color:#64748b;font-size:.68rem}.prof-av-empty-state{text-align:center;padding:3rem 1rem}.prof-av-empty-state>i{font-size:2rem;color:#7c3aed}.prof-av-empty-state h3{margin:.6rem 0 .2rem;color:#fff}.prof-av-empty-state p{color:var(--adm-text-muted);font-size:.7rem}.prof-av-empty-state .prof-av-btn{margin-top:.7rem}@media(max-width:1100px){.prof-av-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.prof-av-hero{align-items:flex-start;flex-direction:column}.prof-av-selected-prof{grid-template-columns:auto 1fr}.prof-av-teaching-inline{grid-column:1/-1}}@media(max-width:700px){.prof-av-stats{grid-template-columns:1fr}.prof-av-panel{padding:.8rem}.prof-av-editor-head,.prof-av-section-head,.prof-av-form-footer{align-items:stretch;flex-direction:column}.prof-av-prof-select-form,.prof-av-search-wrap{min-width:0;width:100%}.prof-av-hero-actions{width:100%}.prof-av-hero-actions .prof-av-btn{flex:1}.prof-av-selected-prof{grid-template-columns:auto 1fr}}


/* Statut global des retours de disponibilités */
.prof-av-progress-banner{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.1rem;border:1px solid rgba(245,158,11,.22);border-radius:17px;background:linear-gradient(135deg,rgba(245,158,11,.085),rgba(15,23,42,.82))}.prof-av-progress-banner.is-complete{border-color:rgba(34,197,94,.22);background:linear-gradient(135deg,rgba(34,197,94,.085),rgba(15,23,42,.82))}.prof-av-progress-main{display:flex;align-items:center;gap:.8rem;min-width:0}.prof-av-progress-icon{width:42px;height:42px;display:grid;place-items:center;flex:0 0 42px;border-radius:13px;color:#fde68a;background:rgba(245,158,11,.12);font-size:1rem}.prof-av-progress-banner.is-complete .prof-av-progress-icon{color:#86efac;background:rgba(34,197,94,.12)}.prof-av-progress-copy{display:grid;gap:.17rem;min-width:0}.prof-av-progress-copy strong{color:#fff;font-size:.82rem}.prof-av-progress-copy span{color:#cbd5e1;font-size:.66rem}.prof-av-progress-copy small{display:flex;align-items:center;gap:.3rem;color:#fbbf24;font-size:.59rem}.prof-av-progress-banner.is-complete .prof-av-progress-copy small{color:#86efac}.prof-av-progress-side{width:170px;flex:0 0 170px;display:grid;gap:.16rem}.prof-av-progress-side>strong{color:#fff;font-size:1rem}.prof-av-progress-side>span{color:#64748b;font-size:.55rem}.prof-av-progress-track{height:6px;overflow:hidden;border-radius:999px;background:rgba(255,255,255,.07)}.prof-av-progress-track>span{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#f59e0b,#fbbf24)}.prof-av-progress-banner.is-complete .prof-av-progress-track>span{background:linear-gradient(90deg,#22c55e,#4ade80)}.prof-av-legend-state{display:inline-flex;align-items:center;gap:.2rem;padding:.18rem .32rem;border-radius:999px;font-size:.48rem;font-weight:850;white-space:nowrap}.prof-av-legend-state.is-received{color:#86efac;background:rgba(34,197,94,.09)}.prof-av-legend-state.is-pending{color:#fde68a;background:rgba(245,158,11,.09)}@media(max-width:760px){.prof-av-progress-banner{align-items:flex-start;flex-direction:column}.prof-av-progress-side{width:100%;flex-basis:auto}}
</style>

<style>
/* =========================================================
   COULEURS PROFESSEURS + FILTRE DE DISPONIBILITÉS
   ========================================================= */

.prof-av-selected-prof {
    border-color: rgba(var(--prof-rgb), .22);
    background:
        linear-gradient(
            135deg,
            rgba(var(--prof-rgb), .09),
            rgba(255,255,255,.018)
        );
}

.prof-av-selected-prof .prof-av-avatar,
.prof-av-week-focus-avatar,
.prof-av-mini-avatar {
    color: #fff;
    background:
        linear-gradient(
            135deg,
            var(--prof-color),
            rgba(var(--prof-rgb), .66)
        );
    box-shadow:
        0 8px 22px rgba(var(--prof-rgb), .20);
}

.prof-av-week-head {
    align-items: flex-end;
}

.prof-av-week-filter {
    min-width: 290px;
}

.prof-av-week-filter label {
    display: block;
    margin-bottom: .3rem;
    color: var(--adm-text-muted);
    font-size: .61rem;
    font-weight: 750;
}

.prof-av-legend-wrap {
    margin: 0 0 1rem;
    padding: .8rem;
    border: 1px solid rgba(255,255,255,.05);
    border-radius: 14px;
    background: rgba(255,255,255,.018);
}

.prof-av-legend-title {
    display: flex;
    align-items: center;
    gap: .4rem;
    margin-bottom: .55rem;
    color: #cbd5e1;
    font-size: .63rem;
    font-weight: 850;
    text-transform: uppercase;
    letter-spacing: .05em;
}

.prof-av-legend-title i {
    color: #a78bfa;
}

.prof-av-legend {
    display: flex;
    flex-wrap: wrap;
    gap: .45rem;
}

.prof-av-legend-item {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    min-height: 34px;
    padding: .38rem .58rem;
    border: 1px solid rgba(var(--prof-rgb), .22);
    border-radius: 10px;
    color: #e5e7eb;
    background: rgba(var(--prof-rgb), .07);
    text-decoration: none;
    transition: .18s ease;
}

.prof-av-legend-item:hover,
.prof-av-legend-item.active {
    color: #fff;
    border-color: rgba(var(--prof-rgb), .48);
    background: rgba(var(--prof-rgb), .16);
    box-shadow: 0 8px 22px rgba(var(--prof-rgb), .10);
    transform: translateY(-1px);
}

.prof-av-legend-item strong {
    font-size: .6rem;
    font-weight: 800;
}

.prof-av-legend-item small {
    display: grid;
    min-width: 20px;
    height: 20px;
    place-items: center;
    border-radius: 999px;
    color: var(--prof-color);
    background: rgba(var(--prof-rgb), .14);
    font-size: .52rem;
    font-weight: 900;
}

.prof-av-legend-item.is-all {
    --prof-color: #94A3B8;
    --prof-rgb: 148,163,184;
}

.prof-av-legend-all-icon {
    display: grid;
    width: 20px;
    height: 20px;
    place-items: center;
    border-radius: 6px;
    color: #e2e8f0;
    background: rgba(148,163,184,.12);
    font-size: .62rem;
}

.prof-av-color-dot,
.prof-av-inline-dot {
    display: inline-block;
    width: 9px;
    height: 9px;
    flex: 0 0 9px;
    border-radius: 999px;
    background: var(--prof-color);
    box-shadow: 0 0 0 3px rgba(var(--prof-rgb), .13);
}

.prof-av-inline-dot {
    width: 7px;
    height: 7px;
    flex-basis: 7px;
    margin-right: .25rem;
    vertical-align: 1px;
}

.prof-av-week-focus {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: .65rem;
    align-items: center;
    margin-bottom: .85rem;
    padding: .7rem .8rem;
    border: 1px solid rgba(var(--prof-rgb), .24);
    border-radius: 13px;
    background:
        linear-gradient(
            135deg,
            rgba(var(--prof-rgb), .11),
            rgba(var(--prof-rgb), .025)
        );
}

.prof-av-week-focus-avatar {
    display: grid;
    width: 38px;
    height: 38px;
    place-items: center;
    border-radius: 11px;
    font-size: .78rem;
    font-weight: 900;
}

.prof-av-week-focus > div {
    display: grid;
    gap: .1rem;
}

.prof-av-week-focus strong {
    color: #fff;
    font-size: .72rem;
}

.prof-av-week-focus span {
    color: #94a3b8;
    font-size: .58rem;
}

.prof-av-focus-edit {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    min-height: 34px;
    padding: .4rem .58rem;
    border: 1px solid rgba(var(--prof-rgb), .22);
    border-radius: 9px;
    color: var(--prof-color);
    background: rgba(var(--prof-rgb), .08);
    text-decoration: none;
    font-size: .58rem;
    font-weight: 800;
}

.prof-av-prof-chip {
    display: inline-flex;
    align-items: center;
    gap: .38rem;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            rgba(var(--prof-rgb), .20),
            rgba(var(--prof-rgb), .09)
        );
    border: 1px solid rgba(var(--prof-rgb), .30);
    box-shadow: inset 3px 0 0 var(--prof-color);
}

.prof-av-prof-chip:hover {
    color: #fff;
    border-color: rgba(var(--prof-rgb), .55);
    background: rgba(var(--prof-rgb), .25);
}

.prof-av-week-cell.has-professors {
    background: rgba(255,255,255,.018);
}

.prof-av-table tbody tr {
    position: relative;
    transition: .18s ease;
}

.prof-av-table tbody tr:hover {
    background: rgba(var(--prof-rgb), .035);
}

.prof-av-table tbody tr td:first-child {
    box-shadow: inset 3px 0 0 rgba(var(--prof-rgb), .78);
}

.prof-av-person-cell strong {
    display: flex;
    align-items: center;
}

.prof-av-day-summary > span {
    border: 1px solid rgba(var(--prof-rgb), .12);
    background: rgba(var(--prof-rgb), .065);
}

.prof-av-day-summary strong {
    color: var(--prof-color);
}

.prof-av-row-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .4rem;
}

.prof-av-see-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    min-height: 32px;
    padding: .36rem .52rem;
    border: 1px solid rgba(var(--prof-rgb), .18);
    border-radius: 9px;
    color: var(--prof-color);
    background: rgba(var(--prof-rgb), .07);
    text-decoration: none;
    white-space: nowrap;
    font-size: .55rem;
    font-weight: 800;
}

.prof-av-see-btn:hover {
    color: #fff;
    border-color: rgba(var(--prof-rgb), .42);
    background: rgba(var(--prof-rgb), .15);
}

.prof-av-icon-btn {
    color: var(--prof-color);
    background: rgba(var(--prof-rgb), .08);
}

@media (max-width: 900px) {
    .prof-av-week-filter {
        width: 100%;
        min-width: 0;
    }

    .prof-av-week-focus {
        grid-template-columns: auto 1fr;
    }

    .prof-av-focus-edit {
        grid-column: 1 / -1;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = Array.from(document.querySelectorAll('[data-av-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-av-panel]'));

    function activateTab(name) {
        tabs.forEach(function (tab) {
            tab.classList.toggle('active', tab.dataset.avTab === name);
        });

        panels.forEach(function (panel) {
            panel.classList.toggle('active', panel.dataset.avPanel === name);
        });

        if (window.history && window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', name);
            window.history.replaceState({}, '', url.toString());
        }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activateTab(tab.dataset.avTab);
        });
    });

    const checkboxes = Array.from(
        document.querySelectorAll('[data-day-checkbox]')
    );

    function refreshCheckboxCard(input) {
        const label = input.closest('.prof-av-slot-check');
        if (label) {
            label.classList.toggle('is-checked', input.checked);
        }
    }

    checkboxes.forEach(function (input) {
        input.addEventListener('change', function () {
            refreshCheckboxCard(input);
        });
    });

    document.querySelectorAll('.prof-av-day-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const day = button.dataset.day;
            const dayInputs = checkboxes.filter(function (input) {
                return input.dataset.dayCheckbox === day;
            });
            const shouldCheck = dayInputs.some(function (input) {
                return !input.checked;
            });

            dayInputs.forEach(function (input) {
                input.checked = shouldCheck;
                refreshCheckboxCard(input);
            });
        });
    });

    const clearButton = document.getElementById('clearAvailability');
    if (clearButton) {
        clearButton.addEventListener('click', function () {
            checkboxes.forEach(function (input) {
                input.checked = false;
                refreshCheckboxCard(input);
            });
        });
    }

    const search = document.getElementById('profAvailabilitySearch');
    const rows = Array.from(document.querySelectorAll('[data-prof-row]'));
    const noResult = document.getElementById('profAvailabilityNoResult');

    if (search) {
        search.addEventListener('input', function () {
            const query = search.value.trim().toLocaleLowerCase('fr');
            let visible = 0;

            rows.forEach(function (row) {
                const haystack = (row.dataset.search || '').toLocaleLowerCase('fr');
                const show = !query || haystack.includes(query);
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (noResult) {
                noResult.hidden = visible !== 0;
            }
        });
    }
});
</script>
@endsection
