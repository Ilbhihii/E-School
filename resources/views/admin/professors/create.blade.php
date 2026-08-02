@extends('layouts.admin')

@section('title', 'Créer un professeur')
@section('page_title', 'Nouveau professeur')
@section('breadcrumb', 'Création du compte professeur')

@section('content')
<div class="professor-create-shell">
    <section class="professor-create-card">
        <header class="professor-create-header">
            <span class="professor-create-icon">
                <i class="bi bi-person-plus-fill"></i>
            </span>

            <div>
                <span class="admin-section-kicker">Nouveau membre</span>
                <h2>Créer un compte professeur</h2>
                <p>
                    Un mot de passe temporaire sécurisé sera généré et envoyé automatiquement par e-mail.
                </p>
            </div>
        </header>

        <div class="professor-create-body">
            @if($errors->any())
                <div class="adm-alert adm-alert-danger mb-3">
                    <span class="adm-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    <div>
                        <strong>Le compte n’a pas encore été créé.</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="professor-security-note">
                <i class="bi bi-shield-lock-fill"></i>
                <span>
                    Le professeur devra remplacer le mot de passe temporaire à sa première connexion. L’accès temporaire est valable pendant 48 heures.
                </span>
            </div>

            <form method="POST" action="{{ route('admin.professors.store') }}">
                @csrf

                <div class="adm-form-group">
                    <label class="adm-form-label" for="professorName">
                        <i class="bi bi-person"></i>
                        Nom complet <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="professorName"
                        name="name"
                        value="{{ old('name') }}"
                        class="adm-form-control @error('name') error @enderror"
                        maxlength="255"
                        autocomplete="name"
                        required
                        autofocus
                        placeholder="Ex. Ahmed El Mansouri"
                    >
                    @error('name')
                        <div class="adm-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="professorEmail">
                        <i class="bi bi-envelope"></i>
                        Adresse e-mail <span class="text-danger">*</span>
                    </label>
                    <input
                        type="email"
                        id="professorEmail"
                        name="email"
                        value="{{ old('email') }}"
                        class="adm-form-control @error('email') error @enderror"
                        maxlength="255"
                        autocomplete="email"
                        required
                        placeholder="professeur@example.com"
                    >
                    @error('email')
                        <div class="adm-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="professor-form-actions">
                    <a href="{{ route('admin.professors.index') }}" class="adm-btn adm-btn-ghost">
                        <i class="bi bi-arrow-left"></i>
                        Annuler
                    </a>

                    <button type="submit" class="adm-btn adm-btn-primary">
                        <i class="bi bi-envelope-check-fill"></i>
                        Créer et envoyer les accès
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
