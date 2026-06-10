@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:900px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Paramètres Admin</span></h1>
                <p class="admin-header-subtitle">Gérez votre profil et sécurité</p>
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
                <div class="adm-card-header" style="background:linear-gradient(135deg,#003A8F,#0F3460);color:white;border-radius:var(--adm-radius-lg) var(--adm-radius-lg) 0 0;">
                    <h3 style="color:white;"><i class="bi bi-person-circle"></i> Mon Profil</h3>
                </div>
                <div class="adm-card-body">
                    <form method="POST" action="{{ route('admin.settings.profile.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <!-- Avatar -->
                        <div style="text-align:center;margin-bottom:1.5rem;">
                            <div style="position:relative;display:inline-block;">
                                <img id="preview"
                                     src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&size=120&background=003A8F&color=fff' }}"
                                     style="width:120px;height:120px;object-fit:cover;border-radius:50%;box-shadow:0 10px 30px rgba(0,58,143,0.3);">
                                <label for="imageInput" style="position:absolute;bottom:5px;right:5px;background:rgba(0,58,143,0.9);color:white;border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;backdrop-filter:blur(10px);">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                                <input type="file" name="profile_photo" id="imageInput" class="d-none" accept="image/*">
                            </div>
                            <div style="font-size:0.8rem;color:var(--adm-text-secondary);margin-top:0.5rem;">Photo de profil (JPG, PNG max 2Mo)</div>
                            @error('profile_photo') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label"><i class="bi bi-person-fill"></i> Nom</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="adm-form-input @error('name') error @enderror">
                            @error('name') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label"><i class="bi bi-envelope-fill"></i> Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="adm-form-input @error('email') error @enderror">
                            @error('email') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="adm-btn adm-btn-primary" style="width:100%;">
                            <i class="bi bi-check-circle-fill"></i> Mettre à jour le profil
                        </button>
                    </form>
                </div>
            </div>

            <!-- SECURITY -->
            <div class="adm-card">
                <div class="adm-card-header" style="background:linear-gradient(135deg,#f093fb,#f5576c);color:white;border-radius:var(--adm-radius-lg) var(--adm-radius-lg) 0 0;">
                    <h3 style="color:white;"><i class="bi bi-shield-lock-fill"></i> Sécurité & Mot de passe</h3>
                </div>
                <div class="adm-card-body">
                    <form method="POST" action="{{ route('admin.settings.password.update') }}">
                        @csrf @method('PUT')

                        <div class="adm-form-group">
                            <label class="adm-form-label">🔒 Mot de passe actuel</label>
                            <input type="password" name="current_password" id="current_password" class="adm-form-input @error('current_password') error @enderror" placeholder="••••••••">
                            @error('current_password') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label">🔑 Nouveau mot de passe</label>
                            <input type="password" name="password" id="new_password" class="adm-form-input @error('password') error @enderror" placeholder="Nouveau mot de passe sécurisé" oninput="checkPasswordStrength()">
                            <div class="adm-progress" style="height:5px;margin-top:0.5rem;">
                                <div class="adm-progress-bar" id="strength-bar" style="width:0%;background:#ddd;"></div>
                            </div>
                            <small id="strength-text" style="color:var(--adm-text-secondary);font-size:0.75rem;">6+ caractères avec majuscules, minuscules, chiffres</small>
                            @error('password') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label">✅ Confirmation</label>
                            <input type="password" name="password_confirmation" id="confirm_password" class="adm-form-input" placeholder="Répétez le nouveau mot de passe">
                        </div>

                        <button type="submit" class="adm-btn adm-btn-danger" style="width:100%;">
                            <i class="bi bi-shield-check"></i> Changer le mot de passe
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    document.getElementById('imageInput')?.addEventListener('change', function(evt) {
        const [file] = this.files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
        }
    });

    function checkPasswordStrength() {
        const password = document.getElementById('new_password').value;
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        let strength = 0;

        if (password.length > 7) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        bar.style.width = (strength * 20) + '%';
        const colors = ['#ff4757', '#ff7831', '#ffa502', '#ffe66d', '#51cf66'];
        bar.style.background = colors[strength - 1] || '#ddd';

        const texts = ['Trop court', 'Faible', 'Moyen', 'Bon', 'Excellent'];
        text.textContent = texts[strength - 1] || '6+ caractères avec majuscules, minuscules, chiffres';
    }
</script>
@endsection
