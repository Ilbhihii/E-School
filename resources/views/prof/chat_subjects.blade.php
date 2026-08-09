@extends('layouts.prof')

@section('title', 'Discussions')
@section('page_title', 'Discussions')
@section('breadcrumb', 'Communication → Discussions')

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/role-group-chat-v1.css') }}?v={{
        file_exists(public_path('css/role-group-chat-v1.css'))
            ? filemtime(public_path('css/role-group-chat-v1.css'))
            : 1
    }}"
>
@endpush

@section('content')
@php
    $subjects =
        ($subjects ?? collect())
            ->unique('id')
            ->values();
@endphp

<div class="rgc-page">
    <section class="rgc-hero rgc-list-hero">
        <div class="rgc-hero-main">
            <span class="rgc-hero-icon">
                <i class="bi bi-chat-square-text-fill"></i>
            </span>

            <div>
                <span class="rgc-kicker">
                    Espace enseignant
                </span>

                <h1>Discussions pédagogiques</h1>

                <p>
                    Répondez aux étudiants dans Arabe ou Coran
                    et échangez avec l’administration.
                </p>
            </div>
        </div>

        <span class="rgc-status">
            <i class="bi bi-circle-fill"></i>
            {{ $subjects->count() }} espace(s)
        </span>
    </section>

    @if($subjects->count() > 2)
        <label class="rgc-search">
            <i class="bi bi-search"></i>

            <input
                type="search"
                placeholder="Rechercher une discussion..."
                data-rgc-search="profDiscussionGrid"
            >
        </label>
    @endif

    <div
        class="rgc-list-grid"
        id="profDiscussionGrid"
    >
        @forelse($subjects as $subject)
            @php
                $name =
                    mb_strtolower(
                        trim($subject->name)
                    );

                $isAdmin =
                    $name === 'administration';

                $isQuran =
                    $name === 'coran';

                $icon =
                    $isAdmin
                        ? 'bi-shield-lock-fill'
                        : (
                            $isQuran
                                ? 'bi-book-half'
                                : 'bi-translate'
                        );

                $class =
                    $isAdmin
                        ? 'is-admin'
                        : (
                            $isQuran
                                ? 'is-quran'
                                : ''
                        );

                $description =
                    $isAdmin
                        ? 'Conversation privée avec l’administration.'
                        : (
                            $isQuran
                                ? 'Questions sur le Coran, Tajwid et mémorisation.'
                                : 'Questions et exercices liés aux cours d’Arabe.'
                        );
            @endphp

            <a
                href="{{
                    route(
                        'prof.chat',
                        $subject->id
                    )
                }}"
                class="rgc-list-card {{ $class }}"
                data-rgc-item="{{ $name }}"
            >
                <div class="rgc-list-card-top">
                    <span class="rgc-list-icon">
                        <i class="bi {{ $icon }}"></i>
                    </span>

                    <i
                        class="
                            bi bi-arrow-up-right
                            rgc-list-arrow
                        "
                    ></i>
                </div>

                <h3>
                    {{
                        $isAdmin
                            ? 'Administration'
                            : (
                                $isQuran
                                    ? 'Groupe Coran'
                                    : 'Groupe Arabe'
                            )
                    }}
                </h3>

                <p>{{ $description }}</p>

                <footer>
                    <span>
                        <i class="bi bi-chat-dots-fill"></i>
                        Ouvrir la discussion
                    </span>

                    <span>
                        <i
                            class="
                                bi
                                {{
                                    $isAdmin
                                        ? 'bi-lock-fill'
                                        : 'bi-people-fill'
                                }}
                            "
                        ></i>
                        {{
                            $isAdmin
                                ? 'Privée'
                                : 'Groupe'
                        }}
                    </span>
                </footer>
            </a>
        @empty
            <div class="rgc-list-empty">
                Aucune discussion disponible.
            </div>
        @endforelse
    </div>
</div>

<script
    src="{{ asset('js/role-group-chat-v1.js') }}?v=1"
></script>
@endsection
