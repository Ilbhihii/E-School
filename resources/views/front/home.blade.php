@extends('layouts.front')

@section('title', 'Accueil')

@section('content')

<!-- HERO -->
<section class="hero-modern text-black text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">
            La plateforme intelligente <br>
            pour réussir vos études
        </h1>
        <p class="lead mb-5">
            Cours interactifs, sessions live et ressources pédagogiques accessibles partout.
        </p>

        <a href="{{ route('front.classes') }}" class="btn btn-primary btn-lg px-5 me-3">
            🚀 Voir les matières
        </a>

        <a href="{{ route('login') }}" class="btn  btn-outline-primary btn-lg px-5">
            Se connecter
        </a>
    </div>
</section>


<!-- STATS -->
<section class="py-5 text-center bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h2 class="fw-bold text-primary">+1000</h2>
                <p>Étudiants actifs</p>
            </div>

            <div class="col-md-3">
                <h2 class="fw-bold text-primary">+50</h2>
                <p>Cours disponibles</p>
            </div>

            <div class="col-md-3">
                <h2 class="fw-bold text-primary">+120</h2>
                <p>Lives organisés</p>
            </div>

            <div class="col-md-3">
                <h2 class="fw-bold text-primary">95%</h2>
                <p>Satisfaction</p>
            </div>
        </div>
    </div>
</section>


<!-- POURQUOI NOUS -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-5">Pourquoi choisir E-School ?</h2>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow border-0 p-4 h-100">
                    <i class="bi bi-laptop fs-1 text-primary"></i>
                    <h5 class="mt-3">Interface moderne</h5>
                    <p class="text-muted">Simple et intuitive.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow border-0 p-4 h-100">
                    <i class="bi bi-broadcast fs-1 text-primary"></i>
                    <h5 class="mt-3">Lives interactifs</h5>
                    <p class="text-muted">Cours en direct avec interaction.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow border-0 p-4 h-100">
                    <i class="bi bi-cloud-arrow-down fs-1 text-primary"></i>
                    <h5 class="mt-3">Supports PDF</h5>
                    <p class="text-muted">Téléchargement facile.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- QUI SOMMES NOUS -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-md-6">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f"
                class="img-fluid rounded shadow">
            </div>

            <div class="col-md-6">
                <h2 class="fw-bold mb-4">Qui sommes-nous ?</h2>

                <p class="text-muted">
                    E-School connecte les étudiants avec des enseignants qualifiés.
                </p>

                <ul class="list-unstyled mt-3">
                    <li><i class="bi bi-check-circle text-primary"></i> Enseignants qualifiés</li>
                    <li><i class="bi bi-check-circle text-primary"></i> Plateforme interactive</li>
                    <li><i class="bi bi-check-circle text-primary"></i> Apprentissage flexible</li>
                </ul>
            </div>

        </div>
    </div>
</section>


<!-- OBJECTIFS -->
<section class="py-5 text-center">
    <div class="container">
        <h2 class="fw-bold mb-5">Nos Objectifs</h2>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card border-0 shadow">
                    <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b"
                    class="card-img-top">
                    <div class="card-body">
                        <h5>Faciliter l’apprentissage</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow">
                    <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c"
                    class="card-img-top">
                    <div class="card-body">
                        <h5>Encourager la réussite</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow">
                    <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085"
                    class="card-img-top">
                    <div class="card-body">
                        <h5>Innover dans l’éducation</h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- COMMENT CA MARCHE -->
<section class="py-5 bg-light text-center">
    <div class="container">
        <h2 class="fw-bold mb-5">Comment ça marche ?</h2>

        <div class="row">
            <div class="col-md-4">
                <h1 class="text-primary">1</h1>
                <h5>Créer un compte</h5>
            </div>

            <div class="col-md-4">
                <h1 class="text-primary">2</h1>
                <h5>Choisir votre niveau</h5>
            </div>

            <div class="col-md-4">
                <h1 class="text-primary">3</h1>
                <h5>Commencer à apprendre</h5>
            </div>
        </div>
    </div>
</section>


<!-- CTA -->
<section class="text-center text-black py-5" style="background:#003A8F;">
    <div class="container">
        <h2 class="fw-bold">Prêt à commencer ?</h2>

        <a href="{{ route('register') }}" class="btn btn-light btn-lg mt-3 px-5">
            S’inscrire maintenant
        </a>
    </div>
</section>

@endsection
