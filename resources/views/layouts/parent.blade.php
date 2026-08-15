<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <title>@yield('title', 'Espace Parent') — Smart School Academy</title>
    <link rel="icon" href="{{ asset('images/logoSSA-removebg-preview.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/parent-space.css') }}">
</head>
<body class="parent-space">
    <div class="parent-shell">
        <aside class="parent-sidebar">
            <a href="{{ route('parent.dashboard') }}" class="parent-brand">
                <img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="2SA">
                <span><strong>SmartSchool</strong><small>Espace parent</small></span>
            </a>

            <nav class="parent-nav">
                <a href="{{ route('parent.dashboard') }}" class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Tableau de bord
                </a>
                <a href="{{ route('home') }}">
                    <i class="bi bi-house-door-fill"></i> Voir le site
                </a>
            </nav>

            <div class="parent-footer">
                <strong>{{ auth()->user()->name }}</strong>
                <small>Compte Parent</small>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
                </form>
            </div>
        </aside>

        <main class="parent-main">
            <header class="parent-topbar">
                <div><small>2SA Smart School Academy</small><h1>@yield('page_title', 'Espace Parent')</h1></div>

                <div style="display:flex;align-items:center;gap:10px;">
                    @include('components.notification-bell')
                    <span><i class="bi bi-shield-check"></i> Suivi sécurisé</span>
                </div>
            </header>

            <section class="parent-content">
                @if(session('success'))
                    <div class="parent-alert">{{ session('success') }}</div>
                @endif
                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
