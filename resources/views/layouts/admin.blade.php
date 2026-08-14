<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#080d18">

    <title>@yield('title', 'Administration') — Smart School Academy</title>

    <link rel="shortcut icon" href="{{ asset('images/logoSSA-removebg-preview.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('images/logoSSA-removebg-preview.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script>
        /*
         * L'espace Administration fonctionne uniquement
         * en mode sombre.
         */
        (function () {
            document.documentElement.classList.remove(
                'light-mode'
            );

            try {
                localStorage.removeItem(
                    'ssa-admin-theme'
                );
            } catch (error) {
                // Le mode sombre reste actif sans stockage local.
            }
        })();
    </script>

    @stack('head')

    <link rel="stylesheet" href="{{ asset('css/admin-premium.css') }}">
    <link rel="stylesheet" href="{{ asset('css/design-refresh.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-refresh.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-pages.css') }}?v={{ file_exists(public_path('css/admin-pages.css')) ? filemtime(public_path('css/admin-pages.css')) : time() }}">

    @stack('styles')

</head>

@php
    $adminRouteName = request()->route()?->getName() ?? 'admin.unknown';
    $adminRouteClass = str_replace(['.', '_'], '-', $adminRouteName);
    $authenticatedAdmin = auth()->user();
    $authenticatedAdminInitial = strtoupper(mb_substr($authenticatedAdmin->name ?? 'A', 0, 1));
    $authenticatedAdminPhoto = $authenticatedAdmin?->profile_photo
        ? asset('storage/' . ltrim($authenticatedAdmin->profile_photo, '/'))
        : null;
@endphp

<body class="admin-portal route-{{ $adminRouteClass }}">
    <div class="admin-sidebar-overlay" id="adminSidebarOverlay" aria-hidden="true"></div>

    <div class="admin-shell">
        <aside class="admin-sidebar" id="adminSidebar" aria-label="Navigation administration">
            <div class="admin-sidebar-head">
                <a href="{{ route('home') }}" class="admin-brand" title="Retour à l'accueil principal">
                    <span class="admin-brand-logo">
                        <img src="{{ asset('images/logoSSA.jpeg') }}" alt="Smart School Academy" class="logo-theme-dark">
                        <img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="Smart School Academy" class="logo-theme-light">
                    </span>
                    <span class="admin-brand-copy">
                        <strong>Smart School</strong>
                        <small>Administration</small>
                    </span>
                </a>

                <button type="button" class="admin-sidebar-close" id="adminSidebarClose" aria-label="Fermer le menu">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <nav class="admin-nav">
                <div class="admin-nav-section">
                    <div class="admin-nav-heading">Vue générale</div>

                    <a href="{{ route('admin.dashboard') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                        <span class="nav-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                        <span class="nav-label">Tableau de bord</span>
                    </a>
                </div>

                <div class="admin-nav-section">
                    <div class="admin-nav-heading">Utilisateurs</div>

                    <a href="{{ route('admin.users.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
                        <span class="nav-label">Utilisateurs</span>
                    </a>

                    <a href="{{ route('admin.professors.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.professors*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-person-video3"></i></span>
                        <span class="nav-label">Professeurs</span>
                    </a>

                    <a href="{{ route('admin.parents.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.parents*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-person-hearts"></i></span>
                        <span class="nav-label">Parents</span>
                    </a>

                    <a href="{{ route('admin.assign.class') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.assign.class') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-person-check-fill"></i></span>
                        <span class="nav-label">Assigner étudiants</span>
                    </a>

                    <a href="{{ route('admin.users.prof-assignments') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.users.prof-assignments') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-person-badge-fill"></i></span>
                        <span class="nav-label">Assigner professeurs</span>
                    </a>
                </div>

                <div class="admin-nav-section">
                    <div class="admin-nav-heading">Pédagogie</div>

                    <a href="{{ route('admin.subjects.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-journals"></i></span>
                        <span class="nav-label">Matières <small>Matière → niveau → classe</small></span>
                    </a>

                    <a href="{{ route('admin.courses.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-play-btn-fill"></i></span>
                        <span class="nav-label">Cours</span>
                    </a>

                    <a href="{{ route('admin.lives.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.lives*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-camera-video-fill"></i></span>
                        <span class="nav-label">Lives</span>
                    </a>

                    <a href="{{ route('admin.schedule.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.schedule*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-calendar3-week-fill"></i></span>
                        <span class="nav-label">Emploi du temps</span>
                    </a>

                    <a href="{{ route('admin.absences') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.absences*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-person-x-fill"></i></span>
                        <span class="nav-label">Absences</span>
                    </a>
                </div>

                <div class="admin-nav-section">
                    <div class="admin-nav-heading">Évaluations</div>

                    <a href="{{ route('admin.vocal-tests.prompts.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.vocal-tests.prompts*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-mic-fill"></i></span>
                        <span class="nav-label">Tests vocaux</span>
                    </a>

                    <a href="{{ route('admin.vocal-tests.submissions.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.vocal-tests.submissions*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-list-check"></i></span>
                        <span class="nav-label">Soumissions</span>
                    </a>
                </div>

                <div class="admin-nav-section">
                    <div class="admin-nav-heading">Communication</div>

                    <a href="{{ route('admin.appointments.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.appointments*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-calendar-check-fill"></i></span>
                        <span class="nav-label">Rendez-vous</span>
                    </a>

                    <a href="{{ route('admin.chat.list') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.chat*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-chat-square-dots-fill"></i></span>
                        <span class="nav-label">Chat</span>
                    </a>
                </div>

                <div class="admin-nav-section">
                    <div class="admin-nav-heading">Mon compte</div>

                    <a href="{{ route('admin.profile') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-person-circle"></i></span>
                        <span class="nav-label">Profil</span>
                    </a>

                    <a href="{{ route('admin.settings') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-sliders2"></i></span>
                        <span class="nav-label">Paramètres</span>
                    </a>
                </div>
            </nav>

            <div class="admin-sidebar-footer">
                <div class="admin-mini-profile">
                    <div class="admin-mini-avatar">
                        @if ($authenticatedAdminPhoto)
                            <img src="{{ $authenticatedAdminPhoto }}" alt="Photo de {{ $authenticatedAdmin->name }}">
                        @else
                            <span>{{ $authenticatedAdminInitial }}</span>
                        @endif
                    </div>
                    <div class="admin-mini-copy">
                        <strong>{{ auth()->user()->name ?? 'Administrateur' }}</strong>
                        <small>Administrateur</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="admin-logout-btn" type="submit">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button type="button" class="admin-icon-button admin-menu-button" id="adminMenuButton" aria-label="Ouvrir le menu">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="admin-page-heading">
                        <span class="admin-page-kicker">Administration</span>
                        <h1>@yield('page_title', 'Tableau de bord')</h1>
                        <nav class="admin-breadcrumb" aria-label="Fil d'Ariane">
                            <a href="{{ route('admin.dashboard') }}">Accueil</a>
                            <i class="bi bi-chevron-right"></i>
                            <span>@yield('breadcrumb', 'Tableau de bord')</span>
                        </nav>
                    </div>
                </div>

                <div class="admin-topbar-actions">
                    <a href="{{ route('home') }}"
                       class="admin-site-link"
                       title="Voir le site public"
                       aria-label="Voir le site public">
                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                    </a>

                    @auth
                        <div class="admin-user-menu-wrap">
                            <button type="button" class="admin-user-trigger" id="adminUserMenuButton" aria-expanded="false">
                                <span class="admin-user-avatar">
                                    @if ($authenticatedAdminPhoto)
                                        <img src="{{ $authenticatedAdminPhoto }}" alt="Photo de {{ $authenticatedAdmin->name }}">
                                    @else
                                        <span>{{ $authenticatedAdminInitial }}</span>
                                    @endif
                                </span>
                                <span class="admin-user-copy">
                                    <strong>{{ auth()->user()->name }}</strong>
                                    <small>Administrateur</small>
                                </span>
                                <i class="bi bi-chevron-down"></i>
                            </button>

                            <div class="admin-user-dropdown" id="adminUserDropdown" hidden>
                                <a href="{{ route('admin.profile') }}"><i class="bi bi-person-circle"></i> Mon profil</a>
                                <a href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i> Paramètres</a>
                                <div class="admin-dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <main class="admin-content" id="adminContent">
                @if(session('success'))
                    <div class="adm-alert adm-alert-success admin-flash-message" role="status">
                        <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="adm-alert-close" data-dismiss-alert aria-label="Fermer">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="adm-alert adm-alert-danger admin-flash-message" role="alert">
                        <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
                        <span>{{ session('error') }}</span>
                        <button type="button" class="adm-alert-close" data-dismiss-alert aria-label="Fermer">&times;</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin-portal.js') }}?v={{ file_exists(public_path('js/admin-portal.js')) ? filemtime(public_path('js/admin-portal.js')) : time() }}"></script>

    @stack('scripts')
</body>
</html>
