<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">

<title>Espace Étudiant</title>
<meta name="viewport" content="width=device-width, initial-scale=1">


<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<link rel="stylesheet" href="{{ asset('css/student.css') }}">


<style>
:root {
--primary: #0066CC;
    --secondary: #00CC99;
    --accent: #FFD166;
    --danger: #D90429;
    --dark: #2B2D42;
    --light: #F5F7FA;
}

/* GLOBAL */
body {
    background: var(--light);
    font-family: 'Segoe UI', sans-serif;
}

/* ================= NAVBAR ================= */
.navbar {
background: linear-gradient(135deg, #0066CC, #00A885);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.navbar .nav-link {
    transition: 0.3s;
    font-weight: 500;
}

.navbar .nav-link:hover {
    opacity: 0.8;
    transform: translateY(-1px);
}

/* ================= SIDEBAR ================= */
.sidebar {
    background: white;
    border-right: 1px solid #e5e7eb;
    box-shadow: 0 0 20px rgba(0,0,0,0.05);
}

/* PROFILE CARD */
.profile-card {
    background: linear-gradient(135deg, #0066CC, #00CC99);
    color: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

/* PROFILE ICON */
.profile-icon-large {
    width: 80px;
    height: 80px;
    margin: auto;
    border-radius: 50%;
    background: white;
    color: var(--primary);
    font-size: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* NAV LINKS */
.sidebar .nav-link {
    color: var(--dark);
    padding: 12px;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.sidebar .nav-link:hover {
background: rgba(0,102,204,0.1);
    color: var(--primary);
    transform: translateX(5px);
}

.sidebar .nav-link.active {
    background: var(--primary);
    color: white;
    font-weight: 600;
}

/* ================= CONTENT ================= */
.content-area {
    background: #f8fafc;
    min-height: 100vh;
    border-radius: 10px;
}

/* ================= BUTTON ================= */
.btn-danger {
    border-radius: 12px;
    transition: 0.3s;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* ================= DROPDOWN ================= */
.dropdown-menu {
    border-radius: 15px;
    border: none;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

/* ================= PROFILE ICON NAV ================= */
.profile-icon {
    width: 40px;
    height: 40px;
    background: white;
    color: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ================= CARD (GLOBAL) ================= */
.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}
.card .card-body {
    padding: 20px;
}

</style>

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">

<span class="navbar-brand fw-bold">
<img src="{{ asset('images/Edu-School.png') }}" width="40" height="40" alt="E-School Logo"> Espace Étudiant
</span>

<div class="ms-auto d-flex align-items-center text-white">

@if(!Route::is('student.waiting'))
        <a class="nav-link text-white me-3" href="{{ route('student.subjects.index') }}" class="nav-link">
         Matières
    </a>
@endif
<!-- Other links (dashboard, lives, chats, assignments, absences) removed as routes updated per task -->
@if(!Route::is('student.waiting'))
<a class="nav-link text-white me-3" href="{{ route('student.lives') }}" class="nav-link">
         Lives
    </a>        
@endif

@if(!Route::is('student.waiting'))
<a class="nav-link text-white me-3" href="{{ route('student.chats') }}" class="nav-link">
         Chat 
    </a>
@endif

@if(!Route::is('student.waiting'))
<a class="nav-link text-white me-3" href="{{ route('student.assignments')}}" class="nav-link">
         Mes Devoirs
    </a>                            
@endif

@if(!Route::is('student.waiting'))
<a class="nav-link text-white me-3" href="{{ route('student.absences')}}" class="nav-link">
         Mes Absences
    </a>
@endif


<div class="dropdown d-flex align-items-center">

    <div class="profile-icon me-2">
        <i class="bi bi-person-circle"></i>
    </div>

    <span class="fw-semibold dropdown-toggle"
          data-bs-toggle="dropdown"
          style="cursor:pointer;">
        {{ auth()->user()->name }}
    </span>

    <ul class="dropdown-menu dropdown-menu-end shadow mt-2">

        <li>
            <a class="dropdown-item" href="{{ route('student.profile') }}">
                <i class="bi bi-person"></i> Mon Profil
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="{{ route('student.settings') }}">
                <i class="bi bi-gear"></i> Paramètres
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </button>
            </form>
        </li>

    </ul>

</div>


</div>
</nav>


<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<div class="col-md-2 sidebar p-3 min-vh-100">

<div class="profile-card mb-4 text-center">

<div class="profile-icon-large">
    <i class="bi bi-person-circle"></i>
</div>


<h6 class="fw-bold mb-0">{{ auth()->user()->name }}</h6>
<small class="text-muted">Étudiant</small>

</div>

<ul class="nav flex-column">

@if(!Route::is('student.waiting'))
<li class="nav-item mb-2">
<a class="nav-link" href="{{ route('student.dashboard') }}">
<i class="bi bi-house me-2"></i> Dashboard
</a>
</li>
@endif

@if(!Route::is('student.waiting'))
<li class="nav-item mb-2">
<a class="nav-link" href="{{ route('student.subjects.index') }}">
<i class="bi bi-book me-2"></i> Matières
</a>
</li>
@endif

@if(!Route::is('student.waiting'))
<li class="nav-item mb-2">
<a class="nav-link" href="{{ route('student.lives') }}">
<i class="bi bi-camera-video me-2"></i> Lives
</a>
</li>
@endif

@if(!Route::is('student.waiting'))
<li class="nav-item mb-2">
<a class="nav-link" href="{{ route('student.chats') }}">
<i class="bi bi-chat-dots me-2"></i> Chat Matières
</a>
</li>
@endif

@if(!Route::is('student.waiting'))
<li class="nav-item mb-2">
<a class="nav-link" href="{{ route('student.assignments') }}">
<i class="bi bi-upload me-2"></i> Mes Devoirs
</a>
</li>
@endif

@if(!Route::is('student.waiting'))
<li class="nav-item mb-2">
<a class="nav-link" href="{{ route('student.absences') }}">
<i class="bi bi-calendar-x me-2"></i> Mes Absences
</a>
</li>
@endif

</ul>


<form method="POST" action="{{ route('logout') }}">
@csrf
<button class="btn btn-danger w-100 mt-4">
<i class="bi bi-box-arrow-right"></i> Se Déconnecter
</button>
</form>

</div>


<!-- CONTENT -->
<div class="col-md-10 p-4 content-area">

@yield('content')

</div>

</div>
</div>


<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>
