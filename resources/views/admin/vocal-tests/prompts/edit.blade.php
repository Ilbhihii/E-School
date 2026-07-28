@extends('layouts.admin')

@section('title', 'Modifier le texte — Test vocal')
@section('page_title', 'Modifier le texte')
@section('breadcrumb', 'Tests vocaux → Modifier')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-pencil-square me-2" style="color:var(--adm-warning);"></i> Modifier le texte</h1>
        <div class="subtitle">{{ $prompt->title }} — {{ $prompt->subject?->name }} / {{ $prompt->level?->name }} / {{ $prompt->classRoom?->name }}</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.vocal-tests.prompts.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-file-earmark-text" style="color:rgba(255,255,255,0.35);"></i> Détails du texte</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.vocal-tests.prompts.update', $prompt) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Matière <span style="color:var(--adm-danger);">*</span></label>
                                <select name="subject_id" class="adm-form-control @error('subject_id') is-invalid @enderror" required>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id', $prompt->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                @error('subject_id') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Niveau <span style="color:var(--adm-danger);">*</span></label>
                                <select name="level_id" class="adm-form-control @error('level_id') is-invalid @enderror" required>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ old('level_id', $prompt->level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                                    @endforeach
                                </select>
                                @error('level_id') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Classe <span style="color:var(--adm-danger);">*</span></label>
                                <select name="class_id" class="adm-form-control @error('class_id') is-invalid @enderror" required>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $prompt->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('class_id') <small style="color:var(--adm-danger);">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre <span style="color:var(--adm-danger);">*</span></label>
                        <input type="text" name="title" class="adm-form-control" value="{{ old('title', $prompt->title) }}" required>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Consigne (optionnelle)</label>
                        <textarea name="instructions" class="adm-form-control" rows="2">{{ old('instructions', $prompt->instructions) }}</textarea>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Texte à lire / réciter <span style="color:var(--adm-danger);">*</span></label>
                        <textarea name="reading_text" class="adm-form-control" rows="8" dir="rtl" lang="ar" required>{{ old('reading_text', $prompt->reading_text) }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Mode de test <span style="color:var(--adm-danger);">*</span></label>
                                <select name="test_mode" class="adm-form-control" required>
                                    @foreach(\App\Models\VocalTestPrompt::getModes() as $value => $label)
                                        <option value="{{ $value }}" {{ old('test_mode', $prompt->test_mode) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Préparation (secondes)</label>
                                <input type="number" name="preparation_seconds" class="adm-form-control" value="{{ old('preparation_seconds', $prompt->preparation_seconds) }}" min="0" max="300">
                                <small style="color:var(--adm-text-muted);">0 = pas de préparation</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Durée max (secondes)</label>
                                <input type="number" name="maximum_duration" class="adm-form-control" value="{{ old('maximum_duration', $prompt->maximum_duration) }}" min="15" max="600">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Comportement du texte</label>
                                <div style="display:flex;align-items:center;gap:12px;padding-top:8px;">
                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                        <input type="checkbox" name="hide_text_during_recording" value="1" {{ old('hide_text_during_recording', $prompt->hide_text_during_recording) ? 'checked' : '' }}>
                                        <span>Masquer le texte pendant l'enregistrement</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Statut</label>
                                <div style="display:flex;align-items:center;gap:12px;padding-top:8px;">
                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $prompt->is_active) ? 'checked' : '' }}>
                                        <span>Actif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4" style="display:flex;gap:12px;justify-content:flex-end;">
                        <a href="{{ route('admin.vocal-tests.prompts.index') }}" class="adm-btn adm-btn-ghost">Annuler</a>
                        <button type="submit" class="adm-btn adm-btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
