@extends('layouts.prof')

@section('title', 'Nouveau mot de passe')
@section('page_title', 'Sécurité')
@section('breadcrumb', 'Première connexion')

@section('content')

<style>
.first-password-shell {
    min-height: calc(100vh - 170px);
    display: grid;
    place-items: center;
    padding: 1rem 0;
}

.first-password-card {
    width: 100%;
    max-width: 650px;
    overflow: hidden;
    border: 1px solid rgba(124,58,237,.2);
    border-radius: 20px;
    background: rgba(15,23,42,.9);
    box-shadow: 0 24px 65px rgba(0,0,0,.25);
}

.first-password-header {
    padding: 1.6rem;
    text-align: center;
    background:
        linear-gradient(
            135deg,
            rgba(37,99,235,.18),
            rgba(124,58,237,.13)
        );
    border-bottom: 1px solid rgba(255,255,255,.06);
}

.first-password-icon {
    width: 64px;
    height: 64px;
    display: grid;
    place-items: center;
    margin: 0 auto .9rem;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563eb,
            #7c3aed
        );
    border-radius: 18px;
    font-size: 1.6rem;
    box-shadow: 0 12px 30px rgba(79,70,229,.3);
}

.first-password-header h1 {
    margin: 0 0 7px;
    color: #f8fafc;
    font-size: 1.35rem;
    font-weight: 850;
}

.first-password-header p {
    max-width: 470px;
    margin: 0 auto;
    color: #94a3b8;
    font-size: .76rem;
    line-height: 1.65;
}

.first-password-body {
    padding: 1.55rem;
}

.first-password-rule {
    margin-bottom: 1.2rem;
    padding: .9rem 1rem;
    color: #cbd5e1;
    background: rgba(255,255,255,.035);
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 11px;
    font-size: .7rem;
    line-height: 1.65;
}

.first-password-submit {
    width: 100%;
    min-height: 46px;
    margin-top: .4rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563eb,
            #7c3aed
        );
    border: 0;
    border-radius: 11px;
    font-size: .78rem;
    font-weight: 800;
    cursor: pointer;
}
</style>

<div class="first-password-shell">
    <div class="first-password-card">
        <div class="first-password-header">
            <div class="first-password-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <h1>
                Créez votre nouveau mot de passe
            </h1>

            <p>
                Pour sécuriser votre compte professeur,
                remplacez le mot de passe temporaire
                reçu par e-mail avant d’accéder
                à votre espace.
            </p>
        </div>

        <div class="first-password-body">
            @if(session('warning'))
                <div
                    class="
                        adm-alert
                        adm-alert-warning
                        mb-3
                    "
                >
                    {{ session('warning') }}
                </div>
            @endif

            @if($isExpired)
                <div
                    class="
                        adm-alert
                        adm-alert-danger
                        mb-3
                    "
                >
                    Le mot de passe temporaire a expiré.
                    Demandez à l’administration de
                    renvoyer vos accès.
                </div>
            @endif

            @if($errors->any())
                <div
                    class="
                        adm-alert
                        adm-alert-danger
                        mb-3
                    "
                >
                    <ul
                        style="
                            margin:0;
                            padding-left:1rem;
                        "
                    >
                        @foreach(
                            $errors->all() as $error
                        )
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="first-password-rule">
                Le nouveau mot de passe doit contenir
                au moins 10 caractères, des lettres
                majuscules et minuscules ainsi
                qu’un chiffre.
            </div>

            <form
                method="POST"
                action="{{
                    route(
                        'prof.password.first.update'
                    )
                }}"
            >
                @csrf
                @method('PUT')

                <div class="adm-form-group">
                    <label
                        class="adm-form-label"
                        for="newPassword"
                    >
                        Nouveau mot de passe *
                    </label>

                    <input
                        type="password"
                        id="newPassword"
                        name="password"
                        class="adm-form-control"
                        autocomplete="new-password"
                        minlength="10"
                        required
                        {{ $isExpired ? 'disabled' : '' }}
                    >
                </div>

                <div class="adm-form-group">
                    <label
                        class="adm-form-label"
                        for="confirmPassword"
                    >
                        Confirmer le mot de passe *
                    </label>

                    <input
                        type="password"
                        id="confirmPassword"
                        name="password_confirmation"
                        class="adm-form-control"
                        autocomplete="new-password"
                        minlength="10"
                        required
                        {{ $isExpired ? 'disabled' : '' }}
                    >
                </div>

                <button
                    type="submit"
                    class="first-password-submit"
                    {{ $isExpired ? 'disabled' : '' }}
                >
                    <i class="bi bi-check-circle-fill"></i>
                    Enregistrer et accéder à mon espace
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
