@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:900px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Paramètres</span></h1>
                <p class="admin-header-subtitle">Gérez votre profil et votre sécurité</p>
            </div>
        </div>

        @if (session('success'))
            <div class="adm-alert adm-alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="adm-alert adm-alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="adm-grid-2">
            <!-- PROFIL -->
            <div class="adm-card">
                <div class="adm-card-header" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;border-radius:var(--adm-radius-lg) var(--adm-radius-lg) 0 0;">
                    <h3 style="color:white;"><i class="bi bi-person-circle"></i> Mon Profil</h3>
                </div>
                <div class="adm-card-body">
                    <form method="POST" action="{{ route('prof.settings.profile.update') }}">
                        @csrf @method('PUT')

                        <div class="adm-form-group">
                            <label class="adm-form-label">Nom complet</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="adm-form-input @error('name') error @enderror" required>
                            @error('name') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label">Adresse Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="adm-form-input @error('email') error @enderror" required>
                            @error('email') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="adm-btn adm-btn-primary" style="width:100%;">
                            <i class="bi bi-check2-circle"></i> Mettre à jour le profil
                        </button>
                    </form>
                </div>
            </div>

            <!-- SECURITY -->
            <div class="adm-card">
                <div class="adm-card-header" style="background:linear-gradient(135deg,#10b981,#059669);color:white;border-radius:var(--adm-radius-lg) var(--adm-radius-lg) 0 0;">
                    <h3 style="color:white;"><i class="bi bi-shield-lock-fill"></i> Sécurité</h3>
                </div>
                <div class="adm-card-body">
                    <form method="POST" action="{{ route('prof.settings.password.update') }}">
                        @csrf @method('PUT')

                        <div class="adm-form-group">
                            <label class="adm-form-label">Mot de passe actuel</label>
                            <input type="password" name="current_password" class="adm-form-input @error('current_password') error @enderror" required>
                            @error('current_password') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label">Nouveau mot de passe</label>
                            <input type="password" name="password" class="adm-form-input @error('password') error @enderror" required minlength="8">
                            @error('password') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" class="adm-form-input" required>
                        </div>

                        <button type="submit" class="adm-btn adm-btn-success" style="width:100%;">
                            <i class="bi bi-lock-fill"></i> Changer le mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
