<!DOCTYPE html prefix ignored - full blade content>
@extends('layouts.prof')

@section('content')

<style>
:root {
  --purple-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
  --profile-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  --password-gradient: linear-gradient(135deg, #10b981 0%, #34d399 100%);
  --glass-bg: rgba(255, 255, 255, 0.1);
  --glass-border: rgba(99, 102, 241, 0.3);
}

.hero-settings {
  background: var(--purple-gradient);
  box-shadow: 0 25px 50px rgba(99, 102, 241, 0.4);
  animation: fadeInUp 0.8s ease-out;
}

.glass-card {
  background: var(--glass-bg);
  backdrop-filter: blur(20px);
  border: 1px solid var(--glass-border);
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.glass-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 30px 60px rgba(99, 102, 241, 0.3);
}

.form-floating-custom .form-control {
  border: 2px solid rgba(99, 102, 241, 0.3);
  background: rgba(255,255,255,0.8);
  transition: all 0.3s ease;
}

.form-floating-custom .form-control:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
  background: white;
}

.btn-modern {
  border: none;
  position: relative;
  overflow: hidden;
}

.btn-modern::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition: 0.6s;
}

.btn-modern:hover::before {
  left: 100%;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes iconSpin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

<!-- Hero Header -->
<div class="row mb-5">
  <div class="col-12">
    <div class="card border-0 hero-settings rounded-5 text-white text-center p-5 position-relative overflow-hidden">
      <div class="position-absolute top-0 start-0 w-100 h-100 opacity-20">
        <div style="animation: iconSpin 20s linear infinite;"></div>
      </div>
      <i class="bi bi-gear-fill fs-1 mb-4 position-relative z-index-1" style="filter: drop-shadow(0 0 20px rgba(255,255,255,0.8)); font-size: 4rem;"></i>
      <h1 class="display-4 fw-bold mb-3 position-relative z-index-1">Paramètres</h1>
      <p class="lead opacity-90 position-relative z-index-1">Gérez votre profil et votre sécurité</p>
    </div>
  </div>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-4 shadow border-0 mb-4 animate__animated animate__fadeIn" role="alert" style="background: linear-gradient(135deg, #10b981, #34d399); color: white; backdrop-filter: blur(10px);">
  <i class="bi bi-check-circle-fill me-2"></i>
  {{ session('success') }}
  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger rounded-4 shadow border-0 mb-4 animate__animated animate__fadeIn" style="backdrop-filter: blur(10px);">
  <i class="bi bi-exclamation-triangle-fill me-2"></i>
  <ul class="mb-0">
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<div class="row g-4">
  <!-- Profil Section -->
  <div class="col-lg-6">
    <div class="card glass-card h-100 rounded-5 border-0 p-0 overflow-hidden">
      <div class="card-header p-4 text-white position-relative overflow-hidden" style="background: var(--profile-gradient);">
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-20" style="background: radial-gradient(circle at 20% 80%, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);"></div>
        <div class="d-flex align-items-center position-relative">
          <i class="bi bi-person-circle fs-2 me-3 position-relative" style="filter: drop-shadow(0 0 10px rgba(255,255,255,0.5));"></i>
          <h5 class="mb-0 fw-bold position-relative">Mon Profil</h5>
        </div>
      </div>
      <div class="card-body p-5">
        <form method="POST" action="{{ route('prof.settings.profile.update') }}">
          @csrf
          @method('PUT')
          
          <div class="form-floating mb-4 form-floating-custom">
            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required placeholder="Votre nom">
            <label for="name">Nom complet</label>
            @error('name')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-floating mb-5 form-floating-custom">
            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required placeholder="votre@email.com">
            <label for="email">Adresse Email</label>
            @error('email')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="btn btn-lg btn-modern w-100 py-3 fw-bold text-white rounded-4 shadow-lg position-relative overflow-hidden" style="background: var(--profile-gradient); font-size: 1.1rem;">
            <i class="bi bi-check2-circle me-2"></i>Mettre à jour le profil
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Password Section -->
  <div class="col-lg-6">
    <div class="card glass-card h-100 rounded-5 border-0 p-0 overflow-hidden">
      <div class="card-header p-4 text-white position-relative overflow-hidden" style="background: var(--password-gradient);">
        <div class="position-absolute top-0 end-0 w-100 h-100 opacity-20" style="background: radial-gradient(circle at 80% 20%, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);"></div>
        <div class="d-flex align-items-center position-relative">
          <i class="bi bi-shield-lock-fill fs-2 me-3 position-relative" style="filter: drop-shadow(0 0 10px rgba(255,255,255,0.5));"></i>
          <h5 class="mb-0 fw-bold position-relative">Sécurité</h5>
        </div>
      </div>
      <div class="card-body p-5">
        <form method="POST" action="{{ route('prof.settings.password.update') }}">
          @csrf
          @method('PUT')
          
          <div class="form-floating mb-4 form-floating-custom">
            <input type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required placeholder="Mot de passe actuel">
            <label for="current_password">Mot de passe actuel</label>
            @error('current_password')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-floating mb-4 form-floating-custom">
            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" required minlength="8" placeholder="Nouveau mot de passe">
            <label for="password">Nouveau mot de passe</label>
            @error('password')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-floating mb-5 form-floating-custom">
            <input type="password" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required placeholder="Confirmer">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            @error('password_confirmation')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="btn btn-lg btn-modern w-100 py-3 fw-bold text-white rounded-4 shadow-lg position-relative overflow-hidden" style="background: var(--password-gradient); font-size: 1.1rem;">
            <i class="bi bi-lock-fill me-2"></i>Changer le mot de passe
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

