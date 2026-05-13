@extends('layouts.student')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
.glass-card { 
    background: rgba(255, 255, 255, 0.25); 
    backdrop-filter: blur(20px); 
    border: 1px solid rgba(255, 255, 255, 0.18); 
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15); 
    min-height: 400px; 
}
.hero-header { 
    min-height: 200px; 
    display: flex; 
    flex-direction: column; 
    justify-content: center; 
}
.btn-hero:hover { 
    transform: translateY(-3px) !important; 
    box-shadow: 0 15px 30px rgba(0,0,0,0.3) !important; 
}
.description-section {
    background: rgba(255, 255, 255, 0.1);
}
.pdf-section .btn {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: bold;
    transition: all 0.3s ease;
    border-radius: 15px;
}
.video-section video {
    max-height: 500px;
    object-fit: cover;
}
@media (max-width: 768px) { 
    .hero-header { padding: 2rem 1rem !important; } 
    .glass-card { margin-top: 2rem; }
}
</style>
@endpush

<div class="container py-5">

    <!-- Hero Header -->
    <div class="hero-header text-center mb-5 p-5 rounded-5 animate__animated animate__fadeIn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);">
        <h1 class="display-4 fw-bold mb-3 animate__animated animate__bounceIn">{{ $course->title }}</h1>
        <div class="badge bg-light text-dark fs-6 px-3 py-2 animate__animated animate__pulse animate__infinite">
            🏫 Classe : {{ $course->classRoom->name ?? '-' }}
        </div>
    </div>

    <!-- Main Content Card - Glassmorphism -->
    <div class="glass-card position-relative overflow-hidden rounded-5 p-5 animate__animated animate__fadeInUp mx-auto" style="max-width: 900px;">

        {{-- Description --}}
        @if($course->description)
            <div class="description-section mb-5 p-4 rounded-4">
                <p class="lead fs-5 text-dark mb-0 lh-lg">
                    {{ $course->description }}
                </p>
            </div>
        @endif

        {{-- PDF --}}
        @if($course->pdf)
            <div class="pdf-section text-center mb-5">
                <div class="row g-3 justify-content-center">
                    <div class="col-md-5 col-12">
                        <a href="{{ asset('storage/'.$course->pdf) }}"
                           target="_blank"
                           class="btn btn-hero btn-primary w-100 mb-3 shadow-lg animate__animated animate__pulse animate__infinite"
                           style="background: linear-gradient(45deg, #ff6b6b, #feca57); border: none;">
                           📄 Voir PDF
                        </a>
                    </div>
                    <div class="col-md-5 col-12">
                        <a href="{{ asset('storage/'.$course->pdf) }}"
                           download
                           class="btn btn-hero btn-outline-primary w-100 shadow-lg border-2"
                           style="backdrop-filter: blur(10px); color: #333;"
                           onmouseover="this.style.background='linear-gradient(45deg, #667eea, #764ba2)'; this.style.color='white';"
                           onmouseout="this.style.background='transparent'; this.style.color='#333';">
                           ⬇ Télécharger PDF
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- VIDEO --}}
        @if($course->video)
            <div class="video-section mt-4 p-4 rounded-4 overflow-hidden shadow-xl animate__animated animate__zoomIn">
                <video class="w-100 rounded-4 shadow-lg" controls preload="metadata">
                    <source src="{{ asset('storage/'.$course->video) }}">
                    Votre navigateur ne supporte pas la vidéo.
                </video>

                <a href="{{ asset('storage/'.$course->video) }}"
                                    download
                                    class="btn btn-outline-success w-100 mt-2">
                                    ⬇ Télécharger
                                    </a>
            </div>
        @endif

        {{-- DEVOIRS --}}
        @if($course->devoirs->count() > 0)
            <div class="mt-5 p-4 rounded-4 shadow-lg animate__animated animate__fadeInUp"
                style="background: linear-gradient(135deg, #ff9a9e, #fad0c4);">

                <h3 class="fw-bold mb-4 text-center text-white">
                    📝 Devoirs
                </h3>

                <div class="row g-4">
                    @foreach($course->devoirs as $devoir)
                        <div class="col-m-9 col-12">
                            <div class="p-4 rounded-4 bg-white shadow-sm h-100">

                                <h5 class="fw-bold mb-2 text-center">
                                    {{ $devoir->title }}
                                </h5>

                                @if($devoir->description)
                                    <p class="text-muted small text-center">
                                        {{ $devoir->description }}
                                    </p>
                                @endif

                                @if($devoir->file)
                                    <a href="{{ asset('storage/'.$devoir->file) }}"
                                    target="_blank"
                                    class="btn btn-success w-100 mt-2">
                                    📄 Voir le devoir
                                    </a>

                                    <a href="{{ asset('storage/'.$devoir->file) }}"
                                    download
                                    class="btn btn-outline-success w-100 mt-2">
                                    ⬇ Télécharger
                                    </a>
                                @endif

                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        @endif


    </div>

</div>

@endsection

