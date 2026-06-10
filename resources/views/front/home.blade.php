@extends('layouts.front')

@section('title', 'Accueil')

@section('content')
<style>
/* ═══════════════════════════════════════
   HOME PAGE — ULTRA PREMIUM DESIGN
   ═══════════════════════════════════════ */

:root {
    --home-primary: #2563eb;
    --home-primary-dark: #1e40af;
    --home-accent: #06b6d4;
    --home-dark: #0f172a;
    --home-surface: #ffffff;
    --home-text: #0f172a;
    --home-text-muted: #64748b;
    --home-border: #e2e8f0;
    --home-radius: 20px;
    --home-radius-sm: 14px;
    --home-shadow: 0 4px 24px rgba(15,23,42,.06);
    --home-shadow-hover: 0 20px 60px rgba(15,23,42,.14);
    --home-transition: all .4s cubic-bezier(.22,.61,.36,1);
}

/* ── Animations ── */
@keyframes homeFadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes homeFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes homeFloat {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    33% { transform: translateY(-12px) rotate(1deg); }
    66% { transform: translateY(6px) rotate(-1deg); }
}
@keyframes homePulseGlow {
    0%, 100% { box-shadow: 0 0 20px rgba(37,99,235,.3); }
    50% { box-shadow: 0 0 40px rgba(37,99,235,.5); }
}
@keyframes homeShimmer {
    0% { background-position: -200% center; }
    100% { background-position: 200% center; }
}
@keyframes homeScaleIn {
    from { opacity: 0; transform: scale(.85); }
    to { opacity: 1; transform: scale(1); }
}

/* ── Hero ── */
.hm-hero {
    position: relative;
    background: linear-gradient(135deg, #0b1120 0%, #0f3460 40%, #1e3a8a 70%, #312e81 100%);
    padding: 7rem 0 8rem;
    overflow: hidden;
    margin-top: calc(-2rem);
    border-radius: 0 0 48px 48px;
}

.hm-hero-particles {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
}

.hm-hero-particle {
    position: absolute;
    border-radius: 50%;
    opacity: .04;
}

.hm-hero-particle-1 {
    width: 600px; height: 600px;
    background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
    top: -250px; right: -150px;
    animation: homeFloat 12s ease-in-out infinite;
}

.hm-hero-particle-2 {
    width: 400px; height: 400px;
    background: radial-gradient(circle, #06b6d4 0%, transparent 70%);
    bottom: -180px; left: -120px;
    animation: homeFloat 15s ease-in-out infinite reverse;
}

.hm-hero-particle-3 {
    width: 200px; height: 200px;
    background: radial-gradient(circle, #8b5cf6 0%, transparent 70%);
    top: 20%; left: 15%;
    animation: homeFloat 10s ease-in-out infinite 2s;
}

.hm-hero-grid {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px);
    background-size: 60px 60px;
    mask-image: radial-gradient(ellipse 80% 60% at 50% 50%, black 30%, transparent 70%);
    -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 50%, black 30%, transparent 70%);
    pointer-events: none;
}

.hm-hero-content { position: relative; z-index: 2; }

.hm-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(6,182,212,.12);
    border: 1px solid rgba(6,182,212,.2);
    color: #22d3ee;
    padding: .4rem 1.1rem .4rem .5rem;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 1.75rem;
    animation: homeFadeUp .6s ease both;
    backdrop-filter: blur(10px);
}

.hm-hero-badge-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #22d3ee;
    margin-right: 2px;
    animation: homePulseGlow 2s infinite;
}

.hm-hero h1 {
    font-size: 3.2rem;
    font-weight: 900;
    letter-spacing: -.035em;
    line-height: 1.12;
    color: #fff;
    margin: 0 0 1rem;
    animation: homeFadeUp .6s ease both .1s;
}

.hm-hero h1 .hm-gradient-text {
    background: linear-gradient(135deg, #60a5fa, #22d3ee, #818cf8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% auto;
    animation: homeShimmer 4s linear infinite;
}

.hm-hero-sub {
    font-size: 1.1rem;
    color: rgba(255,255,255,.55);
    max-width: 530px;
    line-height: 1.75;
    margin: 0 0 2rem;
    animation: homeFadeUp .6s ease both .2s;
}

.hm-hero-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    animation: homeFadeUp .6s ease both .3s;
}

.hm-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff;
    padding: .85rem 2.2rem;
    border-radius: 999px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 8px 32px rgba(37,99,235,.35);
    transition: var(--home-transition);
    border: none;
    cursor: pointer;
}

.hm-btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 40px rgba(37,99,235,.5);
    color: #fff;
}

.hm-btn-primary i { font-size: 14px; transition: transform .3s ease; }
.hm-btn-primary:hover i { transform: translateX(4px); }

.hm-btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1.5px solid rgba(255,255,255,.25);
    color: rgba(255,255,255,.85);
    padding: .85rem 2rem;
    border-radius: 999px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--home-transition);
    backdrop-filter: blur(10px);
}

.hm-btn-ghost:hover {
    border-color: rgba(255,255,255,.5);
    color: #fff;
    background: rgba(255,255,255,.06);
}

.hm-hero-stats {
    display: flex;
    gap: 2.5rem;
    margin-top: 3.5rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255,255,255,.06);
    animation: homeFadeUp .6s ease both .4s;
}

.hm-hero-stat h3 {
    font-size: 1.8rem;
    font-weight: 800;
    color: #fff;
    margin: 0;
    line-height: 1;
}

.hm-hero-stat h3 .hm-stat-plus {
    font-size: 1.2rem;
    color: #22d3ee;
}

.hm-hero-stat span {
    font-size: 13px;
    color: rgba(255,255,255,.4);
    display: block;
    margin-top: 4px;
}

/* ── Sections ── */
.hm-section { padding: 5rem 0; }
.hm-section-alt { background: #f8fafc; }
.hm-section-dark {
    background: linear-gradient(135deg, #0b1120, #0f3460);
    color: #fff;
}

.hm-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--home-primary);
    background: rgba(37,99,235,.08);
    padding: .35rem 1rem;
    border-radius: 999px;
    margin-bottom: .75rem;
}

.hm-label i { font-size: 11px; }

.hm-title {
    font-size: 2.2rem;
    font-weight: 800;
    letter-spacing: -.025em;
    line-height: 1.2;
    margin: 0 0 .75rem;
    color: var(--home-text);
}

.hm-title-center { text-align: center; }
.hm-title-light { color: #fff; }

.hm-subtitle {
    font-size: 1rem;
    color: var(--home-text-muted);
    line-height: 1.7;
    margin: 0 0 2.5rem;
    max-width: 580px;
}

.hm-subtitle-center { text-align: center; margin-left: auto; margin-right: auto; }
.hm-subtitle-light { color: rgba(255,255,255,.5); }

/* ── Feature Cards ── */
.hm-features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.hm-feature-card {
    background: var(--home-surface);
    border-radius: var(--home-radius);
    padding: 2rem 1.75rem;
    box-shadow: var(--home-shadow);
    border: 1px solid var(--home-border);
    transition: var(--home-transition);
    position: relative;
    overflow: hidden;
}

.hm-feature-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--fc-color, #3b82f6), var(--fc-color2, #60a5fa));
    opacity: 0;
    transition: opacity .4s ease;
}

.hm-feature-card:hover::before { opacity: 1; }

.hm-feature-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--home-shadow-hover);
    border-color: transparent;
}

.hm-feature-icon {
    width: 56px; height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 1.25rem;
    transition: transform .3s ease;
}

.hm-feature-card:hover .hm-feature-icon { transform: scale(1.08) rotate(-3deg); }

.hm-feature-card h5 {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--home-text);
    margin: 0 0 .5rem;
}

.hm-feature-card p {
    font-size: 14px;
    color: var(--home-text-muted);
    line-height: 1.65;
    margin: 0;
}

/* ── Subjects Section ── */
.hm-subjects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1rem;
}

.hm-subject-group { margin-bottom: 2.5rem; }
.hm-subject-group:last-child { margin-bottom: 0; }

.hm-subject-group-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--home-text);
    margin: 0 0 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.hm-subject-group-title .hm-sg-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
}

.hm-subject-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--home-surface);
    padding: .9rem 1.1rem;
    border-radius: var(--home-radius-sm);
    box-shadow: 0 1px 8px rgba(15,23,42,.03);
    border: 1px solid var(--home-border);
    text-decoration: none;
    color: inherit;
    transition: var(--home-transition);
}

.hm-subject-card:hover {
    transform: translateX(6px);
    border-color: #bfdbfe;
    box-shadow: 0 6px 20px rgba(37,99,235,.08);
}

.hm-subject-card-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 16px;
    flex-shrink: 0;
}

.hm-subject-card-title {
    flex: 1;
    font-size: 14px;
    font-weight: 600;
    color: var(--home-text);
}

.hm-subject-card-arrow {
    color: #94a3b8;
    font-size: 12px;
    transition: transform .3s ease;
}

.hm-subject-card:hover .hm-subject-card-arrow { transform: translateX(4px); color: var(--home-primary); }

/* ── About ── */
.hm-about-img-wrap {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(15,23,42,.15);
}

.hm-about-img {
    width: 100%;
    height: 420px;
    object-fit: cover;
    display: block;
}

.hm-about-img-badge {
    position: absolute;
    bottom: 1.5rem; left: 1.5rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(0,0,0,.5);
    backdrop-filter: blur(16px);
    color: #fff;
    padding: .6rem 1.2rem;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid rgba(255,255,255,.1);
}

.hm-about-img-badge i { color: #22d3ee; font-size: 16px; }

.hm-check-list {
    list-style: none;
    padding: 0;
    margin: 1.5rem 0 0;
}

.hm-check-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: .55rem 0;
    font-size: 15px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
}

.hm-check-list li:last-child { border-bottom: none; }

.hm-check-list li i {
    color: #22c55e;
    font-size: 17px;
    flex-shrink: 0;
}

/* ── Steps ── */
.hm-steps-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    counter-reset: step;
}

.hm-step {
    text-align: center;
    padding: 2rem 1.5rem;
    position: relative;
}

.hm-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 28px;
    left: 65%;
    width: 70%;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6, #e2e8f0);
    pointer-events: none;
}

.hm-step-num {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff;
    font-size: 1.3rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    box-shadow: 0 8px 24px rgba(37,99,235,.3);
    position: relative;
    transition: var(--home-transition);
}

.hm-step:hover .hm-step-num {
    transform: scale(1.1);
    box-shadow: 0 12px 32px rgba(37,99,235,.4);
}

.hm-step h5 {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--home-text);
    margin: 0 0 .4rem;
}

.hm-step p {
    color: var(--home-text-muted);
    font-size: 14px;
    margin: 0;
}

/* ── Live Section ── */
.hm-lives-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

.hm-live-card {
    background: var(--home-surface);
    border-radius: var(--home-radius);
    padding: 1.5rem;
    box-shadow: var(--home-shadow);
    border: 1px solid var(--home-border);
    transition: var(--home-transition);
}

.hm-live-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--home-shadow-hover);
    border-color: transparent;
}

.hm-live-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.hm-live-badge {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    padding: 4px 12px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.hm-live-badge-live {
    color: #dc2626;
    background: #fef2f2;
}

.hm-live-badge-live .hm-live-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
    background: #dc2626;
    animation: homePulseGlow 1.5s infinite;
}

.hm-live-badge-scheduled {
    color: #d97706;
    background: #fffbeb;
}

.hm-live-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--home-text);
    margin: 0 0 .35rem;
    line-height: 1.3;
}

.hm-live-desc {
    font-size: 13px;
    color: var(--home-text-muted);
    line-height: 1.5;
    margin: 0 0 .75rem;
}

.hm-live-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.hm-live-tag {
    font-size: 11px;
    color: #64748b;
    background: #f8fafc;
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.hm-live-tag i { font-size: 9px; }

.hm-live-card:hover .hm-live-arrow {
    color: var(--home-primary);
    transform: translateX(4px);
}

/* ── CTA ── */
.hm-cta {
    background: linear-gradient(135deg, #0b1120 0%, #0f3460 50%, #312e81 100%);
    border-radius: 28px;
    padding: 4rem 3rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.hm-cta::before {
    content: '';
    position: absolute;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(59,130,246,.08) 0%, transparent 70%);
    top: -150px; right: -100px;
}

.hm-cta::after {
    content: '';
    position: absolute;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(6,182,212,.06) 0%, transparent 70%);
    bottom: -120px; left: -80px;
}

.hm-cta-content { position: relative; z-index: 1; }

.hm-cta h2 {
    font-size: 2.2rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 .75rem;
    letter-spacing: -.02em;
}

.hm-cta p {
    color: rgba(255,255,255,.55);
    font-size: 1.05rem;
    margin: 0 0 2rem;
}

.hm-btn-cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    color: #1e3a8a;
    padding: .9rem 2.5rem;
    border-radius: 999px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 8px 32px rgba(0,0,0,.2);
    transition: var(--home-transition);
}

.hm-btn-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 40px rgba(0,0,0,.3);
    color: #1e3a8a;
}

/* ── Responsive ── */
@media(max-width: 992px) {
    .hm-features-grid { grid-template-columns: repeat(2, 1fr); }
    .hm-steps-grid { grid-template-columns: repeat(2, 1fr); }
    .hm-lives-grid { grid-template-columns: repeat(2, 1fr); }
    .hm-step:not(:last-child)::after { display: none; }
}

@media(max-width: 768px) {
    .hm-hero { padding: 5rem 0 5rem; border-radius: 0 0 32px 32px; }
    .hm-hero h1 { font-size: 2rem; }
    .hm-hero-sub { font-size: 1rem; }
    .hm-hero-stats { gap: 1.5rem; flex-wrap: wrap; }
    .hm-hero-stat h3 { font-size: 1.4rem; }
    .hm-hero-actions { flex-direction: column; }
    .hm-btn-primary, .hm-btn-ghost { justify-content: center; }
    .hm-section { padding: 3rem 0; }
    .hm-title { font-size: 1.6rem; }
    .hm-features-grid, .hm-steps-grid, .hm-lives-grid { grid-template-columns: 1fr; }
    .hm-subjects-grid { grid-template-columns: 1fr; }
    .hm-cta { padding: 2.5rem 1.5rem; border-radius: 20px; }
    .hm-cta h2 { font-size: 1.5rem; }
    .hm-about-img { height: 260px; }
}

@media(max-width: 480px) {
    .hm-hero h1 { font-size: 1.6rem; }
}
</style>

{{-- ═══════ HERO ═══════ --}}
<section class="hm-hero">
    <div class="hm-hero-particles">
        <div class="hm-hero-particle hm-hero-particle-1"></div>
        <div class="hm-hero-particle hm-hero-particle-2"></div>
        <div class="hm-hero-particle hm-hero-particle-3"></div>
    </div>
    <div class="hm-hero-grid"></div>
    <div class="container hm-hero-content">
        <div class="hm-hero-badge">
            <span class="hm-hero-badge-dot"></span>
            Plateforme éducative #1
        </div>
        <h1>
            La plateforme intelligente<br>
            pour <span class="hm-gradient-text">réussir vos études</span>
        </h1>
        <p class="hm-hero-sub">
            Cours interactifs, sessions live en direct et ressources pédagogiques premium accessibles partout, à tout moment.
        </p>
        <div class="hm-hero-actions">
            <a href="{{ route('front.classes') }}" class="hm-btn-primary">
                🚀 Explorer les cours <i class="bi bi-arrow-right"></i>
            </a>
            <a href="{{ route('login') }}" class="hm-btn-ghost">
                <i class="bi bi-box-arrow-in-right"></i> Se connecter
            </a>
        </div>
        <div class="hm-hero-stats">
            <div class="hm-hero-stat">
                <h3>{{ $classes }}+</h3>
                <span>Étudiants actifs</span>
            </div>
            <div class="hm-hero-stat">
                <h3>{{ $courses }}+</h3>
                <span>Cours disponibles</span>
            </div>
            <div class="hm-hero-stat">
                <h3>120+</h3>
                <span>Lives organisés</span>
            </div>
            <div class="hm-hero-stat">
                <h3>95%</h3>
                <span>Satisfaction</span>
            </div>
        </div>
    </div>
</section>

{{-- ═══════ FEATURES ═══════ --}}
<section class="hm-section">
    <div class="container">
        <div class="hm-label" style="display:inline-flex;"><i class="bi bi-lightning-fill"></i> Pourquoi nous ?</div>
        <h2 class="hm-title">Tout ce dont vous avez besoin<br>pour réussir</h2>
        <p class="hm-subtitle">Une plateforme complète avec des fonctionnalités conçues pour maximiser votre apprentissage.</p>
        <div class="hm-features-grid">
            <div class="hm-feature-card" style="--fc-color: #3b82f6; --fc-color2: #60a5fa;">
                <div class="hm-feature-icon" style="background:#eff6ff; color:#3b82f6;"><i class="bi bi-laptop"></i></div>
                <h5>Interface moderne</h5>
                <p>Simple et intuitive, notre plateforme vous offre une expérience d'apprentissage fluide et agréable.</p>
            </div>
            <div class="hm-feature-card" style="--fc-color: #22c55e; --fc-color2: #4ade80;">
                <div class="hm-feature-icon" style="background:#f0fdf4; color:#22c55e;"><i class="bi bi-broadcast"></i></div>
                <h5>Lives interactifs</h5>
                <p>Cours en direct avec interaction en temps réel avec vos enseignants qualifiés.</p>
            </div>
            <div class="hm-feature-card" style="--fc-color: #f59e0b; --fc-color2: #fbbf24;">
                <div class="hm-feature-icon" style="background:#fffbeb; color:#f59e0b;"><i class="bi bi-cloud-arrow-down"></i></div>
                <h5>Supports PDF</h5>
                <p>Téléchargez facilement tous vos supports de cours en format PDF pour réviser hors ligne.</p>
            </div>
            <div class="hm-feature-card" style="--fc-color: #8b5cf6; --fc-color2: #a78bfa;">
                <div class="hm-feature-icon" style="background:#f5f3ff; color:#8b5cf6;"><i class="bi bi-people"></i></div>
                <h5>Suivi personnalisé</h5>
                <p>Un suivi individuel adapté à votre rythme d'apprentissage et à vos objectifs.</p>
            </div>
            <div class="hm-feature-card" style="--fc-color: #06b6d4; --fc-color2: #22d3ee;">
                <div class="hm-feature-icon" style="background:#ecfeff; color:#06b6d4;"><i class="bi bi-phone"></i></div>
                <h5>Accessible partout</h5>
                <p>Apprenez depuis n'importe quel appareil, où que vous soyez, à tout moment.</p>
            </div>
            <div class="hm-feature-card" style="--fc-color: #f43f5e; --fc-color2: #fb7185;">
                <div class="hm-feature-icon" style="background:#fff1f2; color:#f43f5e;"><i class="bi bi-star"></i></div>
                <h5>Évaluations continues</h5>
                <p>Des tests et évaluations réguliers pour mesurer votre progression et consolider vos acquis.</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══════ SUBJECTS ═══════ --}}
@if(isset($subjectsGrouped) && count($subjectsGrouped))
<section class="hm-section hm-section-alt">
    <div class="container">
        <div class="text-center">
            <div class="hm-label" style="display:inline-flex;"><i class="bi bi-book-fill"></i> Nos matières</div>
            <h2 class="hm-title hm-title-center">Explorez nos <span style="color:var(--home-primary);">matières</span></h2>
            <p class="hm-subtitle hm-subtitle-center">Des matières variées pour tous les niveaux, enseignées par des professeurs expérimentés.</p>
        </div>
        <div class="hm-subjects-grid">
            @foreach($subjectsGrouped as $groupName => $group)
                @if(isset($group['subjects']) && $group['subjects']->count())
                    @foreach($group['subjects'] as $subject)
                        @php
                            $subjectIcons = ['bi-book','bi-cpu','bi-calculator','bi-globe2','bi-flask','bi-music-note','bi-palette','bi-translate','bi-activity','bi-star'];
                            $subjectColors = ['#2563eb','#059669','#7c3aed','#d97706','#dc2626','#0891b2','#db2777','#4f46e5','#0d9488','#ea580c'];
                            $si = $loop->index % count($subjectIcons);
                        @endphp
                        <a href="{{ route('front.subject.classes', $subject->id) }}" class="hm-subject-card">
                            <div class="hm-subject-card-icon" style="background: {{ $subjectColors[$si] }};">
                                <i class="bi {{ $subjectIcons[$si] }}"></i>
                            </div>
                            <span class="hm-subject-card-title">{{ $subject->name }}</span>
                            <i class="bi bi-chevron-right hm-subject-card-arrow"></i>
                        </a>
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════ ABOUT ═══════ --}}
<section class="hm-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hm-about-img-wrap">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=700&q=85" alt="Étudiants" class="hm-about-img">
                    <div class="hm-about-img-badge">
                        <i class="bi bi-check-circle-fill"></i>
                        +{{ $classes }} étudiants inscrits
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hm-label"><i class="bi bi-info-circle"></i> À propos</div>
                <h2 class="hm-title">Qui <span style="color:var(--home-primary);">sommes-nous</span> ?</h2>
                <p style="color: #475569; font-size: 15px; line-height: 1.8;">
                    E-School connecte les étudiants avec des enseignants qualifiés pour une expérience d'apprentissage enrichissante et accessible à tous. Notre mission est de rendre l'éducation de qualité accessible partout.
                </p>
                <ul class="hm-check-list">
                    <li><i class="bi bi-check-circle-fill"></i> Enseignants qualifiés et expérimentés</li>
                    <li><i class="bi bi-check-circle-fill"></i> Plateforme interactive et intuitive</li>
                    <li><i class="bi bi-check-circle-fill"></i> Apprentissage flexible, à votre rythme</li>
                    <li><i class="bi bi-check-circle-fill"></i> Support technique disponible 7j/7</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ═══════ HOW IT WORKS ═══════ --}}
<section class="hm-section hm-section-alt">
    <div class="container">
        <div class="text-center">
            <div class="hm-label" style="display:inline-flex;"><i class="bi bi-gear-fill"></i> Fonctionnement</div>
            <h2 class="hm-title hm-title-center">Comment <span style="color:var(--home-primary);">ça marche</span> ?</h2>
            <p class="hm-subtitle hm-subtitle-center">Trois étapes simples pour commencer votre parcours d'apprentissage.</p>
        </div>
        <div class="hm-steps-grid">
            <div class="hm-step">
                <div class="hm-step-num">1</div>
                <h5>Créer un compte</h5>
                <p>Inscrivez-vous gratuitement en quelques secondes et créez votre profil étudiant.</p>
            </div>
            <div class="hm-step">
                <div class="hm-step-num">2</div>
                <h5>Choisir votre niveau</h5>
                <p>Sélectionnez votre niveau scolaire et les matières qui vous intéressent.</p>
            </div>
            <div class="hm-step">
                <div class="hm-step-num">3</div>
                <h5>Commencer à apprendre</h5>
                <p>Accédez à tous les cours, lives et ressources pour progresser à votre rythme.</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══════ LIVES ═══════ --}}
@if(isset($lives) && $lives->count())
<section class="hm-section">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <div class="hm-label"><i class="bi bi-broadcast"></i> En direct</div>
                <h2 class="hm-title" style="margin-bottom:0;">Sessions <span style="color:var(--home-primary);">live</span></h2>
            </div>
            @if($lives->count() > 3)
                <a href="{{ route('front.lives') }}" class="hm-btn-primary" style="padding:.6rem 1.5rem;font-size:13px;">
                    Voir tout <i class="bi bi-arrow-right"></i>
                </a>
            @endif
        </div>
        <p class="hm-subtitle" style="margin-bottom:1.5rem;">Participez à nos sessions live interactives avec des professeurs en temps réel.</p>
        <div class="hm-lives-grid">
            @foreach($lives as $live)
                <div class="hm-live-card">
                    <div class="hm-live-top">
                        <span class="hm-live-badge {{ $live->is_live ?? false ? 'hm-live-badge-live' : 'hm-live-badge-scheduled' }}">
                            @if($live->is_live ?? false)
                                <span class="hm-live-dot"></span> EN DIRECT
                            @else
                                <i class="bi bi-calendar"></i> Planifié
                            @endif
                        </span>
                        <i class="bi bi-arrow-right hm-live-arrow"></i>
                    </div>
                    <h4 class="hm-live-title">{{ $live->title ?? $live->name ?? 'Session Live' }}</h4>
                    <p class="hm-live-desc">{{ Str::limit($live->description ?? 'Rejoignez cette session interactive.', 60) }}</p>
                    <div class="hm-live-meta">
                        @if(isset($live->subject))
                            <span class="hm-live-tag"><i class="bi bi-book"></i> {{ $live->subject->name ?? '' }}</span>
                        @endif
                        @if(isset($live->teacher))
                            <span class="hm-live-tag"><i class="bi bi-person"></i> {{ $live->teacher->name ?? '' }}</span>
                        @endif
                        @if(isset($live->scheduled_at))
                            <span class="hm-live-tag"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($live->scheduled_at)->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════ CTA ═══════ --}}
<section class="hm-section">
    <div class="container">
        <div class="hm-cta">
            <div class="hm-cta-content">
                <h2>Prêt à transformer votre<br>parcours éducatif ?</h2>
                <p>Rejoignez des milliers d'étudiants qui apprennent déjà sur E-School</p>
                <a href="{{ route('register') }}" class="hm-btn-cta">
                    <i class="bi bi-person-plus-fill"></i> S'inscrire maintenant
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
