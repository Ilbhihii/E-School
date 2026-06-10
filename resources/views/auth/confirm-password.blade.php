@extends('layouts.front')

@section('title', 'Confirmer le mot de passe')

@section('content')

<div class="confirm-password-page d-flex justify-content-center align-items-center" 
     style="min-height:100vh; position: relative; overflow: hidden; background: linear-gradient(135deg, #e0f7fa, #ffffff);">

    <!-- Floating shapes décoratifs -->
    <div style="position:absolute; width:220px; height:220px; background: rgba(13,110,253,0.2); border-radius:50%; top:5%; left:5%; animation: float 6s ease-in-out infinite;"></div>
    <div style="position:absolute; width:180px; height:180px; background: rgba(25,135,84,0.15); border-radius:50%; bottom:10%; right:10%; animation: float 5s ease-in-out infinite;"></div>

    <div class="card shadow-lg p-5" 
         style="width:400px; border-radius:25px; background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); color:#333; z-index:1;">

        <h3 class="text-center mb-4 fw-bold text-primary">Confirmer votre mot de passe</h3>

        <div class="mb-4 text-center text-muted">
            Ceci est une zone sécurisée de l\'application. Veuillez confirmer votre mot de passe avant de continuer.
        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="alert alert-danger mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input id="password" class="form-control form-control-light"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
            </div>

            <div class="d-flex justify-end">
                <x-button class="btn btn-primary w-100 fw-bold shadow-sm hover-scale">
                    Confirmer
                </x-button>
            </div>
        </form>

        <div class="text-center mt-4">
            <small>
                <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                    Retour à la connexion
                </a>
            </small>
        </div>

    </div>
</div>

<!-- Styles -->
<style>
/* Inputs clairs et élégants */
.form-control-light {
    background: rgba(255,255,255,0.9);
    border: 1px solid rgba(0,0,0,0.1);
    color: #333;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.form-control-light:focus {
    background: #ffffff;
    border-color: #0d6efd;
    box-shadow: 0 0 12px rgba(13,110,253,0.2);
    color: #333;
}

/* Hover effect buttons */
.hover-scale:hover {
    transform: scale(1.05);
    transition: all 0.3s ease;
}

/* Floating shapes animation */
@keyframes float {
    0% { transform: translateY(0) translateX(0); }
    50% { transform: translateY(-15px) translateX(10px); }
    100% { transform: translateY(0) translateX(0); }
}
</style>

@endsection

