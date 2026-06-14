<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Espace Professeur') — E-School</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @stack('head')

    <!-- 3D Design System + Premium CSS -->
    <link rel="stylesheet" href="{{ asset('css/layouts-3d.css') }}">
    <link rel="stylesheet" href="{{ asset('css/content-3d.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-premium.css') }}">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #0A101E;
            color: rgba(255,255,255,0.85);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        /* Prof sidebar - use adm-sidebar pattern but with prof colors */
        .prof-sidebar {
            width: var(--adm-sidebar-w, 260px);
            min-height: 100vh;
            background: linear-gradient(180deg, rgba(10, 16, 30, 0.98), rgba(15, 23, 42, 0.98));
            border-right: 1px solid rgba(255,255,255,0.04);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-shrink: 0;
        }
        .prof-sidebar-brand {
            padding: 1.5rem 1.25rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .prof-sidebar-brand .brand-icon {
            width: 46px; height: 46px; border-radius: 14px;
            background: linear-gradient(135deg, #7C3AED, #A78BFA);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 0.75rem; font-size: 1.35rem; color: white;
            box-shadow: 0 8px 24px rgba(124,58,237,0.35);
            transition: transform 0.3s ease;
        }
        .prof-sidebar-brand:hover .brand-icon { transform: scale(1.08) rotate(-6deg); }
        .prof-sidebar-brand h3 {
            font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.1rem;
            background: linear-gradient(135deg, #7C3AED, #A78BFA);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            margin: 0;
        }
        .prof-sidebar-brand .brand-sub {
            color: rgba(255,255,255,0.35);
            font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.12em;
        }

        /* Navigation */
        .prof-nav { flex: 1; padding: 1rem 0.65rem; overflow-y: auto; }
        .prof-nav .nav-heading {
            font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.14em;
            color: rgba(255,255,255,0.15); padding: 1.2rem 1rem 0.4rem; font-weight: 700;
        }
        .prof-nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 9px 13px; border-radius: 11px;
            color: rgba(255,255,255,0.5); text-decoration: none;
            transition: all 0.25s ease; margin-bottom: 1px; font-weight: 500; font-size: 0.875rem;
        }
        .prof-nav-link .nav-icon {
            width: 32px; height: 32px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.95rem; flex-shrink: 0;
            background: rgba(255,255,255,0.03); transition: all 0.25s ease;
        }
        .prof-nav-link:hover { color: white; background: rgba(255,255,255,0.04); transform: translateX(3px); }
        .prof-nav-link:hover .nav-icon { background: rgba(255,255,255,0.07); }
        .prof-nav-link.active {
            color: white;
            background: linear-gradient(135deg, rgba(124,58,237,0.3), rgba(167,139,250,0.12));
            border: 1px solid rgba(124,58,237,0.1);
        }
        .prof-nav-link.active .nav-icon {
            background: linear-gradient(135deg, #7C3AED, #A78BFA); color: white;
            box-shadow: 0 3px 10px rgba(124,58,237,0.3);
        }
        .prof-sidebar-footer {
            padding: 0.85rem 0.65rem;
            border-top: 1px solid rgba(255,255,255,0.04);
        }

        /* Prof topbar */
        .prof-topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.65rem 2rem;
            background: rgba(15,23,42,0.55);
            backdrop-filter: blur(20px) saturate(1.5);
            border-bottom: 1px solid rgba(255,255,255,0.04);
            position: sticky; top: 0; z-index: 99; gap: 1rem;
        }
        .prof-topbar-title {
            font-family: 'Poppins', sans-serif; font-weight: 600;
            font-size: 1rem; color: rgba(255,255,255,0.9); margin: 0;
        }
        .prof-topbar-breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: 0.75rem; color: rgba(255,255,255,0.35);
        }
        .prof-topbar-breadcrumb a { color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.2s; }
        .prof-topbar-breadcrumb a:hover { color: rgba(255,255,255,0.7); }

        /* Prof main content area */
        .prof-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .prof-content { padding: 1.5rem 2rem; flex: 1; overflow-y: auto; }

        /* Responsive */
        @media (max-width: 1024px) {
            .prof-sidebar { width: 64px; }
            .prof-sidebar-brand h3, .prof-sidebar-brand .brand-sub,
            .prof-nav-link span, .prof-nav .nav-heading,
            .prof-sidebar-footer span { display: none; }
            .prof-nav-link { justify-content: center; padding: 10px; }
            .prof-nav-link .nav-icon { width: 36px; height: 36px; }
        }
        @media (max-width: 768px) {
            .prof-sidebar { width: 0; overflow: hidden; position: fixed; left: 0; top: 0; z-index: 1000; transition: width 0.3s; }
            .prof-sidebar.open { width: var(--adm-sidebar-w, 260px); }
            .prof-content { padding: 1rem; }
            .prof-topbar { padding: 0.65rem 1rem; }
        }
    </style>
</head>
<body>

<div style="display:flex; min-height:100vh;">
    <!-- ═══ SIDEBAR ═══ -->
    <aside class="prof-sidebar" id="profSidebar">
        <div class="prof-sidebar-brand">
            <div class="brand-icon" style="background:transparent;box-shadow:none;width:auto;height:auto;">
                <img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="Logo" style="width:42px;height:42px;object-fit:contain;filter:brightness(0) invert(1);">
            </div>
            <h3>E-School</h3>
            <div class="brand-sub">Espace enseignant</div>
        </div>

        <nav class="prof-nav">
            <div class="nav-heading">Principal</div>
            <a href="{{ route('prof.dashboard') }}" class="prof-nav-link {{ request()->routeIs('prof.dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                <span>Dashboard</span>
            </a>

            <div class="nav-heading">Gestion</div>
            <a href="{{ route('prof.courses.index') }}" class="prof-nav-link {{ request()->routeIs('prof.courses*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-book"></i></span>
                <span>Cours</span>
            </a>
            <a href="{{ route('prof.lives.index') }}" class="prof-nav-link {{ request()->routeIs('prof.lives*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-camera-video"></i></span>
                <span>Lives</span>
            </a>
            <a href="{{ route('prof.devoir.index') }}" class="prof-nav-link {{ request()->routeIs('prof.devoir*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-file-earmark-check"></i></span>
                <span>Pose Devoirs</span>
            </a>
            <a href="{{ route('prof.assignments') }}" class="prof-nav-link {{ request()->routeIs('prof.assignments') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span>
                <span>Devoirs Étudiants</span>
            </a>
            <a href="{{ route('prof.tests.index') }}" class="prof-nav-link {{ request()->routeIs('prof.tests*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-clipboard-check"></i></span>
                <span>Tests</span>
            </a>

            <div class="nav-heading">Communication</div>
            <a href="{{ route('prof.chat.subjects') }}" class="prof-nav-link {{ request()->routeIs('prof.chat*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-chat-dots"></i></span>
                <span>Questions Étudiants</span>
            </a>
            <a href="{{ route('prof.absences') }}" class="prof-nav-link {{ request()->routeIs('prof.absences*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-calendar-x"></i></span>
                <span>Absences</span>
            </a>
            <a href="{{ route('prof.schedule') }}" class="prof-nav-link {{ request()->routeIs('prof.schedule') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-calendar-week"></i></span>
                <span>Planning</span>
            </a>

            <div class="nav-heading">Compte</div>
            <a href="{{ route('prof.profile') }}" class="prof-nav-link {{ request()->routeIs('prof.profile') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-person-circle"></i></span>
                <span>Mon Profil</span>
            </a>
            <a href="{{ route('prof.settings') }}" class="prof-nav-link {{ request()->routeIs('prof.settings') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-gear"></i></span>
                <span>Paramètres</span>
            </a>
        </nav>

        <div class="prof-sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="adm-logout-btn" type="submit">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ═══ MAIN ═══ -->
    <div class="prof-main">

        <!-- TOPBAR -->
        <header class="prof-topbar">
            <div>
                <h2 class="prof-topbar-title">@yield('page_title', 'Espace Professeur')</h2>
                <div class="prof-topbar-breadcrumb">
                    <a href="{{ route('prof.dashboard') }}">Accueil</a>
                    <span>/</span>
                    <span>@yield('breadcrumb', 'Dashboard')</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 position-relative">
                <div style="display:flex;align-items:center;gap:10px;padding:5px 10px 5px 5px;border-radius:11px;cursor:pointer;transition:all 0.25s;border:1px solid transparent;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='transparent'" onclick="toggleProfMenu()" id="profUserBtn">
                    <div class="adm-user-avatar" style="background:linear-gradient(135deg,#7C3AED,#A78BFA);">
                        {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                    </div>
                    <span class="d-none d-md-inline" style="font-size:0.85rem;font-weight:600;color:rgba(255,255,255,0.85);">{{ auth()->user()->name ?? 'Professeur' }}</span>
                    <i class="bi bi-chevron-down" style="font-size:0.7rem;color:rgba(255,255,255,0.3);"></i>
                </div>
                <div class="adm-dropdown" id="profUserMenu" style="display:none;">
                    <a href="{{ route('prof.profile') }}" class="adm-dropdown-item"><i class="bi bi-person-circle"></i> Mon Profil</a>
                    <a href="{{ route('prof.settings') }}" class="adm-dropdown-item"><i class="bi bi-gear"></i> Paramètres</a>
                    <div class="adm-dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="adm-dropdown-item danger" type="submit"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="prof-content">
            @if(session('success'))
            <div class="adm-alert adm-alert-success mb-4">
                <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                <span>{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="adm-alert adm-alert-danger mb-4">
                <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>

<script>
function toggleProfMenu() {
    const menu = document.getElementById('profUserMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    const menu = document.getElementById('profUserMenu');
    const btn = document.getElementById('profUserBtn');
    if (menu && !e.target.closest('#profUserBtn') && !e.target.closest('.adm-dropdown')) {
        menu.style.display = 'none';
    }
});
</script>

@stack('scripts')

</body>
</html>
