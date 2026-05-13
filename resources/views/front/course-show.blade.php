@extends('layouts.front')

@section('content')

<div class="container py-5">

    <h2 class="fw-bold mb-4">{{ $course->title }}</h2>

    <!-- PREMIUM ALERT -->
    @if(!$course->is_free)
        <div class="alert alert-danger mb-4">
            🔒 Contenu Premium
        </div>
    @endif

    <!-- VIDEO -->
    <div class="video-container mb-4">
        <iframe src="{{ $course->video_url }}" frameborder="0" allowfullscreen></iframe>
    </div>

    <!-- TEST -->
    @auth
        <a href="#" class="btn btn-success mt-3">Passer le test</a>
    @endauth

    @guest
    <div class="alert alert-warning mt-3">
        Connectez-vous pour passer le test
    </div>
    @endguest

</div>

<style>
.video-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
}
.video-container iframe {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 15px;
}
</style>

@endsection

