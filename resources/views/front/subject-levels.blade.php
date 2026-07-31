@extends('layouts.front')

@section('title', $subject->name)

@push('head')
<style>
/* ── Hero section ── */
.subject-hero {
    position: relative;
    padding: 1.9rem 0 1.8rem;
    overflow: hidden;
}
.subject-hero-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #0a1628 0%, #1a1040 50%, #0f2027 100%);
    z-index: 0;
}
.subject-hero-bg::before {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(124,58,237,0.12), transparent 70%);
    top: -150px;
    right: -100px;
    animation: heroDrift 12s ease-in-out infinite;
}
.subject-hero-bg::after {
    content: '';
    position: absolute;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(2,132,199,0.1), transparent 70%);
    bottom: -100px;
    left: -80px;
    animation: heroDrift 15s ease-in-out infinite reverse;
}
@keyframes heroDrift {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(40px, -30px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.95); }
}

/* ── Level cards ── */
.level-card-3d {
    background: rgba(255,255,255,0.04);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 17px;
    padding: 2rem 1.5rem;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    text-decoration: none;
    display: block;
    height: 100%;
    position: relative;
    overflow: hidden;
}
.level-card-3d::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.04), transparent);
    transition: left 0.6s;
}
.level-card-3d:hover::before {
    left: 100%;
}
.level-card-3d:hover {
    transform: translateY(-8px) scale(1.02);
    border-color: rgba(124,58,237,0.2);
    box-shadow: 0 20px 50px rgba(0,0,0,0.2);
}
.level-card-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.4rem;
    transition: all 0.3s ease;
}
.level-card-3d:hover .level-card-icon {
    transform: scale(1.1) rotate(-6deg);
}

/* ── Features list ── */
.feature-3d-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.feature-3d-item:last-child {
    border-bottom: none;
}
.feature-3d-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.84rem;
    flex-shrink: 0;
}

/* ── Stats row ── */
.stat-3d-item {
    text-align: center;
    padding: 0.75rem;
}
.stat-3d-value {
    font-size: 1.8rem;
    font-weight: 800;
    line-height: 1.2;
}
.stat-3d-label {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.4);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* ── Gradient text ── */
.gradient-text {
    background: linear-gradient(135deg, #A78BFA, #38BDF8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* ── Introduction des niveaux dans le hero ── */
.subject-hero-main-row {
    min-height: 205px;
}

.subject-levels-intro {
    position: relative;
    overflow: hidden;
    min-height: 145px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 1rem 1.25rem;
    border: 1px solid rgba(167, 139, 250, 0.18);
    border-radius: 20px;
    background:
        linear-gradient(
            135deg,
            rgba(124, 58, 237, 0.14),
            rgba(6, 182, 212, 0.06)
        );
    box-shadow: 0 22px 55px rgba(0, 0, 0, 0.16);
    backdrop-filter: blur(12px);
}

.subject-levels-intro::before {
    content: '';
    position: absolute;
    width: 110px;
    height: 110px;
    top: -50px;
    right: -30px;
    border-radius: 50%;
    background: radial-gradient(
        circle,
        rgba(167, 139, 250, 0.2),
        transparent 68%
    );
    pointer-events: none;
}

.subject-levels-intro::after {
    content: '\F52A';
    position: absolute;
    right: 14px;
    bottom: 7px;
    font-family: 'bootstrap-icons';
    font-size: 3.4rem;
    line-height: 1;
    color: rgba(255, 255, 255, 0.035);
    pointer-events: none;
}

.subject-levels-intro-content {
    position: relative;
    z-index: 1;
    max-width: 470px;
}

.subject-levels-intro .levels-intro-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    width: fit-content;
    margin-bottom: 0.55rem;
    padding: 5px 10px;
    border: 1px solid rgba(74, 222, 128, 0.17);
    border-radius: 999px;
    background: rgba(34, 197, 94, 0.11);
    color: #4ADE80;
    font-size: 0.7rem;
    font-weight: 650;
}

.subject-levels-intro h2 {
    margin: 0;
    color: rgba(255, 255, 255, 0.96);
    font-family: 'Poppins', sans-serif;
    font-size: clamp(1.35rem, 2.2vw, 1.72rem);
    font-weight: 800;
    line-height: 1.18;
}

.subject-levels-intro p {
    max-width: 440px;
    margin: 0.5rem 0 0;
    color: rgba(255, 255, 255, 0.52);
    font-size: 0.9rem;
    line-height: 1.45;
}

.levels-cards-section {
    padding-top: 3rem;
    padding-bottom: 3rem;
    background: rgba(255, 255, 255, 0.01);
}

@media (max-width: 991.98px) {
    .subject-hero {
        padding: 2rem 0 1.8rem;
    }

    .subject-hero-main-row {
        min-height: auto;
    }

    .subject-levels-intro {
        min-height: auto;
        margin-top: 0.9rem;
    }
}

@media (max-width: 575.98px) {
    .subject-levels-intro {
        padding: 1.05rem;
        border-radius: 19px;
    }

    .subject-levels-intro::after {
        right: 14px;
        bottom: 12px;
        font-size: 4rem;
    }

    .levels-cards-section {
        padding-top: 2rem;
    }
}

/* ── Arbre interactif horizontal : niveau → classes ── */
.level-tree-wrapper {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    transition: max-width 0.4s ease;
}

.level-tree-wrapper.is-open {
    max-width: 1080px;
}

.level-tree-card {
    position: relative;
    display: grid;
    grid-template-columns: minmax(0, 1fr) 0fr;
    overflow: hidden;
    min-height: 245px;
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 21px;
    background: rgba(255, 255, 255, 0.035);
    backdrop-filter: blur(12px);
    transition:
        grid-template-columns 0.42s ease,
        border-color 0.28s ease,
        box-shadow 0.28s ease;
}

.level-tree-card.is-open {
    grid-template-columns:
        minmax(300px, 370px)
        minmax(460px, 1fr);
    border-color: rgba(167, 139, 250, 0.28);
    box-shadow: 0 22px 55px rgba(0, 0, 0, 0.22);
}

.level-tree-toggle {
    position: relative;
    z-index: 1;
    width: 100%;
    min-width: 0;
    min-height: 245px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.7rem 1.35rem;
    color: inherit;
    background: transparent;
    border: 0;
    cursor: pointer;
    text-align: center;
}

.level-tree-toggle::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: -1;
    opacity: 0;
    background:
        linear-gradient(
            135deg,
            rgba(124, 58, 237, 0.08),
            rgba(6, 182, 212, 0.035)
        );
    transition: opacity 0.28s ease;
}

.level-tree-toggle:hover::before,
.level-tree-card.is-open .level-tree-toggle::before {
    opacity: 1;
}

.level-tree-toggle:hover .level-card-icon {
    transform: scale(1.08) rotate(-5deg);
}

.level-tree-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 0.9rem;
    padding: 7px 11px;
    border-radius: 999px;
    background: rgba(124, 58, 237, 0.11);
    color: #C4B5FD;
    font-size: 0.76rem;
    font-weight: 700;
}

.level-tree-chevron {
    transition: transform 0.28s ease;
}

.level-tree-toggle[aria-expanded="true"] .level-tree-chevron {
    transform: rotate(180deg);
}

.level-classes-tree {
    min-width: 0;
    overflow: hidden;
    opacity: 0;
    padding: 0;
    border-left: 1px solid transparent;
    transform: translateX(18px);
    pointer-events: none;
    transition:
        opacity 0.28s ease,
        padding 0.38s ease,
        transform 0.38s ease,
        border-color 0.28s ease;
}

.level-classes-tree.is-open {
    opacity: 1;
    padding: 1.25rem 1.35rem;
    border-left-color: rgba(255, 255, 255, 0.07);
    transform: translateX(0);
    pointer-events: auto;
}

.level-classes-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 1rem;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.82rem;
    font-weight: 800;
}

.level-classes-title i {
    color: #A78BFA;
}

.class-tree-list {
    position: relative;
    display: grid;
    gap: 11px;
    margin: 0;
    padding: 0 0 0 27px;
    list-style: none;
}

.class-tree-list::before {
    content: '';
    position: absolute;
    top: 17px;
    bottom: 17px;
    left: 10px;
    width: 2px;
    border-radius: 2px;
    background:
        linear-gradient(
            to bottom,
            rgba(167, 139, 250, 0.72),
            rgba(56, 189, 248, 0.2)
        );
}

.class-tree-item {
    position: relative;
}

.class-tree-item::before {
    content: '';
    position: absolute;
    top: 50%;
    left: -17px;
    width: 17px;
    height: 2px;
    background: rgba(167, 139, 250, 0.45);
    transform: translateY(-50%);
}

.class-tree-item::after {
    content: '';
    position: absolute;
    top: 50%;
    left: -21px;
    width: 9px;
    height: 9px;
    border: 2px solid rgba(196, 181, 253, 0.9);
    border-radius: 50%;
    background: #11182e;
    transform: translateY(-50%);
}

.class-tree-link {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 62px;
    padding: 10px 12px;
    color: inherit;
    text-decoration: none;
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.028);
    transition:
        transform 0.22s ease,
        border-color 0.22s ease,
        background 0.22s ease;
}

.class-tree-link:hover {
    color: inherit;
    text-decoration: none;
    transform: translateX(5px);
    border-color: rgba(167, 139, 250, 0.24);
    background: rgba(124, 58, 237, 0.08);
}

.class-tree-icon {
    width: 41px;
    height: 41px;
    flex: 0 0 41px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    color: #ffffff;
    font-size: 0.96rem;
}

.class-tree-content {
    min-width: 0;
    flex: 1;
    text-align: left;
}

.class-tree-name {
    display: block;
    margin-bottom: 3px;
    color: rgba(255, 255, 255, 0.94);
    font-size: 0.9rem;
    font-weight: 800;
}

.class-tree-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: rgba(255, 255, 255, 0.46);
    font-size: 0.7rem;
}

.class-tree-status.test {
    color: #C4B5FD;
}

.class-tree-status.no-test {
    color: #86EFAC;
}

.class-tree-arrow {
    color: rgba(255, 255, 255, 0.24);
    transition:
        transform 0.22s ease,
        color 0.22s ease;
}

.class-tree-link:hover .class-tree-arrow {
    color: #C4B5FD;
    transform: translateX(3px);
}

.level-tree-empty {
    margin: 0;
    padding: 1rem;
    color: rgba(255, 255, 255, 0.42);
    border: 1px dashed rgba(255, 255, 255, 0.1);
    border-radius: 13px;
    font-size: 0.8rem;
    text-align: center;
}

@media (max-width: 991.98px) {
    .level-tree-wrapper,
    .level-tree-wrapper.is-open {
        max-width: 760px;
    }

    .level-tree-card,
    .level-tree-card.is-open {
        display: block;
        min-height: auto;
    }

    .level-tree-toggle {
        min-height: 215px;
        padding: 1.45rem 1.1rem;
    }

    .level-classes-tree {
        max-height: 0;
        opacity: 0;
        border-top: 1px solid transparent;
        border-left: 0;
        transform: translateY(-10px);
    }

    .level-classes-tree.is-open {
        max-height: 700px;
        padding: 1rem 1.1rem 1.25rem;
        border-top-color: rgba(255, 255, 255, 0.07);
        transform: translateY(0);
    }
}

@media (max-width: 575.98px) {
    .level-tree-toggle {
        min-height: 195px;
    }

    .class-tree-list {
        padding-left: 23px;
    }

    .class-tree-link {
        min-height: 58px;
        padding: 9px 10px;
    }
}

/* ═══ LIGHT MODE ═══ */

html.light-mode .subject-hero-bg {
    background: linear-gradient(135deg, #f0f4ff 0%, #f5f0ff 50%, #f0f7fa 100%);
}
html.light-mode .subject-hero-bg::before {
    background: radial-gradient(circle, rgba(124,58,237,0.06), transparent 70%);
}
html.light-mode .subject-hero-bg::after {
    background: radial-gradient(circle, rgba(2,132,199,0.05), transparent 70%);
}
html.light-mode .subject-hero h1 {
    color: #1e293b !important;
}
html.light-mode .subject-hero .text-white-50 {
    color: #64748b !important;
}

html.light-mode .subject-levels-intro {
    background:
        linear-gradient(
            135deg,
            rgba(255, 255, 255, 0.82),
            rgba(245, 243, 255, 0.78)
        );
    border-color: rgba(124, 58, 237, 0.12);
    box-shadow: 0 22px 55px rgba(71, 85, 105, 0.1);
}

html.light-mode .subject-levels-intro h2 {
    color: #1e293b;
}

html.light-mode .subject-levels-intro p {
    color: #64748b;
}

html.light-mode .subject-levels-intro::after {
    color: rgba(79, 70, 229, 0.055);
}
html.light-mode .stat-3d-value {
    color: #1e293b !important;
}
html.light-mode .stat-3d-label {
    color: #94a3b8 !important;
}
html.light-mode .level-card-3d {
    background: rgba(255,255,255,0.85);
    border-color: rgba(0,0,0,0.08);
}
html.light-mode .level-card-3d:hover {
    border-color: rgba(124,58,237,0.2);
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
}
html.light-mode .level-card-3d h5 {
    color: #1e293b !important;
}
html.light-mode .level-card-3d .text-white-50 {
    color: #64748b !important;
}

html.light-mode .level-tree-card {
    background: rgba(255, 255, 255, 0.86);
    border-color: rgba(15, 23, 42, 0.08);
}

html.light-mode .level-tree-card.is-open {
    border-color: rgba(124, 58, 237, 0.2);
    box-shadow: 0 20px 45px rgba(71, 85, 105, 0.12);
}

html.light-mode .class-tree-link {
    background: rgba(248, 250, 252, 0.86);
    border-color: rgba(15, 23, 42, 0.08);
}

html.light-mode .class-tree-link:hover {
    background: rgba(124, 58, 237, 0.06);
}

html.light-mode .class-tree-name {
    color: #1E293B;
}

html.light-mode .class-tree-status {
    color: #64748B;
}

html.light-mode .class-tree-item::after {
    background: #F8FAFC;
}
html.light-mode .feature-3d-item {
    border-color: rgba(0,0,0,0.06);
}
html.light-mode .feature-3d-item div:last-child {
    color: #475569 !important;
}
html.light-mode .feature-3d-item small {
    color: #94a3b8 !important;
}
html.light-mode .section-title-3d {
    color: #1e293b !important;
}
html.light-mode .text-white-50 {
    color: #64748b !important;
}
html.light-mode .card-3d {
    background: rgba(255,255,255,0.85);
    border-color: rgba(0,0,0,0.08);
}
html.light-mode .card-3d h5.fw-bold {
    color: #1e293b !important;
}
html.light-mode .card-3d p {
    color: #64748b !important;
}
html.light-mode .card-3d .text-white-50 {
    color: #94a3b8 !important;
}
</style>
@endpush

@section('content')

@php
    $isHighSchoolSupport =
        (bool) ($subject->is_high_school_support ?? false);

    $childPluralLabel = $isHighSchoolSupport
        ? 'Matières'
        : 'Classes';

    $openLabel = $isHighSchoolSupport
        ? 'Afficher les matières'
        : 'Afficher les classes';

    $closeLabel = $isHighSchoolSupport
        ? 'Masquer les matières'
        : 'Masquer les classes';
@endphp

<!-- ═══ HERO SECTION ═══ -->
<section class="subject-hero">
    <div class="subject-hero-bg"></div>
    <div class="container position-relative" style="z-index:1;">
        <div class="row align-items-center g-4 subject-hero-main-row">
            <div class="col-lg-6">
                <!-- Précédent -->
                <a href="{{ $subject->type === 'religieux' ? route('front.religieux') : route('front.scolaires') }}" style="display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,0.4);text-decoration:none;font-size:0.85rem;font-weight:500;margin-bottom:1rem;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                    <i class="bi bi-arrow-left" style="font-size:0.9rem;"></i> Précédent
                </a>

                <!-- Breadcrumb -->
                <nav style="display:flex;align-items:center;gap:8px;margin-bottom:1.5rem;font-size:0.82rem;color:rgba(255,255,255,0.35);flex-wrap:wrap;">
                    <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        <i class="bi bi-house me-1"></i>Accueil
                    </a>
                    <span style="color:rgba(255,255,255,0.12);">/</span>

                    @if($subject->type === 'religieux')
                    <a href="{{ route('front.religieux') }}" style="color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        Matières Religieuses
                    </a>
                    @else
                    <a href="{{ route('front.scolaires') }}" style="color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        Matières Scolaires
                    </a>
                    @endif
                    <span style="color:rgba(255,255,255,0.12);">/</span>
                    <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ $subject->name }}</span>
                </nav>

                <!-- Titre -->
                <h1 class="fw-bold" style="font-size:2.5rem;color:white;font-family:'Poppins',sans-serif;line-height:1.2;">
                    {{ $subject->name }}
                </h1>

                <!-- Description / Publicité du contenu -->
                <p class="text-white-50 mt-3" style="font-size:1.05rem;line-height:1.7;max-width:540px;">
                    {{ $subject->description ?: match (mb_strtolower($subject->name)) {
                        'coran' => 'Apprentissage du Saint Coran à travers la lecture correcte, les règles du Tajwid, la mémorisation et la révision progressive, avec un accompagnement adapté à chaque niveau.',
                        'arabe' => 'Apprentissage progressif de la langue arabe : alphabet, lecture, écriture, vocabulaire, grammaire et communication orale, du niveau débutant au niveau avancé.',
                        default => 'Découvrez cette matière grâce à un parcours progressif, des ressources pédagogiques adaptées et un accompagnement structuré pour chaque niveau.',
                    } }}
                </p>

            </div>

            <!-- Introduction des niveaux, placée à côté de la matière -->
            <div class="col-lg-6">
                <div class="subject-levels-intro">
                    <div class="subject-levels-intro-content">
                        <span class="levels-intro-badge">
                            <i class="bi bi-layers"></i>
                            {{ $isHighSchoolSupport
                                ? 'Parcours disponible'
                                : 'Niveaux disponibles' }}
                        </span>

                        <h2>Choisissez votre niveau</h2>

                        <p>
                            @if($isHighSchoolSupport)
                                Sélectionnez le parcours BAC, puis choisissez
                                la matière de soutien souhaitée.
                            @else
                                Sélectionnez le niveau qui correspond à votre
                                parcours pour accéder aux cours et aux
                                ressources pédagogiques.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div style="display:flex;flex-direction:column;">
<!-- ═══ EXPLICATION / PUBLICITÉ DU CONTENU ═══ -->
<section class="py-5" style="order:2;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 mb-3" style="background:rgba(124,58,237,0.12);color:#A78BFA;border-radius:20px;font-weight:500;font-size:0.8rem;">
                <i class="bi bi-star me-1"></i> Pourquoi choisir {{ $subject->name }} ?
            </span>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card-3d p-4 h-100">
                    <div class="feature-3d-icon mb-3" style="background:rgba(124,58,237,0.1);color:#A78BFA;">
                        <i class="bi bi-play-circle"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="font-family:'Poppins',sans-serif;">Cours interactifs</h5>
                    <p class="text-white-50 small" style="line-height:1.7;">
                        Des cours vidéo et des supports PDF téléchargeables pour apprendre à votre rythme, avec des quiz interactifs pour valider vos acquis.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-3d p-4 h-100">
                    <div class="feature-3d-icon mb-3" style="background:rgba(6,182,212,0.1);color:#22D3EE;">
                        <i class="bi bi-people"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="font-family:'Poppins',sans-serif;">Suivi personnalisé</h5>
                    <p class="text-white-50 small" style="line-height:1.7;">
                        Un accompagnement par des professeurs dédiés, des lives interactifs et un chat pour poser toutes vos questions.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-3d p-4 h-100">
                    <div class="feature-3d-icon mb-3" style="background:rgba(16,185,129,0.1);color:#34D399;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="font-family:'Poppins',sans-serif;">Progression garantie</h5>
                    <p class="text-white-50 small" style="line-height:1.7;">
                        Des exercices pratiques, des devoirs corrigés et un suivi pédagogique complet pour mesurer votre progression.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ CARTES DES NIVEAUX ═══ -->
<section class="levels-cards-section" style="order:1;">
    <div class="container">
        @if($levels->count() > 0)
        <div class="row g-4">
            @foreach($levels as $level)
                @php
                    $hue = (
                        $loop->index * 60
                        + ($subject->type === 'religieux' ? 260 : 190)
                    ) % 360;

                    $icon = $loop->index === 0
                        ? 'bi-mortarboard-fill'
                        : (
                            $loop->index === 1
                                ? 'bi-book-fill'
                                : 'bi-bar-chart-fill'
                        );

                    $treeId = 'level-tree-' . $level->id;
                @endphp

                <div class="col-12">
                    <article
                        class="level-tree-wrapper"
                        data-level-tree
                        data-auto-open="{{
                            (string) request('open')
                            === (string) $level->id
                                ? 'true'
                                : 'false'
                        }}"
                    >
                        <div class="level-tree-card">
                            <button
                                type="button"
                                class="level-tree-toggle"
                                aria-expanded="false"
                                aria-controls="{{ $treeId }}"
                                data-level-toggle
                            >
                                <div
                                    class="level-card-icon"
                                    style="
                                        background:
                                            hsla({{ $hue }},50%,50%,0.1);
                                        color:
                                            hsl({{ $hue }},60%,60%);
                                    "
                                >
                                    <i class="bi {{ $icon }}"></i>
                                </div>

                                <h5
                                    class="fw-bold mb-2"
                                    style="
                                        color:rgba(255,255,255,0.9);
                                        font-family:'Poppins',sans-serif;
                                    "
                                >
                                    {{ $level->name }}
                                </h5>

                                <p
                                    class="text-white-50 small mb-0"
                                    style="line-height:1.6;"
                                >
                                    {{
                                        $level->description
                                        ?: 'Ce parcours vous permet de '
                                            . 'progresser étape par étape en '
                                            . $subject->name
                                            . '.'
                                    }}
                                </p>

                                <span class="level-tree-action">
                                    <span
                                        data-toggle-label
                                        data-open-label="{{ $openLabel }}"
                                        data-close-label="{{ $closeLabel }}"
                                    >
                                        {{ $openLabel }}
                                    </span>

                                    <i
                                        class="bi bi-chevron-down
                                            level-tree-chevron"
                                    ></i>
                                </span>
                            </button>

                            <div
                                id="{{ $treeId }}"
                                class="level-classes-tree"
                                data-level-panel
                                aria-hidden="true"
                            >
                                <div class="level-classes-title">
                                    <i class="bi bi-diagram-3-fill"></i>
                                    {{ $childPluralLabel }} disponibles
                                </div>

                                @if($level->classes->isNotEmpty())
                                    <ul class="class-tree-list">
                                        @foreach($level->classes as $class)
                                            @php
                                                $requiresVocalTest =
                                                    (bool) (
                                                        $class
                                                            ->requires_vocal_test
                                                        ?? false
                                                    );

                                                $withoutVocalTest =
                                                    (bool) (
                                                        $class
                                                            ->is_without_vocal_test
                                                        ?? false
                                                    );

                                                $targetRoute =
                                                    $isHighSchoolSupport
                                                        ? route(
                                                            'plans',
                                                            [
                                                                'offer' =>
                                                                    'soutien_lycee',
                                                            ]
                                                        )
                                                        : (
                                                            $requiresVocalTest
                                                                ? route(
                                                                    'vocal-test.create',
                                                                    [
                                                                        $subject,
                                                                        $level,
                                                                        $class,
                                                                    ]
                                                                )
                                                                : route(
                                                                    'front.courses',
                                                                    [
                                                                        $subject->id,
                                                                        $level->id,
                                                                        $class->id,
                                                                    ]
                                                                )
                                                        );

                                                $normalizedItemName =
                                                    \App\Models\VocalTestPrompt
                                                        ::normalizePathName(
                                                            $class->name
                                                        );

                                                $classGradient = match (
                                                    $normalizedItemName
                                                ) {
                                                    'debutant' =>
                                                        'linear-gradient('
                                                        . '135deg,#16A34A,'
                                                        . '#15803D)',

                                                    'intermediaire' =>
                                                        'linear-gradient('
                                                        . '135deg,#2563EB,'
                                                        . '#1D4ED8)',

                                                    'mathematiques', 'maths' =>
                                                        'linear-gradient('
                                                        . '135deg,#F59E0B,'
                                                        . '#D97706)',

                                                    'physique chimie',
                                                    'physique' =>
                                                        'linear-gradient('
                                                        . '135deg,#06B6D4,'
                                                        . '#2563EB)',

                                                    default =>
                                                        'linear-gradient('
                                                        . '135deg,#7C3AED,'
                                                        . '#581C87)',
                                                };

                                                $supportIcon = match (
                                                    $normalizedItemName
                                                ) {
                                                    'mathematiques', 'maths'
                                                        => 'bi-calculator-fill',

                                                    'physique chimie',
                                                    'physique'
                                                        => 'bi-lightning-charge-fill',

                                                    default
                                                        => 'bi-book-half',
                                                };
                                            @endphp

                                            <li class="class-tree-item">
                                                <a
                                                    href="{{ $targetRoute }}"
                                                    class="class-tree-link"
                                                    aria-label="
                                                        Ouvrir
                                                        {{ $isHighSchoolSupport
                                                            ? 'la matière'
                                                            : 'la classe' }}
                                                        {{ $class->name }}
                                                    "
                                                >
                                                    <span
                                                        class="
                                                            class-tree-icon
                                                        "
                                                        style="
                                                            background:
                                                                {{ $classGradient }};
                                                        "
                                                    >
                                                        @if($isHighSchoolSupport)
                                                            <i
                                                                class="
                                                                    bi
                                                                    {{ $supportIcon }}
                                                                "
                                                            ></i>
                                                        @elseif($requiresVocalTest)
                                                            <i
                                                                class="
                                                                    bi
                                                                    bi-mic-fill
                                                                "
                                                            ></i>
                                                        @elseif($withoutVocalTest)
                                                            <i
                                                                class="
                                                                    bi
                                                                    bi-check-lg
                                                                "
                                                            ></i>
                                                        @else
                                                            <i
                                                                class="
                                                                    bi
                                                                    bi-book-half
                                                                "
                                                            ></i>
                                                        @endif
                                                    </span>

                                                    <span
                                                        class="
                                                            class-tree-content
                                                        "
                                                    >
                                                        <span
                                                            class="
                                                                class-tree-name
                                                            "
                                                        >
                                                            {{ $class->name }}
                                                        </span>

                                                        @if($isHighSchoolSupport)
                                                            <span
                                                                class="
                                                                    class-tree-status
                                                                "
                                                            >
                                                                <i
                                                                    class="
                                                                        bi
                                                                        bi-file-earmark-text-fill
                                                                    "
                                                                ></i>
                                                                Passer le
                                                                test écrit
                                                            </span>
                                                        @elseif($requiresVocalTest)
                                                            <span
                                                                class="
                                                                    class-tree-status
                                                                    test
                                                                "
                                                            >
                                                                <i
                                                                    class="
                                                                        bi
                                                                        bi-mic-fill
                                                                    "
                                                                ></i>
                                                                Test vocal
                                                            </span>
                                                        @elseif($withoutVocalTest)
                                                            <span
                                                                class="
                                                                    class-tree-status
                                                                    no-test
                                                                "
                                                            >
                                                                <i
                                                                    class="
                                                                        bi
                                                                        bi-check-circle-fill
                                                                    "
                                                                ></i>
                                                                Accès direct
                                                                sans test
                                                            </span>
                                                        @else
                                                            <span
                                                                class="
                                                                    class-tree-status
                                                                "
                                                            >
                                                                <i
                                                                    class="
                                                                        bi
                                                                        bi-play-circle
                                                                    "
                                                                ></i>
                                                                Accéder aux
                                                                cours
                                                            </span>
                                                        @endif
                                                    </span>

                                                    <i
                                                        class="
                                                            bi
                                                            bi-arrow-right
                                                            class-tree-arrow
                                                        "
                                                    ></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="level-tree-empty">
                                        Aucune {{
                                            $isHighSchoolSupport
                                                ? 'matière'
                                                : 'classe'
                                        }} disponible pour ce parcours.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-emoji-neutral" style="font-size:2rem;color:rgba(255,255,255,0.15);"></i>
            </div>
            <h5 style="color:rgba(255,255,255,0.4);font-weight:600;">Aucun niveau disponible</h5>
            <p class="text-white-50 small">Les niveaux pour cette matière seront bientôt disponibles.</p>
        </div>
        @endif
    </div>
</section>
</div>

<!-- ═══ AUTRES MATIÈRES DE LA MÊME FAMILLE ═══ -->
@if($sameFamilySubjects && $sameFamilySubjects->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="badge px-3 py-2 mb-2" style="background:rgba(59,130,246,0.12);color:#60A5FA;border-radius:20px;font-weight:500;font-size:0.8rem;">
                    <i class="bi bi-grid me-1"></i> Autres matières
                </span>
                <h2 class="section-title-3d mt-2 mb-0">
                    {{ $subject->type === 'religieux' ? 'Autres matières religieuses' : 'Autres matières scolaires' }}
                </h2>
                <p class="text-white-50 small mt-1 mb-0">
                    Découvrez les autres matières de la même famille
                </p>
            </div>
            <a href="{{ $subject->type === 'religieux' ? route('front.religieux') : route('front.scolaires') }}" class="btn-3d btn-3d-outline" style="flex-shrink:0;">
                <i class="bi bi-arrow-right"></i> Tout voir
            </a>
        </div>

        <div class="row g-3">
            @foreach($sameFamilySubjects as $familySubject)
            @php
                $hue = ($loop->index * 70 + ($subject->type === 'religieux' ? 260 : 190)) % 360;
                $icons = ['bi-book-fill', 'bi-calculator-fill', 'bi-globe2', 'bi-flask-fill', 'bi-translate', 'bi-music-note'];
                $familyIcon = $icons[$loop->index % count($icons)];
            @endphp
            <div class="col-lg-3 col-md-4 col-6">
                <a href="{{ route('front.subject.levels', $familySubject->id) }}" class="level-card-3d" style="text-align:left;padding:1.25rem;">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="level-card-icon" style="width:44px;height:44px;font-size:1rem;margin:0;background:hsla({{ $hue }},50%,50%,0.1);color:hsl({{ $hue }},60%,60%);flex-shrink:0;">
                            <i class="bi {{ $familyIcon }}"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.9rem;">
                                {{ $familySubject->name }}
                            </h6>
                            <small class="text-white-50" style="font-size:0.72rem;">
                                <i class="bi bi-play-circle me-1"></i>{{ $familySubject->courses_count ?? 0 }} cours
                            </small>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;font-size:0.75rem;color:rgba(255,255,255,0.25);margin-top:0.5rem;">
                        <span>Voir les niveaux</span>
                        <i class="bi bi-arrow-right" style="font-size:0.7rem;color:hsl({{ $hue }},60%,60%);"></i>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═══ CTA FINAL ═══ -->
<section class="py-5">
    <div class="container">
        <div class="text-center">
            <div style="background:linear-gradient(135deg,rgba(124,58,237,0.05),rgba(6,182,212,0.05));border:1px solid rgba(255,255,255,0.04);border-radius:24px;padding:3rem 2rem;">
                <h2 class="fw-bold mb-3" style="color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;">
                    Prêt à commencer ?
                </h2>
                <p class="text-white-50 mb-4" style="max-width:450px;margin-left:auto;margin-right:auto;">
                    Rejoignez des milliers d'étudiants et commencez votre apprentissage dès aujourd'hui.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    @auth
                        <a href="{{ route('appointment.create') }}" class="btn-3d btn-3d-gradient">
                            <i class="bi bi-calendar-check"></i> Prise de contact
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-3d btn-3d-gradient">
                            <i class="bi bi-person-plus"></i> Créer un compte gratuit
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const trees = Array.from(
        document.querySelectorAll('[data-level-tree]')
    );

    trees.forEach(tree => {
        const card = tree.querySelector('.level-tree-card');
        const toggle = tree.querySelector('[data-level-toggle]');
        const panel = tree.querySelector('[data-level-panel]');
        const label = tree.querySelector('[data-toggle-label]');

        if (!card || !toggle || !panel) {
            return;
        }

        toggle.addEventListener('click', () => {
            const willOpen =
                toggle.getAttribute('aria-expanded') !== 'true';

            if (willOpen) {
                trees.forEach(otherTree => {
                    if (otherTree === tree) {
                        return;
                    }

                    const otherCard = otherTree.querySelector(
                        '.level-tree-card'
                    );

                    const otherToggle = otherTree.querySelector(
                        '[data-level-toggle]'
                    );

                    const otherPanel = otherTree.querySelector(
                        '[data-level-panel]'
                    );

                    const otherLabel = otherTree.querySelector(
                        '[data-toggle-label]'
                    );

                    otherTree.classList.remove('is-open');
                    otherCard?.classList.remove('is-open');
                    otherPanel?.classList.remove('is-open');

                    otherToggle?.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                    otherPanel?.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    if (otherLabel) {
                        otherLabel.textContent =
                            otherLabel.dataset.openLabel
                            || 'Afficher les classes';
                    }
                });
            }

            toggle.setAttribute(
                'aria-expanded',
                willOpen ? 'true' : 'false'
            );

            panel.setAttribute(
                'aria-hidden',
                willOpen ? 'false' : 'true'
            );

            tree.classList.toggle('is-open', willOpen);
            card.classList.toggle('is-open', willOpen);
            panel.classList.toggle('is-open', willOpen);

            if (label) {
                label.textContent = willOpen
                    ? (
                        label.dataset.closeLabel
                        || 'Masquer les classes'
                    )
                    : (
                        label.dataset.openLabel
                        || 'Afficher les classes'
                    );
            }

            if (willOpen && window.innerWidth < 992) {
                window.setTimeout(() => {
                    tree.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                    });
                }, 220);
            }
        });

        if (tree.dataset.autoOpen === 'true') {
            window.setTimeout(() => {
                toggle.click();
            }, 120);
        }
    });
});
</script>
@endpush

@endsection
