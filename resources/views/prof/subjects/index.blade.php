@extends('layouts.prof')

@section('title', 'Mes matières')
@section('page_title', 'Matières')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-diagram-3-fill"></i>
            Structure pédagogique
        </span>

        <h1 class="pp-page-title">Mes matières</h1>

        <p class="pp-page-description">
            Accédez à vos affectations selon la structure
            Matière → Niveau → Classe → Créneau.
        </p>
    </div>
</section>

<section class="pp-panel">
    <div class="pp-panel-body">
        @if($subjects->isNotEmpty())
            <div class="row g-3">
                @foreach($subjects as $subject)
                    <div class="col-xl-4 col-md-6">
                        <article class="prof-path-card h-100">
                            <div class="prof-path-body">
                                <span class="prof-path-kicker">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                    Matière
                                </span>

                                <h2>{{ $subject->name }}</h2>

                                <div class="pps-path-line mb-3">
                                    <span class="pps-path-chip">
                                        {{ $subject->assigned_levels_count }}
                                        niveau(x)
                                    </span>

                                    <span class="pps-path-chip">
                                        {{ $subject->assigned_classes_count }}
                                        classe(s)
                                    </span>

                                    <span class="pps-slot-badge">
                                        {{ $subject->assigned_slots_count }}
                                        créneau(x)
                                    </span>
                                </div>

                                <a
                                    href="{{
                                        route(
                                            'prof.subjects.levels',
                                            $subject
                                        )
                                    }}"
                                    class="adm-btn adm-btn-primary w-100"
                                >
                                    Voir les niveaux
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @else
            <div class="pps-empty">
                Aucune matière ne vous est encore assignée.
            </div>
        @endif
    </div>
</section>
@endsection
