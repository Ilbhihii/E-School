<div class="d-flex flex-row-reverse align-items-end gap-2">
    <div class="message-time text-gray-500 text-sm">{{ $message->created_at->format('H:i') }}</div>
<x-chat-message :messageObj="$message" />
</div>
