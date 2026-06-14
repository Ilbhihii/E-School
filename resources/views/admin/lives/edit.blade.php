@extends('layouts.admin')

@section('title', 'Modifier le live')
@section('page_title', 'Modifier live')
@section('breadcrumb', 'Modifier le live')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-pencil" style="color:rgba(255,255,255,0.35);"></i> Modifier le live</h4>
            </div>
            <div class="adm-card-body">
                @if(session('success'))
                <div class="adm-alert adm-alert-success mb-4">
                    <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.lives.update', $live) }}">
                    @csrf @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre du live</label>
                        <input type="text" name="title" value="{{ old('title', $live->title) }}" class="adm-form-control @error('title') error @enderror">
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe</label>
                        <select name="class_id" class="adm-form-select @error('class_id') error @enderror">
                            <option value="">Choisir une classe</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $live->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Lien (YouTube / Zoom)</label>
                        <input type="url" name="stream_url" value="{{ old('stream_url', $live->stream_url) }}" class="adm-form-control @error('stream_url') error @enderror">
                        @error('stream_url') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Date & heure</label>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <input type="date" name="live_date" value="{{ old('live_date', $live->live_date) }}" class="adm-form-control">
                            </div>
                            <div class="col-md-4">
                                <input type="time" name="start_time" value="{{ old('start_time', $live->start_time) }}" class="adm-form-control">
                            </div>
                            <div class="col-md-4">
                                <input type="time" name="end_time" value="{{ old('end_time', $live->end_time) }}" class="adm-form-control">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <a href="{{ route('admin.lives.index') }}" class="adm-btn adm-btn-ghost flex-fill text-center">
                            <i class="bi bi-x"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary flex-fill">
                            <i class="bi bi-save"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
