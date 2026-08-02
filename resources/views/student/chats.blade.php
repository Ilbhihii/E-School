@extends('layouts.student')

@section('title', 'Discussions')
@section('page_title', 'Discussions')
@section('breadcrumb', 'Discussions')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/student-chat-v7.css') }}"
    >
@endpush

@section('content')
@php
    $availableSubjects = isset($subjects)
        ? $subjects->unique('id')->values()
        : collect();

    $subjectCount = $availableSubjects->count();

    $hasAdministration = $availableSubjects->contains(
        fn ($subject) =>
            mb_strtolower($subject->name) === 'administration'
    );
@endphp

<div class="student-chat-page student-chat-list-page">

    <section class="student-chat-hero">
        <div class="student-chat-hero-icon">
            <i class="bi bi-chat-square-text-fill"></i>
        </div>

        <div class="student-chat-hero-copy">
            <span class="student-chat-kicker">
                Communication
            </span>

            <h2>Mes discussions</h2>

            <p>
                Échangez avec vos professeurs dans le chat de votre
                matière ou contactez l’administration en privé.
            </p>
        </div>

        <div class="student-chat-hero-count">
            <strong>{{ $subjectCount }}</strong>

            <span>
                espace{{ $subjectCount > 1 ? 's' : '' }}
                disponible{{ $subjectCount > 1 ? 's' : '' }}
            </span>
        </div>
    </section>

    <section class="student-chat-info-bar">
        <div>
            <span class="student-chat-info-icon">
                <i class="bi bi-shield-check"></i>
            </span>

            <div>
                <strong>Discussions sécurisées</strong>

                <p>
                    Le chat Administration est privé. Le chat d’une
                    matière est réservé aux utilisateurs autorisés.
                </p>
            </div>
        </div>

        <span class="student-chat-soft-badge">
            <i class="bi bi-circle-fill"></i>
            Espace étudiant
        </span>
    </section>

    @if($availableSubjects->isNotEmpty())
        <section class="student-chat-section">
            <header class="student-chat-section-header">
                <div>
                    <span class="student-chat-kicker">
                        Conversations
                    </span>

                    <h3>Choisissez un espace</h3>
                </div>

                @if($subjectCount > 2)
                    <label class="student-chat-search">
                        <i class="bi bi-search"></i>

                        <input
                            type="search"
                            id="studentChatSearch"
                            placeholder="Rechercher..."
                            autocomplete="off"
                        >
                    </label>
                @endif
            </header>

            <div
                class="student-chat-subject-grid"
                id="studentChatSubjectGrid"
            >
                @foreach($availableSubjects as $subject)
                    @php
                        $isAdministration =
                            mb_strtolower($subject->name)
                            === 'administration';

                        $subjectSlug =
                            \Illuminate\Support\Str::lower(
                                \Illuminate\Support\Str::ascii(
                                    $subject->name
                                )
                            );

                        if ($isAdministration) {
                            $tone = 'administration';
                            $icon = 'headset';
                            $description =
                                'Conversation privée avec l’administration.';
                        } elseif (
                            str_contains($subjectSlug, 'coran')
                        ) {
                            $tone = 'emerald';
                            $icon = 'book-half';
                            $description =
                                'Posez vos questions liées aux cours de Coran.';
                        } elseif (
                            str_contains($subjectSlug, 'arabe')
                        ) {
                            $tone = 'indigo';
                            $icon = 'translate';
                            $description =
                                'Échangez autour des cours de langue arabe.';
                        } elseif (
                            str_contains($subjectSlug, 'soutien')
                        ) {
                            $tone = 'amber';
                            $icon = 'mortarboard-fill';
                            $description =
                                'Discutez avec les professeurs de soutien.';
                        } else {
                            $tone = 'violet';
                            $icon = 'journal-bookmark-fill';
                            $description =
                                'Posez vos questions aux professeurs.';
                        }
                    @endphp

                    <a
                        href="{{
                            route(
                                'student.student.chat',
                                $subject->id
                            )
                        }}"
                        class="student-chat-subject-card {{ $tone }}"
                        data-chat-subject="{{
                            \Illuminate\Support\Str::lower(
                                $subject->name
                            )
                        }}"
                    >
                        <div class="student-chat-card-top">
                            <span class="student-chat-subject-icon">
                                <i class="bi bi-{{ $icon }}"></i>
                            </span>

                            <span class="student-chat-card-arrow">
                                <i class="bi bi-arrow-up-right"></i>
                            </span>
                        </div>

                        <div class="student-chat-card-body">
                            <span class="student-chat-card-label">
                                {{
                                    $isAdministration
                                        ? 'Support'
                                        : 'Matière'
                                }}
                            </span>

                            <h4>{{ $subject->name }}</h4>

                            <p>{{ $description }}</p>
                        </div>

                        <footer class="student-chat-card-footer">
                            <span>
                                <i class="bi bi-chat-dots-fill"></i>
                                Ouvrir la discussion
                            </span>

                            @if($isAdministration)
                                <small>
                                    <i class="bi bi-lock-fill"></i>
                                    Privée
                                </small>
                            @else
                                <small>
                                    <i class="bi bi-people-fill"></i>
                                    Groupe
                                </small>
                            @endif
                        </footer>
                    </a>
                @endforeach
            </div>

            <div
                class="student-chat-empty compact"
                id="studentChatSearchEmpty"
                hidden
            >
                <span>
                    <i class="bi bi-search"></i>
                </span>

                <h3>Aucune discussion trouvée</h3>

                <p>
                    Essayez avec un autre nom de matière.
                </p>
            </div>
        </section>
    @else
        <section class="student-chat-empty">
            <span>
                <i class="bi bi-chat-square-dots"></i>
            </span>

            <h3>Aucune discussion disponible</h3>

            <p>
                Vous n’avez actuellement accès à aucun chat.
                Contactez l’administration si nécessaire.
            </p>

            <a
                href="{{ route('student.dashboard') }}"
                class="student-chat-primary-button"
            >
                <i class="bi bi-arrow-left"></i>
                Tableau de bord
            </a>
        </section>
    @endif
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/student-chat-v7.js') }}"></script>
@endpush
