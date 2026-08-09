@extends('layouts.prof')

@section('title', 'Chat — ' . ($subject->name ?? 'Discussion'))
@section('page_title', 'Chat')
@section(
    'breadcrumb',
    ($isAdministration ?? false)
        ? 'Conversation privée → Administration'
        : 'Communication → ' . ($subject->name ?? 'Matière')
)

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

    $isQuran =
        $name === 'coran';

    $themeClass =
        $isAdministration
            ? 'is-admin'
            : ($isQuran ? 'is-quran' : '');

    $icon =
        $isAdministration
            ? 'bi-shield-lock-fill'
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
            ? 'Conversation privée avec l’équipe administrative.'
            : (
                $isQuran
                    ? 'Suivi du Coran, Tajwid, mémorisation et devoirs.'
                    : 'Échanges pédagogiques autour des cours d’Arabe.'
            );

    $messages =
        ($messages ?? collect())
            ->sortBy('created_at')
            ->values();

    $context =
        $groupChatContext ?? [
            'participants_count' => 0,
            'active_accounts_count' => 0,
            'students_count' => 0,
            'recent_authors' => collect(),
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

    @if($errors->any())
        <div class="rgc-alert danger">
            <i class="bi bi-exclamation-triangle-fill"></i>

            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <section class="rgc-hero">
        <div>
            <a
                href="{{ route('prof.chat.subjects') }}"
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
                                ? 'Conversation confidentielle'
                                : 'Espace enseignant'
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
                                Échange direct avec
                                l’administration.
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
                            <i class="bi bi-mortarboard-fill"></i>
                            {{
                                $context[
                                    'students_count'
                                ]
                            }}
                            étudiant(s)
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
                            : 'Conseil'
                    }}
                </strong>

                <p>
                    @if($isAdministration)
                        Cette conversation reste entre vous
                        et l’administration.
                    @elseif($isQuran)
                        Répondez aux questions sur le Coran,
                        le Tajwid et la mémorisation.
                    @else
                        Répondez aux questions et exercices
                        liés aux cours d’Arabe.
                    @endif
                </p>
            </section>

            @unless($isAdministration)
                <div class="rgc-section-title">
                    <span>Participants récents</span>
                </div>

                <div class="rgc-person-list">
                    @forelse(
                        $context['recent_authors']->take(6)
                        as $participant
                    )
                        @php
                            $initials =
                                collect(
                                    preg_split(
                                        '/\s+/u',
                                        trim($participant->name)
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
                                {{ $initials ?: 'U' }}
                            </span>

                            <span class="rgc-person-copy">
                                <strong>
                                    {{ $participant->name }}
                                </strong>

                                <small>
                                    {{
                                        $participant->role
                                            === 'prof'
                                                ? 'Professeur'
                                                : 'Étudiant'
                                    }}
                                </small>
                            </span>

                            @if($participant->is_active)
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
                            Aucun participant récent.
                        </div>
                    @endforelse
                </div>
            @endunless

            <div class="rgc-section-title">
                <span>Accès rapides</span>
            </div>

            <div class="rgc-actions">
                <a href="{{ route('prof.subjects.list') }}">
                    <i class="bi bi-journals"></i>
                    <span>Mes matières</span>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="{{ route('prof.courses.index') }}">
                    <i class="bi bi-play-btn-fill"></i>
                    <span>Mes cours</span>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="{{ route('prof.schedule') }}">
                    <i class="bi bi-calendar3"></i>
                    <span>Emploi du temps</span>
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
                                            : 'Étudiant'
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
                                                            'prof.chat.delete'
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
                                                        name="subject_id"
                                                        value="{{
                                                            $subject->id
                                                        }}"
                                                    >

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

                            <h3>Aucun message pour le moment</h3>

                            <p>
                                Commencez la discussion
                                avec votre groupe.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <footer class="rgc-composer">
                <form
                    method="POST"
                    action="{{ route('prof.chat.send') }}"
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
