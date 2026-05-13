@extends('layouts.student')

@section('content')

<!-- HEADER -->
<div class="container py-5">

    <h2 class="fw-bold mb-5 text-center gradient-text">
        <i class="bi bi-chat-dots-fill me-2"></i>
        Chat par matière
    </h2>

    <div class="row g-4">

        @foreach($subjects->unique('name') as $subject)

        <div class="col-lg-4 col-md-6">

            <div class="subject-card glassmorphism h-100 text-center p-4">

                <!-- ICON -->
                <div class="icon-circle mb-4">
                    <i class="bi bi-book-half"></i>
                </div>

                <!-- TITLE -->
                <h5 class="fw-bold mb-3">
                    {{ $subject->name }}
                </h5>

                <p class="text-muted small mb-4">
                    Discutez avec vos professeurs et camarades
                </p>

                <!-- BUTTON -->
                <a href="{{ route('student.student.chat', $subject->id) }}"
                   class="btn btn-chat w-100">

                    <i class="bi bi-chat-dots me-2"></i>
                    Ouvrir Chat

                </a>

            </div>

        </div>

        @endforeach

    </div>

</div>

<style>

/* TEXT GRADIENT */
.gradient-text{
background: linear-gradient(45deg,#3b82f6,#1d4ed8);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* CARD */
.subject-card{
    border-radius:20px;
    transition:0.3s;
    position:relative;
    overflow:hidden;
}

/* HOVER */
.subject-card:hover{
    transform:translateY(-8px) scale(1.02);
    box-shadow:0 20px 40px rgba(0,0,0,0.15);
}

/* ICON */
.icon-circle{
    width:70px;
    height:70px;
    margin:auto;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;

    background:linear-gradient(135deg,#3b82f6,#1d4ed8);
    color:white;
    font-size:28px;

    box-shadow:0 6px 20px rgba(0,0,0,0.2);
    transition:0.3s;
}

/* ICON HOVER */
.subject-card:hover .icon-circle{
    transform:scale(1.1) rotate(5deg);
}

/* BUTTON */
.btn-chat{
    background:linear-gradient(135deg,#3b82f6,#1d4ed8);
    color:white;
    border:none;
    border-radius:12px;
    padding:10px;
    font-weight:600;
    transition:0.3s;
}

/* BUTTON HOVER */
.btn-chat:hover{
    background:linear-gradient(135deg,#1d4ed8,#3b82f6);
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
}

/* GLASS EFFECT */
.glassmorphism{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.4);
}

</style>

@endsection
