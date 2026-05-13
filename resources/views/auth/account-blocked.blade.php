@extends('layouts.front')

@section('title', "Compte en attente")

@section('content')

<div class="d-flex align-items-center justify-content-center min-vh-100 bg-gradient">

<div class="container">
<div class="row justify-content-center">
<div class="col-md-6">

<div class="card shadow-lg border-0 rounded-4 text-center p-4 glass">

<!-- 🔄 LOADER -->
<div class="mb-4">
    <div class="spinner-border text-success" style="width: 4rem; height: 4rem;"></div>
</div>



<!-- TITLE -->
<h3 class="fw-bold mb-2 text-dark">
    Compte en attente ⏳
</h3>

<p class="text-success fw-semibold mb-3">
    Validation en cours...
</p>

<!-- USER -->
<h5 class="text-secondary mb-3">
    Bonjour {{ auth()->user()->name ?? 'Étudiant' }} 👋
</h5>

<!-- MESSAGE -->
<p class="text-muted mb-4">
    Votre inscription est en cours de validation par l’administrateur.
    <br>
    <strong class="text-success">
        Vous serez redirigé automatiquement après activation 🚀
    </strong>
</p>

<!-- STEPS -->
<div class="bg-light border rounded-3 p-3 text-start mb-4">
    <ul class="mb-0 small text-muted">
        <li>🔍 Vérification du compte</li>
        <li>📩 Email de confirmation</li>
        <li>🚀 Accès complet</li>
    </ul>
</div>

<!-- BUTTON -->
<form method="POST" action="{{ route('logout') }}">
@csrf
<button class="btn btn-dark w-100 rounded-3 py-2">
    Se déconnecter
</button>
</form>

<p class="text-muted small mt-3">
    Créé le {{ auth()->user()->created_at?->format('d/m/Y') }}
</p>

</div>

</div>
</div>
</div>

</div>

<!-- 🎨 STYLE -->
<style>
.bg-gradient {
    background: linear-gradient(135deg, #eef2ff, #d1fae5);
}

.glass {
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(10px);
}

/* Hover animation */
.card:hover {
    transform: translateY(-5px);
    transition: 0.3s;
}

/* Dark mode */
body.dark {
    background: #0f172a;
}
body.dark .card {
    background: #1e293b;
    color: white;
}
body.dark .text-muted {
    color: #cbd5f5 !important;
}
</style>

<!-- 🔁 AUTO REDIRECT -->
<script>
setInterval(() => {
    fetch('/check-status')
        .then(res => res.json())
        .then(data => {
            if(data.active){
                window.location.href = "/dashboard";
            }
        });
}, 5000);
</script>

@endsection
