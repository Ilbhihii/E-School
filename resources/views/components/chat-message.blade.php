@props(['messageObj', 'timestampClass' => 'opacity-75'])

<div class="admin-message-bubble p-4 rounded-2xl shadow-sm position-relative">
<p class="message-text mb-2 {{ $messageObj->deleted_at ? 'deleted-message text-muted opacity-75' : '' }}">
    {{ $messageObj->message }}
</p>
@if($messageObj->deleted_at)
    <div class="text-danger fst-italic small mt-1">
        ✂️ Supprimé le {{ $messageObj->deleted_at->format('d/m H:i') }}
    </div>
@endif
    <div class="text-end">
        <small class="message-time {{ $timestampClass }} fs-6">⏰ {{ $messageObj->created_at->format('H:i') }}</small>
    </div>
</div>

<style>
.deleted-message {
    text-decoration: line-through;
}
</style>

