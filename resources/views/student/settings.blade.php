@extends('layouts.student')
@section('title', 'Paramètres')
@section('content')

<div class="page-header">
    <div>
        <h1><i class="bi bi-gear-fill" style="color:#4F46E5;"></i> Paramètres</h1>
        <div class="subtitle">Gérez votre profil et votre sécurité</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('student.profile') }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-person-circle me-1"></i> Voir mon profil</a>
    </div>
</div>

@if(session('success'))
<div class="pr-alert pr-alert-success mb-4"><i class="bi bi-check-circle-fill" style="flex-shrink:0;margin-top:1px;"></i> <span>{{ session('success') }}</span></div>
@endif
@if($errors->any())
<div class="pr-alert pr-alert-danger mb-4">
    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
    <span><ul class="mb-0" style="list-style:none;padding-left:0;">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul></span>
</div>
@endif

<div class="row g-3">
    <!-- Profile -->
    <div class="col-lg-6">
        <div class="pr-card">
            <div class="pr-card-header" style="background:rgba(79,70,229,0.04);">
                <h4><i class="bi bi-person-circle" style="color:#4F46E5;"></i> Mon Profil</h4>
            </div>
            <div class="pr-card-body">
                <form method="POST" action="{{ route('student.settings.profile.update') }}">
                    @csrf @method('PUT')

                    <div style="margin-bottom:1rem;">
                        <label class="pr-label"><i class="bi bi-person-fill me-1" style="color:#4F46E5;"></i>Nom complet</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="pr-input" required>
                    </div>

                    <div style="margin-bottom:1.25rem;">
                        <label class="pr-label"><i class="bi bi-envelope-fill me-1" style="color:#4F46E5;"></i>Adresse Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="pr-input" required>
                    </div>

                    <button type="submit" class="pr-btn pr-btn-primary" style="width:100%;justify-content:center;">
                        <i class="bi bi-check2-circle"></i> Mettre à jour le profil
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Password -->
    <div class="col-lg-6">
        <div class="pr-card">
            <div class="pr-card-header" style="background:rgba(5,150,105,0.04);">
                <h4><i class="bi bi-shield-lock-fill" style="color:#059669;"></i> Sécurité</h4>
            </div>
            <div class="pr-card-body">
                <form method="POST" action="{{ route('student.settings.password.update') }}">
                    @csrf @method('PUT')

                    <div style="margin-bottom:1rem;">
                        <label class="pr-label"><i class="bi bi-lock-fill me-1" style="color:#059669;"></i>Mot de passe actuel</label>
                        <input type="password" name="current_password" class="pr-input" required placeholder="••••••••">
                    </div>

                    <div style="margin-bottom:1rem;">
                        <label class="pr-label"><i class="bi bi-key-fill me-1" style="color:#059669;"></i>Nouveau mot de passe</label>
                        <input type="password" name="password" class="pr-input" required minlength="8" placeholder="Minimum 8 caractères">
                    </div>

                    <div style="margin-bottom:1.25rem;">
                        <label class="pr-label"><i class="bi bi-check-circle-fill me-1" style="color:#059669;"></i>Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" class="pr-input" required placeholder="Retaper le mot de passe">
                    </div>

                    <button type="submit" class="pr-btn pr-btn-success" style="width:100%;justify-content:center;">
                        <i class="bi bi-lock-fill"></i> Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="pr-card mt-3">
    <div class="pr-card-header">
        <h4><i class="bi bi-info-circle" style="color:#64748B;"></i> Informations du compte</h4>
    </div>
    <div class="pr-card-body">
        <div class="row g-2">
            @foreach([
                ['label' => 'Rôle', 'value' => '<span class="pr-badge pr-badge-info">Étudiant</span>'],
                ['label' => 'Membre depuis', 'value' => auth()->user()->created_at->format('d/m/Y')],
                ['label' => 'Statut', 'value' => '<span class="pr-badge pr-badge-success"><i class="bi bi-check-circle-fill me-1"></i> Actif</span>'],
            ] as $info)
            <div class="col-md-4">
                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.04em;color:#64748B;font-weight:600;margin-bottom:2px;">{{ $info['label'] }}</div>
                <div style="font-weight:500;font-size:0.85rem;color:#F1F5F9;">{!! $info['value'] !!}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
