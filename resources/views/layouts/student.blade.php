<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">

    <title>@yield('title', 'Espace Étudiant') — Smart School Academy</title>

    <link rel="shortcut icon" href="{{ asset('images/logoSSA-removebg-preview.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('images/logoSSA-removebg-preview.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    @stack('head')

    {{-- Styles existants du projet --}}
    <link rel="stylesheet" href="{{ asset('css/student-premium.css') }}">
<link rel="stylesheet" href="{{ asset('css/design-refresh.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student-refresh.css') }}">

    {{-- Nouveau design harmonisé avec les espaces Admin et Professeur --}}
    <link rel="stylesheet" href="{{ asset('css/student-admin-prof-v4.css') }}">

    @stack('styles')
</head>

@php
    $route = request()->route()?->getName() ?? '';
    $student = auth()->user();

    $studentPathService = app(
        \App\Services\LearningPathService::class
    );

    $studentAssignmentRows = $studentPathService
        ->studentAssignmentRows(auth()->id());

    $studentAssignedClassIds = $studentAssignmentRows
        ->pluck('class_id')
        ->push($student->class_id)
        ->filter()
        ->map(fn ($classId) => (int) $classId)
        ->unique()
        ->values();

    $studentUpcomingLivesCount = $studentAssignedClassIds->isNotEmpty()
        ? \App\Models\Live::query()
            ->whereIn('class_id', $studentAssignedClassIds)
            ->whereDate('live_date', '>=', now()->toDateString())
            ->count()
        : 0;

    $showStudentBacTests = $studentPathService
        ->studentHasSoutienLyceeBacPath(auth()->id());

    $studentInitial = strtoupper(
        mb_substr(trim($student->name ?? 'E'), 0, 1)
    );

    $studentPhoto = $student->profile_photo ?? null;

    $studentPhotoUrl = $studentPhoto
        ? asset('storage/' . ltrim($studentPhoto, '/'))
        : null;
@endphp

<body class="student-portal {{ Route::is('student.dashboard') ? 'student-dashboard-page' : '' }}">

    <button
        type="button"
        class="student-sidebar-overlay"
        id="studentSidebarOverlay"
        aria-label="Fermer le menu"
        onclick="StudentSpace.closeSidebar()"
    ></button>

    <div class="student-shell">

        {{-- SIDEBAR --}}
        <aside class="student-sidebar" id="studentSidebar">

            <div class="student-sidebar-brand">
                <a href="{{ route('home') }}" class="student-brand-link">
                    <span class="student-brand-logo">
                        <img
                            src="{{ asset('images/logoSSA-removebg-preview.png') }}"
                            alt="Smart School Academy"
                        >
                    </span>

                    <span class="student-brand-text">
                        <strong>SmartSchool</strong>
                        <small>Espace étudiant</small>
                    </span>
                </a>

                <button
                    type="button"
                    class="student-sidebar-close d-xl-none"
                    onclick="StudentSpace.closeSidebar()"
                    aria-label="Fermer le menu"
                >
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <nav class="student-sidebar-nav">

                <div class="student-nav-section">
                    <div class="student-nav-heading">Vue générale</div>

                    <a
                        href="{{ route('student.dashboard') }}"
                        class="student-nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
                    >
                        <span class="student-nav-icon">
                            <i class="bi bi-grid-1x2-fill"></i>
                        </span>
                        <span>Tableau de bord</span>
                    </a>
                </div>

                <div class="student-nav-section">
                    <div class="student-nav-heading">Apprentissage</div>

                    <a
                        href="{{ route('student.subjects.index') }}"
                        class="student-nav-link {{ request()->routeIs('student.subjects.*') ? 'active' : '' }}"
                    >
                        <span class="student-nav-icon">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </span>
                        <span>Matières</span>
                    </a>

                    <a
                        href="{{ route('student.schedule.index') }}"
                        class="student-nav-link {{ request()->routeIs('student.schedule.*') ? 'active' : '' }}"
                    >
                        <span class="student-nav-icon">
                            <i class="bi bi-calendar-week-fill"></i>
                        </span>
                        <span>Emploi du temps</span>
                    </a>

                    <a
                        href="{{ route('student.lives') }}"
                        class="student-nav-link {{ request()->routeIs('student.lives*') ? 'active' : '' }}"
                    >
                        <span class="student-nav-icon">
                            <i class="bi bi-camera-video-fill"></i>
                        </span>
                        <span>Lives</span>

                        @if($studentUpcomingLivesCount > 0)
                            <span class="student-nav-count">
                                {{ $studentUpcomingLivesCount }}
                            </span>
                        @endif
                    </a>

                    @if($showStudentBacTests)
                        <a
                            href="{{ route('student.written-tests.index') }}"
                            class="student-nav-link {{ request()->routeIs('student.written-tests.*') ? 'active' : '' }}"
                        >
                            <span class="student-nav-icon">
                                <i class="bi bi-file-earmark-check-fill"></i>
                            </span>
                            <span>Mes tests BAC</span>
                        </a>
                    @endif
                </div>

                <div class="student-nav-section">
                    <div class="student-nav-heading">Suivi</div>

                    <a
                        href="{{ route('student.assignments') }}"
                        class="student-nav-link {{
                            request()->routeIs('student.assignment*')
                            || request()->routeIs('student.assignments*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <span class="student-nav-icon">
                            <i class="bi bi-file-earmark-arrow-up-fill"></i>
                        </span>
                        <span>Mes devoirs</span>
                    </a>

                    <a
                        href="{{ route('student.chats') }}"
                        class="student-nav-link {{
                            request()->routeIs('student.chat*')
                            || request()->routeIs('student.chats*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <span class="student-nav-icon">
                            <i class="bi bi-chat-square-text-fill"></i>
                        </span>
                        <span>Discussions</span>
                    </a>

                    <a
                        href="{{ route('student.absences') }}"
                        class="student-nav-link {{
                            request()->routeIs('student.absence*')
                            || request()->routeIs('student.absences*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <span class="student-nav-icon">
                            <i class="bi bi-calendar2-x-fill"></i>
                        </span>
                        <span>Absences</span>
                    </a>
                </div>

                <div class="student-nav-section">
                    <div class="student-nav-heading">Compte</div>

                    <a
                        href="{{ route('student.profile') }}"
                        class="student-nav-link {{ request()->routeIs('student.profile*') ? 'active' : '' }}"
                    >
                        <span class="student-nav-icon">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <span>Profil</span>
                    </a>

                    <a
                        href="{{ route('student.settings') }}"
                        class="student-nav-link {{ request()->routeIs('student.settings*') ? 'active' : '' }}"
                    >
                        <span class="student-nav-icon">
                            <i class="bi bi-gear-fill"></i>
                        </span>
                        <span>Paramètres</span>
                    </a>
                </div>
            </nav>

            {{-- Profil en bas, comme Admin et Professeur --}}
            <div class="student-sidebar-footer">

                <a
                    href="{{ route('student.profile') }}"
                    class="student-sidebar-user"
                >
                    <span class="student-avatar student-avatar-medium">
                        @if($studentPhotoUrl)
                            <img
                                src="{{ $studentPhotoUrl }}"
                                alt="Photo de {{ $student->name }}"
                                onerror="this.hidden=true; this.nextElementSibling.hidden=false;"
                            >
                            <span hidden>{{ $studentInitial }}</span>
                        @else
                            <span>{{ $studentInitial }}</span>
                        @endif
                    </span>

                    <span class="student-sidebar-user-copy">
                        <strong>{{ $student->name }}</strong>
                        <small>Étudiant</small>
                    </span>

                    <i class="bi bi-chevron-right"></i>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button class="student-logout-button" type="submit">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- PARTIE PRINCIPALE --}}
        <div class="student-main">

            {{-- TOPBAR, même structure que l’Admin et le Professeur --}}
            <header class="student-topbar">

                <div class="student-topbar-left">

                    <button
                        type="button"
                        class="student-icon-button d-xl-none"
                        onclick="StudentSpace.openSidebar()"
                        aria-label="Ouvrir le menu"
                    >
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="student-page-heading">
                        <span class="student-page-kicker">
                            Espace étudiant
                        </span>

                        <h1>
                            @yield('page_title', 'Tableau de bord')
                        </h1>

                        @unless(Route::is('student.dashboard'))
                            <div class="student-breadcrumb">
                                <a href="{{ route('student.dashboard') }}">
                                    Accueil
                                </a>
                                <i class="bi bi-chevron-right"></i>
                                <span>
                                    @yield('breadcrumb', 'Espace étudiant')
                                </span>
                            </div>
                        @endunless
                    </div>
                </div>

                <div class="student-topbar-actions">

                    <a
                        href="{{ route('home') }}"
                        class="student-site-button"
                    >
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>Voir le site</span>
                    </a>
<div class="student-user-menu">

                        <button
                            type="button"
                            class="student-user-trigger"
                            id="studentUserButton"
                            onclick="StudentSpace.toggleUserMenu()"
                            aria-expanded="false"
                        >
                            <span class="student-avatar student-avatar-small">
                                @if($studentPhotoUrl)
                                    <img
                                        src="{{ $studentPhotoUrl }}"
                                        alt=""
                                        onerror="this.hidden=true; this.nextElementSibling.hidden=false;"
                                    >
                                    <span hidden>{{ $studentInitial }}</span>
                                @else
                                    <span>{{ $studentInitial }}</span>
                                @endif
                            </span>

                            <span class="student-user-trigger-copy">
                                <strong>{{ $student->name }}</strong>
                                <small>Étudiant</small>
                            </span>

                            <i class="bi bi-chevron-down"></i>
                        </button>

                        <div
                            class="student-user-dropdown"
                            id="studentUserDropdown"
                            hidden
                        >
                            <div class="student-dropdown-header">
                                <span class="student-avatar student-avatar-medium">
                                    @if($studentPhotoUrl)
                                        <img
                                            src="{{ $studentPhotoUrl }}"
                                            alt=""
                                            onerror="this.hidden=true; this.nextElementSibling.hidden=false;"
                                        >
                                        <span hidden>{{ $studentInitial }}</span>
                                    @else
                                        <span>{{ $studentInitial }}</span>
                                    @endif
                                </span>

                                <div>
                                    <strong>{{ $student->name }}</strong>
                                    <small>{{ $student->email }}</small>
                                </div>
                            </div>

                            <div class="student-dropdown-divider"></div>

                            <a
                                href="{{ route('student.profile') }}"
                                class="student-dropdown-item"
                            >
                                <i class="bi bi-person"></i>
                                Mon profil
                            </a>

                            <a
                                href="{{ route('student.settings') }}"
                                class="student-dropdown-item"
                            >
                                <i class="bi bi-gear"></i>
                                Paramètres
                            </a>

                            <a
                                href="{{ route('home') }}"
                                class="student-dropdown-item"
                            >
                                <i class="bi bi-house-door"></i>
                                Accueil principal
                            </a>

                            <div class="student-dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button
                                    type="submit"
                                    class="student-dropdown-item danger"
                                >
                                    <i class="bi bi-box-arrow-right"></i>
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="student-content">
                <div class="student-content-inner">

                    @if(session('success'))
                        <div class="student-alert success">
                            <span class="student-alert-icon">
                                <i class="bi bi-check-lg"></i>
                            </span>

                            <div>
                                <strong>Opération réussie</strong>
                                <p>{{ session('success') }}</p>
                            </div>

                            <button
                                type="button"
                                onclick="this.closest('.student-alert').remove()"
                                aria-label="Fermer"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="student-alert danger">
                            <span class="student-alert-icon">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </span>

                            <div>
                                <strong>Une erreur est survenue</strong>
                                <p>{{ session('error') }}</p>
                            </div>

                            <button
                                type="button"
                                onclick="this.closest('.student-alert').remove()"
                                aria-label="Fermer"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
<script src="{{ asset('js/student-admin-prof-v4.js') }}"></script>

    @stack('scripts')
</body>
</html>
