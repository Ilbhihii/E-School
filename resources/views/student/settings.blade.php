@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    @if (session('success'))
      <div class="st-alert st-alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="st-alert st-alert-danger">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    {{-- Header --}}
    <div class="st-hero st-hero-violet st-flex-between st-fade-up">
      <div>
        <h1><i class="bi bi-gear-fill me-2"></i>Paramètres du Compte</h1>
        <p>Gérez votre profil et votre sécurité</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-gear"></i>
      </div>
    </div>

    <div class="st-grid-2">
      {{-- Profil --}}
      <div class="st-card st-fade-up st-fade-up-d1">
        <div class="st-card-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
          <i class="bi bi-person-circle"></i>
          <h5 style="color: white;">Mon Profil</h5>
        </div>
        <div class="st-card-body">
          <form method="POST" action="{{ route('student.settings.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="text-center st-mb-4">
              <div style="position: relative; display: inline-block;">
                <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&size=100&background=667eea&color=fff' }}"
                     alt="Profile" class="rounded-circle border-3 border-white shadow-lg"
                     style="width: 100px; height: 100px; object-fit: cover;">
                <label for="profile_photo" class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-2" style="cursor: pointer; margin: 0 5px 5px 0;" title="Changer la photo">
                  <i class="bi bi-camera text-white fs-6"></i>
                  <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*">
                </label>
              </div>
            </div>

            <div class="st-form-group">
              <label class="st-form-label">Nom Complet</label>
              <input type="text" class="st-form-input @error('name') is-invalid @enderror" name="name" value="{{ old('name', auth()->user()->name) }}" required>
              @error('name')<div class="st-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="st-form-group">
              <label class="st-form-label">Adresse Email</label>
              <input type="email" class="st-form-input @error('email') is-invalid @enderror" name="email" value="{{ old('email', auth()->user()->email) }}" required>
              @error('email')<div class="st-form-error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="st-btn st-btn-primary w-100">
              <i class="bi bi-save"></i> Mettre à jour le Profil
            </button>
          </form>
        </div>
      </div>

      {{-- Password --}}
      <div class="st-card st-fade-up st-fade-up-d2">
        <div class="st-card-header" style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white;">
          <i class="bi bi-lock-fill"></i>
          <h5 style="color: white;">Changer le Mot de Passe</h5>
        </div>
        <div class="st-card-body">
          <form method="POST" action="{{ route('student.settings.password.update') }}">
            @csrf
            @method('PUT')

            <div class="st-form-group">
              <label class="st-form-label">Mot de Passe Actuel</label>
              <input type="password" class="st-form-input @error('current_password') is-invalid @enderror" name="current_password" required>
              @error('current_password')<div class="st-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="st-form-group">
              <label class="st-form-label">Nouveau Mot de Passe <small class="text-muted">(8 caractères min)</small></label>
              <input type="password" class="st-form-input @error('password') is-invalid @enderror" name="password" required minlength="8">
              @error('password')<div class="st-form-error">{{ $message }}</div>@enderror
              <div style="font-size: 12px; color: var(--st-text-light); margin-top: 4px;">
                <i class="bi bi-info-circle me-1"></i>Utilisez une combinaison de lettres, chiffres et symboles
              </div>
            </div>

            <div class="st-form-group">
              <label class="st-form-label">Confirmer le Nouveau Mot de Passe</label>
              <input type="password" class="st-form-input @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
              @error('password_confirmation')<div class="st-form-error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="st-btn w-100" style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white; box-shadow: 0 4px 15px rgba(240,147,251,0.4);">
              <i class="bi bi-key"></i> Changer le Mot de Passe
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
