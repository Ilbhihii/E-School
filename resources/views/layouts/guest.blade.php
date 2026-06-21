<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Smart School Academy — Plateforme Éducative')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @stack('head')

    <!-- 3D Design System -->
    <link rel="stylesheet" href="{{ asset('css/layouts-3d.css') }}">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #080c14;
            color: rgba(255,255,255,0.88);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── COSMIC DARK BACKGROUND ── */
        .cosmic-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            background: radial-gradient(ellipse at 20% 50%, #0a1628, #080c14 60%, #060810);
        }

        .cosmic-bg::before {
            content: '';
            position: absolute;
            width: 800px;
            height: 800px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 58, 143, 0.12), transparent 70%);
            top: -250px;
            right: -150px;
            animation: cosmicDrift 25s ease-in-out infinite;
        }

        .cosmic-bg::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.08), transparent 70%);
            bottom: -200px;
            left: -100px;
            animation: cosmicDrift 30s ease-in-out infinite reverse;
        }

        .cosmic-nebula {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 209, 102, 0.04), transparent 60%);
            top: 40%;
            right: 10%;
            filter: blur(80px);
            animation: cosmicDrift 20s ease-in-out infinite;
            animation-delay: -10s;
        }

        @keyframes cosmicDrift {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(60px, -70px) scale(1.15); }
            66% { transform: translate(-40px, 50px) scale(0.85); }
        }

        /* ── STARS ── */
        .stars-container {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }

        .star {
            position: absolute;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            animation: twinkle var(--duration) ease-in-out infinite;
            animation-delay: var(--delay);
        }

        .star.size-md { width: 3px; height: 3px; }
        .star.size-lg { width: 4px; height: 4px; box-shadow: 0 0 6px rgba(255,255,255,0.3); }

        @keyframes twinkle {
            0%, 100% { opacity: var(--min-opacity, 0.2); transform: scale(1); }
            50% { opacity: var(--max-opacity, 0.9); transform: scale(1.3); }
        }

        /* ── FLOATING ORBS ── */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(50px);
            opacity: 0.08;
            animation: orbFloat 18s ease-in-out infinite;
        }

        .orb:nth-child(1) {
            width: 300px; height: 300px;
            background: #003A8F;
            top: 15%; left: 5%;
            animation-delay: 0s;
        }

        .orb:nth-child(2) {
            width: 250px; height: 250px;
            background: #7C3AED;
            bottom: 20%; right: 10%;
            animation-delay: -6s;
        }

        .orb:nth-child(3) {
            width: 200px; height: 200px;
            background: #FFD166;
            top: 50%; left: 50%;
            animation-delay: -12s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(40px, -60px) scale(1.2); }
            50% { transform: translate(-30px, 40px) scale(0.9); }
            75% { transform: translate(20px, -20px) scale(1.1); }
        }

        /* ── REVEAL 3D ── */
        .reveal-3d {
            opacity: 0;
            transform: translateY(40px) rotateX(-10deg);
            transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .reveal-3d.revealed {
            opacity: 1;
            transform: translateY(0) rotateX(0);
        }

        /* ── GUEST NAV OVERRIDES ── */
        .guest-nav-3d {
            background: rgba(8, 12, 20, 0.6) !important;
            backdrop-filter: blur(20px) saturate(1.5);
            -webkit-backdrop-filter: blur(20px) saturate(1.5);
        }
        .guest-nav-3d.scrolled {
            background: rgba(8, 12, 20, 0.92) !important;
        }

        /* ── AUTH CARD TWEAKS ── */
        .auth-card-3d {
            animation: cardEntry 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes cardEntry {
            from { opacity: 0; transform: translateY(30px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── AUTH LINKS ── */
        .auth-link-3d {
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }
        .auth-link-3d:hover {
            color: var(--3d-gold);
        }

        /* ── SECTION DIVIDER ── */
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.04), transparent);
            margin: 0;
        }

        @media (max-width: 768px) {
            .shooting-star { display: none; }
        }
    </style>
</head>

<body>

<!-- ═══ COSMIC BACKGROUND ═══ -->
<div class="cosmic-bg">
    <div class="cosmic-nebula"></div>
    <div class="orb"></div>
    <div class="orb"></div>
    <div class="orb"></div>
    <div class="stars-container" id="starsContainer"></div>
</div>

<!-- ═══ GUEST NAVBAR ═══ -->
<nav id="guestNav" class="guest-nav-3d d-flex align-items-center justify-content-between px-4 py-3">
    <a href="{{ route('home') }}" class="navbar-brand mb-0">
        <img src="{{ asset('images/Edu-School.png') }}" width="36" height="36" alt="" class="me-2" style="filter: brightness(10); border-radius: 6px;">
        Smart School Academy
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('login') }}" class="btn nav-btn-3d btn-sm px-3" style="font-size: 0.85rem; background: linear-gradient(135deg, #FFD166, #FFB347); color: #1E293B; font-weight: 700; box-shadow: 0 4px 20px rgba(255, 209, 102, 0.3);">
            <i class="bi bi-person"></i> Connexion
        </a>
        <a href="{{ route('register') }}" class="btn nav-btn-3d nav-btn-3d-primary btn-sm px-3" style="font-size: 0.85rem;">
            <i class="bi bi-person-plus"></i> Inscription
        </a>
    </div>
</nav>

<!-- ═══ AUTH CONTENT ═══ -->
<div class="auth-container-3d" style="padding-top: 100px; min-height: 100vh;">
    <div class="auth-card-3d">
        <div class="text-center mb-4">
            <img src="{{ asset('images/Edu-School.png') }}" width="64" height="64" alt="" 
                 style="border-radius: 14px; box-shadow: 0 8px 30px rgba(0,58,143,0.3);">
            <h4 class="auth-title-3d mt-3 mb-0" style="font-size: 1.3rem;">Smart School Academy</h4>
            <small style="color: rgba(255,255,255,0.3); font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase;">Plateforme éducative</small>
        </div>
        @yield('content')
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function() {
    'use strict';

    // ── GENERATE STARS ──
    const container = document.getElementById('starsContainer');
    if (container) {
        const count = 80;
        for (let i = 0; i < count; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            const size = Math.random();
            if (size > 0.9) star.classList.add('size-lg');
            else if (size > 0.7) star.classList.add('size-md');
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.setProperty('--duration', (3 + Math.random() * 4) + 's');
            star.style.setProperty('--delay', (Math.random() * 5) + 's');
            star.style.setProperty('--min-opacity', (0.1 + Math.random() * 0.3));
            star.style.setProperty('--max-opacity', (0.5 + Math.random() * 0.5));
            container.appendChild(star);
        }
    }

    // ── NAVBAR SCROLL ──
    const nav = document.getElementById('guestNav');
    if (nav) {
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 50);
        }, { passive: true });
    }

    // ── REVEAL 3D ──
    const revealEls = document.querySelectorAll('.reveal-3d');
    if (revealEls.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        revealEls.forEach(el => observer.observe(el));
    }
})();
</script>

@stack('scripts')

</body>
</html>
