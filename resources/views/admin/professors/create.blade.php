@extends('layouts.admin')

@section('title', 'Créer un professeur')
@section('page_title', 'Nouveau professeur')
@section('breadcrumb', 'Création du compte professeur')

@section('content')

<style>
.professor-create-shell {
    max-width: 760px;
    margin: 0 auto;
}

.professor-create-card {
    overflow: hidden;
    border: 1px solid rgba(99,102,241,.16);
    border-radius: 18px;
    background: rgba(15,23,42,.83);
    box-shadow: 0 20px 50px rgba(0,0,0,.2);
}

.professor-create-header {
    padding: 1.35rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 12px;
    background:
        linear-gradient(
            135deg,
            rgba(37,99,235,.16),
            rgba(124,58,237,.1)
        );
    border-bottom: 1px solid rgba(255,255,255,.06);
}

.professor-create-icon {
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    border-radius: 13px;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563eb,
            #7c3aed
        );
    font-size: 1.2rem;
}

.professor-create-header h2 {
    margin: 0 0 4px;
    color: #f8fafc;
    font-size: 1.1rem;
    font-weight: 850;
}

.professor-create-header p {
    margin: 0;
    color: #64748b;
    font-size: .72rem;
}

.professor-create-body {
    padding: 1.5rem;
}

.professor-security-note {
    margin-bottom: 1.25rem;
    padding: .95rem 1rem;
    display: flex;
    gap: 9px;
    color: #bfdbfe;
    background: rgba(37,99,235,.08);
    border: 1px solid rgba(96,165,250,.15);
    border-radius: 11px;
    font-size: .72rem;
    line-height: 1.6;
}

.professor-form-actions {
    margin-top: 1.35rem;
    padding-top: 1.2rem;
    display: flex;
    justify-content: flex-end;
    gap: .7rem;
    border-top: 1px solid rgba(255,255,255,.06);
}
</style>

<div class="professor-create-shell">
    <div class="professor-create-card">
        <div class="professor-create-header">
            <div class="professor-create-icon">
                <i class="bi bi-person-plus-fill"></i>
            </div>

            <div>
                <h2>Créer un compte professeur</h2>
                <p>
                    Le mot de passe temporaire sera
                    généré et envoyé automatiquement.
                </p>
            </div>
        </div>

        <div class="professor-create-body">
            @if(session('error'))
                <div
                    class="
                        adm-alert
                        adm-alert-danger
                        mb-3
                    "
                >
                    {{ session('error') }}
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
                            padding-left:1.1rem;
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

            <div class="professor-security-note">
                <i class="bi bi-shield-lock-fill"></i>

                <span>
                    Le professeur recevra son adresse
                    e-mail et un mot de passe temporaire.
                    À sa première connexion, il devra
                    obligatoirement choisir un nouveau
                    mot de passe.
                </span>
            </div>

            <form
                method="POST"
                action="{{
                    route(
                        'admin.professors.store'
                    )
                }}"
            >
                @csrf

                <div class="adm-form-group">
                    <label
                        class="adm-form-label"
                        for="professorName"
                    >
                        Nom complet *
                    </label>

                    <input
                        type="text"
                        id="professorName"
                        name="name"
                        value="{{ old('name') }}"
                        class="adm-form-control"
                        maxlength="255"
                        autocomplete="name"
                        required
                        placeholder="Ex. Ahmed El Mansouri"
                    >
                </div>

                <div class="adm-form-group">
                    <label
                        class="adm-form-label"
                        for="professorEmail"
                    >
                        Adresse e-mail *
                    </label>

                    <input
                        type="email"
                        id="professorEmail"
                        name="email"
                        value="{{ old('email') }}"
                        class="adm-form-control"
                        maxlength="255"
                        autocomplete="email"
                        required
                        placeholder="professeur@example.com"
                    >
                </div>

                <div class="professor-form-actions">
                    <a
                        href="{{
                            route(
                                'admin.professors.index'
                            )
                        }}"
                        class="adm-btn adm-btn-ghost"
                    >
                        Annuler
                    </a>

                    <button
                        type="submit"
                        class="adm-btn adm-btn-primary"
                    >
                        <i
                            class="bi bi-envelope-check-fill"
                        ></i>
                        Créer et envoyer les accès
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
