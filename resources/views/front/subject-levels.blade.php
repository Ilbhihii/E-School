@extends('layouts.front')

@section('title', $subject->name)

@push('head')
<style>
/* ── Hero section ── */
.subject-hero {
    position: relative;
    padding: 5rem 0 4rem;
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
    border-radius: 20px;
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
    font-size: 0.9rem;
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

<!-- ═══ HERO SECTION ═══ -->
<section class="subject-hero">
    <div class="subject-hero-bg"></div>
    <div class="container position-relative" style="z-index:1;">
        <div class="row align-items-center">
            <div class="col-lg-7">
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
                    @if($subject->type === 'religieux')
                        Explorez l'enseignement des sciences religieuses avec des cours structurés et adaptés à chaque niveau. Apprenez à votre rythme avec des professeurs qualifiés.
                    @else
                        Maîtrisez les concepts clés de {{ $subject->name }} grâce à des cours interactifs, des exercices pratiques et un suivi personnalisé. Du niveau débutant à avancé.
                    @endif
                </p>

                <!-- CTA -->
                <div class="d-flex flex-wrap gap-3 mt-4">
                    @auth
                        <a href="{{ route('appointment.create') }}" class="btn-3d btn-3d-gradient">
                            <i class="bi bi-calendar-check"></i> Prise de contact
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-3d btn-3d-gradient">
                            <i class="bi bi-person-plus"></i> Commencer gratuitement
                        </a>
                        <a href="{{ route('plans') }}" class="btn-3d btn-3d-outline">
                            <i class="bi bi-credit-card"></i> Voir les offres
                        </a>
                    @endauth
                </div>

                <!-- Stats -->
                <div style="display:flex;gap:2rem;margin-top:2.5rem;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,0.04);">
                    <div class="stat-3d-item">
                        <div class="stat-3d-value gradient-text">{{ $levels->count() }}</div>
                        <div class="stat-3d-label">Niveaux</div>
                    </div>
                    <div class="stat-3d-item">
                        <div class="stat-3d-value gradient-text">{{ $subject->classes_count ?? 0 }}</div>
                        <div class="stat-3d-label">Classes disponibles</div>
                    </div>
                </div>
            </div>

            <!-- Visuel décoratif -->
            <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                <div style="width:280px;height:280px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,0.08),transparent 70%);display:flex;align-items:center;justify-content:center;font-size:6rem;color:rgba(255,255,255,0.04);">
                    <i class="bi bi-book"></i>
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

<!-- ═══ NIVEAUX DISPONIBLES ═══ -->
<section class="py-5" style="background:rgba(255,255,255,0.01);order:1;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 mb-3" style="background:rgba(34,197,94,0.12);color:#4ADE80;border-radius:20px;font-weight:500;font-size:0.8rem;">
                <i class="bi bi-layers me-1"></i> Niveaux disponibles
            </span>
            <h2 class="section-title-3d">Choisissez votre niveau</h2>
            <p class="text-white-50" style="max-width:500px;margin:0 auto;">
                Sélectionnez le niveau qui correspond à votre parcours pour accéder aux cours et aux ressources pédagogiques.
            </p>
        </div>

        @if($levels->count() > 0)
        <div class="row g-4">
            @foreach($levels as $level)
            @php
                $hue = ($loop->index * 60 + ($subject->type === 'religieux' ? 260 : 190)) % 360;
                $icon = $loop->index === 0 ? 'bi-mortarboard-fill' : ($loop->index === 1 ? 'bi-book-fill' : 'bi-bar-chart-fill');
            @endphp
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('front.subject.level.classes', [$subject->id, $level->id]) }}" class="level-card-3d">
                    <div class="level-card-icon" style="background:hsla({{ $hue }},50%,50%,0.1);color:hsl({{ $hue }},60%,60%);">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;">{{ $level->name }}</h5>
                    <p class="text-white-50 small mb-3" style="line-height:1.6;">
                        {{ $level->description ?: 'Ce niveau vous permet de progresser étape par étape en ' . $subject->name . '.' }}
                    </p>
                    <div style="display:flex;justify-content:center;gap:16px;font-size:0.75rem;color:rgba(255,255,255,0.3);">
                        <span>
                            <i class="bi bi-people-fill me-1"></i>{{ $level->available_classes_count ?? 0 }}
                            {{ ($level->available_classes_count ?? 0) > 1 ? 'classes disponibles' : 'classe disponible' }}
                        </span>
                        <span><i class="bi bi-arrow-right" style="color:hsl({{ $hue }},60%,60%);"></i></span>
                    </div>
                </a>
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
                        <a href="{{ route('plans') }}" class="btn-3d btn-3d-outline">
                            <i class="bi bi-credit-card"></i> Voir les offres
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
