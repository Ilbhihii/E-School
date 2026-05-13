@extends('layouts.admin')

@section('content')
<div class="py-5" style="background: linear-gradient(135deg, #eef2ff 0%, #f8fafc 100%); min-height: 80vh;">
    <div class="container">

        <!-- Avatar Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-4 text-center">
                <div class="profile-icon-large mx-auto mb-4"
                     style="width:150px;height:150px;
                     background:linear-gradient(135deg,#003A8F,#0F3460);
                     border-radius:50%;
                     display:flex;align-items:center;justify-content:center;
                     box-shadow:0 20px 40px rgba(0,58,143,0.4);">

                    <i class="bi bi-person-circle text-white" style="font-size:80px;"></i>
                </div>

                <h2 class="fw-bold text-dark mb-1">Mon Profil Administrateur</h2>

                <span class="badge"
                      style="background:linear-gradient(45deg,#003A8F,#0F3460);
                      color:white;padding:0.5rem 1.5rem;border-radius:50px;">
                    ✔️ Admin Vérifié
                </span>
            </div>
        </div>

        <!-- Infos -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg glassmorphism">
                    <div class="card-body p-5">

                        <div class="row">

                            <div class="col-md-6 mb-4">
                                <h6 class="text-muted">Nom complet</h6>
                                <h5 class="fw-bold">{{ auth()->user()->name }}</h5>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6 class="text-muted">Email</h6>
                                <p class="fs-5">{{ auth()->user()->email }}</p>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6 class="text-muted">Rôle</h6>
                                <span class="badge bg-primary px-3 py-2">
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6 class="text-muted">Inscrit le</h6>
                                <p class="fs-5">
                                    {{ auth()->user()->created_at->format('d/m/Y') }}
                                </p>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>



        <!-- Actions -->
        <div class="text-center mt-5">
            <a href="{{ route('admin.settings') }}"
               class="btn btn-lg me-3"
               style="background:linear-gradient(45deg,#003A8F,#0F3460);
               color:white;border-radius:50px;padding:10px 30px;">
                ⚙️ Modifier Profil
            </a>

            <a href="{{ route('admin.dashboard') }}"
               class="btn btn-outline-primary btn-lg"
               style="border-radius:50px;">
                📊 Dashboard
            </a>
        </div>

    </div>
</div>

<style>
.glassmorphism {
    backdrop-filter: blur(20px);
    background: rgba(255,255,255,0.4);
    border-radius: 15px;
}

.hover-lift {
    transition: 0.3s;
}
.hover-lift:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
</style>

@endsection
