@extends('layouts.front')

@section('title', 'Inscription')

@section('content')

<div class="register-page d-flex justify-content-center align-items-center"
     style="min-height:100vh; background:#F5F7FA; position:relative; overflow:hidden;">

    <!-- SHAPES -->
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>

    <!-- CARD -->
    <div class="card register-card shadow-sm p-5">

        <h3 class="text-center mb-4 fw-bold text-primary">
            Créer un compte
        </h3>

        <!-- ERRORS -->
        @if ($errors->any())
            <div class="alert alert-danger text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- NAME -->
            <div class="mb-3">
                <label class="fw-semibold">Nom</label>
                <input type="text" name="name"
                       class="form-control custom-input"
                       placeholder="Votre nom"
                       required>
            </div>

            <!-- EMAIL -->
            <div class="mb-3">
                <label class="fw-semibold">Email</label>
                <input type="email" name="email"
                       class="form-control custom-input"
                       placeholder="exemple@email.com"
                       required>
            </div>

            <!-- PASSWORD -->
            <div class="mb-3">
                <label class="fw-semibold">Mot de passe</label>
                <input type="password" name="password"
                       class="form-control custom-input"
                       placeholder="********"
                       required>
            </div>

            <!-- CONFIRM -->
            <div class="mb-3">
                <label class="fw-semibold">Confirmer mot de passe</label>
                <input type="password" name="password_confirmation"
                       class="form-control custom-input"
                       placeholder="********"
                       required>
            </div>

            <!-- COUNTRY -->
            <div class="mb-3">
                <label class="fw-semibold">Pays</label>
                <input type="text" name="country"
                       class="form-control custom-input"
                       placeholder="Maroc">
            </div>

            <!-- CITY -->
            <div class="mb-3">
                <label class="fw-semibold">Ville</label>
                <input type="text" name="city"
                       class="form-control custom-input"
                       placeholder="Rabat">
            </div>

            <!-- BTN -->
            <button type="submit"
                    class="btn btn-primary w-100 rounded-pill fw-bold">
                S’inscrire
            </button>
        </form>

        <!-- LINK -->
        <div class="text-center mt-4 small">
            Déjà inscrit ?
            <a href="{{ route('login') }}"
               class="text-primary fw-bold text-decoration-none">
                Se connecter
            </a>
        </div>

    </div>

</div>

<!-- STYLE -->
<style>
.register-card {
    width: 450px;
    border-radius: 20px;
    background: white;
    z-index: 1;
}

/* INPUTS */
.custom-input {
    border-radius: 12px;
    border: 1px solid #ddd;
    transition: 0.3s;
}

.custom-input:focus {
    border-color: #003A8F;
    box-shadow: 0 0 10px rgba(0,58,143,0.2);
}

/* SHAPES */
.shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.1;
}

.shape1 {
    width: 200px;
    height: 200px;
    background: #003A8F;
    top: 10%;
    left: 5%;
}

.shape2 {
    width: 150px;
    height: 150px;
    background: #4DA3FF;
    bottom: 15%;
    right: 10%;
}

/* BUTTON ANIMATION */
.btn:hover {
    transform: scale(1.03);
    transition: 0.3s;
}
</style>

@endsection
