@extends('layouts.admin')

@section('title', 'Créer un étudiant')
@section('page_title', 'Nouvel étudiant')
@section('breadcrumb', 'Création assistée du compte étudiant')

@section('content')
<style>
    .student-create-page {
        --bg: #070b14;
        --panel: #0d1321;
        --panel-2: #111827;
        --panel-3: #172033;
        --border: rgba(148, 163, 184, .16);
        --border-strong: rgba(96, 165, 250, .35);
        --text: #f8fafc;
        --muted: #94a3b8;
        --primary: #3b82f6;
        --primary-2: #7c3aed;
        --success: #22c55e;
        --danger: #ef4444;
        --warning: #f59e0b;

        max-width: 1000px;
        margin: 0 auto;
        padding: 18px 0 40px;
        color: var(--text);
    }

    .student-create-page,
    .student-create-page * {
        box-sizing: border-box;
    }

    .student-hero {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        padding: 34px 32px;
        margin-bottom: 22px;
        border: 1px solid rgba(96,165,250,.18);
        background:
            radial-gradient(circle at 88% 18%, rgba(124,58,237,.24), transparent 34%),
            radial-gradient(circle at 70% 100%, rgba(59,130,246,.16), transparent 42%),
            linear-gradient(135deg, #0b1120 0%, #111827 54%, #171c2e 100%);
        box-shadow: 0 20px 45px rgba(0,0,0,.28);
    }

    .student-hero::after {
        content: "🎓";
        position: absolute;
        right: 30px;
        bottom: -10px;
        font-size: 100px;
        opacity: .075;
        filter: grayscale(1);
        transform: rotate(-7deg);
    }

    .student-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 11px;
        margin-bottom: 13px;
        border-radius: 999px;
        border: 1px solid rgba(96,165,250,.24);
        background: rgba(59,130,246,.09);
        color: #93c5fd;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .student-hero h1 {
        margin: 0 0 9px;
        max-width: 700px;
        color: #fff;
        font-size: clamp(27px, 4vw, 39px);
        line-height: 1.1;
        font-weight: 850;
        letter-spacing: -.03em;
    }

    .student-hero p {
        max-width: 720px;
        margin: 0;
        color: #9caac0;
        font-size: 14px;
        line-height: 1.75;
    }

    .student-form-card {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(17,24,39,.98), rgba(13,19,33,.98));
        box-shadow: 0 18px 45px rgba(0,0,0,.30);
    }

    .student-form-body {
        padding: 30px;
    }

    .section-block + .section-block {
        margin-top: 30px;
        padding-top: 28px;
        border-top: 1px solid rgba(148,163,184,.10);
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 21px;
    }

    .section-icon {
        flex: 0 0 44px;
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        border: 1px solid rgba(96,165,250,.18);
        background: rgba(59,130,246,.09);
        color: #60a5fa;
        font-size: 18px;
    }

    .section-title h2 {
        margin: 0;
        color: #f8fafc;
        font-size: 16px;
        font-weight: 800;
    }

    .section-title p {
        margin: 4px 0 0;
        color: #77869e;
        font-size: 12.5px;
    }

    .field-label {
        display: block;
        margin-bottom: 8px;
        color: #cbd5e1;
        font-size: 12.5px;
        font-weight: 750;
    }

    .field-label .required {
        color: #f87171;
    }

    .input-wrap {
        position: relative;
    }

    .input-wrap > i {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 15px;
        pointer-events: none;
        transition: color .2s ease;
    }

    .student-input {
        width: 100%;
        height: 50px;
        outline: none;
        border: 1px solid rgba(148,163,184,.16);
        border-radius: 12px;
        padding: 0 14px 0 42px;
        background: #0a101c;
        color: #f8fafc;
        font-size: 13.5px;
        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .student-input::placeholder {
        color: #526078;
    }

    .student-input:hover {
        border-color: rgba(148,163,184,.28);
    }

    .student-input:focus {
        border-color: #3b82f6;
        background: #0c1423;
        box-shadow: 0 0 0 4px rgba(59,130,246,.10);
    }

    .input-wrap:focus-within > i {
        color: #60a5fa;
    }

    .student-input.is-invalid {
        border-color: rgba(239,68,68,.65);
        box-shadow: 0 0 0 4px rgba(239,68,68,.07);
    }

    .field-error {
        margin-top: 7px;
        color: #f87171;
        font-size: 12px;
        font-weight: 650;
    }

    .security-box {
        position: relative;
        overflow: hidden;
        margin-top: 30px;
        border: 1px solid rgba(59,130,246,.22);
        border-radius: 16px;
        padding: 18px;
        background:
            radial-gradient(circle at 100% 0%, rgba(124,58,237,.12), transparent 38%),
            rgba(59,130,246,.055);
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .security-box .icon {
        flex: 0 0 44px;
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        border: 1px solid rgba(96,165,250,.19);
        background: rgba(59,130,246,.11);
        color: #60a5fa;
        font-size: 17px;
    }

    .security-box h3 {
        margin: 1px 0 5px;
        color: #dbeafe;
        font-size: 13.5px;
        font-weight: 800;
    }

    .security-box p {
        margin: 0;
        color: #8fa0b8;
        font-size: 12.5px;
        line-height: 1.65;
    }

    .security-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin-top: 12px;
    }

    .security-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        border: 1px solid rgba(148,163,184,.13);
        border-radius: 999px;
        background: rgba(255,255,255,.035);
        color: #aebbd0;
        font-size: 11.5px;
        font-weight: 700;
    }

    .security-pill i {
        color: #60a5fa;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 30px;
        padding-top: 24px;
        border-top: 1px solid rgba(148,163,184,.10);
    }

    .ssa-btn {
        min-height: 47px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 0 18px;
        border: 1px solid transparent;
        border-radius: 12px;
        font-size: 12.5px;
        font-weight: 800;
        text-decoration: none !important;
        cursor: pointer;
        transition:
            transform .2s ease,
            box-shadow .2s ease,
            border-color .2s ease,
            background .2s ease;
    }

    .ssa-btn-secondary {
        border-color: rgba(148,163,184,.17);
        background: #0b1220;
        color: #aebbd0;
    }

    .ssa-btn-secondary:hover {
        border-color: rgba(148,163,184,.28);
        background: #101827;
        color: #fff;
    }

    .ssa-btn-primary {
        border-color: rgba(96,165,250,.20);
        background: linear-gradient(135deg, #2563eb 0%, #4f46e5 58%, #6d28d9 100%);
        color: #fff;
        box-shadow: 0 12px 25px rgba(37,99,235,.19);
    }

    .ssa-btn-primary:hover {
        transform: translateY(-1px);
        color: #fff;
        box-shadow: 0 16px 32px rgba(37,99,235,.25);
    }

    .alert-modern {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 22px;
        padding: 14px 16px;
        border-radius: 13px;
        font-size: 12.5px;
        line-height: 1.6;
    }

    .alert-modern-danger {
        border: 1px solid rgba(239,68,68,.25);
        background: rgba(239,68,68,.075);
        color: #fecaca;
    }

    @media (max-width: 767px) {
        .student-create-page {
            padding-top: 5px;
        }

        .student-hero {
            padding: 25px 20px;
            border-radius: 18px;
        }

        .student-hero::after {
            right: 8px;
            font-size: 72px;
        }

        .student-form-card {
            border-radius: 18px;
        }

        .student-form-body {
            padding: 22px 18px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .ssa-btn {
            width: 100%;
        }

        .security-box {
            padding: 15px;
        }
    }
</style>

<div class="student-create-page">

    <div class="student-hero">
        <div class="student-eyebrow">
            <i class="bi bi-person-plus-fill"></i>
            Nouvel étudiant
        </div>

        <h1>Créer un compte étudiant</h1>

        <p>
            Utilisez ce formulaire lorsqu’un étudiant rencontre un problème avec l’inscription en ligne.
            Son compte sera créé automatiquement et ses accès lui seront envoyés par e-mail.
        </p>
    </div>

    <div class="student-form-card">
        <div class="student-form-body">

            @if(session('error'))
                <div class="alert-modern alert-modern-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="section-block">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="bi bi-person-vcard"></i>
                        </div>

                        <div>
                            <h2>Informations personnelles</h2>
                            <p>Renseignez les informations essentielles de l’étudiant.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="field-label" for="name">
                                Nom complet <span class="required">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-person"></i>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="student-input @error('name') is-invalid @enderror"
                                    placeholder="Ex. Mohamed El Amrani"
                                    maxlength="255"
                                    required
                                    autofocus
                                >
                            </div>

                            @error('name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="field-label" for="email">
                                Adresse e-mail <span class="required">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-envelope"></i>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="student-input @error('email') is-invalid @enderror"
                                    placeholder="etudiant@example.com"
                                    maxlength="255"
                                    required
                                >
                            </div>

                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="section-block">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>

                        <div>
                            <h2>Localisation</h2>
                            <p>Pays et ville de résidence.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="field-label" for="country">
                                Pays <span class="required">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-globe2"></i>
                                <input
                                    id="country"
                                    type="text"
                                    name="country"
                                    value="{{ old('country', 'Maroc') }}"
                                    class="student-input @error('country') is-invalid @enderror"
                                    placeholder="Maroc"
                                    maxlength="120"
                                    required
                                >
                            </div>

                            @error('country')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="field-label" for="city">
                                Ville <span class="required">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-pin-map"></i>
                                <input
                                    id="city"
                                    type="text"
                                    name="city"
                                    value="{{ old('city') }}"
                                    class="student-input @error('city') is-invalid @enderror"
                                    placeholder="Ex. Rabat"
                                    maxlength="120"
                                    required
                                >
                            </div>

                            @error('city')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="security-box">
                    <div class="icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>

                    <div>
                        <h3>Accès sécurisé</h3>

                        <p>
                            Un mot de passe temporaire sécurisé sera généré automatiquement puis envoyé
                            à l’adresse e-mail de l’étudiant.
                        </p>

                        <div class="security-meta">
                            <span class="security-pill">
                                <i class="bi bi-clock-history"></i>
                                Validité : 48 heures
                            </span>

                            <span class="security-pill">
                                <i class="bi bi-envelope-check"></i>
                                Envoi automatique
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.users.index') }}" class="ssa-btn ssa-btn-secondary">
                        <i class="bi bi-arrow-left"></i>
                        Annuler
                    </a>

                    <button type="submit" class="ssa-btn ssa-btn-primary">
                        <i class="bi bi-person-check-fill"></i>
                        Créer et envoyer les accès
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
