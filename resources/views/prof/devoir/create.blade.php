@extends('layouts.prof')

@section('content')
<div class="container-fluid py-5">
    <!-- BACK -->
    <div class="mb-6">
        <a href="{{ route('prof.devoir.index', ['course_id' => $course_id ?? '']) }}"
           class="btn btn-outline-primary">
            ← Retour
        </a>
    </div>

    <!-- COURSE INFO -->
    @if($course)
    <div class="card bg-primary bg-opacity-10 border-primary-subtle mb-5">
        <div class="card-body">
            <h2 class="card-title h3 fw-bold text-primary">{{ $course->title }}</h2>
            <p class="card-text text-primary">
                {{ $course->classRoom->name ?? '' }} • {{ $course->subject->name ?? '' }}
            </p>
        </div>
    </div>
    @endif

    <!-- Enhanced Header -->
    <div class="d-flex align-items-center mb-5">
        <div class="bg-primary bg-opacity-10 p-3 rounded-4 shadow-sm me-4">
            <i class="bi bi-plus-circle-fill fs-1 text-primary"></i>
        </div>
        <div>
            <h1 class="mb-1 fw-bold text-dark">Ajouter un Devoir</h1>
            <p class="mb-0 text-muted fs-6">{{ $course ? 'pour le cours: ' . $course->title : 'Créez et assignez un nouveau devoir à votre classe' }}</p>
        </div>
    </div>

    <!-- Enhanced Form -->
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card border-0 shadow-xl">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('prof.devoir.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">
                            <!-- Left Column: Title & Description -->
                            <div class="col-lg-8">
                                <!-- Title Field -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-3">
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                        Titre du devoir <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-primary-subtle">
                                            <i class="bi bi-pencil-square text-primary"></i>
                                        </span>
                                        <input type="text" 
                                               name="title" 
                                               class="form-control form-control-lg border-primary-subtle shadow-sm @error('title') is-invalid @enderror" 
                                               placeholder="Ex: Devoir de mathématiques - Chapitre 3"
                                               required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Description Field -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-3">
                                        <i class="bi bi-card-text text-primary me-2"></i>
                                        Description détaillée
                                    </label>
                                    <textarea name="description" 
                                              rows="5"
                                              class="form-control form-control-lg border-primary-subtle shadow-sm @error('description') is-invalid @enderror"
                                              placeholder="Expliquez les consignes, les objectifs et les critères d'évaluation..."></textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column: Classe, Date, File -->
                            <div class="col-lg-4">
                                <!-- Classe Field -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-3">
                                        <i class="bi bi-building text-primary me-2"></i>
                                        Classe *
                                    </label>
                                    <select name="class_room_id" required class="form-select form-select-lg border-primary-subtle shadow-sm @error('class_room_id') is-invalid @enderror">
                                        <option value="">Choisir une classe...</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_room_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_room_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(isset($courses) && $courses->count() > 0)
                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-3">
                                        <i class="bi bi-book text-primary me-2"></i>
                                        Cours *
                                    </label>
                                    <select name="course_id" required class="form-select form-select-lg border-primary-subtle shadow-sm @error('course_id') is-invalid @enderror">
                                        <option value="">Sélectionner un cours</option>
                                        @foreach($courses as $c)
                                            <option value="{{ $c->id }}" {{ old('course_id', $course_id ?? '') == $c->id ? 'selected' : '' }}>
                                                {{ $c->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif

                                <!-- Due Date Field -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-3">
                                        <i class="bi bi-calendar-check text-primary me-2"></i>
                                        Date limite
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-primary-subtle">
                                            <i class="bi bi-calendar3"></i>
                                        </span>
                                        <input type="date" 
                                               name="due_date" 
                                               class="form-control form-control-lg border-primary-subtle shadow-sm @error('due_date') is-invalid @enderror"
                                               value="{{ old('due_date') }}">
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                    </div>
                                </div>

                                <!-- File Upload Field -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-3">
                                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                        Fichier PDF (Optionnel)
                                    </label>
                                    <div class="border border-dashed border-primary-subtle rounded-3 p-4 text-center bg-light hover-border-primary transition-all">
                                        <i class="bi bi-cloud-upload fs-1 text-primary-subtle mb-3 d-block"></i>
                                        <input type="file" 
                                               name="file" 
                                               accept=".pdf" 
                                               class="form-control form-control-lg border-0 bg-transparent @error('file') is-invalid @enderror">
                                        <div class="text-muted small mt-2">Maximum 10 Mo - Format PDF uniquement</div>
                                    </div>
                                    @error('file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="course_id" value="{{ old('course_id', $course_id ?? '') }}">

                        <!-- Action Buttons -->
                        <hr class="my-5">
                        <div class="d-flex gap-3 justify-content-end flex-wrap">
                            <a href="{{ route('prof.devoir.index', ['course_id' => $course_id ?? '']) }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="bi bi-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-check-circle-fill me-2"></i>{{ $course ? 'Créer pour ' . $course->title : 'Enregistrer le devoir' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
