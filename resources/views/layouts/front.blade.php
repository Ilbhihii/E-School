<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Plateforme Éducative')</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #F5F7FA;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ================= NAVBAR ================= */
        .modern-navbar {
            background: #00398f6f;
            padding: 15px 0;
            transition: 0.3s;
        }

        .modern-navbar .nav-link {
            color: white !important;
            margin: 0 10px;
            position: relative;
        }

        .modern-navbar .nav-link::after {
            content: "";
            width: 0;
            height: 2px;
            background: #FFD166;
            position: absolute;
            bottom: -5px;
            left: 0;
            transition: 0.3s;
        }

        .modern-navbar .nav-link:hover::after {
            width: 100%;
        }

        .navbar-scrolled {
            background: #002c6b !important;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }

        /* ================= BOUTONS ================= */
        .nav-btn {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
        }

        .btn-primary {
            background: #003A8F;
            border: none;
        }

        .btn-primary:hover {
            background: #002c6b;
        }

        .btn-danger {
            background: #D90429;
            border: none;
        }

        /* ================= CONTENU ================= */
        .front-content {
            margin-top: 100px;
            min-height: 70vh;
        }

        /* ================= FOOTER ================= */
        .footer-modern {
            background: #002c6b;
        }

        .footer-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .footer-link:hover {
            color: #FFD166;
        }



        /* ================= BACK TO TOP ================= */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #003A8F;
            color: white;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: none;
            align-items: center;
            justify-content: center;
            border: none;
        }

        .back-to-top.show {
            display: flex;
        }

        /* hover effect */
.hover-item {
    transition: all 0.25s ease;
    border-radius: 10px;
}

.hover-item:hover {
    background: #f1f5f9;
    transform: translateX(5px);
}

/* dropdown animation */
.dropdown-menu {
    animation: fadeIn 0.25s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}



    </style>
</head>

<body>

<!-- NAVBAR -->
<nav id="mainNavbar" class="navbar navbar-expand-lg modern-navbar fixed-top">
    <div class="container">
        <a class="navbar-brand text-white fw-bold" href="{{ route('home') }}">
        <img src="{{ asset('images/Edu-School.png') }}" width="40" height="40" alt="E-School Logo">
             E-School
        </a>

        <button class="navbar-toggler bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            ☰
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                <!-- Accueil -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Accueil</a>
                </li>

                <!-- Niveaux -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('levels') }}">Niveaux</a>
                </li>

        


                <!-- Lives -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('front.lives') }}">Lives</a>
                </li>

                
                <!-- Offres -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('plans') }}">Offres</a>
                </li>

                <!-- Auth -->
                @auth
                    <li class="nav-item ms-lg-3">
                        <a href="{{ route('student.dashboard') }}" class="btn btn-primary nav-btn">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-danger nav-btn">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item ms-lg-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-light nav-btn me-2">
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-primary nav-btn">
                            Inscription
                        </a>
                    </li>
                @endauth

            </ul>
        </div>
    </div>
</nav>

<!-- CONTENU -->
<main class="front-content container">
    @yield('content')
</main>

<!-- FOOTER -->
<footer class="footer-modern text-white py-4 mt-5">
    <div class="container text-center">
        <h5>🎓 E-School</h5>
        <p>Plateforme éducative moderne pour étudiants et enseignants</p>

        <div class="mb-3">
            <a href="#" class="footer-link me-3">Accueil</a>
            <a href="#" class="footer-link me-3">Matiére</a>
            <a href="#" class="footer-link me-3">Contact</a>
        </div>

        <small>&copy; {{ date('Y') }} Tous droits réservés</small>
    </div>
</footer>

<!-- BACK TO TOP -->
<button id="backToTop" class="back-to-top">
    ↑
</button>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('.dropdown').forEach(function(dropdown) {

    dropdown.addEventListener('mouseenter', function () {
        let menu = this.querySelector('.dropdown-menu');
        menu.classList.add('show');
    });

    dropdown.addEventListener('mouseleave', function () {
        let menu = this.querySelector('.dropdown-menu');
        menu.classList.remove('show');
    });

});
</script>


</body>
</html>
