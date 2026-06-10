@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:600px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Créer un Live</span></h1>
                <p class="admin-header-subtitle">Planifiez un cours en direct</p>
            </div>
        </div>

        @if(session('success'))
            <div class="adm-alert adm-alert-success">{{ session('success') }}</div>
        @endif

        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('prof.lives.store') }}">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du live</label>
                        <input type="text" name="title" class="adm-form-input @error('title') error @enderror" placeholder="Titre du live" value="{{ old('title') }}" required>
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe</label>
                        <select name="class_id" class="adm-form-select @error('class_id') error @enderror" required>
                            <option value="">Choisir la classe</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Lien YouTube / Zoom</label>
                        <input type="url" name="stream_url" class="adm-form-input @error('stream_url') error @enderror" placeholder="https://..." value="{{ old('stream_url') }}" required>
                        @error('stream_url') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Date et Heures du live</label>
                        <div class="adm-grid-3">
                            <div>
                                <label class="adm-form-label" style="font-size:0.75rem;">Date</label>
                                <input type="date" name="live_date" class="adm-form-input" value="{{ old('live_date', \Carbon\Carbon::today()->format('Y-m-d')) }}" required>
                            </div>
                            <div>
                                <label class="adm-form-label" style="font-size:0.75rem;">Début</label>
                                <input type="time" name="start_time" class="adm-form-input" value="{{ old('start_time') }}" required>
                            </div>
                            <div>
                                <label class="adm-form-label" style="font-size:0.75rem;">Fin</label>
                                <input type="time" name="end_time" class="adm-form-input" value="{{ old('end_time') }}" required>
                            </div>
                        </div>
                        @error('live_date') <div class="adm-form-error">{{ $message }}</div> @enderror
                        @error('start_time') <div class="adm-form-error">{{ $message }}</div> @enderror
                        @error('end_time') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="adm-btn adm-btn-danger adm-btn-lg" style="width:100%;">
                        <i class="bi bi-camera-video-fill"></i> Créer Live
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
