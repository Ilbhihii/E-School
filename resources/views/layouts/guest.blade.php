<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Plateforme E-learning</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }

        .hero {
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            padding: 100px 0;
        }

        .feature-icon {
            font-size: 40px;
            color: #198754;
        }

        .custom-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
    <a class="navbar-brand fw-bold text-success" href="{{ route('home') }}">
        🎓 EduPlatform
    </a>

    <div class="ms-auto">
        <a href="{{ route('login') }}" class="btn btn-outline-success me-2">Login</a>
        <a href="{{ route('register') }}" class="btn btn-success">Inscription</a>
    </div>
</nav>

@yield('content')

</body>
</html>
