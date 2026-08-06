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

@section('content')

@php
    $normalizedName = mb_strtolower(
        trim($subject->name)
    );

    $subjectTheme = match ($normalizedName) {
        'arabe' => [
            'icon' => 'bi-translate',
            'gradient' =>
                'linear-gradient(135deg,#0284C7,#2563EB)',
            'accent' => '#38BDF8',
        ],

        'coran' => [
            'icon' => 'bi-book-half',
            'gradient' =>
                'linear-gradient(135deg,#7C3AED,#A855F7)',
            'accent' => '#C084FC',
        ],

        'administration' => [
            'icon' => 'bi-shield-lock-fill',
            'gradient' =>
                'linear-gradient(135deg,#059669,#10B981)',
            'accent' => '#34D399',
        ],

        default => [
            'icon' => 'bi-chat-square-dots-fill',
            'gradient' =>
                'linear-gradient(135deg,#2563EB,#4F46E5)',
            'accent' => '#60A5FA',
        ],
    };

    if ($isAdministration) {
        $subjectTheme =
            ($conversationRole ?? 'student') === 'prof'
                ? [
                    'icon' => 'bi-person-workspace',
                    'gradient' =>
                        'linear-gradient(135deg,#EA580C,#F59E0B)',
                    'accent' => '#FBBF24',
                ]
                : [
                    'icon' => 'bi-mortarboard-fill',
                    'gradient' =>
                        'linear-gradient(135deg,#059669,#10B981)',
                    'accent' => '#34D399',
                ];
    }

    $messageGroups = $messages->groupBy(
        fn ($message) =>
            $message->created_at->format('Y-m-d')
    );
@endphp

<div
    class="admin-conversation-page"
    style="
        --subject-gradient:
            {{ $subjectTheme['gradient'] }};
        --subject-accent:
            {{ $subjectTheme['accent'] }};
    "
>
    <!-- =====================================================
         EN-TÊTE
         ===================================================== -->
    <div class="conversation-page-topbar">
        <a
            href="{{ route('admin.chat.list') }}"
            class="conversation-back-button"
        >
            <i class="bi bi-arrow-left"></i>
            <span>Discussions</span>
        </a>

        <div class="conversation-page-title">
            <span class="conversation-page-icon">
                <i class="bi {{ $subjectTheme['icon'] }}"></i>
            </span>

            <div>
                <h1>{{ $conversationSpaceLabel ?? $subject->name }}</h1>

                <p>
                    @if($isAdministration)
                        @if(($conversationRole ?? 'student') === 'prof')
                            Messages privés avec les professeurs
                        @else
                            Messages privés avec les étudiants
                        @endif
                    @else
                        Discussion pédagogique de groupe
                    @endif
                </p>
            </div>
        </div>

        <div class="conversation-status">
            <span class="conversation-status-dot"></span>
            Espace actif
        </div>
    </div>

    @if(session('success'))
        <div class="adm-alert adm-alert-success mb-3">
            <span class="adm-alert-icon">
                <i class="bi bi-check-circle-fill"></i>
            </span>

            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="adm-alert adm-alert-danger mb-3">
            <span class="adm-alert-icon">
                <i class="bi bi-exclamation-circle-fill"></i>
            </span>

            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <!-- =====================================================
         LAYOUT PRINCIPAL
         ===================================================== -->
    <div
        class="conversation-layout
            {{ $isAdministration
                ? 'has-contacts'
                : 'group-conversation' }}"
    >
        <!-- =================================================
             CONTACTS
             ================================================= -->
        @if($isAdministration)
            <aside class="conversation-contacts">
                <div class="contacts-header">
                    <div>
                        <strong>{{ $conversationSpaceLabel ?? 'Conversations' }}</strong>

                        <span>
                            {{ $conversationUsers->count() }}
                            contact(s)
                        </span>
                    </div>

                    <span class="contacts-header-icon">
                        <i class="bi bi-people-fill"></i>
                    </span>
                </div>

                <div class="contacts-search">
                    <i class="bi bi-search"></i>

                    <input
                        type="search"
                        id="conversationContactSearch"
                        placeholder="Rechercher..."
                        autocomplete="off"
                    >
                </div>

                <div
                    class="contacts-list"
                    id="conversationContacts"
                >
                    @forelse($conversationUsers as $conversationUser)
                        @php
                            $isSelected =
                                $selectedConversationUser
                                && (
                                    (int) $selectedConversationUser->id
                                    === (int) $conversationUser->id
                                );

                            $lastConversationMessage =
                                $conversationUser
                                    ->conversation_last_message;

                            $contactRole =
                                $conversationUser->role === 'prof'
                                    ? 'Professeur'
                                    : 'Étudiant';
                        @endphp

                        <a
                            href="{{
                                route(
                                    'admin.chat',
                                    [
                                        'subject' => $subject->id,
                                        'role' =>
                                            $conversationRole,
                                        'contact' =>
                                            $conversationUser->id,
                                    ]
                                )
                            }}"
                            class="contact-item
                                {{ $isSelected
                                    ? 'is-selected'
                                    : '' }}"
                            data-contact-name="{{
                                mb_strtolower(
                                    $conversationUser->name
                                )
                            }}"
                        >
                            <span class="contact-avatar">
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            $conversationUser->name,
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>

                            <span class="contact-information">
                                <strong>
                                    {{ $conversationUser->name }}
                                </strong>

                                <small>
                                    {{ $contactRole }}

                                    @if($lastConversationMessage)
                                        <span>·</span>

                                        {{
                                            \Illuminate\Support\Str::limit(
                                                $lastConversationMessage
                                                    ->message,
                                                24
                                            )
                                        }}
                                    @endif
                                </small>
                            </span>

                            @if(
                                $conversationUser
                                    ->conversation_message_count
                                > 0
                            )
                                <span class="contact-count">
                                    {{
                                        $conversationUser
                                            ->conversation_message_count
                                    }}
                                </span>
                            @endif
                        </a>
                    @empty
                        <div class="contacts-empty">
                            <i class="bi bi-person-x"></i>

                            <span>
                                Aucun contact disponible
                            </span>
                        </div>
                    @endforelse
                </div>
            </aside>
        @endif

        <!-- =================================================
             CONVERSATION
             ================================================= -->
        <section class="conversation-panel">
            <div class="conversation-panel-header">
                <div class="conversation-recipient">
                    <span class="recipient-avatar">
                        @if(
                            $isAdministration
                            && $selectedConversationUser
                        )
                            {{
                                mb_strtoupper(
                                    mb_substr(
                                        $selectedConversationUser
                                            ->name,
                                        0,
                                        1
                                    )
                                )
                            }}
                        @else
                            <i
                                class="bi {{
                                    $subjectTheme['icon']
                                }}"
                            ></i>
                        @endif
                    </span>

                    <div>
                        <strong>
                            @if(
                                $isAdministration
                                && $selectedConversationUser
                            )
                                {{
                                    $selectedConversationUser
                                        ->name
                                }}
                            @else
                                Groupe {{ $subject->name }}
                            @endif
                        </strong>

                        <span>
                            @if(
                                $isAdministration
                                && $selectedConversationUser
                            )
                                {{
                                    $selectedConversationUser
                                        ->role === 'prof'
                                        ? 'Professeur'
                                        : 'Étudiant'
                                }}
                                · Conversation privée
                            @else
                                {{ $messages->count() }}
                                message(s)
                            @endif
                        </span>
                    </div>
                </div>

                <div class="conversation-security">
                    <i class="bi bi-shield-check"></i>

                    {{
                        $isAdministration
                            ? 'Privée'
                            : 'Groupe'
                    }}
                </div>
            </div>

            <!-- =============================================
                 MESSAGES
                 ============================================= -->
            <div
                class="conversation-messages"
                id="conversationMessages"
            >
                <div class="conversation-message-stack">
                    @forelse(
                        $messageGroups
                        as $messageDate => $groupedMessages
                    )
                        @php
                            $date = \Carbon\Carbon::parse(
                                $messageDate
                            );

                            $dateLabel = $date->isToday()
                                ? 'Aujourd’hui'
                                : (
                                    $date->isYesterday()
                                        ? 'Hier'
                                        : $date->format('d/m/Y')
                                );
                        @endphp

                        <div class="conversation-date-separator">
                            <span>{{ $dateLabel }}</span>
                        </div>

                        @foreach($groupedMessages as $message)
                            @php
                                $isOwnMessage =
                                    (int) $message->user_id
                                    === (int) auth()->id();

                                $messageAuthor =
                                    $message->user?->name
                                    ?? 'Utilisateur inconnu';
                            @endphp

                            <div
                                class="conversation-message
                                    {{ $isOwnMessage
                                        ? 'is-own'
                                        : 'is-other' }}"
                            >
                                @unless($isOwnMessage)
                                    <span class="message-avatar">
                                        {{
                                            mb_strtoupper(
                                                mb_substr(
                                                    $messageAuthor,
                                                    0,
                                                    1
                                                )
                                            )
                                        }}
                                    </span>
                                @endunless

                                <div class="message-column">
                                    @unless($isOwnMessage)
                                        <div class="message-author">
                                            {{ $messageAuthor }}
                                        </div>
                                    @endunless

                                    <div class="message-bubble">
                                        <p
                                            class="{{
                                                $message->deleted_at
                                                    ? 'is-deleted'
                                                    : ''
                                            }}"
                                        >
                                            {{ $message->message }}
                                        </p>

                                        <div class="message-footer">
                                            <span>
                                                {{
                                                    $message->created_at
                                                        ->format('H:i')
                                                }}
                                            </span>

                                            @if($isOwnMessage)
                                                <i
                                                    class="bi
                                                        bi-check2-all"
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
                                                    class="message-delete-form"
                                                    onsubmit="
                                                        return confirm(
                                                            'Supprimer uniquement ce message ?'
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
                                                        class="message-delete-button"
                                                        title="Supprimer ce message"
                                                        aria-label="Supprimer ce message"
                                                    >
                                                        <i
                                                            class="bi
                                                                bi-trash3"
                                                        ></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        @if($message->deleted_at)
                                            <small>
                                                <i
                                                    class="bi bi-trash3"
                                                ></i>

                                                Message supprimé
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="conversation-empty">
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
                                    Choisissez {{
                                        ($conversationRole ?? 'student')
                                            === 'prof'
                                            ? 'un professeur'
                                            : 'un étudiant'
                                    }} dans la liste.
                                @else
                                    Envoyez le premier message dans
                                    cet espace de discussion.
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- =============================================
                 COMPOSER
                 ============================================= -->
            <div class="conversation-composer">
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

                        <div class="composer-shell">
                            <span class="composer-prefix-icon">
                                <i class="bi bi-chat-dots"></i>
                            </span>

                            <textarea
                                name="message"
                                id="adminChatMessage"
                                rows="1"
                                maxlength="5000"
                                placeholder="Écrivez votre message..."
                                required
                            >{{ old('message') }}</textarea>

                            <span class="composer-character-count">
                                <span id="chatCharacterCount">0</span>
                                / 5000
                            </span>
                        </div>

                        <button
                            type="submit"
                            class="conversation-send-button"
                        >
                            <i class="bi bi-send-fill"></i>
                            <span>Envoyer</span>
                        </button>
                    </form>

                    <div class="composer-help">
                        <i class="bi bi-info-circle"></i>

                        Entrée pour envoyer · Maj + Entrée
                        pour revenir à la ligne
                    </div>
                @else
                    <div class="conversation-disabled-composer">
                        <i class="bi bi-person-check"></i>

                        Sélectionnez un contact pour écrire
                        un message.
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>

<style>
.admin-conversation-page {
    --conversation-border:
        rgba(255,255,255,0.065);
}

/* =========================================================
   EN-TÊTE
   ========================================================= */

.conversation-page-topbar {
    min-height: 72px;
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 16px;
    margin-bottom: 0.85rem;
    padding: 0.8rem 0.9rem;
    border: 1px solid var(--conversation-border);
    border-radius: 16px;
    background:
        linear-gradient(
            145deg,
            rgba(17,27,47,0.94),
            rgba(9,17,32,0.97)
        );
    box-shadow:
        0 12px 34px rgba(0,0,0,0.17);
}

.conversation-back-button {
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 8px 11px;
    border: 1px solid rgba(255,255,255,0.075);
    border-radius: 11px;
    color: rgba(255,255,255,0.66);
    background: rgba(255,255,255,0.03);
    font-size: 0.7rem;
    font-weight: 720;
    text-decoration: none;
    transition:
        color 0.2s ease,
        border-color 0.2s ease,
        background 0.2s ease;
}

.conversation-back-button:hover {
    color: #ffffff;
    border-color: rgba(255,255,255,0.14);
    background: rgba(255,255,255,0.055);
}

.conversation-page-title {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 11px;
}

.conversation-page-icon,
.recipient-avatar {
    display: grid;
    place-items: center;
    color: #ffffff;
    background: var(--subject-gradient);
    box-shadow:
        0 9px 24px rgba(0,0,0,0.18);
}

.conversation-page-icon {
    width: 43px;
    height: 43px;
    flex: 0 0 43px;
    border-radius: 13px;
    font-size: 1rem;
}

.conversation-page-title h1 {
    margin: 0;
    color: rgba(255,255,255,0.96);
    font-size: 0.98rem;
    font-weight: 820;
}

.conversation-page-title p {
    margin: 2px 0 0;
    color: var(--adm-text-muted);
    font-size: 0.67rem;
}

.conversation-status {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 10px;
    border: 1px solid rgba(74,222,128,0.14);
    border-radius: 999px;
    color: #86EFAC;
    background: rgba(34,197,94,0.08);
    font-size: 0.63rem;
    font-weight: 760;
}

.conversation-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #4ADE80;
    box-shadow:
        0 0 10px rgba(74,222,128,0.9);
}

/* =========================================================
   LAYOUT
   ========================================================= */

.conversation-layout {
    width: min(100%, 1320px);
    height: clamp(
        540px,
        calc(100vh - 238px),
        680px
    );
    min-height: 540px;
    display: grid;
    margin: 0 auto;
    overflow: hidden;
    border: 1px solid var(--conversation-border);
    border-radius: 18px;
    background:
        linear-gradient(
            150deg,
            rgba(15,23,42,0.98),
            rgba(7,14,27,0.995)
        );
    box-shadow:
        0 20px 52px rgba(0,0,0,0.27);
}

.conversation-layout.has-contacts {
    grid-template-columns:
        270px minmax(0, 1fr);
}

.conversation-layout.group-conversation {
    grid-template-columns: 1fr;
}

/* =========================================================
   CONTACTS
   ========================================================= */

.conversation-contacts {
    min-width: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-right: 1px solid var(--conversation-border);
    background:
        linear-gradient(
            180deg,
            rgba(6,13,25,0.5),
            rgba(10,18,34,0.3)
        );
}

.contacts-header {
    min-height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 0.8rem 0.9rem;
    border-bottom: 1px solid var(--conversation-border);
}

.contacts-header > div {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.contacts-header strong {
    color: rgba(255,255,255,0.9);
    font-size: 0.79rem;
}

.contacts-header span {
    color: var(--adm-text-muted);
    font-size: 0.61rem;
}

.contacts-header-icon {
    width: 31px;
    height: 31px;
    display: grid;
    place-items: center;
    border-radius: 10px;
    color: rgba(255,255,255,0.35);
    background: rgba(255,255,255,0.035);
    font-size: 0.8rem;
}

.contacts-search {
    position: relative;
    margin: 0.7rem;
}

.contacts-search i {
    position: absolute;
    top: 50%;
    left: 11px;
    color: rgba(255,255,255,0.25);
    transform: translateY(-50%);
}

.contacts-search input {
    width: 100%;
    height: 38px;
    padding: 7px 10px 7px 34px;
    border: 1px solid rgba(255,255,255,0.065);
    border-radius: 11px;
    outline: none;
    color: rgba(255,255,255,0.84);
    background: rgba(255,255,255,0.035);
    font-size: 0.69rem;
}

.contacts-search input:focus {
    border-color: rgba(96,165,250,0.21);
    box-shadow:
        0 0 0 3px rgba(37,99,235,0.055);
}

.contacts-list {
    flex: 1;
    overflow-y: auto;
    padding: 0 0.5rem 0.65rem;
}

.contacts-list::-webkit-scrollbar,
.conversation-messages::-webkit-scrollbar {
    width: 5px;
}

.contacts-list::-webkit-scrollbar-thumb,
.conversation-messages::-webkit-scrollbar-thumb {
    border-radius: 999px;
    background: rgba(255,255,255,0.09);
}

.contact-item {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 4px;
    padding: 9px;
    border: 1px solid transparent;
    border-radius: 12px;
    color: inherit;
    text-decoration: none;
    transition:
        border-color 0.2s ease,
        background 0.2s ease,
        transform 0.2s ease;
}

.contact-item:hover {
    color: inherit;
    border-color: rgba(96,165,250,0.11);
    background: rgba(37,99,235,0.055);
    transform: translateX(2px);
}

.contact-item.is-selected {
    color: inherit;
    border-color: rgba(96,165,250,0.2);
    background:
        linear-gradient(
            135deg,
            rgba(37,99,235,0.13),
            rgba(79,70,229,0.08)
        );
    box-shadow:
        inset 3px 0 0 #3B82F6;
}

.contact-avatar {
    width: 39px;
    height: 39px;
    flex: 0 0 39px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    color: #ffffff;
    background:
        linear-gradient(
            135deg,
            #2563EB,
            #7C3AED
        );
    font-size: 0.77rem;
    font-weight: 820;
}

.contact-information {
    min-width: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.contact-information strong {
    overflow: hidden;
    color: rgba(255,255,255,0.86);
    font-size: 0.71rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.contact-information small {
    overflow: hidden;
    color: rgba(255,255,255,0.37);
    font-size: 0.59rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.contact-information small span {
    color: rgba(255,255,255,0.2);
}

.contact-count {
    min-width: 22px;
    height: 22px;
    display: grid;
    place-items: center;
    padding: 0 6px;
    border-radius: 999px;
    color: #DBEAFE;
    background: rgba(37,99,235,0.16);
    font-size: 0.57rem;
    font-weight: 820;
}

.contacts-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 1rem;
    color: rgba(255,255,255,0.34);
    font-size: 0.68rem;
}

/* =========================================================
   PANNEAU
   ========================================================= */

.conversation-panel {
    min-width: 0;
    display: grid;
    grid-template-rows:
        auto minmax(0, 1fr) auto;
}

.conversation-panel-header {
    min-height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 13px;
    padding: 0.75rem 0.9rem;
    border-bottom: 1px solid var(--conversation-border);
    background:
        linear-gradient(
            90deg,
            rgba(255,255,255,0.018),
            rgba(255,255,255,0.008)
        );
}

.conversation-recipient {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.recipient-avatar {
    width: 39px;
    height: 39px;
    flex: 0 0 39px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 820;
}

.conversation-recipient > div {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.conversation-recipient strong {
    overflow: hidden;
    color: rgba(255,255,255,0.92);
    font-size: 0.76rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.conversation-recipient span {
    color: rgba(255,255,255,0.39);
    font-size: 0.61rem;
}

.conversation-security {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 8px;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 999px;
    color: rgba(255,255,255,0.43);
    background: rgba(255,255,255,0.025);
    font-size: 0.6rem;
}

/* =========================================================
   MESSAGES
   ========================================================= */

.conversation-messages {
    position: relative;
    overflow-y: auto;
    padding: 1rem 1.05rem;
    scroll-behavior: smooth;
    background:
        radial-gradient(
            circle at 20% 20%,
            rgba(37,99,235,0.035),
            transparent 35%
        ),
        radial-gradient(
            circle at 80% 80%,
            rgba(16,185,129,0.025),
            transparent 35%
        );
}

.conversation-message-stack {
    min-height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

.conversation-date-separator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0.35rem 0 0.8rem;
}

.conversation-date-separator span {
    padding: 5px 9px;
    border: 1px solid rgba(255,255,255,0.045);
    border-radius: 999px;
    color: rgba(255,255,255,0.34);
    background: rgba(7,14,27,0.7);
    font-size: 0.56rem;
    font-weight: 690;
}

.conversation-message {
    width: fit-content;
    max-width: min(72%, 650px);
    display: flex;
    align-items: flex-end;
    gap: 8px;
    margin-bottom: 0.72rem;
}

.conversation-message.is-own {
    margin-left: auto;
}

.conversation-message.is-other {
    margin-right: auto;
}

.message-avatar {
    width: 31px;
    height: 31px;
    flex: 0 0 31px;
    display: grid;
    place-items: center;
    border-radius: 10px;
    color: #ffffff;
    background:
        linear-gradient(
            135deg,
            #059669,
            #10B981
        );
    font-size: 0.64rem;
    font-weight: 820;
}

.message-column {
    width: fit-content;
    max-width: 100%;
}

.message-author {
    margin: 0 5px 4px;
    color: rgba(255,255,255,0.5);
    font-size: 0.59rem;
    font-weight: 700;
}

.message-bubble {
    width: fit-content;
    min-width: 58px;
    max-width: 100%;
    padding: 9px 10px 6px;
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 15px;
    background: rgba(255,255,255,0.045);
    box-shadow:
        0 8px 18px rgba(0,0,0,0.08);
}

.conversation-message.is-own .message-bubble {
    border-color: rgba(96,165,250,0.16);
    border-bottom-right-radius: 4px;
    background:
        linear-gradient(
            135deg,
            rgba(37,99,235,0.42),
            rgba(79,70,229,0.3)
        );
}

.conversation-message.is-other .message-bubble {
    border-bottom-left-radius: 4px;
}

.message-bubble p {
    margin: 0;
    color: rgba(255,255,255,0.9);
    font-size: 0.74rem;
    line-height: 1.5;
    overflow-wrap: anywhere;
    white-space: pre-wrap;
}

.message-bubble p.is-deleted {
    color: rgba(255,255,255,0.34);
    font-style: italic;
    text-decoration: line-through;
}

.message-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
    margin-top: 4px;
    color: rgba(255,255,255,0.35);
    font-size: 0.52rem;
}

.message-footer > i {
    color: #93C5FD;
    font-size: 0.62rem;
}

.message-delete-form {
    display: inline-flex;
    margin: 0 0 0 2px;
}

.message-delete-button {
    width: 20px;
    height: 20px;
    display: inline-grid;
    place-items: center;
    padding: 0;
    border: 0;
    border-radius: 6px;
    color: rgba(255,255,255,0.35);
    background: transparent;
    font-size: 0.59rem;
    line-height: 1;
    opacity: 0;
    cursor: pointer;
    transition:
        opacity 0.2s ease,
        color 0.2s ease,
        background 0.2s ease;
}

.message-bubble:hover
.message-delete-button,
.message-delete-button:focus-visible {
    opacity: 1;
}

.message-delete-button:hover {
    color: #FCA5A5;
    background: rgba(239,68,68,0.13);
}

.message-bubble small {
    display: block;
    margin-top: 5px;
    color: #FCA5A5;
    font-size: 0.55rem;
}

.conversation-empty {
    min-height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1.5rem;
    text-align: center;
}

.conversation-empty > span {
    width: 70px;
    height: 70px;
    display: grid;
    place-items: center;
    margin-bottom: 0.9rem;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 20px;
    color: rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.028);
    font-size: 1.55rem;
}

.conversation-empty h2 {
    margin: 0 0 0.35rem;
    color: rgba(255,255,255,0.72);
    font-size: 0.92rem;
}

.conversation-empty p {
    max-width: 380px;
    margin: 0;
    color: rgba(255,255,255,0.35);
    font-size: 0.68rem;
    line-height: 1.55;
}

/* =========================================================
   COMPOSER
   ========================================================= */

.conversation-composer {
    padding: 0.72rem 0.85rem 0.65rem;
    border-top: 1px solid var(--conversation-border);
    background:
        linear-gradient(
            180deg,
            rgba(6,13,25,0.5),
            rgba(6,13,25,0.72)
        );
}

.conversation-composer form {
    display: flex;
    align-items: flex-end;
    gap: 8px;
}

.composer-shell {
    position: relative;
    min-width: 0;
    flex: 1;
}

.composer-prefix-icon {
    position: absolute;
    z-index: 2;
    top: 50%;
    left: 12px;
    color: rgba(255,255,255,0.26);
    transform: translateY(-50%);
}

.composer-shell textarea {
    width: 100%;
    min-height: 45px;
    max-height: 115px;
    resize: none;
    padding:
        11px 72px 11px 37px;
    border: 1px solid rgba(255,255,255,0.075);
    border-radius: 14px;
    outline: none;
    color: rgba(255,255,255,0.87);
    background: rgba(255,255,255,0.038);
    font-family: inherit;
    font-size: 0.72rem;
    line-height: 1.45;
}

.composer-shell textarea:focus {
    border-color: rgba(96,165,250,0.23);
    background: rgba(255,255,255,0.052);
    box-shadow:
        0 0 0 3px rgba(37,99,235,0.055);
}

.composer-character-count {
    position: absolute;
    right: 11px;
    bottom: 11px;
    color: rgba(255,255,255,0.22);
    font-size: 0.54rem;
}

.conversation-send-button {
    min-height: 45px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 15px;
    border: 0;
    border-radius: 13px;
    color: #ffffff;
    background: var(--subject-gradient);
    box-shadow:
        0 9px 23px rgba(0,0,0,0.18);
    font-size: 0.7rem;
    font-weight: 790;
    transition:
        filter 0.2s ease,
        transform 0.2s ease;
}

.conversation-send-button:hover {
    filter: brightness(1.07);
    transform: translateY(-2px);
}

.composer-help {
    margin-top: 5px;
    color: rgba(255,255,255,0.25);
    font-size: 0.54rem;
}

.conversation-disabled-composer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 11px;
    border: 1px dashed rgba(255,255,255,0.07);
    border-radius: 12px;
    color: rgba(255,255,255,0.37);
    font-size: 0.67rem;
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 930px) {
    .conversation-layout.has-contacts {
        grid-template-columns:
            235px minmax(0, 1fr);
    }
}

@media (max-width: 720px) {
    .conversation-page-topbar {
        grid-template-columns: auto 1fr;
    }

    .conversation-status {
        display: none;
    }

    .conversation-layout {
        height: auto;
        min-height: 680px;
    }

    .conversation-layout.has-contacts {
        grid-template-columns: 1fr;
        grid-template-rows:
            auto minmax(520px, 1fr);
    }

    .conversation-contacts {
        max-height: 230px;
        border-right: 0;
        border-bottom: 1px solid var(--conversation-border);
    }

    .contacts-list {
        display: flex;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 0.75rem;
    }

    .contact-item {
        min-width: 225px;
    }

    .conversation-message {
        max-width: 88%;
    }
}

@media (max-width: 500px) {
    .conversation-page-topbar {
        grid-template-columns: 1fr;
    }

    .conversation-back-button {
        width: fit-content;
    }

    .conversation-layout {
        margin-inline: -0.45rem;
        border-radius: 15px;
    }

    .conversation-panel-header,
    .conversation-messages,
    .conversation-composer {
        padding-inline: 0.72rem;
    }

    .conversation-send-button {
        width: 45px;
        padding: 0;
    }

    .conversation-send-button span {
        display: none;
    }

    .conversation-message {
        max-width: 94%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const messages =
        document.getElementById(
            'conversationMessages'
        );

    if (messages) {
        messages.scrollTop =
            messages.scrollHeight;

        window.setTimeout(() => {
            messages.scrollTo({
                top: messages.scrollHeight,
                behavior: 'smooth',
            });
        }, 100);
    }

    const messageInput =
        document.getElementById(
            'adminChatMessage'
        );

    const form =
        document.getElementById(
            'adminChatForm'
        );

    const characterCount =
        document.getElementById(
            'chatCharacterCount'
        );

    const resizeMessageInput = () => {
        if (!messageInput) {
            return;
        }

        messageInput.style.height = 'auto';

        messageInput.style.height =
            Math.min(
                messageInput.scrollHeight,
                115
            ) + 'px';

        if (characterCount) {
            characterCount.textContent =
                String(messageInput.value.length);
        }
    };

    if (messageInput) {
        resizeMessageInput();

        messageInput.addEventListener(
            'input',
            resizeMessageInput
        );

        messageInput.addEventListener(
            'keydown',
            event => {
                if (
                    event.key === 'Enter'
                    && !event.shiftKey
                ) {
                    event.preventDefault();

                    if (
                        form
                        && messageInput.value.trim()
                    ) {
                        form.submit();
                    }
                }
            }
        );
    }

    const contactSearch =
        document.getElementById(
            'conversationContactSearch'
        );

    const contactItems =
        Array.from(
            document.querySelectorAll(
                '#conversationContacts '
                + '.contact-item'
            )
        );

    if (contactSearch) {
        contactSearch.addEventListener(
            'input',
            () => {
                const searchValue =
                    contactSearch
                        .value
                        .trim()
                        .toLocaleLowerCase();

                contactItems.forEach(item => {
                    const contactName =
                        item.dataset.contactName
                        ?? '';

                    item.style.display =
                        contactName.includes(
                            searchValue
                        )
                            ? ''
                            : 'none';
                });
            }
        );
    }
});
</script>

@endsection
