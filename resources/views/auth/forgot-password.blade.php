@extends('layouts.front')

@section('title', 'Mot de passe oublié')

@section('content')

<div class="forgot-page d-flex justify-content-center align-items-center"
     style="min-height:100vh; background:#F5F7FA; position:relative; overflow:hidden;">

    <!-- SHAPES -->
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>

    <!-- CARD -->
    <div class="card forgot-card shadow-sm p-5">

        <h3 class="text-center mb-3 fw-bold text-primary">
            Mot de passe oublié ?
        </h3>

        <p class="text-center text-muted small mb-4">
            Entrez votre email et nous vous enverrons un lien de réinitialisation.
        </p>

        <!-- STATUS -->
        <x-auth-session-status class="alert alert-info mb-3" :status="session('status')" />

        <!-- ERRORS -->
        <x-auth-validation-errors class="alert alert-danger mb-3" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- EMAIL -->
            <div class="mb-3">
                <label class="fw-semibold">Email</label>
                <input id="email"
                       class="form-control custom-input"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="exemple@email.com"
                       required
                       autofocus>
            </div>

            <!-- BTN -->
            <button type="submit"
                    class="btn btn-primary w-100 rounded-pill fw-bold">
                Envoyer le lien
            </button>
        </form>

        <!-- BACK -->
        <div class="text-center mt-4 small">
            Retour à la
            <a href="{{ route('login') }}"
               class="text-primary fw-bold text-decoration-none">
                connexion
            </a>
        </div>

    </div>

</div>

<!-- STYLE -->
<style>
.forgot-card {
    width: 400px;
    border-radius: 20px;
    background: white;
    z-index: 1;
}

/* INPUT */
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

/* BUTTON */
.btn:hover {
    transform: scale(1.03);
    transition: 0.3s;
}
</style>

@endsection
