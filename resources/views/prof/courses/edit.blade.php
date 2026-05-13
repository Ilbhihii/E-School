@extends('layouts.prof')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <h2 class="text-4xl font-bold mb-10 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">✏️ Modifier </h2>
                    <form action="{{ route('prof.courses.update',$course->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                    <!-- TITLE -->
                    <div class="col-12 mb-4">
                        <div class="form-floating">
                            <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $course->title) }}" required placeholder="Titre">
                            <label for="title">Titre du cours</label>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <!-- DESCRIPTION -->
                    <div class="mb-4">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" style="height: 120px" required placeholder="Description">{{ old('description', $course->description) }}</textarea>
                            <label for="description">Description du cours</label>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <!-- NIVEAU -->
                    <div class="mb-4">
                        <div class="form-floating">
                            <select class="form-select @error('level_id') is-invalid @enderror" id="level_id" name="level_id" required>
                                <option value="">Choisir niveau</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ old('level_id', $course->level_id) == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }} ({{ $level->subject->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            <label for="level_id">Niveau</label>
                            @error('level_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <!-- SUBJECT -->
                    <div class="mb-4">
                        <div class="form-floating">
                            <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                                <option value="">Choisir une matière</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $course->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="subject_id">Matière</label>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <!-- VIDEO -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2 text-primary">
                            <i class="bi bi-play-circle me-2"></i>Vidéo du cours
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="file" class="form-control @error('video') is-invalid @enderror" name="video" accept="video/*">
                            <span class="input-group-text bg-primary text-white">
                                <i class="bi bi-cloud-upload"></i>
                            </span>
                        </div>
                        @if($course->video)
                            <div class="mt-2 alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Vidéo actuelle :</strong>
                                <a href="{{ asset('storage/' . $course->video) }}" target="_blank" class="ms-2 btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>Voir la vidéo
                                </a>
                            </div>
                        @endif
                        @error('video')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- PDF -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2 text-success">
                            <i class="bi bi-file-earmark-pdf me-2"></i>PDF du cours
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="file" class="form-control @error('pdf') is-invalid @enderror" name="pdf" accept=".pdf">
                            <span class="input-group-text bg-success text-white">
                                <i class="bi bi-cloud-upload"></i>
                            </span>
                        </div>
                        @if($course->pdf)
                            <div class="mt-2 alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>PDF actuel :</strong>
                                <a href="{{ asset('storage/' . $course->pdf) }}" target="_blank" class="ms-2 btn btn-outline-success btn-sm">
                                    <i class="bi bi-eye me-1"></i>Voir le PDF
                                </a>
                            </div>
                        @endif
                        @error('pdf')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- DEVOIRS DU COURS -->
                    <div class="mb-5">

                        <h5 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Devoirs liés au cours
                        </h5>

                        @foreach($course->assignments as $devoir)

                        <div class="border rounded p-3 mb-3 bg-light">

                            <div class="d-flex justify-content-between align-items-center">

                                <div>
                                    <strong>{{ $devoir->title }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $devoir->due_date }}</small>
                                </div>

                                <div>

                                    <a href="{{ route('prof.devoir.edit', $devoir->id) }}" 
                                       class="btn btn-warning btn-sm">
                                        ✏️
                                    </a>

                                    <form method="POST" 
                                          action="{{ route('prof.devoir.destroy', $devoir->id) }}" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">🗑️</button>
                                    </form>

                                </div>

                        </div>

                        @endforeach

                        <!-- AJOUT RAPIDE -->
                        <a href="{{ route('prof.devoir.create', ['course_id'=>$course->id]) }}" 
                           class="btn btn-primary">
                            ➕ Ajouter un devoir à ce cours
                        </a>

                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                        <a href="{{ route('prof.courses.index') }}" class="btn btn-outline-secondary btn-lg px-5">
                            <i class="bi bi-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-gradient-primary btn-lg px-5">
                            <i class="bi bi-save me-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
    </div>

<style>
.btn-gradient-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border: none;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-gradient-primary:hover {
    background: linear-gradient(135deg, #5856eb 0%, #7c3aed 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
}
</style>
@endsection
