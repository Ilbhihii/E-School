@extends('layouts.prof')

@section('title', 'Mes niveaux')
@section('page_title', $subject->name)
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">Matière</span>
        <h1 class="pp-page-title">{{ $subject->name }}</h1>
        <p class="pp-page-description">
            Sélectionnez un niveau pour voir les classes
            et les créneaux qui vous sont affectés.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{ route('prof.subjects.list') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-arrow-left"></i>
            Matières
        </a>
    </div>
</section>

<section class="pp-panel">
    <div class="pp-panel-body">
        @if($levels->isNotEmpty())
            <div class="row g-3">
                @foreach($levels as $level)
                    <div class="col-xl-4 col-md-6">
                        <article class="prof-path-card h-100">
                            <div class="prof-path-body">
                                <span class="prof-path-kicker">
                                    <i class="bi bi-layers-fill"></i>
                                    Niveau
                                </span>

                                <h2>{{ $level->name }}</h2>

                                <div class="pps-path-line mb-3">
                                    <span class="pps-path-chip">
                                        {{ $level->assigned_classes_count }}
                                        classe(s)
                                    </span>

                                    <span class="pps-slot-badge">
                                        {{ $level->assigned_slots_count }}
                                        créneau(x)
                                    </span>
                                </div>

                                <a
                                    href="{{
                                        route(
                                            'prof.subjects.classes',
                                            [$subject, $level]
                                        )
                                    }}"
                                    class="adm-btn adm-btn-primary w-100"
                                >
                                    Voir les classes
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @else
            <div class="pps-empty">
                Aucun niveau assigné.
            </div>
        @endif
    </div>
</section>
@endsection
