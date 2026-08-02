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
            Consultez les présences enregistrées, filtrez-les par classe et corrigez rapidement un statut.
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
            <p class="pp-panel-subtitle">Affichez toutes les classes ou concentrez-vous sur une classe précise.</p>
        </div>
        <span class="pp-panel-meta"><i class="bi bi-sliders"></i> Filtre</span>
    </header>

    <div class="pp-panel-body">
        <form method="GET" action="{{ route('prof.absences.list') }}" class="pp-history-filter">
            <div class="pp-history-filter-field">
                <label for="historyClass" class="pp-label"><i class="bi bi-people-fill"></i> Classe</label>
                <select id="historyClass" name="class_id" class="adm-form-select">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ (string) request('class_id') === (string) $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pp-history-filter-actions">
                <button type="submit" class="adm-btn adm-btn-primary">
                    <i class="bi bi-funnel"></i> Appliquer
                </button>

                @if(request()->filled('class_id'))
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
                                <i class="bi bi-mortarboard-fill"></i>
                                {{ $absence->user?->classRoom?->name ?? 'Classe non assignée' }}
                            </small>
                        </span>
                    </div>

                    <div class="pp-history-date">
                        <span class="pp-history-label">Date</span>
                        <strong>{{ $absence->created_at->format('d/m/Y') }}</strong>
                        <small>{{ $absence->created_at->format('H:i') }}</small>
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
                <p>Aucun enregistrement ne correspond au filtre sélectionné.</p>
                @if(request()->filled('class_id'))
                    <a href="{{ route('prof.absences.list') }}" class="adm-btn adm-btn-ghost mt-3">
                        <i class="bi bi-arrow-counterclockwise"></i> Afficher toutes les classes
                    </a>
                @endif
            </div>
        </div>
    @endif
</section>
@endsection
