<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-School')</title>

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
        <img src="{{ asset('images/Edu-School.png') }}" width="28" height="28" alt="" class="me-2" style="filter: brightness(10); border-radius: 6px;">
        E-School
    </a>

    <div class="app-nav d-flex flex-wrap align-items-center gap-1">
        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                <a href="{{ route('admin.users.index') }}"><i class="bi bi-people me-1"></i>Utilisateurs</a>
                <a href="{{ route('admin.courses.index') }}"><i class="bi bi-book me-1"></i>Cours</a>
                <a href="{{ route('admin.levels.index') }}"><i class="bi bi-layers me-1"></i>Niveaux</a>
            @endif

            @if(auth()->user()->role === 'prof')
                <a href="{{ route('prof.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                <a href="{{ route('prof.courses.index') }}"><i class="bi bi-book me-1"></i>Mes Cours</a>
                <a href="{{ route('prof.lives.index') }}"><i class="bi bi-camera-video me-1"></i>Lives</a>
                <a href="{{ route('prof.devoir.index') }}"><i class="bi bi-file-earmark-check me-1"></i>Devoirs</a>
                <a href="{{ route('prof.tests.index') }}"><i class="bi bi-clipboard-check me-1"></i>Tests</a>
            @endif

            @if(auth()->user()->role === 'student')
                <a href="{{ route('student.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
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

@stack('scripts')

</body>
</html>
