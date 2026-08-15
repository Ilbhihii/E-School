@extends($notificationLayout)

@section('title', 'Notifications')
@section('page_title', 'Notifications')
@section('breadcrumb', 'Centre de notifications')

@section('content')
@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<style>
    .notification-page { --np-border:rgba(148,163,184,.14); --np-muted:#8190a5; max-width:1100px; margin:0 auto; }
    .notification-page * { box-sizing:border-box; }
    .notification-hero { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:22px 24px; margin-bottom:16px; border:1px solid var(--np-border); border-radius:20px; background:linear-gradient(135deg,rgba(59,130,246,.10),rgba(15,23,42,.70)); }
    .notification-hero h2 { margin:0; color:#f8fafc; font-size:1.12rem; font-weight:850; }
    .notification-hero p { margin:5px 0 0; color:var(--np-muted); font-size:.72rem; }
    .notification-count { min-width:48px; height:48px; display:grid; place-items:center; padding:0 10px; border-radius:14px; color:#dbeafe; background:rgba(59,130,246,.12); font-size:.86rem; font-weight:900; }
    .notification-toolbar { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:10px; margin-bottom:14px; }
    .notification-tabs { display:flex; gap:7px; }
    .notification-tab { min-height:36px; display:inline-flex; align-items:center; gap:6px; padding:0 11px; border:1px solid var(--np-border); border-radius:10px; color:#94a3b8; text-decoration:none; background:rgba(255,255,255,.025); font-size:.66rem; font-weight:800; }
    .notification-tab.active, .notification-tab:hover { color:#dbeafe; border-color:rgba(96,165,250,.28); background:rgba(59,130,246,.09); }
    .notification-actions { display:flex; flex-wrap:wrap; gap:7px; }
    .notification-actions form { margin:0; }
    .notification-btn { min-height:36px; display:inline-flex; align-items:center; gap:6px; padding:0 11px; border:1px solid var(--np-border); border-radius:10px; color:#cbd5e1; background:rgba(255,255,255,.03); font-size:.64rem; font-weight:800; cursor:pointer; }
    .notification-btn:hover { color:#fff; border-color:rgba(96,165,250,.28); background:rgba(59,130,246,.08); }
    .notification-btn.danger { color:#fca5a5; }
    .notification-list { display:grid; gap:9px; }
    .notification-card { display:flex; align-items:flex-start; gap:12px; padding:15px; border:1px solid var(--np-border); border-radius:16px; background:rgba(15,23,42,.64); transition:.18s ease; }
    .notification-card.unread { border-color:rgba(96,165,250,.22); background:linear-gradient(90deg,rgba(59,130,246,.08),rgba(15,23,42,.68)); }
    .notification-card:hover { transform:translateY(-1px); border-color:rgba(96,165,250,.26); }
    .notification-card-icon { width:40px; height:40px; flex:0 0 40px; display:grid; place-items:center; border-radius:12px; color:#93c5fd; background:rgba(59,130,246,.11); }
    .notification-card-body { min-width:0; flex:1; }
    .notification-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; }
    .notification-card-title { margin:0; color:#e8eef7; font-size:.76rem; font-weight:850; }
    .notification-card-time { flex:0 0 auto; color:#64748b; font-size:.58rem; }
    .notification-card-message { margin:5px 0 0; color:#93a1b5; font-size:.68rem; line-height:1.55; }
    .notification-card-footer { display:flex; flex-wrap:wrap; align-items:center; gap:7px; margin-top:10px; }
    .notification-open { display:inline-flex; align-items:center; gap:6px; min-height:31px; padding:0 9px; border-radius:9px; color:#bfdbfe; text-decoration:none; background:rgba(59,130,246,.08); font-size:.61rem; font-weight:800; }
    .notification-mini-form { margin:0; }
    .notification-mini-btn { min-height:31px; padding:0 9px; border:1px solid rgba(148,163,184,.11); border-radius:9px; color:#8493a8; background:transparent; font-size:.59rem; cursor:pointer; }
    .notification-mini-btn.delete { color:#fca5a5; }
    .notification-empty { padding:55px 20px; border:1px dashed var(--np-border); border-radius:18px; color:#64748b; text-align:center; background:rgba(15,23,42,.38); }
    .notification-empty i { display:block; margin-bottom:9px; font-size:1.7rem; }
    .notification-pagination { margin-top:15px; }
    html.light-mode .notification-hero, html.light-mode .notification-card { background:#fff; border-color:rgba(15,23,42,.10); }
    html.light-mode .notification-hero h2, html.light-mode .notification-card-title { color:#0f172a; }
    html.light-mode .notification-card.unread { background:#eff6ff; }
    @media(max-width:650px){ .notification-hero{padding:18px}.notification-card-head{display:block}.notification-card-time{display:block;margin-top:3px}.notification-toolbar{align-items:stretch}.notification-actions{width:100%} }
</style>

<div class="notification-page">
    <section class="notification-hero">
        <div>
            <h2><i class="bi bi-bell-fill"></i> Centre de notifications</h2>
            <p>Planning, cours, devoirs, absences, résultats, rendez-vous et informations importantes.</p>
        </div>
        <span class="notification-count" title="Notifications non lues">{{ $unreadCount }}</span>
    </section>

    <div class="notification-toolbar">
        <div class="notification-tabs">
            <a href="{{ route('notifications.index') }}" class="notification-tab {{ $filter === 'all' ? 'active' : '' }}">
                <i class="bi bi-inbox"></i> Toutes
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="notification-tab {{ $filter === 'unread' ? 'active' : '' }}">
                <i class="bi bi-circle-fill"></i> Non lues ({{ $unreadCount }})
            </a>
        </div>

        <div class="notification-actions">
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="notification-btn">
                        <i class="bi bi-check2-all"></i> Tout marquer comme lu
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('notifications.clear-read') }}" onsubmit="return confirm('Supprimer toutes les notifications déjà lues ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="notification-btn danger">
                    <i class="bi bi-trash3"></i> Effacer les lues
                </button>
            </form>
        </div>
    </div>

    <section class="notification-list">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data ?? [];
                $icon = preg_match('/^bi bi-[a-z0-9-]+$/i', (string)($data['icon'] ?? ''))
                    ? $data['icon']
                    : 'bi bi-bell-fill';
            @endphp

            <article class="notification-card {{ $notification->read_at ? '' : 'unread' }}">
                <span class="notification-card-icon"><i class="{{ $icon }}"></i></span>

                <div class="notification-card-body">
                    <div class="notification-card-head">
                        <h3 class="notification-card-title">{{ $data['title'] ?? 'Notification' }}</h3>
                        <time class="notification-card-time">{{ optional($notification->created_at)->diffForHumans() }}</time>
                    </div>

                    <p class="notification-card-message">{{ $data['message'] ?? '' }}</p>

                    <div class="notification-card-footer">
                        <a href="{{ route('notifications.open', $notification->id) }}" class="notification-open">
                            <i class="bi bi-arrow-up-right-circle"></i> Ouvrir
                        </a>

                        @if(!$notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="notification-mini-form">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="notification-mini-btn">
                                    <i class="bi bi-check2"></i> Marquer comme lue
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="notification-mini-form" onsubmit="return confirm('Supprimer cette notification ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="notification-mini-btn delete" title="Supprimer">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="notification-empty">
                <i class="bi bi-bell-slash"></i>
                <strong>Aucune notification</strong><br>
                <small>Les nouvelles informations apparaîtront ici.</small>
            </div>
        @endforelse
    </section>

    @if($notifications->hasPages())
        <div class="notification-pagination">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
