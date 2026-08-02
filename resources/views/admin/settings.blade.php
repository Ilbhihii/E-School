@extends('layouts.admin')

@section('title', 'Paramètres')
@section('page_title', 'Paramètres')
@section('breadcrumb', 'Paramètres')

@php
    $admin = auth()->user();
    $profilePhotoUrl = $admin->profile_photo
        ? asset('storage/' . ltrim($admin->profile_photo, '/'))
        : null;
    $profileInitial = strtoupper(mb_substr($admin->name ?? 'A', 0, 1));
@endphp

@push('styles')
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.12fr) minmax(360px, .88fr);
        gap: 24px;
        align-items: start;
    }

    .settings-section-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
    }

    .settings-section-heading h3 {
        margin: 0 0 5px;
        color: var(--adm-text, #f8fafc);
        font-size: 1.05rem;
        font-weight: 800;
    }

    .settings-section-heading p {
        margin: 0;
        color: var(--adm-text-muted, #94a3b8);
        font-size: .82rem;
        line-height: 1.55;
    }

    .settings-heading-icon {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;
        color: #93c5fd;
        border: 1px solid rgba(96, 165, 250, .2);
        border-radius: 14px;
        background: rgba(37, 99, 235, .11);
    }

    .profile-photo-editor {
        display: grid;
        grid-template-columns: 132px minmax(0, 1fr);
        gap: 22px;
        align-items: center;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid rgba(148, 163, 184, .14);
        border-radius: 18px;
        background: rgba(255, 255, 255, .025);
    }

    .profile-photo-preview {
        position: relative;
        display: grid;
        width: 120px;
        height: 120px;
        place-items: center;
        overflow: hidden;
        color: #fff;
        font-size: 2.25rem;
        font-weight: 800;
        border: 4px solid rgba(255, 255, 255, .08);
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        box-shadow: 0 18px 38px rgba(15, 23, 42, .3);
    }

    .profile-photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-photo-preview .photo-camera-badge {
        position: absolute;
        right: 4px;
        bottom: 4px;
        display: grid;
        width: 31px;
        height: 31px;
        place-items: center;
        color: #fff;
        font-size: .82rem;
        border: 3px solid #111827;
        border-radius: 50%;
        background: #2563eb;
    }

    .profile-photo-copy h4 {
        margin: 0 0 6px;
        color: var(--adm-text, #f8fafc);
        font-size: .96rem;
        font-weight: 800;
    }

    .profile-photo-copy p {
        margin: 0 0 13px;
        color: var(--adm-text-muted, #94a3b8);
        font-size: .78rem;
        line-height: 1.55;
    }

    .photo-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
    }

    .photo-file-name {
        display: block;
        min-height: 18px;
        margin-top: 10px;
        color: #60a5fa;
        font-size: .74rem;
        font-weight: 700;
    }

    .settings-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .settings-form-grid .full-width {
        grid-column: 1 / -1;
    }

    .settings-actions {
        display: flex;
        justify-content: flex-end;
        padding-top: 20px;
        margin-top: 22px;
        border-top: 1px solid rgba(148, 163, 184, .12);
    }

    .password-tip {
        display: flex;
        gap: 10px;
        padding: 13px 14px;
        margin-bottom: 20px;
        color: var(--adm-text-muted, #94a3b8);
        font-size: .76rem;
        line-height: 1.5;
        border: 1px solid rgba(251, 191, 36, .16);
        border-radius: 14px;
        background: rgba(251, 191, 36, .055);
    }

    .password-tip i {
        color: #fbbf24;
        font-size: 1rem;
    }

    .password-field-wrap {
        position: relative;
    }

    .password-field-wrap .adm-form-control {
        padding-right: 44px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 11px;
        display: grid;
        width: 30px;
        height: 30px;
        padding: 0;
        place-items: center;
        color: #94a3b8;
        cursor: pointer;
        border: 0;
        border-radius: 9px;
        background: transparent;
        transform: translateY(-50%);
    }

    .password-toggle:hover {
        color: #fff;
        background: rgba(255, 255, 255, .06);
    }

    .strength-track {
        height: 5px;
        margin-top: 9px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(148, 163, 184, .14);
    }

    .strength-bar {
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: #ef4444;
        transition: width .25s ease, background .25s ease;
    }

    .strength-label {
        display: block;
        margin-top: 6px;
        color: var(--adm-text-muted, #94a3b8);
        font-size: .7rem;
    }

    html.light-mode .profile-photo-editor {
        background: #f8fafc;
    }

    html.light-mode .profile-photo-preview .photo-camera-badge {
        border-color: #fff;
    }

    @media (max-width: 1050px) {
        .settings-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 650px) {
        .profile-photo-editor {
            grid-template-columns: 1fr;
            justify-items: center;
            text-align: center;
        }

        .photo-actions { justify-content: center; }
        .settings-form-grid { grid-template-columns: 1fr; }
        .settings-form-grid .full-width { grid-column: auto; }
        .settings-actions .adm-btn { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')
@if (session('success'))
    <div class="adm-alert adm-alert-success">
        <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="adm-alert adm-alert-danger">
        <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
        <div>
            <strong>Veuillez vérifier les informations saisies.</strong>
            <ul style="margin:.45rem 0 0;padding-left:1.1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="settings-grid">
    <section class="adm-card">
        <div class="adm-card-body">
            <div class="settings-section-heading">
                <div>
                    <h3>Informations du profil</h3>
                    <p>Modifiez votre photo, votre nom et l’adresse e-mail utilisée pour vous connecter.</p>
                </div>
                <span class="settings-heading-icon"><i class="bi bi-person-badge"></i></span>
            </div>

            <form method="POST" action="{{ route('admin.settings.profile.update') }}" enctype="multipart/form-data" id="adminProfileForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="remove_profile_photo" id="removeProfilePhoto" value="0">

                <div class="profile-photo-editor">
                    <div class="profile-photo-preview" id="profilePhotoPreview">
                        <img
                            id="profilePhotoImage"
                            src="{{ $profilePhotoUrl ?: '' }}"
                            alt="Photo de profil de {{ $admin->name }}"
                            style="{{ $profilePhotoUrl ? '' : 'display:none;' }}"
                        >
                        <span id="profilePhotoFallback" style="{{ $profilePhotoUrl ? 'display:none;' : '' }}">{{ $profileInitial }}</span>
                        <span class="photo-camera-badge"><i class="bi bi-camera-fill"></i></span>
                    </div>

                    <div class="profile-photo-copy">
                        <h4>Photo de profil</h4>
                        <p>Formats acceptés : JPG, PNG ou WEBP. Taille maximale : 4 Mo. Une image carrée donne le meilleur résultat.</p>

                        <div class="photo-actions">
                            <label for="profilePhotoInput" class="adm-btn adm-btn-primary" style="cursor:pointer;">
                                <i class="bi bi-upload"></i>
                                <span>{{ $profilePhotoUrl ? 'Changer la photo' : 'Ajouter une photo' }}</span>
                            </label>

                            <button
                                type="button"
                                class="adm-btn adm-btn-ghost"
                                id="removePhotoButton"
                                style="{{ $profilePhotoUrl ? '' : 'display:none;' }}"
                            >
                                <i class="bi bi-trash3"></i>
                                Supprimer
                            </button>
                        </div>

                        <input
                            type="file"
                            name="profile_photo"
                            id="profilePhotoInput"
                            accept="image/jpeg,image/png,image/webp"
                            hidden
                        >
                        <span class="photo-file-name" id="profilePhotoFileName"></span>
                        @error('profile_photo')
                            <div class="adm-form-error" style="margin-top:7px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="settings-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="adminName">Nom complet</label>
                        <input
                            id="adminName"
                            type="text"
                            name="name"
                            value="{{ old('name', $admin->name) }}"
                            class="adm-form-control @error('name') error @enderror"
                            required
                            autocomplete="name"
                        >
                        @error('name') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="adminEmail">Adresse e-mail</label>
                        <input
                            id="adminEmail"
                            type="email"
                            name="email"
                            value="{{ old('email', $admin->email) }}"
                            class="adm-form-control @error('email') error @enderror"
                            required
                            autocomplete="email"
                        >
                        @error('email') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="settings-actions">
                    <button type="submit" class="adm-btn adm-btn-primary">
                        <i class="bi bi-check2-circle"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="adm-card">
        <div class="adm-card-body">
            <div class="settings-section-heading">
                <div>
                    <h3>Sécurité du compte</h3>
                    <p>Utilisez un mot de passe unique pour protéger votre espace d’administration.</p>
                </div>
                <span class="settings-heading-icon" style="color:#fca5a5;border-color:rgba(248,113,113,.2);background:rgba(239,68,68,.08);">
                    <i class="bi bi-shield-lock"></i>
                </span>
            </div>

            <div class="password-tip">
                <i class="bi bi-lightbulb-fill"></i>
                <span>Choisissez au moins 8 caractères avec des majuscules, des chiffres et un symbole.</span>
            </div>

            <form method="POST" action="{{ route('admin.settings.password.update') }}">
                @csrf
                @method('PUT')

                <div class="adm-form-group">
                    <label class="adm-form-label" for="currentPassword">Mot de passe actuel</label>
                    <div class="password-field-wrap">
                        <input id="currentPassword" type="password" name="current_password" class="adm-form-control @error('current_password') error @enderror" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" data-password-toggle="currentPassword" aria-label="Afficher le mot de passe"><i class="bi bi-eye"></i></button>
                    </div>
                    @error('current_password') <div class="adm-form-error">{{ $message }}</div> @enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="newPassword">Nouveau mot de passe</label>
                    <div class="password-field-wrap">
                        <input id="newPassword" type="password" name="password" class="adm-form-control @error('password') error @enderror" autocomplete="new-password" required>
                        <button type="button" class="password-toggle" data-password-toggle="newPassword" aria-label="Afficher le mot de passe"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="strength-track"><div class="strength-bar" id="strengthBar"></div></div>
                    <small class="strength-label" id="strengthText">Saisissez votre nouveau mot de passe.</small>
                    @error('password') <div class="adm-form-error">{{ $message }}</div> @enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="passwordConfirmation">Confirmer le nouveau mot de passe</label>
                    <div class="password-field-wrap">
                        <input id="passwordConfirmation" type="password" name="password_confirmation" class="adm-form-control" autocomplete="new-password" required>
                        <button type="button" class="password-toggle" data-password-toggle="passwordConfirmation" aria-label="Afficher le mot de passe"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <div class="settings-actions">
                    <button type="submit" class="adm-btn adm-btn-danger">
                        <i class="bi bi-key"></i>
                        Modifier le mot de passe
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('profilePhotoInput');
    const image = document.getElementById('profilePhotoImage');
    const fallback = document.getElementById('profilePhotoFallback');
    const fileName = document.getElementById('profilePhotoFileName');
    const removeButton = document.getElementById('removePhotoButton');
    const removeInput = document.getElementById('removeProfilePhoto');

    input?.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Veuillez choisir une image JPG, PNG ou WEBP.');
            this.value = '';
            return;
        }

        if (file.size > 4 * 1024 * 1024) {
            alert('La photo ne doit pas dépasser 4 Mo.');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            image.src = event.target.result;
            image.style.display = 'block';
            fallback.style.display = 'none';
        };
        reader.readAsDataURL(file);

        removeInput.value = '0';
        removeButton.style.display = 'inline-flex';
        fileName.textContent = file.name;
    });

    removeButton?.addEventListener('click', function () {
        input.value = '';
        image.src = '';
        image.style.display = 'none';
        fallback.style.display = 'grid';
        removeInput.value = '1';
        fileName.textContent = 'La photo sera supprimée après l’enregistrement.';
        this.style.display = 'none';
    });

    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const field = document.getElementById(this.dataset.passwordToggle);
            if (!field) return;
            const visible = field.type === 'text';
            field.type = visible ? 'password' : 'text';
            this.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
    });

    const password = document.getElementById('newPassword');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    password?.addEventListener('input', function () {
        const value = this.value;
        let score = 0;
        if (value.length >= 8) score++;
        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score++;
        if (/\d/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;

        const widths = ['0%', '25%', '50%', '75%', '100%'];
        const colors = ['#ef4444', '#ef4444', '#f59e0b', '#3b82f6', '#22c55e'];
        const labels = ['Saisissez votre nouveau mot de passe.', 'Faible', 'Moyen', 'Bon', 'Très bon'];
        strengthBar.style.width = widths[score];
        strengthBar.style.background = colors[score];
        strengthText.textContent = value ? labels[score] : labels[0];
    });
});
</script>
@endsection
