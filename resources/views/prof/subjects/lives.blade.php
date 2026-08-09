@extends('layouts.prof')

@section('title', 'Lives du créneau')
@section('page_title', 'Lives')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau → Lives')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            {{ $subject->name }}
            → {{ $level->name }}
            → {{ $class->name }}
        </span>

        <h1 class="pp-page-title">
            Lives
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
        @forelse($lives as $live)
            <article class="pp-live-row">
                <div class="pp-live-main">
                    <span class="pps-slot-badge">
                        {{ $live->classSlot?->code ?? '—' }}
                    </span>
                    <div class="pp-live-copy">
                        <strong class="pp-live-title">
                            {{ $live->title }}
                        </strong>
                    </div>
                </div>

                @if($live->stream_url)
                    <a
                        href="{{ $live->stream_url }}"
                        target="_blank"
                        rel="noopener"
                        class="adm-btn adm-btn-danger adm-btn-sm"
                    >
                        Ouvrir
                    </a>
                @endif
            </article>
        @empty
            <div class="pps-empty">
                Aucun live dans ce créneau.
            </div>
        @endforelse
    </div>
</section>
@endsection
