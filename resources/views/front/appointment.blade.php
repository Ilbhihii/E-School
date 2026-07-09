@extends('layouts.front')

@section('title', 'Prendre rendez-vous')

@section('content')

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">

                <div class="text-center mb-5">
                    <span class="badge px-3 py-2 mb-3" style="background: rgba(124,58,237,0.15); color: #A78BFA; border-radius: 20px; font-weight: 500; font-size: 0.8rem;">
                        📅 {{ $type === 'test' ? 'Test de niveau' : 'Rendez-vous' }}
                    </span>
                    <h2 class="section-title-3d">
                        {{ $type === 'test' ? 'Prenez rendez-vous pour un test' : 'Prenez rendez-vous' }}
                    </h2>
                    <p class="text-white-50" style="max-width: 450px; margin: 0 auto;">
                        @if($type === 'test')
                            Remplissez ce formulaire pour programmer votre test de niveau. Nous vous contacterons pour fixer le rendez-vous.
                        @else
                            Laissez-nous vos coordonnées et précisez l'objet de votre demande. Nous vous recontacterons rapidement.
                        @endif
                    </p>
                </div>

                <div class="card-3d p-4">
                    <form method="POST" action="{{ route('appointment.store') }}{{ request()->query('from') ? '?' . http_build_query(['redirect' => 'student.waiting']) : '' }}">
                        @csrf

                        @if(session('success'))
                            <div class="alert mb-4" style="background: rgba(34,197,94,0.15); color: #4ADE80; border: 1px solid rgba(34,197,94,0.2); border-radius: 12px; padding: 14px 18px; font-size: 0.92rem;">
                                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        <div class="row g-3">
                            <!-- Type de rendez-vous (visible uniquement pour les visiteurs) -->
                            @if($type !== 'test')
                            <div class="col-12">
                                <label class="form-label text-white-50 small mb-1" style="font-weight: 500;">Type de rendez-vous</label>
                                <select name="type" required
                                        class="form-control chat-input @error('type') is-invalid @enderror"
                                        style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.85); border-radius: 10px; padding: 12px 16px; appearance: auto;
                                               color-scheme: dark;">
                                    <option value="" disabled {{ old('type') ? '' : 'selected' }} style="color:#000;background:#fff;">Choisissez un type</option>
                                    @foreach(\App\Models\TestAppointment::getTypes() as $val => $label)
                                        @if($val !== 'test')
                                        <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }} style="color:#000;background:#fff;">{{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('type')
                                    <small style="color: #FCA5A5;">{{ $message }}</small>
                                @enderror
                            </div>
                            @else
                            <!-- Type caché pour les étudiants (test) -->
                            <input type="hidden" name="type" value="test">
                            @endif

                            <!-- Prénom -->
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small mb-1" style="font-weight: 500;">Prénom</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $user?->name ? explode(' ', $user->name)[0] ?? '' : '') }}" required
                                       class="form-control chat-input @error('first_name') is-invalid @enderror"
                                       placeholder="Votre prénom" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.85); border-radius: 10px; padding: 12px 16px;">
                                @error('first_name')
                                    <small style="color: #FCA5A5;">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Nom -->
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small mb-1" style="font-weight: 500;">Nom</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $user?->name ? implode(' ', array_slice(explode(' ', $user->name), 1)) : '') }}" required
                                       class="form-control chat-input @error('last_name') is-invalid @enderror"
                                       placeholder="Votre nom" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.85); border-radius: 10px; padding: 12px 16px;">
                                @error('last_name')
                                    <small style="color: #FCA5A5;">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Téléphone -->
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small mb-1" style="font-weight: 500;">Numéro de téléphone</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required
                                       class="form-control chat-input @error('phone') is-invalid @enderror"
                                       placeholder="+212 6XX XX XX XX" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.85); border-radius: 10px; padding: 12px 16px;">
                                @error('phone')
                                    <small style="color: #FCA5A5;">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small mb-1" style="font-weight: 500;">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user?->email ?? '') }}" required
                                       class="form-control chat-input @error('email') is-invalid @enderror"
                                       placeholder="exemple@email.com" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.85); border-radius: 10px; padding: 12px 16px;">
                                @error('email')
                                    <small style="color: #FCA5A5;">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn-3d btn-3d-gradient w-100 mt-4" style="padding: 14px;">
                            <i class="bi bi-calendar-check me-2"></i> Envoyer ma demande
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
