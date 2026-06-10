<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-School — Plateforme Éducative')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:     #1e40af;
            --primary-d:   #1e3a8a;
            --primary-l:   #3b82f6;
            --accent:      #f59e0b;
            --accent-l:    #fbbf24;
            --teal:        #0d9488;
            --bg:          #f8fafc;
            --text:        #0f172a;
            --text-light:  #475569;
            --muted:       #94a3b8;
            --border:      #e2e8f0;
            --nav-h:       72px;
            --shadow:      0 4px 24px rgba(0,0,0,.06);
            --shadow-hover: 0 12px 40px rgba(0,0,0,.1);
            --radius:      12px;
            --radius-lg:   20px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg); color: var(--text);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── NAVBAR ── */
        .main-nav {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--nav-h); z-index: 1000;
            background: rgba(15,23,42,.85);
            backdrop-filter: blur(20px) saturate(1.5);
            -webkit-backdrop-filter: blur(20px) saturate(1.5);
            border-bottom: 1px solid rgba(255,255,255,.06);
            transition: all .3s ease;
        }
        .main-nav.scrolled {
            background: rgba(15,23,42,.95);
            box-shadow: 0 4px 30px rgba(0,0,0,.2);
        }
        .nav-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 0 1.5rem; height: 100%;
            display: flex; align-items: center; gap: 2rem;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none; flex-shrink: 0;
        }
        .nav-brand .brand-icon {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
        }
        .nav-brand .brand-icon img {
            width: 100%; height: 100%; object-fit: contain;
        }
        .nav-brand span {
            font-size: 1.2rem; font-weight: 800;
            background: linear-gradient(135deg, #f8fafc 50%, rgba(248,250,252,.6));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -.03em;
        }
        .nav-brand .brand-beta {
            font-size: 9px; font-weight: 700; letter-spacing: .05em;
            text-transform: uppercase;
            background: rgba(6,182,212,.15); color: #22d3ee;
            padding: 2px 7px; border-radius: 4px;
        }

        .nav-links {
            display: flex; align-items: center; gap: 2px;
            flex: 1; list-style: none;
        }
        .nav-links a {
            display: block; padding: .5rem .9rem;
            color: rgba(255,255,255,.7); font-size: 14px; font-weight: 500;
            text-decoration: none; border-radius: 8px;
            transition: all .2s ease; position: relative;
        }
        .nav-links a:hover { color: white; background: rgba(255,255,255,.07); }
        .nav-links a.active {
            color: white; font-weight: 600;
            background: rgba(255,255,255,.08);
        }

        .nav-ctas { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }
        .btn-nav-ghost {
            padding: .45rem 1.1rem; border-radius: 999px;
            color: rgba(255,255,255,.8); font-size: 13px; font-weight: 600;
            text-decoration: none; transition: all .2s ease;
        }
        .btn-nav-ghost:hover { background: rgba(255,255,255,.08); color: white; }
        .btn-nav-primary {
            padding: .45rem 1.25rem; border-radius: 999px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white; font-size: 13px; font-weight: 700;
            text-decoration: none; transition: all .25s ease;
            box-shadow: 0 4px 14px rgba(59,130,246,.35);
        }
        .btn-nav-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59,130,246,.45);
            color: white;
        }
        .btn-nav-dashboard {
            padding: .45rem 1.1rem; border-radius: 999px;
            background: rgba(255,255,255,.12); color: white;
            font-size: 13px; font-weight: 600; text-decoration: none;
            transition: all .2s ease;
            border: 1px solid rgba(255,255,255,.15);
        }
        .btn-nav-dashboard:hover { background: rgba(255,255,255,.2); color: white; }
        .btn-nav-logout {
            padding: .45rem 1rem; border-radius: 999px;
            background: rgba(239,68,68,.12); color: #fca5a5;
            border: 1px solid rgba(239,68,68,.15);
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: all .2s ease; font-family: inherit;
        }
        .btn-nav-logout:hover { background: rgba(239,68,68,.22); color: #f87171; }

        /* Hamburger */
        .nav-toggle {
            display: none; align-items: center; justify-content: center;
            background: none; border: none;
            color: white; font-size: 24px;
            cursor: pointer; width: 38px; height: 38px;
            border-radius: 10px; transition: background .2s;
        }
        .nav-toggle:hover { background: rgba(255,255,255,.1); }

        .mobile-menu {
            display: none;
            position: absolute; top: 100%; left: 0; right: 0;
            background: rgba(15,23,42,.97);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255,255,255,.06);
            padding: 1rem 1.5rem 1.5rem;
            max-height: 0; overflow: hidden;
            transition: max-height .35s cubic-bezier(.22,.61,.36,1);
        }
        .mobile-menu.open {
            display: block;
            max-height: 600px;
        }
        .mobile-menu a, .mobile-menu-link {
            display: block; padding: .7rem 1rem;
            color: rgba(255,255,255,.7);
            font-size: 15px; font-weight: 500;
            text-decoration: none; border-radius: 10px;
            transition: all .15s ease;
        }
        .mobile-menu a:hover { background: rgba(255,255,255,.07); color: white; }
        .mobile-menu-divider {
            border: none; border-top: 1px solid rgba(255,255,255,.06);
            margin: .5rem 0;
        }
        @media(max-width: 768px) {
            .nav-links, .nav-ctas { display: none; }
            .nav-toggle { display: flex; }
            .nav-brand span { font-size: 1rem; }
        }

        /* ── MAIN ── */
        .front-main {
            margin-top: var(--nav-h);
            min-height: calc(100vh - var(--nav-h) - 200px);
            padding: 2rem 0;
        }
        .front-main .container { max-width: 1200px; }

        /* Alerts */
        .front-alert {
            border-radius: var(--radius);
            padding: .9rem 1.2rem;
            font-size: 14px; font-weight: 500;
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 1.25rem;
            animation: alertSlide .4s cubic-bezier(.22,.61,.36,1);
            box-shadow: 0 2px 12px rgba(0,0,0,.04);
        }
        @keyframes alertSlide {
            from { opacity: 0; transform: translateY(-12px) scale(.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .front-alert-success {
            background: #f0fdf4; border: 1px solid #86efac;
            color: #166534;
        }
        .front-alert-danger {
            background: #fef2f2; border: 1px solid #fca5a5;
            color: #dc2626;
        }
        .front-alert-info {
            background: #eff6ff; border: 1px solid #93c5fd;
            color: #1e40af;
        }

        /* ── FOOTER ── */
        .site-footer {
            background: #0f172a;
            background-image: radial-gradient(ellipse at 50% 0%, rgba(59,130,246,.08) 0%, transparent 60%);
            color: white;
            margin-top: 5rem;
            padding: 4rem 0 0;
            position: relative;
            overflow: hidden;
        }
        .site-footer::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59,130,246,.3), transparent);
        }
        .footer-inner { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
        .footer-brand {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 1rem;
        }
        .footer-brand .brand-icon {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
        }
        .footer-brand .brand-icon img {
            width: 100%; height: 100%; object-fit: contain;
        }
        .footer-brand span { font-size: 1.15rem; font-weight: 800; letter-spacing: -.02em; }
        .footer-desc {
            color: rgba(255,255,255,.45);
            font-size: 14px; max-width: 280px;
            line-height: 1.7;
        }
        .footer-social {
            display: flex; gap: .5rem; margin-top: 1.25rem;
        }
        .footer-social a {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.5);
            display: flex; align-items: center; justify-content: center;
            text-decoration: none; font-size: 15px;
            transition: all .2s ease;
        }
        .footer-social a:hover {
            background: rgba(59,130,246,.2);
            color: #93c5fd;
            transform: translateY(-2px);
        }
        .footer-heading {
            font-size: 12px; font-weight: 700;
            letter-spacing: .08em; text-transform: uppercase;
            color: rgba(255,255,255,.3);
            margin-bottom: .9rem;
        }
        .footer-link {
            display: block;
            color: rgba(255,255,255,.55);
            text-decoration: none;
            font-size: 14px; font-weight: 400;
            padding: .3rem 0;
            transition: all .2s ease;
        }
        .footer-link:hover {
            color: white;
            transform: translateX(4px);
        }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.05);
            margin-top: 3rem;
            padding: 1.2rem 1.5rem;
            display: flex; align-items: center;
            justify-content: space-between;
            flex-wrap: wrap; gap: .5rem;
            max-width: 1200px; margin-left: auto; margin-right: auto;
        }
        .footer-bottom-text {
            font-size: 12px;
            color: rgba(255,255,255,.3);
        }

        /* ── BACK TO TOP ── */
        .back-top {
            position: fixed; bottom: 1.5rem; right: 1.5rem;
            width: 44px; height: 44px; border-radius: 14px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white; border: none; cursor: pointer;
            font-size: 18px; display: none;
            align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(59,130,246,.35);
            transition: all .25s ease; z-index: 50;
        }
        .back-top.show { display: flex; }
        .back-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(59,130,246,.45);
        }
        .back-top:active { transform: translateY(0); }
    </style>
    @stack('styles')
</head>
<body>

@php $route = request()->route()->getName() ?? ''; @endphp

<!-- NAVBAR -->
<nav class="main-nav" id="mainNav">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-brand">
            <div class="brand-icon">
                <img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="Logo">
            </div>
            <span>E-School</span>
            <span class="brand-beta">Beta</span>
        </a>

        <ul class="nav-links">
            <li><a href="{{ route('home') }}" class="{{ $route === 'home' ? 'active' : '' }}">Accueil</a></li>
            <li><a href="{{ route('levels') }}" class="{{ str_contains($route,'level') || str_contains($route,'course') ? 'active' : '' }}">Niveaux</a></li>
            <li><a href="{{ route('front.lives') }}" class="{{ str_contains($route,'lives') ? 'active' : '' }}">Lives</a></li>
            <li><a href="{{ route('plans') }}" class="{{ str_contains($route,'plans') ? 'active' : '' }}">Offres</a></li>
        </ul>

        <div class="nav-ctas">
            @auth
                <a href="{{ route('student.dashboard') }}" class="btn-nav-dashboard">
                    <i class="bi bi-grid-fill me-1"></i>Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button class="btn-nav-logout"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-nav-ghost">Connexion</a>
                <a href="{{ route('register') }}" class="btn-nav-primary"><i class="bi bi-person-plus me-1"></i>Inscription</a>
            @endauth
        </div>

        <button class="nav-toggle" onclick="toggleMobile()" id="navToggle" aria-label="Menu">
            <i class="bi bi-list" id="toggleIcon"></i>
        </button>
    </div>

    <!-- Mobile menu -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="{{ route('home') }}"><i class="bi bi-house me-2"></i>Accueil</a>
        <a href="{{ route('levels') }}"><i class="bi bi-layers me-2"></i>Niveaux</a>
        <a href="{{ route('front.lives') }}"><i class="bi bi-camera-video me-2"></i>Lives</a>
        <a href="{{ route('plans') }}"><i class="bi bi-star me-2"></i>Offres</a>
        <hr class="mobile-menu-divider">
        @auth
            <a href="{{ route('student.dashboard') }}"><i class="bi bi-grid me-2"></i>Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button class="mobile-menu-link" style="width:100%;background:none;border:none;text-align:left;color:rgba(255,255,255,.8);cursor:pointer;padding:.7rem 1rem;border-radius:10px;">
                    <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                </button>
            </form>
        @else
            <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2"></i>Connexion</a>
            <a href="{{ route('register') }}"><i class="bi bi-person-plus me-2"></i>Inscription</a>
        @endauth
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="front-main">
    <div class="container">
        @if(session('success'))
            <div class="front-alert front-alert-success">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="front-alert front-alert-danger">
                <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </div>
</main>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="footer-inner">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <div class="brand-icon"><img src="{{ asset('images/logoSSA-removebg-preview.png') }}" alt="Logo"></div>
                    <span>E-School</span>
                </div>
                <p class="footer-desc">Plateforme éducative moderne pour étudiants et enseignants. Apprenez à votre rythme.</p>
                <div class="footer-social">
                    <a href="#" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="#" title="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="footer-heading">Navigation</div>
                <a href="{{ route('home') }}" class="footer-link">Accueil</a>
                <a href="{{ route('levels') }}" class="footer-link">Niveaux</a>
                <a href="{{ route('front.lives') }}" class="footer-link">Lives</a>
                <a href="{{ route('plans') }}" class="footer-link">Offres</a>
            </div>
            <div class="col-lg-2 col-6">
                <div class="footer-heading">Compte</div>
                <a href="{{ route('login') }}" class="footer-link">Connexion</a>
                <a href="{{ route('register') }}" class="footer-link">Inscription</a>
                @auth
                <a href="{{ route('student.dashboard') }}" class="footer-link">Dashboard</a>
                @endauth
            </div>
            <div class="col-lg-4">
                <div class="footer-heading">Contact</div>
                <p style="color:rgba(255,255,255,.6);font-size:14px;line-height:1.6;">
                    <i class="bi bi-envelope me-2"></i>contact@eschool.ma<br>
                    <i class="bi bi-geo-alt me-2"></i>Maroc
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <span class="footer-bottom-text">&copy; {{ date('Y') }} E-School — Tous droits réservés</span>
            <span class="footer-bottom-text">Fait avec ❤️ pour l'éducation</span>
        </div>
    </div>
</footer>

<!-- BACK TO TOP -->
<button class="back-top" id="backTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Navbar scroll
const nav = document.getElementById('mainNav');
window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 40);
    document.getElementById('backTop').classList.toggle('show', window.scrollY > 300);
});

// Mobile toggle
let mobileOpen = false;
function toggleMobile() {
    mobileOpen = !mobileOpen;
    document.getElementById('mobileMenu').classList.toggle('open', mobileOpen);
    document.getElementById('toggleIcon').className = mobileOpen ? 'bi bi-x' : 'bi bi-list';
}
</script>
@stack('scripts')
</body>
</html>