@extends('layouts.prof')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    
                    <!-- Header -->
                    <div class="text-center mb-5">
                        <h1 class="display-4 fw-bold mb-3">
                            <i class="bi bi-plus-circle text-primary me-3"></i>
                            Créer un nouveau cours
                        </h1>
                        <p class="lead text-muted">Remplissez les informations pour ajouter votre cours</p>
                    </div>

                    <form action="{{ route('prof.courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-5">
                            <div class="form-floating">
                                <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" placeholder="Titre du cours" required>
                                <label for="title">
                                    <i class="bi bi-book me-2"></i>Titre du cours
                                </label>
                            </div>
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-5">
                            <div class="form-floating">
                                <textarea class="form-control form-control-lg @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="5" placeholder="Description" 
                                          style="height: 140px" required>{{ old('description') }}</textarea>
                                <label for="description">
                                    <i class="bi bi-card-text me-2"></i>Description
                                </label>
                            </div>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Niveau -->
                        <div class="mb-5">
                            <label for="level_id" class="form-label fw-semibold mb-3">
                                <i class="bi bi-mortarboard me-2"></i>Niveau
                            </label>
                            <select class="form-select form-select-lg @error('level_id') is-invalid @enderror" 
                                    id="level_id" name="level_id" required>
                                <option value="">Choisir un niveau...</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }} ({{ $level->subject->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('level_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Matière -->
                        <div class="mb-5">
                            <label for="subject_id" class="form-label fw-semibold mb-3">
                                <i class="bi bi-journal-bookmark me-2"></i>Matière
                            </label>
                            <select class="form-select form-select-lg @error('subject_id') is-invalid @enderror" 
                                    id="subject_id" name="subject_id" required>
                                <option value="">Choisir une matière...</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Video -->
                        <div class="mb-5">
                            <label for="video" class="form-label fw-semibold mb-3">
                                <i class="bi bi-play-circle me-2"></i>Vidéo introductrice
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="file" class="form-control @error('video') is-invalid @enderror" 
                                       id="video" name="video" accept="video/*" onchange="previewVideo(event)">
                                <span class="input-group-text">
                                    <i class="bi bi-film"></i>
                                </span>
                            </div>
                            @error('video')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="videoPreview" class="mt-3 card border-primary" style="display: none;">
                                <div class="card-body p-0">
                                    <video class="w-100 rounded" controls preload="metadata" style="height: 240px;"></video>
                                </div>
                        </div>

                        <!-- PDF -->
                        <div class="mb-5">
                            <label for="pdf" class="form-label fw-semibold mb-3">
                                <i class="bi bi-file-earmark-pdf me-2"></i>Document PDF (optionnel)
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="file" class="form-control @error('pdf') is-invalid @enderror" 
                                       id="pdf" name="pdf" accept=".pdf" onchange="previewPDF(event)">
                                <span class="input-group-text">
                                    <i class="bi bi-file-pdf"></i>
                                </span>
                            </div>
                            @error('pdf')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="pdfPreview" class="mt-3 card border-warning" style="display: none;">
                                <div class="card-body p-0">
                                    <iframe class="w-100 rounded" style="height: 400px; border: none;"></iframe>
                                </div>
                        </div>

                        <!-- DEVOIR LIÉ AU COURS -->
                        <div class="mb-5">

                            <h4 class="fw-bold mb-4 text-primary">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                Ajouter un devoir (optionnel)
                            </h4>

                            <div class="card border-0 shadow-sm p-4">

                                <!-- Titre devoir -->
                                <div class="mb-4">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               name="assignment_title" 
                                               placeholder="Titre du devoir">
                                        <label>📘 Titre du devoir</label>
                                    </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <div class="form-floating">
                                        <textarea class="form-control" 
                                                  name="assignment_description"
                                                  style="height: 120px"
                                                  placeholder="Description du devoir"></textarea>
                                        <label>📝 Description</label>
                                    </div>

                                <!-- Date -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-calendar-check me-2"></i>Date limite
                                    </label>
                                    <input type="date" name="assignment_due_date" class="form-control form-control-lg">
                                </div>

                                <!-- Fichier -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-file-earmark-pdf me-2"></i>Fichier PDF (optionnel)
                                    </label>
                                    <input type="file" name="assignment_file" class="form-control form-control-lg" accept=".pdf">
                                </div>

                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-3 pt-4">
                            <a href="{{ route('prof.courses.index') }}" class="btn btn-outline-secondary btn-lg px-5">
                                <i class="bi bi-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary px-5 text-white shadow-lg">
                                <i class="bi bi-rocket-takeoff me-2"></i>
                                Créer le cours
                            </button>
                        </div>

                    </form>

                </div>
        </div>
</div>

<script>
function previewVideo(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('videoPreview');
    if (file) {
        preview.querySelector('video').src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}

function previewPDF(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('pdfPreview');
    if (file) {
        preview.querySelector('iframe').src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}
</script>

@endsection
