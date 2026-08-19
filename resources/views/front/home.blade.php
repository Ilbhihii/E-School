@extends('layouts.front')

@section('title', 'Smart School Academy — Plateforme Éducative Intelligente')

@section('content')

<style>
    /* Cartes harmonisées pour les matières actives */
    .home-subjects-grid {
        max-width: 1080px;
    }

    .home-subjects-grid > [class*="col-"] {
        display: flex;
    }

    .home-subject-card {
        width: 100%;
        min-height: 245px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.75rem 1.35rem !important;
    }

    .home-subject-icon {
        width: 78px !important;
        height: 78px !important;
        flex: 0 0 78px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
        border-radius: 21px !important;
        color: #ffffff;
        font-size: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.14);
        box-shadow: 0 13px 28px rgba(0, 0, 0, 0.2);
        transform: translateZ(28px);
        transition:
            transform 0.3s ease,
            box-shadow 0.3s ease;
    }

    .home-subject-card:hover .home-subject-icon {
        transform: translateZ(44px) translateY(-4px) scale(1.06);
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.28);
    }

    .home-subject-card-title {
        min-height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 991.98px) {
        .home-subjects-grid {
            max-width: 760px;
        }
    }

    @media (max-width: 575.98px) {
        .home-subjects-grid {
            max-width: 390px;
        }

        .home-subject-card {
            min-height: 220px;
        }

        .home-subject-icon {
            width: 68px !important;
            height: 68px !important;
            flex-basis: 68px;
            border-radius: 18px !important;
            font-size: 1.75rem;
        }
    }
</style>


<style>
    /*
    |--------------------------------------------------------------------------
    | AMÉLIORATION VISUELLE UNIQUEMENT — V11
    |--------------------------------------------------------------------------
    | Aucun texte, aucune route, aucune boucle Blade et aucune fonctionnalité
    | ne sont modifiés. Cette feuille améliore seulement la présentation.
    */

    :root {
        --home-design-bg: #070d18;
        --home-design-panel: rgba(15, 26, 44, 0.92);
        --home-design-panel-soft: rgba(20, 34, 56, 0.82);
        --home-design-border: rgba(148, 163, 184, 0.14);
        --home-design-border-hover: rgba(112, 139, 255, 0.38);
        --home-design-text: #f8fafc;
        --home-design-muted: #91a0b7;
        --home-design-blue: #4f6ff5;
        --home-design-violet: #7653ea;
        --home-design-green: #27b98a;
        --home-design-gold: #f0b94d;
        --home-design-shadow:
            0 22px 55px rgba(0, 0, 0, 0.24);
    }

    /* Arrière-plan général de la page */
    body {
        background:
            radial-gradient(
                circle at 10% 10%,
                rgba(79, 111, 245, 0.08),
                transparent 27%
            ),
            radial-gradient(
                circle at 90% 25%,
                rgba(118, 83, 234, 0.07),
                transparent 24%
            ),
            var(--home-design-bg);
    }

    main {
        overflow: hidden;
    }

    /* Espacement et séparation des sections */
    section.py-5 {
        padding-top: 5.5rem !important;
        padding-bottom: 5.5rem !important;
    }

    .section-divider {
        width: min(1180px, calc(100% - 40px));
        height: 1px;
        margin: 0 auto;
        opacity: 1;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(99, 125, 255, 0.28),
            rgba(240, 185, 77, 0.18),
            transparent
        );
    }


    /* Prise de contact — Hero */
    .home-contact-wrap {
        max-width: 760px;
        margin: 0 auto 3.25rem;
        text-align: left;
    }

    /* Le formulaire est maintenant entre les objectifs et le CTA final. */
    .home-contact-section {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(
                circle at 50% 0%,
                rgba(79, 111, 245, 0.08),
                transparent 38%
            );
    }

    .home-contact-section .home-contact-wrap {
        margin-bottom: 0;
    }

    .home-contact-card {
        position: relative;
        overflow: hidden;
        padding: 1.6rem;
        border: 1px solid rgba(140, 167, 255, 0.20);
        border-radius: 22px;
        background:
            radial-gradient(
                circle at 8% 0%,
                rgba(79, 111, 245, 0.16),
                transparent 34%
            ),
            linear-gradient(
                145deg,
                rgba(19, 32, 54, 0.96),
                rgba(12, 21, 37, 0.96)
            );
        box-shadow:
            0 22px 55px rgba(0, 0, 0, 0.24),
            inset 0 1px 0 rgba(255, 255, 255, 0.035);
    }

    .home-contact-card::after {
        position: absolute;
        top: -70px;
        right: -50px;
        width: 190px;
        height: 190px;
        content: "";
        pointer-events: none;
        border-radius: 50%;
        background: rgba(118, 83, 234, 0.10);
        filter: blur(2px);
    }

    .home-contact-head {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .home-contact-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        margin-bottom: 0.35rem;
        color: #9fb3ff;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.09em;
        text-transform: uppercase;
    }

    .home-contact-title {
        margin: 0;
        color: #f8fafc;
        font-size: clamp(1.25rem, 2vw, 1.6rem);
        font-weight: 800;
        letter-spacing: -0.025em;
    }

    .home-contact-subtitle {
        margin: 0.3rem 0 0;
        color: #91a0b7;
        font-size: 0.85rem;
        line-height: 1.55;
    }

    .home-contact-form {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
        align-items: stretch;
    }

    .home-contact-field,
    .home-contact-action {
        width: 100%;
    }

    .home-contact-field label {
        display: block;
        margin-bottom: 0.42rem;
        color: #d9e2f1;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .home-contact-input {
        width: 100%;
        height: 48px;
        padding: 0 0.85rem;
        color: #f8fafc;
        font-size: 0.84rem;
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 12px;
        outline: 0;
        background: rgba(7, 13, 24, 0.62);
        transition:
            border-color 180ms ease,
            box-shadow 180ms ease,
            background 180ms ease;
    }

    .home-contact-input::placeholder {
        color: #64748b;
    }

    .home-contact-input:focus {
        border-color: rgba(124, 145, 255, 0.70);
        background: rgba(9, 17, 31, 0.92);
        box-shadow: 0 0 0 3px rgba(79, 111, 245, 0.12);
    }

    .home-contact-input.is-invalid {
        border-color: rgba(248, 113, 113, 0.70);
    }

    .home-contact-field-full {
        width: 100%;
    }

    .home-contact-textarea {
        width: 100%;
        min-height: 105px;
        padding: 0.85rem;
        color: #f8fafc;
        font-size: 0.84rem;
        line-height: 1.55;
        resize: vertical;
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 12px;
        outline: 0;
        background: rgba(7, 13, 24, 0.62);
        transition:
            border-color 180ms ease,
            box-shadow 180ms ease,
            background 180ms ease;
    }

    .home-contact-textarea::placeholder {
        color: #64748b;
    }

    .home-contact-textarea:focus {
        border-color: rgba(124, 145, 255, 0.70);
        background: rgba(9, 17, 31, 0.92);
        box-shadow: 0 0 0 3px rgba(79, 111, 245, 0.12);
    }

    .home-contact-textarea.is-invalid {
        border-color: rgba(248, 113, 113, 0.70);
    }

    .home-contact-consent {
        grid-column: 1 / -1;
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        padding: 0.8rem 0.9rem;
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 12px;
        background: rgba(7, 13, 24, 0.42);
    }

    .home-contact-consent input {
        width: 17px;
        height: 17px;
        margin-top: 2px;
        flex: 0 0 auto;
        accent-color: #7c91ff;
    }

    .home-contact-consent label {
        margin: 0;
        color: #aebbd0;
        font-size: 0.74rem;
        line-height: 1.55;
        cursor: pointer;
    }

    .home-contact-consent strong {
        color: #e2e8f0;
    }

    .home-contact-submit {
        width: 100%;
        min-width: 155px;
        height: 50px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.48rem;
        padding: 0 1rem;
        color: #ffffff;
        font-size: 0.82rem;
        font-weight: 800;
        white-space: nowrap;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(
            110deg,
            #4f6ff5,
            #7653ea
        );
        box-shadow: 0 12px 25px rgba(79, 111, 245, 0.22);
        transition:
            transform 180ms ease,
            box-shadow 180ms ease;
    }

    .home-contact-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 15px 30px rgba(79, 111, 245, 0.30);
    }

    .home-contact-alert {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        margin-bottom: 1rem;
        padding: 0.8rem 0.95rem;
        font-size: 0.8rem;
        line-height: 1.5;
        border-radius: 12px;
    }

    .home-contact-alert.success {
        color: #bbf7d0;
        border: 1px solid rgba(34, 197, 94, 0.22);
        background: rgba(34, 197, 94, 0.09);
    }

    .home-contact-alert.error {
        color: #fecaca;
        border: 1px solid rgba(248, 113, 113, 0.22);
        background: rgba(127, 29, 29, 0.16);
    }

    .home-contact-errors {
        margin: 0.4rem 0 0;
        padding-left: 1rem;
        color: #fecaca;
        font-size: 0.7rem;
    }

    .home-contact-honeypot {
        position: absolute !important;
        left: -10000px !important;
        width: 1px !important;
        height: 1px !important;
        overflow: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    @media (max-width: 767.98px) {
        .home-contact-wrap {
            max-width: 100%;
            margin-bottom: 2.4rem;
        }

        .home-contact-card {
            padding: 1.15rem;
            border-radius: 18px;
        }
    }

    /* HERO */
    .hero-3d {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        background:
            radial-gradient(
                circle at 50% 10%,
                rgba(79, 111, 245, 0.17),
                transparent 34%
            ),
            radial-gradient(
                circle at 88% 44%,
                rgba(118, 83, 234, 0.11),
                transparent 27%
            ),
            linear-gradient(
                180deg,
                rgba(9, 17, 31, 0.98),
                rgba(7, 13, 24, 0.98)
            );
    }

    .hero-3d::before {
        position: absolute;
        inset: 0;
        z-index: -1;
        content: "";
        pointer-events: none;
        opacity: 0.28;
        background-image:
            linear-gradient(
                rgba(148, 163, 184, 0.045) 1px,
                transparent 1px
            ),
            linear-gradient(
                90deg,
                rgba(148, 163, 184, 0.045) 1px,
                transparent 1px
            );
        background-size: 54px 54px;
        mask-image: linear-gradient(
            to bottom,
            rgba(0, 0, 0, 0.8),
            transparent 88%
        );
    }

    .hero-3d-title {
        max-width: 940px !important;
        margin-bottom: 1.35rem !important;
        color: var(--home-design-text);
        font-size: clamp(2.75rem, 6vw, 5.2rem);
        font-weight: 800;
        line-height: 1.08;
        letter-spacing: -0.055em;
        text-wrap: balance;
        text-shadow: 0 15px 42px rgba(0, 0, 0, 0.28);
    }

    .hero-3d-title .gradient-text {
        display: inline-block;
        margin-top: 0.35rem;
        color: transparent;
        background:
            linear-gradient(
                100deg,
                #8ca7ff 0%,
                #a77af4 48%,
                #f3bf59 100%
            );
        background-clip: text;
        -webkit-background-clip: text;
    }

    .hero-3d-subtitle {
        color: var(--home-design-muted) !important;
        line-height: 1.8;
        text-wrap: balance;
    }

    /* Cartes des matières */
    .home-subjects-grid {
        max-width: 1120px;
        margin-top: 2.5rem;
    }

    .home-subjects-grid > [class*="col-"] {
        display: flex;
    }

    .home-subjects-grid a,
    .home-subjects-grid a:link,
    .home-subjects-grid a:visited,
    .home-subjects-grid a:hover,
    .home-subjects-grid a:focus,
    .home-subjects-grid a:active,
    .home-subjects-grid a * {
        text-decoration: none !important;
    }

    .home-subject-card {
        position: relative;
        width: 100%;
        min-height: 260px;
        overflow: hidden;
        align-items: center;
        justify-content: center;
        padding: 2rem 1.55rem !important;
        text-align: center;
        border: 1px solid var(--home-design-border) !important;
        border-radius: 22px !important;
        background:
            radial-gradient(
                circle at 50% -10%,
                rgba(79, 111, 245, 0.10),
                transparent 42%
            ),
            linear-gradient(
                145deg,
                rgba(20, 34, 56, 0.97),
                rgba(12, 22, 38, 0.97)
            ) !important;
        box-shadow:
            0 18px 42px rgba(0, 0, 0, 0.18),
            inset 0 1px 0 rgba(255, 255, 255, 0.025);
        transform: translateZ(0);
        transition:
            transform 220ms ease,
            border-color 220ms ease,
            box-shadow 220ms ease;
    }

    .home-subject-card::before {
        position: absolute;
        top: 0;
        right: 15%;
        left: 15%;
        height: 2px;
        content: "";
        border-radius: 0 0 999px 999px;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(118, 151, 255, 0.9),
            transparent
        );
    }

    .home-subject-card::after {
        position: absolute;
        top: -75px;
        right: -65px;
        width: 170px;
        height: 170px;
        content: "";
        pointer-events: none;
        border-radius: 50%;
        background: rgba(79, 111, 245, 0.08);
        filter: blur(2px);
    }

    .home-subject-card:hover {
        border-color: var(--home-design-border-hover) !important;
        box-shadow:
            0 26px 58px rgba(0, 0, 0, 0.27),
            0 0 0 1px rgba(79, 111, 245, 0.07);
        transform: translateY(-6px);
    }

    .home-subject-icon {
        position: relative;
        z-index: 2;
        width: 76px !important;
        height: 76px !important;
        flex: 0 0 76px;
        margin: 0 auto 1rem !important;
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 22px !important;
        box-shadow:
            0 15px 30px rgba(0, 0, 0, 0.24),
            inset 0 1px 0 rgba(255, 255, 255, 0.15);
    }

    .home-subject-card:hover .home-subject-icon {
        box-shadow:
            0 20px 40px rgba(0, 0, 0, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.15);
        transform: translateY(-4px) scale(1.05);
    }

    .home-subject-card-title {
        position: relative;
        z-index: 2;
        width: 100%;
        min-height: auto;
        justify-content: center;
        margin-top: 0.25rem !important;
        margin-bottom: 0.75rem !important;
        color: var(--home-design-text) !important;
        font-size: 1.18rem;
        text-align: center !important;
        text-decoration: none !important;
    }

    .home-subject-card .badge {
        position: relative;
        z-index: 2;
        display: inline-flex;
        min-height: 30px;
        align-items: center;
        justify-content: center;
        padding: 0.4rem 0.85rem !important;
        border: 1px solid rgba(148, 163, 184, 0.11);
        font-weight: 650;
        letter-spacing: 0.01em;
        text-decoration: none !important;
    }

    .home-subject-card p {
        position: relative;
        z-index: 2;
        width: 100%;
        margin-top: 0.15rem;
        color: #a8b5c8 !important;
        text-align: center !important;
        text-decoration: none !important;
    }

    .home-subject-card p i {
        color: var(--home-design-gold) !important;
        transition: transform 180ms ease;
    }

    .home-subject-card:hover p i {
        transform: translateX(4px);
    }

    /* Titres de sections */
    .section-title-3d {
        max-width: 850px;
        margin-right: auto;
        margin-left: auto;
        color: var(--home-design-text);
        font-size: clamp(1.9rem, 3.4vw, 3rem);
        font-weight: 800;
        line-height: 1.18;
        letter-spacing: -0.04em;
        text-wrap: balance;
    }

    section .text-center > .badge {
        border: 1px solid rgba(148, 163, 184, 0.11);
        backdrop-filter: blur(10px);
    }

    /* Cartes générales */
    .card-3d,
    .step-3d {
        border: 1px solid var(--home-design-border) !important;
        background:
            linear-gradient(
                145deg,
                rgba(19, 32, 53, 0.94),
                rgba(11, 21, 36, 0.96)
            ) !important;
        box-shadow:
            0 18px 42px rgba(0, 0, 0, 0.16),
            inset 0 1px 0 rgba(255, 255, 255, 0.025);
        transition:
            transform 220ms ease,
            border-color 220ms ease,
            box-shadow 220ms ease;
    }

    .card-3d:hover,
    .step-3d:hover {
        border-color: var(--home-design-border-hover) !important;
        box-shadow: var(--home-design-shadow);
        transform: translateY(-5px);
    }

    .step-3d {
        height: 100%;
        min-height: 245px;
        padding: 2rem 1.5rem !important;
        border-radius: 20px !important;
        text-align: center;
    }

    .step-3d-number {
        display: grid;
        width: 58px;
        height: 58px;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 12px 26px rgba(0, 0, 0, 0.2);
    }

    /* Section pourquoi nous choisir */
    section .row.g-4.justify-content-center > .col {
        min-width: 210px;
    }

    section .row.g-4.justify-content-center > .col .card-3d {
        min-height: 280px;
        padding: 1.8rem 1.25rem !important;
        border-radius: 20px !important;
    }

    .card-3d-icon {
        width: 64px !important;
        height: 64px !important;
        border-radius: 18px !important;
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.22);
    }

    /* Section à propos */
    .card-3d.overflow-hidden.p-0 {
        border-radius: 24px !important;
    }

    .card-3d.overflow-hidden.p-0 img {
        transition: transform 500ms ease;
    }

    .card-3d.overflow-hidden.p-0:hover img {
        transform: scale(1.035);
    }

    /* Objectifs */
    section.text-center .row.g-4 .card-3d {
        height: 100%;
        border-radius: 20px !important;
    }

    section.text-center .row.g-4 .card-3d .p-4 {
        min-height: 190px;
    }

    /* Statistiques */
    #statsSection {
        position: relative;
    }

    #statsSection .row {
        overflow: hidden;
        border: 1px solid var(--home-design-border);
        border-radius: 22px;
        background:
            linear-gradient(
                135deg,
                rgba(17, 30, 50, 0.92),
                rgba(10, 19, 33, 0.94)
            );
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.16);
    }

    #statsSection .row > div {
        padding: 0;
    }

    .stat-3d {
        display: flex;
        min-height: 150px;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 1.5rem;
        text-align: center;
        border-right: 1px solid var(--home-design-border);
    }

    #statsSection .row > div:last-child .stat-3d {
        border-right: 0;
    }

    .stat-3d-number {
        color: transparent;
        background: linear-gradient(
            100deg,
            #91aaff,
            #b798ff,
            #f0ba53
        );
        background-clip: text;
        -webkit-background-clip: text;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        line-height: 1;
    }

    .stat-3d-label {
        margin-top: 0.65rem;
        color: var(--home-design-muted);
        font-size: 0.72rem;
        font-weight: 650;
    }

    /* Boutons */
    .btn-3d {
        min-height: 48px;
        border-radius: 13px !important;
        font-weight: 750;
        transition:
            transform 180ms ease,
            box-shadow 180ms ease,
            border-color 180ms ease;
    }

    .btn-3d:hover {
        transform: translateY(-2px);
    }

    .btn-3d-gradient {
        box-shadow: 0 14px 30px rgba(79, 111, 245, 0.22);
    }

    /* CTA final */
    .cta-3d {
        position: relative;
        overflow: hidden;
        padding-top: 6rem !important;
        padding-bottom: 6rem !important;
        border-top: 1px solid rgba(148, 163, 184, 0.08);
        background:
            radial-gradient(
                circle at 20% 20%,
                rgba(79, 111, 245, 0.18),
                transparent 32%
            ),
            radial-gradient(
                circle at 85% 70%,
                rgba(118, 83, 234, 0.15),
                transparent 28%
            ),
            linear-gradient(
                135deg,
                #0d1b32,
                #10162b
            ) !important;
    }

    .cta-3d::before {
        position: absolute;
        top: -130px;
        right: -120px;
        width: 360px;
        height: 360px;
        content: "";
        border: 55px solid rgba(255, 255, 255, 0.025);
        border-radius: 50%;
    }

    .section-title-3d-light {
        color: var(--home-design-text);
        letter-spacing: -0.04em;
        text-wrap: balance;
    }

    /* Animations plus douces */
    .edu-icon {
        filter: drop-shadow(
            0 8px 18px rgba(0, 0, 0, 0.12)
        );
    }

    .reveal-3d {
        transition:
            opacity 500ms ease,
            transform 500ms ease,
            border-color 220ms ease,
            box-shadow 220ms ease !important;
    }

    /* Responsive */
    @media (max-width: 1199.98px) {
        section .row.g-4.justify-content-center > .col {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
    }

    @media (max-width: 991.98px) {
        .hero-3d {
            padding-top: 110px !important;
            padding-bottom: 80px !important;
        }

        .home-subjects-grid {
            max-width: 780px;
        }

        section.py-5 {
            padding-top: 4.5rem !important;
            padding-bottom: 4.5rem !important;
        }

        section .row.g-4.justify-content-center > .col {
            flex: 0 0 50%;
            max-width: 50%;
        }

        #statsSection .row > div:nth-child(2) .stat-3d {
            border-right: 0;
        }

        #statsSection .row > div:nth-child(-n + 2) .stat-3d {
            border-bottom: 1px solid
                var(--home-design-border);
        }
    }

    @media (max-width: 767.98px) {
        .hero-3d-title {
            font-size: clamp(2.35rem, 11vw, 3.8rem);
        }

        .hero-3d-subtitle {
            font-size: 1rem !important;
        }

        .home-subject-card {
            min-height: 235px;
        }

        section .row.g-4.justify-content-center > .col {
            flex: 0 0 100%;
            max-width: 100%;
        }

        section .row.g-4.justify-content-center > .col .card-3d {
            min-height: 230px;
        }

        .section-title-3d-light {
            font-size: 2rem !important;
        }
    }

    @media (max-width: 575.98px) {
        .hero-3d {
            padding-top: 90px !important;
            padding-bottom: 65px !important;
        }

        .hero-3d-title {
            font-size: clamp(2.05rem, 12vw, 3rem);
        }

        .home-subjects-grid {
            max-width: 400px;
        }

        .home-subject-card {
            min-height: 220px;
            padding: 1.65rem 1.15rem !important;
        }

        .home-subject-icon {
            width: 68px !important;
            height: 68px !important;
            flex-basis: 68px;
            border-radius: 19px !important;
        }

        section.py-5 {
            padding-top: 3.8rem !important;
            padding-bottom: 3.8rem !important;
        }

        #statsSection .row {
            border-radius: 18px;
        }

        .stat-3d {
            min-height: 130px;
            border-right: 0;
            border-bottom: 1px solid
                var(--home-design-border);
        }

        #statsSection .row > div:last-child .stat-3d {
            border-bottom: 0;
        }

        .cta-3d .d-flex {
            align-items: stretch;
            flex-direction: column;
        }

        .cta-3d .btn-3d {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .home-subject-card,
        .card-3d,
        .step-3d,
        .btn-3d,
        .reveal-3d {
            transition: none !important;
            animation: none !important;
        }
    }
</style>

<!-- ══════════════════════════════════════════════════════
     HERO SECTION
     ══════════════════════════════════════════════════════ -->
<section class="hero-3d text-center" style="padding: 90px 0 100px;">
    <div class="container hero-3d-content">

        <h1 class="hero-3d-title mb-4 mx-auto" style="max-width: 850px;">
            La plateforme intelligente<br>
            <span class="gradient-text">La réussite est à portée de Clic</span>
        </h1>

        <p class="hero-3d-subtitle mx-auto mb-5" style="max-width: 600px; font-size: 1.15rem;">
            Cours interactifs, sessions live, quiz et suivi personnalisé — 
            tout ce qu'il vous faut pour exceller, accessible partout et à tout moment.
        </p>

        <div class="row g-4 justify-content-center mb-5 mx-auto home-subjects-grid">
            @foreach($homeSubjects as $subject)
                @php
                    $normalizedSubjectName = mb_strtolower(
                        trim($subject->name ?? '')
                    );

                    $subjectDesign = match ($normalizedSubjectName) {
                        'arabe' => [
                            'icon' => 'bi-translate',
                            'gradient' =>
                                'linear-gradient(135deg,#2563EB,#06B6D4)',
                        ],

                        'coran', 'quran', 'couran', 'القرآن', 'القران' => [
                            'icon' => 'bi-book-half',
                            'gradient' =>
                                'linear-gradient(135deg,#7C3AED,#A855F7)',
                        ],

                        'soutien lycée', 'soutien lycee',
                        'soutient lycée', 'soutient lycee' => [
                            'icon' => 'bi-mortarboard-fill',
                            'gradient' =>
                                'linear-gradient(135deg,#F59E0B,#EA580C)',
                        ],

                        'anglais', 'english' => [
                            'icon' => 'bi-globe2',
                            'gradient' =>
                                'linear-gradient(135deg,#0EA5E9,#2563EB)',
                        ],

                        'français', 'francais' => [
                            'icon' => 'bi-chat-square-text-fill',
                            'gradient' =>
                                'linear-gradient(135deg,#EC4899,#8B5CF6)',
                        ],

                        'mathématiques', 'mathematiques', 'maths' => [
                            'icon' => 'bi-calculator-fill',
                            'gradient' =>
                                'linear-gradient(135deg,#10B981,#059669)',
                        ],

                        default => [
                            'icon' => 'bi-journal-bookmark-fill',
                            'gradient' =>
                                'linear-gradient(135deg,#4F46E5,#7C3AED)',
                        ],
                    };
                @endphp

                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('front.subject.levels', $subject->id) }}"
                       class="text-decoration-none d-block h-100"
                       aria-label="Voir les niveaux de {{ $subject->name }}">
                        <div class="card-3d text-center h-100 reveal-3d home-subject-card" style="cursor:pointer;">
                            <div
                                class="home-subject-icon"
                                style="background:{{ $subjectDesign['gradient'] }};"
                                aria-hidden="true"
                            >
                                <i class="bi {{ $subjectDesign['icon'] }}"></i>
                            </div>

                            <h5
                                class="fw-bold text-white mt-2 mb-2 home-subject-card-title"
                                style="font-family:'Poppins',sans-serif;"
                            >
                                {{ $subject->name }}
                            </h5>

                            <span
                                class="badge mx-auto mb-3"
                                style="
                                    background:{{
                                        $subject->type === 'religieux'
                                            ? 'rgba(155,89,182,0.2)'
                                            : 'rgba(52,152,219,0.2)'
                                    }};
                                    color:{{
                                        $subject->type === 'religieux'
                                            ? '#D7A1F9'
                                            : '#7DD3FC'
                                    }};
                                    border-radius:20px;
                                    font-size:0.72rem;
                                "
                            >
                                {{
                                    $subject->type === 'religieux'
                                        ? 'Matière religieuse'
                                        : 'Matière scolaire'
                                }}
                            </span>

                            <p class="text-white-50 small mb-0">
                                Voir les niveaux
                                <i
                                    class="bi bi-arrow-right ms-1"
                                    style="color:var(--3d-gold);"
                                ></i>
                            </p>
                        </div>
                    </a>
                </div>
            @endforeach

            @if($hasMoreSubjects)
                <div class="col-lg-4 col-md-6">
                    <a
                        href="{{ route('front.classes') }}"
                        class="text-decoration-none d-block h-100"
                        aria-label="Explorer toutes les matières"
                    >
                        <div
                            class="
                                card-3d
                                text-center
                                h-100
                                reveal-3d
                                home-subject-card
                                home-subject-explore-card
                            "
                            style="cursor:pointer;"
                        >
                            <div
                                class="home-subject-icon"
                                style="
                                    background:
                                        linear-gradient(
                                            135deg,
                                            #7C3AED,
                                            #F59E0B
                                        );
                                "
                                aria-hidden="true"
                            >
                                <i class="bi bi-grid-3x3-gap-fill"></i>
                            </div>

                            <h5
                                class="fw-bold text-white mt-2 mb-2 home-subject-card-title"
                                style="font-family:'Poppins',sans-serif;"
                            >
                                Explorer les autres matières
                            </h5>

                            <span
                                class="badge mx-auto mb-3"
                                style="
                                    background:rgba(245,158,11,0.14);
                                    color:#FCD34D;
                                    border-radius:20px;
                                    font-size:0.72rem;
                                "
                            >
                                +{{ $otherSubjectsCount }}
                                {{
                                    $otherSubjectsCount > 1
                                        ? 'matières'
                                        : 'matière'
                                }}
                            </span>

                            <p class="text-white-50 small mb-0">
                                Voir toutes les matières
                                <i
                                    class="bi bi-arrow-right ms-1"
                                    style="color:var(--3d-gold);"
                                ></i>
                            </p>
                        </div>
                    </a>
                </div>
            @endif
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


<style>
    /* =========================================================
       SECTIONS PRINCIPALES — VERSION PLUS COMPACTE
       ========================================================= */

    #howItWorksSection,
    #whyChooseUsSection,
    #aboutSection,
    #objectivesSection {
        padding-top: 3.4rem !important;
        padding-bottom: 3.4rem !important;
    }

    #howItWorksSection .text-center.mb-5,
    #whyChooseUsSection .text-center.mb-5,
    #objectivesSection .text-center.mb-5 {
        margin-bottom: 2rem !important;
    }

    #howItWorksSection .section-title-3d,
    #whyChooseUsSection .section-title-3d,
    #aboutSection .section-title-3d,
    #objectivesSection .section-title-3d {
        font-size: clamp(1.65rem, 2.7vw, 2.4rem);
        line-height: 1.15;
    }

    #howItWorksSection .badge,
    #whyChooseUsSection .badge,
    #aboutSection .badge,
    #objectivesSection .badge {
        padding: 0.4rem 0.8rem !important;
        font-size: 0.72rem !important;
        margin-bottom: 0.75rem !important;
    }

    /* Comment ça marche */
    #howItWorksSection .step-3d {
        min-height: 205px;
        padding: 1.35rem 1.15rem !important;
    }

    #howItWorksSection .step-3d-number {
        width: 48px;
        height: 48px;
        font-size: 0.95rem;
    }

    #howItWorksSection .step-3d h5 {
        margin-top: 0.85rem !important;
        font-size: 1rem;
    }

    #howItWorksSection .step-3d p {
        font-size: 0.82rem;
        line-height: 1.55 !important;
    }

    #howItWorksSection .text-center.mt-5 {
        margin-top: 2rem !important;
    }

    #howItWorksSection .btn-3d {
        min-height: 44px;
        padding: 0.7rem 1.5rem !important;
        font-size: 0.92rem !important;
    }

    /* Pourquoi nous choisir */
    #whyChooseUsSection .row.g-4.justify-content-center > .col .card-3d {
        min-height: 225px;
        padding: 1.3rem 1rem !important;
    }

    #whyChooseUsSection .card-3d-icon {
        width: 52px !important;
        height: 52px !important;
        border-radius: 15px !important;
        margin-bottom: 0.85rem !important;
    }

    #whyChooseUsSection .card-3d h5 {
        font-size: 0.96rem;
    }

    #whyChooseUsSection .card-3d p {
        font-size: 0.78rem;
        line-height: 1.55 !important;
    }

    /* Qui sommes-nous */
    #aboutSection .row {
        row-gap: 2rem !important;
    }

    #aboutSection .card-3d.overflow-hidden.p-0 img {
        height: 325px !important;
    }

    #aboutSection .section-title-3d {
        margin-bottom: 1rem !important;
    }

    #aboutSection p {
        font-size: 0.92rem !important;
        line-height: 1.65 !important;
    }

    #aboutSection .d-flex.flex-column.gap-3 {
        gap: 0.75rem !important;
    }

    #aboutSection .d-flex.align-items-start.gap-3 {
        gap: 0.75rem !important;
    }

    #aboutSection .d-flex.align-items-start.gap-3 > span {
        width: 32px !important;
        height: 32px !important;
        border-radius: 9px !important;
    }

    #aboutSection strong {
        font-size: 0.92rem;
    }

    #aboutSection .btn-3d {
        min-height: 44px;
        margin-top: 1rem !important;
        padding: 0.7rem 1.1rem !important;
        font-size: 0.88rem;
    }

    /* Nos objectifs */
    #objectivesSection {
        padding-bottom: 2.4rem !important;
    }

    #objectivesSection .card-3d > div:first-child {
        height: 160px !important;
    }

    #objectivesSection .card-3d .p-4 {
        min-height: 150px !important;
        padding: 1.2rem !important;
    }

    #objectivesSection .card-3d .p-4 > div:first-child {
        width: 34px !important;
        height: 34px !important;
        margin-bottom: 0.75rem !important;
    }

    #objectivesSection .card-3d h5 {
        font-size: 0.96rem;
    }

    #objectivesSection .card-3d p {
        font-size: 0.78rem;
        line-height: 1.55;
    }

    @media (max-width: 991.98px) {
        #howItWorksSection,
        #whyChooseUsSection,
        #aboutSection,
        #objectivesSection {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }

        #aboutSection .card-3d.overflow-hidden.p-0 img {
            height: 290px !important;
        }
    }

    @media (max-width: 575.98px) {
        #howItWorksSection,
        #whyChooseUsSection,
        #aboutSection,
        #objectivesSection {
            padding-top: 2.4rem !important;
            padding-bottom: 2.4rem !important;
        }

        #howItWorksSection .section-title-3d,
        #whyChooseUsSection .section-title-3d,
        #aboutSection .section-title-3d,
        #objectivesSection .section-title-3d {
            font-size: 1.65rem;
        }

        #howItWorksSection .step-3d,
        #whyChooseUsSection .row.g-4.justify-content-center > .col .card-3d {
            min-height: auto;
        }

        #aboutSection .card-3d.overflow-hidden.p-0 img {
            height: 235px !important;
        }

        #objectivesSection .card-3d > div:first-child {
            height: 145px !important;
        }
    }
</style>

<section class="home-promo-videos py-5" id="videos-presentation">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 mb-3 home-video-badge">
                <i class="bi bi-play-circle-fill me-1"></i>
                Découvrez Smart School
            </span>
            <h2 class="section-title-3d">Smart School Academy en vidéos</h2>
            <p class="text-white-50 mt-3 mx-auto home-video-intro">
                Découvrez nos cours, notre accompagnement et les étapes pour rejoindre la plateforme.
            </p>
        </div>

        @php
            $promoVideos = [
                [
                    'src' => 'videos/promotions/inscription-3-etapes.mp4',
                    'title' => 'Inscrivez-vous en 3 étapes',
                    'subtitle' => 'Choisissez un cours, un créneau puis réalisez votre entretien.',
                    'icon' => 'bi-person-check-fill',
                ],
                [
                    'src' => 'videos/promotions/coran-soeurs.mp4',
                    'title' => 'Cours de Coran pour les sœurs',
                    'subtitle' => 'Progressez dans votre lecture du Coran à votre rythme.',
                    'icon' => 'bi-book-half',
                ],
                [
                    'src' => 'videos/promotions/coran-apprentissage.mp4',
                    'title' => 'Lire, réciter et mémoriser le Coran',
                    'subtitle' => 'Un accompagnement progressif adapté à votre niveau.',
                    'icon' => 'bi-stars',
                ],
                [
                    'src' => 'videos/promotions/arabe-live.mp4',
                    'title' => 'Cours d’arabe en live',
                    'subtitle' => 'Transformez votre écran en véritable outil d’apprentissage.',
                    'icon' => 'bi-camera-video-fill',
                ],
            ];
        @endphp

        <div class="row g-4 justify-content-center">
            @foreach($promoVideos as $video)
                <div class="col-lg-6">
                    <article class="home-video-card reveal-3d h-100">
                        <div class="home-video-player-wrap">
                            <video
                                class="home-promo-video"
                                autoplay
                                muted
                                loop
                                controls
                                playsinline
                                preload="auto"
                                aria-label="{{ $video['title'] }}"
                            >
                                <source src="{{ '/' . ltrim($video['src'], '/') }}" type="video/mp4">
                                Votre navigateur ne prend pas en charge la lecture vidéo.
                            </video>
                        </div>
                        <div class="home-video-copy">
                            <div class="home-video-icon" aria-hidden="true">
                                <i class="bi {{ $video['icon'] }}"></i>
                            </div>
                            <div>
                                <h3>{{ $video['title'] }}</h3>
                                <p>{{ $video['subtitle'] }}</p>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('front.classes') }}" class="btn-3d btn-3d-outline home-video-cta">
                <i class="bi bi-grid-fill"></i>
                Découvrir toutes nos matières
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <style>
        .home-promo-videos {
            position: relative;
            background:
                radial-gradient(circle at 20% 10%, rgba(79, 70, 229, 0.11), transparent 32%),
                radial-gradient(circle at 82% 82%, rgba(168, 85, 247, 0.08), transparent 34%);
            border-top: 1px solid rgba(148, 163, 184, 0.08);
            border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        }

        .home-video-badge {
            background: rgba(59, 130, 246, 0.11);
            color: #93C5FD;
            border: 1px solid rgba(96, 165, 250, 0.2);
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.78rem;
            letter-spacing: 0.04em;
        }

        .home-video-intro {
            max-width: 650px;
            line-height: 1.8;
        }

        .home-video-card {
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 24px;
            background: linear-gradient(145deg, rgba(18, 29, 51, 0.96), rgba(10, 18, 34, 0.98));
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.24);
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        .home-video-card:hover {
            transform: translateY(-5px);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 30px 72px rgba(0, 0, 0, 0.34);
        }

        .home-video-player-wrap {
            position: relative;
            background: #030712;
            aspect-ratio: 16 / 9;
            overflow: hidden;
        }

        .home-promo-video {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: contain;
            background: #030712;
        }

        .home-video-copy {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            padding: 1.25rem 1.35rem 1.35rem;
        }

        .home-video-icon {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            color: #fff;
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
        }

        .home-video-copy h3 {
            margin: 0 0 .35rem;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .home-video-copy p {
            margin: 0;
            color: rgba(255,255,255,.54);
            font-size: .86rem;
            line-height: 1.65;
        }

        .home-video-cta {
            display: inline-flex;
            align-items: center;
            gap: .65rem;
        }

        @media (max-width: 767.98px) {
            .home-video-card {
                border-radius: 18px;
            }

            .home-video-copy {
                padding: 1rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const videos = Array.from(document.querySelectorAll('.home-promo-video'));

            // L'autoplay avec son est bloqué par les navigateurs modernes.
            // Les vidéos démarrent donc automatiquement en muet ; l'utilisateur
            // peut activer le son directement depuis les contrôles du lecteur.
            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    const video = entry.target;

                    if (entry.isIntersecting && entry.intersectionRatio >= 0.35) {
                        video.play().catch(function () {
                            // Si le navigateur refuse l'autoplay, les contrôles restent disponibles.
                        });
                    } else {
                        video.pause();
                    }
                });
            }, {
                threshold: [0, 0.35, 0.75]
            });

            videos.forEach(function (video) {
                // Force le chargement des métadonnées immédiatement. Les MP4 sont
                // optimisés avec le bloc MOOV au début (faststart), donc Chrome peut
                // afficher la durée et la première image sans télécharger tout le fichier.
                video.load();

                video.addEventListener('loadedmetadata', function () {
                    if (video.getBoundingClientRect().top < window.innerHeight &&
                        video.getBoundingClientRect().bottom > 0) {
                        video.play().catch(function () {});
                    }
                }, { once: true });

                observer.observe(video);
            });
        });
    </script>
</section>

<!-- ══════════════════════════════════════════════════════
     HOW IT WORKS
     ══════════════════════════════════════════════════════ -->
<section class="py-5" id="howItWorksSection">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 mb-3" style="background: rgba(22, 163, 74, 0.12); color: #4ADE80; border-radius: 20px; font-weight: 500; font-size: 0.8rem; letter-spacing: 0.05em;">
                Comment ça marche
            </span>
            <h2 class="section-title-3d">Commencez en 3 étapes simples</h2>
            <p class="text-white-50 mt-3" style="max-width: 500px; margin: 0 auto;">
                Pas de complexité. Créez votre compte, choisissez votre niveau et commencez à apprendre.
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
<section class="py-5" id="whyChooseUsSection">
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
                        Un accompagnement sur mesure avec des enseignants diplômés pour vous aider à atteindre vos objectifs.
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
<section class="py-5" id="aboutSection">
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
                                <i class="bi bi-broadcast" style="color: #2563EB; font-size: 0.9rem;"></i>
                            </span>
                            <div>
                                <strong class="text-white">Cours interactifs en direct (lives)</strong>
                                <p class="text-white-50 small mb-0">Enseignement en temps réel favorisant l'échange et l'implication des élèves</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(124, 58, 237, 0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="bi bi-graph-up-arrow" style="color: #7C3AED; font-size: 0.9rem;"></i>
                            </span>
                            <div>
                                <strong class="text-white">Suivi personnalisé & précis</strong>
                                <p class="text-white-50 small mb-0">Analyse du niveau, détection des axes de progression et parcours adapté</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 209, 102, 0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="bi bi-arrow-up-circle-fill" style="color: #FFD166; font-size: 0.9rem;"></i>
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
<section class="py-5 text-center" id="objectivesSection">
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
     PRISE DE CONTACT — après les objectifs
     ══════════════════════════════════════════════════════ -->
<section class="home-contact-section py-5" aria-labelledby="homeContactTitle">
    <div class="container">
        <div id="prise-de-contact" class="home-contact-wrap">
            <div class="home-contact-card">
                <div class="home-contact-head">
                    <div>
                        <span class="home-contact-eyebrow">
                            <i class="bi bi-headset"></i>
                            Prise de contact
                        </span>

                        <h2 class="home-contact-title" id="homeContactTitle">
                            Vous souhaitez en savoir plus ?
                        </h2>

                        <p class="home-contact-subtitle">
                            Laissez vos coordonnées. Notre équipe vous recontactera rapidement.
                        </p>
                    </div>
                </div>

                @if(session('contact_success'))
                    <div class="home-contact-alert success" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('contact_success') }}</span>
                    </div>
                @endif

                @if(session('contact_error'))
                    <div class="home-contact-alert error" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ session('contact_error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="home-contact-alert error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <div>
                            <strong>Veuillez vérifier les informations saisies.</strong>
                            <ul class="home-contact-errors">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('contact.store') }}"
                    class="home-contact-form"
                    autocomplete="on"
                >
                    @csrf

                    <div class="home-contact-field">
                        <label for="contact_last_name">Nom</label>
                        <input
                            id="contact_last_name"
                            type="text"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            class="home-contact-input @error('last_name') is-invalid @enderror"
                            placeholder="Votre nom"
                            autocomplete="family-name"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="home-contact-field">
                        <label for="contact_first_name">Prénom</label>
                        <input
                            id="contact_first_name"
                            type="text"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            class="home-contact-input @error('first_name') is-invalid @enderror"
                            placeholder="Votre prénom"
                            autocomplete="given-name"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="home-contact-field">
                        <label for="contact_email">E-mail</label>
                        <input
                            id="contact_email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="home-contact-input @error('email') is-invalid @enderror"
                            placeholder="nom@email.com"
                            autocomplete="email"
                            maxlength="190"
                            required
                        >
                    </div>

                    <div class="home-contact-field">
                        <label for="contact_phone">Téléphone</label>
                        <input
                            id="contact_phone"
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            class="home-contact-input @error('phone') is-invalid @enderror"
                            placeholder="06 00 00 00 00"
                            autocomplete="tel"
                            maxlength="30"
                            required
                        >
                    </div>

                    <div class="home-contact-field">
                        <label for="contact_country">Pays</label>
                        <input
                            id="contact_country"
                            type="text"
                            name="country"
                            value="{{ old('country') }}"
                            class="home-contact-input @error('country') is-invalid @enderror"
                            placeholder="Ex. Maroc"
                            autocomplete="country-name"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="home-contact-field home-contact-field-full">
                        <label for="contact_reason">
                            Commentaire / Raison
                        </label>

                        <textarea
                            id="contact_reason"
                            name="reason"
                            class="home-contact-textarea @error('reason') is-invalid @enderror"
                            rows="4"
                            maxlength="1500"
                            placeholder="Exemple : Je souhaite avoir plus d’informations sur les cours, les tarifs ou les horaires..."
                            required
                        >{{ old('reason') }}</textarea>

                        @error('reason')
                            <small style="
                                display:block;
                                margin-top:.35rem;
                                color:#fca5a5;
                                font-size:.7rem;
                            ">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="home-contact-consent">
                        <input
                            id="contact_marketing_consent"
                            type="checkbox"
                            name="marketing_consent"
                            value="1"
                            {{ old('marketing_consent') ? 'checked' : '' }}
                        >

                        <label for="contact_marketing_consent">
                            <strong>Informations et offres :</strong>
                            j’accepte de recevoir les actualités et offres
                            de Smart School Academy par e-mail.
                        </label>
                    </div>

                    <div class="home-contact-honeypot" aria-hidden="true">
                        <label for="contact_website">Site web</label>
                        <input
                            id="contact_website"
                            type="text"
                            name="website"
                            value=""
                            tabindex="-1"
                            autocomplete="off"
                        >
                    </div>

                    <div class="home-contact-action">
                        <button type="submit" class="home-contact-submit">
                            <i class="bi bi-send-fill"></i>
                            Être contacté
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>




<style>
    /* =========================================================
       CTA FINAL — ESPACEMENT ET ALIGNEMENT CORRIGÉS
       ========================================================= */

    #objectivesSection {
        padding-bottom: 1rem !important;
    }

    .final-cta {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        width: 100%;
        margin: 0 !important;
        padding: 2rem 1.25rem 1.85rem;
        border-top: 1px solid rgba(148, 163, 184, 0.10);
        background:
            radial-gradient(
                circle at 12% 18%,
                rgba(79, 111, 245, 0.23),
                transparent 30%
            ),
            radial-gradient(
                circle at 88% 82%,
                rgba(118, 83, 234, 0.20),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #10203d 0%,
                #101a38 48%,
                #18163f 100%
            );
    }

    .final-cta::before {
        position: absolute;
        top: -145px;
        left: -105px;
        z-index: -1;
        width: 250px;
        height: 250px;
        content: "";
        pointer-events: none;
        border: 58px solid rgba(111, 99, 255, 0.07);
        border-radius: 50%;
    }

    .final-cta::after {
        position: absolute;
        right: -95px;
        bottom: -145px;
        z-index: -1;
        width: 245px;
        height: 245px;
        content: "";
        pointer-events: none;
        border-radius: 50%;
        background: rgba(106, 76, 255, 0.08);
        filter: blur(2px);
    }

    .final-cta__content {
        position: relative;
        z-index: 2;
        width: min(100%, 1080px);
        margin: 0 auto;
        text-align: center;
    }

    .final-cta__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 0.4rem 0.8rem;
        margin: 0 0 0.65rem;
        color: #dbe3f2;
        font-size: 0.78rem;
        font-weight: 700;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
    }

    .final-cta__title {
        max-width: 900px;
        margin: 0 auto 0.6rem;
        color: #ffffff;
        font-size: clamp(1.75rem, 3vw, 2.65rem);
        font-weight: 900;
        line-height: 1.08;
        letter-spacing: -0.045em;
        text-wrap: balance;
    }

    .final-cta__title span {
        display: block;
        margin-top: 0.15rem;
        color: transparent;
        background: linear-gradient(
            100deg,
            #91aaff 0%,
            #b798ff 48%,
            #f0ba53 100%
        );
        background-clip: text;
        -webkit-background-clip: text;
    }

    .final-cta__description {
        max-width: 720px;
        margin: 0 auto 1rem;
        color: #aeb9ce;
        font-size: 0.9rem;
        line-height: 1.75;
        text-wrap: balance;
    }

    .final-cta__actions {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .final-cta__button {
        display: inline-flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        gap: 0.65rem;
        padding: 0.62rem 1rem;
        color: #e8edf7;
        font-size: 0.9rem;
        font-weight: 800;
        text-decoration: none !important;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.035);
        transition:
            transform 180ms ease,
            box-shadow 180ms ease,
            border-color 180ms ease,
            background 180ms ease;
    }

    .final-cta__button:hover {
        color: #ffffff;
        border-color: rgba(139, 119, 255, 0.58);
        background: rgba(109, 84, 255, 0.12);
        transform: translateY(-3px);
    }

    .final-cta__button--primary {
        color: #17213b;
        border-color: #ffc354;
        background: linear-gradient(135deg, #ffd269, #ffb73d);
        box-shadow: 0 14px 30px rgba(255, 184, 61, 0.23);
    }

    .final-cta__button--primary:hover {
        color: #17213b;
        border-color: #ffd16a;
        background: linear-gradient(135deg, #ffda82, #ffc04d);
        box-shadow: 0 18px 38px rgba(255, 184, 61, 0.32);
    }

    .final-cta__security {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        margin-top: 0.7rem;
        color: #8e9ab0;
        font-size: 0.78rem;
    }

    @media (max-width: 767.98px) {
        #objectivesSection {
            padding-bottom: 1.5rem !important;
        }

        .final-cta {
            padding: 1.9rem 0.9rem 1.75rem;
        }

        .final-cta__title {
            font-size: clamp(1.7rem, 8.5vw, 2.45rem);
            letter-spacing: -0.035em;
        }

        .final-cta__description {
            font-size: 0.9rem;
        }

        .final-cta__actions {
            flex-direction: column;
        }

        .final-cta__button {
            width: min(100%, 380px);
        }
    }

    @media (max-width: 575.98px) {
        .final-cta {
            padding-top: 1.7rem;
            padding-bottom: 1.55rem;
        }

        .final-cta__badge {
            margin-bottom: 1rem;
        }

        .final-cta__title {
            margin-bottom: 1rem;
        }

        .final-cta__description {
            margin-bottom: 1.5rem;
        }
    }
</style>

<!-- ══════════════════════════════════════════════════════
     FINAL CTA
     ══════════════════════════════════════════════════════ -->
<section class="final-cta" id="finalCta">
    <div class="final-cta__content">

        <span class="final-cta__badge">
            🚀 Prêt à commencer ?
        </span>

        <h2 class="final-cta__title">
            Rejoignez Smart School Academy
            <span>et progressez à votre rythme</span>
        </h2>

        <p class="final-cta__description">
            Accédez à vos matières, vos cours, vos lives, vos devoirs
            et vos outils de suivi depuis un seul espace.
        </p>

        <div class="final-cta__actions">
            <a href="{{ route('register') }}"
               class="final-cta__button final-cta__button--primary">
                <i class="bi bi-rocket-takeoff"></i>
                S’inscrire maintenant
                <i class="bi bi-arrow-right"></i>
            </a>

            <a href="{{ route('appointment.create') }}"
               class="final-cta__button final-cta__button--secondary">
                <i class="bi bi-calendar-check"></i>
                Prendre contact
            </a>

            <a href="{{ route('plans') }}"
               class="final-cta__button final-cta__button--secondary">
                <i class="bi bi-credit-card"></i>
                Voir les offres
            </a>
        </div>

        <div class="final-cta__security">
            <i class="bi bi-shield-check"></i>
            Sans engagement · Paiement sécurisé
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

{{-- Design global V12 : présentation uniquement, aucun contenu modifié. --}}
@push('scripts')
<link
    rel="stylesheet"
    href="{{ asset('css/front-design-v12.css?v=12.0') }}"
>
@endpush