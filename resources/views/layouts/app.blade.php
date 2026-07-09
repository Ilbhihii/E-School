<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SChool bridge')</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @stack('head')

    <!-- 3D Design System -->
    <link rel="stylesheet" href="{{ asset('css/layouts-3d.css') }}">

    <style>
        body {
            background: var(--3d-bg-dark);
            color: rgba(255,255,255,0.85);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        .app-nav a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
            position: relative;
        }

        .app-nav a:hover {
            color: white;
            background: rgba(255,255,255,0.06);
            transform: translateY(-2px);
        }

        .app-nav a::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #003A8F, #7C3AED);
            transform: translateX(-50%);
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        .app-nav a:hover::after {
            width: 60%;
        }

        .app-content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Buttons in app layout */
        .app-content .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .app-content .btn-primary {
            background: var(--3d-gradient-main);
            border: none;
        }

        .app-content .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 58, 143, 0.4);
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

        /* ══════════════════════════════════════════════════════════════
           MODE CLAIR — App Layout
           ══════════════════════════════════════════════════════════════ */
        html.light-mode body {
            background: #f0f2f5 !important;
            color: #1e293b !important;
        }
        html.light-mode .bg-3d-animated {
            background: #f0f2f5 !important;
        }
        html.light-mode .bg-3d-animated::before,
        html.light-mode .bg-3d-animated::after {
            opacity: 0.3;
        }
        html.light-mode .bg-3d-particles span {
            background: rgba(0,0,0,0.08);
        }
        html.light-mode .navbar-3d {
            background: rgba(255,255,255,0.85) !important;
            border-bottom: 1px solid rgba(0,0,0,0.06) !important;
        }
        html.light-mode .navbar-3d .navbar-brand {
            -webkit-text-fill-color: #1e293b;
        }
        html.light-mode .app-nav a {
            color: #475569 !important;
        }
        html.light-mode .app-nav a:hover {
            color: #1e293b !important;
            background: rgba(0,0,0,0.04) !important;
        }
        html.light-mode .app-content {
            color: #1e293b;
        }

        /* ── LOGO THEME SWITCH ── */
        .logo-theme-dark { display: inline-block; }
        .logo-theme-light { display: none; }
        html.light-mode .logo-theme-dark { display: none; }
        html.light-mode .logo-theme-light { display: inline-block; }

        @media (max-width: 768px) {
            .app-nav {
                flex-direction: column;
                gap: 0.25rem;
            }
            .app-content {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>

<!-- ═══ 3D BACKGROUND ═══ -->
<div class="bg-3d-animated">
    <div class="bg-3d-particles">
        <span></span><span></span><span></span>
        <span></span><span></span>
    </div>
</div>

<!-- ═══ NAVBAR ═══ -->
<nav class="navbar-3d d-flex align-items-center justify-content-between px-4 py-3 flex-wrap gap-3">
    <a href="{{ route('home') }}" class="navbar-brand mb-0">
        <img src="{{ asset('images/logoSSA.jpeg') }}" width="48" height="48" alt="" class="logo-theme-dark me-2" style="border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <img src="{{ asset('images/logoSSA-removebg-preview.png') }}" width="48" height="48" alt="" class="logo-theme-light me-2" style="border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        SChool bridge
    </a>

    <div class="app-nav d-flex flex-wrap align-items-center gap-1">
        <!-- ═══ THEME TOGGLE ═══ -->
        <button class="theme-toggle-btn" id="themeToggle" aria-label="Changer le thème"
                style="width:36px;height:36px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.6);display:flex;align-items:center;justify-content:center;font-size:1rem;cursor:pointer;transition:all 0.3s ease;flex-shrink:0;">
            <i class="bi bi-moon-fill icon-moon"></i>
            <i class="bi bi-sun-fill icon-sun"></i>
        </button>

        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Tableau de bord</a>
                <a href="{{ route('admin.users.index') }}"><i class="bi bi-people me-1"></i>Utilisateurs</a>
                <a href="{{ route('admin.courses.index') }}"><i class="bi bi-book me-1"></i>Cours</a>
                <a href="{{ route('admin.subjects.index') }}"><i class="bi bi-book me-1"></i>Matières</a>
            @endif

            @if(auth()->user()->role === 'prof')
                <a href="{{ route('prof.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Tableau de bord</a>
                <a href="{{ route('prof.courses.index') }}"><i class="bi bi-book me-1"></i>Mes Cours</a>
                <a href="{{ route('prof.lives.index') }}"><i class="bi bi-camera-video me-1"></i>Lives</a>
                <a href="{{ route('prof.devoir.index') }}"><i class="bi bi-file-earmark-check me-1"></i>Devoirs</a>
            @endif

            @if(auth()->user()->role === 'student')
                <a href="{{ route('student.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Tableau de bord</a>
                <a href="{{ route('student.subjects') }}"><i class="bi bi-book me-1"></i>Matières</a>
                <a href="{{ route('student.lives') }}"><i class="bi bi-camera-video me-1"></i>Lives</a>
                <a href="{{ route('student.courses.index') }}"><i class="bi bi-collection me-1"></i>Cours</a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="d-inline ms-2">
                @csrf
                <button class="btn nav-btn-3d nav-btn-3d-danger py-1 px-3" style="font-size: 0.85rem;">
                    <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                </button>
            </form>
        @endauth
    </div>
</nav>

<!-- ═══ CONTENT ═══ -->
<main class="app-content">
    @yield('content')
</main>

@include('partials.theme-toggle-js')

@stack('scripts')

</body>
</html>
