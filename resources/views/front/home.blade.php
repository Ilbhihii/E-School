@extends('layouts.front')

@section('title', 'Smart School Academy — Plateforme Éducative Intelligente')

@section('content')

<!-- ══════════════════════════════════════════════════════
     HERO SECTION
     ══════════════════════════════════════════════════════ -->
<section class="hero-3d text-center" style="padding: 140px 0 100px;">
    <div class="container hero-3d-content">

        <h1 class="hero-3d-title mb-4 mx-auto" style="max-width: 850px;">
            La plateforme intelligente<br>
            <span class="gradient-text">La réussite est à portée de Clic</span>
        </h1>

        <p class="hero-3d-subtitle mx-auto mb-5" style="max-width: 600px; font-size: 1.15rem;">
            Cours interactifs, sessions live, quiz et suivi personnalisé — 
            tout ce qu'il vous faut pour exceller, accessible partout et à tout moment.
        </p>

        <div class="row g-4 justify-content-center mb-5 mx-auto" style="max-width:760px;">
            @foreach($subjectsGrouped as $group)
                @foreach($group['subjects'] as $subject)
                    <div class="col-md-6">
                        <a href="{{ route('front.subject.levels', $subject->id) }}"
                           class="text-decoration-none d-block h-100"
                           aria-label="Voir les niveaux de {{ $subject->name }}">
                            <div class="card-3d text-center h-100 reveal-3d" style="cursor:pointer;padding:2rem;">
                                <div class="card-3d-icon mx-auto">
                                    <i class="bi {{ $subject->name === 'Coran' ? 'bi-book-half' : 'bi-translate' }}"></i>
                                </div>
                                <h5 class="fw-bold text-white mt-3 mb-2" style="font-family:'Poppins',sans-serif;">
                                    {{ $subject->name }}
                                </h5>
                                <span class="badge mx-auto mb-3" style="background:{{ $subject->type === 'religieux' ? 'rgba(155,89,182,0.2)' : 'rgba(52,152,219,0.2)' }};color:{{ $subject->type === 'religieux' ? '#D7A1F9' : '#7DD3FC' }};border-radius:20px;font-size:0.72rem;">
                                    {{ $subject->type === 'religieux' ? 'Matière religieuse' : 'Matière scolaire' }}
                                </span>
                                <p class="text-white-50 small mb-0">
                                    Voir les niveaux <i class="bi bi-arrow-right ms-1" style="color:var(--3d-gold);"></i>
                                </p>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- Floating 3D shapes (from CSS) -->
    <div class="shape-3d"></div>
    <div class="shape-3d"></div>
    <div class="shape-3d"></div>

    <!-- 🎓 Floating Educational Icons Background -->
    <div class="edu-floating-icons" aria-hidden="true">
        <div class="edu-icon edu-icon-1"><i class="bi bi-book"></i></div>
        <div class="edu-icon edu-icon-2"><i class="bi bi-mortarboard-fill"></i></div>
        <div class="edu-icon edu-icon-3"><i class="bi bi-pencil-fill"></i></div>
        <div class="edu-icon edu-icon-4"><i class="bi bi-journal-text"></i></div>
        <div class="edu-icon edu-icon-5"><i class="bi bi-calculator-fill"></i></div>
        <div class="edu-icon edu-icon-6"><i class="bi bi-globe"></i></div>
        <div class="edu-icon edu-icon-7"><i class="bi bi-flask"></i></div>
        <div class="edu-icon edu-icon-8"><i class="bi bi-star-fill"></i></div>
        <div class="edu-icon edu-icon-9"><i class="bi bi-puzzle-fill"></i></div>
        <div class="edu-icon edu-icon-10"><i class="bi bi-trophy-fill"></i></div>
        <div class="edu-icon edu-icon-11"><i class="bi bi-laptop"></i></div>
        <div class="edu-icon edu-icon-12"><i class="bi bi-clipboard-check"></i></div>
    </div>

    <style>
        /* ═══════════════════════════════════════════════
           FLOATING EDUCATIONAL ICONS
           ═══════════════════════════════════════════════ */
        .edu-floating-icons {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }
        .hero-3d-content { position: relative; z-index: 2; }

        .edu-icon {
            position: absolute;
            opacity: 0;
            color: rgba(255, 255, 255, 0.08);
            font-size: 2rem;
            animation: eduFloatUp 14s ease-in-out infinite;
            transform-origin: center;
        }

        /* Position & delay each icon */
        .edu-icon-1  { left: 5%;   top: 10%; font-size: 2.4rem; animation-delay: 0s;   animation-duration: 16s; }
        .edu-icon-2  { left: 12%;  top: 50%; font-size: 3.2rem; animation-delay: 1.5s; animation-duration: 18s; color: rgba(167, 139, 250, 0.10); }
        .edu-icon-3  { right: 8%;  top: 15%; font-size: 2rem;   animation-delay: 3s;   animation-duration: 15s; }
        .edu-icon-4  { right: 18%; top: 55%; font-size: 2.8rem; animation-delay: 0.8s; animation-duration: 20s; color: rgba(96, 165, 250, 0.10); }
        .edu-icon-5  { left: 20%;  top: 70%; font-size: 1.8rem; animation-delay: 4.5s; animation-duration: 14s; }
        .edu-icon-6  { right: 25%; top: 30%; font-size: 2.6rem; animation-delay: 2.2s; animation-duration: 17s; color: rgba(74, 222, 128, 0.08); }
        .edu-icon-7  { left: 30%;  top: 25%; font-size: 1.6rem; animation-delay: 5.5s; animation-duration: 19s; }
        .edu-icon-8  { right: 35%; top: 70%; font-size: 1.4rem; animation-delay: 1.2s; animation-duration: 13s; color: rgba(252, 211, 77, 0.12); }
        .edu-icon-9  { left: 45%;  top: 80%; font-size: 2.2rem; animation-delay: 3.8s; animation-duration: 16s; }
        .edu-icon-10 { right: 5%;  top: 80%; font-size: 2rem;   animation-delay: 6s;   animation-duration: 15s; color: rgba(167, 139, 250, 0.08); }
        .edu-icon-11 { left: 8%;   top: 35%; font-size: 2.5rem; animation-delay: 4s;   animation-duration: 18s; }
        .edu-icon-12 { right: 12%; top: 45%; font-size: 1.5rem; animation-delay: 2.8s; animation-duration: 14s; color: rgba(74, 222, 128, 0.10); }

        @keyframes eduFloatUp {
            0% {
                opacity: 0;
                transform: translateY(80px) translateX(0) scale(0.6) rotate(-10deg);
            }
            10% {
                opacity: 1;
            }
            45% {
                transform: translateY(-40px) translateX(30px) scale(1.1) rotate(5deg);
            }
            70% {
                opacity: 0.9;
                transform: translateY(-80px) translateX(-20px) scale(1) rotate(-3deg);
            }
            100% {
                opacity: 0;
                transform: translateY(-140px) translateX(10px) scale(0.7) rotate(8deg);
            }
        }

        /* Slow drift variation for some icons */
        .edu-icon-2 { animation: eduFloatDrift 20s ease-in-out infinite; }
        .edu-icon-6 { animation: eduFloatDrift 22s ease-in-out infinite; }
        .edu-icon-8 { animation: eduFloatDrift 18s ease-in-out infinite; }

        @keyframes eduFloatDrift {
            0% {
                opacity: 0;
                transform: translateY(60px) translateX(0) scale(0.7);
            }
            12% {
                opacity: 1;
            }
            50% {
                transform: translateY(-60px) translateX(-40px) scale(1.15);
            }
            80% {
                opacity: 0.8;
                transform: translateY(-120px) translateX(20px) scale(0.9);
            }
            100% {
                opacity: 0;
                transform: translateY(-180px) translateX(-10px) scale(0.6);
            }
        }

        @media (max-width: 768px) {
            .edu-icon { display: none; }
            .edu-icon-1, .edu-icon-2, .edu-icon-3, .edu-icon-4 { display: block; font-size: 1.4rem; }
        }
    </style>
</section>

<!-- ══════════════════════════════════════════════════════
     HOW IT WORKS
     ══════════════════════════════════════════════════════ -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 mb-3" style="background: rgba(22, 163, 74, 0.12); color: #4ADE80; border-radius: 20px; font-weight: 500; font-size: 0.8rem; letter-spacing: 0.05em;">
                Comment ça marche
            </span>
            <h2 class="section-title-3d">Commencez en 3 étapes simples</h2>
            <p class="text-white-50 mt-3" style="max-width: 500px; margin: 0 auto;">
                Pas de complicité. Créez votre compte, choisissez votre niveau et commencez à apprendre.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="step-3d reveal-3d">
                    <div class="step-3d-number mx-auto">1</div>
                    <h5 class="fw-bold text-white mt-3 mb-2">Créez votre compte</h5>
                    <p class="text-white-50 small" style="line-height: 1.7;">
                        Inscrivez-vous gratuitement en moins d'une minute. Aucune carte bancaire requise.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="step-3d reveal-3d" style="transition-delay: 0.15s;">
                    <div class="step-3d-number mx-auto" style="background: linear-gradient(135deg, #7C3AED, #FFD166);">2</div>
                    <h5 class="fw-bold text-white mt-3 mb-2">Choisissez votre niveau</h5>
                    <p class="text-white-50 small" style="line-height: 1.7;">
                        Sélectionnez votre matière et votre niveau scolaire parmi nos nombreuses offres.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="step-3d reveal-3d" style="transition-delay: 0.3s;">
                    <div class="step-3d-number mx-auto" style="background: linear-gradient(135deg, #FFD166, #FFB347); color: #1E293B;">3</div>
                    <h5 class="fw-bold text-white mt-3 mb-2">Commencez à apprendre</h5>
                    <p class="text-white-50 small" style="line-height: 1.7;">
                        Accédez à tous les cours, lives et ressources. Apprenez à votre rythme et réussissez !
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('register') }}" class="btn-3d btn-3d-gradient" style="padding: 16px 44px; font-size: 1.1rem;">
                <i class="bi bi-person-plus"></i>
                Créer mon compte
            </a>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ══════════════════════════════════════════════════════
     WHY CHOOSE US
     ══════════════════════════════════════════════════════ -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 mb-3" style="background: rgba(124, 58, 237, 0.15); color: #A78BFA; border-radius: 20px; font-weight: 500; font-size: 0.8rem; letter-spacing: 0.05em;">
                Pourquoi nous choisir ?
            </span>
            <h2 class="section-title-3d">Une expérience d'apprentissage <br class="d-none d-md-block">complète et moderne</h2>
            <p class="text-white-50 mt-3" style="max-width: 540px; margin: 0 auto; font-size: 1.05rem;">
                Tout ce dont vous avez besoin pour réussir, réuni en un seul endroit.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col">
                <div class="card-3d text-center h-100 reveal-3d">
                    <div class="card-3d-icon mx-auto">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">Interface moderne</h5>
                    <p class="text-white-50 small mb-0" style="line-height: 1.7;">
                        Une plateforme intuitive et élégante conçue pour une expérience d'apprentissage fluide et agréable.
                    </p>
                </div>
            </div>

            <div class="col">
                <div class="card-3d text-center h-100 reveal-3d" style="transition-delay: 0.1s;">
                    <div class="card-3d-icon mx-auto" style="background: linear-gradient(135deg, #7C3AED, #FFD166);">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">Évolution</h5>
                    <p class="text-white-50 small mb-0" style="line-height: 1.7;">
                        La plateforme utilise des outils intelligents pour détecter les axes d'amélioration et vous accompagner vers la perfection.
                    </p>
                </div>
            </div>

            <div class="col">
                <div class="card-3d text-center h-100 reveal-3d" style="transition-delay: 0.2s;">
                    <div class="card-3d-icon mx-auto" style="background: linear-gradient(135deg, #FFD166, #FFB347);">
                        <i class="bi bi-cloud-arrow-down"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">Supports PDF & vidéos</h5>
                    <p class="text-white-50 small mb-0" style="line-height: 1.7;">
                        Accédez à tous les cours, ressources PDF et vidéos téléchargeables à tout moment.
                    </p>
                </div>
            </div>

            <div class="col">
                <div class="card-3d text-center h-100 reveal-3d" style="transition-delay: 0.3s;">
                    <div class="card-3d-icon mx-auto" style="background: linear-gradient(135deg, #16A34A, #22C55E);">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">Suivi personnalisé</h5>
                    <p class="text-white-50 small mb-0" style="line-height: 1.7;">
                        Un accompagnement sur mesure avec des enseignants diplomer pour vous aider à atteindre vos objectifs.
                    </p>
                </div>
            </div>

            <div class="col">
                <div class="card-3d text-center h-100 reveal-3d" style="transition-delay: 0.4s;">
                    <div class="card-3d-icon mx-auto" style="background: linear-gradient(135deg, #D90429, #EF4444);">
                        <i class="bi bi-phone"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">Accessible partout</h5>
                    <p class="text-white-50 small mb-0" style="line-height: 1.7;">
                        Apprenez depuis n'importe quel appareil — ordinateur, tablette ou smartphone, où que vous soyez.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ══════════════════════════════════════════════════════
     ABOUT / QUI SOMMES-NOUS
     ══════════════════════════════════════════════════════ -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="reveal-3d position-relative">
                    <div class="card-3d overflow-hidden p-0" style="border-radius: 24px;">
                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&q=80"
                             alt="Étudiants" class="w-100" style="height: 400px; object-fit: cover; display: block;">
                        <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(0,58,143,0.3), rgba(124,58,237,0.2));"></div>
                    </div>
                    <!-- Floating badge -->
                    <div class="position-absolute bottom-0 end-0 translate-middle-y me-4"
                         style="background: var(--3d-gradient-main); border-radius: 16px; padding: 16px 20px; box-shadow: 0 10px 40px rgba(0,58,143,0.4);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill" style="color: #22C55E; font-size: 1.2rem;"></i>
                            <div>
                                <small class="fw-bold text-white" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.7;">Depuis</small>
                                <div class="fw-bold text-white">2026</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="reveal-3d">
                    <span class="badge px-3 py-2 mb-3" style="background: rgba(0, 58, 143, 0.15); color: #2563EB; border-radius: 20px; font-weight: 500; font-size: 0.8rem; letter-spacing: 0.05em;">
                        Qui sommes-nous ?
                    </span>
                    <h2 class="section-title-3d mb-4">L'école à domicile <br>pour tous</h2>
                    <p class="text-white-50 mb-3" style="line-height: 1.8; font-size: 1.05rem;">
                        <strong class="text-white">Smart School Academy</strong> est un projet porté par un ingénieur et un enseignant en programmation, 
                        également doctorant en chimie et formateur d'enseignants. Il vise à transformer le temps passé 
                        devant les écrans en une véritable opportunité d'apprentissage utile et efficace.
                    </p>
                    <p class="text-white-50 mb-4" style="line-height: 1.8; font-size: 1.05rem;">
                        L'objectif simple : <strong class="text-white">rendre l'éducation accessible à domicile</strong>, pour que chacun puisse  
                        apprendre et progresser sans contrainte.
                    </p>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(0, 58, 143, 0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="bi bi-broadcast" style="color: #2563EB; font-size: 1rem;"></i>
                            </span>
                            <div>
                                <strong class="text-white">Cours interactifs en direct (lives)</strong>
                                <p class="text-white-50 small mb-0">Enseignement en temps réel favorisant l'échange et l'implication des élèves</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(124, 58, 237, 0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="bi bi-graph-up-arrow" style="color: #7C3AED; font-size: 1rem;"></i>
                            </span>
                            <div>
                                <strong class="text-white">Suivi personnalisé & précis</strong>
                                <p class="text-white-50 small mb-0">Analyse du niveau, détection des axes de progression et parcours adapté</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 209, 102, 0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="bi bi-arrow-up-circle-fill" style="color: #FFD166; font-size: 1rem;"></i>
                            </span>
                            <div>
                                <strong class="text-white">Progrès visibles & amélioration continue</strong>
                                <p class="text-white-50 small mb-0">Un suivi rigoureux pour des résultats concrets et durables</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="btn-3d btn-3d-gradient mt-4">
                        Rejoindre Smart School Academy <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ══════════════════════════════════════════════════════
     OBJECTIFS
     ══════════════════════════════════════════════════════ -->
<section class="py-5 text-center">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 mb-3" style="background: rgba(255, 209, 102, 0.12); color: #FFD166; border-radius: 20px; font-weight: 500; font-size: 0.8rem; letter-spacing: 0.05em;">
                Nos objectifs
            </span>
            <h2 class="section-title-3d">Notre mission pour votre réussite</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-3d overflow-hidden p-0 reveal-3d" style="border-radius: 20px;">
                    <div style="height: 200px; overflow: hidden;">
                        <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400&q=80"
                             class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;" alt="">
                    </div>
                    <div class="p-4 text-start">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--3d-gradient-main); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <i class="bi bi-book text-white" style="font-size: 1.1rem;"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2">Faciliter l'apprentissage</h5>
                        <p class="text-white-50 small mb-0">Rendre l'éducation accessible et simplifiée grâce à des outils modernes et interactifs.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-3d overflow-hidden p-0 reveal-3d" style="border-radius: 20px; transition-delay: 0.15s;">
                    <div style="height: 200px; overflow: hidden;">
                        <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=400&q=80"
                             class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;" alt="">
                    </div>
                    <div class="p-4 text-start">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #7C3AED, #FFD166); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <i class="bi bi-trophy text-white" style="font-size: 1.1rem;"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2">Encourager la réussite</h5>
                        <p class="text-white-50 small mb-0">Motiver et accompagner chaque étudiant vers l'excellence académique et la confiance en soi.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-3d overflow-hidden p-0 reveal-3d" style="border-radius: 20px; transition-delay: 0.3s;">
                    <div style="height: 200px; overflow: hidden;">
                        <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=400&q=80"
                             class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;" alt="">
                    </div>
                    <div class="p-4 text-start">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #FFD166, #FFB347); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <i class="bi bi-lightbulb text-dark" style="font-size: 1.1rem;"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2">Innover dans l'éducation</h5>
                        <p class="text-white-50 small mb-0">Repousser les limites de l'enseignement traditionnel avec des méthodes pédagogiques innovantes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ══════════════════════════════════════════════════════
     STATS SECTION — avec compteurs animés
     ══════════════════════════════════════════════════════ -->
<section class="py-5" id="statsSection">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-lg-3">
                <div class="reveal-3d stat-3d">
                    <span class="stat-3d-number counter-value" data-target="1200" data-prefix="+" data-suffix="">
                        <span class="counter-inner">0</span>
                    </span>
                    <span class="stat-3d-label">Étudiants actifs</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="reveal-3d stat-3d">
                    <span class="stat-3d-number counter-value" data-target="50" data-prefix="+" data-suffix="">
                        <span class="counter-inner">0</span>
                    </span>
                    <span class="stat-3d-label">Cours disponibles</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="reveal-3d stat-3d">
                    <span class="stat-3d-number counter-value" data-target="120" data-prefix="+" data-suffix="">
                        <span class="counter-inner">0</span>
                    </span>
                    <span class="stat-3d-label">Lives organisés</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="reveal-3d stat-3d">
                    <span class="stat-3d-number counter-value" data-target="95" data-prefix="" data-suffix="%">
                        <span class="counter-inner">0</span>
                    </span>
                    <span class="stat-3d-label">Satisfaction</span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ══════════════════════════════════════════════════════
     FINAL CTA
     ══════════════════════════════════════════════════════ -->
<section class="cta-3d text-center py-5">
    <div class="container cta-3d-content">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3" style="background: rgba(255, 255, 255, 0.1); color: rgba(255,255,255,0.8); border-radius: 20px; font-weight: 500; font-size: 0.8rem; letter-spacing: 0.05em;">
                    🚀 Prêt à commencer ?
                </span>
                <h2 class="section-title-3d-light fw-bold mb-3" style="font-family: 'Poppins', sans-serif; font-size: 2.5rem;">
                    Rejoignez des milliers d'étudiants <br>qui réussissent avec Smart School Academy
                </h2>
                <p class="text-white-50 mb-4" style="font-size: 1.1rem; max-width: 550px; margin: 0 auto 1.5rem;">
                    Inscription gratuite. Annulation à tout moment. <br>Rejoignez l'aventure éducative dès aujourd'hui !
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('register') }}" class="btn-3d btn-3d-gold" style="padding: 16px 44px; font-size: 1.1rem;">
                        <i class="bi bi-rocket-takeoff"></i>
                        S'inscrire maintenant
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="{{ route('appointment.create') }}" class="btn-3d btn-3d-outline" style="padding: 16px 36px; font-size: 1.1rem;">
                        <i class="bi bi-calendar-check"></i>
                        prise de contact
                    </a>
                    <a href="{{ route('plans') }}" class="btn-3d btn-3d-outline" style="padding: 16px 36px; font-size: 1.1rem;">
                        <i class="bi bi-credit-card"></i>
                        Voir les offres
                    </a>
                </div>
                <p class="mt-3" style="color: rgba(255,255,255,0.3); font-size: 0.8rem;">
                    <i class="bi bi-shield-check"></i> Sans engagement · Paiement sécurisé
                </p>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function() {
    'use strict';

    /**
     * Compteur animé — chaque nombre défile de 0 à `data-target`
     * avec un décalage progressif entre les compteurs (stagger),
     * lorsque la section des stats entre dans le viewport.
     */
    const counters = document.querySelectorAll('.counter-value');
    if (!counters.length) return;

    let animationStarted = false;

    function onScrollCheck() {
        if (animationStarted) return;
        const section = document.getElementById('statsSection');
        if (!section) return;

        const rect = section.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight - 80 && rect.bottom > 0;
        if (!isVisible) return;

        animationStarted = true;

        // Nettoyer le listener dès que l'animation démarre
        window.removeEventListener('scroll', onScrollCheck);
        window.removeEventListener('load', onScrollCheck);

        const staggerDelay = 250; // ms entre chaque compteur

        counters.forEach((counter, index) => {
            const inner = counter.querySelector('.counter-inner');
            if (!inner) return;

            const target = parseInt(counter.dataset.target, 10);
            const prefix = counter.dataset.prefix || '';
            const suffix = counter.dataset.suffix || '';
            const duration = 1800 + (target > 1000 ? target * 0.5 : 0); // plus de temps pour les grands nombres

            setTimeout(() => {
                const startTime = performance.now();

                function formatNumber(n) {
                    return n.toLocaleString('fr-FR');
                }

                function update(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);

                    // Ease-out quartique (plus doux)
                    const eased = 1 - Math.pow(1 - progress, 4);
                    const current = Math.round(eased * target);

                    inner.textContent = prefix + formatNumber(current) + suffix;

                    if (progress < 1) {
                        requestAnimationFrame(update);
                    } else {
                        inner.textContent = prefix + formatNumber(target) + suffix;
                    }
                }

                requestAnimationFrame(update);
            }, index * staggerDelay);
        });
    }

    window.addEventListener('scroll', onScrollCheck, { passive: true });
    window.addEventListener('load', onScrollCheck);
    onScrollCheck(); // vérification immédiate
})();
</script>
@endpush

@endsection
