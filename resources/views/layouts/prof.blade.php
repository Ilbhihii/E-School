<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Professeur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <!-- jQuery for dynamic forms -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <style>
    :root{
        --blue-main:#003A8F;      /* logo */
        --blue-dark:#0F3460;
        --violet-main:#7C3AED;
        --violet-light:#8B5CF6;
        --gradient-main: linear-gradient(135deg,#003A8F,#7C3AED);

        --gray-light:#F5F7FA;
        --gray-dark:#1F2937;
        --white:#FFFFFF;
    }


        body{
            background: var(--gray-light);
            color: var(--gray-dark);
        }

    .sidebar{
        background: linear-gradient(180deg,#003A8F,#0F3460);
        color: white;
        min-height: 100vh;
        box-shadow: 5px 0 30px rgba(0,0,0,0.2);
    }


        .sidebar h4{
            font-weight: bold;
            letter-spacing: 1px;
        }

        .sidebar .nav-link{
    color: rgba(255,255,255,0.85);
    padding: 12px;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover{
    background: rgba(255,255,255,0.15);
    transform: translateX(6px) scale(1.02);
}

        .sidebar .nav-link.active{
    background: white;
    color: var(--violet-main);
    font-weight: bold;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

        .topbar{
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    padding: 15px 20px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

        .content-area{
    background: var(--gray-light);
    min-height: 100vh;
    border-radius: 15px;
    padding: 25px;
}

        .profile-icon{
    width:50px;
    height:50px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;

    background: linear-gradient(135deg,#003A8F,#7C3AED);

    color:white;
    font-size:22px;

    transition:0.3s;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

.profile-icon:hover{
    transform: scale(1.1) rotate(5deg);
    box-shadow:0 10px 25px rgba(124,58,237,0.5);
}

        .btn-primary{
    background: linear-gradient(135deg,#003A8F,#7C3AED);
    border: none;
    border-radius: 10px;
}

.btn-primary:hover{
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(124,58,237,0.4);
}

.btn-danger{
    background: #D90429;
    border-radius: 10px;
}

        .profile-icon + strong{

            display:block;
            margin-top:6px;

            font-size:14px;
            font-weight:600;

            color:#374151;
            text-align:center;

        }

    </style>
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar p-3">

            <h4 class="text-center mb-4 fw-bold">
                <img src="{{ asset('images/Edu-School.png') }}" width="40" height="40" alt="E-School Logo"> E-School Prof
            </h4>

            <ul class="nav flex-column">

                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="{{ route('prof.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="{{ route('prof.courses.index') }}">
                        <i class="bi bi-book"></i> Cours
                    </a>
                </li>
                
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="{{ route('prof.lives.index') }}">
                        <i class="bi bi-camera-video-fill"></i> Lives
                    </a>
                </li> 
                
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="{{ route('prof.devoir.index') }}">
                        <i class="bi bi-file-earmark-check"></i> Pose Devoirs
                    </a>
                </li> 
                

                <!-- CHAT -->
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="{{ route('prof.chat.subjects') }}">
                        <i class="bi bi-chat-dots"></i> Questions Étudiants
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="{{ route('prof.assignments') }}">
                        <i class="bi bi-file-earmark-text"></i>
                        Devoirs Étudiants
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="{{ route('prof.absences') }}">
                        <i class="bi bi-calendar-x"></i>
                        Absences
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="{{ route('prof.tests.index') }}">
                        <i class="bi bi-clipboard-check"></i> Tests
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="{{ route('prof.schedule') }}">
                        <i class="bi bi-calendar-week"></i> Planning
                    </a>
                </li>


            </ul>

            <!-- LOGOUT -->
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </button>
            </form>

        </div>

        <!-- CONTENT -->
        <div class="col-md-10 content-area">

            <!-- TOPBAR -->
            <div class="topbar d-flex justify-content-between align-items-center mb-4">

                <h4>Espace Professeur</h4>

                <div class="dropdown text-center">

                    <div class="profile-icon dropdown-toggle" data-bs-toggle="dropdown" style="cursor:pointer;">
                        <i class="bi bi-person-circle"></i>
                    </div>

                    <strong>{{ auth()->user()->name ?? 'Professeur' }}</strong>

                    <ul class="dropdown-menu dropdown-menu-end mt-2 shadow">

                        <li>
                            <a class="dropdown-item" href="{{ route('prof.profile') }}">
                                <i class="bi bi-person"></i> Mon profil
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="{{ route('prof.settings') }}">
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

            <!-- PAGE CONTENT -->
            @yield('content')

        </div>

    </div>
</div>

</body>
</html>
