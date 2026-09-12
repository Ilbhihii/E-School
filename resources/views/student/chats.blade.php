@extends('layouts.student')

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
                    Espace étudiant
                </span>

                <h1>Mes discussions</h1>

                <p>
                    Arabe, Coran et conversation privée
                    avec l’administration.
                </p>
            </div>
        </div>

        <span class="rgc-status">
            <i class="bi bi-circle-fill"></i>
            {{ $subjects->count() + 1 }} espace(s)
        </span>
    </section>

    @if($subjects->count() + 1 > 2)
        <label class="rgc-search">
            <i class="bi bi-search"></i>

            <input
                type="search"
                placeholder="Rechercher une discussion..."
                data-rgc-search="studentDiscussionGrid"
            >
        </label>
    @endif

    <div
        class="rgc-list-grid"
        id="studentDiscussionGrid"
    >
        <a
            href="{{ route('student.private-chats.index') }}"
            class="rgc-list-card is-private"
            data-rgc-item="messages privés professeur conversation individuelle"
            style="
                border-color: rgba(56,189,248,.16);
                background:
                    radial-gradient(circle at 100% 0%, rgba(56,189,248,.10), transparent 38%),
                    rgba(10,20,42,.92);
            "
        >
            <div class="rgc-list-card-top">
                <span
                    class="rgc-list-icon"
                    style="
                        background:rgba(56,189,248,.14);
                        color:#7dd3fc;
                    "
                >
                    <i class="bi bi-person-video3"></i>
                </span>

                <i
                    class="
                        bi bi-arrow-up-right
                        rgc-list-arrow
                    "
                ></i>
            </div>

            <h3>Messages privés</h3>

            <p>
                Échange individuel et confidentiel
                avec votre professeur.
            </p>

            <footer>
                <span>
                    <i class="bi bi-person-lines-fill"></i>
                    Choisir un professeur
                </span>

                <span>
                    <i class="bi bi-lock-fill"></i>
                    Privé
                </span>
            </footer>
        </a>

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
                        ? 'bi-headset'
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
                                ? 'Coran, Tajwid et mémorisation.'
                                : 'Cours et activités de langue arabe.'
                        );
            @endphp

            <a
                href="{{
                    route(
                        'student.student.chat',
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
