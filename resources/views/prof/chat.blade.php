@extends('layouts.prof')

@section('title', 'Discussion — ' . ($subject->name ?? 'Chat'))
@section('page_title', 'Discussion')
@section('breadcrumb', ($isAdministration ?? false) ? 'Conversation privée avec l’administration' : 'Questions étudiants')

@section('content')
@php
    $isAdministrationChat = (bool) ($isAdministration ?? false);
    $messageCount = isset($messages) ? $messages->count() : 0;
    $ownMessageCount = isset($messages)
        ? $messages->where('user_id', auth()->id())->count()
        : 0;
    $lastMessageDate = null;
@endphp

<section class="pp-page-head pp-chat-page-head">
    <div class="pp-page-copy">
        <a href="{{ route('prof.chat.subjects') }}" class="pp-chat-back-link">
            <i class="bi bi-arrow-left"></i>
            <span>Retour aux discussions</span>
        </a>

        <span class="pp-eyebrow">
            <i class="bi {{ $isAdministrationChat ? 'bi-shield-lock-fill' : 'bi-chat-square-dots-fill' }}"></i>
            {{ $isAdministrationChat ? 'Conversation confidentielle' : 'Communication pédagogique' }}
        </span>

        <h1 class="pp-page-title">{{ $subject->name ?? 'Discussion' }}</h1>

        <p class="pp-page-description">
            {{ $isAdministrationChat
                ? 'Échangez directement et en privé avec l’équipe administrative.'
                : 'Répondez aux questions des étudiants dans un espace clair et organisé.'
            }}
        </p>
    </div>

    <div class="pp-page-actions">
        <span class="pp-soft-chip">
            <i class="bi bi-chat-left-text-fill"></i>
            {{ $messageCount }} message{{ $messageCount > 1 ? 's' : '' }}
        </span>
    </div>
</section>

@if(session('success'))
    <div class="alert alert-success pp-chat-alert" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger pp-chat-alert" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger pp-chat-alert" role="alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        <div>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

<div class="pp-chat-workspace">
    <section class="pp-conversation-card" aria-label="Discussion {{ $subject->name ?? '' }}">
        <header class="pp-conversation-header">
            <div class="pp-conversation-identity">
                <span class="pp-conversation-avatar {{ $isAdministrationChat ? 'is-admin' : '' }}">
                    <i class="bi {{ $isAdministrationChat ? 'bi-shield-check' : 'bi-book-half' }}"></i>
                </span>

                <div class="pp-conversation-copy">
                    <div class="pp-conversation-title-line">
                        <h2>{{ $subject->name ?? 'Chat' }}</h2>
                        <span class="pp-conversation-status">
                            <span class="pp-status-dot" aria-hidden="true"></span>
                            Discussion active
                        </span>
                    </div>

                    <p>
                        {{ $isAdministrationChat
                            ? 'Visible uniquement par vous et l’administration.'
                            : 'Espace d’échange avec les étudiants de cette matière.'
                        }}
                    </p>
                </div>
            </div>

            @if($ownMessageCount > 0)
                <button
                    type="button"
                    id="selectToggleProf"
                    class="pp-chat-select-button"
                    aria-pressed="false"
                >
                    <i class="bi bi-check2-square"></i>
                    <span id="toggleTextProf">Sélectionner</span>
                </button>
            @endif
        </header>

        <div class="pp-conversation-messages" id="profMsgList" aria-live="polite">
            @forelse($messages as $msg)
                @php
                    $isMine = (int) $msg->user_id === (int) auth()->id();
                    $messageDate = optional($msg->created_at)->toDateString();
                    $showDateSeparator = $messageDate !== $lastMessageDate;

                    if ($showDateSeparator) {
                        if (optional($msg->created_at)->isToday()) {
                            $dateLabel = 'Aujourd’hui';
                        } elseif (optional($msg->created_at)->isYesterday()) {
                            $dateLabel = 'Hier';
                        } else {
                            $dateLabel = optional($msg->created_at)->translatedFormat('l d F Y');
                        }

                        $lastMessageDate = $messageDate;
                    }
                @endphp

                @if($showDateSeparator)
                    <div class="pp-chat-date-separator" role="separator">
                        <span>{{ ucfirst($dateLabel) }}</span>
                    </div>
                @endif

                <article
                    class="pp-message-row {{ $isMine ? 'is-mine' : 'is-other' }}"
                    data-message-id="{{ $msg->id }}"
                    data-selectable="{{ $isMine ? '1' : '0' }}"
                >
                    @if(!$isMine)
                        <span class="pp-message-person-avatar" aria-hidden="true">
                            {{ mb_strtoupper(mb_substr($msg->user->name ?? ($isAdministrationChat ? 'Administration' : 'Étudiant'), 0, 1)) }}
                        </span>
                    @endif

                    <div class="pp-message-stack">
                        @if(!$isMine)
                            <span class="pp-message-author">
                                {{ $msg->user->name ?? ($isAdministrationChat ? 'Administration' : 'Étudiant') }}
                            </span>
                        @endif

                        <div class="pp-message-bubble {{ $msg->deleted_at ? 'is-deleted' : '' }}">
                            <p>{{ $msg->message }}</p>

                            @if($msg->deleted_at)
                                <span class="pp-message-deleted-note">
                                    <i class="bi bi-trash3"></i>
                                    Supprimé le {{ $msg->deleted_at->format('d/m/Y à H:i') }}
                                </span>
                            @endif

                            <span class="pp-message-meta">
                                {{ optional($msg->created_at)->format('H:i') }}
                                @if($isMine)
                                    <i class="bi bi-check2-all" aria-label="Message envoyé"></i>
                                @endif
                            </span>
                        </div>
                    </div>

                    @if($isMine)
                        <label class="pp-message-checkbox" title="Sélectionner ce message">
                            <input
                                type="checkbox"
                                value="{{ $msg->id }}"
                                class="msg-checkbox"
                                aria-label="Sélectionner le message du {{ optional($msg->created_at)->format('d/m/Y à H:i') }}"
                            >
                            <span><i class="bi bi-check-lg"></i></span>
                        </label>
                    @endif
                </article>
            @empty
                <div class="pp-chat-empty">
                    <span class="pp-chat-empty-icon">
                        <i class="bi bi-chat-heart"></i>
                    </span>
                    <h3>Aucun message pour le moment</h3>
                    <p>
                        {{ $isAdministrationChat
                            ? 'Commencez votre conversation privée avec l’administration.'
                            : 'Les étudiants n’ont pas encore envoyé de question dans cette matière.'
                        }}
                    </p>
                </div>
            @endforelse
        </div>

        <footer class="pp-conversation-composer">
            <form method="POST" action="{{ route('prof.chat.send') }}" class="pp-chat-compose-form" id="profChatForm">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id ?? '' }}">

                <div class="pp-chat-compose-field">
                    <textarea
                        name="message"
                        id="profChatMessage"
                        class="pp-chat-textarea"
                        rows="1"
                        maxlength="5000"
                        placeholder="Écrivez votre message…"
                        required
                        autocomplete="off"
                        aria-label="Votre message"
                    >{{ old('message') }}</textarea>

                    <div class="pp-chat-compose-footer">
                        <span class="pp-chat-keyboard-hint">
                            <i class="bi bi-keyboard"></i>
                            Entrée pour envoyer · Maj + Entrée pour une nouvelle ligne
                        </span>
                        <span class="pp-chat-character-count" id="profChatCounter">0 / 5000</span>
                    </div>
                </div>

                <button type="submit" class="pp-chat-send-button" id="profChatSendButton">
                    <span>Envoyer</span>
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </footer>
    </section>
</div>

@if($ownMessageCount > 0)
    <div class="pp-chat-selection-bar" id="deleteBarProf" aria-hidden="true">
        <form method="POST" action="{{ route('prof.chat.delete') }}" id="profDeleteMessagesForm">
            @csrf
            @method('DELETE')
            <input type="hidden" name="subject_id" value="{{ $subject->id ?? '' }}">
            <div id="selectedMessageInputs"></div>

            <div class="pp-chat-selection-copy">
                <span class="pp-selection-icon"><i class="bi bi-check2-square"></i></span>
                <div>
                    <strong id="countLabelProf">0 message sélectionné</strong>
                    <small>Vous pouvez supprimer uniquement vos messages.</small>
                </div>
            </div>

            <div class="pp-chat-selection-actions">
                <button type="button" id="selectAllProf" class="pp-selection-button is-secondary">
                    Tout sélectionner
                </button>
                <button type="button" id="cancelProf" class="pp-selection-button is-secondary">
                    Annuler
                </button>
                <button type="submit" class="pp-selection-button is-danger" id="confirmDeleteProf" disabled>
                    <i class="bi bi-trash3-fill"></i>
                    Supprimer
                </button>
            </div>
        </form>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const messageList = document.getElementById('profMsgList');
    const composeForm = document.getElementById('profChatForm');
    const messageInput = document.getElementById('profChatMessage');
    const counter = document.getElementById('profChatCounter');
    const sendButton = document.getElementById('profChatSendButton');

    function scrollConversationToBottom(behavior = 'auto') {
        if (!messageList) return;
        messageList.scrollTo({ top: messageList.scrollHeight, behavior });
    }

    scrollConversationToBottom();

    if (messageList) {
        new MutationObserver(function () {
            scrollConversationToBottom('smooth');
        }).observe(messageList, { childList: true, subtree: true });
    }

    function resizeComposer() {
        if (!messageInput) return;
        messageInput.style.height = 'auto';
        messageInput.style.height = Math.min(messageInput.scrollHeight, 150) + 'px';
    }

    function updateCharacterCount() {
        if (!messageInput || !counter) return;
        counter.textContent = messageInput.value.length + ' / 5000';
    }

    if (messageInput) {
        resizeComposer();
        updateCharacterCount();

        messageInput.addEventListener('input', function () {
            resizeComposer();
            updateCharacterCount();
        });

        messageInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();

                if (composeForm && messageInput.value.trim()) {
                    composeForm.requestSubmit();
                }
            }
        });
    }

    if (composeForm) {
        composeForm.addEventListener('submit', function () {
            if (!sendButton) return;
            sendButton.disabled = true;
            sendButton.classList.add('is-loading');
            sendButton.querySelector('span').textContent = 'Envoi…';
            sendButton.querySelector('i').className = 'bi bi-arrow-repeat';
        });
    }

    const toggle = document.getElementById('selectToggleProf');
    const toggleText = document.getElementById('toggleTextProf');
    const selectionBar = document.getElementById('deleteBarProf');
    const countLabel = document.getElementById('countLabelProf');
    const confirmButton = document.getElementById('confirmDeleteProf');
    const selectAllButton = document.getElementById('selectAllProf');
    const cancelButton = document.getElementById('cancelProf');
    const deleteForm = document.getElementById('profDeleteMessagesForm');
    const selectedInputs = document.getElementById('selectedMessageInputs');
    const selectableRows = Array.from(document.querySelectorAll('.pp-message-row[data-selectable="1"]'));
    const checkboxes = Array.from(document.querySelectorAll('.msg-checkbox'));
    let selectionMode = false;

    function selectedCheckboxes() {
        return checkboxes.filter(function (checkbox) {
            return checkbox.checked;
        });
    }

    function updateSelection() {
        const selected = selectedCheckboxes();
        const count = selected.length;

        selectableRows.forEach(function (row) {
            const checkbox = row.querySelector('.msg-checkbox');
            row.classList.toggle('is-selected', Boolean(checkbox && checkbox.checked));
        });

        if (countLabel) {
            countLabel.textContent = count + ' message' + (count > 1 ? 's' : '') + ' sélectionné' + (count > 1 ? 's' : '');
        }

        if (confirmButton) {
            confirmButton.disabled = count === 0;
        }
    }

    function setSelectionMode(enabled) {
        selectionMode = enabled;
        document.body.classList.toggle('pp-chat-selection-mode', enabled);

        if (toggle) {
            toggle.classList.toggle('is-active', enabled);
            toggle.setAttribute('aria-pressed', enabled ? 'true' : 'false');
        }

        if (toggleText) {
            toggleText.textContent = enabled ? 'Quitter la sélection' : 'Sélectionner';
        }

        if (selectionBar) {
            selectionBar.classList.toggle('is-visible', enabled);
            selectionBar.setAttribute('aria-hidden', enabled ? 'false' : 'true');
        }

        if (!enabled) {
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            updateSelection();
        }
    }

    if (toggle) {
        toggle.addEventListener('click', function () {
            setSelectionMode(!selectionMode);
        });
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateSelection);
    });

    selectableRows.forEach(function (row) {
        row.addEventListener('click', function (event) {
            if (!selectionMode || event.target.closest('.pp-message-checkbox')) return;
            const checkbox = row.querySelector('.msg-checkbox');
            if (!checkbox) return;
            checkbox.checked = !checkbox.checked;
            updateSelection();
        });
    });

    if (selectAllButton) {
        selectAllButton.addEventListener('click', function () {
            const shouldSelectAll = selectedCheckboxes().length !== checkboxes.length;
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = shouldSelectAll;
            });
            updateSelection();
        });
    }

    if (cancelButton) {
        cancelButton.addEventListener('click', function () {
            setSelectionMode(false);
        });
    }

    if (deleteForm) {
        deleteForm.addEventListener('submit', function (event) {
            const selected = selectedCheckboxes();

            if (!selected.length) {
                event.preventDefault();
                return;
            }

            const confirmed = window.confirm(
                'Supprimer ' + selected.length + ' message' + (selected.length > 1 ? 's' : '') + ' ?'
            );

            if (!confirmed) {
                event.preventDefault();
                return;
            }

            selectedInputs.innerHTML = '';
            selected.forEach(function (checkbox) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'messages[]';
                input.value = checkbox.value;
                selectedInputs.appendChild(input);
            });
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && selectionMode) {
            setSelectionMode(false);
        }
    });

    updateSelection();
});
</script>
@endpush
@endsection
