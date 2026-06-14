@extends('layouts.prof')

@section('title', 'Modifier le Live')
@section('page_title', 'Modifier le Live')
@section('breadcrumb', 'Édition de live')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-pencil-square me-2" style="color:var(--adm-danger);"></i> Modifier le Live</h1>
        <div class="subtitle">{{ $live->title }}</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.lives.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-camera-video" style="color:rgba(255,255,255,0.35);"></i> Détails du live</h4>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="{{ route('prof.lives.update', $live) }}">
            @csrf @method('PUT')

            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Title -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du live <span style="color:var(--adm-danger);">*</span></label>
                        <input type="text" name="title" id="title" class="adm-form-control" placeholder="Titre du live" value="{{ old('title', $live->title) }}" required>
                        @error('title')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stream URL -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Lien YouTube / Zoom <span style="color:var(--adm-danger);">*</span></label>
                        <input type="url" name="stream_url" id="stream_url" class="adm-form-control" placeholder="https://youtube.com/..." value="{{ old('stream_url', $live->stream_url) }}" required>
                        @error('stream_url')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Class -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe <span style="color:var(--adm-danger);">*</span></label>
                        <select name="class_id" id="class_id" class="adm-form-select" required>
                            <option value="">Choisir la classe</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $live->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Date & Time Section -->
            <div class="adm-card" style="margin-bottom:1.25rem;border-color:rgba(220,53,69,0.1);">
                <div class="adm-card-header" style="background:linear-gradient(135deg,rgba(220,53,69,0.1),rgba(239,68,68,0.05));">
                    <h4><i class="bi bi-calendar-event" style="color:rgba(255,255,255,0.35);"></i> Date et horaire</h4>
                </div>
                <div class="adm-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Date <span style="color:var(--adm-danger);">*</span></label>
                                <input type="date" name="live_date" class="adm-form-control" value="{{ old('live_date', $live->live_date) }}" required>
                                @error('live_date')
                                    <div class="adm-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Heure début <span style="color:var(--adm-danger);">*</span></label>
                                <input type="time" name="start_time" class="adm-form-control" value="{{ old('start_time', $live->start_time) }}" required>
                                @error('start_time')
                                    <div class="adm-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Heure fin <span style="color:var(--adm-danger);">*</span></label>
                                <input type="time" name="end_time" class="adm-form-control" value="{{ old('end_time', $live->end_time) }}" required>
                                @error('end_time')
                                    <div class="adm-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 pt-4" style="border-top:1px solid rgba(255,255,255,0.06);">
                <a href="{{ route('prof.lives.index') }}" class="adm-btn adm-btn-ghost">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
                <button type="submit" class="adm-btn adm-btn-danger" style="margin-left:auto;">
                    <i class="bi bi-save me-1"></i> Modifier le Live
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
