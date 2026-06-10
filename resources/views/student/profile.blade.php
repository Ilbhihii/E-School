@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    {{-- Hero --}}
    <div class="st-hero st-hero-violet st-fade-up">
      <div class="text-center">
        <div class="st-profile-avatar" style="width: 120px; height: 120px; font-size: 42px; border: 4px solid rgba(255,255,255,0.3);">
          <i class="bi bi-person-circle"></i>
        </div>
        <h1 style="font-size: 1.8rem;">{{ auth()->user()->name }}</h1>
        <div class="st-flex st-flex-center st-gap-2 st-mb-2">
          <span class="st-badge st-badge-success"><i class="bi bi-mortarboard-fill me-1"></i>Étudiant Actif</span>
        </div>
        <p style="color: rgba(255,255,255,0.75); font-size: 14px;">
          Membre depuis {{ auth()->user()->created_at->format('F Y') }}
        </p>
        <a href="{{ route('student.settings') }}" class="st-btn st-btn-primary" style="background: rgba(255,255,255,0.15); box-shadow: none;">
          <i class="bi bi-gear"></i> Gérer Mon Profil
        </a>
      </div>
    </div>

    {{-- Stats --}}
    <div class="st-stats st-stats-3 st-fade-up st-fade-up-d1">
      <div class="st-stat">
        <div class="st-stat-icon purple"><i class="bi bi-building"></i></div>
        <h3>{{ auth()->user()->classe->name ?? 'Non assigné' }}</h3>
        <small>Classe Actuelle</small>
      </div>
      <div class="st-stat">
        <div class="st-stat-icon blue"><i class="bi bi-book"></i></div>
        <h3>0</h3>
        <small>Cours Suivis</small>
      </div>
      <div class="st-stat">
        <div class="st-stat-icon green"><i class="bi bi-upload"></i></div>
        <h3>0</h3>
        <small>Devoirs Soumis</small>
      </div>
    </div>

    {{-- Info Card --}}
    <div class="st-card st-fade-up st-fade-up-d2">
      <div class="st-card-header">
        <i class="bi bi-info-circle-fill" style="color: var(--st-primary);"></i>
        <h5>Informations de Contact</h5>
      </div>
      <div class="st-card-body">
        <div class="st-grid-2">
          <div class="st-flex st-flex-center st-gap-3" style="padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px solid var(--st-border);">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--st-primary), var(--st-secondary)); display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-person text-white"></i>
            </div>
            <div>
              <div style="font-weight: 700; color: var(--st-text); font-size: 15px;">{{ auth()->user()->name }}</div>
              <small style="color: var(--st-text-light);">Nom complet</small>
            </div>
          </div>
          <div class="st-flex st-flex-center st-gap-3" style="padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px solid var(--st-border);">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--st-accent), #a78bfa); display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-envelope text-white"></i>
            </div>
            <div>
              <div style="font-weight: 700; color: var(--st-text); font-size: 15px;">{{ auth()->user()->email }}</div>
              <small style="color: var(--st-text-light);">Adresse Email</small>
            </div>
          </div>
        </div>

        @if(auth()->user()->phone)
        <div class="st-flex st-flex-center st-gap-3 st-mt-4" style="padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px solid var(--st-border);">
          <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--st-info), #22d3ee); display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-telephone text-white"></i>
          </div>
          <div>
            <div style="font-weight: 700; color: var(--st-text); font-size: 15px;">{{ auth()->user()->phone }}</div>
            <small style="color: var(--st-text-light);">Téléphone</small>
          </div>
        </div>
        @endif

        <div class="st-flex st-flex-center st-gap-3 st-mt-4">
          <a href="{{ route('student.settings') }}" class="st-btn st-btn-primary st-btn-lg">
            <i class="bi bi-pencil-square"></i> Modifier Profil
          </a>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
