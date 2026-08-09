@extends('layouts.student')

@section('title', 'Chat — ' . ($subject->name ?? 'Discussion'))
@section('page_title', 'Chat')
@section('breadcrumb', 'Discussions → ' . ($subject->name ?? 'Chat'))

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
    $isAdministration =
        (bool) ($isAdministration ?? false);

    $name =
        mb_strtolower(
            trim($subject->name ?? '')
        );

    $isQuran = $name === 'coran';

    $themeClass =
        $isAdministration
            ? 'is-admin'
            : ($isQuran ? 'is-quran' : '');

    $icon =
        $isAdministration
            ? 'bi-headset'
            : (
                $isQuran
                    ? 'bi-book-half'
                    : 'bi-translate'
            );

    $title =
        $isAdministration
            ? 'Administration'
            : (
                $isQuran
                    ? 'Groupe Coran'
                    : 'Groupe Arabe'
            );

    $description =
        $isAdministration
            ? 'Conversation privée avec l’administration.'
            : (
                $isQuran
                    ? 'Coran, Tajwid, mémorisation et devoirs.'
                    : 'Cours, exercices et activités de langue arabe.'
            );

    $messages =
        $messages
            ->sortBy('created_at')
            ->values();

    $context =
        $groupChatContext ?? [
            'participants_count' => 0,
            'active_accounts_count' => 0,
            'professors_count' => 0,
            'professors' => collect(),
            'last_activity' => null,
        ];

    $dateGroups =
        $messages->groupBy(
            fn ($message) =>
                $message->created_at
                    ->format('Y-m-d')
        );
@endphp

<div class="rgc-page {{ $themeClass }}">
    @if(session('success'))
        <div class="rgc-alert success">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rgc-alert danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    <section class="rgc-hero">
        <div>
            <a
                href="{{ route('student.chats') }}"
                class="rgc-back"
            >
                <i class="bi bi-arrow-left"></i>
                Discussions
            </a>

            <div class="rgc-hero-main">
                <span class="rgc-hero-icon">
                    <i class="bi {{ $icon }}"></i>
                </span>

                <div>
                    <span class="rgc-kicker">
                        {{
                            $isAdministration
                                ? 'Conversation privée'
                                : 'Espace étudiant'
                        }}
                    </span>

                    <h1>{{ $title }}</h1>
                    <p>{{ $description }}</p>
                </div>
            </div>
        </div>

        <span class="rgc-status">
            <i class="bi bi-circle-fill"></i>
            Actif
        </span>
    </section>

    <div class="rgc-layout">
        <aside class="rgc-side">
            <section class="rgc-side-card">
                <div class="rgc-side-head">
                    <span class="rgc-side-icon">
                        <i class="bi {{ $icon }}"></i>
                    </span>

                    <div>
                        <h2>{{ $title }}</h2>

                        <p>
                            @if($isAdministration)
                                Vos échanges avec l’administration
                                restent privés.
                            @else
                                {{ $description }}
                            @endif
                        </p>
                    </div>
                </div>

                @unless($isAdministration)
                    <div class="rgc-side-meta">
                        <span>
                            <i class="bi bi-people"></i>
                            {{
                                $context[
                                    'participants_count'
                                ]
                            }}
                            participant(s)
                        </span>

                        <span>
                            <i class="bi bi-person-check-fill"></i>
                            {{
                                $context[
                                    'active_accounts_count'
                                ]
                            }}
                            compte(s) actif(s)
                        </span>
                    </div>
                @endunless
            </section>

            <div class="rgc-stats">
                <article class="rgc-stat">
                    <i class="bi bi-chat-left-text-fill"></i>

                    <div>
                        <small>Messages</small>
                        <strong>{{ $messages->count() }}</strong>
                    </div>
                </article>

                <article class="rgc-stat">
                    <i class="bi bi-clock-history"></i>

                    <div>
                        <small>Activité</small>

                        <strong>
                            @if(
                                !$isAdministration
                                && $context['last_activity']
                            )
                                {{
                                    $context['last_activity']
                                        ->isToday()
                                            ? 'Aujourd’hui'
                                            : $context[
                                                'last_activity'
                                            ]
                                                ->locale('fr')
                                                ->isoFormat('D MMM')
                                }}
                            @elseif($messages->last())
                                {{
                                    $messages
                                        ->last()
                                        ->created_at
                                        ->isToday()
                                            ? 'Aujourd’hui'
                                            : $messages
                                                ->last()
                                                ->created_at
                                                ->locale('fr')
                                                ->isoFormat('D MMM')
                                }}
                            @else
                                —
                            @endif
                        </strong>
                    </div>
                </article>
            </div>

            <section class="rgc-note">
                <strong>
                    <i class="bi bi-pin-angle-fill"></i>
                    {{
                        $isAdministration
                            ? 'Confidentialité'
                            : 'Rappel'
                    }}
                </strong>

                <p>
                    @if($isAdministration)
                        Cette discussion est visible uniquement
                        par vous et l’administration.
                    @elseif($isQuran)
                        Utilisez le chat pour les questions de Coran,
                        Tajwid et mémorisation.
                    @else
                        Utilisez le chat pour vos questions,
                        exercices et révisions d’Arabe.
                    @endif
                </p>
            </section>

            @unless($isAdministration)
                <div class="rgc-section-title">
                    <span>Équipe pédagogique</span>

                    <small>
                        {{
                            $context[
                                'professors_count'
                            ]
                        }}
                    </small>
                </div>

                <div class="rgc-person-list">
                    @forelse(
                        $context['professors']->take(5)
                        as $professor
                    )
                        @php
                            $initials =
                                collect(
                                    preg_split(
                                        '/\s+/u',
                                        trim($professor->name)
                                    )
                                )
                                ->filter()
                                ->take(2)
                                ->map(
                                    fn ($part) =>
                                        mb_strtoupper(
                                            mb_substr(
                                                $part,
                                                0,
                                                1
                                            )
                                        )
                                )
                                ->implode('');
                        @endphp

                        <div class="rgc-person">
                            <span class="rgc-person-avatar">
                                {{ $initials ?: 'P' }}
                            </span>

                            <span class="rgc-person-copy">
                                <strong>
                                    {{ $professor->name }}
                                </strong>

                                <small>Professeur</small>
                            </span>

                            @if($professor->is_active)
                                <i
                                    class="
                                        bi bi-circle-fill
                                        rgc-person-active
                                    "
                                ></i>
                            @endif
                        </div>
                    @empty
                        <div class="rgc-list-empty">
                            Aucun professeur affiché.
                        </div>
                    @endforelse
                </div>
            @endunless

            <div class="rgc-section-title">
                <span>Accès rapides</span>
            </div>

            <div class="rgc-actions">
                <a href="{{ route('student.subjects.index') }}">
                    <i class="bi bi-journals"></i>
                    <span>Mes matières</span>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="{{ route('student.lives') }}">
                    <i class="bi bi-camera-video-fill"></i>
                    <span>Lives</span>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="{{ route('student.assignments') }}">
                    <i class="bi bi-file-earmark-check-fill"></i>
                    <span>Devoirs</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </aside>

        <section class="rgc-chat">
            <header class="rgc-chat-header">
                <div class="rgc-chat-id">
                    <span class="rgc-chat-avatar">
                        <i class="bi {{ $icon }}"></i>
                    </span>

                    <div class="rgc-chat-copy">
                        <strong>{{ $title }}</strong>
                        <span>{{ $description }}</span>
                    </div>
                </div>

                <span class="rgc-message-count">
                    <i class="bi bi-chat-left-text"></i>
                    {{ $messages->count() }}
                </span>
            </header>

            <div
                class="rgc-messages"
                data-rgc-messages
                aria-live="polite"
            >
                <div class="rgc-stack">
                    @forelse(
                        $dateGroups
                        as $dateKey => $dayMessages
                    )
                        @php
                            $date =
                                \Carbon\Carbon::parse(
                                    $dateKey
                                );

                            $dateLabel =
                                $date->isToday()
                                    ? 'Aujourd’hui'
                                    : (
                                        $date->isYesterday()
                                            ? 'Hier'
                                            : ucfirst(
                                                $date
                                                    ->locale('fr')
                                                    ->isoFormat(
                                                        'dddd D MMMM YYYY'
                                                    )
                                            )
                                    );
                        @endphp

                        <div class="rgc-date">
                            <span>{{ $dateLabel }}</span>
                        </div>

                        @foreach($dayMessages as $message)
                            @php
                                $own =
                                    (int) $message->user_id
                                    === (int) auth()->id();

                                $author =
                                    $message->user?->name
                                    ?? (
                                        $isAdministration
                                            ? 'Administration'
                                            : 'Utilisateur'
                                    );

                                $initials =
                                    collect(
                                        preg_split(
                                            '/\s+/u',
                                            trim($author)
                                        )
                                    )
                                    ->filter()
                                    ->take(2)
                                    ->map(
                                        fn ($part) =>
                                            mb_strtoupper(
                                                mb_substr(
                                                    $part,
                                                    0,
                                                    1
                                                )
                                            )
                                    )
                                    ->implode('');
                            @endphp

                            <article
                                class="rgc-message
                                    {{ $own ? 'own' : 'other' }}"
                            >
                                @unless($own)
                                    <span class="rgc-msg-avatar">
                                        {{ $initials ?: 'U' }}
                                    </span>
                                @endunless

                                <div class="rgc-msg-wrap">
                                    @unless($own)
                                        <div class="rgc-author">
                                            {{ $author }}
                                        </div>
                                    @endunless

                                    <div class="rgc-bubble">
                                        <p>{{ $message->message }}</p>

                                        <footer>
                                            <span>
                                                {{
                                                    $message
                                                        ->created_at
                                                        ->format('H:i')
                                                }}
                                            </span>

                                            @if($own)
                                                <i
                                                    class="
                                                        bi bi-check2-all
                                                        rgc-read
                                                    "
                                                ></i>

                                                <form
                                                    method="POST"
                                                    action="{{
                                                        route(
                                                            'student.chat.delete'
                                                        )
                                                    }}"
                                                    class="rgc-delete-form"
                                                    onsubmit="
                                                        return confirm(
                                                            'Supprimer ce message ?'
                                                        )
                                                    "
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <input
                                                        type="hidden"
                                                        name="messages[]"
                                                        value="{{
                                                            $message->id
                                                        }}"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="rgc-delete"
                                                        title="Supprimer"
                                                    >
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </footer>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    @empty
                        <div class="rgc-empty">
                            <span>
                                <i class="bi bi-chat-heart"></i>
                            </span>

                            <h3>Commencez la conversation</h3>

                            <p>
                                Envoyez le premier message
                                dans cet espace.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <footer class="rgc-composer">
                <form
                    method="POST"
                    action="{{ route('student.chat.send') }}"
                    data-rgc-form
                >
                    @csrf

                    <input
                        type="hidden"
                        name="subject_id"
                        value="{{ $subject->id }}"
                    >

                    <button
                        type="button"
                        class="rgc-icon-btn"
                        disabled
                        title="Pièces jointes bientôt disponibles"
                    >
                        <i class="bi bi-paperclip"></i>
                    </button>

                    <div class="rgc-field">
                        <textarea
                            name="message"
                            rows="1"
                            maxlength="5000"
                            placeholder="Écrivez votre message..."
                            required
                            data-rgc-textarea
                        >{{ old('message') }}</textarea>

                        <span
                            class="rgc-counter"
                            data-rgc-counter
                        >
                            0 / 5000
                        </span>
                    </div>

                    <button
                        type="button"
                        class="rgc-icon-btn"
                        data-rgc-emoji
                        title="Ajouter un emoji"
                    >
                        <i class="bi bi-emoji-smile"></i>
                    </button>

                    <button
                        type="submit"
                        class="rgc-send"
                        data-rgc-send
                    >
                        <i class="bi bi-send-fill"></i>
                        Envoyer
                    </button>
                </form>

                <div class="rgc-help">
                    Entrée pour envoyer · Maj + Entrée
                    pour une nouvelle ligne
                </div>
            </footer>
        </section>
    </div>
</div>

<script
    src="{{ asset('js/role-group-chat-v1.js') }}?v={{
        file_exists(public_path('js/role-group-chat-v1.js'))
            ? filemtime(public_path('js/role-group-chat-v1.js'))
            : 1
    }}"
></script>
@endsection
