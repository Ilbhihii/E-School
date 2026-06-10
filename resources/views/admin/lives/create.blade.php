@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:600px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Créer un Live</span></h1>
                <p class="admin-header-subtitle">Planifier un cours en direct</p>
            </div>
        </div>

        @if(session('success'))
            <div class="adm-alert adm-alert-success">{{ session('success') }}</div>
        @endif

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.lives.store') }}">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du live</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="adm-form-input" placeholder="Ex: Révision Math" required>
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe</label>
                        <select name="class_id" class="adm-form-select" required>
                            <option value="">Choisir une classe</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Lien (YouTube / Zoom)</label>
                        <input type="url" name="stream_url" value="{{ old('stream_url') }}" class="adm-form-input" placeholder="https://..." required>
                        @error('stream_url') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Date & heure</label>
                        <div class="adm-grid-3">
                            <input type="date" name="live_date" class="adm-form-input" required>
                            <input type="time" name="start_time" class="adm-form-input" required>
                            <input type="time" name="end_time" class="adm-form-input" required>
                        </div>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-danger adm-btn-lg" style="width:100%;">
                        <i class="bi bi-camera-video-fill"></i> Créer le Live
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
