@extends('layouts.front')

@section('title', 'Cours Religieux')

@section('content')

<!-- HERO -->
<section class="py-5 text-center text-white"
    style="background: linear-gradient(135deg,#0d6efd,#6610f2);">
    <div class="container">
        <h1 class="display-4 fw-bold">📖 Cours Religieux</h1>
        <p class="lead">Apprenez le Quran et la lecture facilement</p>
    </div>
</section>

<!-- MATIÈRES -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">

            @forelse($subjects as $subject)
            <div class="col-md-4">
                <a href="{{ route('front.subject.classes', $subject->id) }}"
                   class="text-decoration-none text-dark">

                    <div class="card shadow rounded-4 text-center p-4 h-100">
                        <h5 class="fw-bold">{{ $subject->name }}</h5>
                        <p class="text-muted">Voir les classes</p>
                    </div>

                </a>
            </div>
            @empty
                <p class="text-center text-danger">Aucune matière religieuse</p>
            @endforelse

        </div>
    </div>
</section>

@endsection
