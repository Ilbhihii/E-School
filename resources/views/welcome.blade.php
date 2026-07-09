@extends('layouts.front')

@section('content')

<!-- HERO -->
<section class="hero text-center">
    <div class="container">
        <h1 class="fw-bold display-4">
        La plateforme intelligente <br>
        pour réussir vos études
        </h1>

        <p class="lead mt-4">
        Cours interactifs, sessions live et ressources pédagogiques
        accessibles partout.
        </p>
        <div class="mt-4">
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
            Se connecter
            </a>
        </div>

    </div>
</section>


<!-- STATS -->
<section class="py-5 bg-white text-center">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3">
                <h2 class="fw-bold text-success">+1000</h2>
                <p class="text-muted">Étudiants actifs</p>
            </div>

            <div class="col-md-3">
                <h2 class="fw-bold text-success">+50</h2>
                <p class="text-muted">Cours disponibles</p>
            </div>

            <div class="col-md-3">
                <h2 class="fw-bold text-success">+120</h2>
                <p class="text-muted">Lives organisés</p>
            </div>

            <div class="col-md-3">
                <h2 class="fw-bold text-success">95%</h2>
                <p class="text-muted">Taux de satisfaction</p>
            </div>

        </div>

    </div>
</section>


<!-- POURQUOI NOUS -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="fw-bold mb-5">Pourquoi choisir EduPlatform ?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card custom-card p-4 h-100">
                    <i class="bi bi-laptop feature-icon mb-3"></i>
                    <h5>Interface moderne</h5>
                    <p class="text-muted">
                    Une expérience utilisateur simple et intuitive.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card custom-card p-4 h-100">
                    <i class="bi bi-broadcast feature-icon mb-3"></i>
                    <h5>Lives interactifs</h5>
                    <p class="text-muted">
                    Participez à des sessions en direct avec vos enseignants.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card custom-card p-4 h-100">
                    <i class="bi bi-cloud-arrow-down feature-icon mb-3"></i>
                    <h5>Supports téléchargeables</h5>
                    <p class="text-muted">
                    Téléchargez vos cours PDF et révisez hors ligne.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- QUI SOMMES NOUS -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f"
                class="img-fluid rounded-4 shadow">
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold mb-4">Qui sommes-nous ?</h2>
                <p class="text-muted">
                EduPlatform est une plateforme d’apprentissage moderne
                qui connecte les étudiants avec des enseignants
                expérimentés afin de faciliter l’accès à l’éducation.
                </p>
                <p class="text-muted">
                Notre objectif est d’offrir une expérience éducative
                interactive grâce aux technologies numériques.
                </p>
                <ul class="list-unstyled mt-4">
                    <li class="mb-2">
                    <i class="bi bi-check-circle text-success"></i>
                    Enseignants qualifiés
                    </li>
                    <li class="mb-2">
                    <i class="bi bi-check-circle text-success"></i>
                    Plateforme interactive
                    </li>
                    <li class="mb-2">
                    <i class="bi bi-check-circle text-success"></i>
                    Apprentissage flexible
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>


<!-- OBJECTIFS -->
<section class="py-5 bg-light text-center">
    <div class="container">
        <h2 class="fw-bold mb-5">Nos Objectifs</h2>
        <div class="row g-4">
            <div class="col-md-4">
            <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b"
            class="img-fluid rounded-3 mb-3">
            <h5>Faciliter l’apprentissage</h5>
            <p class="text-muted">
            Rendre l’éducation accessible à tous.
            </p>
        </div>

        <div class="col-md-4">
            <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c"
            class="img-fluid rounded-3 mb-3">
            <h5>Encourager la réussite</h5>
            <p class="text-muted">
            Accompagner les étudiants vers la réussite.
            </p>
        </div>

        <div class="col-md-4">
            <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085"
            class="img-fluid rounded-3 mb-3">
            <h5>Innover dans l’éducation</h5>
            <p class="text-muted">
            Utiliser la technologie pour améliorer l’apprentissage.
            </p>
        </div>
    </div>
</section>


<!-- METHODES -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-5">Nos Méthodes</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card custom-card p-4 h-100">
                    <i class="bi bi-camera-video feature-icon"></i>
                    <h5 class="mt-3">Cours en direct</h5>
                    <p class="text-muted">
                    Sessions live avec interaction en temps réel.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card custom-card p-4 h-100">
                    <i class="bi bi-journal-text feature-icon"></i>
                    <h5 class="mt-3">Supports pédagogiques</h5>
                    <p class="text-muted">
                    Documents PDF et ressources téléchargeables.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card custom-card p-4 h-100">
                    <i class="bi bi-graph-up feature-icon"></i>
                    <h5 class="mt-3">Suivi des progrès</h5>
                    <p class="text-muted">
                    Suivez votre progression et améliorez vos résultats.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- COMMENT ÇA MARCHE -->
<section class="py-5 text-center bg-light">
    <div class="container">
        <h2 class="fw-bold mb-5">Comment ça marche ?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <h1 class="text-success">1</h1>
                <h5>Créer un compte</h5>
                <p class="text-muted">
                Inscrivez-vous gratuitement sur la plateforme.
                </p>
            </div>

            <div class="col-md-4">
                <h1 class="text-success">2</h1>
                <h5>Prise de contact</h5>
                <p class="text-muted">
                Passe le test pour valider ton niveau
                </p>
            </div>

            <div class="col-md-4">
                <h1 class="text-success">3</h1>
                <h5>Commencer à apprendre</h5>
                <p class="text-muted">
                Suivez les cours et téléchargez les supports.
                </p>
            </div>
        </div>
    </div>
</section>


<!-- CTA FINAL -->
<section class="hero text-center">
    <div class="container">
        <h2 class="fw-bold">
        Prêt à améliorer votre apprentissage ?
        </h2>
        <a href="{{ route('register') }}"
        class="btn btn-light btn-lg mt-4 px-5">
        S’inscrire maintenant
        </a>
    </div>
</section>


<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-4">

© {{ date('Y') }} EduPlatform | Tous droits réservés

</footer>

@endsection
