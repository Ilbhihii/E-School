@extends('layouts.prof')

@section('title', 'Mes devoirs')
@section('page_title', 'Devoirs')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-file-earmark-check-fill"></i>
            Activités pédagogiques
        </span>

        <h1 class="pp-page-title">Mes devoirs</h1>

        <p class="pp-page-description">
            Chaque devoir est maintenant rattaché
            à un créneau pédagogique précis.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{ route('prof.devoir.create') }}"
            class="adm-btn adm-btn-success"
        >
            <i class="bi bi-plus-lg"></i>
            Nouveau devoir
        </a>
    </div>
</section>

@include(
    'prof.partials.path-filter',
    [
        'action' => route('prof.devoir.index'),
        'buttonLabel' => 'Afficher les devoirs',
        'extraQuery' => [
            'course_id' => $courseId ?? null,
        ],
    ]
)

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-list-check"></i>
                Devoirs publiés
            </h2>
        </div>

        <span class="pp-panel-meta">
            {{ $devoirs->total() }} devoir(s)
        </span>
    </header>

    <div class="pp-panel-body">
        <div class="row g-3">
            @forelse($devoirs as $devoir)
                <div class="col-xl-6">
                    <article class="prof-path-card h-100">
                        <div class="prof-path-body">
                            <span class="prof-path-kicker">
                                <i class="bi bi-file-earmark-text"></i>
                                Devoir
                            </span>

                            <h2>{{ $devoir->title }}</h2>

                            <div class="pps-path-line my-2">
                                <span class="pps-path-chip">
                                    {{
                                        $devoir
                                            ->classSlot
                                            ?->subject
                                            ?->name
                                        ?? $devoir
                                            ->subject
                                            ?->name
                                        ?? 'Matière'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-path-chip">
                                    {{
                                        $devoir
                                            ->classSlot
                                            ?->level
                                            ?->name
                                        ?? 'Niveau'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-path-chip">
                                    {{
                                        $devoir
                                            ->classSlot
                                            ?->classRoom
                                            ?->name
                                        ?? 'Classe'
                                    }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span class="pps-slot-badge">
                                    {{
                                        $devoir
                                            ->classSlot
                                            ?->code
                                        ?? '—'
                                    }}
                                </span>
                            </div>

                            <p class="pp-panel-subtitle">
                                Date limite :
                                {{
                                    \Carbon\Carbon::parse(
                                        $devoir->due_date
                                    )->format('d/m/Y')
                                }}
                            </p>

                            <div class="pps-inline-actions mt-3">
                                <a
                                    href="{{
                                        route(
                                            'prof.devoir.edit',
                                            $devoir
                                        )
                                    }}"
                                    class="adm-btn adm-btn-ghost adm-btn-sm"
                                >
                                    Modifier
                                </a>

                                <form
                                    method="POST"
                                    action="{{
                                        route(
                                            'prof.devoir.destroy',
                                            $devoir
                                        )
                                    }}"
                                    onsubmit="return confirm('Supprimer ce devoir ?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="adm-btn adm-btn-danger adm-btn-sm"
                                    >
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="pps-empty">
                        Aucun devoir pour le parcours sélectionné.
                    </div>
                </div>
            @endforelse
        </div>

        @if($devoirs->hasPages())
            <div class="pp-pagination mt-3">
                {{ $devoirs->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
