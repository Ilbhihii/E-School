@extends('layouts.admin')

@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --profile-gradient: linear-gradient(135deg, #003A8F 0%, #0F3460 100%);
        --security-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --glass-bg: rgba(255, 255, 255, 0.15);
        --glass-border: rgba(255, 255, 255, 0.25);
    }
    
    .glass-card { 
        background: var(--glass-bg); 
        backdrop-filter: blur(25px); 
        border: 1px solid var(--glass-border); 
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }
    
    .avatar-container {
        position: relative;
        display: inline-block;
    }
    
    #preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 0 10px 30px rgba(0,58,143,0.4);
        transition: all 0.3s ease;
    }
    
    #preview:hover {
        transform: scale(1.05);
    }
    
    .camera-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: rgba(0,58,143,0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    
    .camera-btn:hover {
        background: rgba(0,58,143,1);
        transform: scale(1.1);
    }
    
    .avatar-ring { 
        position: relative; 
        transition: all 0.3s ease;
    }
    .avatar-ring::before { 
        content: ''; 
        position: absolute; 
        top: -8px; 
        left: -8px; 
        right: -8px; 
        bottom: -8px; 
        background: var(--profile-gradient); 
        border-radius: 50%; 
        z-index: -1; 
        opacity: 0.7;
        filter: blur(10px);
    }
    
    .btn-hover { 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
        position: relative;
        overflow: hidden;
    }
    .btn-hover::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    .btn-hover:hover::before { left: 100%; }
    .btn-hover:hover { 
        transform: translateY(-3px) scale(1.02); 
        box-shadow: 0 15px 35px rgba(0,0,0,0.25);
    }
    
    .input-group-text { 
        background: transparent !important; 
        border: none !important; 
        border-right: 1px solid rgba(0,0,0,0.1) !important;
        color: #495057;
    }
    
    .password-strength { 
        height: 5px; 
        border-radius: 3px; 
        transition: all 0.4s ease;
        background: #e9ecef;
    }
    
    .card-hover { 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
    }
    .card-hover:hover { 
        transform: translateY(-8px) !important; 
        box-shadow: 0 30px 60px rgba(0,0,0,0.2) !important;
    }
    
    .form-control:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
        transform: scale(1.02);
    }
    
    .bg-gradient-primary {
        background: var(--profile-gradient) !important;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .floating-elements {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
    }
    
    .floating-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        animation: float 6s ease-in-out infinite;
    }
    
    .floating-circle:nth-child(1) { width: 80px; height: 80px; top: 20%; right: 10%; animation-delay: 0s; }
    .floating-circle:nth-child(2) { width: 120px; height: 120px; top: 60%; left: 10%; animation-delay: 2s; }
    .floating-circle:nth-child(3) { width: 60px; height: 60px; bottom: 20%; right: 20%; animation-delay: 4s; }
</style>


<div class="container-fluid py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; position: relative; overflow: hidden;">
    <div class="floating-elements">
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
    </div>
    <div class="bg-white opacity-10 position-absolute top-0 start-0 end-0 bottom-0"></div>

    <div class="row justify-content-center">

        <div class="col-lg-10 col-xl-8">

            <div class="glass-card card border-0 shadow-lg mx-auto" style="border-radius: 30px; max-width: 900px; backdrop-filter: blur(10px);">
                <div class="card-body p-5">

                    <!-- HEADER -->
                    <div class="text-center mb-5 animate__animated animate__fadeInDown">
                        <div class="avatar-ring mb-3">
                            <i class="fas fa-cog fa-4x text-primary"></i>
                        </div>
                        <h1 class="fw-bold text-dark mb-2" style="font-size: 2.5rem;">Paramètres Admin</h1>
                        <p class="text-muted lead">Gérez votre profil et sécurité avec style</p>
                    </div>


                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
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

                        <!-- PROFIL -->
                        <div class="col-md-6 mb-4">
                            <div class="card glass-card card-hover h-100" style="border-radius: 25px; border: none;">
                                <div class="section-header card-header text-white py-4" style="border-radius:25px 25px 0 0 !important;">
                                    <i class="fas fa-user-circle fa-2x me-2"></i> 
                                    <span class="h5 mb-0">Mon Profil</span>
                                </div>

                                <div class="card-body p-4">

                                    <form method="POST" action="{{ route('admin.settings.profile.update') }}" enctype="multipart/form-data">
                                        @csrf @method('PUT')

                                        <!-- Avatar -->
                                        <div class="text-center mb-5">
                                            <div class="avatar-container mb-3">
                                                <img id="preview"
                                                     src="{{ auth()->user()->profile_photo 
                                                        ? asset('storage/' . auth()->user()->profile_photo) 
                                                        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&size=120&background=003A8F&color=fff' }}"
                                                     class="avatar-ring" id="avatarImage">

                                                <!-- CAMERA BUTTON -->
                                                <label for="imageInput" class="camera-btn" title="Changer photo">
                                                    <i class="fas fa-camera"></i>
                                                </label>
                                                <input type="file" name="profile_photo" id="imageInput" class="d-none" accept="image/*">
                                            </div>
                                            <h6 class="mt-3 mb-0 fw-bold text-dark">{{ auth()->user()->name }}</h6>
                                            <small class="text-muted">Photo de profil (JPG, PNG max 2Mo)</small>
                                            @error('profile_photo')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- NAME -->
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="fas fa-user text-primary"></i></span>
                                            <input type="text" name="name" 
                                                   value="{{ old('name', auth()->user()->name) }}" 
                                                   class="form-control border-0 shadow-sm @error('name') is-invalid @enderror" 
                                                   placeholder="Votre nom complet"
                                                   style="border-radius: 0 15px 15px 0; padding: 12px 20px;">
                                            @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- EMAIL -->
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="fas fa-envelope text-primary"></i></span>
                                            <input type="email" name="email" 
                                                   value="{{ old('email', auth()->user()->email) }}" 
                                                   class="form-control border-0 shadow-sm @error('email') is-invalid @enderror" 
                                                   placeholder="votre@email.com"
                                                   style="border-radius: 0 15px 15px 0; padding: 12px 20px;">
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button class="btn btn-primary btn-hover w-100 py-3 fw-bold shadow-lg" style="border-radius: 15px;">
                                            <i class="fas fa-check-circle me-2"></i> 💾 Mettre à jour le profil
                                        </button>

                                    </form>

                                </div>
                            </div>

                        </div>

                        <!-- PASSWORD -->
                        <div class="col-md-6 mb-4">
                            <div class="card glass-card card-hover h-100" style="border-radius: 25px; border: none;">
                                <div class="card-header text-white py-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius:25px 25px 0 0 !important;">
                                    <i class="fas fa-shield-alt fa-2x me-2"></i> 
                                    <span class="h5 mb-0">Sécurité & Mot de passe</span>
                                </div>

                                <div class="card-body p-4">

                                    <form method="POST" action="{{ route('admin.settings.password.update') }}">
                                        @csrf @method('PUT')

                                        <div class="mb-4">
                                            <label class="form-label fw-bold mb-2">🔒 Mot de passe actuel</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock text-warning"></i></span>
                                                <input type="password" name="current_password" id="current_password"
                                                       class="form-control border-0 shadow-sm @error('current_password') is-invalid @enderror" 
                                                       placeholder="••••••••"
                                                       style="border-radius: 0 15px 15px 0; padding: 12px 20px;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('current_password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold mb-2">🔑 Nouveau mot de passe</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-key text-success"></i></span>
                                                <input type="password" name="password" id="new_password"
                                                       class="form-control border-0 shadow-sm @error('password') is-invalid @enderror" 
                                                       placeholder="Nouveau mot de passe sécurisé"
                                                       style="border-radius: 0 15px 15px 0; padding: 12px 20px;"
                                                       oninput="checkPasswordStrength()">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="password-strength mt-2" id="strength-bar" style="background: #ddd;"></div>
                                            <small class="text-muted" id="strength-text">6+ caractères avec majuscules, minuscules, chiffres</small>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold mb-2">✅ Confirmation</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-check-double text-info"></i></span>
                                                <input type="password" name="password_confirmation" id="confirm_password"
                                                       class="form-control border-0 shadow-sm" 
                                                       placeholder="Répétez le nouveau mot de passe"
                                                       style="border-radius: 0 15px 15px 0; padding: 12px 20px;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <button class="btn btn-gradient-danger btn-hover w-100 py-3 fw-bold shadow-lg" style="border-radius: 15px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none;">
                                            <i class="fas fa-magic me-2"></i> 🔒 Changer le mot de passe
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

    <script>
        // Photo preview
        document.getElementById('imageInput').onchange = function(evt) {
            const [file] = this.files;
            if (file) {
                document.getElementById('preview').src = URL.createObjectURL(file);
            }
        }

        function togglePassword(id) {
            const pwd = document.getElementById(id);
            const icon = pwd.nextElementSibling.querySelector('i');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

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

            const texts = [
                'Trop court',
                'Faible', 
                'Moyen',
                'Bon',
                'Excellent'
            ];
            text.textContent = texts[strength - 1] || '6+ caractères avec majuscules, minuscules, chiffres';
        }
    </script>

</div>

@endsection

