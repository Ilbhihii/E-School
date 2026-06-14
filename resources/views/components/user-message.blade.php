<div style="display:flex;flex-direction:row-reverse;align-items:flex-end;gap:8px;">
    <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);white-space:nowrap;">{{ $message->created_at->format('H:i') }}</div>
    <x-chat-message :messageObj="$message" />
</div>