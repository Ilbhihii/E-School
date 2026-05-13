<div class="d-flex align-items-start gap-3">
    <div class="message-avatar avatar-student mt-1 flex-shrink-0">
        {{ strtoupper(substr(($message->user?->name ?? 'U'), 0, 1)) }}
    </div>
    <div class="flex-1">
        <div class="message-meta">
            <span class="message-author">{{ $message->user?->name ?? 'Utilisateur inconnu' }}</span>
            <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
        </div>
<x-chat-message :messageObj="$message" />
    </div>
</div>
