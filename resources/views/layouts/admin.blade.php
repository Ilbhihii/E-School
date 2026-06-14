<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — E-School</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @stack('head')

    <link rel="stylesheet" href="{{ asset('css/admin-premium.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

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
        .toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 9999; }

        /* Sidebar overlay for mobile */
        .adm-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
        }
        .adm-sidebar-overlay.show { display: block; }

        /* Slim scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        @media (max-width: 768px) {
            .adm-sidebar { transition: transform 0.3s ease; }
            .adm-sidebar:not(.open) { transform: translateX(-100%); }
            .adm-sidebar.open { transform: translateX(0); }
        }

        /* Toast animation */
        .adm-toast {
            animation: admSlideIn 0.3s ease;
        }
        @keyframes admSlideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>

    <!-- Mobile sidebar overlay -->
    <div class="adm-sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="adm-wrapper">

        <!-- ═══ SIDEBAR ═══ -->
        <aside class="adm-sidebar" id="mainSidebar">

            <div class="adm-sidebar-brand">
                <div class="brand-icon" style="background:transparent;box-shadow:none;width:auto;height:auto;">
                    <img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="Logo" style="width:42px;height:42px;object-fit:contain;filter:brightness(0) invert(1);">
                </div>
                <h3>Edu School</h3>
                <div class="brand-sub">Administration</div>
            </div>

            <nav class="adm-sidebar-nav">
                @php
                    $route = request()->route()->getName();
                @endphp

                <div class="nav-heading">Principal</div>

                <a href="{{ route('admin.dashboard') }}"
                   class="adm-nav-link {{ preg_match('/dashboard/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-speedometer2"></i></span>
                    <span>Dashboard</span>
                </a>

                <div class="nav-heading">Gestion</div>

                <a href="{{ route('admin.users.index') }}"
                   class="adm-nav-link {{ preg_match('/users/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-people"></i></span>
                    <span>Utilisateurs</span>
                </a>

                <a href="{{ route('admin.levels.index') }}"
                   class="adm-nav-link {{ preg_match('/levels/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-layers"></i></span>
                    <span>Niveaux <span style="font-size:0.6rem;color:var(--adm-text-muted);margin-left:auto;">→ Classes → Cours</span></span>
                </a>

                <a href="{{ route('admin.courses.index') }}"
                   class="adm-nav-link {{ preg_match('/courses/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-book"></i></span>
                    <span>Tous les cours</span>
                </a>

                <a href="{{ route('admin.lives.index') }}"
                   class="adm-nav-link {{ preg_match('/lives/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-camera-video"></i></span>
                    <span>Lives</span>
                </a>

                <a href="{{ route('admin.schedule.index') }}"
                   class="adm-nav-link {{ preg_match('/schedule/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-calendar-week"></i></span>
                    <span>Planning</span>
                </a>

                <a href="{{ route('admin.absences') }}"
                   class="adm-nav-link {{ preg_match('/absence/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-calendar-x"></i></span>
                    <span>Absences</span>
                </a>

                <div class="nav-heading">Communication</div>

                <a href="{{ route('admin.chat.list') }}"
                   class="adm-nav-link {{ preg_match('/chat/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-chat-dots"></i></span>
                    <span>Chat</span>
                </a>

                <a href="{{ route('admin.assign.class') }}"
                   class="adm-nav-link {{ preg_match('/assign/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-person-plus"></i></span>
                    <span>Assigner étudiants</span>
                </a>

                <a href="{{ route('admin.users.prof-assignments') }}"
                   class="adm-nav-link {{ preg_match('/prof-assignments/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-person-badge"></i></span>
                    <span>Assigner professeurs</span>
                </a>

                <div class="nav-heading">Compte</div>

                <a href="{{ route('admin.profile') }}"
                   class="adm-nav-link {{ preg_match('/profile/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-person-circle"></i></span>
                    <span>Profil</span>
                </a>

                <a href="{{ route('admin.settings') }}"
                   class="adm-nav-link {{ preg_match('/settings/', $route ?? '') ? 'active' : '' }}">
                    <span class="nav-link-icon"><i class="bi bi-gear"></i></span>
                    <span>Paramètres</span>
                </a>
            </nav>

            <div class="adm-sidebar-footer">
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
        <div class="adm-main">

            <!-- TOPBAR -->
            <header class="adm-topbar">
                <div class="adm-topbar-left">
                    <button onclick="toggleSidebar()" class="adm-icon-btn d-md-none" style="font-size:1.2rem;">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h2 class="adm-topbar-page-title">
                            @yield('page_title', 'Dashboard')
                        </h2>
                        <div class="adm-topbar-breadcrumb">
                            <a href="{{ route('admin.dashboard') }}">Accueil</a>
                            <span>/</span>
                            <span>@yield('breadcrumb', 'Dashboard')</span>
                        </div>
                    </div>
                </div>

                <div class="adm-topbar-actions">
                    @auth
                    <div class="position-relative" style="cursor:pointer;">
                        <button class="adm-user-trigger" onclick="toggleUserMenu()" id="userMenuBtn">
                            <div class="adm-user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="adm-user-name">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down" style="font-size:0.7rem;color:rgba(255,255,255,0.3);margin-left:2px;"></i>
                        </button>
                        <div class="adm-dropdown" id="userDropdown" style="display:none;">
                            <a href="{{ route('admin.profile') }}" class="adm-dropdown-item">
                                <i class="bi bi-person-circle"></i> Mon Profil
                            </a>
                            <a href="{{ route('admin.settings') }}" class="adm-dropdown-item">
                                <i class="bi bi-gear"></i> Paramètres
                            </a>
                            <div class="adm-dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="adm-dropdown-item danger" type="submit">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- CONTENT -->
            <main class="adm-content">
                @if(session('success'))
                <div class="adm-alert adm-alert-success adm-toast">
                    <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                    <span>{{ session('success') }}</span>
                    <button class="adm-alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
                @endif
                @if(session('error'))
                <div class="adm-alert adm-alert-danger adm-toast">
                    <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
                    <span>{{ session('error') }}</span>
                    <button class="adm-alert-close" onclick="this.parentElement.remove()">&times;</button>
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
        if (menu && !e.target.closest('.adm-user-trigger') && !e.target.closest('.adm-dropdown')) {
            menu.style.display = 'none';
        }
    });

    // Auto-dismiss alerts
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.adm-alert.adm-toast').forEach(function(el) {
            setTimeout(function() { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 5000);
        });
    });
    </script>

    @stack('scripts')

</body>
</html>
