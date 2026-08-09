<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#090d18">

    <title>@yield('title', 'Espace Professeur') — Smart School Academy</title>

    <link rel="shortcut icon" href="{{ asset('images/logoSSA-removebg-preview.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('images/logoSSA-removebg-preview.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    {{-- Les pages professeur utilisent beaucoup les classes Bootstrap (row, col-md-*, d-flex, gap-*...). --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script>
        /*
         * L'espace Professeur fonctionne uniquement
         * en mode sombre.
         */
        (function () {
            document.documentElement.classList.remove(
                'light-mode'
            );

            try {
                localStorage.removeItem(
                    'ssa-prof-theme'
                );
            } catch (error) {
                // Le mode sombre reste actif sans stockage local.
            }
        })();
    </script>

    @stack('head')

    <link rel="stylesheet" href="{{ asset('css/layouts-3d.css') }}">
    <link rel="stylesheet" href="{{ asset('css/content-3d.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-premium.css') }}">
    <link rel="stylesheet" href="{{ asset('css/design-refresh.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prof-refresh.css') }}?v={{ file_exists(public_path('css/prof-refresh.css')) ? filemtime(public_path('css/prof-refresh.css')) : time() }}">
    <link rel="stylesheet" href="{{ asset('css/prof-pages.css') }}?v={{ file_exists(public_path('css/prof-pages.css')) ? filemtime(public_path('css/prof-pages.css')) : time() }}">

    <link rel="stylesheet" href="{{ asset('css/prof-path-structure-v1.css') }}?v={{ file_exists(public_path('css/prof-path-structure-v1.css')) ? filemtime(public_path('css/prof-path-structure-v1.css')) : time() }}">
    @stack('styles')

</head>

@php
    $profRouteName = request()->route()?->getName() ?? 'prof.unknown';
    $profRouteClass = str_replace(['.', '_'], '-', $profRouteName);
@endphp

<body class="prof-portal route-{{ $profRouteClass }}">
    <div class="prof-sidebar-overlay" id="profSidebarOverlay" aria-hidden="true"></div>

    <div class="prof-shell">
        <aside class="prof-sidebar" id="profSidebar" aria-label="Navigation professeur">
            <div class="prof-sidebar-head">
                <a href="{{ route('home') }}" class="prof-brand" title="Retour à l'accueil principal">
                    <span class="prof-brand-logo">
                        <img src="{{ asset('images/logoSSA.jpeg') }}" alt="Smart School Academy" class="logo-theme-dark">
                        <img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="Smart School Academy" class="logo-theme-light">
                    </span>
                    <span class="prof-brand-copy">
                        <strong>Smart School</strong>
                        <small>Espace enseignant</small>
                    </span>
                </a>

                <button
                    type="button"
                    class="prof-sidebar-close"
                    id="profSidebarClose"
                    aria-label="Fermer le menu"
                >
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <nav class="prof-nav">
                <div class="prof-nav-section">
                    <div class="prof-nav-heading">Vue générale</div>

                    <a
                        href="{{ route('prof.dashboard') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.dashboard') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.dashboard')) aria-current="page" @endif
                    >
                        <span class="nav-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                        <span class="nav-label">Tableau de bord</span>
                    </a>
                </div>

                <div class="prof-nav-section">
                    <div class="prof-nav-heading">Pédagogie</div>

                    <a
                        href="{{ route('prof.subjects.list') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.subjects*') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.subjects*')) aria-current="page" @endif
                    >
                        <span class="nav-icon"><i class="bi bi-journals"></i></span>
                        <span class="nav-label">
                            Matières
                            <small>Matière → niveau → classe</small>
                        </span>
                    </a>

                    <a
                        href="{{ route('prof.courses.index') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.courses.*') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.courses.*')) aria-current="page" @endif
                    >
                        <span class="nav-icon">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                        </span>
                        <span class="nav-label">
                            Mes cours
                            <small>Proposer → validation admin</small>
                        </span>
                    </a>

                    <a
                        href="{{ route('prof.lives.index') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.lives*') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.lives*')) aria-current="page" @endif
                    >
                        <span class="nav-icon"><i class="bi bi-camera-video-fill"></i></span>
                        <span class="nav-label">Lives</span>
                    </a>

                    <a
                        href="{{ route('prof.devoir.index') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.devoir*') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.devoir*')) aria-current="page" @endif
                    >
                        <span class="nav-icon"><i class="bi bi-file-earmark-plus-fill"></i></span>
                        <span class="nav-label">Créer des devoirs</span>
                    </a>

                    <a
                        href="{{ route('prof.assignments') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.assignments') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.assignments')) aria-current="page" @endif
                    >
                        <span class="nav-icon"><i class="bi bi-journal-check"></i></span>
                        <span class="nav-label">Copies des étudiants</span>
                    </a>

                    <a
                        href="{{ route('prof.schedule') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.schedule*') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.schedule*')) aria-current="page" @endif
                    >
                        <span class="nav-icon"><i class="bi bi-calendar3-week-fill"></i></span>
                        <span class="nav-label">Emploi du temps</span>
                    </a>
                </div>

                <div class="prof-nav-section">
                    <div class="prof-nav-heading">Évaluations</div>

                    <a
                        href="{{ route('prof.vocal-tests.index') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.vocal-tests*') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.vocal-tests*')) aria-current="page" @endif
                    >
                        <span class="nav-icon">
                            <i class="bi bi-mic-fill"></i>
                        </span>

                        <span class="nav-label">
                            Tests reçus
                            <small>Affectés par l’administration</small>
                        </span>
                    </a>
                </div>

                <div class="prof-nav-section">
                    <div class="prof-nav-heading">Suivi & échanges</div>

                    <a
                        href="{{ route('prof.chat.subjects') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.chat*') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.chat*')) aria-current="page" @endif
                    >
                        <span class="nav-icon"><i class="bi bi-chat-square-dots-fill"></i></span>
                        <span class="nav-label">Questions étudiants</span>
                    </a>

                    <a
                        href="{{ route('prof.absences') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.absences*') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.absences*')) aria-current="page" @endif
                    >
                        <span class="nav-icon"><i class="bi bi-person-x-fill"></i></span>
                        <span class="nav-label">Présences & absences</span>
                    </a>
                </div>

                <div class="prof-nav-section">
                    <div class="prof-nav-heading">Mon compte</div>

                    <a
                        href="{{ route('prof.profile') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.profile') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.profile')) aria-current="page" @endif
                    >
                        <span class="nav-icon"><i class="bi bi-person-badge-fill"></i></span>
                        <span class="nav-label">Mon profil</span>
                    </a>

                    <a
                        href="{{ route('prof.settings') }}"
                        class="prof-nav-link {{ request()->routeIs('prof.settings*') ? 'active' : '' }}"
                        @if(request()->routeIs('prof.settings*')) aria-current="page" @endif
                    >
                        <span class="nav-icon"><i class="bi bi-sliders2"></i></span>
                        <span class="nav-label">Paramètres</span>
                    </a>
                </div>
            </nav>

            <div class="prof-sidebar-footer">
                <div class="prof-mini-profile">
                    <div class="prof-mini-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                    </div>
                    <div class="prof-mini-copy">
                        <strong>{{ auth()->user()->name ?? 'Professeur' }}</strong>
                        <small>Enseignant</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="prof-logout-btn" type="submit">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="prof-main">
            <header class="prof-topbar">
                <div class="prof-topbar-left">
                    <button
                        type="button"
                        class="prof-icon-button prof-menu-button"
                        id="profMenuButton"
                        aria-controls="profSidebar"
                        aria-expanded="false"
                        aria-label="Ouvrir le menu"
                    >
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="prof-page-identification">
                        <h1 class="prof-topbar-title">@yield('page_title', 'Espace Professeur')</h1>
                        <nav class="prof-topbar-breadcrumb" aria-label="Fil d'Ariane">
                            <a href="{{ route('prof.dashboard') }}">Accueil</a>
                            <i class="bi bi-chevron-right"></i>
                            <span>@yield('breadcrumb', 'Tableau de bord')</span>
                        </nav>
                    </div>
                </div>

                <div class="prof-topbar-actions">
                    <a href="{{ route('home') }}" class="prof-icon-button" title="Accueil principal" aria-label="Accueil principal">
                        <i class="bi bi-house-door"></i>
                    </a>

                    <div class="prof-user-area">
                        <button
                            type="button"
                            class="prof-user-trigger"
                            id="profUserBtn"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <span class="prof-user-avatar">
                                {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                            </span>
                            <span class="prof-user-copy">
                                <strong>{{ auth()->user()->name ?? 'Professeur' }}</strong>
                                <small>Mon espace</small>
                            </span>
                            <i class="bi bi-chevron-down prof-user-chevron"></i>
                        </button>

                        <div class="prof-user-menu" id="profUserMenu" role="menu" hidden>
                            <div class="prof-user-menu-head">
                                <span class="prof-user-avatar large">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                                </span>
                                <span>
                                    <strong>{{ auth()->user()->name ?? 'Professeur' }}</strong>
                                    <small>{{ auth()->user()->email ?? 'Compte professeur' }}</small>
                                </span>
                            </div>

                            <a href="{{ route('prof.profile') }}" class="prof-dropdown-item" role="menuitem">
                                <i class="bi bi-person-circle"></i>
                                <span>Mon profil</span>
                            </a>

                            <a href="{{ route('prof.settings') }}" class="prof-dropdown-item" role="menuitem">
                                <i class="bi bi-gear"></i>
                                <span>Paramètres</span>
                            </a>

                            <div class="prof-dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="prof-dropdown-item danger" type="submit" role="menuitem">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="prof-content">
                <div class="prof-content-inner">
                    @if(session('success'))
                        <div class="adm-alert adm-alert-success prof-toast" role="status">
                            <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                            <span>{{ session('success') }}</span>
                            <button class="prof-alert-close" type="button" aria-label="Fermer">&times;</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="adm-alert adm-alert-danger prof-toast" role="alert">
                            <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
                            <span>{{ session('error') }}</span>
                            <button class="prof-alert-close" type="button" aria-label="Fermer">&times;</button>
                        </div>
                    @endif

                    @if(session('alert'))
                        <div class="adm-alert adm-alert-warning prof-toast" role="alert">
                            <span class="adm-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                            <span>{{ session('alert') }}</span>
                            <button class="prof-alert-close" type="button" aria-label="Fermer">&times;</button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="{{ asset('js/prof-portal.js') }}?v={{ file_exists(public_path('js/prof-portal.js')) ? filemtime(public_path('js/prof-portal.js')) : time() }}"></script>
    @stack('scripts')
</body>
</html>
