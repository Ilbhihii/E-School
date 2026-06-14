@extends('layouts.admin')

@section('title', 'Créer un live')
@section('page_title', 'Nouveau live')
@section('breadcrumb', 'Créer un live')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-camera-video" style="color:rgba(255,255,255,0.35);"></i> Créer un live</h4>
            </div>
            <div class="adm-card-body">
                @if(session('success'))
                <div class="adm-alert adm-alert-success mb-4">
                    <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.lives.store') }}">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du live</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="adm-form-control @error('title') error @enderror" placeholder="Ex: Révision Math" required>
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe</label>
                        <select name="class_id" class="adm-form-select @error('class_id') error @enderror" required>
                            <option value="">Choisir une classe</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Lien (YouTube / Zoom)</label>
                        <input type="url" name="stream_url" value="{{ old('stream_url') }}" class="adm-form-control @error('stream_url') error @enderror" placeholder="https://..." required>
                        @error('stream_url') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Date & heure</label>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <input type="date" name="live_date" class="adm-form-control" required>
                            </div>
                            <div class="col-md-4">
                                <input type="time" name="start_time" class="adm-form-control" required>
                            </div>
                            <div class="col-md-4">
                                <input type="time" name="end_time" class="adm-form-control" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-danger w-100 mt-3">
                        <i class="bi bi-broadcast"></i> Créer le live
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
