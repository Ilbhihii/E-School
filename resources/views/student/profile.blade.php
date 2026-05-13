@extends('layouts.student')

@section('content')

<style>
: root {
  --student-green: linear-gradient(135deg, #16a34a 0%, #22c55e 50%, #4ade80 100%);
  --glass-bg: rgba(255, 255, 255, 0.25);
  --glass-border: rgba(22, 163, 74, 0.3);
  --purple-nebula: linear-gradient(135deg, #8b5cf6, #3b82f6, #06b6d4);
  --shadow-neumorph: 0 20px 40px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.5);
}

.hero-profile {
  min-height: 50vh;
  background: linear-gradient(135deg, #545d70 0%, #6485ba9c 50%, #334155 100%);
  position: relative;
  overflow: hidden;
  border-radius: 32px;
  margin-bottom: 4rem;
}

.hero-profile::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 30% 20%, rgba(139, 92, 246, 0.3) 0%, transparent 50%),
              radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.3) 0%, transparent 50%),
              radial-gradient(circle at 20% 80%, rgba(6, 182, 212, 0.3) 0%, transparent 50%);
  animation: nebulaShift 15s ease-in-out infinite;
}

.profile-hero-content {
  position: relative;
  z-index: 2;
}

.avatar-orbit {
  position: relative;
  display: inline-block;
}

.avatar-orbit::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 300px;
  height: 300px;
  margin: -150px 0 0 -150px;
  border: 2px solid rgba(22, 163, 74, 0.3);
  border-radius: 50%;
  animation: orbitRotate 20s linear infinite;
}

.avatar-orbit::after {
  content: '';
  position: absolute;
  top: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 6px;
  height: 6px;
  background: var(--purple-nebula);
  border-radius: 50%;
  box-shadow: 0 0 20px rgba(139, 92, 246, 0.8);
  animation: floatDot 4s ease-in-out infinite;
}

.profile-avatar {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  background: var(--student-green);
  backdrop-filter: blur(20px);
  border: 5px solid rgba(255,255,255,0.3);
  box-shadow: var(--shadow-neumorph);
  transition: all 0.5s ease;
  animation: avatarFloat 6s ease-in-out infinite;
}

.profile-avatar:hover {
  transform: scale(1.05) rotate(5deg);
  box-shadow: 0 30px 60px rgba(22, 163, 74, 0.4);
}

.name-gradient {
  background: linear-gradient(135deg, #ffffff, #e2e8f0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.profile-glass {
  background: var(--glass-bg);
  backdrop-filter: blur(25px);
  border: 1px solid var(--glass-border);
  box-shadow: var(--shadow-neumorph);
  border-radius: 28px;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.profile-glass:hover {
  transform: translateY(-12px);
  box-shadow: 0 35px 70px rgba(59, 55, 55, 0.2);
}

.info-badge {
  background: var(--student-green);
  box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
  border-radius: 20px;
  padding: 0.75rem 1.5rem;
  backdrop-filter: blur(10px);
}

@keyframes nebulaShift {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.1); }
}

@keyframes orbitRotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes avatarFloat {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}

@keyframes floatDot {
  0%, 100% { transform: translateX(-50%) translateY(0); opacity: 1; }
  50% { transform: translateX(-50%) translateY(-20px); opacity: 0.7; }
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<!-- Hero Profile Section -->
<div class="hero-profile rounded-5 p-5 mb-5 position-relative overflow-hidden">
  <div class="position-absolute top-0 start-0 w-100 h-100 particles-bg"></div>
  <div class="row align-items-center min-vh-60">
    <div class="col-lg-4 text-center text-lg-start profile-hero-content animate__animated animate__fadeInLeft">
      <div class="avatar-orbit mb-4">
        <div class="profile-avatar d-flex align-items-center justify-content-center">
          <i class="bi bi-person-circle fs-1 text-white" style="text-shadow: 0 0 20px rgba(255,255,255,0.8);"></i>
        </div>
      </div>
      <h1 class="display-3 fw-bold name-gradient mb-3">{{ auth()->user()->name }}</h1>
      <div class="info-badge d-inline-flex align-items-center text-white fw-semibold mb-3">
        <i class="bi bi-mortarboard-fill me-2"></i>
        Étudiant Actif
      </div>
      <p class="lead text-white-50 mb-4">Membre depuis {{ auth()->user()->created_at->format('F Y') }}</p>
      <a href="{{ route('student.settings') }}" class="btn btn-lg text-white fw-bold px-5 py-3 rounded-pill shadow-lg" style="background: var(--purple-nebula); border: none;">
        <i class="bi bi-gear me-2"></i>Gérer Mon Profil
      </a>
    </div>
    <div class="col-lg-8">
      <!-- Profile Stats Ring -->
      <div class="row g-4">
        <div class="col-md-4">
          <div class="profile-glass p-4 text-center h-100">
            <div class="h2 fw-bold text-success mb-2">{{ auth()->user()->classe->name ?? 'Non assigné' }}</div>
            <div class="text-muted small">Classe Actuelle</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="profile-glass p-4 text-center h-100">
            <div class="h2 fw-bold text-primary mb-2">0</div>
            <div class="text-muted small">Cours Suivis</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="profile-glass p-4 text-center h-100">
            <div class="h2 fw-bold text-info mb-2">0</div>
            <div class="text-muted small">Devoirs Soumis</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main Profile Info Card -->
<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="profile-glass p-5 text-center animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
        <h2 class="h3 fw-bold mb-5" style="color: #1e293b;">Informations de Contact</h2>
        
        <div class="row g-4 mb-5">
          <div class="col-md-6">
            <div class="glassmorphism-sm p-4 rounded-4">
              <div class="d-flex align-items-center mb-3">
                <div class="bg-success rounded-circle p-3 me-3 shadow-sm" style="width: 60px; height: 60px;">
                  <i class="bi bi-person text-white fs-5"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1 text-dark">{{ auth()->user()->name }}</h6>
                  <small class="text-muted">Nom complet</small>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="glassmorphism-sm p-4 rounded-4">
              <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle p-3 me-3 shadow-sm" style="width: 60px; height: 60px;">
                  <i class="bi bi-envelope text-white fs-5"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1 text-dark">{{ auth()->user()->email }}</h6>
                  <small class="text-muted">Adresse Email</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        @if(auth()->user()->phone)
        <div class="glassmorphism-sm p-4 rounded-4 mb-4">
          <div class="d-flex align-items-center">
            <div class="bg-info rounded-circle p-3 me-3 shadow-sm" style="width: 60px; height: 60px;">
              <i class="bi bi-telephone text-white fs-5"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-1 text-dark">{{ auth()->user()->phone }}</h6>
              <small class="text-muted">Téléphone</small>
            </div>
          </div>
        </div>
        @endif

        <div class="d-flex gap-3 justify-content-center">
          <a href="{{ route('student.settings') }}" class="btn btn-success btn-lg px-5 rounded-pill shadow-lg">
            <i class="bi bi-pencil-square me-2"></i>Modifier Profil
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

