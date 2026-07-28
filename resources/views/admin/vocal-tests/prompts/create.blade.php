@extends('layouts.admin')

@section('title', 'Nouveau texte — Test vocal')
@section('page_title', 'Nouveau texte')
@section('breadcrumb', 'Tests vocaux → Nouveau')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-plus-circle me-2" style="color:var(--adm-primary);"></i> Créer un texte de test vocal</h1>
        <div class="subtitle">Définissez le texte à réciter pour une matière, un niveau et une classe spécifiques</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.vocal-tests.prompts.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

@if(session('error'))
<div class="adm-alert adm-alert-danger mb-4">{{ session('error') }}</div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-file-earmark-text" style="color:rgba(255,255,255,0.35);"></i> Détails du texte</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.vocal-tests.prompts.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Matière <span style="color:var(--adm-danger);">*</span></label>
                                <select name="subject_id" class="adm-form-control @error('subject_id') is-invalid @enderror" required>
                                    <option value="">— Choisir —</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                @error('subject_id') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Niveau <span style="color:var(--adm-danger);">*</span></label>
                                <select name="level_id" class="adm-form-control @error('level_id') is-invalid @enderror" required>
                                    <option value="">— Choisir —</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                                    @endforeach
                                </select>
                                @error('level_id') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Classe <span style="color:var(--adm-danger);">*</span></label>
                                <select name="class_id" class="adm-form-control @error('class_id') is-invalid @enderror" required>
                                    <option value="">— Choisir —</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('class_id') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Titre -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre <span style="color:var(--adm-danger);">*</span></label>
                        <input type="text" name="title" class="adm-form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Ex: Lecture guidée de la sourate Al-Fatiha" required>
                        @error('title') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                    </div>

                    <!-- Instructions -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Consigne (optionnelle)</label>
                        <textarea name="instructions" class="adm-form-control @error('instructions') is-invalid @enderror" rows="2" placeholder="Ex: Lisez lentement en appliquant les règles du tajwid.">{{ old('instructions') }}</textarea>
                        @error('instructions') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                    </div>

                    <!-- Texte à lire -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Texte à lire / réciter <span style="color:var(--adm-danger);">*</span></label>
                        <textarea name="reading_text" class="adm-form-control @error('reading_text') is-invalid @enderror" rows="8" dir="rtl" lang="ar" placeholder="بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ..." required>{{ old('reading_text') }}</textarea>
                        @error('reading_text') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                    </div>

                    <div class="row g-3">
                        <!-- Mode de test -->
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Mode de test <span style="color:var(--adm-danger);">*</span></label>
                                <select name="test_mode" class="adm-form-control @error('test_mode') is-invalid @enderror" required>
                                    @foreach(\App\Models\VocalTestPrompt::getModes() as $value => $label)
                                        <option value="{{ $value }}" {{ old('test_mode', 'reading') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('test_mode') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Temps de préparation -->
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Préparation (secondes)</label>
                                <input type="number" name="preparation_seconds" class="adm-form-control @error('preparation_seconds') is-invalid @enderror" value="{{ old('preparation_seconds', 0) }}" min="0" max="300">
                                <small style="color:var(--adm-text-muted);">0 = pas de préparation. Utile pour le mode Hifd.</small>
                                @error('preparation_seconds') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Durée maximale -->
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Durée max (secondes)</label>
                                <input type="number" name="maximum_duration" class="adm-form-control @error('maximum_duration') is-invalid @enderror" value="{{ old('maximum_duration', 120) }}" min="15" max="600">
                                @error('maximum_duration') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Masquer le texte -->
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Comportement du texte</label>
                                <div style="display:flex;align-items:center;gap:12px;padding-top:8px;">
                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                        <input type="checkbox" name="hide_text_during_recording" value="1" {{ old('hide_text_during_recording') ? 'checked' : '' }}>
                                        <span>Masquer le texte pendant l'enregistrement (mode Hifd)</span>
                                    </label>
                                </div>
                                <small style="color:var(--adm-text-muted);">Le texte devient invisible lorsque l'élève commence à enregistrer.</small>
                            </div>
                        </div>

                        <!-- Statut actif -->
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Statut</label>
                                <div style="display:flex;align-items:center;gap:12px;padding-top:8px;">
                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <span>Actif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4" style="display:flex;gap:12px;justify-content:flex-end;">
                        <a href="{{ route('admin.vocal-tests.prompts.index') }}" class="adm-btn adm-btn-ghost">Annuler</a>
                        <button type="submit" class="adm-btn adm-btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Créer le texte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
