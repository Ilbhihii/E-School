@extends('layouts.prof')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <i class="bi bi-chat-dots-fill fs-1 text-success mb-3 d-block"></i>
                <h2 class="display-5 fw-bold text-dark mb-2">Liste des Matières</h2>
                <p class="lead text-muted">Sélectionnez une matière pour commencer la discussion avec vos étudiants</p>
            </div>

            <!-- Subjects Grid -->
            <div class="row g-4">
                @forelse($subjects as $subject)
                    @if($subject->name !== 'Administration')
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('prof.chat', $subject->id) }}" class="text-decoration-none">
                            <div class="card border-0 shadow h-100 hover-lift hover-shadow-lg text-center p-4 bg-gradient rounded-4 position-relative overflow-hidden">
                                <!-- Gradient overlay -->
                                <div class="gradient-overlay"></div>
                                
                                <!-- Icon -->
                                <div class="icon-wrapper mb-4 position-relative">
                                    <i class="bi bi-journal-text fs-1 text-white"></i>
                                </div>
                                
                                <!-- Subject Name -->
                                <h4 class="h5 fw-bold text-white mb-2 position-relative">{{ $subject->name }}</h4>
                                
                                <!-- Action button -->
                                <div class="position-relative">
                                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-semibold">
                                        Ouvrir Chat <i class="bi bi-chevron-right ms-1"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune matière disponible</h5>
                        <p class="text-muted">Les matières apparaîtront ici une fois créées.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-8px);
}

.hover-shadow-lg:hover {
    box-shadow: 0 25px 50px rgba(0,0,0,0.15) !important;
}

.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.gradient-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102,126,234,0.9) 0%, rgba(118,75,162,0.9) 100%);
    z-index: 1;
}

.icon-wrapper, h4, .badge {
    position: relative;
    z-index: 2;
}

.card {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}
</style>
@endsection
