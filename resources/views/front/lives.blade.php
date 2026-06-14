@extends('layouts.front')

@section('title', 'Lives en direct')

@section('content')

<!-- HERO -->
<section class="py-5 text-center text-white" style="background:#003A8F;">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Lives en direct</h1>
        <p class="lead">
            Participez aux sessions interactives avec vos enseignants en temps réel.
        </p>
    </div>
</section>

<!-- LIVES -->
<section class="py-5" style="background:#F5F7FA;">
    <div class="container">
        <div class="row g-4">

            @forelse($lives as $live)
            <div class="col-md-4">

                <div class="card live-card border-0 shadow-sm h-100 p-4">

                    <!-- Badge LIVE -->
                    <span class="badge bg-danger position-absolute top-0 start-0 m-3">
                        🔴 EN DIRECT
                    </span>

                    <!-- Icon -->
                    <div class="text-center mb-3">
                        <div class="live-icon">
                            <i class="bi bi-camera-video-fill fs-2 text-primary"></i>
                        </div>
                    </div>

                    <h5 class="fw-bold text-center">{{ $live->title }}</h5>

                    <p class="text-muted text-center small">
                        {{ \Illuminate\Support\Str::limit($live->description ?? 'Session en direct', 80) }}
                    </p>

                    <a href="{{ $live->stream_url }}"
                       target="_blank"
                       class="btn btn-primary w-100 mt-3 rounded-pill">
                        ▶ Rejoindre le live
                    </a>

                </div>

            </div>
            @empty

            <div class="text-center">
                <div class="alert alert-danger">
                    Aucun live disponible pour le moment
                </div>
            </div>

            @endforelse

        </div>
    </div>
</section>

<!-- STYLE -->
<style>
.live-card {
    border-radius: 15px;
    transition: all 0.3s ease;
    position: relative;
}

.live-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.live-icon {
    width: 70px;
    height: 70px;
    background: #F5F7FA;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: auto;
}

.live-card:hover .live-icon {
    background: #003A8F;
}

.live-card:hover i {
    color: white !important;
}

/* bouton animation */
.live-card .btn:hover {
    transform: scale(1.05);
    transition: 0.3s;
}
</style>

@endsection
