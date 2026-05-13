@extends('layouts.prof')

@section('content')
<div class="py-5" style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); min-height: 80vh;">
    <div class="container">
        <!-- Hero Avatar Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-4 col-md-6 text-center">
                <div class="profile-icon-large mx-auto mb-4" style="width: 150px; height: 150px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 20px 40px rgba(99,102,241,0.3); transition: all 0.3s ease; cursor: pointer;">
                    <i class="bi bi-person-circle" style="font-size: 80px; color: white;"></i>
                </div>
                <h2 class="mb-1 fw-bold text-dark">Mon Profil Professeur</h2>
                <span class="badge bg-gradient text-primary" style="background: linear-gradient(45deg, #6366f1, #8b5cf6); color: white; padding: 0.5rem 1.5rem; border-radius: 50px; font-size: 0.9rem;">
                    <i class="bi bi-patch-check text-primary me-1"></i> Rôle Vérifié
                </span>
            </div>
        </div>

        <!-- Profile Info & Stats Grid -->
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg glassmorphism" style="backdrop-filter: blur(20px); background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.2);">
                    <div class="card-body p-5">
                        <div class="row text-center text-md-start">
                            <div class="col-md-6 mb-4">
                                <h6 class="text-muted mb-2"><i class="bi bi-person-fill text-primary me-2"></i>Nom Complet</h6>
                                <h5 class="fw-bold">{{ auth()->user()->name }}</h5>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6 class="text-muted mb-2"><i class="bi bi-envelope-fill text-success me-2"></i>Email</h6>
                                <p class="fs-5">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6 class="text-muted mb-2"><i class="bi bi-geo-alt-fill text-warning me-2"></i>Location</h6>
                                <p class="fs-5">{{ auth()->user()->location ?? 'Non spécifiée' }}</p>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6 class="text-muted mb-2"><i class="bi bi-calendar-check-fill text-info me-2"></i>Inscrit le</h6>
                                <p class="fs-5">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="col-lg-8">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border-0 h-100 text-center glassmorphism hover-lift" style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); transition: all 0.3s ease;">
                            <div class="card-body">
                                <i class="bi bi-building fs-1 text-primary mb-2"></i>
                                <h4>3</h4>
                                <p class="text-muted mb-0">Classes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 h-100 text-center glassmorphism hover-lift" style="background: rgba(139,92,246,0.1); border: 1px solid rgba(139,92,246,0.2);">
                            <div class="card-body">
                                <i class="bi bi-people fs-1 text-purple mb-2"></i>
                                <h4>45</h4>
                                <p class="text-muted mb-0">Étudiants</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 h-100 text-center glassmorphism hover-lift" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2);">
                            <div class="card-body">
                                <i class="bi bi-book fs-1 text-success mb-2"></i>
                                <h4>12</h4>
                                <p class="text-muted mb-0">Cours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row justify-content-center mt-4">
                <div class="col-md-6 text-center">
                    <a href="{{ route('prof.settings') }}" class="btn btn-lg btn-gradient me-3 mb-2" style="background: linear-gradient(45deg, #6366f1, #8b5cf6); border: none; color: white; padding: 0.75rem 2rem; border-radius: 50px; font-weight: 600; box-shadow: 0 10px 30px rgba(99,102,241,0.4); transition: all 0.3s ease;">
                        <i class="bi bi-gear me-2"></i> Modifier Profil
                    </a>
                    <a href="{{ route('prof.dashboard') }}" class="btn btn-lg btn-outline-primary mb-2" style="border-radius: 50px; padding: 0.75rem 2rem; font-weight: 600;">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.glassmorphism {
    backdrop-filter: blur(20px);
    transition: all 0.3s ease;
}
.glassmorphism:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}
.hover-lift:hover {
    transform: translateY(-8px);
}
.animate-float {
    animation: float 3s ease-in-out infinite;
}
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}
.btn-gradient:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 40px rgba(99,102,241,0.5);
}
@media (max-width: 768px) {
    .profile-icon-large { width: 120px !important; height: 120px !important; }
    .profile-icon-large i { font-size: 60px !important; }
}
</style>

@endsection

