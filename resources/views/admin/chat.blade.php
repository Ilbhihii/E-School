@extends('layouts.admin')

@section(
    'title',
    'Chat - ' . ($conversationSpaceLabel ?? $subject->name)
)

@section('page_title', 'Chat')

@section(
    'breadcrumb',
    'Communication → ' . ($conversationSpaceLabel ?? $subject->name)
)

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/admin-group-chat-v2.css') }}?v={{
        file_exists(
            public_path('css/admin-group-chat-v2.css')
        )
            ? filemtime(
                public_path('css/admin-group-chat-v2.css')
            )
            : 2
    }}"
>
@endpush

@section('content')
@php
    $normalizedName =
        mb_strtolower(
            trim($subject->name)
        );

    $isArabicGroup =
        !$isAdministration
        && $normalizedName === 'arabe';

    $isQuranGroup =
        !$isAdministration
        && $normalizedName === 'coran';

    $isPedagogicalGroup =
        $isArabicGroup
        || $isQuranGroup;

    $theme = match ($normalizedName) {
        'arabe' => [
            'icon' => 'bi-translate',
            'label' => 'Groupe Arabe',
            'description' =>
                'Discussion sur les cours, exercices '
                . 'et activités d’Arabe.',
            'eyebrow' => 'Langue arabe',
            'note' =>
                'Utilisez cet espace pour coordonner '
                . 'les cours, exercices et révisions.',
            'primary' => '#6366F1',
            'secondary' => '#8B5CF6',
            'soft' => 'rgba(99,102,241,.14)',
        ],

        'coran' => [
            'icon' => 'bi-book-half',
            'label' => 'Groupe Coran',
            'description' =>
                'Discussion pédagogique autour du Coran, '
                . 'du Tajwid et de la mémorisation.',
            'eyebrow' => 'Coran & Tajwid',
            'note' =>
                'Centralisez ici les rappels de mémorisation, '
                . 'les devoirs et les points de Tajwid.',
            'primary' => '#7C3AED',
            'secondary' => '#4F46E5',
            'soft' => 'rgba(124,58,237,.14)',
        ],

        default => [
            'icon' =>
                ($conversationRole ?? 'student') === 'prof'
                    ? 'bi-person-workspace'
                    : 'bi-mortarboard-fill',
            'label' =>
                $conversationSpaceLabel
                ?? 'Communication',
            'description' =>
                ($conversationRole ?? 'student') === 'prof'
                    ? 'Conversations privées avec les professeurs.'
                    : 'Conversations privées avec les étudiants.',
            'eyebrow' => 'Conversation privée',
            'note' => '',
            'primary' =>
                ($conversationRole ?? 'student') === 'prof'
                    ? '#F59E0B'
                    : '#10B981',
            'secondary' =>
                ($conversationRole ?? 'student') === 'prof'
                    ? '#EA580C'
                    : '#059669',
            'soft' =>
                ($conversationRole ?? 'student') === 'prof'
                    ? 'rgba(245,158,11,.12)'
                    : 'rgba(16,185,129,.12)',
        ],
    };

    $visibleMessages =
        $messages->count();

    $lastMessage =
        $messages
            ->sortByDesc('created_at')
            ->first();

    $messageGroups =
        $messages->groupBy(
            fn ($message) =>
                $message->created_at
                    ->format('Y-m-d')
        );

    $currentTitle =
        $isAdministration
            ? (
                $selectedConversationUser
                    ? $selectedConversationUser->name
                    : $theme['label']
            )
            : $theme['label'];

    $currentSubtitle =
        $isAdministration
            ? (
                $selectedConversationUser
                    ? (
                        $selectedConversationUser->role
                            === 'prof'
                                ? 'Professeur · conversation privée'
                                : 'Étudiant · conversation privée'
                    )
                    : $theme['description']
            )
            : $theme['description'];

    $groupParticipants =
        $groupParticipants
        ?? collect();

    $groupParticipantsCount =
        $groupParticipantsCount
        ?? 0;

    $groupActiveParticipantsCount =
        $groupActiveParticipantsCount
        ?? 0;
@endphp

<div
    class="agc-page"
    style="
        --agc-primary: {{ $theme['primary'] }};
        --agc-secondary: {{ $theme['secondary'] }};
        --agc-soft: {{ $theme['soft'] }};
    "
>
    @if(session('success'))
        <div class="agc-alert agc-alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="agc-alert agc-alert-danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="agc-alert agc-alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>

            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <section class="agc-hero">
        <div class="agc-hero-copy">
            <a
                href="{{ route('admin.chat.list') }}"
                class="agc-back"
            >
                <i class="bi bi-arrow-left"></i>
                Discussions
            </a>

            <div class="agc-hero-title">
                <span class="agc-hero-icon">
                    <i class="bi {{ $theme['icon'] }}"></i>
                </span>

                <div>
                    <span class="agc-eyebrow">
                        {{ $theme['eyebrow'] }}
                    </span>

                    <h1>{{ $currentTitle }}</h1>

                    <p>{{ $currentSubtitle }}</p>
                </div>
            </div>
        </div>

        <div class="agc-hero-status">
            <span class="agc-active-dot"></span>

            {{
                $isAdministration
                    ? 'Conversation privée'
                    : 'Espace actif'
            }}
        </div>
    </section>

    <div
        class="agc-layout
            {{ $isPedagogicalGroup
                ? 'is-group'
                : 'is-private' }}"
    >
        <aside class="agc-sidebar">
            @if($isPedagogicalGroup)
                <section class="agc-group-card">
                    <div class="agc-group-heading">
                        <span class="agc-group-icon">
                            <i class="bi {{ $theme['icon'] }}"></i>
                        </span>

                        <div>
                            <div class="agc-group-title-row">
                                <h2>{{ $theme['label'] }}</h2>

                                <span class="agc-group-badge">
                                    <i class="bi bi-shield-check"></i>
                                    Groupe
                                </span>
                            </div>

                            <p>
                                {{ $theme['description'] }}
                            </p>
                        </div>
                    </div>

                    <div class="agc-participant-line">
                        <span>
                            <i class="bi bi-people"></i>
                            {{ $groupParticipantsCount }}
                            participant(s)
                        </span>

                        <span>
                            <i class="bi bi-person-check-fill"></i>
                            {{ $groupActiveParticipantsCount }}
                            compte(s) actif(s)
                        </span>
                    </div>
                </section>

                <div class="agc-mini-stats">
                    <article>
                        <span>
                            <i class="bi bi-chat-left-text-fill"></i>
                        </span>

                        <div>
                            <small>Messages</small>
                            <strong>{{ $visibleMessages }}</strong>
                        </div>
                    </article>

                    <article>
                        <span>
                            <i class="bi bi-clock-history"></i>
                        </span>

                        <div>
                            <small>Activité récente</small>

                            <strong>
                                @if($groupLastActivity)
                                    {{
                                        $groupLastActivity
                                            ->isToday()
                                                ? 'Aujourd’hui'
                                                : $groupLastActivity
                                                    ->locale('fr')
                                                    ->isoFormat('D MMM')
                                    }}
                                @else
                                    —
                                @endif
                            </strong>

                            @if($groupLastActivity)
                                <em>
                                    {{
                                        $groupLastActivity
                                            ->format('H:i')
                                    }}
                                </em>
                            @endif
                        </div>
                    </article>
                </div>

                <section class="agc-note">
                    <div class="agc-note-title">
                        <i class="bi bi-pin-angle-fill"></i>
                        Note du groupe
                    </div>

                    <p>{{ $theme['note'] }}</p>
                </section>

                <section class="agc-members">
                    <div class="agc-sidebar-section-title">
                        <span>Membres</span>

                        <small>
                            {{ $groupParticipantsCount }}
                        </small>
                    </div>

                    <div class="agc-member-list">
                        @forelse(
                            $groupParticipants->take(6)
                            as $participant
                        )
                            @php
                                $participantName =
                                    $participant->name
                                    ?? 'Utilisateur';

                                $initials =
                                    collect(
                                        preg_split(
                                            '/\s+/u',
                                            trim(
                                                $participantName
                                            )
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

                            <div class="agc-member">
                                <span class="agc-member-avatar">
                                    {{ $initials ?: 'U' }}
                                </span>

                                <span class="agc-member-copy">
                                    <strong>
                                        {{ $participantName }}
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
                                        class="bi bi-circle-fill
                                            agc-member-active"
                                        title="Compte actif"
                                    ></i>
                                @endif
                            </div>
                        @empty
                            <div class="agc-side-empty">
                                Aucun membre assigné.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="agc-quick-actions">
                    <div class="agc-sidebar-section-title">
                        <span>Actions rapides</span>
                    </div>

                    <a href="{{ url('/admin/subjects') }}">
                        <i class="bi bi-journals"></i>
                        <span>Matières</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <a href="{{ url('/admin/courses') }}">
                        <i class="bi bi-play-btn"></i>
                        <span>Cours</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <a href="{{ url('/admin/schedule') }}">
                        <i class="bi bi-calendar3"></i>
                        <span>Emploi du temps</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </section>
            @else
                <section class="agc-private-heading">
                    <div>
                        <span class="agc-group-icon">
                            <i class="bi {{ $theme['icon'] }}"></i>
                        </span>

                        <div>
                            <h2>{{ $theme['label'] }}</h2>

                            <p>
                                {{
                                    $conversationUsers->count()
                                }}
                                contact(s)
                            </p>
                        </div>
                    </div>
                </section>

                <label class="agc-contact-search">
                    <i class="bi bi-search"></i>

                    <input
                        type="search"
                        id="adminChatContactSearch"
                        placeholder="Rechercher un contact..."
                        autocomplete="off"
                    >
                </label>

                <div
                    class="agc-contact-list"
                    id="adminChatContactList"
                >
                    @forelse(
                        $conversationUsers
                        as $conversationUser
                    )
                        @php
                            $isSelected =
                                $selectedConversationUser
                                && (int)
                                    $selectedConversationUser->id
                                    === (int)
                                    $conversationUser->id;

                            $contactLast =
                                $conversationUser
                                    ->conversation_last_message;

                            $contactInitials =
                                collect(
                                    preg_split(
                                        '/\s+/u',
                                        trim(
                                            $conversationUser->name
                                        )
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

                        <a
                            href="{{
                                route(
                                    'admin.chat',
                                    [
                                        'subject' =>
                                            $subject->id,
                                        'role' =>
                                            $conversationRole,
                                        'contact' =>
                                            $conversationUser->id,
                                    ]
                                )
                            }}"
                            class="agc-contact
                                {{
                                    $isSelected
                                        ? 'is-selected'
                                        : ''
                                }}"
                            data-contact-name="{{
                                mb_strtolower(
                                    $conversationUser->name
                                )
                            }}"
                        >
                            <span class="agc-contact-avatar">
                                {{ $contactInitials ?: 'U' }}
                            </span>

                            <span class="agc-contact-copy">
                                <strong>
                                    {{ $conversationUser->name }}
                                </strong>

                                <small>
                                    @if($contactLast)
                                        {{
                                            \Illuminate\Support\Str::limit(
                                                $contactLast->deleted_at
                                                    ? 'Message supprimé'
                                                    : $contactLast->message,
                                                38
                                            )
                                        }}
                                    @else
                                        Aucune conversation
                                    @endif
                                </small>
                            </span>

                            @if(
                                $conversationUser
                                    ->conversation_message_count
                                > 0
                            )
                                <span class="agc-contact-count">
                                    {{
                                        $conversationUser
                                            ->conversation_message_count
                                    }}
                                </span>
                            @endif
                        </a>
                    @empty
                        <div class="agc-side-empty">
                            Aucun contact disponible.
                        </div>
                    @endforelse
                </div>
            @endif
        </aside>

        <section class="agc-chat">
            <header class="agc-chat-header">
                <div class="agc-chat-identity">
                    <span class="agc-chat-avatar">
                        @if(
                            $isAdministration
                            && $selectedConversationUser
                        )
                            @php
                                $selectedInitials =
                                    collect(
                                        preg_split(
                                            '/\s+/u',
                                            trim(
                                                $selectedConversationUser
                                                    ->name
                                            )
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

                            {{ $selectedInitials ?: 'U' }}
                        @else
                            <i class="bi {{ $theme['icon'] }}"></i>
                        @endif
                    </span>

                    <div>
                        <strong>{{ $currentTitle }}</strong>

                        <span>{{ $currentSubtitle }}</span>
                    </div>
                </div>

                <div class="agc-chat-header-actions">
                    @if($isPedagogicalGroup)
                        <span class="agc-header-counter">
                            <i class="bi bi-chat-left-text"></i>
                            {{ $visibleMessages }}
                        </span>
                    @endif

                    <span class="agc-header-active">
                        <span></span>
                        Actif
                    </span>
                </div>
            </header>

            <div
                class="agc-messages"
                id="adminChatMessages"
            >
                <div class="agc-message-stack">
                    @forelse(
                        $messageGroups
                        as $messageDate => $groupedMessages
                    )
                        @php
                            $date =
                                \Carbon\Carbon::parse(
                                    $messageDate
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

                        <div class="agc-date-separator">
                            <span>{{ $dateLabel }}</span>
                        </div>

                        @foreach(
                            $groupedMessages
                            as $message
                        )
                            @php
                                $isOwn =
                                    (int) $message->user_id
                                    === (int) auth()->id();

                                $author =
                                    $message->user?->name
                                    ?? 'Utilisateur';

                                $authorInitials =
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
                                class="agc-message
                                    {{
                                        $isOwn
                                            ? 'is-own'
                                            : 'is-other'
                                    }}
                                    {{
                                        $message->deleted_at
                                            ? 'is-deleted'
                                            : ''
                                    }}"
                            >
                                @unless($isOwn)
                                    <span class="agc-message-avatar">
                                        {{ $authorInitials ?: 'U' }}
                                    </span>
                                @endunless

                                <div class="agc-message-content">
                                    @unless($isOwn)
                                        <div class="agc-message-author">
                                            {{ $author }}
                                        </div>
                                    @endunless

                                    <div class="agc-message-bubble">
                                        @if($message->deleted_at)
                                            <div class="agc-deleted-message">
                                                <i class="bi bi-trash3"></i>
                                                Message supprimé
                                            </div>
                                        @else
                                            <p>
                                                {{ $message->message }}
                                            </p>
                                        @endif

                                        <footer>
                                            <span>
                                                {{
                                                    $message
                                                        ->created_at
                                                        ->format('H:i')
                                                }}
                                            </span>

                                            @if(
                                                $isOwn
                                                && !$message->deleted_at
                                            )
                                                <i
                                                    class="
                                                        bi
                                                        bi-check2-all
                                                        agc-read-check
                                                    "
                                                ></i>
                                            @endif

                                            @if(!$message->deleted_at)
                                                <form
                                                    method="POST"
                                                    action="{{
                                                        route(
                                                            'admin.chat.delete'
                                                        )
                                                    }}"
                                                    class="agc-delete-form"
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
                                                        value="{{ $subject->id }}"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="messages[]"
                                                        value="{{ $message->id }}"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="agc-delete"
                                                        title="Supprimer"
                                                        aria-label="Supprimer le message"
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
                        <div class="agc-empty">
                            <span>
                                <i class="bi bi-chat-heart"></i>
                            </span>

                            <h2>
                                @if(
                                    $isAdministration
                                    && !$selectedConversationUser
                                )
                                    Sélectionnez un contact
                                @else
                                    Commencez la conversation
                                @endif
                            </h2>

                            <p>
                                @if(
                                    $isAdministration
                                    && !$selectedConversationUser
                                )
                                    Choisissez un contact dans
                                    la colonne de gauche.
                                @else
                                    Le premier message apparaîtra ici.
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <footer class="agc-composer">
                @if(
                    !$isAdministration
                    || $selectedConversationUser
                )
                    <form
                        method="POST"
                        action="{{ route('admin.chat.send') }}"
                        id="adminChatForm"
                    >
                        @csrf

                        <input
                            type="hidden"
                            name="subject_id"
                            value="{{ $subject->id }}"
                        >

                        @if(
                            $isAdministration
                            && $selectedConversationUser
                        )
                            <input
                                type="hidden"
                                name="conversation_user_id"
                                value="{{
                                    $selectedConversationUser->id
                                }}"
                            >
                        @endif

                        <button
                            type="button"
                            class="agc-composer-icon"
                            id="adminChatAttachmentButton"
                            title="Pièces jointes bientôt disponibles"
                            aria-label="Pièces jointes bientôt disponibles"
                            disabled
                        >
                            <i class="bi bi-paperclip"></i>
                        </button>

                        <div class="agc-composer-field">
                            <textarea
                                name="message"
                                id="adminChatMessage"
                                rows="1"
                                maxlength="5000"
                                placeholder="Écrivez votre message..."
                                required
                            >{{ old('message') }}</textarea>

                            <span
                                class="agc-character-count"
                                id="adminChatCharacterCount"
                            >
                                0 / 5000
                            </span>
                        </div>

                        <button
                            type="button"
                            class="agc-composer-icon"
                            id="adminChatEmojiButton"
                            title="Ajouter un emoji"
                        >
                            <i class="bi bi-emoji-smile"></i>
                        </button>

                        <button
                            type="submit"
                            class="agc-send"
                        >
                            <i class="bi bi-send-fill"></i>
                            <span>Envoyer</span>
                        </button>
                    </form>

                    <div class="agc-composer-help">
                        Entrée pour envoyer · Maj + Entrée pour
                        revenir à la ligne
                    </div>
                @else
                    <div class="agc-disabled-composer">
                        <i class="bi bi-person-check"></i>
                        Sélectionnez un contact pour écrire.
                    </div>
                @endif
            </footer>
        </section>
    </div>
</div>

<script
    src="{{ asset('js/admin-group-chat-v2.js') }}?v={{
        file_exists(
            public_path('js/admin-group-chat-v2.js')
        )
            ? filemtime(
                public_path('js/admin-group-chat-v2.js')
            )
            : 2
    }}"
></script>
@endsection
