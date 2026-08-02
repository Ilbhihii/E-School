@extends('layouts.student')

@section('title', 'Cours — ' . $subject->name)
@section('page_title', 'Cours de ' . $subject->name)
@section('breadcrumb', 'Matières / Cours')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/student-subjects-v5.css') }}"
    >
@endpush

@section('content')
@php
    $courseCount = $courses->count();

    $homeworkCount = $courses->sum(
        fn ($course) => (int) ($course->devoirs_count ?? 0)
    );

    $mediaCount = $courses->filter(
        function ($course) {
            return !empty($course->video)
                || !empty($course->video_url)
                || !empty($course->pdf)
                || !empty($course->course_link);
        }
    )->count();

    $subjectSlug = \Illuminate\Support\Str::lower(
        \Illuminate\Support\Str::ascii($subject->name)
    );

    if (str_contains($subjectSlug, 'coran')) {
        $heroTone = 'emerald';
        $heroIcon = 'book-half';
    } elseif (str_contains($subjectSlug, 'arabe')) {
        $heroTone = 'indigo';
        $heroIcon = 'translate';
    } elseif (str_contains($subjectSlug, 'soutien')) {
        $heroTone = 'amber';
        $heroIcon = 'mortarboard-fill';
    } else {
        $heroTone = 'violet';
        $heroIcon = 'journal-bookmark-fill';
    }
@endphp

<div class="student-learning-page">

    <a
        href="{{ route('student.subjects.index') }}"
        class="learning-back-link"
    >
        <i class="bi bi-arrow-left"></i>
        Retour aux matières
    </a>

    <section class="learning-course-hero {{ $heroTone }}">
        <div class="learning-course-hero-icon">
            <i class="bi bi-{{ $heroIcon }}"></i>
        </div>

        <div class="learning-course-hero-copy">
            <span class="learning-section-kicker">
                Parcours pédagogique
            </span>

            <h2>{{ $subject->name }}</h2>

            <div class="learning-course-path">
                <span>
                    <i class="bi bi-mortarboard-fill"></i>
                    {{ $level->name }}
                </span>

                <i class="bi bi-chevron-right"></i>

                <span>
                    <i class="bi bi-building-fill"></i>
                    {{ $class->name }}
                </span>
            </div>
        </div>

        <div class="learning-course-hero-actions">
            <span class="learning-course-total">
                <strong>{{ $courseCount }}</strong>
                cours disponible{{ $courseCount > 1 ? 's' : '' }}
            </span>
        </div>
    </section>

    <section class="learning-course-metrics">
        <article>
            <span class="blue">
                <i class="bi bi-play-circle-fill"></i>
            </span>

            <div>
                <small>Cours</small>
                <strong>{{ $courseCount }}</strong>
            </div>
        </article>

        <article>
            <span class="green">
                <i class="bi bi-folder-fill"></i>
            </span>

            <div>
                <small>Ressources</small>
                <strong>{{ $mediaCount }}</strong>
            </div>
        </article>

        <article>
            <span class="amber">
                <i class="bi bi-file-earmark-check-fill"></i>
            </span>

            <div>
                <small>Devoirs associés</small>
                <strong>{{ $homeworkCount }}</strong>
            </div>
        </article>
    </section>

    @if($courses->isNotEmpty())
        <section class="learning-course-toolbar">
            <div>
                <span class="learning-section-kicker">
                    Bibliothèque de cours
                </span>

                <h3>Leçons disponibles</h3>
            </div>

            @if($courseCount > 3)
                <label class="learning-course-search">
                    <i class="bi bi-search"></i>

                    <input
                        type="search"
                        id="courseSearchInput"
                        placeholder="Rechercher un cours..."
                        autocomplete="off"
                    >
                </label>
            @endif
        </section>

        <div class="learning-course-grid" id="courseGrid">
            @foreach($courses as $course)
                @php
                    $hasVideo = !empty($course->video)
                        || !empty($course->video_url)
                        || !empty($course->course_link);

                    $hasPdf = !empty($course->pdf);
                    $devoirCount = (int) ($course->devoirs_count ?? 0);

                    $courseDescription = trim(
                        strip_tags($course->description ?? '')
                    );
                @endphp

                <article
                    class="learning-course-card"
                    data-course-title="{{
                        \Illuminate\Support\Str::lower(
                            $course->title
                            . ' '
                            . $courseDescription
                        )
                    }}"
                >
                    <a
                        href="{{ route(
                            'student.course.show',
                            $course->id
                        ) }}"
                        class="learning-course-card-link"
                    >
                        <div class="learning-course-cover {{ $heroTone }}">
                            <span class="learning-course-number">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <span class="learning-course-cover-icon">
                                @if($hasVideo)
                                    <i class="bi bi-play-fill"></i>
                                @elseif($hasPdf)
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                @else
                                    <i class="bi bi-journal-text"></i>
                                @endif
                            </span>

                            <span class="learning-course-open">
                                <i class="bi bi-arrow-up-right"></i>
                            </span>
                        </div>

                        <div class="learning-course-card-body">
                            <div class="learning-course-card-meta">
                                <span>
                                    <i class="bi bi-journal-bookmark"></i>
                                    Leçon {{ $loop->iteration }}
                                </span>

                                @if((bool) ($course->is_free ?? false))
                                    <span class="free">
                                        Accès libre
                                    </span>
                                @endif
                            </div>

                            <h4>{{ $course->title }}</h4>

                            <p>
                                {{
                                    $courseDescription !== ''
                                        ? \Illuminate\Support\Str::limit(
                                            $courseDescription,
                                            110
                                        )
                                        : 'Consultez les ressources et le contenu de cette leçon.'
                                }}
                            </p>

                            <div class="learning-course-resources">
                                @if($hasVideo)
                                    <span>
                                        <i class="bi bi-play-circle"></i>
                                        Vidéo
                                    </span>
                                @endif

                                @if($hasPdf)
                                    <span>
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        PDF
                                    </span>
                                @endif

                                <span>
                                    <i class="bi bi-file-earmark-check"></i>
                                    {{ $devoirCount }}
                                    devoir{{ $devoirCount > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>

                        <div class="learning-course-card-footer">
                            <span>Ouvrir le cours</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        <div
            class="learning-empty-state compact"
            id="courseSearchEmpty"
            hidden
        >
            <span class="learning-empty-icon">
                <i class="bi bi-search"></i>
            </span>

            <h3>Aucun cours trouvé</h3>

            <p>
                Essayez un autre mot dans la recherche.
            </p>
        </div>
    @else
        <section class="learning-empty-state">
            <span class="learning-empty-icon">
                <i class="bi bi-journal-x"></i>
            </span>

            <h3>Aucun cours disponible</h3>

            <p>
                Aucun cours n’est encore publié pour cette matière,
                ce niveau et cette classe.
            </p>

            <a
                href="{{ route('student.subjects.index') }}"
                class="learning-primary-button"
            >
                <i class="bi bi-arrow-left"></i>
                Retour aux matières
            </a>
        </section>
    @endif
</div>
@endsection

@if($courses->count() > 3)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById(
                    'courseSearchInput'
                );

                const cards = Array.from(
                    document.querySelectorAll(
                        '.learning-course-card'
                    )
                );

                const emptyState = document.getElementById(
                    'courseSearchEmpty'
                );

                if (!searchInput || cards.length === 0) {
                    return;
                }

                searchInput.addEventListener('input', function () {
                    const query = searchInput.value
                        .trim()
                        .toLocaleLowerCase('fr');

                    let visibleCount = 0;

                    cards.forEach(function (card) {
                        const haystack =
                            card.dataset.courseTitle || '';

                        const visible =
                            query === ''
                            || haystack.includes(query);

                        card.hidden = !visible;

                        if (visible) {
                            visibleCount += 1;
                        }
                    });

                    if (emptyState) {
                        emptyState.hidden = visibleCount !== 0;
                    }
                });
            });
        </script>
    @endpush
@endif
