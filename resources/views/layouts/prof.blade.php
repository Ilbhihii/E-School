<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Prof — E-School')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary:   #7c3aed;
            --primary-d: #6d28d9;
            --blue:      #2563eb;
            --sidebar-w: 260px;
            --topbar-h:  64px;
            --bg:        #f5f3ff;
            --surface:   #ffffff;
            --border:    #e5e7eb;
            --text:      #111827;
            --muted:     #6b7280;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: var(--text); }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
            display: flex; flex-direction: column;
            z-index: 100; transition: transform .3s;
            overflow-y: auto;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
            padding: 1.4rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand img { width: 36px; height: 36px; border-radius: 8px; }
        .sidebar-brand span { font-size: 1.05rem; font-weight: 700; color: white; }

        .sidebar-section {
            padding: .5rem 1rem .25rem;
            font-size: 10px; font-weight: 700; letter-spacing: .08em;
            color: rgba(255,255,255,.3); text-transform: uppercase; margin-top: .5rem;
        }

        .nav-link-s {
            display: flex; align-items: center; gap: 10px;
            padding: .65rem 1rem; border-radius: 10px;
            color: rgba(255,255,255,.7); font-size: 14px; font-weight: 500;
            text-decoration: none; transition: all .2s; margin: 2px .75rem;
        }
        .nav-link-s i { font-size: 16px; width: 20px; flex-shrink: 0; }
        .nav-link-s:hover { background: rgba(255,255,255,.1); color: white; }
        .nav-link-s.active {
            background: rgba(167,139,250,.2); color: #c4b5fd;
            font-weight: 600; box-shadow: inset 3px 0 0 #a78bfa;
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.08);
            margin-top: auto;
        }
        .sidebar-user { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .sidebar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg,#7c3aed,#a78bfa);
            color: white; font-weight: 700; font-size: 14px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .sidebar-user-name { font-size: 13px; font-weight: 600; color: white; }
        .sidebar-user-role { font-size: 11px; color: rgba(255,255,255,.45); }
        .btn-logout {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: .6rem;
            background: rgba(239,68,68,.12); color: #fca5a5;
            border: 1px solid rgba(239,68,68,.18); border-radius: 10px;
            font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,.22); color: #f87171; }

        /* ── TOPBAR ── */
        .topbar {
            position: fixed; top: 0;
            left: var(--sidebar-w); right: 0; height: var(--topbar-h);
            background: var(--surface); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; padding: 0 1.5rem;
            z-index: 99; gap: 1rem;
        }
        .topbar-title { font-size: 15px; font-weight: 700; color: var(--text); flex: 1; }
        .topbar-actions { display: flex; align-items: center; gap: .75rem; }
        .topbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg,#7c3aed,#a78bfa);
            color: white; font-weight: 700; font-size: 13px;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
        }
        .dropdown-menu-custom {
            position: absolute; right: 0; top: calc(100% + 8px);
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; box-shadow: 0 20px 40px rgba(15,23,42,.12);
            min-width: 200px; padding: .5rem; z-index: 200; display: none;
        }
        .dropdown-menu-custom.show { display: block; animation: fadeDown .15s ease; }
        @keyframes fadeDown { from{opacity:0;transform:translateY(-6px);}to{opacity:1;transform:translateY(0);} }
        .dd-item {
            display: flex; align-items: center; gap: 8px;
            padding: .6rem .85rem; border-radius: 10px;
            color: var(--text); font-size: 13px; font-weight: 500;
            text-decoration: none; cursor: pointer; transition: background .15s;
        }
        .dd-item:hover { background: var(--bg); color: var(--text); }
        .dd-item.danger { color: #dc2626; }
        .dd-item.danger:hover { background: #fef2f2; }
        .dd-divider { border: none; border-top: 1px solid var(--border); margin: .3rem .5rem; }

        /* ── MAIN ── */
        .main-content {
            margin-left: var(--sidebar-w); margin-top: var(--topbar-h);
            padding: 1.75rem; min-height: calc(100vh - var(--topbar-h));
        }

        .hamburger { display:none; background:none; border:none; color:var(--muted); font-size:20px; cursor:pointer; }
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:99; }
        @media(max-width:768px){
            .sidebar{transform:translateX(-100%);}
            .sidebar.open{transform:translateX(0);}
            .sidebar-overlay.show{display:block;}
            .topbar{left:0;} .main-content{margin-left:0;} .hamburger{display:flex;}
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
        <img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="Logo">
        <span>E-School Prof</span>
    </div>

    <nav style="padding:.75rem 0; flex:1;">

        <div class="sidebar-section">Principal</div>

        <a href="{{ route('prof.dashboard') }}" class="nav-link-s {{ str_contains($route,'dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>

        <div class="sidebar-section">Pédagogie</div>

        <a href="{{ route('prof.courses.index') }}" class="nav-link-s {{ str_contains($route,'courses') ? 'active' : '' }}">
            <i class="bi bi-book-fill"></i> Mes Cours
        </a>

        <a href="{{ route('prof.lives.index') }}" class="nav-link-s {{ str_contains($route,'lives') ? 'active' : '' }}">
            <i class="bi bi-camera-video-fill"></i> Lives
        </a>

        <a href="{{ route('prof.devoir.index') }}" class="nav-link-s {{ str_contains($route,'devoir') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-check-fill"></i> Devoirs posés
        </a>

        <a href="{{ route('prof.tests.index') }}" class="nav-link-s {{ str_contains($route,'tests') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check-fill"></i> Tests
        </a>

        <div class="sidebar-section">Suivi</div>

        <a href="{{ route('prof.assignments') }}" class="nav-link-s {{ str_contains($route,'assignments') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text-fill"></i> Devoirs étudiants
        </a>

        <a href="{{ route('prof.absences') }}" class="nav-link-s {{ str_contains($route,'absences') ? 'active' : '' }}">
            <i class="bi bi-calendar-x-fill"></i> Absences
        </a>

        <a href="{{ route('prof.schedule') }}" class="nav-link-s {{ str_contains($route,'schedule') ? 'active' : '' }}">
            <i class="bi bi-calendar-week-fill"></i> Planning
        </a>

        <a href="{{ route('prof.chat.subjects') }}" class="nav-link-s {{ str_contains($route,'chat') ? 'active' : '' }}">
            <i class="bi bi-chat-dots-fill"></i> Questions
        </a>

        <div class="sidebar-section">Compte</div>

        <a href="{{ route('prof.profile') }}" class="nav-link-s {{ str_contains($route,'profile') ? 'active' : '' }}">
            <i class="bi bi-person-fill"></i> Profil
        </a>

        <a href="{{ route('prof.settings') }}" class="nav-link-s {{ str_contains($route,'settings') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> Paramètres
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
            <div>
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">Professeur</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn-logout"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
        </form>
    </div>
</aside>

<!-- TOPBAR -->
<header class="topbar">
    <button class="hamburger" onclick="openSidebar()"><i class="bi bi-list"></i></button>
    <div class="topbar-title">@yield('page-title', 'Espace Professeur')</div>
    <div class="topbar-actions">
        <div style="position:relative;">
            <div class="topbar-avatar" onclick="toggleDropdown()">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>
            <div class="dropdown-menu-custom" id="userDropdown">
                <div style="padding:.5rem .85rem .75rem;border-bottom:1px solid var(--border);margin-bottom:.3rem;">
                    <div style="font-size:13px;font-weight:700;">{{ auth()->user()->name }}</div>
                    <div style="font-size:11px;color:var(--muted);">{{ auth()->user()->email }}</div>
                </div>
                <a href="{{ route('prof.profile') }}" class="dd-item"><i class="bi bi-person"></i> Mon profil</a>
                <a href="{{ route('prof.settings') }}" class="dd-item"><i class="bi bi-gear"></i> Paramètres</a>
                <hr class="dd-divider">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dd-item danger" style="width:100%;border:none;background:none;text-align:left;">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<main class="main-content">
    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:.9rem 1.2rem;color:#166534;font-size:14px;font-weight:600;margin-bottom:1.2rem;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:12px;padding:.9rem 1.2rem;color:#dc2626;font-size:14px;font-weight:600;margin-bottom:1.2rem;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
        </div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleDropdown(){document.getElementById('userDropdown').classList.toggle('show');}
document.addEventListener('click',function(e){if(!e.target.closest('.topbar-avatar')&&!e.target.closest('#userDropdown'))document.getElementById('userDropdown').classList.remove('show');});
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}
</script>
@stack('scripts')
</body>
</html>