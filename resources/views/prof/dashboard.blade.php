@extends('layouts.prof')

@section('content')

<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-lg">
            <div class="card-body p-5 text-center bg-gradient text-black rounded-top-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="bi bi-person-video3 fs-1 mb-3 d-block"></i>
                <h1 class="display-5 fw-bold mb-2">Dashboard Professeur</h1>
                <div class="bg-white bg-opacity-20 p-4 rounded-4 mb-3">
                    <h3 class="h2 fw-bold">Bienvenue professeur {{ auth()->user()->name }}</h3>
                </div>
                <p class="lead opacity-90">Gérez vos classes, devoirs et communications avec vos étudiants</p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow h-100 text-center hover-lift">
            <div class="card-body p-4">
                <i class="bi bi-people-fill text-primary fs-1 mb-3 d-block"></i>
                <h3 class="h4 fw-bold text-dark mb-1">Étudiants</h3>
<div class="h2 fw-bold text-primary">{{ $studentsCount ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow h-100 text-center hover-lift">
            <div class="card-body p-4">
                <i class="bi bi-book-half text-success fs-1 mb-3 d-block"></i>
                <h3 class="h4 fw-bold text-dark mb-1">Classes</h3>
<div class="h2 fw-bold text-success">{{ $classesCount ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow h-100 text-center hover-lift">
            <div class="card-body p-4">
                <i class="bi bi-file-earmark-text-fill text-warning fs-1 mb-3 d-block"></i>
                <h3 class="h4 fw-bold text-dark mb-1">Devoirs</h3>
<div class="h2 fw-bold text-warning">{{ $assignmentsCount ?? 0 }}</div>
            </div>
        </div>
    </div>
<div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow h-100 text-center hover-lift">
            <div class="card-body p-4">
                <i class="bi bi-calendar-x-fill text-danger fs-1 mb-3 d-block"></i>
                <h3 class="h4 fw-bold text-dark mb-1">Absences</h3>
<div class="h2 fw-bold text-danger">{{ $absencesCount ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- New Tests Card -->
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow h-100 text-center hover-lift">
            <div class="card-body p-4">
                <i class="bi bi-clipboard-check text-info fs-1 mb-3 d-block"></i>
                <h3 class="h4 fw-bold text-dark mb-1">Tests</h3>
                <div class="h2 fw-bold text-info">{{ $testsCount ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <h3 class="h4 fw-bold mb-4 text-center text-muted">Actions Rapides</h3>
        <div class="row g-3 justify-content-center">
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('prof.absences') }}" class="card border-0 shadow text-decoration-none h-100 text-center p-4 hover-lift hover-shadow-lg">
                    <i class="bi bi-calendar-x fs-2 text-danger mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark mb-1">Absences</h5>
                    <p class="text-muted mb-0 small">Marquer les présences</p>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('prof.assignments') }}" class="card border-0 shadow text-decoration-none h-100 text-center p-4 hover-lift hover-shadow-lg">
                    <i class="bi bi-file-earmark-text fs-2 text-primary mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark mb-1">Devoirs</h5>
                    <p class="text-muted mb-0 small">Gérer les devoirs</p>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('prof.chat.subjects') }}" class="card border-0 shadow text-decoration-none h-100 text-center p-4 hover-lift hover-shadow-lg">
                    <i class="bi bi-chat-dots fs-2 text-success mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark mb-1">Messages</h5>
                    <p class="text-muted mb-0 small">Répondre étudiants</p>
                </a>
            </div>
<div class="col-md-3 col-sm-6">
                <a href="{{ route('prof.tests.index') }}" class="card border-0 shadow text-decoration-none h-100 text-center p-4 hover-lift hover-shadow-lg">
                    <i class="bi bi-clipboard-check fs-2 text-info mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark mb-1">Tests</h5>
                    <p class="text-muted mb-0 small">Créer / Gérer tests</p>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: all 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
}
.hover-shadow-lg:hover {
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}
.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

@endsection

