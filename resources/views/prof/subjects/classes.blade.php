@extends('layouts.prof')

@section('title', 'Mes classes et créneaux')
@section('page_title', $level->name)
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            {{ $subject->name }} → {{ $level->name }}
        </span>

        <h1 class="pp-page-title">
            Classes & créneaux
        </h1>

        <p class="pp-page-description">
            Chaque classe affiche uniquement les créneaux
            qui vous ont été affectés par l’administration.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{
                route(
                    'prof.subjects.levels',
                    $subject
                )
            }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-arrow-left"></i>
            Niveaux
        </a>
    </div>
</section>

<div class="row g-3">
    @forelse($classes as $class)
        <div class="col-12">
            <section class="pp-panel">
                <header class="pp-panel-head">
                    <div class="pp-panel-title-wrap">
                        <h2 class="pp-panel-title">
                            <i class="bi bi-people-fill"></i>
                            {{ $class->name }}
                        </h2>

                        <p class="pp-panel-subtitle">
                            {{ $subject->name }}
                            → {{ $level->name }}
                            → {{ $class->name }}
                            → Créneau
                        </p>
                    </div>

                    <span class="pp-panel-meta">
                        {{
                            $class->assignedSlots
                                ->count()
                        }}
                        créneau(x)
                    </span>
                </header>

                <div class="pp-panel-body">
                    @if(
                        $class->assignedSlots
                            ->isNotEmpty()
                    )
                        <div class="pps-slot-grid">
                            @foreach(
                                $class->assignedSlots
                                as $slot
                            )
                                <article class="pps-slot-card">
                                    <div class="pps-slot-card-copy">
                                        <span class="pps-slot-badge">
                                            {{ $slot->code }}
                                        </span>

                                        <small>
                                            {{
                                                $subject->name
                                            }}
                                            →
                                            {{
                                                $level->name
                                            }}
                                            →
                                            {{
                                                $class->name
                                            }}
                                        </small>
                                    </div>

                                    <div class="pps-inline-actions">
                                        <a
                                            href="{{
                                                route(
                                                    'prof.subjects.courses',
                                                    [
                                                        $subject,
                                                        $level,
                                                        $class,
                                                        'class_slot_id'
                                                            => $slot->id,
                                                    ]
                                                )
                                            }}"
                                            class="adm-btn adm-btn-primary adm-btn-sm"
                                        >
                                            Cours
                                        </a>

                                        <a
                                            href="{{
                                                route(
                                                    'prof.subjects.lives',
                                                    [
                                                        $subject,
                                                        $level,
                                                        $class,
                                                        'class_slot_id'
                                                            => $slot->id,
                                                    ]
                                                )
                                            }}"
                                            class="adm-btn adm-btn-danger adm-btn-sm"
                                        >
                                            Lives
                                        </a>

                                        <a
                                            href="{{
                                                route(
                                                    'prof.subjects.devoirs',
                                                    [
                                                        $subject,
                                                        $level,
                                                        $class,
                                                        'class_slot_id'
                                                            => $slot->id,
                                                    ]
                                                )
                                            }}"
                                            class="adm-btn adm-btn-success adm-btn-sm"
                                        >
                                            Devoirs
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="pps-empty">
                            Aucun créneau ne vous est assigné
                            dans cette classe.
                        </div>
                    @endif
                </div>
            </section>
        </div>
    @empty
        <div class="col-12">
            <div class="pps-empty">
                Aucune classe assignée.
            </div>
        </div>
    @endforelse
</div>
@endsection
