@extends('layouts.front')

@section('title', $subject->name . ' - ' . $level->name)

@section('content')

<section class="py-5">
    <div class="container">

        <div class="d-flex align-items-center gap-2 mb-4 flex-wrap" style="font-size: 0.85rem;">
            <a
                href="{{ route('front.classes') }}"
                style="color: rgba(255,255,255,0.4); text-decoration: none;"
            >
                <i class="bi bi-grid-3x3-gap me-1"></i>
                Matières
            </a>

            <i class="bi bi-chevron-right" style="color: rgba(255,255,255,0.15); font-size: 0.7rem;"></i>

            <a
                href="{{ route('front.subject.levels', $subject->id) }}"
                style="color: rgba(255,255,255,0.4); text-decoration: none;"
            >
                {{ $subject->name }}
            </a>

            <i class="bi bi-chevron-right" style="color: rgba(255,255,255,0.15); font-size: 0.7rem;"></i>

            <span style="color: rgba(255,255,255,0.6);">
                {{ $level->name }}
            </span>
        </div>

        <div class="text-center mb-5">
            <span
                class="badge px-3 py-2 mb-3"
                style="background: rgba(124,58,237,0.15); color: #A78BFA; border-radius: 20px;"
            >
                {{ $level->name }}
            </span>

            <h2 class="section-title-3d">
                {{ $subject->name }}
            </h2>

            <p class="text-white-50" style="max-width: 680px; margin: 0 auto;">
                Choisissez votre niveau. Pour les parcours d’Arabe en classe
                Débutant, vous continuez directement sans test vocal.
            </p>
        </div>

        <div class="row g-4">
            @forelse($classes as $class)
                @php
                    $requiresVocalTest = (bool) ($class->requires_vocal_test ?? false);
                    $withoutVocalTest = (bool) ($class->is_without_vocal_test ?? false);

                    $targetRoute = $requiresVocalTest
                        ? route('vocal-test.create', [$subject, $level, $class])
                        : route(
                            'front.courses',
                            [$subject->id, $level->id, $class->id]
                        );
                @endphp

                <div class="col-md-6 col-lg-4">
                    <a href="{{ $targetRoute }}" class="text-decoration-none">
                        <div
                            class="card-3d text-center h-100 reveal-3d"
                            style="cursor: pointer;"
                        >
                            <div
                                class="card-3d-icon mx-auto"
                                style="background: linear-gradient(135deg,
                                    @switch($loop->index % 3)
                                        @case(0) #16A34A, #15803D @break
                                        @case(1) #2563EB, #1D4ED8 @break
                                        @default #6D28D9, #581C87
                                    @endswitch
                                );"
                            >
                                @if($requiresVocalTest)
                                    <i class="bi bi-mic-fill" style="font-size: 1.5rem; color: white;"></i>
                                @elseif($withoutVocalTest)
                                    <i class="bi bi-check-circle-fill" style="font-size: 1.5rem; color: white;"></i>
                                @else
                                    <i class="bi bi-arrow-right-circle-fill" style="font-size: 1.5rem; color: white;"></i>
                                @endif
                            </div>

                            <h5 class="fw-bold text-white mt-3 mb-2">
                                {{ $class->name }}
                            </h5>

                            @if($requiresVocalTest)
                                <span
                                    class="badge mb-2"
                                    style="background: rgba(124,58,237,0.18); color: #C4B5FD;"
                                >
                                    Test vocal
                                </span>

                                <p class="text-white-50 small mb-0">
                                    Passer le test vocal
                                    <i class="bi bi-arrow-right ms-1" style="color: var(--3d-gold);"></i>
                                </p>
                            @elseif($withoutVocalTest)
                                <span
                                    class="badge mb-2"
                                    style="background: rgba(34,197,94,0.16); color: #86EFAC;"
                                >
                                    Sans test vocal
                                </span>

                                <p class="text-white-50 small mb-0">
                                    Continuer directement
                                    <i class="bi bi-arrow-right ms-1" style="color: var(--3d-gold);"></i>
                                </p>
                            @else
                                <p class="text-white-50 small mb-0">
                                    Continuer
                                    <i class="bi bi-arrow-right ms-1" style="color: var(--3d-gold);"></i>
                                </p>
                            @endif
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div
                        class="alert"
                        style="background: rgba(239,68,68,0.15); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; display: inline-block;"
                    >
                        Aucun niveau disponible pour ce parcours.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

@endsection
