@extends('layouts.prof')

@section('title', 'Cours')
@section('page_title', 'Cours')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau → Cours')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            {{ $subject->name }}
            → {{ $level->name }}
            → {{ $class->name }}
        </span>

        <h1 class="pp-page-title">
            Cours
            @if($selectedSlot)
                — {{ $selectedSlot->code }}
            @endif
        </h1>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{
                route(
                    'prof.subjects.classes',
                    [$subject, $level]
                )
            }}"
            class="adm-btn adm-btn-ghost"
        >
            Retour aux créneaux
        </a>
    </div>
</section>

<section class="pp-panel">
    <div class="pp-panel-body">
        @if($selectedSlot)
            <div class="pps-path-line mb-3">
                <span class="pps-path-chip">{{ $subject->name }}</span>
                <i class="bi bi-chevron-right"></i>
                <span class="pps-path-chip">{{ $level->name }}</span>
                <i class="bi bi-chevron-right"></i>
                <span class="pps-path-chip">{{ $class->name }}</span>
                <i class="bi bi-chevron-right"></i>
                <span class="pps-slot-badge">{{ $selectedSlot->code }}</span>
            </div>
        @endif

        <div class="row g-3">
            @forelse($courses as $course)
                <div class="col-xl-4 col-md-6">
                    <article class="prof-path-card h-100">
                        <div class="prof-path-body">
                            <span class="prof-path-kicker">
                                {{ $course->slot_code }}
                            </span>
                            <h2>{{ $course->title }}</h2>
                            <p class="pp-panel-subtitle">
                                {{ \Illuminate\Support\Str::limit($course->description, 110) }}
                            </p>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="pps-empty">
                        Aucun cours dans ce créneau.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
