<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Espace Étudiant') — Smart School Academy</title>

    <link rel="shortcut icon" href="{{ asset('images/logoSSA-removebg-preview.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('images/logoSSA-removebg-preview.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    @stack('head')

    <link rel="stylesheet" href="{{ asset('css/student-premium.css') }}">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #0A101E;
            color: rgba(255,255,255,0.85);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        a { text-decoration: none; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        /* Mobile sidebar overlay */
        .st-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
        }
        .st-sidebar-overlay.show { display: block; }

        @media (max-width: 768px) {
            .st-sidebar { transition: transform 0.3s ease; }
            .st-sidebar:not(.open) { transform: translateX(-100%); }
            .st-sidebar.open { transform: translateX(0); }
        }
        /* ── TOGGLE ICONS ── */
        .theme-toggle-btn .icon-sun { display: none; }
        .theme-toggle-btn .icon-moon { display: inline; }
        html.light-mode .theme-toggle-btn .icon-sun { display: inline; }
        html.light-mode .theme-toggle-btn .icon-moon { display: none; }
        html.light-mode .theme-toggle-btn {
            border-color: rgba(0,0,0,0.1) !important;
            background: rgba(0,0,0,0.03) !important;
            color: #64748b !important;
        }
        html.light-mode .theme-toggle-btn:hover {
            color: #003A8F !important;
        }

        /* ══════════════════════════════════════════════════════════════
           MODE CLAIR — Student Layout
           ══════════════════════════════════════════════════════════════ */
        html.light-mode body {
            background: #f0f2f5;
            color: #1e293b;
        }
        html.light-mode .st-wrapper {
            background: #f0f2f5;
        }
        html.light-mode .st-sidebar {
            background: rgba(255,255,255,0.98) !important;
            border-right: 1px solid rgba(0,0,0,0.06);
        }
        html.light-mode .st-sidebar-brand h3 {
            color: #1e293b !important;
            -webkit-text-fill-color: #1e293b !important;
        }
        html.light-mode .st-sidebar-brand .brand-sub {
            color: #94a3b8 !important;
        }
        html.light-mode .st-sidebar-profile {
            background: rgba(0,0,0,0.02);
            border-color: rgba(0,0,0,0.06);
        }
        html.light-mode .st-sp-name {
            color: #1e293b !important;
        }
        html.light-mode .st-sp-role {
            color: #64748b !important;
        }
        html.light-mode .st-nav-link {
            color: #475569 !important;
        }
        html.light-mode .st-nav-link:hover {
            color: #1e293b !important;
            background: rgba(0,0,0,0.03) !important;
        }
        html.light-mode .st-nav-link .nav-icon {
            background: rgba(0,0,0,0.04) !important;
            color: #64748b !important;
        }
        html.light-mode .st-nav-link.active {
            background: linear-gradient(135deg, rgba(0,58,143,0.08), rgba(37,99,235,0.04)) !important;
            color: #003A8F !important;
        }
        html.light-mode .st-nav-link.active .nav-icon {
            background: linear-gradient(135deg, #003A8F, #2563EB) !important;
            color: white !important;
        }
        html.light-mode .st-nav-link .nav-badge {
            background: rgba(0,58,143,0.1) !important;
            color: #003A8F !important;
        }
        html.light-mode .st-sidebar-footer {
            border-color: rgba(0,0,0,0.06);
        }
        html.light-mode .st-sidebar-nav .nav-heading {
            color: rgba(0,0,0,0.25) !important;
        }
        html.light-mode .st-topbar {
            background: rgba(255,255,255,0.85) !important;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
        html.light-mode .st-topbar-page-title {
            color: #1e293b !important;
        }
        html.light-mode .st-topbar-breadcrumb,
        html.light-mode .st-topbar-breadcrumb a {
            color: #94a3b8 !important;
        }
        html.light-mode .st-topbar-breadcrumb a:hover {
            color: #1e293b !important;
        }
        html.light-mode .st-tb-link {
            color: #64748b !important;
        }
        html.light-mode .st-tb-link:hover {
            color: #1e293b !important;
            background: rgba(0,0,0,0.04) !important;
        }
        html.light-mode .st-tb-link.active {
            color: #003A8F !important;
        }
        html.light-mode .st-user-trigger {
            background: rgba(0,0,0,0.03) !important;
        }
        html.light-mode .st-user-name {
            color: #1e293b !important;
        }
        html.light-mode .st-user-avatar {
            background: linear-gradient(135deg, #003A8F, #2563EB) !important;
            color: white !important;
        }
        html.light-mode .st-content {
            background: #f0f2f5;
        }
        html.light-mode .st-alert-success {
            background: rgba(34,197,94,0.1) !important;
            color: #15803d !important;
            border-color: rgba(34,197,94,0.15) !important;
        }
        html.light-mode .st-alert-danger {
            background: rgba(239,68,68,0.1) !important;
            color: #b91c1c !important;
            border-color: rgba(239,68,68,0.15) !important;
        }
        html.light-mode ::-webkit-scrollbar-track {
            background: #f0f2f5 !important;
        }

        @media (max-width: 768px) {
            html.light-mode .st-sidebar:not(.open) {
                background: rgba(255,255,255,0.98) !important;
            }
        }

        /* ── LOGO THEME SWITCH ── */
        .logo-theme-dark { display: inline-block; }
        .logo-theme-light { display: none; }
        html.light-mode .logo-theme-dark { display: none; }
        html.light-mode .logo-theme-light { display: inline-block; }
    </style>
    <link id="globalLightTheme" rel="stylesheet" href="{{ asset('css/light-global.css') }}" disabled>
    <script src="{{ asset('js/global-theme-sync.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/design-refresh.css') }}">
</head>
<body>

    <!-- Mobile sidebar overlay -->
    <div class="st-sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="st-wrapper">

        <!-- ═══ SIDEBAR ═══ -->
        <aside class="st-sidebar" id="mainSidebar">

            <div class="st-sidebar-brand">
                <a href="{{ route('home') }}" class="brand-icon" aria-label="Retour à l'accueil"
                   style="background:transparent;box-shadow:none;width:auto;height:auto;text-decoration:none;">
                    <img src="{{ asset('images/logoSSA.jpeg') }}" alt="Logo" class="logo-theme-dark" style="width:64px;height:64px;object-fit:contain;border-radius: 16px;animation: preloaderPulse 1.5s ease-in-out infinite;">
                    <img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="Logo" class="logo-theme-light" style="width:64px;height:64px;object-fit:contain;border-radius: 16px;animation: preloaderPulse 1.5s ease-in-out infinite;">
                </a>
                <h3>Smart School Academy</h3>
                <div class="brand-sub">Espace Étudiant</div>
            </div>

            <div class="st-sidebar-profile">
                <div class="st-sp-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="st-sp-name">{{ auth()->user()->name }}</div>
                <div class="st-sp-role"><span class="dot"></span> Étudiant</div>
            </div>

            <nav class="st-sidebar-nav">
                @php $route = request()->route()->getName(); @endphp

                <div class="nav-heading">Principal</div>

                <a href="{{ route('student.dashboard') }}"
                   class="st-nav-link {{ str_contains($route ?? '', 'dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                    <span>Tableau de bord</span>
                </a>

                <a href="{{ route('student.subjects.index') }}"
                   class="st-nav-link {{ str_contains($route ?? '', 'subject') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-book"></i></span>
                    <span>Matières</span>
                </a>

                <div class="nav-heading">Activités</div>

                <a href="{{ route('student.lives') }}"
                   class="st-nav-link {{ str_contains($route ?? '', 'lives') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-camera-video"></i></span>
                    <span>Lives</span>
                    @php $livesCount = \App\Models\Live::whereDate('live_date', '>=', now())->count(); @endphp
                    @if($livesCount > 0)
                        <span class="nav-badge">{{ $livesCount }}</span>
                    @endif
                </a>

                <a href="{{ route('student.chats') }}"
                   class="st-nav-link {{ str_contains($route ?? '', 'chat') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-chat-dots"></i></span>
                    <span>Chat</span>
                </a>

                <a href="{{ route('student.assignments') }}"
                   class="st-nav-link {{ str_contains($route ?? '', 'assignment') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-upload"></i></span>
                    <span>Mes Devoirs</span>
                </a>

                <a href="{{ route('student.absences') }}"
                   class="st-nav-link {{ str_contains($route ?? '', 'absence') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-calendar-x"></i></span>
                    <span>Absences</span>
                </a>

                <div class="nav-heading">Compte</div>

                <a href="{{ route('student.profile') }}"
                   class="st-nav-link {{ str_contains($route ?? '', 'profile') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-person-circle"></i></span>
                    <span>Profil</span>
                </a>

                <a href="{{ route('student.settings') }}"
                   class="st-nav-link {{ str_contains($route ?? '', 'settings') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-gear"></i></span>
                    <span>Paramètres</span>
                </a>
            </nav>

            <div class="st-sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="st-logout-btn" type="submit">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ═══ MAIN ═══ -->
        <div class="st-main">

            <!-- TOPBAR -->
            <header class="st-topbar">
                <div class="st-topbar-left">
                    <button onclick="toggleSidebar()" class="d-md-none" style="width:36px;height:36px;border-radius:9px;border:none;background:transparent;color:rgba(255,255,255,0.45);font-size:1.2rem;cursor:pointer;">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h2 class="st-topbar-page-title">
                            @yield('page_title', 'Tableau de bord')
                        </h2>
                        <div class="st-topbar-breadcrumb">
                            <a href="{{ route('student.dashboard') }}">Accueil</a>
                            <span>/</span>
                            <span>@yield('breadcrumb', 'Tableau de bord')</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">

                    @if(!Route::is('student.waiting'))
                    <a href="{{ route('student.subjects.index') }}" class="st-tb-link {{ str_contains($route ?? '', 'subject') ? 'active' : '' }}">
                        <i class="bi bi-book"></i> <span class="d-none d-md-inline">Matières</span>
                    </a>
                    <a href="{{ route('student.lives') }}" class="st-tb-link {{ str_contains($route ?? '', 'lives') ? 'active' : '' }}">
                        <i class="bi bi-camera-video"></i> <span class="d-none d-md-inline">Lives</span>
                    </a>
                    <a href="{{ route('student.chats') }}" class="st-tb-link {{ str_contains($route ?? '', 'chat') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots"></i> <span class="d-none d-md-inline">Chat</span>
                    </a>
                    @endif

                    <div class="position-relative">
                        <div class="st-user-trigger" onclick="toggleUserMenu()" id="userMenuBtn">
                            <div class="st-user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="st-user-name">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down" style="font-size:0.7rem;color:rgba(255,255,255,0.3);margin-left:2px;"></i>
                        </div>
                        <div class="st-dropdown" id="userDropdown" style="display:none;">
                            <a href="{{ route('student.profile') }}" class="st-dropdown-item">
                                <i class="bi bi-person-circle"></i> Mon Profil
                            </a>
                            <a href="{{ route('student.settings') }}" class="st-dropdown-item">
                                <i class="bi bi-gear"></i> Paramètres
                            </a>
                            <div class="st-dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="st-dropdown-item danger" type="submit">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <main class="st-content">
                @if(session('success'))
                <div class="st-alert st-alert-success" style="animation:stDropdownIn 0.3s ease;">
                    <i class="bi bi-check-circle-fill" style="font-size:1.1rem;flex-shrink:0;margin-top:1px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif
                @if(session('error'))
                <div class="st-alert st-alert-danger" style="animation:stDropdownIn 0.3s ease;">
                    <i class="bi bi-exclamation-circle-fill" style="font-size:1.1rem;flex-shrink:0;margin-top:1px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                @yield('content')
            </main>

        </div>

    </div>

    <script>
    function toggleSidebar() {
        document.getElementById('mainSidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }

    function toggleUserMenu() {
        const menu = document.getElementById('userDropdown');
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('userDropdown');
        const btn = document.getElementById('userMenuBtn');
        if (menu && !e.target.closest('.st-user-trigger') && !e.target.closest('.st-dropdown')) {
            menu.style.display = 'none';
        }
    });
    </script>

    @stack('scripts')

</body>
</html>
