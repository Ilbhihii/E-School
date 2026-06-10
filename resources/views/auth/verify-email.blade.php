@extends('layouts.front')

@section('title', 'Vérification email')

@section('content')

<div class="verify-email-page d-flex justify-content-center align-items-center" 
     style="min-height:100vh; position: relative; overflow: hidden; background: linear-gradient(135deg, #e0f7fa, #ffffff);">

    <!-- Floating shapes décoratifs -->
    <div style="position:absolute; width:220px; height:220px; background: rgba(13,110,253,0.2); border-radius:50%; top:5%; left:5%; animation: float 6s ease-in-out infinite;"></div>
    <div style="position:absolute; width:180px; height:180px; background: rgba(25,135,84,0.15); border-radius:50%; bottom:10%; right:10%; animation: float 5s ease-in-out infinite;"></div>

    <div class="card shadow-lg p-5" 
         style="width:400px; border-radius:25px; background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); color:#333; z-index:1;">

        <h3 class="text-center mb-4 fw-bold text-primary">Vérification email</h3>

        <div class="mb-4 text-center text-muted">
            Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer ? Si vous n\'avez pas reçu l\'email, nous serons ravis de vous en renvoyer un autre.
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success mb-4 text-center">
                Un nouveau lien de vérification a été envoyé à l\'adresse email que vous avez fournie lors de l\'inscription.
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mt-4 gap-3">
            <form method="POST" action="{{ route('verification.send') }}" class="flex-grow-1">
                @csrf
                <button type="submit" class="btn btn-outline-primary w-100 fw-bold hover-scale">
                    Renvoyer l\'email de vérification
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="btn btn-link text-danger p-0">
                    Se déconnecter
                </button>
            </form>
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

