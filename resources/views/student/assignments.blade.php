@extends('layouts.student')

@section('content')

<div class="glass-card neumorphism-hover mb-5 p-4">
    <div class="d-flex align-items-center mb-4">
        <div class="bg-gradient-primary rounded-circle p-3 me-3 shadow-lg">
            <i class="bi bi-clipboard-check-fill text-black fs-3"></i>
        </div>
        <div>
            <h2 class="fw-bold mb-1 text-dark">📋 Mes Devoirs</h2>
            <p class="text-muted mb-0">Envoyer & suivre vos devoirs</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-modern shadow-lg border-0 rounded-4 mb-4 animate__animated animate__fadeIn">
    <div class="d-flex align-items-center">
        <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
        <div>
            <strong>Succès !</strong> {{ session('success') }}
        </div>
    </div>
</div>
@endif


<div class="custom-card shadow-lg rounded-4 p-5 mb-5 glassmorphism-hover">
    <h4 class="fw-bold mb-4 text-primary">
        <i class="bi bi-cloud-upload-fill me-2"></i>Envoyer un nouveau devoir
    </h4>
    
    <form method="POST" action="{{ route('student.assignments.send') }}" enctype="multipart/form-data" class="upload-form-modern">
        @csrf
        
        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-book-half me-2 text-success"></i>Cours <span class="text-danger">*</span>
                </label>
                <select name="course_id" class="form-select form-select-lg shadow-sm" required>
                    <option value="">Sélectionner un cours...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title ?? $course->name }} ({{ $course->classRoom->name ?? 'Classe' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">
                    <i class="bi bi-journal-text me-2 text-primary"></i>Titre du devoir <span class="text-danger">*</span>
                </label>
                <input type="text" name="title" class="form-control form-control-lg shadow-sm" placeholder="Ex: Devoir de mathématiques - Chapitre 3" required>
            </div>

            
            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Fichier (PDF / Word)
                </label>
                <div class="file-upload-zone border-dashed border-3 border-primary rounded-4 p-4 text-center hover-lift cursor-pointer">
                    <i class="bi bi-cloud-arrow-up-fill fs-1 text-primary mb-3 d-block"></i>
                    <p class="mb-2 fw-semibold text-muted">Glisser-déposer ou cliquer</p>
                    <small class="text-muted">PDF, DOC, DOCX (Max 10MB)</small>
                    <input type="file" name="file" class="form-control mt-3 file-input-custom" accept=".pdf,.doc,.docx" required>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <button type="submit" class="btn btn-gradient-green btn-lg px-5 py-3 fw-bold shadow-lg hover-scale">
                <i class="bi bi-upload me-2"></i>
                <span class="upload-text">Envoyer le devoir</span>
            </button>
        </div>
    </form>
</div>


<div class="glass-card p-5 shadow-lg rounded-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-list-check me-2 text-success"></i>Mes devoirs envoyés
        </h3>
        <span class="badge bg-light text-dark fs-6 px-3 py-2">
            {{ $assignments->count() }} devoir{{ $assignments->count() > 1 ? 's' : '' }}
        </span>
    </div>
    
    @if($assignments->count() === 0)
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted mb-4"></i>
            <h5 class="text-muted mb-3">Aucun devoir envoyé</h5>
            <p class="text-muted mb-4">Commencez par envoyer votre premier devoir ci-dessus !</p>
            <a href="#upload-section" class="btn btn-outline-primary">📤 Envoyer un devoir</a>
        </div>
    @else
        <div class="table-responsive rounded-3 overflow-hidden shadow-lg">
            <table class="table table-hover modern-table mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="fw-bold">Devoir</th>
                        <th class="fw-bold">Fichier</th>
<th class="fw-bold text-center">Statut</th>
                        <th class="fw-bold">Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $a)
                    <tr class="assignment-row hover-lift">
                        <td>
                            <div>
                                <div class="fw-bold">{{ Str::limit($a->title, 40) }}</div>
                                <small class="text-muted">{{ $a->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            <a href="{{ asset('storage/'.$a->file) }}" target="_blank" class="btn btn-outline-primary btn-sm fw-semibold">
                                <i class="bi bi-eye me-1"></i>Voir fichier
                            </a>
                        </td>
                        <td class="text-center ">
@if($a->grade)
                                <span class="badge bg-gradient-success text-black px-3 py-2 fw-semibold fs-6">
                                    <i class="bi bi-check-circle-fill me-1"></i>✅ Bien fait
                                </span>
                            @else
                                <span class="badge bg-gradient-danger text-white px-3 py-2 fw-semibold fs-6">
                                    <i class="bi bi-x-circle-fill me-1"></i>❌ N'a pas fait
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($a->comment)
                                <span class="badge bg-light text-black px-3 py-2 d-block w-100 text-start small">
                                    {{ Str::limit($a->comment, 60) }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
