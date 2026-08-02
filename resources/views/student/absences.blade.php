@extends('layouts.student')

@section('title', 'Mes absences')
@section('page_title', 'Mes absences')
@section('breadcrumb', 'Absences')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/student-pages-v6.css') }}"
    >
@endpush

@section('content')
@php
    $unjustifiedCount =
        $totalAbsences - ($justifiedCount ?? 0);

    $situationClass = match ($color ?? 'success') {
        'danger' => 'danger',
        'warning' => 'warning',
        default => 'success',
    };

    $situationIcon = match ($color ?? 'success') {
        'danger' => 'exclamation-octagon-fill',
        'warning' => 'exclamation-triangle-fill',
        default => 'check-circle-fill',
    };
@endphp

<div class="sp-page sp-absences-page">

    <section class="sp-hero sp-hero-absence">
        <div class="sp-hero-icon">
            <i class="bi bi-calendar2-x-fill"></i>
        </div>

        <div class="sp-hero-copy">
            <span class="sp-kicker">
                Suivi de présence
            </span>

            <h2>Mes absences</h2>

            <p>
                Consultez votre historique et suivez votre
                situation d’assiduité.
            </p>
        </div>

        <div class="sp-absence-situation {{ $situationClass }}">
            <i class="bi bi-{{ $situationIcon }}"></i>

            <div>
                <small>Situation actuelle</small>
                <strong>{{ $situation }}</strong>
            </div>
        </div>
    </section>

    <section class="sp-metrics sp-metrics-three">
        <article class="sp-metric-card">
            <span class="sp-metric-icon red">
                <i class="bi bi-x-circle-fill"></i>
            </span>

            <div>
                <small>Total des absences</small>
                <strong>{{ $totalAbsences }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon amber">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </span>

            <div>
                <small>Non justifiées</small>
                <strong>{{ $unjustifiedCount }}</strong>
            </div>
        </article>

        <article class="sp-metric-card">
            <span class="sp-metric-icon green">
                <i class="bi bi-check-circle-fill"></i>
            </span>

            <div>
                <small>Justifiées</small>
                <strong>{{ $justifiedCount ?? 0 }}</strong>
            </div>
        </article>
    </section>

    <section class="sp-attendance-banner {{ $situationClass }}">
        <span>
            <i class="bi bi-{{ $situationIcon }}"></i>
        </span>

        <div>
            <small>État du suivi</small>
            <strong>{{ $situation }}</strong>

            <p>
                @if(($color ?? 'success') === 'danger')
                    Votre nombre d’absences nécessite un suivi
                    avec l’administration.
                @elseif(($color ?? 'success') === 'warning')
                    Restez attentif à votre présence pendant
                    les prochaines séances.
                @else
                    Votre situation est actuellement normale.
                @endif
            </p>
        </div>
    </section>

    <section class="sp-table-card">
        <header class="sp-section-header">
            <div>
                <span class="sp-section-icon red">
                    <i class="bi bi-clock-history"></i>
                </span>

                <div>
                    <h3>Historique des absences</h3>

                    <p>
                        Liste des absences enregistrées sur votre compte.
                    </p>
                </div>
            </div>

            <span class="sp-soft-badge">
                {{ $absences->count() }}
                entrée{{ $absences->count() > 1 ? 's' : '' }}
            </span>
        </header>

        @if($absences->isNotEmpty())
            <div class="sp-responsive-table">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Jour</th>
                            <th>Statut</th>
                            <th>Justification</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($absences as $absence)
                            @php
                                $absenceDate =
                                    \Carbon\Carbon::parse(
                                        $absence->date
                                    );
                            @endphp

                            <tr>
                                <td data-label="Date">
                                    <div class="sp-table-date">
                                        <span>
                                            {{
                                                $absenceDate
                                                    ->format('d')
                                            }}
                                        </span>

                                        <div>
                                            <strong>
                                                {{
                                                    $absenceDate
                                                        ->translatedFormat(
                                                            'F Y'
                                                        )
                                                }}
                                            </strong>

                                            <small>
                                                {{
                                                    $absenceDate
                                                        ->format('d/m/Y')
                                                }}
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Jour">
                                    {{
                                        ucfirst(
                                            $absenceDate
                                                ->translatedFormat('l')
                                        )
                                    }}
                                </td>

                                <td data-label="Statut">
                                    <span class="sp-status-badge danger">
                                        <i class="bi bi-x-circle-fill"></i>
                                        Absent
                                    </span>
                                </td>

                                <td data-label="Justification">
                                    @if($absence->justified)
                                        <span class="sp-status-badge success">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Justifiée
                                        </span>
                                    @else
                                        <span class="sp-status-badge warning">
                                            <i class="bi bi-hourglass-split"></i>
                                            Non justifiée
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="sp-empty-state">
                <span class="sp-empty-icon green">
                    <i class="bi bi-calendar2-check-fill"></i>
                </span>

                <h3>Aucune absence enregistrée</h3>

                <p>
                    Votre historique de présence ne contient
                    actuellement aucune absence.
                </p>
            </div>
        @endif
    </section>
</div>
@endsection
