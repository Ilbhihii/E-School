@extends('layouts.prof')

@section('title', 'Devoirs du créneau')
@section('page_title', 'Devoirs')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau → Devoirs')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            {{ $subject->name }}
            → {{ $level->name }}
            → {{ $class->name }}
        </span>

        <h1 class="pp-page-title">
            Devoirs
            @if($selectedSlot)
                — {{ $selectedSlot->code }}
            @endif
        </h1>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{
                route(
                    'prof.devoir.create',
                    [
                        'subject_id' => $subject->id,
                        'level_id' => $level->id,
                        'class_id' => $class->id,
                        'class_slot_id' => $selectedSlot?->id,
                    ]
                )
            }}"
            class="adm-btn adm-btn-success"
        >
            Nouveau devoir
        </a>
    </div>
</section>

<section class="pp-panel">
    <div class="pp-panel-body">
        <div class="row g-3">
            @forelse($devoirs as $devoir)
                <div class="col-xl-6">
                    <article class="prof-path-card h-100">
                        <div class="prof-path-body">
                            <span class="pps-slot-badge">
                                {{ $devoir->classSlot?->code ?? '—' }}
                            </span>

                            <h2 class="mt-2">
                                {{ $devoir->title }}
                            </h2>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="pps-empty">
                        Aucun devoir dans ce créneau.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
