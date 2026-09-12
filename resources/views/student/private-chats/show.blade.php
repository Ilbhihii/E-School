@extends('layouts.student')

@section('title', 'Conversation privée')
@section('page_title', 'Conversation privée')
@section('breadcrumb', 'Communication → Messages privés')

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
    $messages =
        ($messages ?? collect())
            ->sortBy('created_at')
            ->values();

    $other = $professor;

    $initials =
        collect(
            preg_split(
                '/\s+/u',
                trim($other->name ?? '')
            )
        )
        ->filter()
        ->take(2)
        ->map(
            fn ($part) =>
                mb_strtoupper(
                    mb_substr($part, 0, 1)
                )
        )
        ->implode('');

    $dateGroups =
        $messages->groupBy(
            fn ($message) =>
                $message->created_at
                    ->format('Y-m-d')
        );

    $lastActivity =
        $messages->last()?->created_at;
@endphp

<div class="rgc-page is-admin">
    @if(session('success'))
        <div class="rgc-alert success">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
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
                href="{{ route('student.private-chats.index') }}"
                class="rgc-back"
            >
                <i class="bi bi-arrow-left"></i>
                Messages privés
            </a>

            <div class="rgc-hero-main">
                <span class="rgc-hero-icon">
                    <i class="bi bi-person-lock"></i>
                </span>

                <div>
                    <span class="rgc-kicker">
                        Conversation confidentielle
                    </span>

                    <h1>{{ $other->name }}</h1>

                    <p>
                        Discussion privée étudiant ↔ professeur
                        pour {{ $subject->name }}.
                    </p>
                </div>
            </div>
        </div>

        <span class="rgc-status">
            <i class="bi bi-lock-fill"></i>
            Conversation privée
        </span>
    </section>

    <div class="rgc-layout">
        <aside class="rgc-side">
            <section class="rgc-side-card">
                <div class="rgc-side-head">
                    <span class="rgc-side-icon">
                        {{ $initials ?: 'E' }}
                    </span>

                    <div>
                        <h2>{{ $other->name }}</h2>

                        <p>
                            Professeur · {{ $subject->name }}
                        </p>
                    </div>
                </div>

                <div class="rgc-side-meta">
                    <span>
                        <i class="bi bi-book"></i>
                        {{ $subject->name }}
                    </span>

                    <span>
                        <i class="bi bi-mortarboard-fill"></i>
                        {{ $path->level_name }}
                    </span>

                    <span>
                        <i class="bi bi-diagram-3"></i>
                        {{ $path->class_name }}
                    </span>
                </div>
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
                            @if($lastActivity)
                                {{
                                    $lastActivity->isToday()
                                        ? 'Aujourd’hui'
                                        : $lastActivity
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
                    <i class="bi bi-shield-lock-fill"></i>
                    Confidentialité
                </strong>

                <p>
                    Cette conversation est visible uniquement
                    par vous et ce professeur.
                </p>
            </section>

            <div class="rgc-section-title">
                <span>Contact</span>
            </div>

            <div class="rgc-person-list">
                <div class="rgc-person">
                    <span class="rgc-person-avatar">
                        {{ $initials ?: 'E' }}
                    </span>

                    <span class="rgc-person-copy">
                        <strong>{{ $other->name }}</strong>
                        <small>Professeur</small>
                    </span>

                    <i class="bi bi-person-badge-fill rgc-person-active"></i>
                </div>
            </div>

            <div class="rgc-section-title">
                <span>Accès rapides</span>
            </div>

            <div class="rgc-actions">
                <a href="{{ route('student.private-chats.index') }}">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Messages privés</span>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="{{ route('student.chats') }}">
                    <i class="bi bi-chat-square-text-fill"></i>
                    <span>Discussions</span>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="javascript:history.back()">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>Retour</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </aside>

        <section class="rgc-chat">
            <header class="rgc-chat-header">
                <div class="rgc-chat-id">
                    <span class="rgc-chat-avatar">
                        {{ $initials ?: 'E' }}
                    </span>

                    <div class="rgc-chat-copy">
                        <strong>{{ $other->name }}</strong>
                        <span>
                            Professeur · {{ $subject->name }} ·
                            {{ $path->level_name }} ·
                            {{ $path->class_name }}
                        </span>
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
                    @forelse($dateGroups as $dateKey => $dayMessages)
                        @php
                            $date = \Carbon\Carbon::parse($dateKey);

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
                                    $own
                                        ? 'Vous'
                                        : ($message->user?->name ?? 'Professeur');

                                $authorInitials =
                                    collect(
                                        preg_split(
                                            '/\s+/u',
                                            trim($message->user?->name ?? 'Professeur')
                                        )
                                    )
                                    ->filter()
                                    ->take(2)
                                    ->map(
                                        fn ($part) =>
                                            mb_strtoupper(
                                                mb_substr($part, 0, 1)
                                            )
                                    )
                                    ->implode('');
                            @endphp

                            <article
                                class="rgc-message {{ $own ? 'own' : 'other' }}"
                            >
                                @unless($own)
                                    <span class="rgc-msg-avatar">
                                        {{ $authorInitials ?: 'E' }}
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
                                                {{ $message->created_at->format('H:i') }}
                                            </span>

                                            @if($own)
                                                <i class="bi bi-check2-all rgc-read"></i>

                                                <form
                                                    method="POST"
                                                    action="{{ route('student.private-chats.delete', ['professor' => $other->id, 'subject' => $subject->id]) }}"
                                                    class="rgc-delete-form"
                                                    onsubmit="return confirm('Supprimer ce message ?')"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <input
                                                        type="hidden"
                                                        name="message_id"
                                                        value="{{ $message->id }}"
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
                                Commencez la discussion privée
                                avec ce professeur.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <footer class="rgc-composer">
                <form
                    method="POST"
                    action="{{ route('student.private-chats.send', ['professor' => $other->id, 'subject' => $subject->id]) }}"
                    data-rgc-form
                >
                    @csrf

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
                            placeholder="Écrivez votre message privé..."
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
