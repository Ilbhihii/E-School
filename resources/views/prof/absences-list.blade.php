@extends('layouts.prof')

@section('title', 'Historique des absences')
@section('page_title', 'Historique des absences')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-clock-history"></i>
            Suivi des présences
        </span>

        <h1 class="pp-page-title">
            Historique des absences
        </h1>

        <p class="pp-page-description">
            Filtrez l’historique selon la même structure
            Matière → Niveau → Classe → Créneau.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{ route('prof.absences') }}"
            class="adm-btn adm-btn-primary"
        >
            <i class="bi bi-person-check-fill"></i>
            Faire l’appel
        </a>
    </div>
</section>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@include(
    'prof.partials.path-filter',
    [
        'action' => route('prof.absences.list'),
        'buttonLabel' => 'Filtrer l’historique',
    ]
)

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-list-check"></i>
                Présences enregistrées
            </h2>
        </div>

        <span class="pp-panel-meta">
            {{ $absences->total() }} résultat(s)
        </span>
    </header>

    <div class="pp-panel-body">
        @forelse($absences as $absence)
            <article class="pp-history-row mb-2">
                <div class="pp-history-student">
                    <span class="pp-history-avatar">
                        {{
                            mb_strtoupper(
                                mb_substr(
                                    $absence->user?->name
                                        ?? 'E',
                                    0,
                                    1
                                )
                            )
                        }}
                    </span>

                    <span class="pp-history-student-copy">
                        <strong>
                            {{
                                $absence->user?->name
                                ?? 'Étudiant'
                            }}
                        </strong>

                        <div class="pps-path-line mt-1">
                            <span class="pps-path-chip">
                                {{
                                    $absence->subject?->name
                                    ?? 'Matière'
                                }}
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span class="pps-path-chip">
                                {{
                                    $absence->level?->name
                                    ?? 'Niveau'
                                }}
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span class="pps-path-chip">
                                {{
                                    $absence
                                        ->classRoom
                                        ?->name
                                    ?? 'Classe'
                                }}
                            </span>

                            <i class="bi bi-chevron-right"></i>

                            <span class="pps-slot-badge">
                                {{
                                    $absence
                                        ->classSlot
                                        ?->code
                                    ?? '—'
                                }}
                            </span>
                        </div>
                    </span>
                </div>

                <div class="pp-history-date">
                    <span class="pp-history-label">
                        Date
                    </span>

                    <strong>
                        {{
                            optional(
                                $absence->date
                            )->format('d/m/Y')
                            ?? '-'
                        }}
                    </strong>
                </div>

                <div class="pp-history-status">
                    @if($absence->present)
                        <span class="pp-history-badge is-present">
                            Présent
                        </span>
                    @else
                        <span class="pp-history-badge is-absent">
                            Absent
                        </span>
                    @endif
                </div>

                <form
                    method="POST"
                    action="{{
                        route(
                            'prof.absences.update',
                            $absence->id
                        )
                    }}"
                    class="pp-history-action"
                >
                    @csrf
                    @method('PUT')

                    <select
                        name="present"
                        class="adm-form-select"
                        onchange="this.form.submit()"
                    >
                        <option
                            value="1"
                            {{
                                $absence->present
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Présent
                        </option>

                        <option
                            value="0"
                            {{
                                !$absence->present
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Absent
                        </option>
                    </select>
                </form>
            </article>
        @empty
            <div class="pps-empty">
                Aucun enregistrement pour ce parcours.
            </div>
        @endforelse

        @if($absences->hasPages())
            <div class="pp-pagination mt-3">
                {{ $absences->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
