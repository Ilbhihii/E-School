@extends('layouts.admin')

@section('title', 'Paramètres')
@section('page_title', 'Paramètres')
@section('breadcrumb', 'Paramètres')

@section('content')

@if (session('success'))
<div class="adm-alert adm-alert-success">
    <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
    <span>{{ session('success') }}</span>
</div>
@endif

@if ($errors->any())
<div class="adm-alert adm-alert-danger">
    <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
    <ul style="margin:0;padding-left:1.25rem;">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row g-4">
    <!-- Profile Settings -->
    <div class="col-md-6">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-person-circle" style="color:#60A5FA;"></i> Mon Profil</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.settings.profile.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div style="text-align:center;margin-bottom:1.5rem;">
                        <div class="adm-avatar adm-avatar-lg" style="width:80px;height:80px;font-size:1.5rem;margin:0 auto 0.75rem;background:var(--adm-gradient-primary);border-radius:50%;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div style="font-size:0.8rem;color:var(--adm-text-muted);">Photo de profil (optionnelle)</div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="adm-form-control @error('name') error @enderror">
                        @error('name') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="adm-form-control @error('email') error @enderror">
                        @error('email') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="adm-btn adm-btn-primary w-100">
                        <i class="bi bi-check-lg"></i> Mettre à jour le profil
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Settings -->
    <div class="col-md-6">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-shield-lock" style="color:#FCA5A5;"></i> Sécurité & Mot de passe</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.settings.password.update') }}">
                    @csrf @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label">Mot de passe actuel</label>
                        <input type="password" name="current_password" class="adm-form-control @error('current_password') error @enderror" placeholder="••••••••">
                        @error('current_password') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Nouveau mot de passe</label>
                        <input type="password" name="password" id="new_password" class="adm-form-control @error('password') error @enderror" placeholder="Nouveau mot de passe sécurisé" oninput="checkStrength()">
                        @error('password') <div class="adm-form-error">{{ $message }}</div> @enderror
                        <div style="margin-top:8px;">
                            <div style="height:4px;border-radius:2px;background:rgba(255,255,255,0.06);overflow:hidden;">
                                <div id="strengthBar" style="height:100%;width:0%;border-radius:2px;background:#FCA5A5;transition:all 0.3s ease;"></div>
                            </div>
                            <small id="strengthText" style="color:var(--adm-text-muted);font-size:0.7rem;">6+ caractères minimum</small>
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" class="adm-form-control" placeholder="Répétez le mot de passe">
                    </div>

                    <button type="submit" class="adm-btn adm-btn-danger w-100">
                        <i class="bi bi-check-lg"></i> Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function checkStrength() {
    const pwd = document.getElementById('new_password').value;
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    let score = 0;
    if (pwd.length > 6) score += 25;
    if (/[A-Z]/.test(pwd)) score += 25;
    if (/[0-9]/.test(pwd)) score += 25;
    if (/[^A-Za-z0-9]/.test(pwd)) score += 25;
    bar.style.width = score + '%';
    const colors = ['#FCA5A5','#FCD34D','#86EFAC','#34D399','#059669'];
    bar.style.background = score > 0 ? colors[Math.floor(score/25)-1] : '#FCA5A5';
    const labels = ['Très faible','Faible','Moyen','Bon','Excellent'];
    text.textContent = labels[Math.floor(score/25)-1] || '6+ caractères minimum';
}
</script>
@endsection
