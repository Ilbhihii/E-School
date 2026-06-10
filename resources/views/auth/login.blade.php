@extends('layouts.front')

@section('title', 'Connexion')

@section('content')

<div class="login-page d-flex justify-content-center align-items-center"
     style="min-height:100vh; background:#F5F7FA; position:relative; overflow:hidden;">

    <!-- Shapes -->
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>

    <!-- CARD -->
    <div class="card login-card shadow-sm p-5">

        <h3 class="text-center mb-4 fw-bold text-primary">
            Connexion
        </h3>

        <!-- Errors -->
        @if ($errors->any())
            <div class="alert alert-danger text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

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

            <!-- REMEMBER -->
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input">
                <label class="form-check-label">Se souvenir de moi</label>
            </div>

            <!-- BTN -->
            <button type="submit"
                    class="btn btn-primary w-100 rounded-pill fw-bold">
                Se connecter
            </button>
        </form>

        <!-- LINKS -->
        <div class="text-center mt-4 small">

            <p class="mb-2">
                Pas encore inscrit ?
                <a href="{{ route('register') }}"
                   class="text-primary fw-bold text-decoration-none">
                    Créer un compte
                </a>
            </p>

            <a href="{{ route('password.request') }}"
               class="text-muted text-decoration-none">
                Mot de passe oublié ?
            </a>

        </div>

    </div>

</div>

<!-- STYLE -->
<style>
.login-card {
    width: 400px;
    border-radius: 20px;
    background: white;
    z-index: 1;
}

/* Inputs */
.custom-input {
    border-radius: 12px;
    border: 1px solid #ddd;
    transition: 0.3s;
}

.custom-input:focus {
    border-color: #003A8F;
    box-shadow: 0 0 10px rgba(0,58,143,0.2);
}

/* Shapes */
.shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.1;
}

.shape1 {
    width: 200px;
    height: 200px;
    background: #003A8F;
    top: 5%;
    left: 5%;
}

.shape2 {
    width: 150px;
    height: 150px;
    background: #4DA3FF;
    bottom: 10%;
    right: 10%;
}

/* Hover button */
.btn:hover {
    transform: scale(1.03);
    transition: 0.3s;
}
</style>

@endsection
