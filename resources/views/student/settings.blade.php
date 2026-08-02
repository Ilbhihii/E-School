@extends('layouts.student')

@section('title', 'Paramètres')
@section('page_title', 'Paramètres')
@section('breadcrumb', 'Paramètres')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/student-account-v8.css') }}"
    >
@endpush

@section('content')
@php
    $student = auth()->user();

    $studentInitial = strtoupper(
        mb_substr(trim($student->name ?? 'E'), 0, 1)
    );

    $studentPhotoUrl = $student->profile_photo
        ? asset(
            'storage/'
            . ltrim($student->profile_photo, '/')
        )
        : null;
@endphp

<div class="student-account-page">

    <section class="student-account-intro">
        <div>
            <span class="student-account-kicker">
                <i class="bi bi-sliders2"></i>
                Gestion du compte
            </span>

            <h2>Paramètres</h2>

            <p>
                Modifiez votre photo, vos informations personnelles
                et votre mot de passe.
            </p>
        </div>

        <a
            href="{{ route('student.profile') }}"
            class="student-account-secondary-button"
        >
            <i class="bi bi-person-circle"></i>
            Voir mon profil
        </a>
    </section>

    @if($errors->any())
        <section class="student-account-error-list">
            <span>
                <i class="bi bi-exclamation-triangle-fill"></i>
            </span>

            <div>
                <strong>
                    Certaines informations sont incorrectes
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <div class="student-settings-grid">

        <section class="student-account-card">
            <header class="student-account-card-header">
                <div>
                    <span class="student-account-card-icon blue">
                        <i class="bi bi-person-fill-gear"></i>
                    </span>

                    <div>
                        <h3>Profil et photo</h3>

                        <p>
                            Personnalisez les informations visibles
                            dans votre espace étudiant.
                        </p>
                    </div>
                </div>
            </header>

            <form
                method="POST"
                action="{{
                    route(
                        'student.settings.profile.update'
                    )
                }}"
                enctype="multipart/form-data"
                id="studentProfileSettingsForm"
                class="student-account-form"
            >
                @csrf
                @method('PUT')

                <div class="student-photo-editor">
                    <div class="student-photo-preview-wrap">
                        <span
                            class="student-photo-preview"
                            id="studentPhotoPreview"
                        >
                            @if($studentPhotoUrl)
                                <img
                                    src="{{ $studentPhotoUrl }}"
                                    alt="Photo actuelle"
                                    id="studentPhotoPreviewImage"
                                    onerror="
                                        this.hidden=true;
                                        document
                                            .getElementById(
                                                'studentPhotoFallback'
                                            )
                                            .hidden=false;
                                    "
                                >

                                <span
                                    id="studentPhotoFallback"
                                    hidden
                                >
                                    {{ $studentInitial }}
                                </span>
                            @else
                                <img
                                    src=""
                                    alt="Nouvelle photo"
                                    id="studentPhotoPreviewImage"
                                    hidden
                                >

                                <span id="studentPhotoFallback">
                                    {{ $studentInitial }}
                                </span>
                            @endif
                        </span>

                        <span class="student-photo-camera">
                            <i class="bi bi-camera-fill"></i>
                        </span>
                    </div>

                    <div class="student-photo-editor-copy">
                        <h4>Photo de profil</h4>

                        <p>
                            JPG, JPEG, PNG ou WEBP. Taille maximale :
                            4 Mo. Une image carrée donne le meilleur
                            résultat.
                        </p>

                        <div class="student-photo-actions">
                            <label
                                for="studentProfilePhoto"
                                class="student-account-primary-button"
                            >
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                Choisir une photo
                            </label>

                            <button
                                type="button"
                                class="student-account-danger-button"
                                id="studentRemovePhotoButton"
                                {{
                                    $student->profile_photo
                                        ? ''
                                        : 'disabled'
                                }}
                            >
                                <i class="bi bi-trash3"></i>
                                Supprimer
                            </button>
                        </div>

                        <input
                            type="file"
                            name="profile_photo"
                            id="studentProfilePhoto"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            hidden
                        >

                        <input
                            type="hidden"
                            name="remove_profile_photo"
                            id="studentRemovePhotoInput"
                            value="0"
                        >

                        <small
                            class="student-photo-file-name"
                            id="studentPhotoFileName"
                        >
                            {{
                                $student->profile_photo
                                    ? 'Photo actuelle enregistrée'
                                    : 'Aucune photo sélectionnée'
                            }}
                        </small>

                        @error('profile_photo')
                            <small class="student-account-field-error">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                </div>

                <div class="student-account-form-grid">
                    <div class="student-account-field">
                        <label for="studentName">
                            Nom complet
                        </label>

                        <div class="student-account-input-wrap">
                            <i class="bi bi-person-fill"></i>

                            <input
                                type="text"
                                name="name"
                                id="studentName"
                                value="{{
                                    old(
                                        'name',
                                        $student->name
                                    )
                                }}"
                                required
                            >
                        </div>

                        @error('name')
                            <small class="student-account-field-error">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="student-account-field">
                        <label for="studentEmail">
                            Adresse e-mail
                        </label>

                        <div class="student-account-input-wrap">
                            <i class="bi bi-envelope-fill"></i>

                            <input
                                type="email"
                                name="email"
                                id="studentEmail"
                                value="{{
                                    old(
                                        'email',
                                        $student->email
                                    )
                                }}"
                                required
                            >
                        </div>

                        @error('email')
                            <small class="student-account-field-error">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                </div>

                <div class="student-account-form-footer">
                    <span>
                        <i class="bi bi-info-circle-fill"></i>
                        La nouvelle photo apparaîtra dans la sidebar,
                        la topbar et votre profil.
                    </span>

                    <button
                        type="submit"
                        class="student-account-primary-button"
                    >
                        <i class="bi bi-check2-circle"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </section>

        <section class="student-account-card">
            <header class="student-account-card-header">
                <div>
                    <span class="student-account-card-icon green">
                        <i class="bi bi-shield-lock-fill"></i>
                    </span>

                    <div>
                        <h3>Sécurité du compte</h3>

                        <p>
                            Utilisez un mot de passe unique et
                            difficile à deviner.
                        </p>
                    </div>
                </div>
            </header>

            <div class="student-password-advice">
                <span>
                    <i class="bi bi-lightbulb-fill"></i>
                </span>

                <p>
                    Choisissez au moins 8 caractères avec des lettres,
                    des chiffres et un symbole.
                </p>
            </div>

            <form
                method="POST"
                action="{{
                    route(
                        'student.settings.password.update'
                    )
                }}"
                class="student-account-form"
                id="studentPasswordForm"
            >
                @csrf
                @method('PUT')

                <div class="student-account-field">
                    <label for="studentCurrentPassword">
                        Mot de passe actuel
                    </label>

                    <div class="student-account-input-wrap password">
                        <i class="bi bi-lock-fill"></i>

                        <input
                            type="password"
                            name="current_password"
                            id="studentCurrentPassword"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            required
                        >

                        <button
                            type="button"
                            class="student-password-toggle"
                            data-password-target="studentCurrentPassword"
                            aria-label="Afficher le mot de passe"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    @error('current_password')
                        <small class="student-account-field-error">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <div class="student-account-field">
                    <label for="studentNewPassword">
                        Nouveau mot de passe
                    </label>

                    <div class="student-account-input-wrap password">
                        <i class="bi bi-key-fill"></i>

                        <input
                            type="password"
                            name="password"
                            id="studentNewPassword"
                            autocomplete="new-password"
                            minlength="8"
                            placeholder="Minimum 8 caractères"
                            required
                        >

                        <button
                            type="button"
                            class="student-password-toggle"
                            data-password-target="studentNewPassword"
                            aria-label="Afficher le mot de passe"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div class="student-password-strength">
                        <span
                            id="studentPasswordStrengthBar"
                        ></span>
                    </div>

                    <small
                        class="student-password-strength-label"
                        id="studentPasswordStrengthLabel"
                    >
                        Saisissez un nouveau mot de passe.
                    </small>

                    @error('password')
                        <small class="student-account-field-error">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <div class="student-account-field">
                    <label for="studentPasswordConfirmation">
                        Confirmer le nouveau mot de passe
                    </label>

                    <div class="student-account-input-wrap password">
                        <i class="bi bi-check-circle-fill"></i>

                        <input
                            type="password"
                            name="password_confirmation"
                            id="studentPasswordConfirmation"
                            autocomplete="new-password"
                            placeholder="Retapez le mot de passe"
                            required
                        >

                        <button
                            type="button"
                            class="student-password-toggle"
                            data-password-target="studentPasswordConfirmation"
                            aria-label="Afficher le mot de passe"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <small
                        class="student-password-match"
                        id="studentPasswordMatch"
                    ></small>
                </div>

                <div class="student-account-form-footer">
                    <span>
                        <i class="bi bi-shield-check"></i>
                        Votre mot de passe ne sera jamais affiché.
                    </span>

                    <button
                        type="submit"
                        class="student-account-primary-button green"
                    >
                        <i class="bi bi-key-fill"></i>
                        Modifier le mot de passe
                    </button>
                </div>
            </form>
        </section>
    </div>

    <section class="student-account-card">
        <header class="student-account-card-header">
            <div>
                <span class="student-account-card-icon violet">
                    <i class="bi bi-info-circle-fill"></i>
                </span>

                <div>
                    <h3>Informations du compte</h3>

                    <p>
                        Résumé de votre espace Smart School Academy.
                    </p>
                </div>
            </div>
        </header>

        <div class="student-account-summary-grid">
            <article>
                <span>
                    <i class="bi bi-person-badge-fill"></i>
                </span>

                <div>
                    <small>Rôle</small>
                    <strong>Étudiant</strong>
                </div>
            </article>

            <article>
                <span>
                    <i class="bi bi-calendar3"></i>
                </span>

                <div>
                    <small>Membre depuis</small>
                    <strong>
                        {{
                            $student->created_at
                                ->format('d/m/Y')
                        }}
                    </strong>
                </div>
            </article>

            <article>
                <span>
                    <i class="bi bi-check-circle-fill"></i>
                </span>

                <div>
                    <small>Statut</small>
                    <strong>
                        {{
                            $student->is_active
                                ? 'Actif'
                                : 'En attente'
                        }}
                    </strong>
                </div>
            </article>

            <article>
                <span>
                    <i class="bi bi-image-fill"></i>
                </span>

                <div>
                    <small>Photo de profil</small>
                    <strong>
                        {{
                            $student->profile_photo
                                ? 'Configurée'
                                : 'Non configurée'
                        }}
                    </strong>
                </div>
            </article>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/student-account-v8.js') }}"></script>
@endpush
