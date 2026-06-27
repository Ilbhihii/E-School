<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart School Academy — Plateforme Éducative')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- 3D Design System -->
    <link rel="stylesheet" href="{{ asset('css/layouts-3d.css') }}">
    <style>
        /* ══════════════════════════════════════════════════════════════
           DARK MODE 3D — FRONT LAYOUT
           ══════════════════════════════════════════════════════════════ */

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #080c14;
            color: rgba(255,255,255,0.88);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── BADGE NAV ── */
        .badge-nav {
            position: absolute;
            bottom: -11px;
            left: 50%;
            transform: translateX(-50%);
            background: #fbbf24;
            color: #78350f;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 4px;
            white-space: nowrap;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            pointer-events: none;
            animation: badgePulse 2s ease-in-out infinite;
        }
        @keyframes badgePulse {
            0%, 100% { transform: translateX(-50%) scale(1); }
            50% { transform: translateX(-50%) scale(1.08); }
        }

        /* ── PULSE GLOW ON REGISTER BTN ── */
        .pulse-glow {
            animation: btnPulseGlow 2.5s ease-in-out infinite;
        }
        @keyframes btnPulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(0, 58, 143, 0.4); }
            50% { box-shadow: 0 0 0 12px rgba(0, 58, 143, 0), 0 0 30px rgba(124, 58, 237, 0.2); }
        }

        /* ── FLOATING CTA BANNER ── */
        .floating-cta {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 9995;
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 12px 0;
            transform: translateY(100%);
            transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.3);
        }
        .floating-cta.show {
            transform: translateY(0);
        }
        .floating-cta .cta-pulse-ring {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #22C55E;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5);
            animation: livePulse 2s ease-in-out infinite;
            flex-shrink: 0;
        }
        @keyframes livePulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); }
            50% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
        .floating-cta-close {
            background: rgba(255, 255, 255, 0.06);
            border: none;
            color: rgba(255, 255, 255, 0.4);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        .floating-cta-close:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
        }

        /* ── FLOATING WHATSAPP / CHAT ── */
        .floating-chat {
            position: fixed;
            bottom: 90px;
            right: 30px;
            z-index: 9994;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 8px 30px rgba(37, 211, 102, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            animation: chatBounce 2s ease-in-out infinite;
        }
        .floating-chat:hover {
            transform: scale(1.1) translateY(-4px);
            box-shadow: 0 12px 40px rgba(37, 211, 102, 0.4);
            color: white;
        }
        .floating-chat .chat-tooltip {
            position: absolute;
            right: 66px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            color: white;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            transform: translateX(10px);
            transition: all 0.3s ease;
            pointer-events: none;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .floating-chat:hover .chat-tooltip {
            opacity: 1;
            transform: translateX(0);
        }
        @keyframes chatBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @media (max-width: 768px) {
            .floating-chat {
                bottom: 80px;
                right: 16px;
                width: 48px;
                height: 48px;
                font-size: 1.25rem;
            }
            .floating-cta .btn-3d-gold {
                padding: 8px 16px !important;
                font-size: 0.8rem !important;
            }
        }

        /* ── PRELOADER ── */
        .preloader-3d {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: #080c14;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 24px;
            transition: opacity 0.8s ease, visibility 0.8s ease;
        }
        .preloader-3d.loaded {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .preloader-3d-logo {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            animation: preloaderPulse 1.5s ease-in-out infinite;
        }
        .preloader-3d-bar {
            width: 160px;
            height: 3px;
            background: rgba(255,255,255,0.06);
            border-radius: 4px;
            overflow: hidden;
        }
        .preloader-3d-bar-inner {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #003A8F, #7C3AED, #FFD166);
            border-radius: 4px;
            animation: preloaderFill 0.7s ease-in-out forwards;
        }
        @keyframes preloaderPulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 1; }
        }
        @keyframes preloaderFill {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        /* ── COSMIC DARK BACKGROUND ── */
        .cosmic-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            background: radial-gradient(ellipse at 20% 50%, #0a1628, #080c14 60%, #060810);
        }

        /* Deep space gradient layers */
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

        /* Nebula accent */
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

        /* ── SHOOTING STARS ── */
        .shooting-star {
            position: absolute;
            width: 120px;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6));
            border-radius: 1px;
            animation: shoot 4s ease-in-out infinite;
            opacity: 0;
        }
        .shooting-star:nth-child(1) { top: 10%; left: 80%; animation-delay: 3s; }
        .shooting-star:nth-child(2) { top: 25%; left: 60%; animation-delay: 8s; }
        .shooting-star:nth-child(3) { top: 5%; left: 90%; animation-delay: 15s; }
        .shooting-star::after {
            content: '';
            position: absolute;
            right: 0;
            top: -2px;
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        @keyframes shoot {
            0% { transform: translateX(0) translateY(0) rotate(-35deg); opacity: 0; }
            5% { opacity: 1; }
            15% { transform: translateX(-300px) translateY(200px) rotate(-35deg); opacity: 0; }
            100% { opacity: 0; }
        }

        /* ── IMAGE HOVER ZOOM IN CARDS ── */
        .card-3d:hover img {
            transform: scale(1.08);
        }

        /* ── 3D CUSTOM CURSOR ── */
        .cursor-3d {
            position: fixed;
            width: 24px;
            height: 24px;
            border: 2px solid rgba(255, 209, 102, 0.4);
            border-radius: 50%;
            pointer-events: none;
            z-index: 99998;
            transition: width 0.3s, height 0.3s, border-color 0.3s, background 0.3s, transform 0.1s;
            transform: translate(-50%, -50%);
            backdrop-filter: blur(4px);
            display: none;
        }
        .cursor-3d.active {
            width: 48px;
            height: 48px;
            border-color: rgba(255, 209, 102, 0.6);
            background: rgba(255, 209, 102, 0.06);
        }
        .cursor-3d-dot {
            position: fixed;
            width: 6px;
            height: 6px;
            background: #FFD166;
            border-radius: 50%;
            pointer-events: none;
            z-index: 99999;
            transform: translate(-50%, -50%);
            transition: transform 0.05s;
            display: none;
        }

        /* ── CONTENT WRAPPER ── */
        .front-content {
            padding-top: 90px;
            min-height: 70vh;
            position: relative;
            z-index: 2;
        }

        /* ── SECTION DIVIDERS ── */
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.04), transparent);
            margin: 0;
        }

        /* ── SMOOTH TRANSITIONS ── */
        .fade-in-3d {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .fade-in-3d.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── GLOW TEXT ── */
        .glow-text {
            text-shadow: 0 0 40px rgba(0, 58, 143, 0.3), 0 0 80px rgba(124, 58, 237, 0.15);
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

        /* ── NAVBAR OVERRIDES ── */
        .navbar-3d {
            background: rgba(153, 153, 215, 0) !important;
        }
        .navbar-3d.scrolled {
            background: rgba(8, 12, 20, 0.92) !important;
        }

        /* ── FOOTER OVERRIDES ── */
        .footer-3d {
            background: rgba(8, 12, 20, 0.95);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .cursor-3d, .cursor-3d-dot { display: none !important; }
            .shooting-star { display: none; }
        }

        @media (min-width: 1024px) {
            .cursor-3d, .cursor-3d-dot { display: block; }
        }
    </style>

    @stack('head')
</head>

<body>

<!-- ═══ PRELOADER ═══ -->
<div class="preloader-3d" id="preloader">
    <img src="{{ asset('images/logoSSA.jpeg') }}" alt="" class="preloader-3d-logo">
    <div class="preloader-3d-bar">
        <div class="preloader-3d-bar-inner"></div>
    </div>
    <small style="color: rgba(255,255,255,0.25); font-size: 0.75rem; letter-spacing: 0.15em; text-transform: uppercase;">
        Chargement...
    </small>
</div>

<!-- ═══ 3D CURSOR ═══ -->
<div class="cursor-3d" id="cursor3d"></div>
<div class="cursor-3d-dot" id="cursorDot"></div>

<!-- ═══ COSMIC DARK BACKGROUND ═══ -->
<div class="cosmic-bg">
    <div class="cosmic-nebula"></div>
    <div class="orb"></div>
    <div class="orb"></div>
    <div class="orb"></div>

    <!-- Stars -->
    <div class="stars-container" id="starsContainer"></div>

    <!-- Shooting stars -->
    <div class="shooting-star"></div>
    <div class="shooting-star"></div>
    <div class="shooting-star"></div>
</div>

<!-- ═══ 3D GLASS NAVBAR ═══ -->
<nav id="navbar3d" class="navbar navbar-expand-lg navbar-3d fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logoSSA.jpeg') }}" width="60" height="60" alt="Smart School Academy" class="me-2" style="border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            Smart School Academy
        </a>

        <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                style="background: rgba(255,255,255,0.06); border-radius: 10px;" aria-label="Toggle navigation">
            <i class="bi bi-list text-white fs-4"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Accueil</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('front.classes') }}">Matières</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('front.lives') }}">Lives</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('appointment.create') }}" style="color: #FFD166;">
                        Rendez-vous
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('plans') }}">Offres</a>
                </li>

                @auth
                    <li class="nav-item ms-lg-2">
                        <a href="{{ route('student.dashboard') }}" class="btn nav-btn-3d nav-btn-3d-primary">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item ms-lg-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn nav-btn-3d nav-btn-3d-danger">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item ms-lg-2">
                        <a href="{{ route('login') }}" class="btn nav-btn-3d" style="background: linear-gradient(135deg, #FFD166, #FFB347); color: #1E293B; font-weight: 700; box-shadow: 0 4px 20px rgba(255, 209, 102, 0.3);">
                            <i class="bi bi-person"></i> Connexion
                        </a>
                    </li>

                    <li class="nav-item ms-lg-1" style="position: relative; overflow: visible; padding-bottom: 8px;">
                        <a href="{{ route('register') }}"
                           class="btn nav-btn-3d nav-btn-3d-primary pulse-glow"
                           style="position: relative; overflow: visible;">
                            <i class="bi bi-person-plus"></i> Inscription
                            <span class="badge-nav">Gratuit</span>
                        </a>
                    </li>
                @endauth

            </ul>
        </div>
    </div>
</nav>

<!-- ═══ CONTENT ═══ -->
<main class="front-content">
    @yield('content')
</main>

<!-- ═══ 3D FOOTER ═══ -->
<footer class="footer-3d text-white py-5 mt-5">
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('images/logoSSA.jpeg') }}" width="64" height="64" alt="" style="border-radius: 12px;">
                    <div>
                        <h5 class="fw-bold mb-0" style="font-family: 'Poppins', sans-serif;">Smart School Academy</h5>
                        <small style="color: rgba(255,255,255,0.3);">Apprentissage intelligent</small>
                    </div>
                </div>
                <p class="text-white-50 small" style="line-height: 1.8; max-width: 320px;">
                    Plateforme éducative intelligente qui connecte étudiants et enseignants pour une expérience d'apprentissage moderne et interactive.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="social-icon-3d"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon-3d"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon-3d"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="social-icon-3d"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="fw-bold mb-3 text-white" style="font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.5;">Navigation</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('home') }}" class="footer-link-3d">Accueil</a>
                    <a href="{{ route('front.classes') }}" class="footer-link-3d">Matières</a>
                    <a href="{{ route('front.lives') }}" class="footer-link-3d">Lives</a>
                    <a href="{{ route('appointment.create') }}" class="footer-link-3d">Rendez-vous</a>
                    <a href="{{ route('plans') }}" class="footer-link-3d">Offres</a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="fw-bold mb-3 text-white" style="font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.5;">Matières</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('front.religieux') }}" class="footer-link-3d">Religieuses</a>
                    <a href="{{ route('front.classes') }}" class="footer-link-3d">Scolaires</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <h6 class="fw-bold mb-3 text-white" style="font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.5;">Contact</h6>
                <div class="d-flex flex-column gap-2 small text-white-50">
                    <span class="d-flex align-items-center gap-2">
                        <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-envelope" style="font-size: 0.85rem;"></i>
                        </span>
                        contact@e-school.com
                    </span>
                    <span class="d-flex align-items-center gap-2">
                        <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-telephone" style="font-size: 0.85rem;"></i>
                        </span>
                        +212 5XX XX XX XX
                    </span>
                    <span class="d-flex align-items-center gap-2">
                        <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-geo-alt" style="font-size: 0.85rem;"></i>
                        </span>
                        Casablanca, Maroc
                    </span>
                </div>
            </div>

        </div>

        <hr class="my-4" style="border-color: rgba(255,255,255,0.04);">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <small style="color: rgba(255,255,255,0.25);">
                &copy; {{ date('Y') }} Smart School Academy. Tous droits réservés.
            </small>
            <small style="color: rgba(255,255,255,0.2);">
                Fièrement conçu avec <i class="bi bi-heart-fill" style="color: #D90429;"></i> pour l'éducation
            </small>
        </div>
    </div>
</footer>

<!-- ═══ BACK TO TOP 3D ═══ -->
<button id="backToTop" class="back-to-top-3d" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Retour en haut">
    <i class="bi bi-arrow-up"></i>
</button>

<!-- ═══ FLOATING CTA BANNER ═══ -->
<div class="floating-cta" id="floatingCta">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="d-none d-md-flex align-items-center gap-3">
            <div class="cta-pulse-ring"></div>
            <span class="fw-semibold" style="font-size: 0.95rem;">
                🎓 <span style="color: var(--3d-gold);">50%</span> de réduction sur l'abonnement Premium — offre limitée
            </span>
        </div>
        <div class="d-flex align-items-center gap-3 w-100 w-md-auto justify-content-center justify-content-md-end">
            <a href="{{ route('register') }}" class="btn-3d btn-3d-gold" style="padding: 10px 24px; font-size: 0.9rem; white-space: nowrap;">
                <i class="bi bi-rocket-takeoff"></i> Commencer maintenant
            </a>
            <button class="floating-cta-close" onclick="document.getElementById('floatingCta').classList.add('d-none')" aria-label="Fermer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
</div>

<!-- ═══ FLOATING WHATSAPP ═══ -->
<a href="https://wa.me/2125XXXXXXXX?text=Bonjour%20Smart%20School%20Academy%20!%20J'aimerais%20en%20savoir%20plus" target="_blank" class="floating-chat" aria-label="WhatsApp">
    <i class="bi bi-whatsapp"></i>
    <span class="chat-tooltip">Besoin d'aide ?</span>
</a>

<!-- ═══ AI CHATBOT ═══ -->
<x-ai-chatbot />

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function() {
        'use strict';

        // ── PRELOADER ──
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('preloader').classList.add('loaded');
            }, 1200);
        });

        // ── GENERATE STARS ──
        (function createStars() {
            const container = document.getElementById('starsContainer');
            if (!container) return;
            const count = 150;
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
        })();

        // ── NAVBAR SCROLL ──
        const navbar = document.getElementById('navbar3d');
        if (navbar) {
            window.addEventListener('scroll', () => {
                navbar.classList.toggle('scrolled', window.scrollY > 50);
            }, { passive: true });
        }

        // ── BACK TO TOP ──
        const backBtn = document.getElementById('backToTop');
        if (backBtn) {
            window.addEventListener('scroll', () => {
                backBtn.classList.toggle('show', window.scrollY > 400);
            }, { passive: true });
        }

        // ── 3D CURSOR ──
        const cursor = document.getElementById('cursor3d');
        const cursorDot = document.getElementById('cursorDot');
        if (cursor && cursorDot && window.innerWidth >= 1024) {
            let mouseX = 0, mouseY = 0;
            let cursorX = 0, cursorY = 0;

            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
                cursorDot.style.left = mouseX + 'px';
                cursorDot.style.top = mouseY + 'px';
            });

            function animateCursor() {
                cursorX += (mouseX - cursorX) * 0.12;
                cursorY += (mouseY - cursorY) * 0.12;
                cursor.style.left = cursorX + 'px';
                cursor.style.top = cursorY + 'px';
                requestAnimationFrame(animateCursor);
            }
            animateCursor();

            document.querySelectorAll('a, button, .card-3d, .btn, .nav-link').forEach(el => {
                el.addEventListener('mouseenter', () => cursor.classList.add('active'));
                el.addEventListener('mouseleave', () => cursor.classList.remove('active'));
            });
        }

        // ── FLOATING CTA ──
        const floatingCta = document.getElementById('floatingCta');
        if (floatingCta) {
            const ctaDismissed = localStorage.getItem('ctaDismissed');
            if (ctaDismissed === 'true') {
                floatingCta.style.display = 'none';
            } else {
                let ctaShown = false;
                window.addEventListener('scroll', () => {
                    if (!ctaShown && window.scrollY > 600) {
                        floatingCta.classList.add('show');
                        ctaShown = true;
                    }
                }, { passive: true });

                const closeBtn = floatingCta.querySelector('.floating-cta-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        floatingCta.classList.remove('show');
                        floatingCta.style.display = 'none';
                        localStorage.setItem('ctaDismissed', 'true');
                    });
                }
            }
        }

        // ── SCROLL REVEAL (fade-in-3d) ──
        const fadeEls = document.querySelectorAll('.fade-in-3d');
        if (fadeEls.length) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            fadeEls.forEach(el => observer.observe(el));
        }

        // ── SCROLL REVEAL (reveal-3d) ──
        const reveal3dEls = document.querySelectorAll('.reveal-3d');
        if (reveal3dEls.length) {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });
            reveal3dEls.forEach(el => revealObserver.observe(el));
        }

        // ── 3D TILT ON CARDS ──
        document.querySelectorAll('.card-3d').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;
                card.style.transform =
                    `perspective(1000px) rotateY(${x * 8}deg) rotateX(${-y * 8}deg) translateY(-10px) translateZ(10px)`;
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
            });
        });

        // ── AUTO-CLOSE MOBILE NAV ──
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const collapse = document.getElementById('navbarContent');
                if (collapse && collapse.classList.contains('show')) {
                    bootstrap.Collapse.getInstance(collapse)?.hide();
                }
            });
        });

    })();
</script>

@stack('scripts')

</body>
</html>