<div style="display:flex;align-items:flex-start;gap:10px;">
    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#10B981,#059669);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;color:white;flex-shrink:0;margin-top:2px;">
        {{ strtoupper(substr(($message->user?->name ?? 'U'), 0, 1)) }}
    </div>
    <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <span style="font-weight:600;font-size:0.85rem;color:rgba(255,255,255,0.8);">{{ $message->user?->name ?? 'Utilisateur inconnu' }}</span>
            <span style="font-size:0.7rem;color:rgba(255,255,255,0.35);">{{ $message->created_at->format('H:i') }}</span>
        </div>
        <x-chat-message :messageObj="$message" />
    </div>
</div>