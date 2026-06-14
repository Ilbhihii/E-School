@extends('layouts.prof')

@section('content')
<div class="container-fluid min-vh-100 py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <h1 class="card-title text-center mb-5 fw-bold fs-1 text-dark">Créer un Live</h1>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('prof.lives.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold text-dark">Titre du live</label>
                            <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" id="title" name="title" placeholder="Titre" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="class_id" class="form-label fw-semibold text-dark">Classe</label>
                            <select class="form-select form-select-lg @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                                <option value="">Choisir la classe</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stream_url" class="form-label fw-semibold text-dark">Lien YouTube / Zoom</label>
                            <input type="url" class="form-control form-control-lg @error('stream_url') is-invalid @enderror" id="stream_url" name="stream_url" placeholder="Lien" value="{{ old('stream_url') }}" required>
                            @error('stream_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-3">Date et Heures du live</label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-muted small mb-2">Date</label>
                                    <input type="date" class="form-control form-control-lg @error('live_date') is-invalid @enderror" name="live_date" value="{{ old('live_date', \Carbon\Carbon::today()->format('Y-m-d')) }}" required>
                                    @error('live_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-muted small mb-2">Heure début</label>
                                    <input type="time" class="form-control form-control-lg @error('start_time') is-invalid @enderror" name="start_time" value="{{ old('start_time') }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-muted small mb-2">Heure fin</label>
                                    <input type="time" class="form-control form-control-lg @error('end_time') is-invalid @enderror" name="end_time" value="{{ old('end_time') }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-semibold shadow-lg border-0">
                            <i class="fas fa-video me-2"></i>Créer Live
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

