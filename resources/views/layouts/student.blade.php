<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Espace Étudiant') — Smart School Academy</title>

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
    </style>
</head>
<body>

    <!-- Mobile sidebar overlay -->
    <div class="st-sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="st-wrapper">

        <!-- ═══ SIDEBAR ═══ -->
        <aside class="st-sidebar" id="mainSidebar">

            <div class="st-sidebar-brand">
                <div class="brand-icon" style="background:transparent;box-shadow:none;width:auto;height:auto;">
                    <img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="Logo" style="width:54px;height:54px;object-fit:contain;filter:brightness(0) invert(1);">
                </div>
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
                    <span>Dashboard</span>
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
                            @yield('page_title', 'Dashboard')
                        </h2>
                        <div class="st-topbar-breadcrumb">
                            <a href="{{ route('student.dashboard') }}">Accueil</a>
                            <span>/</span>
                            <span>@yield('breadcrumb', 'Dashboard')</span>
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
