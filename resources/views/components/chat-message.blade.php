@props(['messageObj'])

<div style="border-radius:18px;padding:12px 18px;position:relative;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
    <p style="margin:0 0 6px;line-height:1.6;font-size:0.92rem;{{ $messageObj->deleted_at ? 'text-decoration:line-through;color:rgba(255,255,255,0.4);' : 'color:rgba(255,255,255,0.9);' }}">
        {{ $messageObj->message }}
    </p>
    @if($messageObj->deleted_at)
    <div style="color:#FCA5A5;font-style:italic;font-size:0.78rem;margin-top:4px;">
        ✂️ Supprimé le {{ $messageObj->deleted_at->format('d/m H:i') }}
    </div>
    @endif
    <div style="text-align:right;">
        <small style="font-size:0.7rem;color:rgba(255,255,255,0.35);">⏰ {{ $messageObj->created_at->format('H:i') }}</small>
    </div>
</div>
