@extends('layouts.student')

@section('title', 'Discussion — ' . ($subject->name ?? 'Chat'))
@section('page_title', $subject->name ?? 'Discussion')
@section('breadcrumb', 'Discussions / ' . ($subject->name ?? 'Chat'))

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/student-chat-v7.css') }}"
    >
@endpush

@section('content')
@php
    $isAdministration =
        mb_strtolower($subject->name ?? '') === 'administration';

    $displayMessages = $messages
        ->sortBy('created_at')
        ->values();

    $messageCount = $displayMessages->count();

    $subjectSlug = \Illuminate\Support\Str::lower(
        \Illuminate\Support\Str::ascii(
            $subject->name ?? ''
        )
    );

    if ($isAdministration) {
        $chatTone = 'administration';
        $chatIcon = 'headset';
    } elseif (str_contains($subjectSlug, 'coran')) {
        $chatTone = 'emerald';
        $chatIcon = 'book-half';
    } elseif (str_contains($subjectSlug, 'arabe')) {
        $chatTone = 'indigo';
        $chatIcon = 'translate';
    } elseif (str_contains($subjectSlug, 'soutien')) {
        $chatTone = 'amber';
        $chatIcon = 'mortarboard-fill';
    } else {
        $chatTone = 'violet';
        $chatIcon = 'chat-square-text-fill';
    }

    $lastMessageDate = null;
@endphp

<div class="student-chat-page student-conversation-page">

    <a
        href="{{ route('student.chats') }}"
        class="student-chat-back-link"
    >
        <i class="bi bi-arrow-left"></i>
        Retour aux discussions
    </a>

    <section class="student-conversation-shell {{ $chatTone }}">

        <header class="student-conversation-header">
            <div class="student-conversation-heading">
                <span class="student-conversation-icon">
                    <i class="bi bi-{{ $chatIcon }}"></i>
                </span>

                <div>
                    <span class="student-chat-kicker">
                        {{
                            $isAdministration
                                ? 'Conversation privée'
                                : 'Discussion de matière'
                        }}
                    </span>

                    <h2>
                        {{ $subject->name ?? 'Discussion' }}
                    </h2>

                    <p>
                        @if($isAdministration)
                            Échange direct et privé avec
                            l’administration.
                        @else
                            Posez vos questions et échangez avec les
                            professeurs autorisés.
                        @endif
                    </p>
                </div>
            </div>

            <div class="student-conversation-status">
                <span class="student-chat-online-dot"></span>

                <div>
                    <strong>{{ $messageCount }}</strong>

                    <small>
                        message{{ $messageCount > 1 ? 's' : '' }}
                    </small>
                </div>
            </div>
        </header>

        <div class="student-conversation-notice">
            <i
                class="bi {{
                    $isAdministration
                        ? 'bi-lock-fill'
                        : 'bi-info-circle-fill'
                }}"
            ></i>

            <span>
                @if($isAdministration)
                    Cette conversation est visible uniquement par
                    vous et l’administration.
                @else
                    Restez respectueux et utilisez cet espace pour
                    les échanges pédagogiques.
                @endif
            </span>
        </div>

        <div
            class="student-conversation-messages"
            id="studentChatBox"
            aria-live="polite"
        >
            @forelse($displayMessages as $msg)
                @php
                    $messageDate =
                        $msg->created_at->toDateString();

                    $isOwnMessage =
                        (int) $msg->user_id
                        === (int) auth()->id();

                    $authorName =
                        $msg->user->name
                        ?? 'Utilisateur';

                    $authorInitial = strtoupper(
                        mb_substr(
                            trim($authorName),
                            0,
                            1
                        )
                    );

                    if ($msg->created_at->isToday()) {
                        $dateLabel = 'Aujourd’hui';
                    } elseif ($msg->created_at->isYesterday()) {
                        $dateLabel = 'Hier';
                    } else {
                        $dateLabel = ucfirst(
                            $msg->created_at
                                ->translatedFormat(
                                    'l d F Y'
                                )
                        );
                    }
                @endphp

                @if($lastMessageDate !== $messageDate)
                    <div class="student-chat-date-separator">
                        <span>{{ $dateLabel }}</span>
                    </div>

                    @php
                        $lastMessageDate = $messageDate;
                    @endphp
                @endif

                <article
                    class="student-message-row {{
                        $isOwnMessage
                            ? 'own'
                            : 'other'
                    }}"
                >
                    @unless($isOwnMessage)
                        <span class="student-message-avatar">
                            {{ $authorInitial }}
                        </span>
                    @endunless

                    <div class="student-message-content">
                        @unless($isOwnMessage)
                            <div class="student-message-author">
                                {{ $authorName }}
                            </div>
                        @endunless

                        <div class="student-message-bubble">
                            <p>
                                {!! nl2br(e($msg->message)) !!}
                            </p>

                            <time
                                datetime="{{
                                    $msg->created_at->toIso8601String()
                                }}"
                            >
                                {{ $msg->created_at->format('H:i') }}

                                @if($isOwnMessage)
                                    <i class="bi bi-check2-all"></i>
                                @endif
                            </time>
                        </div>
                    </div>
                </article>
            @empty
                <div class="student-conversation-empty">
                    <span>
                        <i class="bi bi-chat-heart-fill"></i>
                    </span>

                    <h3>Commencez la conversation</h3>

                    <p>
                        Aucun message n’a encore été envoyé dans cet
                        espace. Écrivez le premier message.
                    </p>
                </div>
            @endforelse
        </div>

        <footer class="student-conversation-composer">
            @if($errors->has('message'))
                <div class="student-chat-validation-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $errors->first('message') }}
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('student.chat.send') }}"
                id="studentChatForm"
            >
                @csrf

                <input
                    type="hidden"
                    name="subject_id"
                    value="{{ $subject->id ?? '' }}"
                >

                <div class="student-chat-composer-main">
                    <span class="student-chat-composer-icon">
                        <i class="bi bi-chat-dots-fill"></i>
                    </span>

                    <textarea
                        name="message"
                        id="studentChatInput"
                        rows="1"
                        maxlength="5000"
                        placeholder="Écrivez votre message..."
                        required
                    >{{ old('message') }}</textarea>

                    <button
                        type="submit"
                        class="student-chat-send-button"
                        id="studentChatSendButton"
                    >
                        <i class="bi bi-send-fill"></i>
                        <span>Envoyer</span>
                    </button>
                </div>

                <div class="student-chat-composer-help">
                    <span>
                        <i class="bi bi-keyboard"></i>
                        Entrée pour envoyer · Maj + Entrée
                        pour une nouvelle ligne
                    </span>

                    <span id="studentChatCharacterCount">
                        0 / 5000
                    </span>
                </div>
            </form>
        </footer>
    </section>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/student-chat-v7.js') }}"></script>
@endpush
