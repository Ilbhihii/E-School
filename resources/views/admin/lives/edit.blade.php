@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:600px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Modifier le Live</span></h1>
                <p class="admin-header-subtitle">Mettre à jour les informations du live</p>
            </div>
        </div>

        @if(session('success'))
            <div class="adm-alert adm-alert-success">{{ session('success') }}</div>
        @endif

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.lives.update', $live) }}">
                    @csrf @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du live</label>
                        <input type="text" name="title" value="{{ old('title', $live->title) }}" class="adm-form-input">
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe</label>
                        <select name="class_id" class="adm-form-select">
                            <option value="">Choisir une classe</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $live->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Lien (YouTube / Zoom)</label>
                        <input type="url" name="stream_url" value="{{ old('stream_url', $live->stream_url) }}" class="adm-form-input">
                        @error('stream_url') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Date & heure</label>
                        <div class="adm-grid-3">
                            <input type="date" name="live_date" value="{{ old('live_date', $live->live_date) }}" class="adm-form-input">
                            <input type="time" name="start_time" value="{{ old('start_time', $live->start_time) }}" class="adm-form-input">
                            <input type="time" name="end_time" value="{{ old('end_time', $live->end_time) }}" class="adm-form-input">
                        </div>
                    </div>

                    <div class="adm-flex adm-gap-2 adm-mt-2">
                        <a href="{{ route('admin.lives.index') }}" class="adm-btn adm-btn-ghost" style="flex:1;text-align:center;">
                            Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary" style="flex:1;">
                            <i class="bi bi-save-fill"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
