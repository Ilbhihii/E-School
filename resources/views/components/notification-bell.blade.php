@auth
@php
    $notificationUser = auth()->user();
    $notificationUnreadCount = $notificationUser->unreadNotifications()->count();
    $notificationPreview = $notificationUser->notifications()->latest()->limit(6)->get();
@endphp

@once
<style>
    .ssa-notify { position: relative; z-index: 1250; font-family: Inter, system-ui, sans-serif; }
    .ssa-notify * { box-sizing: border-box; }
    .ssa-notify-trigger {
        position: relative; width: 44px; height: 44px; display: grid; place-items: center;
        padding: 0; border: 1px solid rgba(148,163,184,.16); border-radius: 13px;
        color: #cbd5e1; background: rgba(255,255,255,.045); cursor: pointer;
        transition: transform .18s ease, border-color .18s ease, background .18s ease;
    }
    .ssa-notify-trigger:hover { transform: translateY(-1px); color:#fff; border-color:rgba(96,165,250,.36); background:rgba(255,255,255,.075); }
    .ssa-notify-trigger > i { font-size: 1.05rem; }
    .ssa-notify-badge {
        position:absolute; top:-5px; right:-5px; min-width:20px; height:20px; display:grid; place-items:center;
        padding:0 5px; border:2px solid #0f172a; border-radius:999px; color:#fff; background:#ef4444;
        font-size:.61rem; font-weight:800; line-height:1;
    }
    .ssa-notify-badge[hidden] { display:none !important; }
    .ssa-notify-panel {
        position:absolute; top:calc(100% + 12px); right:0; width:min(390px, calc(100vw - 28px));
        overflow:hidden; border:1px solid rgba(148,163,184,.16); border-radius:18px;
        color:#e2e8f0; background:rgba(9,17,31,.985); box-shadow:0 28px 75px rgba(0,0,0,.46);
        backdrop-filter:blur(22px);
    }
    .ssa-notify-panel[hidden] { display:none !important; }
    .ssa-notify-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:15px 16px 12px; border-bottom:1px solid rgba(148,163,184,.10); }
    .ssa-notify-head strong { display:block; color:#f8fafc; font-size:.84rem; }
    .ssa-notify-head small { display:block; margin-top:2px; color:#64748b; font-size:.65rem; }
    .ssa-notify-read-all { margin:0; }
    .ssa-notify-read-all button { border:0; color:#93c5fd; background:transparent; font-size:.66rem; font-weight:750; cursor:pointer; }
    .ssa-notify-list { max-height:390px; overflow:auto; padding:7px; }
    .ssa-notify-item { display:flex; align-items:flex-start; gap:10px; padding:11px 10px; border-radius:12px; color:inherit; text-decoration:none; transition:background .16s ease; }
    .ssa-notify-item:hover { background:rgba(255,255,255,.055); }
    .ssa-notify-item.is-unread { background:rgba(59,130,246,.065); }
    .ssa-notify-icon { width:34px; height:34px; flex:0 0 34px; display:grid; place-items:center; border-radius:10px; color:#93c5fd; background:rgba(59,130,246,.11); }
    .ssa-notify-content { min-width:0; flex:1; }
    .ssa-notify-title { display:block; overflow:hidden; color:#e5edf8; font-size:.71rem; font-weight:800; text-overflow:ellipsis; white-space:nowrap; }
    .ssa-notify-message { display:-webkit-box; overflow:hidden; margin-top:3px; color:#8fa0b8; font-size:.64rem; line-height:1.45; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
    .ssa-notify-time { display:block; margin-top:5px; color:#56657a; font-size:.57rem; }
    .ssa-notify-dot { width:7px; height:7px; flex:0 0 7px; margin-top:7px; border-radius:50%; background:#60a5fa; box-shadow:0 0 0 4px rgba(96,165,250,.08); }
    .ssa-notify-item:not(.is-unread) .ssa-notify-dot { opacity:0; }
    .ssa-notify-empty { padding:28px 15px; color:#64748b; text-align:center; font-size:.67rem; }
    .ssa-notify-empty i { display:block; margin-bottom:7px; font-size:1.35rem; }
    .ssa-notify-foot { padding:9px; border-top:1px solid rgba(148,163,184,.10); }
    .ssa-notify-all { display:flex; align-items:center; justify-content:center; gap:7px; min-height:38px; border-radius:10px; color:#bfdbfe; text-decoration:none; background:rgba(59,130,246,.07); font-size:.67rem; font-weight:800; }
    .ssa-notify-all:hover { color:#fff; background:rgba(59,130,246,.12); }
    html.light-mode .ssa-notify-trigger { color:#475569; border-color:rgba(15,23,42,.10); background:#fff; }
    html.light-mode .ssa-notify-panel { color:#334155; border-color:rgba(15,23,42,.10); background:rgba(255,255,255,.99); box-shadow:0 25px 60px rgba(15,23,42,.16); }
    html.light-mode .ssa-notify-head strong, html.light-mode .ssa-notify-title { color:#0f172a; }
    html.light-mode .ssa-notify-item:hover { background:#f8fafc; }
    html.light-mode .ssa-notify-item.is-unread { background:#eff6ff; }
    html.light-mode .ssa-notify-badge { border-color:#fff; }
    @media (max-width: 575.98px) { .ssa-notify-panel { position:fixed; top:72px; right:14px; left:14px; width:auto; } }
</style>
@endonce

<div
    class="ssa-notify"
    data-ssa-notify
    data-feed-url="{{ route('notifications.feed') }}"
>
    <button
        type="button"
        class="ssa-notify-trigger"
        data-ssa-notify-trigger
        aria-label="Notifications"
        aria-haspopup="true"
        aria-expanded="false"
    >
        <i class="bi bi-bell"></i>
        <span class="ssa-notify-badge" data-ssa-notify-badge @if($notificationUnreadCount < 1) hidden @endif>
            {{ $notificationUnreadCount > 99 ? '99+' : $notificationUnreadCount }}
        </span>
    </button>

    <div class="ssa-notify-panel" data-ssa-notify-panel hidden>
        <div class="ssa-notify-head">
            <span>
                <strong>Notifications</strong>
                <small data-ssa-notify-summary>
                    {{ $notificationUnreadCount }} non lue(s)
                </small>
            </span>

            @if($notificationUnreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}" class="ssa-notify-read-all">
                    @csrf
                    @method('PATCH')
                    <button type="submit">Tout lire</button>
                </form>
            @endif
        </div>

        <div class="ssa-notify-list" data-ssa-notify-list>
            @forelse($notificationPreview as $notification)
                @php
                    $nData = $notification->data ?? [];
                    $nIcon = preg_match('/^bi bi-[a-z0-9-]+$/i', (string)($nData['icon'] ?? ''))
                        ? $nData['icon']
                        : 'bi bi-bell-fill';
                @endphp
                <a
                    href="{{ route('notifications.open', $notification->id) }}"
                    class="ssa-notify-item {{ $notification->read_at ? '' : 'is-unread' }}"
                >
                    <span class="ssa-notify-icon"><i class="{{ $nIcon }}"></i></span>
                    <span class="ssa-notify-content">
                        <span class="ssa-notify-title">{{ $nData['title'] ?? 'Notification' }}</span>
                        <span class="ssa-notify-message">{{ $nData['message'] ?? '' }}</span>
                        <span class="ssa-notify-time">{{ optional($notification->created_at)->diffForHumans() }}</span>
                    </span>
                    <span class="ssa-notify-dot" aria-hidden="true"></span>
                </a>
            @empty
                <div class="ssa-notify-empty">
                    <i class="bi bi-bell-slash"></i>
                    Aucune notification pour le moment.
                </div>
            @endforelse
        </div>

        <div class="ssa-notify-foot">
            <a href="{{ route('notifications.index') }}" class="ssa-notify-all">
                Voir toutes les notifications
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

@once
<script>
(function () {
    'use strict';

    function text(tag, className, value) {
        const el = document.createElement(tag);
        if (className) el.className = className;
        el.textContent = value || '';
        return el;
    }

    function render(center, payload) {
        const badge = center.querySelector('[data-ssa-notify-badge]');
        const summary = center.querySelector('[data-ssa-notify-summary]');
        const list = center.querySelector('[data-ssa-notify-list]');
        const unread = Number(payload.unread_count || 0);

        if (badge) {
            badge.textContent = unread > 99 ? '99+' : String(unread);
            badge.hidden = unread < 1;
        }
        if (summary) summary.textContent = unread + ' non lue(s)';
        if (!list) return;

        list.replaceChildren();
        const items = Array.isArray(payload.notifications) ? payload.notifications : [];

        if (!items.length) {
            const empty = text('div', 'ssa-notify-empty', 'Aucune notification pour le moment.');
            const icon = document.createElement('i');
            icon.className = 'bi bi-bell-slash';
            empty.prepend(icon);
            list.appendChild(empty);
            return;
        }

        items.forEach(item => {
            const a = document.createElement('a');
            a.href = item.open_url;
            a.className = 'ssa-notify-item' + (item.is_read ? '' : ' is-unread');

            const iconBox = document.createElement('span');
            iconBox.className = 'ssa-notify-icon';
            const icon = document.createElement('i');
            icon.className = /^bi bi-[a-z0-9-]+$/i.test(item.icon || '')
                ? item.icon
                : 'bi bi-bell-fill';
            iconBox.appendChild(icon);

            const content = document.createElement('span');
            content.className = 'ssa-notify-content';
            content.appendChild(text('span', 'ssa-notify-title', item.title));
            content.appendChild(text('span', 'ssa-notify-message', item.message));
            content.appendChild(text('span', 'ssa-notify-time', item.time));

            const dot = document.createElement('span');
            dot.className = 'ssa-notify-dot';
            dot.setAttribute('aria-hidden', 'true');

            a.append(iconBox, content, dot);
            list.appendChild(a);
        });
    }

    function init(center) {
        if (center.dataset.ssaReady === '1') return;
        center.dataset.ssaReady = '1';

        const trigger = center.querySelector('[data-ssa-notify-trigger]');
        const panel = center.querySelector('[data-ssa-notify-panel]');
        const feedUrl = center.dataset.feedUrl;

        if (!trigger || !panel) return;

        trigger.addEventListener('click', function (event) {
            event.stopPropagation();
            const willOpen = panel.hidden;

            document.querySelectorAll('[data-ssa-notify-panel]').forEach(other => {
                if (other !== panel) other.hidden = true;
            });

            panel.hidden = !willOpen;
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });

        panel.addEventListener('click', event => event.stopPropagation());

        async function refresh() {
            if (!feedUrl || document.hidden) return;
            try {
                const response = await fetch(feedUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (!response.ok) return;
                render(center, await response.json());
            } catch (error) {
                // Le centre reste utilisable même si le polling échoue.
            }
        }

        setInterval(refresh, 30000);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) refresh();
        });
    }

    document.addEventListener('click', function () {
        document.querySelectorAll('[data-ssa-notify-panel]').forEach(panel => panel.hidden = true);
        document.querySelectorAll('[data-ssa-notify-trigger]').forEach(button => button.setAttribute('aria-expanded', 'false'));
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-ssa-notify]').forEach(init);
    });
})();
</script>
@endonce
@endauth
