@extends('layouts.prof')

@section('title', 'Paramètres')
@section('page_title', 'Paramètres')
@section('breadcrumb', 'Gestion du profil')

@section('content')

<style>
:root {
  --prof-gradient-primary: linear-gradient(135deg, #7C3AED, #A78BFA);
  --prof-gradient-success: linear-gradient(135deg, #10B981, #34D399);
}

.modern-input {
    background: rgba(255, 255, 255, 0.04) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 12px !important;
    padding: 14px 18px !important;
    color: rgba(255, 255, 255, 0.85) !important;
    font-size: 0.95rem !important;
    transition: all 0.3s ease !important;
    font-family: inherit !important;
    outline: none !important;
    width: 100% !important;
}
.modern-input:focus {
    border-color: rgba(124, 58, 237, 0.4) !important;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.08) !important;
    background: rgba(255, 255, 255, 0.06) !important;
}
.modern-input::placeholder {
    color: rgba(255, 255, 255, 0.2) !important;
}
.modern-input.error {
    border-color: rgba(239, 68, 68, 0.4) !important;
}

.floating-label {
    position: relative;
    margin-bottom: 1.25rem;
}
.floating-label label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 6px;
}
.floating-label .form-error {
    font-size: 0.72rem;
    color: #FCA5A5;
    margin-top: 4px;
}

.settings-card {
    background: rgba(255, 255, 255, 0.04);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}
.settings-card:hover {
    border-color: rgba(255, 255, 255, 0.12);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.settings-card-header {
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 12px;
}
.settings-card-header h5 {
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    font-size: 1rem;
}

.settings-card-body {
    padding: 1.5rem;
}

.btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 14px 24px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    color: white;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: inherit;
    position: relative;
    overflow: hidden;
}
.btn-submit::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
    transition: left 0.6s ease;
}
.btn-submit:hover::before {
    left: 100%;
}
.btn-submit:hover {
    transform: translateY(-2px);
}
.btn-submit.profile {
    background: var(--prof-gradient-primary);
    box-shadow: 0 8px 25px rgba(124, 58, 237, 0.3);
}
.btn-submit.profile:hover {
    box-shadow: 0 12px 35px rgba(124, 58, 237, 0.45);
}
.btn-submit.password {
    background: var(--prof-gradient-success);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}
.btn-submit.password:hover {
    box-shadow: 0 12px 35px rgba(16, 185, 129, 0.45);
}

@keyframes slideInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.slide-in {
    animation: slideInUp 0.5s ease forwards;
}
</style>

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-gear-fill me-2" style="color:var(--adm-accent);"></i> Paramètres</h1>
        <div class="subtitle">Gérez votre profil et votre sécurité</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.profile') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-person-circle me-1"></i> Voir mon profil
        </a>
    </div>
</div>

@if (session('success'))
<div class="adm-alert adm-alert-success mb-4 slide-in">
    <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
    <span>{{ session('success') }}</span>
</div>
@endif

@if ($errors->any())
<div class="adm-alert adm-alert-danger mb-4 slide-in">
    <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
    <span>
        <ul class="mb-0" style="list-style:none;padding-left:0;">
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </span>
</div>
@endif

<div class="row g-4">
    <!-- Profile Section -->
    <div class="col-lg-6">
        <div class="settings-card slide-in" style="animation-delay:0.05s;">
            <div class="settings-card-header" style="background:linear-gradient(135deg,rgba(124,58,237,0.12),rgba(167,139,250,0.05));">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(124,58,237,0.15);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#A78BFA;">
                    <i class="bi bi-person-circle"></i>
                </div>
                <h5>Mon Profil</h5>
            </div>
            <div class="settings-card-body">
                <form method="POST" action="{{ route('prof.settings.profile.update') }}">
                    @csrf @method('PUT')

                    <div class="floating-label">
                        <label for="name"><i class="bi bi-person-fill me-1" style="color:#A78BFA;"></i>Nom complet</label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}"
                               class="modern-input @error('name') error @enderror" required placeholder="Votre nom">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="floating-label">
                        <label for="email"><i class="bi bi-envelope-fill me-1" style="color:#A78BFA;"></i>Adresse Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                               class="modern-input @error('email') error @enderror" required placeholder="votre@email.com">
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="floating-label" style="margin-bottom:0;">
                        <label for="location"><i class="bi bi-geo-alt-fill me-1" style="color:#A78BFA;"></i>Localisation</label>
                        <input type="text" id="location" name="location" value="{{ old('location', auth()->user()->location ?? '') }}"
                               class="modern-input" placeholder="Ville, pays">
                    </div>

                    <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid rgba(255,255,255,0.06);">
                        <button type="submit" class="btn-submit profile">
                            <i class="bi bi-check2-circle"></i> Mettre à jour le profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Section -->
    <div class="col-lg-6">
        <div class="settings-card slide-in" style="animation-delay:0.1s;">
            <div class="settings-card-header" style="background:linear-gradient(135deg,rgba(16,185,129,0.12),rgba(52,211,153,0.05));">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(16,185,129,0.15);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#34D399;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h5>Sécurité</h5>
            </div>
            <div class="settings-card-body">
                <form method="POST" action="{{ route('prof.settings.password.update') }}">
                    @csrf @method('PUT')

                    <div class="floating-label">
                        <label for="current_password"><i class="bi bi-lock-fill me-1" style="color:#34D399;"></i>Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password"
                               class="modern-input @error('current_password') error @enderror" required placeholder="••••••••">
                        @error('current_password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="floating-label">
                        <label for="password"><i class="bi bi-key-fill me-1" style="color:#34D399;"></i>Nouveau mot de passe</label>
                        <input type="password" id="password" name="password"
                               class="modern-input @error('password') error @enderror" required minlength="8" placeholder="Minimum 8 caractères">
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="floating-label">
                        <label for="password_confirmation"><i class="bi bi-check-circle-fill me-1" style="color:#34D399;"></i>Confirmer le mot de passe</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="modern-input @error('password_confirmation') error @enderror" required placeholder="Retaper le mot de passe">
                        @error('password_confirmation') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid rgba(255,255,255,0.06);">
                        <button type="submit" class="btn-submit password">
                            <i class="bi bi-lock-fill"></i> Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Account Info Card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="adm-card slide-in" style="animation-delay:0.15s;">
            <div class="adm-card-header">
                <h4><i class="bi bi-info-circle" style="color:var(--adm-text-muted);"></i> Informations du compte</h4>
            </div>
            <div class="adm-card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="d-flex flex-column gap-1">
                            <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--adm-text-muted);font-weight:600;">Rôle</span>
                            <span class="adm-badge adm-badge-accent" style="width:fit-content;">Professeur</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex flex-column gap-1">
                            <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--adm-text-muted);font-weight:600;">Membre depuis</span>
                            <span style="font-weight:500;color:rgba(255,255,255,0.8);">{{ auth()->user()->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex flex-column gap-1">
                            <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--adm-text-muted);font-weight:600;">Email vérifié</span>
                            <span class="adm-badge adm-badge-success" style="width:fit-content;">
                                <i class="bi bi-check-circle-fill me-1"></i> Vérifié
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex flex-column gap-1">
                            <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--adm-text-muted);font-weight:600;">Statut</span>
                            <span class="adm-badge adm-badge-success" style="width:fit-content;">
                                <i class="bi bi-shield-check me-1"></i> Actif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
