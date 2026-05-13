@extends('layouts.student')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #66ea90 0%, #4ba28552 100%); min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; backdrop-filter: blur(10px); background: rgba(255,255,255,0.95);">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <i class="fas fa-cog fa-3x text-primary mb-3"></i>
                        <h2 class="display-5 fw-bold text-dark mb-1">Paramètres du Compte</h2>
                        <p class="text-muted lead">Gérez votre profil et votre sécurité</p>
                    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Profil Section -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-gradient-primary text-white border-0" style="border-radius: 20px 20px 0 0 !important; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x me-3"></i>
                        <h5 class="mb-0 fw-bold">Mon Profil</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student.settings.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Profile Picture -->
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&size=120&background=667eea&color=fff' }}" 
                                     alt="Profile" class="rounded-circle border-4 border-white shadow-lg" style="width: 120px; height: 120px; object-fit: cover;">
                                <label for="profile_photo" class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-2 cursor-pointer" style="margin: 0 10px 10px 0;" title="Changer la photo">
                                    <i class="fas fa-camera text-white fs-6"></i>
                                    <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*">
                                </label>
                            </div>
                            <p class="text-muted mt-2">{{ auth()->user()->name }}</p>
                        </div>

                        <div class="mb-4 position-relative">
                            <label for="name" class="form-label fw-bold">Nom Complet</label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror shadow-sm" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 position-relative">
                            <label for="email" class="form-label fw-bold">Adresse Email</label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror shadow-sm" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-lg" style="border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; transition: all 0.3s ease;">
                            <i class="fas fa-save me-2"></i>Mettre à jour le Profil
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Section -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-gradient-success text-white border-0" style="border-radius: 20px 20px 0 0 !important; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lock fa-2x me-3"></i>
                        <h5 class="mb-0 fw-bold">Changer le Mot de Passe</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student.settings.password.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4 position-relative">
                            <label for="current_password" class="form-label fw-bold">Mot de Passe Actuel</label>
                            <input type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror shadow-sm" id="current_password" name="current_password" required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 position-relative">
                            <label for="password" class="form-label fw-bold">Nouveau Mot de Passe <small class="text-muted">(8 caractères min)</small></label>
                            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror shadow-sm" id="password" name="password" required minlength="8" style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-1"><i class="fas fa-info-circle me-1"></i>Utilisez une combinaison de lettres, chiffres et symboles</div>
                        </div>

                        <div class="mb-4 position-relative">
                            <label for="password_confirmation" class="form-label fw-bold">Confirmer le Nouveau Mot de Passe</label>
                            <input type="password" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror shadow-sm" id="password_confirmation" name="password_confirmation" required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow-lg" style="border-radius: 12px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; transition: all 0.3s ease;">
                            <i class="fas fa-key me-2"></i>Changer le Mot de Passe
                        </button>
                    </form>
                </div>
            </div>
        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer { cursor: pointer; }
.form-control:focus, .form-control-lg:focus {
    border-color: #667eea !important;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
    transform: translateY(-2px);
}
.btn:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important; }
.card { transition: all 0.3s ease; }
.card:hover { transform: translateY(-5px); }
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.card { animation: fadeInUp 0.6s ease-out; }
</style>

@endsection
