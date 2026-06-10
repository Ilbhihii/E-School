<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Étudiant — E-School')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/student.css') }}">
    <style>
        :root {
            --sidebar-w: 280px;
            --topbar-h: 68px;
            --bg: #f0f4f9;
            --sidebar-bg: #0b1120;
            --sidebar-surface: #111b2e;
            --sidebar-hover: rgba(255,255,255,.06);
            --sidebar-active: rgba(59,130,246,.15);
            --sidebar-text: rgba(255,255,255,.55);
            --sidebar-text-active: #e2e8f0;
            --sidebar-accent: #3b82f6;
            --topbar-bg: rgba(255,255,255,.75);
            --shadow-elevation: 0 4px 24px rgba(0,0,0,.06), 0 1px 3px rgba(0,0,0,.04);
            --shadow-dropdown: 0 24px 80px rgba(0,0,0,.14), 0 1px 4px rgba(0,0,0,.06);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: var(--sidebar-bg);
            background-image: radial-gradient(ellipse at 50% 0%, rgba(59,130,246,.06) 0%, transparent 60%);
            display: flex; flex-direction: column;
            z-index: 100;
            transition: transform .35s cubic-bezier(.22,.61,.36,1);
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,.05);
        }
        .sidebar::-webkit-scrollbar { width: 3px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); }

        /* Brand */
        .sidebar-brand {
            display: flex; align-items: center; gap: 12px;
            padding: 1.25rem 1.25rem .9rem;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .sidebar-brand .brand-icon {
            width: 40px; height: 40px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
        }
        .sidebar-brand .brand-icon img {
            width: 100%; height: 100%; object-fit: contain;
        }
        .sidebar-brand .brand-text {
            font-size: 1.05rem; font-weight: 700;
            letter-spacing: -.01em;
            background: linear-gradient(135deg, #f8fafc 50%, rgba(248,250,252,.6));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .sidebar-brand .brand-badge {
            font-size: 9px; font-weight: 700; letter-spacing: .05em;
            text-transform: uppercase;
            background: rgba(6,182,212,.15); color: #22d3ee;
            padding: 1px 7px; border-radius: 4px;
            margin-left: auto;
        }

        /* Profile */
        .sidebar-profile-wrap {
            margin: .85rem .75rem .15rem;
            position: relative;
        }
        .sidebar-profile {
            background: var(--sidebar-surface);
            border-radius: var(--radius-md);
            padding: .8rem 1rem;
            display: flex; align-items: center; gap: 10px;
            transition: all .2s ease;
            border: 1px solid rgba(255,255,255,.06);
            cursor: pointer;
            text-decoration: none;
        }
        .sidebar-profile:hover {
            background: rgba(255,255,255,.08);
            border-color: rgba(255,255,255,.1);
        }
        .sidebar-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            color: white; font-weight: 700; font-size: 15px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(37,99,235,.3);
        }
        .sidebar-profile-wrap .online-dot {
            position: absolute; bottom: 0; right: 0;
            width: 10px; height: 10px;
            background: #22c55e;
            border: 2px solid var(--sidebar-surface);
            border-radius: 50%;
            box-shadow: 0 0 6px rgba(34,197,94,.5);
        }
        .sidebar-profile-name {
            font-size: 13px; font-weight: 600; color: #f1f5f9;
            line-height: 1.2;
        }
        .sidebar-profile-role {
            font-size: 11px; color: rgba(255,255,255,.35);
        }

        /* Sections */
        .sidebar-label {
            padding: .8rem 1.25rem .3rem;
            font-size: 10px; font-weight: 700; letter-spacing: .08em;
            color: rgba(255,255,255,.2); text-transform: uppercase;
            display: flex; align-items: center; gap: 8px;
        }
        .sidebar-label::after {
            content: '';
            flex: 1; height: 1px;
            background: rgba(255,255,255,.05);
        }

        /* Nav */
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: .6rem 1rem; border-radius: 10px;
            color: var(--sidebar-text);
            font-size: 13.5px; font-weight: 500;
            text-decoration: none;
            transition: all .2s ease;
            margin: 1.5px .75rem;
            position: relative;
        }
        .nav-item i {
            font-size: 17px; width: 20px; flex-shrink: 0;
            color: rgba(255,255,255,.3);
            transition: all .2s ease;
        }
        .nav-item:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text-active);
        }
        .nav-item:hover i {
            color: rgba(255,255,255,.6);
            transform: translateX(2px);
        }
        .nav-item.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text-active);
            font-weight: 600;
        }
        .nav-item.active i {
            color: var(--sidebar-accent);
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 22px;
            background: var(--sidebar-accent);
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 12px rgba(59,130,246,.5);
        }
        .nav-item .nav-count {
            margin-left: auto;
            font-size: 11px; font-weight: 600;
            background: rgba(255,255,255,.08);
            color: rgba(255,255,255,.4);
            padding: 1px 8px;
            border-radius: 999px;
            min-width: 20px; text-align: center;
        }
        .nav-item.active .nav-count {
            background: rgba(59,130,246,.25);
            color: #93c5fd;
        }

        /* Footer */
        .sidebar-footer {
            padding: .85rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.05);
            margin-top: auto;
        }
        .btn-logout {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: .6rem;
            background: rgba(239,68,68,.08);
            color: rgba(248,113,113,.7);
            border: 1px solid rgba(239,68,68,.1);
            border-radius: 10px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; font-family: inherit;
            transition: all .2s ease;
        }
        .btn-logout:hover {
            background: rgba(239,68,68,.15);
            color: #fca5a5;
            border-color: rgba(239,68,68,.2);
            transform: translateY(-1px);
        }
        .btn-logout:active { transform: translateY(0); }

        /* ── TOPBAR ── */
        .topbar {
            position: fixed; top: 0;
            left: var(--sidebar-w); right: 0; height: var(--topbar-h);
            background: var(--topbar-bg);
            backdrop-filter: blur(20px) saturate(1.4);
            -webkit-backdrop-filter: blur(20px) saturate(1.4);
            border-bottom: 1px solid rgba(226,232,240,.8);
            display: flex; align-items: center;
            padding: 0 1.75rem;
            z-index: 99; gap: .75rem;
        }
        .topbar-title-area {
            display: flex; align-items: center; gap: 10px; flex: 1;
        }
        .topbar-title-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            flex-shrink: 0;
            box-shadow: 0 0 12px rgba(37,99,235,.35);
            animation: dotPulse 3s ease-in-out infinite;
        }
        @keyframes dotPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .6; transform: scale(.85); }
        }
        .topbar-title-text {
            font-size: 16px; font-weight: 700;
            color: #0f172a;
            letter-spacing: -.01em;
        }
        .topbar-title-text small {
            font-weight: 400; color: #64748b;
            font-size: 13px;
        }

        .topbar-actions {
            display: flex; align-items: center; gap: 2px;
        }
        .topbar-btn {
            width: 36px; height: 36px; border-radius: 10px;
            border: none; background: transparent;
            color: #64748b; font-size: 18px;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all .2s ease;
            position: relative;
            text-decoration: none;
        }
        .topbar-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .topbar-btn .badge-dot {
            position: absolute; top: 6px; right: 6px;
            width: 7px; height: 7px;
            background: #ef4444;
            border-radius: 50%;
            box-shadow: 0 0 6px rgba(239,68,68,.5);
        }

        .topbar-divider {
            width: 1px; height: 28px;
            background: #e2e8f0;
            margin: 0 6px;
        }

        .topbar-link {
            font-size: 13px; font-weight: 500;
            color: #64748b; text-decoration: none;
            padding: .4rem .85rem; border-radius: 8px;
            transition: all .2s ease;
            position: relative;
        }
        .topbar-link:hover {
            background: #f1f5f9;
            color: #0f172a;
        }.topbar-link.active { color: #2563eb; font-weight: 600; }
        .topbar-link.active::after { content: ''; position: absolute; bottom: -2px; left: 50%; transform: translateX(-50%); width: 18px; height: 2.5px; background: #2563eb; border-radius: 3px; box-shadow: 0 0 8px rgba(37,99,235,.4); }

        .topbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            color: white; font-weight: 700; font-size: 13px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .25s ease;
            box-shadow: 0 2px 8px rgba(37,99,235,.25);
            border: 2px solid transparent;
            margin-left: 4px;
        }
        .topbar-avatar:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 16px rgba(37,99,235,.35);
            border-color: rgba(255,255,255,.8);
        }

        /* Dropdown */
        .dropdown-wrap { position: relative; }
        .dropdown-menu-custom {
            position: absolute; right: 0; top: calc(100% + 10px);
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: var(--shadow-dropdown);
            min-width: 240px; padding: .5rem;
            z-index: 200; display: none;
            transform-origin: top right;
        }
        .dropdown-menu-custom.show {
            display: block;
            animation: dropIn .2s cubic-bezier(.22,.61,.36,1);
        }
        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-8px) scale(.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .dropdown-header {
            padding: .55rem .85rem .7rem;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: .25rem;
        }
        .dropdown-header strong {
            font-size: 14px; font-weight: 700; color: #0f172a;
            display: block;
        }
        .dropdown-header small {
            font-size: 12px; color: #64748b;
        }
        .dd-item {
            display: flex; align-items: center; gap: 10px;
            padding: .6rem .85rem; border-radius: 10px;
            color: #0f172a; font-size: 13px; font-weight: 500;
            text-decoration: none; cursor: pointer;
            transition: all .15s ease;
        }
        .dd-item i {
            font-size: 15px; width: 20px;
            color: #64748b; text-align: center;
            transition: color .15s;
        }
        .dd-item:hover {
            background: #f8fafc;
        }
        .dd-item:hover i { color: #2563eb; }
        .dd-item.danger { color: #dc2626; }
        .dd-item.danger i { color: #dc2626; }
        .dd-item.danger:hover { background: #fef2f2; }
        .dd-divider {
            border: none; border-top: 1px solid #f1f5f9;
            margin: .25rem .5rem;
        }

        /* Hamburger */
        .hamburger {
            display: none; align-items: center; justify-content: center;
            background: none; border: none;
            color: #64748b; font-size: 22px;
            cursor: pointer; width: 36px; height: 36px;
            border-radius: 10px; transition: all .2s ease;
        }
        .hamburger:hover { background: #f1f5f9; color: #0f172a; }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 99;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: opacity .3s ease;
        }

        /* Main */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 1.5rem 1.75rem;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* Alerts */
        .st-alert {
            animation: alertIn .4s cubic-bezier(.22,.61,.36,1);
            box-shadow: 0 4px 16px rgba(0,0,0,.06);
        }
        @keyframes alertIn {
            from { opacity: 0; transform: translateY(-12px) scale(.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── RESPONSIVE ── */
        @media(max-width: 768px) {
            .sidebar { transform: translateX(-105%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .topbar { left: 0; padding: 0 1rem; }
            .main-content { margin-left: 0; padding: 1rem; }
            .hamburger { display: flex; }
            .topbar-link { display: none; }
        }
        @media(max-width: 480px) {
            .main-content { padding: .75rem; }
            .topbar-title-text { font-size: 14px; }
        }
    </style>
    @stack('styles')
</head>
<body>

@php $route = request()->route()->getName(); @endphp

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="Logo"></div>
        <span class="brand-text">E-School</span>
        <span class="brand-badge">Étudiant</span>
    </div>

    <div class="sidebar-profile-wrap">
        <a href="{{ route('student.profile') }}" class="sidebar-profile">
            <div style="position: relative;">
                <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                <span class="online-dot"></span>
            </div>
            <div>
                <div class="sidebar-profile-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-profile-role">Étudiant</div>
            </div>
        </a>
    </div>

    <nav style="padding: .15rem 0; flex: 1;">

        @if(!Route::is('student.waiting'))

        <div class="sidebar-label">Navigation</div>

        <a href="{{ route('student.dashboard') }}" class="nav-item {{ str_contains($route, 'dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>

        <a href="{{ route('student.classes') }}" class="nav-item {{ str_contains($route, 'classes') || str_contains($route, 'subjects') || str_contains($route, 'courses') || str_contains($route, 'course.show') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Mes Cours
        </a>

        <a href="{{ route('student.lives') }}" class="nav-item {{ str_contains($route, 'lives') ? 'active' : '' }}">
            <i class="bi bi-camera-video-fill"></i> Lives
        </a>

        <a href="{{ route('student.chats') }}" class="nav-item {{ str_contains($route, 'chats') ? 'active' : '' }}">
            <i class="bi bi-chat-dots-fill"></i> Chat Matières
        </a>

        <div class="sidebar-label">Suivi</div>

        <a href="{{ route('student.assignments') }}" class="nav-item {{ str_contains($route, 'assignments') ? 'active' : '' }}">
            <i class="bi bi-upload"></i> Mes Devoirs
        </a>

        <a href="{{ route('student.absences') }}" class="nav-item {{ str_contains($route, 'absences') ? 'active' : '' }}">
            <i class="bi bi-calendar-x-fill"></i> Mes Absences
        </a>

        <div class="sidebar-label">Compte</div>

        <a href="{{ route('student.profile') }}" class="nav-item {{ str_contains($route, 'profile') ? 'active' : '' }}">
            <i class="bi bi-person-fill"></i> Mon Profil
        </a>

        <a href="{{ route('student.settings') }}" class="nav-item {{ str_contains($route, 'settings') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> Paramètres
        </a>

        @endif

    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn-logout" type="submit"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
        </form>
    </div>
</aside>

<!-- TOPBAR -->
<header class="topbar">
    <button class="hamburger" onclick="openSidebar()" aria-label="Menu"><i class="bi bi-list"></i></button>

    <div class="topbar-title-area">
        <span class="topbar-title-dot"></span>
        <div class="topbar-title-text">
            @yield('page-title', 'Espace Étudiant')
        </div>
    </div>

    <div class="topbar-actions">
        @if(!Route::is('student.waiting'))
        <a href="{{ route('student.dashboard') }}" class="topbar-link {{ str_contains($route, 'dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('student.classes') }}" class="topbar-link">Cours</a>
        <a href="{{ route('student.lives') }}" class="topbar-link">Lives</a>
        <div class="topbar-divider"></div>
        @endif

        {{-- Notification bell --}}
        <a href="javascript:void(0)" class="topbar-btn" title="Notifications" role="button">
            <i class="bi bi-bell"></i>
            <span class="badge-dot"></span>
        </a>

        {{-- Avatar --}}
        <div class="dropdown-wrap">
            <div class="topbar-avatar" onclick="toggleDropdown()" title="Mon compte">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="dropdown-menu-custom" id="userDropdown">
                <div class="dropdown-header">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->email }}</small>
                </div>

                <a href="{{ route('student.profile') }}" class="dd-item">
                    <i class="bi bi-person"></i> Mon profil
                </a>
                <a href="{{ route('student.settings') }}" class="dd-item">
                    <i class="bi bi-gear"></i> Paramètres
                </a>
                <hr class="dd-divider">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dd-item danger" style="width: 100%; border: none; background: none; text-align: left; font-family: inherit;">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<main class="main-content">
    @if(session('success'))
        <div class="st-alert st-alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="st-alert st-alert-warning"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="st-alert st-alert-danger"><i class="bi bi-x-circle-fill"></i> {{ session('error') }}</div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleDropdown() {
    document.getElementById('userDropdown').classList.toggle('show');
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.topbar-avatar') && !e.target.closest('#userDropdown')) {
        document.getElementById('userDropdown').classList.remove('show');
    }
});
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
    document.body.style.overflow = '';
}
</script>
@stack('scripts')
</body>
</html>