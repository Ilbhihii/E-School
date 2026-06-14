@extends('layouts.front')

@section('title', $course->title)

@section('content')

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Back link --><a href="{{ url()->previous() }}" style="color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.9rem; transition: color 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;"
   onmouseover="this.style.color='rgba(255,209,102,0.9)'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">
    <i class="bi bi-arrow-left"></i> Retour
</a>

                <!-- Title -->
                <h2 class="section-title-3d mb-4" style="font-size: 2rem;">{{ $course->title }}</h2>

                <!-- Premium alert -->
                @if(!$course->is_free)
                    <div class="alert mb-4" style="background: rgba(255,209,102,0.12); color: #FFD166; border: 1px solid rgba(255,209,102,0.2); border-radius: 12px;">
                        <i class="bi bi-lock-fill me-2"></i> Contenu Premium — Abonnez-vous pour accéder à ce cours
                    </div>
                @endif

                <!-- Video -->
                @if($course->video_url)
                <div class="card-3d overflow-hidden p-0 mb-4" style="border-radius: 20px;">
                    <div style="position: relative; padding-bottom: 56.25%; height: 0;">
                        <iframe src="{{ $course->video_url }}" frameborder="0" allowfullscreen
                                style="position: absolute; width: 100%; height: 100%; top: 0; left: 0;"></iframe>
                    </div>
                </div>
                @endif

                <!-- Description -->
                <div class="card-3d mb-4">
                    <h5 class="fw-bold text-white mb-3" style="font-family: 'Poppins', sans-serif;">
                        <i class="bi bi-info-circle me-2" style="color: rgba(255,255,255,0.4);"></i>Description
                    </h5>
                    <p style="color: rgba(255,255,255,0.6); line-height: 1.8;">
                        {{ $course->description ?? 'Aucune description disponible pour ce cours.' }}
                    </p>
                </div>

                <!-- Test button -->
                <div class="d-flex flex-wrap gap-3">
                    @auth
                        <a href="#" class="btn-3d btn-3d-gradient">
                            <i class="bi bi-pencil-square"></i> Passer le test
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-3d btn-3d-outline">
                            <i class="bi bi-box-arrow-in-right"></i> Connectez-vous pour passer le test
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
